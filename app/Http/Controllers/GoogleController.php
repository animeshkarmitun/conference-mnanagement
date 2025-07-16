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

    public function showGmailThreads()
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
            $threads = $this->googleService->listThreads();

            return view('gmail.index', ['threads' => $threads]);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to load Gmail threads: ' . $e->getMessage());
        }
    }
} 