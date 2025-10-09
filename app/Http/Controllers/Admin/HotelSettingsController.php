<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotels = Hotel::with(['rooms'])
            ->orderBy('name')
            ->paginate(15);
            
        return view('admin.settings.hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.settings.hotels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'room_capacity' => 'required|integer|min:1',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'amenities' => 'nullable|array',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Hotel::create($validated);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        $hotel->load(['rooms.roomType']);
        $roomTypes = \App\Models\RoomType::where('is_active', true)->get(['id', 'name', 'default_beds']);
        return view('admin.settings.hotels.show', compact('hotel', 'roomTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        return view('admin.settings.hotels.edit', compact('hotel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'room_capacity' => 'required|integer|min:1',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'amenities' => 'nullable|array',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $hotel->update($validated);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel deleted successfully!');
    }
}
