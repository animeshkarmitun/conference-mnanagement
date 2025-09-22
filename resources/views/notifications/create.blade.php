@extends('layouts.app')

@section('title', 'Create Notification')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Create Notification</h2>
        
        <form method="POST" action="{{ route('notifications.store') }}">
            @csrf
            
            <div class="mb-4">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="conference_id" class="block text-sm font-medium text-gray-700 mb-2">Conference</label>
                <select name="conference_id" id="conference_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select a conference</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}">{{ $conference->name }}</option>
                    @endforeach
                </select>
                @error('conference_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select notification type</option>
                    <option value="General">General</option>
                    <option value="TaskUpdate">Task Update</option>
                    <option value="ConferenceUpdate">Conference Update</option>
                    <option value="SessionUpdate">Session Update</option>
                    <option value="TravelUpdate">Travel Update</option>
                    <option value="ProfileUpdate">Profile Update</option>
                    <option value="MissingDocuments">Missing Documents</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" id="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter notification message..." required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="related_model" class="block text-sm font-medium text-gray-700 mb-2">Related Model (Optional)</label>
                <input type="text" name="related_model" id="related_model" value="{{ old('related_model') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Task, Conference, Session">
                @error('related_model')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="related_id" class="block text-sm font-medium text-gray-700 mb-2">Related ID (Optional)</label>
                <input type="number" name="related_id" id="related_id" value="{{ old('related_id') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 1, 2, 3">
                @error('related_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="action_url" class="block text-sm font-medium text-gray-700 mb-2">Action URL (Optional)</label>
                <input type="url" name="action_url" id="action_url" value="{{ old('action_url') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., /tasks/1, /conferences/1">
                @error('action_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('notifications.index') }}" class="px-6 py-3 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" style="background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 999 !important; height: auto !important;">
                        Create Notification
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = document.querySelector('button[type="submit"]');
    
    console.log('Form found:', form);
    console.log('Submit button found:', submitButton);
    
    if (submitButton) {
        submitButton.style.display = 'block';
        submitButton.style.visibility = 'visible';
        submitButton.style.opacity = '1';
        submitButton.style.background = '#3b82f6';
        submitButton.style.color = 'white';
        submitButton.style.padding = '12px 24px';
        submitButton.style.border = 'none';
        submitButton.style.borderRadius = '8px';
        submitButton.style.fontWeight = '500';
        submitButton.style.cursor = 'pointer';
        console.log('Submit button styles applied');
    } else {
        console.log('Submit button not found!');
    }
    
    form.addEventListener('submit', function(e) {
        console.log('Form submitted');
        // Let the form submit normally
    });
});
</script>
@endpush
@endsection
