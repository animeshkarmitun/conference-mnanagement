@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(isset($error))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <p class="font-medium">{{ $error }}</p>
            </div>
        @endif

        <!-- Welcome Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Welcome, {{ $user->first_name }} {{ $user->last_name }}!
                    </h1>
                    <p class="text-gray-600 mt-1">
                        @if($roles->isNotEmpty())
                            Role: 
                            @foreach($roles as $role)
                                <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full mr-1">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-red-600">No role assigned</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Dashboard</p>
                    <p class="text-lg font-semibold text-gray-900">{{ now()->format('F j, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        @if(isset($stats) && !empty($stats))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            @if(isset($stats['conferences']))
            <div class="stat-card bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Conferences</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['conferences'] }}</p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($stats['participants']))
            <div class="stat-card bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Participants</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['participants'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($stats['sessions']))
            <div class="stat-card bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sessions</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['sessions'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($stats['total']))
            <div class="stat-card bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tasks</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                        @if(isset($stats['pending']))
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['pending'] }} pending</p>
                        @endif
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($stats['upcoming_sessions']))
            <div class="stat-card bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Upcoming Sessions</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['upcoming_sessions'] }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($stats['unread_notifications']))
            <div class="stat-card bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Unread Notifications</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['unread_notifications'] }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Conferences Section -->
            @if($user->hasPermission('conferences.view') && isset($conferences))
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Conferences</h2>
                    @if($user->hasPermission('conferences.create'))
                    <a href="{{ route('conferences.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">+ New Conference</a>
                    @endif
                </div>
                @if($conferences->isNotEmpty())
                    @if(isset($selectedConferenceId))
                    <form method="GET" action="{{ route('role-dashboard') }}" class="mb-4">
                        <select name="conference_id" onchange="this.form.submit()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($conferences as $conf)
                            <option value="{{ $conf->id }}" {{ $conf->id == $selectedConferenceId ? 'selected' : '' }}>
                                {{ $conf->name }} - {{ $conf->start_date ? (\Carbon\Carbon::parse($conf->start_date)->format('M d, Y')) : 'TBD' }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                    <div class="space-y-3">
                        @foreach($conferences->take(5) as $conference)
                        <div class="border-b border-gray-200 pb-3 last:border-b-0">
                            <h3 class="font-medium text-gray-900">{{ $conference->name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $conference->start_date ? (\Carbon\Carbon::parse($conference->start_date)->format('M d, Y')) : 'TBD' }}
                                @if($conference->end_date)
                                    - {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                @endif
                            </p>
                            @if($user->hasPermission('conferences.view'))
                            <a href="{{ route('conferences.show', $conference) }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-1 inline-block">View Details →</a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No conferences available.</p>
                @endif
            </div>
            @endif

            <!-- Recent Participants -->
            @if($user->hasPermission('participants.view') && isset($recent_participants))
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Participants</h2>
                    @if($user->hasPermission('participants.create'))
                    <a href="{{ route('participants.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">+ New Participant</a>
                    @endif
                </div>
                @if($recent_participants->isNotEmpty())
                <div class="space-y-3">
                    @foreach($recent_participants as $participant)
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3 last:border-b-0">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $participant->user->first_name }} {{ $participant->user->last_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $participant->participantType->name ?? 'Participant' }}</p>
                        </div>
                        @if($user->hasPermission('participants.view'))
                        <a href="{{ route('participants.show', $participant) }}" class="text-sm text-indigo-600 hover:text-indigo-800">View →</a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-gray-500">No participants yet.</p>
                @endif
            </div>
            @endif

            <!-- Recent Sessions -->
            @if($user->hasPermission('sessions.view') && isset($recent_sessions))
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Sessions</h2>
                    @if($user->hasPermission('sessions.create'))
                    <a href="{{ route('sessions.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">+ New Session</a>
                    @endif
                </div>
                @if($recent_sessions->isNotEmpty())
                <div class="space-y-3">
                    @foreach($recent_sessions->take(5) as $session)
                    <div class="border-b border-gray-200 pb-3 last:border-b-0">
                        <h3 class="font-medium text-gray-900">{{ $session->title }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $session->start_time ? $session->start_time->format('M d, Y g:i A') : 'TBD' }}
                        </p>
                        @if($user->hasPermission('sessions.view'))
                        <a href="{{ route('sessions.show', $session) }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-1 inline-block">View Details →</a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-gray-500">No sessions yet.</p>
                @endif
            </div>
            @endif

            <!-- Upcoming Sessions -->
            @if($user->hasPermission('sessions.view') && isset($upcoming_sessions) && $upcoming_sessions->isNotEmpty())
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Sessions</h2>
                <div class="space-y-3">
                    @foreach($upcoming_sessions as $session)
                    <div class="border-b border-gray-200 pb-3 last:border-b-0">
                        <h3 class="font-medium text-gray-900">{{ $session->title }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $session->start_time->format('M d, Y g:i A') }}
                            @if($session->venue)
                            - {{ $session->venue->name }}
                            @endif
                        </p>
                        @if($user->hasPermission('sessions.view'))
                        <a href="{{ route('sessions.show', $session) }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-1 inline-block">View Details →</a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Tasks -->
            @if($user->hasPermission('tasks.view') && isset($recent_tasks))
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Tasks</h2>
                    @if($user->hasPermission('tasks.create'))
                    <a href="{{ route('tasks.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">+ New Task</a>
                    @endif
                </div>
                @if($recent_tasks->isNotEmpty())
                <div class="space-y-3">
                    @foreach($recent_tasks->take(5) as $task)
                    <div class="border-b border-gray-200 pb-3 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $task->title }}</h3>
                                <p class="text-sm text-gray-600">
                                    Status: <span class="inline-block px-2 py-1 text-xs rounded-full 
                                        @if($task->status === 'completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </p>
                            </div>
                            @if($user->hasPermission('tasks.view'))
                            <a href="{{ route('tasks.show', $task) }}" class="text-sm text-indigo-600 hover:text-indigo-800">View →</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-gray-500">No tasks yet.</p>
                @endif
            </div>
            @endif

            <!-- Recent Notifications -->
            @if($user->hasPermission('notifications.view') && isset($recent_notifications))
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Notifications</h2>
                @if($recent_notifications->isNotEmpty())
                <div class="space-y-3">
                    @foreach($recent_notifications as $notification)
                    <div class="border-b border-gray-200 pb-3 last:border-b-0">
                        <p class="text-sm text-gray-900">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-gray-500">No notifications yet.</p>
                @endif
            </div>
            @endif
        </div>

        <!-- Empty State -->
        @if(!isset($stats) || empty($stats))
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No data available</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first conference or assigning permissions.</p>
        </div>
        @endif
    </div>
</div>
@endsection

