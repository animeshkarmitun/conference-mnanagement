@extends('layouts.app')

@section('title', 'Venue Details')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">{{ $venue->name }}</h2>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Address:</span>
        <span>{{ $venue->address }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Capacity:</span>
        <span>{{ $venue->capacity }}</span>
    </div>
    <div class="flex justify-end mt-6 space-x-2">
        <a href="{{ route('venues.edit', $venue) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Edit</a>
        <form method="POST" action="{{ route('venues.destroy', $venue) }}" onsubmit="return confirm('Are you sure you want to delete this venue?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
        </form>
    </div>
    <div class="mt-4">
        <a href="{{ route('venues.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>
@endsection 