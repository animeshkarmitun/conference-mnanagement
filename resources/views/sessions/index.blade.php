@extends('layouts.app')

@section('title', 'Sessions')

@push('styles')
<style>
    .session-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .session-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
    
    .tab-link {
        transition: all 0.2s ease-in-out;
    }
    
    .tab-link:hover {
        transform: translateY(-1px);
    }
    
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .session-icon {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        font-weight: bold;
    }
    
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }
    
    .stat-card:hover::before {
        left: 100%;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .animate-delay-1 { animation-delay: 0.1s; }
    .animate-delay-2 { animation-delay: 0.2s; }
    .animate-delay-3 { animation-delay: 0.3s; }
    .animate-delay-4 { animation-delay: 0.4s; }
    
    .sortable-header {
        user-select: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .sortable-header:hover {
        background-color: #f1f5f9;
    }
    
    .sort-icon {
        transition: all 0.2s ease;
    }
    
    .sort-icon.asc {
        transform: rotate(180deg);
    }
    
    /* Modern color scheme overrides */
    .modern-primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
    }
    
    .modern-primary:hover {
        background: linear-gradient(135deg, #5855eb, #7c3aed);
    }
    
    .modern-secondary {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }
    
    .modern-secondary:hover {
        background: linear-gradient(135deg, #475569, #334155);
    }
    
    .modern-success {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }
    
    .modern-success:hover {
        background: linear-gradient(135deg, #047857, #065f46);
    }
    
    .modern-warning {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: white;
    }
    
    .modern-warning:hover {
        background: linear-gradient(135deg, #be123c, #9f1239);
    }
    
    .modern-info {
        background: linear-gradient(135deg, #0891b2, #0e7490);
        color: white;
    }
    
    .modern-info:hover {
        background: linear-gradient(135deg, #0e7490, #155e75);
    }
    
    .modern-admin {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
    }
    
    .modern-admin:hover {
        background: linear-gradient(135deg, #6d28d9, #5b21b6);
    }
</style>
@endpush

@section('content')
<!-- Enhanced Header Section -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-slate-100 animate-fade-in-up">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h2 class="text-3xl font-bold text-slate-800">Sessions</h2>
            <p class="text-slate-600 mt-1">Manage conference sessions and schedules</p>
        </div>
        
        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <button class="quick-action-btn modern-info p-3 rounded-full shadow-lg transition-all duration-200" title="Import Sessions">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
            </button>
            
            <button class="quick-action-btn modern-success p-3 rounded-full shadow-lg transition-all duration-200" title="Generate Reports">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </button>
            
            <button class="quick-action-btn modern-admin p-3 rounded-full shadow-lg transition-all duration-200" title="Export Data">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </button>
            
            <button class="quick-action-btn modern-warning p-3 rounded-full shadow-lg transition-all duration-200" title="Bulk Operations">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
            
            <a href="{{ route('sessions.create') }}" class="modern-primary px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Session
            </a>
        </div>
    </div>
</div>



<!-- Enhanced Session Status Tabs -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-gray-100 animate-fade-in-up animate-delay-2">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('sessions.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'all' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    All Sessions
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $sessionCounts['all'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('sessions.index', ['status' => 'active']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'active' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Active Sessions
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $sessionCounts['active'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('sessions.index', ['status' => 'upcoming']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'upcoming' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Upcoming Sessions
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $sessionCounts['upcoming'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('sessions.index', ['status' => 'finished']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'finished' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Finished Sessions
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $sessionCounts['finished'] }}</span>
                </div>
            </a>
        </nav>
    </div>
</div>

<!-- Conference Filter -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-gray-100 animate-fade-in-up animate-delay-3">
    <div class="p-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <label for="conference_search" class="text-sm font-medium text-gray-700">Filter by Conference:</label>
                </div>
                <div class="relative">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="conference_search" 
                            placeholder="Search conferences..." 
                            class="w-64 rounded-lg border-gray-300 text-sm focus:ring-yellow-500 focus:border-yellow-500 pr-16"
                            autocomplete="off"
                        >
                        <button 
                            type="button" 
                            id="clear_conference" 
                            class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                            title="Clear selection"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            id="dropdown_toggle" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            title="Show all conferences"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="conference_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                        <div id="conference_options" class="py-1">
                            <!-- Options will be populated by JavaScript -->
                        </div>
                    </div>
                    <input type="hidden" id="conference_id" name="conference_id" value="{{ request('conference_id') }}">
                </div>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ $sessions->total() }} session{{ $sessions->total() !== 1 ? 's' : '' }} found</span>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Session Table -->
<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 animate-fade-in-up animate-delay-3">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="sessionsTable">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="status">
                        Status
                        <svg class="w-4 h-4 inline sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="title">
                        Title
                        <svg class="w-4 h-4 inline sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="conference">
                        Conference
                        <svg class="w-4 h-4 inline sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="schedule">
                        Schedule
                        <svg class="w-4 h-4 inline sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="duration">
                        Duration
                        <svg class="w-4 h-4 inline sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sessions as $session)
                    @php
                        $timeData = \App\Helpers\DateHelper::formatSessionTime($session->start_time, $session->end_time);
                        $statusClass = \App\Helpers\DateHelper::getStatusColorClass($timeData['is_active'], $timeData['is_past'], $timeData['is_today']);
                        $durationClass = \App\Helpers\DateHelper::getDurationColorClass($timeData['duration_minutes']);
                        
                        if ($timeData['is_active']) {
                            $statusText = 'Active';
                        } elseif ($timeData['is_past']) {
                            $statusText = 'Finished';
                        } else {
                            $statusText = 'Upcoming';
                        }
                    @endphp
                    
                    <tr class="table-row-hover hover:bg-yellow-50 transition-all duration-200 border-b border-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap" 
                            data-sort-value="{{ $statusText }}" 
                            data-sort-priority="{{ $timeData['is_active'] ? 1 : ($timeData['is_today'] ? 2 : ($timeData['is_past'] ? 3 : 4)) }}">
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $statusClass }}">
                                @if($timeData['is_active'])
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M13 10V3L4 14h7v7l9-11h-7z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($timeData['is_today'])
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif(!$timeData['is_past'])
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 13l4 4L19 7" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $session->title }}">
                            <div class="flex items-center">
                                <div class="w-10 h-10 session-icon rounded-full flex items-center justify-center mr-3 shadow-lg">
                                    <span class="text-sm font-bold">
                                        {{ substr($session->title, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $session->title }}</div>
                                    @if($session->description)
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $session->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500" data-sort-value="{{ $session->conference->name ?? 'N/A' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>{{ $session->conference->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $session->start_time }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-900 font-medium">{{ $timeData['time_string'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $timeData['status_info'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $timeData['duration_minutes'] }}">
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $durationClass }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $timeData['duration'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('sessions.show', $session) }}" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800 rounded-lg transition-all duration-200 border border-blue-200 shadow-sm"
                                   title="View Session Details"
                                   aria-label="View session details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('sessions.edit', $session) }}" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 rounded-lg transition-all duration-200 border border-yellow-200 shadow-sm"
                                   title="Edit Session"
                                   aria-label="Edit session">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this session?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="quick-action-btn inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-all duration-200 border border-red-200 shadow-sm"
                                            title="Delete Session"
                                            aria-label="Delete session">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-gray-500 mb-2">
                                    @if($status === 'active')
                                        No active sessions at the moment.
                                    @elseif($status === 'upcoming')
                                        No upcoming sessions scheduled.
                                    @elseif($status === 'finished')
                                        No finished sessions found.
                                    @else
                                        No sessions found.
                                    @endif
                                </p>
                                <a href="{{ route('sessions.create') }}" class="text-yellow-600 hover:text-yellow-700 font-medium">Create your first session</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $sessions->appends(['status' => $status, 'conference_id' => request('conference_id')])->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('sessionsTable');
    const tbody = table.querySelector('tbody');
    const headers = table.querySelectorAll('.sortable-header');
    
    let currentSort = {
        column: null,
        direction: 'asc'
    };
    
    // Add click event listeners to all sortable headers
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sort');
            console.log('Sorting sessions by column:', column);
            sortTable(column);
        });
    });
    
    console.log('Found', headers.length, 'sortable headers for sessions');
    console.log('Found', tbody.querySelectorAll('tr').length, 'session table rows');
    
    function sortTable(column) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Filter out empty rows (like the "no sessions" message)
        const dataRows = rows.filter(row => row.cells.length > 1);
        
        if (dataRows.length === 0) return;
        
        // Determine sort direction
        let direction = 'asc';
        if (currentSort.column === column) {
            direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        }
        
        // Update current sort state
        currentSort.column = column;
        currentSort.direction = direction;
        
        // Update visual indicators
        updateSortIndicators(column, direction);
        
        // Sort the rows
        dataRows.sort((a, b) => {
            const aValue = getCellValue(a, column);
            const bValue = getCellValue(b, column);
            
            let comparison = 0;
            
            if (column === 'status') {
                // Sort by status priority (Active=1, Today=2, Upcoming=3, Finished=4)
                const aPriority = parseInt(a.cells[0].getAttribute('data-sort-priority'));
                const bPriority = parseInt(b.cells[0].getAttribute('data-sort-priority'));
                comparison = aPriority - bPriority;
            } else if (column === 'duration') {
                // Sort by duration minutes (numeric)
                comparison = parseInt(aValue) - parseInt(bValue);
            } else if (column === 'capacity') {
                // Sort by capacity (numeric)
                comparison = parseInt(aValue) - parseInt(bValue);
            } else if (column === 'schedule') {
                // Sort by start date
                comparison = new Date(aValue) - new Date(bValue);
            } else {
                // Sort alphabetically for title, conference, and room
                comparison = aValue.localeCompare(bValue);
            }
            
            return direction === 'asc' ? comparison : -comparison;
        });
        
        // Re-append sorted rows
        dataRows.forEach(row => tbody.appendChild(row));
    }
    
    function getCellValue(row, column) {
        // Get the cell in the specific column (0-indexed)
        const columnIndex = getColumnIndex(column);
        const cell = row.cells[columnIndex];
        
        if (!cell) return '';
        
        if (column === 'status') {
            return parseInt(cell.getAttribute('data-sort-priority'));
        }
        
        return cell.getAttribute('data-sort-value');
    }
    
    function getColumnIndex(column) {
        const columnMap = {
            'status': 0,
            'title': 1,
            'conference': 2,
            'schedule': 3,
            'duration': 4,
            'room': 5,
            'capacity': 6
        };
        return columnMap[column] || 0;
    }
    
    function updateSortIndicators(activeColumn, direction) {
        // Reset all sort icons
        headers.forEach(header => {
            const icon = header.querySelector('.sort-icon');
            icon.classList.remove('active', 'asc', 'desc');
            icon.style.color = '#9ca3af'; // gray-400
        });
        
        // Update active column icon
        const activeHeader = table.querySelector(`[data-sort="${activeColumn}"]`);
        if (activeHeader) {
            const icon = activeHeader.querySelector('.sort-icon');
            icon.classList.add('active', direction);
            icon.style.color = '#f59e0b'; // yellow-500
        }
    }

    // Conference search functionality
    const conferences = @json($conferences ?? []);
    const conferenceSearch = document.getElementById('conference_search');
    const conferenceDropdown = document.getElementById('conference_dropdown');
    const conferenceOptions = document.getElementById('conference_options');
    const conferenceIdInput = document.getElementById('conference_id');
    const clearConferenceBtn = document.getElementById('clear_conference');
    const dropdownToggle = document.getElementById('dropdown_toggle');
    
    let selectedConference = null;
    let filteredConferences = [];
    let isDropdownOpen = false;
    
    // Initialize with current selection
    const currentConferenceId = conferenceIdInput.value;
    if (currentConferenceId) {
        const currentConference = conferences.find(c => c.id == currentConferenceId);
        if (currentConference) {
            selectedConference = currentConference;
            conferenceSearch.value = currentConference.name;
            clearConferenceBtn.classList.remove('hidden');
        }
    }
    
    function filterConferences(query) {
        if (!query.trim()) {
            return conferences;
        }
        const lowerQuery = query.toLowerCase();
        return conferences.filter(conference => 
            conference.name.toLowerCase().includes(lowerQuery)
        );
    }
    
    function renderConferenceOptions(conferences) {
        conferenceOptions.innerHTML = '';
        
        if (conferences.length === 0) {
            conferenceOptions.innerHTML = `
                <div class="px-4 py-2 text-sm text-gray-500">
                    No conferences found
                </div>
            `;
            return;
        }
        
        conferences.forEach(conference => {
            const option = document.createElement('div');
            option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-yellow-50 transition-colors duration-150';
            option.textContent = conference.name;
            option.dataset.id = conference.id;
            option.dataset.name = conference.name;
            
            option.addEventListener('click', () => {
                selectConference(conference);
            });
            
            conferenceOptions.appendChild(option);
        });
    }
    
    function selectConference(conference) {
        selectedConference = conference;
        conferenceSearch.value = conference.name;
        conferenceIdInput.value = conference.id;
        conferenceDropdown.classList.add('hidden');
        isDropdownOpen = false;
        clearConferenceBtn.classList.remove('hidden');
        
        // Update URL and reload
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('conference_id', conference.id);
        
        // Preserve existing status parameter
        const statusParam = currentUrl.searchParams.get('status');
        if (statusParam) {
            currentUrl.searchParams.set('status', statusParam);
        }
        
        window.location.href = currentUrl.toString();
    }
    
    function clearConferenceSelection() {
        selectedConference = null;
        conferenceSearch.value = '';
        conferenceIdInput.value = '';
        conferenceDropdown.classList.add('hidden');
        isDropdownOpen = false;
        clearConferenceBtn.classList.add('hidden');
        
        // Update URL and reload
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('conference_id');
        
        // Preserve existing status parameter
        const statusParam = currentUrl.searchParams.get('status');
        if (statusParam) {
            currentUrl.searchParams.set('status', statusParam);
        }
        
        window.location.href = currentUrl.toString();
    }
    
    function toggleDropdown() {
        if (isDropdownOpen) {
            conferenceDropdown.classList.add('hidden');
            isDropdownOpen = false;
        } else {
            const query = conferenceSearch.value.trim();
            if (query) {
                // Show filtered results
                filteredConferences = filterConferences(query);
                renderConferenceOptions(filteredConferences);
            } else {
                // Show all conferences
                renderConferenceOptions(conferences);
            }
            conferenceDropdown.classList.remove('hidden');
            isDropdownOpen = true;
        }
    }
    
    // Event listeners
    if (conferenceSearch) {
        conferenceSearch.addEventListener('input', function() {
            const query = this.value;
            filteredConferences = filterConferences(query);
            
            if (query.trim() && filteredConferences.length > 0) {
                renderConferenceOptions(filteredConferences);
                conferenceDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            } else if (query.trim() && filteredConferences.length === 0) {
                renderConferenceOptions([]);
                conferenceDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            } else {
                conferenceDropdown.classList.add('hidden');
                isDropdownOpen = false;
            }
        });
        
        conferenceSearch.addEventListener('focus', function() {
            if (this.value.trim()) {
                filteredConferences = filterConferences(this.value);
                renderConferenceOptions(filteredConferences);
                conferenceDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            }
        });
        
        conferenceSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                conferenceDropdown.classList.add('hidden');
                isDropdownOpen = false;
                this.blur();
            }
        });
    }
    
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown();
        });
    }
    
    if (clearConferenceBtn) {
        clearConferenceBtn.addEventListener('click', clearConferenceSelection);
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#conference_search') && !e.target.closest('#conference_dropdown') && !e.target.closest('#dropdown_toggle')) {
            conferenceDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });
});
</script>
@endsection 