<?php

namespace App\Listeners;

use App\Events\TravelEvent;
use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTravelNotification implements ShouldQueue
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
            // Get users who should receive travel notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->createNotification($user, $event);
            }

            Log::info('Travel notification sent', [
                'event_type' => $event->eventType,
                'participant_id' => $event->participant->id,
                'users_notified' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send travel notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'participant_id' => $event->participant->id
            ]);
        }
    }

    /**
     * Get users who should receive travel notifications
     */
    private function getUsersToNotify(TravelEvent $event): array
    {
        $users = [];

        // Always notify the participant
        $users[] = $event->participant->user;

        // Get admin and superadmin users
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
     * Create notification for a specific user
     */
    private function createNotification(User $user, TravelEvent $event): void
    {
        // Don't create duplicate notifications for the same event
        $existingNotification = Notification::where([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'type' => 'TravelUpdate'
        ])
        ->where('message', 'LIKE', '%' . $event->participant->user->first_name . '%')
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
            'type' => 'TravelUpdate',
            'related_model' => 'Participant',
            'related_id' => $event->participant->id,
            'action_url' => route('participants.show', $event->participant->id),
            'sent_at' => now(),
            'read_status' => false,
        ]);
    }
}
