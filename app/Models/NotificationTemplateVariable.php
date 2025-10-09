<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplateVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'variable_name',
        'notification_type',
        'variable_description',
        'example_value',
        'is_system_variable',
    ];

    protected $casts = [
        'is_system_variable' => 'boolean',
    ];

    /**
     * Get variables by notification type
     */
    public static function getByNotificationType(string $notificationType): array
    {
        return static::where('notification_type', $notificationType)
            ->orderBy('is_system_variable', 'desc')
            ->orderBy('variable_name')
            ->get()
            ->toArray();
    }

    /**
     * Get all system variables
     */
    public static function getSystemVariables(): array
    {
        return static::where('is_system_variable', true)
            ->orderBy('variable_name')
            ->get()
            ->toArray();
    }

    /**
     * Get all custom variables
     */
    public static function getCustomVariables(): array
    {
        return static::where('is_system_variable', false)
            ->orderBy('variable_name')
            ->get()
            ->toArray();
    }

    /**
     * Get all variables
     */
    public static function getAllVariables(): array
    {
        return static::orderBy('notification_type')
            ->orderBy('is_system_variable', 'desc')
            ->orderBy('variable_name')
            ->get()
            ->toArray();
    }

    /**
     * Check if variable exists for notification type
     */
    public static function variableExists(string $variableName, string $notificationType): bool
    {
        return static::where('variable_name', $variableName)
            ->where('notification_type', $notificationType)
            ->exists();
    }

    /**
     * Get variable by name and notification type
     */
    public static function getByName(string $variableName, string $notificationType): ?self
    {
        return static::where('variable_name', $variableName)
            ->where('notification_type', $notificationType)
            ->first();
    }
}