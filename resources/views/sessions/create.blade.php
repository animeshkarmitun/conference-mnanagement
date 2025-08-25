@extends('layouts.app')

@section('title', 'Create Session')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Create Session</h2>
    <form method="POST" action="{{ route('sessions.store') }}">
        @csrf
        
        <!-- Basic Session Information -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Session Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference *</label>
                    <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Conference</option>
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                        @endforeach
                    </select>
                    @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="venue_id" class="block text-sm font-medium text-gray-700">Venue *</label>
                    <select name="venue_id" id="venue_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Venue</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                        @endforeach
                    </select>
                    @error('venue_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <p id="start_hint" class="text-xs text-gray-500 mt-1"></p>
                    <p id="start_error" class="text-red-600 text-sm mt-1 hidden"></p>
                    @error('start_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <p id="end_hint" class="text-xs text-gray-500 mt-1"></p>
                    <p id="end_error" class="text-red-600 text-sm mt-1 hidden"></p>
                    @error('end_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Room (Optional)</label>
                    <input type="text" name="room" id="room" value="{{ old('room') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('room')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity (Optional)</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('capacity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Enhanced Participant Selection -->
        <div class="bg-blue-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Participant Management (Optional)</h3>
            <p class="text-sm text-gray-600 mb-4">Use the search and filter tools below to easily manage hundreds of participants for this session.</p>
            
            <!-- Search and Filter Bar -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label for="participant_search" class="block text-sm font-medium text-gray-700 mb-2">Search Participants</label>
                        <div class="relative">
                            <input type="text" id="participant_search" placeholder="Search by name, email, or organization..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="participant_type_filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                        <select id="participant_type_filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">All Types</option>
                            @foreach($participantTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="organization_filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Organization</label>
                        <select id="organization_filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">All Organizations</option>
                            @foreach($organizations ?? [] as $org)
                                <option value="{{ $org }}">{{ $org }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button type="button" id="select_all" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                        Select All
                    </button>
                    <button type="button" id="deselect_all" class="text-sm bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">
                        Deselect All
                    </button>
                    <button type="button" id="add_selected" class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                        Add Selected (<span id="selected_count">0</span>)
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="total_available">0</span> available participants
                </div>
            </div>

            <!-- Split View: Available and Selected Participants -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Available Participants -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h4 class="font-semibold text-gray-800">Available Participants</h4>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div id="available_participants" class="p-4 space-y-2">
                            @foreach($participants as $participant)
                                <div class="participant-item available-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50" data-id="{{ $participant->id }}" data-name="{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}" data-email="{{ $participant->user->email }}" data-organization="{{ $participant->user->organization ?? '' }}" data-type="{{ $participant->participantType->name ?? '' }}">
                                    <input type="checkbox" class="participant-checkbox mr-3 h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</div>
                                        <div class="text-sm text-gray-500">{{ $participant->user->email }}</div>
                                        @if($participant->user->organization)
                                            <div class="text-xs text-gray-400">{{ $participant->user->organization }}</div>
                                        @endif
                                    </div>
                                    <button type="button" class="add-participant-btn ml-2 text-green-600 hover:text-green-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Selected Participants -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h4 class="font-semibold text-gray-800">Session Participants</h4>
                        <p class="text-sm text-gray-600">Participants assigned to this session</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div id="selected_participants" class="p-4 space-y-2">
                            <!-- Selected participants will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden input for form submission -->
            <input type="hidden" id="participants_input" name="participants" value="">
        </div>

        <div class="flex justify-end">
            <a href="{{ route('sessions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Create Session</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('participant_search');
    const typeFilter = document.getElementById('participant_type_filter');
    const orgFilter = document.getElementById('organization_filter');
    const selectAllBtn = document.getElementById('select_all');
    const deselectAllBtn = document.getElementById('deselect_all');
    const addSelectedBtn = document.getElementById('add_selected');
    const selectedCountSpan = document.getElementById('selected_count');
    const totalAvailableSpan = document.getElementById('total_available');
    const availableContainer = document.getElementById('available_participants');
    const selectedContainer = document.getElementById('selected_participants');
    const participantsInput = document.getElementById('participants_input');

    let selectedParticipants = new Set();
    let availableParticipants = new Set();

    // Update hidden input and selected count
    function updateParticipantsInput() {
        const selectedArray = Array.from(selectedParticipants);
        participantsInput.value = JSON.stringify(selectedArray);
        selectedCountSpan.textContent = selectedParticipants.size;
    }

    // Filter participants
    function filterParticipants() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeFilterValue = typeFilter.value;
        const orgFilterValue = orgFilter.value;

        document.querySelectorAll('.available-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const email = item.dataset.email.toLowerCase();
            const organization = (item.dataset.organization || '').toLowerCase();
            const type = item.dataset.type;

            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm) || organization.includes(searchTerm);
            const matchesType = !typeFilterValue || type === typeFilterValue;
            const matchesOrg = !orgFilterValue || organization === orgFilterValue.toLowerCase();

            if (matchesSearch && matchesType && matchesOrg && !selectedParticipants.has(item.dataset.id)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Update total available count
        const visibleCount = document.querySelectorAll('.available-item[style="display: block"]').length;
        totalAvailableSpan.textContent = visibleCount;
    }

    // Add participant to session
    function addParticipant(participantId) {
        if (!selectedParticipants.has(participantId)) {
            selectedParticipants.add(participantId);
            availableParticipants.delete(participantId);
            
            // Move item from available to selected
            const item = document.querySelector(`.available-item[data-id="${participantId}"]`);
            if (item) {
                const clone = item.cloneNode(true);
                clone.classList.remove('available-item');
                clone.classList.add('selected-item');
                clone.classList.add('bg-green-50');
                clone.classList.add('border-green-200');
                
                // Update checkbox
                const checkbox = clone.querySelector('input[type="checkbox"]');
                checkbox.checked = true;
                checkbox.name = 'participants[]';
                
                // Update button
                const btn = clone.querySelector('button');
                btn.classList.remove('add-participant-btn', 'text-green-600', 'hover:text-green-800');
                btn.classList.add('remove-participant-btn', 'text-red-600', 'hover:text-red-800');
                btn.innerHTML = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                
                selectedContainer.appendChild(clone);
                item.style.display = 'none';
            }
            
            updateParticipantsInput();
            filterParticipants();
        }
    }

    // Remove participant from session
    function removeParticipant(participantId) {
        if (selectedParticipants.has(participantId)) {
            selectedParticipants.delete(participantId);
            availableParticipants.add(participantId);
            
            // Remove item from selected
            const item = document.querySelector(`.selected-item[data-id="${participantId}"]`);
            if (item) {
                item.remove();
            }
            
            // Show item in available again
            const availableItem = document.querySelector(`.available-item[data-id="${participantId}"]`);
            if (availableItem) {
                availableItem.style.display = 'block';
            }
            
            updateParticipantsInput();
            filterParticipants();
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterParticipants);
    typeFilter.addEventListener('change', filterParticipants);
    orgFilter.addEventListener('change', filterParticipants);

    // Select all visible available participants
    selectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.available-item[style="display: block"] input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // Deselect all available participants
    deselectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.available-item input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // Add selected available participants
    addSelectedBtn.addEventListener('click', function() {
        document.querySelectorAll('.available-item input[type="checkbox"]:checked').forEach(checkbox => {
            const participantId = checkbox.closest('.participant-item').dataset.id;
            addParticipant(participantId);
        });
    });

    // Add individual participant
    availableContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-participant-btn')) {
            const participantId = e.target.closest('.participant-item').dataset.id;
            addParticipant(participantId);
        }
    });

    // Remove individual participant
    selectedContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-participant-btn')) {
            const participantId = e.target.closest('.participant-item').dataset.id;
            removeParticipant(participantId);
        }
    });

    // Initialize
    updateParticipantsInput();
    filterParticipants();
});

// Conference change handler (existing functionality)
const conferenceVenues = @json($conferenceVenues);
const conferenceDates = @json($conferenceDates);
const confSelect = document.getElementById('conference_id');
const startInput = document.getElementById('start_time');
const endInput = document.getElementById('end_time');
const startHint = document.getElementById('start_hint');
const endHint = document.getElementById('end_hint');
const startErr = document.getElementById('start_error');
const endErr = document.getElementById('end_error');

function clearErrors() { [startErr, endErr].forEach(e => { if (!e) return; e.textContent=''; e.classList.add('hidden'); }); }

function applyBounds() {
    clearErrors();
    const confId = confSelect.value;
    if (!confId || !conferenceDates[confId]) {
        startInput.removeAttribute('min'); startInput.removeAttribute('max');
        endInput.removeAttribute('min'); endInput.removeAttribute('max');
        if (startHint) startHint.textContent = '';
        if (endHint) endHint.textContent = '';
        return;
    }
    const minStr = conferenceDates[confId].start_date + 'T00:00';
    const maxStr = conferenceDates[confId].end_date + 'T23:59';
    startInput.min = minStr; startInput.max = maxStr;
    endInput.min = minStr; endInput.max = maxStr;
    if (startHint) startHint.textContent = `Allowed: ${minStr} to ${maxStr}`;
    if (endHint) endHint.textContent = `Allowed: ${minStr} to ${maxStr}`;
}

function validateRange() {
    clearErrors();
    const s = startInput.value ? new Date(startInput.value) : null;
    const e = endInput.value ? new Date(endInput.value) : null;
    const confId = confSelect.value;
    if (!confId || !conferenceDates[confId]) return true;
    const min = new Date(conferenceDates[confId].start_date + 'T00:00');
    const max = new Date(conferenceDates[confId].end_date + 'T23:59');
    let ok = true;
    if (s && (s < min || s > max)) { if (startErr) { startErr.textContent = 'Start time must be within conference dates.'; startErr.classList.remove('hidden'); } ok = false; }
    if (e && (e < min || e > max)) { if (endErr) { endErr.textContent = 'End time must be within conference dates.'; endErr.classList.remove('hidden'); } ok = false; }
    if (s && e && e <= s) { if (endErr) { endErr.textContent = 'End time must be after start time.'; endErr.classList.remove('hidden'); } ok = false; }
    return ok;
}

confSelect.addEventListener('change', () => {
    const confId = confSelect.value;
    const venueId = conferenceVenues[confId];
    if (venueId) document.getElementById('venue_id').value = venueId;
    applyBounds();
});

[startInput, endInput].forEach(el => {
    el.addEventListener('change', validateRange);
    el.addEventListener('input', validateRange);
});

// Initial
applyBounds();
</script>
@endsection 