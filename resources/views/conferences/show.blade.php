@extends('layouts.app')

@section('title', 'Conference Details')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">{{ $conference->name }}</h2>
        <div class="mb-2"><span class="font-semibold text-gray-700">Start Date:</span> {{ $conference->start_date }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">End Date:</span> {{ $conference->end_date }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Status:</span> {{ ucfirst($conference->status) }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Location:</span> {{ $conference->location }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Venue:</span> {{ $conference->venue->name ?? '-' }}<br><span class="text-sm text-gray-500">{{ $conference->venue->address ?? '' }}</span></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Sessions -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Sessions</h3>
            @if($conference->sessions->count())
                <ul class="divide-y divide-gray-200">
                    @foreach($conference->sessions as $session)
                        <li class="py-2">
                            <span class="font-semibold">{{ $session->title }}</span><br>
                            <span class="text-sm text-gray-500">{{ $session->start_time }} - {{ $session->end_time }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No sessions found.</p>
            @endif
        </div>
        <!-- Speakers -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Speakers</h3>
            @php
                $speakers = $conference->participants->filter(fn($p) => $p->roles->contains('name', 'Speaker'));
            @endphp
            @if($speakers->count())
                <ul class="divide-y divide-gray-200">
                    @foreach($speakers as $speaker)
                        <li class="py-2">
                            <span class="font-semibold">{{ $speaker->user->first_name ?? '' }} {{ $speaker->user->last_name ?? '' }}</span>
                            <span class="text-sm text-gray-500"> ({{ $speaker->user->email ?? '' }})</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No speakers found.</p>
            @endif
        </div>
        <!-- Participants -->
        <div class="bg-white rounded-xl shadow p-6 md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Participants</h3>
            @if($conference->participants->count())
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Serial No.</th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Gender</th>
                            <th class="px-4 py-2 text-left">Nationality</th>
                            <th class="px-4 py-2 text-left">Profession</th>
                            <th class="px-4 py-2 text-left">Age</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-left">Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conference->participants as $i => $participant)
                            @php
                                $user = $participant->user;
                                $serial = $participant->serial_number ?? (sprintf('CONF%04d-%03d', $conference->id, $i+1));
                                $dob = $user->date_of_birth ?? null;
                                $age = $dob ? \Carbon\Carbon::parse($dob)->age : '';
                                $category = $participant->category ?? ($participant->participantType->name === 'Delegate' ? 'Delegate' : '');
                            @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $serial }}</td>
                                <td class="px-4 py-2">{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</td>
                                <td class="px-4 py-2">{{ $user->email ?? '' }}</td>
                                <td class="px-4 py-2">{{ $user->gender ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $user->nationality ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $user->profession ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $age }}</td>
                                <td class="px-4 py-2">{{ $participant->participantType->name ?? '' }}</td>
                                <td class="px-4 py-2">{{ $category }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No participants found.</p>
            @endif
        </div>
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