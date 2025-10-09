@extends('layouts.participant')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Participant Profiles</h1>
                <p class="mt-2 text-gray-600">Manage your participant profiles across different conferences</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Profile Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($participants as $participant)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                    <!-- Profile Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $participant->getProfileDisplayName() }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $participant->conference->name }}
                                </p>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($participant->profile_type === 'personal') bg-blue-100 text-blue-800
                                        @elseif($participant->profile_type === 'professional') bg-green-100 text-green-800
                                        @elseif($participant->profile_type === 'academic') bg-purple-100 text-purple-800
                                        @elseif($participant->profile_type === 'media') bg-orange-100 text-orange-800
                                        @elseif($participant->profile_type === 'speaker') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($participant->profile_type) }}
                                    </span>
                                    @if($participant->is_primary)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Primary
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center space-x-2">
                                    @if($participant->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($participant->status === 'archived')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Archived
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Conference Dates</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($participant->conference->start_date)->format('M d, Y') }} - 
                                    {{ \Carbon\Carbon::parse($participant->conference->end_date)->format('M d, Y') }}
                                </dd>
                            </div>
                            
                            @if($participant->profile_description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ Str::limit($participant->profile_description, 100) }}</dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registration Status</dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($participant->registration_status === 'approved') bg-green-100 text-green-800
                                        @elseif($participant->registration_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($participant->registration_status === 'rejected') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($participant->registration_status) }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                @if($participant->status === 'active')
                                    <form method="POST" action="{{ route('participant-profiles.switch', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            Switch To
                                        </button>
                                    </form>

                                    @if(!$participant->is_primary)
                                        <form method="POST" action="{{ route('participant-profiles.set-primary', $participant->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Set Primary
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('participant-profiles.archive', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                                onclick="return confirm('Are you sure you want to archive this profile?')">
                                            Archive
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('participant-profiles.restore', $participant->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Restore
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if(!$participant->is_primary)
                                <form method="POST" action="{{ route('participant-profiles.destroy', $participant->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            onclick="return confirm('Are you sure you want to permanently delete this profile? This action cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No participant profiles</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new participant profile.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection



