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
            <span class="ml-2 inline-block px-3 py-1 rounded-full text-xs font-semibold 
                {{ $participant->visa_status == 'approved' ? 'bg-green-100 text-green-700' : 
                   ($participant->visa_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                   ($participant->visa_status == 'issue' ? 'bg-red-100 text-red-700' : 
                   ($participant->visa_status == 'required' ? 'bg-blue-100 text-blue-700' : 
                   'bg-gray-100 text-gray-700'))) }}">
                {{ ucfirst(str_replace('_', ' ', $participant->visa_status)) }}
            </span>
        </div>
        @if($participant->visa_status == 'issue' && $participant->visa_issue_description)
        <div class="col-span-2">
            <span class="font-semibold text-gray-700">Visa Issue Description:</span>
            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">{{ $participant->visa_issue_description }}</p>
            </div>
        </div>
        @endif
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