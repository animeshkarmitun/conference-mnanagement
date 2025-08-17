<?php

namespace App\Services;

use App\Events\ConferenceEvent;
use App\Models\Conference;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ConferenceNotificationService
{
    /**
     * Send notification when conference dates are updated
     */
    public function notifyConferenceDatesUpdated(Conference $conference, array $oldData, array $newData): void
    {
        $changes = [];
        
        // Check for start date changes
        if (($oldData['start_date'] ?? '') !== ($newData['start_date'] ?? '')) {
            $oldStartDate = $oldData['start_date'] ? Carbon::parse($oldData['start_date'])->format('l, M j, Y') : 'TBD';
            $newStartDate = $newData['start_date'] ? Carbon::parse($newData['start_date'])->format('l, M j, Y') : 'TBD';
            $changes[] = "start date from {$oldStartDate} to {$newStartDate}";
        }
        
        // Check for end date changes
        if (($oldData['end_date'] ?? '') !== ($newData['end_date'] ?? '')) {
            $oldEndDate = $oldData['end_date'] ? Carbon::parse($oldData['end_date'])->format('l, M j, Y') : 'TBD';
            $newEndDate = $newData['end_date'] ? Carbon::parse($newData['end_date'])->format('l, M j, Y') : 'TBD';
            $changes[] = "end date from {$oldEndDate} to {$newEndDate}";
        }
        
        if (!empty($changes)) {
            $changesText = implode(', ', $changes);
            $message = "Conference '{$conference->name}' schedule updated - Changes: {$changesText}";
            
            $this->triggerConferenceEvent($conference, 'conference_dates_updated', $message, [
                'old_data' => $oldData,
                'new_data' => $newData,
                'changes' => $changes
            ]);
        }
    }

    /**
     * Send notification when conference venue is updated
     */
    public function notifyConferenceVenueUpdated(Conference $conference, string $oldVenue, string $newVenue): void
    {
        $message = "Conference '{$conference->name}' venue updated - Changed from '{$oldVenue}' to '{$newVenue}'";
        
        $this->triggerConferenceEvent($conference, 'conference_venue_updated', $message, [
            'old_venue' => $oldVenue,
            'new_venue' => $newVenue
        ]);
    }

    /**
     * Send notification when conference is cancelled
     */
    public function notifyConferenceCancelled(Conference $conference): void
    {
        $message = "Conference '{$conference->name}' has been cancelled";
        
        $this->triggerConferenceEvent($conference, 'conference_cancelled', $message);
    }

    /**
     * Send notification when conference is postponed
     */
    public function notifyConferencePostponed(Conference $conference, array $oldData, array $newData): void
    {
        $oldStartDate = $oldData['start_date'] ? Carbon::parse($oldData['start_date'])->format('l, M j, Y') : 'TBD';
        $newStartDate = $newData['start_date'] ? Carbon::parse($newData['start_date'])->format('l, M j, Y') : 'TBD';
        
        $message = "Conference '{$conference->name}' has been postponed from {$oldStartDate} to {$newStartDate}";
        
        $this->triggerConferenceEvent($conference, 'conference_postponed', $message, [
            'old_data' => $oldData,
            'new_data' => $newData
        ]);
    }

    /**
     * Trigger conference event
     */
    private function triggerConferenceEvent(
        Conference $conference, 
        string $eventType, 
        string $message, 
        array $changes = []
    ): void {
        try {
            $event = new ConferenceEvent($conference, $eventType, $message, $changes);
            event($event);
            
            Log::info('Conference event triggered', [
                'conference_id' => $conference->id,
                'event_type' => $eventType,
                'message' => $message,
                'changes' => $changes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to trigger conference event', [
                'error' => $e->getMessage(),
                'conference_id' => $conference->id,
                'event_type' => $eventType
            ]);
        }
    }
}
