<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Conference Kit</h3>
                <p class="text-gray-600">Access your conference materials, session links, and important information.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('conference-kit.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Full Kit
                </a>
            </div>
        </div>
    </div>

    @php
        // Get participant's conference kit
        $conferenceKit = \App\Models\ConferenceKit::with(['conferenceKitItems'])
            ->where('conference_id', $participant->conference_id)
            ->first();
    @endphp

    @if($conferenceKit)
        @php
            $sessionLinks = $conferenceKit->conferenceKitItems->where('type', 'SessionLink');
            $contacts = $conferenceKit->conferenceKitItems->where('type', 'Contact');
            $cityGuide = $conferenceKit->conferenceKitItems->where('type', 'CityGuide')->first();
        @endphp

        <!-- Quick Access Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Session Links -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Session Links</h4>
                        <p class="text-sm text-gray-500">{{ $sessionLinks->count() }} sessions available</p>
                    </div>
                </div>
                @if($sessionLinks->count() > 0)
                    <div class="space-y-2">
                        @foreach($sessionLinks->take(3) as $sessionLink)
                            @php $content = json_decode($sessionLink->content, true); @endphp
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $content['title'] ?? 'Session' }}</div>
                                <div class="text-gray-500">{{ $content['time'] ?? '' }} • {{ $content['room'] ?? '' }}</div>
                            </div>
                        @endforeach
                        @if($sessionLinks->count() > 3)
                            <div class="text-sm text-blue-600 font-medium">+{{ $sessionLinks->count() - 3 }} more sessions</div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">No session links available yet.</p>
                @endif
            </div>

            <!-- Contacts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Important Contacts</h4>
                        <p class="text-sm text-gray-500">{{ $contacts->count() }} contacts available</p>
                    </div>
                </div>
                @if($contacts->count() > 0)
                    <div class="space-y-2">
                        @foreach($contacts->take(2) as $contact)
                            @php $content = json_decode($contact->content, true); @endphp
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $content['name'] ?? 'Contact' }}</div>
                                <div class="text-gray-500">{{ $content['role'] ?? '' }}</div>
                            </div>
                        @endforeach
                        @if($contacts->count() > 2)
                            <div class="text-sm text-blue-600 font-medium">+{{ $contacts->count() - 2 }} more contacts</div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">No contacts available yet.</p>
                @endif
            </div>

            <!-- City Guide -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">City Guide</h4>
                        <p class="text-sm text-gray-500">Local information & tips</p>
                    </div>
                </div>
                @if($cityGuide)
                    @php $content = json_decode($cityGuide->content, true); @endphp
                    <div class="text-sm">
                        <div class="font-medium text-gray-900">{{ $content['title'] ?? 'City Guide' }}</div>
                        <div class="text-gray-500 mt-1">{{ Str::limit($content['description'] ?? '', 60) }}</div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">City guide not available yet.</p>
                @endif
            </div>
        </div>

        <!-- Download Section -->
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Download Conference Kit</h4>
                    <p class="text-gray-600">Get a complete PDF version of your conference kit with all session links, contacts, and city guide information.</p>
                </div>
                <a href="{{ route('conference-kit.download', $conferenceKit) }}" 
                   class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>

    @else
        <!-- No Conference Kit Available -->
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">Conference Kit Not Available</h4>
            <p class="text-gray-600 mb-4">Your conference kit will be available once the conference organizers have prepared it.</p>
            <div class="text-sm text-gray-500">
                <p>This will include:</p>
                <ul class="mt-2 space-y-1">
                    <li>• Session links and schedules</li>
                    <li>• Important contact information</li>
                    <li>• City guide and local information</li>
                    <li>• Downloadable PDF version</li>
                </ul>
            </div>
        </div>
    @endif
</div>
