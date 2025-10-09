<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\ConferenceConflict;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConferenceConflictService
{
    /**
     * Check for conflicts when a user tries to register for a conference
     */
    public function checkUserConferenceConflicts($userId, $conferenceId, $excludeParticipantId = null)
    {
        $user = User::find($userId);
        $conference = Conference::find($conferenceId);

        if (!$user || !$conference) {
            return collect();
        }

        $conflicts = collect();

        // Get all active participants of the user
        $userParticipants = $user->participants()
            ->where('status', 'active')
            ->with('conference')
            ->get();

        foreach ($userParticipants as $participant) {
            if ($excludeParticipantId && $participant->id === $excludeParticipantId) {
                continue;
            }

            $participantConference = $participant->conference;
            if (!$participantConference) {
                continue;
            }

            // Check for date overlap
            if ($conference->hasDateOverlap($participantConference)) {
                $conflicts->push([
                    'type' => 'date_overlap',
                    'participant' => $participant,
                    'conference' => $participantConference,
                    'details' => "Conference dates overlap: {$conference->name} ({$conference->start_date} - {$conference->end_date}) conflicts with {$participantConference->name} ({$participantConference->start_date} - {$participantConference->end_date})"
                ]);
            }
        }

        return $conflicts;
    }

    /**
     * Validate conference dates against existing conferences
     */
    public function validateConferenceDates($startDate, $endDate, $excludeConferenceId = null)
    {
        $query = Conference::where('id', '!=', $excludeConferenceId);

        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                       ->where('end_date', '>=', $endDate);
              });
        })->get();
    }

    /**
     * Get detailed conflict information for a user
     */
    public function getConflictDetails($userId, $conferenceId)
    {
        $user = User::find($userId);
        $conference = Conference::find($conferenceId);

        if (!$user || !$conference) {
            return collect();
        }

        $conflicts = $this->checkUserConferenceConflicts($userId, $conferenceId);

        // Store conflicts in database
        foreach ($conflicts as $conflict) {
            $this->storeConflict([
                'user_id' => $userId,
                'participant_id' => $conflict['participant']->id,
                'conference_id' => $conferenceId,
                'conflicting_conference_id' => $conflict['conference']->id,
                'conflict_type' => $conflict['type'],
                'conflict_details' => $conflict['details'],
                'status' => 'pending'
            ]);
        }

        return $conflicts;
    }

    /**
     * Store conflict in database
     */
    public function storeConflict($conflictData)
    {
        // Check if conflict already exists
        $existingConflict = ConferenceConflict::where([
            'user_id' => $conflictData['user_id'],
            'participant_id' => $conflictData['participant_id'],
            'conference_id' => $conflictData['conference_id'],
            'conflicting_conference_id' => $conflictData['conflicting_conference_id'],
        ])->first();

        if ($existingConflict) {
            return $existingConflict;
        }

        return ConferenceConflict::create($conflictData);
    }

    /**
     * Resolve a conflict
     */
    public function resolveConflict($conflictId, $resolutionNotes = null, $resolvedBy = null)
    {
        $conflict = ConferenceConflict::find($conflictId);
        
        if (!$conflict) {
            return false;
        }

        $conflict->resolve($resolutionNotes, $resolvedBy);
        return true;
    }

    /**
     * Ignore a conflict
     */
    public function ignoreConflict($conflictId, $notes = null, $resolvedBy = null)
    {
        $conflict = ConferenceConflict::find($conflictId);
        
        if (!$conflict) {
            return false;
        }

        $conflict->ignore($notes, $resolvedBy);
        return true;
    }

    /**
     * Get all pending conflicts for a user
     */
    public function getUserPendingConflicts($userId)
    {
        return ConferenceConflict::where('user_id', $userId)
            ->where('status', 'pending')
            ->with(['conference', 'conflictingConference', 'participant'])
            ->get();
    }

    /**
     * Get conflicts for a specific conference
     */
    public function getConferenceConflicts($conferenceId)
    {
        return ConferenceConflict::where('conference_id', $conferenceId)
            ->orWhere('conflicting_conference_id', $conferenceId)
            ->where('status', 'pending')
            ->with(['user', 'participant', 'conference', 'conflictingConference'])
            ->get();
    }

    /**
     * Check if a participant can be created without conflicts
     */
    public function canCreateParticipant($userId, $conferenceId, $participantData = [])
    {
        $conflicts = $this->checkUserConferenceConflicts($userId, $conferenceId);
        
        // If there are conflicts, check if they can be resolved
        if ($conflicts->isNotEmpty()) {
            // Check if user has other active participants in the same conference
            $existingParticipants = User::find($userId)
                ->participants()
                ->where('conference_id', $conferenceId)
                ->where('status', 'active')
                ->count();

            // If no existing participants, allow creation but flag conflicts
            return $existingParticipants === 0;
        }

        return true;
    }

    /**
     * Get suggested resolutions for a conflict
     */
    public function getSuggestedResolutions($conflictId)
    {
        $conflict = ConferenceConflict::with(['conference', 'conflictingConference', 'participant'])->find($conflictId);
        
        if (!$conflict) {
            return collect();
        }

        $suggestions = collect();

        // Suggestion 1: Use different participant profile
        $user = $conflict->user;
        $availableProfiles = $user->participants()
            ->where('conference_id', '!=', $conflict->conference_id)
            ->where('conference_id', '!=', $conflict->conflicting_conference_id)
            ->where('status', 'active')
            ->get();

        if ($availableProfiles->isNotEmpty()) {
            $suggestions->push([
                'type' => 'use_different_profile',
                'title' => 'Use Different Participant Profile',
                'description' => 'Switch to a different participant profile that doesn\'t have conflicts',
                'profiles' => $availableProfiles
            ]);
        }

        // Suggestion 2: Reschedule one of the conferences
        $suggestions->push([
            'type' => 'reschedule_conference',
            'title' => 'Reschedule Conference',
            'description' => 'Contact conference organizers to reschedule one of the conflicting conferences',
            'conferences' => [$conflict->conference, $conflict->conflictingConference]
        ]);

        // Suggestion 3: Archive conflicting participant
        $suggestions->push([
            'type' => 'archive_participant',
            'title' => 'Archive Conflicting Participant',
            'description' => 'Archive the conflicting participant profile to resolve the conflict',
            'participant' => $conflict->participant
        ]);

        return $suggestions;
    }

    /**
     * Clean up resolved conflicts older than specified days
     */
    public function cleanupOldConflicts($daysOld = 30)
    {
        $cutoffDate = now()->subDays($daysOld);
        
        return ConferenceConflict::where('status', 'resolved')
            ->where('resolved_at', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Get conflict statistics for dashboard
     */
    public function getConflictStatistics($userId = null)
    {
        $query = ConferenceConflict::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total_conflicts' => $query->count(),
            'pending_conflicts' => $query->where('status', 'pending')->count(),
            'resolved_conflicts' => $query->where('status', 'resolved')->count(),
            'ignored_conflicts' => $query->where('status', 'ignored')->count(),
            'date_overlap_conflicts' => $query->where('conflict_type', 'date_overlap')->count(),
        ];
    }
}


