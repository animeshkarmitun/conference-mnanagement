@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Create Task</h2>
    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @error('due_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select name="priority" id="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference</label>
                <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Select Conference</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                    @endforeach
                </select>
                @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign To (Multiple Selection)</label>
            
            <!-- Selected Users Box -->
            <div id="selected-users-box" class="mb-3 p-3 border border-gray-300 rounded-md bg-gray-50 min-h-[60px]">
                <div id="selected-users-list" class="flex flex-wrap gap-2">
                    <!-- Selected users will be displayed here -->
                </div>
                <div id="no-users-selected" class="text-gray-500 text-sm italic">
                    No users selected. Click on users below to assign them.
                </div>
            </div>

            <!-- Available Users List -->
            <div class="border border-gray-300 rounded-md max-h-48 overflow-y-auto">
                <div class="p-2 bg-gray-100 border-b border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Available Users (Click to select)</span>
                </div>
                <div id="available-users-list" class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <div class="user-item p-3 hover:bg-blue-50 cursor-pointer transition-colors" 
                             data-user-id="{{ $user->id }}" 
                             data-user-name="{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}" 
                             data-user-email="{{ $user->email }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</span>
                                    <span class="text-sm text-gray-500 ml-2">({{ $user->email }})</span>
                                </div>
                                <div class="text-blue-600 text-sm font-medium">
                                    Click to select
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Hidden input to store selected user IDs -->
            <input type="hidden" name="assigned_to" id="assigned_to_input" value="{{ old('assigned_to') ? implode(',', old('assigned_to')) : '' }}">
            
            @error('assigned_to')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            @error('assigned_to.*')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('tasks.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Create Task</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedUsers = new Set();
    const selectedUsersList = document.getElementById('selected-users-list');
    const noUsersSelected = document.getElementById('no-users-selected');
    const assignedToInput = document.getElementById('assigned_to_input');
    const userItems = document.querySelectorAll('.user-item');

    // Initialize with old values if they exist
    const oldValues = assignedToInput.value;
    if (oldValues) {
        const userIds = oldValues.split(',');
        userIds.forEach(userId => {
            if (userId.trim()) {
                const userItem = document.querySelector(`[data-user-id="${userId.trim()}"]`);
                if (userItem) {
                    selectUser(userItem);
                }
            }
        });
    }

    // Add click event listeners to user items
    userItems.forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            
            if (selectedUsers.has(userId)) {
                // User is already selected, remove them
                removeUser(userId);
            } else {
                // User is not selected, add them
                selectUser(this);
            }
        });
    });

    function selectUser(userItem) {
        const userId = userItem.getAttribute('data-user-id');
        const userName = userItem.getAttribute('data-user-name');
        const userEmail = userItem.getAttribute('data-user-email');

        if (selectedUsers.has(userId)) return; // Already selected

        selectedUsers.add(userId);
        
        // Add visual feedback to the user item
        userItem.classList.add('bg-blue-100', 'border-blue-300');
        userItem.classList.remove('hover:bg-blue-50');
        
        // Update the "Click to select" text
        const clickText = userItem.querySelector('.text-blue-600');
        if (clickText) {
            clickText.textContent = 'Selected';
            clickText.classList.add('text-green-600');
            clickText.classList.remove('text-blue-600');
        }

        // Create selected user chip
        const userChip = document.createElement('div');
        userChip.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 border border-blue-200';
        userChip.innerHTML = `
            <span class="mr-2">${userName} (${userEmail})</span>
            <button type="button" class="ml-1 text-blue-600 hover:text-blue-800 font-bold" onclick="removeUser('${userId}')">
                ×
            </button>
        `;
        userChip.setAttribute('data-user-id', userId);

        selectedUsersList.appendChild(userChip);
        
        // Hide "no users selected" message
        noUsersSelected.style.display = 'none';
        
        // Update hidden input
        updateHiddenInput();
    }

    function removeUser(userId) {
        selectedUsers.delete(userId);
        
        // Remove visual feedback from the user item
        const userItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (userItem) {
            userItem.classList.remove('bg-blue-100', 'border-blue-300');
            userItem.classList.add('hover:bg-blue-50');
            
            // Update the "Selected" text back to "Click to select"
            const clickText = userItem.querySelector('.text-green-600, .text-blue-600');
            if (clickText) {
                clickText.textContent = 'Click to select';
                clickText.classList.add('text-blue-600');
                clickText.classList.remove('text-green-600');
            }
        }

        // Remove user chip
        const userChip = document.querySelector(`[data-user-id="${userId}"]`);
        if (userChip) {
            userChip.remove();
        }

        // Show "no users selected" message if no users are selected
        if (selectedUsers.size === 0) {
            noUsersSelected.style.display = 'block';
        }
        
        // Update hidden input
        updateHiddenInput();
    }

    function updateHiddenInput() {
        const userIds = Array.from(selectedUsers);
        assignedToInput.value = userIds.join(',');
    }

    // Make removeUser function globally available for the remove buttons
    window.removeUser = removeUser;
});
</script>
@endsection 