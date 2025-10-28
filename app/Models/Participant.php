<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conference_id',
        'participant_type_id',
        'visa_status',
        'visa_issue_description',
        'bio',
        'organization',
        'travel_intent',
        'registration_status',
        'approved',
        'category',
        'profile_name',
        'profile_type',
        'status',
        'profile_description',
        'is_primary',
        'hashtags',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function participantType()
    {
        return $this->belongsTo(ParticipantType::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'participant_role');
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'participant_session')->withPivot('role');
    }

    public function travelDetails()
    {
        return $this->hasOne(TravelDetail::class);
    }

    public function roomAllocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }

    public function roomAllocation()
    {
        return $this->hasOne(RoomAllocation::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function participantRoles()
    {
        return $this->hasMany(ParticipantRole::class);
    }

    public function participantSessions()
    {
        return $this->belongsToMany(Session::class, 'participant_session')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    public function conferenceConflicts()
    {
        return $this->hasMany(ConferenceConflict::class);
    }

    public function participantProfileSessions()
    {
        return $this->hasMany(ParticipantProfileSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByProfileType($query, $profileType)
    {
        return $query->where('profile_type', $profileType);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPrimary()
    {
        return $this->is_primary;
    }

    public function getProfileDisplayName()
    {
        if ($this->profile_name) {
            return $this->profile_name;
        }

        return $this->user->first_name . ' ' . $this->user->last_name . ' (' . ucfirst($this->profile_type) . ')';
    }

    public function checkConferenceConflicts()
    {
        $user = $this->user;
        $conference = $this->conference;

        if (!$conference) {
            return collect();
        }

        // Check for date overlaps with other conferences
        $conflictingConferences = Conference::where('id', '!=', $conference->id)
            ->where(function ($query) use ($conference) {
                $query->whereBetween('start_date', [$conference->start_date, $conference->end_date])
                    ->orWhereBetween('end_date', [$conference->start_date, $conference->end_date])
                    ->orWhere(function ($q) use ($conference) {
                        $q->where('start_date', '<=', $conference->start_date)
                          ->where('end_date', '>=', $conference->end_date);
                    });
            })
            ->get();

        $conflicts = collect();

        foreach ($conflictingConferences as $conflictingConference) {
            // Check if user has participants in conflicting conference
            $conflictingParticipants = $user->participants()
                ->where('conference_id', $conflictingConference->id)
                ->where('status', 'active')
                ->get();

            foreach ($conflictingParticipants as $conflictingParticipant) {
                $conflicts->push([
                    'type' => 'date_overlap',
                    'conference' => $conflictingConference,
                    'participant' => $conflictingParticipant,
                    'details' => "Conference dates overlap: {$conference->name} ({$conference->start_date} - {$conference->end_date}) conflicts with {$conflictingConference->name} ({$conflictingConference->start_date} - {$conflictingConference->end_date})"
                ]);
            }
        }

        return $conflicts;
    }

    public function getConflictsWithOtherParticipants()
    {
        $user = $this->user;
        $conference = $this->conference;

        if (!$conference) {
            return collect();
        }

        // Get all other participants of the same user in different conferences
        $otherParticipants = $user->participants()
            ->where('id', '!=', $this->id)
            ->where('status', 'active')
            ->with('conference')
            ->get();

        $conflicts = collect();

        foreach ($otherParticipants as $otherParticipant) {
            $otherConference = $otherParticipant->conference;
            
            if ($this->conference->hasDateOverlap($otherConference)) {
                $conflicts->push([
                    'type' => 'date_overlap',
                    'participant' => $otherParticipant,
                    'conference' => $otherConference,
                    'details' => "This participant profile conflicts with another profile in overlapping conference dates"
                ]);
            }
        }

        return $conflicts;
    }
}
