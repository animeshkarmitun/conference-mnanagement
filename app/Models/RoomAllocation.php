<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'participant_id',
        'room_number',
        'check_in',
        'check_out',
    ];

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
