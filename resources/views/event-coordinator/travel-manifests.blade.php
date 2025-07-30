@extends('layouts.app')

@section('title', 'Travel Manifests')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-blue-100 via-blue-50 to-white shadow flex items-center justify-between px-8 py-6 mb-10 border border-blue-200">
    <div class="flex items-center">
        <div class="flex items-center justify-center w-16 h-16 bg-blue-200 rounded-full mr-6 shadow">
            <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-extrabold text-blue-800 tracking-tight mb-1">Travel Manifests</h1>
            <div class="text-gray-600 text-lg font-medium">View and export participant travel details</div>
        </div>
    </div>
    <div>
        <a href="{{ route('event-coordinator.export-manifest') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold text-lg shadow-lg transition duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Filters</h2>
    <form method="GET" action="{{ route('event-coordinator.travel-manifests') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Conference</label>
            <select name="conference_id" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Conferences</option>
                @foreach($conferences as $conference)
                    <option value="{{ $conference->id }}" {{ request('conference_id') == $conference->id ? 'selected' : '' }}>
                        {{ $conference->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hotel</label>
            <select name="hotel_id" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Hotels</option>
                @foreach(\App\Models\Hotel::all() as $hotel)
                    <option value="{{ $hotel->id }}" {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>
                        {{ $hotel->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold mr-2">Filter</button>
            <a href="{{ route('event-coordinator.travel-manifests') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold">Clear</a>
        </div>
    </form>
</div>

<!-- Travel Manifests Table -->
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Travel Details</h2>
        <div class="text-sm text-gray-500">{{ $travelManifests->total() }} records found</div>
    </div>

    @if($travelManifests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flight Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Nights</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($travelManifests as $manifest)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $manifest->participant->user->first_name ?? $manifest->participant->user->name }} {{ $manifest->participant->user->last_name ?? '' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $manifest->participant->user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $manifest->participant->conference->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $manifest->hotel->name ?? 'No hotel assigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($manifest->arrival_date)
                                    <div>{{ date('M d, Y', strtotime($manifest->arrival_date)) }}</div>
                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($manifest->arrival_date)) }}</div>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($manifest->departure_date)
                                    <div>{{ date('M d, Y', strtotime($manifest->departure_date)) }}</div>
                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($manifest->departure_date)) }}</div>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($manifest->flight_info)
                                    <div class="max-w-xs truncate" title="{{ $manifest->flight_info }}">
                                        {{ $manifest->flight_info }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $manifest->extra_nights ?? 0 }} nights
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $manifest->participant->organization ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $travelManifests->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No travel manifests found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
        </div>
    @endif
</div>

<!-- Back to Dashboard -->
<div class="mt-8">
    <a href="{{ route('event-coordinator.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
        ← Back to Dashboard
    </a>
</div>
@endsection 