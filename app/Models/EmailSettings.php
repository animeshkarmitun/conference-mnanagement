<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmailSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_type',
        'subject_template',
        'greeting_template',
        'body_template',
        'closing_template',
        'system_signature',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Email type constants (matching Email model)
    const TYPE_TASK_NOTIFICATION = 'task_notification';
    const TYPE_TRAVEL_NOTIFICATION = 'travel_notification';
    const TYPE_PASSWORDLESS_LOGIN = 'passwordless_login';
    const TYPE_CONFERENCE_UPDATE = 'conference_update';
    const TYPE_SESSION_UPDATE = 'session_update';
    const TYPE_SESSION_NOTIFICATION = 'session_notification';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_GENERAL = 'general';

    /**
     * Get all available email types
     */
    public static function getEmailTypes(): array
    {
        return [
            self::TYPE_TASK_NOTIFICATION => 'Task Notification',
            self::TYPE_TRAVEL_NOTIFICATION => 'Travel Notification',
            self::TYPE_PASSWORDLESS_LOGIN => 'Passwordless Login',
            self::TYPE_CONFERENCE_UPDATE => 'Conference Update',
            self::TYPE_SESSION_UPDATE => 'Session Update',
            self::TYPE_SESSION_NOTIFICATION => 'Session Notification',
            self::TYPE_PROFILE_UPDATE => 'Profile Update',
            self::TYPE_GENERAL => 'General',
        ];
    }

    /**
     * Get email settings by type
     */
    public static function getByType(string $emailType): ?self
    {
        return static::where('email_type', $emailType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all active email settings
     */
    public static function getAllActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('email_type')
            ->get();
    }

    /**
     * Scope for active settings
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the display name for the email type
     */
    public function getTypeNameAttribute(): string
    {
        return self::getEmailTypes()[$this->email_type] ?? $this->email_type;
    }

    /**
     * Check if this is a valid email type
     */
    public function isValidEmailType(): bool
    {
        return array_key_exists($this->email_type, self::getEmailTypes());
    }
}