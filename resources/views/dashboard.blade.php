@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .activity-item {
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        background-color: #f8fafc;
        transform: translateX(4px);
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
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
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
    .animate-delay-5 { animation-delay: 0.5s; }
    
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
<!-- Enhanced Dashboard Header -->
<div class="rounded-2xl bg-gradient-to-r from-indigo-100 via-indigo-50 to-white shadow-lg flex items-center px-8 py-6 mb-10 border border-indigo-200 animate-fade-in-up">
    <div class="flex items-center justify-center w-16 h-16 bg-indigo-200 rounded-full mr-6 shadow-lg">
        <svg class="w-8 h-8 text-indigo-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V6a2 2 0 012-2h14a2 2 0 012 2v7.5M3 13.5l9 6 9-6M3 13.5l9-6 9 6"/></svg>
    </div>
    <div class="flex-1">
        <h1 class="text-3xl font-extrabold text-indigo-800 tracking-tight mb-1">Dashboard</h1>
        <div class="text-slate-600 text-lg font-medium">Conference Management Overview</div>
    </div>
    
    <!-- Quick Actions -->
    <div class="flex space-x-3">
        <button class="quick-action-btn modern-primary p-3 rounded-full shadow-lg transition-all duration-200" title="Add Task">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>
        <button class="quick-action-btn modern-info p-3 rounded-full shadow-lg transition-all duration-200" title="Add Participant">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
        </button>
        <button class="quick-action-btn modern-success p-3 rounded-full shadow-lg transition-all duration-200" title="Create Session">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </button>
    </div>
</div>

@if(isset($noConferences) && $noConferences)
    <!-- Enhanced No Conferences Message -->
    <div class="max-w-4xl mx-auto text-center py-12 animate-fade-in-up">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-700 mb-2">No Conferences Found</h2>
            <p class="text-gray-500 mb-6">Get started by creating your first conference to begin managing events.</p>
            <a href="{{ route('conferences.create') }}" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Conference
            </a>
        </div>
    </div>
@else
    <!-- Enhanced Conference Selection Section -->
    <div class="max-w-7xl mx-auto mb-8 animate-fade-in-up animate-delay-1">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <form method="GET" action="" class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-1/2">
                    <label for="conference_id" class="block text-sm font-medium text-gray-700 mb-2">Select Conference</label>
                    <select name="conference_id" id="conference_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-all duration-200" onchange="this.form.submit()">
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}" {{ $selectedConferenceId == $conference->id ? 'selected' : '' }}>
                                {{ $conference->name }} ({{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/2 text-right">
                    @if($dashboardData && $dashboardData['conference_progress']['conference'])
                        <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold border border-yellow-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $dashboardData['conference_progress']['conference']->name }}
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($dashboardData)
        <!-- Enhanced Conference Progress Section -->
        <div class="max-w-7xl mx-auto mb-8 animate-fade-in-up animate-delay-2">
            <div class="bg-white rounded-2xl shadow-lg flex flex-col md:flex-row items-center justify-between p-6 border-l-4 border-yellow-400 card-hover">
                <div class="flex-1 flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full shadow-lg">
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
                    <div class="w-40 h-3 bg-gray-200 rounded-full overflow-hidden mb-1 shadow-inner">
                        <div class="h-3 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full progress-bar shadow-sm" style="width: {{ $dashboardData['conference_progress']['progress_percentage'] }}%"></div>
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

        <!-- Enhanced Task Progress Section -->
        <div class="max-w-7xl mx-auto mb-8 animate-fade-in-up animate-delay-3">
            <div class="bg-white rounded-2xl shadow-lg flex flex-col md:flex-row items-center justify-between p-6 border-l-4 border-green-400 card-hover">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full shadow-lg">
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
                    <div class="w-40 h-3 bg-gray-200 rounded-full overflow-hidden mb-1 shadow-inner">
                        <div class="h-3 bg-gradient-to-r from-green-400 to-green-500 rounded-full progress-bar shadow-sm" style="width: {{ $dashboardData['task_progress']['progress_percentage'] }}%"></div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4"/></svg>
                        <span>{{ $dashboardData['task_progress']['remaining_tasks'] }} tasks remaining</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Summary Stats Section -->
        <div class="max-w-7xl mx-auto mb-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <!-- Invited -->
                <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-blue-400 stat-card card-hover animate-fade-in-up animate-delay-1">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-3 shadow-lg">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <div class="text-3xl font-bold text-blue-700 mb-1">{{ $dashboardData['summary_stats']['invited'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Invited</div>
                </div>
                
                <!-- Accepted -->
                <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-green-400 stat-card card-hover animate-fade-in-up animate-delay-2">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3 shadow-lg">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div class="text-3xl font-bold text-green-700 mb-1">{{ $dashboardData['summary_stats']['accepted'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Accepted</div>
                </div>
                
                <!-- Flying -->
                <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-yellow-400 stat-card card-hover animate-fade-in-up animate-delay-3">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full mb-3 shadow-lg">
                        <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2a5 5 0 00-2 4v5a2 2 0 002 2h10a2 2 0 002-2v-5a5 5 0 00-2-4z"/></svg>
                    </span>
                    <div class="text-3xl font-bold text-yellow-700 mb-1">{{ $dashboardData['summary_stats']['flying'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Flying</div>
                </div>
                
                <!-- Status Breakdown -->
                <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-pink-400 stat-card card-hover animate-fade-in-up animate-delay-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-pink-100 rounded-full mb-3 shadow-lg">
                        <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01"/></svg>
                    </span>
                    <div class="flex flex-col items-center mb-1">
                        <div class="text-xs text-gray-500">Pending: <span class="font-bold text-pink-700">{{ $dashboardData['summary_stats']['status_breakdown']['pending'] }}</span></div>
                        <div class="text-xs text-gray-500">Approved: <span class="font-bold text-green-700">{{ $dashboardData['summary_stats']['status_breakdown']['approved'] }}</span></div>
                        <div class="text-xs text-gray-500">Declined: <span class="font-bold text-red-700">{{ $dashboardData['summary_stats']['status_breakdown']['declined'] }}</span></div>
                    </div>
                    <div class="text-sm text-gray-500 font-medium">Status</div>
                </div>
                
                <!-- Speakers -->
                <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-purple-400 stat-card card-hover animate-fade-in-up animate-delay-5">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-3 shadow-lg">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    </span>
                    <div class="text-3xl font-bold text-purple-700 mb-1">{{ $dashboardData['speaker_statistics']['total_speakers'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Speakers</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Activity Feed Section -->
        <div class="max-w-7xl mx-auto mb-10 animate-fade-in-up animate-delay-5">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Recent Activities</h3>
                    <button class="text-yellow-600 hover:text-yellow-700 font-medium text-sm">View All</button>
                </div>
                
                <div class="space-y-4">
                    @forelse($dashboardData['recent_activities'] as $activity)
                        <div class="activity-item flex items-start gap-4 p-4 rounded-lg border border-gray-100 hover:border-yellow-200">
                            <div class="flex-shrink-0">
                                @if($activity['type'] === 'participant')
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
                                    {{ $activity['type'] === 'participant' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($activity['type']) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500">No recent activities</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars on load
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // Add click handlers for quick action buttons
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.classList.add('absolute', 'bg-white', 'rounded-full', 'opacity-50');
            ripple.style.width = ripple.style.height = '20px';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.transform = 'translate(-50%, -50%)';
            ripple.style.animation = 'ripple 0.6s linear';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
});
</script>
@endpush
