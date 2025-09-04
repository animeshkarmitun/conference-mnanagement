<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'capacity',
    ];

    // Relationships
    public function conferences()
    {
        return $this->hasMany(Conference::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
