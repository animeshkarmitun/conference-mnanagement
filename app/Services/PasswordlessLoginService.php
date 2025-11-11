<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordlessLogin;
use App\Models\Conference;
use App\Models\EmailSettings;
use App\Services\EmailTemplateService;
use App\Services\EmailTrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PasswordlessLoginService
{
    protected EmailTrackingService $emailTrackingService;
    protected EmailTemplateService $emailTemplateService;

    public function __construct(EmailTrackingService $emailTrackingService, EmailTemplateService $emailTemplateService)
    {
        $this->emailTrackingService = $emailTrackingService;
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Generate a login link for a user
     */
    public function generateLoginLink(User $user, int $expirationHours = 24, Conference $conference = null, Carbon $explicitExpiresAt = null): PasswordlessLogin
    {
        // Rate limiting check
        $key = 'passwordless-login:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw new \Exception('Too many login link requests. Please try again later.');
        }

        RateLimiter::hit($key, 3600); // 1 hour cooldown

        // Create the passwordless login token
        if ($explicitExpiresAt instanceof Carbon) {
            $passwordlessLogin = PasswordlessLogin::createForUserWithExpiry($user, $explicitExpiresAt);
        } else {
            $passwordlessLogin = PasswordlessLogin::createForUser($user, $expirationHours);
        }

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
            $ccRecipients = config('mail.passwordless_login_cc', []);

            $templateVariables = $this->buildTemplateVariables($user, $passwordlessLogin, $conference, $loginUrl);
            $template = $this->emailTemplateService->processTemplate(
                EmailSettings::TYPE_PASSWORDLESS_LOGIN,
                $templateVariables
            );

            $subject = $template['subject'] ?? ('Your Conference Dashboard Access' . ($conference ? ' - ' . $conference->name : ''));
            $useCustomLayout = $this->shouldUseCustomLayout($template);

            // Create email content using dynamic template
            $emailContent = view('emails.passwordless-login', [
                'template' => $template,
                'templateVariables' => $templateVariables,
                'user' => $user,
                'loginUrl' => $loginUrl,
                'conference' => $conference,
                'expiresAt' => $passwordlessLogin->expires_at,
                'token' => $passwordlessLogin,
                'useCustomLayout' => $useCustomLayout,
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
                ],
                null,
                $ccRecipients
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
     * Build template variables for passwordless login email
     */
    protected function buildTemplateVariables(User $user, PasswordlessLogin $passwordlessLogin, ?Conference $conference, string $loginUrl): array
    {
        $expiresAt = $passwordlessLogin->expires_at ? $passwordlessLogin->expires_at->timezone(config('app.timezone', 'UTC')) : null;

        $escapedLoginUrl = e($loginUrl);

        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => trim($user->first_name . ' ' . $user->last_name),
            'user_email' => $user->email,
            'login_url' => $loginUrl,
            'login_button' => "<a href=\"{$escapedLoginUrl}\" style=\"display:inline-block;background:linear-gradient(90deg,#5b21b6,#2563eb);color:#fff;padding:12px 24px;border-radius:9999px;text-decoration:none;font-weight:600;\">Access My Dashboard</a>",
            'expires_at' => $expiresAt ? $expiresAt->format('M d, Y g:i A T') : '',
            'expires_at_date' => $expiresAt ? $expiresAt->format('M d, Y') : '',
            'expires_at_time' => $expiresAt ? $expiresAt->format('g:i A T') : '',
            'expires_in_minutes' => $expiresAt ? max(1, now($expiresAt->getTimezone())->diffInMinutes($expiresAt)) : '',
            'conference_name' => $conference->name ?? '',
            'conference_date' => $conference && $conference->start_date ? \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') : '',
            'conference_venue' => $conference && $conference->venue ? $conference->venue->name : '',
            'email_heading' => '🎉 Welcome to Your Conference Dashboard',
            'email_cta_label' => 'Access My Dashboard',
            'email_cta_emoji' => '🚀',
            'signature' => config('mail.from.name') ?? config('app.name', 'Conference Team'),
        ];
    }

    /**
     * Determine if template body contains full custom layout
     */
    protected function shouldUseCustomLayout(array $template): bool
    {
        $body = $template['body'] ?? '';

        return Str::contains(Str::lower($body), ['<!doctype', '<html', '<body']);
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
     *
     * @param array $userIds
     * @param int $expirationHours
     * @param Conference|null $conference
     * @param Carbon|null $explicitExpiresAt Optional explicit expiry timestamp. When provided, overrides $expirationHours.
     * @param array $sessionIds Optional session IDs used for tracking email sends per session.
     */
    public function generateBulkLoginLinks(array $userIds, int $expirationHours = 24, Conference $conference = null, Carbon $explicitExpiresAt = null, array $sessionIds = []): array
    {
        $results = [];
        $users = User::whereIn('id', $userIds)
                    ->whereHas('participants') // Only users who are participants
                    ->get();

        foreach ($users as $user) {
            try {
                $passwordlessLogin = $this->generateLoginLink($user, $expirationHours, $conference, $explicitExpiresAt);
                $emailSent = $this->sendLoginEmail($user, $passwordlessLogin, $conference);
                
                // Track email sends for sessions if provided
                if (!empty($sessionIds) && $emailSent) {
                    $this->trackEmailSendsForSessions($user, $sessionIds);
                }
                
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
     * Track email sends for sessions
     */
    private function trackEmailSendsForSessions(User $user, array $sessionIds): void
    {
        // Get participant records for this user
        $participants = $user->participants;
        
        foreach ($participants as $participant) {
            foreach ($sessionIds as $sessionId) {
                // Check if this participant is assigned to this session
                $isAssignedToSession = $participant->participantSessions()
                    ->where('session_id', $sessionId)
                    ->exists();
                
                if ($isAssignedToSession) {
                    // Create or update email tracking record
                    $tracking = \App\Models\ParticipantSessionEmailTracking::getOrCreateTracking($sessionId, $participant->id);
                    $tracking->incrementEmailCount();
                    $tracking->update([
                        'email_recipients' => [$user->email]
                    ]);
                }
            }
        }
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
