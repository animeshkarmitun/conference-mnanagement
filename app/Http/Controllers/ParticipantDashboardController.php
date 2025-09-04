<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantDashboardController extends Controller
{
    /**
     * Participant Dashboard - for attendees and speakers
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get participant's conference
        $participant = Participant::with(['conference', 'participantType', 'sessions'])
            ->where('user_id', $user->id)
            ->where('registration_status', 'approved')
            ->latest()
            ->first();
            
        if (!$participant) {
            return redirect()->route('participants.profile')->with('error', 'No approved conference registration found.');
        }
        
        // Get participant's sessions
        $sessions = $participant->sessions()
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();
            
        // Get recent notifications (if any)
        $notifications = collect(); // Placeholder for future notification system
        
        // Get conference statistics
        $conferenceStats = [
            'total_sessions' => Session::where('conference_id', $participant->conference_id)->count(),
            'my_sessions' => $participant->sessions()->count(),
            'days_until_conference' => $participant->conference->start_date ? now()->diffInDays(\Carbon\Carbon::parse($participant->conference->start_date), false) : 0,
            'conference_status' => $participant->conference->status ?? 'active',
        ];
        
        return view('participant-dashboard.index', compact(
            'participant',
            'sessions',
            'notifications',
            'conferenceStats'
        ));
    }
}
