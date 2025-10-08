<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\Venue;
use App\Services\SessionNotificationService;
use App\Events\SessionEvent;
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
        // Don't load all participants initially - they will be loaded dynamically based on selected conference
        $participants = collect();
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
        // Debug: Log the incoming request data
        \Log::info('Session creation request data:', $request->all());
        
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'venue_id' => 'required|exists:venues,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'nullable|string|max:255',
            'participants' => 'nullable|string', // JSON string from enhanced interface
        ]);

        \Log::info('Validated data:', $validated);

        $session = Session::create($validated);
        \Log::info('Session created with ID:', ['session_id' => $session->id]);

        // Handle participants from enhanced interface
        if ($request->has('participants') && $request->participants) {
            \Log::info('Processing participants:', ['participants' => $request->participants]);
            $participantIds = json_decode($request->participants, true);
            \Log::info('Decoded participant IDs:', ['participant_ids' => $participantIds]);
            
            if (is_array($participantIds)) {
                // Create array with participant IDs as keys and default role as values
                $participantData = [];
                foreach ($participantIds as $participantId) {
                    $participantData[$participantId] = ['role' => 'participant'];
                }
                \Log::info('Participant data to sync:', $participantData);
                $session->participants()->sync($participantData);
                \Log::info('Participants synced successfully');
            }
        } else {
            \Log::info('No participants data received');
        }

        // Trigger session creation event to notify only participants assigned to this session
        $conference = Conference::find($session->conference_id);
        $message = "A new session '{$session->title}' has been added to {$conference->name}";
        event(new SessionEvent($session, 'session_created', $message, [
            'created_by' => auth()->user()->id,
            'conference_name' => $conference->name
        ]));

        return redirect()->route('sessions.index')
            ->with('success', 'Session created successfully.');
    }

    public function show(Session $session)
    {
        $session->load(['conference', 'participants.user']);
        \Log::info('Session show data:', [
            'session_id' => $session->id,
            'conference_id' => $session->conference_id,
            'conference_name' => $session->conference ? $session->conference->name : 'No conference',
            'participants_count' => $session->participants->count()
        ]);
        return view('sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        $conferences = Conference::all();
        // Load participants for the current session's conference
        $participants = Participant::with(['user', 'participantType'])
            ->where('conference_id', $session->conference_id)
            ->get();
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
            'participants' => 'nullable|string', // JSON string from enhanced interface
        ]);

        $session->update($validated);

        // Handle participants from enhanced interface
        if ($request->has('participants') && $request->participants) {
            $participantIds = json_decode($request->participants, true);
            if (is_array($participantIds)) {
                // Create array with participant IDs as keys and default role as values
                $participantData = [];
                foreach ($participantIds as $participantId) {
                    $participantData[$participantId] = ['role' => 'participant'];
                }
                $session->participants()->sync($participantData);
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

        // Trigger session update event to notify only participants assigned to this session
        $conference = Conference::find($session->conference_id);
        $message = "Session '{$session->title}' has been updated in {$conference->name}";
        event(new SessionEvent($session, 'session_updated', $message, [
            'updated_by' => auth()->user()->id,
            'conference_name' => $conference->name,
            'changes' => array_diff_assoc($validated, $oldData)
        ]));

        return redirect()->route('sessions.index')
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(Session $session)
    {
        // Store session data before deletion for notification
        $sessionTitle = $session->title;
        $conference = Conference::find($session->conference_id);
        $conferenceName = $conference ? $conference->name : 'Conference';
        
        // Trigger session deletion event to notify only participants assigned to this session
        $message = "Session '{$sessionTitle}' has been deleted from {$conferenceName}";
        event(new SessionEvent($session, 'session_deleted', $message, [
            'deleted_by' => auth()->user()->id,
            'conference_name' => $conferenceName,
            'session_title' => $sessionTitle
        ]));

        $session->participants()->detach();
        $session->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Session deleted successfully.');
    }

    /**
     * Export sessions to CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status', 'all');
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
                      ->orderBy('end_time', 'asc');
                break;
                
            case 'upcoming':
                $query->where('start_time', '>', $now)
                      ->orderBy('start_time', 'asc');
                break;
                
            case 'finished':
                $query->where('end_time', '<', $now)
                      ->orderBy('end_time', 'desc');
                break;
                
            case 'all':
            default:
                $query->orderBy('start_time', 'asc');
                break;
        }
        
        $sessions = $query->get();
        
        $filename = 'sessions_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($sessions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Title',
                'Description',
                'Conference',
                'Start Time',
                'End Time',
                'Duration (minutes)',
                'Status',
                'Participants Count',
                'Created At',
                'Updated At'
            ]);
            
            // CSV Data
            foreach ($sessions as $session) {
                $timeData = \App\Helpers\DateHelper::formatSessionTime($session->start_time, $session->end_time);
                
                if ($timeData['is_active']) {
                    $statusText = 'Active';
                } elseif ($timeData['is_past']) {
                    $statusText = 'Finished';
                } else {
                    $statusText = 'Upcoming';
                }
                
                fputcsv($file, [
                    $session->id,
                    $session->title,
                    $session->description ?? '',
                    $session->conference->name ?? 'N/A',
                    $session->start_time->format('Y-m-d H:i:s'),
                    $session->end_time->format('Y-m-d H:i:s'),
                    $timeData['duration_minutes'],
                    $statusText,
                    $session->participants->count(),
                    $session->created_at->format('Y-m-d H:i:s'),
                    $session->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get participants for a specific conference
     */
    public function getParticipantsByConference(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['participants' => []]);
        }

        $participants = Participant::with(['user', 'participantType'])
            ->where('conference_id', $conferenceId)
            ->get()
            ->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'name' => ($participant->user->first_name ?? $participant->user->name) . ' ' . ($participant->user->last_name ?? ''),
                    'email' => $participant->user->email,
                    'organization' => $participant->user->organization ?? '',
                    'type' => $participant->participantType->name ?? '',
                    'type_id' => $participant->participant_type_id
                ];
            });

        return response()->json(['participants' => $participants]);
    }
} 