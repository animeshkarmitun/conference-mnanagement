@extends('layouts.app')

@section('title', 'Add Venue')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Add Venue</h2>
    <form method="POST" action="{{ route('venues.store') }}">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
            <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            @error('capacity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('venues.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Venue</button>
        </div>
    </form>
</div>
@endsection 