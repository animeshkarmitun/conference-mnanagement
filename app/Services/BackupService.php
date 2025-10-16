<?php

namespace App\Services;

use App\Models\BackupRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use PDO;

class BackupService
{
    protected $backupPath;
    protected $encryptionKey;

    public function __construct()
    {
        $this->backupPath = config('backup.storage_path', 'backups');
        $this->encryptionKey = config('backup.encryption_key', env('BACKUP_ENCRYPTION_KEY'));
    }

    /**
     * Create a new backup
     */
    public function createBackup(string $type = 'full', ?User $user = null): BackupRecord
    {
        $user = $user ?? auth()->user();
        
        // Create backup record
        $backupRecord = BackupRecord::create([
            'backup_type' => $type,
            'file_path' => '',
            'file_name' => '',
            'file_size' => 0,
            'status' => 'pending',
            'created_by' => $user->id,
            'started_at' => now(),
        ]);

        try {
            $backupRecord->update(['status' => 'in_progress']);

            // Generate backup filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$type}_{$timestamp}.sql";
            $filePath = "{$this->backupPath}/{$filename}";

            // Create backup directory if it doesn't exist
            $fullPath = storage_path("app/{$this->backupPath}");
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            // Perform database backup
            $this->performDatabaseBackup($filePath);

            // Get file size and generate checksum
            $fullFilePath = storage_path("app/{$filePath}");
            $fileSize = filesize($fullFilePath);
            $checksum = hash_file('sha256', $fullFilePath);

            // Update backup record
            $backupRecord->update([
                'file_path' => $filePath,
                'file_name' => $filename,
                'file_size' => $fileSize,
                'checksum' => $checksum,
                'status' => 'completed',
                'completed_at' => now(),
                'metadata' => [
                    'database_name' => config('database.connections.mysql.database'),
                    'tables_count' => $this->getTablesCount(),
                    'backup_version' => '1.0',
                ],
            ]);

            Log::info("Backup created successfully", [
                'backup_id' => $backupRecord->id,
                'type' => $type,
                'file_size' => $fileSize,
                'user_id' => $user->id,
            ]);

            return $backupRecord;

        } catch (Exception $e) {
            $backupRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error("Backup failed", [
                'backup_id' => $backupRecord->id,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            throw $e;
        }
    }

    /**
     * Perform the actual database backup
     */
    protected function performDatabaseBackup(string $filePath): void
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $fullFilePath = storage_path("app/{$filePath}");

        // Try multiple backup methods
        $backupMethods = [
            'mysqldump' => $this->tryMysqldumpBackup($host, $port, $username, $password, $database, $fullFilePath),
            'php_pdo' => $this->tryPhpPdoBackup($database, $fullFilePath),
        ];

        $lastError = null;
        foreach ($backupMethods as $method => $callback) {
            try {
                Log::info("Attempting backup using method: {$method}");
                $callback();
                if (file_exists($fullFilePath) && filesize($fullFilePath) > 0) {
                    Log::info("Backup created successfully using method: {$method}");
                    return;
                } else {
                    Log::warning("Backup method {$method} completed but file is empty or missing");
                }
            } catch (Exception $e) {
                $lastError = $e;
                Log::error("Backup method {$method} failed: " . $e->getMessage());
                Log::error("Backup method {$method} stack trace: " . $e->getTraceAsString());
                continue;
            }
        }

        throw new Exception("All backup methods failed. Last error: " . ($lastError ? $lastError->getMessage() : 'Unknown error'));
    }

    /**
     * Try mysqldump backup method
     */
    protected function tryMysqldumpBackup(string $host, string $port, string $username, string $password, string $database, string $fullFilePath): callable
    {
        return function() use ($host, $port, $username, $password, $database, $fullFilePath) {
            // Check if mysqldump is available
            $mysqldumpPath = $this->findMysqldumpPath();
            
            if (!$mysqldumpPath) {
                throw new Exception("mysqldump command not found in system PATH");
            }

            // Build mysqldump command with proper escaping
            $command = sprintf(
                '%s --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --no-tablespaces %s > %s 2>&1',
                escapeshellarg($mysqldumpPath),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($fullFilePath)
            );

            // Execute backup command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $errorOutput = implode("\n", $output);
                throw new Exception("mysqldump failed with return code: {$returnCode}. Output: {$errorOutput}");
            }

            if (!file_exists($fullFilePath) || filesize($fullFilePath) === 0) {
                throw new Exception("Backup file was not created or is empty");
            }
        };
    }

    /**
     * Try PHP PDO backup method (fallback)
     */
    protected function tryPhpPdoBackup(string $database, string $fullFilePath): callable
    {
        return function() use ($database, $fullFilePath) {
            Log::info("Starting PHP PDO backup for database: {$database}");
            
            try {
                $pdo = DB::connection()->getPdo();
                Log::info("PDO connection established successfully");
                
                $tables = $this->getDatabaseTables();
                Log::info("Found " . count($tables) . " tables to backup: " . implode(', ', $tables));
                
                $backupContent = "-- Database backup created at " . now() . "\n";
                $backupContent .= "-- Database: {$database}\n\n";
                $backupContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Get table structure
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
                $backupContent .= "-- Table structure for table `{$table}`\n";
                $backupContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $backupContent .= $createTable['Create Table'] . ";\n\n";

                // Get table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $backupContent .= "-- Dumping data for table `{$table}`\n";
                    foreach ($rows as $row) {
                        $values = array_map(function($value) use ($pdo) {
                            return $value === null ? 'NULL' : $pdo->quote($value);
                        }, $row);
                        $backupContent .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $backupContent .= "\n";
                }
            }

            $backupContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

            Log::info("Writing backup content to file: {$fullFilePath}");
            $bytesWritten = file_put_contents($fullFilePath, $backupContent);
            
            if ($bytesWritten === false) {
                throw new Exception("Failed to write backup file to: {$fullFilePath}");
            }
            
            Log::info("Backup file written successfully. Size: {$bytesWritten} bytes");
        } catch (Exception $e) {
            Log::error("PHP PDO backup failed: " . $e->getMessage());
            Log::error("PHP PDO backup stack trace: " . $e->getTraceAsString());
            throw $e;
        }
        };
    }

    /**
     * Find mysqldump executable path
     */
    protected function findMysqldumpPath(): ?string
    {
        $possiblePaths = [
            'mysqldump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
        ];

        foreach ($possiblePaths as $path) {
            if ($this->commandExists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Check if command exists
     */
    protected function commandExists(string $command): bool
    {
        $returnCode = 0;
        exec("which {$command} 2>/dev/null || where {$command} 2>nul", $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Get all database tables
     */
    protected function getDatabaseTables(): array
    {
        try {
            Log::info("Fetching database tables...");
            $tables = DB::select("SHOW TABLES");
            $tableNames = [];
            
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                $tableNames[] = array_values($tableArray)[0];
            }
            
            Log::info("Successfully retrieved " . count($tableNames) . " tables");
            return $tableNames;
        } catch (Exception $e) {
            Log::error("Failed to get database tables: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fix backup file paths for existing records
     */
    public function fixBackupPaths(): int
    {
        $fixedCount = 0;
        $backups = BackupRecord::all();
        
        foreach ($backups as $backup) {
            $currentPath = $backup->file_path;
            $fullPath = $backup->getFullFilePath();
            
            // Check if the current path is a directory
            if (is_dir($fullPath)) {
                Log::warning("Backup path is a directory, attempting to fix", [
                    'backup_id' => $backup->id,
                    'current_path' => $currentPath,
                    'full_path' => $fullPath,
                ]);
                
                // Try to find the actual backup file in the directory
                $backupDir = $fullPath;
                $files = glob($backupDir . '/*.sql');
                
                if (!empty($files)) {
                    $actualFile = basename($files[0]);
                    $newPath = 'backups/' . $actualFile;
                    
                    $backup->update([
                        'file_path' => $newPath,
                        'file_name' => $actualFile,
                    ]);
                    
                    $fixedCount++;
                    Log::info("Fixed backup path", [
                        'backup_id' => $backup->id,
                        'old_path' => $currentPath,
                        'new_path' => $newPath,
                    ]);
                } else {
                    Log::warning("No backup files found in directory", [
                        'backup_id' => $backup->id,
                        'directory' => $backupDir,
                    ]);
                }
            }
        }
        
        return $fixedCount;
    }

    /**
     * Get list of all backups
     */
    public function getBackups(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return BackupRecord::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get backup by ID
     */
    public function getBackup(int $id): ?BackupRecord
    {
        return BackupRecord::with(['creator', 'restoreRecords.creator'])->find($id);
    }

    /**
     * Delete a backup
     */
    public function deleteBackup(int $id): bool
    {
        $backup = BackupRecord::find($id);
        
        if (!$backup) {
            Log::warning("Backup not found for deletion", ['backup_id' => $id]);
            return false;
        }

        $fullPath = $backup->getFullFilePath();
        
        try {
            // Delete physical file if it exists
            if (file_exists($fullPath)) {
                if (is_file($fullPath)) {
                    unlink($fullPath);
                    Log::info("Backup file deleted", [
                        'backup_id' => $id,
                        'file_path' => $fullPath,
                    ]);
                } else {
                    Log::warning("Backup path is not a file", [
                        'backup_id' => $id,
                        'file_path' => $fullPath,
                        'is_dir' => is_dir($fullPath),
                    ]);
                }
            } else {
                Log::info("Backup file does not exist, proceeding with database deletion", [
                    'backup_id' => $id,
                    'file_path' => $fullPath,
                ]);
            }

            // Delete database record
            $backup->delete();

            Log::info("Backup record deleted successfully", [
                'backup_id' => $id,
                'file_path' => $backup->file_path,
                'user_id' => auth()->id(),
            ]);

            return true;
            
        } catch (Exception $e) {
            Log::error("Failed to delete backup", [
                'backup_id' => $id,
                'file_path' => $fullPath,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Clean up old backups based on retention policy
     */
    public function cleanupOldBackups(int $retentionDays = 30): int
    {
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        
        $oldBackups = BackupRecord::where('created_at', '<', $cutoffDate)->get();
        $deletedCount = 0;

        foreach ($oldBackups as $backup) {
            if ($this->deleteBackup($backup->id)) {
                $deletedCount++;
            }
        }

        Log::info("Backup cleanup completed", [
            'deleted_count' => $deletedCount,
            'retention_days' => $retentionDays,
        ]);

        return $deletedCount;
    }

    /**
     * Preview cleanup operation
     */
    public function previewCleanup(array $cleanupTypes, int $retentionDays, bool $includeFailed = false, bool $includeInProgress = false): array
    {
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        
        $query = BackupRecord::where('created_at', '<', $cutoffDate);
        
        if (!$includeFailed) {
            $query->where('status', '!=', 'failed');
        }
        
        if (!$includeInProgress) {
            $query->where('status', '!=', 'in_progress');
        }
        
        $backups = $query->get();
        $totalSize = $backups->sum('file_size');
        
        return [
            'count' => $backups->count(),
            'size' => $this->formatBytes($totalSize)
        ];
    }

    /**
     * Cleanup backups with options
     */
    public function cleanupBackups(array $cleanupTypes, int $retentionDays, bool $includeFailed = false, bool $includeInProgress = false): array
    {
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        $deletedCount = 0;
        $freedSpace = 0;
        
        $query = BackupRecord::where('created_at', '<', $cutoffDate);
        
        if (!$includeFailed) {
            $query->where('status', '!=', 'failed');
        }
        
        if (!$includeInProgress) {
            $query->where('status', '!=', 'in_progress');
        }
        
        $backups = $query->get();
        
        foreach ($backups as $backup) {
            $fileSize = $backup->file_size ?? 0;
            
            // Delete files if requested
            if (in_array('files', $cleanupTypes) && $backup->file_path && file_exists($backup->file_path)) {
                unlink($backup->file_path);
            }
            
            // Delete database record if requested
            if (in_array('database', $cleanupTypes)) {
                $backup->delete();
                $deletedCount++;
                $freedSpace += $fileSize;
            } else if (in_array('files', $cleanupTypes) && !in_array('database', $cleanupTypes)) {
                // If only files are being cleaned, still count the record as processed
                $deletedCount++;
                $freedSpace += $fileSize;
            }
        }
        
        $message = "Successfully cleaned up {$deletedCount} backup(s)";
        if ($freedSpace > 0) {
            $message .= " and freed {$this->formatBytes($freedSpace)} of storage space";
        }
        
        Log::info("Enhanced backup cleanup completed", [
            'deleted_count' => $deletedCount,
            'freed_space' => $freedSpace,
            'cleanup_types' => $cleanupTypes,
            'retention_days' => $retentionDays,
        ]);
        
        return [
            'message' => $message,
            'deleted_count' => $deletedCount,
            'freed_space' => $this->formatBytes($freedSpace)
        ];
    }

    /**
     * Verify backup integrity
     */
    public function verifyBackup(int $id): bool
    {
        $backup = BackupRecord::find($id);
        
        if (!$backup || !$backup->exists()) {
            return false;
        }

        $currentChecksum = hash_file('sha256', $backup->getFullFilePath());
        
        // Check file integrity
        if ($currentChecksum !== $backup->checksum) {
            return false;
        }
        
        // Check if file contains valid SQL content
        return $this->validateBackupContent($backup->getFullFilePath());
    }
    
    /**
     * Validate backup file content
     */
    public function validateBackupContent(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }
        
        // Check if content contains valid SQL statements
        $sqlStatements = array_filter(
            array_map('trim', explode(';', $content)),
            function($stmt) {
                return !empty($stmt) && 
                       !preg_match('/^--/', $stmt) && // Skip comments
                       !preg_match('/^\/\*/', $stmt) && // Skip block comments
                       !preg_match('/^max-width:/', $stmt) && // Skip CSS content
                       !preg_match('/^[a-zA-Z-]+:\s*[a-zA-Z0-9\s]+$/', $stmt) && // Skip CSS properties
                       !preg_match('/^<[^>]+>$/', $stmt); // Skip HTML tags
            }
        );
        
        // Check if we have any valid SQL statements
        $validSqlCount = 0;
        foreach ($sqlStatements as $statement) {
            if (preg_match('/^(CREATE|INSERT|UPDATE|DELETE|DROP|ALTER|SET|USE|LOCK|UNLOCK)/i', $statement)) {
                $validSqlCount++;
            }
        }
        
        // File is valid if it contains at least some SQL statements
        return $validSqlCount > 0;
    }

    /**
     * Get backup statistics
     */
    public function getBackupStats(): array
    {
        $totalBackups = BackupRecord::count();
        $completedBackups = BackupRecord::completed()->count();
        $failedBackups = BackupRecord::where('status', 'failed')->count();
        $totalSize = BackupRecord::completed()->sum('file_size');
        $lastBackup = BackupRecord::completed()->latest()->first();

        return [
            'total_backups' => $totalBackups,
            'completed_backups' => $completedBackups,
            'failed_backups' => $failedBackups,
            'success_rate' => $totalBackups > 0 ? round(($completedBackups / $totalBackups) * 100, 2) : 0,
            'total_size' => $totalSize,
            'formatted_size' => $this->formatBytes($totalSize),
            'last_backup' => $lastBackup?->created_at,
            'last_backup_size' => $lastBackup?->formatted_file_size,
        ];
    }

    /**
     * Get storage usage information
     */
    public function getStorageInfo(): array
    {
        $backupDir = storage_path("app/{$this->backupPath}");
        
        if (!is_dir($backupDir)) {
            return [
                'used_space' => 0,
                'formatted_used_space' => '0 B',
                'file_count' => 0,
            ];
        }

        $totalSize = 0;
        $fileCount = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($backupDir)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }

        return [
            'used_space' => $totalSize,
            'formatted_used_space' => $this->formatBytes($totalSize),
            'file_count' => $fileCount,
        ];
    }

    /**
     * Get count of database tables
     */
    protected function getTablesCount(): int
    {
        return DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [
            config('database.connections.mysql.database')
        ])[0]->count;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get available backup types
     */
    public function getBackupTypes(): array
    {
        return [
            'full' => 'Full Backup (Complete database)',
            'incremental' => 'Incremental Backup (Changes since last backup)',
            'differential' => 'Differential Backup (Changes since last full backup)',
            'emergency' => 'Emergency Backup (Manual trigger)',
        ];
    }
}
