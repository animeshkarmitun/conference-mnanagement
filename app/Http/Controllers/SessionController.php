<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['conference', 'participants'])->latest()->paginate(10);
        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        $conferences = Conference::all();
        $participants = Participant::with(['user', 'participantType'])->get();
        $conferenceVenues = Conference::pluck('venue_id', 'id');
        $venues = Venue::all();
        $conferenceDates = Conference::all()->mapWithKeys(function($conf) {
            return [$conf->id => [
                'start_date' => $conf->start_date,
                'end_date' => $conf->end_date,
            ]];
        });
        
        // Get participant types for filter
        $participantTypes = \App\Models\ParticipantType::all();
        
        // Get unique organizations for filter
        $organizations = \App\Models\User::whereNotNull('organization')
            ->distinct()
            ->pluck('organization')
            ->filter()
            ->sort()
            ->values();
        
        return view('sessions.create', compact(
            'conferences', 
            'participants', 
            'conferenceVenues', 
            'venues', 
            'conferenceDates',
            'participantTypes',
            'organizations'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'venue_id' => 'required|exists:venues,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'participants' => 'nullable|string', // JSON string from enhanced interface
        ]);

        $session = Session::create($validated);

        // Handle participants from enhanced interface
        if ($request->has('participants') && $request->participants) {
            $participantIds = json_decode($request->participants, true);
            if (is_array($participantIds)) {
                $session->participants()->sync($participantIds);
            }
        }

        return redirect()->route('sessions.index')
            ->with('success', 'Session created successfully.');
    }

    public function show(Session $session)
    {
        $session->load(['conference', 'participants.user']);
        return view('sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        $conferences = Conference::all();
        $participants = Participant::with(['user', 'participantType'])->get();
        $conferenceVenues = Conference::pluck('venue_id', 'id');
        $venues = Venue::all();
        $conferenceDates = Conference::all()->mapWithKeys(function($conf) {
            return [$conf->id => [
                'start_date' => $conf->start_date,
                'end_date' => $conf->end_date,
            ]];
        });
        
        // Get participant types for filter
        $participantTypes = \App\Models\ParticipantType::all();
        
        // Get unique organizations for filter
        $organizations = \App\Models\User::whereNotNull('organization')
            ->distinct()
            ->pluck('organization')
            ->filter()
            ->sort()
            ->values();
        
        return view('sessions.edit', compact(
            'session', 
            'conferences', 
            'participants', 
            'conferenceVenues', 
            'venues', 
            'conferenceDates',
            'participantTypes',
            'organizations'
        ));
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'venue_id' => 'required|exists:venues,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'participants' => 'nullable|string', // JSON string from enhanced interface
        ]);

        $session->update($validated);

        // Handle participants from enhanced interface
        if ($request->has('participants') && $request->participants) {
            $participantIds = json_decode($request->participants, true);
            if (is_array($participantIds)) {
                $session->participants()->sync($participantIds);
            }
        } else {
            $session->participants()->detach();
        }

        return redirect()->route('sessions.index')
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(Session $session)
    {
        $session->participants()->detach();
        $session->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Session deleted successfully.');
    }
} 