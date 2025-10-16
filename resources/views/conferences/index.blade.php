@extends('layouts.app')

@section('title', 'Conferences')

@push('styles')
<style>
    .conference-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .conference-card:hover {
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
    
    /* Clean Table Header Styles */
    .table-header {
        background: #f1f5f9;
        border-bottom: 2px solid #cbd5e1;
    }
    
    .sortable-header {
        user-select: none;
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 1.25rem 1.5rem;
        font-weight: 700;
        font-size: 0.875rem;
        color: #1f2937;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-right: 1px solid #e5e7eb;
        background: transparent;
        position: relative;
    }
    
    .sortable-header:last-child {
        border-right: none;
    }
    
    .sortable-header:hover {
        background: #f8fafc;
        color: #6366f1;
    }
    
    .sortable-header.active {
        background: #f8fafc;
        color: #6366f1;
        border-bottom: 2px solid #6366f1;
    }
    
    .sort-icon {
        opacity: 0.8;
        color: #4b5563;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    
    .sortable-header:hover .sort-icon {
        opacity: 1;
        color: #6366f1;
    }
    
    .sort-icon.active {
        color: #6366f1;
        opacity: 1;
    }
    
    .sort-icon.asc {
        transform: rotate(0deg);
    }
    
    .sort-icon.desc {
        transform: rotate(180deg);
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
    
    .conference-icon {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        font-weight: bold;
    }
    
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: #fefce8;
        transform: scale(1.01);
    }
    
    /* Conference table container for horizontal scrolling */
    .conferences-table-container {
        max-height: none;
        overflow-x: auto;
        width: 100%;
        position: relative;
    }
    
    /* Webkit scrollbar styling for conference table */
    .conferences-table-container::-webkit-scrollbar {
        height: 8px;
    }
    
    .conferences-table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .conferences-table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
        transition: background 0.2s ease;
    }
    
    .conferences-table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Visual feedback for scrollable content */
    .conferences-table-container.can-scroll-left::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 20px;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.8), transparent);
        pointer-events: none;
        z-index: 1;
    }
    
    .conferences-table-container.can-scroll-right::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 20px;
        background: linear-gradient(to left, rgba(255, 255, 255, 0.8), transparent);
        pointer-events: none;
        z-index: 1;
    }
    
    /* Smooth scrolling hint */
    .conferences-table-container:hover {
        cursor: grab;
    }
    
    .conferences-table-container:active {
        cursor: grabbing;
    }
</style>
@endpush

@section('content')
<!-- Enhanced Header Section -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-slate-100 animate-fade-in-up">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h2 class="text-3xl font-bold text-slate-800">Conferences</h2>
            <p class="text-slate-600 mt-1">Manage conference events and schedules</p>
        </div>
        
        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <button onclick="exportConferenceData()" class="quick-action-btn modern-admin p-3 rounded-full shadow-lg transition-all duration-200" title="Export Data">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </button>
            
            <a href="{{ route('conferences.create') }}" class="modern-primary px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Conference
            </a>
        </div>
    </div>
</div>

<!-- Enhanced Conference Status Tabs -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-gray-100 animate-fade-in-up animate-delay-1">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('conferences.index', ['status' => 'active']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'active' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Active Conferences
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['active'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('conferences.index', ['status' => 'upcoming']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'upcoming' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Upcoming Conferences
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['upcoming'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('conferences.index', ['status' => 'finished']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'finished' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Finished Conferences
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['finished'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('conferences.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'all' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    All Conferences
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['all'] }}</span>
                </div>
            </a>
        </nav>
    </div>
</div>

<!-- Enhanced Search and Secondary Filters -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
    <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
        <!-- Enhanced Search Bar -->
        <div class="flex-1 max-w-md">
            <form method="GET" action="{{ route('conferences.index') }}" class="flex">
                <input type="hidden" name="status" value="{{ $status }}">
                @if(request()->has('conference_id'))
                    <input type="hidden" name="conference_id" value="{{ request('conference_id') }}">
                @endif
                <div class="relative flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search conferences by name, vanue ..."
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <button type="submit" class="ml-3 bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
                @if($search)
                    <a href="{{ route('participants.index', array_merge(request()->except('search'), ['status' => $status])) }}" class="ml-3 bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Enhanced Secondary Filter Tabs -->
        <div class="flex gap-3 flex-shrink-0">
            <!-- Conference Filter -->
            <form method="GET" action="{{ route('conferences.index') }}" class="flex items-center gap-2">
                @foreach(request()->except(['conference_id']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <label for="conference_search" class="text-sm text-gray-600">Conference</label>
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
                        <div id="conference_options">
                            <!-- Conference options will be populated here -->
                        </div>
                    </div>
                </div>
                <input type="hidden" id="conference_id" name="conference_id" value="{{ request('conference_id') }}">
            </form>
            <!-- Visa Status Filter -->
            {{-- <div class="relative group">
                <button class="filter-dropdown bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 border border-blue-200 shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Visa Status
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Filter by Visa Status</div>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'required'])) }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <span>Required</span>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">{{ $visaCounts['required'] }}</span>
                            </div>
                        </a>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'approved'])) }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <span>Approved</span>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{ $visaCounts['approved'] }}</span>
                            </div>
                        </a>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'pending'])) }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-yellow-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <span>Pending</span>
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">{{ $visaCounts['pending'] }}</span>
                            </div>
                        </a>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'issue'])) }}" 
                           class="block px-4 py-3 text-sm text-gray-700 hover:bg-red-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <span>Issues</span>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">{{ $visaCounts['issue'] }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div> --}}
            
            <!-- Enhanced Participant Type Filter -->
            {{-- <div class="relative group">
                <button class="filter-dropdown bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 border border-purple-200 shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Participant Type
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Filter by Type</div>
                        @foreach($participantTypes as $type)
                            <a href="{{ route('participants.index', array_merge(request()->query(), ['type' => $type->name])) }}" 
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <span>{{ $type->name }}</span>
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">{{ $typeCounts[$type->name] ?? 0 }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

<!-- Enhanced Conference Table -->
<div class="table-container bg-white animate-fade-in-up animate-delay-2">
    <div class="overflow-x-auto conferences-table-container">
        <table class="w-full" id="conferencesTable">
            <thead class="table-header">
                <tr>
                    <th class="sortable-header" data-sort="status">
                        Status
                        <svg class="sort-icon ml-8 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="sortable-header" data-sort="title">
                        Title
                        <svg class="sort-icon ml-8 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="sortable-header" data-sort="schedule">
                        Schedule
                        <svg class="sort-icon ml-8 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="sortable-header" data-sort="duration">
                        Duration
                        <svg class="sort-icon ml-8 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="sortable-header" data-sort="venue">
                        Venue
                        <svg class="sort-icon ml-8 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </th>
                    <th class="px-6 py-4 text-right bg-gray-50 border-r border-gray-200">
                        <span class="text-sm font-semibold text-gray-600">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($conferences ?? [] as $conference)
                    @php
                        $conferenceData = \App\Helpers\DateHelper::formatConferenceDates($conference->start_date, $conference->end_date);
                        $statusClass = \App\Helpers\DateHelper::getConferenceStatusColorClass(
                            $conferenceData['is_active'], 
                            $conferenceData['is_past'], 
                            $conferenceData['is_today'], 
                            $conferenceData['is_upcoming']
                        );
                        $durationClass = \App\Helpers\DateHelper::getConferenceDurationColorClass($conferenceData['duration_days']);
                        $statusText = \App\Helpers\DateHelper::getConferenceStatusText(
                            $conferenceData['is_active'], 
                            $conferenceData['is_past'], 
                            $conferenceData['is_today'], 
                            $conferenceData['is_upcoming']
                        );
                    @endphp
                    
                    <tr class="table-row-hover hover:bg-yellow-50 transition-all duration-200 border-b border-gray-200">
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $statusText }}" data-sort-priority="{{ $conferenceData['is_active'] ? 1 : ($conferenceData['is_today'] ? 2 : ($conferenceData['is_upcoming'] ? 3 : 4)) }}">
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $statusClass }}">
                                @if($conferenceData['is_active'])
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M13 10V3L4 14h7v7l9-11h-7z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($conferenceData['is_today'])
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($conferenceData['is_upcoming'])
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
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ strtolower($conference->name) }}">
                            <div class="flex items-center">
                                <div class="w-10 h-10 conference-icon rounded-full flex items-center justify-center mr-3 shadow-lg">
                                    <span class="text-sm font-bold">
                                        {{ substr($conference->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $conference->name }}</div>
                                    @if($conference->description)
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $conference->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $conference->start_date }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-900 font-medium">{{ $conferenceData['schedule_string'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $conferenceData['start_date_formatted'] }} - {{ $conferenceData['end_date_formatted'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $conferenceData['duration_days'] }}">
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $durationClass }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $conferenceData['duration'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500" data-sort-value="{{ strtolower($conference->venue->name ?? 'N/A') }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>{{ $conference->venue->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('conferences.show', $conference) }}" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800 rounded-lg transition-all duration-200 border border-blue-200 shadow-sm"
                                   title="View Conference Details"
                                   aria-label="View conference details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('conferences.edit', $conference) }}" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 rounded-lg transition-all duration-200 border border-yellow-200 shadow-sm"
                                   title="Edit Conference"
                                   aria-label="Edit conference">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <button type="button" 
                                        onclick="confirmConferenceDeletion({{ $conference->id }}, '{{ addslashes($conference->name) }}')"
                                        class="quick-action-btn inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-all duration-200 border border-red-200 shadow-sm"
                                        title="Delete Conference"
                                        aria-label="Delete conference">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-gray-500 mb-2">
                                    @if($status === 'active')
                                        No active conferences at the moment.
                                    @elseif($status === 'upcoming')
                                        No upcoming conferences scheduled.
                                    @elseif($status === 'finished')
                                        No finished conferences found.
                                    @else
                                        No conferences found.
                                    @endif
                                </p>
                                <a href="{{ route('conferences.create') }}" class="text-yellow-600 hover:text-yellow-700 font-medium">Create your first conference</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $conferences->appends(['status' => $status])->links() }}
    </div>
</div>

<script>
// Conference data for dropdown
const conferences = @json($allConferences ?? []);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize conference searchable dropdown
    initializeConferenceDropdown();
    
    const table = document.getElementById('conferencesTable');
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
            console.log('Sorting by column:', column);
            sortTable(column);
        });
    });
    
    console.log('Found', headers.length, 'sortable headers');
    console.log('Found', tbody.querySelectorAll('tr').length, 'table rows');
    
    function sortTable(column) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Filter out empty rows (like the "no conferences" message)
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
                // Sort by status priority (Active=1, Today=2, Upcoming=3, Completed=4)
                const aPriority = parseInt(a.cells[0].getAttribute('data-sort-priority'));
                const bPriority = parseInt(b.cells[0].getAttribute('data-sort-priority'));
                comparison = aPriority - bPriority;
            } else if (column === 'duration') {
                // Sort by duration days (numeric)
                comparison = parseInt(aValue) - parseInt(bValue);
            } else if (column === 'schedule') {
                // Sort by start date
                comparison = new Date(aValue) - new Date(bValue);
            } else {
                // Sort alphabetically for title and venue
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
            'schedule': 2,
            'duration': 3,
            'venue': 4
        };
        return columnMap[column] || 0;
    }
    
    function updateSortIndicators(activeColumn, direction) {
        // Reset all sort headers and icons
        headers.forEach(header => {
            header.classList.remove('active');
            const icon = header.querySelector('.sort-icon');
            icon.classList.remove('active', 'asc', 'desc');
        });
        
        // Update active column header and icon
        const activeHeader = table.querySelector(`[data-sort="${activeColumn}"]`);
        if (activeHeader) {
            activeHeader.classList.add('active');
            const icon = activeHeader.querySelector('.sort-icon');
            icon.classList.add('active', direction);
        }
    }
    
    // Horizontal scroll functionality for conferences table
    const tableContainer = document.querySelector('.conferences-table-container');
    if (tableContainer) {
        let isScrolling = false;
        let scrollTimeout;
        
        function updateScrollIndicators() {
            const { scrollLeft, scrollWidth, clientWidth } = tableContainer;
            const canScrollLeft = scrollLeft > 0;
            const canScrollRight = scrollLeft < scrollWidth - clientWidth;
            
            tableContainer.classList.toggle('can-scroll-left', canScrollLeft);
            tableContainer.classList.toggle('can-scroll-right', canScrollRight);
        }
        
        function handleScroll() {
            if (!isScrolling) {
                isScrolling = true;
                tableContainer.style.cursor = 'grabbing';
            }
            
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                isScrolling = false;
                tableContainer.style.cursor = 'grab';
            }, 150);
            
            updateScrollIndicators();
        }
        
        // Initial check
        updateScrollIndicators();
        
        // Add scroll event listener
        tableContainer.addEventListener('scroll', handleScroll);
        
        // Add mouse events for better UX
        tableContainer.addEventListener('mouseenter', () => {
            if (!isScrolling) {
                tableContainer.style.cursor = 'grab';
            }
        });
        
        tableContainer.addEventListener('mouseleave', () => {
            tableContainer.style.cursor = 'default';
        });
        
        // Handle window resize
        window.addEventListener('resize', updateScrollIndicators);
    }
});

// Export function
function exportConferenceData() {
    const currentUrl = new URL(window.location.href);
    const status = currentUrl.searchParams.get('status') || 'upcoming'; // Match index default
    const search = currentUrl.searchParams.get('search') || '';
    
    // Build export URL with current filters
    const exportUrl = new URL('{{ route("conferences.export") }}', window.location.origin);
    exportUrl.searchParams.set('status', status);
    if (search) {
        exportUrl.searchParams.set('search', search);
    }
    
    // Trigger download
    window.location.href = exportUrl.toString();
}

// Conference searchable dropdown functionality
function initializeConferenceDropdown() {
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
        clearConferenceBtn.classList.remove('hidden');
        conferenceDropdown.classList.add('hidden');
        isDropdownOpen = false;
        
        // Submit the form to apply the filter
        conferenceSearch.closest('form').submit();
    }
    
    function clearSelection() {
        selectedConference = null;
        conferenceSearch.value = '';
        conferenceIdInput.value = '';
        clearConferenceBtn.classList.add('hidden');
        conferenceDropdown.classList.add('hidden');
        isDropdownOpen = false;
        
        // Submit the form to clear the filter
        conferenceSearch.closest('form').submit();
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
            }
        });
    }
    
    if (clearConferenceBtn) {
        clearConferenceBtn.addEventListener('click', clearSelection);
    }
    
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', toggleDropdown);
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            conferenceDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });
}

// Conference deletion confirmation functions
function confirmConferenceDeletion(conferenceId, conferenceName) {
    // Show loading state
    const modal = document.getElementById('deleteConferenceModal');
    const modalContent = modal.querySelector('.modal-content');
    modalContent.innerHTML = `
        <div class="flex items-center justify-center p-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
            <span class="ml-3 text-gray-600">Loading conference details...</span>
        </div>
    `;
    modal.classList.remove('hidden');
    
    // Fetch deletion info
    fetch(`/conferences/${conferenceId}/deletion-info`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        showDeletionConfirmation(data);
    })
    .catch(error => {
        console.error('Error fetching deletion info:', error);
        modalContent.innerHTML = `
            <div class="p-6">
                <div class="flex items-center justify-center text-red-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error Loading Details</h3>
                <p class="text-gray-600 mb-2">Unable to load conference deletion details.</p>
                <p class="text-sm text-gray-500 mb-6">Error: ${error.message}</p>
                <div class="flex justify-end">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        `;
    });
}

function showDeletionConfirmation(data) {
    const modalContent = document.getElementById('deleteConferenceModal').querySelector('.modal-content');
    const { conference, venue, related_data, participant_users_info, has_related_data } = data;
    
    let relatedDataHtml = '';
    if (has_related_data) {
        const items = [];
        if (related_data.participants > 0) items.push(`${related_data.participants} participant(s)`);
        if (related_data.sessions > 0) items.push(`${related_data.sessions} session(s)`);
        if (related_data.tasks > 0) items.push(`${related_data.tasks} task(s)`);
        if (related_data.notifications > 0) items.push(`${related_data.notifications} notification(s)`);
        if (related_data.communications > 0) items.push(`${related_data.communications} communication(s)`);
        if (related_data.checkins > 0) items.push(`${related_data.checkins} checkin(s)`);
        if (related_data.conference_docs > 0) items.push(`${related_data.conference_docs} document(s)`);
        
        relatedDataHtml = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-yellow-800 mb-2">Warning: This action will also delete:</h4>
                        <ul class="text-yellow-700 space-y-1">
                            ${items.map(item => `<li>• ${item}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }
    
    modalContent.innerHTML = `
        <div class="p-6">
            <div class="flex items-center justify-center text-red-600 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Delete Conference</h3>
            <p class="text-gray-600 mb-4">
                Are you sure you want to delete the conference <strong>"${conference.name}"</strong>?
            </p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Start Date:</span>
                        <span class="text-gray-600">${new Date(conference.start_date).toLocaleDateString()}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">End Date:</span>
                        <span class="text-gray-600">${new Date(conference.end_date).toLocaleDateString()}</span>
                    </div>
                </div>
            </div>
            ${venue ? `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-800 mb-2">Venue Information</h4>
                            <div class="space-y-1 text-sm">
                                <div><span class="font-medium text-blue-700">Venue:</span> <span class="text-blue-600">${venue.name}</span></div>
                                <div><span class="font-medium text-blue-700">Address:</span> <span class="text-blue-600">${venue.address || 'Not specified'}</span></div>
                                <div><span class="font-medium text-blue-700">Capacity:</span> <span class="text-blue-600">${venue.capacity || 'Not specified'}</span></div>
                                ${venue.other_conferences_count > 0 ? `
                                    <div class="mt-2 p-2 bg-blue-100 rounded">
                                        <div class="font-medium text-blue-800">⚠️ This venue is also used by ${venue.other_conferences_count} other conference(s):</div>
                                        <ul class="mt-1 text-blue-700 text-xs">
                                            ${venue.other_conferences.map(conf => `<li>• ${conf.name} (${new Date(conf.start_date).toLocaleDateString()} - ${new Date(conf.end_date).toLocaleDateString()})</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : `
                                    <div class="mt-2 p-2 bg-green-100 rounded">
                                        <div class="font-medium text-green-800">✓ This venue is only used by this conference</div>
                                        <div class="mt-2">
                                            <label class="flex items-center">
                                                <input type="checkbox" id="deleteVenueCheckbox" name="delete_venue" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                                                <span class="ml-2 text-sm text-green-700 font-medium">Also delete this venue</span>
                                            </label>
                                            <p class="text-xs text-green-600 mt-1">⚠️ This will permanently delete the venue and cannot be undone.</p>
                                        </div>
                                    </div>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            ` : ''}
            ${participant_users_info && participant_users_info.single_participant_users > 0 ? `
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-orange-800 mb-2">Participant User Deletion</h4>
                            <div class="text-sm text-orange-700 mb-3">
                                ${participant_users_info.single_participant_users} user(s) have only one participant profile (this conference only):
                            </div>
                            <div class="space-y-2 mb-3">
                                ${participant_users_info.single_participant_users_list.map(user => `
                                    <div class="text-xs bg-orange-100 p-2 rounded">
                                        <span class="font-medium">${user.user_name}</span> (${user.user_email})
                                    </div>
                                `).join('')}
                            </div>
                            <label class="flex items-center">
                                <input type="checkbox" id="deleteParticipantUsersCheckbox" name="delete_participant_users" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <span class="ml-2 text-sm text-orange-700 font-medium">Also delete these user accounts</span>
                            </label>
                            <p class="text-xs text-orange-600 mt-1">⚠️ This will permanently delete the user accounts and cannot be undone.</p>
                        </div>
                    </div>
                </div>
            ` : ''}
            ${relatedDataHtml}
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="deleteConference(${conference.id})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Delete Conference
                </button>
            </div>
        </div>
    `;
}

function deleteConference(conferenceId) {
    // Create and submit the form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/conferences/${conferenceId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    // Add venue deletion checkbox value if checked
    const deleteVenueCheckbox = document.getElementById('deleteVenueCheckbox');
    if (deleteVenueCheckbox && deleteVenueCheckbox.checked) {
        const deleteVenueField = document.createElement('input');
        deleteVenueField.type = 'hidden';
        deleteVenueField.name = 'delete_venue';
        deleteVenueField.value = '1';
        form.appendChild(deleteVenueField);
    }
    
    // Add participant user deletion checkbox value if checked
    const deleteParticipantUsersCheckbox = document.getElementById('deleteParticipantUsersCheckbox');
    if (deleteParticipantUsersCheckbox && deleteParticipantUsersCheckbox.checked) {
        const deleteParticipantUsersField = document.createElement('input');
        deleteParticipantUsersField.type = 'hidden';
        deleteParticipantUsersField.name = 'delete_participant_users';
        deleteParticipantUsersField.value = '1';
        form.appendChild(deleteParticipantUsersField);
    }
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

function closeDeleteModal() {
    document.getElementById('deleteConferenceModal').classList.add('hidden');
}
</script>

<!-- Conference Deletion Confirmation Modal -->
<div id="deleteConferenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="modal-content">
            <!-- Content will be dynamically loaded here -->
        </div>
    </div>
</div>

@endsection 