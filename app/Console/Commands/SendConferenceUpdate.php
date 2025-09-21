<?php

namespace App\Console\Commands;

use App\Services\EmailTrackingService;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Console\Command;

class SendConferenceUpdate extends Command
{
    protected $signature = 'conference:send-update {conference_id} {subject} {message}';
    protected $description = 'Send conference update to all participants via Gmail';

    public function handle()
    {
        $conferenceId = $this->argument('conference_id');
        $subject = $this->argument('subject');
        $message = $this->argument('message');
        
        $this->info("Sending conference update for conference ID: {$conferenceId}");
        
        // Get conference
        $conference = Conference::find($conferenceId);
        if (!$conference) {
            $this->error("Conference not found with ID: {$conferenceId}");
            return 1;
        }
        
        $this->info("Conference: {$conference->name}");
        
        // Get admin user with Gmail token
        $adminUser = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'superadmin']);
        })->whereNotNull('google_token')->first();
        
        if (!$adminUser) {
            $this->error('No admin user with Gmail token found. Please connect Gmail first.');
            return 1;
        }
        
        // Get all participants for this conference
        $participants = Participant::where('conference_id', $conferenceId)
            ->with('user')
            ->get();
        
        if ($participants->isEmpty()) {
            $this->error('No participants found for this conference.');
            return 1;
        }
        
        $this->info("Found {$participants->count()} participants");
        
        $emailTrackingService = app(EmailTrackingService::class);
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($participants as $participant) {
            try {
                $emailBody = $this->buildEmailBody($conference, $participant, $message);
                
                $email = $emailTrackingService->sendTrackedEmailViaGmail(
                    $participant->user->email,
                    $subject,
                    $emailBody,
                    'conference_update',
                    $adminUser,
                    $conference
                );
                
                $this->info("✓ Sent to: {$participant->user->email}");
                $sentCount++;
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$participant->user->email}: " . $e->getMessage());
                $failedCount++;
            }
        }
        
        $this->info("\nConference update completed!");
        $this->info("Sent: {$sentCount}");
        $this->info("Failed: {$failedCount}");
        
        return 0;
    }
    
    private function buildEmailBody($conference, $participant, $message)
    {
        return "
        <h2>Conference Update: {$conference->name}</h2>
        
        <p>Dear {$participant->user->first_name} {$participant->user->last_name},</p>
        
        <p>{$message}</p>
        
        <h3>Conference Details:</h3>
        <ul>
            <li><strong>Conference:</strong> {$conference->name}</li>
            <li><strong>Start Date:</strong> " . ($conference->start_date ? $conference->start_date->format('F j, Y') : 'TBD') . "</li>
            <li><strong>End Date:</strong> " . ($conference->end_date ? $conference->end_date->format('F j, Y') : 'TBD') . "</li>
            <li><strong>Location:</strong> " . ($conference->venue ? $conference->venue->name : 'TBD') . "</li>
        </ul>
        
        <p>If you have any questions, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        Conference Management Team</p>
        ";
    }
}


