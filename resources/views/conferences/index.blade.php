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
    
    .sortable-header {
        user-select: none;
        transition: all 0.2s ease;
    }
    
    .sortable-header:hover {
        background-color: #fefce8;
        color: #f59e0b;
    }
    
    .sort-icon {
        transition: all 0.2s ease-in-out;
    }
    
    .sort-icon.active {
        color: #f59e0b;
        transform: rotate(180deg);
    }
    
    .sort-icon.asc {
        transform: rotate(0deg);
    }
    
    .sort-icon.desc {
        transform: rotate(180deg);
    }
    
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: #fefce8;
        transform: scale(1.01);
    }
    
    .conference-icon {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        font-weight: bold;
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
</style>
@endpush

@section('content')
<!-- Enhanced Header with Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100 animate-fade-in-up">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Conferences</h2>
            <p class="text-gray-600 mt-1">Manage and organize conference events</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Quick Actions -->
            <div class="flex space-x-3">
                <button class="quick-action-btn bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Import Conferences">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Generate Reports">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Export Data">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </button>
            </div>
            <a href="{{ route('conferences.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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

<!-- Enhanced Conference Table -->
<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 animate-fade-in-up animate-delay-2">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="conferencesTable">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="status">
                        <div class="flex items-center space-x-1">
                            <span>Status</span>
                            <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="title">
                        <div class="flex items-center space-x-1">
                            <span>Title</span>
                            <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="schedule">
                        <div class="flex items-center space-x-1">
                            <span>Schedule</span>
                            <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="duration">
                        <div class="flex items-center space-x-1">
                            <span>Duration</span>
                            <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="venue">
                        <div class="flex items-center space-x-1">
                            <span>Venue</span>
                            <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
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
                    
                    <tr class="table-row-hover hover:bg-yellow-50 transition-all duration-200 border-b border-gray-100">
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
                                
                                <form action="{{ route('conferences.destroy', $conference) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this conference?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="quick-action-btn inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-all duration-200 border border-red-200 shadow-sm"
                                            title="Delete Conference"
                                            aria-label="Delete conference">
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
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection 