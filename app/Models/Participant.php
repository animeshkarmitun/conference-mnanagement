<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conference_id',
        'participant_type_id',
        'visa_status',
        'travel_form_submitted',
        'bio',
        'approved',
        'organization',
        'dietary_needs',
        'travel_intent',
        'registration_status',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function participantType()
    {
        return $this->belongsTo(ParticipantType::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'participant_role');
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'participant_session')->withPivot('role');
    }

    public function travelDetails()
    {
        return $this->hasOne(TravelDetail::class);
    }

    public function roomAllocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function participantRoles()
    {
        return $this->hasMany(ParticipantRole::class);
    }

    public function participantSessions()
    {
        return $this->hasMany(ParticipantSession::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }
}
