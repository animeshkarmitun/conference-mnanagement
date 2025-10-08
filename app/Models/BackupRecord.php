<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class BackupRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_type',
        'file_path',
        'file_name',
        'file_size',
        'checksum',
        'status',
        'error_message',
        'metadata',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function restoreRecords(): HasMany
    {
        return $this->hasMany(RestoreRecord::class, 'backup_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('backup_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Accessors
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        
        return $this->started_at->diffForHumans($this->completed_at, true);
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getFullFilePath(): string
    {
        return storage_path('app/' . $this->file_path);
    }

    public function exists(): bool
    {
        return file_exists($this->getFullFilePath());
    }
}
