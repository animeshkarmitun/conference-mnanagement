<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\GoogleService;
use Illuminate\Support\Facades\Log;

class TestGmailAPICredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:gmail-api-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Gmail API credentials and token validity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Gmail API credentials...');
        
        // Check if we have Google credentials in .env
        $clientId = config('google.client_id');
        $clientSecret = config('google.client_secret');
        
        if (!$clientId || !$clientSecret) {
            $this->error('Google credentials not found in .env file');
            return 1;
        }
        
        $this->info('✓ Google credentials found in .env');
        $this->info('Client ID: ' . substr($clientId, 0, 20) . '...');
        
        // Check if we have a user with Google token
        $user = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'superadmin']);
        })->whereNotNull('google_token')->first();
        
        if (!$user) {
            $this->error('No admin user with Google token found');
            return 1;
        }
        
        $this->info('✓ Found admin user with Google token: ' . $user->email);
        
        // Test the Google service
        try {
            $googleService = new GoogleService();
            $googleService->setAccessToken(json_decode($user->google_token, true));
            
            // Test token validity by trying to list threads
            $this->info('Testing Gmail API token validity...');
            $threads = $googleService->listThreads('me', 1);
            $this->info('✓ Gmail API token is valid');
            $this->info('Found ' . count($threads['threads']) . ' email threads');
            
            // Test sending a simple email
            $this->info('Testing email sending...');
            $testEmail = 'test@example.com';
            $subject = 'Test Email from Conference Management System';
            $body = 'This is a test email to verify Gmail API functionality.';
            
            $result = $googleService->sendEmail($testEmail, $subject, $body);
            $this->info('✓ Test email sent successfully');
            $this->info('Message ID: ' . $result->getId());
            
        } catch (\Exception $e) {
            $this->error('Gmail API test failed: ' . $e->getMessage());
            Log::error('Gmail API test failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            return 1;
        }
        
        $this->info('✓ All Gmail API tests passed!');
        return 0;
    }
}
