@extends('layouts.app')

@section('title', 'Participant Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('participants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Participants
            </a>
        </div>

        <!-- Professional Page Header -->
        <div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
            <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
                <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">
                    {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                </h1>
                <div class="text-gray-600 text-lg font-medium">
                    {{ ucwords(str_replace('_', ' ', $participant->participantType->name ?? '')) }}
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $participant->conference->name ?? 'Conference' }}
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('participants.edit', $participant) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Participant
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
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
                        <button class="tab-link py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-800 focus:outline-none transition-all duration-200 relative {{ ($participant->registration_status ?? '') === 'approved' ? 'tab-complete' : '' }}" data-tab="status">
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
            </div>
        </div>
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
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tab links
            tabLinks.forEach(link => {
                link.classList.remove('border-green-500', 'text-green-600');
                link.classList.add('border-transparent', 'text-gray-600');
                const indicator = link.querySelector('.tab-indicator');
                if (indicator) {
                    indicator.style.width = '0';
                }
            });
            
            // Show selected tab content
            const selectedContent = document.getElementById('tab-' + tabName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
            
            // Activate selected tab link
            const selectedLink = document.querySelector(`[data-tab="${tabName}"]`);
            if (selectedLink) {
                selectedLink.classList.remove('border-transparent', 'text-gray-600');
                selectedLink.classList.add('border-green-500', 'text-green-600');
                const indicator = selectedLink.querySelector('.tab-indicator');
                if (indicator) {
                    indicator.style.width = '100%';
                }
            }
            
            // Save active tab to localStorage
            localStorage.setItem('participant-active-tab', tabName);
            
            // Update tab completion indicators
            updateTabCompletion();
        }
        
        // Add tab completion indicators
        function updateTabCompletion() {
            tabLinks.forEach(link => {
                const tabName = link.dataset.tab;
                const content = document.getElementById(`tab-${tabName}`);
                
                // Special handling for travel tab
                if (tabName === 'travel') {
                    const arrivalDate = content.querySelector('input[name="arrival_date"]');
                    const departureDate = content.querySelector('input[name="departure_date"]');
                    const flightInfo = content.querySelector('textarea[name="flight_info"]');
                    
                    // Debug logging
                    console.log('Travel tab completion check (admin):');
                    console.log('Arrival Date:', arrivalDate?.value);
                    console.log('Departure Date:', departureDate?.value);
                    console.log('Flight Info:', flightInfo?.value);
                    
                    // Check if essential travel fields are filled
                    // Hotel is optional, so we don't require it for completion
                    const isComplete = arrivalDate && arrivalDate.value.trim() !== '' &&
                                     departureDate && departureDate.value.trim() !== '' &&
                                     flightInfo && flightInfo.value.trim() !== '';
                    
                    console.log('Is travel complete (admin):', isComplete);
                    
                    if (isComplete) {
                        link.classList.add('tab-complete');
                        console.log('Added tab-complete class to travel tab (admin)');
                    } else {
                        link.classList.remove('tab-complete');
                        console.log('Removed tab-complete class from travel tab (admin)');
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
                    console.log('Personal Info tab completion check (admin):');
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
                    
                    console.log('Is Personal Info complete (admin):', isComplete);
                    
                    if (isComplete) {
                        link.classList.add('tab-complete');
                        console.log('Added tab-complete class to Personal Info tab (admin)');
                    } else {
                        link.classList.remove('tab-complete');
                        console.log('Removed tab-complete class from Personal Info tab (admin)');
                    }
                } else {
                    // Default completion logic for other tabs
                    const requiredFields = content.querySelectorAll('[required], .required-field');
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
        
        // Add click event listeners to tab links
        tabLinks.forEach(link => {
            link.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                switchTab(tabName);
            });
        });
        
        // Update completion on form changes
        document.addEventListener('input', updateTabCompletion);
        document.addEventListener('change', updateTabCompletion);
        
        // Initialize with active tab
        switchTab(activeTab);
        
        // Initial completion check
        setTimeout(updateTabCompletion, 100);
    });
</script>

<style>
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
</style>
@endpush
@endsection
