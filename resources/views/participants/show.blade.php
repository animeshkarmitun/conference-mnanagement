@extends('layouts.app')

@section('title', 'Participant Details')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">
            {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
        </h1>
        <div class="text-gray-600 text-lg font-medium">
            {{ $participant->participantType->name ?? '' }}
        </div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 participant-details-fix mt-8 min-h-[60vh]">
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
<style>
    /* Targeted fix for participant details page layout shift */
    .participant-details-fix {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
</style>
@endpush 

@endsection