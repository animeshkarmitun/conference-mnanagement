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
    <table class="min-w-full divide-y divide-gray-200" id="conferencesTable">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="status">
                    <div class="flex items-center space-x-1">
                        <span>Status</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="title">
                    <div class="flex items-center space-x-1">
                        <span>Title</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="schedule">
                    <div class="flex items-center space-x-1">
                        <span>Schedule</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="duration">
                    <div class="flex items-center space-x-1">
                        <span>Duration</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-50 transition-colors duration-200 sortable-header" data-sort="venue">
                    <div class="flex items-center space-x-1">
                        <span>Venue</span>
                        <svg class="w-4 h-4 sort-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </th>
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
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $statusText }}" data-sort-priority="{{ $conferenceData['is_active'] ? 1 : ($conferenceData['is_today'] ? 2 : ($conferenceData['is_upcoming'] ? 3 : 4)) }}">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900" data-sort-value="{{ strtolower($conference->name) }}">{{ $conference->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $conference->start_date }}">
                        <div class="text-sm text-gray-900 font-medium">{{ $conferenceData['schedule_string'] }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $conferenceData['start_date_formatted'] }} - {{ $conferenceData['end_date_formatted'] }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sort-value="{{ $conferenceData['duration_days'] }}">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $durationClass }}">
                            {{ $conferenceData['duration'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500" data-sort-value="{{ strtolower($conference->venue->name ?? 'N/A') }}">{{ $conference->venue->name ?? 'N/A' }}</td>
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

.sortable-header {
    user-select: none;
}

.sortable-header:hover {
    background-color: #f9fafb;
}

.sort-icon {
    transition: all 0.2s ease-in-out;
}

.sort-icon.active {
    color: #3b82f6;
}

.sort-icon.asc {
    transform: rotate(0deg);
}

.sort-icon.desc {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('conferencesTable');
    const tbody = table.querySelector('tbody');
    const headers = table.querySelectorAll('.sortable-header');
    
    let currentSort = {
        column: null,
        direction: 'asc'
    };
    
    // Add click event listeners to all sortable headers
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sort');
            console.log('Sorting by column:', column);
            sortTable(column);
        });
    });
    
    console.log('Found', headers.length, 'sortable headers');
    console.log('Found', tbody.querySelectorAll('tr').length, 'table rows');
    
    function sortTable(column) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Filter out empty rows (like the "no conferences" message)
        const dataRows = rows.filter(row => row.cells.length > 1);
        
        if (dataRows.length === 0) return;
        
        // Determine sort direction
        let direction = 'asc';
        if (currentSort.column === column) {
            direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        }
        
        // Update current sort state
        currentSort.column = column;
        currentSort.direction = direction;
        
        // Update visual indicators
        updateSortIndicators(column, direction);
        
        // Sort the rows
        dataRows.sort((a, b) => {
            const aValue = getCellValue(a, column);
            const bValue = getCellValue(b, column);
            
            let comparison = 0;
            
            if (column === 'status') {
                // Sort by status priority (Active=1, Today=2, Upcoming=3, Completed=4)
                const aPriority = parseInt(a.cells[0].getAttribute('data-sort-priority'));
                const bPriority = parseInt(b.cells[0].getAttribute('data-sort-priority'));
                comparison = aPriority - bPriority;
            } else if (column === 'duration') {
                // Sort by duration days (numeric)
                comparison = parseInt(aValue) - parseInt(bValue);
            } else if (column === 'schedule') {
                // Sort by start date
                comparison = new Date(aValue) - new Date(bValue);
            } else {
                // Sort alphabetically for title and venue
                comparison = aValue.localeCompare(bValue);
            }
            
            return direction === 'asc' ? comparison : -comparison;
        });
        
        // Re-append sorted rows
        dataRows.forEach(row => tbody.appendChild(row));
    }
    
    function getCellValue(row, column) {
        // Get the cell in the specific column (0-indexed)
        const columnIndex = getColumnIndex(column);
        const cell = row.cells[columnIndex];
        
        if (!cell) return '';
        
        if (column === 'status') {
            return parseInt(cell.getAttribute('data-sort-priority'));
        }
        
        return cell.getAttribute('data-sort-value');
    }
    
    function getColumnIndex(column) {
        const columnMap = {
            'status': 0,
            'title': 1,
            'schedule': 2,
            'duration': 3,
            'venue': 4
        };
        return columnMap[column] || 0;
    }
    
    function updateSortIndicators(activeColumn, direction) {
        // Reset all sort icons
        headers.forEach(header => {
            const icon = header.querySelector('.sort-icon');
            icon.classList.remove('active', 'asc', 'desc');
            icon.style.color = '#9ca3af'; // gray-400
        });
        
        // Update active column icon
        const activeHeader = table.querySelector(`[data-sort="${activeColumn}"]`);
        if (activeHeader) {
            const icon = activeHeader.querySelector('.sort-icon');
            icon.classList.add('active', direction);
            icon.style.color = '#3b82f6'; // blue-500
        }
    }
});
</script>
@endsection 