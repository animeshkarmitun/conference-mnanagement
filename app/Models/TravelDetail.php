<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'flight_info',
        'arrival_date',
        'departure_date',
        'hotel_info',
        'extra_nights',
        'hotel_id',
        'room_id',
        'travel_documents',
        'room_check_in',
        'room_check_out',
        'itineraries_status',
        'takeoff_airport',
        'flight_info_details',
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
        'departure_date' => 'datetime',
        'room_check_in' => 'datetime',
        'room_check_out' => 'datetime',
    ];

    // Relationships
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
