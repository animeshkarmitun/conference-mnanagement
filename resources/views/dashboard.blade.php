@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Conferences</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalConferences ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Participants</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalParticipants ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Sessions</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $upcomingSessions ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Tasks</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $pendingTasks ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h2>
        <div class="space-y-4">
            @forelse($recentActivities ?? [] as $activity)
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="p-2 rounded-full bg-gray-100">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $activity->description ?? '' }}</p>
                        <p class="text-sm text-gray-500">{{ $activity->created_at ?? '' }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No recent activities.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events</h2>
        <div class="space-y-4">
            @forelse($upcomingEvents ?? [] as $event)
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="p-2 rounded-full bg-yellow-100">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $event->title ?? '' }}</p>
                        <p class="text-sm text-gray-500">{{ $event->start_time ?? '' }}</p>
                        <p class="text-sm text-gray-500">{{ $event->venue->name ?? '' }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No upcoming events.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
