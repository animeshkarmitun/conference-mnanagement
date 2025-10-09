<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestGmailConnection extends Command
{
    protected $signature = 'gmail:test-connection {email?}';
    protected $description = 'Test Gmail connection for a user';

    public function handle()
    {
        $email = $this->argument('email') ?? 'conferencescgs@gmail.com';
        
        $this->info("Testing Gmail connection for: {$email}");
        
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }
        
        if (!$user->google_token) {
            $this->warn("No Gmail token found for user. User needs to connect Gmail first.");
            return 0;
        }
        
        $googleService = new GoogleService();
        
        try {
            $token = json_decode($user->google_token, true);
            if (!$token) {
                $this->error("Invalid token format");
                return 1;
            }
            
            $this->info("Testing token validity...");
            $isValid = $googleService->testToken($token);
            
            if ($isValid) {
                $this->info("✅ Gmail token is valid!");
                
                // Test fetching threads
                $this->info("Testing Gmail API call...");
                $googleService->setAccessToken($token);
                $result = $googleService->listThreads('me', 5);
                
                $this->info("✅ Successfully fetched " . count($result['threads']) . " threads");
                
            } else {
                $this->error("❌ Gmail token is invalid or expired");
                
                // Try to refresh
                $this->info("Attempting to refresh token...");
                try {
                    $newToken = $googleService->refreshTokenIfNeeded($token);
                    $user->google_token = json_encode($newToken);
                    $user->save();
                    $this->info("✅ Token refreshed successfully!");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to refresh token: " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Gmail connection test failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
