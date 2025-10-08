<!-- Personal Information Display -->
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Basic Information -->
    <div class="bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <div class="text-gray-900 font-medium">
                    {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="text-gray-900">{{ $participant->user->email }}</div>
            </div>
        </div>

        <!-- Enhanced participant fields -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <div class="text-gray-900">
                    @if($participant->user->gender)
                        {{ ucwords(str_replace('_', ' ', $participant->user->gender)) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pronoun</label>
                <div class="text-gray-900">
                    @if($participant->user->pronoun)
                        {{ ucwords(str_replace('_', '/', $participant->user->pronoun)) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact No</label>
                <div class="text-gray-900">
                    {{ $participant->user->contact_no ?: 'Not provided' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp No</label>
                <div class="text-gray-900">
                    {{ $participant->user->whatsapp_no ?: 'Not provided' }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <div class="text-gray-900">
                    @if($participant->user->date_of_birth)
                        {{ \Carbon\Carbon::parse($participant->user->date_of_birth)->format('M d, Y') }}
                        @php
                            $age = \Carbon\Carbon::parse($participant->user->date_of_birth)->age;
                        @endphp
                        <span class="text-sm text-gray-500">({{ $age }} years old)</span>
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Field of Work/Study</label>
                <div class="text-gray-900">
                    {{ $participant->user->field_of_work_study ?: 'Not specified' }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                <div class="text-gray-900">
                    {{ $participant->user->designation ?: 'Not specified' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Organization/Institution</label>
                <div class="text-gray-900">
                    {{ $participant->user->organization_institution ?: 'Not specified' }}
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
            <div class="text-gray-900">
                {{ $participant->organization ?: 'Not specified' }}
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <div class="text-gray-900">
                {{ $participant->user->address ?: 'Not provided' }}
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Home District</label>
                <div class="text-gray-900">
                    {{ $participant->user->home_district ?: 'Not specified' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">How did you find out about BoBC?</label>
                <div class="text-gray-900">
                    @if($participant->user->how_found_bobc)
                        {{ ucwords(str_replace('_', ' ', $participant->user->how_found_bobc)) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <div class="text-gray-900">
                    @if($participant->category)
                        {{ ucwords($participant->category) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Travel Intent</label>
                <div class="text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $participant->travel_intent === 'international' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($participant->travel_intent ?? 'National') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Have you attended BOB-C before?</label>
            <div class="text-gray-900">
                @if($participant->user->attended_previous_bobc === '1')
                    <span class="text-green-600 font-medium">Yes</span>
                @elseif($participant->user->attended_previous_bobc === '0')
                    <span class="text-gray-600">No</span>
                @else
                    <span class="text-gray-400">Not specified</span>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Are you a student?</label>
            <div class="text-gray-900">
                @if($participant->user->is_student === '1')
                    <span class="text-blue-600 font-medium">Yes</span>
                @elseif($participant->user->is_student === '0')
                    <span class="text-gray-600">No</span>
                @else
                    <span class="text-gray-400">Not specified</span>
                @endif
            </div>
        </div>

        @if($participant->user->is_student === '1')
        <div class="mt-6 bg-blue-50 p-4 rounded-lg">
            <h4 class="text-md font-semibold text-blue-800 mb-3">Student Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <div class="text-gray-900">
                        @if($participant->user->year)
                            {{ ucwords(str_replace('_', ' ', $participant->user->year)) }}
                        @else
                            <span class="text-gray-400">Not specified</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department Name</label>
                    <div class="text-gray-900">
                        {{ $participant->user->department_name ?: 'Not specified' }}
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Institution Name</label>
                <div class="text-gray-900">
                    {{ $participant->user->institution_name ?: 'Not specified' }}
                </div>
            </div>
        </div>
        @endif

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Expertise/Interests</label>
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
    </div>

    <!-- Media-specific fields (shown when participant type is press) -->
    @if($participant->participantType && $participant->participantType->category === 'press')
    <div class="bg-blue-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Media Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type of Media</label>
                <div class="text-gray-900">
                    @if($participant->user->media_type)
                        {{ ucwords(str_replace('_', ' ', $participant->user->media_type)) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Speaker-specific fields (shown when participant type is presenter) -->
    @if($participant->participantType && $participant->participantType->category === 'presenter')
    <div class="bg-green-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800 mb-4">Speaker Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Other Contact Type</label>
                <div class="text-gray-900">
                    @if($participant->user->other_contact_type)
                        {{ ucwords($participant->user->other_contact_type) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Other Contact Number</label>
                <div class="text-gray-900">
                    {{ $participant->user->other_contact_no ?: 'Not provided' }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                <div class="text-gray-900">
                    @if($participant->user->sector)
                        {{ ucwords(str_replace('_', ' ', $participant->user->sector)) }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Areas of Expertise</label>
            <div class="text-gray-900">
                @if($participant->user->areas_of_expertise)
                    <div class="bg-gray-100 p-3 rounded-lg">
                        {{ $participant->user->areas_of_expertise }}
                    </div>
                @else
                    <span class="text-gray-400">Not provided</span>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Topic to Speak</label>
            <div class="text-gray-900">
                {{ $participant->user->preferred_topic ?: 'Not specified' }}
            </div>
        </div>

        <!-- Social Links -->
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Social Links</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                    <div class="text-gray-900">
                        @if($participant->user->linkedin_link)
                            <a href="{{ $participant->user->linkedin_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                {{ $participant->user->linkedin_link }}
                            </a>
                        @else
                            <span class="text-gray-400">Not provided</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Twitter</label>
                    <div class="text-gray-900">
                        @if($participant->user->twitter_link)
                            <a href="{{ $participant->user->twitter_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                {{ $participant->user->twitter_link }}
                            </a>
                        @else
                            <span class="text-gray-400">Not provided</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                    <div class="text-gray-900">
                        @if($participant->user->facebook_link)
                            <a href="{{ $participant->user->facebook_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                {{ $participant->user->facebook_link }}
                            </a>
                        @else
                            <span class="text-gray-400">Not provided</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Passport/Visa Information -->
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Passport & Visa Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valid Passport</label>
                    <div class="text-gray-900">
                        @if($participant->user->has_valid_passport === '1')
                            <span class="text-green-600 font-medium">Yes</span>
                        @elseif($participant->user->has_valid_passport === '0')
                            <span class="text-red-600">No</span>
                        @else
                            <span class="text-gray-400">Not specified</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Previous Visa Issues</label>
                    <div class="text-gray-900">
                        @if($participant->user->had_visa_issue_bd === '1')
                            <span class="text-orange-600 font-medium">Yes</span>
                        @elseif($participant->user->had_visa_issue_bd === '0')
                            <span class="text-green-600">No</span>
                        @else
                            <span class="text-gray-400">Not specified</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($participant->user->had_visa_issue_bd === '1' && $participant->user->visa_issue_explanation)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Visa Issue Explanation</label>
                <div class="text-gray-900 bg-gray-100 p-3 rounded-lg">
                    {{ $participant->user->visa_issue_explanation }}
                </div>
            </div>
            @endif
        </div>

        <!-- Dietary Requirements for Speakers -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Requirements</label>
            <div class="text-gray-900">
                @if($participant->user->dietary_requirements)
                    @if($participant->user->dietary_requirements === 'others' && $participant->user->dietary_requirements_other)
                        {{ $participant->user->dietary_requirements_other }}
                    @else
                        {{ ucwords(str_replace('_', ' ', $participant->user->dietary_requirements)) }}
                    @endif
                @else
                    <span class="text-gray-400">Not specified</span>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    <!-- System Information (Admin Only) -->
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('superadmin'))
    <div class="bg-purple-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-purple-800 mb-4">System Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Registration Status</label>
                <div class="text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($participant->registration_status === 'approved') bg-green-100 text-green-800
                        @elseif($participant->registration_status === 'rejected') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($participant->registration_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Additional Information -->
    <div class="bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Information</h3>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
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
    </div>
    
    <!-- Visa Information -->
    <div class="bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Visa Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Visa Status</label>
                <div class="text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($participant->visa_status === 'approved') bg-green-100 text-green-800
                        @elseif($participant->visa_status === 'issue') bg-red-100 text-red-800
                        @elseif($participant->visa_status === 'not_required') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucwords(str_replace('_', ' ', $participant->visa_status)) }}
                    </span>
                </div>
            </div>
        </div>
        
        @if($participant->visa_status === 'issue' && $participant->visa_issue_description)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Visa Issue Description</label>
            <div class="text-gray-900 bg-gray-100 p-3 rounded-lg">
                {{ $participant->visa_issue_description }}
            </div>
        </div>
        @endif
    </div>
    
    <!-- Documents & Files -->
    <div class="bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Documents & Files</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                <div class="mt-1 flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if($participant->user->profile_picture && Storage::disk('public')->exists($participant->user->profile_picture))
                            <img src="{{ route('participants.profile-picture', $participant) }}" 
                                 alt="Profile Picture" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center" style="display: none;">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        @if($participant->user->profile_picture && Storage::disk('public')->exists($participant->user->profile_picture))
                            <a href="{{ route('participants.profile-picture', $participant) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                View Profile Picture
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">No profile picture uploaded</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NID/Passport/Birth Certificate</label>
                <div class="mt-1">
                    @if($participant->user->nid_passport_birth_certificate && Storage::disk('public')->exists($participant->user->nid_passport_birth_certificate))
                        <a href="{{ Storage::disk('public')->url($participant->user->nid_passport_birth_certificate) }}" 
                           target="_blank"
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Document
                        </a>
                    @else
                        <span class="text-gray-400 text-sm">No document uploaded</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>