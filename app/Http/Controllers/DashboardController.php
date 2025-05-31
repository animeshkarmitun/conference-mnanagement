<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Participant;
use App\Models\Session;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalConferences = Conference::count();
        $totalParticipants = Participant::count();
        $upcomingSessions = Session::where('start_time', '>', now())->count();
        $pendingTasks = Task::where('status', '!=', 'completed')->count();

        // Get recent activities (last 5)
        $recentActivities = DB::table('activity_log')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return (object) [
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    'type_color' => $this->getActivityTypeColor($activity->type),
                    'icon_color' => $this->getActivityIconColor($activity->type),
                    'icon' => $this->getActivityIcon($activity->type),
                ];
            });

        // Get upcoming events (next 5)
        $upcomingEvents = Session::with('venue')
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalConferences',
            'totalParticipants',
            'upcomingSessions',
            'pendingTasks',
            'recentActivities',
            'upcomingEvents'
        ));
    }

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