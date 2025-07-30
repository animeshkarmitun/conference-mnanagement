@extends('layouts.app')

@section('title', 'Room Allocations')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Room Allocations</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hotel
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Room Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Arrival Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Departure Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check-in Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check-out Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($participants as $participant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                                    </td>
                                    <form method="POST" action="{{ route('admin.room-allocations.update', $participant) }}" class="contents">
                                        @csrf
                                        @method('POST')
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select name="hotel_id" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500">
                                                <option value="">Select Hotel</option>
                                                @foreach($hotels as $hotel)
                                                    <option value="{{ $hotel->id }}" {{ optional(optional($participant->roomAllocation)->hotel)->id == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text" name="room_number" value="{{ optional($participant->roomAllocation)->room_number }}" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 w-24">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ optional($participant->travelDetails)->arrival_date ? \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('M d, Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ optional($participant->travelDetails)->departure_date ? \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('M d, Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="datetime-local" name="check_in" value="{{ optional($participant->roomAllocation)->check_in ? \Carbon\Carbon::parse($participant->roomAllocation->check_in)->format('Y-m-d\TH:i') : '' }}" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 w-48">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="datetime-local" name="check_out" value="{{ optional($participant->roomAllocation)->check_out ? \Carbon\Carbon::parse($participant->roomAllocation->check_out)->format('Y-m-d\TH:i') : '' }}" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 w-48">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded font-semibold">Save</button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 