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
        
        // Ensure tables_restored is an array
        if (!is_array($this->tables_restored)) {
            // Try to decode if it's a JSON string
            if (is_string($this->tables_restored)) {
                $decoded = json_decode($this->tables_restored, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->tables_restored = $decoded;
                } else {
                    return 'All tables';
                }
            } else {
                return 'All tables';
            }
        }
        
        // Filter out any null or empty values
        $tables = array_filter($this->tables_restored, function($table) {
            return !empty($table) && is_string($table);
        });
        
        if (empty($tables)) {
            return 'All tables';
        }
        
        return implode(', ', $tables);
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
