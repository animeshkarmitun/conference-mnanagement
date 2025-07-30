@extends('layouts.app')

@section('title', 'Event Coordinator Dashboard')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-blue-100 via-blue-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-blue-200">
    <div class="flex items-center justify-center w-16 h-16 bg-blue-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-blue-800 tracking-tight mb-1">Event Coordinator Dashboard</h1>
        <div class="text-gray-600 text-lg font-medium">Manage travel logistics and participant coordination</div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Participants</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalParticipants }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">With Travel Details</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $participantsWithTravel }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Room Allocations</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $participantsWithRooms }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Conferences</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $activeConferences }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Recent Travel Details -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Travel Details</h2>
            <a href="{{ route('event-coordinator.travel-manifests') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        
        @if($recentTravelDetails->count() > 0)
            <div class="space-y-4">
                @foreach($recentTravelDetails as $detail)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $detail->participant->user->first_name ?? $detail->participant->user->name }} {{ $detail->participant->user->last_name ?? '' }}
                            </p>
                            <p class="text-sm text-gray-600">{{ $detail->hotel->name ?? 'No hotel assigned' }}</p>
                            @if($detail->arrival_date)
                                <p class="text-xs text-gray-500">Arrives: {{ date('M d, Y H:i', strtotime($detail->arrival_date)) }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $detail->participant->conference->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No recent travel details found.</p>
        @endif
    </div>

    <!-- Upcoming Room Allocations -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Upcoming Room Allocations</h2>
            <a href="{{ route('event-coordinator.travel-manifests') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
        </div>
        
        @if($upcomingRooms->count() > 0)
            <div class="space-y-4">
                @foreach($upcomingRooms as $room)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $room->participant->user->first_name ?? $room->participant->user->name }} {{ $room->participant->user->last_name ?? '' }}
                            </p>
                            <p class="text-sm text-gray-600">{{ $room->hotel->name ?? 'No hotel assigned' }}</p>
                            @if($room->room_number)
                                <p class="text-xs text-gray-500">Room: {{ $room->room_number }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($room->check_in)
                                <p class="text-sm font-medium text-gray-900">{{ date('M d', strtotime($room->check_in)) }}</p>
                                <p class="text-xs text-gray-500">{{ date('H:i', strtotime($room->check_in)) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No upcoming room allocations found.</p>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('event-coordinator.travel-manifests') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            <div class="p-2 bg-blue-200 rounded-lg mr-4">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">View Travel Manifests</h3>
                <p class="text-sm text-gray-600">Manage and export travel details</p>
            </div>
        </a>

        <a href="{{ route('event-coordinator.export-manifest') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
            <div class="p-2 bg-green-200 rounded-lg mr-4">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">Export Manifest</h3>
                <p class="text-sm text-gray-600">Download travel data as CSV</p>
            </div>
        </a>

        <a href="{{ route('participants.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
            <div class="p-2 bg-purple-200 rounded-lg mr-4">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-gray-900">View Participants</h3>
                <p class="text-sm text-gray-600">Manage participant information</p>
            </div>
        </a>
    </div>
</div>
@endsection 