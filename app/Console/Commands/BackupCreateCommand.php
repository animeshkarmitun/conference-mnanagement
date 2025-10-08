<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use App\Models\User;
use Illuminate\Console\Command;
use Exception;

class BackupCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create 
                            {--type=full : Type of backup (full, incremental, differential, emergency)}
                            {--user= : User ID to associate with backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database backup';

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
        $type = $this->option('type');
        $userId = $this->option('user');

        // Validate backup type
        $validTypes = ['full', 'incremental', 'differential', 'emergency'];
        if (!in_array($type, $validTypes)) {
            $this->error("Invalid backup type. Valid types: " . implode(', ', $validTypes));
            return 1;
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

        $this->info("Creating {$type} backup...");

        try {
            $backup = $this->backupService->createBackup($type, $user);

            $this->info("Backup created successfully!");
            $this->line("Backup ID: {$backup->id}");
            $this->line("File: {$backup->file_name}");
            $this->line("Size: {$backup->formatted_file_size}");
            $this->line("Status: {$backup->status}");

            return 0;

        } catch (Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return 1;
        }
    }
}
