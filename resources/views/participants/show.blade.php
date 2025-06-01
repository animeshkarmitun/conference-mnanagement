@extends('layouts.app')

@section('title', 'Participant Details')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6 flex items-center">
        <span>{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</span>
        <span class="ml-4 text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ $participant->participantType->name ?? '' }}</span>
    </h2>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button class="tab-link py-4 px-1 border-b-2 font-medium text-sm text-gray-600 focus:outline-none" data-tab="info">Personal Info</button>
            <button class="tab-link py-4 px-1 border-b-2 font-medium text-sm text-gray-600 focus:outline-none" data-tab="sessions">Sessions</button>
            <button class="tab-link py-4 px-1 border-b-2 font-medium text-sm text-gray-600 focus:outline-none" data-tab="status">Status</button>
            <button class="tab-link py-4 px-1 border-b-2 font-medium text-sm text-gray-600 focus:outline-none" data-tab="notifications">Notifications</button>
            <button class="tab-link py-4 px-1 border-b-2 font-medium text-sm text-gray-600 focus:outline-none" data-tab="comments">Comments</button>
            <button class="tab-link py-4 px-1 border-b-2 font-medium text-sm text-gray-600 focus:outline-none" data-tab="travel">Travel & Accommodation</button>
        </nav>
    </div>

    <!-- Tabs Content -->
    <div id="tab-info" class="tab-content">
        @include('participants.partials.profile-info', ['participant' => $participant])
    </div>
    <div id="tab-sessions" class="tab-content hidden">
        @include('participants.partials.profile-sessions', ['sessions' => $sessions ?? []])
    </div>
    <div id="tab-status" class="tab-content hidden">
        @include('participants.partials.profile-status', ['participant' => $participant])
    </div>
    <div id="tab-notifications" class="tab-content hidden">
        @include('participants.partials.profile-notifications', ['notifications' => $notifications ?? []])
    </div>
    <div id="tab-comments" class="tab-content hidden">
        @include('participants.partials.profile-comments', ['comments' => $comments ?? [], 'participant' => $participant])
    </div>
    <div id="tab-travel" class="tab-content hidden">
        @include('participants.partials.profile-travel', ['participant' => $participant, 'travelDetail' => $travelDetail ?? null, 'hotels' => $hotels ?? []])
    </div>

    <div class="mt-4">
        <a href="{{ route('participants.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        tabLinks.forEach(link => {
            link.addEventListener('click', function () {
                tabLinks.forEach(l => l.classList.remove('border-yellow-600', 'text-yellow-600'));
                tabContents.forEach(c => c.classList.add('hidden'));
                this.classList.add('border-yellow-600', 'text-yellow-600');
                document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
            });
        });
        // Activate first tab by default
        tabLinks[0].classList.add('border-yellow-600', 'text-yellow-600');
        tabContents[0].classList.remove('hidden');
    });
</script>
@endpush 