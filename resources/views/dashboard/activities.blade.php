@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">All Activities</h1>
                        <p class="text-gray-600 mt-2">View all recent activities across conferences</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conference Selector -->
        @if($conferences->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('dashboard.activities') }}" class="flex items-center space-x-4">
                    <label for="conference_id" class="text-sm font-medium text-gray-700">Select Conference:</label>
                    <select name="conference_id" id="conference_id" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}" {{ $selectedConferenceId == $conference->id ? 'selected' : '' }}>
                                {{ $conference->name }} ({{ $conference->start_date ? \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') : 'No date' }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        @endif

        @if(isset($noConferences) && $noConferences)
            <!-- No Conferences Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No conferences found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new conference.</p>
                    <div class="mt-6">
                        <a href="{{ route('conferences.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Conference
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Activities List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($activities->count() > 0)
                        <div class="space-y-4">
                            @foreach($activities as $activity)
                                <div class="activity-item flex items-start gap-4 p-4 rounded-lg border border-gray-100 hover:border-yellow-200 transition-colors duration-200">
                                    <div class="flex-shrink-0">
                                        @if($activity['type'] === 'participant')
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @elseif($activity['type'] === 'task')
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $activity['type'] === 'participant' ? 'bg-blue-100 text-blue-800' : ($activity['type'] === 'task' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($activity['type']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- No Activities Message -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No activities found</h3>
                            <p class="mt-1 text-sm text-gray-500">There are no activities for the selected conference yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
