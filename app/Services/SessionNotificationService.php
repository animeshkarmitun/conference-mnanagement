<?php

namespace App\Services;

use App\Events\SessionEvent;
use App\Models\Session;
use App\Models\Conference;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SessionNotificationService
{
    /**
     * Send notification when session dates are updated
     */
    public function notifySessionDatesUpdated(Session $session, array $oldData, array $newData): void
    {
        $changes = [];
        
        // Check for start time changes
        if (($oldData['start_time'] ?? '') !== ($newData['start_time'] ?? '')) {
            $oldStartTime = $oldData['start_time'] ? Carbon::parse($oldData['start_time'])->format('l, M j, Y \a\t g:i A') : 'TBD';
            $newStartTime = $newData['start_time'] ? Carbon::parse($newData['start_time'])->format('l, M j, Y \a\t g:i A') : 'TBD';
            $changes[] = "start time from {$oldStartTime} to {$newStartTime}";
        }
        
        // Check for end time changes
        if (($oldData['end_time'] ?? '') !== ($newData['end_time'] ?? '')) {
            $oldEndTime = $oldData['end_time'] ? Carbon::parse($oldData['end_time'])->format('l, M j, Y \a\t g:i A') : 'TBD';
            $newEndTime = $newData['end_time'] ? Carbon::parse($newData['end_time'])->format('l, M j, Y \a\t g:i A') : 'TBD';
            $changes[] = "end time from {$oldEndTime} to {$newEndTime}";
        }
        
        if (!empty($changes)) {
            $changesText = implode(', ', $changes);
            $message = "Session '{$session->title}' schedule updated - Changes: {$changesText}";
            
            $this->triggerSessionEvent($session, 'session_dates_updated', $message, [
                'old_data' => $oldData,
                'new_data' => $newData,
                'changes' => $changes
            ]);
        }
    }

    /**
     * Send notification when session venue/room is updated
     */
    public function notifySessionVenueUpdated(Session $session, string $oldVenue, string $newVenue): void
    {
        $message = "Session '{$session->title}' venue updated - Changed from '{$oldVenue}' to '{$newVenue}'";
        
        $this->triggerSessionEvent($session, 'session_venue_updated', $message, [
            'old_venue' => $oldVenue,
            'new_venue' => $newVenue
        ]);
    }

    /**
     * Send notification when session is cancelled
     */
    public function notifySessionCancelled(Session $session): void
    {
        $message = "Session '{$session->title}' has been cancelled";
        
        $this->triggerSessionEvent($session, 'session_cancelled', $message);
    }

    /**
     * Send notification when session is rescheduled
     */
    public function notifySessionRescheduled(Session $session, array $oldData, array $newData): void
    {
        $oldStartTime = $oldData['start_time'] ? Carbon::parse($oldData['start_time'])->format('l, M j, Y \a\t g:i A') : 'TBD';
        $newStartTime = $newData['start_time'] ? Carbon::parse($newData['start_time'])->format('l, M j, Y \a\t g:i A') : 'TBD';
        
        $message = "Session '{$session->title}' has been rescheduled from {$oldStartTime} to {$newStartTime}";
        
        $this->triggerSessionEvent($session, 'session_rescheduled', $message, [
            'old_data' => $oldData,
            'new_data' => $newData
        ]);
    }

    /**
     * Trigger session event
     */
    private function triggerSessionEvent(
        Session $session, 
        string $eventType, 
        string $message, 
        array $changes = []
    ): void {
        try {
            $event = new SessionEvent($session, $eventType, $message, $changes);
            event($event);
            
            Log::info('Session event triggered', [
                'session_id' => $session->id,
                'event_type' => $eventType,
                'message' => $message,
                'changes' => $changes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to trigger session event', [
                'error' => $e->getMessage(),
                'session_id' => $session->id,
                'event_type' => $eventType
            ]);
        }
    }
}
