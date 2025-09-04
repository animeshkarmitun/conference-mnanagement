<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Hotel;
use App\Models\TravelDetail;
use App\Models\RoomAllocation;
use App\Services\TravelNotificationService;
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
        $travelDetails = TravelDetail::with(['participant.user', 'hotel', 'participant.conference'])->get();
        return view('admin.travel.travel-manifests', compact('travelDetails'));
    }

    // Admin view for travel conflicts
    public function travelConflicts()
    {
        $conflicts = [];
        $travelDetails = TravelDetail::with(['participant.user', 'hotel'])->get();
        $travelNotificationService = new TravelNotificationService();

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
                                $conflictDetails = "Room {$a->room_number} in hotel {$a->hotel->name} assigned to both {$a->participant->user->name} and {$b->participant->user->name} for overlapping dates.";
                                
                                $conflicts[] = [
                                    'type' => 'Room Overlap',
                                    'details' => $conflictDetails
                                ];

                                // Send notifications for room overlap
                                $travelNotificationService->notifyRoomOverlap(
                                    $a->participant, 
                                    $b->participant, 
                                    $a->hotel->name, 
                                    $a->room_number
                                );
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
                $conflictDetails = "Hotel {$hotel->name} has more participants assigned (" . count($details) . ") than its capacity ({$hotel->room_capacity}).";
                
                $conflicts[] = [
                    'type' => 'Hotel Overbooked',
                    'details' => $conflictDetails
                ];

                // Send notification for hotel overbooking
                $travelNotificationService->notifyHotelOverbooked($hotel->name, count($details), $hotel->room_capacity);
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

        // Send room allocation notification
        if ($roomAllocation->hotel_id) {
            $travelNotificationService = new TravelNotificationService();
            $travelNotificationService->notifyRoomAllocated($participant, $roomAllocation);
        }

        return redirect()->back()->with('success', 'Room allocation updated.');
    }

    /**
     * Export Travel Manifest as CSV
     */
    public function exportManifest()
    {
        $travelDetails = TravelDetail::with(['participant.user', 'hotel', 'participant.conference'])->get();

        $filename = 'travel_manifest_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($travelDetails) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Participant Name',
                'Email',
                'Conference',
                'Hotel',
                'Arrival Date',
                'Departure Date',
                'Flight Info',
                'Extra Nights',
                'Organization'
            ]);

            // CSV Data
            foreach ($travelDetails as $detail) {
                fputcsv($file, [
                    ($detail->participant->user->first_name ?? $detail->participant->user->name) . ' ' . ($detail->participant->user->last_name ?? ''),
                    $detail->participant->user->email,
                    $detail->participant->conference->name ?? 'N/A',
                    $detail->hotel->name ?? 'N/A',
                    $detail->arrival_date ? date('Y-m-d H:i', strtotime($detail->arrival_date)) : 'N/A',
                    $detail->departure_date ? date('Y-m-d H:i', strtotime($detail->departure_date)) : 'N/A',
                    $detail->flight_info ?? 'N/A',
                    $detail->extra_nights ?? 0,
                    $detail->participant->organization ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 