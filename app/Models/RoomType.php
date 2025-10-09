<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_beds',
        'base_price',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    // Relationships
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
