<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_role');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
