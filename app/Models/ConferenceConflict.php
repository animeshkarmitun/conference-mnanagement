<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceConflict extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_id',
        'conference_id',
        'conflicting_conference_id',
        'conflict_type',
        'conflict_details',
        'status',
        'resolution_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
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

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function conflictingConference()
    {
        return $this->belongsTo(Conference::class, 'conflicting_conference_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('conflict_type', $type);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function resolve($notes = null, $resolvedBy = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolution_notes' => $notes,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
        ]);
    }

    public function ignore($notes = null, $resolvedBy = null)
    {
        $this->update([
            'status' => 'ignored',
            'resolution_notes' => $notes,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
        ]);
    }
}
