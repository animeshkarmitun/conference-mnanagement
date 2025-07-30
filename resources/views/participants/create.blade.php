@extends('layouts.app')

@section('title', 'Add Participant')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Add Participant</h2>
    <form method="POST" action="{{ route('participants.store') }}">
        @csrf
        <div class="mb-4">
            <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
            <select name="user_id" id="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference</label>
            <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Conference</option>
                @foreach($conferences as $conference)
                    <option value="{{ $conference->id }}">{{ $conference->title }}</option>
                @endforeach
            </select>
            @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="participant_type_id" class="block text-sm font-medium text-gray-700">Type</label>
            <select name="participant_type_id" id="participant_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Type</option>
                @foreach($participantTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
            @error('participant_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="visa_status" class="block text-sm font-medium text-gray-700">Visa Status</label>
            <select name="visa_status" id="visa_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="required">Required</option>
                <option value="not_required">Not Required</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="issue">Issue (Problem)</option>
            </select>
            @error('visa_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4" id="visa-issue-description" style="display: none;">
            <label for="visa_issue_description" class="block text-sm font-medium text-gray-700">Visa Issue Description</label>
            <textarea name="visa_issue_description" id="visa_issue_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Please describe the visa issue or problem..."></textarea>
            @error('visa_issue_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="travel_form_submitted" class="block text-sm font-medium text-gray-700">Travel Form Submitted</label>
            <input type="checkbox" name="travel_form_submitted" id="travel_form_submitted" value="1">
            @error('travel_form_submitted')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
            <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
            @error('bio')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="approved" class="block text-sm font-medium text-gray-700">Approved</label>
            <input type="checkbox" name="approved" id="approved" value="1">
            @error('approved')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
            <input type="text" name="organization" id="organization" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('organization')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="dietary_needs" class="block text-sm font-medium text-gray-700">Dietary Needs</label>
            <input type="text" name="dietary_needs" id="dietary_needs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('dietary_needs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
            @error('profile_picture')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="resume" class="block text-sm font-medium text-gray-700">Resume/CV</label>
            <input type="file" name="resume" id="resume" accept="application/pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500">
            @error('resume')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Travel Intent</label>
            <div class="mt-2 flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="travel_intent" value="1" class="form-radio text-yellow-600">
                    <span class="ml-2">Yes</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="travel_intent" value="0" class="form-radio text-yellow-600">
                    <span class="ml-2">No</span>
                </label>
            </div>
            @error('travel_intent')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label for="registration_status" class="block text-sm font-medium text-gray-700">Registration Status</label>
            <select name="registration_status" id="registration_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            @error('registration_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('participants.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Create</button>
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
});
</script>
@endsection 