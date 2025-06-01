@extends('layouts.app')

@section('title', 'Sessions')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Sessions</h2>
    <a href="{{ route('sessions.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Session</a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conference</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sessions as $session)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $session->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $session->conference->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $session->start_time->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $session->end_time->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $session->room }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $session->capacity }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('sessions.show', $session) }}" class="text-yellow-600 hover:underline">View</a>
                        <a href="{{ route('sessions.edit', $session) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Delete this session?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No sessions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
</div>
@endsection 