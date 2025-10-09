<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_number' => 'required|string|max:50',
            'room_type_id' => 'nullable|exists:room_types,id',
            'beds' => 'nullable|integer|min:1|max:10',
            'price_per_night' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        // Check for unique room number within the hotel
        $existingRoom = Room::where('hotel_id', $validated['hotel_id'])
            ->where('room_number', $validated['room_number'])
            ->first();

        if ($existingRoom) {
            return redirect()->back()->withErrors(['room_number' => 'Room number already exists in this hotel.']);
        }

        $validated['is_available'] = $request->has('is_available');

        Room::create($validated);

        return redirect()->back()->with('success', 'Room created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return response()->json([
            'success' => true,
            'room' => $room
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'room_type_id' => 'nullable|exists:room_types,id',
            'beds' => 'nullable|integer|min:1|max:10',
            'price_per_night' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        // Check for unique room number within the hotel (excluding current room)
        $existingRoom = Room::where('hotel_id', $room->hotel_id)
            ->where('room_number', $validated['room_number'])
            ->where('id', '!=', $room->id)
            ->first();

        if ($existingRoom) {
            return redirect()->back()->withErrors(['room_number' => 'Room number already exists in this hotel.']);
        }

        $validated['is_available'] = $request->has('is_available');

        $room->update($validated);

        return redirect()->back()->with('success', 'Room updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Check if room has any allocations
        if ($room->getRoomAllocationsCount() > 0) {
            return redirect()->back()->with('error', 'Cannot delete room that has been assigned to participants.');
        }

        $room->delete();

        return redirect()->back()->with('success', 'Room deleted successfully!');
    }
}
