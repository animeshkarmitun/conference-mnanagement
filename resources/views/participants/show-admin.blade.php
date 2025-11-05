@extends('layouts.app')

@section('title', 'View Participant')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-blue-100 via-blue-50 to-white shadow flex items-center px-8 py-6 mb-6 border border-blue-200">
    <div class="flex items-center justify-center w-16 h-16 bg-blue-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
    </div>
    <div class="flex-1">
        <h1 class="text-3xl font-extrabold text-blue-800 tracking-tight mb-1">
            {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
        </h1>
        <div class="text-gray-600 text-lg font-medium">
            {{ ucwords(str_replace('_', ' ', $participant->participantType->name ?? '')) }} - {{ $participant->conference->name ?? 'No Conference' }}
        </div>
    </div>
    <div class="flex space-x-4">
        <a href="{{ route('participants.index') }}" 
           class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            ← Back to Participants
        </a>
        <a href="{{ route('participants.edit', $participant) }}" 
           class="inline-flex items-center px-8 py-3 modern-primary rounded-lg font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Participant
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <!-- Participant Information Section -->
    <div class="mb-8 p-6 bg-blue-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Participant Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Conference</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->conference->name ?? 'Not specified' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Participant Type</label>
                <div class="mt-1 text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $participant->participantType->name ?? 'Not specified')) }}</div>
                @if($participant->participantType)
                    <div class="mt-1 text-xs text-gray-500">
                        <div>Requires Approval: {{ $participant->participantType->requires_approval ? 'Yes' : 'No' }}</div>
                        <div>Has Special Privileges: {{ $participant->participantType->has_special_privileges ? 'Yes' : 'No' }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Profile/Bio Section -->
    @if($participant->bio)
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Profile</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Bio</label>
            <div class="mt-1 text-sm text-gray-900">{{ $participant->bio }}</div>
        </div>
    </div>
    @endif

    <!-- Personal Information Section -->
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Personal Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Gender</label>
                <div class="mt-1 text-sm text-gray-900">{{ ucfirst($participant->user->gender ?? 'Not specified') }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Pronoun</label>
                <div class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', '/', $participant->user->pronoun ?? 'Not specified')) }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Contact No</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->contact_no ?? 'Not provided' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Messaging Platform</label>
                <div class="mt-1 text-sm text-gray-900">
                    {{ ucfirst($participant->user->messaging_type ?? trim($participant->user->whatsapp_no ?? 'Not specified')) }}
                    @if($participant->user->messaging_number)
                        - {{ $participant->user->messaging_number }}
                    @elseif($participant->user->whatsapp_no)
                        - {{ $participant->user->whatsapp_no }}
                    @endif
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->email }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->date_of_birth ? \Carbon\Carbon::parse($participant->user->date_of_birth)->format('M d, Y') : 'Not specified' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Field of Work/Study/Expertise/Interests</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->field_of_work_study ?? 'Not specified' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Designation</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->designation ?? 'Not specified' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Organization/Institution</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->organization_institution ?? 'Not specified' }}</div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Country</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->country ?? 'Not specified' }}</div>
            </div>
            
            @if($participant->user->profile_picture)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
                <div class="mt-1">
                    @php
                        $picPath = $participant->user->profile_picture;
                        $picUrl = \Illuminate\Support\Str::startsWith($picPath, ['http://','https://']) 
                            ? $picPath 
                            : \Illuminate\Support\Facades\Storage::url($picPath);
                    @endphp
                    <img src="{{ $picUrl }}" alt="Profile Picture" class="w-20 h-20 rounded-full object-cover">
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($participant->participantType && $participant->participantType->category === 'press')
    <!-- Media Registration Section -->
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Media Registration</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Type of Media</label>
                <div class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $participant->user->media_type ?? 'Not specified')) }}</div>
            </div>
        </div>
    </div>
    @endif

    @if($participant->participantType && $participant->participantType->category === 'presenter')
    <!-- Speaker Additional Information Section -->
    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Speaker Additional Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Dietary Requirements</label>
                <div class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $participant->user->dietary_requirements ?? 'Not specified')) }}</div>
                @if($participant->user->dietary_requirements === 'others' && $participant->user->dietary_requirements_other)
                    <div class="mt-1 text-xs text-gray-500">Details: {{ $participant->user->dietary_requirements_other }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Professional Information Section -->
    <div class="mb-8 p-6 bg-green-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-green-800 border-b border-green-200 pb-2">Professional Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Preferred topic to speak</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->preferred_topic ?? 'Not specified' }}</div>
            </div>
        </div>
    </div>

    <!-- Additional Details Section -->
    <div class="mb-8 p-6 bg-purple-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Additional Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">LinkedIn Link</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->user->linkedin_link)
                        <a href="{{ $participant->user->linkedin_link }}" target="_blank" class="text-blue-600 hover:underline">{{ $participant->user->linkedin_link }}</a>
                    @else
                        Not provided
                    @endif
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Twitter Link</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->user->twitter_link)
                        <a href="{{ $participant->user->twitter_link }}" target="_blank" class="text-blue-600 hover:underline">{{ $participant->user->twitter_link }}</a>
                    @else
                        Not provided
                    @endif
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Facebook Link</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->user->facebook_link)
                        <a href="{{ $participant->user->facebook_link }}" target="_blank" class="text-blue-600 hover:underline">{{ $participant->user->facebook_link }}</a>
                    @else
                        Not provided
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Supporting Documents Section -->
    <div class="mb-8 p-6 bg-yellow-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-yellow-800 border-b border-yellow-200 pb-2">Supporting Documents</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Valid Passport</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->has_valid_passport ? 'Yes' : ($participant->user->has_valid_passport === '0' ? 'No' : 'Not specified') }}</div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Previous Visa Issues to Bangladesh</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->had_visa_issue_bd ? 'Yes' : ($participant->user->had_visa_issue_bd === '0' ? 'No' : 'Not specified') }}</div>
            </div>
        </div>
        @if($participant->user->had_visa_issue_bd && $participant->user->visa_issue_explanation)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Visa Issue Explanation</label>
            <div class="mt-1 text-sm text-gray-900">{{ $participant->user->visa_issue_explanation }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Student Information Section -->
    @if($participant->user->is_student)
    <div class="mb-8 p-6 bg-blue-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Student Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Year</label>
                <div class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $participant->user->year ?? 'Not specified')) }}</div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Department Name</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->department_name ?? 'Not specified' }}</div>
            </div>
            <div class="mb-4 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Institution Name</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->institution_name ?? 'Not specified' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Address Information Section -->
    <div class="mb-8 p-6 bg-green-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-green-800 border-b border-green-200 pb-2">Address Information</h3>
        <div class="grid grid-cols-1 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->address ?? 'Not provided' }}</div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Home District</label>
                <div class="mt-1 text-sm text-gray-900">{{ $participant->user->home_district ?? 'Not specified' }}</div>
            </div>
        </div>
    </div>

    <!-- Document Upload Section -->
    @if($participant->user->nid_passport_birth_certificate)
    <div class="mb-8 p-6 bg-yellow-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-yellow-800 border-b border-yellow-200 pb-2">Document Upload</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">NID/Passport/Birth Certificate</label>
            <div class="mt-1">
                <img src="{{ asset('storage/' . $participant->user->nid_passport_birth_certificate) }}" alt="Document" class="w-32 h-32 object-cover rounded-lg border">
            </div>
        </div>
    </div>
    @endif


    <!-- Hashtags Section -->
    @if($participant->hashtags)
    <div class="mb-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
        <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Hashtags</h3>
        <div class="flex flex-wrap gap-2">
            @foreach(explode(',', $participant->hashtags) as $hashtag)
                @if(trim($hashtag))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                        {{ trim($hashtag) }}
                    </span>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Travel Info Section -->
    <div class="mb-8 p-6 bg-purple-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Travel Info</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Visa Status</label>
                <div class="mt-1">
                    @php
                        $visaStatus = $participant->visa_status ?? 'not_specified';
                        $statusColors = [
                            'required' => 'bg-yellow-100 text-yellow-800',
                            'not_required' => 'bg-green-100 text-green-800',
                            'pending' => 'bg-blue-100 text-blue-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'issue' => 'bg-red-100 text-red-800',
                        ];
                        $colorClass = $statusColors[$visaStatus] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                        {{ ucfirst(str_replace('_', ' ', $visaStatus)) }}
                    </span>
                </div>
                @if($participant->visa_status === 'issue' && $participant->visa_issue_description)
                    <div class="mt-2 text-sm text-gray-700">{{ $participant->visa_issue_description }}</div>
                @endif
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Registration Status</label>
                <div class="mt-1">
                    @php
                        $regStatus = $participant->registration_status ?? 'pending';
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                        $colorClass = $statusColors[$regStatus] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                        {{ ucfirst($regStatus) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Travel Intent</label>
                <div class="mt-1 text-sm text-gray-900">
                    @php
                        $travelIntent = $participant->travel_intent ?? 'none';
                        $travelIntentMap = [
                            'none' => 'None',
                            'national' => 'National',
                            'international' => 'International',
                            '0' => 'None',
                            '1' => 'National', 
                            '2' => 'International'
                        ];
                        $displayValue = $travelIntentMap[$travelIntent] ?? 'None';
                    @endphp
                    {{ $displayValue }}
                </div>
            </div>
        </div>
        
        @if(in_array($participant->travel_intent, ['national', 'international', '1', '2']))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Arrival Date</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->travelDetails && $participant->travelDetails->arrival_date)
                        {{ \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('M d, Y g:i A') }}
                    @else
                        Not specified
                    @endif
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Departure Date</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->travelDetails && $participant->travelDetails->departure_date)
                        {{ \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('M d, Y g:i A') }}
                    @else
                        Not specified
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Additional Travel Fields -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Itineraries Status</label>
                <div class="mt-1">
                    @if($participant->travelDetails && $participant->travelDetails->itineraries_status)
                        @php
                            $status = $participant->travelDetails->itineraries_status;
                            $statusColors = [
                                'approved' => 'bg-green-100 text-green-800',
                                'n_a' => 'bg-gray-100 text-gray-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                            ];
                            $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                            @if($status === 'n_a')
                                N/A
                            @else
                                {{ ucfirst($status) }}
                            @endif
                        </span>
                    @else
                        <span class="text-gray-400">Not set</span>
                    @endif
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Takeoff Airport</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->travelDetails && $participant->travelDetails->takeoff_airport)
                        {{ $participant->travelDetails->takeoff_airport }}
                    @else
                        Not provided
                    @endif
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Flight Info Details</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->travelDetails && $participant->travelDetails->flight_info_details)
                        {{ $participant->travelDetails->flight_info_details }}
                    @elseif($participant->travelDetails && $participant->travelDetails->flight_info)
                        {{ $participant->travelDetails->flight_info }}
                    @else
                        Not provided
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Additional Travel Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Room Check-in</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->travelDetails && $participant->travelDetails->room_check_in)
                        {{ \Carbon\Carbon::parse($participant->travelDetails->room_check_in)->format('M d, Y g:i A') }}
                    @else
                        Not specified
                    @endif
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Room Check-out</label>
                <div class="mt-1 text-sm text-gray-900">
                    @if($participant->travelDetails && $participant->travelDetails->room_check_out)
                        {{ \Carbon\Carbon::parse($participant->travelDetails->room_check_out)->format('M d, Y g:i A') }}
                    @else
                        Not specified
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Hotel Information -->
        @if($participant->travelDetails && $participant->travelDetails->hotel_info)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Hotel Information</label>
            <div class="mt-1 text-sm text-gray-900">
                {{ $participant->travelDetails->hotel_info }}
            </div>
        </div>
        @endif
        
        <!-- Travel Documents -->
        @if($participant->travelDetails && $participant->travelDetails->travel_documents)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Travel Documents</label>
            <div class="mt-1">
                <a href="{{ asset('storage/' . $participant->travelDetails->travel_documents) }}" 
                   target="_blank" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Travel Document
                </a>
            </div>
        </div>
        @endif
        @endif
        
    </div>
</div>

<!-- Assigned Sessions Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-calendar-check text-indigo-500 mr-3"></i>
        Assigned Sessions
    </h2>
    
    @if(isset($sessions) && count($sessions) > 0)
        <div class="space-y-4">
            @foreach($sessions as $index => $session)
                <div class="border border-gray-200 rounded-lg p-6 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center flex-wrap gap-2 mb-3">
                                <h4 class="text-lg font-bold text-indigo-600">{{ $session->title }}</h4>
                                @if($session->pivot && $session->pivot->role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($session->pivot->role === 'speaker') bg-purple-100 text-purple-800
                                        @elseif($session->pivot->role === 'moderator') bg-blue-100 text-blue-800
                                        @elseif($session->pivot->role === 'panelist') bg-green-100 text-green-800
                                        @elseif($session->pivot->role === 'organizer') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($session->pivot->role) }}
                                    </span>
                                @endif
                                
                                @php
                                    // Time-based status
                                    $now = \Carbon\Carbon::now();
                                    $startTime = \Carbon\Carbon::parse($session->start_time);
                                    $endTime = \Carbon\Carbon::parse($session->end_time);
                                    
                                    $isActive = $startTime->isPast() && $endTime->isFuture();
                                    $isPast = $endTime->isPast();
                                    $isToday = $startTime->isToday();
                                    
                                    if ($isActive) {
                                        $timeStatusText = 'Active';
                                        $timeStatusClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                        $timeStatusIcon = 'fas fa-play-circle';
                                    } elseif ($isPast) {
                                        $timeStatusText = 'Finished';
                                        $timeStatusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                        $timeStatusIcon = 'fas fa-check-circle';
                                    } elseif ($isToday) {
                                        $timeStatusText = 'Today';
                                        $timeStatusClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                        $timeStatusIcon = 'fas fa-clock';
                                    } else {
                                        $timeStatusText = 'Upcoming';
                                        $timeStatusClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                        $timeStatusIcon = 'fas fa-calendar';
                                    }
                                    
                                    // Publication status
                                    $sessionStatus = $session->status ?? 'draft';
                                    $publishStatusText = $sessionStatus === 'published' ? 'Published' : 'Draft';
                                    $publishStatusClass = $sessionStatus === 'published' 
                                        ? 'bg-green-100 text-green-800 border-green-200' 
                                        : 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                    $publishStatusIcon = $sessionStatus === 'published' 
                                        ? 'fas fa-check-circle' 
                                        : 'fas fa-edit';
                                @endphp
                                
                                <!-- Time-based Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold shadow-sm border {{ $timeStatusClass }}">
                                    <i class="{{ $timeStatusIcon }} text-xs mr-1"></i>
                                    {{ $timeStatusText }}
                                </span>
                                
                                <!-- Publication Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold shadow-sm border {{ $publishStatusClass }}">
                                    <i class="{{ $publishStatusIcon }} text-xs mr-1"></i>
                                    {{ $publishStatusText }}
                                </span>
                            </div>
                            
                            @if($session->description)
                            <div class="mb-3">
                                <span class="text-sm font-semibold text-gray-700">Description:</span>
                                <p class="text-sm text-gray-900 mt-1">{{ $session->description }}</p>
                            </div>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700">Start Date & Time:</span>
                                    <p class="text-sm text-gray-900 mt-1">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('F j, Y g:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-700">End Date & Time:</span>
                                    <p class="text-sm text-gray-900 mt-1">
                                        {{ \Carbon\Carbon::parse($session->end_time)->format('F j, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <span class="text-sm font-semibold text-gray-700">Duration:</span>
                                <p class="text-sm text-gray-900 mt-1">
                                    {{ \Carbon\Carbon::parse($session->start_time)->diffInMinutes(\Carbon\Carbon::parse($session->end_time)) }} minutes
                                </p>
                            </div>
                            
                            @if($session->venue || $session->room)
                            <div class="mt-3">
                                <span class="text-sm font-semibold text-gray-700">Location:</span>
                                <div class="mt-1 text-sm text-gray-900">
                                    @if($session->venue)
                                        <p><span class="font-medium">Venue:</span> {{ $session->venue->name }}</p>
                                        @if($session->venue->address)
                                            <p class="text-gray-600"><span class="font-medium">Address:</span> {{ $session->venue->address }}</p>
                                        @endif
                                    @endif
                                    @if($session->room)
                                        <p><span class="font-medium">Room:</span> {{ $session->room }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No sessions assigned to this participant</p>
        </div>
    @endif
</div>

<!-- Comments Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-comments text-purple-500 mr-3"></i>
        Comments & Notes
    </h2>
    
    <!-- Add Comment Form -->
    <div class="mb-6">
        <form id="commentForm" class="space-y-4">
            @csrf
            <div>
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Add a comment</label>
                <textarea id="comment" name="content" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Write your comment here..." required></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Post Comment
                </button>
            </div>
        </form>
    </div>
    
    <!-- Comments List -->
    <div id="commentsList" class="space-y-4">
        @if(isset($comments) && count($comments) > 0)
            @foreach($comments as $comment)
                <div class="border-l-4 border-blue-500 pl-4 py-2 comment-item" data-comment-id="{{ $comment->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-gray-900">
                                    @php
                                        $userName = trim(($comment->user->first_name ?? '') . ' ' . ($comment->user->last_name ?? ''));
                                        $displayName = $userName ?: ($comment->user->email ?? 'Unknown User');
                                    @endphp
                                    {{ $displayName }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <p class="text-gray-700">{{ $comment->content }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="deleteComment({{ $comment->id }})" 
                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-full transition-colors duration-200"
                                    title="Delete comment">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-8">
                <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No comments yet</p>
            </div>
        @endif
    </div>
</div>

@endsection

<style>
    /* Modern color scheme overrides */
    .modern-primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
    }
    
    .modern-primary:hover {
        background: linear-gradient(135deg, #5855eb, #7c3aed);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Comments form submission
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const commentText = formData.get('content');
        
        if (!commentText.trim()) {
            alert('Please enter a comment');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
        submitBtn.disabled = true;
        
        // Submit comment via AJAX
        fetch('{{ route("participants.comments.store", $participant) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add comment to the list
                addCommentToList(data.comment);
                // Clear form
                this.reset();
            } else {
                alert('Error posting comment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error posting comment. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Function to add comment to the list
    function addCommentToList(comment) {
        const commentsList = document.getElementById('commentsList');
        const emptyState = commentsList.querySelector('.text-center');
        
        // Remove empty state if it exists
        if (emptyState) {
            emptyState.remove();
        }
        
        // Create new comment element
        const commentElement = document.createElement('div');
        commentElement.className = 'border-l-4 border-blue-500 pl-4 py-2 comment-item';
        commentElement.setAttribute('data-comment-id', comment.id);
        commentElement.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm font-medium text-gray-900">${comment.user_name || 'Unknown User'}</span>
                            <span class="text-xs text-gray-500">${comment.created_at}</span>
                        </div>
                    <p class="text-gray-700">${comment.content}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button onclick="deleteComment(${comment.id})" 
                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-full transition-colors duration-200"
                            title="Delete comment">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Add to the top of the comments list
        commentsList.insertBefore(commentElement, commentsList.firstChild);
    }
    
    // Function to delete a comment
    window.deleteComment = function(commentId) {
        if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
            return;
        }
        
        // Show loading state on the delete button
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        const deleteBtn = commentElement.querySelector('button');
        const originalContent = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
        deleteBtn.disabled = true;
        
        // Send delete request
        fetch(`{{ route('participants.comments.destroy', [$participant, 'COMMENT_ID']) }}`.replace('COMMENT_ID', commentId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the comment element from the DOM
                commentElement.remove();
                
                // Check if there are no more comments and show empty state
                const commentsList = document.getElementById('commentsList');
                const remainingComments = commentsList.querySelectorAll('.comment-item');
                if (remainingComments.length === 0) {
                    commentsList.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No comments yet</p>
                        </div>
                    `;
                }
            } else {
                alert('Error deleting comment: ' + (data.message || 'Unknown error'));
                // Reset button state
                deleteBtn.innerHTML = originalContent;
                deleteBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting comment. Please try again.');
            // Reset button state
            deleteBtn.innerHTML = originalContent;
            deleteBtn.disabled = false;
        });
    };
});
</script>