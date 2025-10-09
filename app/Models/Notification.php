<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_id',
        'conference_id',
        'message',
        'type',
        'related_model',
        'related_id',
        'action_url',
        'sent_at',
        'read_status',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_status' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo('related', 'related_model', 'related_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('read_status', false);
    }

    public function scopeRead($query)
    {
        return $query->where('read_status', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForConference($query, int $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update(['read_status' => true]);
    }

    public function markAsUnread(): void
    {
        $this->update(['read_status' => false]);
    }

    public function isRead(): bool
    {
        return $this->read_status;
    }

    public function isUnread(): bool
    {
        return !$this->read_status;
    }
}
