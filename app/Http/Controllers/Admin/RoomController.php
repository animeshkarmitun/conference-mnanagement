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
        \Log::info('Room creation request received', [
            'method' => $request->method(),
            'url' => $request->url(),
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        try {
            $validated = $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'room_number' => 'required|string|max:50',
                'room_type_id' => 'nullable|exists:room_types,id',
                'beds' => 'nullable|integer|min:1|max:10',
                'description' => 'nullable|string',
                'is_available' => 'nullable',
            ]);
            
            \Log::info('Validation passed, validated data:', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Check for unique room number within the hotel
        $existingRoom = Room::where('hotel_id', $validated['hotel_id'])
            ->where('room_number', $validated['room_number'])
            ->first();

        if ($existingRoom) {
            return redirect()->back()->withErrors(['room_number' => 'Room number already exists in this hotel.']);
        }

        $validated['is_available'] = $request->has('is_available');
        
        // Set room_type based on room_type_id if provided
        if (!empty($validated['room_type_id'])) {
            $roomType = \App\Models\RoomType::find($validated['room_type_id']);
            $validated['room_type'] = $roomType ? $roomType->name : 'Standard';
        } else {
            $validated['room_type'] = 'Standard';
        }

        try {
            $room = Room::create($validated);
            \Log::info('Room created successfully with ID:', ['room_id' => $room->id]);
            return redirect()->back()->with('success', 'Room created successfully!');
        } catch (\Exception $e) {
            \Log::error('Room creation failed:', ['error' => $e->getMessage(), 'data' => $validated]);
            return redirect()->back()->withErrors(['error' => 'Failed to create room: ' . $e->getMessage()]);
        }
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
            'description' => 'nullable|string',
            'is_available' => 'nullable',
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
        
        // Set room_type based on room_type_id if provided
        if (!empty($validated['room_type_id'])) {
            $roomType = \App\Models\RoomType::find($validated['room_type_id']);
            $validated['room_type'] = $roomType ? $roomType->name : 'Standard';
        } else {
            $validated['room_type'] = 'Standard';
        }

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
