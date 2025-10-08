@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Create Task</h2>
    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
            <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @error('due_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
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
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference <span class="text-red-500">*</span></label>
                <select name="conference_id" id="conference_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Select Conference</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>{{ $conference->name }}</option>
                    @endforeach
                </select>
                @error('conference_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

<<<<<<< Updated upstream
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                <select name="assigned_to" id="assigned_to" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="">Select User</option>
=======
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign To (Multiple Selection) <span class="text-red-500">*</span></label>
            
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
>>>>>>> Stashed changes
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('assigned_to')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('tasks.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Create Task</button>
        </div>
    </form>
</div>
@endsection 