<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'status',
        'email_type',
        'related_model_type',
        'related_model_id',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'error_message',
        'message_id',
        'template_name',
        'conference_id',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Email status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_OPENED = 'opened';
    const STATUS_CLICKED = 'clicked';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_FAILED = 'failed';

    // Email type constants
    const TYPE_TASK_NOTIFICATION = 'task_notification';
    const TYPE_TRAVEL_NOTIFICATION = 'travel_notification';
    const TYPE_PASSWORDLESS_LOGIN = 'passwordless_login';
    const TYPE_CONFERENCE_UPDATE = 'conference_update';
    const TYPE_SESSION_UPDATE = 'session_update';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_GENERAL = 'general';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function relatedModel()
    {
        return $this->morphTo('related_model');
    }

    // Get related model safely (handles invalid types)
    public function getRelatedModelAttribute()
    {
        if (!$this->related_model_type || !$this->related_model_id) {
            return null;
        }

        try {
            return $this->relatedModel;
        } catch (\Exception $e) {
            // Return null if the related model class doesn't exist
            return null;
        }
    }

    // Scopes
    public function scopeSent(Builder $query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeDelivered(Builder $query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeOpened(Builder $query)
    {
        return $query->where('status', self::STATUS_OPENED);
    }

    public function scopeBounced(Builder $query)
    {
        return $query->where('status', self::STATUS_BOUNCED);
    }

    public function scopeFailed(Builder $query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByType(Builder $query, string $type)
    {
        return $query->where('email_type', $type);
    }

    public function scopeByConference(Builder $query, int $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    public function scopeRecent(Builder $query, int $days = 30)
    {
        return $query->where('sent_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isOpened(): bool
    {
        return $this->status === self::STATUS_OPENED;
    }

    public function isBounced(): bool
    {
        return $this->status === self::STATUS_BOUNCED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function markAsOpened(): void
    {
        $this->update([
            'status' => self::STATUS_OPENED,
            'opened_at' => now(),
        ]);
    }

    public function markAsBounced(string $errorMessage = null): void
    {
        $this->update([
            'status' => self::STATUS_BOUNCED,
            'bounced_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    // Static helper methods
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_OPENED => 'Opened',
            self::STATUS_CLICKED => 'Clicked',
            self::STATUS_BOUNCED => 'Bounced',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_TASK_NOTIFICATION => 'Task Notification',
            self::TYPE_TRAVEL_NOTIFICATION => 'Travel Notification',
            self::TYPE_PASSWORDLESS_LOGIN => 'Passwordless Login',
            self::TYPE_CONFERENCE_UPDATE => 'Conference Update',
            self::TYPE_SESSION_UPDATE => 'Session Update',
            self::TYPE_PROFILE_UPDATE => 'Profile Update',
            self::TYPE_GENERAL => 'General',
        ];
    }
}
