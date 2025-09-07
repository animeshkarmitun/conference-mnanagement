<?php

namespace App\Services;

use App\Models\BackupRecord;
use App\Models\RestoreRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Exception;

class RestoreService
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
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
        if (!$this->backupService->verifyBackup($backupId)) {
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

            // Create a backup before restore (safety measure)
            $safetyBackup = $this->backupService->createBackup('emergency', $user);
            
            $restoreRecord->update([
                'metadata' => [
                    'safety_backup_id' => $safetyBackup->id,
                    'restore_reason' => 'Manual restore operation',
                ]
            ]);

            // Perform the restore
            if ($restoreType === 'full') {
                $this->performFullRestore($backup);
            } else {
                $this->performSelectiveRestore($backup, $tables);
            }

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

        // Build mysql command for restore
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backupFile)
        );

        // Execute restore command
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception("Database restore failed with return code: {$returnCode}");
        }

        // Clear application cache after restore
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
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
