<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function participant()
    {
        return $this->belongsTo(\App\Models\Participant::class);
    }
}
