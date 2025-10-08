<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailTrackingService;
use App\Models\Email;

class TestEmailTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email tracking functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing email tracking...');
        
        $service = app(EmailTrackingService::class);
        
        try {
            // Test tracking without actually sending email
            $email = $service->trackEmail(
                'test@example.com',
                'Test Email Subject',
                'This is a test email for tracking functionality.',
                Email::TYPE_GENERAL,
                null, // sender
                null, // conference
                'test',
                1,
                'test-template',
                ['test' => 'data', 'timestamp' => now()]
            );
            
            // Mark as sent manually for testing
            $service->markAsSent($email, 'test-message-id');
            
            $this->info("✅ Email tracking test successful!");
            $this->info("Email ID: {$email->id}");
            $this->info("Status: {$email->status}");
            $this->info("Recipient: {$email->recipient_email}");
            $this->info("Subject: {$email->subject}");
            
            // Test statistics
            $stats = $service->getEmailStats();
            $this->info("\n📊 Email Statistics:");
            $this->info("Total emails: {$stats['total']}");
            $this->info("Sent: {$stats['sent']}");
            $this->info("Delivered: {$stats['delivered']}");
            $this->info("Opened: {$stats['opened']}");
            
        } catch (\Exception $e) {
            $this->error("❌ Email tracking test failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}