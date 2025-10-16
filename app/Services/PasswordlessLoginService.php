<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordlessLogin;
use App\Models\Conference;
use App\Services\EmailTrackingService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class PasswordlessLoginService
{
    protected EmailTrackingService $emailTrackingService;

    public function __construct(EmailTrackingService $emailTrackingService)
    {
        $this->emailTrackingService = $emailTrackingService;
    }

    /**
     * Generate a login link for a user
     */
    public function generateLoginLink(User $user, int $expirationHours = 24, Conference $conference = null): PasswordlessLogin
    {
        // Rate limiting check
        $key = 'passwordless-login:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw new \Exception('Too many login link requests. Please try again later.');
        }

        RateLimiter::hit($key, 3600); // 1 hour cooldown

        // Create the passwordless login token
        $passwordlessLogin = PasswordlessLogin::createForUser($user, $expirationHours);

        // Store conference information if provided
        if ($conference) {
            $passwordlessLogin->update([
                'data' => [
                    'conference_id' => $conference->id,
                    'conference_name' => $conference->name,
                ]
            ]);
        }

        Log::info('Passwordless login link generated', [
            'user_id' => $user->id,
            'token_id' => $passwordlessLogin->id,
            'expires_at' => $passwordlessLogin->expires_at,
            'conference_id' => $conference ? $conference->id : null,
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
            $subject = 'Your Conference Dashboard Access' . ($conference ? ' - ' . $conference->name : '');
            
            // Create email content
            $emailContent = view('emails.passwordless-login', [
                'user' => $user,
                'loginUrl' => $loginUrl,
                'conference' => $conference,
                'expiresAt' => $passwordlessLogin->expires_at,
                'token' => $passwordlessLogin,
            ])->render();
            
            // Send tracked email
            $this->emailTrackingService->sendTrackedEmail(
                $user->email,
                $subject,
                $emailContent,
                \App\Models\Email::TYPE_PASSWORDLESS_LOGIN,
                null, // System sender
                $conference,
                PasswordlessLogin::class,
                $passwordlessLogin->id,
                'passwordless-login',
                [
                    'user_id' => $user->id,
                    'token_id' => $passwordlessLogin->id,
                    'expires_at' => $passwordlessLogin->expires_at,
                ]
            );

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
            return [
                'success' => false,
                'message' => 'This login link has expired.',
                'error_type' => 'expired'
            ];
        }

        // Check if user is a participant (has participant records)
        $user = $passwordlessLogin->user;
        if (!$user->participants()->exists()) {
            return [
                'success' => false,
                'message' => 'Access denied. This link is only for participants.',
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

        // Get conference from the passwordless login data or request
        $conference = null;
        if (isset($passwordlessLogin->data['conference_id'])) {
            $conference = Conference::find($passwordlessLogin->data['conference_id']);
        }

        // Set the default active participant profile for the user
        $this->setDefaultActiveProfile($user, $conference);

        Log::info('Passwordless login successful', [
            'user_id' => $user->id,
            'token_id' => $passwordlessLogin->id,
            'ip_address' => $request->ip(),
            'conference_id' => $conference ? $conference->id : null,
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
                        $query->whereIn('name', ['organizer', 'speaker', 'attendee', 'tasker']);
                    })
                    ->get();

        foreach ($users as $user) {
            try {
                $passwordlessLogin = $this->generateLoginLink($user, $expirationHours, $conference);
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
     * Set the default active participant profile for a user
     */
    private function setDefaultActiveProfile(User $user, Conference $conference = null): void
    {
        $defaultParticipant = null;

        // If conference is specified, try to find participant for that conference
        if ($conference) {
            $defaultParticipant = $user->participants()
                ->where('status', 'active')
                ->where('conference_id', $conference->id)
                ->first();
        }

        // If no conference-specific participant found, get the primary participant or first active participant
        if (!$defaultParticipant) {
            $defaultParticipant = $user->participants()
                ->where('status', 'active')
                ->where('is_primary', true)
                ->first() ?? $user->participants()
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if ($defaultParticipant) {
            // Set the active profile in session
            $sessionId = session()->getId();
            
            // Remove any existing profile session for this user
            \App\Models\ParticipantProfileSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->delete();
            
            // Create new profile session
            \App\Models\ParticipantProfileSession::create([
                'user_id' => $user->id,
                'participant_id' => $defaultParticipant->id,
                'session_id' => $sessionId,
            ]);
            
            Log::info('Set default active profile for user', [
                'user_id' => $user->id,
                'participant_id' => $defaultParticipant->id,
                'conference_id' => $defaultParticipant->conference_id,
                'conference_name' => $defaultParticipant->conference->name ?? 'No Conference',
                'requested_conference_id' => $conference ? $conference->id : null,
                'requested_conference_name' => $conference ? $conference->name : null,
            ]);
        }
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
     * Get login statistics (only for participants: organizer, speaker, attendee, tasker)
     */
    public function getLoginStats(): array
    {
        $participantTokens = PasswordlessLogin::whereHas('user.participants');

        return [
            'total_tokens' => $participantTokens->count(),
            'active_tokens' => (clone $participantTokens)->active()->count(),
            'used_tokens' => (clone $participantTokens)->used()->count(),
            'expired_tokens' => (clone $participantTokens)->expired()->count(),
            'tokens_created_today' => (clone $participantTokens)->whereDate('created_at', today())->count(),
            'tokens_used_today' => (clone $participantTokens)->whereDate('used_at', today())->count(),
            'total_uses' => (clone $participantTokens)->sum('use_count'),
        ];
    }
}
