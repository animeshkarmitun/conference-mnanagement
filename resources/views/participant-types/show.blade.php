@extends('layouts.app')

@section('title', 'View Participant Type')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">View Participant Type</h1>
        <div class="space-x-3">
            <a href="{{ route('participant-types.edit', $participantType) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('participant-types.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ ucwords(str_replace('_', ' ', $participantType->name)) }}</p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $categories[$participantType->category] ?? $participantType->category }}</p>
                    </div>

                    <!-- Display Order -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Display Order</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $participantType->display_order }}</p>
                    </div>

                    <!-- Status Flags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1 space-y-2">
                            @if($participantType->requires_approval)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Requires Approval
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    No Approval Required
                                </span>
                            @endif
                            
                            @if($participantType->has_special_privileges)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                    Special Privileges
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                    Standard Access
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($participantType->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $participantType->description }}</p>
                    </div>
                @endif

                <!-- Participants Count -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Participants Using This Type</label>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $participantType->participants->count() }} participants</p>
                    
                    @if($participantType->participants->count() > 0)
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 mb-2">Participants:</p>
                            <div class="max-h-40 overflow-y-auto">
                                <ul class="space-y-1">
                                    @foreach($participantType->participants->take(10) as $participant)
                                        <li class="text-sm text-gray-700">
                                            {{ $participant->user->name ?? 'Unknown' }} 
                                            <span class="text-gray-500">({{ $participant->user->email ?? 'No email' }})</span>
                                        </li>
                                    @endforeach
                                    @if($participantType->participants->count() > 10)
                                        <li class="text-sm text-gray-500 italic">
                                            ... and {{ $participantType->participants->count() - 10 }} more
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Timestamps -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <div>
                            <span class="font-medium">Created:</span> {{ $participantType->created_at->format('M d, Y \a\t g:i A') }}
                        </div>
                        <div>
                            <span class="font-medium">Last Updated:</span> {{ $participantType->updated_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
