@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-6">{{ $task->title }}</h2>
    
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Description:</span>
        <p class="mt-1">{{ $task->description }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Due Date:</span>
            <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
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
            @if($task->users->count() > 0)
                <div class="flex flex-wrap gap-1 mt-1">
                    @foreach($task->users as $user)
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </span>
                    @endforeach
                </div>
            @else
                <span class="text-gray-500 italic">No one assigned</span>
            @endif
        </div>
    </div>

    <div class="mb-6">
        <span class="font-semibold text-gray-700">Created By:</span>
        <span>{{ $task->createdBy ? $task->createdBy->first_name . ' ' . $task->createdBy->last_name : 'Unknown' }}</span>
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