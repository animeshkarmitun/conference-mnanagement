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
                        <option value="{{ $hotel->id }}" {{ optional(optional($participant->travelDetails)->hotel)->id == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="room_number" value="{{ optional($participant->travelDetails)->room_number }}" class="rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 w-24">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                {{ optional($participant->travelDetails)->arrival_date ? \Carbon\Carbon::parse($participant->travelDetails->arrival_date)->format('M d, Y H:i') : '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                {{ optional($participant->travelDetails)->departure_date ? \Carbon\Carbon::parse($participant->travelDetails->departure_date)->format('M d, Y H:i') : '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded font-semibold">Save</button>
            </td>
        </form>
    </tr>
@endforeach 