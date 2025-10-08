<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Conference;
use App\Services\TaskNotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user has tasker role
        $isTasker = $user->roles()->where('name', 'tasker')->exists();
        
        $query = Task::with(['users', 'createdBy']);
        
        if ($isTasker) {
            // Taskers only see tasks assigned to them (using many-to-many relationship)
            $query->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }
        
        // Apply filters
        $this->applyFilters($query, $request);
        
        $tasks = $query->latest()->get();
        
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // Date filter
        if ($request->has('date_filter') && $request->date_filter) {
            $now = now();
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('due_date', $now->toDateString());
                    break;
                case 'tomorrow':
                    $query->whereDate('due_date', $now->copy()->addDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('due_date', [
                        $now->copy()->startOfWeek()->toDateString(),
                        $now->copy()->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'next_week':
                    $nextWeek = $now->copy()->addWeek();
                    $query->whereBetween('due_date', [
                        $nextWeek->startOfWeek()->toDateString(),
                        $nextWeek->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'overdue':
                    $query->where('due_date', '<', $now->toDateString())
                          ->where('status', '!=', 'completed');
                    break;
            }
        }
        
        // Priority filter
        if ($request->has('priority_filter') && $request->priority_filter) {
            $query->where('priority', $request->priority_filter);
        }
        
        // Status filter
        if ($request->has('status_filter') && $request->status_filter) {
            $query->where('status', $request->status_filter);
        }
    }

    public function create()
    {
        // Only users with the 'tasker' role should be assignable
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'tasker');
        })->get();
        $conferences = Conference::all();
        return view('tasks.create', compact('users', 'conferences'));
    }

    public function store(Request $request)
    {
        // Handle the new assigned_to format (comma-separated string)
        $assignedToInput = $request->input('assigned_to');
        if (is_string($assignedToInput) && !empty($assignedToInput)) {
            $assignedUsers = array_filter(explode(',', $assignedToInput));
        } else {
            $assignedUsers = [];
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        // Validate that all assigned user IDs exist
        if (!empty($assignedUsers)) {
            $existingUserIds = User::whereIn('id', $assignedUsers)->pluck('id')->toArray();
            $invalidUserIds = array_diff($assignedUsers, $existingUserIds);
            if (!empty($invalidUserIds)) {
                return back()->withErrors(['assigned_to' => 'Some selected users are invalid.'])->withInput();
            }
        } else {
            return back()->withErrors(['assigned_to' => 'Please select at least one user to assign the task to.'])->withInput();
        }

        $validated['created_by'] = auth()->id();
        $validated['conference_id'] = $request->conference_id;

        $task = Task::create($validated);

        // Attach multiple users to the task
        $task->users()->attach($assignedUsers, [
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Send notification to all assigned taskers
        $taskNotificationService = new TaskNotificationService();
        foreach ($assignedUsers as $userId) {
            $user = User::find($userId);
            $taskNotificationService->notifyTaskAssigned($task, $user);
        }

        // Determine who gets notified and provide appropriate message
        $currentUser = auth()->user();
        $isCurrentUserAdmin = $currentUser->roles()->whereIn('name', ['admin', 'superadmin'])->exists();
        
        if ($isCurrentUserAdmin) {
            $message = 'Task created successfully. Assigned users have been notified.';
        } else {
            $message = 'Task created successfully. Admins and superadmins have been notified.';
        }

        return redirect()->route('tasks.index')->with('success', $message);
    }

    public function show(Task $task)
    {
        $task->load(['assignedTo', 'createdBy']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // Only users with the 'tasker' role should be assignable
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'tasker');
        })->get();
        $conferences = Conference::all();
        return view('tasks.edit', compact('task', 'users', 'conferences'));
    }

    public function update(Request $request, Task $task)
    {
        // Handle the new assigned_to format (comma-separated string)
        $assignedToInput = $request->input('assigned_to');
        if (is_string($assignedToInput) && !empty($assignedToInput)) {
            $assignedUsers = array_filter(explode(',', $assignedToInput));
        } else {
            $assignedUsers = [];
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        // Validate that all assigned user IDs exist
        if (!empty($assignedUsers)) {
            $existingUserIds = User::whereIn('id', $assignedUsers)->pluck('id')->toArray();
            $invalidUserIds = array_diff($assignedUsers, $existingUserIds);
            if (!empty($invalidUserIds)) {
                return back()->withErrors(['assigned_to' => 'Some selected users are invalid.'])->withInput();
            }
        } else {
            return back()->withErrors(['assigned_to' => 'Please select at least one user to assign the task to.'])->withInput();
        }

        $oldStatus = $task->status;
        $oldAssignedUsers = $task->users->pluck('id')->toArray();

        $validated['conference_id'] = $request->conference_id;

        $task->update($validated);

        // Update user assignments
        $task->users()->sync($assignedUsers);

        // Send notifications for task updates
        $taskNotificationService = new TaskNotificationService();
        
        // If status changed, notify about status change
        if ($oldStatus !== $task->status) {
            $taskNotificationService->notifyTaskStatusChanged($task, $oldStatus);
        }
        
        // Check if assignments changed
        $newAssignedUsers = $assignedUsers;
        $addedUsers = array_diff($newAssignedUsers, $oldAssignedUsers);
        $removedUsers = array_diff($oldAssignedUsers, $newAssignedUsers);
        
        // Notify newly assigned users
        foreach ($addedUsers as $userId) {
            $user = User::find($userId);
            $taskNotificationService->notifyTaskAssigned($task, $user);
        }
        
        // Notify existing users about updates (if no assignment changes)
        if (empty($addedUsers) && empty($removedUsers)) {
            $taskNotificationService->notifyTaskUpdated($task);
        }

        // Determine who gets notified and provide appropriate message
        $currentUser = auth()->user();
        $isCurrentUserAdmin = $currentUser->roles()->whereIn('name', ['admin', 'superadmin'])->exists();
        
        if ($isCurrentUserAdmin) {
            $message = 'Task updated successfully. Assigned users have been notified.';
        } else {
            $message = 'Task updated successfully. Admins and superadmins have been notified.';
        }

        return redirect()->route('tasks.index')->with('success', $message);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $oldStatus = $task->status;
        $task->update($validated);

        \Log::info('Task status updated', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'old_status' => $oldStatus,
            'new_status' => $task->status,
            'updated_by' => auth()->user()->email
        ]);

        // Send notification for status change
        $taskNotificationService = new TaskNotificationService();
        $taskNotificationService->notifyTaskStatusChanged($task, $oldStatus);

        // If task is completed, send completion notification
        if ($task->status === 'completed') {
            $taskNotificationService->notifyTaskCompleted($task);
        }

        // Determine who gets notified and provide appropriate message
        $currentUser = auth()->user();
        $isCurrentUserAdmin = $currentUser->roles()->whereIn('name', ['admin', 'superadmin'])->exists();
        
        if ($isCurrentUserAdmin) {
            $message = 'Task status updated successfully. Assigned users have been notified.';
        } else {
            $message = 'Task status updated successfully. Admins and superadmins have been notified.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export tasks to CSV
     */
    public function export(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // Check if user has tasker role
        $isTasker = $user->roles()->where('name', 'tasker')->exists();
        
        $query = Task::with(['users', 'createdBy']);
        
        if ($isTasker) {
            // Taskers only see tasks assigned to them (using many-to-many relationship)
            $query->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }
        
        // Apply filters
        $this->applyFilters($query, $request);
        
        $tasks = $query->latest()->get();
        
        $filename = 'tasks_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Title',
                'Description',
                'Theme',
                'Priority',
                'Status',
                'Due Date',
                'Assigned Users',
                'Created By',
                'Created At',
                'Updated At'
            ]);
            
            // CSV Data
            foreach ($tasks as $task) {
                $assignedUsers = $task->users->map(function($user) {
                    return ($user->first_name ?? $user->name) . ' ' . ($user->last_name ?? '') . ' (' . $user->email . ')';
                })->join('; ');
                
                $createdBy = $task->createdBy ? 
                    ($task->createdBy->first_name ?? $task->createdBy->name) . ' ' . ($task->createdBy->last_name ?? '') . ' (' . $task->createdBy->email . ')' : 
                    'N/A';
                
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->description ?? '',
                    $task->theme ?? '',
                    ucfirst($task->priority),
                    ucfirst(str_replace('_', ' ', $task->status)),
                    $task->due_date ? $task->due_date->format('Y-m-d H:i:s') : 'N/A',
                    $assignedUsers ?: 'Unassigned',
                    $createdBy,
                    $task->created_at->format('Y-m-d H:i:s'),
                    $task->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 