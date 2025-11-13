<?php

namespace App\Console\Commands;

use App\Services\RestoreService;
use App\Models\User;
use Illuminate\Console\Command;
use Exception;

class RestoreExecuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:execute 
                            {backup_id : ID of the backup to restore from}
                            {--type=full : Type of restore (full, selective)}
                            {--tables= : Comma-separated list of tables for selective restore}
                            {--user= : User ID to associate with restore}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from a backup';

    protected $restoreService;

    public function __construct(RestoreService $restoreService)
    {
        parent::__construct();
        $this->restoreService = $restoreService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupId = (int) $this->argument('backup_id');
        $type = $this->option('type');
        $tables = $this->option('tables');
        $userId = $this->option('user');
        $force = $this->option('force');

        // Validate restore type
        $validTypes = ['full', 'selective'];
        if (!in_array($type, $validTypes)) {
            $this->error("Invalid restore type. Valid types: " . implode(', ', $validTypes));
            return 1;
        }

        // Parse tables for selective restore
        $tablesArray = null;
        if ($type === 'selective') {
            if (!$tables) {
                $this->error("Tables must be specified for selective restore");
                return 1;
            }
            $tablesArray = array_map('trim', explode(',', $tables));
        }

        // Get user
        $user = null;
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return 1;
            }
        }

        // Get backup info
        $backup = \App\Models\BackupRecord::find($backupId);
        if (!$backup) {
            $this->error("Backup with ID {$backupId} not found");
            return 1;
        }

        // Show restore information
        $this->info("Restore Information:");
        $this->line("Backup ID: {$backup->id}");
        $this->line("Backup Type: {$backup->backup_type}");
        $this->line("Backup Date: {$backup->created_at->format('Y-m-d H:i:s')}");
        $this->line("File Size: {$backup->formatted_file_size}");
        $this->line("Restore Type: {$type}");
        
        if ($type === 'selective' && $tablesArray) {
            $this->line("Tables: " . implode(', ', $tablesArray));
        }

        // Confirmation prompt
        if (!$force) {
            if (!$this->confirm("Are you sure you want to proceed with this restore? This will overwrite current data.")) {
                $this->info("Restore cancelled.");
                return 0;
            }
        }

        // Wait a moment to ensure restore record is created by controller
        sleep(2);
        
        // Find existing restore record for this backup (created by controller)
        $restoreRecord = \App\Models\RestoreRecord::where('backup_id', $backupId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->first();

        if (!$restoreRecord) {
            $this->error("No restore record found for backup ID {$backupId}");
            $this->error("This might happen if the restore record was not created properly.");
            $this->error("Please check the logs for more information.");
            return 1;
        }

        $this->info("Found restore record ID: {$restoreRecord->id}");
        $this->info("Starting restore operation...");
        $this->info("Backup file: {$backup->getFullFilePath()}");

        try {
            // Increase execution time limit
            set_time_limit(0);
            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '512M');
            
            // Update restore record status to in_progress if it's pending
            if ($restoreRecord->status === 'pending') {
                $restoreRecord->update(['status' => 'in_progress']);
            }

            // Execute restore using the existing restore record
            $this->info("Calling restoreFromBackup service...");
            $restore = $this->restoreService->restoreFromBackup($backupId, $type, $tablesArray, $user);

            $this->info("Restore completed successfully!");
            $this->line("Restore ID: {$restore->id}");
            $this->line("Status: {$restore->status}");
            if ($restore->completed_at) {
                $this->line("Completed at: {$restore->completed_at->format('Y-m-d H:i:s')}");
            }
            
            // Verify restore record was updated
            $restoreRecord->refresh();
            $this->line("Final restore record status: {$restoreRecord->status}");

            return 0;

        } catch (Exception $e) {
            $this->error("Restore failed: " . $e->getMessage());
            $this->error("Error trace: " . $e->getTraceAsString());
            
            // Update restore record with error
            try {
                $restoreRecord->refresh();
                $restoreRecord->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);
                $this->info("Restore record updated with error status");
            } catch (Exception $updateError) {
                $this->error("Failed to update restore record: " . $updateError->getMessage());
            }
            
            return 1;
        }
    }
}
