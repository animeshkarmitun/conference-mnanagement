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
    public function roomAllocations(Request $request)
    {
        $conferences = \App\Models\Conference::orderBy('name')->get();
        $selectedConferenceId = $request->get('conference_id');
        
        $query = Participant::with(['user', 'travelDetails', 'travelDetails.hotel', 'roomAllocations', 'conference']);
        
        if ($selectedConferenceId) {
            $query->where('conference_id', $selectedConferenceId);
        }
        
        // Filter participants to only show those with travel intent 'national' or 'international'
        $query->whereIn('travel_intent', ['national', 'international']);
        
        $participants = $query->get();
        $hotels = Hotel::with(['rooms'])->where('is_active', true)->orderBy('name')->get();
        
        return view('admin.travel.room-allocations', compact('participants', 'hotels', 'conferences', 'selectedConferenceId'));
    }

    // Admin view for itineraries
    public function itineraries()
    {
        $travelDetails = TravelDetail::with([
            'participant.user', 
            'hotel', 
            'participant.conference',
            'room',
            'room.roomType',
            'participant.roomAllocations'
        ])
        ->whereHas('participant', function($query) {
            // Only include travel details for participants with travel intent 'national' or 'international'
            $query->whereIn('travel_intent', ['national', 'international']);
        })
        ->get();
        
        $conferences = \App\Models\Conference::all();
        return view('admin.travel.itineraries', compact('travelDetails', 'conferences'));
    }

    // Get participant data for modal
    public function getParticipantData($participantId)
    {
        try {
            $participant = Participant::with('travelDetails')->findOrFail($participantId);
            
            return response()->json([
                'success' => true,
                'participant' => [
                    'visa_status' => $participant->visa_status,
                ],
                'travel_details' => [
                    'itineraries_status' => $participant->travelDetails ? $participant->travelDetails->itineraries_status : null,
                    'takeoff_airport' => $participant->travelDetails ? $participant->travelDetails->takeoff_airport : null,
                    'flight_info_details' => $participant->travelDetails ? $participant->travelDetails->flight_info_details : null,
                    'hotel_info' => $participant->travelDetails ? $participant->travelDetails->hotel_info : null,
                    'room_check_in' => $participant->travelDetails ? $participant->travelDetails->room_check_in : null,
                    'room_check_out' => $participant->travelDetails ? $participant->travelDetails->room_check_out : null,
                    'arrival_date' => $participant->travelDetails ? $participant->travelDetails->arrival_date : null,
                    'departure_date' => $participant->travelDetails ? $participant->travelDetails->departure_date : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Participant not found'
            ], 404);
        }
    }

    // Update participant travel details
    public function updateParticipantDetails(Request $request, $participantId)
    {
        try {
            $participant = Participant::findOrFail($participantId);
            
            // Debug: Log incoming request data
            \Log::info('Travel details update request:', [
                'participant_id' => $participantId,
                'request_data' => $request->all(),
                'room_check_in' => $request->input('room_check_in'),
                'room_check_out' => $request->input('room_check_out'),
            ]);
            
            $validated = $request->validate([
                'visa_status' => 'nullable|in:required,not_required,pending,approved,issue',
                'itineraries_status' => 'nullable|in:pending,approved,n_a',
                'takeoff_airport' => 'nullable|string|max:255',
                'flight_info_details' => 'nullable|string|max:1000',
                'hotel_info' => 'nullable|string|max:2000',
                'room_check_in' => 'nullable|date_format:Y-m-d\TH:i',
                'room_check_out' => 'nullable|date_format:Y-m-d\TH:i|after:room_check_in',
            ], [
                'room_check_in.date_format' => 'Check-in time must be in valid date and time format.',
                'room_check_out.date_format' => 'Check-out time must be in valid date and time format.',
                'room_check_out.after' => 'Room check-out date must be after check-in date.',
            ]);

            // Additional validation: Check-in and check-out times must be between arrival and departure times
            $travelDetails = $participant->travelDetails;
            if ($travelDetails && ($travelDetails->arrival_date || $travelDetails->departure_date)) {
                $arrivalDate = $travelDetails->arrival_date ? \Carbon\Carbon::parse($travelDetails->arrival_date) : null;
                $departureDate = $travelDetails->departure_date ? \Carbon\Carbon::parse($travelDetails->departure_date) : null;
                
                // Validate check-in time
                if (!empty($validated['room_check_in'])) {
                    $checkInDate = \Carbon\Carbon::parse($validated['room_check_in']);
                    
                    if ($arrivalDate && $checkInDate->lt($arrivalDate)) {
                        return redirect()->back()->withErrors([
                            'room_check_in' => 'Room check-in time cannot be before arrival time (' . $arrivalDate->format('M d, Y g:i A') . ').'
                        ])->withInput();
                    }
                    
                    if ($departureDate && $checkInDate->gt($departureDate)) {
                        return redirect()->back()->withErrors([
                            'room_check_in' => 'Room check-in time cannot be after departure time (' . $departureDate->format('M d, Y g:i A') . ').'
                        ])->withInput();
                    }
                }
                
                // Validate check-out time
                if (!empty($validated['room_check_out'])) {
                    $checkOutDate = \Carbon\Carbon::parse($validated['room_check_out']);
                    
                    if ($arrivalDate && $checkOutDate->lt($arrivalDate)) {
                        return redirect()->back()->withErrors([
                            'room_check_out' => 'Room check-out time cannot be before arrival time (' . $arrivalDate->format('M d, Y g:i A') . ').'
                        ])->withInput();
                    }
                    
                    if ($departureDate && $checkOutDate->gt($departureDate)) {
                        return redirect()->back()->withErrors([
                            'room_check_out' => 'Room check-out time cannot be after departure time (' . $departureDate->format('M d, Y g:i A') . ').'
                        ])->withInput();
                    }
                }
                
                // Validate that check-in is before check-out if both are provided
                if (!empty($validated['room_check_in']) && !empty($validated['room_check_out'])) {
                    $checkInDate = \Carbon\Carbon::parse($validated['room_check_in']);
                    $checkOutDate = \Carbon\Carbon::parse($validated['room_check_out']);
                    
                    if ($checkInDate->gte($checkOutDate)) {
                        return redirect()->back()->withErrors([
                            'room_check_out' => 'Room check-out time must be after check-in time.'
                        ])->withInput();
                    }
                }
            }

            // Update participant visa status
            if (isset($validated['visa_status'])) {
                $participant->visa_status = $validated['visa_status'];
                $participant->save();
            }

            // Update or create travel details
            if (isset($validated['itineraries_status']) || isset($validated['takeoff_airport']) || isset($validated['flight_info_details']) || 
                isset($validated['hotel_info']) || isset($validated['room_check_in']) || isset($validated['room_check_out'])) {
                
                // Get or create travel details
                $travelDetail = $participant->travelDetails;
                if (!$travelDetail) {
                    $travelDetail = new TravelDetail();
                    $travelDetail->participant_id = $participant->id;
                }
                
                // Update fields
                if (isset($validated['itineraries_status'])) {
                    $travelDetail->itineraries_status = $validated['itineraries_status'];
                }
                if (isset($validated['takeoff_airport'])) {
                    $travelDetail->takeoff_airport = $validated['takeoff_airport'];
                }
                if (isset($validated['flight_info_details'])) {
                    $travelDetail->flight_info_details = $validated['flight_info_details'];
                }
                if (isset($validated['hotel_info'])) {
                    $travelDetail->hotel_info = $validated['hotel_info'];
                }
                if (isset($validated['room_check_in'])) {
                    $travelDetail->room_check_in = $validated['room_check_in'];
                }
                if (isset($validated['room_check_out'])) {
                    $travelDetail->room_check_out = $validated['room_check_out'];
                }
                
                $travelDetail->save();
                
                \Log::info('Travel details updated', [
                    'participant_id' => $participant->id,
                    'travel_detail_id' => $travelDetail->id,
                    'room_check_in' => $validated['room_check_in'] ?? null,
                    'room_check_out' => $validated['room_check_out'] ?? null,
                ]);
            }

            return redirect()->back()->with('success', 'Travel details updated successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update travel details: ' . $e->getMessage());
        }
    }

    // Admin view for travel conflicts
    public function travelConflicts()
    {
        $conflicts = [];
        $travelDetails = TravelDetail::with(['participant.user', 'hotel'])
            ->whereHas('participant', function($query) {
                // Only include travel details for participants with travel intent 'national' or 'international'
                $query->whereIn('travel_intent', ['national', 'international']);
            })
            ->get();
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

    public function updateTravelDetails(Request $request, Participant $participant)
    {
        try {
            $validated = $request->validate([
                'arrival_date' => 'nullable|date',
                'departure_date' => 'nullable|date|after:arrival_date',
                'flight_info' => 'nullable|string|max:1000',
                'hotel_id' => 'nullable|exists:hotels,id',
                'room_id' => 'nullable|exists:rooms,id',
                'extra_nights' => 'nullable|integer|min:0',
                'travel_intent' => 'nullable|in:domestic,international',
                'room_check_in' => 'nullable|date',
                'room_check_out' => 'nullable|date|after:room_check_in',
                'travel_documents' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ], [
                'departure_date.after' => 'Departure date must be after arrival date.',
                'room_check_out.after' => 'Room check-out date must be after check-in date.',
                'travel_documents.mimes' => 'Travel documents must be a PDF, DOC, DOCX, JPG, JPEG, or PNG file.',
                'travel_documents.max' => 'Travel documents file size cannot exceed 10MB.'
            ]);

            // Handle file upload
            if ($request->hasFile('travel_documents')) {
                $file = $request->file('travel_documents');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('travel_documents', $filename, 'public');
                $validated['travel_documents'] = $path;
            }

            // Update or create travel details
            $travelDetail = $participant->travelDetails()->first() ?: new TravelDetail();
            $travelDetail->participant_id = $participant->id;
            $travelDetail->fill($validated);
            $travelDetail->save();

            // Send travel details notification
            $travelNotificationService = new TravelNotificationService();
            $travelNotificationService->notifyTravelDetailsUpdated($participant, $travelDetail);

            return redirect()->back()->with('success', 'Travel details updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Travel details update failed', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to update travel details: ' . $e->getMessage());
        }
    }

    public function updateRoomAllocation(Request $request, Participant $participant)
    {
        \Log::info('Room allocation request received', [
            'participant_id' => $participant->id,
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax(),
            'headers' => $request->headers->all()
        ]);

        try {
            $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_number' => 'nullable|string|max:50',
            'number_of_beds' => 'nullable|integer|min:1|max:10',
            'check_in' => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
        ], [
            'hotel_id.required' => 'Hotel selection is required.',
            'hotel_id.exists' => 'Selected hotel is invalid.',
            'number_of_beds.integer' => 'Number of beds must be a valid number.',
            'number_of_beds.min' => 'Number of beds must be at least 1.',
            'number_of_beds.max' => 'Number of beds cannot exceed 10.',
            'check_in.after' => 'Check-in time cannot be in the past.',
            'check_out.after' => 'Check-out time must be after check-in time.',
        ]);

        // Additional validation: Check-in time cannot be after departure time
        if (!empty($validated['check_in'])) {
            $travelDetails = $participant->travelDetails()->first();
            if ($travelDetails && $travelDetails->departure_date) {
                $checkInDate = \Carbon\Carbon::parse($validated['check_in']);
                $departureDate = \Carbon\Carbon::parse($travelDetails->departure_date);
                
                if ($checkInDate->gt($departureDate)) {
                    $errorMessage = 'Check-in time cannot be after departure time (' . $departureDate->format('M d, Y g:i A') . ').';
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => ['check_in' => [$errorMessage]]
                        ], 422);
                    }
                    
                    return redirect()->back()->withErrors(['check_in' => $errorMessage])->withInput();
                }
            }
        }

        // Additional validation: Check-out time cannot be after departure time
        if (!empty($validated['check_out'])) {
            $travelDetails = $participant->travelDetails()->first();
            if ($travelDetails && $travelDetails->departure_date) {
                $checkOutDate = \Carbon\Carbon::parse($validated['check_out']);
                $departureDate = \Carbon\Carbon::parse($travelDetails->departure_date);
                
                if ($checkOutDate->gt($departureDate)) {
                    $errorMessage = 'Check-out time cannot be after departure time (' . $departureDate->format('M d, Y g:i A') . ').';
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => ['check_out' => [$errorMessage]]
                        ], 422);
                    }
                    
                    return redirect()->back()->withErrors(['check_out' => $errorMessage])->withInput();
                }
            }
        }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Room allocation validation failed', [
                'participant_id' => $participant->id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        try {
            // Update or create room allocation
            $roomAllocation = $participant->roomAllocations()->first() ?: new RoomAllocation();
            $roomAllocation->hotel_id = $validated['hotel_id'];
            $roomAllocation->participant_id = $participant->id;
            $roomAllocation->room_number = $validated['room_number'] ?? null;
            $roomAllocation->number_of_beds = $validated['number_of_beds'] ?? null;
            
            // Only set check-in/check-out if they are provided and not empty
            if (!empty($validated['check_in'])) {
                $roomAllocation->check_in = $validated['check_in'];
            }
            if (!empty($validated['check_out'])) {
                $roomAllocation->check_out = $validated['check_out'];
            }
            
            $roomAllocation->save();

            // Sync room allocation data to travel details
            $this->syncRoomAllocationToTravelDetails($participant, $roomAllocation);

            \Log::info('Room allocation saved successfully', [
                'room_allocation_id' => $roomAllocation->id,
                'participant_id' => $participant->id
            ]);

            // Send room allocation notification
            if ($roomAllocation->hotel_id) {
                $travelNotificationService = new TravelNotificationService();
                $travelNotificationService->notifyRoomAllocated($participant, $roomAllocation);
            }

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room allocation updated successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Room allocation updated.');
        } catch (\Exception $e) {
            \Log::error('Room allocation save failed', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update room allocation: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update room allocation: ' . $e->getMessage());
        }
    }

    /**
     * Export Itinerary as CSV
     */
    public function exportItinerary()
    {
        $travelDetails = TravelDetail::with([
            'participant.user', 
            'hotel', 
            'participant.conference',
            'room',
            'room.roomType',
            'participant.roomAllocations'
        ])
        ->whereHas('participant') // Only include travel details with valid participants
        ->get();

        $filename = 'itinerary_' . date('Y-m-d_H-i-s') . '.csv';
        
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
                'Visa Status',
                'Itineraries Status',
                'Hotel',
                'Room Number',
                'Room Type',
                'Arrival Date',
                'Departure Date',
                'Takeoff Airport',
                'Room Check-in',
                'Room Check-out',
                'Flight Info',
                'Flight Info Details'
            ]);

            // CSV Data
            foreach ($travelDetails as $detail) {
                // Use travel details data for check-in/check-out times
                $checkIn = $detail->room_check_in;
                $checkOut = $detail->room_check_out;
                
                fputcsv($file, [
                    ($detail->participant->user->first_name ?? $detail->participant->user->name) . ' ' . ($detail->participant->user->last_name ?? ''),
                    $detail->participant->user->email,
                    $detail->participant->conference->name ?? 'N/A',
                    $detail->participant->visa_status ?? 'Not set',
                    $detail->itineraries_status ?? 'Not set',
                    $detail->hotel->name ?? 'N/A',
                    $detail->room->room_number ?? 'N/A',
                    $detail->room->roomType->name ?? 'N/A',
                    $detail->arrival_date ? date('Y-m-d H:i', strtotime($detail->arrival_date)) : 'N/A',
                    $detail->departure_date ? date('Y-m-d H:i', strtotime($detail->departure_date)) : 'N/A',
                    $detail->takeoff_airport ?? 'N/A',
                    $checkIn ? \Carbon\Carbon::parse($checkIn)->format('Y-m-d H:i') : 'N/A',
                    $checkOut ? \Carbon\Carbon::parse($checkOut)->format('Y-m-d H:i') : 'N/A',
                    $detail->flight_info ?? 'N/A',
                    $detail->flight_info_details ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get rooms for a specific hotel
     */
    public function getHotelRooms(Request $request, Hotel $hotel)
    {
        $rooms = $hotel->rooms()
            ->where('is_available', true)
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'room_type', 'beds', 'max_occupancy']);

        return response()->json([
            'success' => true,
            'rooms' => $rooms
        ]);
    }

    /**
     * Sync room allocation data to travel details
     * Always overwrites travel details with room allocation data
     */
    private function syncRoomAllocationToTravelDetails($participant, $roomAllocation)
    {
        try {
            $travelDetail = $participant->travelDetails()->first();
            
            if (!$travelDetail) {
                $travelDetail = new TravelDetail();
                $travelDetail->participant_id = $participant->id;
            }
            
            // Always update hotel and room information from room allocation
            $travelDetail->hotel_id = $roomAllocation->hotel_id;
            
            // Always update check-in/check-out times from room allocation (overwrite any existing values)
            $travelDetail->room_check_in = $roomAllocation->check_in;
            $travelDetail->room_check_out = $roomAllocation->check_out;
            
            $travelDetail->save();
            
            \Log::info('Room allocation synced to travel details (overwritten)', [
                'participant_id' => $participant->id,
                'room_allocation_id' => $roomAllocation->id,
                'travel_detail_id' => $travelDetail->id,
                'check_in' => $roomAllocation->check_in,
                'check_out' => $roomAllocation->check_out
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to sync room allocation to travel details', [
                'participant_id' => $participant->id,
                'room_allocation_id' => $roomAllocation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 