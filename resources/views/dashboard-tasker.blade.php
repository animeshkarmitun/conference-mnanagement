@extends('layouts.app')

@section('title', 'Tasker Dashboard')

@section('content')
@php
    // Get user's notifications
    $notifications = \App\Models\Notification::where('user_id', auth()->id())
        ->where('type', 'TaskUpdate')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Get user's assigned tasks
    $assignedTasks = \App\Models\Task::where('assigned_to', auth()->id())
        ->with(['conference'])
        ->orderBy('due_date', 'asc')
        ->limit(5)
        ->get();
@endphp
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Tasker Dashboard</h1>
        <div class="text-gray-600 text-lg font-medium">Your assigned tasks and quick actions</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <!-- My Tasks Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="My Tasks">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">My Tasks</h2>
            </div>
            <a href="{{ route('tasks.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
        </div>
        @if($assignedTasks->count() > 0)
            <ul class="mt-4 space-y-3">
                @foreach($assignedTasks as $task)
                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-sm text-gray-600">{{ $task->conference->name ?? 'Conference' }}</div>
                            @if($task->due_date)
                                <div class="text-xs text-gray-500">Due: {{ $task->due_date->format('M d, Y') }}</div>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold 
                                @if($task->status === 'completed') bg-green-100 text-green-700
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                                @elseif($task->status === 'cancelled') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold 
                                @if($task->priority === 'high') bg-red-100 text-red-700
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p>No tasks assigned yet</p>
            </div>
        @endif
    </div>
    <!-- Notifications Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="Notifications">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 rounded-full mr-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 014 6v10a4 4 0 004 4h10a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.83 1.17z"/></svg>
                </span>
                <h2 class="text-lg font-semibold text-gray-900">Recent Notifications</h2>
            </div>
            <a href="{{ route('notifications.index') }}" class="text-sm text-green-600 hover:text-green-800">View All</a>
        </div>
        @if($notifications->count() > 0)
            <ul class="mt-4 space-y-3">
                @foreach($notifications as $notification)
                    <li class="p-3 bg-gray-50 rounded-lg border-l-4 border-green-500">
                        <div class="text-sm text-gray-900">{{ $notification->message }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 014 6v10a4 4 0 004 4h10a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.83 1.17z"></path>
                </svg>
                <p>No notifications yet</p>
            </div>
        @endif
    </div>
</div>
<div class="bg-white rounded-2xl shadow-lg p-6 md:col-span-2" aria-label="Task Status Overview">
    <div class="flex items-center mb-2">
        <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 rounded-full mr-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h-1v-4h-1m4 0h-1v-4h-1"/></svg>
        </span>
        <h2 class="text-lg font-semibold text-gray-900">Task Status Overview</h2>
    </div>
    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $pendingCount = \App\Models\Task::where('assigned_to', auth()->id())->where('status', 'pending')->count();
            $inProgressCount = \App\Models\Task::where('assigned_to', auth()->id())->where('status', 'in_progress')->count();
            $completedCount = \App\Models\Task::where('assigned_to', auth()->id())->where('status', 'completed')->count();
            $overdueCount = \App\Models\Task::where('assigned_to', auth()->id())
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();
        @endphp
        <div class="bg-yellow-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-700 mb-1">{{ $pendingCount }}</div>
            <div class="text-sm text-gray-700">Pending</div>
        </div>
        <div class="bg-blue-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-700 mb-1">{{ $inProgressCount }}</div>
            <div class="text-sm text-gray-700">In Progress</div>
        </div>
        <div class="bg-green-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-700 mb-1">{{ $completedCount }}</div>
            <div class="text-sm text-gray-700">Completed</div>
        </div>
        <div class="bg-red-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-700 mb-1">{{ $overdueCount }}</div>
            <div class="text-sm text-gray-700">Overdue</div>
        </div>
    </div>
</div>
@endsection 