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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visa Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Itineraries Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Takeoff Airport</th>
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
                                        <div class="text-sm font-medium text-blue-600 hover:text-blue-800 cursor-pointer underline hover:no-underline transition-all duration-200" 
                                             onclick="openEditModal({{ $detail->participant->id }}, '{{ $detail->participant->user->first_name ?? $detail->participant->user->name }} {{ $detail->participant->user->last_name ?? '' }}')">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($detail->participant && $detail->participant->visa_status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($detail->participant->visa_status === 'approved') bg-green-100 text-green-800
                                        @elseif($detail->participant->visa_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($detail->participant->visa_status === 'issue') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $detail->participant->visa_status)) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($detail->itineraries_status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($detail->itineraries_status === 'approved') bg-green-100 text-green-800
                                        @elseif($detail->itineraries_status === 'n_a') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        @if($detail->itineraries_status === 'n_a')
                                            N/A
                                        @else
                                            {{ ucfirst($detail->itineraries_status) }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->hotel_info)
                                    <div class="max-w-xs truncate" title="{{ $detail->hotel_info }}">
                                        {{ $detail->hotel_info }}
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">No hotel info provided</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->arrival_date)
                                    <div>{{ \Carbon\Carbon::parse($detail->arrival_date)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($detail->arrival_date)->format('g:i A') }}</div>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->departure_date)
                                    <div>{{ \Carbon\Carbon::parse($detail->departure_date)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($detail->departure_date)->format('g:i A') }}</div>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->takeoff_airport)
                                    <div class="max-w-xs truncate" title="{{ $detail->takeoff_airport }}">
                                        {{ $detail->takeoff_airport }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    // Use travel details data for check-in/check-out times
                                    $checkIn = $detail->room_check_in;
                                    $checkOut = $detail->room_check_out;
                                @endphp
                                
                                @if($checkIn || $checkOut)
                                    <div class="space-y-1">
                                        @if($checkIn)
                                            @php
                                                $checkInCarbon = \Carbon\Carbon::parse($checkIn);
                                            @endphp
                                            <div class="flex items-center text-green-700 bg-green-50 px-2 py-1 rounded text-xs">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Check-in:</div>
                                                    <div>{{ $checkInCarbon->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        @if($checkInCarbon->format('H:i') === '00:00')
                                                            12:00 AM
                                                        @else
                                                            {{ $checkInCarbon->format('g:i A') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($checkOut)
                                            @php
                                                $checkOutCarbon = \Carbon\Carbon::parse($checkOut);
                                            @endphp
                                            <div class="flex items-center text-red-700 bg-red-50 px-2 py-1 rounded text-xs">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Check-out:</div>
                                                    <div>{{ $checkOutCarbon->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        @if($checkOutCarbon->format('H:i') === '00:00')
                                                            12:00 AM
                                                        @else
                                                            {{ $checkOutCarbon->format('g:i A') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($detail->flight_info_details)
                                    <div class="max-w-xs truncate" title="{{ $detail->flight_info_details }}">
                                        {{ $detail->flight_info_details }}
                                    </div>
                                @elseif($detail->flight_info)
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
<div class="mt-8">
    <a href="{{ route('participants.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors max-w-md">
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

<!-- Edit Travel Details Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Travel Details</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600">Participant: <span id="modalParticipantName" class="font-medium"></span></p>
                <div id="travel-dates-info" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md hidden">
                    <p class="text-sm text-blue-800">
                        <strong>Travel Dates:</strong> 
                        <span id="arrival-date-display"></span> - 
                        <span id="departure-date-display"></span>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">Check-in and check-out times must be within these travel dates.</p>
                </div>
            </div>
            
            <form id="editTravelForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="visa_status" class="block text-sm font-medium text-gray-700 mb-2">Visa Status</label>
                        <select name="visa_status" id="visa_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Status</option>
                            <option value="required">Required</option>
                            <option value="not_required">Not Required</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="issue">Issue</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="itineraries_status" class="block text-sm font-medium text-gray-700 mb-2">Itineraries Status</label>
                        <select name="itineraries_status" id="itineraries_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="n_a">N/A</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="takeoff_airport" class="block text-sm font-medium text-gray-700 mb-2">Takeoff Airport</label>
                        <input type="text" name="takeoff_airport" id="takeoff_airport" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., JFK, LAX, Heathrow">
                    </div>
                    
                    <div>
                        <label for="flight_info_details" class="block text-sm font-medium text-gray-700 mb-2">Flight Info Details</label>
                        <textarea name="flight_info_details" id="flight_info_details" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Additional flight information..."></textarea>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="hotel_info" class="block text-sm font-medium text-gray-700 mb-2">Hotel Information</label>
                    <textarea name="hotel_info" id="hotel_info" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter hotel details, room information, and any special requirements..."></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="room_check_in" class="block text-sm font-medium text-gray-700 mb-2">Room Check-in</label>
                        <input type="datetime-local" name="room_check_in" id="room_check_in" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="validateCheckInOut()">
                        <div id="check_in_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label for="room_check_out" class="block text-sm font-medium text-gray-700 mb-2">Room Check-out</label>
                        <input type="datetime-local" name="room_check_out" id="room_check_out" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="validateCheckInOut()">
                        <div id="check_out_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Update Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentConferenceFilter = '';
let currentHotelFilter = '';
let currentParticipantId = null;

// Modal functions
function openEditModal(participantId, participantName) {
    currentParticipantId = participantId;
    document.getElementById('modalParticipantName').textContent = participantName;
    document.getElementById('editTravelForm').action = `/admin/travel/update-participant-details/${participantId}`;
    
    // Load current data
    loadParticipantData(participantId);
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentParticipantId = null;
}

// Handle form submission
document.getElementById('editTravelForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate check-in/check-out times before submission
    if (!validateCheckInOut()) {
        return; // Stop submission if validation fails
    }
    
    const formData = new FormData(this);
    const url = this.action;
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    // Debug: Check specific datetime values
    console.log('Check-in value:', document.getElementById('room_check_in').value);
    console.log('Check-out value:', document.getElementById('room_check_out').value);
    
    // Clear previous error messages
    clearErrorMessages();
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            closeEditModal();
            // Refresh the page to show updated data
            window.location.reload();
        } else {
            return response.text().then(html => {
                // Parse the response to extract validation errors
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const errorElements = doc.querySelectorAll('.error, .invalid-feedback, [class*="error"]');
                
                if (errorElements.length > 0) {
                    // Display errors in the modal
                    displayErrors(html);
                } else {
                    throw new Error('Update failed');
                }
            });
        }
    })
    .catch(error => {
        console.error('Error updating travel details:', error);
        alert('Failed to update travel details. Please try again.');
    });
});

// Function to clear error messages
function clearErrorMessages() {
    const errorContainer = document.getElementById('error-messages');
    if (errorContainer) {
        errorContainer.innerHTML = '';
        errorContainer.classList.add('hidden');
    }
    
    // Remove error styling from form fields
    const formFields = document.querySelectorAll('#editTravelForm input, #editTravelForm textarea, #editTravelForm select');
    formFields.forEach(field => {
        field.classList.remove('border-red-500', 'border-red-300');
        field.classList.add('border-gray-300');
    });
}

// Function to display validation errors
function displayErrors(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    
    // Look for error messages in the response
    const errorMessages = [];
    
    // Check for Laravel validation errors
    const errorElements = doc.querySelectorAll('.error, .invalid-feedback, [class*="error"]');
    errorElements.forEach(element => {
        if (element.textContent.trim()) {
            errorMessages.push(element.textContent.trim());
        }
    });
    
    // Check for session error messages
    const sessionErrors = doc.querySelectorAll('.alert-danger, .alert-error');
    sessionErrors.forEach(element => {
        if (element.textContent.trim()) {
            errorMessages.push(element.textContent.trim());
        }
    });
    
    if (errorMessages.length > 0) {
        // Create or update error container
        let errorContainer = document.getElementById('error-messages');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'error-messages';
            errorContainer.className = 'mb-4 p-4 bg-red-50 border border-red-200 rounded-md';
            
            // Insert after the participant name
            const participantDiv = document.querySelector('.mb-4');
            participantDiv.insertAdjacentElement('afterend', errorContainer);
        }
        
        errorContainer.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            ${errorMessages.map(error => `<li>${error}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        `;
        errorContainer.classList.remove('hidden');
    }
}

function loadParticipantData(participantId) {
    // Fetch current participant data
    fetch(`/admin/travel/get-participant-data/${participantId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Debug: Log received data
                console.log('Received travel details data:', data.travel_details);
                console.log('Room check-in raw:', data.travel_details.room_check_in);
                console.log('Room check-out raw:', data.travel_details.room_check_out);
                
                document.getElementById('visa_status').value = data.participant.visa_status || '';
                document.getElementById('itineraries_status').value = data.travel_details.itineraries_status || '';
                document.getElementById('takeoff_airport').value = data.travel_details.takeoff_airport || '';
                document.getElementById('flight_info_details').value = data.travel_details.flight_info_details || '';
                document.getElementById('hotel_info').value = data.travel_details.hotel_info || '';
                
                // Format datetime-local inputs
                if (data.travel_details.room_check_in) {
                    const checkInDate = new Date(data.travel_details.room_check_in);
                    // Format for datetime-local input (YYYY-MM-DDTHH:MM)
                    const year = checkInDate.getFullYear();
                    const month = String(checkInDate.getMonth() + 1).padStart(2, '0');
                    const day = String(checkInDate.getDate()).padStart(2, '0');
                    const hours = String(checkInDate.getHours()).padStart(2, '0');
                    const minutes = String(checkInDate.getMinutes()).padStart(2, '0');
                    const formattedValue = `${year}-${month}-${day}T${hours}:${minutes}`;
                    console.log('Formatted check-in value:', formattedValue);
                    document.getElementById('room_check_in').value = formattedValue;
                }
                if (data.travel_details.room_check_out) {
                    const checkOutDate = new Date(data.travel_details.room_check_out);
                    // Format for datetime-local input (YYYY-MM-DDTHH:MM)
                    const year = checkOutDate.getFullYear();
                    const month = String(checkOutDate.getMonth() + 1).padStart(2, '0');
                    const day = String(checkOutDate.getDate()).padStart(2, '0');
                    const hours = String(checkOutDate.getHours()).padStart(2, '0');
                    const minutes = String(checkOutDate.getMinutes()).padStart(2, '0');
                    const formattedValue = `${year}-${month}-${day}T${hours}:${minutes}`;
                    console.log('Formatted check-out value:', formattedValue);
                    document.getElementById('room_check_out').value = formattedValue;
                }
                
                // Show travel dates if available
                if (data.travel_details.arrival_date || data.travel_details.departure_date) {
                    const travelDatesInfo = document.getElementById('travel-dates-info');
                    const arrivalDisplay = document.getElementById('arrival-date-display');
                    const departureDisplay = document.getElementById('departure-date-display');
                    
                    if (data.travel_details.arrival_date) {
                        const arrivalDate = new Date(data.travel_details.arrival_date);
                        arrivalDisplay.textContent = arrivalDate.toLocaleDateString() + ' ' + arrivalDate.toLocaleTimeString();
                    } else {
                        arrivalDisplay.textContent = 'Not set';
                    }
                    
                    if (data.travel_details.departure_date) {
                        const departureDate = new Date(data.travel_details.departure_date);
                        departureDisplay.textContent = departureDate.toLocaleDateString() + ' ' + departureDate.toLocaleTimeString();
                    } else {
                        departureDisplay.textContent = 'Not set';
                    }
                    
                    travelDatesInfo.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error loading participant data:', error);
        });
}

// Client-side validation for check-in/check-out times
function validateCheckInOut() {
    const checkInInput = document.getElementById('room_check_in');
    const checkOutInput = document.getElementById('room_check_out');
    const checkInError = document.getElementById('check_in_error');
    const checkOutError = document.getElementById('check_out_error');
    
    // Clear previous errors
    checkInError.classList.add('hidden');
    checkOutError.classList.add('hidden');
    checkInInput.classList.remove('border-red-500');
    checkOutInput.classList.remove('border-red-500');
    
    const checkInValue = checkInInput.value;
    const checkOutValue = checkOutInput.value;
    
    if (!checkInValue && !checkOutValue) {
        return; // No validation needed if both are empty
    }
    
    // Get travel dates from the display
    const arrivalDisplay = document.getElementById('arrival-date-display').textContent;
    const departureDisplay = document.getElementById('departure-date-display').textContent;
    
    if (arrivalDisplay === 'Not set' && departureDisplay === 'Not set') {
        return; // No validation possible without travel dates
    }
    
    let hasErrors = false;
    
    // Validate check-in
    if (checkInValue) {
        const checkInDate = new Date(checkInValue);
        
        if (arrivalDisplay !== 'Not set') {
            const arrivalDate = new Date(arrivalDisplay);
            if (checkInDate < arrivalDate) {
                checkInError.textContent = 'Check-in time cannot be before arrival time';
                checkInError.classList.remove('hidden');
                checkInInput.classList.add('border-red-500');
                hasErrors = true;
            }
        }
        
        if (departureDisplay !== 'Not set') {
            const departureDate = new Date(departureDisplay);
            if (checkInDate > departureDate) {
                checkInError.textContent = 'Check-in time cannot be after departure time';
                checkInError.classList.remove('hidden');
                checkInInput.classList.add('border-red-500');
                hasErrors = true;
            }
        }
    }
    
    // Validate check-out
    if (checkOutValue) {
        const checkOutDate = new Date(checkOutValue);
        
        if (arrivalDisplay !== 'Not set') {
            const arrivalDate = new Date(arrivalDisplay);
            if (checkOutDate < arrivalDate) {
                checkOutError.textContent = 'Check-out time cannot be before arrival time';
                checkOutError.classList.remove('hidden');
                checkOutInput.classList.add('border-red-500');
                hasErrors = true;
            }
        }
        
        if (departureDisplay !== 'Not set') {
            const departureDate = new Date(departureDisplay);
            if (checkOutDate > departureDate) {
                checkOutError.textContent = 'Check-out time cannot be after departure time';
                checkOutError.classList.remove('hidden');
                checkOutInput.classList.add('border-red-500');
                hasErrors = true;
            }
        }
    }
    
    // Validate check-in vs check-out
    if (checkInValue && checkOutValue) {
        const checkInDate = new Date(checkInValue);
        const checkOutDate = new Date(checkOutValue);
        
        if (checkInDate >= checkOutDate) {
            checkOutError.textContent = 'Check-out time must be after check-in time';
            checkOutError.classList.remove('hidden');
            checkOutInput.classList.add('border-red-500');
            hasErrors = true;
        }
    }
    
    return !hasErrors;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeEditModal();
    }
});

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
