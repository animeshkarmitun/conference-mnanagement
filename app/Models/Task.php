<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'title',
        'description',
        'theme',
        'status',
        'notes',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user')->withPivot('status', 'notes');
    }

    public function assignedTo()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
