@extends(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin') ? 'layouts.app' : 'layouts.participant')

@section('title', 'Session Details')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">{{ $session->title }}</h2>
    
    <div class="mb-4">
        <span class="font-semibold text-gray-700">Conference:</span>
        <span>{{ $session->conference->name ?? 'N/A' }}</span>
    </div>

    <div class="mb-4">
        <span class="font-semibold text-gray-700">Description:</span>
        <p class="mt-1">{{ $session->description }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Start Time:</span>
            <span>{{ \Carbon\Carbon::parse($session->start_time)->format('l, M j, Y \a\t g:i A') }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">End Time:</span>
            <span>{{ \Carbon\Carbon::parse($session->end_time)->format('l, M j, Y \a\t g:i A') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <span class="font-semibold text-gray-700">Room:</span>
            <span>{{ $session->room }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Capacity:</span>
            <span>{{ $session->capacity }}</span>
        </div>
    </div>

    <div class="mb-6">
        <span class="font-semibold text-gray-700">Participants:</span>
        <div class="mt-2 space-y-3">
            @forelse($session->participants as $participant)
                @php
                    $tracking = \App\Models\ParticipantSessionEmailTracking::where('session_id', $session->id)
                        ->where('participant_id', $participant->id)
                        ->first();
                    $emailCount = $tracking ? $tracking->email_send_count : 0;
                    $lastSent = $tracking && $tracking->last_email_sent_at ? $tracking->last_email_sent_at->format('M d, Y H:i:s') : 'Never';
                @endphp
                
                <div class="bg-gray-50 p-3 rounded-lg border">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <span class="text-gray-600 font-medium">
                                {{ $participant->user->first_name ?? $participant->user->name }} 
                                {{ $participant->user->last_name ?? '' }}
                            </span>
                            <span class="text-gray-500 text-sm ml-2">({{ $participant->user->email }})</span>
                        </div>
                        
                        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">Emails Sent</div>
                                    <div class="font-semibold text-blue-600" id="email-count-{{ $participant->id }}">{{ $emailCount }}</div>
                                    <div class="text-xs text-gray-400" id="last-sent-{{ $participant->id }}">{{ $lastSent }}</div>
                                </div>
                                <button 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium"
                                    onclick="resendEmailToParticipant({{ $session->id }}, {{ $participant->id }})"
                                    id="resend-btn-{{ $participant->id }}">
                                    <i class="fas fa-paper-plane mr-1"></i> Resend
                                </button>
                                <span id="resend-status-{{ $participant->id }}" class="text-xs"></span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-gray-500 bg-gray-50 p-3 rounded-lg">No participants assigned to this session.</div>
            @endforelse
        </div>
        
        @if($session->participants->count() > 0 && (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')))
            <div class="mt-4 pt-4 border-t">
                <button 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold"
                    onclick="resendEmailToAll({{ $session->id }})"
                    id="resendAllBtn">
                    <i class="fas fa-paper-plane mr-1"></i> Resend All
                </button>
                <span id="resendAllStatus" class="ml-3 text-sm"></span>
            </div>
        @endif
    </div>


    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
        <div class="flex justify-end space-x-4">
            <a href="{{ route('sessions.edit', $session) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Edit</a>
            <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this session?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
            </form>
        </div>
    @endif

    <div class="mt-4">
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
            <a href="{{ route('sessions.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
        @else
            <a href="{{ route('participant-dashboard') }}" class="text-gray-600 hover:text-gray-900">Back to Dashboard</a>
        @endif
    </div>
</div>

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
<script>
function resendEmailToParticipant(sessionId, participantId) {
    const btn = document.getElementById(`resend-btn-${participantId}`);
    const status = document.getElementById(`resend-status-${participantId}`);
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';
    status.textContent = '';
    
    fetch(`/sessions/${sessionId}/resend-email-to-participant/${participantId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            status.innerHTML = '<span class="text-green-600 font-semibold">✓ Sent</span>';
            
            // Update the email count and last sent time for this participant
            const emailCountElement = document.getElementById(`email-count-${participantId}`);
            const lastSentElement = document.getElementById(`last-sent-${participantId}`);
            
            if (emailCountElement) {
                emailCountElement.textContent = data.email_count;
            }
            if (lastSentElement) {
                lastSentElement.textContent = data.last_sent;
            }
            
            // Show success message for 3 seconds
            setTimeout(() => {
                status.textContent = '';
            }, 3000);
        } else {
            status.innerHTML = '<span class="text-red-600 font-semibold">✗ ' + data.message + '</span>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        status.innerHTML = '<span class="text-red-600 font-semibold">✗ Error</span>';
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Resend';
    });
}

function resendEmailToAll(sessionId) {
    const btn = document.getElementById('resendAllBtn');
    const status = document.getElementById('resendAllStatus');
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending to All...';
    status.textContent = '';
    
    fetch(`/sessions/${sessionId}/resend-email-to-all`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            status.innerHTML = '<span class="text-green-600 font-semibold">✓ ' + data.message + '</span>';
            
            // Update email counts for all participants
            if (data.results) {
                data.results.forEach(result => {
                    const emailCountElement = document.getElementById(`email-count-${result.participant_id}`);
                    if (emailCountElement) {
                        emailCountElement.textContent = result.email_count;
                    }
                    // Update last sent time to now
                    const lastSentElement = document.getElementById(`last-sent-${result.participant_id}`);
                    if (lastSentElement) {
                        const now = new Date();
                        lastSentElement.textContent = now.toLocaleDateString('en-US', { 
                            month: 'short', 
                            day: 'numeric', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                    }
                });
            }
            
            // Show success message for 5 seconds
            setTimeout(() => {
                status.textContent = '';
            }, 5000);
        } else {
            status.innerHTML = '<span class="text-red-600 font-semibold">✗ ' + data.message + '</span>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        status.innerHTML = '<span class="text-red-600 font-semibold">✗ Error sending emails</span>';
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Resend All';
    });
}

// Legacy function for backward compatibility
function resendSessionEmail(sessionId) {
    resendEmailToAll(sessionId);
}
</script>
@endif
@endsection 