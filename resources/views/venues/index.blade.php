@extends('layouts.app')

@section('title', 'Venues')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Venues</h2>
    <a href="{{ route('venues.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Venue</a>
</div>
<div class="bg-white rounded-xl shadow p-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($venues as $venue)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $venue->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $venue->address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $venue->capacity }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('venues.show', $venue) }}" class="text-yellow-600 hover:underline">View</a>
                        <a href="{{ route('venues.edit', $venue) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('venues.destroy', $venue) }}" method="POST" class="inline" onsubmit="return confirm('Delete this venue?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No venues found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $venues->links() }}
    </div>
</div>
@endsection 