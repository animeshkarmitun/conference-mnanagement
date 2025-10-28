<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Venue;
use App\Services\ConferenceNotificationService;
use App\Services\ConferenceConflictService;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    protected $conflictService;

    public function __construct(ConferenceConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
        
        // Restrict all conference management to admins only (except API methods)
        $this->middleware(function ($request, $next) {
            // Skip middleware for API methods
            if ($request->routeIs('api.conferences')) {
                return $next($request);
            }
            
            if (!auth()->user() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $status = $request->get('status', 'upcoming'); // Default to upcoming conferences
        $conferenceId = $request->get('conference_id');
        $now = now();
        
        $query = Conference::with('venue');
        
        // Filter by specific conference if selected
        if ($conferenceId) {
            $query->where('id', $conferenceId);
        }
        
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
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
            });
        }
        
        $conferences = $query->paginate(10)->withQueryString();
        $search = $request->get('search', '');
        
        // Get conference counts for each category
        $conferenceCounts = [
            'active' => Conference::where('start_date', '<=', $now)
                                  ->where('end_date', '>=', $now)
                                  ->count(),
            'upcoming' => Conference::where('start_date', '>', $now)->count(),
            'finished' => Conference::where('end_date', '<', $now)->count(),
            'all' => Conference::count(),
        ];
        
        // Get all conferences for the filter dropdown
        $allConferences = Conference::orderBy('name')->get();
        
        return view('conferences.index', compact('conferences', 'conferenceCounts', 'status', 'search', 'allConferences', 'conferenceId'));
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
            'venue_id' => 'required|exists:venues,id',
            'sessions_json' => 'nullable|string',
        ]);

        // Use database transaction to ensure data consistency
        return \DB::transaction(function () use ($request, $conferenceValidated) {
            // Create conference with the venue ID
            $conference = Conference::create($conferenceValidated);

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

                // Server-side overlap validation per venue among incoming sessions
                $byVenue = [];
                foreach ($sessions as $i => $s) {
                    $byVenue[$s['venue_id']][] = array_merge($s, ['__index' => $i]);
                }
                foreach ($byVenue as $venueId => $list) {
                    usort($list, function ($a, $b) {
                        return strtotime($a['start_time']) <=> strtotime($b['start_time']);
                    });
                    for ($i = 1; $i < count($list); $i++) {
                        $prev = $list[$i - 1];
                        $curr = $list[$i];
                        if (strtotime($curr['start_time']) < strtotime($prev['end_time'])) {
                            $msg = 'Session overlaps with another session at the same venue.';
                            return back()
                                ->withErrors([
                                    "sessions.".$curr['__index'].".start_time" => $msg,
                                    "sessions.".$curr['__index'].".end_time" => $msg,
                                ])
                                ->withInput($request->all());
                        }
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
        $conference->load(['sessions' => function ($q) {
            $q->withCount('participants');
        }]);
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

            // Handle sessions changes if provided
            $incoming = [];
            if ($request->filled('sessions_json')) {
                try {
                    $incoming = json_decode($request->input('sessions_json'), true) ?: [];
                } catch (\Throwable $e) {
                    $incoming = [];
                }
            }

            if (!empty($incoming)) {
                // Validate items
                foreach ($incoming as $idx => $item) {
                    $action = $item['_action'] ?? 'update';
                    if (!in_array($action, ['create','update','delete'], true)) {
                        return back()->withErrors(["sessions.$idx._action" => 'Invalid session action.'])->withInput($request->all());
                    }

                    if ($action === 'delete') {
                        // Only id required
                        $validator = \Validator::make($item, [
                            'id' => 'required|exists:sessions,id',
                        ], [], [ 'id' => "sessions.$idx.id" ]);
                        if ($validator->fails()) {
                            return back()->withErrors($validator)->withInput($request->all());
                        }
                        continue;
                    }

                    // create/update validations
                    $validator = \Validator::make($item, [
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
                        return back()->withErrors($validator)->withInput($request->all());
                    }
                }

                // Range validation against updated conference dates
                $conferenceStart = \Carbon\Carbon::parse($conference->start_date)->startOfDay();
                $conferenceEnd = \Carbon\Carbon::parse($conference->end_date)->endOfDay();
                foreach ($incoming as $idx => $item) {
                    if (($item['_action'] ?? 'update') === 'delete') continue;
                    $sessionStart = \Carbon\Carbon::parse($item['start_time']);
                    $sessionEnd = \Carbon\Carbon::parse($item['end_time']);
                    if ($sessionStart->lt($conferenceStart) || $sessionEnd->gt($conferenceEnd)) {
                        $message = 'Session times must be within the conference dates.';
                        return back()->withErrors([
                            "sessions.$idx.start_time" => $message,
                            "sessions.$idx.end_time" => $message,
                        ])->withInput($request->all());
                    }
                }

                // Per-venue overlap among incoming creates/updates only
                $sets = [];
                foreach ($incoming as $i => $it) {
                    if (($it['_action'] ?? 'update') === 'delete') continue;
                    $sets[$it['venue_id']][] = array_merge($it, ['__index' => $i]);
                }
                foreach ($sets as $venueIdKey => $list) {
                    usort($list, function ($a, $b) {
                        return strtotime($a['start_time']) <=> strtotime($b['start_time']);
                    });
                    for ($i = 1; $i < count($list); $i++) {
                        $prev = $list[$i - 1];
                        $curr = $list[$i];
                        if (strtotime($curr['start_time']) < strtotime($prev['end_time'])) {
                            $msg = 'Session overlaps with another session at the same venue.';
                            return back()->withErrors([
                                'sessions.'.$curr['__index'].'.start_time' => $msg,
                                'sessions.'.$curr['__index'].'.end_time' => $msg,
                            ])->withInput($request->all());
                        }
                    }
                }

                // Apply changes
                foreach ($incoming as $item) {
                    $action = $item['_action'] ?? 'update';
                    if ($action === 'create') {
                        \App\Models\Session::create([
                            'conference_id' => $conference->id,
                            'title' => $item['title'],
                            'description' => $item['description'] ?? null,
                            'start_time' => $item['start_time'],
                            'end_time' => $item['end_time'],
                            'venue_id' => $item['venue_id'],
                            'seating_arrangement' => $item['seating_arrangement'] ?? null,
                        ]);
                    } elseif ($action === 'update') {
                        $session = \App\Models\Session::where('conference_id', $conference->id)->findOrFail($item['id']);
                        $session->update([
                            'title' => $item['title'],
                            'description' => $item['description'] ?? null,
                            'start_time' => $item['start_time'],
                            'end_time' => $item['end_time'],
                            'venue_id' => $item['venue_id'],
                            'seating_arrangement' => $item['seating_arrangement'] ?? null,
                        ]);
                    } elseif ($action === 'delete') {
                        $session = \App\Models\Session::where('conference_id', $conference->id)->findOrFail($item['id']);
                        $session->delete();
                    }
                }
            }

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

    public function getDeletionInfo(Conference $conference)
    {
        // Get counts of related data
        $relatedData = [
            'participants' => $conference->participants()->count(),
            'sessions' => $conference->sessions()->count(),
            'tasks' => $conference->tasks()->count(),
            'notifications' => $conference->notifications()->count(),
            'communications' => $conference->communications()->count(),
            'checkins' => $conference->checkins()->count(),
            'conference_docs' => $conference->conferenceDocs()->count(),
        ];
        
        // Get participant user information for deletion option
        $participantUsersInfo = null;
        if ($relatedData['participants'] > 0) {
            $participantUsers = $conference->participants()
                ->with('user')
                ->get()
                ->groupBy('user_id')
                ->map(function ($userParticipants) {
                    $user = $userParticipants->first()->user;
                    $totalParticipants = $user->participants()->count();
                    $singleParticipant = $totalParticipants === 1;
                    
                    return [
                        'user_id' => $user->id,
                        'user_name' => $user->first_name . ' ' . $user->last_name,
                        'user_email' => $user->email,
                        'total_participants' => $totalParticipants,
                        'single_participant' => $singleParticipant,
                        'conference_participants' => $userParticipants->count()
                    ];
                });
            
            $singleParticipantUsers = $participantUsers->where('single_participant', true);
            
            $participantUsersInfo = [
                'total_users' => $participantUsers->count(),
                'single_participant_users' => $singleParticipantUsers->count(),
                'single_participant_users_list' => $singleParticipantUsers->values()->toArray()
            ];
        }
        
        // Get venue information
        $venueInfo = null;
        if ($conference->venue) {
            $venueInfo = [
                'id' => $conference->venue->id,
                'name' => $conference->venue->name,
                'address' => $conference->venue->address,
                'capacity' => $conference->venue->capacity,
                'other_conferences_count' => $conference->venue->conferences()->where('id', '!=', $conference->id)->count(),
                'other_conferences' => $conference->venue->conferences()->where('id', '!=', $conference->id)->get(['id', 'name', 'start_date', 'end_date'])
            ];
        }
        
        return response()->json([
            'conference' => [
                'id' => $conference->id,
                'name' => $conference->name,
                'start_date' => $conference->start_date,
                'end_date' => $conference->end_date,
            ],
            'venue' => $venueInfo,
            'related_data' => $relatedData,
            'participant_users_info' => $participantUsersInfo,
            'has_related_data' => array_sum($relatedData) > 0
        ]);
    }

    public function destroy(Conference $conference, Request $request)
    {
        // Get counts of related data before deletion
        $participantCount = $conference->participants()->count();
        $sessionCount = $conference->sessions()->count();
        $taskCount = $conference->tasks()->count();
        $notificationCount = $conference->notifications()->count();
        $communicationCount = $conference->communications()->count();
        $checkinCount = $conference->checkins()->count();
        $conferenceDocCount = $conference->conferenceDocs()->count();
        
        // Store related data counts for potential rollback
        $relatedData = [
            'participants' => $participantCount,
            'sessions' => $sessionCount,
            'tasks' => $taskCount,
            'notifications' => $notificationCount,
            'communications' => $communicationCount,
            'checkins' => $checkinCount,
            'conference_docs' => $conferenceDocCount,
        ];
        
        // Handle venue deletion if requested
        $venueDeleted = false;
        $venueName = null;
        if ($request->has('delete_venue') && $request->delete_venue == '1' && $conference->venue) {
            // Check if venue is used by other conferences
            $otherConferencesCount = $conference->venue->conferences()->where('id', '!=', $conference->id)->count();
            
            if ($otherConferencesCount == 0) {
                $venueName = $conference->venue->name;
                $conference->venue->delete();
                $venueDeleted = true;
            }
        }
        
        // Handle participant user deletion if requested
        $deletedUsersCount = 0;
        if ($request->has('delete_participant_users') && $request->delete_participant_users == '1') {
            // Get participants with their users
            $participants = $conference->participants()->with('user')->get();
            
            // Group by user_id and check which users have only this conference's participants
            $userParticipantCounts = $participants->groupBy('user_id')->map(function ($userParticipants) {
                $user = $userParticipants->first()->user;
                $totalParticipants = $user->participants()->count();
                return [
                    'user' => $user,
                    'total_participants' => $totalParticipants,
                    'conference_participants' => $userParticipants->count(),
                    'single_participant' => $totalParticipants === $userParticipants->count()
                ];
            });
            
            // Delete users who have only this conference's participants
            foreach ($userParticipantCounts as $userInfo) {
                if ($userInfo['single_participant']) {
                    // Delete the user (this will cascade to participants due to foreign key constraints)
                    $userInfo['user']->delete();
                    $deletedUsersCount++;
                }
            }
        }
        
        // Manually delete sessions before deleting conference
        $deletedSessionsCount = 0;
        if ($sessionCount > 0) {
            $sessions = $conference->sessions()->get();
            foreach ($sessions as $session) {
                // Store session data before deletion for notification
                $sessionTitle = $session->title;
                $conferenceName = $conference->name;
                
                // Trigger session deletion event to notify participants
                $message = "Session '{$sessionTitle}' has been deleted from {$conferenceName}";
                event(new \App\Events\SessionEvent($session, 'session_deleted', $message, [
                    'deleted_by' => auth()->user()->id,
                    'conference_name' => $conferenceName,
                    'deleted_via_conference' => true
                ]));
                
                // Delete the session
                $session->delete();
                $deletedSessionsCount++;
            }
        }
        
        // Log the deletion with related data counts
        \Log::info('Conference deletion initiated', [
            'conference_id' => $conference->id,
            'conference_name' => $conference->name,
            'related_data' => $relatedData,
            'venue_deleted' => $venueDeleted,
            'venue_name' => $venueName,
            'participant_users_deleted' => $deletedUsersCount,
            'sessions_deleted_manually' => $deletedSessionsCount
        ]);
        
        $conference->delete();
        
        // Create detailed success message
        $message = 'Conference "' . $conference->name . '" deleted successfully.';
        
        $removedItems = [];
        if ($participantCount > 0) $removedItems[] = "{$participantCount} participant(s)";
        if ($sessionCount > 0) $removedItems[] = "{$sessionCount} session(s)";
        if ($taskCount > 0) $removedItems[] = "{$taskCount} task(s)";
        if ($notificationCount > 0) $removedItems[] = "{$notificationCount} notification(s)";
        if ($communicationCount > 0) $removedItems[] = "{$communicationCount} communication(s)";
        if ($checkinCount > 0) $removedItems[] = "{$checkinCount} checkin(s)";
        if ($conferenceDocCount > 0) $removedItems[] = "{$conferenceDocCount} document(s)";
        if ($venueDeleted) $removedItems[] = "venue '{$venueName}'";
        if ($deletedUsersCount > 0) $removedItems[] = "{$deletedUsersCount} user account(s)";
        
        if (!empty($removedItems)) {
            $message .= ' Related data also removed: ' . implode(', ', $removedItems) . '.';
        }
        
        return redirect()->route('conferences.index')->with('success', $message);
    }

    public function export(Request $request)
    {
        $status = $request->get('status', 'upcoming'); // Match index default
        $now = now();
        
        $query = Conference::with('venue');
        
        // Filter conferences based on status - EXACT same logic as index method
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

        // Apply search filter if provided - EXACT same logic as index method
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
            });
        }

        // Get ALL results that match the current filters (no pagination limit)
        $conferences = $query->get();

        $filename = 'conferences_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($conferences) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Description',
                'Start Date',
                'End Date',
                'Duration (Days)',
                'Location',
                'Venue Name',
                'Venue Address',
                'Venue Capacity',
                'Status',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($conferences as $conference) {
                $conferenceData = \App\Helpers\DateHelper::formatConferenceDates($conference->start_date, $conference->end_date);
                $statusText = \App\Helpers\DateHelper::getConferenceStatusText(
                    $conferenceData['is_active'], 
                    $conferenceData['is_past'], 
                    $conferenceData['is_today'], 
                    $conferenceData['is_upcoming']
                );

                fputcsv($file, [
                    $conference->id,
                    $conference->name,
                    $conference->description ?? '',
                    $conference->start_date,
                    $conference->end_date,
                    $conferenceData['duration_days'],
                    $conference->location,
                    $conference->venue->name ?? 'N/A',
                    $conference->venue->address ?? 'N/A',
                    $conference->venue->capacity ?? 'N/A',
                    $statusText,
                    $conference->created_at->format('Y-m-d H:i:s'),
                    $conference->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check for conflicts when creating or updating a conference
     */
    public function checkConflicts(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $excludeConferenceId = $request->input('exclude_conference_id');

        if (!$startDate || !$endDate) {
            return response()->json(['conflicts' => []]);
        }

        $conflictingConferences = $this->conflictService->validateConferenceDates($startDate, $endDate, $excludeConferenceId);

        return response()->json([
            'has_conflicts' => $conflictingConferences->isNotEmpty(),
            'conflicts' => $conflictingConferences->map(function ($conference) {
                return [
                    'id' => $conference->id,
                    'name' => $conference->name,
                    'start_date' => $conference->start_date,
                    'end_date' => $conference->end_date,
                    'location' => $conference->location,
                ];
            })->toArray()
        ]);
    }

    /**
     * Get conflicts for a specific conference
     */
    public function getConferenceConflicts($conferenceId)
    {
        $conflicts = $this->conflictService->getConferenceConflicts($conferenceId);

        return response()->json([
            'conflicts' => $conflicts->map(function ($conflict) {
                return [
                    'id' => $conflict->id,
                    'user' => $conflict->user->first_name . ' ' . $conflict->user->last_name,
                    'participant' => $conflict->participant->getProfileDisplayName(),
                    'conflicting_conference' => $conflict->conflictingConference->name,
                    'conflict_type' => $conflict->conflict_type,
                    'conflict_details' => $conflict->conflict_details,
                    'status' => $conflict->status,
                    'created_at' => $conflict->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray()
        ]);
    }

    /**
     * Resolve a conference conflict
     */
    public function resolveConflict(Request $request, $conflictId)
    {
        $resolutionNotes = $request->input('resolution_notes');
        $action = $request->input('action'); // 'resolve' or 'ignore'

        if ($action === 'resolve') {
            $this->conflictService->resolveConflict($conflictId, $resolutionNotes, auth()->id());
            $message = 'Conflict resolved successfully.';
        } else {
            $this->conflictService->ignoreConflict($conflictId, $resolutionNotes, auth()->id());
            $message = 'Conflict ignored successfully.';
        }

        return back()->with('success', $message);
    }

    /**
     * Get conferences for API (used by passwordless login modal)
     */
    public function getConferencesForApi()
    {
        // Skip the constructor middleware for this API method
        $conferences = Conference::orderBy('name')->get(['id', 'name', 'start_date', 'end_date']);
        
        return response()->json([
            'success' => true,
            'conferences' => $conferences
        ]);
    }
} 