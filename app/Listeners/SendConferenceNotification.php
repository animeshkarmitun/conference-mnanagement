<?php

namespace App\Listeners;

use App\Events\ConferenceEvent;
use App\Models\Notification;
use App\Models\User;
use App\Models\Participant;
use App\Services\NotificationTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendConferenceNotification implements ShouldQueue
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

        // Prepare variables for template
        $variables = $this->prepareConferenceVariables($event, $user);

        // Generate message using template
        $message = $this->notificationTemplateService->processTemplate('ConferenceUpdate', $variables);

        Notification::create([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'message' => $message,
            'type' => 'ConferenceUpdate',
            'related_model' => 'Conference',
            'related_id' => $event->conference->id,
            'action_url' => route('conferences.show', $event->conference->id),
            'sent_at' => now(),
            'read_status' => false,
        ]);
    }

    /**
     * Prepare variables for conference notification template
     */
    private function prepareConferenceVariables(ConferenceEvent $event, User $user): array
    {
        $conference = $event->conference;

        return [
            'conference_name' => $conference->name,
            'action' => $this->getActionDescription($event->eventType),
            'additional_info' => $this->getAdditionalInfo($event),
            'user_name' => $user->first_name . ' ' . $user->last_name,
        ];
    }

    /**
     * Get human-readable action description
     */
    private function getActionDescription(string $eventType): string
    {
        $actions = [
            'conference_created' => 'created',
            'conference_updated' => 'updated',
            'conference_deleted' => 'deleted',
            'conference_published' => 'published',
            'conference_unpublished' => 'unpublished',
            'conference_schedule_updated' => 'schedule updated',
            'conference_venue_updated' => 'venue updated',
        ];

        return $actions[$eventType] ?? 'modified';
    }

    /**
     * Get additional information based on event type
     */
    private function getAdditionalInfo(ConferenceEvent $event): string
    {
        $info = [];

        if (isset($event->changes['start_date'])) {
            $info[] = 'Start date changed to ' . \Carbon\Carbon::parse($event->changes['start_date'])->format('M d, Y');
        }

        if (isset($event->changes['end_date'])) {
            $info[] = 'End date changed to ' . \Carbon\Carbon::parse($event->changes['end_date'])->format('M d, Y');
        }

        if (isset($event->changes['venue_id'])) {
            $info[] = 'Venue updated';
        }

        if (isset($event->changes['status'])) {
            $info[] = 'Status changed to ' . ucfirst($event->changes['status']);
        }

        return implode('. ', $info) ?: 'No additional details';
    }
}
