@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ isset($participant->user->first_name) ? $participant->user->first_name : $participant->user->name }} {{ isset($participant->user->last_name) ? $participant->user->last_name : '' }}
                        </h1>
                        <p class="text-gray-600">
                            {{ isset($participant->participantType->name) ? $participant->participantType->name : '' }} - {{ isset($participant->conference->name) ? $participant->conference->name : 'Conference' }}
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('participants.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Participants
                        </a>
                        <a href="{{ route('participants.edit', $participant) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-edit mr-2"></i>Edit Participant
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Single Page Layout - All Sections -->
                <div class="space-y-8">
                    
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
                                Sessions Management
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
                                Status Management
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
            </div>
        </div>
    </div>
</div>
@endsection