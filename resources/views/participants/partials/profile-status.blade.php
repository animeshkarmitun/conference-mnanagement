<div class="w-full min-h-96 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Status Management</h3>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="font-semibold text-gray-700">Registration Status:</span>
            <span class="ml-2 inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $participant->registration_status == 'approved' ? 'bg-green-100 text-green-700' : ($participant->registration_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                {{ ucfirst($participant->registration_status) }}
            </span>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('superadmin'))
                @if($participant->registration_status !== 'rejected')
                    <button type="button" 
                            onclick="updateRegistrationStatus('rejected')" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject
                    </button>
                @endif
                @if($participant->registration_status !== 'pending')
                    <button type="button" 
                            onclick="updateRegistrationStatus('pending')" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Set Pending
                    </button>
                @endif
            @endif
        </div>
        <div>
            <span class="font-semibold text-gray-700">CGS Approved:</span>
            <span class="ml-2 inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $participant->approved ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $participant->approved ? 'Yes' : 'No' }}
            </span>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('superadmin'))
                @if(!$participant->approved)
                    <button type="button" 
                            onclick="toggleApproved(true)" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve
                    </button>
                @else
                    <button type="button" 
                            onclick="toggleApproved(false)" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Revoke
                    </button>
                @endif
            @endif
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
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('superadmin'))
                @if($participant->visa_status !== 'approved')
                    <button type="button" 
                            onclick="updateVisaStatus('approved')" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve
                    </button>
                @endif
                @if($participant->visa_status !== 'pending')
                    <button type="button" 
                            onclick="updateVisaStatus('pending')" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Set Pending
                    </button>
                @endif
                @if($participant->visa_status !== 'issue')
                    <button type="button" 
                            onclick="updateVisaStatus('issue')" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Set Issue
                    </button>
                @endif
                @if($participant->visa_status !== 'not_required')
                    <button type="button" 
                            onclick="updateVisaStatus('not_required')" 
                            class="ml-2 inline-flex items-center px-2 py-1 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                        Not Required
                    </button>
                @endif
            @endif
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
            <span class="font-semibold text-gray-700">Travel Intent:</span>
            <span class="ml-2">{{ ucfirst($participant->travel_intent ?? 'National') }}</span>
        </div>
    </div>
    
    <div id="status-update-message" class="mt-4 hidden">
        <div class="p-3 rounded-md text-sm">
            <span id="status-message-text"></span>
        </div>
    </div>
</div>

<script>
function updateRegistrationStatus(status) {
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
            registration_status: status
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
            messageText.textContent = data.message || 'Error updating registration status';
            messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
            messageDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageText.textContent = 'Error updating registration status';
        messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
        messageDiv.classList.remove('hidden');
    });
}

function updateVisaStatus(status) {
    if (!confirm(`Are you sure you want to set the visa status to "${status.replace('_', ' ')}"?`)) {
        return;
    }
    
    const messageDiv = document.getElementById('status-update-message');
    const messageText = document.getElementById('status-message-text');
    
    fetch('{{ route("participants.update-visa-status", $participant) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            visa_status: status
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
            messageText.textContent = data.message || 'Error updating visa status';
            messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
            messageDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageText.textContent = 'Error updating visa status';
        messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
        messageDiv.classList.remove('hidden');
    });
}

function toggleApproved(approved) {
    const action = approved ? 'approve' : 'revoke';
    if (!confirm(`Are you sure you want to ${action} this participant?`)) {
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
            approved: approved ? 1 : 0
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
            messageText.textContent = data.message || 'Error updating approval status';
            messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
            messageDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageText.textContent = 'Error updating approval status';
        messageDiv.className = 'mt-4 p-3 rounded-md text-sm bg-red-100 text-red-700';
        messageDiv.classList.remove('hidden');
    });
}
</script> 