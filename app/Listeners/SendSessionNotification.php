<?php

namespace App\Listeners;

use App\Events\SessionEvent;
use App\Models\Notification;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSessionNotification implements ShouldQueue
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
    public function handle(SessionEvent $event): void
    {
        try {
            // Get participants who should receive session notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->createNotification($user, $event);
            }

            Log::info('Session notification sent', [
                'event_type' => $event->eventType,
                'session_id' => $event->session->id,
                'users_notified' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send session notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'session_id' => $event->session->id
            ]);
        }
    }

    /**
     * Get users who should receive session notifications
     */
    private function getUsersToNotify(SessionEvent $event): array
    {
        $users = [];

        // For session removal, only notify the specific participant who was removed
        if ($event->eventType === 'session_removed' && isset($event->changes['participant_id'])) {
            $participant = Participant::with('user')->find($event->changes['participant_id']);
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
            $participant = Participant::with('user')->find($event->changes['participant_id']);
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
        // For other events, also notify participants who are part of the conference but not specifically assigned to this session
        elseif (!in_array($event->eventType, ['session_assigned', 'session_removed'])) {
            $conferenceParticipants = Participant::where('conference_id', $event->conferenceId)
                ->with('user')
                ->get();
                
            foreach ($conferenceParticipants as $participant) {
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
     * Create notification for a specific user
     */
    private function createNotification(User $user, SessionEvent $event): void
    {
        // Don't create duplicate notifications for the same event
        $existingNotification = Notification::where([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'type' => 'SessionUpdate'
        ])
        ->where('message', 'LIKE', '%' . $event->session->title . '%')
        ->where('message', 'LIKE', '%' . $event->eventType . '%')
        ->where('created_at', '>=', now()->subMinutes(5))
        ->first();

        if ($existingNotification) {
            return;
        }

        Notification::create([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'message' => $event->message,
            'type' => 'SessionUpdate',
            'related_model' => 'Session',
            'related_id' => $event->session->id,
            'action_url' => route('sessions.show', $event->session->id),
            'sent_at' => now(),
            'read_status' => false,
        ]);
    }
}
