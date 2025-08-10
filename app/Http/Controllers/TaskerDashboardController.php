<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Notification;

class TaskerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get tasks assigned to the current tasker
        $assignedTasks = Task::with(['conference', 'createdBy'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->get();
            
        // Get recent notifications for the tasker
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Debug: Log the data being passed to the view
        \Log::info('TaskerDashboard Data', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'tasks_count' => $assignedTasks->count(),
            'notifications_count' => $notifications->count(),
            'notifications' => $notifications->pluck('message')->toArray()
        ]);
            
        return view('dashboard-tasker', compact('assignedTasks', 'notifications'));
    }
}
