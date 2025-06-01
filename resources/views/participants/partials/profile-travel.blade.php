<form method="POST" action="{{ route('participants.travel.update', $participant) }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Arrival Date</label>
            <input type="datetime-local" name="arrival_date" value="{{ old('arrival_date', optional($travelDetail)->arrival_date ? \Carbon\Carbon::parse($travelDetail->arrival_date)->format('Y-m-d\TH:i') : '' ) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Departure Date</label>
            <input type="datetime-local" name="departure_date" value="{{ old('departure_date', optional($travelDetail)->departure_date ? \Carbon\Carbon::parse($travelDetail->departure_date)->format('Y-m-d\TH:i') : '' ) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Flight Info</label>
        <textarea name="flight_info" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('flight_info', optional($travelDetail)->flight_info) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Hotel</label>
        <select name="hotel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            <option value="">Select Hotel</option>
            @foreach($hotels as $hotel)
                <option value="{{ $hotel->id }}" {{ old('hotel_id', optional($travelDetail)->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Extra Nights</label>
        <input type="number" name="extra_nights" min="0" value="{{ old('extra_nights', optional($travelDetail)->extra_nights ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Travel Documents</label>
        <input type="file" name="travel_documents" class="mt-1 block w-full text-sm text-gray-500">
        @if(optional($travelDetail)->travel_documents)
            <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" target="_blank" class="text-blue-600 hover:underline mt-2 block">View Uploaded Document</a>
        @endif
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Save Travel Details</button>
    </div>
</form> 