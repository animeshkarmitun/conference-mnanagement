<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'room_type',
        'beds',
        'description',
        'is_available',
        'floor_number',
        'max_occupancy',
        'amenities',
        'view_type',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_available' => 'boolean',
    ];

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getRoomAllocationsCount()
    {
        return RoomAllocation::where('hotel_id', $this->hotel_id)
                            ->where('room_number', $this->room_number)
                            ->count();
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
