@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Professional Dashboard Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V6a2 2 0 012-2h14a2 2 0 012 2v7.5M3 13.5l9 6 9-6M3 13.5l9-6 9 6"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Dashboard</h1>
        <div class="text-gray-600 text-lg font-medium">Conference Management Overview</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">

@if(isset($noConferences) && $noConferences)
    <!-- No Conferences Message -->
    <div class="max-w-4xl mx-auto text-center py-12">
        <div class="bg-white rounded-2xl shadow p-8">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-700 mb-2">No Conferences Found</h2>
            <p class="text-gray-500 mb-6">There are no conferences available to display. Please create a conference first.</p>
            <a href="{{ route('conferences.create') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Conference
            </a>
        </div>
    </div>
@else
    <!-- Conference Selection Section -->
    <div class="max-w-4xl mx-auto mb-8">
        <form method="GET" action="" class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="w-full md:w-1/2">
                <label for="conference_id" class="block text-sm font-medium text-gray-700 mb-1">Select Conference</label>
                <select name="conference_id" id="conference_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" onchange="this.form.submit()">
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}" {{ $selectedConferenceId == $conference->id ? 'selected' : '' }}>
                            {{ $conference->name }} ({{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/2 text-right">
                @if($dashboardData && $dashboardData['conference_progress']['conference'])
                    <span class="inline-block bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold">
                        {{ $dashboardData['conference_progress']['conference']->name }}
                    </span>
                @endif
            </div>
        </form>
    </div>

    @if($dashboardData)
        <!-- Conference Progress Section -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-white rounded-2xl shadow flex flex-col md:flex-row items-center justify-between p-6 border-l-4 border-yellow-400">
                <div class="flex-1 flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full">
                            <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <div class="text-lg font-bold text-yellow-700">Conference Progress</div>
                            <div class="text-sm text-gray-500">{{ $dashboardData['conference_progress']['conference']->name }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col md:ml-8">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg>
                            <span>{{ \Carbon\Carbon::parse($dashboardData['conference_progress']['start_date'])->format('M d') }} – {{ \Carbon\Carbon::parse($dashboardData['conference_progress']['end_date'])->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 mt-1">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
                            <span>Sessions Completed: <span class="font-semibold text-green-700">{{ $dashboardData['conference_progress']['completed_sessions'] }}</span> / {{ $dashboardData['conference_progress']['total_sessions'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end mt-4 md:mt-0 md:ml-8 min-w-[180px]">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm text-gray-500">Progress:</span>
                        <span class="text-lg font-bold text-yellow-700">{{ $dashboardData['conference_progress']['progress_percentage'] }}%</span>
                    </div>
                    <div class="w-40 h-3 bg-gray-200 rounded-full overflow-hidden mb-1">
                        <div class="h-3 bg-yellow-400 rounded-full" style="width: {{ $dashboardData['conference_progress']['progress_percentage'] }}%"></div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        @if($dashboardData['conference_progress']['days_remaining'] > 0)
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                            <span>{{ $dashboardData['conference_progress']['days_remaining'] }} days remaining</span>
                        @else
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Conference completed</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Progress Section -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-white rounded-2xl shadow flex flex-col md:flex-row items-center justify-between p-6 border-l-4 border-green-400">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <div class="text-lg font-bold text-green-700">Task Progress</div>
                        <div class="text-sm text-gray-500">Completed Tasks</div>
                    </div>
                </div>
                <div class="flex flex-col items-end mt-4 md:mt-0 md:ml-8 min-w-[180px]">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm text-gray-500">{{ $dashboardData['task_progress']['completed_tasks'] }} / {{ $dashboardData['task_progress']['total_tasks'] }}</span>
                        <span class="text-lg font-bold text-green-700">{{ $dashboardData['task_progress']['progress_percentage'] }}%</span>
                    </div>
                    <div class="w-40 h-3 bg-gray-200 rounded-full overflow-hidden mb-1">
                        <div class="h-3 bg-green-400 rounded-full" style="width: {{ $dashboardData['task_progress']['progress_percentage'] }}%"></div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4"/></svg>
                        <span>{{ $dashboardData['task_progress']['remaining_tasks'] }} tasks remaining</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats Section -->
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
                <!-- Invited -->
                <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-blue-400">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full mb-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <div class="text-2xl font-bold text-blue-700">{{ $dashboardData['summary_stats']['invited'] }}</div>
                    <div class="text-sm text-gray-500">Invited</div>
                </div>
                <!-- Accepted -->
                <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-green-400">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 rounded-full mb-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div class="text-2xl font-bold text-green-700">{{ $dashboardData['summary_stats']['accepted'] }}</div>
                    <div class="text-sm text-gray-500">Accepted</div>
                </div>
                <!-- Flying -->
                <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-yellow-400">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-full mb-2">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2a5 5 0 00-2 4v5a2 2 0 002 2h10a2 2 0 002-2v-5a5 5 0 00-2-4z"/></svg>
                    </span>
                    <div class="text-2xl font-bold text-yellow-700">{{ $dashboardData['summary_stats']['flying'] }}</div>
                    <div class="text-sm text-gray-500">Flying</div>
                </div>
                <!-- Status Breakdown -->
                <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-pink-400">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-pink-100 rounded-full mb-2">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01"/></svg>
                    </span>
                    <div class="flex flex-col items-center">
                        <div class="text-xs text-gray-500">Pending: <span class="font-bold text-pink-700">{{ $dashboardData['summary_stats']['status_breakdown']['pending'] }}</span></div>
                        <div class="text-xs text-gray-500">Approved: <span class="font-bold text-green-700">{{ $dashboardData['summary_stats']['status_breakdown']['approved'] }}</span></div>
                        <div class="text-xs text-gray-500">Declined: <span class="font-bold text-red-700">{{ $dashboardData['summary_stats']['status_breakdown']['declined'] }}</span></div>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Status</div>
                </div>
                <!-- Speakers -->
                <div class="bg-white rounded-2xl shadow flex flex-col items-center p-5 border-t-4 border-purple-400">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 rounded-full mb-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    </span>
                    <div class="text-2xl font-bold text-purple-700">{{ $dashboardData['speaker_statistics']['total_speakers'] }}</div>
                    <div class="text-sm text-gray-500">Speakers</div>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
