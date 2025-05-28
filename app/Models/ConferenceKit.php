<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceKit extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function conferenceKitItems()
    {
        return $this->hasMany(ConferenceKitItem::class, 'kit_id');
    }
}
