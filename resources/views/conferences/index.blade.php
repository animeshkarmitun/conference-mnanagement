@extends('layouts.app')

@section('title', 'Conferences')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Conferences</h2>
    <a href="{{ route('conferences.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Conference</a>
</div>
<div class="bg-white rounded-xl shadow p-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($conferences ?? [] as $conference)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $conference->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $conference->start_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $conference->end_date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $conference->venue->name ?? '' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('conferences.show', $conference) }}" class="text-yellow-600 hover:underline">View</a>
                        <a href="{{ route('conferences.edit', $conference) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('conferences.destroy', $conference) }}" method="POST" class="inline" onsubmit="return confirm('Delete this conference?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No conferences found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 