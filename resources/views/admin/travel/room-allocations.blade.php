@extends('layouts.app')

@section('title', 'Room Allocations')

@section('content')
<div class="w-full py-6 px-4">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Room Allocations</h2>
            
            <!-- Success/Error Messages -->
            <div id="message-container" class="hidden mb-4">
                <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"></div>
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
            </div>
                
                <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participant
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
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($participants as $participant)
                            <tr id="participant-row-{{ $participant->id }}">
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                                    </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <form id="room-allocation-form-{{ $participant->id }}" class="room-allocation-form" data-participant-id="{{ $participant->id }}">
                                        @csrf
                                        <select name="hotel_id" class="w-full text-sm rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" required>
                                                <option value="">Select Hotel</option>
                                                @foreach($hotels as $hotel)
                                                    <option value="{{ $hotel->id }}" {{ optional(optional($participant->roomAllocation)->hotel)->id == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                                @endforeach
                                            </select>
                                    </form>
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="text" name="room_number" form="room-allocation-form-{{ $participant->id }}" value="{{ optional($participant->roomAllocation)->room_number }}" class="w-full text-sm rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500">
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ optional($participant->travelDetails)->arrival_date ? \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('M d, Y H:i') : '-' }}
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ optional($participant->travelDetails)->departure_date ? \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('M d, Y H:i') : '-' }}
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="datetime-local" name="check_in" form="room-allocation-form-{{ $participant->id }}" value="{{ optional($participant->roomAllocation)->check_in ? \Carbon\Carbon::parse($participant->roomAllocation->check_in)->format('Y-m-d\TH:i') : '' }}" class="w-full text-sm rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 check-in-input" data-participant-id="{{ $participant->id }}">
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <input type="datetime-local" name="check_out" form="room-allocation-form-{{ $participant->id }}" value="{{ optional($participant->roomAllocation)->check_out ? \Carbon\Carbon::parse($participant->roomAllocation->check_out)->format('Y-m-d\TH:i') : '' }}" class="w-full text-sm rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 check-out-input" data-participant-id="{{ $participant->id }}">
                                        </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <button type="submit" form="room-allocation-form-{{ $participant->id }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm font-semibold transition-colors duration-200">
                                        <span class="save-text">Save</span>
                                        <span class="saving-text hidden">Saving...</span>
                                    </button>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
// Force refresh to avoid caching issues - Version 3.0
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    ready(function() {
        console.log('Room allocation script loaded - Version 3.0');
        
        // Handle form submissions
        const forms = document.querySelectorAll('.room-allocation-form');
        console.log('Found forms:', forms.length);
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted for participant:', this.dataset.participantId);
                
                try {
                    const participantId = this.dataset.participantId;
                    if (!participantId) {
                        throw new Error('Participant ID not found');
                    }
                    
                    // Validate datetime inputs
                    const checkInInputValidation = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
                    const checkOutInputValidation = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
                    
                    if (checkInInputValidation && checkOutInputValidation) {
                        const checkInValue = checkInInputValidation.value;
                        const checkOutValue = checkOutInputValidation.value;
                        
                        if (checkInValue && checkOutValue) {
                            const checkInDate = new Date(checkInValue);
                            const checkOutDate = new Date(checkOutValue);
                            
                            if (checkInDate >= checkOutDate) {
                                showError('Check-out time must be after check-in time.');
                                return;
                            }
                            
                            // Check if check-in is in the past
                            const now = new Date();
                            if (checkInDate < now) {
                                showError('Check-in time cannot be in the past.');
                                return;
                            }
                        }
                    }
                    
                    const formData = new FormData(this);
                    
                    // Add other form inputs that are outside the form
                    const roomNumberInput = document.querySelector(`input[name="room_number"][form="room-allocation-form-${participantId}"]`);
                    const checkInInput = document.querySelector(`input[name="check_in"][form="room-allocation-form-${participantId}"]`);
                    const checkOutInput = document.querySelector(`input[name="check_out"][form="room-allocation-form-${participantId}"]`);
                    
                    if (roomNumberInput) formData.append('room_number', roomNumberInput.value);
                    if (checkInInput) formData.append('check_in', checkInInput.value);
                    if (checkOutInput) formData.append('check_out', checkOutInput.value);
                    
                    const submitButton = document.querySelector(`button[form="room-allocation-form-${participantId}"]`);
                    
                    if (!submitButton) {
                        throw new Error('Submit button not found');
                    }
                    
                    const saveText = submitButton.querySelector('.save-text');
                    const savingText = submitButton.querySelector('.saving-text');
                    
                    if (!saveText || !savingText) {
                        throw new Error('Button text elements not found');
                    }
                    
                    // Get CSRF token - try multiple methods
                    let csrfToken = null;
                    
                    // Method 1: From form input
                    const tokenInput = this.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        csrfToken = tokenInput.value;
                    }
                    
                    // Method 2: From meta tag
                    if (!csrfToken) {
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            csrfToken = metaTag.getAttribute('content');
                        }
                    }
                    
                    // Method 3: Fallback to server token
                    if (!csrfToken) {
                        csrfToken = '{{ csrf_token() }}';
                    }
                    
                    console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
                    console.log('Participant ID:', participantId);
                    
                    // Show loading state
                    saveText.classList.add('hidden');
                    savingText.classList.remove('hidden');
                    submitButton.disabled = true;
                    
                    // Hide previous messages
                    hideMessages();
                    
                    // Make the request
                    fetch(`/admin/room-allocations/${participantId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(function(response) {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(function(data) {
                        console.log('Response data:', data);
                        if (data.success) {
                            showSuccess(data.message || 'Room allocation updated successfully!');
                        } else {
                            showError(data.message || 'Failed to update room allocation.');
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        showError('An error occurred while updating room allocation. Please try again.');
                    })
                    .finally(function() {
                        // Reset button state
                        saveText.classList.remove('hidden');
                        savingText.classList.add('hidden');
                        submitButton.disabled = false;
                    });
                    
                } catch (error) {
                    console.error('Form submission error:', error);
                    showError('Form submission error: ' + error.message);
                    
                    // Reset button state
                    const submitButton = document.querySelector(`button[form="room-allocation-form-${participantId}"]`);
                    if (submitButton) {
                        const saveText = submitButton.querySelector('.save-text');
                        const savingText = submitButton.querySelector('.saving-text');
                        if (saveText && savingText) {
                            saveText.classList.remove('hidden');
                            savingText.classList.add('hidden');
                            submitButton.disabled = false;
                        }
                    }
                }
            });
        });
        
        // Add real-time datetime validation
        document.querySelectorAll('.check-in-input, .check-out-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const participantId = this.dataset.participantId;
                const isCheckIn = this.classList.contains('check-in-input');
                const isCheckOut = this.classList.contains('check-out-input');
                
                if (isCheckIn) {
                    const checkOutInput = document.querySelector(`input[name="check_out"][data-participant-id="${participantId}"]`);
                    if (checkOutInput && checkOutInput.value) {
                        const checkInDate = new Date(this.value);
                        const checkOutDate = new Date(checkOutInput.value);
                        
                        if (checkInDate >= checkOutDate) {
                            this.style.borderColor = '#ef4444';
                            showError('Check-in time must be before check-out time.');
                        } else {
                            this.style.borderColor = '';
                            hideMessages();
                        }
                    }
                }
                
                if (isCheckOut) {
                    const checkInInput = document.querySelector(`input[name="check_in"][data-participant-id="${participantId}"]`);
                    if (checkInInput && checkInInput.value) {
                        const checkInDate = new Date(checkInInput.value);
                        const checkOutDate = new Date(this.value);
                        
                        if (checkInDate >= checkOutDate) {
                            this.style.borderColor = '#ef4444';
                            showError('Check-out time must be after check-in time.');
                        } else {
                            this.style.borderColor = '';
                            hideMessages();
                        }
                    }
                }
                
                // Check if check-in is in the past
                if (isCheckIn && this.value) {
                    const checkInDate = new Date(this.value);
                    const now = new Date();
                    
                    if (checkInDate < now) {
                        this.style.borderColor = '#ef4444';
                        showError('Check-in time cannot be in the past.');
                    } else {
                        this.style.borderColor = '';
                        hideMessages();
                    }
                }
            });
        });
        
        function showSuccess(message) {
            const successDiv = document.getElementById('success-message');
            const messageContainer = document.getElementById('message-container');
            
            if (successDiv && messageContainer) {
                successDiv.textContent = message;
                successDiv.classList.remove('hidden');
                messageContainer.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(hideMessages, 5000);
            }
        }
        
        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            const messageContainer = document.getElementById('message-container');
            
            if (errorDiv && messageContainer) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                messageContainer.classList.remove('hidden');
                
                // Auto-hide after 7 seconds
                setTimeout(hideMessages, 7000);
            }
        }
        
        function hideMessages() {
            const successDiv = document.getElementById('success-message');
            const errorDiv = document.getElementById('error-message');
            const messageContainer = document.getElementById('message-container');
            
            if (successDiv) successDiv.classList.add('hidden');
            if (errorDiv) errorDiv.classList.add('hidden');
            if (messageContainer) messageContainer.classList.add('hidden');
        }
    });
})();
</script>
@endsection 