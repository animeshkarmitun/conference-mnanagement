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
     * Bulk delete participant profiles with user cleanup
     */
    public function bulkDelete(Request $request)
    {
        // Check permissions - allow admin, super_admin, and superadmin roles
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Access denied. Please log in.');
        }
        
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || 
                        in_array('super_admin', $userRoles) || 
                        in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return redirect()->back()->with('error', 'Access denied. Admin privileges required.');
        }

        // Validate request
        $validated = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
            'confirm_user_deletion' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Get participants to be deleted with their users
            $participants = Participant::whereIn('id', $validated['participant_ids'])
                ->with(['user', 'conference'])
                ->get();

            if ($participants->isEmpty()) {
                return redirect()->back()->with('error', 'No valid participants found for deletion.');
            }

            // Check for primary profiles in selection
            $primaryParticipants = $participants->where('is_primary', true);
            if ($primaryParticipants->isNotEmpty()) {
                return redirect()->back()->with('error', 'Cannot delete primary participant profiles in bulk operation.');
            }

            $deletedCount = 0;
            $usersToCleanup = collect();

            // Delete participants and collect users for cleanup
            foreach ($participants as $participant) {
                $userId = $participant->user_id;
                
                // Delete the participant (this will cascade to related data)
                $participant->delete();
                $deletedCount++;

                // Check if user has any remaining active participants
                $remainingParticipants = Participant::where('user_id', $userId)
                    ->where('status', 'active')
                    ->count();

                if ($remainingParticipants === 0) {
                    $usersToCleanup->push($userId);
                }
            }

            // Cleanup users who have no remaining participants
            $cleanupCount = 0;
            foreach ($usersToCleanup->unique() as $userId) {
                $this->cleanupUserData($userId);
                $cleanupCount++;
            }

            DB::commit();

            $message = "Successfully deleted {$deletedCount} participant profile(s).";
            if ($cleanupCount > 0) {
                $message .= " Also deleted {$cleanupCount} user(s) who had no remaining participant profiles.";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Bulk delete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete participants: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup user data when user has no remaining participants
     */
    private function cleanupUserData($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        try {
            // Delete user-related data in correct order to avoid foreign key constraints
            $user->roles()->detach(); // Remove role associations
            $user->tasks()->detach(); // Remove task assignments
            
            // Delete related records
            $user->assignedTasks()->delete();
            $user->createdTasks()->delete();
            $user->notifications()->delete();
            $user->communications()->delete();
            $user->comments()->delete();
            $user->passwordlessLogins()->delete();
            $user->emails()->delete();
            $user->conferenceConflicts()->delete();
            $user->participantProfileSessions()->delete();

            // Finally delete the user
            $user->delete();

            \Log::info("User cleanup completed for user ID: {$userId}");
        } catch (\Exception $e) {
            \Log::error("User cleanup failed for user ID {$userId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Bulk archive participant profiles
     */
    public function bulkArchive(Request $request)
    {
        // Check permissions - allow admin, super_admin, and superadmin roles
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Access denied. Please log in.');
        }
        
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || 
                        in_array('super_admin', $userRoles) || 
                        in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return redirect()->back()->with('error', 'Access denied. Admin privileges required.');
        }

        // Validate request
        $validated = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id'
        ]);

        try {
            // Get participants to be archived
            $participants = Participant::whereIn('id', $validated['participant_ids'])
                ->where('status', 'active')
                ->get();

            if ($participants->isEmpty()) {
                return redirect()->back()->with('error', 'No active participants found for archiving.');
            }

            // Check for primary profiles in selection
            $primaryParticipants = $participants->where('is_primary', true);
            if ($primaryParticipants->isNotEmpty()) {
                return redirect()->back()->with('error', 'Cannot archive primary participant profiles in bulk operation.');
            }

            $archivedCount = $participants->each(function($participant) {
                $participant->update(['status' => 'archived']);
            })->count();

            return redirect()->back()->with('success', "Successfully archived {$archivedCount} participant profile(s).");

        } catch (\Exception $e) {
            \Log::error('Bulk archive failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to archive participants: ' . $e->getMessage());
        }
    }

    /**
     * Bulk restore participant profiles
     */
    public function bulkRestore(Request $request)
    {
        // Check permissions - allow admin, super_admin, and superadmin roles
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Access denied. Please log in.');
        }
        
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = in_array('admin', $userRoles) || 
                        in_array('super_admin', $userRoles) || 
                        in_array('superadmin', $userRoles);
        
        if (!$hasPermission) {
            return redirect()->back()->with('error', 'Access denied. Admin privileges required.');
        }

        // Validate request
        $validated = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id'
        ]);

        try {
            // Get participants to be restored
            $participants = Participant::whereIn('id', $validated['participant_ids'])
                ->where('status', 'archived')
                ->get();

            if ($participants->isEmpty()) {
                return redirect()->back()->with('error', 'No archived participants found for restoration.');
            }

            $restoredCount = $participants->each(function($participant) {
                $participant->update(['status' => 'active']);
            })->count();

            return redirect()->back()->with('success', "Successfully restored {$restoredCount} participant profile(s).");

        } catch (\Exception $e) {
            \Log::error('Bulk restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restore participants: ' . $e->getMessage());
        }
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
