<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    /**
     * Store a newly created hotel via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'room_capacity' => 'required|integer|min:1',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        try {
            $hotel = Hotel::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Hotel created successfully!',
                'hotel' => [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'address' => $hotel->address,
                    'room_capacity' => $hotel->room_capacity,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create hotel. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rooms for a specific hotel
     */
    public function getRooms(Hotel $hotel): JsonResponse
    {
        try {
            $rooms = $hotel->rooms()->where('is_available', true)->get();
            
            return response()->json([
                'success' => true,
                'rooms' => $rooms->map(function($room) {
                    return [
                        'id' => $room->id,
                        'room_number' => $room->room_number,
                        'room_type' => $room->room_type,
                        'beds' => $room->beds,
                        'price_per_night' => $room->price_per_night,
                        'description' => $room->description,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load rooms.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
