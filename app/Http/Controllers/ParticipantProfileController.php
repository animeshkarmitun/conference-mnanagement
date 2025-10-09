<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Models\ParticipantType;
use App\Services\ConferenceConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParticipantProfileController extends Controller
{
    protected $conflictService;

    public function __construct(ConferenceConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

    /**
     * Display all participant profiles for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $participants = $user->participants()
            ->with(['conference', 'participantType'])
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $conferences = Conference::active()->get();
        $participantTypes = ParticipantType::all();

        return view('participant-profiles.index', compact('participants', 'conferences', 'participantTypes'));
    }

    /**
     * Show the form for creating a new participant profile
     */
    public function create()
    {
        // Restrict to admins only
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $conferences = Conference::active()->get();
        $participantTypes = ParticipantType::all();
        $user = Auth::user();

        return view('participant-profiles.create', compact('conferences', 'participantTypes', 'user'));
    }

    /**
     * Store a newly created participant profile
     */
    public function store(Request $request)
    {
        // Restrict to admins only
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'participant_type_id' => 'required|exists:participant_types,id',
            'profile_name' => 'required|string|max:255',
            'profile_type' => 'required|in:personal,professional,academic,media,speaker',
            'profile_description' => 'nullable|string|max:1000',
            'visa_status' => 'nullable|in:required,not_required,pending,approved,issue',
            'visa_issue_description' => 'nullable|string|max:1000',
            'bio' => 'nullable|string',
            'organization' => 'nullable|string|max:255',
            'travel_intent' => 'nullable|in:national,international',
            'registration_status' => 'nullable|in:pending,approved,rejected',
            'category' => 'nullable|string|max:50',
        ]);

        // Check for conflicts before creating
        $conflicts = $this->conflictService->checkUserConferenceConflicts($user->id, $validated['conference_id']);

        if ($conflicts->isNotEmpty() && !$request->has('ignore_conflicts')) {
            return back()
                ->withErrors(['conflict' => 'Conference conflicts detected. Please review and resolve conflicts before proceeding.'])
                ->with('conflicts', $conflicts)
                ->withInput($request->all());
        }

        DB::beginTransaction();
        try {
            $participant = Participant::create(array_merge($validated, [
                'user_id' => $user->id,
                'status' => 'active',
                'is_primary' => false, // New profiles are not primary by default
            ]));

            // If this is the first participant for the user, make it primary
            if ($user->participants()->count() === 1) {
                $participant->update(['is_primary' => true]);
            }

            // Store conflicts if any
            if ($conflicts->isNotEmpty()) {
                foreach ($conflicts as $conflict) {
                    $this->conflictService->storeConflict([
                        'user_id' => $user->id,
                        'participant_id' => $participant->id,
                        'conference_id' => $validated['conference_id'],
                        'conflicting_conference_id' => $conflict['conference']->id,
                        'conflict_type' => $conflict['type'],
                        'conflict_details' => $conflict['details'],
                        'status' => 'pending'
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('participant-profiles.index')
                ->with('success', 'Participant profile created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['error' => 'Failed to create participant profile. Please try again.'])
                ->withInput($request->all());
        }
    }

    /**
     * Switch to a different participant profile
     */
    public function switch(Request $request, $participantId)
    {
        $user = Auth::user();
        $participant = $user->participants()->find($participantId);

        if (!$participant || $participant->status !== 'active') {
            return back()->withErrors(['error' => 'Invalid participant profile selected.']);
        }

        if ($user->setActiveParticipantProfile($participantId)) {
            return back()->with('success', 'Switched to ' . $participant->getProfileDisplayName());
        }

        return back()->withErrors(['error' => 'Failed to switch participant profile.']);
    }

    /**
     * Set a participant as primary
     */
    public function setPrimary($participantId)
    {
        $user = Auth::user();
        $participant = $user->participants()->find($participantId);

        if (!$participant || $participant->status !== 'active') {
            return back()->withErrors(['error' => 'Invalid participant profile selected.']);
        }

        DB::beginTransaction();
        try {
            // Remove primary status from all other participants
            $user->participants()->update(['is_primary' => false]);

            // Set this participant as primary
            $participant->update(['is_primary' => true]);

            DB::commit();

            return back()->with('success', 'Primary participant profile updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update primary participant profile.']);
        }
    }

    /**
     * Archive a participant profile
     */
    public function archive($participantId)
    {
        $user = Auth::user();
        $participant = $user->participants()->find($participantId);

        if (!$participant) {
            return back()->withErrors(['error' => 'Invalid participant profile selected.']);
        }

        if ($participant->is_primary) {
            return back()->withErrors(['error' => 'Cannot archive primary participant profile.']);
        }

        $participant->update(['status' => 'archived']);

        return back()->with('success', 'Participant profile archived successfully.');
    }

    /**
     * Restore an archived participant profile
     */
    public function restore($participantId)
    {
        $user = Auth::user();
        $participant = $user->participants()->find($participantId);

        if (!$participant) {
            return back()->withErrors(['error' => 'Invalid participant profile selected.']);
        }

        $participant->update(['status' => 'active']);

        return back()->with('success', 'Participant profile restored successfully.');
    }

    /**
     * Delete a participant profile permanently
     */
    public function destroy($participantId)
    {
        $user = Auth::user();
        $participant = $user->participants()->find($participantId);

        if (!$participant) {
            return back()->withErrors(['error' => 'Invalid participant profile selected.']);
        }

        if ($participant->is_primary) {
            return back()->withErrors(['error' => 'Cannot delete primary participant profile.']);
        }

        $participant->delete();

        return back()->with('success', 'Participant profile deleted successfully.');
    }

    /**
     * Check conflicts for a specific conference
     */
    public function checkConflicts(Request $request)
    {
        $user = Auth::user();
        $conferenceId = $request->input('conference_id');

        if (!$conferenceId) {
            return response()->json(['conflicts' => []]);
        }

        $conflicts = $this->conflictService->checkUserConferenceConflicts($user->id, $conferenceId);

        return response()->json([
            'has_conflicts' => $conflicts->isNotEmpty(),
            'conflicts' => $conflicts->toArray()
        ]);
    }

    /**
     * Get suggested resolutions for a conflict
     */
    public function getConflictResolutions($conflictId)
    {
        $user = Auth::user();
        $conflict = $user->conferenceConflicts()->find($conflictId);

        if (!$conflict) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = $this->conflictService->getSuggestedResolutions($conflictId);

        return response()->json([
            'suggestions' => $suggestions->toArray()
        ]);
    }

    /**
     * Resolve a conflict
     */
    public function resolveConflict(Request $request, $conflictId)
    {
        $user = Auth::user();
        $conflict = $user->conferenceConflicts()->find($conflictId);

        if (!$conflict) {
            return back()->withErrors(['error' => 'Conflict not found.']);
        }

        $resolutionNotes = $request->input('resolution_notes');
        $action = $request->input('action'); // 'resolve' or 'ignore'

        if ($action === 'resolve') {
            $this->conflictService->resolveConflict($conflictId, $resolutionNotes, $user->id);
            $message = 'Conflict resolved successfully.';
        } else {
            $this->conflictService->ignoreConflict($conflictId, $resolutionNotes, $user->id);
            $message = 'Conflict ignored successfully.';
        }

        return back()->with('success', $message);
    }
}
