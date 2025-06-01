@extends('layouts.app')

@section('title', 'Participants')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Participants</h2>
    <a href="{{ route('participants.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Participant</a>
</div>
<div class="bg-white rounded-xl shadow p-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($participants ?? [] as $participant)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('participants.show', $participant) }}" class="text-blue-700 hover:underline font-semibold">
                            {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->participantType->name ?? '' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->organization }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $participant->registration_status == 'approved' ? 'bg-green-100 text-green-700' : ($participant->registration_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($participant->registration_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('participants.show', $participant) }}" class="text-yellow-600 hover:underline">View</a>
                        <a href="{{ route('participants.edit', $participant) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline" onsubmit="return confirm('Delete this participant?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No participants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 