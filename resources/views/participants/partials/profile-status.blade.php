<div>
    <h3 class="text-lg font-semibold mb-4">Status</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="font-semibold text-gray-700">Registration Status:</span>
            <span class="ml-2 inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $participant->registration_status == 'approved' ? 'bg-green-100 text-green-700' : ($participant->registration_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                {{ ucfirst($participant->registration_status) }}
            </span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Approved:</span>
            <span class="ml-2">{{ $participant->approved ? 'Yes' : 'No' }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Visa Status:</span>
            <span class="ml-2">{{ ucfirst($participant->visa_status) }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Travel Form Submitted:</span>
            <span class="ml-2">{{ $participant->travel_form_submitted ? 'Yes' : 'No' }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Travel Intent:</span>
            <span class="ml-2">{{ $participant->travel_intent ? 'Yes' : 'No' }}</span>
        </div>
    </div>
</div> 