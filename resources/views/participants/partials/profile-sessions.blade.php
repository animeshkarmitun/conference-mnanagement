<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Sessions</h3>
        <button type="button" 
                onclick="openSessionModal()" 
                class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold text-sm flex items-center transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Assign Session
        </button>
    </div>

    @php
        $sessionCount = count($sessions ?? []);
        echo "<script>console.log('Sessions count: $sessionCount');</script>";
    @endphp
    @if($sessionCount)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($sessions as $session)
                    <li class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $session->title }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                @if($session->description)
                                    <div class="text-sm text-gray-600 mt-2">{{ Str::limit($session->description, 100) }}</div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">
                                    {{ $session->pivot->role ?? 'Participant' }}
                                </span>
                                <button type="button" 
                                        onclick="removeSession({{ $session->id }})" 
                                        class="text-red-600 hover:text-red-800 p-1 transition-colors duration-200"
                                        title="Remove from session">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No sessions assigned</h3>
            <p class="mt-1 text-sm text-gray-500">This participant hasn't been assigned to any sessions yet.</p>
            <div class="mt-6">
                <button type="button" 
                        onclick="openSessionModal()" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold text-sm flex items-center mx-auto transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Assign First Session
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Session Assignment Modal -->
<div id="sessionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Assign Session</h3>
                <button type="button" onclick="closeSessionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            @php
                $assignUrl = route('participants.assign-session', $participant);
                echo "<script>console.log('Assign session URL: $assignUrl');</script>";
                echo "<script>console.log('Participant ID: " . $participant->id . "');</script>";
                echo "<script>console.log('Participant user ID: " . $participant->user_id . "');</script>";
            @endphp
            <form id="sessionAssignmentForm" method="POST" action="{{ $assignUrl }}" onsubmit="console.log('Form submitting...'); console.log('Form action: ' + this.action); return true;">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Session</label>
                    <select name="session_id" id="sessionSelect" class="w-full rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" required>
                        <option value="">Choose a session...</option>
                        @foreach(\App\Models\Session::where('conference_id', $participant->conference_id)->orderBy('start_time')->get() as $session)
                            <option value="{{ $session->id }}">{{ $session->title }} ({{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y H:i') }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role in Session</label>
                    <select name="role" class="w-full rounded-md border-gray-300 focus:border-yellow-500 focus:ring-yellow-500" required>
                        <option value="participant">Participant</option>
                        <option value="speaker">Speaker</option>
                        <option value="moderator">Moderator</option>
                        <option value="panelist">Panelist</option>
                        <option value="organizer">Organizer</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSessionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700" onclick="console.log('Submit button clicked...');">
                        Assign Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSessionModal() {
    console.log('Opening session modal...');
    document.getElementById('sessionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSessionModal() {
    document.getElementById('sessionModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function removeSession(sessionId) {
    if (confirm('Are you sure you want to remove this participant from the session?')) {
        fetch(`{{ route('participants.remove-session', $participant) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ session_id: sessionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error removing session: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing session');
        });
    }
}

// Close modal when clicking outside
document.getElementById('sessionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSessionModal();
    }
});
</script> 