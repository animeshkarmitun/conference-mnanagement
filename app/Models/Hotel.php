<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'name',
        'address',
        'room_capacity',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function travelDetails()
    {
        return $this->hasMany(TravelDetail::class);
    }

    public function roomAllocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }
}
