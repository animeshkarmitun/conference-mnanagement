@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">{{ $task->title }}</h2>
    
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Description:</span>
        <p class="mt-1">{{ $task->description }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Due Date:</span>
            <span>{{ $task->due_date->format('M d, Y') }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Priority:</span>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                {{ $task->priority === 'high' ? 'bg-red-100 text-red-700' : 
                   ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : 
                   'bg-green-100 text-green-700') }}">
                {{ ucfirst($task->priority) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Status:</span>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : 
                   ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 
                   ($task->status === 'cancelled' ? 'bg-red-100 text-red-700' : 
                   'bg-yellow-100 text-yellow-700')) }}">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Assigned To:</span>
            <span>{{ $task->assignedTo->name }}</span>
        </div>
    </div>

    <div class="mb-6">
        <span class="font-semibold text-gray-700">Created By:</span>
        <span>{{ $task->createdBy->name }}</span>
    </div>

    <div class="mb-6">
        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="flex items-center space-x-4">
            @csrf
            @method('PATCH')
            <label for="status" class="font-semibold text-gray-700">Update Status:</label>
            <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Update Status</button>
        </form>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Edit</a>
        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>
@endsection 