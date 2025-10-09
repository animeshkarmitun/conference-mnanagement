<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'room_capacity',
        'contact_email',
        'contact_phone',
        'website',
        'amenities',
        'check_in_time',
        'check_out_time',
        'is_active',
        'description',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    // Relationships

    public function travelDetails()
    {
        return $this->hasMany(TravelDetail::class);
    }

    public function roomAllocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
