@extends('layouts.participant')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Participant Profile</h1>
                    <p class="mt-2 text-gray-600">Register for a new conference with a different participant profile</p>
                </div>
                <a href="{{ route('participant-profiles.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Profiles
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('participant-profiles.store') }}" class="space-y-6 p-6">
                @csrf

                <!-- Conference Selection -->
                <div>
                    <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference *</label>
                    <select name="conference_id" id="conference_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('conference_id') border-red-300 @enderror"
                            required>
                        <option value="">Select a conference</option>
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}" 
                                    {{ old('conference_id') == $conference->id ? 'selected' : '' }}
                                    data-start-date="{{ $conference->start_date }}"
                                    data-end-date="{{ $conference->end_date }}">
                                {{ $conference->name }} ({{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('conference_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Profile Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="profile_name" class="block text-sm font-medium text-gray-700">Profile Name *</label>
                        <input type="text" name="profile_name" id="profile_name" 
                               value="{{ old('profile_name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('profile_name') border-red-300 @enderror"
                               placeholder="e.g., John Doe - Academic Profile"
                               required>
                        @error('profile_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="profile_type" class="block text-sm font-medium text-gray-700">Profile Type *</label>
                        <select name="profile_type" id="profile_type" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('profile_type') border-red-300 @enderror"
                                required>
                            <option value="">Select profile type</option>
                            <option value="personal" {{ old('profile_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                            <option value="professional" {{ old('profile_type') == 'professional' ? 'selected' : '' }}>Professional</option>
                            <option value="academic" {{ old('profile_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="media" {{ old('profile_type') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="speaker" {{ old('profile_type') == 'speaker' ? 'selected' : '' }}>Speaker</option>
                        </select>
                        @error('profile_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="profile_description" class="block text-sm font-medium text-gray-700">Profile Description</label>
                    <textarea name="profile_description" id="profile_description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('profile_description') border-red-300 @enderror"
                              placeholder="Brief description of this participant profile...">{{ old('profile_description') }}</textarea>
                    @error('profile_description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Participant Type -->
                <div>
                    <label for="participant_type_id" class="block text-sm font-medium text-gray-700">Participant Type *</label>
                    <select name="participant_type_id" id="participant_type_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('participant_type_id') border-red-300 @enderror"
                            required>
                        <option value="">Select participant type</option>
                        @foreach($participantTypes as $type)
                            <option value="{{ $type->id }}" {{ old('participant_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('participant_type_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="visa_status" class="block text-sm font-medium text-gray-700">Visa Status</label>
                        <select name="visa_status" id="visa_status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('visa_status') border-red-300 @enderror">
                            <option value="pending" {{ old('visa_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="required" {{ old('visa_status') == 'required' ? 'selected' : '' }}>Required</option>
                            <option value="not_required" {{ old('visa_status') == 'not_required' ? 'selected' : '' }}>Not Required</option>
                            <option value="approved" {{ old('visa_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="issue" {{ old('visa_status') == 'issue' ? 'selected' : '' }}>Issue</option>
                        </select>
                        @error('visa_status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="travel_intent" class="block text-sm font-medium text-gray-700">Travel Intent</label>
                        <select name="travel_intent" id="travel_intent" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('travel_intent') border-red-300 @enderror">
                            <option value="national" {{ old('travel_intent') == 'national' ? 'selected' : '' }}>National</option>
                            <option value="international" {{ old('travel_intent') == 'international' ? 'selected' : '' }}>International</option>
                        </select>
                        @error('travel_intent')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Visa Issue Description (conditional) -->
                <div id="visa_issue_description_field" style="display: none;">
                    <label for="visa_issue_description" class="block text-sm font-medium text-gray-700">Visa Issue Description</label>
                    <textarea name="visa_issue_description" id="visa_issue_description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('visa_issue_description') border-red-300 @enderror"
                              placeholder="Please describe the visa issue...">{{ old('visa_issue_description') }}</textarea>
                    @error('visa_issue_description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea name="bio" id="bio" rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('bio') border-red-300 @enderror"
                              placeholder="Brief biography...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Organization -->
                <div>
                    <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
                    <input type="text" name="organization" id="organization" 
                           value="{{ old('organization') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('organization') border-red-300 @enderror"
                           placeholder="Your organization or institution">
                    @error('organization')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conflict Warning -->
                <div id="conflict-warning" class="hidden bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.726-1.36 3.491 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Conference Conflict Detected</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This conference has overlapping dates with your existing participant profiles. Please review the conflicts below:</p>
                                <ul id="conflict-list" class="mt-2 list-disc list-inside">
                                    <!-- Conflicts will be populated here -->
                                </ul>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center">
                                    <input id="ignore_conflicts" name="ignore_conflicts" type="checkbox" 
                                           class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                    <label for="ignore_conflicts" class="ml-2 block text-sm text-yellow-700">
                                        I understand the conflicts and want to proceed anyway
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('participant-profiles.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Create Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conferenceSelect = document.getElementById('conference_id');
    const conflictWarning = document.getElementById('conflict-warning');
    const conflictList = document.getElementById('conflict-list');
    const visaStatusSelect = document.getElementById('visa_status');
    const visaIssueField = document.getElementById('visa_issue_description_field');

    // Handle visa status change
    visaStatusSelect.addEventListener('change', function() {
        if (this.value === 'issue') {
            visaIssueField.style.display = 'block';
        } else {
            visaIssueField.style.display = 'none';
        }
    });

    // Handle conference selection change
    conferenceSelect.addEventListener('change', function() {
        if (this.value) {
            checkConflicts(this.value);
        } else {
            conflictWarning.classList.add('hidden');
        }
    });

    function checkConflicts(conferenceId) {
        fetch('{{ route("participant-profiles.check-conflicts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                conference_id: conferenceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_conflicts) {
                conflictList.innerHTML = '';
                data.conflicts.forEach(conflict => {
                    const li = document.createElement('li');
                    li.textContent = conflict.details;
                    conflictList.appendChild(li);
                });
                conflictWarning.classList.remove('hidden');
            } else {
                conflictWarning.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error checking conflicts:', error);
        });
    }
});
</script>
@endsection
