@extends('layouts.participant')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Participant Profiles</h1>
                    <p class="mt-2 text-gray-600">Manage your participant profiles across different conferences</p>
                </div>
                
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                    <!-- Bulk Actions (Admin Only) -->
                    <div class="flex items-center space-x-4" id="bulk-actions" style="display: none;">
                        <span class="text-sm text-gray-600">
                            <span id="selected-count">0</span> selected
                        </span>
                        <div class="flex space-x-2">
                            <button type="button" 
                                    id="bulk-archive-btn"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l6 6m0 0l6-6m-6 6V4"></path>
                                </svg>
                                Bulk Archive
                            </button>
                            <button type="button" 
                                    id="bulk-restore-btn"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Bulk Restore
                            </button>
                            <button type="button" 
                                    id="bulk-delete-btn"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Bulk Delete
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <!-- Bulk Selection Controls -->
                <div class="mt-4 flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Select All</span>
                    </label>
                    <button type="button" id="deselect-all" class="text-sm text-gray-500 hover:text-gray-700">Deselect All</button>
                </div>
            @endif
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Profile Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($participants as $participant)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                    <!-- Profile Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                                    <!-- Bulk Selection Checkbox -->
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" 
                                               class="participant-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                               value="{{ $participant->id }}"
                                               @if($participant->is_primary) disabled title="Cannot delete primary profile" @endif>
                                        <span class="ml-2 text-xs text-gray-500">
                                            @if($participant->is_primary) (Primary) @endif
                                        </span>
                                    </div>
                                @endif
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $participant->getProfileDisplayName() }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $participant->conference->name }}
                                </p>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($participant->profile_type === 'personal') bg-blue-100 text-blue-800
                                        @elseif($participant->profile_type === 'professional') bg-green-100 text-green-800
                                        @elseif($participant->profile_type === 'academic') bg-purple-100 text-purple-800
                                        @elseif($participant->profile_type === 'media') bg-orange-100 text-orange-800
                                        @elseif($participant->profile_type === 'speaker') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($participant->profile_type) }}
                                    </span>
                                    @if($participant->is_primary)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Primary
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center space-x-2">
                                    @if($participant->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($participant->status === 'archived')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Archived
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Conference Dates</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($participant->conference->start_date)->format('M d, Y') }} - 
                                    {{ \Carbon\Carbon::parse($participant->conference->end_date)->format('M d, Y') }}
                                </dd>
                            </div>
                            
                            @if($participant->profile_description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ Str::limit($participant->profile_description, 100) }}</dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registration Status</dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($participant->registration_status === 'approved') bg-green-100 text-green-800
                                        @elseif($participant->registration_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($participant->registration_status === 'rejected') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($participant->registration_status) }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                @if($participant->status === 'active')
                                    <form method="POST" action="{{ route('participant-profiles.switch', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            Switch To
                                        </button>
                                    </form>

                                    @if(!$participant->is_primary)
                                        <form method="POST" action="{{ route('participant-profiles.set-primary', $participant->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Set Primary
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('participant-profiles.archive', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                                onclick="return confirm('Are you sure you want to archive this profile?')">
                                            Archive
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('participant-profiles.restore', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Restore
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if(!$participant->is_primary)
                                <form method="POST" action="{{ route('participant-profiles.destroy', $participant->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            onclick="return confirm('Are you sure you want to permanently delete this profile? This action cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No participant profiles</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new participant profile.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
@if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
<div id="bulk-delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900">Confirm Bulk Delete</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete <span id="delete-count">0</span> participant profile(s)?
                    </p>
                    <div class="mt-3">
                        <div id="participants-to-delete" class="text-sm text-gray-700 max-h-32 overflow-y-auto"></div>
                    </div>
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="text-sm text-yellow-800">
                            <strong>Warning:</strong> This action cannot be undone. Users with no remaining participant profiles will also be deleted along with all their data.
                        </p>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirm-bulk-delete" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    Delete Selected
                </button>
                <button id="cancel-bulk-delete" 
                        class="mt-3 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Forms (Hidden) -->
<form id="bulk-delete-form" method="POST" action="{{ route('participant-profiles.bulk-delete') }}" style="display: none;">
    @csrf
    <div id="bulk-delete-inputs"></div>
</form>

<form id="bulk-archive-form" method="POST" action="{{ route('participant-profiles.bulk-archive') }}" style="display: none;">
    @csrf
    <div id="bulk-archive-inputs"></div>
</form>

<form id="bulk-restore-form" method="POST" action="{{ route('participant-profiles.bulk-restore') }}" style="display: none;">
    @csrf
    <div id="bulk-restore-inputs"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const deselectAllBtn = document.getElementById('deselect-all');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkArchiveBtn = document.getElementById('bulk-archive-btn');
    const bulkRestoreBtn = document.getElementById('bulk-restore-btn');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const bulkDeleteModal = document.getElementById('bulk-delete-modal');
    const confirmBulkDeleteBtn = document.getElementById('confirm-bulk-delete');
    const cancelBulkDeleteBtn = document.getElementById('cancel-bulk-delete');
    const deleteCount = document.getElementById('delete-count');
    const participantsToDelete = document.getElementById('participants-to-delete');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');
    const bulkArchiveForm = document.getElementById('bulk-archive-form');
    const bulkArchiveInputs = document.getElementById('bulk-archive-inputs');
    const bulkRestoreForm = document.getElementById('bulk-restore-form');
    const bulkRestoreInputs = document.getElementById('bulk-restore-inputs');

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        deleteCount.textContent = count;
        
        if (count > 0) {
            bulkActions.style.display = 'flex';
            bulkArchiveBtn.disabled = false;
            bulkRestoreBtn.disabled = false;
            bulkDeleteBtn.disabled = false;
        } else {
            bulkActions.style.display = 'none';
            bulkArchiveBtn.disabled = true;
            bulkRestoreBtn.disabled = true;
            bulkDeleteBtn.disabled = true;
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            participantCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = this.checked;
                }
            });
            updateBulkActions();
        });
    }

    // Deselect all functionality
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            participantCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
            updateBulkActions();
        });
    }

    // Individual checkbox change
    participantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            
            // Update select all checkbox state
            if (selectAllCheckbox) {
                const totalCheckboxes = document.querySelectorAll('.participant-checkbox:not([disabled])').length;
                const checkedCheckboxes = document.querySelectorAll('.participant-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCheckboxes === totalCheckboxes;
                selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
            }
        });
    });

    // Bulk archive button click
    if (bulkArchiveBtn) {
        bulkArchiveBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one participant profile to archive.');
                return;
            }

            if (confirm(`Are you sure you want to archive ${checkedBoxes.length} participant profile(s)?`)) {
                // Clear previous inputs
                bulkArchiveInputs.innerHTML = '';
                
                // Add hidden inputs for selected participant IDs
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'participant_ids[]';
                    input.value = checkbox.value;
                    bulkArchiveInputs.appendChild(input);
                });

                // Submit form
                bulkArchiveForm.submit();
            }
        });
    }

    // Bulk restore button click
    if (bulkRestoreBtn) {
        bulkRestoreBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one participant profile to restore.');
                return;
            }

            if (confirm(`Are you sure you want to restore ${checkedBoxes.length} participant profile(s)?`)) {
                // Clear previous inputs
                bulkRestoreInputs.innerHTML = '';
                
                // Add hidden inputs for selected participant IDs
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'participant_ids[]';
                    input.value = checkbox.value;
                    bulkRestoreInputs.appendChild(input);
                });

                // Submit form
                bulkRestoreForm.submit();
            }
        });
    }

    // Bulk delete button click
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one participant profile to delete.');
                return;
            }

            // Populate modal with selected participants
            const participantNames = Array.from(checkedBoxes).map(checkbox => {
                const card = checkbox.closest('.bg-white');
                const nameElement = card.querySelector('h3');
                return nameElement ? nameElement.textContent.trim() : 'Unknown';
            });

            participantsToDelete.innerHTML = participantNames.map(name => 
                `<div class="flex items-center py-1">
                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    ${name}
                </div>`
            ).join('');

            bulkDeleteModal.classList.remove('hidden');
        });
    }

    // Confirm bulk delete
    if (confirmBulkDeleteBtn) {
        confirmBulkDeleteBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
            
            // Clear previous inputs
            bulkDeleteInputs.innerHTML = '';
            
            // Add hidden inputs for selected participant IDs
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'participant_ids[]';
                input.value = checkbox.value;
                bulkDeleteInputs.appendChild(input);
            });

            // Submit form
            bulkDeleteForm.submit();
        });
    }

    // Cancel bulk delete
    if (cancelBulkDeleteBtn) {
        cancelBulkDeleteBtn.addEventListener('click', function() {
            bulkDeleteModal.classList.add('hidden');
        });
    }

    // Close modal when clicking outside
    bulkDeleteModal.addEventListener('click', function(e) {
        if (e.target === bulkDeleteModal) {
            bulkDeleteModal.classList.add('hidden');
        }
    });

    // Initialize bulk actions state
    updateBulkActions();
});
</script>
@endif
@endsection










