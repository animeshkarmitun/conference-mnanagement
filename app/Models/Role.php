<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // Ensure permissions are always an array
    public function getPermissionsAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

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
