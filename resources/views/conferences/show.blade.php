@extends('layouts.app')

@section('title', 'Conference Details')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">{{ $conference->title }}</h2>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Start Date:</span>
        <span>{{ $conference->start_date }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">End Date:</span>
        <span>{{ $conference->end_date }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Venue:</span>
        <span>{{ $conference->venue->name ?? '' }}</span>
    </div>
    <div class="flex justify-end mt-6">
        <a href="{{ route('conferences.edit', $conference) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold mr-2">Edit</a>
        <form method="POST" action="{{ route('conferences.destroy', $conference) }}" onsubmit="return confirm('Are you sure you want to delete this conference?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
        </form>
    </div>
    <div class="mt-4">
        <a href="{{ route('conferences.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>
@endsection 