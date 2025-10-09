<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantProfileSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_id',
        'session_id',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('last_activity', '>', now()->subHours(24));
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->last_activity->isAfter(now()->subHours(24));
    }

    public function updateActivity()
    {
        $this->update(['last_activity' => now()]);
    }
}
