@extends('layouts.app')

@section('title', 'Conferences')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Conferences</h2>
    <a href="{{ route('conferences.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Conference</a>
</div>

<!-- Conference Status Tabs -->
<div class="bg-white rounded-xl shadow mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('conferences.index', ['status' => 'active']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'active' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Active Conferences
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['active'] }}</span>
            </a>
            
            <a href="{{ route('conferences.index', ['status' => 'upcoming']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'upcoming' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Upcoming Conferences
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['upcoming'] }}</span>
            </a>
            
            <a href="{{ route('conferences.index', ['status' => 'finished']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'finished' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Finished Conferences
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['finished'] }}</span>
            </a>
            
            <a href="{{ route('conferences.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'all' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                All Conferences
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $conferenceCounts['all'] }}</span>
            </a>
        </nav>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($conferences ?? [] as $conference)
                @php
                    $conferenceData = \App\Helpers\DateHelper::formatConferenceDates($conference->start_date, $conference->end_date);
                    $statusClass = \App\Helpers\DateHelper::getConferenceStatusColorClass(
                        $conferenceData['is_active'], 
                        $conferenceData['is_past'], 
                        $conferenceData['is_today'], 
                        $conferenceData['is_upcoming']
                    );
                    $durationClass = \App\Helpers\DateHelper::getConferenceDurationColorClass($conferenceData['duration_days']);
                    $statusText = \App\Helpers\DateHelper::getConferenceStatusText(
                        $conferenceData['is_active'], 
                        $conferenceData['is_past'], 
                        $conferenceData['is_today'], 
                        $conferenceData['is_upcoming']
                    );
                @endphp
                
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $conference->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ $conferenceData['schedule_string'] }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $conferenceData['start_date_formatted'] }} - {{ $conferenceData['end_date_formatted'] }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $durationClass }}">
                            {{ $conferenceData['duration'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $conference->venue->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('conferences.show', $conference) }}" 
                           class="inline-flex items-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                           title="View Conference Details"
                           aria-label="View conference details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        
                        <a href="{{ route('conferences.edit', $conference) }}" 
                           class="inline-flex items-center p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded-lg transition-colors duration-200"
                           title="Edit Conference"
                           aria-label="Edit conference">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <form action="{{ route('conferences.destroy', $conference) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this conference?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Delete Conference"
                                    aria-label="Delete conference">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        @if($status === 'active')
                            No active conferences at the moment.
                        @elseif($status === 'upcoming')
                            No upcoming conferences scheduled.
                        @elseif($status === 'finished')
                            No finished conferences found.
                        @else
                            No conferences found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $conferences->appends(['status' => $status])->links() }}
    </div>
</div>

<style>
.tab-link {
    transition: all 0.2s ease-in-out;
}

.tab-link:hover {
    transform: translateY(-1px);
}
</style>
@endsection 