@extends('layouts.app')

@section('title', 'Edit Session')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Edit Session</h2>
    <form method="POST" action="{{ route('sessions.update', $session) }}">
        @csrf
        @method('PUT')
        
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
                    <input type="hidden" id="conference_id" name="conference_id" value="{{ old('conference_id', $session->conference_id) }}">
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
                    <input type="hidden" id="venue_id" name="venue_id" value="{{ old('venue_id', $session->venue_id) }}">
                    @error('venue_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $session->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($session->start_time)->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <p id="start_hint" class="text-xs text-gray-500 mt-1"></p>
                    <p id="start_error" class="text-red-600 text-sm mt-1 hidden"></p>
                    @error('start_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($session->end_time)->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <p id="end_hint" class="text-xs text-gray-500 mt-1"></p>
                    <p id="end_error" class="text-red-600 text-sm mt-1 hidden"></p>
                    @error('end_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Room (Optional)</label>
                    <input type="text" name="room" id="room" value="{{ old('room', $session->room) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('room')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description', $session->description) }}</textarea>
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
                                     data-organization="{{ $participant->user->organization ?? '' }}" 
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
                                        @if($participant->user->organization)
                                            <div class="text-xs text-gray-400">{{ $participant->user->organization }}</div>
                                        @endif
                                        @if($participant->hashtags)
                                            <div class="text-xs text-blue-500 mt-1">
                                                <span class="font-medium">Tags:</span> {{ $participant->hashtags }}
                                            </div>
                                        @endif
                                        @if($participant->user->country)
                                            <div class="text-xs text-green-600 mt-1">
                                                <span class="font-medium">Country:</span> {{ $participant->user->country }}
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
                        <p class="text-sm text-gray-600">Currently assigned to this session</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div id="selected_participants" class="p-4 space-y-2">
                            @foreach($session->participants as $participant)
                                @php
                                    $isModerator = $participant->pivot->role === 'moderator';
                                @endphp
                                <div class="participant-item selected-item flex items-center p-3 border border-green-200 rounded-lg bg-green-50" 
                                     data-id="{{ $participant->id }}"
                                     data-name="{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}" 
                                     data-email="{{ $participant->user->email }}" 
                                     data-organization="{{ $participant->user->organization ?? '' }}" 
                                     data-type="{{ $participant->participantType->name ?? '' }}"
                                     data-hashtags="{{ $participant->hashtags ?? '' }}"
                                     data-bio="{{ $participant->bio ?? '' }}"
                                     data-designation="{{ $participant->user->designation ?? '' }}"
                                     data-field-of-work="{{ $participant->user->field_of_work_study ?? '' }}"
                                     data-country="{{ $participant->user->country ?? '' }}">
                                    <div class="flex items-center mr-3">
                                        <input type="checkbox" name="participants[]" value="{{ $participant->id }}" checked class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <div class="font-medium text-gray-900">{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</div>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $participant->user->email }}</div>
                                        @if($participant->user->organization)
                                            <div class="text-xs text-gray-400">{{ $participant->user->organization }}</div>
                                        @endif
                                        @if($participant->hashtags)
                                            <div class="text-xs text-blue-500 mt-1">
                                                <span class="font-medium">Tags:</span> {{ $participant->hashtags }}
                                            </div>
                                        @endif
                                        @if($participant->user->country)
                                            <div class="text-xs text-green-600 mt-1">
                                                <span class="font-medium">Country:</span> {{ $participant->user->country }}
                                            </div>
                                        @endif
                                        <div class="mt-2">
                                            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                                <input type="checkbox" 
                                                       name="moderators[]" 
                                                       value="{{ $participant->id }}" 
                                                       class="moderator-checkbox mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                       {{ $isModerator ? 'checked' : '' }}>
                                                <span class="text-xs font-medium text-blue-600">Moderator</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                        <input type="radio" id="status_draft" name="status" value="draft" {{ old('status', $session->status ?? 'draft') === 'draft' ? 'checked' : '' }} class="mr-2">
                        <label for="status_draft" class="text-sm font-medium text-gray-700">Draft</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="status_published" name="status" value="published" {{ old('status', $session->status ?? 'draft') === 'published' ? 'checked' : '' }} class="mr-2">
                        <label for="status_published" class="text-sm font-medium text-gray-700">Published</label>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <div id="draft_status" class="text-sm text-gray-600 {{ old('status', $session->status ?? 'draft') === 'draft' ? '' : 'hidden' }}">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8 0a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd"></path>
                            </svg>
                            Draft
                        </span>
                    </div>
                    <div id="publish_status" class="text-sm text-gray-600 {{ old('status', $session->status ?? 'draft') === 'published' ? '' : 'hidden' }}">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Published
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <div id="draft_message" class="text-sm text-gray-600 {{ old('status', $session->status ?? 'draft') === 'draft' ? '' : 'hidden' }}">
                    <p>Session is saved as draft. No emails will be sent to participants.</p>
                </div>
                <div id="publish_message" class="text-sm text-gray-600 {{ old('status', $session->status ?? 'draft') === 'published' ? '' : 'hidden' }}">
                    <p>Session will be published and emails will be sent to all participants.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('sessions.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2">Cancel</a>
            <button type="button" id="save_draft_btn" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Save as Draft</button>
            <button type="submit" id="publish_btn" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Update Session</button>
        </div>
    </form>
</div>

<!-- Venue Modal -->
<div id="venueModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Venue</h3>
                <button type="button" id="closeVenueModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="venueForm">
                @csrf
                <div class="mb-4">
                    <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                    <input type="text" id="venue_name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                
                <div class="mb-4">
                    <label for="venue_address" class="block text-sm font-medium text-gray-700">Address *</label>
                    <textarea id="venue_address" name="address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="venue_capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" id="venue_capacity" name="capacity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelVenueModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Venue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===================== Conference and Venue Management =====================
    
    // Conference management
    const conferenceSearch = document.getElementById('conference_search');
    const conferenceDropdown = document.getElementById('conference_dropdown');
    const conferenceOptions = document.getElementById('conference_options');
    const conferenceIdInput = document.getElementById('conference_id');
    const clearConferenceBtn = document.getElementById('clear_conference');
    const conferenceDropdownToggle = document.getElementById('conference_dropdown_toggle');
    
    // Venue management
    const venueSearch = document.getElementById('venue_search');
    const venueDropdown = document.getElementById('venue_dropdown');
    const venueOptions = document.getElementById('venue_options');
    const venueIdInput = document.getElementById('venue_id');
    const clearVenueBtn = document.getElementById('clear_venue');
    const venueDropdownToggle = document.getElementById('venue_dropdown_toggle');
    const openVenueModalBtn = document.getElementById('openVenueModalBtn');
    const venueModal = document.getElementById('venueModal');
    const closeVenueModal = document.getElementById('closeVenueModal');
    const cancelVenueModal = document.getElementById('cancelVenueModal');
    const venueForm = document.getElementById('venueForm');
    
    // Participant management
    const searchInput = document.getElementById('participant_search');
    const typeFilter = document.getElementById('participant_type_filter');
    const orgFilter = document.getElementById('organization_filter');
    const selectAllBtn = document.getElementById('select_all');
    const deselectAllBtn = document.getElementById('deselect_all');
    const totalAvailableSpan = document.getElementById('total_available');
    const availableContainer = document.getElementById('available_participants');
    const selectedContainer = document.getElementById('selected_participants');
    const participantsInput = document.getElementById('participants_input');

    // ===================== Participant Management Variables =====================
    let selectedParticipants = new Set();
    let availableParticipants = new Set();
    
    // ===================== Conflict Detection Variables =====================
    let conflictCheckTimeout;
    let currentConflicts = [];
    const currentSessionId = {{ $session->id }};

    // ===================== Conference Management =====================
    
    let selectedConference = null;
    let selectedVenue = null;
    let isConferenceDropdownOpen = false;
    let isVenueDropdownOpen = false;
    let conferences = [];
    let venues = [];
    let filteredConferences = [];
    let filteredVenues = [];
    
    // Load conferences
    async function loadConferences() {
        try {
            console.log('Loading conferences...');
            const response = await fetch('/api/conferences', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            console.log('Conference response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Conference API error:', response.status, errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Conference data received:', data);
            conferences = data.conferences || [];
            filteredConferences = conferences;
            renderConferenceOptions(filteredConferences);
            console.log('Conferences loaded successfully:', conferences.length);
            return conferences;
        } catch (error) {
            console.error('Error loading conferences:', error);
            return [];
        }
    }
    
    // Load venues
    async function loadVenues() {
        try {
            console.log('Loading venues...');
            const response = await fetch('/api/venues', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            console.log('Venue response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Venue API error:', response.status, errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Venue data received:', data);
            venues = data.venues || [];
            filteredVenues = venues;
            renderVenueOptions(filteredVenues);
            console.log('Venues loaded successfully:', venues.length);
            return venues;
        } catch (error) {
            console.error('Error loading venues:', error);
            return [];
        }
    }
    
    // Render conference options
    function renderConferenceOptions(conferences) {
        conferenceOptions.innerHTML = '';
        
        if (conferences.length === 0) {
            conferenceOptions.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No conferences found</div>';
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
    
    // Render venue options
    function renderVenueOptions(venues) {
        venueOptions.innerHTML = '';
        
        if (venues.length === 0) {
            venueOptions.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No venues found</div>';
            return;
        }
        
        venues.forEach(venue => {
            const option = document.createElement('div');
            option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-yellow-50 transition-colors duration-150';
            option.textContent = venue.name;
            option.dataset.id = venue.id;
            option.dataset.name = venue.name;
            
            option.addEventListener('click', () => {
                selectVenue(venue);
            });
            
            venueOptions.appendChild(option);
        });
    }
    
    // Select conference
    function selectConference(conference) {
        selectedConference = conference;
        conferenceSearch.value = conference.name;
        conferenceIdInput.value = conference.id;
        conferenceDropdown.classList.add('hidden');
        isConferenceDropdownOpen = false;
        clearConferenceBtn.classList.remove('hidden');
        
        // Load participants for the selected conference
        loadParticipantsForConference(conference.id);
    }
    
    // Select venue
    function selectVenue(venue) {
        selectedVenue = venue;
        venueSearch.value = venue.name;
        venueIdInput.value = venue.id;
        venueDropdown.classList.add('hidden');
        isVenueDropdownOpen = false;
        clearVenueBtn.classList.remove('hidden');
    }
    
    // Clear conference selection
    function clearConferenceSelection() {
        selectedConference = null;
        conferenceSearch.value = '';
        conferenceIdInput.value = '';
        clearConferenceBtn.classList.add('hidden');
        conferenceDropdown.classList.add('hidden');
        isConferenceDropdownOpen = false;
    }
    
    // Clear venue selection
    function clearVenueSelection() {
        selectedVenue = null;
        venueSearch.value = '';
        venueIdInput.value = '';
        clearVenueBtn.classList.add('hidden');
        venueDropdown.classList.add('hidden');
        isVenueDropdownOpen = false;
    }
    
    // Filter conferences
    function filterConferences(query) {
        return conferences.filter(conference => 
            conference.name.toLowerCase().includes(query.toLowerCase())
        );
    }
    
    // Filter venues
    function filterVenues(query) {
        return venues.filter(venue => 
            venue.name.toLowerCase().includes(query.toLowerCase())
        );
    }
    
    // ===================== Venue Modal Management =====================
    
    // Open venue modal
    function openVenueModal() {
        venueModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Close venue modal
    function closeVenueModalFunc() {
        venueModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        venueForm.reset();
    }
    
    // Add new venue
    async function addVenue(formData) {
        try {
            const response = await fetch('/venues', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                const newVenue = data.venue;
                
                // Add to venues array
                venues.push(newVenue);
                filteredVenues = venues;
                renderVenueOptions(filteredVenues);
                
                // Select the new venue
                selectVenue(newVenue);
                
                // Close modal
                closeVenueModalFunc();
                
                showNotification('Venue added successfully', 'success');
            } else {
                const errorData = await response.json();
                showNotification(errorData.message || 'Error adding venue', 'error');
            }
        } catch (error) {
            console.error('Error adding venue:', error);
            showNotification('Error adding venue', 'error');
        }
    }
    
    // ===================== Participant Management =====================

    // Initialize selected participants from existing session participants
    document.querySelectorAll('#selected_participants input[type="checkbox"][name="participants[]"]').forEach(checkbox => {
        selectedParticipants.add(checkbox.value);
        availableParticipants.delete(checkbox.value);
    });
    
    // Initialize participants input with existing moderator states
    updateParticipantsInput();
    
    // Initial conflict check for existing participants
    setTimeout(() => {
        debouncedConflictCheck();
    }, 1000); // Check conflicts after 1 second to allow page to fully load

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
        // Note: selectedCountSpan is not defined in the template, removing this line
        // selectedCountSpan.textContent = selectedParticipants.size;
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
            const hashtags = (item.dataset.hashtags || '').toLowerCase();
            const bio = (item.dataset.bio || '').toLowerCase();
            const designation = (item.dataset.designation || '').toLowerCase();
            const fieldOfWork = (item.dataset.fieldOfWork || '').toLowerCase();
            const country = (item.dataset.country || '').toLowerCase();

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
        console.log('addParticipant called with ID:', participantId);
        console.log('Current selectedParticipants:', Array.from(selectedParticipants));
        if (!selectedParticipants.has(participantId)) {
            selectedParticipants.add(participantId);
            availableParticipants.delete(participantId);
            console.log('Added to selectedParticipants, new size:', selectedParticipants.size);
            
            // Move item from available to selected
            const item = document.querySelector(`.available-item[data-id="${participantId}"]`);
            console.log('Found item to move:', item);
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
                }
                
                // Add moderator checkbox under country
                if (flexDiv) {
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
                
                // Hide the original item and uncheck its checkbox
                item.style.display = 'none';
                const originalCheckbox = item.querySelector('input[type="checkbox"]');
                if (originalCheckbox) {
                    originalCheckbox.checked = false;
                }
            }
            
            updateParticipantsInput();
            filterParticipants();
            debouncedConflictCheck(); // Check for conflicts when participant is added
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
            
            // Show item in available again and uncheck its checkbox
            const availableItem = document.querySelector(`.available-item[data-id="${participantId}"]`);
            if (availableItem) {
                availableItem.style.display = 'block';
                const checkbox = availableItem.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = false;
                }
            }
            
            updateParticipantsInput();
            filterParticipants();
            debouncedConflictCheck(); // Check for conflicts when participant is removed
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterParticipants);
    typeFilter.addEventListener('change', filterParticipants);
    orgFilter.addEventListener('change', filterParticipants);

    // Select all visible available participants
    selectAllBtn.addEventListener('click', function() {
        console.log('Select All clicked');
        // Get all available items that are not hidden (either by style or by being already selected)
        const allAvailableItems = document.querySelectorAll('.available-item');
        console.log('Total available items found:', allAvailableItems.length);
        console.log('Available items:', allAvailableItems);
        
        const visibleItems = Array.from(allAvailableItems).filter(item => {
            const isHidden = item.style.display === 'none';
            const isAlreadySelected = selectedParticipants.has(item.dataset.id);
            console.log('Item:', item.dataset.id, 'isHidden:', isHidden, 'isAlreadySelected:', isAlreadySelected);
            return !isHidden && !isAlreadySelected;
        });
        console.log('Found visible items:', visibleItems.length);
        visibleItems.forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                console.log('Adding participant:', item.dataset.id);
                checkbox.checked = true;
                // Automatically add to selected panel
                addParticipant(item.dataset.id);
            }
        });
        console.log('Selected participants count:', selectedParticipants.size);
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
                removeParticipant(participantId);
            }
        });
    });

    // Auto-add/remove participants when checkboxes are clicked
    availableContainer.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            const participantId = e.target.closest('.participant-item').dataset.id;
            if (e.target.checked) {
                addParticipant(participantId);
            } else {
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

    // Initialize
    updateParticipantsInput();
    filterParticipants();

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
    const formEl = document.querySelector('form[action*="sessions/"]');
    if (formEl) {
        formEl.addEventListener('submit', function() {
            addCheckedAvailableToSelected();
            updateParticipantsInput(); // Ensure participants input is updated with moderator roles
        });
    }

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
            
            const response = await fetch(`/sessions/participants/by-conference?conference_id=${conferenceId}`, {
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
            
            // Reset filters and search
            document.getElementById('participant_search').value = '';
            document.getElementById('participant_type_filter').value = '';
            document.getElementById('organization_filter').value = '';
            
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
        
        let additionalInfo = '';
        if (participant.hashtags) {
            additionalInfo += `<div class="text-xs text-blue-500 mt-1"><span class="font-medium">Tags:</span> ${participant.hashtags}</div>`;
        }
        if (participant.country) {
            additionalInfo += `<div class="text-xs text-green-600 mt-1"><span class="font-medium">Country:</span> ${participant.country}</div>`;
        }
        
        div.innerHTML = `
            <input type="checkbox" class="participant-checkbox mr-3 h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
            <div class="flex-1">
                <div class="font-medium text-gray-900">${participant.name}</div>
                <div class="text-sm text-gray-500">${participant.email}</div>
                ${participant.organization ? `<div class="text-xs text-gray-400">${participant.organization}</div>` : ''}
                ${additionalInfo}
            </div>
        `;
        
        return div;
    }

    // ===================== Session Status Management =====================
    
    // Status management variables
    const statusDraft = document.getElementById('status_draft');
    const statusPublished = document.getElementById('status_published');
    const draftStatus = document.getElementById('draft_status');
    const publishStatus = document.getElementById('publish_status');
    const draftMessage = document.getElementById('draft_message');
    const publishMessage = document.getElementById('publish_message');
    const publishBtn = document.getElementById('publish_btn');
    const saveDraftBtn = document.getElementById('save_draft_btn');
    
    // Auto-save variables
    let autoSaveTimeout;
    let currentDraftSessionId = {{ $session->id }};
    
    // Update status display
    function updateStatusDisplay() {
        if (statusDraft.checked) {
            draftStatus.classList.remove('hidden');
            publishStatus.classList.add('hidden');
            draftMessage.classList.remove('hidden');
            publishMessage.classList.add('hidden');
            publishBtn.textContent = 'Update Session';
        } else {
            draftStatus.classList.add('hidden');
            publishStatus.classList.remove('hidden');
            draftMessage.classList.add('hidden');
            publishMessage.classList.remove('hidden');
            publishBtn.textContent = 'Publish Session';
        }
    }
    
    // Check required fields
    function checkRequiredFields() {
        const title = document.getElementById('title').value.trim();
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const conferenceId = document.getElementById('conference_id').value;
        
        return title && startTime && endTime && conferenceId;
    }
    
    // Auto-save draft
    function autoSaveDraft() {
        if (!checkRequiredFields()) {
            return;
        }
        
        const formData = new FormData();
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('start_time', document.getElementById('start_time').value);
        formData.append('end_time', document.getElementById('end_time').value);
        formData.append('conference_id', document.getElementById('conference_id').value);
        formData.append('venue_id', document.getElementById('venue_id').value);
        formData.append('room', document.getElementById('room').value);
        formData.append('status', 'draft');
        formData.append('draft_session_id', currentDraftSessionId);
        formData.append('participants', JSON.stringify(Array.from(selectedParticipants)));
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
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
            if (data.success) {
                currentDraftSessionId = data.session_id;
                showNotification('Draft saved automatically', 'success');
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
        });
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Debounced auto-save
    function debouncedAutoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            autoSaveDraft();
        }, 2000);
    }
    
    // Publish existing draft
    function publishExistingDraft() {
        if (!currentDraftSessionId) {
            showNotification('No session to publish', 'error');
            return;
        }

        // Ensure any currently-checked available participants are included
        addCheckedAvailableToSelected();
        updateParticipantsInput();

        const formData = new FormData();
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('start_time', document.getElementById('start_time').value);
        formData.append('end_time', document.getElementById('end_time').value);
        formData.append('conference_id', document.getElementById('conference_id').value);
        formData.append('venue_id', document.getElementById('venue_id').value);
        formData.append('room', document.getElementById('room').value);
        formData.append('participants', JSON.stringify(Array.from(selectedParticipants)));
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
        formData.append('session_id', currentSessionId); // Include current session ID to exclude it from conflicts
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
    

    // Add event listeners for time changes to trigger conflict checking
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    if (startTimeInput) {
        startTimeInput.addEventListener('change', debouncedConflictCheck);
        startTimeInput.addEventListener('input', debouncedConflictCheck);
    }
    
    if (endTimeInput) {
        endTimeInput.addEventListener('change', debouncedConflictCheck);
        endTimeInput.addEventListener('input', debouncedConflictCheck);
    }

    // ===================== Event Listeners =====================
    
    // Status radio button listeners
    if (statusDraft) {
        statusDraft.addEventListener('change', updateStatusDisplay);
    }
    if (statusPublished) {
        statusPublished.addEventListener('change', updateStatusDisplay);
    }
    
    // Auto-save on form field changes
    const formFields = ['title', 'description', 'start_time', 'end_time', 'conference_id', 'room'];
    formFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', debouncedAutoSave);
            field.addEventListener('change', debouncedAutoSave);
        }
    });
    
    // Save draft button
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            statusDraft.checked = true;
            updateStatusDisplay();
            autoSaveDraft();
        });
    }
    
    // Publish button
    if (publishBtn) {
        publishBtn.addEventListener('click', function(e) {
            if (statusDraft.checked) {
                e.preventDefault();
                publishExistingDraft();
            }
            // If published, let the form submit normally
        });
    }
    
    // Initialize status display
    updateStatusDisplay();

    // ===================== Event Listeners =====================
    
    // Conference search and dropdown
    if (conferenceSearch) {
        conferenceSearch.addEventListener('input', function() {
            const query = this.value;
            if (query.trim()) {
                filteredConferences = filterConferences(query);
                renderConferenceOptions(filteredConferences);
                conferenceDropdown.classList.remove('hidden');
                isConferenceDropdownOpen = true;
            } else {
                conferenceDropdown.classList.add('hidden');
                isConferenceDropdownOpen = false;
            }
        });
        
        conferenceSearch.addEventListener('focus', function() {
            if (this.value.trim()) {
                filteredConferences = filterConferences(this.value);
                renderConferenceOptions(filteredConferences);
                conferenceDropdown.classList.remove('hidden');
                isConferenceDropdownOpen = true;
            }
        });
        
        conferenceSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                conferenceDropdown.classList.add('hidden');
                isConferenceDropdownOpen = false;
            }
        });
    }
    
    if (conferenceDropdownToggle) {
        conferenceDropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isConferenceDropdownOpen) {
                conferenceDropdown.classList.add('hidden');
                isConferenceDropdownOpen = false;
            } else {
                conferenceDropdown.classList.remove('hidden');
                isConferenceDropdownOpen = true;
                renderConferenceOptions(filteredConferences);
            }
        });
    }
    
    if (clearConferenceBtn) {
        clearConferenceBtn.addEventListener('click', clearConferenceSelection);
    }
    
    // Venue search and dropdown
    if (venueSearch) {
        venueSearch.addEventListener('input', function() {
            const query = this.value;
            if (query.trim()) {
                filteredVenues = filterVenues(query);
                renderVenueOptions(filteredVenues);
                venueDropdown.classList.remove('hidden');
                isVenueDropdownOpen = true;
            } else {
                venueDropdown.classList.add('hidden');
                isVenueDropdownOpen = false;
            }
        });
        
        venueSearch.addEventListener('focus', function() {
            if (this.value.trim()) {
                filteredVenues = filterVenues(this.value);
                renderVenueOptions(filteredVenues);
                venueDropdown.classList.remove('hidden');
                isVenueDropdownOpen = true;
            }
        });
        
        venueSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                venueDropdown.classList.add('hidden');
                isVenueDropdownOpen = false;
            }
        });
    }
    
    if (venueDropdownToggle) {
        venueDropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isVenueDropdownOpen) {
                venueDropdown.classList.add('hidden');
                isVenueDropdownOpen = false;
            } else {
                venueDropdown.classList.remove('hidden');
                isVenueDropdownOpen = true;
                renderVenueOptions(filteredVenues);
            }
        });
    }
    
    if (clearVenueBtn) {
        clearVenueBtn.addEventListener('click', clearVenueSelection);
    }
    
    // Venue modal
    if (openVenueModalBtn) {
        openVenueModalBtn.addEventListener('click', openVenueModal);
    }
    
    if (closeVenueModal) {
        closeVenueModal.addEventListener('click', closeVenueModalFunc);
    }
    
    if (cancelVenueModal) {
        cancelVenueModal.addEventListener('click', closeVenueModalFunc);
    }
    
    if (venueForm) {
        venueForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            addVenue(formData);
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#conference_search') && !e.target.closest('#conference_dropdown') && !e.target.closest('#conference_dropdown_toggle')) {
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
        }
        
        if (!e.target.closest('#venue_search') && !e.target.closest('#venue_dropdown') && !e.target.closest('#venue_dropdown_toggle')) {
            venueDropdown.classList.add('hidden');
            isVenueDropdownOpen = false;
        }
    });
    
    // Initialize data and set initial values
    async function initializeForm() {
        try {
            await Promise.all([loadConferences(), loadVenues()]);
            
            // Set initial values for edit form after data is loaded
            const currentConferenceId = conferenceIdInput.value;
            const currentVenueId = venueIdInput.value;
            
            console.log('Initializing form with:', { currentConferenceId, currentVenueId, conferences: conferences.length, venues: venues.length });
            
            if (currentConferenceId) {
                // Find and set the current conference
                const currentConference = conferences.find(c => c.id == currentConferenceId);
                console.log('Found conference:', currentConference);
                if (currentConference) {
                    selectConference(currentConference);
                    // Load participants for the current conference
                    await loadParticipantsForConference(currentConferenceId);
                } else {
                    console.error('Conference not found for ID:', currentConferenceId);
                }
            }
            
            if (currentVenueId) {
                // Find and set the current venue
                const currentVenue = venues.find(v => v.id == currentVenueId);
                console.log('Found venue:', currentVenue);
                if (currentVenue) {
                    selectVenue(currentVenue);
                } else {
                    console.error('Venue not found for ID:', currentVenueId);
                }
            }
        } catch (error) {
            console.error('Error initializing form:', error);
        }
    }
    
    // Initialize the form
    initializeForm();
}); // Close DOMContentLoaded function
</script>

<style>
/* Ensure all X buttons in selected participants are right-aligned */
.selected-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.selected-item .remove-participant-btn {
    margin-left: auto !important;
    order: 2 !important;
    flex-shrink: 0 !important;
}

.selected-item .flex-1 {
    order: 1 !important;
    flex: 1 !important;
}

/* Ensure conflict X buttons are right-aligned */
.conflict-x-button {
    margin-left: auto !important;
    float: right !important;
}

/* Ensure proper alignment for conflict indicators */
.selected-item .conflict-indicator {
    margin-left: auto;
    display: flex;
    justify-content: flex-end;
}
</style>
@endsection 