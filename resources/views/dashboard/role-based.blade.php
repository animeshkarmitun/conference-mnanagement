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
    
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Custom scrollbar for country modal */
    .country-modal-content {
        max-height: 70vh;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }
    
    .country-modal-content::-webkit-scrollbar {
        width: 8px;
    }
    
    .country-modal-content::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .country-modal-content::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.5);
        border-radius: 10px;
    }
    
    .country-modal-content::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.8);
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
    
    .animate-delay-4 { animation-delay: 0.4s; }
    .animate-delay-5 { animation-delay: 0.5s; }
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

        <!-- Dashboard Cards Section (Country Distribution, Gender, Status, etc.) -->
        @if(isset($dashboardStats) && !empty($dashboardStats) && isset($selectedConferenceId) && $user->hasPermission('participants.view'))
        @php
            $statsData = $dashboardStats;
        @endphp
        <!-- Enhanced Country Statistics Section -->
        <div class="mb-8 animate-fade-in-up animate-delay-4">
            <div id="countryCard" class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-400 card-hover cursor-pointer" title="Click to view full list">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full shadow-lg">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <div class="flex-1">
                        <div class="text-lg font-bold text-blue-700">Country Distribution</div>
                        <div class="text-sm text-gray-500">Participants by Country</div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-700">{{ $statsData['country_statistics']['total_participants_with_country'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">{{ ($statsData['country_statistics']['total_participants_with_country'] ?? 0) === 1 ? 'Participant' : 'Participants' }}</div>
                        <div class="text-[10px] text-gray-400 mt-1">{{ $statsData['country_statistics']['total_countries'] ?? 0 }} {{ ($statsData['country_statistics']['total_countries'] ?? 0) === 1 ? 'country' : 'countries' }}</div>
                    </div>
                </div>
                
                @if(($statsData['country_statistics']['total_countries'] ?? 0) === 0)
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">No country data available for participants</p>
                    </div>
                @else
                    <div class="text-center py-4 text-sm text-gray-500">
                        Click to view full list
                    </div>
                @endif

                @if(($statsData['country_statistics']['participants_without_country'] ?? 0) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Participants without country data
                            </span>
                            <span class="font-semibold">{{ $statsData['country_statistics']['participants_without_country'] ?? 0 }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Country Full List Modal -->
        <div id="countryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="text-lg font-semibold">Participants by Country</h3>
                        <button id="countryModalClose" class="text-gray-500 hover:text-gray-700" aria-label="Close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-4 country-modal-content">
                        @if(($statsData['country_statistics']['total_countries'] ?? 0) > 0)
                            <div class="space-y-2">
                                @foreach($statsData['country_statistics']['countries'] ?? [] as $country)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700">{{ $country['country'] }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-blue-700">{{ $country['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">No country data available for participants</div>
                        @endif
                    </div>
                    <div class="p-4 border-t text-right">
                        <button id="countryModalClose2" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Summary Stats Section -->
        <div class="mb-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Gender Breakdown -->
        <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-blue-400 stat-card card-hover animate-fade-in-up animate-delay-5">
            <span class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-3 shadow-lg">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </span>
            <div class="flex flex-col items-center mb-1">
                <div class="text-xs text-slate-500">Male: <span class="font-bold text-blue-700">{{ $statsData['summary_stats']['gender_breakdown']['male'] ?? 0 }}</span></div>
                <div class="text-xs text-slate-500">Female: <span class="font-bold text-pink-700">{{ $statsData['summary_stats']['gender_breakdown']['female'] ?? 0 }}</span></div>
                <div class="text-xs text-slate-500">Other: <span class="font-bold text-purple-700">{{ $statsData['summary_stats']['gender_breakdown']['other'] ?? 0 }}</span></div>
            </div>
            <div class="text-sm text-slate-500 font-medium">Gender Distribution</div>
        </div>

        <!-- Registration Status -->
        <div class="bg-white rounded-2xl shadow-lg flex flex-col items-center p-5 border-t-4 border-rose-400 stat-card card-hover animate-fade-in-up animate-delay-5">
            <span class="inline-flex items-center justify-center w-12 h-12 bg-rose-100 rounded-full mb-3 shadow-lg">
                <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </span>
            <div class="flex flex-col items-center mb-1">
                <div class="text-xs text-slate-500">Pending: <span class="font-bold text-rose-700">{{ $statsData['summary_stats']['status_breakdown']['pending'] ?? 0 }}</span></div>
                <div class="text-xs text-slate-500">Approved: <span class="font-bold text-emerald-700">{{ $statsData['summary_stats']['status_breakdown']['approved'] ?? 0 }}</span></div>
                <div class="text-xs text-slate-500">Declined: <span class="font-bold text-rose-700">{{ $statsData['summary_stats']['status_breakdown']['declined'] ?? 0 }}</span></div>
            </div>
            <div class="text-sm text-slate-500 font-medium">Registration Status</div>
        </div>
        
        <!-- Participant Types (counts, desc) -->
        <div class="bg-white rounded-2xl shadow-lg p-5 border-t-4 border-indigo-400 stat-card card-hover animate-fade-in-up animate-delay-5">
            <div class="flex items-center gap-3 mb-3">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full shadow-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <h4 class="text-base font-semibold text-slate-700">Participant Types</h4>
            </div>
            <div class="w-full space-y-1">
                @foreach($statsData['summary_stats']['participant_type_counts'] ?? [] as $typeName => $typeCount)
                    @if($typeCount > 0)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600">{{ $typeName }}</span>
                        <span class="font-semibold text-slate-800">{{ $typeCount }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Passwordless Login Stats -->
        <div class="bg-white rounded-2xl shadow-lg p-5 border-t-4 border-violet-400 stat-card card-hover animate-fade-in-up animate-delay-5">
            <div class="flex items-center gap-3 mb-3">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-violet-100 rounded-full shadow-lg">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </span>
                <h4 class="text-base font-semibold text-slate-700">Passwordless Login</h4>
            </div>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="flex items-center justify-between bg-violet-50 rounded p-2">
                    <span class="text-slate-600">Total</span>
                    <span class="font-semibold text-violet-700">{{ $statsData['summary_stats']['passwordless_login_stats']['total'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between bg-emerald-50 rounded p-2">
                    <span class="text-slate-600">Active</span>
                    <span class="font-semibold text-emerald-700">{{ $statsData['summary_stats']['passwordless_login_stats']['active'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between bg-blue-50 rounded p-2">
                    <span class="text-slate-600">Used</span>
                    <span class="font-semibold text-blue-700">{{ $statsData['summary_stats']['passwordless_login_stats']['used'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between bg-rose-50 rounded p-2">
                    <span class="text-slate-600">Expired</span>
                    <span class="font-semibold text-rose-700">{{ $statsData['summary_stats']['passwordless_login_stats']['expired'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Country modal open/close
    const countryCard = document.getElementById('countryCard');
    const countryModal = document.getElementById('countryModal');
    const closeBtns = [document.getElementById('countryModalClose'), document.getElementById('countryModalClose2')];
    if (countryCard && countryModal) {
        countryCard.addEventListener('click', () => countryModal.classList.remove('hidden'));
        closeBtns.forEach(b => b && b.addEventListener('click', () => countryModal.classList.add('hidden')));
        countryModal.addEventListener('click', (e) => {
            if (e.target === countryModal) countryModal.classList.add('hidden');
        });
    }
});
</script>
@endpush

