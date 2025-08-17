<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Participant;
use App\Models\Session;
use App\Models\Task;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        // SECURITY: Check if user has admin privileges
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            // Redirect participants to their profile page
            if ($user->hasRole('attendee') || $user->hasRole('speaker')) {
                return redirect()->route('participants.profile')->with('error', 'Access denied. Please use your participant dashboard.');
            }
            
            // For other roles, redirect to appropriate dashboard
            if ($user->hasRole('tasker')) {
                return redirect()->route('dashboard.tasker');
            }
            
            if ($user->hasRole('event_coordinator')) {
                return redirect()->route('event-coordinator.dashboard');
            }
            
            // Default fallback - redirect to profile
            return redirect()->route('participants.profile')->with('error', 'Access denied. Insufficient privileges.');
        }

        // Get all conferences for dropdown
        $conferences = Conference::orderBy('start_date', 'desc')->get();
        
        // Get selected conference (default to first if none selected)
        $selectedConferenceId = $request->get('conference_id') ?? ($conferences->first()?->id);
        
        if (!$selectedConferenceId) {
            // No conferences exist, return empty dashboard
            return view('dashboard', [
                'conferences' => collect(),
                'selectedConferenceId' => null,
                'dashboardData' => null,
                'noConferences' => true,
            ]);
        }

        // Get all dashboard data for the selected conference
        $dashboardData = $this->dashboardService->getAllDashboardData($selectedConferenceId);

        // Get global statistics (across all conferences)
        $globalStats = $this->getGlobalStatistics();

        return view('dashboard', compact(
            'conferences',
            'selectedConferenceId',
            'dashboardData',
            'globalStats'
        ));
    }

    /**
     * Get global statistics across all conferences
     */
    private function getGlobalStatistics()
    {
        return [
            'total_conferences' => Conference::count(),
            'total_participants' => Participant::count(),
            'upcoming_sessions' => Session::where('start_time', '>', now())->count(),
            'pending_tasks' => Task::where('status', '!=', 'completed')->count(),
        ];
    }

    /**
     * AJAX endpoint for getting dashboard data
     */
    public function getDashboardData(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        try {
            $dashboardData = $this->dashboardService->getAllDashboardData($conferenceId);
            return response()->json($dashboardData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load dashboard data'], 500);
        }
    }

    /**
     * Get conference progress data only
     */
    public function getConferenceProgress(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        try {
            $progressData = $this->dashboardService->getConferenceProgress($conferenceId);
            return response()->json($progressData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load conference progress'], 500);
        }
    }

    /**
     * Get task progress data only
     */
    public function getTaskProgress(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        try {
            $taskData = $this->dashboardService->getTaskProgress($conferenceId);
            return response()->json($taskData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load task progress'], 500);
        }
    }

    /**
     * Get participant statistics
     */
    public function getParticipantStats(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        try {
            $participantData = $this->dashboardService->getParticipantStatistics($conferenceId);
            return response()->json($participantData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load participant statistics'], 500);
        }
    }

    /**
     * Get speaker statistics
     */
    public function getSpeakerStats(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        try {
            $speakerData = $this->dashboardService->getSpeakerStatistics($conferenceId);
            return response()->json($speakerData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load speaker statistics'], 500);
        }
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats(Request $request)
    {
        $conferenceId = $request->get('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        try {
            $summaryData = $this->dashboardService->getSummaryStats($conferenceId);
            return response()->json($summaryData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load summary statistics'], 500);
        }
    }

    // Legacy methods for backward compatibility
    private function getActivityTypeColor($type)
    {
        return match ($type) {
            'conference' => 'bg-yellow-100',
            'participant' => 'bg-blue-100',
            'session' => 'bg-green-100',
            'task' => 'bg-red-100',
            default => 'bg-gray-100',
        };
    }

    private function getActivityIconColor($type)
    {
        return match ($type) {
            'conference' => 'text-yellow-500',
            'participant' => 'text-blue-500',
            'session' => 'text-green-500',
            'task' => 'text-red-500',
            default => 'text-gray-500',
        };
    }

    private function getActivityIcon($type)
    {
        return match ($type) {
            'conference' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>',
            'participant' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
            'session' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'task' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>',
            default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        };
    }
} 