@extends('layouts.app')

@section('title', 'Participants')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Participants</h2>
    <a href="{{ route('participants.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Participant</a>
</div>

<!-- Participant Status Tabs -->
<div class="bg-white rounded-xl shadow mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('participants.index', ['status' => 'approved']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'approved' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Approved Participants
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['approved'] }}</span>
            </a>
            
            <a href="{{ route('participants.index', ['status' => 'pending']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Pending Participants
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['pending'] }}</span>
            </a>
            
            <a href="{{ route('participants.index', ['status' => 'rejected']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'rejected' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Rejected Participants
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['rejected'] }}</span>
            </a>
            
            <a href="{{ route('participants.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'all' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                All Participants
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $counts['all'] }}</span>
            </a>
        </nav>
    </div>
</div>

<!-- Search and Secondary Filters -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <!-- Search Bar -->
        <div class="flex-1 max-w-md">
            <form method="GET" action="{{ route('participants.index') }}" class="flex">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search participants by name, email, organization, or serial number..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <button type="submit" class="ml-2 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('participants.index', ['status' => $status]) }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold">
                        Clear
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Secondary Filter Tabs -->
        <div class="flex flex-wrap gap-2">
            <!-- Visa Status Filter -->
            <div class="relative group">
                <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-blue-200">
                    Visa Status
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                    <div class="py-2">
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'required'])) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Required ({{ $visaCounts['required'] }})
                        </a>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'approved'])) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Approved ({{ $visaCounts['approved'] }})
                        </a>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'pending'])) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Pending ({{ $visaCounts['pending'] }})
                        </a>
                        <a href="{{ route('participants.index', array_merge(request()->query(), ['visa_filter' => 'issue'])) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Issues ({{ $visaCounts['issue'] }})
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Participant Type Filter -->
            <div class="relative group">
                <button class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-purple-200">
                    Participant Type
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                    <div class="py-2">
                        @foreach($participantTypes as $type)
                            <a href="{{ route('participants.index', array_merge(request()->query(), ['type' => $type->name])) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $type->name }} ({{ $typeCounts[$type->name] ?? 0 }})
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            @if($status === 'approved')
                Approved Participants
            @elseif($status === 'pending')
                Pending Participants
            @elseif($status === 'rejected')
                Rejected Participants
            @else
                All Participants
            @endif
        </h2>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">{{ $participants->total() ?? 0 }} participants</div>
            <div class="flex items-center space-x-2">
                <select id="format-select" class="rounded-md border-2 border-gray-400 bg-white text-gray-700 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium shadow-sm">
                    <option value="pdf">PDF</option>
                    <option value="zip">ZIP</option>
                </select>
                <button id="download-biographies-btn" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-lg font-semibold text-sm shadow-lg transition duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-blue-700">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Resumes (<span id="selected-count">0</span>)
                </button>
                <button id="export-csv-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow-lg transition duration-200 transform hover:scale-105 border-2 border-green-700">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>
    
    <div class="mb-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div class="flex items-center">
            <label class="flex items-center">
                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
            </label>
        </div>
        
        <!-- Bulk Actions -->
        <div class="flex flex-wrap gap-2" id="bulk-actions" style="display: none;">
            <select id="bulk-status" class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">Update Status</option>
                <option value="approved">Approve</option>
                <option value="pending">Mark Pending</option>
                <option value="rejected">Reject</option>
            </select>
            <button id="bulk-update-btn" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg font-semibold text-sm">
                Update Selected
            </button>
            <button id="bulk-email-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold text-sm">
                Send Email
            </button>
        </div>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" id="select-all-header" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial No.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visa Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($participants ?? [] as $i => $participant)
                @php
                    $user = $participant->user;
                    $serial = $participant->serial_number ?? (sprintf('CONF%04d-%03d', $participant->conference_id ?? 0, $i+1));
                    $dob = $user->date_of_birth ?? null;
                    $age = $dob ? \Carbon\Carbon::parse($dob)->age : '';
                    $category = $participant->category ?? ($participant->participantType->name === 'Delegate' ? 'Delegate' : '');
                @endphp
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="participant-checkbox" value="{{ $participant->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $serial }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('participants.show', $participant) }}" class="text-blue-700 hover:underline font-semibold">
                            {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->participantType->name ?? '' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->organization }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $participant->registration_status == 'approved' ? 'bg-green-100 text-green-700' : ($participant->registration_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($participant->registration_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                            {{ $participant->visa_status == 'approved' ? 'bg-green-100 text-green-700' : 
                               ($participant->visa_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                               ($participant->visa_status == 'issue' ? 'bg-red-100 text-red-700' : 
                               ($participant->visa_status == 'required' ? 'bg-blue-100 text-blue-700' : 
                               'bg-gray-100 text-gray-700'))) }}">
                            {{ ucfirst(str_replace('_', ' ', $participant->visa_status)) }}
                        </span>
                        @if($participant->visa_status == 'issue')
                            <div class="text-xs text-red-600 mt-1" title="{{ $participant->visa_issue_description }}">
                                ⚠️ Issue reported
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('participants.show', $participant) }}" 
                           class="inline-flex items-center p-2 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800 rounded-lg transition-colors duration-200 border border-blue-200"
                           title="View Participant Details"
                           aria-label="View participant details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        
                        <a href="{{ route('participants.edit', $participant) }}" 
                           class="inline-flex items-center p-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 rounded-lg transition-colors duration-200 border border-yellow-200"
                           title="Edit Participant"
                           aria-label="Edit participant">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this participant?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-colors duration-200 border border-red-200"
                                    title="Delete Participant"
                                    aria-label="Delete participant">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if($status === 'approved')
                            No approved participants found.
                        @elseif($status === 'pending')
                            No pending participants found.
                        @elseif($status === 'rejected')
                            No rejected participants found.
                        @else
                            No participants found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $participants->appends(['status' => $status])->links() }}
    </div>
</div>

<style>
.tab-link {
    transition: all 0.2s ease-in-out;
}

.tab-link:hover {
    transform: translateY(-1px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const selectAllHeaderCheckbox = document.getElementById('select-all-header');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    const downloadBtn = document.getElementById('download-biographies-btn');
    const selectedCountSpan = document.getElementById('selected-count');
    const formatSelect = document.getElementById('format-select');
    
    // Function to update selected count and button state
    function updateSelectionState() {
        const selectedCount = document.querySelectorAll('.participant-checkbox:checked').length;
        selectedCountSpan.textContent = selectedCount;
        
        if (selectedCount > 0) {
            downloadBtn.disabled = false;
            downloadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            document.getElementById('bulk-actions').style.display = 'flex';
        } else {
            downloadBtn.disabled = true;
            downloadBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('bulk-actions').style.display = 'none';
        }
    }
    
    // Function to handle select all
    function handleSelectAll(checked) {
        participantCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateSelectionState();
    }
    
    // Select all checkbox event listeners
    selectAllCheckbox.addEventListener('change', function() {
        handleSelectAll(this.checked);
        selectAllHeaderCheckbox.checked = this.checked;
    });
    
    selectAllHeaderCheckbox.addEventListener('change', function() {
        handleSelectAll(this.checked);
        selectAllCheckbox.checked = this.checked;
    });
    
    // Individual checkbox event listeners
    participantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionState();
            
            // Update select all checkboxes
            const allChecked = document.querySelectorAll('.participant-checkbox:checked').length === participantCheckboxes.length;
            const someChecked = document.querySelectorAll('.participant-checkbox:checked').length > 0;
            
            selectAllCheckbox.checked = allChecked;
            selectAllHeaderCheckbox.checked = allChecked;
        });
    });

    // Format select event listener
    formatSelect.addEventListener('change', function() {
        // Just update the format for the download, no need to change URL
        console.log('Format selected:', this.value);
    });
    
    // Download biographies button event listener
    downloadBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        // If no participants are selected, download all participants
        if (selectedIds.length === 0) {
            if (confirm('No participants selected. Download resumes for all participants?')) {
                selectedIds = []; // Empty array means download all
            } else {
                return;
            }
        }
        
        const selectedFormat = formatSelect.value;
        
        // Show loading state
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Generating ${selectedFormat.toUpperCase()}...
        `;
        downloadBtn.disabled = true;
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.participants.download-biographies") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add selected participant IDs
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'participant_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        // Add format preference
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = selectedFormat;
        form.appendChild(formatInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Reset button after a delay
        setTimeout(() => {
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;
        }, 3000);
    });
    
    // Bulk update functionality
    document.getElementById('bulk-update-btn').addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
            .map(checkbox => checkbox.value);
        const newStatus = document.getElementById('bulk-status').value;
        
        if (selectedIds.length === 0) {
            alert('Please select at least one participant.');
            return;
        }
        
        if (!newStatus) {
            alert('Please select a status to update.');
            return;
        }
        
        if (confirm(`Are you sure you want to update ${selectedIds.length} participant(s) to "${newStatus}" status?`)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("participants.bulk-update") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add selected participant IDs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'participant_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            // Add new status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = newStatus;
            form.appendChild(statusInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    });
    
    // CSV Export functionality
    document.getElementById('export-csv-btn').addEventListener('click', function() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    });
    
    // Bulk email functionality
    document.getElementById('bulk-email-btn').addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one participant.');
            return;
        }
        
        // Redirect to bulk email page with selected IDs
        const params = new URLSearchParams();
        selectedIds.forEach(id => params.append('participant_ids[]', id));
        window.location.href = '{{ route("bulk.email") }}?' + params.toString();
    });
    
    // Initialize state
    updateSelectionState();
});
</script>
@endsection 