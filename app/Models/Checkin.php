<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'conference_id',
        'qr_code',
        'qr_code_expiry',
        'checkin_time',
        'status',
    ];

    // Relationships
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
