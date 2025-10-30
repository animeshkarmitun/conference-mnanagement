<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\Venue;
use App\Events\SessionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function __construct()
    {
        // Permission-gated access for sessions
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) {
                abort(403, 'Unauthorized');
            }

            if ($user->hasRole('superadmin')) {
                return $next($request);
            }

            $action = $request->route()->getActionMethod();
            $permissionMap = [
                'index' => 'sessions.view',
                'show' => 'sessions.view',
                'create' => 'sessions.create',
                'store' => 'sessions.create',
                'edit' => 'sessions.edit',
                'update' => 'sessions.edit',
                'destroy' => 'sessions.delete',
                'publish' => 'sessions.publish',
                'export' => 'sessions.export',
                'resendEmail' => 'sessions.view',
                'resendEmailToParticipant' => 'sessions.view',
                'resendEmailToAll' => 'sessions.view',
                'getParticipantsByConference' => 'sessions.view',
                'autoSaveDraft' => 'sessions.edit',
                'checkParticipantConflicts' => 'sessions.view',
                'testConflictDetection' => 'sessions.view',
            ];

            $needed = $permissionMap[$action] ?? 'sessions.view';
            if (!$user->hasPermission($needed)) {
                abort(403, 'Access denied. Missing permission: ' . $needed);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // Default to all sessions
        $conferenceId = $request->get('conference_id');
        $sessionStatus = $request->get('session_status'); // New parameter for draft/published
        $sessionTitle = $request->get('session_title'); // New parameter for session title search
        $now = now();
        
        $query = Session::with(['conference', 'participants']);
        
        // Filter by conference if specified
        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }
        
        // Filter by session status (draft/published) if specified
        if ($sessionStatus && in_array($sessionStatus, ['draft', 'published'])) {
            $query->where('status', $sessionStatus);
        }
        
        // Filter by session title if specified
        if ($sessionTitle && !empty(trim($sessionTitle))) {
            $query->where(function($q) use ($sessionTitle) {
                $q->where('title', 'like', '%' . $sessionTitle . '%')
                  ->orWhere('description', 'like', '%' . $sessionTitle . '%');
            });
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
        
        // Get session counts for each category (with conference and title filters if applied)
        $countQuery = Session::query();
        if ($conferenceId) {
            $countQuery->where('conference_id', $conferenceId);
        }
        if ($sessionTitle && !empty(trim($sessionTitle))) {
            $countQuery->where(function($q) use ($sessionTitle) {
                $q->where('title', 'like', '%' . $sessionTitle . '%')
                  ->orWhere('description', 'like', '%' . $sessionTitle . '%');
            });
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
        
        return view('sessions.index', compact('sessions', 'sessionCounts', 'status', 'conferences', 'sessionTitle'));
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
            'status' => 'nullable|in:draft,published',
        ]);

        // Set default status to draft if not provided
        $validated['status'] = $validated['status'] ?? 'draft';

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

        // No email notifications for session creation

        $message = $session->status === 'published' 
            ? 'Session published successfully.' 
            : 'Session saved as draft successfully.';

        return redirect()->route('sessions.index')
            ->with('success', $message);
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
        // Load participants for the current session's conference, excluding already selected ones
        $selectedParticipantIds = $session->participants->pluck('id')->toArray();
        $participants = Participant::with(['user', 'participantType'])
            ->where('conference_id', $session->conference_id)
            ->whereNotIn('id', $selectedParticipantIds)
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
            'status' => 'nullable|in:draft,published',
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

        // No email notifications for session updates

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
        $sessionTitle = $request->get('session_title');
        $now = now();
        
        $query = Session::with(['conference', 'participants']);
        
        // Filter by conference if specified
        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }
        
        // Filter by session title if specified
        if ($sessionTitle && !empty(trim($sessionTitle))) {
            $query->where(function($q) use ($sessionTitle) {
                $q->where('title', 'like', '%' . $sessionTitle . '%')
                  ->orWhere('description', 'like', '%' . $sessionTitle . '%');
            });
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
        $search = $request->get('search', '');
        
        if (!$conferenceId) {
            return response()->json(['participants' => []]);
        }

        $query = Participant::with(['user', 'participantType'])
            ->where('conference_id', $conferenceId);

        // Add search functionality including hashtags, bio, designation, field of work, and country
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('organization_institution', 'like', "%{$search}%")
                              ->orWhere('designation', 'like', "%{$search}%")
                              ->orWhere('field_of_work_study', 'like', "%{$search}%")
                              ->orWhere('country', 'like', "%{$search}%");
                })
                ->orWhere('organization', 'like', "%{$search}%")
                ->orWhere('hashtags', 'like', "%{$search}%")
                ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        $participants = $query->get()
            ->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'name' => ($participant->user->first_name ?? $participant->user->name) . ' ' . ($participant->user->last_name ?? ''),
                    'email' => $participant->user->email,
                    'organization' => $participant->user->organization_institution ?? $participant->user->organization ?? '',
                    'type' => $participant->participantType->name ?? '',
                    'type_id' => $participant->participant_type_id,
                    'hashtags' => $participant->hashtags ?? '',
                    'bio' => $participant->bio ?? '',
                    'designation' => $participant->user->designation ?? '',
                    'field_of_work_study' => $participant->user->field_of_work_study ?? '',
                    'country' => $participant->user->country ?? ''
                ];
            });

        return response()->json(['participants' => $participants]);
    }

    /**
     * Auto-save session as draft
     */
    public function autoSaveDraft(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'venue_id' => 'required|exists:venues,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'nullable|string|max:255',
            'participants' => 'nullable|string',
            'draft_session_id' => 'nullable|exists:sessions,id', // For updating existing draft
        ]);

        // Check if all required fields are filled
        $requiredFields = ['conference_id', 'venue_id', 'title', 'start_time', 'end_time'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($validated[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields: ' . implode(', ', $missingFields),
                'missing_fields' => $missingFields
            ], 422);
        }

        // Check if we're updating an existing draft
        $draftSessionId = $request->input('draft_session_id');
        $isUpdate = false;
        
        if ($draftSessionId) {
            $existingDraft = Session::where('id', $draftSessionId)
                ->where('status', 'draft')
                ->first();
                
            if ($existingDraft) {
                // Update existing draft
                $existingDraft->update($validated);
                $session = $existingDraft;
                $isUpdate = true;
            } else {
                // Draft doesn't exist or is not a draft, create new one
                $session = Session::create(array_merge($validated, ['status' => 'draft']));
            }
        } else {
            // Check if there's already a draft for this user (optional: you might want to limit to one draft per user)
            $existingDraft = Session::where('status', 'draft')
                ->where('conference_id', $validated['conference_id'])
                ->where('title', $validated['title'])
                ->first();
                
            if ($existingDraft) {
                // Update existing draft
                $existingDraft->update($validated);
                $session = $existingDraft;
                $isUpdate = true;
            } else {
                // Create new draft
                $session = Session::create(array_merge($validated, ['status' => 'draft']));
            }
        }

        // Handle participants
        if ($request->has('participants') && $request->participants) {
            $participantIds = json_decode($request->participants, true);
            if (is_array($participantIds)) {
                $participantData = [];
                foreach ($participantIds as $participantId) {
                    $participantData[$participantId] = ['role' => 'participant'];
                }
                $session->participants()->sync($participantData);
            }
        }

        $message = $isUpdate ? 'Draft updated successfully' : 'Draft saved successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'session_id' => $session->id,
            'status' => 'draft',
            'is_update' => $isUpdate
        ]);
    }

    /**
     * Publish session
     */
    public function publish(Request $request, Session $session)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'venue_id' => 'required|exists:venues,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'room' => 'nullable|string|max:255',
            'participants' => 'nullable|string',
        ]);

        // Update session to published
        $session->update(array_merge($validated, ['status' => 'published']));

        // Handle participants
        if ($request->has('participants') && $request->participants) {
            $participantIds = json_decode($request->participants, true);
            if (is_array($participantIds)) {
                $participantData = [];
                foreach ($participantIds as $participantId) {
                    $participantData[$participantId] = ['role' => 'participant'];
                }
                $session->participants()->sync($participantData);
            }
        }

        // No email notifications for session publishing

        return response()->json([
            'success' => true,
            'message' => 'Session published successfully',
            'session_id' => $session->id,
            'status' => 'published'
        ]);
    }

    /**
     * Check for participant session conflicts
     */
    public function checkParticipantConflicts(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Conflict check request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'participant_ids' => 'required|array',
                'participant_ids.*' => 'required|exists:participants,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'session_id' => 'nullable|exists:sessions,id', // For editing existing session
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Conflict check validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'has_conflicts' => false,
                'conflicts' => [],
                'total_conflicts' => 0,
                'validation_error' => $e->errors()
            ], 422);
        }
        
        \Log::info('Conflict check validated data:', $validated);

        $participantIds = $validated['participant_ids'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        $sessionId = $validated['session_id'] ?? null;

        \Log::info("Conflict check parameters:", [
            'participant_ids' => $participantIds,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'session_id' => $sessionId
        ]);

        // Debug: Check all sessions (published and draft)
        $allSessions = \App\Models\Session::whereIn('status', ['published', 'draft'])
            ->with(['participants', 'conference'])
            ->get();
        \Log::info("All sessions (published and draft):", $allSessions->toArray());

        $conflicts = [];

        foreach ($participantIds as $participantId) {
            \Log::info("Checking conflicts for participant ID: {$participantId}");
            
            $participant = Participant::with(['user', 'sessions' => function($query) use ($startTime, $endTime, $sessionId) {
                $query->where(function($q) use ($startTime, $endTime) {
                    // Check for overlapping sessions
                    $q->where(function($timeQuery) use ($startTime, $endTime) {
                        // Session starts before our end time and ends after our start time
                        $timeQuery->where('start_time', '<', $endTime)
                                 ->where('end_time', '>', $startTime);
                    });
                })
                ->whereIn('status', ['published', 'draft']) // Check both published and draft sessions
                ->when($sessionId, function($q) use ($sessionId) {
                    // Exclude current session when editing
                    $q->where('sessions.id', '!=', $sessionId);
                });
            }])->find($participantId);

            \Log::info("Participant found: " . ($participant ? 'Yes' : 'No'));
            if ($participant) {
                \Log::info("Participant sessions count: " . $participant->sessions->count());
                \Log::info("Participant sessions: " . json_encode($participant->sessions->toArray()));
            }

            if ($participant && $participant->sessions->count() > 0) {
                $conflictingSessions = $participant->sessions->map(function($session) {
                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'start_time' => $session->start_time->format('Y-m-d H:i'),
                        'end_time' => $session->end_time->format('Y-m-d H:i'),
                        'status' => $session->status,
                        'conference' => $session->conference->name ?? 'Unknown Conference'
                    ];
                });

                $conflicts[] = [
                    'participant_id' => $participantId,
                    'participant_name' => ($participant->user->first_name ?? $participant->user->name) . ' ' . ($participant->user->last_name ?? ''),
                    'participant_email' => $participant->user->email,
                    'conflicting_sessions' => $conflictingSessions
                ];
            }
        }

        \Log::info("Final conflicts result:", [
            'has_conflicts' => count($conflicts) > 0,
            'conflicts' => $conflicts,
            'total_conflicts' => count($conflicts)
        ]);

        return response()->json([
            'has_conflicts' => count($conflicts) > 0,
            'conflicts' => $conflicts,
            'total_conflicts' => count($conflicts)
        ]);
    }

    /**
     * Test method to debug conflict detection
     */
    public function testConflictDetection(Request $request)
    {
        $participantId = $request->get('participant_id');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        if (!$participantId || !$startTime || !$endTime) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $participant = Participant::with(['user', 'sessions' => function($query) {
            $query->whereIn('status', ['published', 'draft']);
        }])->find($participantId);

        if (!$participant) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        // Check for time overlaps manually
        $overlappingSessions = [];
        foreach ($participant->sessions as $session) {
            $sessionStart = $session->start_time;
            $sessionEnd = $session->end_time;
            
            // Check if sessions overlap
            if ($sessionStart < $endTime && $sessionEnd > $startTime) {
                $overlappingSessions[] = [
                    'id' => $session->id,
                    'title' => $session->title,
                    'start_time' => $sessionStart->format('Y-m-d H:i:s'),
                    'end_time' => $sessionEnd->format('Y-m-d H:i:s'),
                    'conference' => $session->conference->name ?? 'Unknown'
                ];
            }
        }

        return response()->json([
            'participant' => [
                'id' => $participant->id,
                'name' => ($participant->user->first_name ?? $participant->user->name) . ' ' . ($participant->user->last_name ?? ''),
                'email' => $participant->user->email
            ],
            'check_time_range' => [
                'start' => $startTime,
                'end' => $endTime
            ],
            'all_sessions' => $participant->sessions->map(function($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'start_time' => $session->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $session->end_time->format('Y-m-d H:i:s'),
                    'status' => $session->status,
                    'conference' => $session->conference->name ?? 'Unknown'
                ];
            }),
            'overlapping_sessions' => $overlappingSessions,
            'has_conflicts' => count($overlappingSessions) > 0
        ]);
    }

    /**
     * Resend session email to a specific participant
     */
    public function resendEmailToParticipant(Request $request, Session $session, $participant)
    {
        // Check permissions
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            // Find the participant
            $participantModel = $session->participants()->where('participant_id', $participant)->first();
            
            if (!$participantModel) {
                return response()->json(['success' => false, 'message' => 'Participant not found in this session'], 404);
            }

            // Create passwordless login token for this participant
            $passwordlessToken = null;
            if ($participantModel->user) {
                $passwordlessLoginService = app(\App\Services\PasswordlessLoginService::class);
                $conference = $session->conference;
                $token = $passwordlessLoginService->generateLoginLink($participantModel->user, 72, $conference);
                $passwordlessToken = [
                    'user_id' => $participantModel->user->id,
                    'token' => $token->token,
                    'login_url' => $token->getLoginUrl()
                ];
            }

            // Send email notification with passwordless login link
            $conference = $session->conference;
            $message = "A session '{$session->title}' has been published in {$conference->name}";
            
            event(new SessionEvent($session, 'session_created', $message, [
                'created_by' => auth()->user()->id,
                'conference_name' => $conference->name,
                'passwordless_tokens' => [$passwordlessToken],
                'is_resend' => true,
                'single_participant' => true,
                'target_participant_id' => $participantModel->id
            ]));

            // Update individual participant email tracking
            $tracking = \App\Models\ParticipantSessionEmailTracking::getOrCreateTracking($session->id, $participantModel->id);
            $tracking->incrementEmailCount();
            $tracking->update([
                'email_recipients' => [$participantModel->user->email]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session email resent successfully to ' . $participantModel->user->first_name . ' ' . $participantModel->user->last_name,
                'participant_id' => $participantModel->id,
                'email_count' => $tracking->email_send_count,
                'last_sent' => $tracking->last_email_sent_at->format('M d, Y H:i:s')
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to resend session email to participant', [
                'session_id' => $session->id,
                'participant_id' => $participant,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend session email to all participants
     */
    public function resendEmailToAll(Request $request, Session $session)
    {
        // Check permissions
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            // Get participants assigned to this session
            $participants = $session->participants()->with('user')->get();
            
            if ($participants->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No participants assigned to this session'], 400);
            }

            // Create passwordless login tokens for all participants
            $passwordlessTokens = [];
            $conference = $session->conference;
            
            foreach ($participants as $participant) {
                if ($participant->user) {
                    $passwordlessLoginService = app(\App\Services\PasswordlessLoginService::class);
                    $token = $passwordlessLoginService->generateLoginLink($participant->user, 72, $conference);
                    $passwordlessTokens[] = [
                        'user_id' => $participant->user->id,
                        'token' => $token->token,
                        'login_url' => $token->getLoginUrl()
                    ];
                }
            }

            // Send email notifications with passwordless login links
            $conference = $session->conference;
            $message = "A session '{$session->title}' has been published in {$conference->name}";
            
            event(new SessionEvent($session, 'session_created', $message, [
                'created_by' => auth()->user()->id,
                'conference_name' => $conference->name,
                'passwordless_tokens' => $passwordlessTokens,
                'is_resend' => true,
                'resend_all' => true
            ]));

            // Update individual participant email tracking for each participant
            $results = [];
            foreach ($participants as $participant) {
                $tracking = \App\Models\ParticipantSessionEmailTracking::getOrCreateTracking($session->id, $participant->id);
                $tracking->incrementEmailCount();
                $tracking->update([
                    'email_recipients' => [$participant->user->email]
                ]);
                $results[] = [
                    'participant_id' => $participant->id,
                    'email_count' => $tracking->email_send_count
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Session email resent successfully to ' . $participants->count() . ' participants',
                'participant_count' => $participants->count(),
                'results' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to resend session email to all participants', [
                'session_id' => $session->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend emails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend session email to participants (legacy method for backward compatibility)
     */
    public function resendEmail(Request $request, Session $session)
    {
        // Redirect to the new resendEmailToAll method for backward compatibility
        return $this->resendEmailToAll($request, $session);
    }

    /**
     * Get sessions for API (used by passwordless login modal)
     */
    public function getSessions(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        $query = Session::with('venue');
        
        if ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }
        
        $sessions = $query->orderBy('start_time')->get([
            'id', 'title', 'start_time', 'end_time', 'conference_id', 'venue_id'
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get participants for selected sessions with email tracking info
     */
    public function getSessionParticipants(Request $request)
    {
        $sessionIds = $request->input('session_ids', []);
        
        if (empty($sessionIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No sessions selected'
            ], 400);
        }
        
        // Get participants who are assigned to any of the selected sessions
        $participants = Participant::whereHas('participantSessions', function ($query) use ($sessionIds) {
            $query->whereIn('sessions.id', $sessionIds);
        })
        ->with(['user', 'participantType', 'participantSessions' => function ($query) use ($sessionIds) {
            $query->whereIn('sessions.id', $sessionIds);
        }])
        ->get()
        ->map(function ($participant) use ($sessionIds) {
            // Get email tracking info for this participant across selected sessions
            $emailTracking = \App\Models\ParticipantSessionEmailTracking::where('participant_id', $participant->id)
                ->whereIn('session_id', $sessionIds)
                ->get();
            
            $totalEmailCount = $emailTracking->sum('email_send_count');
            $lastEmailSent = $emailTracking->max('last_email_sent_at');
            
            // Get participant types for selected sessions
            $participantTypes = $participant->participantSessions
                ->map(function ($session) {
                    return $session->pivot->role;
                })
                ->unique()
                ->implode(', ');
            
            return [
                'id' => $participant->user->id,
                'participant_id' => $participant->id,
                'first_name' => $participant->user->first_name,
                'last_name' => $participant->user->last_name,
                'email' => $participant->user->email,
                'participant_types' => $participantTypes,
                'email_send_count' => $totalEmailCount,
                'last_email_sent_at' => $lastEmailSent,
                'sessions' => $participant->participantSessions->pluck('id')->toArray()
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $participants->unique('id')->values() // Remove duplicates by user ID
        ]);
    }
} 