<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordlessLogin;
use App\Models\Conference;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class PasswordlessLoginService
{
    /**
     * Generate a login link for a user
     */
    public function generateLoginLink(User $user, int $expirationHours = 24): PasswordlessLogin
    {
        // Rate limiting check
        $key = 'passwordless-login:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw new \Exception('Too many login link requests. Please try again later.');
        }

        RateLimiter::hit($key, 3600); // 1 hour cooldown

        // Create the passwordless login token
        $passwordlessLogin = PasswordlessLogin::createForUser($user, $expirationHours);

        Log::info('Passwordless login link generated', [
            'user_id' => $user->id,
            'token_id' => $passwordlessLogin->id,
            'expires_at' => $passwordlessLogin->expires_at,
        ]);

        return $passwordlessLogin;
    }

    /**
     * Send login email to user
     */
    public function sendLoginEmail(User $user, PasswordlessLogin $passwordlessLogin, Conference $conference = null): bool
    {
        try {
            $loginUrl = $passwordlessLogin->getLoginUrl();
            
            Mail::send('emails.passwordless-login', [
                'user' => $user,
                'loginUrl' => $loginUrl,
                'conference' => $conference,
                'expiresAt' => $passwordlessLogin->expires_at,
                'token' => $passwordlessLogin,
            ], function ($message) use ($user, $conference) {
                $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                        ->subject('Your Conference Dashboard Access' . ($conference ? ' - ' . $conference->name : ''));
            });

            Log::info('Passwordless login email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'token_id' => $passwordlessLogin->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send passwordless login email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Validate and process login token
     */
    public function validateAndLogin(string $token, Request $request): array
    {
        $passwordlessLogin = PasswordlessLogin::where('token', $token)->first();

        if (!$passwordlessLogin) {
            return [
                'success' => false,
                'message' => 'Invalid login link.',
                'error_type' => 'invalid_token'
            ];
        }

        if (!$passwordlessLogin->isValid()) {
            if ($passwordlessLogin->used_at) {
                return [
                    'success' => false,
                    'message' => 'This login link has already been used.',
                    'error_type' => 'already_used'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'This login link has expired.',
                    'error_type' => 'expired'
                ];
            }
        }

        // Check if user is an attendee or speaker
        $user = $passwordlessLogin->user;
        if (!$user->roles()->whereIn('name', ['attendee', 'speaker'])->exists()) {
            return [
                'success' => false,
                'message' => 'Access denied. This link is only for attendees and speakers.',
                'error_type' => 'access_denied'
            ];
        }

        // Mark token as used
        $passwordlessLogin->markAsUsed(
            $request->ip(),
            $request->userAgent()
        );

        // Log the user in
        auth()->login($user);

        Log::info('Passwordless login successful', [
            'user_id' => $user->id,
            'token_id' => $passwordlessLogin->id,
            'ip_address' => $request->ip(),
        ]);

        return [
            'success' => true,
            'user' => $user,
            'message' => 'Login successful!'
        ];
    }

    /**
     * Generate and send login link for multiple users
     */
    public function generateBulkLoginLinks(array $userIds, int $expirationHours = 24, Conference $conference = null): array
    {
        $results = [];
        $users = User::whereIn('id', $userIds)
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('name', ['attendee', 'speaker']);
                    })
                    ->get();

        foreach ($users as $user) {
            try {
                $passwordlessLogin = $this->generateLoginLink($user, $expirationHours);
                $emailSent = $this->sendLoginEmail($user, $passwordlessLogin, $conference);
                
                $results[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'success' => $emailSent,
                    'login_url' => $passwordlessLogin->getLoginUrl(),
                    'expires_at' => $passwordlessLogin->expires_at,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get active login links for a user
     */
    public function getActiveLinksForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return PasswordlessLogin::where('user_id', $user->id)
                               ->valid()
                               ->orderBy('created_at', 'desc')
                               ->get();
    }

    /**
     * Revoke all active links for a user
     */
    public function revokeAllLinksForUser(User $user): int
    {
        return PasswordlessLogin::where('user_id', $user->id)
                               ->valid()
                               ->update(['used_at' => now()]);
    }

    /**
     * Clean up expired tokens
     */
    public function cleanupExpiredTokens(): int
    {
        $deleted = PasswordlessLogin::cleanupExpired();
        
        Log::info('Cleaned up expired passwordless login tokens', [
            'deleted_count' => $deleted
        ]);

        return $deleted;
    }

    /**
     * Get login statistics
     */
    public function getLoginStats(): array
    {
        return [
            'total_tokens' => PasswordlessLogin::count(),
            'active_tokens' => PasswordlessLogin::valid()->count(),
            'used_tokens' => PasswordlessLogin::used()->count(),
            'expired_tokens' => PasswordlessLogin::expired()->count(),
            'tokens_created_today' => PasswordlessLogin::whereDate('created_at', today())->count(),
            'tokens_used_today' => PasswordlessLogin::whereDate('used_at', today())->count(),
        ];
    }
}
