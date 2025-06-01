@extends('layouts.app')

@section('title', 'Travel Manifests')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Travel Manifests</h2>
    <div class="mb-4 flex justify-end">
        <a href="#" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Download CSV</a>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Nights</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($travelDetails as $detail)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $detail->participant->user->first_name ?? $detail->participant->user->name }} {{ $detail->participant->user->last_name ?? '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ optional($detail->hotel)->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $detail->arrival_date ? \Carbon\Carbon::parse($detail->arrival_date)->format('M d, Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $detail->departure_date ? \Carbon\Carbon::parse($detail->departure_date)->format('M d, Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $detail->extra_nights ?? 0 }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 