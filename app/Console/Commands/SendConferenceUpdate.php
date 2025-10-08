<?php

namespace App\Console\Commands;

use App\Services\EmailTrackingService;
use App\Services\EmailTemplateService;
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
        $emailTemplateService = app(EmailTemplateService::class);
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($participants as $participant) {
            try {
                // Prepare variables for template
                $variables = [
                    'first_name' => $participant->user->first_name ?? 'User',
                    'last_name' => $participant->user->last_name ?? '',
                    'conference_name' => $conference->name,
                    'custom_message' => $message,
                ];

                // Get template from service
                $template = $emailTemplateService->processTemplate(
                    \App\Models\Email::TYPE_CONFERENCE_UPDATE,
                    $variables
                );

                // Build full email body with custom message
                $emailBody = $this->buildEmailBody($template, $conference, $participant, $message);
                
                $email = $emailTrackingService->sendTrackedEmailViaGmail(
                    $participant->user->email,
                    $template['subject'],
                    $emailBody,
                    \App\Models\Email::TYPE_CONFERENCE_UPDATE,
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
    
    private function buildEmailBody($template, $conference, $participant, $message)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
            <div style='background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <p>{$template['greeting']}</p>
                
                <div style='margin: 20px 0;'>
                    <div style='background-color: #f0f9ff; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #3b82f6;'>
                        <h3 style='color: #1e40af; margin: 0 0 10px 0; font-size: 18px;'>Conference Update: {$conference->name}</h3>
                        <div style='color: #374151; line-height: 1.6; white-space: pre-line;'>{$message}</div>
                    </div>
                    
                    <div style='background-color: #f8fafc; padding: 20px; border-radius: 6px; margin: 20px 0;'>
                        <h3 style='color: #1f2937; margin: 0 0 15px 0; font-size: 16px;'>Conference Details:</h3>
                        <ul style='color: #374151; line-height: 1.6; padding-left: 20px; margin: 0;'>
                            <li><strong>Conference:</strong> {$conference->name}</li>
                            <li><strong>Start Date:</strong> " . ($conference->start_date ? $conference->start_date->format('F j, Y') : 'TBD') . "</li>
                            <li><strong>End Date:</strong> " . ($conference->end_date ? $conference->end_date->format('F j, Y') : 'TBD') . "</li>
                            <li><strong>Location:</strong> " . ($conference->venue ? $conference->venue->name : 'TBD') . "</li>
                        </ul>
                    </div>
                </div>
                
                <p style='margin: 20px 0;'>{$template['closing']}</p>
                <p style='margin: 20px 0;'>{$template['signature']}</p>
                
                <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                    This is an automated notification from the Conference Management System.
                </p>
            </div>
        </div>
        ";
    }
}




















