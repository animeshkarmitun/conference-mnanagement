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
    <form method="POST" action="{{ route('participants.update', $participant) }}" enctype="multipart/form-data" onsubmit="return validateEditForm(event)">
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
                    <label for="whatsapp_no" class="block text-sm font-medium text-gray-700">WhatsApp No (Optional)</label>
                    <input type="tel" name="whatsapp_no" id="whatsapp_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="+8801234567890" value="{{ old('whatsapp_no', $participant->user->whatsapp_no) }}">
                    @error('whatsapp_no')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
                        <label for="other_contact_type" class="block text-sm font-medium text-gray-700">Other Contact Number (Optional)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <select name="other_contact_type" id="other_contact_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('other_contact_type') border-red-300 @enderror">
                                <option value="">Select</option>
                                <option value="whatsapp" {{ old('other_contact_type', $participant->user->other_contact_type) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="telegram" {{ old('other_contact_type', $participant->user->other_contact_type) == 'telegram' ? 'selected' : '' }}>Telegram</option>
                                <option value="signal" {{ old('other_contact_type', $participant->user->other_contact_type) == 'signal' ? 'selected' : '' }}>Signal</option>
                            </select>
                            <input type="text" name="other_contact_no" id="other_contact_no" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('other_contact_no') border-red-300 @enderror" placeholder="Number" value="{{ old('other_contact_no', $participant->user->other_contact_no) }}">
                        </div>
                        @error('other_contact_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('other_contact_no')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
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
                    <div class="mb-4">
                        <label for="sector" class="block text-sm font-medium text-gray-700">Sector (Optional)</label>
                        <select name="sector" id="sector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">Select Sector</option>
                            <option value="academia" {{ old('sector', $participant->user->sector) == 'academia' ? 'selected' : '' }}>Academia</option>
                            <option value="government" {{ old('sector', $participant->user->sector) == 'government' ? 'selected' : '' }}>Government</option>
                            <option value="international_organization" {{ old('sector', $participant->user->sector) == 'international_organization' ? 'selected' : '' }}>International Organization</option>
                            <option value="media" {{ old('sector', $participant->user->sector) == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="ngo" {{ old('sector', $participant->user->sector) == 'ngo' ? 'selected' : '' }}>NGO</option>
                            <option value="private" {{ old('sector', $participant->user->sector) == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="think_tank" {{ old('sector', $participant->user->sector) == 'think_tank' ? 'selected' : '' }}>Think-Tank</option>
                        </select>
                        @error('sector')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4 md:col-span-2">
                        <label for="areas_of_expertise" class="block text-sm font-medium text-gray-700">Areas of Expertise (Optional)</label>
                        <textarea name="areas_of_expertise" id="areas_of_expertise" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('areas_of_expertise', $participant->user->areas_of_expertise) }}</textarea>
                        @error('areas_of_expertise')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
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
                        <label for="linkedin_link" class="block text-sm font-medium text-gray-700">LinkedIn Link</label>
                        <input type="url" name="linkedin_link" id="linkedin_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('linkedin_link', $participant->user->linkedin_link) }}">
                        @error('linkedin_link')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="twitter_link" class="block text-sm font-medium text-gray-700">Twitter Link</label>
                        <input type="url" name="twitter_link" id="twitter_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" value="{{ old('twitter_link', $participant->user->twitter_link) }}">
                        @error('twitter_link')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="facebook_link" class="block text-sm font-medium text-gray-700">Facebook Link</label>
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

        <!-- Student Information Section -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg">
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
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="nid_passport_birth_certificate" class="block text-sm font-medium text-gray-700">NID/Passport/Birth Certificate (Optional)</label>
                    <input type="file" name="nid_passport_birth_certificate" id="nid_passport_birth_certificate" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                    <p class="text-xs text-gray-500 mt-1">Photo max 300kb</p>
                    @error('nid_passport_birth_certificate')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="mb-8 p-6 bg-purple-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">Additional Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="how_found_bobc" class="block text-sm font-medium text-gray-700">How did you find out about BOB-C? (Optional)</label>
                    <select name="how_found_bobc" id="how_found_bobc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select</option>
                        <option value="social_media" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="bobc_cgs_website" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'bobc_cgs_website' ? 'selected' : '' }}>BOB-C CGS Website</option>
                        <option value="friend_teacher_department" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'friend_teacher_department' ? 'selected' : '' }}>Friend/Teacher/Department</option>
                        <option value="traditional_media" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'traditional_media' ? 'selected' : '' }}>Traditional Media</option>
                        <option value="other" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('how_found_bobc')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
        <div class="mb-4">
                    <label for="attended_previous_bobc" class="block text-sm font-medium text-gray-700">Have you attended BOB-C before? (Optional)</label>
                    <select name="attended_previous_bobc" id="attended_previous_bobc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select</option>
                        <option value="1" {{ old('attended_previous_bobc', $participant->user->attended_previous_bobc) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('attended_previous_bobc', $participant->user->attended_previous_bobc) == '0' ? 'selected' : '' }}>No</option>
            </select>
                    @error('attended_previous_bobc')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label for="expertise_interests" class="block text-sm font-medium text-gray-700">Expertise and Interests (Optional)</label>
                <textarea name="expertise_interests" id="expertise_interests" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Please provide your expertise and interests (200 words max)" maxlength="1000">{{ old('expertise_interests', $participant->user->expertise_interests) }}</textarea>
                @error('expertise_interests')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Professional Information Section -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-green-800 border-b border-green-200 pb-2">Professional Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="organization" class="block text-sm font-medium text-gray-700">Organization (Optional)</label>
                    <input type="text" name="organization" id="organization" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Your organization or company" value="{{ old('organization', $participant->organization) }}">
                    @error('organization')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="dietary_needs" class="block text-sm font-medium text-gray-700">Dietary Needs (Optional)</label>
                    <input type="text" name="dietary_needs" id="dietary_needs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Vegetarian, Halal, Gluten-free" value="{{ old('dietary_needs', $participant->dietary_needs) }}">
                    @error('dietary_needs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- System Information Section -->
        <div class="mb-8 p-6 bg-purple-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-purple-800 border-b border-purple-200 pb-2">System Information</h3>
            
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
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Category</option>
                        <option value="student" {{ old('category', $participant->category) == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="academic" {{ old('category', $participant->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="industry" {{ old('category', $participant->category) == 'industry' ? 'selected' : '' }}>Industry</option>
                        <option value="government" {{ old('category', $participant->category) == 'government' ? 'selected' : '' }}>Government</option>
                        <option value="ngo" {{ old('category', $participant->category) == 'ngo' ? 'selected' : '' }}>NGO</option>
                        <option value="other" {{ old('category', $participant->category) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label for="travel_intent" class="block text-sm font-medium text-gray-700">Travel Intent (Optional)</label>
                    <select name="travel_intent" id="travel_intent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="national" {{ old('travel_intent', $participant->travel_intent) == 'national' ? 'selected' : '' }}>National</option>
                        <option value="international" {{ old('travel_intent', $participant->travel_intent) == 'international' ? 'selected' : '' }}>International</option>
                    </select>
                    @error('travel_intent')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
    
    function toggleVisaIssueDescription() {
        if (visaStatusSelect.value === 'issue') {
            visaIssueDescription.style.display = 'block';
        } else {
            visaIssueDescription.style.display = 'none';
        }
    }
    
    // Initial state
    toggleVisaIssueDescription();
    
    // Listen for changes
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    
    // Participant type description handling
    const participantTypeSelect = document.getElementById('participant_type_id');
    const participantTypeDescription = document.getElementById('participant-type-description');
    
    function updateParticipantTypeDescription() {
        const selectedOption = participantTypeSelect.options[participantTypeSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.description) {
            const requiresApproval = selectedOption.dataset.requiresApproval === 'true' ? 'Yes' : 'No';
            const hasPrivileges = selectedOption.dataset.hasPrivileges === 'true' ? 'Yes' : 'No';
            
            participantTypeDescription.innerHTML = `
                <div>Requires Approval: ${requiresApproval}</div>
                <div>Has Special Privileges: ${hasPrivileges}</div>
            `;
        } else {
            participantTypeDescription.innerHTML = `
                <div>Requires Approval: No</div>
                <div>Has Special Privileges: No</div>
            `;
        }
    }
    
    // Initial state
    updateParticipantTypeDescription();
    
    // Listen for changes
    participantTypeSelect.addEventListener('change', updateParticipantTypeDescription);
    
    // Student fields handling
    function toggleStudentFields() {
        const isStudentYes = document.getElementById('is_student_yes');
        const studentFields = document.getElementById('student-fields');
        
        if (isStudentYes.checked) {
            studentFields.classList.remove('hidden');
        } else {
            studentFields.classList.add('hidden');
        }
    }
    
    // Initial state
    toggleStudentFields();
    
    // Listen for changes
    const isStudentRadios = document.querySelectorAll('input[name="is_student"]');
    isStudentRadios.forEach(radio => {
        radio.addEventListener('change', toggleStudentFields);
    });
    
    // Dietary requirements handling
    const dietaryRequirements = document.getElementById('dietary_requirements');
    const dietaryReqOtherWrap = document.getElementById('dietary_req_other_wrap');
    
    function toggleDietaryReqOther() {
        if (dietaryRequirements.value === 'others') {
            dietaryReqOtherWrap.style.display = 'block';
        } else {
            dietaryReqOtherWrap.style.display = 'none';
        }
    }
    
    // Initial state
    toggleDietaryReqOther();
    
    // Listen for changes
    dietaryRequirements.addEventListener('change', toggleDietaryReqOther);
    
    // Visa issue explanation handling
    const hadVisaIssueBd = document.getElementById('had_visa_issue_bd');
    const visaIssueExplanationWrap = document.getElementById('visa_issue_explanation_wrap');
    
    function toggleVisaIssueExplanation() {
        if (hadVisaIssueBd.value === '1') {
            visaIssueExplanationWrap.style.display = 'block';
        } else {
            visaIssueExplanationWrap.style.display = 'none';
        }
    }
    
    // Initial state
    toggleVisaIssueExplanation();
    
    // Listen for changes
    hadVisaIssueBd.addEventListener('change', toggleVisaIssueExplanation);
    
    // Category-based section visibility
    const categorySelect = document.getElementById('category');
    const mediaSection = document.getElementById('media-section');
    const speakerSection = document.getElementById('speaker-section');
    
    function toggleCategorySections() {
        const category = categorySelect.value;
        
        // Hide all sections first
        mediaSection.classList.add('hidden');
        speakerSection.classList.add('hidden');
        
        // Show relevant section based on category
        if (category === 'press') {
            mediaSection.classList.remove('hidden');
        } else if (category === 'presenter') {
            speakerSection.classList.remove('hidden');
        }
    }
    
    // Initial state
    toggleCategorySections();
    
    // Listen for changes
    categorySelect.addEventListener('change', toggleCategorySections);

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

    // Form validation is handled by the onsubmit attribute on the form element
});

// Global form validation function for edit form
function validateEditForm(event) {
    // Get all required elements
    const participantTypeSelect = document.getElementById('participant_type_id');
    const mediaTypeSelect = document.getElementById('media_type');
    const dietaryRequirements = document.getElementById('dietary_requirements');
    const dietaryRequirementsOther = document.getElementById('dietary_requirements_other');
    
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