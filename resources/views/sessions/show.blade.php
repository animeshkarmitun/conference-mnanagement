@extends('layouts.app')

@section('title', 'Session Details')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">{{ $session->title }}</h2>
    
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Conference:</span>
        <span>{{ $session->conference->title }}</span>
    </div>

    <div class="mb-4">
        <span class="font-semibold text-gray-700">Description:</span>
        <p class="mt-1">{{ $session->description }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Start Time:</span>
            <span>{{ $session->start_time->format('M d, Y H:i') }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">End Time:</span>
            <span>{{ $session->end_time->format('M d, Y H:i') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Room:</span>
            <span>{{ $session->room }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Capacity:</span>
            <span>{{ $session->capacity }}</span>
        </div>
    </div>

    <div class="mb-6">
        <span class="font-semibold text-gray-700">Participants:</span>
        <ul class="mt-2 space-y-2">
            @forelse($session->participants as $participant)
                <li class="flex items-center">
                    <span class="text-gray-600">
                        {{ $participant->user->first_name ?? $participant->user->name }} 
                        {{ $participant->user->last_name ?? '' }}
                        ({{ $participant->user->email }})
                    </span>
                </li>
            @empty
                <li class="text-gray-500">No participants assigned to this session.</li>
            @endforelse
        </ul>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('sessions.edit', $session) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Edit</a>
        <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this session?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('sessions.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>
@endsection 