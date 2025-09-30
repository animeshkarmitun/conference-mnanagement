<?php

namespace App\Console\Commands;

use App\Services\EmailTrackingService;
use App\Services\GmailIncomingEmailService;
use App\Models\User;
use Illuminate\Console\Command;

class TestGmailIntegration extends Command
{
    protected $signature = 'test:gmail-integration {participant_email}';
    protected $description = 'Test Gmail integration by sending an email and syncing Gmail messages';

    public function handle()
    {
        $participantEmail = $this->argument('participant_email');
        
        $this->info("Testing Gmail integration for participant: {$participantEmail}");
        
        // Check if admin user has Gmail token
        $adminUser = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'superadmin']);
        })->whereNotNull('google_token')->first();
        
        if (!$adminUser) {
            $this->error('No admin user with Gmail token found. Please connect Gmail first.');
            return 1;
        }
        
        $this->info('Found admin user with Gmail token: ' . $adminUser->email);
        
        // Test sending email via Gmail
        try {
            $emailTrackingService = app(EmailTrackingService::class);
            
            $email = $emailTrackingService->sendTrackedEmailViaGmail(
                $participantEmail,
                'Test Email from Conference Management System',
                'This is a test email sent via Gmail API integration.',
                'general',
                $adminUser
            );
            
            $this->info("Email sent successfully! Email ID: {$email->id}");
            $this->info("Gmail Message ID: {$email->message_id}");
            $this->info("Gmail Thread ID: {$email->thread_id}");
            
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }
        
        // Test syncing Gmail messages
        try {
            $gmailIncomingService = app(GmailIncomingEmailService::class);
            
            $syncedCount = $gmailIncomingService->syncParticipantGmailMessages($participantEmail);
            
            $this->info("Synced {$syncedCount} Gmail messages for participant");
            
        } catch (\Exception $e) {
            $this->error("Failed to sync Gmail messages: " . $e->getMessage());
            return 1;
        }
        
        // Show conversation
        try {
            $conversation = $emailTrackingService->getParticipantConversation($participantEmail);
            $stats = $emailTrackingService->getParticipantConversationStats($participantEmail);
            
            $this->info("Conversation stats:");
            $this->info("- Total emails: {$stats['total_emails']}");
            $this->info("- Outgoing: {$stats['outgoing_emails']}");
            $this->info("- Incoming: {$stats['incoming_emails']}");
            
            if ($conversation->count() > 0) {
                $this->info("\nRecent emails:");
                foreach ($conversation->take(3) as $email) {
                    $direction = $email->direction === 'outgoing' ? 'OUT' : 'IN';
                    $this->info("- [{$direction}] {$email->subject} - {$email->created_at}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to get conversation: " . $e->getMessage());
            return 1;
        }
        
        $this->info("\nGmail integration test completed successfully!");
        return 0;
    }
}














