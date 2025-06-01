@extends('layouts.app')

@section('title', 'Participant Details')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</h2>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Email:</span>
        <span>{{ $participant->user->email }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Conference:</span>
        <span>{{ $participant->conference->title ?? '' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Type:</span>
        <span>{{ $participant->participantType->name ?? '' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Visa Status:</span>
        <span>{{ ucfirst($participant->visa_status) }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Travel Form Submitted:</span>
        <span>{{ $participant->travel_form_submitted ? 'Yes' : 'No' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Bio:</span>
        <span>{{ $participant->bio }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Approved:</span>
        <span>{{ $participant->approved ? 'Yes' : 'No' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Organization:</span>
        <span>{{ $participant->organization }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Dietary Needs:</span>
        <span>{{ $participant->dietary_needs }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Travel Intent:</span>
        <span>{{ $participant->travel_intent ? 'Yes' : 'No' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Registration Status:</span>
        <span>{{ ucfirst($participant->registration_status) }}</span>
    </div>
    <div class="flex justify-end mt-6">
        <a href="{{ route('participants.edit', $participant) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold mr-2">Edit</a>
        <form method="POST" action="{{ route('participants.destroy', $participant) }}" onsubmit="return confirm('Are you sure you want to delete this participant?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
        </form>
    </div>
    <div class="mt-4">
        <a href="{{ route('participants.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>
@endsection 