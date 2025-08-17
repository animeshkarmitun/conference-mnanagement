@extends('layouts.participant')

@section('title', 'Conference Kit')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Conference Kit</h1>
                <p class="text-blue-100 text-lg">{{ $participant->conference->name ?? 'Conference' }}</p>
                <p class="text-blue-200">Welcome, {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }} ({{ $participant->participantType->name ?? '' }})</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('conference-kit.download', $conferenceKit) }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('participants.profile') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Session Links Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Session Links & Schedule</h2>
                <p class="text-gray-600">Access all your conference sessions and meeting links</p>
            </div>
        </div>

        @if($sessionLinks->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($sessionLinks as $sessionLink)
                    @php $content = json_decode($sessionLink->content, true); @endphp
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $content['title'] ?? 'Session' }}</h3>
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $content['time'] ?? 'Time TBD' }}
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mb-3">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $content['room'] ?? 'Room TBD' }}
                                </div>
                                <p class="text-gray-700 mb-3">{{ $content['description'] ?? 'No description available.' }}</p>
                                <div class="text-sm text-gray-600">
                                    <strong>Speaker:</strong> {{ $content['speaker'] ?? 'TBD' }}
                                </div>
                            </div>
                        </div>
                        @if(isset($content['zoom_link']) && $content['zoom_link'])
                            <a href="{{ $content['zoom_link'] }}" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Join Session
                            </a>
                        @else
                            <span class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-600 text-sm font-medium rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Link Coming Soon
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Sessions Available Yet</h3>
                <p class="text-gray-600">Session links will be added as they become available.</p>
            </div>
        @endif
    </div>

    <!-- Important Contacts Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Important Contacts</h2>
                <p class="text-gray-600">Get in touch with conference organizers and support team</p>
            </div>
        </div>

        @if($contacts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($contacts as $contact)
                    @php $content = json_decode($contact->content, true); @endphp
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $content['name'] ?? 'Contact' }}</h3>
                                <p class="text-sm text-gray-600">{{ $content['role'] ?? '' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if(isset($content['email']) && $content['email'])
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <a href="mailto:{{ $content['email'] }}" class="text-blue-600 hover:text-blue-800">{{ $content['email'] }}</a>
                                </div>
                            @endif
                            @if(isset($content['phone']) && $content['phone'])
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <a href="tel:{{ $content['phone'] }}" class="text-blue-600 hover:text-blue-800">{{ $content['phone'] }}</a>
                                </div>
                            @endif
                            @if(isset($content['availability']) && $content['availability'])
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $content['availability'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Contacts Available Yet</h3>
                <p class="text-gray-600">Contact information will be added as it becomes available.</p>
            </div>
        @endif
    </div>

    <!-- City Guide Section -->
    @if($cityGuide)
        @php $content = json_decode($cityGuide->content, true); @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $content['title'] ?? 'City Guide' }}</h2>
                    <p class="text-gray-600">Essential information for your conference stay</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Welcome & Overview</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $content['description'] ?? 'No description available.' }}</p>
                </div>

                <div class="space-y-6">
                    @if(isset($content['transportation']) && $content['transportation'])
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Transportation
                            </h4>
                            <p class="text-gray-700 text-sm">{{ $content['transportation'] }}</p>
                        </div>
                    @endif

                    @if(isset($content['restaurants']) && $content['restaurants'])
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18z"></path>
                                </svg>
                                Dining Options
                            </h4>
                            <p class="text-gray-700 text-sm">{{ $content['restaurants'] }}</p>
                        </div>
                    @endif

                    @if(isset($content['attractions']) && $content['attractions'])
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Local Attractions
                            </h4>
                            <p class="text-gray-700 text-sm">{{ $content['attractions'] }}</p>
                        </div>
                    @endif

                    @if(isset($content['emergency_contacts']) && $content['emergency_contacts'])
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Emergency Contacts
                            </h4>
                            <p class="text-gray-700 text-sm">{{ $content['emergency_contacts'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="bg-gray-50 rounded-xl p-6 text-center">
        <p class="text-gray-600 mb-4">Need help? Contact the conference support team or visit your profile for more options.</p>
        <div class="flex items-center justify-center space-x-4">
            <a href="{{ route('participants.profile') }}" class="text-blue-600 hover:text-blue-800 font-medium">Back to Profile</a>
            <span class="text-gray-400">•</span>
            <a href="{{ route('conference-kit.download', $conferenceKit) }}" class="text-blue-600 hover:text-blue-800 font-medium">Download PDF</a>
        </div>
    </div>
</div>
@endsection
