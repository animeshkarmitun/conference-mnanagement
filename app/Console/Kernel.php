<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Schedule daily backup creation at 2:00 AM
        $schedule->command('backup:create --type=full')
            ->dailyAt('02:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/backup.log'));
        
        // Schedule daily cleanup to keep last 30 days of backups at 3:00 AM
        $schedule->command('backup:cleanup --days=30')
            ->dailyAt('03:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/backup-cleanup.log'));
        
        // Schedule cleanup of stuck restore records every hour
        $schedule->call(function () {
            $timeoutMinutes = 30;
            $stuckRestores = \App\Models\RestoreRecord::where('status', 'in_progress')
                ->where('started_at', '<', now()->subMinutes($timeoutMinutes))
                ->get();

            foreach ($stuckRestores as $restore) {
                $restore->update([
                    'status' => 'failed',
                    'error_message' => 'Restore operation timed out after ' . $timeoutMinutes . ' minutes',
                    'completed_at' => now(),
                ]);
            }
        })->hourly()->appendOutputTo(storage_path('logs/restore-cleanup.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
