<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'room_type',
        'beds',
        'price_per_night',
        'description',
        'is_available',
    ];

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomAllocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }
}
