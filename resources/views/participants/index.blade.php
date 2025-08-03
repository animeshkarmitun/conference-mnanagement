@extends('layouts.app')

@section('title', 'Participants')

@push('styles')
<style>
    .participant-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .participant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .filter-dropdown {
        transition: all 0.2s ease;
    }
    
    .filter-dropdown:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .avatar {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        font-weight: bold;
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
    
    .bulk-action-panel {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .search-highlight {
        background-color: #fef3c7;
        padding: 2px 4px;
        border-radius: 4px;
    }
    
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: #fefce8;
        transform: scale(1.01);
    }
    
    .sortable-header {
        transition: all 0.2s ease;
    }
    
    .sortable-header:hover {
        background-color: #fefce8;
        color: #f59e0b;
    }
    
    .sort-icon.active {
        color: #f59e0b;
        transform: rotate(180deg);
    }
</style>
@endpush

@section('content')
<!-- Enhanced Header with Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Participants</h2>
            <p class="text-gray-600 mt-1">Manage conference participants and registrations</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Quick Actions -->
            <div class="flex space-x-3">
                <button class="quick-action-btn bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Import Participants">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Send Bulk Email">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Generate Reports">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"></path>
                    </svg>
                </button>
            </div>
            <a href="{{ route('participants.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Participant
            </a>
        </div>
    </div>
</div>

<!-- Enhanced Participant Status Tabs -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-gray-100">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('participants.index', ['status' => 'approved']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'approved' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Approved Participants
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['approved'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('participants.index', ['status' => 'pending']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'pending' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pending Participants
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['pending'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('participants.index', ['status' => 'rejected']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'rejected' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Rejected Participants
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['rejected'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('participants.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'all' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    All Participants
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['all'] }}</span>
                </div>
            </a>
        </nav>
    </div>
</div>

<!-- Enhanced Search and Secondary Filters -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
    <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
        <!-- Enhanced Search Bar -->
        <div class="flex-1 max-w-md">
            <form method="GET" action="{{ route('participants.index') }}" class="flex">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search participants by name, email, organization, or serial number..."
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
                    <a href="{{ route('participants.index', ['status' => $status]) }}" class="ml-3 bg-gray-500 hover:bg-gray-600 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Enhanced Secondary Filter Tabs -->
        <div class="flex flex-wrap gap-3">
            <!-- Visa Status Filter -->
            <div class="relative group">
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
            </div>
            
            <!-- Enhanced Participant Type Filter -->
            <div class="relative group">
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
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            @if($status === 'approved')
                Approved Participants
            @elseif($status === 'pending')
                Pending Participants
            @elseif($status === 'rejected')
                Rejected Participants
            @else
                All Participants
            @endif
        </h2>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">{{ $participants->total() ?? 0 }} participants</div>
            <div class="flex items-center space-x-2">
                <select id="format-select" class="rounded-md border-2 border-gray-400 bg-white text-gray-700 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium shadow-sm">
                    <option value="pdf">PDF</option>
                    <option value="zip">ZIP</option>
                </select>
                <button id="download-biographies-btn" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-lg font-semibold text-sm shadow-lg transition duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-blue-700">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Resumes (<span id="selected-count">0</span>)
                </button>
                <button id="export-csv-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow-lg transition duration-200 transform hover:scale-105 border-2 border-green-700">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>
    
    <div class="mb-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div class="flex items-center">
            <label class="flex items-center">
                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
            </label>
        </div>
        
        <!-- Bulk Actions -->
        <div class="flex flex-wrap gap-2" id="bulk-actions" style="display: none;">
            <select id="bulk-status" class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">Update Status</option>
                <option value="approved">Approve</option>
                <option value="pending">Mark Pending</option>
                <option value="rejected">Reject</option>
            </select>
            <button id="bulk-update-btn" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg font-semibold text-sm">
                Update Selected
            </button>
            <button id="bulk-email-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold text-sm">
                Send Email
            </button>
        </div>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200" id="participantsTable">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" id="select-all-header" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="serial">
                    <div class="flex items-center space-x-1">
                        <span>Serial No.</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="name">
                    <div class="flex items-center space-x-1">
                        <span>Name</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="email">
                    <div class="flex items-center space-x-1">
                        <span>Email</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="type">
                    <div class="flex items-center space-x-1">
                        <span>Type</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="organization">
                    <div class="flex items-center space-x-1">
                        <span>Organization</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="status">
                    <div class="flex items-center space-x-1">
                        <span>Status</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="visa_status">
                    <div class="flex items-center space-x-1">
                        <span>Visa Status</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($participants ?? [] as $i => $participant)
                @php
                    $user = $participant->user;
                    $serial = $participant->serial_number ?? (sprintf('CONF%04d-%03d', $participant->conference_id ?? 0, $i+1));
                    $dob = $user->date_of_birth ?? null;
                    $age = $dob ? \Carbon\Carbon::parse($dob)->age : '';
                    $category = $participant->category ?? ($participant->participantType->name === 'Delegate' ? 'Delegate' : '');
                    
                    // Extract numeric parts for serial number sorting
                    $serialParts = explode('-', $serial);
                    $conferenceNum = isset($serialParts[0]) ? intval(substr($serialParts[0], 4)) : 0;
                    $participantNum = isset($serialParts[1]) ? intval($serialParts[1]) : 0;
                    $serialSortValue = sprintf('%04d-%03d', $conferenceNum, $participantNum);
                    
                    // Status priorities
                    $statusPriority = $participant->registration_status === 'approved' ? 1 : ($participant->registration_status === 'pending' ? 2 : 3);
                    $visaPriority = $participant->visa_status === 'approved' ? 1 : ($participant->visa_status === 'pending' ? 2 : ($participant->visa_status === 'required' ? 3 : 4));
                @endphp
                <tr class="table-row-hover hover:bg-yellow-50 transition-all duration-200 border-b border-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="participant-checkbox" value="{{ $participant->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $serialSortValue }}">{{ $serial }}</td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ strtolower(($user->first_name ?? $user->name) . ' ' . ($user->last_name ?? '')) }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 avatar rounded-full flex items-center justify-center mr-3 shadow-lg">
                                <span class="text-sm font-bold">
                                    {{ substr($user->first_name ?? $user->name, 0, 1) }}{{ substr($user->last_name ?? '', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('participants.show', $participant) }}" class="text-blue-700 hover:text-blue-800 font-semibold transition-colors duration-200">
                                    {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}
                                </a>
                                @if($age)
                                    <div class="text-xs text-gray-500">{{ $age }} years old</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ strtolower($user->email) }}">
                        <button onclick="openEmailModal('{{ $user->email }}', '{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}')" class="text-blue-700 hover:text-blue-800 hover:underline cursor-pointer font-semibold transition-colors duration-200 border-none bg-transparent p-0">
                            {{ $user->email }}
                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ strtolower($participant->participantType->name ?? '') }}">{{ $participant->participantType->name ?? '' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ strtolower($participant->organization ?? 'zzz') }}">{{ $participant->organization }}</td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ ucfirst($participant->registration_status) }}" data-sort-priority="{{ $statusPriority }}">
                        <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $participant->registration_status == 'approved' ? 'bg-green-100 text-green-700 border border-green-200' : ($participant->registration_status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                            @if($participant->registration_status == 'approved')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($participant->registration_status == 'pending')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            {{ ucfirst($participant->registration_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ ucfirst(str_replace('_', ' ', $participant->visa_status)) }}" data-sort-priority="{{ $visaPriority }}">
                        <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm 
                            {{ $participant->visa_status == 'approved' ? 'bg-green-100 text-green-700 border border-green-200' : 
                               ($participant->visa_status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 
                               ($participant->visa_status == 'issue' ? 'bg-red-100 text-red-700 border border-red-200' : 
                               ($participant->visa_status == 'required' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                               'bg-gray-100 text-gray-700 border border-gray-200'))) }}">
                            @if($participant->visa_status == 'approved')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($participant->visa_status == 'pending')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($participant->visa_status == 'issue')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($participant->visa_status == 'required')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            {{ ucfirst(str_replace('_', ' ', $participant->visa_status)) }}
                        </span>
                        @if($participant->visa_status == 'issue')
                            <div class="text-xs text-red-600 mt-1 flex items-center" title="{{ $participant->visa_issue_description }}">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Issue reported
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('participants.show', $participant) }}" 
                               class="quick-action-btn inline-flex items-center p-2 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800 rounded-lg transition-all duration-200 border border-blue-200 shadow-sm"
                               title="View Participant Details"
                               aria-label="View participant details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            
                            <a href="{{ route('participants.edit', $participant) }}" 
                               class="quick-action-btn inline-flex items-center p-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 rounded-lg transition-all duration-200 border border-yellow-200 shadow-sm"
                               title="Edit Participant"
                               aria-label="Edit participant">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            
                            <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this participant?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="quick-action-btn inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-all duration-200 border border-red-200 shadow-sm"
                                        title="Delete Participant"
                                        aria-label="Delete participant">
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
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if($status === 'approved')
                            No approved participants found.
                        @elseif($status === 'pending')
                            No pending participants found.
                        @elseif($status === 'rejected')
                            No rejected participants found.
                        @else
                            No participants found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $participants->appends(['status' => $status])->links() }}
    </div>
</div>

<style>
.tab-link {
    transition: all 0.2s ease-in-out;
}

.tab-link:hover {
    transform: translateY(-1px);
}

.sortable-header {
    user-select: none;
}

.sortable-header:hover {
    background-color: #f9fafb;
}

.sort-icon {
    transition: all 0.2s ease-in-out;
}

.sort-icon.active {
    color: #3b82f6;
}

.sort-icon.asc {
    transform: rotate(0deg);
}

.sort-icon.desc {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Existing participant functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const selectAllHeaderCheckbox = document.getElementById('select-all-header');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    const downloadBtn = document.getElementById('download-biographies-btn');
    const selectedCountSpan = document.getElementById('selected-count');
    const formatSelect = document.getElementById('format-select');
    
    // Sorting functionality
    const table = document.getElementById('participantsTable');
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
            console.log('Sorting participants by column:', column);
            sortTable(column);
        });
    });
    
    console.log('Found', headers.length, 'sortable headers for participants');
    console.log('Found', tbody.querySelectorAll('tr').length, 'participant table rows');
    
    function sortTable(column) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Filter out empty rows (like the "no participants" message)
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
            
            if (column === 'status' || column === 'visa_status') {
                // Sort by status priority
                const aPriority = parseInt(a.cells[getColumnIndex(column)].getAttribute('data-sort-priority'));
                const bPriority = parseInt(b.cells[getColumnIndex(column)].getAttribute('data-sort-priority'));
                comparison = aPriority - bPriority;
            } else if (column === 'serial') {
                // Sort by serial number (numeric parts)
                comparison = aValue.localeCompare(bValue);
            } else {
                // Sort alphabetically for other columns
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
        
        if (column === 'status' || column === 'visa_status') {
            return parseInt(cell.getAttribute('data-sort-priority'));
        }
        
        return cell.getAttribute('data-sort-value');
    }
    
    function getColumnIndex(column) {
        const columnMap = {
            'serial': 1,      // Skip checkbox column (0)
            'name': 2,
            'email': 3,
            'type': 4,
            'organization': 5,
            'status': 6,
            'visa_status': 7
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
            icon.style.color = '#3b82f6'; // blue-500
        }
    }
    
    // Function to update selected count and button state
    function updateSelectionState() {
        const selectedCount = document.querySelectorAll('.participant-checkbox:checked').length;
        selectedCountSpan.textContent = selectedCount;
        
        if (selectedCount > 0) {
            downloadBtn.disabled = false;
            downloadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            document.getElementById('bulk-actions').style.display = 'flex';
        } else {
            downloadBtn.disabled = true;
            downloadBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('bulk-actions').style.display = 'none';
        }
    }
    
    // Function to handle select all
    function handleSelectAll(checked) {
        participantCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateSelectionState();
    }
    
    // Select all checkbox event listeners
    selectAllCheckbox.addEventListener('change', function() {
        handleSelectAll(this.checked);
        selectAllHeaderCheckbox.checked = this.checked;
    });
    
    selectAllHeaderCheckbox.addEventListener('change', function() {
        handleSelectAll(this.checked);
        selectAllCheckbox.checked = this.checked;
    });
    
    // Individual checkbox event listeners
    participantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionState();
            
            // Update select all checkboxes
            const allChecked = document.querySelectorAll('.participant-checkbox:checked').length === participantCheckboxes.length;
            const someChecked = document.querySelectorAll('.participant-checkbox:checked').length > 0;
            
            selectAllCheckbox.checked = allChecked;
            selectAllHeaderCheckbox.checked = allChecked;
        });
    });

    // Format select event listener
    formatSelect.addEventListener('change', function() {
        // Just update the format for the download, no need to change URL
        console.log('Format selected:', this.value);
    });
    
    // Download biographies button event listener
    downloadBtn.addEventListener('click', function() {
        let selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        // If no participants are selected, download all participants
        if (selectedIds.length === 0) {
            if (confirm('No participants selected. Download resumes for all participants?')) {
                selectedIds = []; // Empty array means download all
            } else {
                return;
            }
        }
        
        const selectedFormat = formatSelect.value;
        
        // Show loading state
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Generating ${selectedFormat.toUpperCase()}...
        `;
        downloadBtn.disabled = true;
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.participants.download-biographies") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add selected participant IDs
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'participant_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        // Add format preference
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = selectedFormat;
        form.appendChild(formatInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Reset button after a delay
        setTimeout(() => {
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;
        }, 3000);
    });
    
    // Bulk update functionality
    document.getElementById('bulk-update-btn').addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
            .map(checkbox => checkbox.value);
        const newStatus = document.getElementById('bulk-status').value;
        
        if (selectedIds.length === 0) {
            alert('Please select at least one participant.');
            return;
        }
        
        if (!newStatus) {
            alert('Please select a status to update.');
            return;
        }
        
        if (confirm(`Are you sure you want to update ${selectedIds.length} participant(s) to "${newStatus}" status?`)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("participants.bulk-update") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add selected participant IDs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'participant_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            // Add new status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = newStatus;
            form.appendChild(statusInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    });
    
    // CSV Export functionality
    document.getElementById('export-csv-btn').addEventListener('click', function() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    });
    
    // Bulk email functionality
    document.getElementById('bulk-email-btn').addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one participant.');
            return;
        }
        
        // Redirect to bulk email page with selected IDs
        const params = new URLSearchParams();
        selectedIds.forEach(id => params.append('participant_ids[]', id));
        window.location.href = '{{ route("bulk.email") }}?' + params.toString();
    });
    
    // Initialize state
    updateSelectionState();
});

// Email Modal Functions
function openEmailModal(email, name) {
    console.log('openEmailModal called with:', email, name);
    
    const modal = document.getElementById('emailModal');
    const toEmail = document.getElementById('toEmail');
    const message = document.getElementById('message');
    
    if (!modal) {
        console.error('Modal not found!');
        return;
    }
    
    // Set the recipient email
    toEmail.value = email;
    
    // Pre-fill message with greeting
    const greeting = `Dear ${name},\n\n`;
    const defaultMessage = `Thank you for your participation in our conference. We hope you find the sessions informative and engaging.\n\nBest regards,\nConference Team`;
    message.value = greeting + defaultMessage;
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    console.log('Modal should be visible now');
}

function closeEmailModal() {
    const modal = document.getElementById('emailModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const emailForm = document.getElementById('emailForm');
    const sendButton = document.getElementById('sendButton');
    
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const originalText = sendButton.textContent;
            sendButton.textContent = 'Sending...';
            sendButton.disabled = true;
            
            // Simulate email sending (placeholder for now)
            setTimeout(() => {
                // Show success message
                alert('Email sent successfully! (This is a placeholder - email functionality will be implemented later)');
                
                // Reset button
                sendButton.textContent = originalText;
                sendButton.disabled = false;
                
                // Close modal
                closeEmailModal();
            }, 1500);
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('emailModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEmailModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeEmailModal();
        }
    });
});
</script>

<!-- Email Modal -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Send Email</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="emailForm" class="space-y-4">
                <div>
                    <label for="fromEmail" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="email" id="fromEmail" name="from" value="admin@conference.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" readonly>
                </div>
                
                <div>
                    <label for="toEmail" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="email" id="toEmail" name="to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" readonly>
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" id="subject" name="subject" value="Conference Update" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="Type your message here..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEmailModal()" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" id="sendButton" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md font-medium transition-colors duration-200">
                        Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 