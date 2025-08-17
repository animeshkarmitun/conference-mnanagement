@extends('layouts.participant')

@section('title', 'Participant Details')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">
            {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
        </h1>
        <div class="text-gray-600 text-lg font-medium">
            {{ $participant->participantType->name ?? '' }}
        </div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 participant-details-fix mt-8 min-h-[60vh]">
    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="info">
                Personal Info
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="travel">
                Travel & Accommodation
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="sessions">
                Sessions
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="status">
                Status
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="notifications">
                Notifications
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="comments">
                Comments
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
            <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative" data-tab="conference-kit">
                Conference Kit
                <span class="tab-indicator absolute -bottom-0.5 left-0 w-0 h-0.5 bg-green-500 transition-all duration-200"></span>
            </button>
        </nav>
    </div>

    <!-- Tabs Content -->
    <div id="tab-info" class="tab-content">
        @include('participants.partials.profile-info', ['participant' => $participant])
    </div>
    <div id="tab-travel" class="tab-content hidden">
        @include('participants.partials.profile-travel', ['participant' => $participant, 'travelDetail' => $travelDetail ?? null, 'hotels' => $hotels ?? []])
    </div>
    <div id="tab-sessions" class="tab-content hidden">
        @include('participants.partials.profile-sessions', ['sessions' => $sessions ?? [], 'participant' => $participant])
    </div>
    <div id="tab-status" class="tab-content hidden">
        @include('participants.partials.profile-status', ['participant' => $participant])
    </div>
    <div id="tab-notifications" class="tab-content hidden">
        @include('participants.partials.profile-notifications', ['notifications' => $notifications ?? []])
    </div>
    <div id="tab-comments" class="tab-content hidden">
        @include('participants.partials.profile-comments', ['comments' => $comments ?? [], 'participant' => $participant])
    </div>
    <div id="tab-conference-kit" class="tab-content hidden">
        @include('participants.partials.profile-conference-kit', ['participant' => $participant])
    </div>

    <div class="mt-4">
        <a href="{{ route('participant-dashboard') }}" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        
        // Get active tab from localStorage or default to first tab
        const activeTab = localStorage.getItem('participant-active-tab') || 'info';
        
        function switchTab(tabName) {
            // Remove active classes from all tabs
            tabLinks.forEach(l => {
                l.classList.remove('border-yellow-600', 'text-yellow-600');
                l.classList.add('border-transparent');
            });
            tabContents.forEach(c => c.classList.add('hidden'));
            
            // Add active classes to selected tab
            const activeLink = document.querySelector(`[data-tab="${tabName}"]`);
            const activeContent = document.getElementById(`tab-${tabName}`);
            
            if (activeLink && activeContent) {
                activeLink.classList.add('border-yellow-600', 'text-yellow-600');
                activeLink.classList.remove('border-transparent');
                activeContent.classList.remove('hidden');
                
                // Save active tab to localStorage
                localStorage.setItem('participant-active-tab', tabName);
                
                // Trigger custom event for tab-specific functionality
                window.dispatchEvent(new CustomEvent('tabChanged', { detail: { tab: tabName } }));
            }
        }
        
        // Add click handlers
        tabLinks.forEach(link => {
            link.addEventListener('click', function () {
                switchTab(this.dataset.tab);
            });
        });
        
        // Initialize with saved tab or first tab
        switchTab(activeTab);
        
        // Add tab completion indicators
        function updateTabCompletion() {
            tabLinks.forEach(link => {
                const tabName = link.dataset.tab;
                const content = document.getElementById(`tab-${tabName}`);
                const requiredFields = content.querySelectorAll('[required], .required-field');
                
                // Special handling for travel tab
                if (tabName === 'travel') {
                    const arrivalDate = content.querySelector('input[name="arrival_date"]');
                    const departureDate = content.querySelector('input[name="departure_date"]');
                    const flightInfo = content.querySelector('textarea[name="flight_info"]');
                    
                    // Debug logging
                    console.log('Travel tab completion check:');
                    console.log('Arrival Date:', arrivalDate?.value);
                    console.log('Departure Date:', departureDate?.value);
                    console.log('Flight Info:', flightInfo?.value);
                    
                    // Check if essential travel fields are filled
                    // Hotel is optional, so we don't require it for completion
                    const isComplete = arrivalDate && arrivalDate.value.trim() !== '' &&
                                     departureDate && departureDate.value.trim() !== '' &&
                                     flightInfo && flightInfo.value.trim() !== '';
                    
                    console.log('Is travel complete:', isComplete);
                    
                    if (isComplete) {
                        link.classList.add('tab-complete');
                        console.log('Added tab-complete class to travel tab');
                    } else {
                        link.classList.remove('tab-complete');
                        console.log('Removed tab-complete class from travel tab');
                    }
                } else if (tabName === 'sessions') {
                    // Special handling for sessions tab
                    const sessionItems = content.querySelectorAll('ul li');
                    const hasSessions = sessionItems.length > 0;
                    
                    if (hasSessions) {
                        link.classList.add('tab-complete');
                    } else {
                        link.classList.remove('tab-complete');
                    }
                } else if (tabName === 'info') {
                    // Special handling for Personal Info tab - require ALL fields to be filled
                    const firstName = content.querySelector('input[name="first_name"]');
                    const lastName = content.querySelector('input[name="last_name"]');
                    const email = content.querySelector('input[name="email"]');
                    const organization = content.querySelector('input[name="organization"]');
                    const bio = content.querySelector('textarea[name="bio"]');
                    const dietaryNeeds = content.querySelector('select[name="dietary_needs"]');
                    const visaStatus = content.querySelector('select[name="visa_status"]');
                    
                    // Debug logging
                    console.log('Personal Info tab completion check:');
                    console.log('First Name:', firstName?.value);
                    console.log('Last Name:', lastName?.value);
                    console.log('Email:', email?.value);
                    console.log('Organization:', organization?.value);
                    console.log('Bio:', bio?.value);
                    console.log('Dietary Needs:', dietaryNeeds?.value);
                    console.log('Visa Status:', visaStatus?.value);
                    
                    // Check if ALL fields are filled
                    const isComplete = firstName && firstName.value.trim() !== '' &&
                                     lastName && lastName.value.trim() !== '' &&
                                     email && email.value.trim() !== '' &&
                                     organization && organization.value.trim() !== '' &&
                                     bio && bio.value.trim() !== '' &&
                                     dietaryNeeds && dietaryNeeds.value !== '' &&
                                     visaStatus && visaStatus.value !== '';
                    
                    console.log('Is Personal Info complete:', isComplete);
                    
                    if (isComplete) {
                        link.classList.add('tab-complete');
                        console.log('Added tab-complete class to Personal Info tab');
                    } else {
                        link.classList.remove('tab-complete');
                        console.log('Removed tab-complete class from Personal Info tab');
                    }
                } else {
                    // Default completion logic for other tabs
                    const filledFields = Array.from(requiredFields).filter(field => {
                        if (field.type === 'file') return field.files.length > 0;
                        return field.value.trim() !== '';
                    });
                    
                    // Add completion indicator
                    if (requiredFields.length > 0) {
                        const completion = (filledFields.length / requiredFields.length) * 100;
                        if (completion === 100) {
                            link.classList.add('tab-complete');
                        } else {
                            link.classList.remove('tab-complete');
                        }
                    }
                }
            });
        }
        
        // Update completion on form changes
        document.addEventListener('input', updateTabCompletion);
        document.addEventListener('change', updateTabCompletion);
        
        // Initial completion check
        setTimeout(updateTabCompletion, 100);
    });
</script>
<style>
    /* Targeted fix for participant details page layout shift */
    .participant-details-fix {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    /* Tab completion indicators */
    .tab-link {
        position: relative;
    }
    
    .tab-complete::after {
        content: '✓';
        position: absolute;
        top: 0;
        right: 0;
        background: #10b981;
        color: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 2px;
        margin-right: 2px;
        z-index: 10;
    }
    
    /* Tab indicator animation */
    .tab-link.border-yellow-600 .tab-indicator {
        width: 100%;
    }
    
    /* Form field focus states */
    .form-field-focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Loading animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Success notification animation */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification-slide-in {
        animation: slideInRight 0.3s ease-out;
    }
    
    /* File upload styling */
    input[type="file"]::-webkit-file-upload-button {
        background: #fbbf24;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 9999px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-right: 8px;
    }
    
    input[type="file"]::-webkit-file-upload-button:hover {
        background: #f59e0b;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    input[type="file"]::-webkit-file-upload-button:active {
        transform: translateY(0);
    }
    
    /* Firefox file input styling */
    input[type="file"]::file-selector-button {
        background: #fbbf24;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 9999px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-right: 8px;
    }
    
    input[type="file"]::file-selector-button:hover {
        background: #f59e0b;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* File input container styling */
    .file-input-container {
        position: relative;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .file-input-container:hover {
        border-color: #fbbf24;
        background-color: #fef3c7;
    }
    
    .file-input-container.dragover {
        border-color: #f59e0b;
        background-color: #fde68a;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .tab-link {
            padding: 12px 8px;
            font-size: 14px;
        }
        
        .grid-cols-1.md\\:grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush 

@endsection