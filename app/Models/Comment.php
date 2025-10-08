<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_id',
        'conference_id',
        'content',
    ];

    public function participant()
    {
        return $this->belongsTo(\App\Models\Participant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
