@extends('layouts.app')

@section('title', 'Edit Participant')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-blue-100 via-blue-50 to-white shadow flex items-center px-8 py-6 mb-6 border border-blue-200">
    <div class="flex items-center justify-center w-16 h-16 bg-blue-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-blue-800 tracking-tight mb-1">Edit Participant</h1>
        <div class="text-gray-600 text-lg font-medium">Update participant information and details</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('participants.update', $participant) }}" enctype="multipart/form-data" onsubmit="return validateForm(event)">
        @csrf
        @method('PUT')
        <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
        <!-- Participant Information Section -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Participant Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference *</label>
                    <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Conference</option>
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}" {{ old('conference_id', $participant->conference_id) == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                        @endforeach
                    </select>
                    @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="participant_type_id" class="block text-sm font-medium text-gray-700">Participant Type *</label>
                    <select name="participant_type_id" id="participant_type_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Type</option>
                        @php
                            $groupedTypes = $participantTypes->groupBy('category');
                            $categories = \App\Models\ParticipantType::getCategories();
                        @endphp
                        @foreach($categories as $categoryKey => $categoryName)
                            @if($groupedTypes->has($categoryKey))
                                <optgroup label="{{ $categoryName }}">
                                    @foreach($groupedTypes[$categoryKey] as $type)
                                        <option value="{{ $type->id }}" 
                                                data-description="{{ $type->description }}"
                                                data-category="{{ $type->category }}"
                                                data-requires-approval="{{ $type->requires_approval ? 'true' : 'false' }}"
                                                data-has-privileges="{{ $type->has_special_privileges ? 'true' : 'false' }}"
                                                {{ old('participant_type_id', $participant->participant_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $type->name)) }}
                                            @if($type->requires_approval)
                                                (Requires Approval)
                                            @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <div id="participant-type-description" class="mt-1 text-sm text-gray-500">
                        <div>Requires Approval: No</div>
                        <div>Has Special Privileges: No</div>
                    </div>
                    @error('participant_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="first_name" id="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="First Name" value="{{ old('first_name', $participant->user->first_name ?? $participant->user->name) }}">
                        <input type="text" name="last_name" id="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Last Name" value="{{ old('last_name', $participant->user->last_name) }}">
                    </div>
                    @error('first_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('last_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender (Optional)</label>
                    <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $participant->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $participant->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="prefer_not_to_say" {{ old('gender', $participant->user->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer Not to Say</option>
                    </select>
                    @error('gender')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="pronoun" class="block text-sm font-medium text-gray-700">Pronoun (Optional)</label>
                    <select name="pronoun" id="pronoun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Pronoun</option>
                        <option value="he_him" {{ old('pronoun', $participant->user->pronoun) == 'he_him' ? 'selected' : '' }}>He/Him</option>
                        <option value="she_her" {{ old('pronoun', $participant->user->pronoun) == 'she_her' ? 'selected' : '' }}>She/Her</option>
                        <option value="they_them" {{ old('pronoun', $participant->user->pronoun) == 'they_them' ? 'selected' : '' }}>They/Them</option>
                    </select>
                    @error('pronoun')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="contact_no" class="block text-sm font-medium text-gray-700">Contact No (Optional)</label>
                    <input type="tel" name="contact_no" id="contact_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="+8801234567890" value="{{ old('contact_no', $participant->user->contact_no) }}">
                    @error('contact_no')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Messaging Platform (Optional)</label>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <input type="radio" name="messaging_type" id="messaging_whatsapp" value="whatsapp" class="form-radio text-blue-600" {{ old('messaging_type', $participant->user->messaging_type) == 'whatsapp' ? 'checked' : '' }}>
                            <label for="messaging_whatsapp" class="ml-2 text-sm text-gray-700">WhatsApp</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="messaging_type" id="messaging_signal" value="signal" class="form-radio text-blue-600" {{ old('messaging_type', $participant->user->messaging_type) == 'signal' ? 'checked' : '' }}>
                            <label for="messaging_signal" class="ml-2 text-sm text-gray-700">Signal</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="messaging_type" id="messaging_telegram" value="telegram" class="form-radio text-blue-600" {{ old('messaging_type', $participant->user->messaging_type) == 'telegram' ? 'checked' : '' }}>
                            <label for="messaging_telegram" class="ml-2 text-sm text-gray-700">Telegram</label>
                        </div>
                    </div>
                    <input type="tel" name="messaging_number" id="messaging_number" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="+8801234567890" value="{{ old('messaging_number', $participant->user->messaging_number) }}">
                    @error('messaging_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('messaging_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('email', $participant->user->email) }}">
                    @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth (Optional)</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('date_of_birth', $participant->user->date_of_birth) }}">
                    <p class="text-xs text-gray-500 mt-1">Age will be calculated automatically</p>
                    @error('date_of_birth')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="field_of_work_study" class="block text-sm font-medium text-gray-700">Field of Work/Study (Optional)</label>
                    <input type="text" name="field_of_work_study" id="field_of_work_study" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Computer Science, Medicine, Engineering" value="{{ old('field_of_work_study', $participant->user->field_of_work_study) }}">
                    @error('field_of_work_study')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="designation" class="block text-sm font-medium text-gray-700">Designation (Optional)</label>
                    <input type="text" name="designation" id="designation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Software Engineer, Professor, Student" value="{{ old('designation', $participant->user->designation) }}">
                    @error('designation')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="organization_institution" class="block text-sm font-medium text-gray-700">Organization/Institution (Optional)</label>
                    <input type="text" name="organization_institution" id="organization_institution" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Your current organization or institution" value="{{ old('organization_institution', $participant->user->organization_institution) }}">
                    @error('organization_institution')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="country" class="block text-sm font-medium text-gray-700">Country (Optional)</label>
                    <input type="text" name="country" id="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Bangladesh, USA, UK" value="{{ old('country', $participant->user->country) }}">
                    @error('country')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture (Optional)</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                    @error('profile_picture')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Media Registration Section (shown when category is 'press') -->
        <div id="media-section" class="mb-8 p-6 bg-gray-50 rounded-lg hidden">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Media Registration</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="media_type" class="block text-sm font-medium text-gray-700">Type of Media *</label>
                    <select name="media_type" id="media_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('media_type') border-red-300 @enderror">
                        <option value="">Select Type</option>
                        <option value="print" {{ old('media_type', $participant->user->media_type) == 'print' ? 'selected' : '' }}>Print</option>
                        <option value="television" {{ old('media_type', $participant->user->media_type) == 'television' ? 'selected' : '' }}>Television</option>
                        <option value="online_portal" {{ old('media_type', $participant->user->media_type) == 'online_portal' ? 'selected' : '' }}>Online Portal</option>
                    </select>
                    @error('media_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Speaker Sections (shown when category is 'presenter') -->
        <div id="speaker-section" class="hidden">
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Speaker Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="dietary_requirements" class="block text-sm font-medium text-gray-700">Dietary Requirements (Optional)</label>
                        <select name="dietary_requirements" id="dietary_requirements" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('dietary_requirements') border-red-300 @enderror">
                            <option value="">Select</option>
                            <option value="veg" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'veg' ? 'selected' : '' }}>Veg</option>
                            <option value="non_veg" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'non_veg' ? 'selected' : '' }}>Non-Veg</option>
                            <option value="vegan" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'vegan' ? 'selected' : '' }}>Vegan</option>
                            <option value="others" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                        @error('dietary_requirements')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4" id="dietary_req_other_wrap" style="display:none;">
                        <label for="dietary_requirements_other" class="block text-sm font-medium text-gray-700">Please specify</label>
                        <input type="text" name="dietary_requirements_other" id="dietary_requirements_other" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('dietary_requirements_other') border-red-300 @enderror" value="{{ old('dietary_requirements_other', $participant->user->dietary_requirements_other) }}">
                        @error('dietary_requirements_other')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-8 p-6 bg-green-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-green-800 border-b border-green-200 pb-2">Professional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4 md:col-span-2">
                        <label for="preferred_topic" class="block text-sm font-medium text-gray-700">Preferred topic to speak (Optional)</label>
                        <input type="text" name="preferred_topic" id="preferred_topic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('preferred_topic', $participant->user->preferred_topic) }}">
                        @error('preferred_topic')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-8 p-6 bg-purple-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Additional Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="mb-4">
                        <label for="linkedin_link" class="block text-sm font-medium text-gray-700">LinkedIn Link (Optional)</label>
                        <input type="url" name="linkedin_link" id="linkedin_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('linkedin_link', $participant->user->linkedin_link) }}">
                        @error('linkedin_link')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="twitter_link" class="block text-sm font-medium text-gray-700">Twitter Link (Optional)</label>
                        <input type="url" name="twitter_link" id="twitter_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('twitter_link', $participant->user->twitter_link) }}">
                        @error('twitter_link')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="facebook_link" class="block text-sm font-medium text-gray-700">Facebook Link (Optional)</label>
                        <input type="url" name="facebook_link" id="facebook_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('facebook_link', $participant->user->facebook_link) }}">
                        @error('facebook_link')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-8 p-6 bg-yellow-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-4 text-yellow-800 border-b border-yellow-200 pb-2">Supporting Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="has_valid_passport" class="block text-sm font-medium text-gray-700">Do you have a current/valid passport? (Optional)</label>
                        <select name="has_valid_passport" id="has_valid_passport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">Select</option>
                            <option value="1" {{ old('has_valid_passport', $participant->user->has_valid_passport) == '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('has_valid_passport', $participant->user->has_valid_passport) == '0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('has_valid_passport')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="had_visa_issue_bd" class="block text-sm font-medium text-gray-700">Did you previously face issues regarding a visa to Bangladesh? (Optional)</label>
                        <select name="had_visa_issue_bd" id="had_visa_issue_bd" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">Select</option>
                            <option value="1" {{ old('had_visa_issue_bd', $participant->user->had_visa_issue_bd) == '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('had_visa_issue_bd', $participant->user->had_visa_issue_bd) == '0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('had_visa_issue_bd')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mb-2" id="visa_issue_explanation_wrap" style="display:none;">
                    <label for="visa_issue_explanation" class="block text-sm font-medium text-gray-700">If yes, kindly provide a brief explanation</label>
                    <textarea name="visa_issue_explanation" id="visa_issue_explanation" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="These details will be kept strictly confidential and applied solely for visa facilitation.">{{ old('visa_issue_explanation', $participant->user->visa_issue_explanation) }}</textarea>
                    @error('visa_issue_explanation')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Student Information Section - HIDDEN -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg hidden">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Student Information</h3>
            <p class="text-sm text-gray-600 mb-4">Skip this section if you are not a student.</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Are you a student? *</label>
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_student" value="1" id="is_student_yes" class="form-radio text-blue-600" onchange="toggleStudentFields()" {{ old('is_student', $participant->user->is_student) == '1' ? 'checked' : '' }}>
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_student" value="0" id="is_student_no" class="form-radio text-blue-600" onchange="toggleStudentFields()" {{ old('is_student', $participant->user->is_student) == '0' ? 'checked' : '' }}>
                        <span class="ml-2">No</span>
                    </label>
                </div>
                @error('is_student')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div id="student-fields" class="{{ old('is_student', $participant->user->is_student) == '1' ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="year" class="block text-sm font-medium text-gray-700">Year *</label>
                        <select name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">Select Year</option>
                            <option value="honors_final_year" {{ old('year', $participant->user->year) == 'honors_final_year' ? 'selected' : '' }}>Honors Final Year</option>
                            <option value="masters" {{ old('year', $participant->user->year) == 'masters' ? 'selected' : '' }}>Master's</option>
                        </select>
                        @error('year')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="department_name" class="block text-sm font-medium text-gray-700">Name of Department *</label>
                        <input type="text" name="department_name" id="department_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Computer Science" value="{{ old('department_name', $participant->user->department_name) }}">
                        @error('department_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4 md:col-span-2">
                        <label for="institution_name" class="block text-sm font-medium text-gray-700">Name of Institution *</label>
                        <input type="text" name="institution_name" id="institution_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Your university or college name" value="{{ old('institution_name', $participant->user->institution_name) }}">
                        @error('institution_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information Section -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-green-800 border-b border-green-200 pb-2">Address Information</h3>
            
            <div class="grid grid-cols-1 gap-4">
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address (Optional)</label>
                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Your full address">{{ old('address', $participant->user->address) }}</textarea>
                    @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="home_district" class="block text-sm font-medium text-gray-700">Home District (Optional)</label>
                    <input type="text" name="home_district" id="home_district" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Dhaka, Chittagong, Sylhet" value="{{ old('home_district', $participant->user->home_district) }}">
                    @error('home_district')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Document Upload Section -->
        <div class="mb-8 p-6 bg-yellow-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-yellow-800 border-b border-yellow-200 pb-2">Document Upload</h3>
            
            <div class="mb-4">
                <label for="nid_passport_birth_certificate" class="block text-sm font-medium text-gray-700">NID/Passport/Birth Certificate (Optional)</label>
                <input type="file" name="nid_passport_birth_certificate" id="nid_passport_birth_certificate" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                <p class="text-xs text-gray-500 mt-1">Photo max 300kb</p>
                @error('nid_passport_birth_certificate')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Conference Information Section -->
        <div class="mb-8 p-6 bg-purple-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Conference Information</h3>
            
            
            <div class="mb-4">
                <label for="expertise_interests" class="block text-sm font-medium text-gray-700">Provide your expertise/interests aligning with the theme of BoBC (Optional)</label>
                <textarea name="expertise_interests" id="expertise_interests" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Please provide your expertise and interests (200 words max)" maxlength="1000">{{ old('expertise_interests', $participant->user->expertise_interests) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Any use of AI in the answers would be banned from the conference in the future</p>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Word count: <span id="word-count">0</span>/200</span>
                    <span>Character count: <span id="char-count">0</span>/1000</span>
                </div>
                @error('expertise_interests')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Modern Hashtag Input Section -->
        <div class="mb-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
            <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Hashtags</h3>
            <div class="mb-4">
                <label for="hashtag-input" class="block text-sm font-medium text-gray-700 mb-2">Add Hashtags (Optional)</label>
                <div class="relative">
                    <input type="text" 
                           id="hashtag-input" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" 
                           placeholder="Type hashtags and press Enter or comma..."
                           autocomplete="off">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add hashtags. Click on tags to remove them.</p>
            </div>
            
            <!-- Hashtag Display Area -->
            <div id="hashtag-container" class="flex flex-wrap gap-2 min-h-[40px] p-3 bg-white rounded-lg border border-gray-200">
                <span class="text-gray-400 text-sm italic">No hashtags added yet</span>
            </div>
            
            <!-- Hidden input for form submission -->
            <input type="hidden" id="hashtags" name="hashtags_input" value="{{ old('hashtags', $participant->hashtags) }}">
            @error('hashtags')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <!-- Conference registration details continue -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="visa_status" class="block text-sm font-medium text-gray-700">Visa Status *</label>
                    <select name="visa_status" id="visa_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Status</option>
                <option value="required" {{ old('visa_status', $participant->visa_status) == 'required' ? 'selected' : '' }}>Required</option>
                <option value="not_required" {{ old('visa_status', $participant->visa_status) == 'not_required' ? 'selected' : '' }}>Not Required</option>
                <option value="pending" {{ old('visa_status', $participant->visa_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ old('visa_status', $participant->visa_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="issue" {{ old('visa_status', $participant->visa_status) == 'issue' ? 'selected' : '' }}>Issue (Problem)</option>
            </select>
            @error('visa_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
                
                <div class="mb-4">
                    <label for="registration_status" class="block text-sm font-medium text-gray-700">Registration Status *</label>
                    <select name="registration_status" id="registration_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Status</option>
                        <option value="pending" {{ old('registration_status', $participant->registration_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('registration_status', $participant->registration_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('registration_status', $participant->registration_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('registration_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="travel_intent" class="block text-sm font-medium text-gray-700">Travel Intent (Optional)</label>
                    <select name="travel_intent" id="travel_intent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" onchange="toggleTravelDates()">
                        <option value="none" {{ old('travel_intent', $participant->travel_intent) == 'none' ? 'selected' : '' }}>None</option>
                        <option value="national" {{ old('travel_intent', $participant->travel_intent) == 'national' ? 'selected' : '' }}>National</option>
                        <option value="international" {{ old('travel_intent', $participant->travel_intent) == 'international' ? 'selected' : '' }}>International</option>
                    </select>
                    @error('travel_intent')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <!-- Travel Dates Section (shown when National or International is selected) -->
            <div id="travel-dates-section" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="arrival_date" class="block text-sm font-medium text-gray-700">Arrival Date <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="arrival_date" id="arrival_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('arrival_date', optional($participant->travelDetails)->arrival_date ? \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('Y-m-d\TH:i') : '') }}" placeholder="YYYY-MM-DDTHH:MM">
                        <p class="text-xs text-gray-500 mt-1">Format: YYYY-MM-DDTHH:MM (e.g., 2024-01-15T14:30)</p>
                        @error('arrival_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="departure_date" class="block text-sm font-medium text-gray-700">Departure Date <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="departure_date" id="departure_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('departure_date', optional($participant->travelDetails)->departure_date ? \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('Y-m-d\TH:i') : '') }}" placeholder="YYYY-MM-DDTHH:MM">
                        <p class="text-xs text-gray-500 mt-1">Format: YYYY-MM-DDTHH:MM (e.g., 2024-01-17T10:00)</p>
                        @error('departure_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            
        <div class="mb-4" id="visa-issue-description" style="display: {{ old('visa_status', $participant->visa_status) == 'issue' ? 'block' : 'none' }};">
            <label for="visa_issue_description" class="block text-sm font-medium text-gray-700">Visa Issue Description</label>
            <textarea name="visa_issue_description" id="visa_issue_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Please describe the visa issue or problem...">{{ old('visa_issue_description', $participant->visa_issue_description) }}</textarea>
            @error('visa_issue_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
            
        <div class="mb-4">
            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Brief biography or description...">{{ old('bio', $participant->bio) }}</textarea>
            @error('bio')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- Action Buttons Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('participants.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-8 py-3 modern-primary rounded-lg font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Update Participant
                </button>
        </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Visa status handling
    const visaStatusSelect = document.getElementById('visa_status');
    const visaIssueDescription = document.getElementById('visa-issue-description');
    const participantTypeSelect = document.getElementById('participant_type_id');
    const participantTypeDescription = document.getElementById('participant-type-description');
    const expertiseTextarea = document.getElementById('expertise_interests');
    const wordCountSpan = document.getElementById('word-count');
    const charCountSpan = document.getElementById('char-count');
    
    function toggleVisaIssueDescription() {
        if (visaStatusSelect && visaStatusSelect.value === 'issue') {
            visaIssueDescription.style.display = 'block';
        } else if (visaIssueDescription) {
            visaIssueDescription.style.display = 'none';
        }
    }

    function updateParticipantTypeDescription() {
        if (!participantTypeSelect || !participantTypeDescription) return;
        
        const selectedOption = participantTypeSelect.options[participantTypeSelect.selectedIndex];
        const description = selectedOption.getAttribute('data-description');
        const category = selectedOption.getAttribute('data-category');
        const requiresApproval = selectedOption.getAttribute('data-requires-approval');
        const hasPrivileges = selectedOption.getAttribute('data-has-privileges');
        const participantTypeName = selectedOption.textContent.toLowerCase().trim();

        let descriptionText = '';
        if (description) {
            descriptionText += description + '<br>';
        }
        if (requiresApproval === 'true') {
            descriptionText += 'Requires Approval: Yes<br>';
        } else {
            descriptionText += 'Requires Approval: No<br>';
        }
        if (hasPrivileges === 'true') {
            descriptionText += 'Has Special Privileges: Yes<br>';
        } else {
            descriptionText += 'Has Special Privileges: No<br>';
        }

        participantTypeDescription.innerHTML = descriptionText;
        participantTypeDescription.style.display = 'block';

        // Toggle Media/Speaker sections
        const mediaSection = document.getElementById('media-section');
        const speakerSection = document.getElementById('speaker-section');
        
        if (mediaSection && speakerSection) {
            if (category === 'press') {
                mediaSection.classList.remove('hidden');
                speakerSection.classList.add('hidden');
            } else if (category === 'presenter') {
                speakerSection.classList.remove('hidden');
                mediaSection.classList.add('hidden');
            } else {
                mediaSection.classList.add('hidden');
                speakerSection.classList.add('hidden');
            }
        }

        // Handle student radio button logic
        const studentYesRadio = document.getElementById('is_student_yes');
        const studentNoRadio = document.getElementById('is_student_no');
        
        if (studentYesRadio && studentNoRadio) {
            // Check if the selected participant type is "student"
            if (participantTypeName === 'student') {
                // Auto-select "Yes" for student
                studentYesRadio.checked = true;
                studentNoRadio.checked = false;
                // Show student fields
                toggleStudentFields();
            } else {
                // For all other types, default to "No"
                studentYesRadio.checked = false;
                studentNoRadio.checked = true;
                // Hide student fields
                toggleStudentFields();
            }
        }
    }
    
    // Student fields toggle
    window.toggleStudentFields = function() {
        const studentFields = document.getElementById('student-fields');
        const isStudentRadios = document.querySelectorAll('input[name="is_student"]');
        const isStudent = Array.from(isStudentRadios).find(radio => radio.checked);
        
        if (studentFields) {
            if (isStudent && isStudent.value === '1') {
                studentFields.classList.remove('hidden');
                // Make student fields required
                const yearSelect = document.getElementById('year');
                const deptInput = document.getElementById('department_name');
                const instInput = document.getElementById('institution_name');
                
                if (yearSelect) yearSelect.required = true;
                if (deptInput) deptInput.required = true;
                if (instInput) instInput.required = true;
            } else {
                studentFields.classList.add('hidden');
                // Make student fields not required
                const yearSelect = document.getElementById('year');
                const deptInput = document.getElementById('department_name');
                const instInput = document.getElementById('institution_name');
                
                if (yearSelect) yearSelect.required = false;
                if (deptInput) deptInput.required = false;
                if (instInput) instInput.required = false;
            }
        }
    };
    
    // Travel dates toggle
    window.toggleTravelDates = function() {
        const travelIntentSelect = document.getElementById('travel_intent');
        const travelDatesSection = document.getElementById('travel-dates-section');
        
        if (travelIntentSelect && travelDatesSection) {
            if (travelIntentSelect.value === 'national' || travelIntentSelect.value === 'international') {
                travelDatesSection.classList.remove('hidden');
                // Set default values if fields are empty
                setDefaultTravelDates();
                // Make fields required
                makeTravelDatesRequired(true);
            } else {
                travelDatesSection.classList.add('hidden');
                // Clear the date fields when hidden
                const arrivalDate = document.getElementById('arrival_date');
                const departureDate = document.getElementById('departure_date');
                if (arrivalDate) arrivalDate.value = '';
                if (departureDate) departureDate.value = '';
                // Make fields not required
                makeTravelDatesRequired(false);
            }
        }
    };
    
    // Set default travel dates based on conference dates
    function setDefaultTravelDates() {
        const arrivalDate = document.getElementById('arrival_date');
        const departureDate = document.getElementById('departure_date');
        
        // Only set defaults if fields are empty
        if (arrivalDate && !arrivalDate.value) {
            arrivalDate.value = '{{ $participant->conference->start_date ? \Carbon\Carbon::parse($participant->conference->start_date)->subDay()->format('Y-m-d\TH:i') : '' }}';
        }
        if (departureDate && !departureDate.value) {
            departureDate.value = '{{ $participant->conference->end_date ? \Carbon\Carbon::parse($participant->conference->end_date)->addDay()->format('Y-m-d\TH:i') : '' }}';
        }
    }
    
    // Make travel date fields required or not
    function makeTravelDatesRequired(required) {
        const arrivalDate = document.getElementById('arrival_date');
        const departureDate = document.getElementById('departure_date');
        
        if (arrivalDate) {
            arrivalDate.required = required;
        }
        if (departureDate) {
            departureDate.required = required;
        }
    }
    
    // Word and character counting
    function updateWordCount() {
        if (!expertiseTextarea || !wordCountSpan || !charCountSpan) return;
        
        const text = expertiseTextarea.value;
        const words = text.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        const charCount = text.length;
        
        wordCountSpan.textContent = wordCount;
        charCountSpan.textContent = charCount;
        
        // Add visual feedback for limits
        if (wordCount > 200) {
            wordCountSpan.style.color = 'red';
        } else if (wordCount > 180) {
            wordCountSpan.style.color = 'orange';
        } else {
            wordCountSpan.style.color = 'inherit';
        }
        
        if (charCount > 1000) {
            charCountSpan.style.color = 'red';
        } else if (charCount > 900) {
            charCountSpan.style.color = 'orange';
        } else {
            charCountSpan.style.color = 'inherit';
        }
    }
    
    // Age calculation
    function calculateAge() {
        const dobInput = document.getElementById('date_of_birth');
        if (!dobInput || !dobInput.value) return;
        
        const dob = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        // You can display the age somewhere if needed
        console.log('Calculated age:', age);
    }
    
    // Initial state
    toggleVisaIssueDescription();
    updateParticipantTypeDescription();
    toggleStudentFields();
    toggleTravelDates();
    updateWordCount();
    
    // Event listeners
    if (visaStatusSelect) {
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    }
    if (participantTypeSelect) {
    participantTypeSelect.addEventListener('change', updateParticipantTypeDescription);
    }
    if (expertiseTextarea) {
        expertiseTextarea.addEventListener('input', updateWordCount);
    }
    
    const travelIntentSelect = document.getElementById('travel_intent');
    if (travelIntentSelect) {
        travelIntentSelect.addEventListener('change', toggleTravelDates);
    }
    
    const dobInput = document.getElementById('date_of_birth');
    if (dobInput) {
        dobInput.addEventListener('change', calculateAge);
    }

    // Dietary requirements other toggle
    const dietaryReq = document.getElementById('dietary_requirements');
    const dietaryReqOtherWrap = document.getElementById('dietary_req_other_wrap');
    if (dietaryReq && dietaryReqOtherWrap) {
        const toggleDietaryReqOther = () => {
            if (dietaryReq.value === 'others') {
                dietaryReqOtherWrap.style.display = '';
            } else {
                dietaryReqOtherWrap.style.display = 'none';
            }
        };
        toggleDietaryReqOther();
        dietaryReq.addEventListener('change', toggleDietaryReqOther);
    }

    // Visa issue explanation toggle
    const hadVisaIssue = document.getElementById('had_visa_issue_bd');
    const visaIssueWrap = document.getElementById('visa_issue_explanation_wrap');
    if (hadVisaIssue && visaIssueWrap) {
        const toggleVisaIssue = () => {
            if (hadVisaIssue.value === '1') {
                visaIssueWrap.style.display = '';
        } else {
                visaIssueWrap.style.display = 'none';
        }
        };
        toggleVisaIssue();
        hadVisaIssue.addEventListener('change', toggleVisaIssue);
    }

    // Email validation with AJAX
    const emailInput = document.getElementById('email');
    const emailError = document.createElement('div');
    emailError.className = 'text-red-600 text-sm mt-1 hidden';
    emailInput.parentNode.appendChild(emailError);

    let emailTimeout;
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(emailTimeout);
        
        // Hide error initially
        emailError.classList.add('hidden');
        emailError.textContent = '';
        
        // Remove error styling
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
        
        // Only validate if email is not empty and looks valid
        if (email && email.includes('@')) {
            // Debounce the AJAX call
            emailTimeout = setTimeout(() => {
                fetch('{{ route("participants.check-email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        user_id: {{ $participant->user_id }} // Exclude current user from check
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        emailError.textContent = data.message;
                        emailError.classList.remove('hidden');
                        emailInput.classList.remove('border-gray-300');
                        emailInput.classList.add('border-red-500');
                    } else {
                        emailError.classList.add('hidden');
                        emailInput.classList.remove('border-red-500');
                        emailInput.classList.add('border-gray-300');
                    }
                })
                .catch(error => {
                    console.error('Email validation error:', error);
                });
            }, 500); // 500ms delay
        }
    });

    // Initialize form state on page load (for validation errors)
    function initializeFormState() {
        const participantTypeSelect = document.getElementById('participant_type_id');
        if (participantTypeSelect && participantTypeSelect.value) {
            // Trigger the change event to show appropriate sections
            participantTypeSelect.dispatchEvent(new Event('change'));
        }
    }

    // Initialize form state
    initializeFormState();
    
    // Messaging platform selection handler
    const messagingRadios = document.querySelectorAll('input[name="messaging_type"]');
    const messagingNumberInput = document.getElementById('messaging_number');
    
    function updateMessagingPlaceholder() {
        const selectedPlatform = document.querySelector('input[name="messaging_type"]:checked');
        if (selectedPlatform && messagingNumberInput) {
            switch(selectedPlatform.value) {
                case 'whatsapp':
                    messagingNumberInput.placeholder = '+8801234567890';
                    break;
                case 'signal':
                    messagingNumberInput.placeholder = '+8801234567890';
                    break;
                case 'telegram':
                    messagingNumberInput.placeholder = '@username or +8801234567890';
                    break;
                default:
                    messagingNumberInput.placeholder = '+8801234567890';
            }
        }
    }
    
    // Add event listeners to messaging platform radios
    messagingRadios.forEach(radio => {
        radio.addEventListener('change', updateMessagingPlaceholder);
    });
    
    // Initialize placeholder
    updateMessagingPlaceholder();
    
    // Modern Hashtag Input Handling
    const hashtagInput = document.getElementById('hashtag-input');
    const hashtagContainer = document.getElementById('hashtag-container');
    const hiddenInput = document.getElementById('hashtags');
    let hashtags = [];

    if (hashtagInput && hashtagContainer && hiddenInput) {
        // Initialize hashtags from existing value
        const existingHashtags = hiddenInput.value;
        if (existingHashtags) {
            hashtags = existingHashtags.split(',').map(tag => tag.trim().replace(/^#+/, '')).filter(tag => tag);
        }

        // Function to create hashtag element
        function createHashtagElement(tag) {
            const tagElement = document.createElement('div');
            tagElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200 hover:from-purple-200 hover:to-pink-200 transition-all duration-200 cursor-pointer group';
            tagElement.innerHTML = `
                <span class="mr-1">#</span>
                <span>${tag}</span>
                <button type="button" class="ml-2 text-purple-600 hover:text-purple-800 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            // Add click to remove functionality
            tagElement.addEventListener('click', function() {
                removeHashtag(tag);
            });
            
            return tagElement;
        }

        // Function to add hashtag
        function addHashtag(tag) {
            const cleanTag = tag.trim().replace(/^#+/, ''); // Remove existing # symbols
            if (cleanTag && !hashtags.includes(cleanTag)) {
                hashtags.push(cleanTag);
                updateDisplay();
                updateHiddenInput();
            }
        }

        // Function to remove hashtag
        function removeHashtag(tag) {
            hashtags = hashtags.filter(t => t !== tag);
            updateDisplay();
            updateHiddenInput();
        }

        // Function to update display
        function updateDisplay() {
            hashtagContainer.innerHTML = '';
            if (hashtags.length === 0) {
                hashtagContainer.innerHTML = '<span class="text-gray-400 text-sm italic">No hashtags added yet</span>';
            } else {
                hashtags.forEach(tag => {
                    hashtagContainer.appendChild(createHashtagElement(tag));
                });
            }
        }

        // Function to update hidden input
        function updateHiddenInput() {
            hiddenInput.value = hashtags.join(', ');
        }

        // Handle input events
        hashtagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const value = this.value.trim();
                if (value) {
                    addHashtag(value);
                    this.value = '';
                }
            }
        });

        // Handle paste events
        hashtagInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                const value = this.value.trim();
                if (value) {
                    // Split by comma and add each hashtag
                    value.split(',').forEach(tag => {
                        if (tag.trim()) {
                            addHashtag(tag.trim());
                        }
                    });
                    this.value = '';
                }
            }, 0);
        });

        // Handle blur event
        hashtagInput.addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                addHashtag(value);
                this.value = '';
            }
        });

        // Initialize display
        updateDisplay();
    }
});

// Global form validation function
function validateForm(event) {
    // Get all required elements
    const participantTypeSelect = document.getElementById('participant_type_id');
    const mediaTypeSelect = document.getElementById('media_type');
    const dietaryRequirements = document.getElementById('dietary_requirements');
    const dietaryRequirementsOther = document.getElementById('dietary_requirements_other');
    const travelIntentSelect = document.getElementById('travel_intent');
    const arrivalDate = document.getElementById('arrival_date');
    const departureDate = document.getElementById('departure_date');
    
    // Check if participant type is selected
    if (participantTypeSelect && participantTypeSelect.value) {
        const selectedOption = participantTypeSelect.options[participantTypeSelect.selectedIndex];
        const category = selectedOption.getAttribute('data-category');
        
        // Check if it's a press/media type
        if (category === 'press') {
            // Check if media type is selected
            if (mediaTypeSelect && !mediaTypeSelect.value) {
                alert('Please select a Type of Media.');
                mediaTypeSelect.focus();
                return false;
            }
        }
        
        // Check if it's a presenter/speaker type
        if (category === 'presenter') {
            // Check dietary requirements validation
            if (dietaryRequirements && dietaryRequirements.value === 'others') {
                if (!dietaryRequirementsOther || !dietaryRequirementsOther.value.trim()) {
                    alert('Please specify your dietary requirements when "Others" is selected.');
                    dietaryRequirementsOther.focus();
                    return false;
                }
            }
        }
    }
    
    // Check travel dates validation
    if (travelIntentSelect && (travelIntentSelect.value === 'national' || travelIntentSelect.value === 'international')) {
        if (!arrivalDate || !arrivalDate.value) {
            alert('Please select an arrival date when travel intent is selected.');
            if (arrivalDate) arrivalDate.focus();
            return false;
        }
        if (!departureDate || !departureDate.value) {
            alert('Please select a departure date when travel intent is selected.');
            if (departureDate) departureDate.focus();
            return false;
        }
        
        // Check if departure date is after arrival date
        if (arrivalDate && departureDate && arrivalDate.value && departureDate.value) {
            const arrival = new Date(arrivalDate.value);
            const departure = new Date(departureDate.value);
            if (departure <= arrival) {
                alert('Departure date must be after arrival date.');
                departureDate.focus();
                return false;
            }
        }
    }
    
    return true;
}
</script>

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
@endsection 