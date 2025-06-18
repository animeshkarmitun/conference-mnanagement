@extends('layouts.app')

@section('title', 'Add Conference')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-8">
    <h2 class="text-2xl font-bold mb-6">Add Conference</h2>
    <form method="POST" action="{{ route('conferences.store') }}" class="space-y-6">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('start_date')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('end_date')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
            <input type="text" id="location" name="location" value="{{ old('location') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('location')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="venue_id" class="block text-sm font-medium text-gray-700">Venue</label>
            <select id="venue_id" name="venue_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Venue</option>
                @foreach($venues as $venue)
                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                @endforeach
            </select>
            @error('venue_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Create Conference</button>
        </div>
    </form>
</div>
@endsection 