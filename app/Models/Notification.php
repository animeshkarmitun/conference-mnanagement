<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conference_id',
        'message',
        'type',
        'related_model',
        'related_id',
        'action_url',
        'sent_at',
        'read_status',
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

    // Scope: Unread notifications
    public function scopeUnread($query)
    {
        return $query->where('read_status', false);
    }
}
