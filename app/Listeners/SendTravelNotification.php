<?php

namespace App\Listeners;

use App\Events\TravelEvent;
use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use App\Services\NotificationTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTravelNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationTemplateService $notificationTemplateService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationTemplateService $notificationTemplateService)
    {
        $this->notificationTemplateService = $notificationTemplateService;
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

        // Prepare variables for template
        $variables = $this->prepareTravelVariables($event, $user);

        // Generate message using template
        $message = $this->notificationTemplateService->processTemplate('TravelUpdate', $variables);

        Notification::create([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'message' => $message,
            'type' => 'TravelUpdate',
            'related_model' => 'Participant',
            'related_id' => $event->participant->id,
            'action_url' => route('participants.show', $event->participant->id),
            'sent_at' => now(),
            'read_status' => false,
        ]);
    }

    /**
     * Prepare variables for travel notification template
     */
    private function prepareTravelVariables(TravelEvent $event, User $user): array
    {
        $participant = $event->participant;
        $conference = $participant->conference;

        $variables = [
            'participant_name' => $participant->user->first_name . ' ' . $participant->user->last_name,
            'action' => $this->getActionDescription($event->eventType),
            'conference_name' => $conference->name ?? 'Unknown Conference',
        ];

        // Add travel-specific variables if available
        if ($event->travelDetail) {
            $variables['hotel_name'] = $event->travelDetail->hotel ? $event->travelDetail->hotel->name : 'TBD';
            $variables['check_in'] = $event->travelDetail->check_in ? \Carbon\Carbon::parse($event->travelDetail->check_in)->format('M d, Y g:i A') : 'Not set';
            $variables['check_out'] = $event->travelDetail->check_out ? \Carbon\Carbon::parse($event->travelDetail->check_out)->format('M d, Y g:i A') : 'Not set';
        }

        // Add room allocation variables if available
        if ($event->roomAllocation) {
            $variables['hotel_name'] = $event->roomAllocation->hotel ? $event->roomAllocation->hotel->name : 'TBD';
            $variables['room_number'] = $event->roomAllocation->room_number ?? 'TBD';
            $variables['check_in'] = $event->roomAllocation->check_in ? \Carbon\Carbon::parse($event->roomAllocation->check_in)->format('M d, Y g:i A') : 'Not set';
            $variables['check_out'] = $event->roomAllocation->check_out ? \Carbon\Carbon::parse($event->roomAllocation->check_out)->format('M d, Y g:i A') : 'Not set';
        }

        // Add additional info based on event type
        $variables['additional_info'] = $this->getAdditionalInfo($event);

        return $variables;
    }

    /**
     * Get human-readable action description
     */
    private function getActionDescription(string $eventType): string
    {
        $actions = [
            'travel_created' => 'travel information created',
            'travel_updated' => 'travel information updated',
            'room_allocated' => 'room allocated',
            'room_updated' => 'room allocation updated',
            'travel_conflict_detected' => 'travel conflict detected',
            'travel_documents_uploaded' => 'travel documents uploaded',
            'hotel_overbooked' => 'hotel overbooked',
        ];

        return $actions[$eventType] ?? 'travel information modified';
    }

    /**
     * Get additional information based on event type
     */
    private function getAdditionalInfo(TravelEvent $event): string
    {
        $info = [];

        if ($event->eventType === 'room_allocated' && $event->roomAllocation) {
            $info[] = 'Hotel: ' . ($event->roomAllocation->hotel ? $event->roomAllocation->hotel->name : 'TBD');
            $info[] = 'Room: ' . ($event->roomAllocation->room_number ?? 'TBD');
        }

        if ($event->eventType === 'travel_conflict_detected') {
            $info[] = 'Please review your travel arrangements';
        }

        if ($event->eventType === 'hotel_overbooked') {
            $info[] = 'Alternative arrangements will be made';
        }

        return implode('. ', $info) ?: 'No additional details';
    }
}
