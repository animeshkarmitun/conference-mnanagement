<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomAllocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    /**
     * Check for room conflicts
     */
    public function checkConflicts(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'participant_id' => 'required|exists:participants,id'
        ]);

        $roomId = $request->room_id;
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $participantId = $request->participant_id;

        // Get the room to find hotel_id and room_number
        $room = Room::findOrFail($roomId);

        // Check for existing allocations that conflict
        $conflictingAllocations = RoomAllocation::where('hotel_id', $room->hotel_id)
            ->where('room_number', $room->room_number)
            ->where('participant_id', '!=', $participantId)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    // Check if the new dates overlap with existing allocations
                    $q->where('check_in', '<=', $checkOut)
                      ->where('check_out', '>=', $checkIn);
                });
            })
            ->with('participant')
            ->get();

        if ($conflictingAllocations->count() > 0) {
            $conflictingParticipant = $conflictingAllocations->first()->participant;
            $message = "Room {$room->room_number} is already allocated to {$conflictingParticipant->first_name} {$conflictingParticipant->last_name} during the selected dates.";
            
            return response()->json([
                'has_conflict' => true,
                'message' => $message,
                'conflicting_allocations' => $conflictingAllocations
            ]);
        }

        return response()->json([
            'has_conflict' => false,
            'message' => 'No conflicts found'
        ]);
    }
}
