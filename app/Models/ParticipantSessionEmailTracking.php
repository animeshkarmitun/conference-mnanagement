<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantSessionEmailTracking extends Model
{
    use HasFactory;

    protected $table = 'participant_session_email_tracking';

    protected $fillable = [
        'session_id',
        'participant_id',
        'email_send_count',
        'last_email_sent_at',
        'email_recipients',
    ];

    protected $casts = [
        'last_email_sent_at' => 'datetime',
        'email_recipients' => 'array',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    // Helper methods
    public function incrementEmailCount()
    {
        $this->increment('email_send_count');
        $this->update(['last_email_sent_at' => now()]);
    }

    public static function getOrCreateTracking($sessionId, $participantId)
    {
        return self::firstOrCreate(
            ['session_id' => $sessionId, 'participant_id' => $participantId],
            ['email_send_count' => 0]
        );
    }
}
