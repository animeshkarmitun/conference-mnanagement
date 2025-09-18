<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmailThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'subject',
        'participant_email',
        'conference_id',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    // Disable auto-incrementing for UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'thread_id', 'id');
    }

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_email', 'email');
    }

    // Scopes
    public function scopeByParticipant(Builder $query, string $participantEmail)
    {
        return $query->where('participant_email', $participantEmail);
    }

    public function scopeByConference(Builder $query, int $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    public function scopeRecent(Builder $query, int $days = 30)
    {
        return $query->where('last_activity_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getParticipantNameAttribute(): string
    {
        if ($this->participant) {
            return $this->participant->first_name . ' ' . $this->participant->last_name;
        }
        return $this->participant_email;
    }

    public function getEmailCountAttribute(): int
    {
        return $this->emails()->count();
    }

    public function getLastEmailAttribute(): ?Email
    {
        return $this->emails()->orderBy('created_at', 'desc')->first();
    }

    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    // Static helper methods
    public static function createThread(string $subject, string $participantEmail, ?int $conferenceId = null): self
    {
        return self::create([
            'id' => \Str::uuid(),
            'subject' => $subject,
            'participant_email' => $participantEmail,
            'conference_id' => $conferenceId,
        ]);
    }
}
