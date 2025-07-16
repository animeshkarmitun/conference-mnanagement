<?php
namespace App\Http\Controllers;

use App\Services\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    protected $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
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

            $token = $this->googleService->authenticate($request->get('code'));
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found. Please login again.');
            }

            $user->google_token = json_encode($token);
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Gmail connected successfully');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to connect Gmail: ' . $e->getMessage());
        }
    }

    public function showDashboard()
    {
        return view('dashboard');
    }

    public function showGmailThreads(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first to access Gmail.');
            }

            $user = Auth::user();
            if (!$user->google_token) {
                return redirect()->route('google.redirect')->with('error', 'Please connect your Gmail account.');
            }

            $this->googleService->setAccessToken(json_decode($user->google_token, true));
            $maxResults = $request->input('maxResults', 10);
            $pageToken = $request->input('pageToken');
            $result = $this->googleService->listThreads('me', $maxResults, $pageToken);

            return view('gmail.index', [
                'threads' => $result['threads'],
                'nextPageToken' => $result['nextPageToken'],
                'maxResults' => $maxResults,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to load Gmail threads: ' . $e->getMessage());
        }
    }

    public function showReplyForm($threadId)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first to reply to emails.');
            }

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
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first to send emails.');
            }

            $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string|max:255',
                'body' => 'required|string'
            ]);

            $user = Auth::user();
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
} 