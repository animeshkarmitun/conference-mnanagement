@extends('layouts.participant')

@section('title', 'My Profile')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center space-x-6">
            <div class="flex-shrink-0">
                @if($participant && $participant->user->profile_picture && Storage::disk('public')->exists($participant->user->profile_picture))
                    <img src="{{ route('participants.profile-picture', $participant) }}" 
                         alt="Profile Picture" 
                         class="w-20 h-20 rounded-full object-cover border-4 border-yellow-200"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center border-4 border-yellow-200" style="display: none;">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @else
                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center border-4 border-yellow-200">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                </h1>
                <p class="text-lg text-gray-600 mb-2">{{ $participant->participantType->name ?? '' }}</p>
                <p class="text-sm text-gray-500">{{ $participant->conference->name ?? '' }}</p>
            </div>
            <div class="text-right">
                <div class="mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($participant->registration_status === 'approved') bg-green-100 text-green-800
                        @elseif($participant->registration_status === 'rejected') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($participant->registration_status) }}
                    </span>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($participant->visa_status === 'approved') bg-green-100 text-green-800
                        @elseif($participant->visa_status === 'issue') bg-red-100 text-red-800
                        @elseif($participant->visa_status === 'not_required') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucwords(str_replace('_', ' ', $participant->visa_status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($participant)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Personal Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="text-gray-900">{{ $participant->user->email }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gender</label>
                            <p class="text-gray-900">
                                @if($participant->user->gender)
                                    {{ ucwords(str_replace('_', ' ', $participant->user->gender)) }}
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pronoun</label>
                            <p class="text-gray-900">
                                @if($participant->user->pronoun)
                                    {{ ucwords(str_replace('_', '/', $participant->user->pronoun)) }}
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact No</label>
                            <p class="text-gray-900">{{ $participant->user->contact_no ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">WhatsApp No</label>
                            <p class="text-gray-900">{{ $participant->user->whatsapp_no ?: 'Not provided' }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <p class="text-gray-900">
                            @if($participant->user->date_of_birth)
                                {{ \Carbon\Carbon::parse($participant->user->date_of_birth)->format('M d, Y') }}
                                @php
                                    $age = \Carbon\Carbon::parse($participant->user->date_of_birth)->age;
                                @endphp
                                <span class="text-sm text-gray-500">({{ $age }} years old)</span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <p class="text-gray-900">{{ $participant->user->address ?: 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Home District</label>
                        <p class="text-gray-900">{{ $participant->user->home_district ?: 'Not specified' }}</p>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Professional Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Organization</label>
                        <p class="text-gray-900">{{ $participant->organization ?: 'Not specified' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Designation</label>
                        <p class="text-gray-900">{{ $participant->user->designation ?: 'Not specified' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Field of Work/Study</label>
                        <p class="text-gray-900">{{ $participant->user->field_of_work_study ?: 'Not specified' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <p class="text-gray-900">
                            @if($participant->category)
                                {{ ucwords($participant->category) }}
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Travel Intent</label>
                        <p class="text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $participant->travel_intent === 'international' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($participant->travel_intent ?? 'National') }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Are you a student?</label>
                        <p class="text-gray-900">
                            @if($participant->user->is_student === '1')
                                <span class="text-blue-600 font-medium">Yes</span>
                            @elseif($participant->user->is_student === '0')
                                <span class="text-gray-600">No</span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    
                    @if($participant->user->is_student === '1')
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-md font-semibold text-blue-800 mb-2">Student Information</h4>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Year</label>
                                <p class="text-gray-900">
                                    @if($participant->user->year)
                                        {{ ucwords(str_replace('_', ' ', $participant->user->year)) }}
                                    @else
                                        <span class="text-gray-400">Not specified</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                <p class="text-gray-900">{{ $participant->user->department_name ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Institution</label>
                                <p class="text-gray-900">{{ $participant->user->institution_name ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Additional Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bio</label>
                        <div class="text-gray-900">
                            @if($participant->bio)
                                <div class="bg-gray-100 p-3 rounded-lg">
                                    {{ $participant->bio }}
                                </div>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expertise/Interests</label>
                        <div class="text-gray-900">
                            @if($participant->user->expertise_interests)
                                <div class="bg-gray-100 p-3 rounded-lg">
                                    {{ $participant->user->expertise_interests }}
                                </div>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">How did you find out about BoBC?</label>
                        <p class="text-gray-900">
                            @if($participant->user->how_found_bobc)
                                {{ ucwords(str_replace('_', ' ', $participant->user->how_found_bobc)) }}
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Have you attended BOB-C before?</label>
                        <p class="text-gray-900">
                            @if($participant->user->attended_previous_bobc === '1')
                                <span class="text-green-600 font-medium">Yes</span>
                            @elseif($participant->user->attended_previous_bobc === '0')
                                <span class="text-gray-600">No</span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Travel & Accommodation Information -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Travel & Accommodation</h3>
                
                <div class="space-y-4">
                    @if($participant->travelDetails)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Arrival Date</label>
                            <p class="text-gray-900">
                                @if($participant->travelDetails->arrival_date)
                                    {{ \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('M d, Y H:i') }}
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Departure Date</label>
                            <p class="text-gray-900">
                                @if($participant->travelDetails->departure_date)
                                    {{ \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('M d, Y H:i') }}
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Flight Information</label>
                            <p class="text-gray-900">{{ $participant->travelDetails->flight_info ?: 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Extra Nights</label>
                            <p class="text-gray-900">{{ $participant->travelDetails->extra_nights ?? 0 }} nights</p>
                        </div>
                        
                        @if($participant->travelDetails->hotel)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assigned Hotel</label>
                            <p class="text-gray-900">{{ $participant->travelDetails->hotel->name }}</p>
                        </div>
                        @endif
                    @else
                        <p class="text-gray-400">No travel details provided</p>
                    @endif
                </div>
            </div>

            <!-- Visa Information -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Visa Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Visa Status</label>
                        <p class="text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($participant->visa_status === 'approved') bg-green-100 text-green-800
                                @elseif($participant->visa_status === 'issue') bg-red-100 text-red-800
                                @elseif($participant->visa_status === 'not_required') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucwords(str_replace('_', ' ', $participant->visa_status)) }}
                            </span>
                        </p>
                    </div>
                    
                    @if($participant->visa_status === 'issue' && $participant->visa_issue_description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Visa Issue Description</label>
                        <div class="text-gray-900 bg-red-50 p-3 rounded-lg border border-red-200">
                            {{ $participant->visa_issue_description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sessions Information -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Assigned Sessions</h3>
                
                <div class="space-y-3">
                    @forelse($participant->sessions as $session)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $session->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y H:i') }} - 
                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            </p>
                            @if($session->venue)
                                <p class="text-sm text-gray-500 mt-1">Venue: {{ $session->venue->name }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400">No sessions assigned yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-gray-500 text-lg">No profile found for your account.</p>
        </div>
    @endif
</div>
@endsection