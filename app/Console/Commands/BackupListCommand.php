<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:list 
                            {--limit=20 : Number of backups to display}
                            {--type= : Filter by backup type}
                            {--status= : Filter by status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all database backups';

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
        $limit = (int) $this->option('limit');
        $type = $this->option('type');
        $status = $this->option('status');

        $this->info("Database Backups");
        $this->line(str_repeat('=', 50));

        try {
            $backups = $this->backupService->getBackups($limit);

            if ($backups->isEmpty()) {
                $this->warn("No backups found.");
                return 0;
            }

            $headers = ['ID', 'Type', 'Status', 'Size', 'Created', 'Creator'];
            $rows = [];

            foreach ($backups as $backup) {
                $rows[] = [
                    $backup->id,
                    $backup->backup_type,
                    $backup->status,
                    $backup->formatted_file_size,
                    $backup->created_at->format('Y-m-d H:i:s'),
                    $backup->creator->first_name . ' ' . $backup->creator->last_name,
                ];
            }

            $this->table($headers, $rows);

            // Show statistics
            $stats = $this->backupService->getBackupStats();
            $this->line('');
            $this->info("Backup Statistics:");
            $this->line("Total Backups: {$stats['total_backups']}");
            $this->line("Completed: {$stats['completed_backups']}");
            $this->line("Failed: {$stats['failed_backups']}");
            $this->line("Success Rate: {$stats['success_rate']}%");
            $this->line("Total Size: {$stats['formatted_size']}");

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to list backups: " . $e->getMessage());
            return 1;
        }
    }
}
