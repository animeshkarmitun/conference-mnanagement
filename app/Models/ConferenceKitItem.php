<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceKitItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kit_id',
        'type',
        'content',
    ];

    // Relationships
    public function conferenceKit()
    {
        return $this->belongsTo(ConferenceKit::class, 'kit_id');
    }
}
