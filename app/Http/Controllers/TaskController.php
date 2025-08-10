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
        $tasks = Task::with(['assignedTo', 'createdBy'])->latest()->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();
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
            'assigned_to' => 'required|exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = $request->assigned_to;
        $validated['conference_id'] = $request->conference_id;

        $task = Task::create($validated);

        // Send notification to the assigned tasker
        $taskNotificationService = new TaskNotificationService();
        $taskNotificationService->notifyTaskAssigned($task);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['assignedTo', 'createdBy']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $users = User::all();
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
            'assigned_to' => 'required|exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
        ]);

        $oldStatus = $task->status;
        $oldAssignedTo = $task->assigned_to;

        $validated['assigned_to'] = $request->assigned_to;
        $validated['conference_id'] = $request->conference_id;

        $task->update($validated);

        // Send notifications for task updates
        $taskNotificationService = new TaskNotificationService();
        
        // If status changed, notify about status change
        if ($oldStatus !== $task->status) {
            $taskNotificationService->notifyTaskStatusChanged($task, $oldStatus);
        }
        
        // If assigned to different person, notify about assignment
        if ($oldAssignedTo !== $task->assigned_to) {
            $taskNotificationService->notifyTaskAssigned($task);
        } else {
            // Otherwise, notify about general update
            $taskNotificationService->notifyTaskUpdated($task);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
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

        // Send notification for status change
        $taskNotificationService = new TaskNotificationService();
        $taskNotificationService->notifyTaskStatusChanged($task, $oldStatus);

        // If task is completed, send completion notification
        if ($task->status === 'completed') {
            $taskNotificationService->notifyTaskCompleted($task);
        }

        return redirect()->back()
            ->with('success', 'Task status updated successfully.');
    }
} 