@extends('layouts.app')

@section('title', 'Participants')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Participants</h1>
        <div class="text-gray-600 text-lg font-medium">Manage and view all event participants</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Participants</h2>
    <a href="{{ route('participants.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Participant</a>
</div>
<div class="bg-white rounded-xl shadow p-6 mt-8">
    <table class="min-w-full divide-y divide-gray-200" id="myTable">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial No.</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profession</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($participants ?? [] as $i => $participant)
                @php
                    $user = $participant->user;
                    $serial = $participant->serial_number ?? (sprintf('CONF%04d-%03d', $participant->conference_id ?? 0, $i+1));
                    $dob = $user->date_of_birth ?? null;
                    $age = $dob ? \Carbon\Carbon::parse($dob)->age : '';
                    $category = $participant->category ?? ($participant->participantType->name === 'Delegate' ? 'Delegate' : '');
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $serial }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('participants.show', $participant) }}" class="text-blue-700 hover:underline font-semibold">
                            {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->gender ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->nationality ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->profession ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $age }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $participant->participantType->name ?? '' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $category }}</td>
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
                    <td colspan="12" class="px-6 py-4 text-center text-gray-500">No participants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 