<?php

namespace App\Listeners;

use App\Events\TravelEvent;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTravelEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
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
        // For now, we'll log the email notification since the email system is not fully implemented
        // This can be replaced with actual email sending when the email system is ready
        
        $emailData = [
            'to' => $user->email,
            'subject' => $this->getEmailSubject($event),
            'message' => $this->getEmailMessage($user, $event),
            'participant_name' => $event->participant->user->first_name . ' ' . $event->participant->user->last_name,
            'event_type' => $event->eventType,
            'conference_name' => $event->participant->conference->name ?? 'Conference'
        ];

        // Log the email notification for now
        Log::info('Travel email notification would be sent', $emailData);

        // TODO: Uncomment when email system is implemented
        // Mail::to($user->email)->send(new TravelNotificationMail($emailData));
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
