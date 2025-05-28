<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'title',
        'body',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
