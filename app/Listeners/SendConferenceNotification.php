<?php

namespace App\Listeners;

use App\Events\ConferenceEvent;
use App\Models\Notification;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendConferenceNotification implements ShouldQueue
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
    public function handle(ConferenceEvent $event): void
    {
        try {
            // Get participants who should receive conference notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->createNotification($user, $event);
            }

            Log::info('Conference notification sent', [
                'event_type' => $event->eventType,
                'conference_id' => $event->conference->id,
                'users_notified' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send conference notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'conference_id' => $event->conference->id
            ]);
        }
    }

    /**
     * Get users who should receive conference notifications (all participants in the conference)
     */
    private function getUsersToNotify(ConferenceEvent $event): array
    {
        $users = [];

        // Get all participants in this conference
        $participants = Participant::where('conference_id', $event->conferenceId)
            ->with('user')
            ->get();
            
        foreach ($participants as $participant) {
            if ($participant->user) {
                $users[] = $participant->user;
            }
        }

        return $users;
    }

    /**
     * Create notification for a specific user
     */
    private function createNotification(User $user, ConferenceEvent $event): void
    {
        // Don't create duplicate notifications for the same event
        $existingNotification = Notification::where([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'type' => 'ConferenceUpdate'
        ])
        ->where('message', 'LIKE', '%' . $event->conference->name . '%')
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
            'type' => 'ConferenceUpdate',
            'related_model' => 'Conference',
            'related_id' => $event->conference->id,
            'action_url' => route('conferences.show', $event->conference->id),
            'sent_at' => now(),
            'read_status' => false,
        ]);
    }
}
