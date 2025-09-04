<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\Venue;
use App\Services\SessionNotificationService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // Default to all sessions
        $conferenceId = $request->get('conference_id');
        $now = now();
        
        $query = Session::with(['conference', 'participants']);
        
        // Filter by conference if specified
        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }
        
        // Filter sessions based on status
        switch ($status) {
            case 'active':
                $query->where('start_time', '<=', $now)
                      ->where('end_time', '>=', $now)
                      ->orderBy('end_time', 'asc'); // Ending soonest first
                break;
                
            case 'upcoming':
                $query->where('start_time', '>', $now)
                      ->orderBy('start_time', 'asc'); // Starting soonest first
                break;
                
            case 'finished':
                $query->where('end_time', '<', $now)
                      ->orderBy('end_time', 'desc'); // Most recent first
                break;
                
            case 'all':
            default:
                $query->orderBy('start_time', 'asc'); // Default ordering
                break;
        }
        
        $sessions = $query->paginate(10);
        
        // Get session counts for each category (with conference filter if applied)
        $countQuery = Session::query();
        if ($conferenceId) {
            $countQuery->where('conference_id', $conferenceId);
        }
        
        $sessionCounts = [
            'active' => (clone $countQuery)->where('start_time', '<=', $now)
                              ->where('end_time', '>=', $now)
                              ->count(),
            'upcoming' => (clone $countQuery)->where('start_time', '>', $now)->count(),
            'finished' => (clone $countQuery)->where('end_time', '<', $now)->count(),
            'all' => $countQuery->count(),
        ];
        
        // Get all conferences for the filter dropdown
        $conferences = Conference::orderBy('name')->get();
        
        return view('sessions.index', compact('sessions', 'sessionCounts', 'status', 'conferences'));
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
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
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
        // Store old data for comparison
        $oldData = [
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
            'room' => $session->room,
        ];

        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'venue_id' => 'required|exists:venues,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
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

        // Send notifications if dates or venue changed
        $sessionNotificationService = new SessionNotificationService();
        
        // Check for date changes
        $sessionNotificationService->notifySessionDatesUpdated($session, $oldData, $validated);
        
        // Check for venue/room changes
        if (($oldData['room'] ?? '') !== ($validated['room'] ?? '')) {
            $oldVenue = $oldData['room'] ?? 'TBD';
            $newVenue = $validated['room'] ?? 'TBD';
            $sessionNotificationService->notifySessionVenueUpdated($session, $oldVenue, $newVenue);
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