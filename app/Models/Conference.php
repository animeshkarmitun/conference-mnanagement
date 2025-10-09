<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'location',
        'status',
        'venue_id',
    ];

    // Relationships
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function conferenceDocs()
    {
        return $this->hasMany(ConferenceDoc::class);
    }

    // Legacy relationship for backward compatibility
    public function conferenceKits()
    {
        return $this->conferenceDocs();
    }

    public function venue()
    {
        return $this->belongsTo(\App\Models\Venue::class);
    }

    public function conferenceConflicts()
    {
        return $this->hasMany(ConferenceConflict::class);
    }

    public function conflictingConferences()
    {
        return $this->hasMany(ConferenceConflict::class, 'conflicting_conference_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'ongoing']);
    }

    // Conflict detection methods
    public function hasDateOverlap($otherConference)
    {
        if (!$otherConference) {
            return false;
        }

        $thisStart = \Carbon\Carbon::parse($this->start_date);
        $thisEnd = \Carbon\Carbon::parse($this->end_date);
        $otherStart = \Carbon\Carbon::parse($otherConference->start_date);
        $otherEnd = \Carbon\Carbon::parse($otherConference->end_date);

        return $thisStart->lte($otherEnd) && $thisEnd->gte($otherStart);
    }

    public function getOverlappingConferences()
    {
        return Conference::where('id', '!=', $this->id)
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($q) {
                        $q->where('start_date', '<=', $this->start_date)
                          ->where('end_date', '>=', $this->end_date);
                    });
            })
            ->get();
    }

    public function checkUserConflicts($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }

        $conflicts = collect();
        $overlappingConferences = $this->getOverlappingConferences();

        foreach ($overlappingConferences as $overlappingConference) {
            $userParticipants = $user->participants()
                ->where('conference_id', $overlappingConference->id)
                ->where('status', 'active')
                ->get();

            foreach ($userParticipants as $participant) {
                $conflicts->push([
                    'type' => 'date_overlap',
                    'conference' => $overlappingConference,
                    'participant' => $participant,
                    'details' => "Conference dates overlap: {$this->name} ({$this->start_date} - {$this->end_date}) conflicts with {$overlappingConference->name} ({$overlappingConference->start_date} - {$overlappingConference->end_date})"
                ]);
            }
        }

        return $conflicts;
    }

    public function isDateOverlapping($startDate, $endDate, $excludeConferenceId = null)
    {
        $query = Conference::where('id', '!=', $this->id);
        
        if ($excludeConferenceId) {
            $query->where('id', '!=', $excludeConferenceId);
        }

        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                       ->where('end_date', '>=', $endDate);
              });
        })->exists();
    }
}
