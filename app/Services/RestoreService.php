<?php

namespace App\Services;

use App\Models\BackupRecord;
use App\Models\RestoreRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Exception;
use PDO;

class RestoreService
{
    public function __construct()
    {
        // No dependencies to avoid circular dependency
    }

    /**
     * Restore from a backup
     */
    public function restoreFromBackup(
        int $backupId, 
        string $restoreType = 'full', 
        ?array $tables = null, 
        ?User $user = null
    ): RestoreRecord {
        $user = $user ?? auth()->user();
        $backup = BackupRecord::find($backupId);

        if (!$backup) {
            throw new Exception("Backup not found");
        }

        if (!$backup->isCompleted()) {
            throw new Exception("Cannot restore from incomplete backup");
        }

        if (!$backup->exists()) {
            throw new Exception("Backup file does not exist");
        }

        // Verify backup integrity
        $backupService = app(BackupService::class);
        if (!$backupService->verifyBackup($backupId)) {
            throw new Exception("Backup file is corrupted");
        }

        // Create restore record
        $restoreRecord = RestoreRecord::create([
            'backup_id' => $backupId,
            'restore_type' => $restoreType,
            'tables_restored' => $tables,
            'status' => 'pending',
            'created_by' => $user->id,
            'started_at' => now(),
        ]);

        try {
            $restoreRecord->update(['status' => 'in_progress']);

            // Create a backup before restore (safety measure) - but make it optional
            $safetyBackupId = null;
            try {
                $backupService = app(BackupService::class);
                $safetyBackup = $backupService->createBackup('emergency', $user);
                $safetyBackupId = $safetyBackup->id;
                Log::info("Safety backup created before restore", ['safety_backup_id' => $safetyBackupId]);
            } catch (Exception $e) {
                Log::warning("Failed to create safety backup, proceeding with restore anyway", ['error' => $e->getMessage()]);
            }
            
            $restoreRecord->update([
                'metadata' => [
                    'safety_backup_id' => $safetyBackupId,
                    'restore_reason' => 'Manual restore operation',
                ]
            ]);

            // Perform the restore
            Log::info("Starting restore operation", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'restore_type' => $restoreType
            ]);
            
            if ($restoreType === 'full') {
                $this->performFullRestore($backup);
            } else {
                $this->performSelectiveRestore($backup, $tables);
            }
            
            Log::info("Restore operation completed successfully", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId
            ]);

            // Update restore record
            $restoreRecord->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info("Restore completed successfully", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'type' => $restoreType,
                'user_id' => $user->id,
            ]);

            return $restoreRecord;

        } catch (Exception $e) {
            $restoreRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error("Restore failed", [
                'restore_id' => $restoreRecord->id,
                'backup_id' => $backupId,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            throw $e;
        }
    }

    /**
     * Perform full database restore
     */
    protected function performFullRestore(BackupRecord $backup): void
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $backupFile = $backup->getFullFilePath();

        // Try multiple restore methods
        $restoreMethods = [
            'mysql_command' => $this->tryMysqlCommandRestore($host, $port, $username, $password, $database, $backupFile),
            'php_pdo' => $this->tryPhpPdoRestore($backupFile),
        ];

        $lastError = null;
        foreach ($restoreMethods as $method => $callback) {
            try {
                Log::info("Attempting restore using method: {$method}");
                $callback();
                Log::info("Restore completed successfully using method: {$method}");
                
                // Clear application cache after restore
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                return;
                
            } catch (Exception $e) {
                $lastError = $e;
                Log::warning("Restore method {$method} failed: " . $e->getMessage());
                continue;
            }
        }

        throw new Exception("All restore methods failed. Last error: " . ($lastError ? $lastError->getMessage() : 'Unknown error'));
    }

    /**
     * Try mysql command restore method
     */
    protected function tryMysqlCommandRestore(string $host, string $port, string $username, string $password, string $database, string $backupFile): callable
    {
        return function() use ($host, $port, $username, $password, $database, $backupFile) {
            // Check if mysql command is available
            $mysqlPath = $this->findMysqlPath();
            
            if (!$mysqlPath) {
                throw new Exception("mysql command not found in system PATH");
            }

            // Build mysql command for restore
            $command = sprintf(
                '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
                escapeshellarg($mysqlPath),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($backupFile)
            );

            // Execute restore command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $errorOutput = implode("\n", $output);
                throw new Exception("mysql restore failed with return code: {$returnCode}. Output: {$errorOutput}");
            }
        };
    }

    /**
     * Try PHP PDO restore method (fallback)
     */
    protected function tryPhpPdoRestore(string $backupFile): callable
    {
        return function() use ($backupFile) {
            Log::info("Starting PHP PDO restore from file: {$backupFile}");
            
            if (!file_exists($backupFile)) {
                throw new Exception("Backup file does not exist: {$backupFile}");
            }
            
            $sqlContent = file_get_contents($backupFile);
            if ($sqlContent === false) {
                throw new Exception("Failed to read backup file");
            }
            
            // Split SQL content into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sqlContent)),
                function($stmt) {
                    return !empty($stmt) && 
                           !preg_match('/^--/', $stmt) && // Skip comments
                           !preg_match('/^\/\*/', $stmt) && // Skip block comments
                           !preg_match('/^max-width:/', $stmt) && // Skip CSS content
                           !preg_match('/^[a-zA-Z-]+:\s*[a-zA-Z0-9\s]+$/', $stmt) && // Skip CSS properties
                           !preg_match('/^<[^>]+>$/', $stmt) && // Skip HTML tags
                           !preg_match('/Mozilla\/5\.0/', $stmt) && // Skip browser user agent strings
                           !preg_match('/Windows NT 10\.0/', $stmt) && // Skip OS strings
                           !preg_match('/Chrome\/\d+\.\d+\.\d+\.\d+/', $stmt) && // Skip browser version strings
                           !preg_match('/Safari\/\d+\.\d+/', $stmt) && // Skip Safari version strings
                           !preg_match('/^\s*$/', $stmt) && // Skip empty statements
                           (preg_match('/^(CREATE|INSERT|UPDATE|DELETE|DROP|ALTER|SET|USE|LOCK|UNLOCK)/i', $stmt) || // SQL commands
                            preg_match('/^\/\*.*\*\/$/', $stmt) || // Block comments
                            preg_match('/^--/', $stmt)); // Line comments
                }
            );
            
            $pdo = DB::connection()->getPdo();
            
            // Disable foreign key checks temporarily
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            try {
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        try {
                            // Convert INSERT statements to INSERT ... ON DUPLICATE KEY UPDATE
                            if (preg_match('/^INSERT INTO\s+`?(\w+)`?\s+VALUES\s*\(/i', $statement, $matches)) {
                                $tableName = $matches[1];
                                $modifiedStatement = $this->convertInsertToUpsert($statement, $tableName);
                                Log::debug("Converted INSERT to UPSERT for table: {$tableName}");
                                $pdo->exec($modifiedStatement);
                            } else {
                                Log::debug("Executing SQL statement: " . substr($statement, 0, 100) . "...");
                                $pdo->exec($statement);
                            }
                        } catch (Exception $e) {
                            // Log the error but continue with other statements
                            Log::warning("SQL statement failed: " . $e->getMessage());
                            Log::debug("Failed statement: " . substr($statement, 0, 200) . (strlen($statement) > 200 ? '...' : ''));
                            
                            // If it's a "table already exists" error, ignore and continue
                            if (
                                strpos($e->getMessage(), 'already exists') !== false ||
                                strpos($e->getMessage(), 'SQLSTATE[42S01]') !== false ||
                                strpos($e->getMessage(), 'errno: 1050') !== false
                            ) {
                                Log::info("Ignoring 'table already exists' error");
                                continue;
                            }
                            // If it's a syntax error with non-SQL content, skip it
                            else if (strpos($e->getMessage(), 'syntax error') !== false && 
                                    (preg_match('/max-width:/', $statement) || 
                                     preg_match('/^[a-zA-Z-]+:\s*[a-zA-Z0-9\s]+$/', $statement) ||
                                     preg_match('/Mozilla\/5\.0/', $statement) ||
                                     preg_match('/Windows NT 10\.0/', $statement) ||
                                     preg_match('/Chrome\/\d+\.\d+\.\d+\.\d+/', $statement) ||
                                     preg_match('/Safari\/\d+\.\d+/', $statement))) {
                                Log::info("Skipping non-SQL content: " . substr($statement, 0, 50) . "...");
                                continue;
                            }
                            
                            // If it's a duplicate key error, we can ignore it (data already exists)
                            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                                Log::info("Ignoring 'duplicate entry' error - data already exists");
                                continue;
                            }
                            
                            // For other errors, re-throw
                            throw $e;
                        }
                    }
                }
                
                // Re-enable foreign key checks
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                
                Log::info("PHP PDO restore completed successfully");
                
            } catch (Exception $e) {
                // Re-enable foreign key checks even if restore fails
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                throw $e;
            }
        };
    }

    /**
     * Find mysql executable path
     */
    protected function findMysqlPath(): ?string
    {
        $possiblePaths = [
            'mysql',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
            '/opt/mysql/bin/mysql',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.21\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
        ];

        foreach ($possiblePaths as $path) {
            $returnCode = 0;
            exec("which {$path} 2>/dev/null || where {$path} 2>nul", $output, $returnCode);
            if ($returnCode === 0) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Perform selective restore (specific tables)
     */
    protected function performSelectiveRestore(BackupRecord $backup, ?array $tables): void
    {
        if (empty($tables)) {
            throw new Exception("No tables specified for selective restore");
        }

        $backupFile = $backup->getFullFilePath();
        
        // Extract specific tables from backup file
        $extractedTables = $this->extractTablesFromBackup($backupFile, $tables);
        
        if (empty($extractedTables)) {
            throw new Exception("No valid tables found in backup for selective restore");
        }

        // Restore each table
        foreach ($extractedTables as $tableSql) {
            DB::unprepared($tableSql);
        }

        // Clear application cache after restore
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
    }

    /**
     * Extract specific tables from backup file
     */
    protected function extractTablesFromBackup(string $backupFile, array $tables): array
    {
        $content = file_get_contents($backupFile);
        $extractedTables = [];

        foreach ($tables as $table) {
            // Look for table structure and data
            $pattern = "/-- Table structure for table `{$table}`.*?-- Dumping data for table `{$table}`.*?-- Table structure for table|-- Dumping data for table `{$table}`.*?-- Table structure for table|$/s";
            
            if (preg_match($pattern, $content, $matches)) {
                $tableSql = $matches[0];
                // Clean up the SQL
                $tableSql = preg_replace('/-- Table structure for table.*?-- Dumping data for table.*?-- Table structure for table|-- Dumping data for table.*?-- Table structure for table|$/s', '', $tableSql);
                $extractedTables[] = trim($tableSql);
            }
        }

        return $extractedTables;
    }

    /**
     * Get list of all restore operations
     */
    public function getRestores(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return RestoreRecord::with(['backup', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get restore by ID
     */
    public function getRestore(int $id): ?RestoreRecord
    {
        return RestoreRecord::with(['backup', 'creator'])->find($id);
    }

    /**
     * Get available tables for selective restore
     */
    public function getAvailableTables(): array
    {
        $tables = DB::select("SHOW TABLES");
        $tableNames = [];
        
        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableNames[] = array_values($tableArray)[0];
        }
        
        return $tableNames;
    }

    /**
     * Preview backup contents
     */
    public function previewBackup(int $backupId): array
    {
        $backup = BackupRecord::find($backupId);
        
        if (!$backup || !$backup->exists()) {
            throw new Exception("Backup not found or file does not exist");
        }

        $backupFile = $backup->getFullFilePath();
        $content = file_get_contents($backupFile);
        
        // Extract table information
        preg_match_all('/-- Table structure for table `([^`]+)`/', $content, $matches);
        $tables = $matches[1] ?? [];
        
        // Get file info
        $fileInfo = [
            'file_size' => filesize($backupFile),
            'created_at' => date('Y-m-d H:i:s', filemtime($backupFile)),
            'tables_count' => count($tables),
            'tables' => $tables,
        ];

        return $fileInfo;
    }

    /**
     * Convert INSERT statement to INSERT ... ON DUPLICATE KEY UPDATE
     */
    protected function convertInsertToUpsert(string $insertStatement, string $tableName): string
    {
        try {
            // Get table structure to determine columns
            $pdo = DB::connection()->getPdo();
            $columns = $pdo->query("SHOW COLUMNS FROM `{$tableName}`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($columns)) {
                Log::warning("Could not get columns for table: {$tableName}, using original statement");
                return $insertStatement;
            }
            
            // Extract the VALUES part
            if (preg_match('/^INSERT INTO\s+`?(\w+)`?\s+VALUES\s*(.+)$/i', $insertStatement, $matches)) {
                $valuesPart = $matches[2];
                
                // Build the ON DUPLICATE KEY UPDATE clause
                $updateClause = [];
                foreach ($columns as $column) {
                    $columnName = $column['Field'];
                    // Skip auto-increment primary keys
                    if ($column['Extra'] !== 'auto_increment') {
                        $updateClause[] = "`{$columnName}` = VALUES(`{$columnName}`)";
                    }
                }
                
                if (!empty($updateClause)) {
                    $upsertStatement = "INSERT INTO `{$tableName}` VALUES {$valuesPart} ON DUPLICATE KEY UPDATE " . implode(', ', $updateClause);
                    Log::debug("Converted INSERT to UPSERT: " . substr($upsertStatement, 0, 200) . "...");
                    return $upsertStatement;
                }
            }
            
            Log::warning("Could not convert INSERT statement for table: {$tableName}");
            return $insertStatement;
            
        } catch (Exception $e) {
            Log::warning("Error converting INSERT to UPSERT for table {$tableName}: " . $e->getMessage());
            return $insertStatement;
        }
    }

    /**
     * Get restore statistics
     */
    public function getRestoreStats(): array
    {
        $totalRestores = RestoreRecord::count();
        $completedRestores = RestoreRecord::completed()->count();
        $failedRestores = RestoreRecord::where('status', 'failed')->count();
        $lastRestore = RestoreRecord::completed()->latest()->first();

        return [
            'total_restores' => $totalRestores,
            'completed_restores' => $completedRestores,
            'failed_restores' => $failedRestores,
            'success_rate' => $totalRestores > 0 ? round(($completedRestores / $totalRestores) * 100, 2) : 0,
            'last_restore' => $lastRestore?->created_at,
            'last_restore_type' => $lastRestore?->restore_type,
        ];
    }

    /**
     * Rollback to a previous restore (using safety backup)
     */
    public function rollbackRestore(int $restoreId): RestoreRecord
    {
        $restore = RestoreRecord::find($restoreId);
        
        if (!$restore) {
            throw new Exception("Restore record not found");
        }

        if (!$restore->isCompleted()) {
            throw new Exception("Cannot rollback incomplete restore");
        }

        $safetyBackupId = $restore->metadata['safety_backup_id'] ?? null;
        
        if (!$safetyBackupId) {
            throw new Exception("No safety backup found for rollback");
        }

        // Perform rollback restore
        return $this->restoreFromBackup($safetyBackupId, 'full', null, auth()->user());
    }

    /**
     * Get restore types
     */
    public function getRestoreTypes(): array
    {
        return [
            'full' => 'Full Restore (Complete database)',
            'selective' => 'Selective Restore (Specific tables only)',
        ];
    }
}
