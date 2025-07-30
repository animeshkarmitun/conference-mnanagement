@extends('layouts.app')

@section('title', 'Participants')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center justify-between px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center">
        <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
            <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Participants</h1>
            <div class="text-gray-600 text-lg font-medium">Manage and view all event participants</div>
        </div>
    </div>
    <div>
        <a href="{{ route('participants.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-bold text-xl shadow-xl border-2 border-red-800 transition duration-200 transform hover:scale-105 inline-block">
            <svg class="w-6 h-6 inline mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            ADD PARTICIPANT
        </a>
    </div>
</div>

<hr class="mb-8 border-yellow-200">
<<<<<<< HEAD

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
        <h2 class="text-2xl font-bold text-gray-800">All Participants</h2>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">{{ $participants->total() ?? 0 }} participants</div>
            <div class="flex items-center space-x-2">
                <select id="format-select" class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="pdf">PDF</option>
                    <option value="zip">ZIP</option>
                </select>
                <button id="download-biographies-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow-lg transition duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Biographies (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>
    </div>
    
    <div class="mb-4">
        <label class="flex items-center">
            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
        </label>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200">
=======
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Participants</h2>
    <a href="{{ route('participants.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Participant</a>
</div>
<div class="bg-white rounded-xl shadow p-6 mt-8">
    <table class="min-w-full divide-y divide-gray-200" id="myTable">
>>>>>>> ddfd3d6f748e51d42b503b4114f72d3e6244ac8e
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" id="select-all-header" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial No.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profession</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
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
                <tr>
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
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->gender ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->nationality ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->profession ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $age }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->participantType->name ?? '' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $category }}</td>
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
                        <a href="{{ route('participants.show', $participant) }}" class="text-yellow-600 hover:underline">View</a>
                        <a href="{{ route('participants.edit', $participant) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline" onsubmit="return confirm('Delete this participant?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="px-6 py-4 text-center text-gray-500">No participants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

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
        } else {
            downloadBtn.disabled = true;
            downloadBtn.classList.add('opacity-50', 'cursor-not-allowed');
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
        
        if (selectedIds.length === 0) {
            alert('Please select at least one participant to download biographies.');
            return;
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
    
    // Initialize state
    updateSelectionState();
});
</script>
@endsection 