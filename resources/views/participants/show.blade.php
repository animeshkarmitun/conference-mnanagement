@extends('layouts.participant')

@section('title', 'My Profile')

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

<!-- Profile Switcher -->
@if(isset($allParticipants) && $allParticipants->count() > 1)
<div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Switch Participant Profile:</span>
        </div>
        <div class="flex space-x-2">
            @foreach($allParticipants as $profile)
                <form method="POST" action="{{ route('participants.switch-profile', $profile->id) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full transition-colors duration-200
                                   @if($profile->id === $participant->id)
                                       bg-yellow-100 text-yellow-800 border border-yellow-200
                                   @else
                                       bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200
                                   @endif">
                        @if($profile->id === $participant->id)
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        {{ $profile->getProfileDisplayName() }}
                        @if($profile->is_primary)
                            <span class="ml-1 text-xs bg-yellow-200 text-yellow-800 px-1.5 py-0.5 rounded-full">Primary</span>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
    <div class="mt-2 text-xs text-gray-500">
        Currently viewing: <strong>{{ $participant->getProfileDisplayName() }}</strong> 
        @if($participant->conference)
            for <strong>{{ $participant->conference->name }}</strong>
        @endif
    </div>
</div>
@endif

<hr class="mb-8 border-yellow-200">

<!-- Single Page Layout - All Sections -->
<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- Personal Information Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user mr-3 text-blue-600"></i>
                Personal Information
            </h2>
        </div>
        <div class="p-6">
            @include('participants.partials.profile-info', ['participant' => $participant])
        </div>
    </div>

    <!-- Travel & Accommodation Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-plane mr-3 text-green-600"></i>
                Travel & Accommodation
            </h2>
        </div>
        <div class="p-6">
            @include('participants.partials.profile-travel', ['participant' => $participant, 'travelDetail' => $travelDetail, 'hotels' => $hotels, 'roomTypes' => $roomTypes])
        </div>
    </div>

    <!-- Sessions Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-calendar-alt mr-3 text-purple-600"></i>
                My Sessions
            </h2>
    </div>
        <div class="p-6">
            @include('participants.partials.profile-sessions', ['sessions' => $sessions, 'participant' => $participant])
    </div>
    </div>

    <!-- Status Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-check-circle mr-3 text-yellow-600"></i>
                Registration Status
            </h2>
    </div>
        <div class="p-6">
        @include('participants.partials.profile-status', ['participant' => $participant])
    </div>
    </div>

    <!-- Conference Documents Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-file-alt mr-3 text-indigo-600"></i>
                Conference Documents
            </h2>
        </div>
        <div class="p-6">
            @include('participants.partials.profile-conference-docs', ['participant' => $participant])
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-bell mr-3 text-red-600"></i>
                Notifications
            </h2>
        </div>
        <div class="p-6">
            @include('participants.partials.profile-notifications', ['notifications' => $notifications, 'participant' => $participant])
        </div>
    </div>

    <!-- Comments Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-comments mr-3 text-teal-600"></i>
                Comments
            </h2>
        </div>
        <div class="p-6">
            @include('participants.partials.profile-comments', ['comments' => $comments, 'participant' => $participant])
        </div>
    </div>

</div>

<!-- Back to Dashboard Link -->
<div class="max-w-4xl mx-auto mt-8">
    <a href="{{ route('participant-dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Dashboard
    </a>
</div>

@endsection