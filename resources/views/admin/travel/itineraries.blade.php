@extends('layouts.app')

@section('title', 'Itineraries')

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
            <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Itineraries</h1>
            <div class="text-gray-600 text-lg font-medium">Admin view of participant travel details</div>
        </div>
    </div>
    <div>
        <a href="{{ route('admin.export-itinerary') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg shadow-lg transition duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download CSV
        </a>
    </div>
</div>

<!-- Itineraries Table -->
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Travel Details</h2>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">{{ $travelDetails->count() }} records found</div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Conference:</span>
                    <div class="relative">
                        <input type="text" 
                               id="conferenceSearch" 
                               placeholder="Search conferences..." 
                               class="text-sm border border-gray-300 rounded px-3 py-2 min-w-[200px] pr-8"
                               onkeyup="filterConferenceOptions()"
                               onfocus="showConferenceDropdown()"
                               onblur="hideConferenceDropdown()">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div id="conferenceDropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded shadow-lg hidden max-h-60 overflow-y-auto">
                            <div class="p-2 text-xs text-gray-500 border-b">All Conferences</div>
                            <div class="p-2 hover:bg-gray-100 cursor-pointer" onclick="selectConference('')">All Conferences</div>
                            @foreach($conferences as $conference)
                                <div class="p-2 hover:bg-gray-100 cursor-pointer conference-option" data-conference="{{ $conference->name }}" onclick="selectConference('{{ $conference->name }}')">
                                    {{ $conference->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Hotel:</span>
                    <select class="text-sm border border-gray-300 rounded px-3 py-2 min-w-[150px]" onchange="filterByHotel(this.value)">
                        <option value="">All Hotels</option>
                        @foreach($travelDetails->pluck('hotel.name')->filter()->unique() as $hotelName)
                            <option value="{{ $hotelName }}">{{ $hotelName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($travelDetails->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel & Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Check-in/out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flight Info</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($travelDetails as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    @if($detail->participant && $detail->participant->user)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $detail->participant->user->first_name ?? $detail->participant->user->name }} {{ $detail->participant->user->last_name ?? '' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $detail->participant->user->email }}</div>
                                    @else
                                        <div class="text-sm font-medium text-gray-500 italic">
                                            Participant not found
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            ID: {{ $detail->participant_id ?? 'N/A' }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $detail->participant && $detail->participant->conference ? $detail->participant->conference->name : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>
                                    <div class="font-medium flex items-center">
                                        @if($detail->hotel)
                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $detail->hotel->name }}
                                        @else
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                            No hotel assigned
                                        @endif
                                    </div>
                                    @if($detail->room)
                                        <div class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded mt-1 inline-block">
                                            <span class="font-medium">Room:</span> {{ $detail->room->room_number ?? 'N/A' }}
                                            @if($detail->room->roomType)
                                                <span class="text-gray-500">({{ $detail->room->roomType->name }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
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
                                @php
                                    // Always use room allocation data as primary source for check-in/check-out times
                                    $roomAllocation = $detail->participant ? $detail->participant->roomAllocations->first() : null;
                                    $checkIn = $roomAllocation ? $roomAllocation->check_in : null;
                                    $checkOut = $roomAllocation ? $roomAllocation->check_out : null;
                                @endphp
                                
                                @if($checkIn || $checkOut)
                                    <div class="space-y-1">
                                        @if($checkIn)
                                            <div class="flex items-center text-green-700 bg-green-50 px-2 py-1 rounded text-xs">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Check-in:</div>
                                                    <div>{{ date('M d, Y', strtotime($checkIn)) }}</div>
                                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($checkIn)) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($checkOut)
                                            <div class="flex items-center text-red-700 bg-red-50 px-2 py-1 rounded text-xs">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Check-out:</div>
                                                    <div>{{ date('M d, Y', strtotime($checkOut)) }}</div>
                                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($checkOut)) }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Not specified</span>
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
            <h3 class="mt-2 text-sm font-medium text-gray-900">No itineraries found</h3>
            <p class="mt-1 text-sm text-gray-500">No travel details have been added yet.</p>
            <div class="mt-4">
                <a href="{{ route('participants.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Participants
                </a>
            </div>
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

<script>
let currentConferenceFilter = '';
let currentHotelFilter = '';

function filterByConference(conferenceName) {
    currentConferenceFilter = conferenceName;
    applyFilters();
}

function filterByHotel(hotelName) {
    currentHotelFilter = hotelName;
    applyFilters();
}

function selectConference(conferenceName) {
    document.getElementById('conferenceSearch').value = conferenceName;
    currentConferenceFilter = conferenceName;
    hideConferenceDropdown();
    applyFilters();
}

function showConferenceDropdown() {
    document.getElementById('conferenceDropdown').classList.remove('hidden');
}

function hideConferenceDropdown() {
    setTimeout(() => {
        document.getElementById('conferenceDropdown').classList.add('hidden');
    }, 200);
}

function filterConferenceOptions() {
    const searchTerm = document.getElementById('conferenceSearch').value.toLowerCase();
    const options = document.querySelectorAll('.conference-option');
    
    options.forEach(option => {
        const conferenceName = option.getAttribute('data-conference').toLowerCase();
        if (conferenceName.includes(searchTerm)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Show dropdown when typing
    if (searchTerm.length > 0) {
        showConferenceDropdown();
    }
}

function applyFilters() {
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const conferenceCell = row.querySelector('td:nth-child(2)');
        const hotelCell = row.querySelector('td:nth-child(3)');
        
        let showRow = true;
        
        // Check conference filter
        if (currentConferenceFilter !== '') {
            const conferenceText = conferenceCell ? conferenceCell.textContent.trim() : '';
            if (!conferenceText.includes(currentConferenceFilter)) {
                showRow = false;
            }
        }
        
        // Check hotel filter
        if (currentHotelFilter !== '' && showRow) {
            const hotelText = hotelCell ? hotelCell.textContent.trim() : '';
            if (!hotelText.includes(currentHotelFilter)) {
                showRow = false;
            }
        }
        
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update record count
    document.querySelector('.text-sm.text-gray-500').textContent = `${visibleCount} records found`;
}
</script>
@endsection
