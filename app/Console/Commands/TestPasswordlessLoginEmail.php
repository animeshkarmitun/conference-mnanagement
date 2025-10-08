<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Conference;
use App\Services\PasswordlessLoginService;
use App\Services\EmailTrackingService;

class TestPasswordlessLoginEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:passwordless-login-email {email : Email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test passwordless login email sending with HTML content';

    protected $passwordlessLoginService;
    protected $emailTrackingService;

    public function __construct(PasswordlessLoginService $passwordlessLoginService, EmailTrackingService $emailTrackingService)
    {
        parent::__construct();
        $this->passwordlessLoginService = $passwordlessLoginService;
        $this->emailTrackingService = $emailTrackingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing passwordless login email to: ' . $email);
        $this->line('');
        
        // Find or create a test user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->warn('User not found. Creating a test user...');
            $user = User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => $email,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }
        
        // Get the latest conference
        $conference = Conference::latest()->first();
        if (!$conference) {
            $this->error('No conference found. Please create a conference first.');
            return 1;
        }
        
        try {
            // Generate passwordless login link
            $this->info('Generating passwordless login link...');
            $passwordlessLogin = $this->passwordlessLoginService->generateLoginLink($user, 24);
            
            // Send the email
            $this->info('Sending passwordless login email...');
            $emailSent = $this->passwordlessLoginService->sendLoginEmail($user, $passwordlessLogin, $conference);
            
            if ($emailSent) {
                $this->info('✓ Passwordless login email sent successfully!');
                $this->line('');
                $this->info('Login URL: ' . $passwordlessLogin->getLoginUrl());
                $this->info('Expires at: ' . $passwordlessLogin->expires_at);
                $this->line('');
                $this->info('The email should now display as HTML instead of showing raw HTML code.');
            } else {
                $this->error('✗ Failed to send passwordless login email');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
