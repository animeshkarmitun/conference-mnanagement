<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordlessLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Generate a secure random token
     */
    public static function generateToken(): string
    {
        return hash('sha256', Str::random(64));
    }

    /**
     * Create a new passwordless login token
     */
    public static function createForUser(User $user, int $expirationHours = 24): self
    {
        // Clean up any existing tokens for this user
        self::where('user_id', $user->id)
            ->where('expires_at', '<', now())
            ->delete();

        return self::create([
            'user_id' => $user->id,
            'token' => self::generateToken(),
            'expires_at' => now()->addHours($expirationHours),
        ]);
    }

    /**
     * Check if token is valid and not expired
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture() && is_null($this->used_at);
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(string $ipAddress = null, string $userAgent = null): void
    {
        $this->update([
            'used_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Get the login URL for this token
     */
    public function getLoginUrl(): string
    {
        return route('passwordless-login.verify', ['token' => $this->token]);
    }

    /**
     * Clean up expired tokens
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for valid tokens
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->whereNull('used_at');
    }

    /**
     * Scope for expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope for used tokens
     */
    public function scopeUsed($query)
    {
        return $query->whereNotNull('used_at');
    }
}
