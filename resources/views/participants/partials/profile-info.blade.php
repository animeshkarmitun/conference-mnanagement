<form method="POST" action="{{ route('participants.update', $participant) }}" enctype="multipart/form-data" class="space-y-6" id="personal-info-form">
    @csrf
    @method('PUT')
    
<<<<<<< Updated upstream
=======
    <!-- Hidden fields for admin-only validation (not shown in personal info form) -->
    <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
    <input type="hidden" name="conference_id" value="{{ $participant->conference_id }}">
    <input type="hidden" name="participant_type_id" value="{{ $participant->participant_type_id }}">
    
>>>>>>> Stashed changes
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
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" 
                       name="first_name" 
                       value="{{ old('first_name', $participant->user->first_name ?? $participant->user->name) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('first_name') border-red-300 @enderror"
                       required
                       maxlength="50"
                       placeholder="First Name">
                <input type="text" 
                       name="last_name" 
                       value="{{ old('last_name', $participant->user->last_name) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('last_name') border-red-300 @enderror"
                       required
                       maxlength="50"
                       placeholder="Last Name">
            </div>
            @error('first_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('last_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Email <span class="text-red-500">*</span>
            </label>
            <input type="email" 
                   name="email" 
                   value="{{ old('email', $participant->user->email) }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('email') border-red-300 @enderror"
                   required>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
<<<<<<< Updated upstream
=======

        <!-- Enhanced participant fields -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('gender') border-red-300 @enderror">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $participant->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $participant->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="prefer_not_to_say" {{ old('gender', $participant->user->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer Not to Say</option>
                </select>
                @error('gender')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pronoun</label>
                <select name="pronoun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('pronoun') border-red-300 @enderror">
                    <option value="">Select Pronoun</option>
                    <option value="he_him" {{ old('pronoun', $participant->user->pronoun) == 'he_him' ? 'selected' : '' }}>He/Him</option>
                    <option value="she_her" {{ old('pronoun', $participant->user->pronoun) == 'she_her' ? 'selected' : '' }}>She/Her</option>
                    <option value="they_them" {{ old('pronoun', $participant->user->pronoun) == 'they_them' ? 'selected' : '' }}>They/Them</option>
                </select>
                @error('pronoun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact No (Optional)</label>
                <input type="tel" 
                       name="contact_no" 
                       value="{{ old('contact_no', $participant->user->contact_no ?: '+8801234567890') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('contact_no') border-red-300 @enderror"
                       placeholder="+8801234567890">
                @error('contact_no')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp No (Optional)</label>
                <input type="tel" 
                       name="whatsapp_no" 
                       value="{{ old('whatsapp_no', $participant->user->whatsapp_no ?: '+8801234567890') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('whatsapp_no') border-red-300 @enderror"
                       placeholder="+8801234567890">
                @error('whatsapp_no')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth (Optional)</label>
                <input type="date" 
                       name="date_of_birth" 
                       value="{{ old('date_of_birth', $participant->user->date_of_birth) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('date_of_birth') border-red-300 @enderror">
                <p class="text-xs text-gray-500 mt-1">Age will be calculated automatically</p>
                @error('date_of_birth')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Field of Work/Study (Optional)</label>
                <input type="text" 
                       name="field_of_work_study" 
                       value="{{ old('field_of_work_study', $participant->user->field_of_work_study) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('field_of_work_study') border-red-300 @enderror"
                       placeholder="e.g., Computer Science, Medicine, Engineering">
                @error('field_of_work_study')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Designation (Optional)</label>
                <input type="text" 
                       name="designation" 
                       value="{{ old('designation', $participant->user->designation) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('designation') border-red-300 @enderror"
                       placeholder="e.g., Software Engineer, Professor, Student">
                @error('designation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Organization/Institution (Optional)</label>
                <input type="text" 
                       name="organization_institution" 
                       value="{{ old('organization_institution', $participant->user->organization_institution) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('organization_institution') border-red-300 @enderror"
                       placeholder="Your current organization or institution">
                @error('organization_institution')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

>>>>>>> Stashed changes
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
            <input type="text" 
                   name="organization" 
                   value="{{ old('organization', $participant->organization) }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('organization') border-red-300 @enderror"
                   placeholder="Your organization or company"
                   maxlength="100">
            @error('organization')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
<<<<<<< Updated upstream
    </div>
=======

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" 
                      rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('address') border-red-300 @enderror"
                      placeholder="Your full address">{{ old('address', $participant->user->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Home District</label>
                <input type="text" 
                       name="home_district" 
                       value="{{ old('home_district', $participant->user->home_district) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('home_district') border-red-300 @enderror"
                       placeholder="e.g., Dhaka, Chittagong, Sylhet">
                @error('home_district')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">How did you find out about BoBC?</label>
                <select name="how_found_bobc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('how_found_bobc') border-red-300 @enderror">
                    <option value="">Select Option</option>
                    <option value="social_media" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'social_media' ? 'selected' : '' }}>Social Media</option>
                    <option value="bobc_cgs_website" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'bobc_cgs_website' ? 'selected' : '' }}>BoBC/CGS Website</option>
                    <option value="friend_teacher_department" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'friend_teacher_department' ? 'selected' : '' }}>Friend/Teacher/Department</option>
                    <option value="traditional_media" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'traditional_media' ? 'selected' : '' }}>Traditional Media</option>
                    <option value="other" {{ old('how_found_bobc', $participant->user->how_found_bobc) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('how_found_bobc')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('category') border-red-300 @enderror">
                    <option value="">Select Category</option>
                    <option value="student" {{ old('category', $participant->category) == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="academic" {{ old('category', $participant->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                    <option value="industry" {{ old('category', $participant->category) == 'industry' ? 'selected' : '' }}>Industry</option>
                    <option value="government" {{ old('category', $participant->category) == 'government' ? 'selected' : '' }}>Government</option>
                    <option value="ngo" {{ old('category', $participant->category) == 'ngo' ? 'selected' : '' }}>NGO</option>
                    <option value="press" {{ old('category', $participant->category) == 'press' ? 'selected' : '' }}>Press</option>
                    <option value="presenter" {{ old('category', $participant->category) == 'presenter' ? 'selected' : '' }}>Presenter</option>
                    <option value="other" {{ old('category', $participant->category) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Travel Intent</label>
                <select name="travel_intent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('travel_intent') border-red-300 @enderror">
                    <option value="national" {{ old('travel_intent', $participant->travel_intent) == 'national' ? 'selected' : '' }}>National</option>
                    <option value="international" {{ old('travel_intent', $participant->travel_intent) == 'international' ? 'selected' : '' }}>International</option>
                </select>
                @error('travel_intent')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Have you attended BOB-C before?</label>
            <select name="attended_previous_bobc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('attended_previous_bobc') border-red-300 @enderror">
                <option value="">Select</option>
                <option value="1" {{ old('attended_previous_bobc', $participant->user->attended_previous_bobc) == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('attended_previous_bobc', $participant->user->attended_previous_bobc) == '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('attended_previous_bobc')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Are you a student?</label>
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="is_student" value="1" id="is_student_yes" {{ old('is_student', $participant->user->is_student) == '1' ? 'checked' : '' }} class="form-radio text-blue-600" onchange="toggleStudentFields()">
                    <span class="ml-2">Yes</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="is_student" value="0" id="is_student_no" {{ old('is_student', $participant->user->is_student) == '0' ? 'checked' : '' }} class="form-radio text-blue-600" onchange="toggleStudentFields()">
                    <span class="ml-2">No</span>
                </label>
            </div>
            @error('is_student')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div id="student-fields" class="mt-4 {{ old('is_student', $participant->user->is_student) == '1' ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('year') border-red-300 @enderror">
                        <option value="">Select Year</option>
                        <option value="honors_final_year" {{ old('year', $participant->user->year) == 'honors_final_year' ? 'selected' : '' }}>Honors Final Year</option>
                        <option value="masters" {{ old('year', $participant->user->year) == 'masters' ? 'selected' : '' }}>Master's</option>
                    </select>
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department Name</label>
                    <input type="text" 
                           name="department_name" 
                           value="{{ old('department_name', $participant->user->department_name) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('department_name') border-red-300 @enderror"
                           placeholder="e.g., Computer Science">
                    @error('department_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Institution Name</label>
                <input type="text" 
                       name="institution_name" 
                       value="{{ old('institution_name', $participant->user->institution_name) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('institution_name') border-red-300 @enderror"
                       placeholder="Your university or college name">
                @error('institution_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Expertise/Interests</label>
            <textarea name="expertise_interests" 
                      rows="4" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('expertise_interests') border-red-300 @enderror"
                      placeholder="Provide your expertise/interests aligning with the theme of BoBC (200 words max)" 
                      maxlength="1000">{{ old('expertise_interests', $participant->user->expertise_interests) }}</textarea>
            <div class="mt-1 text-xs text-gray-500 text-right">
                <span id="expertise-char-count">0</span>/1000 characters
            </div>
            @error('expertise_interests')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>


    <!-- Media-specific fields (shown when participant type is press) -->
    @if($participant->participantType && $participant->participantType->category === 'press')
    <div class="bg-blue-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Media Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type of Media</label>
                <select name="media_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('media_type') border-red-300 @enderror">
                    <option value="">Select Type</option>
                    <option value="print" {{ old('media_type', $participant->user->media_type) == 'print' ? 'selected' : '' }}>Print</option>
                    <option value="television" {{ old('media_type', $participant->user->media_type) == 'television' ? 'selected' : '' }}>Television</option>
                    <option value="online_portal" {{ old('media_type', $participant->user->media_type) == 'online_portal' ? 'selected' : '' }}>Online Portal</option>
                </select>
                @error('media_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    @endif

    <!-- Speaker-specific fields (shown when participant type is presenter) -->
    @if($participant->participantType && $participant->participantType->category === 'presenter')
    <div class="bg-green-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800 mb-4">Speaker Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Other Contact Type</label>
                <select name="other_contact_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('other_contact_type') border-red-300 @enderror">
                    <option value="">Select Type</option>
                    <option value="whatsapp" {{ old('other_contact_type', $participant->user->other_contact_type) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="telegram" {{ old('other_contact_type', $participant->user->other_contact_type) == 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="signal" {{ old('other_contact_type', $participant->user->other_contact_type) == 'signal' ? 'selected' : '' }}>Signal</option>
                </select>
                @error('other_contact_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Other Contact Number</label>
                <input type="text" 
                       name="other_contact_no" 
                       value="{{ old('other_contact_no', $participant->user->other_contact_no) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('other_contact_no') border-red-300 @enderror"
                       placeholder="Contact number">
                @error('other_contact_no')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                <select name="sector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('sector') border-red-300 @enderror">
                    <option value="">Select Sector</option>
                    <option value="academia" {{ old('sector', $participant->user->sector) == 'academia' ? 'selected' : '' }}>Academia</option>
                    <option value="government" {{ old('sector', $participant->user->sector) == 'government' ? 'selected' : '' }}>Government</option>
                    <option value="international_organization" {{ old('sector', $participant->user->sector) == 'international_organization' ? 'selected' : '' }}>International Organization</option>
                    <option value="media" {{ old('sector', $participant->user->sector) == 'media' ? 'selected' : '' }}>Media</option>
                    <option value="ngo" {{ old('sector', $participant->user->sector) == 'ngo' ? 'selected' : '' }}>NGO</option>
                    <option value="private" {{ old('sector', $participant->user->sector) == 'private' ? 'selected' : '' }}>Private</option>
                    <option value="think_tank" {{ old('sector', $participant->user->sector) == 'think_tank' ? 'selected' : '' }}>Think-Tank</option>
                </select>
                @error('sector')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>


        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Areas of Expertise</label>
            <textarea name="areas_of_expertise" 
                      rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('areas_of_expertise') border-red-300 @enderror"
                      placeholder="Describe your areas of expertise...">{{ old('areas_of_expertise', $participant->user->areas_of_expertise) }}</textarea>
            @error('areas_of_expertise')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Topic to Speak</label>
            <input type="text" 
                   name="preferred_topic" 
                   value="{{ old('preferred_topic', $participant->user->preferred_topic) }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('preferred_topic') border-red-300 @enderror"
                   placeholder="What topic would you like to speak about?">
            @error('preferred_topic')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>


        <!-- Social Links -->
        <div class="mt-4">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Social Links</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Link</label>
                    <input type="url" 
                           name="linkedin_link" 
                           value="{{ old('linkedin_link', $participant->user->linkedin_link) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('linkedin_link') border-red-300 @enderror"
                           placeholder="https://linkedin.com/in/yourprofile">
                    @error('linkedin_link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Twitter Link</label>
                    <input type="url" 
                           name="twitter_link" 
                           value="{{ old('twitter_link', $participant->user->twitter_link) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('twitter_link') border-red-300 @enderror"
                           placeholder="https://twitter.com/yourhandle">
                    @error('twitter_link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Link</label>
                    <input type="url" 
                           name="facebook_link" 
                           value="{{ old('facebook_link', $participant->user->facebook_link) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('facebook_link') border-red-300 @enderror"
                           placeholder="https://facebook.com/yourprofile">
                    @error('facebook_link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Passport/Visa Information -->
        <div class="mt-4">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Passport & Visa Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Do you have a current/valid passport?</label>
                    <select name="has_valid_passport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('has_valid_passport') border-red-300 @enderror">
                        <option value="">Select</option>
                        <option value="1" {{ old('has_valid_passport', $participant->user->has_valid_passport) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('has_valid_passport', $participant->user->has_valid_passport) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('has_valid_passport')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Did you previously face issues regarding a visa to Bangladesh?</label>
                    <select name="had_visa_issue_bd" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('had_visa_issue_bd') border-red-300 @enderror">
                        <option value="">Select</option>
                        <option value="1" {{ old('had_visa_issue_bd', $participant->user->had_visa_issue_bd) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('had_visa_issue_bd', $participant->user->had_visa_issue_bd) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('had_visa_issue_bd')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-4" id="visa-issue-explanation" style="display: {{ old('had_visa_issue_bd', $participant->user->had_visa_issue_bd) == '1' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 mb-1">If yes, kindly provide a brief explanation</label>
                <textarea name="visa_issue_explanation" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('visa_issue_explanation') border-red-300 @enderror"
                          placeholder="These details will be kept strictly confidential and applied solely for visa facilitation.">{{ old('visa_issue_explanation', $participant->user->visa_issue_explanation) }}</textarea>
                @error('visa_issue_explanation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Dietary Requirements for Speakers -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Requirements (Optional)</label>
                <select name="dietary_requirements" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('dietary_requirements') border-red-300 @enderror">
                    <option value="">Select</option>
                    <option value="veg" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'veg' ? 'selected' : '' }}>Veg</option>
                    <option value="non_veg" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'non_veg' ? 'selected' : '' }}>Non-Veg</option>
                    <option value="vegan" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'vegan' ? 'selected' : '' }}>Vegan</option>
                    <option value="others" {{ old('dietary_requirements', $participant->user->dietary_requirements) == 'others' ? 'selected' : '' }}>Others</option>
                </select>
                @error('dietary_requirements')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div id="dietary-requirements-other" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Please specify</label>
                <input type="text" 
                       name="dietary_requirements_other" 
                       value="{{ old('dietary_requirements_other', $participant->user->dietary_requirements_other) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('dietary_requirements_other') border-red-300 @enderror"
                       placeholder="Please specify your dietary requirements">
                @error('dietary_requirements_other')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    @endif
    
    <!-- System Information (Admin Only) -->
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('superadmin'))
    <div class="bg-purple-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-purple-800 mb-4">System Information</h3>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Status</label>
            <select name="registration_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('registration_status') border-red-300 @enderror">
                <option value="pending" {{ old('registration_status', $participant->registration_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ old('registration_status', $participant->registration_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ old('registration_status', $participant->registration_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('registration_status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    @endif
>>>>>>> Stashed changes
    
    <!-- Additional Information -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Information</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea name="bio" 
                      rows="4" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('bio') border-red-300 @enderror"
                      placeholder="Tell us about yourself, your background, and interests..."
                      maxlength="500">{{ old('bio', $participant->bio) }}</textarea>
            <div class="mt-1 text-xs text-gray-500 text-right">
                <span id="bio-char-count">0</span>/500 characters
            </div>
            @error('bio')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
    </div>
    <!-- Visa Information -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Visa Information</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Visa Status <span class="text-red-500">*</span>
            </label>
            <select name="visa_status" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('visa_status') border-red-300 @enderror"
                    required>
                <option value="">Select visa status</option>
                <option value="required" {{ old('visa_status', $participant->visa_status) == 'required' ? 'selected' : '' }}>Required</option>
                <option value="not_required" {{ old('visa_status', $participant->visa_status) == 'not_required' ? 'selected' : '' }}>Not Required</option>
                <option value="pending" {{ old('visa_status', $participant->visa_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ old('visa_status', $participant->visa_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="issue" {{ old('visa_status', $participant->visa_status) == 'issue' ? 'selected' : '' }}>Issue (Problem)</option>
            </select>
            @error('visa_status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div id="visa-issue-description" class="mb-4 {{ old('visa_status', $participant->visa_status) == 'issue' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Visa Issue Description</label>
            <textarea name="visa_issue_description" 
                      rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('visa_issue_description') border-red-300 @enderror"
                      placeholder="Please describe the visa issue or problem you're experiencing..."
                      maxlength="1000">{{ old('visa_issue_description', $participant->visa_issue_description) }}</textarea>
            <div class="mt-1 text-xs text-gray-500 text-right">
                <span id="visa-char-count">0</span>/1000 characters
            </div>
            @error('visa_issue_description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
    <!-- Documents & Files -->
    <div class="bg-gray-50 p-4 rounded-lg">
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
                        <input type="file" 
                               name="profile_picture" 
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 transition-colors duration-200">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
                        <div id="profile-picture-preview" class="mt-2 hidden">
                            <p class="text-xs text-green-600">✓ File selected</p>
                        </div>
                    </div>
                </div>
                @error('profile_picture')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NID/Passport/Birth Certificate</label>
                <div class="mt-1">
                    @if($participant->user->nid_passport_birth_certificate && Storage::disk('public')->exists($participant->user->nid_passport_birth_certificate))
                        <div class="mb-2">
                            <a href="{{ Storage::disk('public')->url($participant->user->nid_passport_birth_certificate) }}" 
                               target="_blank"
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Current Document
                            </a>
                        </div>
                    @endif
                    <input type="file" 
                           name="nid_passport_birth_certificate" 
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 transition-colors duration-200">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
                    <div id="nid-preview" class="mt-2 hidden">
                        <p class="text-xs text-green-600">✓ File selected</p>
                    </div>
                </div>
                @error('nid_passport_birth_certificate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
        <div class="text-sm text-gray-500">
            <span class="text-red-500">*</span> Required fields
        </div>
        <div class="flex space-x-3">
            <button type="button" 
                    onclick="window.location.reload()" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors duration-200">
                Cancel
            </button>
            <button type="submit" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg font-semibold focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Update Information
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Visa status handling
    const visaStatusSelect = document.querySelector('select[name="visa_status"]');
    const visaIssueDescription = document.getElementById('visa-issue-description');
    const visaCharCount = document.getElementById('visa-char-count');
    const visaTextarea = document.querySelector('textarea[name="visa_issue_description"]');
    
    function toggleVisaIssueDescription() {
        if (visaStatusSelect.value === 'issue') {
            visaIssueDescription.classList.remove('hidden');
            if (visaTextarea) {
                visaTextarea.focus();
            }
        } else {
            visaIssueDescription.classList.add('hidden');
        }
    }
    
    // Character counter for visa description
    if (visaTextarea && visaCharCount) {
        function updateVisaCharCount() {
            const length = visaTextarea.value.length;
            visaCharCount.textContent = length;
            if (length > 900) {
                visaCharCount.classList.add('text-red-500');
            } else {
                visaCharCount.classList.remove('text-red-500');
            }
        }
        
        visaTextarea.addEventListener('input', updateVisaCharCount);
        updateVisaCharCount(); // Initial count
    }
    
    
    // Bio character counter
    const bioTextarea = document.querySelector('textarea[name="bio"]');
    const bioCharCount = document.getElementById('bio-char-count');
    
    if (bioTextarea && bioCharCount) {
        function updateBioCharCount() {
            const length = bioTextarea.value.length;
            bioCharCount.textContent = length;
            if (length > 450) {
                bioCharCount.classList.add('text-red-500');
            } else {
                bioCharCount.classList.remove('text-red-500');
            }
        }
        
        bioTextarea.addEventListener('input', updateBioCharCount);
        updateBioCharCount(); // Initial count
    }
    
    // File upload validation and preview
    const profilePictureInput = document.querySelector('input[name="profile_picture"]');
    const nidInput = document.querySelector('input[name="nid_passport_birth_certificate"]');
    const profilePicturePreview = document.getElementById('profile-picture-preview');
    const nidPreview = document.getElementById('nid-preview');
    
    function validateFileSize(file, maxSizeMB) {
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            alert(`File size must be less than ${maxSizeMB}MB`);
            return false;
        }
        return true;
    }
    
    function showFilePreview(input, previewElement, maxSizeMB) {
        const file = input.files[0];
        if (file) {
            if (validateFileSize(file, maxSizeMB)) {
                previewElement.classList.remove('hidden');
                previewElement.querySelector('p').textContent = `✓ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`;
                previewElement.querySelector('p').className = 'text-xs text-green-600';
            } else {
                input.value = '';
                previewElement.classList.add('hidden');
            }
        } else {
            previewElement.classList.add('hidden');
        }
    }
    
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function() {
            showFilePreview(this, profilePicturePreview, 2);
        });
    }
    
    
    if (nidInput) {
        nidInput.addEventListener('change', function() {
            showFilePreview(this, nidPreview, 2);
        });
    }
    
    // Form auto-save functionality
    const form = document.getElementById('personal-info-form');
    let autoSaveTimeout;
    
    function autoSave() {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Show saving indicator
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        submitButton.disabled = true;
        
        // Simulate auto-save (in real implementation, this would be an AJAX call)
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            // Show success indicator
            const successIndicator = document.createElement('div');
            successIndicator.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            successIndicator.textContent = 'Draft saved automatically';
            document.body.appendChild(successIndicator);
            
            setTimeout(() => {
                successIndicator.remove();
            }, 3000);
        }, 1000);
    }
    
    // Auto-save on input changes
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(autoSave, 2000); // Auto-save after 2 seconds of inactivity
    });
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating...
        `;
        submitButton.disabled = true;
    });
    
<<<<<<< Updated upstream
    // Initialize all event listeners
    toggleVisaIssueDescription();
    toggleDietaryOther();
    
    // Listen for changes
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    dietarySelect.addEventListener('change', toggleDietaryOther);
=======
    // Student fields toggle
    window.toggleStudentFields = function() {
        const studentFields = document.getElementById('student-fields');
        const isStudentRadios = document.querySelectorAll('input[name="is_student"]');
        const isStudent = Array.from(isStudentRadios).find(radio => radio.checked);
        
        if (studentFields) {
            if (isStudent && isStudent.value === '1') {
                studentFields.classList.remove('hidden');
                // Make student fields required
                const yearSelect = document.querySelector('select[name="year"]');
                const deptInput = document.querySelector('input[name="department_name"]');
                const instInput = document.querySelector('input[name="institution_name"]');
                
                if (yearSelect) yearSelect.required = true;
                if (deptInput) deptInput.required = true;
                if (instInput) instInput.required = true;
            } else {
                studentFields.classList.add('hidden');
                // Make student fields not required
                const yearSelect = document.querySelector('select[name="year"]');
                const deptInput = document.querySelector('input[name="department_name"]');
                const instInput = document.querySelector('input[name="institution_name"]');
                
                if (yearSelect) yearSelect.required = false;
                if (deptInput) deptInput.required = false;
                if (instInput) instInput.required = false;
            }
        }
    };

    // Dietary requirements "other" field toggle
    const dietaryRequirementsSelect = document.querySelector('select[name="dietary_requirements"]');
    const dietaryRequirementsOther = document.getElementById('dietary-requirements-other');
    
    function toggleDietaryRequirementsOther() {
        if (dietaryRequirementsSelect && dietaryRequirementsOther) {
            if (dietaryRequirementsSelect.value === 'others') {
                dietaryRequirementsOther.classList.remove('hidden');
            } else {
                dietaryRequirementsOther.classList.add('hidden');
            }
        }
    }
    
    // Initialize all event listeners
    toggleVisaIssueDescription();
    toggleStudentFields();
    toggleDietaryRequirementsOther();
    
    // Listen for changes
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    if (dietaryRequirementsSelect) {
        dietaryRequirementsSelect.addEventListener('change', toggleDietaryRequirementsOther);
    }

    // Visa issue explanation toggle for speaker section
    const hadVisaIssueBd = document.querySelector('select[name="had_visa_issue_bd"]');
    const visaIssueExplanation = document.getElementById('visa-issue-explanation');
    
    if (hadVisaIssueBd && visaIssueExplanation) {
        function toggleVisaIssueExplanation() {
            if (hadVisaIssueBd.value === '1') {
                visaIssueExplanation.style.display = 'block';
            } else {
                visaIssueExplanation.style.display = 'none';
            }
        }
        
        toggleVisaIssueExplanation();
        hadVisaIssueBd.addEventListener('change', toggleVisaIssueExplanation);
    }
>>>>>>> Stashed changes
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
        
        // Escape to cancel
        if (e.key === 'Escape') {
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                window.location.reload();
            }
        }
    });
});
</script> 