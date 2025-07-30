<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Hotel;
use App\Models\TravelDetail;
use App\Models\RoomAllocation;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    // Admin view for room allocations
    public function roomAllocations()
    {
        $participants = Participant::with(['user', 'travelDetails', 'travelDetails.hotel', 'roomAllocations'])->get();
        $hotels = Hotel::with('travelDetails')->get();
        return view('admin.travel.room-allocations', compact('participants', 'hotels'));
    }

    // Admin view for travel manifests
    public function travelManifests()
    {
        $travelDetails = TravelDetail::with(['participant.user', 'hotel'])->get();
        return view('admin.travel.travel-manifests', compact('travelDetails'));
    }

    // Admin view for travel conflicts
    public function travelConflicts()
    {
        $conflicts = [];
        $travelDetails = TravelDetail::with(['participant.user', 'hotel'])->get();

        // Detect duplicate room assignments for overlapping dates
        $byHotelRoom = [];
        foreach ($travelDetails as $detail) {
            if ($detail->hotel_id && $detail->room_number) {
                $key = $detail->hotel_id . '-' . $detail->room_number;
                $byHotelRoom[$key][] = $detail;
            }
        }
        foreach ($byHotelRoom as $key => $details) {
            if (count($details) > 1) {
                // Check for overlapping dates
                for ($i = 0; $i < count($details); $i++) {
                    for ($j = $i + 1; $j < count($details); $j++) {
                        $a = $details[$i];
                        $b = $details[$j];
                        if ($a->arrival_date && $a->departure_date && $b->arrival_date && $b->departure_date) {
                            if (!($a->departure_date <= $b->arrival_date || $b->departure_date <= $a->arrival_date)) {
                                $conflicts[] = [
                                    'type' => 'Room Overlap',
                                    'details' => "Room {$a->room_number} in hotel {$a->hotel->name} assigned to both {$a->participant->user->name} and {$b->participant->user->name} for overlapping dates."
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Detect overbooked hotels (more participants than hotel capacity)
        $hotelCounts = [];
        foreach ($travelDetails as $detail) {
            if ($detail->hotel_id) {
                $hotelCounts[$detail->hotel_id][] = $detail;
            }
        }
        $hotels = Hotel::all()->keyBy('id');
        foreach ($hotelCounts as $hotelId => $details) {
            $hotel = $hotels[$hotelId] ?? null;
            if ($hotel && isset($hotel->room_capacity) && count($details) > $hotel->room_capacity) {
                $conflicts[] = [
                    'type' => 'Hotel Overbooked',
                    'details' => "Hotel {$hotel->name} has more participants assigned (" . count($details) . ") than its capacity ({$hotel->room_capacity})."
                ];
            }
        }

        return view('admin.travel.travel-conflicts', compact('conflicts'));
    }

    public function updateRoomAllocation(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'hotel_id' => 'nullable|exists:hotels,id',
            'room_number' => 'nullable|string|max:50',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after_or_equal:check_in',
        ]);

        // Update or create room allocation
        $roomAllocation = $participant->roomAllocations()->first() ?: new RoomAllocation();
        $roomAllocation->hotel_id = $validated['hotel_id'] ?? null;
        $roomAllocation->participant_id = $participant->id;
        $roomAllocation->room_number = $validated['room_number'] ?? null;
        $roomAllocation->check_in = $validated['check_in'] ?? null;
        $roomAllocation->check_out = $validated['check_out'] ?? null;
        $roomAllocation->save();

        return redirect()->back()->with('success', 'Room allocation updated.');
    }
} 