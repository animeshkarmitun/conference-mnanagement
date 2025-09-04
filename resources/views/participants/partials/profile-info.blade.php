<form method="POST" action="{{ route('participants.update', $participant) }}" enctype="multipart/form-data" class="space-y-6" id="personal-info-form">
    @csrf
    @method('PUT')
    
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="first_name" 
                       value="{{ old('first_name', $participant->user->first_name ?? $participant->user->name) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('first_name') border-red-300 @enderror"
                       required
                       maxlength="50">
                @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="last_name" 
                       value="{{ old('last_name', $participant->user->last_name) }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('last_name') border-red-300 @enderror"
                       required
                       maxlength="50">
                @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
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
    </div>
    
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
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Needs</label>
            <select name="dietary_needs" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200 @error('dietary_needs') border-red-300 @enderror">
                <option value="">Select dietary preference</option>
                <option value="none" {{ old('dietary_needs', $participant->dietary_needs) == 'none' ? 'selected' : '' }}>No special requirements</option>
                <option value="vegetarian" {{ old('dietary_needs', $participant->dietary_needs) == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                <option value="vegan" {{ old('dietary_needs', $participant->dietary_needs) == 'vegan' ? 'selected' : '' }}>Vegan</option>
                <option value="gluten-free" {{ old('dietary_needs', $participant->dietary_needs) == 'gluten-free' ? 'selected' : '' }}>Gluten-free</option>
                <option value="dairy-free" {{ old('dietary_needs', $participant->dietary_needs) == 'dairy-free' ? 'selected' : '' }}>Dairy-free</option>
                <option value="halal" {{ old('dietary_needs', $participant->dietary_needs) == 'halal' ? 'selected' : '' }}>Halal</option>
                <option value="kosher" {{ old('dietary_needs', $participant->dietary_needs) == 'kosher' ? 'selected' : '' }}>Kosher</option>
                <option value="other" {{ old('dietary_needs', $participant->dietary_needs) == 'other' ? 'selected' : '' }}>Other (please specify)</option>
            </select>
            @error('dietary_needs')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div id="dietary-other" class="mb-4 {{ old('dietary_needs', $participant->dietary_needs) == 'other' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Specify Dietary Requirements</label>
            <input type="text" 
                   name="dietary_needs_other" 
                   value="{{ old('dietary_needs_other') }}" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors duration-200"
                   placeholder="Please specify your dietary requirements">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Resume/CV</label>
                <div class="mt-1">
                    @if($participant->user->resume && Storage::disk('public')->exists($participant->user->resume))
                        <div class="mb-2">
                            <a href="{{ route('participants.download-resume', $participant) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Current Resume
                            </a>
                        </div>
                    @endif
                                            <input type="file" 
                               name="resume" 
                               accept=".pdf,.doc,.docx"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 transition-colors duration-200">
                        <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX up to 5MB</p>
                        <div id="resume-preview" class="mt-2 hidden">
                            <p class="text-xs text-green-600">✓ File selected</p>
                        </div>
                </div>
                @error('resume')
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
    
    // Dietary needs handling
    const dietarySelect = document.querySelector('select[name="dietary_needs"]');
    const dietaryOther = document.getElementById('dietary-other');
    
    function toggleDietaryOther() {
        if (dietarySelect.value === 'other') {
            dietaryOther.classList.remove('hidden');
        } else {
            dietaryOther.classList.add('hidden');
        }
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
    const resumeInput = document.querySelector('input[name="resume"]');
    const profilePicturePreview = document.getElementById('profile-picture-preview');
    const resumePreview = document.getElementById('resume-preview');
    
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
    
    if (resumeInput) {
        resumeInput.addEventListener('change', function() {
            showFilePreview(this, resumePreview, 5);
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
    
    // Initialize all event listeners
    toggleVisaIssueDescription();
    toggleDietaryOther();
    
    // Listen for changes
    visaStatusSelect.addEventListener('change', toggleVisaIssueDescription);
    dietarySelect.addEventListener('change', toggleDietaryOther);
    
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