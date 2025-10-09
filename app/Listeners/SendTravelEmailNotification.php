<?php

namespace App\Listeners;

use App\Events\TravelEvent;
use App\Models\User;
use App\Models\Role;
use App\Services\EmailTrackingService;
use App\Services\EmailTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTravelEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected EmailTrackingService $emailTrackingService;
    protected EmailTemplateService $emailTemplateService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailTrackingService $emailTrackingService, EmailTemplateService $emailTemplateService)
    {
        $this->emailTrackingService = $emailTrackingService;
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Handle the event.
     */
    public function handle(TravelEvent $event): void
    {
        try {
            // Get users who should receive email notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->sendEmailNotification($user, $event);
            }

            Log::info('Travel email notification sent', [
                'event_type' => $event->eventType,
                'participant_id' => $event->participant->id,
                'emails_sent' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send travel email notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'participant_id' => $event->participant->id
            ]);
        }
    }

    /**
     * Get users who should receive email notifications
     */
    private function getUsersToNotify(TravelEvent $event): array
    {
        $users = [];

        // Always notify the participant via email
        $users[] = $event->participant->user;

        // Get admin and superadmin users for email notifications
        $adminRoleIds = Role::whereIn('name', ['admin', 'superadmin'])->pluck('id');
        $adminUsers = User::whereHas('roles', function ($query) use ($adminRoleIds) {
            $query->whereIn('role_id', $adminRoleIds);
        })->get();

        $users = array_merge($users, $adminUsers->toArray());

        // Get event coordinator users
        $eventCoordinatorRole = Role::where('name', 'event_coordinator')->first();
        if ($eventCoordinatorRole) {
            $eventCoordinators = User::whereHas('roles', function ($query) use ($eventCoordinatorRole) {
                $query->where('role_id', $eventCoordinatorRole->id);
            })->get();
            $users = array_merge($users, $eventCoordinators->toArray());
        }

        // Remove duplicates
        $uniqueUsers = [];
        $seenUserIds = [];
        foreach ($users as $user) {
            if (!in_array($user->id, $seenUserIds)) {
                $uniqueUsers[] = $user;
                $seenUserIds[] = $user->id;
            }
        }

        return $uniqueUsers;
    }

    /**
     * Send email notification to a specific user
     */
    private function sendEmailNotification(User $user, TravelEvent $event): void
    {
        try {
            // Prepare variables for template
            $variables = [
                'first_name' => $user->first_name ?? 'User',
                'last_name' => $user->last_name ?? '',
                'conference_name' => $event->participant->conference->name ?? 'Conference',
                'participant_name' => $event->participant->user->first_name . ' ' . $event->participant->user->last_name,
                'travel_type' => $event->participant->travelDetails->travel_type ?? 'Travel',
                'departure_date' => $event->participant->travelDetails->departure_date ? $event->participant->travelDetails->departure_date->format('M d, Y') : 'TBD',
                'return_date' => $event->participant->travelDetails->return_date ? $event->participant->travelDetails->return_date->format('M d, Y') : 'TBD',
                'hotel_name' => $event->participant->travelDetails->hotel_name ?? 'TBD',
                'event_type' => $event->eventType,
            ];

            // Get template from service
            $template = $this->emailTemplateService->processTemplate(
                \App\Models\Email::TYPE_TRAVEL_NOTIFICATION,
                $variables
            );

            // Build full email body
            $fullBody = $this->buildFullEmailBody($template);

            // Send tracked email
            $this->emailTrackingService->sendTrackedEmail(
                $user->email,
                $template['subject'],
                $fullBody,
                \App\Models\Email::TYPE_TRAVEL_NOTIFICATION,
                auth()->user(), // Sender
                $event->participant->conference,
                \App\Models\Participant::class,
                $event->participant->id,
                'travel-notification',
                [
                    'participant_name' => $event->participant->user->first_name . ' ' . $event->participant->user->last_name,
                    'event_type' => $event->eventType,
                    'conference_name' => $event->participant->conference->name ?? 'Conference'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to send travel email notification', [
                'user_id' => $user->id,
                'participant_id' => $event->participant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build full email body from template parts
     */
    private function buildFullEmailBody(array $template): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
            <div style='background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <p>{$template['greeting']}</p>
                
                <div style='margin: 20px 0;'>
                    {$template['body']}
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

    /**
     * Get email subject based on event type
     */
    private function getEmailSubject(TravelEvent $event): string
    {
        $conferenceName = $event->participant->conference->name ?? 'Conference';
        
        switch ($event->eventType) {
            case 'travel_details_updated':
                return "Travel Details Updated - {$conferenceName}";
            case 'room_allocated':
                return "Room Allocation Confirmed - {$conferenceName}";
            case 'travel_conflict_detected':
                return "Travel Conflict Detected - {$conferenceName}";
            case 'travel_documents_uploaded':
                return "Travel Documents Received - {$conferenceName}";
            default:
                return "Travel Update - {$conferenceName}";
        }
    }

    /**
     * Get email message based on event type and user
     */
    private function getEmailMessage(User $user, TravelEvent $event): string
    {
        $participantName = $event->participant->user->first_name . ' ' . $event->participant->user->last_name;
        $conferenceName = $event->participant->conference->name ?? 'Conference';
        
        // Check if user is the participant or an admin
        $isParticipant = $user->id === $event->participant->user_id;
        
        switch ($event->eventType) {
            case 'travel_details_updated':
                if ($isParticipant) {
                    return "Dear {$user->first_name},\n\nYour travel details for {$conferenceName} have been successfully updated. Please review the information and contact us if you need any changes.\n\nBest regards,\nConference Team";
                } else {
                    return "Dear {$user->first_name},\n\nTravel details for participant {$participantName} have been updated for {$conferenceName}.\n\nEvent: {$event->message}\n\nBest regards,\nConference Team";
                }
                
            case 'room_allocated':
                if ($isParticipant) {
                    return "Dear {$user->first_name},\n\nYour room has been allocated for {$conferenceName}. Please check your room details in your participant profile.\n\nBest regards,\nConference Team";
                } else {
                    return "Dear {$user->first_name},\n\nRoom allocation completed for participant {$participantName} for {$conferenceName}.\n\nEvent: {$event->message}\n\nBest regards,\nConference Team";
                }
                
            case 'travel_conflict_detected':
                return "Dear {$user->first_name},\n\nA travel conflict has been detected for {$conferenceName}.\n\nParticipant: {$participantName}\nConflict: {$event->message}\n\nPlease review and resolve this conflict.\n\nBest regards,\nConference Team";
                
            case 'travel_documents_uploaded':
                if ($isParticipant) {
                    return "Dear {$user->first_name},\n\nYour travel documents have been successfully uploaded for {$conferenceName}. We will review them and get back to you soon.\n\nBest regards,\nConference Team";
                } else {
                    return "Dear {$user->first_name},\n\nTravel documents have been uploaded by participant {$participantName} for {$conferenceName}.\n\nEvent: {$event->message}\n\nBest regards,\nConference Team";
                }
                
            default:
                return "Dear {$user->first_name},\n\nA travel update has been made for {$conferenceName}.\n\nEvent: {$event->message}\n\nBest regards,\nConference Team";
        }
    }
}
