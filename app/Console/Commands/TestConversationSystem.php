<?php

namespace App\Console\Commands;

use App\Services\EmailTrackingService;
use App\Models\Email;
use App\Models\User;
use App\Models\Participant;
use App\Models\Conference;
use Illuminate\Console\Command;

class TestConversationSystem extends Command
{
    protected $signature = 'test:conversation-system {participant_email}';
    protected $description = 'Test the complete conversation system with a real participant email';

    public function handle()
    {
        $participantEmail = $this->argument('participant_email');
        
        $this->info("🧪 Testing Conversation System for: {$participantEmail}");
        $this->newLine();
        
        // Check if participant exists
        $user = User::where('email', $participantEmail)->first();
        if (!$user) {
            $this->error("❌ Participant not found: {$participantEmail}");
            $this->info("💡 Please create the participant first or use an existing email.");
            return 1;
        }
        
        $participant = Participant::where('user_id', $user->id)->first();
        if (!$participant) {
            $this->error("❌ No participant record found for: {$participantEmail}");
            return 1;
        }
        
        $conference = $participant->conference;
        $admin = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        })->first();
        
        $this->info("✅ Found participant: {$user->first_name} {$user->last_name}");
        $this->info("✅ Conference: {$conference->name}");
        $this->info("✅ Admin: {$admin->email}");
        $this->newLine();
        
        // Clear existing test emails for this participant
        Email::where(function($q) use ($participantEmail) {
            $q->where('recipient_email', $participantEmail)
              ->orWhere('sender_email', $participantEmail);
        })->where('metadata->test', true)->delete();
        
        $this->info("🧹 Cleared existing test emails");
        
        // Create test conversation
        $this->createTestConversation($user, $participant, $conference, $admin, $participantEmail);
        
        // Test conversation retrieval
        $this->testConversationRetrieval($participantEmail, $conference->id);
        
        // Test statistics
        $this->testConversationStatistics($participantEmail, $conference->id);
        
        $this->newLine();
        $this->info("🎉 Conversation system test completed successfully!");
        $this->info("🌐 You can now view this conversation in the Email Tracking Dashboard:");
        $this->info("   → Go to /admin/email-tracking");
        $this->info("   → Switch to 'Conversations' view");
        $this->info("   → Select '{$participantEmail}' from the dropdown");
        
        return 0;
    }
    
    private function createTestConversation($user, $participant, $conference, $admin, $participantEmail)
    {
        $this->info("📧 Creating test conversation...");
        
        $emails = [
            [
                'direction' => 'outgoing',
                'subject' => 'Welcome to ' . $conference->name,
                'body' => $this->getWelcomeEmailBody($user, $conference),
                'email_type' => 'conference_update',
                'sent_at' => now()->subHours(3)
            ],
            [
                'direction' => 'incoming',
                'subject' => 'Re: Welcome to ' . $conference->name,
                'body' => $this->getParticipantReplyBody($user),
                'email_type' => 'general',
                'received_at' => now()->subHours(2)
            ],
            [
                'direction' => 'outgoing',
                'subject' => 'Re: Welcome to ' . $conference->name,
                'body' => $this->getAdminReplyBody($user),
                'email_type' => 'general',
                'sent_at' => now()->subHour()
            ],
            [
                'direction' => 'incoming',
                'subject' => 'Question about conference schedule',
                'body' => $this->getScheduleQuestionBody($user),
                'email_type' => 'general',
                'received_at' => now()->subMinutes(30)
            ],
            [
                'direction' => 'outgoing',
                'subject' => 'Re: Question about conference schedule',
                'body' => $this->getScheduleAnswerBody($user, $conference),
                'email_type' => 'general',
                'sent_at' => now()->subMinutes(15)
            ]
        ];
        
        foreach ($emails as $index => $emailData) {
            $email = Email::create([
                'user_id' => $emailData['direction'] === 'outgoing' ? $admin->id : null,
                'recipient_email' => $emailData['direction'] === 'outgoing' ? $participantEmail : $admin->email,
                'recipient_name' => $emailData['direction'] === 'outgoing' ? $user->first_name . ' ' . $user->last_name : 'Admin',
                'sender_email' => $emailData['direction'] === 'outgoing' ? $admin->email : $participantEmail,
                'sender_name' => $emailData['direction'] === 'outgoing' ? 'Admin' : $user->first_name . ' ' . $user->last_name,
                'subject' => $emailData['subject'],
                'body' => $emailData['body'],
                'status' => $emailData['direction'] === 'outgoing' ? 'sent' : 'delivered',
                'email_type' => $emailData['email_type'],
                'direction' => $emailData['direction'],
                'conference_id' => $conference->id,
                'sent_at' => $emailData['direction'] === 'outgoing' ? $emailData['sent_at'] : null,
                'received_at' => $emailData['direction'] === 'incoming' ? $emailData['received_at'] : null,
                'metadata' => ['test' => true, 'test_index' => $index + 1]
            ]);
            
            $direction = $emailData['direction'] === 'outgoing' ? 'OUT' : 'IN';
            $this->line("  ✓ [{$direction}] {$emailData['subject']}");
        }
        
        $this->info("✅ Created 5 test emails");
    }
    
    private function testConversationRetrieval($participantEmail, $conferenceId)
    {
        $this->newLine();
        $this->info("🔍 Testing conversation retrieval...");
        
        $emailTrackingService = app(EmailTrackingService::class);
        $conversation = $emailTrackingService->getParticipantConversation($participantEmail, $conferenceId);
        
        $this->info("✅ Retrieved {$conversation->count()} emails in conversation");
        
        $this->line("📋 Conversation preview:");
        foreach ($conversation as $email) {
            $direction = $email->direction === 'outgoing' ? 'OUT' : 'IN';
            $sender = $email->direction === 'outgoing' ? 'Admin' : 'Participant';
            $time = $email->created_at->format('M j, H:i');
            $this->line("  [{$direction}] {$sender}: {$email->subject} ({$time})");
        }
    }
    
    private function testConversationStatistics($participantEmail, $conferenceId)
    {
        $this->newLine();
        $this->info("📊 Testing conversation statistics...");
        
        $emailTrackingService = app(EmailTrackingService::class);
        $stats = $emailTrackingService->getParticipantConversationStats($participantEmail, $conferenceId);
        
        $this->info("✅ Conversation Statistics:");
        $this->line("  • Total emails: {$stats['total_emails']}");
        $this->line("  • Outgoing: {$stats['outgoing_emails']}");
        $this->line("  • Incoming: {$stats['incoming_emails']}");
        $this->line("  • Sent: " . ($stats['sent_emails'] ?? 0));
        $this->line("  • Delivered: " . ($stats['delivered_emails'] ?? 0));
    }
    
    private function getWelcomeEmailBody($user, $conference)
    {
        return "Dear {$user->first_name},

Welcome to {$conference->name}! We are excited to have you as a participant.

Conference Details:
- Date: " . ($conference->start_date ? \Carbon\Carbon::parse($conference->start_date)->format('F j, Y') : 'TBD') . "
- Location: " . ($conference->venue ? $conference->venue->name : 'TBD') . "
- Duration: Full day event

Please let us know if you have any questions or special requirements.

Best regards,
Conference Management Team";
    }
    
    private function getParticipantReplyBody($user)
    {
        return "Hi Admin,

Thank you for the welcome email! I am very excited to participate in the conference.

I have a few questions:
1. What time does the conference start?
2. Is there parking available at the venue?
3. Will there be lunch provided?
4. Do I need to bring anything specific?

Looking forward to the event!

Best regards,
{$user->first_name}";
    }
    
    private function getAdminReplyBody($user)
    {
        return "Hi {$user->first_name},

Thank you for your questions! Here are the answers:

1. The conference starts at 9:00 AM
2. Yes, there is free parking available at the venue
3. Yes, lunch and refreshments will be provided throughout the day
4. Just bring yourself and any business cards you'd like to exchange

If you have any other questions, feel free to ask!

Best regards,
Conference Team";
    }
    
    private function getScheduleQuestionBody($user)
    {
        return "Hi Admin,

I was wondering about the conference schedule. Could you please send me the detailed agenda for the day?

I'm particularly interested in:
- Keynote speaker sessions
- Breakout sessions
- Networking opportunities

Thanks!
{$user->first_name}";
    }
    
    private function getScheduleAnswerBody($user, $conference)
    {
        return "Hi {$user->first_name},

Great question! Here's the detailed schedule for {$conference->name}:

9:00 AM - Registration & Welcome Coffee
9:30 AM - Opening Keynote
10:30 AM - Breakout Session 1
11:30 AM - Coffee Break
12:00 PM - Breakout Session 2
1:00 PM - Lunch & Networking
2:00 PM - Afternoon Keynote
3:00 PM - Panel Discussion
4:00 PM - Closing Remarks
4:30 PM - Networking Reception

The detailed agenda will be available at the registration desk.

Best regards,
Conference Team";
    }
}
