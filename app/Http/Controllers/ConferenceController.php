<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Venue;
use App\Services\ConferenceNotificationService;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'upcoming'); // Default to upcoming conferences
        $now = now();
        
        $query = Conference::with('venue');
        
        // Filter conferences based on status
        switch ($status) {
            case 'active':
                $query->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now)
                      ->orderBy('end_date', 'asc'); // Ending soonest first
                break;
                
            case 'upcoming':
                $query->where('start_date', '>', $now)
                      ->orderBy('start_date', 'asc'); // Starting soonest first
                break;
                
            case 'finished':
                $query->where('end_date', '<', $now)
                      ->orderBy('end_date', 'desc'); // Most recent first
                break;
                
            case 'all':
            default:
                $query->orderBy('start_date', 'asc'); // Default ordering
                break;
        }
        
        $conferences = $query->paginate(10);
        
        // Get conference counts for each category
        $conferenceCounts = [
            'active' => Conference::where('start_date', '<=', $now)
                                  ->where('end_date', '>=', $now)
                                  ->count(),
            'upcoming' => Conference::where('start_date', '>', $now)->count(),
            'finished' => Conference::where('end_date', '<', $now)->count(),
            'all' => Conference::count(),
        ];
        
        return view('conferences.index', compact('conferences', 'conferenceCounts', 'status'));
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
            'sessions_json' => 'nullable|string',
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
            $conference = Conference::create($conferenceData);

            // Handle sessions creation if provided
            $sessions = [];
            if ($request->filled('sessions_json')) {
                try {
                    $sessions = json_decode($request->input('sessions_json'), true) ?: [];
                } catch (\Throwable $e) {
                    $sessions = [];
                }
            }

            if (!empty($sessions)) {
                // Validate each session entry
                foreach ($sessions as $idx => $session) {
                    $validator = \Validator::make($session, [
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'start_time' => 'required|date',
                        'end_time' => 'required|date|after:start_time',
                        'venue_id' => 'required|exists:venues,id',
                        'seating_arrangement' => 'nullable|string',
                    ], [], [
                        'title' => "sessions.$idx.title",
                        'start_time' => "sessions.$idx.start_time",
                        'end_time' => "sessions.$idx.end_time",
                        'venue_id' => "sessions.$idx.venue_id",
                    ]);

                    if ($validator->fails()) {
                        // Bubble up with old input preserved
                        return back()
                            ->withErrors($validator)
                            ->withInput($request->all());
                    }
                }

                // Additional range validation against conference dates
                $conferenceStart = \Carbon\Carbon::parse($conference->start_date)->startOfDay();
                $conferenceEnd = \Carbon\Carbon::parse($conference->end_date)->endOfDay();

                foreach ($sessions as $idx => $session) {
                    $sessionStart = \Carbon\Carbon::parse($session['start_time']);
                    $sessionEnd = \Carbon\Carbon::parse($session['end_time']);

                    if ($sessionStart->lt($conferenceStart) || $sessionEnd->gt($conferenceEnd)) {
                        $message = "Session times must be within the conference dates.";
                        return back()
                            ->withErrors([
                                "sessions.$idx.start_time" => $message,
                                "sessions.$idx.end_time" => $message,
                            ])
                            ->withInput($request->all());
                    }
                }

                foreach ($sessions as $session) {
                    \App\Models\Session::create([
                        'conference_id' => $conference->id,
                        'title' => $session['title'],
                        'description' => $session['description'] ?? null,
                        'start_time' => $session['start_time'],
                        'end_time' => $session['end_time'],
                        'venue_id' => $session['venue_id'],
                        'seating_arrangement' => $session['seating_arrangement'] ?? null,
                    ]);
                }
            }

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
        // Store old data for comparison
        $oldData = [
            'start_date' => $conference->start_date,
            'end_date' => $conference->end_date,
            'venue_id' => $conference->venue_id,
        ];

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
        return \DB::transaction(function () use ($request, $conferenceValidated, $conference, $oldData) {
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

            // Send notifications if dates or venue changed
            $conferenceNotificationService = new ConferenceNotificationService();
            
            // Check for date changes
            $conferenceNotificationService->notifyConferenceDatesUpdated($conference, $oldData, $conferenceData);
            
            // Check for venue changes
            if ($oldData['venue_id'] !== $venueId) {
                $oldVenue = $conference->venue ? $conference->venue->name : 'TBD';
                $newVenue = \App\Models\Venue::find($venueId) ? \App\Models\Venue::find($venueId)->name : 'TBD';
                $conferenceNotificationService->notifyConferenceVenueUpdated($conference, $oldVenue, $newVenue);
            }

            return redirect()->route('conferences.index')->with('success', 'Conference updated successfully.');
        });
    }

    public function destroy(Conference $conference)
    {
        $conference->delete();
        return redirect()->route('conferences.index')->with('success', 'Conference deleted successfully.');
    }
} 