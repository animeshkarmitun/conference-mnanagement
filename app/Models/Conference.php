<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'location',
        'status',
    ];

    // Relationships
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function conferenceKits()
    {
        return $this->hasMany(ConferenceKit::class);
    }
}
