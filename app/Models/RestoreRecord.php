<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RestoreRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_id',
        'restore_type',
        'tables_restored',
        'status',
        'error_message',
        'metadata',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'tables_restored' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function backup(): BelongsTo
    {
        return $this->belongsTo(BackupRecord::class, 'backup_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('restore_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Accessors
    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        
        return $this->started_at->diffForHumans($this->completed_at, true);
    }

    public function getTablesRestoredListAttribute(): string
    {
        if (!$this->tables_restored || empty($this->tables_restored)) {
            return 'All tables';
        }
        
        return implode(', ', $this->tables_restored);
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

    public function isSelective(): bool
    {
        return $this->restore_type === 'selective';
    }

    public function isFull(): bool
    {
        return $this->restore_type === 'full';
    }
}
