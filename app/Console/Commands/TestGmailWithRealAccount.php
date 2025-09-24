<?php

namespace App\Console\Commands;

use App\Services\EmailTrackingService;
use App\Models\User;
use Illuminate\Console\Command;

class TestGmailWithRealAccount extends Command
{
    protected $signature = 'test:gmail-real {participant_email}';
    protected $description = 'Test Gmail integration with real Gmail account (conferencescgs@gmail.com)';

    public function handle()
    {
        $participantEmail = $this->argument('participant_email');
        
        $this->info("🧪 Testing Gmail Integration with Real Account");
        $this->info("Admin Email: conferencescgs@gmail.com");
        $this->info("Participant Email: {$participantEmail}");
        $this->newLine();
        
        // Get admin user
        $adminUser = User::where('email', 'conferencescgs@gmail.com')->first();
        if (!$adminUser) {
            $this->error("❌ Admin user not found: conferencescgs@gmail.com");
            $this->info("💡 Run: php setup_admin.php first");
            return 1;
        }
        
        $this->info("✅ Found admin user: {$adminUser->first_name} {$adminUser->last_name}");
        
        // Check if Gmail is connected
        if (!$adminUser->google_token) {
            $this->warn("⚠️  Gmail not connected yet");
            $this->info("📋 To connect Gmail:");
            $this->info("   1. Login to the system with: conferencescgs@gmail.com");
            $this->info("   2. Go to: /google/redirect");
            $this->info("   3. Authorize Gmail access");
            $this->info("   4. Run this command again");
            $this->newLine();
            
            // Test without Gmail (database only)
            $this->testDatabaseOnly($adminUser, $participantEmail);
            return 0;
        }
        
        $this->info("✅ Gmail connected!");
        
        // Test with Gmail
        $this->testWithGmail($adminUser, $participantEmail);
        
        return 0;
    }
    
    private function testDatabaseOnly($adminUser, $participantEmail)
    {
        $this->info("📧 Testing database-only email tracking...");
        
        $emailTrackingService = app(EmailTrackingService::class);
        
        try {
            // Test sending email (will fail without Gmail, but we can test the tracking)
            $email = $emailTrackingService->sendTrackedEmailViaGmail(
                $participantEmail,
                'Test Email from CGS Conference System',
                'This is a test email from the CGS Conference Management System.

This email was sent via Gmail API integration.

Best regards,
CGS Conference Team',
                'general',
                $adminUser
            );
            
            $this->error("❌ This should have failed without Gmail connection");
            
        } catch (\Exception $e) {
            $this->info("✅ Expected error (Gmail not connected): " . $e->getMessage());
        }
        
        // Test conversation retrieval
        $this->info("🔍 Testing conversation retrieval...");
        $conversation = $emailTrackingService->getParticipantConversation($participantEmail);
        $stats = $emailTrackingService->getParticipantConversationStats($participantEmail);
        
        $this->info("✅ Conversation found:");
        $this->line("  • Total emails: {$stats['total_emails']}");
        $this->line("  • Outgoing: {$stats['outgoing_emails']}");
        $this->line("  • Incoming: {$stats['incoming_emails']}");
        
        if ($conversation->count() > 0) {
            $this->line("📋 Recent emails:");
            foreach ($conversation->take(3) as $email) {
                $direction = $email->direction === 'outgoing' ? 'OUT' : 'IN';
                $sender = $email->direction === 'outgoing' ? 'Admin' : 'Participant';
                $this->line("  [{$direction}] {$sender}: {$email->subject}");
            }
        }
    }
    
    private function testWithGmail($adminUser, $participantEmail)
    {
        $this->info("📧 Testing with Gmail API...");
        
        $emailTrackingService = app(EmailTrackingService::class);
        
        try {
            // Send test email via Gmail
            $email = $emailTrackingService->sendTrackedEmailViaGmail(
                $participantEmail,
                'Test Email from CGS Conference System',
                'This is a test email from the CGS Conference Management System.

This email was sent via Gmail API integration.

Best regards,
CGS Conference Team',
                'general',
                $adminUser
            );
            
            $this->info("✅ Email sent successfully via Gmail!");
            $this->line("  • Email ID: {$email->id}");
            $this->line("  • Gmail Message ID: {$email->message_id}");
            $this->line("  • Gmail Thread ID: {$email->thread_id}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email via Gmail: " . $e->getMessage());
            return;
        }
        
        // Test conversation retrieval
        $this->info("🔍 Testing conversation retrieval...");
        $conversation = $emailTrackingService->getParticipantConversation($participantEmail);
        $stats = $emailTrackingService->getParticipantConversationStats($participantEmail);
        
        $this->info("✅ Updated conversation stats:");
        $this->line("  • Total emails: {$stats['total_emails']}");
        $this->line("  • Outgoing: {$stats['outgoing_emails']}");
        $this->line("  • Incoming: {$stats['incoming_emails']}");
        
        $this->newLine();
        $this->info("🎉 Gmail integration test completed!");
        $this->info("📧 Check your Gmail inbox for the sent email");
        $this->info("🌐 View conversation at: /admin/email-tracking");
    }
}








