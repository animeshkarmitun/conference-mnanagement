<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'venue_id',
        'seating_arrangement',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_session')->withPivot('role');
    }

    public function participantSessions()
    {
        return $this->hasMany(ParticipantSession::class);
    }

    // Scope: Upcoming sessions
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    // Helper: Is this session ongoing?
    public function isOngoing()
    {
        $now = now();
        return $this->start_time <= $now && $this->end_time >= $now;
    }
}
