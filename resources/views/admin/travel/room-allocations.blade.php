@extends('layouts.app')

@section('title', 'Room Allocations')

@push('styles')
<style>
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--bootstrap-5 .select2-selection:focus {
        border-color: #eab308;
        box-shadow: 0 0 0 0.2rem rgba(234, 179, 8, 0.25);
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@section('content')
<div class="w-full py-6 px-4">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Room Allocations</h2>
            
            <!-- Conference Selection -->
            <div class="mb-6">
                <label for="conference-select" class="block text-sm font-medium text-gray-700 mb-2">Select Conference</label>
                <div class="text-sm text-blue-600 mb-2">
                    <i class="fas fa-info-circle"></i> Only participants with "National" or "International" travel intent are shown
                </div>
                <select id="conference-select" name="conference_id" class="w-full max-w-md form-select">
                    <option value="">All Conferences</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}" {{ $selectedConferenceId == $conference->id ? 'selected' : '' }}>
                            {{ $conference->name }} ({{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Success/Error/Info Messages -->
            <div id="message-container" class="hidden mb-4">
                <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"></div>
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
                <div id="info-message" class="hidden bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded"></div>
            </div>
            
            @if($participants->count() == 0)
                <div class="text-center py-12">
                    <div class="text-gray-500 text-lg mb-4">
                        <i class="fas fa-bed text-4xl mb-4"></i>
                        <p>No participants with travel intent found</p>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Only participants with "National" or "International" travel intent are shown in room allocations.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participant
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Travel Intent
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hotel <span class="text-red-500">*</span>
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Room Number
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Arrival Date
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Departure Date
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check-in Time
                                </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check-out Time
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($participants as $participant)
                            <tr id="participant-row-{{ $participant->id }}" 
                                data-travel-intent="{{ $participant->travel_intent ?? 'national' }}"
                                data-arrival-date="{{ optional($participant->travelDetails)->arrival_date }}"
                                data-departure-date="{{ optional($participant->travelDetails)->departure_date }}">
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                                    </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                    @if($participant->travel_intent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $participant->travel_intent === 'international' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($participant->travel_intent) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <form id="room-allocation-form-{{ $participant->id }}" class="room-allocation-form" data-participant-id="{{ $participant->id }}">
                                        @csrf
                                        <select name="hotel_id" class="hotel-select w-full text-sm auto-save-input form-select" data-participant-id="{{ $participant->id }}" required>
                                                <option value="">Select Hotel</option>
                                                @foreach($hotels as $hotel)
                                                    <option value="{{ $hotel->id }}" 
                                                            data-address="{{ $hotel->address }}"
                                                            data-capacity="{{ $hotel->room_capacity }}"
                                                            {{ optional(optional($participant->roomAllocation)->hotel)->id == $hotel->id ? 'selected' : '' }}>
                                                        {{ $hotel->name }}
                                                        @if($hotel->address) - {{ $hotel->address }} @endif
                                                        @if($hotel->room_capacity) - {{ $hotel->room_capacity }} rooms @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                    </form>
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <select name="room_number" form="room-allocation-form-{{ $participant->id }}" class="room-select w-full text-sm auto-save-input form-select" data-participant-id="{{ $participant->id }}" data-hotel-id="{{ optional(optional($participant->roomAllocation)->hotel)->id }}">
                                        <option value="">Select Room</option>
                                        @if(optional($participant->roomAllocation)->hotel)
                                            @foreach(optional($participant->roomAllocation)->hotel->rooms->where('is_available', true) as $room)
                                                <option value="{{ $room->room_number }}" {{ optional($participant->roomAllocation)->room_number == $room->room_number ? 'selected' : '' }}>
                                                    {{ $room->room_number }} - {{ $room->room_type }} ({{ $room->beds }} bed{{ $room->beds > 1 ? 's' : '' }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ optional($participant->travelDetails)->arrival_date ? \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('M d, Y g:i A') : '-' }}
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ optional($participant->travelDetails)->departure_date ? \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('M d, Y g:i A') : '-' }}
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="datetime-local" name="check_in" form="room-allocation-form-{{ $participant->id }}" value="{{ optional($participant->roomAllocation)->check_in ? \Carbon\Carbon::parse($participant->roomAllocation->check_in)->format('Y-m-d\TH:i') : '' }}" class="w-full text-sm rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 check-in-input auto-save-input" data-participant-id="{{ $participant->id }}">
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="datetime-local" name="check_out" form="room-allocation-form-{{ $participant->id }}" value="{{ optional($participant->roomAllocation)->check_out ? \Carbon\Carbon::parse($participant->roomAllocation->check_out)->format('Y-m-d\TH:i') : '' }}" class="w-full text-sm rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 check-out-input auto-save-input" data-participant-id="{{ $participant->id }}">
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            </div>
        </div>
    </div>

<script>
// Room Allocations with Auto-Save and Conference Filtering - Version 4.0
(function() {
    'use strict';
    
    let saveTimeouts = {};
    let isSaving = {};
    
    // Wait for DOM to be ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    ready(function() {
        console.log('Room allocation script loaded - Version 5.0');
        
        // Debug: Check if data is available
        console.log('Conferences count:', {{ $conferences->count() }});
        console.log('Hotels count:', {{ $hotels->count() }});
        console.log('Participants count:', {{ $participants->count() }});
        
        // Wait a bit for DOM to be fully ready
        setTimeout(function() {
            console.log('jQuery version:', $.fn.jquery);
            console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');
            
            // Check if Select2 is available
            if (typeof $.fn.select2 === 'undefined') {
                console.error('Select2 is not loaded, falling back to regular select');
                // Fallback to regular select behavior
                $('#conference-select').addClass('form-select');
                return;
            }
            
            // Initialize Select2 for conference select
            if ($('#conference-select').length) {
                try {
                    $('#conference-select').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Search conferences...',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    console.log('Conference select initialized');
                } catch (error) {
                    console.error('Error initializing conference select:', error);
                }
            }
            
            // Conference selection handler
            $('#conference-select').on('change', function() {
                const conferenceId = this.value;
                const url = new URL(window.location);
                
                if (conferenceId) {
                    url.searchParams.set('conference_id', conferenceId);
                } else {
                    url.searchParams.delete('conference_id');
                }
                
                window.location.href = url.toString();
            });
            
            // Initialize Select2 for hotel selects
            if ($('.hotel-select').length) {
                try {
                    $('.hotel-select').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select hotel...',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    console.log('Hotel selects initialized:', $('.hotel-select').length);
                } catch (error) {
                    console.error('Error initializing hotel selects:', error);
                    // Fallback to regular select
                    $('.hotel-select').addClass('form-select');
                }
            }
            
            // Initialize Select2 for room selects
            if ($('.room-select').length) {
                try {
                    $('.room-select').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select room...',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    // Add change event for room selects after initialization
                    $('.room-select').on('change', function() {
                        const participantId = $(this).data('participant-id');
                        console.log('Room select change event for participant:', participantId, 'Value:', $(this).val());
                        if (participantId) {
                            setupAutoSave(this);
                        }
                    });
                    
                    console.log('Room selects initialized:', $('.room-select').length);
                } catch (error) {
                    console.error('Error initializing room selects:', error);
                    // Fallback to regular select
                    $('.room-select').addClass('form-select');
                }
            }
        }, 100);
        
        // Hotel change handler - load rooms for selected hotel
        $(document).on('change', '.hotel-select', function() {
            const participantId = $(this).data('participant-id');
            const hotelId = $(this).val();
            const roomSelect = $(`select[name="room_number"][data-participant-id="${participantId}"]`);
            
            // Clear room selection
            roomSelect.empty().append('<option value="">Select Room</option>');
            
            if (hotelId) {
                // Show loading state
                roomSelect.append('<option value="" disabled>Loading rooms...</option>');
                roomSelect.prop('disabled', true);
                
                // Fetch rooms for selected hotel
                fetch(`/admin/hotels/${hotelId}/rooms`)
                    .then(response => response.json())
                    .then(data => {
                        roomSelect.prop('disabled', false);
                        roomSelect.empty().append('<option value="">Select Room</option>');
                        
                        if (data.success && data.rooms.length > 0) {
                            data.rooms.forEach(room => {
                                let optionText = `${room.room_number} - ${room.room_type} (${room.beds} bed${room.beds > 1 ? 's' : ''})`;
                                roomSelect.append(`<option value="${room.room_number}">${optionText}</option>`);
                            });
                            
                            // Re-initialize Select2 for the room select
                            roomSelect.select2({
                                theme: 'bootstrap-5',
                                placeholder: 'Select room...',
                                allowClear: true,
                                width: '100%'
                            });
                        } else {
                            roomSelect.append('<option value="" disabled>No rooms available for this hotel</option>');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading rooms:', error);
                        roomSelect.prop('disabled', false);
                        roomSelect.empty().append('<option value="">Error loading rooms</option>');
                    });
            }
            
            // Trigger auto-save
            if (participantId) {
                clearTimeout(saveTimeouts[participantId]);
                saveTimeouts[participantId] = setTimeout(function() {
                    saveParticipantData(participantId);
                }, 1000);
            }
            
            // Check for conflicts immediately when hotel is selected and room is already chosen
            const hotelRoomSelect = document.querySelector(`select[name="room_number"][data-participant-id="${participantId}"]`);
            const hotelCheckInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
            const hotelCheckOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
            
            if (hotelRoomSelect && hotelRoomSelect.value && hotelCheckInInput && hotelCheckInInput.value) {
                const checkOutTime = hotelCheckOutInput?.value || new Date(new Date(hotelCheckInInput.value).getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
                console.log('Checking conflicts immediately for hotel selection');
                validateRoomConflicts(participantId, hotelId, hotelRoomSelect.value, hotelCheckInInput.value, checkOutTime);
            }
        });
        
        // Enhanced auto-save functionality for all inputs
        function setupAutoSave(input) {
            // Handle both jQuery objects and DOM elements
            let element = input;
            if (input.jquery) {
                // It's a jQuery object, get the DOM element
                element = input[0];
            }
            
            const participantId = element.dataset?.participantId || 
                                 element.getAttribute('data-participant-id') ||
                                 element.getAttribute('form')?.replace('room-allocation-form-', '');
            
            console.log('setupAutoSave called for input:', element, 'Participant ID:', participantId);
            
            if (!participantId) {
                console.error('Participant ID not found for input:', element);
                return;
            }
            
            // Clear existing timeout for this participant
            if (saveTimeouts[participantId]) {
                clearTimeout(saveTimeouts[participantId]);
            }
            
            // Validate before saving
            if (!validateParticipantData(participantId)) {
                console.log('Validation failed for participant:', participantId);
                return;
            }
            
            console.log('Setting up auto-save for participant:', participantId);
            
            // Set new timeout for auto-save
            saveTimeouts[participantId] = setTimeout(function() {
                console.log('Executing auto-save for participant:', participantId);
                saveParticipantData(participantId);
            }, 1000); // 1 second delay
        }
        
        // Setup auto-save for all inputs
        document.querySelectorAll('.auto-save-input').forEach(function(input) {
            // Handle different input types
            if (input.type === 'text' || input.type === 'datetime-local') {
                input.addEventListener('input', function() {
                    setupAutoSave(this);
                });
            }
            
            input.addEventListener('change', function() {
                setupAutoSave(this);
            });
        });
        
        // Setup auto-save for Select2 elements
        $(document).on('select2:select select2:unselect', '.hotel-select, .room-select', function() {
            const participantId = $(this).data('participant-id');
            console.log('Select2 event triggered for participant:', participantId, 'Value:', $(this).val());
            if (participantId) {
                // Pass the select element itself, not this[0]
                setupAutoSave(this);
            }
        });
        
        // Additional event listeners for room select changes
        $(document).on('change', '.room-select', function() {
            const participantId = $(this).data('participant-id');
            console.log('Room select change event for participant:', participantId, 'Value:', $(this).val());
            if (participantId) {
                // Pass the select element itself, not this[0]
                setupAutoSave(this);
                
                // Check for conflicts immediately when room is selected
                const roomHotelSelect = document.querySelector(`select[name="hotel_id"][data-participant-id="${participantId}"]`);
                const roomCheckInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
                const roomCheckOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
                
                if (roomHotelSelect && roomHotelSelect.value && roomCheckInInput && roomCheckInInput.value) {
                    const checkOutTime = roomCheckOutInput?.value || new Date(new Date(roomCheckInInput.value).getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
                    console.log('Checking conflicts immediately for room selection');
                    validateRoomConflicts(participantId, roomHotelSelect.value, $(this).val(), roomCheckInInput.value, checkOutTime);
                }
            }
        });
        
        // Real-time validation for check-in/check-out times
        document.querySelectorAll('.check-in-input, .check-out-input').forEach(function(input) {
            input.addEventListener('change', function() {
                    const participantId = this.dataset.participantId;
                validateTimeInputs(participantId);
                
                // Check for conflicts immediately when check-in time is selected
                if (this.classList.contains('check-in-input') && this.value) {
                    console.log('Check-in time selected for participant:', participantId, 'Value:', this.value);
                    const checkinHotelSelect = document.querySelector(`select[name="hotel_id"][data-participant-id="${participantId}"]`);
                    const checkinRoomSelect = document.querySelector(`select[name="room_number"][data-participant-id="${participantId}"]`);
                    const checkinCheckOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
                    
                    console.log('Check-in conflict check - Hotel:', checkinHotelSelect?.value, 'Room:', checkinRoomSelect?.value);
                    
                    if (checkinHotelSelect && checkinRoomSelect && checkinHotelSelect.value && checkinRoomSelect.value) {
                        // Use check-out time if available, otherwise use check-in + 1 day as temporary end time
                        const checkOutTime = checkinCheckOutInput?.value || new Date(new Date(this.value).getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
                        console.log('Checking conflicts immediately for check-in time selection - CheckOut:', checkOutTime);
                        validateRoomConflicts(participantId, checkinHotelSelect.value, checkinRoomSelect.value, this.value, checkOutTime);
                    } else {
                        console.log('Cannot check conflicts - missing hotel or room selection');
                    }
                }
                
                // Check for conflicts immediately when check-out time is selected
                if (this.classList.contains('check-out-input') && this.value) {
                    console.log('Check-out time selected for participant:', participantId, 'Value:', this.value);
                    const checkoutHotelSelect = document.querySelector(`select[name="hotel_id"][data-participant-id="${participantId}"]`);
                    const checkoutRoomSelect = document.querySelector(`select[name="room_number"][data-participant-id="${participantId}"]`);
                    const checkoutCheckInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
                    
                    console.log('Check-out conflict check - Hotel:', checkoutHotelSelect?.value, 'Room:', checkoutRoomSelect?.value, 'CheckIn:', checkoutCheckInInput?.value);
                    
                    if (checkoutHotelSelect && checkoutRoomSelect && checkoutHotelSelect.value && checkoutRoomSelect.value && checkoutCheckInInput && checkoutCheckInInput.value) {
                        console.log('Checking conflicts immediately for check-out time selection');
                        validateRoomConflicts(participantId, checkoutHotelSelect.value, checkoutRoomSelect.value, checkoutCheckInInput.value, this.value);
                    } else {
                        console.log('Cannot check conflicts - missing hotel, room, or check-in selection');
                    }
                }
            });
            
            // Add real-time validation on input
            input.addEventListener('input', function() {
                const participantId = this.dataset.participantId;
                // Only validate if both fields have values
                const checkInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
                const checkOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
                
                if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
                    validateTimeInputs(participantId);
                    
                    // Also check for room conflicts in real-time
                    const realtimeHotelSelect = document.querySelector(`select[name="hotel_id"][data-participant-id="${participantId}"]`);
                    const realtimeRoomSelect = document.querySelector(`select[name="room_number"][data-participant-id="${participantId}"]`);
                    
                    if (realtimeHotelSelect && realtimeRoomSelect && realtimeHotelSelect.value && realtimeRoomSelect.value) {
                        validateRoomConflicts(participantId, realtimeHotelSelect.value, realtimeRoomSelect.value, checkInInput.value, checkOutInput.value);
                    }
                }
            });
        });
        
        function validateParticipantData(participantId) {
            const checkInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
            const checkOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
            const hotelSelect = document.querySelector(`select[name="hotel_id"][data-participant-id="${participantId}"]`);
            const roomSelect = document.querySelector(`select[name="room_number"][data-participant-id="${participantId}"]`);
            
            // Validate hotel selection
            if (hotelSelect && !hotelSelect.value) {
                showError('Please select a hotel before saving.');
                return false;
            }
            
            // Validate time inputs
            if (checkInInput && checkOutInput) {
                if (!validateTimeInputs(participantId)) {
                    return false;
                }
            }
            
            // Check for room conflicts
            if (hotelSelect && roomSelect && hotelSelect.value && roomSelect.value) {
                console.log('Calling room conflict validation for participant:', participantId);
                if (!validateRoomConflicts(participantId, hotelSelect.value, roomSelect.value, checkInInput?.value, checkOutInput?.value)) {
                    console.log('Room conflict validation failed for participant:', participantId);
                    return false;
                }
            }
            
            return true;
        }
        
        function validateTimeInputs(participantId) {
            const checkInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
            const checkOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
            
            if (!checkInInput || !checkOutInput) {
                return true;
            }
            
            const checkInValue = checkInInput.value;
            const checkOutValue = checkOutInput.value;
            
            // Reset border colors
            checkInInput.style.borderColor = '';
            checkOutInput.style.borderColor = '';
            
            // Basic time validation
                        if (checkInValue && checkOutValue) {
                            const checkInDate = new Date(checkInValue);
                            const checkOutDate = new Date(checkOutValue);
                            
                            if (checkInDate >= checkOutDate) {
                    checkInInput.style.borderColor = '#ef4444';
                    checkOutInput.style.borderColor = '#ef4444';
                                showError('Check-out time must be after check-in time.');
                    return false;
                }
                
                // Check if stay is too long (more than 30 days)
                const timeDiff = checkOutDate - checkInDate;
                const daysDiff = timeDiff / (1000 * 60 * 60 * 24);
                if (daysDiff > 30) {
                    showError('Room stay cannot exceed 30 days.');
                    return false;
                }
                            }
                            
                            // Check if check-in is in the past
            if (checkInValue) {
                const checkInDate = new Date(checkInValue);
                            const now = new Date();
                
                            if (checkInDate < now) {
                    checkInInput.style.borderColor = '#ef4444';
                                showError('Check-in time cannot be in the past.');
                    return false;
                }
            }
            
            // Check travel dates for participants (regardless of travel intent)
            const participantRow = document.getElementById(`participant-row-${participantId}`);
            if (participantRow) {
                const arrivalDate = participantRow.dataset.arrivalDate;
                const departureDate = participantRow.dataset.departureDate;
                
                // If participant has travel dates, validate against them
                if (arrivalDate || departureDate) {
                    return validateTravelTimes(participantId, checkInValue, checkOutValue);
                }
            }
            
            // Don't hide messages automatically - let them persist until manually dismissed
            // hideMessages();
            return true;
        }
        
        function validateRoomConflicts(participantId, hotelId, roomNumber, checkIn, checkOut) {
            console.log('Checking room conflicts for participant:', participantId, 'Hotel:', hotelId, 'Room:', roomNumber, 'CheckIn:', checkIn, 'CheckOut:', checkOut);
            
            if (!checkIn) {
                console.log('Skipping room conflict check - no check-in time provided');
                return true; // Skip room conflict check if check-in time not set
            }
            
            // If no check-out time provided, use check-in + 1 day as temporary end time
            if (!checkOut) {
                checkOut = new Date(new Date(checkIn).getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
                console.log('Using temporary check-out time:', checkOut);
            }
            
            // Validate that check-in is before check-out
            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            
            if (checkInDate >= checkOutDate) {
                console.log('Invalid time range - check-in must be before check-out');
                showConflictError('Check-in time must be before check-out time.');
                return false;
            }
            
            // Check for conflicts with other participants
            const allParticipants = document.querySelectorAll('[id^="participant-row-"]');
            const conflicts = [];
            
            console.log('Checking against', allParticipants.length, 'participants');
            
            allParticipants.forEach(function(row) {
                const rowParticipantId = row.id.replace('participant-row-', '');
                if (rowParticipantId === participantId) {
                    console.log('Skipping self:', rowParticipantId);
                    return; // Skip self
                }
                
                const rowHotelSelect = document.querySelector(`select[name="hotel_id"][data-participant-id="${rowParticipantId}"]`);
                const rowRoomSelect = document.querySelector(`select[name="room_number"][data-participant-id="${rowParticipantId}"]`);
                const rowCheckIn = document.querySelector(`input[name="check_in"][data-participant-id="${rowParticipantId}"]`);
                const rowCheckOut = document.querySelector(`input[name="check_out"][data-participant-id="${rowParticipantId}"]`);
                
                console.log('Checking participant', rowParticipantId, 'Hotel:', rowHotelSelect?.value, 'Room:', rowRoomSelect?.value, 'CheckIn:', rowCheckIn?.value, 'CheckOut:', rowCheckOut?.value);
                
                if (rowHotelSelect && rowRoomSelect && rowCheckIn && rowCheckOut &&
                    rowHotelSelect.value === hotelId && rowRoomSelect.value === roomNumber &&
                    rowCheckIn.value && rowCheckOut.value) {
                    
                    const rowCheckInDate = new Date(rowCheckIn.value);
                    const rowCheckOutDate = new Date(rowCheckOut.value);
                    const newCheckInDate = new Date(checkIn);
                    const newCheckOutDate = new Date(checkOut);
                    
                    console.log('Comparing times - Row:', rowCheckInDate, 'to', rowCheckOutDate, 'New:', newCheckInDate, 'to', newCheckOutDate);
                    
                    // Check for time overlap - two time ranges overlap if one starts before the other ends
                    // More explicit overlap detection with detailed logging
                    const condition1 = newCheckInDate < rowCheckOutDate;
                    const condition2 = newCheckOutDate > rowCheckInDate;
                    const hasOverlap = condition1 && condition2;
                    
                    console.log('Overlap detection details:');
                    console.log('- newCheckInDate < rowCheckOutDate:', condition1, `(${newCheckInDate} < ${rowCheckOutDate})`);
                    console.log('- newCheckOutDate > rowCheckInDate:', condition2, `(${newCheckOutDate} > ${rowCheckInDate})`);
                    console.log('- Has overlap:', hasOverlap);
                    
                    // Additional overlap scenarios for debugging
                    if (newCheckInDate >= rowCheckInDate && newCheckInDate < rowCheckOutDate) {
                        console.log('- New check-in overlaps with existing booking');
                    }
                    if (newCheckOutDate > rowCheckInDate && newCheckOutDate <= rowCheckOutDate) {
                        console.log('- New check-out overlaps with existing booking');
                    }
                    if (newCheckInDate <= rowCheckInDate && newCheckOutDate >= rowCheckOutDate) {
                        console.log('- New booking completely encompasses existing booking');
                    }
                    if (newCheckInDate >= rowCheckInDate && newCheckOutDate <= rowCheckOutDate) {
                        console.log('- New booking is completely within existing booking');
                    }
                    
                    if (hasOverlap) {
                        const participantName = row.querySelector('td:first-child').textContent.trim();
                        conflicts.push(participantName);
                        console.log('Conflict found with:', participantName);
                    }
                }
            });
            
            console.log('Total conflicts found:', conflicts.length);
            
            if (conflicts.length > 0) {
                showConflictError(`Room ${roomNumber} is already allocated to: ${conflicts.join(', ')} for overlapping dates.`);
                return false;
            }
            
            return true;
        }
        
        function validateTravelTimes(participantId, checkInValue, checkOutValue) {
            const participantRow = document.getElementById(`participant-row-${participantId}`);
            if (!participantRow) return true;
            
            const arrivalDate = participantRow.dataset.arrivalDate;
            const departureDate = participantRow.dataset.departureDate;
            const travelIntent = participantRow.dataset.travelIntent;
            const checkInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
            const checkOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
            
            console.log('Validating travel times for participant:', participantId);
            console.log('Travel intent:', travelIntent);
            console.log('Arrival date:', arrivalDate);
            console.log('Departure date:', departureDate);
            console.log('Check-in value:', checkInValue);
            console.log('Check-out value:', checkOutValue);
            
            // Validate if participant has travel dates (regardless of travel intent)
            if (!arrivalDate && !departureDate) {
                console.log('No travel dates available, skipping travel validation');
                return true;
            }
            
            // Validate check-in against arrival
            if (arrivalDate && checkInValue) {
                const arrival = new Date(arrivalDate);
                const checkIn = new Date(checkInValue);
                
                console.log('Arrival date object:', arrival);
                console.log('Check-in date object:', checkIn);
                
                // Check if dates are valid
                if (isNaN(arrival.getTime()) || isNaN(checkIn.getTime())) {
                    console.log('Invalid date format detected');
                    return true; // Let other validation handle invalid dates
                }
                
                // Check-in should be on or after arrival date
                if (checkIn < arrival) {
                    checkInInput.style.borderColor = '#ef4444';
                    showError('Check-in time cannot be before arrival date.');
                    return false;
                }
                
                // For international travel, check-in should be within 24 hours of arrival
                if (travelIntent === 'international') {
                    const timeDiff = Math.abs(checkIn - arrival);
                    const hoursDiff = timeDiff / (1000 * 60 * 60);
                    
                    if (hoursDiff > 24) {
                        checkInInput.style.borderColor = '#ef4444';
                        showError('For international travel, check-in should be within 24 hours of arrival time.');
                        return false;
                    }
                }
            }
            
            // Validate check-in against departure date
            if (departureDate && checkInValue) {
                const departure = new Date(departureDate);
                const checkIn = new Date(checkInValue);
                
                console.log('Departure date object:', departure);
                console.log('Check-in date object:', checkIn);
                
                // Check if dates are valid
                if (isNaN(departure.getTime()) || isNaN(checkIn.getTime())) {
                    console.log('Invalid date format detected for departure/check-in');
                    return true; // Let other validation handle invalid dates
                }
                
                // Check-in should be on or before departure date
                if (checkIn > departure) {
                    checkInInput.style.borderColor = '#ef4444';
                    showError('Check-in time cannot be after departure time.');
                    return false;
                }
            }
            
            // Validate check-out against departure
            if (departureDate && checkOutValue) {
                const departure = new Date(departureDate);
                const checkOut = new Date(checkOutValue);
                
                console.log('Departure date object:', departure);
                console.log('Check-out date object:', checkOut);
                
                // Check if dates are valid
                if (isNaN(departure.getTime()) || isNaN(checkOut.getTime())) {
                    console.log('Invalid date format detected for departure/check-out');
                    return true; // Let other validation handle invalid dates
                }
                
                // Check-out should be on or before departure date
                if (checkOut > departure) {
                    checkOutInput.style.borderColor = '#ef4444';
                    showError('Check-out time cannot be after departure date.');
                    return false;
                }
                
                // For international travel, check-out should be within 24 hours of departure
                if (travelIntent === 'international') {
                    const timeDiff = Math.abs(checkOut - departure);
                    const hoursDiff = timeDiff / (1000 * 60 * 60);
                    
                    if (hoursDiff > 24) {
                        checkOutInput.style.borderColor = '#ef4444';
                        showError('For international travel, check-out should be within 24 hours of departure time.');
                        return false;
                    }
                }
            }
            
            // Validate that check-in is before check-out
            if (checkInValue && checkOutValue) {
                const checkIn = new Date(checkInValue);
                const checkOut = new Date(checkOutValue);
                
                console.log('Check-in date object:', checkIn);
                console.log('Check-out date object:', checkOut);
                
                // Check if dates are valid
                if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) {
                    console.log('Invalid date format detected for check-in/check-out');
                    return true; // Let other validation handle invalid dates
                }
                
                if (checkIn >= checkOut) {
                    checkInInput.style.borderColor = '#ef4444';
                    checkOutInput.style.borderColor = '#ef4444';
                    showError('Check-out must be after check-in time.');
                    return false;
                }
            }
            
            return true;
        }
        
        // Keep the old function name for backward compatibility
        function validateInternationalTravel(participantId, checkInValue, checkOutValue) {
            return validateTravelTimes(participantId, checkInValue, checkOutValue);
        }
        
        function saveParticipantData(participantId) {
            if (isSaving[participantId]) {
                return; // Already saving
            }
            
            isSaving[participantId] = true;
            
            const form = document.getElementById(`room-allocation-form-${participantId}`);
            if (!form) {
                console.error('Form not found for participant:', participantId);
                isSaving[participantId] = false;
                return;
            }
            
            const formData = new FormData(form);
            
            // Add other form inputs that are outside the form
            const roomNumberSelect = document.querySelector(`select[name="room_number"][form="room-allocation-form-${participantId}"]`);
            const checkInInput = document.querySelector(`input[name="check_in"][form="room-allocation-form-${participantId}"]`);
            const checkOutInput = document.querySelector(`input[name="check_out"][form="room-allocation-form-${participantId}"]`);
            
            if (roomNumberSelect) formData.append('room_number', roomNumberSelect.value);
            if (checkInInput) formData.append('check_in', checkInInput.value);
            if (checkOutInput) formData.append('check_out', checkOutInput.value);
            
            // Get CSRF token
                    let csrfToken = null;
            const tokenInput = form.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        csrfToken = tokenInput.value;
            } else {
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            csrfToken = metaTag.getAttribute('content');
                        }
                    }
                    
                    if (!csrfToken) {
                console.error('CSRF token not found');
                isSaving[participantId] = false;
                return;
            }
            
            // Show saving indicator
            const participantRow = document.getElementById(`participant-row-${participantId}`);
            const participantName = participantRow ? participantRow.querySelector('td:first-child').textContent.trim() : 'Participant';
            
            if (participantRow) {
                participantRow.style.opacity = '0.7';
                participantRow.style.backgroundColor = '#fef3c7'; // Light yellow background
            }
            
            // Show saving message
            showInfo(`Saving room allocation for ${participantName}...`);
            
                    fetch(`/admin/room-allocations/${participantId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                    showSuccess(`Room allocation saved for ${participantName}!`);
                    // Add visual success indicator
                    if (participantRow) {
                        participantRow.style.backgroundColor = '#d1fae5'; // Light green
                        setTimeout(() => {
                            participantRow.style.backgroundColor = '';
                        }, 2000);
                    }
                        } else {
                    showError(data.message || `Failed to save room allocation for ${participantName}.`);
                    if (participantRow) {
                        participantRow.style.backgroundColor = '#fee2e2'; // Light red
                        setTimeout(() => {
                            participantRow.style.backgroundColor = '';
                        }, 3000);
                    }
                }
            })
            .catch(function(error) {
                console.error('Save error:', error);
                showError(`Failed to save room allocation for ${participantName}. Please try again.`);
                if (participantRow) {
                    participantRow.style.backgroundColor = '#fee2e2'; // Light red
                    setTimeout(() => {
                        participantRow.style.backgroundColor = '';
                    }, 3000);
                }
            })
            .finally(function() {
                isSaving[participantId] = false;
                if (participantRow) {
                    participantRow.style.opacity = '';
                }
            });
        }
        
        function showSuccess(message) {
            hideMessages(); // Hide other messages first
            const successDiv = document.getElementById('success-message');
            const messageContainer = document.getElementById('message-container');
            
            if (successDiv && messageContainer) {
                successDiv.innerHTML = message + ' <button type="button" class="ml-2 text-green-600 hover:text-green-800" onclick="hideMessages()">&times;</button>';
                successDiv.classList.remove('hidden');
                messageContainer.classList.remove('hidden');
                
                // Auto-hide after 4 seconds for success messages
                setTimeout(hideMessages, 4000);
            }
        }
        
        function showError(message) {
            hideMessages(); // Hide other messages first
            const errorDiv = document.getElementById('error-message');
            const messageContainer = document.getElementById('message-container');
            
            if (errorDiv && messageContainer) {
                errorDiv.innerHTML = message + ' <button type="button" class="ml-2 text-red-600 hover:text-red-800" onclick="hideMessages()">&times;</button>';
                errorDiv.classList.remove('hidden');
                messageContainer.classList.remove('hidden');
                
                // Don't auto-hide error messages - keep them visible until manually dismissed
                // setTimeout(hideMessages, 6000);
            }
        }
        
        function showInfo(message) {
            hideMessages(); // Hide other messages first
            const infoDiv = document.getElementById('info-message');
            const messageContainer = document.getElementById('message-container');
            
            if (infoDiv && messageContainer) {
                infoDiv.innerHTML = message + ' <button type="button" class="ml-2 text-blue-600 hover:text-blue-800" onclick="hideMessages()">&times;</button>';
                infoDiv.classList.remove('hidden');
                messageContainer.classList.remove('hidden');
                
                // Auto-hide after 2 seconds for info messages
                setTimeout(hideMessages, 2000);
            }
        }
        
        function hideMessages() {
            const successDiv = document.getElementById('success-message');
            const errorDiv = document.getElementById('error-message');
            const infoDiv = document.getElementById('info-message');
            const messageContainer = document.getElementById('message-container');
            
            if (successDiv) successDiv.classList.add('hidden');
            if (errorDiv) errorDiv.classList.add('hidden');
            if (infoDiv) infoDiv.classList.add('hidden');
            if (messageContainer) messageContainer.classList.add('hidden');
        }
        
        // Special function for conflict messages that are always persistent
        function showConflictError(message) {
            hideMessages(); // Hide other messages first
            const errorDiv = document.getElementById('error-message');
            const messageContainer = document.getElementById('message-container');
            
            if (errorDiv && messageContainer) {
                errorDiv.innerHTML = '<strong>⚠️ Room Conflict:</strong> ' + message + ' <button type="button" class="ml-2 text-red-600 hover:text-red-800 font-bold" onclick="hideMessages()">&times;</button>';
                errorDiv.classList.remove('hidden');
                messageContainer.classList.remove('hidden');
                
                // Make the message more prominent
                errorDiv.style.backgroundColor = '#fef2f2';
                errorDiv.style.borderLeft = '4px solid #ef4444';
                errorDiv.style.padding = '12px';
                
                // Don't auto-hide conflict messages - keep them visible until manually dismissed
            }
        }
        
        // Make hideMessages globally accessible for close buttons
        window.hideMessages = hideMessages;
        window.showConflictError = showConflictError;
    });
})();
</script>
@endsection 