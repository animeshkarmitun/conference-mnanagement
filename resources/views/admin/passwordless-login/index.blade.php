@extends('layouts.app')

@section('title', 'Passwordless Login Management')

@push('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stat-item {
        text-align: center;
        padding: 10px;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .action-btn {
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
/* Sort indicators for activity table */
.pla-sort-icon { margin-left: 6px; color: #9ca3af; display: inline-block; transition: transform .2s ease, color .2s ease; }
.pla-sort-icon.active { color: #3b82f6; }
.pla-sort-icon.asc { transform: rotate(180deg); }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Passwordless Login Management</h1>
        <p class="text-gray-600 mt-2">Generate secure login links for conference attendees and speakers</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_tokens'] }}</div>
                <div class="stat-label">Total Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['active_tokens'] }}</div>
                <div class="stat-label">Active Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['used_tokens'] }}</div>
                <div class="stat-label">Used Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['tokens_created_today'] }}</div>
                <div class="stat-label">Created Today</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="openGenerateModal()" 
                    class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Generate Single Link
            </button>
            
            <button onclick="openBulkModal()" 
                    class="action-btn bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Generate Bulk Links
            </button>
            
            <button onclick="cleanupExpired()" 
                    class="action-btn bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Cleanup Expired
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Filter Participants</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Participant Type</label>
                <select id="typeFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="globalSearch" placeholder="Search participants..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sortBy" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="name" selected>Name</option>
                    <option value="email">Email</option>
                    <option value="type">Type</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                <select id="sortDir" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="asc" selected>Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="applyFilters()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Passwordless Login Activity -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Passwordless Login Activity</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="plaTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th data-sort="user" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            <span>User</span>
                            <svg class="pla-sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 11 12 6 17 11"></polyline></svg>
                        </th>
                        <th data-sort="type" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            <span>Type</span>
                            <svg class="pla-sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 11 12 6 17 11"></polyline></svg>
                        </th>
                        <th data-sort="status" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            <span>Status</span>
                            <svg class="pla-sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 11 12 6 17 11"></polyline></svg>
                        </th>
                        <th data-sort="created" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            <span>Created</span>
                            <svg class="pla-sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 11 12 6 17 11"></polyline></svg>
                        </th>
                        <th data-sort="expires" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                            <span>Expires</span>
                            <svg class="pla-sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 11 12 6 17 11"></polyline></svg>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="plaBody">
                    @forelse($recentLogins as $login)
                        @php
                            $types = optional($login->user->participants)->map(function($p){return $p->participantType->name ?? null;})->filter()->unique()->values()->all();
                            $typesLabel = implode(', ', $types);
                            $statusLabel = $login->expires_at->isPast() ? 'Expired' : ($login->used_at ? 'Used' : 'Active');
                        @endphp
                        <tr 
                            data-user="{{ strtolower($login->user->first_name . ' ' . $login->user->last_name . ' ' . $login->user->email) }}"
                            data-type="{{ strtolower($typesLabel) }}"
                            data-status="{{ strtolower($statusLabel) }}"
                            data-created-ts="{{ $login->created_at->timestamp }}"
                            data-expires-ts="{{ $login->expires_at->timestamp }}"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($login->user->first_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $login->user->first_name }} {{ $login->user->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $login->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $typesLabel ?: '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($login->expires_at->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @elseif($login->used_at)
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Used
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $login->use_count }}x
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $login->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $login->expires_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($login->expires_at->isFuture())
                                    <button onclick="copyToClipboard('{{ route('passwordless-login.verify', $login->token) }}')" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Copy Link
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No recent login activity
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Single Link Modal -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Generate Login Link</h3>
                <form id="generateForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Conference</label>
                        <div class="relative">
                            <div class="relative">
                                <input type="text" id="conference_search" placeholder="Search conferences..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-16" 
                                       autocomplete="off" required>
                                <button type="button" id="clear_conference" class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden" title="Clear selection">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                </button>
                                <button type="button" id="conference_dropdown_toggle" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Show all conferences">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="conference_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                <div id="conference_options"></div>
                        </div>
                        </div>
                        <input type="hidden" id="conference_id" name="conference_id" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Participant</label>
                        <div class="relative">
                            <div class="relative">
                                <input type="text" id="participant_search" placeholder="Search participants..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-16" 
                                       autocomplete="off" required>
                                <button type="button" id="clear_participant" class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden" title="Clear selection">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                </button>
                                <button type="button" id="participant_dropdown_toggle" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Show all participants">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="participant_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                <div id="participant_options"></div>
                        </div>
                        </div>
                        <input type="hidden" id="participant_id" name="participant_id" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration (days)</label>
                        <input type="number" id="expirationDays" value="1" min="1" max="90" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeGenerateModal()" id="singleCancelBtn"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" id="singleSubmitBtn"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                            <span id="singleSubmitText">Generate</span>
                            <svg id="singleLoadingSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div id="bulkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
    <div class="flex items-start justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full my-8 flex flex-col max-h-[calc(100vh-4rem)] relative">
            <!-- Progress Overlay -->
            <div id="bulkProgressOverlay" class="hidden absolute inset-0 bg-white bg-opacity-95 z-10 rounded-lg flex flex-col items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-16 w-16 text-green-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Generating Login Links...</h3>
                    <p class="text-gray-600 mb-4">Please wait while we create passwordless login links for the selected participants.</p>
                    <div class="bg-gray-200 rounded-full h-2 w-64 mx-auto overflow-hidden">
                        <div id="bulkProgressBar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="bulkProgressText" class="text-sm text-gray-500 mt-2">Preparing...</p>
                </div>
            </div>
            
            <div class="flex-shrink-0 p-6 border-b">
                <h3 class="text-lg font-semibold">Generate Bulk Login Links</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-6 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                <form id="bulkForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Conference</label>
                        <div class="relative">
                            <div class="relative">
                                <input type="text" id="bulk_conference_search" placeholder="Search conferences..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-16" 
                                       autocomplete="off" required>
                                <button type="button" id="bulk_clear_conference" class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden" title="Clear selection">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                </button>
                                <button type="button" id="bulk_conference_dropdown_toggle" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Show all conferences">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="bulk_conference_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                <div id="bulk_conference_options"></div>
                        </div>
                        </div>
                        <input type="hidden" id="bulk_conference_id" name="conference_id" required>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Select Sessions</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAllSessions" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSelectAllSessions()">
                                <label for="selectAllSessions" class="text-sm text-gray-600 cursor-pointer">Select All</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div id="sessionList" class="max-h-32 overflow-y-auto border border-gray-300 rounded-md p-3">
                                <div class="text-center text-gray-500">Select a conference first...</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Select Participants</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAllParticipants" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSelectAllParticipants()">
                                <label for="selectAllParticipants" class="text-sm text-gray-600 cursor-pointer">Select All</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <input type="text" id="bulkParticipantSearch" placeholder="Search participants..." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="participantList" class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                            <div class="text-center text-gray-500">Select sessions first...</div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration (days)</label>
                        <input type="number" id="bulkExpirationDays" value="1" min="1" max="90" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </form>
            </div>
            <div class="flex-shrink-0 p-6 border-t bg-gray-50 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBulkModal()" id="bulkCancelBtn"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" form="bulkForm" id="bulkSubmitBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
                        <span id="bulkSubmitText">Generate All</span>
                        <svg id="bulkLoadingSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Global variables for storing data
let allConferences = [];
let allParticipants = [];
let filteredParticipants = [];
let allSessions = [];
let selectedSessions = [];
let sessionParticipants = [];

// Global function to render participant options
function renderParticipantOptions(participants) {
    const participantOptions = document.getElementById('participant_options');
    if (!participantOptions) return;
    
    participantOptions.innerHTML = '';
    
    if (participants.length === 0) {
        participantOptions.innerHTML = `
            <div class="px-4 py-2 text-sm text-gray-500">
                No participants found
            </div>
        `;
        return;
    }
    
    participants.forEach(participant => {
        const option = document.createElement('div');
        option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors duration-150';
        
        const nameDiv = document.createElement('div');
        nameDiv.className = 'font-medium text-gray-900';
        nameDiv.textContent = `${participant.first_name} ${participant.last_name}`;
        
        const emailDiv = document.createElement('div');
        emailDiv.className = 'text-xs text-gray-500';
        emailDiv.textContent = participant.email;
        
        const typeDiv = document.createElement('div');
        typeDiv.className = 'text-xs text-blue-600';
        typeDiv.textContent = participant.participant_types;
        
        option.appendChild(nameDiv);
        option.appendChild(emailDiv);
        option.appendChild(typeDiv);
        option.dataset.id = participant.id;
        option.dataset.name = `${participant.first_name} ${participant.last_name}`;
        
        option.addEventListener('click', () => {
            selectParticipant(participant);
        });
        
        participantOptions.appendChild(option);
    });
}

// Global function to select participant
function selectParticipant(participant) {
    const participantSearch = document.getElementById('participant_search');
    const participantIdInput = document.getElementById('participant_id');
    const clearParticipantBtn = document.getElementById('clear_participant');
    const participantDropdown = document.getElementById('participant_dropdown');
    
    if (participantSearch) participantSearch.value = `${participant.first_name} ${participant.last_name}`;
    if (participantIdInput) participantIdInput.value = participant.id;
    if (clearParticipantBtn) clearParticipantBtn.classList.remove('hidden');
    if (participantDropdown) participantDropdown.classList.add('hidden');
    
    // Also update local state if the dropdown is initialized
    if (window.participantDropdownState) {
        window.participantDropdownState.selectedParticipant = participant;
        window.participantDropdownState.isParticipantDropdownOpen = false;
    }
}

// Load participant types for filtering
async function loadParticipantTypes() {
    try {
        const response = await fetch('{{ route("passwordless-login.participant-types") }}');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('typeFilter');
            select.innerHTML = '<option value="">All Types</option>';
            
            Object.keys(data.data).forEach(category => {
                const optgroup = document.createElement('optgroup');
                optgroup.label = category;
                data.data[category].forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.name;
                    option.textContent = type.name;
                    optgroup.appendChild(option);
                });
                select.appendChild(optgroup);
            });
        }
    } catch (error) {
        console.error('Error loading participant types:', error);
    }
}

// Apply filters
async function applyFilters() {
    const typeFilter = document.getElementById('typeFilter').value;
    const searchTerm = document.getElementById('globalSearch').value;
    const sortBy = document.getElementById('sortBy').value;
    const sortDir = document.getElementById('sortDir').value;
    
    try {
        let url = '{{ route("passwordless-login.participants") }}';
        if (typeFilter) {
            url = '{{ route("passwordless-login.participants.by-type") }}?type=' + encodeURIComponent(typeFilter);
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            let filteredParticipants = data.data;
            
            if (searchTerm) {
                const searchLower = searchTerm.toLowerCase();
                filteredParticipants = filteredParticipants.filter(participant => 
                    participant.first_name.toLowerCase().includes(searchLower) ||
                    participant.last_name.toLowerCase().includes(searchLower) ||
                    participant.email.toLowerCase().includes(searchLower) ||
                    participant.participant_types.toLowerCase().includes(searchLower)
                );
            }
            // sort
            filteredParticipants = sortParticipants(filteredParticipants, sortBy, sortDir);
            
            // Update the display
            updateParticipantDisplay(filteredParticipants);
        }
    } catch (error) {
        console.error('Error applying filters:', error);
    }
}

// Update participant display
function updateParticipantDisplay(participants) {
    // Update single select
    const select = document.getElementById('userSelect');
    select.innerHTML = '<option value="">Select a participant...</option>';
    // Apply current sort to keep consistent when only search changes
    const sortBy = document.getElementById('sortBy') ? document.getElementById('sortBy').value : 'name';
    const sortDir = document.getElementById('sortDir') ? document.getElementById('sortDir').value : 'asc';
    const sorted = sortParticipants(participants, sortBy, sortDir);
    sorted.forEach(participant => {
        const option = document.createElement('option');
        option.value = participant.id;
        option.textContent = `${participant.first_name} ${participant.last_name} (${participant.email}) - ${participant.participant_types}`;
        select.appendChild(option);
    });
    
    // Update bulk list
    allParticipants = sorted;
    renderBulkParticipants(sorted);
}

// Utility: sort participants client-side
function sortParticipants(list, by = 'name', dir = 'asc') {
    const direction = dir === 'desc' ? -1 : 1;
    const safeLower = v => (v || '').toString().toLowerCase();
    return [...list].sort((a,b) => {
        let av, bv;
        if (by === 'email') { av = safeLower(a.email); bv = safeLower(b.email); }
        else if (by === 'type') { av = safeLower(a.participant_types); bv = safeLower(b.participant_types); }
        else { av = safeLower(`${a.first_name} ${a.last_name}`); bv = safeLower(`${b.first_name} ${b.last_name}`); }
        if (av < bv) return -1 * direction;
        if (av > bv) return 1 * direction;
        return 0;
    });
}

// Load conferences for dropdown
async function loadConferences() {
    try {
        const response = await fetch('{{ route("api.conferences") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        // Check if response is successful
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'Failed to load conferences' }));
            console.error('Error loading conferences:', errorData.message || `HTTP ${response.status}`);
            return;
        }
        
        const data = await response.json();
        
        if (data.success && data.conferences) {
            allConferences = data.conferences;
            
            // Update bulk conference select
            const bulkSelect = document.getElementById('bulkConferenceSelect');
            if (bulkSelect) {
                bulkSelect.innerHTML = '<option value="">Search and select a conference...</option>';
                data.conferences.forEach(conference => {
                    const option = document.createElement('option');
                    option.value = conference.id;
                    option.textContent = conference.name;
                    bulkSelect.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error loading conferences:', error);
    }
}

// Load participants for single link generation
async function loadParticipants(conferenceId = null) {
    try {
        let url = '{{ route("passwordless-login.participants") }}';
        if (conferenceId) {
            url = `{{ route("passwordless-login.participants") }}?conference_id=${conferenceId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            allParticipants = data.data;
            filteredParticipants = data.data;
            
            // Update participant dropdown if it exists
            const participantDropdown = document.getElementById('participant_dropdown');
            const participantOptions = document.getElementById('participant_options');
            
            if (participantDropdown && participantOptions) {
                // Clear any existing selection
                const participantSearch = document.getElementById('participant_search');
                const participantIdInput = document.getElementById('participant_id');
                const clearParticipantBtn = document.getElementById('clear_participant');
                
                if (participantSearch) participantSearch.value = '';
                if (participantIdInput) participantIdInput.value = '';
                if (clearParticipantBtn) clearParticipantBtn.classList.add('hidden');
                
                // Re-render participant options
                renderParticipantOptions(allParticipants);
            }
        }
    } catch (error) {
        console.error('Error loading participants:', error);
    }
}

// Searchable Conference Select Functionality
function initializeConferenceDropdown() {
    const conferenceSearch = document.getElementById('conference_search');
    const conferenceDropdown = document.getElementById('conference_dropdown');
    const conferenceOptions = document.getElementById('conference_options');
    const conferenceIdInput = document.getElementById('conference_id');
    const clearConferenceBtn = document.getElementById('clear_conference');
    const conferenceDropdownToggle = document.getElementById('conference_dropdown_toggle');
    
    let selectedConference = null;
    let filteredConferences = [];
    let isConferenceDropdownOpen = false;
    
    function filterConferences(query) {
        if (!query.trim()) {
            return allConferences;
        }
        const lowerQuery = query.toLowerCase();
        return allConferences.filter(conference => 
            conference.name.toLowerCase().includes(lowerQuery)
        );
    }
    
    function renderConferenceOptions(conferences) {
        conferenceOptions.innerHTML = '';
        
        if (conferences.length === 0) {
            conferenceOptions.innerHTML = `
                <div class="px-4 py-2 text-sm text-gray-500">
                    No conferences found
                </div>
            `;
            return;
        }
        
        conferences.forEach(conference => {
            const option = document.createElement('div');
            option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors duration-150';
            option.textContent = conference.name;
            option.dataset.id = conference.id;
            option.dataset.name = conference.name;
            
            option.addEventListener('click', () => {
                selectConference(conference);
            });
            
            conferenceOptions.appendChild(option);
        });
    }
    
    function selectConference(conference) {
        selectedConference = conference;
        conferenceSearch.value = conference.name;
        conferenceIdInput.value = conference.id;
        clearConferenceBtn.classList.remove('hidden');
        conferenceDropdown.classList.add('hidden');
        isConferenceDropdownOpen = false;
        
        // Load participants for the selected conference
        loadParticipants(conference.id);
        
        // Clear participant selection when conference changes
        const participantSearch = document.getElementById('participant_search');
        const participantIdInput = document.getElementById('participant_id');
        const clearParticipantBtn = document.getElementById('clear_participant');
        
        if (participantSearch) participantSearch.value = '';
        if (participantIdInput) participantIdInput.value = '';
        if (clearParticipantBtn) clearParticipantBtn.classList.add('hidden');
        
        // Trigger conference change event for existing functionality
        const event = new Event('change');
        conferenceIdInput.dispatchEvent(event);
    }
    
    function clearConferenceSelection() {
        selectedConference = null;
        conferenceSearch.value = '';
        conferenceIdInput.value = '';
        clearConferenceBtn.classList.add('hidden');
        conferenceDropdown.classList.add('hidden');
        isConferenceDropdownOpen = false;
    }
    
    function toggleConferenceDropdown() {
        if (isConferenceDropdownOpen) {
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
        } else {
            filteredConferences = filterConferences(conferenceSearch.value);
            renderConferenceOptions(filteredConferences);
            conferenceDropdown.classList.remove('hidden');
            isConferenceDropdownOpen = true;
        }
    }
    
    // Event listeners
    conferenceSearch.addEventListener('input', (e) => {
        filteredConferences = filterConferences(e.target.value);
        renderConferenceOptions(filteredConferences);
        conferenceDropdown.classList.remove('hidden');
        isConferenceDropdownOpen = true;
    });
    
    conferenceSearch.addEventListener('focus', () => {
        if (!isConferenceDropdownOpen) {
            filteredConferences = filterConferences(conferenceSearch.value);
            renderConferenceOptions(filteredConferences);
            conferenceDropdown.classList.remove('hidden');
            isConferenceDropdownOpen = true;
        }
    });
    
    clearConferenceBtn.addEventListener('click', clearConferenceSelection);
    conferenceDropdownToggle.addEventListener('click', toggleConferenceDropdown);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#conference_search') && !e.target.closest('#conference_dropdown')) {
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
        }
    });
}

// Searchable Bulk Conference Select Functionality
function initializeBulkConferenceDropdown() {
    const conferenceSearch = document.getElementById('bulk_conference_search');
    const conferenceDropdown = document.getElementById('bulk_conference_dropdown');
    const conferenceOptions = document.getElementById('bulk_conference_options');
    const conferenceIdInput = document.getElementById('bulk_conference_id');
    const clearConferenceBtn = document.getElementById('bulk_clear_conference');
    const conferenceDropdownToggle = document.getElementById('bulk_conference_dropdown_toggle');
    
    let selectedConference = null;
    let filteredConferences = [];
    let isConferenceDropdownOpen = false;
    
    function filterConferences(query) {
        if (!query.trim()) {
            return allConferences;
        }
        const lowerQuery = query.toLowerCase();
        return allConferences.filter(conference => 
            conference.name.toLowerCase().includes(lowerQuery)
        );
    }
    
    function renderConferenceOptions(conferences) {
        conferenceOptions.innerHTML = '';
        
        if (conferences.length === 0) {
            conferenceOptions.innerHTML = `
                <div class="px-4 py-2 text-sm text-gray-500">
                    No conferences found
                </div>
            `;
            return;
        }
        
        conferences.forEach(conference => {
            const option = document.createElement('div');
            option.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors duration-150';
            option.textContent = conference.name;
            option.dataset.id = conference.id;
            option.dataset.name = conference.name;
            
            option.addEventListener('click', () => {
                selectConference(conference);
            });
            
            conferenceOptions.appendChild(option);
        });
    }
    
    function selectConference(conference) {
        selectedConference = conference;
        conferenceSearch.value = conference.name;
        conferenceIdInput.value = conference.id;
        clearConferenceBtn.classList.remove('hidden');
        conferenceDropdown.classList.add('hidden');
        isConferenceDropdownOpen = false;
        
        // Load sessions for the selected conference
        loadSessions(conference.id);
        
        // Clear participant list until sessions are selected
        document.getElementById('participantList').innerHTML = '<div class="text-center text-gray-500">Select sessions first...</div>';
        
        // Trigger conference change event for existing functionality
        const event = new Event('change');
        conferenceIdInput.dispatchEvent(event);
    }
    
    function clearConferenceSelection() {
        selectedConference = null;
        conferenceSearch.value = '';
        conferenceIdInput.value = '';
        clearConferenceBtn.classList.add('hidden');
        conferenceDropdown.classList.add('hidden');
        isConferenceDropdownOpen = false;
    }
    
    function toggleConferenceDropdown() {
        if (isConferenceDropdownOpen) {
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
        } else {
            filteredConferences = filterConferences(conferenceSearch.value);
            renderConferenceOptions(filteredConferences);
            conferenceDropdown.classList.remove('hidden');
            isConferenceDropdownOpen = true;
        }
    }
    
    // Event listeners
    conferenceSearch.addEventListener('input', (e) => {
        filteredConferences = filterConferences(e.target.value);
        renderConferenceOptions(filteredConferences);
        conferenceDropdown.classList.remove('hidden');
        isConferenceDropdownOpen = true;
    });
    
    conferenceSearch.addEventListener('focus', () => {
        if (!isConferenceDropdownOpen) {
            filteredConferences = filterConferences(conferenceSearch.value);
            renderConferenceOptions(filteredConferences);
            conferenceDropdown.classList.remove('hidden');
            isConferenceDropdownOpen = true;
        }
    });
    
    clearConferenceBtn.addEventListener('click', clearConferenceSelection);
    conferenceDropdownToggle.addEventListener('click', toggleConferenceDropdown);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#bulk_conference_search') && !e.target.closest('#bulk_conference_dropdown')) {
            conferenceDropdown.classList.add('hidden');
            isConferenceDropdownOpen = false;
        }
    });
}

// Searchable Participant Select Functionality
function initializeParticipantDropdown() {
    const participantSearch = document.getElementById('participant_search');
    const participantDropdown = document.getElementById('participant_dropdown');
    const participantOptions = document.getElementById('participant_options');
    const participantIdInput = document.getElementById('participant_id');
    const clearParticipantBtn = document.getElementById('clear_participant');
    const participantDropdownToggle = document.getElementById('participant_dropdown_toggle');
    
    // Store state globally for access from other functions
    window.participantDropdownState = {
        selectedParticipant: null,
        filteredParticipants: [],
        isParticipantDropdownOpen: false
    };
    
    let selectedParticipant = null;
    let filteredParticipants = [];
    let isParticipantDropdownOpen = false;
    
    function filterParticipants(query) {
        if (!query.trim()) {
            return allParticipants;
        }
        const lowerQuery = query.toLowerCase();
        return allParticipants.filter(participant => 
            participant.first_name.toLowerCase().includes(lowerQuery) ||
            participant.last_name.toLowerCase().includes(lowerQuery) ||
            participant.email.toLowerCase().includes(lowerQuery) ||
            participant.participant_types.toLowerCase().includes(lowerQuery)
        );
    }
    
    function selectParticipantLocal(participant) {
        selectedParticipant = participant;
        window.participantDropdownState.selectedParticipant = participant;
        participantSearch.value = `${participant.first_name} ${participant.last_name}`;
        participantIdInput.value = participant.id;
        clearParticipantBtn.classList.remove('hidden');
        participantDropdown.classList.add('hidden');
        isParticipantDropdownOpen = false;
        window.participantDropdownState.isParticipantDropdownOpen = false;
    }
    
    function clearParticipantSelection() {
        selectedParticipant = null;
        window.participantDropdownState.selectedParticipant = null;
        participantSearch.value = '';
        participantIdInput.value = '';
        clearParticipantBtn.classList.add('hidden');
        participantDropdown.classList.add('hidden');
        isParticipantDropdownOpen = false;
        window.participantDropdownState.isParticipantDropdownOpen = false;
    }
    
    function toggleParticipantDropdown() {
        if (isParticipantDropdownOpen) {
            participantDropdown.classList.add('hidden');
            isParticipantDropdownOpen = false;
            window.participantDropdownState.isParticipantDropdownOpen = false;
        } else {
            filteredParticipants = filterParticipants(participantSearch.value);
            renderParticipantOptions(filteredParticipants);
            participantDropdown.classList.remove('hidden');
            isParticipantDropdownOpen = true;
            window.participantDropdownState.isParticipantDropdownOpen = true;
        }
    }
    
    // Event listeners
    participantSearch.addEventListener('input', (e) => {
        filteredParticipants = filterParticipants(e.target.value);
        renderParticipantOptions(filteredParticipants);
        participantDropdown.classList.remove('hidden');
        isParticipantDropdownOpen = true;
    });
    
    participantSearch.addEventListener('focus', () => {
        if (!isParticipantDropdownOpen) {
            filteredParticipants = filterParticipants(participantSearch.value);
            renderParticipantOptions(filteredParticipants);
            participantDropdown.classList.remove('hidden');
            isParticipantDropdownOpen = true;
        }
    });
    
    clearParticipantBtn.addEventListener('click', clearParticipantSelection);
    participantDropdownToggle.addEventListener('click', toggleParticipantDropdown);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#participant_search') && !e.target.closest('#participant_dropdown')) {
            participantDropdown.classList.add('hidden');
            isParticipantDropdownOpen = false;
        }
    });
}


// Load sessions for selected conference
async function loadSessions(conferenceId) {
    try {
        const response = await fetch(`/api/sessions?conference_id=${conferenceId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        const data = await response.json();
        
        if (data.success) {
            allSessions = data.data;
            renderSessions(allSessions);
        }
    } catch (error) {
        console.error('Error loading sessions:', error);
    }
}

// Render sessions in bulk modal
function renderSessions(sessions) {
    const container = document.getElementById('sessionList');
    container.innerHTML = '';
    
    if (sessions.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500">No sessions found for this conference</div>';
        return;
    }
    
    sessions.forEach(session => {
        const div = document.createElement('div');
        div.className = 'flex items-center mb-2 session-item';
        div.innerHTML = `
            <input type="checkbox" id="session_${session.id}" value="${session.id}" 
                   class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500 session-checkbox">
            <label for="session_${session.id}" class="text-sm text-gray-700 flex-1">
                <div class="font-medium">${session.title}</div>
                <div class="text-xs text-gray-500">${new Date(session.start_time).toLocaleString()}</div>
            </label>
        `;
        container.appendChild(div);
    });
    
    // Add event listeners for session selection
    const sessionCheckboxes = container.querySelectorAll('.session-checkbox');
    sessionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', handleSessionSelection);
    });
    
    // Update select all checkbox state
    updateSelectAllSessionsState();
}

// Toggle select all sessions
function toggleSelectAllSessions() {
    const selectAllCheckbox = document.getElementById('selectAllSessions');
    const sessionCheckboxes = document.querySelectorAll('.session-checkbox');
    
    sessionCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    // Trigger session selection handler for each checkbox
    sessionCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            handleSessionSelection({ target: checkbox });
        } else {
            // Remove from selected sessions
            const sessionId = parseInt(checkbox.value);
            selectedSessions = selectedSessions.filter(id => id !== sessionId);
        }
    });
}

// Update select all sessions checkbox state
function updateSelectAllSessionsState() {
    const selectAllCheckbox = document.getElementById('selectAllSessions');
    const sessionCheckboxes = document.querySelectorAll('.session-checkbox');
    
    if (sessionCheckboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        return;
    }
    
    const checkedCount = Array.from(sessionCheckboxes).filter(cb => cb.checked).length;
    
    if (checkedCount === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCount === sessionCheckboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

// Handle session selection
function handleSessionSelection() {
    selectedSessions = Array.from(document.querySelectorAll('.session-checkbox:checked'))
        .map(checkbox => parseInt(checkbox.value));
    
    // Update select all checkbox state
    updateSelectAllSessionsState();
    
    if (selectedSessions.length > 0) {
        loadSessionParticipants(selectedSessions);
    } else {
        document.getElementById('participantList').innerHTML = '<div class="text-center text-gray-500">Select sessions first...</div>';
    }
}

// Load participants for selected sessions
async function loadSessionParticipants(sessionIds) {
    try {
        const response = await fetch(`/api/sessions/participants`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin',
            body: JSON.stringify({ session_ids: sessionIds })
        });
        
        const data = await response.json();
        
        if (data.success) {
            sessionParticipants = data.data;
            renderBulkParticipants(sessionParticipants);
            setupSearch();
        }
    } catch (error) {
        console.error('Error loading session participants:', error);
    }
}

// Load participants for bulk generation
async function loadBulkParticipants(conferenceId = null) {
    try {
        let url = '{{ route("passwordless-login.participants") }}';
        if (conferenceId) {
            url = `{{ route("passwordless-login.participants") }}?conference_id=${conferenceId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            allParticipants = data.data;
            renderBulkParticipants(allParticipants);
            setupSearch();
        }
    } catch (error) {
        console.error('Error loading participants:', error);
    }
}

// Render participants in bulk modal
function renderBulkParticipants(participants) {
    const container = document.getElementById('participantList');
    container.innerHTML = '';
    
    if (participants.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500">No participants found for selected sessions</div>';
        updateSelectAllParticipantsState();
        return;
    }
    
    participants.forEach(participant => {
        const div = document.createElement('div');
        div.className = 'flex items-center mb-2 participant-item';
        
        // Get email count for this participant across selected sessions
        const emailCount = participant.email_send_count || 0;
        const lastEmailSent = participant.last_email_sent_at ? new Date(participant.last_email_sent_at).toLocaleDateString() : 'Never';
        
        div.innerHTML = `
            <input type="checkbox" id="participant_${participant.id}" value="${participant.id}" 
                   class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500 participant-checkbox">
            <label for="participant_${participant.id}" class="text-sm text-gray-700 flex-1">
                <div class="font-medium">${participant.first_name} ${participant.last_name}</div>
                <div class="text-xs text-gray-500">${participant.email}</div>
                <div class="text-xs text-blue-600">${participant.participant_types}</div>
                <div class="text-xs text-green-600">Emails sent: ${emailCount} | Last: ${lastEmailSent}</div>
            </label>
        `;
        container.appendChild(div);
    });
    
    // Update select all checkbox state
    updateSelectAllParticipantsState();
}

// Toggle select all participants
function toggleSelectAllParticipants() {
    const selectAllCheckbox = document.getElementById('selectAllParticipants');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    
    participantCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Update select all participants checkbox state
function updateSelectAllParticipantsState() {
    const selectAllCheckbox = document.getElementById('selectAllParticipants');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    
    if (participantCheckboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        return;
    }
    
    const checkedCount = Array.from(participantCheckboxes).filter(cb => cb.checked).length;
    
    if (checkedCount === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCount === participantCheckboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

// Setup search functionality
function setupSearch() {
    const searchInput = document.getElementById('bulkParticipantSearch');
    if (!searchInput) return;
    
    // Remove existing event listeners
    searchInput.removeEventListener('input', handleParticipantSearch);
    searchInput.addEventListener('input', handleParticipantSearch);
}

function handleParticipantSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const participantsToSearch = selectedSessions.length > 0 ? sessionParticipants : allParticipants;
    const filteredParticipants = participantsToSearch.filter(participant => 
        participant.first_name.toLowerCase().includes(searchTerm) ||
        participant.last_name.toLowerCase().includes(searchTerm) ||
        participant.email.toLowerCase().includes(searchTerm) ||
        participant.participant_types.toLowerCase().includes(searchTerm)
    );
    renderBulkParticipants(filteredParticipants);
}

// Modal functions
function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
    loadConferences();
    loadParticipants();
    // Setup searchable selects after a short delay to ensure DOM is ready
    setTimeout(() => {
        initializeConferenceDropdown();
        initializeParticipantDropdown();
    }, 100);
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
    
    // Reset button state
    const submitBtn = document.getElementById('singleSubmitBtn');
    const submitText = document.getElementById('singleSubmitText');
    const loadingSpinner = document.getElementById('singleLoadingSpinner');
    const cancelBtn = document.getElementById('singleCancelBtn');
    
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
    if (cancelBtn) {
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    if (submitText) submitText.textContent = 'Generate';
    if (loadingSpinner) loadingSpinner.classList.add('hidden');
}

async function openBulkModal() {
    const modal = document.getElementById('bulkModal');
    modal.classList.remove('hidden');
    
    // Scroll modal to top when opening
    modal.scrollTop = 0;
    
    await loadConferences();
    loadBulkParticipants();
    // Setup searchable select after conferences are loaded
    setTimeout(() => {
        initializeBulkConferenceDropdown();
    }, 100);
}

function closeBulkModal() {
    const modal = document.getElementById('bulkModal');
    modal.classList.add('hidden');
    
    // Reset scroll position when closing
    modal.scrollTop = 0;
    
    // Reset button state
    const submitBtn = document.getElementById('bulkSubmitBtn');
    const submitText = document.getElementById('bulkSubmitText');
    const loadingSpinner = document.getElementById('bulkLoadingSpinner');
    const cancelBtn = document.getElementById('bulkCancelBtn');
    const progressOverlay = document.getElementById('bulkProgressOverlay');
    const progressBar = document.getElementById('bulkProgressBar');
    const progressText = document.getElementById('bulkProgressText');
    
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
    if (cancelBtn) {
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    if (submitText) submitText.textContent = 'Generate All';
    if (loadingSpinner) loadingSpinner.classList.add('hidden');
    if (progressOverlay) progressOverlay.classList.add('hidden');
    if (progressBar) progressBar.style.width = '0%';
    if (progressText) progressText.textContent = 'Preparing...';
}

// Form submissions
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = document.getElementById('singleSubmitBtn');
    const submitText = document.getElementById('singleSubmitText');
    const loadingSpinner = document.getElementById('singleLoadingSpinner');
    const cancelBtn = document.getElementById('singleCancelBtn');
    
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    cancelBtn.disabled = true;
    cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
    submitText.textContent = 'Generating...';
    loadingSpinner.classList.remove('hidden');
    
    const formData = new FormData();
    formData.append('user_id', document.getElementById('participant_id').value);
    formData.append('conference_id', document.getElementById('conference_id').value);
    formData.append('expiration_days', document.getElementById('expirationDays').value);
    
    try {
        const response = await fetch('{{ route("passwordless-login.generate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success state briefly
            submitText.textContent = 'Complete!';
            loadingSpinner.classList.add('hidden');
            
            setTimeout(() => {
                alert('Login link generated successfully!');
                closeGenerateModal();
                location.reload();
            }, 500);
        } else {
            // Reset button state on error
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Generate';
            loadingSpinner.classList.add('hidden');
            
            // Show detailed error messages if validation errors exist
            let errorMessage = data.message || 'An error occurred';
            if (data.errors) {
                errorMessage += '\n\nValidation Errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `- ${key}: ${data.errors[key].join(', ')}\n`;
                });
            }
            alert('Error: ' + errorMessage);
        }
    } catch (error) {
        // Reset button state on error
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        submitText.textContent = 'Generate';
        loadingSpinner.classList.add('hidden');
        
        console.error('Error:', error);
        alert('An error occurred while generating the link.');
    }
});

document.getElementById('bulkForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const selectedParticipants = Array.from(document.querySelectorAll('#participantList input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedParticipants.length === 0) {
        alert('Please select at least one participant.');
        return;
    }
    
    if (selectedSessions.length === 0) {
        alert('Please select at least one session.');
        return;
    }
    
    // Show loading state and progress overlay
    const submitBtn = document.getElementById('bulkSubmitBtn');
    const submitText = document.getElementById('bulkSubmitText');
    const loadingSpinner = document.getElementById('bulkLoadingSpinner');
    const cancelBtn = document.getElementById('bulkCancelBtn');
    const progressOverlay = document.getElementById('bulkProgressOverlay');
    const progressBar = document.getElementById('bulkProgressBar');
    const progressText = document.getElementById('bulkProgressText');
    
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    cancelBtn.disabled = true;
    cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
    submitText.textContent = 'Generating...';
    loadingSpinner.classList.remove('hidden');
    
    // Show progress overlay
    progressOverlay.classList.remove('hidden');
    progressBar.style.width = '10%';
    progressText.textContent = `Generating links for ${selectedParticipants.length} participant(s)...`;
    
    // Use JSON request instead of FormData for proper array handling
    const requestData = {
        user_ids: selectedParticipants,
        session_ids: selectedSessions,
        conference_id: document.getElementById('bulk_conference_id').value,
        expiration_days: document.getElementById('bulkExpirationDays').value
    };
    
    try {
        // Update progress
        progressBar.style.width = '30%';
        progressText.textContent = 'Sending request...';
        
        const response = await fetch('{{ route("passwordless-login.generate.bulk") }}', {
            method: 'POST',
            body: JSON.stringify(requestData),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        // Update progress
        progressBar.style.width = '70%';
        progressText.textContent = 'Processing response...';
        
        const data = await response.json();
        
        // Update progress
        progressBar.style.width = '100%';
        progressText.textContent = 'Complete!';
        
        if (data.success) {
            // Show success state briefly
            submitText.textContent = 'Complete!';
            loadingSpinner.classList.add('hidden');
            
            setTimeout(() => {
                alert(data.message);
                progressOverlay.classList.add('hidden');
                closeBulkModal();
                location.reload();
            }, 800);
        } else {
            // Reset button state on error
            progressOverlay.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Generate All';
            loadingSpinner.classList.add('hidden');
            
            // Show detailed error messages if validation errors exist
            let errorMessage = data.message || 'An error occurred';
            if (data.errors) {
                errorMessage += '\n\nValidation Errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `- ${key}: ${data.errors[key].join(', ')}\n`;
                });
            }
            alert('Error: ' + errorMessage);
        }
    } catch (error) {
        // Reset button state on error
        progressOverlay.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        cancelBtn.disabled = false;
        cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        submitText.textContent = 'Generate All';
        loadingSpinner.classList.add('hidden');
        
        console.error('Error:', error);
        alert('An error occurred while generating the links.');
    }
});

// Cleanup expired tokens
async function cleanupExpired() {
    if (!confirm('Are you sure you want to cleanup expired tokens?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("passwordless-login.cleanup") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while cleaning up expired tokens.');
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link to clipboard.');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadParticipantTypes();
    loadParticipants();
    
    // Add event listener for conference selection
    document.addEventListener('change', function(e) {
        if (e.target.id === 'conference_id') {
            const conferenceId = e.target.value;
            loadParticipants(conferenceId);
        }
        if (e.target.id === 'bulk_conference_id') {
            const conferenceId = e.target.value;
            loadBulkParticipants(conferenceId);
        }
    });
});

// Filtering and sorting for Passwordless Login Activity table
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('plaBody');
    const table = document.getElementById('plaTable');
    const headers = table.querySelectorAll('thead [data-sort]');
    let currentSort = { key: 'created', dir: 'desc' };

    function applyActivityFiltersAndSort() {
        const typeVal = (document.getElementById('typeFilter').value || '').toLowerCase();
        const q = (document.getElementById('globalSearch').value || '').toLowerCase();
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        // Filter
        rows.forEach(row => {
            const matchesType = !typeVal || row.dataset.type.includes(typeVal);
            const matchesSearch = !q || row.dataset.user.includes(q) || row.dataset.type.includes(q);
            row.style.display = matchesType && matchesSearch ? '' : 'none';
        });

        // Sort
        const visible = rows.filter(r => r.style.display !== 'none');
        visible.sort((a,b) => compareRows(a,b,currentSort.key,currentSort.dir));
        visible.forEach(r => tableBody.appendChild(r));
    }

    function compareRows(a,b,key,dir){
        const d = dir === 'desc' ? -1 : 1;
        if (key === 'created') return (Number(a.dataset.createdTs) - Number(b.dataset.createdTs)) * d;
        if (key === 'expires') return (Number(a.dataset.expiresTs) - Number(b.dataset.expiresTs)) * d;
        if (key === 'user') return a.dataset.user.localeCompare(b.dataset.user) * d;
        if (key === 'type') return a.dataset.type.localeCompare(b.dataset.type) * d;
        if (key === 'status') return a.dataset.status.localeCompare(b.dataset.status) * d;
        return 0;
    }

    // Hook filters
    ['typeFilter','globalSearch'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', applyActivityFiltersAndSort);
        if (el) el.addEventListener('change', applyActivityFiltersAndSort);
    });

    // Sorting via headers
    headers.forEach(h => {
        h.addEventListener('click', () => {
            const key = h.getAttribute('data-sort');
            if (currentSort.key === key) currentSort.dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
            else currentSort = { key, dir: 'asc' };
            updatePlaSortIndicators();
            applyActivityFiltersAndSort();
        });
    });

    // Initial
    applyActivityFiltersAndSort();
    updatePlaSortIndicators();

    function updatePlaSortIndicators(){
        headers.forEach(h => {
            const icon = h.querySelector('.pla-sort-icon');
            if (!icon) return;
            icon.classList.remove('active','asc');
        });
        const active = Array.from(headers).find(h => h.getAttribute('data-sort') === currentSort.key);
        if (active) {
            const icon = active.querySelector('.pla-sort-icon');
            if (icon) {
                icon.classList.add('active');
                if (currentSort.dir === 'asc') icon.classList.add('asc');
            }
        }
    }
});
</script>
@endpush
