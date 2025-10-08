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
        'room',
        'capacity',
        'seating_arrangement',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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

    // Helper: Is this session published?
    public function isPublished()
    {
        return $this->status === 'published';
    }

    // Helper: Is this session a draft?
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    // Scope: Published sessions
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Scope: Draft sessions
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
