@extends('layouts.app')

@section('title', 'Add Participant')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Add New Participant</h2>
    <form method="POST" action="{{ route('participants.store') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- User Information Section -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">User Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                    <input type="text" name="first_name" id="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('first_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('last_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer_not_to_say">Prefer not to say</option>
                    </select>
                    @error('gender')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                    <input type="text" name="nationality" id="nationality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., American, British, Indian">
                    @error('nationality')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="profession" class="block text-sm font-medium text-gray-700">Profession</label>
                    <input type="text" name="profession" id="profession" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Software Engineer, Professor, Student">
                    @error('profession')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('date_of_birth')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
                    <input type="text" name="organization" id="organization" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('organization')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="dietary_needs" class="block text-sm font-medium text-gray-700">Dietary Needs</label>
                    <input type="text" name="dietary_needs" id="dietary_needs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Vegetarian, Gluten-free">
                    @error('dietary_needs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                    @error('profile_picture')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="resume" class="block text-sm font-medium text-gray-700">Resume/CV</label>
                    <input type="file" name="resume" id="resume" accept="application/pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500">
                    @error('resume')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Participant Information Section -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Participant Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference *</label>
                    <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Conference</option>
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}">{{ $conference->title }}</option>
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
                                                data-requires-approval="{{ $type->requires_approval ? 'true' : 'false' }}"
                                                data-has-privileges="{{ $type->has_special_privileges ? 'true' : 'false' }}">
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
                    <div id="participant-type-description" class="mt-1 text-sm text-gray-500 hidden"></div>
                    @error('participant_type_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="visa_status" class="block text-sm font-medium text-gray-700">Visa Status *</label>
                    <select name="visa_status" id="visa_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="required">Required</option>
                        <option value="not_required">Not Required</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="issue">Issue (Problem)</option>
                    </select>
                    @error('visa_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="registration_status" class="block text-sm font-medium text-gray-700">Registration Status *</label>
                    <select name="registration_status" id="registration_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    @error('registration_status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Category</option>
                        <option value="student">Student</option>
                        <option value="academic">Academic</option>
                        <option value="industry">Industry</option>
                        <option value="government">Government</option>
                        <option value="ngo">NGO</option>
                        <option value="other">Other</option>
                    </select>
                    @error('category')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="mb-4" id="visa-issue-description" style="display: none;">
                <label for="visa_issue_description" class="block text-sm font-medium text-gray-700">Visa Issue Description</label>
                <textarea name="visa_issue_description" id="visa_issue_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Please describe the visa issue or problem..."></textarea>
                @error('visa_issue_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Travel Intent *</label>
                    <div class="mt-2 flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="travel_intent" value="1" required class="form-radio text-yellow-600">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="travel_intent" value="0" required class="form-radio text-yellow-600">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                    @error('travel_intent')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="mb-4">
                    <label for="travel_form_submitted" class="block text-sm font-medium text-gray-700">Travel Form Submitted</label>
                    <input type="checkbox" name="travel_form_submitted" id="travel_form_submitted" value="1">
                    @error('travel_form_submitted')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea name="bio" id="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Brief biography or description..."></textarea>
                @error('bio')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="approved" class="block text-sm font-medium text-gray-700">Approved</label>
                <input type="checkbox" name="approved" id="approved" value="1">
                @error('approved')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        
        <div class="flex justify-end">
            <a href="{{ route('participants.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Create Participant</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visaStatusSelect = document.getElementById('visa_status');
    const visaIssueDescription = document.getElementById('visa-issue-description');
    const participantTypeSelect = document.getElementById('participant_type_id');
    const participantTypeDescription = document.getElementById('participant-type-description');
    
    function toggleVisaIssueDescription() {
        if (visaStatusSelect.value === 'issue') {
            visaIssueDescription.style.display = 'block';
        } else {
            visaIssueDescription.style.display = 'none';
        }
    }

    function updateParticipantTypeDescription() {
        const selectedOption = participantTypeSelect.options[participantTypeSelect.selectedIndex];
        const description = selectedOption.getAttribute('data-description');
        const requiresApproval = selectedOption.getAttribute('data-requires-approval');
        const hasPrivileges = selectedOption.getAttribute('data-has-privileges');

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
    }
    
    // Initial state
    toggleVisaIssueDescription();
    updateParticipantTypeDescription(); // Set initial description
    
    // Listen for changes
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    participantTypeSelect.addEventListener('change', updateParticipantTypeDescription);
});
</script>
@endsection 