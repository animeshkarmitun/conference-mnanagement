@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">My Profile</h2>
    @if($participant)
        <div class="mb-4">
            <span class="font-semibold text-gray-700">Conference:</span>
            <span>{{ $participant->conference->title ?? '' }}</span>
        </div>
        <div class="mb-4">
            <span class="font-semibold text-gray-700">Type:</span>
            <span>{{ $participant->participantType->name ?? '' }}</span>
        </div>
        <div class="mb-4">
            <span class="font-semibold text-gray-700">Bio:</span>
            <span>{{ $participant->bio }}</span>
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
        <div class="mb-4">
            <span class="font-semibold text-gray-700">Sessions:</span>
            <ul class="list-disc ml-6">
                @forelse($participant->sessions as $session)
                    <li>{{ $session->title }} ({{ $session->start_time }})</li>
                @empty
                    <li>No sessions assigned.</li>
                @endforelse
            </ul>
        </div>
    @else
        <p class="text-gray-500">No profile found for your account.</p>
    @endif
</div>
@endsection 