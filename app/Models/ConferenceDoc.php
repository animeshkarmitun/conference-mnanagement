<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceDoc extends Model
{
    use HasFactory;

    protected $table = 'conference_kits';

    protected $fillable = [
        'conference_id',
        'participant_id',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function conferenceDocItems()
    {
        return $this->hasMany(ConferenceDocItem::class, 'kit_id');
    }

    // Legacy relationship for backward compatibility
    public function conferenceKitItems()
    {
        return $this->conferenceDocItems();
    }

    // Get media files specifically
    public function mediaFiles()
    {
        return $this->conferenceDocItems()->where('type', 'MediaFile');
    }

    // Get total file size of all media files
    public function getTotalFileSize()
    {
        return $this->mediaFiles()->sum('file_size') ?? 0;
    }

    // Get count of media files
    public function getMediaFileCount()
    {
        return $this->mediaFiles()->count();
    }
}
