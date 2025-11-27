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
                    <div class="relative">
                        <div class="relative">
                            <input type="text" id="conference_search" placeholder="Search conferences..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 pr-16" autocomplete="off">
                            <button type="button" id="clear_conference" class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden" title="Clear selection">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <button type="button" id="conference_dropdown_toggle" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Show all conferences">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="conference_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                            <div id="conference_options"></div>
                        </div>
                    </div>
                    <input type="hidden" id="conference_id" name="conference_id" value="{{ old('conference_id') }}">
                    @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="venue_id" class="block text-sm font-medium text-gray-700">Venue *</label>
                        <button type="button" id="openVenueModalBtn" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New
                        </button>
                    </div>
                    <div class="relative">
                        <div class="relative">
                            <input type="text" id="venue_search" placeholder="Search venues..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 pr-16" autocomplete="off">
                            <button type="button" id="clear_venue" class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden" title="Clear selection">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <button type="button" id="venue_dropdown_toggle" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Show all venues">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="venue_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                            <div id="venue_options"></div>
                        </div>
                    </div>
                    <input type="hidden" id="venue_id" name="venue_id" value="{{ old('venue_id') }}">
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

            </div>

            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Enhanced Participant Selection -->
        <div class="bg-blue-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Participant Management (Optional)</h3>
            <p class="text-sm text-gray-600 mb-4">Participants are automatically added to the session when you check them. Use the search and filter tools below to easily manage hundreds of participants.</p>
            
            <!-- Search and Filter Bar -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label for="participant_search" class="block text-sm font-medium text-gray-700 mb-2">Search Participants</label>
                        <div class="relative">
                            <input type="text" id="participant_search" placeholder="Search by name, email, hashtag, bio, designation, organization, field of work, or country..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 pl-10">
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
                                <div class="participant-item available-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50" 
                                     data-id="{{ $participant->id }}" 
                                     data-name="{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}" 
                                     data-email="{{ $participant->user->email }}" 
                                     data-organization="{{ $participant->user->organization_institution ?? $participant->user->organization ?? '' }}" 
                                     data-type="{{ $participant->participantType->name ?? '' }}" 
                                     data-hashtags="{{ $participant->hashtags ?? '' }}"
                                     data-bio="{{ $participant->bio ?? '' }}"
                                     data-designation="{{ $participant->user->designation ?? '' }}"
                                     data-field-of-work="{{ $participant->user->field_of_work_study ?? '' }}"
                                     data-country="{{ $participant->user->country ?? '' }}">
                                    <input type="checkbox" class="participant-checkbox mr-3 h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</div>
                                        <div class="text-sm text-gray-500">{{ $participant->user->email }}</div>
                                        @if($participant->user->organization_institution ?? $participant->user->organization)
                                            <div class="text-xs text-gray-400">{{ $participant->user->organization_institution ?? $participant->user->organization }}</div>
                                        @endif
                                        @if($participant->hashtags)
                                            <div class="text-xs text-purple-600 mt-1">
                                                @foreach(explode(',', $participant->hashtags) as $hashtag)
                                                    @php $clean = ltrim(trim($hashtag), '#'); @endphp
                                                    @if($clean)
                                                        <span class="inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs mr-1 mb-1">#{{ $clean }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
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
                        
                        <!-- Conflict Warnings -->
                        <div id="conflict_warnings" class="p-4 border-t border-gray-200 bg-red-50 hidden">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h5 class="text-sm font-medium text-red-800 mb-2">Schedule Conflicts Detected</h5>
                                    <div id="conflict_details" class="text-sm text-red-700 space-y-2">
                                        <!-- Conflict details will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden input for form submission -->
            <input type="hidden" id="participants_input" name="participants" value="">
            <input type="hidden" id="draft_session_id" name="draft_session_id" value="">
        </div>

        <!-- Session Status and Actions -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Session Status</h3>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="radio" id="status_draft" name="status" value="draft" checked class="mr-2">
                        <label for="status_draft" class="text-sm font-medium text-gray-700">Draft</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="status_published" name="status" value="published" class="mr-2">
                        <label for="status_published" class="text-sm font-medium text-gray-700">Published</label>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <div id="draft_status" class="text-sm text-gray-600 hidden">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Draft
                        </span>
                    </div>
                    <div id="publish_status" class="text-sm text-gray-600 hidden">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Published
                        </span>
                    </div>
                </div>
            </div>
            
            <div id="draft_message" class="mt-3 text-sm text-gray-600 hidden">
                <p>Session will be saved as draft. No emails will be sent to participants.</p>
            </div>
            
            <div id="publish_message" class="mt-3 text-sm text-gray-600 hidden">
                <p>Session will be published and emails will be sent to all participants.</p>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('sessions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="button" id="save_draft_btn" class="mr-4 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Save as Draft</button>
            <button type="submit" id="publish_btn" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Publish Session</button>
        </div>
    </form>
</div>

<!-- Venue Creation Modal -->
<div id="venueModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create New Venue</h3>
                <button type="button" id="closeVenueModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="venueForm">
                @csrf
                <div class="mb-4">
                    <label for="modal_venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                    <input type="text" id="modal_venue_name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p id="venue_name_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                
                <div class="mb-4">
                    <label for="modal_venue_address" class="block text-sm font-medium text-gray-700">Address *</label>
                    <textarea id="modal_venue_address" name="address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <p id="venue_address_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                
                <div class="mb-4">
                    <label for="modal_venue_capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" id="modal_venue_capacity" name="capacity" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p id="venue_capacity_error" class="text-red-600 text-sm mt-1 hidden"></p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelVenueModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Venue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===================== Searchable Dropdowns =====================
    
    // Conference data
    const conferences = @json($conferences);
    const venues = @json($venues);
    
    // ===================== Conference Searchable Dropdown =====================
    function initializeConferenceDropdown() {
        const conferenceSearch = document.getElementById('conference_search');
        const conferenceDropdown = document.getElementById('conference_dropdown');
        const conferenceOptions = document.getElementById('conference_options');
        const conferenceIdInput = document.getElementById('conference_id');
        const clearConferenceBtn = document.getElementById('clear_conference');
        const conferenceDropdownToggle = document.getElementById('conference_dropdown_toggle');
        
        let selectedConference = null;
        let filteredConferences = [];
        let isConferenceDropdownOpen = false;
        
        function filterConferences(query) {
            if (!query.trim()) {
                return conferences;
            }
            const lowerQuery = query.toLowerCase();
            return conferences.filter(conference => 
                conference.name.toLowerCase().includes(lowerQuery)
            );
        }
        
        function renderConferenceOptions(conferences) {
            conferenceOptions.innerHTML = '';
            
            if (conferences.length === 0) {
                conferenceOptions.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-500">
                        No conferences found
                    </div>
                `;
                return;
            }
            
            conferences.forEach(conference => {
                const option = document.createElement('div');
                option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-yellow-50 transition-colors duration-150';
                option.textContent = conference.name;
                option.dataset.id = conference.id;
                option.dataset.name = conference.name;
                
                option.addEventListener('click', () => {
                    selectConference(conference);
                });
                
                conferenceOptions.appendChild(option);
            });
        }
        
        function selectConference(conference) {
            selectedConference = conference;
            conferenceSearch.value = conference.name;
            conferenceIdInput.value = conference.id;
            clearConferenceBtn.classList.remove('hidden');
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
            
            // Trigger conference change event for existing functionality
            const event = new Event('change');
            conferenceIdInput.dispatchEvent(event);
        }
        
        function clearConferenceSelection() {
            selectedConference = null;
            conferenceSearch.value = '';
            conferenceIdInput.value = '';
            clearConferenceBtn.classList.add('hidden');
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
        }
        
        function toggleConferenceDropdown() {
            if (isConferenceDropdownOpen) {
                conferenceDropdown.classList.add('hidden');
                isConferenceDropdownOpen = false;
            } else {
                filteredConferences = filterConferences(conferenceSearch.value);
                renderConferenceOptions(filteredConferences);
                conferenceDropdown.classList.remove('hidden');
                isConferenceDropdownOpen = true;
            }
        }
        
        // Event listeners
        conferenceSearch.addEventListener('input', (e) => {
            filteredConferences = filterConferences(e.target.value);
            renderConferenceOptions(filteredConferences);
            conferenceDropdown.classList.remove('hidden');
            isConferenceDropdownOpen = true;
        });
        
        conferenceSearch.addEventListener('focus', () => {
            if (!isConferenceDropdownOpen) {
                filteredConferences = filterConferences(conferenceSearch.value);
                renderConferenceOptions(filteredConferences);
                conferenceDropdown.classList.remove('hidden');
                isConferenceDropdownOpen = true;
            }
        });
        
        clearConferenceBtn.addEventListener('click', clearConferenceSelection);
        conferenceDropdownToggle.addEventListener('click', toggleConferenceDropdown);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#conference_search') && !e.target.closest('#conference_dropdown')) {
                conferenceDropdown.classList.add('hidden');
                isConferenceDropdownOpen = false;
            }
        });
        
        // Initialize with existing value
        const existingConferenceId = conferenceIdInput.value;
        if (existingConferenceId) {
            const existingConference = conferences.find(c => c.id == existingConferenceId);
            if (existingConference) {
                selectConference(existingConference);
            }
        }
    }
    
    // ===================== Venue Searchable Dropdown =====================
    function initializeVenueDropdown() {
        const venueSearch = document.getElementById('venue_search');
        const venueDropdown = document.getElementById('venue_dropdown');
        const venueOptions = document.getElementById('venue_options');
        const venueIdInput = document.getElementById('venue_id');
        const clearVenueBtn = document.getElementById('clear_venue');
        const venueDropdownToggle = document.getElementById('venue_dropdown_toggle');
        
        let selectedVenue = null;
        let filteredVenues = [];
        let isVenueDropdownOpen = false;
        
        function filterVenues(query) {
            if (!query.trim()) {
                return venues;
            }
            const lowerQuery = query.toLowerCase();
            return venues.filter(venue => 
                venue.name.toLowerCase().includes(lowerQuery) ||
                venue.address.toLowerCase().includes(lowerQuery)
            );
        }
        
        function renderVenueOptions(venues) {
            venueOptions.innerHTML = '';
            
            if (venues.length === 0) {
                venueOptions.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-500">
                        No venues found
                    </div>
                `;
                return;
            }
            
            venues.forEach(venue => {
                const option = document.createElement('div');
                option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-yellow-50 transition-colors duration-150';
                option.textContent = `${venue.name} - ${venue.address}`;
                option.dataset.id = venue.id;
                option.dataset.name = venue.name;
                
                option.addEventListener('click', () => {
                    selectVenue(venue);
                });
                
                venueOptions.appendChild(option);
            });
        }
        
        function selectVenue(venue) {
            selectedVenue = venue;
            venueSearch.value = `${venue.name} - ${venue.address}`;
            venueIdInput.value = venue.id;
            clearVenueBtn.classList.remove('hidden');
            venueDropdown.classList.add('hidden');
            isVenueDropdownOpen = false;
        }
        
        function clearVenueSelection() {
            selectedVenue = null;
            venueSearch.value = '';
            venueIdInput.value = '';
            clearVenueBtn.classList.add('hidden');
            venueDropdown.classList.add('hidden');
            isVenueDropdownOpen = false;
        }
        
        function toggleVenueDropdown() {
            if (isVenueDropdownOpen) {
                venueDropdown.classList.add('hidden');
                isVenueDropdownOpen = false;
            } else {
                filteredVenues = filterVenues(venueSearch.value);
                renderVenueOptions(filteredVenues);
                venueDropdown.classList.remove('hidden');
                isVenueDropdownOpen = true;
            }
        }
        
        // Event listeners
        venueSearch.addEventListener('input', (e) => {
            filteredVenues = filterVenues(e.target.value);
            renderVenueOptions(filteredVenues);
            venueDropdown.classList.remove('hidden');
            isVenueDropdownOpen = true;
        });
        
        venueSearch.addEventListener('focus', () => {
            if (!isVenueDropdownOpen) {
                filteredVenues = filterVenues(venueSearch.value);
                renderVenueOptions(filteredVenues);
                venueDropdown.classList.remove('hidden');
                isVenueDropdownOpen = true;
            }
        });
        
        clearVenueBtn.addEventListener('click', clearVenueSelection);
        venueDropdownToggle.addEventListener('click', toggleVenueDropdown);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#venue_search') && !e.target.closest('#venue_dropdown')) {
                venueDropdown.classList.add('hidden');
                isVenueDropdownOpen = false;
            }
        });
        
        // Initialize with existing value
        const existingVenueId = venueIdInput.value;
        if (existingVenueId) {
            const existingVenue = venues.find(v => v.id == existingVenueId);
            if (existingVenue) {
                selectVenue(existingVenue);
            }
        }
    }
    
    // ===================== Venue Modal =====================
    function initializeVenueModal() {
        const venueModal = document.getElementById('venueModal');
        const openVenueModalBtn = document.getElementById('openVenueModalBtn');
        const closeVenueModalBtn = document.getElementById('closeVenueModal');
        const cancelVenueModalBtn = document.getElementById('cancelVenueModal');
        const venueForm = document.getElementById('venueForm');
        
        function openVenueModal() {
            venueModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeVenueModal() {
            venueModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            clearVenueErrors();
            venueForm.reset();
        }
        
        function clearVenueErrors() {
            const errorElements = document.querySelectorAll('[id$="_error"]');
            errorElements.forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }
        
        function validateVenueForm() {
            clearVenueErrors();
            let isValid = true;
            
            const name = document.getElementById('modal_venue_name').value.trim();
            const address = document.getElementById('modal_venue_address').value.trim();
            
            if (!name) {
                document.getElementById('venue_name_error').textContent = 'Venue name is required';
                document.getElementById('venue_name_error').classList.remove('hidden');
                isValid = false;
            }
            
            if (!address) {
                document.getElementById('venue_address_error').textContent = 'Address is required';
                document.getElementById('venue_address_error').classList.remove('hidden');
                isValid = false;
            }
            
            return isValid;
        }
        
        function createVenue() {
            if (!validateVenueForm()) {
                return;
            }
            
            const formData = new FormData(venueForm);
            
            fetch('{{ route("venues.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.log('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response');
                    });
                }
            })
            .then(data => {
                console.log('Venue created successfully:', data);
                
                if (data.success) {
                    // Add new venue to venues array
                    venues.push(data.venue);
                    
                    // Update venue dropdown
                    const venueSearch = document.getElementById('venue_search');
                    const venueIdInput = document.getElementById('venue_id');
                    const clearVenueBtn = document.getElementById('clear_venue');
                    
                    // Select the newly created venue
                    venueSearch.value = `${data.venue.name} - ${data.venue.address}`;
                    venueIdInput.value = data.venue.id;
                    clearVenueBtn.classList.remove('hidden');
                    
                    closeVenueModal();
                    
                    // Show success message
                    alert('Venue created successfully!');
                } else {
                    throw new Error(data.message || 'Failed to create venue');
                }
            })
            .catch(error => {
                console.error('Error creating venue:', error);
                alert('Error creating venue: ' + error.message);
            });
        }
        
        // Event listeners
        openVenueModalBtn.addEventListener('click', openVenueModal);
        closeVenueModalBtn.addEventListener('click', closeVenueModal);
        cancelVenueModalBtn.addEventListener('click', closeVenueModal);
        
        venueForm.addEventListener('submit', (e) => {
            e.preventDefault();
            createVenue();
        });
        
        // Close modal when clicking outside
        venueModal.addEventListener('click', (e) => {
            if (e.target === venueModal) {
                closeVenueModal();
            }
        });
    }
    
    // Initialize all dropdowns and modals
    initializeConferenceDropdown();
    initializeVenueDropdown();
    initializeVenueModal();
    const searchInput = document.getElementById('participant_search');
    const typeFilter = document.getElementById('participant_type_filter');
    const orgFilter = document.getElementById('organization_filter');
    const selectAllBtn = document.getElementById('select_all');
    const deselectAllBtn = document.getElementById('deselect_all');
    const totalAvailableSpan = document.getElementById('total_available');
    const availableContainer = document.getElementById('available_participants');
    const selectedContainer = document.getElementById('selected_participants');
    const participantsInput = document.getElementById('participants_input');

    let selectedParticipants = new Set();
    let availableParticipants = new Set();

    // Update hidden input and selected count
    function updateParticipantsInput() {
        const selectedArray = Array.from(selectedParticipants).map(id => parseInt(id));
        const moderatorsArray = Array.from(document.querySelectorAll('.moderator-checkbox:checked')).map(cb => parseInt(cb.value));
        
        // Create object with participant IDs and their roles
        const participantsData = {};
        selectedArray.forEach(participantId => {
            participantsData[participantId] = moderatorsArray.includes(participantId) ? 'moderator' : 'participant';
        });
        
        participantsInput.value = JSON.stringify(participantsData);
        console.log('Updated participants input:', participantsInput.value);
    }
    
    // Update selected count (placeholder function - no count display element exists)
    function updateSelectedCount() {
        // No selected count display element exists in the template
        // This function is called but does nothing to prevent errors
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
            const hashtags = (item.dataset.hashtags || '').toLowerCase();
            const bio = (item.dataset.bio || '').toLowerCase();
            const designation = (item.dataset.designation || '').toLowerCase();
            const fieldOfWork = (item.dataset.fieldOfWork || '').toLowerCase();
            const country = (item.dataset.country || '').toLowerCase();
            const type = item.dataset.type;

            const matchesSearch = name.includes(searchTerm) || 
                                 email.includes(searchTerm) || 
                                 organization.includes(searchTerm) || 
                                 hashtags.includes(searchTerm) ||
                                 bio.includes(searchTerm) ||
                                 designation.includes(searchTerm) ||
                                 fieldOfWork.includes(searchTerm) ||
                                 country.includes(searchTerm);
            const matchesType = !typeFilterValue || type === typeFilterValue;
            const matchesOrg = !orgFilterValue || organization === orgFilterValue.toLowerCase();

            if (matchesSearch && matchesType && matchesOrg && !selectedParticipants.has(item.dataset.id)) {
                item.style.display = 'block';
                item.classList.add('visible');
            } else {
                item.style.display = 'none';
                item.classList.remove('visible');
            }
        });

        // Update total available count
        const visibleCount = document.querySelectorAll('.available-item.visible').length;
        totalAvailableSpan.textContent = visibleCount;
    }

    // Add participant to session
    function addParticipant(participantId) {
        console.log('Adding participant:', participantId);
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
                checkbox.disabled = false; // Enable checkbox for removal
                
                // Ensure hashtags and country are displayed in the selected item
                const hashtags = item.dataset.hashtags;
                const country = item.dataset.country;
                const flexDiv = clone.querySelector('.flex-1');
                
                if (flexDiv) {
                    // Remove existing additional info if any
                    const existingTags = flexDiv.querySelector('.text-blue-500');
                    const existingCountry = flexDiv.querySelector('.text-green-600');
                    if (existingTags) existingTags.remove();
                    if (existingCountry) existingCountry.remove();
                    
                    // Add hashtags if available
                    if (hashtags) {
                        const hashtagDiv = document.createElement('div');
                        hashtagDiv.className = 'text-xs text-blue-500 mt-1';
                        hashtagDiv.innerHTML = `<span class="font-medium">Tags:</span> ${hashtags}`;
                        flexDiv.appendChild(hashtagDiv);
                    }
                    
                    // Add country if available
                    if (country) {
                        const countryDiv = document.createElement('div');
                        countryDiv.className = 'text-xs text-green-600 mt-1';
                        countryDiv.innerHTML = `<span class="font-medium">Country:</span> ${country}`;
                        flexDiv.appendChild(countryDiv);
                    }
                    
                    // Add moderator checkbox under country
                    const moderatorDiv = document.createElement('div');
                    moderatorDiv.className = 'mt-2';
                    moderatorDiv.innerHTML = `
                        <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" 
                                   name="moderators[]" 
                                   value="${participantId}" 
                                   class="moderator-checkbox mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="text-xs font-medium text-blue-600">Moderator</span>
                        </label>
                    `;
                    flexDiv.appendChild(moderatorDiv);
                }
                
                selectedContainer.appendChild(clone);
                item.style.display = 'none';
                
                // Uncheck the original checkbox
                const originalCheckbox = item.querySelector('input[type="checkbox"]');
                if (originalCheckbox) {
                    originalCheckbox.checked = false;
                }
            }
            
            updateParticipantsInput();
            filterParticipants();
            debouncedConflictCheck(); // Check for conflicts when participant is added
            console.log('Participant added. Total selected:', selectedParticipants.size);
        }
    }

    // Remove participant from session
    function removeParticipant(participantId) {
        console.log('Removing participant:', participantId);
        if (selectedParticipants.has(participantId)) {
            selectedParticipants.delete(participantId);
            availableParticipants.add(participantId);
            
            // Remove item from selected
            const item = document.querySelector(`.selected-item[data-id="${participantId}"]`);
            if (item) {
                item.remove();
            }
            
            // Show item in available again and uncheck it
            const availableItem = document.querySelector(`.available-item[data-id="${participantId}"]`);
            if (availableItem) {
                availableItem.style.display = 'block';
                const checkbox = availableItem.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = false;
                }
            }
            
            updateParticipantsInput();
            updateSelectedCount();
            filterParticipants();
            debouncedConflictCheck(); // Check for conflicts when participant is removed
            console.log('Participant removed. Total selected:', selectedParticipants.size);
        }
    }

    // Event listeners
    searchInput.addEventListener('input', function() {
        // Debounce the search to avoid too many requests
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => {
            const conferenceId = document.getElementById('conference_id').value;
            if (conferenceId) {
                loadParticipantsForConference(conferenceId);
            } else {
                filterParticipants();
            }
        }, 300);
    });
    typeFilter.addEventListener('change', filterParticipants);
    orgFilter.addEventListener('change', filterParticipants);

    // Select all visible available participants
    selectAllBtn.addEventListener('click', function() {
        const visibleItems = document.querySelectorAll('.available-item.visible');
        visibleItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox && !selectedParticipants.has(item.dataset.id)) {
                checkbox.checked = true;
                // Automatically add to selected panel
                addParticipant(item.dataset.id);
            }
        });
        updateSelectedCount();
        // Note: Conflict checking is already triggered by addParticipant function
    });

    // Deselect all available participants
    deselectAllBtn.addEventListener('click', function() {
        // Uncheck all available checkboxes
        document.querySelectorAll('.available-item input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Remove all participants from selected panel
        const selectedItems = document.querySelectorAll('.selected-item');
        selectedItems.forEach(item => {
            const participantId = item.dataset.id;
            if (selectedParticipants.has(participantId)) {
                selectedParticipants.delete(participantId);
                availableParticipants.add(participantId);
                
                // Show item in available again
                const availableItem = document.querySelector(`.available-item[data-id="${participantId}"]`);
                if (availableItem) {
                    availableItem.style.display = 'block';
                    const checkbox = availableItem.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            }
        });
        
        // Clear selected panel
        selectedContainer.innerHTML = '';
        
        updateParticipantsInput();
        updateSelectedCount();
        filterParticipants();
        debouncedConflictCheck(); // Check for conflicts when all participants are deselected
    });

    // Checkbox change event for available participants
    availableContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('participant-checkbox')) {
            const participantId = e.target.closest('.participant-item').dataset.id;
            if (e.target.checked) {
                // Automatically add to selected panel when checked
                addParticipant(participantId);
            } else {
                // Automatically remove from selected panel when unchecked
                removeParticipant(participantId);
            }
        }
    });

    // Handle checkbox changes in selected participants panel
    selectedContainer.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            const participantId = e.target.closest('.participant-item').dataset.id;
            if (e.target.classList.contains('moderator-checkbox')) {
                // Moderator checkbox changed, update the input
                updateParticipantsInput();
            } else if (!e.target.checked) {
                // Unchecking removes the participant
                removeParticipant(participantId);
            }
        }
    });

    // Function to load participants for a specific conference
    async function loadParticipantsForConference(conferenceId) {
        console.log('Loading participants for conference:', conferenceId);
        
        if (!conferenceId || conferenceId === '') {
            // Clear participants if no conference selected
            document.getElementById('available_participants').innerHTML = '<p class="text-gray-500 p-4">Please select a conference to view available participants.</p>';
            document.getElementById('total_available').textContent = '0';
            return;
        }

        // Show loading state
        document.getElementById('available_participants').innerHTML = '<p class="text-blue-500 p-4">Loading participants...</p>';

        try {
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenElement) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }
            const csrfToken = csrfTokenElement.getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            
            const searchTerm = document.getElementById('participant_search').value;
            const url = `/sessions/participants/by-conference?conference_id=${conferenceId}${searchTerm ? `&search=${encodeURIComponent(searchTerm)}` : ''}`;
            
            const response = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            console.log('Response URL:', response.url);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status} - ${errorText.substring(0, 100)}`);
            }
            
            const data = await response.json();
            console.log('Received data:', data);
            console.log('Participants count:', data.participants ? data.participants.length : 'No participants array');
            
            // Clear existing participants
            const availableContainer = document.getElementById('available_participants');
            if (!availableContainer) {
                throw new Error('Available participants container not found');
            }
            availableContainer.innerHTML = '';
            
            // Add new participants
            if (data.participants && Array.isArray(data.participants)) {
                data.participants.forEach((participant, index) => {
                    console.log(`Creating participant ${index + 1}:`, participant);
                    try {
                        const participantElement = createParticipantElement(participant);
                        availableContainer.appendChild(participantElement);
                    } catch (error) {
                        console.error(`Error creating participant ${index + 1}:`, error);
                    }
                });
            } else {
                throw new Error('Invalid participants data format');
            }
            
            // Update total count
            document.getElementById('total_available').textContent = data.participants.length;
            
            // Only reset filters and search if this is initial load (no search term)
            if (!searchTerm) {
                document.getElementById('participant_type_filter').value = '';
                document.getElementById('organization_filter').value = '';
            }
            
            // Apply initial filtering
            filterParticipants();
            
        } catch (error) {
            console.error('Error loading participants:', error);
            let errorMessage = 'Error loading participants. Please try again.';
            if (error.message.includes('401')) {
                errorMessage = 'Authentication required. Please login and try again.';
            } else if (error.message.includes('403')) {
                errorMessage = 'Access denied. Please check your permissions.';
            } else if (error.message.includes('404')) {
                errorMessage = 'API endpoint not found. Please contact support.';
            } else if (error.message.includes('CSRF token not found')) {
                errorMessage = 'Security token not found. Please refresh the page.';
            }
            document.getElementById('available_participants').innerHTML = `<p class="text-red-500 p-4">${errorMessage}</p>`;
        }
    }

    // Function to create participant element
    function createParticipantElement(participant) {
        const div = document.createElement('div');
        div.className = 'participant-item available-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50';
        div.setAttribute('data-id', participant.id);
        div.setAttribute('data-name', participant.name);
        div.setAttribute('data-email', participant.email);
        div.setAttribute('data-organization', participant.organization);
        div.setAttribute('data-type', participant.type);
        div.setAttribute('data-hashtags', participant.hashtags || '');
        div.setAttribute('data-bio', participant.bio || '');
        div.setAttribute('data-designation', participant.designation || '');
        div.setAttribute('data-field-of-work', participant.field_of_work_study || '');
        div.setAttribute('data-country', participant.country || '');
        
        // Create hashtags HTML
        let hashtagsHtml = '';
        if (participant.hashtags) {
            const hashtags = participant.hashtags.split(',').map(tag => tag.trim().replace(/^#+/, '')).filter(tag => tag);
            hashtagsHtml = `
                <div class="text-xs text-purple-600 mt-1">
                    ${hashtags.map(tag => `<span class="inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs mr-1 mb-1">#${tag}</span>`).join('')}
                </div>
            `;
        }
        
        div.innerHTML = `
            <input type="checkbox" class="participant-checkbox mr-3 h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
            <div class="flex-1">
                <div class="font-medium text-gray-900">${participant.name}</div>
                <div class="text-sm text-gray-500">${participant.email}</div>
                ${participant.organization ? `<div class="text-xs text-gray-400">${participant.organization}</div>` : ''}
                ${hashtagsHtml}
            </div>
        `;
        
        return div;
    }

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
        if (venueId) {
            // Update venue dropdown with conference's default venue
            const venue = venues.find(v => v.id == venueId);
            if (venue) {
                const venueSearch = document.getElementById('venue_search');
                const venueIdInput = document.getElementById('venue_id');
                const clearVenueBtn = document.getElementById('clear_venue');
                
                venueSearch.value = `${venue.name} - ${venue.address}`;
                venueIdInput.value = venue.id;
                clearVenueBtn.classList.remove('hidden');
            }
        }
        applyBounds();
        
        // Load participants for the selected conference
        loadParticipantsForConference(confId);
    });

    [startInput, endInput].forEach(el => {
        el.addEventListener('change', function() {
            validateRange();
            debouncedConflictCheck(); // Check for conflicts when time changes
        });
        el.addEventListener('input', function() {
            validateRange();
            debouncedConflictCheck(); // Check for conflicts when time changes
        });
    });

    // Helper: add all currently-checked available participants (same as clicking "Add Selected")
    function addCheckedAvailableToSelected() {
        document.querySelectorAll('.available-item input[type="checkbox"]:checked').forEach(checkbox => {
            const participantId = checkbox.closest('.participant-item')?.dataset.id;
            if (participantId) {
                addParticipant(participantId);
            }
        });
        updateParticipantsInput();
    }

    // Ensure checked selections are applied visually and included in payload on submit
    const formEl = document.querySelector('form[action*="sessions"]');
    if (formEl) {
        formEl.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            addCheckedAvailableToSelected();
            updateParticipantsInput(); // Ensure participants input is updated with moderator roles
            console.log('Selected participants:', Array.from(selectedParticipants));
            console.log('Participants input value:', participantsInput.value);
        });
    }

    // ===================== Session Status Management =====================
    
    // Status radio button elements
    const statusDraft = document.getElementById('status_draft');
    const statusPublished = document.getElementById('status_published');
    const draftStatus = document.getElementById('draft_status');
    const publishStatus = document.getElementById('publish_status');
    const draftMessage = document.getElementById('draft_message');
    const publishMessage = document.getElementById('publish_message');
    const saveDraftBtn = document.getElementById('save_draft_btn');
    const publishBtn = document.getElementById('publish_btn');
    
    // Auto-save variables
    let autoSaveTimeout;
    let isAutoSaving = false;
    let lastSavedData = {};
    let currentDraftSessionId = null;
    
    // Conflict detection variables
    let conflictCheckTimeout;
    let currentConflicts = [];
    
    // Update status display
    function updateStatusDisplay() {
        if (statusDraft.checked) {
            draftStatus.classList.remove('hidden');
            publishStatus.classList.add('hidden');
            draftMessage.classList.remove('hidden');
            publishMessage.classList.add('hidden');
            saveDraftBtn.classList.remove('hidden');
            publishBtn.textContent = 'Publish Session';
        } else {
            draftStatus.classList.add('hidden');
            publishStatus.classList.remove('hidden');
            draftMessage.classList.add('hidden');
            publishMessage.classList.remove('hidden');
            saveDraftBtn.classList.add('hidden');
            publishBtn.textContent = 'Create Session';
        }
    }
    
    // Check if all required fields are filled
    function checkRequiredFields() {
        const requiredFields = [
            'conference_id',
            'venue_id', 
            'title',
            'start_time',
            'end_time'
        ];
        
        const missingFields = [];
        
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element || !element.value.trim()) {
                missingFields.push(field);
            }
        });
        
        return {
            isValid: missingFields.length === 0,
            missingFields: missingFields
        };
    }
    
    // Auto-save as draft
    function autoSaveDraft() {
        if (isAutoSaving) return;
        
        const validation = checkRequiredFields();
        if (!validation.isValid) {
            console.log('Cannot auto-save: missing required fields:', validation.missingFields);
            return;
        }
        
        const formData = new FormData();
        formData.append('conference_id', document.getElementById('conference_id').value);
        formData.append('venue_id', document.getElementById('venue_id').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('start_time', document.getElementById('start_time').value);
        formData.append('end_time', document.getElementById('end_time').value);
        formData.append('room', document.getElementById('room').value);
        formData.append('participants', participantsInput.value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add draft session ID if we have one (for updates)
        if (currentDraftSessionId) {
            formData.append('draft_session_id', currentDraftSessionId);
        }
        
        isAutoSaving = true;
        
        fetch('{{ route("sessions.auto-save-draft") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            isAutoSaving = false;
            if (data.success) {
                console.log('Draft saved successfully');
                showNotification(data.message, 'success');
                
                // Store the session ID for future updates
                currentDraftSessionId = data.session_id;
                
                // Update the hidden input field
                const draftSessionIdInput = document.getElementById('draft_session_id');
                if (draftSessionIdInput) {
                    draftSessionIdInput.value = data.session_id;
                }
                
                // Update the form action to include the session ID for publishing
                const form = document.querySelector('form[action*="sessions"]');
                if (form && data.is_update) {
                    // If this is an update, we might want to change the form action
                    // to update instead of create
                    console.log('Updated existing draft session:', data.session_id);
                }
            } else {
                console.log('Draft save failed:', data.message);
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            isAutoSaving = false;
            console.error('Error saving draft:', error);
            showNotification('Error saving draft', 'error');
        });
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Debounced auto-save
    function debouncedAutoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            if (statusDraft.checked) {
                autoSaveDraft();
            }
        }, 2000); // Auto-save after 2 seconds of inactivity
    }
    
    // Event listeners for status radio buttons
    statusDraft.addEventListener('change', updateStatusDisplay);
    statusPublished.addEventListener('change', updateStatusDisplay);
    
    // Event listeners for form fields to trigger auto-save
    const formFields = ['conference_id', 'venue_id', 'title', 'description', 'start_time', 'end_time', 'room'];
    formFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', debouncedAutoSave);
            field.addEventListener('change', debouncedAutoSave);
        }
    });
    
    // Event listener for participants input
    participantsInput.addEventListener('change', debouncedAutoSave);
    
    // Save draft button
    saveDraftBtn.addEventListener('click', function(e) {
        e.preventDefault();
        autoSaveDraft();
    });
    
    // Publish button - change form action based on status
    publishBtn.addEventListener('click', function(e) {
        if (statusDraft.checked) {
            e.preventDefault();
            // If we have a draft session ID, publish it directly
            if (currentDraftSessionId) {
                publishExistingDraft();
            } else {
                // Save as draft first, then publish
                autoSaveDraft();
            }
        }
        // If published, let the form submit normally
    });
    
    // Publish existing draft
    function publishExistingDraft() {
        if (!currentDraftSessionId) {
            showNotification('No draft session to publish', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('conference_id', document.getElementById('conference_id').value);
        formData.append('venue_id', document.getElementById('venue_id').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('start_time', document.getElementById('start_time').value);
        formData.append('end_time', document.getElementById('end_time').value);
        formData.append('room', document.getElementById('room').value);
        formData.append('participants', participantsInput.value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/sessions/${currentDraftSessionId}/publish`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Session published successfully', 'success');
                // Redirect to sessions index after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("sessions.index") }}';
                }, 1500);
            } else {
                showNotification(data.message || 'Error publishing session', 'error');
            }
        })
        .catch(error => {
            console.error('Error publishing session:', error);
            showNotification('Error publishing session', 'error');
        });
    }
    
    // ===================== Conflict Detection =====================
    
    // Check for participant conflicts
    function checkParticipantConflicts() {
        const participantIds = Array.from(selectedParticipants);
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        console.log('Checking conflicts for participants:', participantIds);
        console.log('Start time:', startTime);
        console.log('End time:', endTime);
        
        if (participantIds.length === 0 || !startTime || !endTime) {
            hideConflictWarnings();
            return;
        }
        
        // Ensure participant IDs are valid
        const validParticipantIds = participantIds.filter(id => id && !isNaN(parseInt(id)));
        if (validParticipantIds.length === 0) {
            hideConflictWarnings();
            return;
        }
        
        const formData = new FormData();
        // Append each participant ID individually for proper array handling
        // Convert to integers to ensure proper validation
        validParticipantIds.forEach((id, index) => {
            formData.append(`participant_ids[${index}]`, parseInt(id));
        });
        formData.append('start_time', startTime);
        formData.append('end_time', endTime);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('{{ route("sessions.check-conflicts") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Conflict check response status:', response.status);
            if (!response.ok) {
                return response.json().then(errorData => {
                    console.error('Conflict check error response:', errorData);
                    if (errorData.validation_error) {
                        console.error('Validation errors:', errorData.validation_error);
                        throw new Error(`Validation failed: ${JSON.stringify(errorData.validation_error)}`);
                    }
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Unknown error'}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Conflict check response data:', data);
            currentConflicts = data.conflicts || [];
            if (data.has_conflicts) {
                showConflictWarnings(data.conflicts);
            } else {
                hideConflictWarnings();
            }
        })
        .catch(error => {
            console.error('Error checking conflicts:', error);
            showNotification('Error checking participant conflicts: ' + error.message, 'error');
        });
    }
    
    // Show conflict warnings
    function showConflictWarnings(conflicts) {
        const conflictWarnings = document.getElementById('conflict_warnings');
        const conflictDetails = document.getElementById('conflict_details');
        
        if (!conflictWarnings || !conflictDetails) return;
        
        let conflictHtml = '';
        conflicts.forEach(conflict => {
            conflictHtml += `
                <div class="mb-3 p-3 bg-red-100 rounded-lg border border-red-200">
                    <div class="font-medium text-red-800 mb-1">
                        ${conflict.participant_name} (${conflict.participant_email})
                    </div>
                    <div class="text-sm text-red-700">
                        <div class="mb-1">Has conflicting sessions:</div>
                        <ul class="list-disc list-inside space-y-1">
                            ${conflict.conflicting_sessions.map(session => 
                                `<li>${session.title} - ${session.start_time} to ${session.end_time} (${session.conference}) <span class="text-xs px-2 py-1 rounded ${session.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${session.status}</span></li>`
                            ).join('')}
                        </ul>
                    </div>
                </div>
            `;
        });
        
        conflictDetails.innerHTML = conflictHtml;
        conflictWarnings.classList.remove('hidden');
    }
    
    // Hide conflict warnings
    function hideConflictWarnings() {
        const conflictWarnings = document.getElementById('conflict_warnings');
        if (conflictWarnings) {
            conflictWarnings.classList.add('hidden');
        }
    }
    
    // Debounced conflict check
    function debouncedConflictCheck() {
        clearTimeout(conflictCheckTimeout);
        conflictCheckTimeout = setTimeout(() => {
            checkParticipantConflicts();
        }, 1000); // Check conflicts after 1 second of inactivity
    }
    
    // Check for existing drafts on page load
    function checkForExistingDrafts() {
        // This could be enhanced to check for drafts based on current form data
        // For now, we'll just initialize the tracking
        console.log('Checking for existing drafts...');
    }
    
    // Initialize status display
    updateStatusDisplay();
    
    // Check for existing drafts
    checkForExistingDrafts();

    // Initialize
    updateParticipantsInput();
    updateSelectedCount();
    filterParticipants();
    applyBounds();
});
</script>
@endsection 