<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FixGmailCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:gmail-credentials {--test : Test email sending after fixing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Help fix Gmail SMTP credentials and test email sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Gmail SMTP Credentials Fix Helper');
        $this->line('');
        
        // Check current configuration
        $this->info('Current Gmail Configuration:');
        $this->line('Host: ' . config('mail.mailers.smtp.host'));
        $this->line('Port: ' . config('mail.mailers.smtp.port'));
        $this->line('Encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->line('Username: ' . config('mail.mailers.smtp.username'));
        $this->line('Password: ' . (config('mail.mailers.smtp.password') ? '***' . substr(config('mail.mailers.smtp.password'), -4) : 'Not set'));
        $this->line('');
        
        // Provide instructions
        $this->warn('Gmail SMTP Authentication Failed!');
        $this->line('');
        $this->info('To fix this issue, you need to:');
        $this->line('');
        $this->line('1. Enable 2-Factor Authentication on your Gmail account');
        $this->line('2. Generate an App Password:');
        $this->line('   - Go to: https://myaccount.google.com/security');
        $this->line('   - Click "2-Step Verification"');
        $this->line('   - Scroll down to "App passwords"');
        $this->line('   - Select "Mail" and generate a password');
        $this->line('');
        $this->line('3. Update your .env file with the 16-character app password:');
        $this->line('   MAIL_PASSWORD=your_16_character_app_password');
        $this->line('');
        $this->line('4. Clear config cache: php artisan config:clear');
        $this->line('');
        
        // Test email if requested
        if ($this->option('test')) {
            $this->info('Testing email sending...');
            $this->testEmailSending();
        }
        
        return 0;
    }
    
    private function testEmailSending()
    {
        try {
            $testEmail = 'test@example.com';
            $subject = 'Test Email from Conference Management System';
            $body = 'This is a test email to verify SMTP functionality.';
            
            Mail::raw($body, function ($message) use ($testEmail, $subject) {
                $message->to($testEmail)->subject($subject);
            });
            
            $this->info('✓ Test email sent successfully!');
            Log::info('Test email sent successfully via SMTP');
            
        } catch (\Exception $e) {
            $this->error('✗ Test email failed: ' . $e->getMessage());
            Log::error('Test email failed', ['error' => $e->getMessage()]);
            
            $this->line('');
            $this->warn('Common issues:');
            $this->line('- Make sure you\'re using an App Password, not your regular Gmail password');
            $this->line('- Ensure 2-Factor Authentication is enabled on your Gmail account');
            $this->line('- Check that the App Password is correctly copied (no spaces)');
            $this->line('- Try regenerating the App Password if it still doesn\'t work');
        }
    }
}
