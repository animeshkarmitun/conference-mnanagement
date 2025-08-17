<?php

namespace App\Services;

use App\Events\TravelEvent;
use App\Models\Participant;
use App\Models\TravelDetail;
use App\Models\RoomAllocation;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TravelNotificationService
{
    /**
     * Send notification when travel details are updated
     */
    public function notifyTravelDetailsUpdated(Participant $participant, TravelDetail $travelDetail): void
    {
        $message = "Travel details updated for {$participant->user->first_name} {$participant->user->last_name}";
        
        if ($travelDetail->arrival_date) {
            $message .= " - Arrival: " . Carbon::parse($travelDetail->arrival_date)->format('M d, Y H:i');
        }
        if ($travelDetail->departure_date) {
            $message .= " - Departure: " . Carbon::parse($travelDetail->departure_date)->format('M d, Y H:i');
        }
        if ($travelDetail->hotel) {
            $message .= " - Hotel: {$travelDetail->hotel->name}";
        }

        $this->triggerTravelEvent($participant, 'travel_details_updated', $message, $travelDetail);
    }

    /**
     * Send notification when room is allocated
     */
    public function notifyRoomAllocated(Participant $participant, RoomAllocation $roomAllocation): void
    {
        $hotelName = $roomAllocation->hotel ? $roomAllocation->hotel->name : 'Unknown Hotel';
        $roomNumber = $roomAllocation->room_number ?: 'TBD';
        
        $message = "Room allocated for {$participant->user->first_name} {$participant->user->last_name} - Hotel: {$hotelName}, Room: {$roomNumber}";
        
        if ($roomAllocation->check_in) {
            $message .= " - Check-in: " . Carbon::parse($roomAllocation->check_in)->format('M d, Y H:i');
        }
        if ($roomAllocation->check_out) {
            $message .= " - Check-out: " . Carbon::parse($roomAllocation->check_out)->format('M d, Y H:i');
        }

        $this->triggerTravelEvent($participant, 'room_allocated', $message, null, $roomAllocation);
    }

    /**
     * Send notification when travel conflict is detected
     */
    public function notifyTravelConflict(Participant $participant, string $conflictDetails): void
    {
        $message = "Travel conflict detected for {$participant->user->first_name} {$participant->user->last_name}: {$conflictDetails}";
        
        $this->triggerTravelEvent($participant, 'travel_conflict_detected', $message);
    }

    /**
     * Send notification when travel documents are uploaded
     */
    public function notifyTravelDocumentsUploaded(Participant $participant): void
    {
        $message = "Travel documents uploaded by {$participant->user->first_name} {$participant->user->last_name}";
        
        $this->triggerTravelEvent($participant, 'travel_documents_uploaded', $message);
    }

    /**
     * Send notification when hotel is overbooked
     */
    public function notifyHotelOverbooked(string $hotelName, int $assignedCount, int $capacity): void
    {
        // For hotel overbooking, we need to notify all admins and event coordinators
        // This is a special case where we don't have a specific participant
        $message = "Hotel overbooking alert: {$hotelName} has {$assignedCount} participants assigned but only {$capacity} capacity";
        
        Log::warning('Hotel overbooking detected', [
            'hotel_name' => $hotelName,
            'assigned_count' => $assignedCount,
            'capacity' => $capacity
        ]);

        // TODO: Implement admin notification for hotel overbooking
        // This would require a different approach since it's not participant-specific
    }

    /**
     * Send notification when room overlap is detected
     */
    public function notifyRoomOverlap(Participant $participant1, Participant $participant2, string $hotelName, string $roomNumber): void
    {
        $message = "Room overlap detected: Room {$roomNumber} in {$hotelName} assigned to both {$participant1->user->first_name} {$participant1->user->last_name} and {$participant2->user->first_name} {$participant2->user->last_name}";
        
        // Notify both participants
        $this->triggerTravelEvent($participant1, 'travel_conflict_detected', $message);
        $this->triggerTravelEvent($participant2, 'travel_conflict_detected', $message);
    }

    /**
     * Trigger travel event
     */
    private function triggerTravelEvent(
        Participant $participant, 
        string $eventType, 
        string $message, 
        ?TravelDetail $travelDetail = null,
        ?RoomAllocation $roomAllocation = null
    ): void {
        try {
            $event = new TravelEvent($participant, $eventType, $message, $travelDetail, $roomAllocation);
            event($event);
            
            Log::info('Travel event triggered', [
                'participant_id' => $participant->id,
                'event_type' => $eventType,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to trigger travel event', [
                'error' => $e->getMessage(),
                'participant_id' => $participant->id,
                'event_type' => $eventType
            ]);
        }
    }
}
