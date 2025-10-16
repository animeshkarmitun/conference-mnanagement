<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conference;
use App\Models\PasswordlessLogin;
use App\Services\PasswordlessLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PasswordlessLoginController extends Controller
{
    protected $passwordlessLoginService;

    public function __construct(PasswordlessLoginService $passwordlessLoginService)
    {
        $this->passwordlessLoginService = $passwordlessLoginService;
    }

    /**
     * Show the login verification page
     */
    public function showVerification(Request $request, string $token)
    {
        $passwordlessLogin = PasswordlessLogin::where('token', $token)->first();

        if (!$passwordlessLogin) {
            return view('auth.passwordless-login.invalid', [
                'message' => 'Invalid login link.',
                'error_type' => 'invalid_token'
            ]);
        }

        if (!$passwordlessLogin->isValid()) {
            return view('auth.passwordless-login.invalid', [
                'message' => 'This login link has expired.',
                'error_type' => 'expired'
            ]);
        }

        return view('auth.passwordless-login.verify', [
            'token' => $token,
            'user' => $passwordlessLogin->user,
            'expires_at' => $passwordlessLogin->expires_at
        ]);
    }

    /**
     * Process the login verification
     */
    public function verify(Request $request, string $token)
    {
        $result = $this->passwordlessLoginService->validateAndLogin($token, $request);

        if (!$result['success']) {
            return view('auth.passwordless-login.invalid', [
                'message' => $result['message'],
                'error_type' => $result['error_type']
            ]);
        }

        // Redirect to my-profile
        return redirect()->route('my-profile')
                        ->with('success', 'Welcome! You have successfully logged in.');
    }

    /**
     * Show admin interface for generating login links
     */
    public function adminIndex()
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $stats = $this->passwordlessLoginService->getLoginStats();
        $recentLogins = PasswordlessLogin::with('user')
                                       ->whereHas('user.participants')
                                       ->orderBy('created_at', 'desc')
                                       ->limit(10)
                                       ->get();

        return view('admin.passwordless-login.index', compact('stats', 'recentLogins'));
    }

    /**
     * Generate login link for a specific user
     */
    public function generateLink(Request $request)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'conference_id' => 'nullable|exists:conferences,id',
            'expiration_days' => 'integer|min:1|max:90', // Max 90 days
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($request->user_id);
            
            // Check if user is a participant (has participant records)
            if (!$user->participants()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a participant.'
                ], 400);
            }

            $expirationDays = $request->expiration_days ?? 1;
            $expirationHours = $expirationDays * 24; // Convert days to hours
            
            // Use provided conference_id or get latest conference
            $conference = $request->conference_id ? Conference::find($request->conference_id) : Conference::latest()->first();
            $passwordlessLogin = $this->passwordlessLoginService->generateLoginLink($user, $expirationHours, $conference);
            
            $emailSent = $this->passwordlessLoginService->sendLoginEmail($user, $passwordlessLogin, $conference);

            return response()->json([
                'success' => true,
                'message' => 'Login link generated successfully.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                    ],
                    'login_url' => $passwordlessLogin->getLoginUrl(),
                    'expires_at' => $passwordlessLogin->expires_at,
                    'email_sent' => $emailSent,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate bulk login links
     */
    public function generateBulkLinks(Request $request)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'conference_id' => 'nullable|exists:conferences,id',
            'expiration_days' => 'integer|min:1|max:90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $expirationDays = $request->expiration_days ?? 1;
            $expirationHours = $expirationDays * 24; // Convert days to hours
            
            // Use provided conference_id or get latest conference
            $conference = $request->conference_id ? Conference::find($request->conference_id) : Conference::latest()->first();
            
            $results = $this->passwordlessLoginService->generateBulkLoginLinks(
                $request->user_ids, 
                $expirationHours, 
                $conference
            );

            $successCount = collect($results)->where('success', true)->count();
            $failureCount = collect($results)->where('success', false)->count();

            return response()->json([
                'success' => true,
                'message' => "Generated {$successCount} login links successfully. {$failureCount} failed.",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active links for a user
     */
    public function getUserLinks(Request $request, User $user)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $activeLinks = $this->passwordlessLoginService->getActiveLinksForUser($user);

        return response()->json([
            'success' => true,
            'data' => $activeLinks->map(function ($link) {
                return [
                    'id' => $link->id,
                    'login_url' => $link->getLoginUrl(),
                    'expires_at' => $link->expires_at,
                    'created_at' => $link->created_at,
                ];
            })
        ]);
    }

    /**
     * Revoke all links for a user
     */
    public function revokeUserLinks(Request $request, User $user)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        try {
            $revokedCount = $this->passwordlessLoginService->revokeAllLinksForUser($user);

            return response()->json([
                'success' => true,
                'message' => "Revoked {$revokedCount} active login links for {$user->first_name} {$user->last_name}.",
                'revoked_count' => $revokedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up expired tokens
     */
    public function cleanupExpired(Request $request)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        try {
            $deletedCount = $this->passwordlessLoginService->cleanupExpiredTokens();

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} expired tokens.",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get participants for bulk link generation
     */
    public function getParticipants(Request $request)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Get all users who are participants (have participant records)
        $query = User::whereHas('participants', function ($q) use ($request) {
            if ($request->conference_id) {
                $q->where('conference_id', $request->conference_id);
            }
        });
        
        $participants = $query->with(['participants.participantType'])
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($user) {
                $participantTypes = $user->participants->map(function ($participant) {
                    return $participant->participantType->name ?? 'Unknown';
                })->unique()->implode(', ');
                
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'participant_types' => $participantTypes,
                    'conferences' => $user->participants->pluck('conference_id')->unique()->count()
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $participants
        ]);
    }

    /**
     * Get participants by type for filtering
     */
    public function getParticipantsByType(Request $request)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $participantType = $request->get('type');
        $conferenceId = $request->get('conference_id');

        $query = User::whereHas('participants', function ($q) use ($participantType, $conferenceId) {
            if ($participantType) {
                $q->whereHas('participantType', function ($typeQuery) use ($participantType) {
                    $typeQuery->where('name', $participantType);
                });
            }
            if ($conferenceId) {
                $q->where('conference_id', $conferenceId);
            }
        })
        ->with(['participants.participantType'])
        ->select('id', 'first_name', 'last_name', 'email')
        ->orderBy('first_name');

        $participants = $query->get()->map(function ($user) {
            $participantTypes = $user->participants->map(function ($participant) {
                return $participant->participantType->name ?? 'Unknown';
            })->unique()->implode(', ');
            
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'participant_types' => $participantTypes,
                'conferences' => $user->participants->pluck('conference_id')->unique()->count()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $participants
        ]);
    }

    /**
     * Get participant types for filtering
     */
    public function getParticipantTypes(Request $request)
    {
        // Check if user has admin or superadmin role
        if (!auth()->user()->roles()->whereIn('name', ['admin', 'superadmin'])->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $types = \App\Models\ParticipantType::select('name', 'category', 'description')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }
}
