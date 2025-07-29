@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Tasks</h2>
    <a href="{{ route('tasks.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Task</a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <table class="min-w-full divide-y divide-gray-200" id="myTable">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tasks as $task)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $task->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($task->assignedTo)
                            {{ $task->assignedTo->first_name ?? $task->assignedTo->name }} {{ $task->assignedTo->last_name ?? '' }} ({{ $task->assignedTo->email }})
                        @else
                            <span class="text-gray-400">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($task->due_date)
                            {{ is_object($task->due_date) ? $task->due_date->format('M d, Y') : \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                            {{ $task->priority === 'high' ? 'bg-red-100 text-red-700' : 
                               ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : 
                               'bg-green-100 text-green-700') }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : 
                               ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 
                               ($task->status === 'cancelled' ? 'bg-red-100 text-red-700' : 
                               'bg-yellow-100 text-yellow-700')) }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('tasks.show', $task) }}" class="text-yellow-600 hover:underline">View</a>
                        <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No tasks found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</div>
@endsection 