@extends('layouts.app')

@section('title', 'Travel Manifests')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center justify-between px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center">
        <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
            <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Travel Manifests</h1>
            <div class="text-gray-600 text-lg font-medium">Admin view of participant travel details</div>
        </div>
    </div>
    <div>
        <a href="{{ route('admin.export-manifest') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg shadow-lg transition duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download CSV
        </a>
    </div>
</div>

<!-- Travel Manifests Table -->
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Travel Details</h2>
        <div class="text-sm text-gray-500">{{ $travelDetails->count() }} records found</div>
    </div>

    @if($travelDetails->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flight Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Nights</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($travelDetails as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $detail->participant->user->first_name ?? $detail->participant->user->name }} {{ $detail->participant->user->last_name ?? '' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $detail->participant->user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $detail->participant->conference->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->hotel->name ?? 'No hotel assigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->arrival_date)
                                    <div>{{ date('M d, Y', strtotime($detail->arrival_date)) }}</div>
                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($detail->arrival_date)) }}</div>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->departure_date)
                                    <div>{{ date('M d, Y', strtotime($detail->departure_date)) }}</div>
                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($detail->departure_date)) }}</div>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->flight_info)
                                    <div class="max-w-xs truncate" title="{{ $detail->flight_info }}">
                                        {{ $detail->flight_info }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->extra_nights ?? 0 }} nights
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->participant->organization ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No travel manifests found</h3>
            <p class="mt-1 text-sm text-gray-500">No travel details have been added yet.</p>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('admin.room-allocations') }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
        <div class="p-2 bg-yellow-200 rounded-lg mr-4">
            <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div>
            <h3 class="font-medium text-gray-900">Room Allocations</h3>
            <p class="text-sm text-gray-600">Manage hotel room assignments</p>
        </div>
    </a>

    <a href="{{ route('admin.travel-conflicts') }}" class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
        <div class="p-2 bg-red-200 rounded-lg mr-4">
            <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-medium text-gray-900">Travel Conflicts</h3>
            <p class="text-sm text-gray-600">View and resolve conflicts</p>
        </div>
    </a>

    <a href="{{ route('participants.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
        <div class="p-2 bg-blue-200 rounded-lg mr-4">
            <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-medium text-gray-900">Manage Participants</h3>
            <p class="text-sm text-gray-600">Add or edit participant details</p>
        </div>
    </a>
</div>
@endsection 