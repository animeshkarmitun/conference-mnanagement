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
    
    /* Session title link styling */
    .session-title-link {
        transition: all 0.2s ease;
        position: relative;
    }
    
    .session-title-link:hover {
        transform: translateY(-1px);
    }
    
    .session-title-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        transition: width 0.3s ease;
    }
    
    .session-title-link:hover::after {
        width: 100%;
    }
    
    /* Conference dropdown z-index fix - ensure it appears above everything */
    #conference_dropdown {
        z-index: 99999 !important;
        position: fixed !important;
    }
    
    /* Ensure parent container doesn't clip dropdown */
    .relative.flex-1.min-w-0.z-50 {
        z-index: 50;
        position: relative;
    }
    
    /* Make sure filter container allows overflow */
    .bg-white.rounded-2xl.shadow-lg.mb-6.border {
        position: relative;
        z-index: 1;
        overflow: visible !important;
    }
    
    /* Ensure overflow containers don't clip dropdown */
    .w-full.overflow-x-auto {
        overflow-y: visible !important;
        position: relative;
    }
    
    /* Grid container should allow overflow */
    .grid.grid-cols-1 {
        overflow: visible !important;
    }
    
    /* Table container should have lower z-index */
    #sessionsTable {
        position: relative;
        z-index: 0;
    }
    
    /* Custom scrollbar for conference dropdown */
    #conference_dropdown::-webkit-scrollbar {
        width: 8px;
    }
    
    #conference_dropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    #conference_dropdown::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    #conference_dropdown::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
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
            <button id="export-sessions-btn" class="modern-success px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
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

<!-- Conference and Session Title Filters -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-gray-100 animate-fade-in-up animate-delay-3" style="position: relative; z-index: 1; overflow: visible;">
    <div class="p-4 border-b border-gray-200" style="overflow: visible;">
        <div class="w-full overflow-x-auto" style="overflow-y: visible !important; position: relative;">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-center min-w-0" style="overflow: visible;">
                <!-- Conference Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 min-w-0">
                    <label for="conference_search" class="text-sm text-gray-600 whitespace-nowrap flex-shrink-0 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Conference:
                    </label>
                    <div class="relative flex-1 min-w-0 z-50">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="conference_search" 
                                placeholder="Search conferences..." 
                                class="w-full min-w-0 rounded-lg border-gray-300 text-sm focus:ring-yellow-500 focus:border-yellow-500 pr-16 px-3 py-2"
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
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 z-10"
                                title="Show all conferences"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="conference_dropdown" class="absolute z-[99999] w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-2xl max-h-60 overflow-y-auto hidden" style="position: fixed; display: none;">
                            <div id="conference_options" class="py-1">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                        <input type="hidden" id="conference_id" name="conference_id" value="{{ request('conference_id') }}">
                    </div>
                </div>
                
                <!-- Session Title Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 min-w-0">
                    <label for="session_title_search" class="text-sm text-gray-600 whitespace-nowrap flex-shrink-0 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Session Title:
                    </label>
                    <div class="relative flex-1 min-w-0">
                        <input 
                            type="text" 
                            id="session_title_search" 
                            placeholder="Search session titles..." 
                            value="{{ request('session_title') }}"
                            class="w-full min-w-0 rounded-lg border-gray-300 text-sm focus:ring-yellow-500 focus:border-yellow-500 pr-8 px-3 py-2"
                            autocomplete="off"
                        >
                        <button 
                            type="button" 
                            id="clear_session_title" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 {{ request('session_title') ? '' : 'hidden' }}"
                            title="Clear session title search"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 min-w-0">
                    <label for="status_filter" class="text-sm text-gray-600 whitespace-nowrap flex-shrink-0 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Status:
                    </label>
                    <div class="relative flex-1 min-w-0">
                        <select 
                            id="status_filter" 
                            class="w-full min-w-0 rounded-lg border-gray-300 text-sm focus:ring-yellow-500 focus:border-yellow-500 px-3 py-2"
                        >
                            <option value="">All Statuses</option>
                            <option value="published" {{ request('session_status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('session_status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                </div>

                <!-- Session Count -->
                <div class="flex items-center gap-2 text-sm text-gray-500 min-w-0">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="whitespace-nowrap">{{ $sessions->total() }} session{{ $sessions->total() !== 1 ? 's' : '' }} found</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Session Table -->
<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 animate-fade-in-up animate-delay-3">
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200" id="sessionsTable">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="title">
                        Title
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="participants">
                        Participants
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="room">
                        Room
                        <svg class="w-4 h-4 inline sort-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email Tracking
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header" data-sort="status">
                        Status
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
                        <!-- Title -->
                        <td class="px-6 py-4" data-sort-value="{{ $session->title }}">
                            <div class="flex items-center">
                                <div class="w-10 h-10 session-icon rounded-full flex items-center justify-center mr-3 shadow-lg">
                                    <span class="text-sm font-bold">
                                        {{ substr($session->title, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        <a href="{{ route('sessions.edit', $session) }}" 
                                           class="session-title-link text-blue-600 hover:text-blue-800"
                                           title="Edit session: {{ $session->title }}">
                                            {{ $session->title }}
                                        </a>
                                    </div>
                                    @if($session->description)
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $session->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <!-- Schedule -->
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
                        <!-- Participants -->
                        <td class="px-6 py-4" data-sort-value="{{ $session->participants->map(function($p) { return ($p->user->first_name ?? '') . ' ' . ($p->user->last_name ?? ''); })->filter()->join(', ') }}">
                            @if($session->participants->count() > 0)
                                <div class="text-sm text-gray-900 max-w-xs">
                                    {{ $session->participants->map(function($p) { return trim(($p->user->first_name ?? '') . ' ' . ($p->user->last_name ?? '')); })->filter()->join(', ') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $session->participants->count() }} participant{{ $session->participants->count() !== 1 ? 's' : '' }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400">No participants</span>
                            @endif
                        </td>
                        <!-- Duration -->
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $timeData['duration_minutes'] }}">
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $durationClass }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $timeData['duration'] }}
                            </span>
                        </td>
                        <!-- Room -->
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $session->room ?? 'N/A' }}">
                            @if($session->room)
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span>{{ $session->room }}</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">N/A</span>
                            @endif
                        </td>
                        <!-- Email Tracking -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                // Get email tracking data for this session
                                $emailTracking = \App\Models\ParticipantSessionEmailTracking::where('session_id', $session->id)->get();
                                $totalEmailsSent = $emailTracking->sum('email_send_count');
                                $lastEmailSent = $emailTracking->max('last_email_sent_at');
                                $participantCount = $emailTracking->count();
                            @endphp
                            <div class="flex items-center space-x-2">
                                <div class="text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">{{ $totalEmailsSent }}</span>
                                        <span class="text-xs text-gray-500 ml-1">sent</span>
                                    </div>
                                    @if($lastEmailSent)
                                        <div class="text-xs text-gray-500">
                                            Last: {{ \Carbon\Carbon::parse($lastEmailSent)->format('M d, H:i') }}
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">Never sent</div>
                                    @endif
                                </div>
                                @if($participantCount > 0)
                                    <button onclick="resendSessionEmails({{ $session->id }})" 
                                            class="quick-action-btn inline-flex items-center p-1.5 bg-green-100 text-green-700 hover:bg-green-200 hover:text-green-800 rounded-lg transition-all duration-200 border border-green-200 shadow-sm"
                                            title="Resend emails to all participants"
                                            aria-label="Resend session emails">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <!-- Status -->
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
                        <!-- Actions -->
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
                        <td colspan="8" class="px-6 py-8 text-center">
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
        {{ $sessions->appends(['status' => $status, 'conference_id' => request('conference_id'), 'session_title' => request('session_title')])->links('pagination.custom') }}
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
                const columnIndex = getColumnIndex('status');
                const aPriority = parseInt(a.cells[columnIndex].getAttribute('data-sort-priority'));
                const bPriority = parseInt(b.cells[columnIndex].getAttribute('data-sort-priority'));
                comparison = aPriority - bPriority;
            } else if (column === 'duration') {
                // Sort by duration minutes (numeric)
                comparison = parseInt(aValue) - parseInt(bValue);
            } else if (column === 'schedule') {
                // Sort by start date
                comparison = new Date(aValue) - new Date(bValue);
            } else {
                // Sort alphabetically for title, participants, and room
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
            return parseInt(cell.getAttribute('data-sort-priority')) || 0;
        }
        
        return cell.getAttribute('data-sort-value') || '';
    }
    
    function getColumnIndex(column) {
        const columnMap = {
            'title': 0,
            'schedule': 1,
            'participants': 2,
            'duration': 3,
            'room': 4,
            'status': 6
        };
        return columnMap[column] !== undefined ? columnMap[column] : 0;
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
        conferenceDropdown.style.display = 'none';
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
        
        // Preserve existing session title parameter
        const sessionTitleParam = currentUrl.searchParams.get('session_title');
        if (sessionTitleParam) {
            currentUrl.searchParams.set('session_title', sessionTitleParam);
        }
        
        // Preserve existing session status parameter
        const sessionStatusParam = currentUrl.searchParams.get('session_status');
        if (sessionStatusParam) {
            currentUrl.searchParams.set('session_status', sessionStatusParam);
        }
        
        window.location.href = currentUrl.toString();
    }
    
    function clearConferenceSelection() {
        selectedConference = null;
        conferenceSearch.value = '';
        conferenceIdInput.value = '';
        conferenceDropdown.classList.add('hidden');
        conferenceDropdown.style.display = 'none';
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
        
        // Preserve existing session title parameter
        const sessionTitleParam = currentUrl.searchParams.get('session_title');
        if (sessionTitleParam) {
            currentUrl.searchParams.set('session_title', sessionTitleParam);
        }
        
        // Preserve existing session status parameter
        const sessionStatusParam = currentUrl.searchParams.get('session_status');
        if (sessionStatusParam) {
            currentUrl.searchParams.set('session_status', sessionStatusParam);
        }
        
        window.location.href = currentUrl.toString();
    }
    
    function positionDropdown() {
        if (!conferenceSearch || !conferenceDropdown) return;
        
        const inputRect = conferenceSearch.getBoundingClientRect();
        
        // Position dropdown below the input field using fixed positioning
        // Fixed positioning is relative to viewport, so use getBoundingClientRect() values directly
        conferenceDropdown.style.top = (inputRect.bottom + 4) + 'px';
        conferenceDropdown.style.left = inputRect.left + 'px';
        conferenceDropdown.style.width = inputRect.width + 'px';
    }
    
    function toggleDropdown() {
        if (isDropdownOpen) {
            conferenceDropdown.classList.add('hidden');
            conferenceDropdown.style.display = 'none';
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
            positionDropdown();
            conferenceDropdown.style.display = 'block';
            conferenceDropdown.classList.remove('hidden');
            isDropdownOpen = true;
        }
    }
    
    // Update dropdown position on scroll and resize
    function updateDropdownPosition() {
        if (isDropdownOpen && !conferenceDropdown.classList.contains('hidden')) {
            positionDropdown();
        }
    }
    
    window.addEventListener('scroll', updateDropdownPosition, true);
    window.addEventListener('resize', updateDropdownPosition);
    
    // Event listeners
    if (conferenceSearch) {
        conferenceSearch.addEventListener('input', function() {
            const query = this.value;
            filteredConferences = filterConferences(query);
            
            if (query.trim() && filteredConferences.length > 0) {
                renderConferenceOptions(filteredConferences);
                positionDropdown();
                conferenceDropdown.style.display = 'block';
                conferenceDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            } else if (query.trim() && filteredConferences.length === 0) {
                renderConferenceOptions([]);
                positionDropdown();
                conferenceDropdown.style.display = 'block';
                conferenceDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            } else {
                conferenceDropdown.classList.add('hidden');
                conferenceDropdown.style.display = 'none';
                isDropdownOpen = false;
            }
        });
        
        conferenceSearch.addEventListener('focus', function() {
            if (this.value.trim()) {
                filteredConferences = filterConferences(this.value);
                renderConferenceOptions(filteredConferences);
                positionDropdown();
                conferenceDropdown.style.display = 'block';
                conferenceDropdown.classList.remove('hidden');
                isDropdownOpen = true;
            }
        });
        
        conferenceSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                conferenceDropdown.classList.add('hidden');
                conferenceDropdown.style.display = 'none';
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
            conferenceDropdown.style.display = 'none';
            isDropdownOpen = false;
        }
    });

    // Status filter functionality
    const statusFilter = document.getElementById('status_filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const currentUrl = new URL(window.location);
            const sessionStatus = this.value;
            
            if (sessionStatus) {
                currentUrl.searchParams.set('session_status', sessionStatus);
            } else {
                currentUrl.searchParams.delete('session_status');
            }
            
            // Preserve conference filter if it exists
            const conferenceId = currentUrl.searchParams.get('conference_id');
            if (conferenceId) {
                currentUrl.searchParams.set('conference_id', conferenceId);
            }
            
            // Preserve session title filter if it exists
            const sessionTitle = currentUrl.searchParams.get('session_title');
            if (sessionTitle) {
                currentUrl.searchParams.set('session_title', sessionTitle);
            }
            
            window.location.href = currentUrl.toString();
        });
    }

    // Session title search functionality
    const sessionTitleSearch = document.getElementById('session_title_search');
    const clearSessionTitleBtn = document.getElementById('clear_session_title');
    
    if (sessionTitleSearch) {
        let searchTimeout;
        
        sessionTitleSearch.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Show/hide clear button
            if (query) {
                clearSessionTitleBtn.classList.remove('hidden');
            } else {
                clearSessionTitleBtn.classList.add('hidden');
            }
            
            // Debounce search to avoid too many requests
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSessionTitleSearch(query);
            }, 500); // 500ms delay
        });
        
        sessionTitleSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                performSessionTitleSearch(this.value.trim());
            } else if (e.key === 'Escape') {
                this.value = '';
                clearSessionTitleBtn.classList.add('hidden');
                clearTimeout(searchTimeout);
                performSessionTitleSearch('');
            }
        });
    }
    
    if (clearSessionTitleBtn) {
        clearSessionTitleBtn.addEventListener('click', function() {
            sessionTitleSearch.value = '';
            this.classList.add('hidden');
            performSessionTitleSearch('');
        });
    }
    
    function performSessionTitleSearch(query) {
        const currentUrl = new URL(window.location);
        
        if (query) {
            currentUrl.searchParams.set('session_title', query);
        } else {
            currentUrl.searchParams.delete('session_title');
        }
        
        // Preserve conference filter if it exists
        const conferenceId = currentUrl.searchParams.get('conference_id');
        if (conferenceId) {
            currentUrl.searchParams.set('conference_id', conferenceId);
        }
        
        // Preserve status filter if it exists
        const status = currentUrl.searchParams.get('status');
        if (status) {
            currentUrl.searchParams.set('status', status);
        }
        
        // Preserve session status filter if it exists
        const sessionStatus = currentUrl.searchParams.get('session_status');
        if (sessionStatus) {
            currentUrl.searchParams.set('session_status', sessionStatus);
        }
        
        window.location.href = currentUrl.toString();
    }

    // Export sessions functionality
    const exportBtn = document.getElementById('export-sessions-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportSessionData();
        });
    }

    function exportSessionData() {
        // Get current filter parameters
        const currentUrl = new URL(window.location);
        const status = currentUrl.searchParams.get('status') || 'all';
        const sessionStatus = currentUrl.searchParams.get('session_status') || '';
        const conferenceId = currentUrl.searchParams.get('conference_id') || '';
        
        // Build export URL with current filters
        let exportUrl = '{{ route("sessions.export") }}?';
        if (status && status !== 'all') {
            exportUrl += 'status=' + encodeURIComponent(status) + '&';
        }
        if (sessionStatus) {
            exportUrl += 'session_status=' + encodeURIComponent(sessionStatus) + '&';
        }
        if (conferenceId) {
            exportUrl += 'conference_id=' + encodeURIComponent(conferenceId) + '&';
        }
        
        const sessionTitle = currentUrl.searchParams.get('session_title') || '';
        if (sessionTitle) {
            exportUrl += 'session_title=' + encodeURIComponent(sessionTitle) + '&';
        }
        
        // Remove trailing & if present
        exportUrl = exportUrl.replace(/&$/, '');
        
        // Trigger download
        window.location.href = exportUrl;
    }

    // Resend session emails function
    window.resendSessionEmails = function(sessionId) {
        if (!confirm('Are you sure you want to resend emails to all participants for this session?')) {
            return;
        }

        // Show loading state
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
        button.disabled = true;

        fetch(`/sessions/${sessionId}/resend-email-all`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Reload the page to update the email counters
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resending emails.');
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    };
});
</script>
@endsection 