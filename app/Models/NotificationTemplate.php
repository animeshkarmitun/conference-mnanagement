<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type',
        'message_template',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Notification type constants (matching Notification model types)
    const TYPE_TASK_UPDATE = 'TaskUpdate';
    const TYPE_SESSION_UPDATE = 'SessionUpdate';
    const TYPE_TRAVEL_UPDATE = 'TravelUpdate';
    const TYPE_CONFERENCE_UPDATE = 'ConferenceUpdate';
    const TYPE_PROFILE_UPDATE = 'ProfileUpdate';
    const TYPE_MISSING_DOCUMENTS = 'MissingDocuments';
    const TYPE_GENERAL = 'General';

    /**
     * Get all available notification types
     */
    public static function getNotificationTypes(): array
    {
        return [
            self::TYPE_TASK_UPDATE => 'Task Update',
            self::TYPE_SESSION_UPDATE => 'Session Update',
            self::TYPE_TRAVEL_UPDATE => 'Travel Update',
            self::TYPE_CONFERENCE_UPDATE => 'Conference Update',
            self::TYPE_PROFILE_UPDATE => 'Profile Update',
            self::TYPE_MISSING_DOCUMENTS => 'Missing Documents',
            self::TYPE_GENERAL => 'General',
        ];
    }

    /**
     * Get notification template by type
     */
    public static function getByType(string $notificationType): ?self
    {
        return static::where('notification_type', $notificationType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all active notification templates
     */
    public static function getAllActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('notification_type')
            ->get();
    }

    /**
     * Scope for active templates
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the display name for the notification type
     */
    public function getTypeNameAttribute(): string
    {
        return self::getNotificationTypes()[$this->notification_type] ?? $this->notification_type;
    }

    /**
     * Check if this is a valid notification type
     */
    public function isValidNotificationType(): bool
    {
        return array_key_exists($this->notification_type, self::getNotificationTypes());
    }

    /**
     * Get available variables for this notification type
     */
    public function getAvailableVariables(): array
    {
        return NotificationTemplateVariable::getByNotificationType($this->notification_type);
    }
}