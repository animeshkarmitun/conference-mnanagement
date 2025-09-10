<?php

namespace App\Console\Commands;

use App\Services\PasswordlessLoginService;
use Illuminate\Console\Command;

class CleanupExpiredPasswordlessLogins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwordless-login:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired passwordless login tokens';

    /**
     * Execute the console command.
     */
    public function handle(PasswordlessLoginService $passwordlessLoginService)
    {
        $this->info('Starting cleanup of expired passwordless login tokens...');
        
        $deletedCount = $passwordlessLoginService->cleanupExpiredTokens();
        
        $this->info("Cleanup completed. Deleted {$deletedCount} expired tokens.");
        
        return Command::SUCCESS;
    }
}
