@extends('layouts.app')

@section('title', 'Passwordless Login Management')

@push('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stat-item {
        text-align: center;
        padding: 10px;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .action-btn {
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Passwordless Login Management</h1>
        <p class="text-gray-600 mt-2">Generate secure login links for conference attendees and speakers</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_tokens'] }}</div>
                <div class="stat-label">Total Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['active_tokens'] }}</div>
                <div class="stat-label">Active Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['used_tokens'] }}</div>
                <div class="stat-label">Used Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['tokens_created_today'] }}</div>
                <div class="stat-label">Created Today</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Filter Participants</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Participant Type</label>
                <select id="typeFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="globalSearch" placeholder="Search participants..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button onclick="applyFilters()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="openGenerateModal()" 
                    class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Generate Single Link
            </button>
            
            <button onclick="openBulkModal()" 
                    class="action-btn bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Generate Bulk Links
            </button>
            
            <button onclick="cleanupExpired()" 
                    class="action-btn bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Cleanup Expired
            </button>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Login Activity</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentLogins as $login)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($login->user->first_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $login->user->first_name }} {{ $login->user->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $login->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($login->used_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Used
                                    </span>
                                @elseif($login->expires_at->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $login->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $login->expires_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if(!$login->used_at && $login->expires_at->isFuture())
                                    <button onclick="copyToClipboard('{{ route('passwordless-login.verify', $login->token) }}')" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Copy Link
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No recent login activity
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Single Link Modal -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Generate Login Link</h3>
                <form id="generateForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Conference</label>
                        <div class="relative">
                            <select id="conferenceSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none" required>
                                <option value="">Search and select a conference...</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Participant</label>
                        <div class="relative">
                            <select id="userSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none" required>
                                <option value="">Search and select a participant...</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration (days)</label>
                        <input type="number" id="expirationDays" value="1" min="1" max="90" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeGenerateModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div id="bulkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Generate Bulk Login Links</h3>
                <form id="bulkForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Conference</label>
                        <div class="relative">
                            <select id="bulkConferenceSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                                <option value="">Search and select a conference...</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Participants</label>
                        <div class="mb-2">
                            <input type="text" id="bulkParticipantSearch" placeholder="Search participants..." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="participantList" class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                            <div class="text-center text-gray-500">Loading participants...</div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration (days)</label>
                        <input type="number" id="bulkExpirationDays" value="1" min="1" max="90" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeBulkModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Generate All</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Load participant types for filtering
async function loadParticipantTypes() {
    try {
        const response = await fetch('{{ route("passwordless-login.participant-types") }}');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('typeFilter');
            select.innerHTML = '<option value="">All Types</option>';
            
            Object.keys(data.data).forEach(category => {
                const optgroup = document.createElement('optgroup');
                optgroup.label = category;
                data.data[category].forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.name;
                    option.textContent = type.name;
                    optgroup.appendChild(option);
                });
                select.appendChild(optgroup);
            });
        }
    } catch (error) {
        console.error('Error loading participant types:', error);
    }
}

// Apply filters
async function applyFilters() {
    const typeFilter = document.getElementById('typeFilter').value;
    const searchTerm = document.getElementById('globalSearch').value;
    
    try {
        let url = '{{ route("passwordless-login.participants") }}';
        if (typeFilter) {
            url = '{{ route("passwordless-login.participants.by-type") }}?type=' + encodeURIComponent(typeFilter);
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            let filteredParticipants = data.data;
            
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                filteredParticipants = filteredParticipants.filter(participant => 
                    participant.first_name.toLowerCase().includes(searchLower) ||
                    participant.last_name.toLowerCase().includes(searchLower) ||
                    participant.email.toLowerCase().includes(searchLower) ||
                    participant.participant_types.toLowerCase().includes(searchLower)
                );
            }
            
            // Update the display
            updateParticipantDisplay(filteredParticipants);
        }
    } catch (error) {
        console.error('Error applying filters:', error);
    }
}

// Update participant display
function updateParticipantDisplay(participants) {
    // Update single select
    const select = document.getElementById('userSelect');
    select.innerHTML = '<option value="">Select a participant...</option>';
    participants.forEach(participant => {
        const option = document.createElement('option');
        option.value = participant.id;
        option.textContent = `${participant.first_name} ${participant.last_name} (${participant.email}) - ${participant.participant_types}`;
        select.appendChild(option);
    });
    
    // Update bulk list
    allParticipants = participants;
    renderBulkParticipants(participants);
}

// Load conferences for dropdown
async function loadConferences() {
    try {
        const response = await fetch('{{ route("api.conferences") }}');
        const data = await response.json();
        
        if (data.conferences) {
            const select = document.getElementById('conferenceSelect');
            select.innerHTML = '<option value="">Search and select a conference...</option>';
            data.conferences.forEach(conference => {
                const option = document.createElement('option');
                option.value = conference.id;
                option.textContent = conference.name;
                select.appendChild(option);
            });
            
            // Update searchable select if it exists
            if (window.conferenceSelect) {
                // Refresh the searchable select with new data
                setTimeout(() => {
                    setupConferenceSearchableSelect();
                }, 50);
            }
        }
    } catch (error) {
        console.error('Error loading conferences:', error);
    }
}

// Load participants for single link generation
async function loadParticipants(conferenceId = null) {
    try {
        let url = '{{ route("passwordless-login.participants") }}';
        if (conferenceId) {
            url = `{{ route("passwordless-login.participants") }}?conference_id=${conferenceId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('userSelect');
            select.innerHTML = '<option value="">Select a participant...</option>';
            data.data.forEach(participant => {
                const option = document.createElement('option');
                option.value = participant.id;
                option.textContent = `${participant.first_name} ${participant.last_name} (${participant.email}) - ${participant.participant_types}`;
                select.appendChild(option);
            });
            
            // Store participants for search functionality
            window.allParticipants = data.data;
            
            // Update searchable select if it exists
            if (window.userSelect) {
                // Refresh the searchable select with new data
                setTimeout(() => {
                    setupParticipantSearchableSelect();
                }, 50);
            }
        }
    } catch (error) {
        console.error('Error loading participants:', error);
    }
}

// Store participants data globally for search functionality
let allParticipants = [];
let allConferences = [];

// Searchable select functionality for conferences
function setupConferenceSearchableSelect() {
    const select = document.getElementById('conferenceSelect');
    const originalOptions = Array.from(select.options);
    
    // Create a custom searchable select
    const searchableSelect = document.createElement('div');
    searchableSelect.className = 'relative';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search and select a conference...';
    searchInput.className = 'w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500';
    
    const dropdown = document.createElement('div');
    dropdown.className = 'absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden';
    
    const searchIcon = document.createElement('div');
    searchIcon.className = 'absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none';
    searchIcon.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
    
    // Replace the select with our custom component
    select.parentNode.replaceChild(searchableSelect, select);
    searchableSelect.appendChild(searchInput);
    searchableSelect.appendChild(searchIcon);
    searchableSelect.appendChild(dropdown);
    
    // Populate dropdown with options
    function populateDropdown(options) {
        dropdown.innerHTML = '';
        options.forEach(option => {
            if (option.value === '') return;
            
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
            item.textContent = option.textContent;
            item.dataset.value = option.value;
            
            item.addEventListener('click', function() {
                searchInput.value = this.textContent;
                dropdown.classList.add('hidden');
                select.value = this.dataset.value;
                select.dispatchEvent(new Event('change'));
            });
            
            dropdown.appendChild(item);
        });
    }
    
    // Show/hide dropdown
    searchInput.addEventListener('focus', function() {
        dropdown.classList.remove('hidden');
    });
    
    searchInput.addEventListener('blur', function() {
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredOptions = originalOptions.filter(option => 
            option.value !== '' && option.textContent.toLowerCase().includes(searchTerm)
        );
        populateDropdown(filteredOptions);
        dropdown.classList.remove('hidden');
    });
    
    // Store reference to select for form submission
    window.conferenceSelect = select;
}

// Searchable select functionality for participants
function setupParticipantSearchableSelect() {
    const select = document.getElementById('userSelect');
    const originalOptions = Array.from(select.options);
    
    // Create a custom searchable select
    const searchableSelect = document.createElement('div');
    searchableSelect.className = 'relative';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search and select a participant...';
    searchInput.className = 'w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500';
    
    const dropdown = document.createElement('div');
    dropdown.className = 'absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden';
    
    const searchIcon = document.createElement('div');
    searchIcon.className = 'absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none';
    searchIcon.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
    
    // Replace the select with our custom component
    select.parentNode.replaceChild(searchableSelect, select);
    searchableSelect.appendChild(searchInput);
    searchableSelect.appendChild(searchIcon);
    searchableSelect.appendChild(dropdown);
    
    // Populate dropdown with options
    function populateDropdown(options) {
        dropdown.innerHTML = '';
        options.forEach(option => {
            if (option.value === '') return;
            
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
            item.textContent = option.textContent;
            item.dataset.value = option.value;
            
            item.addEventListener('click', function() {
                searchInput.value = this.textContent;
                dropdown.classList.add('hidden');
                select.value = this.dataset.value;
                select.dispatchEvent(new Event('change'));
            });
            
            dropdown.appendChild(item);
        });
    }
    
    // Show/hide dropdown
    searchInput.addEventListener('focus', function() {
        dropdown.classList.remove('hidden');
    });
    
    searchInput.addEventListener('blur', function() {
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredOptions = originalOptions.filter(option => 
            option.value !== '' && option.textContent.toLowerCase().includes(searchTerm)
        );
        populateDropdown(filteredOptions);
        dropdown.classList.remove('hidden');
    });
    
    // Store reference to select for form submission
    window.userSelect = select;
}

// Searchable select functionality for bulk conference selection
function setupBulkConferenceSearchableSelect() {
    const select = document.getElementById('bulkConferenceSelect');
    const originalOptions = Array.from(select.options);
    
    // Create a custom searchable select
    const searchableSelect = document.createElement('div');
    searchableSelect.className = 'relative';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search and select a conference...';
    searchInput.className = 'w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500';
    
    const dropdown = document.createElement('div');
    dropdown.className = 'absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden';
    
    const searchIcon = document.createElement('div');
    searchIcon.className = 'absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none';
    searchIcon.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
    
    // Replace the select with our custom component
    select.parentNode.replaceChild(searchableSelect, select);
    searchableSelect.appendChild(searchInput);
    searchableSelect.appendChild(searchIcon);
    searchableSelect.appendChild(dropdown);
    
    // Populate dropdown with options
    function populateDropdown(options) {
        dropdown.innerHTML = '';
        options.forEach(option => {
            if (option.value === '') return;
            
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';
            item.textContent = option.textContent;
            item.dataset.value = option.value;
            
            item.addEventListener('click', function() {
                searchInput.value = this.textContent;
                dropdown.classList.add('hidden');
                select.value = this.dataset.value;
                select.dispatchEvent(new Event('change'));
            });
            
            dropdown.appendChild(item);
        });
    }
    
    // Show/hide dropdown
    searchInput.addEventListener('focus', function() {
        dropdown.classList.remove('hidden');
    });
    
    searchInput.addEventListener('blur', function() {
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredOptions = originalOptions.filter(option => 
            option.value !== '' && option.textContent.toLowerCase().includes(searchTerm)
        );
        populateDropdown(filteredOptions);
        dropdown.classList.remove('hidden');
    });
    
    // Store reference to select for form submission
    window.bulkConferenceSelect = select;
}

// Load participants for bulk generation
async function loadBulkParticipants(conferenceId = null) {
    try {
        let url = '{{ route("passwordless-login.participants") }}';
        if (conferenceId) {
            url = `{{ route("passwordless-login.participants") }}?conference_id=${conferenceId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            allParticipants = data.data;
            renderBulkParticipants(allParticipants);
            setupSearch();
        }
    } catch (error) {
        console.error('Error loading participants:', error);
    }
}

// Render participants in bulk modal
function renderBulkParticipants(participants) {
    const container = document.getElementById('participantList');
    container.innerHTML = '';
    participants.forEach(participant => {
        const div = document.createElement('div');
        div.className = 'flex items-center mb-2 participant-item';
        div.innerHTML = `
            <input type="checkbox" id="participant_${participant.id}" value="${participant.id}" 
                   class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="participant_${participant.id}" class="text-sm text-gray-700 flex-1">
                <div class="font-medium">${participant.first_name} ${participant.last_name}</div>
                <div class="text-xs text-gray-500">${participant.email}</div>
                <div class="text-xs text-blue-600">${participant.participant_types}</div>
            </label>
        `;
        container.appendChild(div);
    });
}

// Setup search functionality
function setupSearch() {
    const searchInput = document.getElementById('participantSearch');
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredParticipants = allParticipants.filter(participant => 
            participant.first_name.toLowerCase().includes(searchTerm) ||
            participant.last_name.toLowerCase().includes(searchTerm) ||
            participant.email.toLowerCase().includes(searchTerm) ||
            participant.participant_types.toLowerCase().includes(searchTerm)
        );
        renderBulkParticipants(filteredParticipants);
    });
}

// Modal functions
function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
    loadConferences();
    loadParticipants();
    // Setup searchable selects after a short delay to ensure DOM is ready
    setTimeout(() => {
        setupConferenceSearchableSelect();
        setupParticipantSearchableSelect();
    }, 100);
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function openBulkModal() {
    document.getElementById('bulkModal').classList.remove('hidden');
    loadConferences();
    loadBulkParticipants();
    // Setup searchable select after a short delay to ensure DOM is ready
    setTimeout(() => {
        setupBulkConferenceSearchableSelect();
    }, 100);
}

function closeBulkModal() {
    document.getElementById('bulkModal').classList.add('hidden');
}

// Form submissions
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('user_id', window.userSelect ? window.userSelect.value : document.getElementById('userSelect').value);
    formData.append('conference_id', window.conferenceSelect ? window.conferenceSelect.value : document.getElementById('conferenceSelect').value);
    formData.append('expiration_days', document.getElementById('expirationDays').value);
    
    try {
        const response = await fetch('{{ route("passwordless-login.generate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Login link generated successfully!');
            closeGenerateModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while generating the link.');
    }
});

document.getElementById('bulkForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const selectedParticipants = Array.from(document.querySelectorAll('#participantList input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedParticipants.length === 0) {
        alert('Please select at least one participant.');
        return;
    }
    
    const formData = new FormData();
    formData.append('user_ids', JSON.stringify(selectedParticipants));
    formData.append('conference_id', window.bulkConferenceSelect ? window.bulkConferenceSelect.value : document.getElementById('bulkConferenceSelect').value);
    formData.append('expiration_days', document.getElementById('bulkExpirationDays').value);
    
    try {
        const response = await fetch('{{ route("passwordless-login.generate.bulk") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeBulkModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while generating the links.');
    }
});

// Cleanup expired tokens
async function cleanupExpired() {
    if (!confirm('Are you sure you want to cleanup expired tokens?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("passwordless-login.cleanup") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while cleaning up expired tokens.');
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link to clipboard.');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadParticipantTypes();
    loadParticipants();
    
    // Add event listener for conference selection
    document.addEventListener('change', function(e) {
        if (e.target.id === 'conferenceSelect' || e.target === window.conferenceSelect) {
            const conferenceId = e.target.value;
            loadParticipants(conferenceId);
        }
        if (e.target.id === 'bulkConferenceSelect' || e.target === window.bulkConferenceSelect) {
            const conferenceId = e.target.value;
            loadBulkParticipants(conferenceId);
        }
    });
});
</script>
@endpush
