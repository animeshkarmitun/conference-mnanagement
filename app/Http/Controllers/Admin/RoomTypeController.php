<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::orderBy('name')->paginate(15);
        return view('admin.settings.room-types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.settings.room-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:room_types,name',
            'description' => 'nullable|string',
            'default_beds' => 'required|integer|min:1|max:10',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        RoomType::create($validated);

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomType $roomType)
    {
        $roomType->load('rooms.hotel');
        return view('admin.settings.room-types.show', compact('roomType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomType $roomType)
    {
        return view('admin.settings.room-types.edit', compact('roomType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:room_types,name,' . $roomType->id,
            'description' => 'nullable|string',
            'default_beds' => 'required|integer|min:1|max:10',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $roomType->update($validated);

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType)
    {
        // Check if room type is being used by any rooms
        if ($roomType->rooms()->count() > 0) {
            return redirect()->route('admin.room-types.index')
                ->with('error', 'Cannot delete room type that is being used by rooms.');
        }

        $roomType->delete();

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type deleted successfully!');
    }
}
