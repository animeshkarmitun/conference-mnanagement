@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Notifications</h2>
</div>
<div class="bg-white rounded-xl shadow p-6">
    <ul class="divide-y divide-gray-200">
        @forelse($notifications ?? [] as $notification)
            <li class="py-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-500">{{ $notification->created_at }}</p>
                </div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $notification->read_status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $notification->read_status ? 'Read' : 'Unread' }}
                </span>
            </li>
        @empty
            <li class="py-4 text-center text-gray-500">No notifications found.</li>
        @endforelse
    </ul>
</div>
@endsection 