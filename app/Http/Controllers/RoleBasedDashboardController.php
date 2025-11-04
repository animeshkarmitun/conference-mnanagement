<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Participant;
use App\Models\Session;
use App\Models\Task;
use App\Models\Notification;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleBasedDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    /**
     * Display the role-based dashboard
     * Shows different widgets and data based on user's permissions
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all permissions
        $permissions = $user->getAllPermissions();
        
        // If no roles or permissions, show error message
        if (!$user->hasAnyRole()) {
            return view('dashboard.role-based', [
                'error' => 'No role assigned. Please contact an administrator.',
                'user' => $user,
                'permissions' => [],
                'dashboardData' => [],
            ]);
        }
        
        $dashboardData = [
            'user' => $user,
            'roles' => $user->roles,
            'permissions' => $permissions,
        ];
        
        // Load data based on permissions
        
        // Conference-related data
        $selectedConferenceId = null;
        if ($user->hasPermission('conferences.view')) {
            $conferences = Conference::orderBy('start_date', 'desc')->get();
            $dashboardData['conferences'] = $conferences;
            $dashboardData['conference_count'] = $conferences->count();
            
            // Get selected conference (default to first if none selected)
            $selectedConferenceId = $request->get('conference_id') ?? ($conferences->first()?->id);
            $dashboardData['selectedConferenceId'] = $selectedConferenceId;
            
            if ($selectedConferenceId) {
                $conference = Conference::find($selectedConferenceId);
                $dashboardData['selectedConference'] = $conference;
            }
        }
        
        // Get dashboard data for the selected conference if user has participants permission
        if ($user->hasPermission('participants.view') && $selectedConferenceId) {
            try {
                $dashboardData['dashboardStats'] = $this->dashboardService->getAllDashboardData($selectedConferenceId);
            } catch (\Exception $e) {
                // If there's an error, set empty dashboard data
                $dashboardData['dashboardStats'] = null;
            }
        }
        
        // Participant-related data
        if ($user->hasPermission('participants.view')) {
            $query = Participant::query();
            
            // If user can only view participants, limit based on selected conference
            if (isset($dashboardData['selectedConferenceId'])) {
                $query->where('conference_id', $dashboardData['selectedConferenceId']);
            }
            
            $participants = $query->with(['user', 'participantType', 'conference'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $dashboardData['recent_participants'] = $participants;
            $dashboardData['participant_count'] = Participant::when(
                isset($dashboardData['selectedConferenceId']),
                fn($q) => $q->where('conference_id', $dashboardData['selectedConferenceId'])
            )->count();
        }
        
        // Session-related data
        if ($user->hasPermission('sessions.view')) {
            $query = Session::query();
            
            if (isset($dashboardData['selectedConferenceId'])) {
                $query->where('conference_id', $dashboardData['selectedConferenceId']);
            }
            
            $sessions = $query->with(['conference', 'participants'])
                ->orderBy('start_time', 'desc')
                ->limit(10)
                ->get();
            
            $dashboardData['recent_sessions'] = $sessions;
            $dashboardData['session_count'] = Session::when(
                isset($dashboardData['selectedConferenceId']),
                fn($q) => $q->where('conference_id', $dashboardData['selectedConferenceId'])
            )->count();
            
            // Upcoming sessions
            $dashboardData['upcoming_sessions'] = Session::when(
                isset($dashboardData['selectedConferenceId']),
                fn($q) => $q->where('conference_id', $dashboardData['selectedConferenceId'])
            )
            ->where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();
        }
        
        // Task-related data
        if ($user->hasPermission('tasks.view')) {
            $query = Task::query();
            
            if (isset($dashboardData['selectedConferenceId'])) {
                $query->where('conference_id', $dashboardData['selectedConferenceId']);
            }
            
            // If user is assigned tasks, show their assigned tasks
            if (!$user->hasPermission('tasks.*') && $user->assignedTasks()->exists()) {
                $query->where('assigned_to', $user->id);
            }
            
            $tasks = $query->with(['conference', 'assignedTo', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $dashboardData['recent_tasks'] = $tasks;
            
            // Task statistics
            if (isset($dashboardData['selectedConferenceId'])) {
                $taskQuery = Task::where('conference_id', $dashboardData['selectedConferenceId']);
            } else {
                $taskQuery = Task::query();
            }
            
            if (!$user->hasPermission('tasks.*') && $user->assignedTasks()->exists()) {
                $taskQuery->where('assigned_to', $user->id);
            }
            
            $dashboardData['task_stats'] = [
                'total' => $taskQuery->count(),
                'pending' => (clone $taskQuery)->where('status', 'pending')->count(),
                'in_progress' => (clone $taskQuery)->where('status', 'in_progress')->count(),
                'completed' => (clone $taskQuery)->where('status', 'completed')->count(),
            ];
        }
        
        // Notification-related data
        if ($user->hasPermission('notifications.view')) {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $dashboardData['recent_notifications'] = $notifications;
            $dashboardData['unread_notification_count'] = Notification::where('user_id', $user->id)
                ->where('read_status', false)
                ->count();
        }
        
        // Statistics summary
        $dashboardData['stats'] = $this->getStatisticsSummary($user, $dashboardData);
        
        return view('dashboard.role-based', $dashboardData);
    }
    
    /**
     * Get statistics summary based on user permissions
     */
    private function getStatisticsSummary($user, $dashboardData): array
    {
        $stats = [];
        
        if ($user->hasPermission('conferences.view')) {
            $stats['conferences'] = $dashboardData['conference_count'] ?? 0;
        }
        
        if ($user->hasPermission('participants.view')) {
            $stats['participants'] = $dashboardData['participant_count'] ?? 0;
        }
        
        if ($user->hasPermission('sessions.view')) {
            $stats['sessions'] = $dashboardData['session_count'] ?? 0;
            $stats['upcoming_sessions'] = isset($dashboardData['upcoming_sessions']) 
                ? $dashboardData['upcoming_sessions']->count() 
                : 0;
        }
        
        if ($user->hasPermission('tasks.view')) {
            $stats = array_merge($stats, $dashboardData['task_stats'] ?? []);
        }
        
        if ($user->hasPermission('notifications.view')) {
            $stats['unread_notifications'] = $dashboardData['unread_notification_count'] ?? 0;
        }
        
        return $stats;
    }
}



