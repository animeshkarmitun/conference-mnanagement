<?php

namespace App\Listeners;

use App\Events\SessionEvent;
use App\Models\User;
use App\Models\Role;
use App\Services\EmailTrackingService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSessionEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected EmailTrackingService $emailTrackingService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailTrackingService $emailTrackingService)
    {
        $this->emailTrackingService = $emailTrackingService;
    }

    /**
     * Handle the event.
     */
    public function handle(SessionEvent $event): void
    {
        try {
            // Get users who should receive email notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->sendEmailNotification($user, $event);
            }

            Log::info('Session email notification sent', [
                'event_type' => $event->eventType,
                'session_id' => $event->session->id,
                'emails_sent' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send session email notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'session_id' => $event->session->id
            ]);
        }
    }

    /**
     * Get users who should receive email notifications
     */
    private function getUsersToNotify(SessionEvent $event): array
    {
        $users = [];

        // For session removal, only notify the specific participant who was removed
        if ($event->eventType === 'session_removed' && isset($event->changes['participant_id'])) {
            $participant = \App\Models\Participant::with('user')->find($event->changes['participant_id']);
            if ($participant && $participant->user) {
                $users[] = $participant->user;
            }
            return $users;
        }

        // For other session events, get participants assigned to this session
        $participants = $event->session->participants()->with('user')->get();
        
        foreach ($participants as $participant) {
            if ($participant->user) {
                $users[] = $participant->user;
            }
        }

        // For session assignment, also notify the specific participant who was assigned
        if ($event->eventType === 'session_assigned' && isset($event->changes['participant_id'])) {
            $participant = \App\Models\Participant::with('user')->find($event->changes['participant_id']);
            if ($participant && $participant->user && !in_array($participant->user->id, array_column($users, 'id'))) {
                $users[] = $participant->user;
            }
        }

        // For session creation, update, and deletion events, notify only participants assigned to this session
        if (in_array($event->eventType, ['session_created', 'session_updated', 'session_deleted'])) {
            // Get only participants who are assigned to this specific session
            $sessionParticipants = $event->session->participants()->with('user')->get();
            
            foreach ($sessionParticipants as $participant) {
                if ($participant->user && !in_array($participant->user->id, array_column($users, 'id'))) {
                    $users[] = $participant->user;
                }
            }
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
    private function sendEmailNotification(User $user, SessionEvent $event): void
    {
        try {
            $subject = $this->getEmailSubject($event);
            $body = $this->getEmailMessage($user, $event);
            
            // Send tracked email
            $this->emailTrackingService->sendTrackedEmailViaGmail(
                $user->email,
                $subject,
                $body,
                \App\Models\Email::TYPE_SESSION_NOTIFICATION,
                auth()->user(), // Sender
                $event->session->conference,
                'session',
                $event->session->id,
                'session-notification',
                [
                    'session_title' => $event->session->title,
                    'event_type' => $event->eventType,
                    'conference_name' => $event->session->conference->name ?? 'Conference'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to send session email notification', [
                'user_id' => $user->id,
                'session_id' => $event->session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get email subject based on event type
     */
    private function getEmailSubject(SessionEvent $event): string
    {
        $conferenceName = $event->session->conference->name ?? 'Conference';
        
        switch ($event->eventType) {
            case 'session_assigned':
                return "You've Been Assigned to a Session - {$conferenceName}";
            case 'session_removed':
                return "Session Assignment Removed - {$conferenceName}";
            case 'session_created':
                return "New Session Added - {$conferenceName}";
            case 'session_updated':
                return "Session Information Updated - {$conferenceName}";
            case 'session_deleted':
                return "Session Deleted - {$conferenceName}";
            case 'session_cancelled':
                return "Session Cancelled - {$conferenceName}";
            case 'session_rescheduled':
                return "Session Rescheduled - {$conferenceName}";
            default:
                return "Session Update - {$conferenceName}";
        }
    }

    /**
     * Get email message based on event type and user
     */
    private function getEmailMessage(User $user, SessionEvent $event): string
    {
        $sessionTitle = $event->session->title;
        $conferenceName = $event->session->conference->name ?? 'Conference';
        $sessionDate = $event->session->start_time ? $event->session->start_time->format('M d, Y \a\t g:i A') : 'TBD';
        $sessionLocation = $event->session->venue->name ?? 'TBD';
        
        // Get participant's role in this session
        $participant = $event->session->participants()->where('user_id', $user->id)->first();
        $role = $participant ? ucfirst($participant->pivot->role ?? 'participant') : 'Participant';
        
        switch ($event->eventType) {
            case 'session_created':
                return "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>New Session Added</h2>
                    
                    <p>Dear {$user->first_name} {$user->last_name},</p>
                    
                    <p>A new session has been added to <strong>{$conferenceName}</strong>:</p>
                    
                    <div style='background-color: #f0fdf4; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #22c55e;'>
                        <h3 style='color: #374151; margin-top: 0;'>New Session Details:</h3>
                        <p><strong>Title:</strong> {$sessionTitle}</p>
                        <p><strong>Date & Time:</strong> {$sessionDate}</p>
                        <p><strong>Location:</strong> {$sessionLocation}</p>
                        <p><strong>Description:</strong> " . ($event->session->description ?? 'No description provided') . "</p>
                    </div>
                    
                    <p>Please check the session details and register if you're interested in participating.</p>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('sessions.show', $event->session->id) . "' 
                           style='background-color: #22c55e; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Session Details
                        </a>
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                        Thank you for your participation in {$conferenceName}. We look forward to seeing you at the conference!
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
                ";
                
            case 'session_deleted':
                return "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Session Deleted</h2>
                    
                    <p>Dear {$user->first_name} {$user->last_name},</p>
                    
                    <p>The following session has been removed from <strong>{$conferenceName}</strong>:</p>
                    
                    <div style='background-color: #fef2f2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444;'>
                        <h3 style='color: #374151; margin-top: 0;'>Deleted Session:</h3>
                        <p><strong>Title:</strong> {$sessionTitle}</p>
                        <p><strong>Date & Time:</strong> {$sessionDate}</p>
                        <p><strong>Location:</strong> {$sessionLocation}</p>
                    </div>
                    
                    <p>If you were planning to attend this session, please check the updated conference schedule for alternative sessions.</p>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                        We apologize for any inconvenience this may cause. Please contact our team if you have any questions.
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
                ";
                
            case 'session_assigned':
                return "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Session Assignment Notification</h2>
                    
                    <p>Dear {$user->first_name} {$user->last_name},</p>
                    
                    <p>You have been assigned to a session for the <strong>{$conferenceName}</strong> conference.</p>
                    
                    <div style='background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b;'>
                        <h3 style='color: #374151; margin-top: 0;'>Session Details:</h3>
                        <p><strong>Title:</strong> {$sessionTitle}</p>
                        <p><strong>Date & Time:</strong> {$sessionDate}</p>
                        <p><strong>Location:</strong> {$sessionLocation}</p>
                        <p><strong>Your Role:</strong> {$role}</p>
                    </div>
                    
                    <p>Please review the session details and prepare accordingly. If you have any questions or concerns about this assignment, please contact our team.</p>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('sessions.show', $event->session->id) . "' 
                           style='background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Session Details
                        </a>
                    </div>
                    
                    <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                        Thank you for your participation in {$conferenceName}. We look forward to seeing you at the session!
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
                ";
                
            case 'session_removed':
                return "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Session Assignment Removed</h2>
                    
                    <p>Dear {$user->first_name} {$user->last_name},</p>
                    
                    <p>Your assignment to the following session for <strong>{$conferenceName}</strong> has been removed:</p>
                    
                    <div style='background-color: #fef2f2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444;'>
                        <h3 style='color: #374151; margin-top: 0;'>Session Details:</h3>
                        <p><strong>Title:</strong> {$sessionTitle}</p>
                        <p><strong>Date & Time:</strong> {$sessionDate}</p>
                        <p><strong>Location:</strong> {$sessionLocation}</p>
                    </div>
                    
                    <p>If you believe this is an error or have any questions, please contact our team immediately.</p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
                ";
                
            case 'session_updated':
                return "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Session Information Updated</h2>
                    
                    <p>Dear {$user->first_name} {$user->last_name},</p>
                    
                    <p>The following session for <strong>{$conferenceName}</strong> has been updated:</p>
                    
                    <div style='background-color: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6;'>
                        <h3 style='color: #374151; margin-top: 0;'>Updated Session Details:</h3>
                        <p><strong>Title:</strong> {$sessionTitle}</p>
                        <p><strong>Date & Time:</strong> {$sessionDate}</p>
                        <p><strong>Location:</strong> {$sessionLocation}</p>
                        <p><strong>Your Role:</strong> {$role}</p>
                    </div>
                    
                    <p>Please review the updated information and adjust your plans accordingly.</p>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('sessions.show', $event->session->id) . "' 
                           style='background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Updated Session
                        </a>
                    </div>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
                ";
                
            default:
                return "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #1f2937; margin-bottom: 20px;'>Session Update</h2>
                    
                    <p>Dear {$user->first_name} {$user->last_name},</p>
                    
                    <p>There has been an update regarding your session assignment for <strong>{$conferenceName}</strong>:</p>
                    
                    <div style='background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='color: #374151; margin-top: 0;'>Session Details:</h3>
                        <p><strong>Title:</strong> {$sessionTitle}</p>
                        <p><strong>Date & Time:</strong> {$sessionDate}</p>
                        <p><strong>Location:</strong> {$sessionLocation}</p>
                        <p><strong>Your Role:</strong> {$role}</p>
                    </div>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='" . route('sessions.show', $event->session->id) . "' 
                           style='background-color: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                            View Session Details
                        </a>
                    </div>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                        This is an automated notification from the CGS Events management system.
                    </p>
                </div>
                ";
        }
    }
}
