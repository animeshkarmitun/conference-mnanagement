@extends('layouts.app')

@section('title', 'Create Session')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Create Session</h2>
    <form method="POST" action="{{ route('sessions.store') }}">
        @csrf
        <div class="mb-4">
            <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference</label>
            <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Conference</option>
                @foreach($conferences as $conference)
                    <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                @endforeach
            </select>
            @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="venue_id" class="block text-sm font-medium text-gray-700">Venue</label>
            <select name="venue_id" id="venue_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Venue</option>
                @foreach($venues as $venue)
                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                @endforeach
            </select>
            @error('venue_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @error('start_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @error('end_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="room" class="block text-sm font-medium text-gray-700">Room</label>
                <input type="text" name="room" id="room" value="{{ old('room') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @error('room')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @error('capacity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="participants" class="block text-sm font-medium text-gray-700">Participants</label>
            <select name="participants[]" id="participants" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @foreach($participants as $participant)
                    <option value="{{ $participant->id }}" {{ in_array($participant->id, old('participants', [])) ? 'selected' : '' }}>
                        {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }} ({{ $participant->user->email }})
                    </option>
                @endforeach
            </select>
            @error('participants')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('sessions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Create Session</button>
        </div>
    </form>
</div>

<script>
    const conferenceVenues = @json($conferenceVenues);
    const conferenceDates = @json($conferenceDates);
    document.getElementById('conference_id').addEventListener('change', function() {
        const confId = this.value;
        const venueId = conferenceVenues[confId];
        if (venueId) {
            document.getElementById('venue_id').value = venueId;
        }
        if (conferenceDates[confId]) {
            // Format as 'YYYY-MM-DDTHH:MM' for datetime-local input
            const start = conferenceDates[confId].start_date + 'T09:00';
            const end = conferenceDates[confId].end_date + 'T17:00';
            document.getElementById('start_time').value = start;
            document.getElementById('end_time').value = end;
        }
    });
</script>
@endsection 