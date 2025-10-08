<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'variable_name',
        'variable_description',
        'example_value',
        'is_system_variable',
    ];

    protected $casts = [
        'is_system_variable' => 'boolean',
    ];

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
        return static::orderBy('is_system_variable', 'desc')
            ->orderBy('variable_name')
            ->get()
            ->toArray();
    }

    /**
     * Check if variable exists
     */
    public static function variableExists(string $variableName): bool
    {
        return static::where('variable_name', $variableName)->exists();
    }

    /**
     * Get variable by name
     */
    public static function getByName(string $variableName): ?self
    {
        return static::where('variable_name', $variableName)->first();
    }
}