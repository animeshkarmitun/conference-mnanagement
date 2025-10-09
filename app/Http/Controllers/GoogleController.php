<?php
namespace App\Http\Controllers;

use App\Services\GoogleService;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    protected $googleService;
    protected $emailTrackingService;

    public function __construct(GoogleService $googleService, EmailTrackingService $emailTrackingService)
    {
        $this->googleService = $googleService;
        $this->emailTrackingService = $emailTrackingService;
    }

    public function redirectToGoogle()
    {
        return redirect()->away($this->googleService->getAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first to connect your Gmail account.');
            }

            // Get the authorization code
            $code = $request->get('code');
            if (!$code) {
                return redirect()->route('gmail.index')->with('error', 'No authorization code received from Google.');
            }

            // Authenticate with Google
            $token = $this->googleService->authenticate($code);
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found. Please login again.');
            }

            // Save the Google token
            $user->google_token = json_encode($token);
            $user->save();

            // Redirect back to Gmail page instead of dashboard
            return redirect()->route('gmail.index')->with('success', 'Gmail connected successfully! You can now view your conversations.');
        } catch (\Exception $e) {
            \Log::error('Gmail OAuth callback error: ' . $e->getMessage());
            return redirect()->route('gmail.index')->with('error', 'Failed to connect Gmail: ' . $e->getMessage());
        }
    }

    public function showDashboard()
    {
        return view('dashboard');
    }

    /**
     * Clear Gmail connection and redirect to reconnection
     */
    public function disconnectGmail()
    {
        try {
            $user = Auth::user();
            $user->google_token = null;
            $user->save();
            
            \Log::info('Gmail disconnected for user: ' . $user->id);
            
            return redirect()->route('gmail.index')->with('success', 'Gmail account disconnected successfully. You can reconnect anytime.');
        } catch (\Exception $e) {
            \Log::error('Gmail disconnect error: ' . $e->getMessage());
            return redirect()->route('gmail.index')->with('error', 'Failed to disconnect Gmail: ' . $e->getMessage());
        }
    }

    public function showGmailThreads(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if user has admin or superadmin role
            if (!$user->hasRole('admin') && !$user->hasRole('superadmin')) {
                return view('gmail.index', [
                    'threads' => [],
                    'nextPageToken' => null,
                    'maxResults' => 30,
                    'searchQuery' => $request->input('q'),
                    'needsConnection' => false,
                    'accessDenied' => true,
                    'error' => 'Access denied. Gmail conversations are only available to administrators.'
                ]);
            }
            
            // Check if user has Google token
            if (!$user->google_token) {
                return view('gmail.index', [
                    'threads' => [],
                    'nextPageToken' => null,
                    'maxResults' => 30,
                    'searchQuery' => $request->input('q'),
                    'needsConnection' => true,
                ]);
            }

            // Decode and validate the token
            $token = json_decode($user->google_token, true);
            if (!$token) {
                \Log::error('Invalid token format for user: ' . $user->id);
                return view('gmail.index', [
                    'threads' => [],
                    'nextPageToken' => null,
                    'maxResults' => 30,
                    'searchQuery' => $request->input('q'),
                    'needsConnection' => true,
                    'error' => 'Invalid Gmail token. Please reconnect your account.'
                ]);
            }

            try {
                // Ensure token is valid and refresh if needed
                $validToken = $this->googleService->ensureValidToken($token);
                
                // If token was refreshed, save the new token
                if ($validToken !== $token) {
                    $user->google_token = json_encode($validToken);
                    $user->save();
                    \Log::info('Gmail token refreshed for user: ' . $user->id);
                }
                
                $this->googleService->setAccessToken($validToken);
                
            } catch (\Exception $e) {
                \Log::error('Gmail token validation failed for user ' . $user->id . ': ' . $e->getMessage());
                
                // Clear invalid token and show reconnection option
                $user->google_token = null;
                $user->save();
                
                return view('gmail.index', [
                    'threads' => [],
                    'nextPageToken' => null,
                    'maxResults' => 30,
                    'searchQuery' => $request->input('q'),
                    'needsConnection' => true,
                    'error' => 'Gmail authentication expired. Please reconnect your account.'
                ]);
            }

            $maxResults = $request->input('maxResults', 30);
            $pageToken = $request->input('pageToken');
            $query = $request->input('q');
            $participant = $request->input('participant');
            
            // If participant is selected, modify the query to search for that participant's emails
            if ($participant) {
                if ($query) {
                    $query = "(from:{$participant} OR to:{$participant}) {$query}";
                } else {
                    $query = "from:{$participant} OR to:{$participant}";
                }
            }
            
            $result = $this->googleService->listThreads('me', $maxResults, $pageToken, $query);

            // Get participants for dropdown
            $participants = $this->emailTrackingService->getParticipantsWithEmails();

            return view('gmail.index', [
                'threads' => $result['threads'],
                'nextPageToken' => $result['nextPageToken'],
                'maxResults' => $maxResults,
                'searchQuery' => $request->input('q'),
                'selectedParticipant' => $participant,
                'needsConnection' => false,
                'participants' => $participants,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Gmail threads error: ' . $e->getMessage());
            
            // Check if it's an authentication error
            if (strpos($e->getMessage(), 'authentication') !== false || strpos($e->getMessage(), '401') !== false) {
                // Clear the token and show reconnection option
                $user = Auth::user();
                $user->google_token = null;
                $user->save();
                
                return view('gmail.index', [
                    'threads' => [],
                    'nextPageToken' => null,
                    'maxResults' => 30,
                    'searchQuery' => $request->input('q'),
                    'needsConnection' => true,
                    'error' => 'Gmail authentication failed. Please reconnect your account.'
                ]);
            }
            
            return view('gmail.index', [
                'threads' => [],
                'nextPageToken' => null,
                'maxResults' => 30,
                'searchQuery' => $request->input('q'),
                'needsConnection' => false,
                'error' => 'Failed to load Gmail threads: ' . $e->getMessage()
            ]);
        }
    }

    public function showReplyForm($threadId)
    {
        try {
            // Note: Authentication and admin role checks are handled by middleware
            $user = Auth::user();
            
            if (!$user->google_token) {
                return redirect()->route('google.redirect')->with('error', 'Please connect your Gmail account.');
            }

            $this->googleService->setAccessToken(json_decode($user->google_token, true));
            $thread = $this->googleService->getThread($threadId);
            
            if (!$thread || !$thread->getMessages()) {
                return redirect()->route('gmail.index')->with('error', 'Thread not found.');
            }

            $originalMessage = $thread->getMessages()[0];
            $from = $this->googleService->getHeader($originalMessage, 'From');
            $subject = $this->googleService->getHeader($originalMessage, 'Subject');
            $toEmail = $this->googleService->extractEmailAddress($from);

            return view('gmail.reply', [
                'threadId' => $threadId,
                'toEmail' => $toEmail,
                'originalSubject' => $subject,
                'originalMessage' => $originalMessage,
                'thread' => $thread
            ]);
        } catch (\Exception $e) {
            return redirect()->route('gmail.index')->with('error', 'Failed to load reply form: ' . $e->getMessage());
        }
    }

    public function sendReply(Request $request, $threadId)
    {
        try {
            // Note: Authentication and admin role checks are handled by middleware
            $user = Auth::user();

            $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string|max:255',
                'body' => 'required|string'
            ]);

            if (!$user->google_token) {
                return redirect()->route('google.redirect')->with('error', 'Please connect your Gmail account.');
            }

            $this->googleService->setAccessToken(json_decode($user->google_token, true));
            
            $result = $this->googleService->sendEmail(
                $request->to,
                $request->subject,
                $request->body,
                $threadId
            );

            return redirect()->route('gmail.index')->with('success', 'Reply sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send reply: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get participants for dropdown
     */
    public function getParticipants(Request $request)
    {
        try {
            $participants = $this->emailTrackingService->getParticipantsWithEmails();
            
            $formattedParticipants = $participants->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'email' => $participant->user->email,
                    'name' => $participant->user->first_name . ' ' . $participant->user->last_name,
                    'conference' => $participant->conference->name ?? 'N/A',
                    'participant_type' => $participant->participantType->name ?? 'N/A',
                ];
            });
            
            return response()->json($formattedParticipants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 