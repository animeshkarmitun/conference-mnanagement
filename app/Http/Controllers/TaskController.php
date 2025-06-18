<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Conference;
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

        $validated['assigned_to'] = $request->assigned_to;
        $validated['conference_id'] = $request->conference_id;

        $task->update($validated);

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

        $task->update($validated);

        return redirect()->back()
            ->with('success', 'Task status updated successfully.');
    }
} 