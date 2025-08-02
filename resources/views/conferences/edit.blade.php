@extends('layouts.app')

@section('title', 'Edit Conference')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-8">
    <h2 class="text-2xl font-bold mb-6">Edit Conference</h2>
    <form method="POST" action="{{ route('conferences.update', $conference) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Conference Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b border-gray-200 pb-2">Conference Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $conference->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location *</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $conference->location) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., New York, NY">
                    @error('location')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $conference->start_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $conference->end_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Venue Selection/Creation -->
        <div class="bg-blue-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-800 border-b border-blue-200 pb-2">Venue Information</h3>
            
            <!-- Venue Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Venue Selection *</label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="venue_type" value="existing" id="venue_existing" class="form-radio text-yellow-600" {{ old('venue_type', 'existing') == 'existing' ? 'checked' : '' }}>
                        <span class="ml-2">Select Existing Venue</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="venue_type" value="new" id="venue_new" class="form-radio text-yellow-600" {{ old('venue_type') == 'new' ? 'checked' : '' }}>
                        <span class="ml-2">Create New Venue</span>
                    </label>
                </div>
                @error('venue_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Existing Venue Selection -->
            <div id="existing_venue_section" class="venue-section">
                <div>
                    <label for="venue_id" class="block text-sm font-medium text-gray-700">Select Venue *</label>
                    <select id="venue_id" name="venue_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <option value="">Select Venue</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}" {{ old('venue_id', $conference->venue_id) == $venue->id ? 'selected' : '' }}>{{ $venue->name }} - {{ $venue->address }}</option>
                        @endforeach
                    </select>
                    @error('venue_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- New Venue Creation -->
            <div id="new_venue_section" class="venue-section" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                        <input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., Convention Center">
                        @error('venue_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="venue_capacity" class="block text-sm font-medium text-gray-700">Capacity *</label>
                        <input type="number" id="venue_capacity" name="venue_capacity" value="{{ old('venue_capacity') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="e.g., 500">
                        @error('venue_capacity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="venue_address" class="block text-sm font-medium text-gray-700">Address *</label>
                    <textarea id="venue_address" name="venue_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Full address of the venue">{{ old('venue_address') }}</textarea>
                    @error('venue_address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('conferences.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold text-lg">Update Conference</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const venueExisting = document.getElementById('venue_existing');
    const venueNew = document.getElementById('venue_new');
    const existingSection = document.getElementById('existing_venue_section');
    const newSection = document.getElementById('new_venue_section');
    const venueIdSelect = document.getElementById('venue_id');
    const venueNameInput = document.getElementById('venue_name');
    const venueCapacityInput = document.getElementById('venue_capacity');
    const venueAddressInput = document.getElementById('venue_address');

    function toggleVenueSections() {
        if (venueExisting.checked) {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
            // Clear new venue fields
            venueNameInput.value = '';
            venueCapacityInput.value = '';
            venueAddressInput.value = '';
            // Make existing venue required
            venueIdSelect.required = true;
            venueNameInput.required = false;
            venueCapacityInput.required = false;
            venueAddressInput.required = false;
        } else if (venueNew.checked) {
            existingSection.style.display = 'none';
            newSection.style.display = 'block';
            // Clear existing venue selection
            venueIdSelect.value = '';
            // Make new venue fields required
            venueIdSelect.required = false;
            venueNameInput.required = true;
            venueCapacityInput.required = true;
            venueAddressInput.required = true;
        }
    }

    // Initial state
    toggleVenueSections();

    // Listen for changes
    venueExisting.addEventListener('change', toggleVenueSections);
    venueNew.addEventListener('change', toggleVenueSections);
});
</script>
@endsection 