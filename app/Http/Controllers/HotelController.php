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
}
