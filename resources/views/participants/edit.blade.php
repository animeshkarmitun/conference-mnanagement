@extends('layouts.app')

@section('title', 'Edit Participant')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Edit Participant</h2>
    <form method="POST" action="{{ route('participants.update', $participant) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
            <select name="user_id" id="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $participant->user_id) == $user->id ? 'selected' : '' }}>{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- User Information Fields -->
        <div class="mb-4">
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $participant->user->first_name ?? $participant->user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('first_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $participant->user->last_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('last_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $participant->user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference</label>
            <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Conference</option>
                @foreach($conferences as $conference)
                    <option value="{{ $conference->id }}" {{ old('conference_id', $participant->conference_id) == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                @endforeach
            </select>
            @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            
            <!-- Conference Change Warning -->
            <div id="conference-change-warning" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md hidden">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Warning: Conference Change</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Changing the conference will automatically remove:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>All session assignments</li>
                                <li>Travel details and arrangements</li>
                                <li>Room allocations</li>
                                <li>Check-in records</li>
                                <li>Reset registration status to pending</li>
                            </ul>
                            <p class="mt-2 font-medium">The participant will be notified of this change via email.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <label for="participant_type_id" class="block text-sm font-medium text-gray-700">Type</label>
            <select name="participant_type_id" id="participant_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Type</option>
                @foreach($participantTypes as $type)
                    <option value="{{ $type->id }}" {{ old('participant_type_id', $participant->participant_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('participant_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="visa_status" class="block text-sm font-medium text-gray-700">Visa Status</label>
            <select name="visa_status" id="visa_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="required" {{ old('visa_status', $participant->visa_status) == 'required' ? 'selected' : '' }}>Required</option>
                <option value="not_required" {{ old('visa_status', $participant->visa_status) == 'not_required' ? 'selected' : '' }}>Not Required</option>
                <option value="pending" {{ old('visa_status', $participant->visa_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ old('visa_status', $participant->visa_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="issue" {{ old('visa_status', $participant->visa_status) == 'issue' ? 'selected' : '' }}>Issue (Problem)</option>
            </select>
            @error('visa_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4" id="visa-issue-description" style="display: {{ old('visa_status', $participant->visa_status) == 'issue' ? 'block' : 'none' }};">
            <label for="visa_issue_description" class="block text-sm font-medium text-gray-700">Visa Issue Description</label>
            <textarea name="visa_issue_description" id="visa_issue_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Please describe the visa issue or problem...">{{ old('visa_issue_description', $participant->visa_issue_description) }}</textarea>
            @error('visa_issue_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="travel_form_submitted" class="block text-sm font-medium text-gray-700">Travel Form Submitted</label>
            <input type="checkbox" name="travel_form_submitted" id="travel_form_submitted" value="1" {{ old('travel_form_submitted', $participant->travel_form_submitted) ? 'checked' : '' }}>
            @error('travel_form_submitted')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
            <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('bio', $participant->bio) }}</textarea>
            @error('bio')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="approved" class="block text-sm font-medium text-gray-700">Approved</label>
            <input type="checkbox" name="approved" id="approved" value="1" {{ old('approved', $participant->approved) ? 'checked' : '' }}>
            @error('approved')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
            <input type="text" name="organization" id="organization" value="{{ old('organization', $participant->organization) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('organization')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="dietary_needs" class="block text-sm font-medium text-gray-700">Dietary Needs</label>
            <input type="text" name="dietary_needs" id="dietary_needs" value="{{ old('dietary_needs', $participant->dietary_needs) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('dietary_needs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="travel_intent" class="block text-sm font-medium text-gray-700">Travel Intent</label>
            <input type="checkbox" name="travel_intent" id="travel_intent" value="1" {{ old('travel_intent', $participant->travel_intent) ? 'checked' : '' }}>
            @error('travel_intent')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label for="registration_status" class="block text-sm font-medium text-gray-700">Registration Status</label>
            <select name="registration_status" id="registration_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="pending" {{ old('registration_status', $participant->registration_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ old('registration_status', $participant->registration_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ old('registration_status', $participant->registration_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('registration_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('participants.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Update</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visaStatusSelect = document.getElementById('visa_status');
    const visaIssueDescription = document.getElementById('visa-issue-description');
    
    function toggleVisaIssueDescription() {
        if (visaStatusSelect.value === 'issue') {
            visaIssueDescription.style.display = 'block';
        } else {
            visaIssueDescription.style.display = 'none';
        }
    }
    
    // Initial state
    toggleVisaIssueDescription();
    
    // Listen for changes
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    
    // Conference change warning
    const conferenceSelect = document.getElementById('conference_id');
    const conferenceWarning = document.getElementById('conference-change-warning');
    const originalConferenceId = conferenceSelect.value;
    
    function toggleConferenceWarning() {
        if (conferenceSelect.value && conferenceSelect.value !== originalConferenceId) {
            conferenceWarning.classList.remove('hidden');
        } else {
            conferenceWarning.classList.add('hidden');
        }
    }
    
    conferenceSelect.addEventListener('change', toggleConferenceWarning);
    toggleConferenceWarning(); // Initial call
});
</script>
@endsection 