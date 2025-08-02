<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Status</h3>
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('superadmin'))
            <div class="flex space-x-2">
                @if($participant->registration_status !== 'approved')
                    <button type="button" 
                            onclick="updateStatus('approved')" 
                            style="background-color: #059669; color: white; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; border: 2px solid #047857; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;">
                        Approve
                    </button>
                @endif
                @if($participant->registration_status !== 'rejected')
                    <button type="button" 
                            onclick="updateStatus('rejected')" 
                            style="background-color: #dc2626; color: white; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; border: 2px solid #b91c1c; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;">
                        Reject
                    </button>
                @endif
                @if($participant->registration_status !== 'pending')
                    <button type="button" 
                            onclick="updateStatus('pending')" 
                            style="background-color: #d97706; color: white; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; border: 2px solid #b45309; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s;">
                        Set Pending
                    </button>
                @endif
            </div>
        @endif
    </div>
    
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
    
    <div id="status-update-message" class="mt-4 hidden">
        <div class="p-3 rounded-md text-sm">
            <span id="status-message-text"></span>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    if (!confirm(`Are you sure you want to set the registration status to "${status}"?`)) {
        return;
    }
    
    const messageDiv = document.getElementById('status-update-message');
    const messageText = document.getElementById('status-message-text');
    
    fetch('{{ route("participants.update-status", $participant) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            registration_status: status,
            approved: status === 'approved' ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageText.textContent = data.message;
            messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-green-100 text-green-700';
            messageDiv.classList.remove('hidden');
            
            // Reload the page after a short delay to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            messageText.textContent = data.message || 'Error updating status';
            messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
            messageDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageText.textContent = 'Error updating status';
        messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
        messageDiv.classList.remove('hidden');
    });
}
</script> 