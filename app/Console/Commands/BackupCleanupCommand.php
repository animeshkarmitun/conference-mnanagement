<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cleanup 
                            {--days=30 : Number of days to retain backups}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old backup files based on retention policy';

    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info("DRY RUN MODE - No files will be deleted");
            $this->line('');
        }

        $this->info("Cleaning up backups older than {$days} days...");

        try {
            if ($dryRun) {
                // Show what would be deleted
                $cutoffDate = now()->subDays($days);
                $oldBackups = \App\Models\BackupRecord::where('created_at', '<', $cutoffDate)->get();
                
                if ($oldBackups->isEmpty()) {
                    $this->info("No backups would be deleted.");
                    return 0;
                }

                $this->line("Backups that would be deleted:");
                $headers = ['ID', 'Type', 'Size', 'Created', 'File'];
                $rows = [];

                foreach ($oldBackups as $backup) {
                    $rows[] = [
                        $backup->id,
                        $backup->backup_type,
                        $backup->formatted_file_size,
                        $backup->created_at->format('Y-m-d H:i:s'),
                        $backup->file_name,
                    ];
                }

                $this->table($headers, $rows);
                $this->line("Total backups to delete: " . $oldBackups->count());
                
            } else {
                $deletedCount = $this->backupService->cleanupOldBackups($days);
                
                if ($deletedCount > 0) {
                    $this->info("Successfully deleted {$deletedCount} old backup(s).");
                } else {
                    $this->info("No old backups found to delete.");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Cleanup failed: " . $e->getMessage());
            return 1;
        }
    }
}
