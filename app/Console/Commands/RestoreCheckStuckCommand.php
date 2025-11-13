<?php

namespace App\Console\Commands;

use App\Models\RestoreRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RestoreCheckStuckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:check-stuck 
                            {--fix : Automatically fix stuck restore records}
                            {--minutes=30 : Minutes to consider a restore as stuck}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix stuck restore records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $fix = $this->option('fix');

        $this->info("Checking for stuck restore records (stuck for more than {$minutes} minutes)...");

        $stuckRestores = RestoreRecord::where('status', 'in_progress')
            ->where('started_at', '<', now()->subMinutes($minutes))
            ->get();

        if ($stuckRestores->isEmpty()) {
            $this->info("No stuck restore records found.");
            return 0;
        }

        $this->warn("Found {$stuckRestores->count()} stuck restore record(s):");
        $this->newLine();

        foreach ($stuckRestores as $restore) {
            $duration = $restore->started_at->diffForHumans(now(), true);
            $this->line("  - Restore ID: {$restore->id}");
            $this->line("    Backup ID: {$restore->backup_id}");
            $this->line("    Status: {$restore->status}");
            $this->line("    Started: {$restore->started_at->format('Y-m-d H:i:s')}");
            $this->line("    Duration: {$duration}");
            $this->line("    Type: {$restore->restore_type}");
            $this->newLine();

            if ($fix) {
                try {
                    $restore->update([
                        'status' => 'failed',
                        'error_message' => "Restore operation timed out after {$minutes} minutes (marked as stuck by cleanup command)",
                        'completed_at' => now(),
                    ]);

                    $this->info("  ✓ Fixed restore record ID: {$restore->id}");
                    
                    Log::info("Fixed stuck restore record", [
                        'restore_id' => $restore->id,
                        'backup_id' => $restore->backup_id,
                        'duration_minutes' => $minutes
                    ]);
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to fix restore record ID: {$restore->id} - {$e->getMessage()}");
                    
                    Log::error("Failed to fix stuck restore record", [
                        'restore_id' => $restore->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        if (!$fix) {
            $this->info("To automatically fix these stuck restore records, run:");
            $this->line("  php artisan restore:check-stuck --fix");
        }

        return 0;
    }
}

