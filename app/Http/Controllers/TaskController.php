<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Conference;
use App\Services\TaskNotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user has tasker role
        $isTasker = $user->roles()->where('name', 'tasker')->exists();
        
        if ($isTasker) {
            // Taskers only see tasks assigned to them (using many-to-many relationship)
            $tasks = Task::with(['users', 'createdBy'])
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        } else {
            // Admins and other roles see all tasks
            $tasks = Task::with(['users', 'createdBy'])->latest()->get();
        }
        
        return view('tasks.index', compact('tasks'));
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['conference_id'] = $request->conference_id;
        
        // Remove assigned_to from validated data as we'll handle it separately
        $assignedUsers = $validated['assigned_to'];
        unset($validated['assigned_to']);

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        $oldStatus = $task->status;
        $oldAssignedUsers = $task->users->pluck('id')->toArray();

        $assignedUsers = $validated['assigned_to'];
        unset($validated['assigned_to']);
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
} 