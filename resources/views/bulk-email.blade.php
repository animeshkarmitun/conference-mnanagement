@extends('layouts.app')

@section('title', 'Send Bulk Emails')

@section('content')
<div class="max-w-4xl mx-auto mt-8 bg-white rounded-2xl shadow p-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-yellow-700 flex items-center gap-2">
            <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Send Bulk Emails
        </h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <h4 class="font-bold">Validation Errors:</h4>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Selection Options (only show when no pre-selected participants) -->
    @if(!$participants || $participants->count() == 0)
        <div class="mb-6 bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Select Recipients</h2>
            
            <!-- Conference Selection -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Conference</label>
                <select id="conference-select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Choose a conference to load participants</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}" {{ $selectedConferenceId == $conference->id ? 'selected' : '' }}>
                            {{ $conference->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Participant Selection -->
            <div id="participant-selection" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Participants</label>
                <div class="mb-3">
                    <label class="flex items-center">
                        <input type="checkbox" id="select-all-participants" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700">Select All Participants</span>
                    </label>
                </div>
                <div id="participants-list" class="bg-white rounded-lg border border-gray-200 max-h-60 overflow-y-auto">
                    <!-- Participants will be loaded here via AJAX -->
                </div>
                <div class="mt-3">
                    <span id="selected-count" class="text-sm text-gray-600">0 participants selected</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Email Form (Always visible when participants are selected) -->
    <div id="email-form-section" class="{{ ($participants && $participants->count() > 0) ? '' : 'hidden' }}">
        <!-- Email Form -->
        <form method="POST" action="{{ route('bulk.email.send') }}" id="email-form">
            @csrf
            
            <!-- Hidden participant IDs -->
            <div id="hidden-participant-ids">
                @if($participants && $participants->count() > 0)
                    @foreach($participants as $participant)
                        <input type="hidden" name="participant_ids[]" value="{{ $participant->id }}">
                    @endforeach
                @endif
            </div>

            <!-- Conference ID (hidden, set from selection above) -->
            <input type="hidden" name="conference_id" id="selected-conference-id" value="">

            <!-- Subject -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject', 'Conference Update') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                       placeholder="Enter email subject" required>
                @error('subject')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="8" 
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                          placeholder="Enter your message here..." required>{{ old('message', 'Dear Participant,

Thank you for your participation in our conference. We hope you find the sessions informative and engaging.

Best regards,
Conference Team') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Send Button -->
            <div class="flex justify-end">
                <button type="submit" id="send-button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200">
                    Send Email
                </button>
            </div>
            
        </form>
    </div>

    @if($participants && $participants->count() > 0)
        <!-- Pre-loaded Participants (from URL parameters) -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Selected Participants ({{ $participants->count() }})</h2>
            <div class="bg-gray-50 rounded-lg p-4 max-h-60 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($participants as $participant)
                        <div class="flex items-center space-x-3 p-2 bg-white rounded border">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-bold text-blue-600">
                                    {{ substr($participant->user->first_name ?? $participant->user->name, 0, 1) }}{{ substr($participant->user->last_name ?? '', 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">{{ $participant->user->email }}</p>
                                <p class="text-xs text-gray-400">{{ $participant->conference->name ?? 'No Conference' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Show email form for pre-loaded participants -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('email-form-section').classList.remove('hidden');
            });
        </script>
    @else
        <div id="no-participants-message" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Participants Selected</h3>
            <p class="text-gray-500 mb-6">Please select a conference and participants to send bulk emails.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conferenceSelect = document.getElementById('conference-select');
    const participantSelection = document.getElementById('participant-selection');
    const participantsList = document.getElementById('participants-list');
    const selectAllCheckbox = document.getElementById('select-all-participants');
    const selectedCount = document.getElementById('selected-count');
    
    // Check if elements exist before adding event listeners
    if (!conferenceSelect || !participantSelection || !participantsList || !selectAllCheckbox || !selectedCount) {
        console.log('Some elements not found, skipping dynamic participant selection setup');
        return;
    }
    
    // Conference selection change handler
    conferenceSelect.addEventListener('change', function() {
        const conferenceId = this.value;
        
        if (conferenceId) {
            loadParticipants(conferenceId);
            participantSelection.classList.remove('hidden');
            // Set the conference ID in the hidden input
            document.getElementById('selected-conference-id').value = conferenceId;
        } else {
            participantSelection.classList.add('hidden');
            participantsList.innerHTML = '';
            updateSelectedCount();
            // Clear the conference ID
            document.getElementById('selected-conference-id').value = '';
        }
    });
    
    // Select all participants handler
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = participantsList.querySelectorAll('.participant-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });
    
    // Load participants via AJAX
    function loadParticipants(conferenceId) {
        fetch(`{{ route('bulk.email.participants') }}?conference_id=${conferenceId}`)
            .then(response => response.json())
            .then(data => {
                displayParticipants(data.participants);
            })
            .catch(error => {
                console.error('Error loading participants:', error);
                participantsList.innerHTML = '<div class="p-4 text-center text-red-600">Error loading participants</div>';
            });
    }
    
    // Display participants in the list
    function displayParticipants(participants) {
        if (participants.length === 0) {
            participantsList.innerHTML = '<div class="p-4 text-center text-gray-500">No participants found for this conference</div>';
            return;
        }
        
        let html = '';
        participants.forEach(participant => {
            const statusClass = getStatusClass(participant.registration_status);
            html += `
                <div class="flex items-center p-3 border-b border-gray-100 hover:bg-gray-50">
                    <input type="checkbox" class="participant-checkbox rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" 
                           value="${participant.id}" data-email="${participant.email}" data-name="${participant.name}">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">${participant.name}</p>
                                <p class="text-xs text-gray-500">${participant.email}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-400">${participant.participant_type}</span>
                                <span class="px-2 py-1 text-xs rounded-full ${statusClass}">${participant.registration_status}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        participantsList.innerHTML = html;
        
        // Add event listeners to individual checkboxes
        participantsList.querySelectorAll('.participant-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        updateSelectedCount();
    }
    
    // Get status class for styling
    function getStatusClass(status) {
        switch(status) {
            case 'approved': return 'bg-green-100 text-green-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'rejected': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
    
    // Update selected count
    function updateSelectedCount() {
        const selectedCheckboxes = participantsList.querySelectorAll('.participant-checkbox:checked');
        const count = selectedCheckboxes.length;
        selectedCount.textContent = `${count} participant${count !== 1 ? 's' : ''} selected`;
        
        // Show/hide email form and no participants message based on selection
        const emailFormSection = document.getElementById('email-form-section');
        const noParticipantsMessage = document.getElementById('no-participants-message');
        
        if (count > 0) {
            emailFormSection.classList.remove('hidden');
            if (noParticipantsMessage) {
                noParticipantsMessage.classList.add('hidden');
            }
        } else {
            emailFormSection.classList.add('hidden');
            if (noParticipantsMessage) {
                noParticipantsMessage.classList.remove('hidden');
            }
        }
        
        // Update select all checkbox state
        const allCheckboxes = participantsList.querySelectorAll('.participant-checkbox');
        if (allCheckboxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === allCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else if (count > 0) {
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        }
    }
    
    // Form submission handler
    const emailForm = document.getElementById('email-form');
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            // Check if we have pre-selected participants (from URL) or dynamically selected ones
            @if($participants && $participants->count() > 0)
                // Pre-selected participants are already in the HTML
                const participantIdInputs = this.querySelectorAll('input[name="participant_ids[]"]');
            @else
                // Dynamically selected participants
                const selectedCheckboxes = participantsList ? participantsList.querySelectorAll('.participant-checkbox:checked') : [];
                
                if (selectedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one participant to send emails to.');
                    return;
                }
                
                // Add hidden inputs for selected participant IDs
                const hiddenContainer = document.getElementById('hidden-participant-ids');
                if (hiddenContainer) {
                    hiddenContainer.innerHTML = '';
                    
                    selectedCheckboxes.forEach(checkbox => {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'participant_ids[]';
                        hiddenInput.value = checkbox.value;
                        hiddenContainer.appendChild(hiddenInput);
                    });
                }
            @endif
            
            // Update send button text
            const sendButton = document.getElementById('send-button');
            const participantCount = {{ $participants ? $participants->count() : 0 }};
            if (participantCount > 0 && sendButton) {
                sendButton.textContent = `Send to ${participantCount} Participant${participantCount > 1 ? 's' : ''}`;
            }
        });
    }
    
    // Load participants if conference is pre-selected
    @if($selectedConferenceId)
        if (typeof loadParticipants === 'function') {
            loadParticipants('{{ $selectedConferenceId }}');
            if (participantSelection) {
                participantSelection.classList.remove('hidden');
            }
        }
    @endif
    
});
</script>
@endsection 