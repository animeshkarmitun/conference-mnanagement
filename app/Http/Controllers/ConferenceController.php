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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue_id' => 'required|exists:venues,id',
            'location' => 'required|string|max:255',
        ]);
        Conference::create($validated);
        return redirect()->route('conferences.index')->with('success', 'Conference created successfully.');
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue_id' => 'required|exists:venues,id',
        ]);
        $conference->update($validated);
        return redirect()->route('conferences.index')->with('success', 'Conference updated successfully.');
    }

    public function destroy(Conference $conference)
    {
        $conference->delete();
        return redirect()->route('conferences.index')->with('success', 'Conference deleted successfully.');
    }
} 