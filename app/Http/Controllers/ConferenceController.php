<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Venue;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function index()
    {
        $conferences = Conference::with('venue')->latest()->paginate(20);
        return view('conferences.index', compact('conferences'));
    }

    public function create()
    {
        $venues = Venue::all();
        return view('conferences.create', compact('venues'));
    }

    public function store(Request $request)
    {
        // Validate basic conference data
        $conferenceValidated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'venue_type' => 'required|in:existing,new',
        ]);

        // Validate venue data based on type
        if ($request->venue_type === 'existing') {
            $request->validate([
                'venue_id' => 'required|exists:venues,id',
            ]);
        } else {
            $request->validate([
                'venue_name' => 'required|string|max:255',
                'venue_address' => 'required|string|max:500',
                'venue_capacity' => 'required|integer|min:1',
            ]);
        }

        // Use database transaction to ensure data consistency
        return \DB::transaction(function () use ($request, $conferenceValidated) {
            $venueId = null;

            if ($request->venue_type === 'existing') {
                // Use existing venue
                $venueId = $request->venue_id;
            } else {
                // Create new venue
                $venue = Venue::create([
                    'name' => $request->venue_name,
                    'address' => $request->venue_address,
                    'capacity' => $request->venue_capacity,
                ]);
                $venueId = $venue->id;
            }

            // Create conference with the venue ID
            $conferenceData = array_merge($conferenceValidated, ['venue_id' => $venueId]);
            Conference::create($conferenceData);

            return redirect()->route('conferences.index')->with('success', 'Conference created successfully.');
        });
    }

    public function show(Conference $conference)
    {
        $conference->load('venue');
        return view('conferences.show', compact('conference'));
    }

    public function edit(Conference $conference)
    {
        $venues = Venue::all();
        return view('conferences.edit', compact('conference', 'venues'));
    }

    public function update(Request $request, Conference $conference)
    {
        // Validate basic conference data
        $conferenceValidated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'venue_type' => 'required|in:existing,new',
        ]);

        // Validate venue data based on type
        if ($request->venue_type === 'existing') {
            $request->validate([
                'venue_id' => 'required|exists:venues,id',
            ]);
        } else {
            $request->validate([
                'venue_name' => 'required|string|max:255',
                'venue_address' => 'required|string|max:500',
                'venue_capacity' => 'required|integer|min:1',
            ]);
        }

        // Use database transaction to ensure data consistency
        return \DB::transaction(function () use ($request, $conferenceValidated, $conference) {
            $venueId = null;

            if ($request->venue_type === 'existing') {
                // Use existing venue
                $venueId = $request->venue_id;
            } else {
                // Create new venue
                $venue = Venue::create([
                    'name' => $request->venue_name,
                    'address' => $request->venue_address,
                    'capacity' => $request->venue_capacity,
                ]);
                $venueId = $venue->id;
            }

            // Update conference with the venue ID
            $conferenceData = array_merge($conferenceValidated, ['venue_id' => $venueId]);
            $conference->update($conferenceData);

            return redirect()->route('conferences.index')->with('success', 'Conference updated successfully.');
        });
    }

    public function destroy(Conference $conference)
    {
        $conference->delete();
        return redirect()->route('conferences.index')->with('success', 'Conference deleted successfully.');
    }
} 