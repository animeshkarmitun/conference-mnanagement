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
        'is_active',
        'description',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
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
