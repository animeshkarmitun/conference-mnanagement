@if(isset($error))
    <div class="text-center py-4">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h5>Error</h5>
        <p class="text-muted">{{ $error }}</p>
    </div>
@elseif($conversations->count() > 0)
    <!-- Conversation Statistics -->
    @if(isset($stats))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-envelope"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Emails</span>
                    <span class="info-box-number">{{ $stats['total_emails'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-paper-plane"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Outgoing</span>
                    <span class="info-box-number">{{ $stats['outgoing_emails'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-inbox"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Incoming</span>
                    <span class="info-box-number">{{ $stats['incoming_emails'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-comments"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Conversations</span>
                    <span class="info-box-number">{{ $stats['conversations'] }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control" id="conversation-search" placeholder="Search in conversations..." value="">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" onclick="searchConversations()">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-primary" onclick="refreshConversations()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Conversations List -->
    <div id="conversations-list">
        @foreach($conversations as $threadId => $emails)
            @php
                $firstEmail = $emails->first();
                $lastEmail = $emails->last();
                $participants = $emails->pluck('recipient_email')->merge($emails->pluck('sender_email'))->unique()->filter();
            @endphp
            <div class="conversation-item card mb-3" data-thread-id="{{ $threadId }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $firstEmail->subject }}</h5>
                        <div class="d-flex align-items-center">
                            <small class="text-muted mr-3">
                                <i class="fas fa-envelope"></i> {{ $emails->count() }} emails
                            </small>
                            <small class="text-muted mr-3">
                                <i class="fas fa-clock"></i> Last activity: {{ $firstEmail->created_at->diffForHumans() }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-users"></i> {{ $participants->count() }} participants
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-{{ $firstEmail->isOutgoing() ? 'primary' : 'secondary' }} mr-2">
                            {{ ucfirst($firstEmail->direction) }}
                        </span>
                        <button class="btn btn-sm btn-primary" onclick="viewThread('{{ $threadId }}')">
                            <i class="fas fa-eye"></i> View Thread
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="email-preview">
                                <p class="mb-2 text-muted">
                                    <strong>Latest message:</strong>
                                </p>
                                <p class="mb-1">{{ Str::limit(strip_tags($firstEmail->body), 200) }}</p>
                                <small class="text-muted">
                                    by {{ $firstEmail->sender_name }} • {{ $firstEmail->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="participants-list">
                                <p class="mb-2 text-muted">
                                    <strong>Participants:</strong>
                                </p>
                                @foreach($participants as $email)
                                    <div class="participant-item mb-1">
                                        <i class="fas fa-user-circle text-muted mr-1"></i>
                                        <small>{{ $email }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Search Results (hidden by default) -->
    <div id="search-results" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Search Results</h5>
            </div>
            <div class="card-body">
                <div id="search-results-content">
                    <!-- Search results will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-comments fa-4x text-muted mb-4"></i>
        <h4>No conversations found</h4>
        <p class="text-muted">
            @if($participantEmail)
                This participant hasn't had any email conversations yet.
            @else
                Please select a participant to view their email conversations.
            @endif
        </p>
        @if($participantEmail)
            <button class="btn btn-primary" onclick="startNewConversation()">
                <i class="fas fa-plus"></i> Start New Conversation
            </button>
        @endif
    </div>
@endif

<script>
function viewThread(threadId) {
    window.open(`{{ route('admin.email-tracking.thread', '') }}/${threadId}`, '_blank');
}

function searchConversations() {
    const searchTerm = document.getElementById('conversation-search').value.trim();
    const participantEmail = '{{ $participantEmail }}';
    
    if (!searchTerm) {
        alert('Please enter a search term');
        return;
    }
    
    // Show loading state
    document.getElementById('search-results').style.display = 'block';
    document.getElementById('search-results-content').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
    
    // Make AJAX request
    fetch(`{{ route('admin.email-tracking.search') }}?participant_email=${participantEmail}&search_term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.emails);
            } else {
                document.getElementById('search-results-content').innerHTML = 
                    '<div class="alert alert-danger">Error: ' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            document.getElementById('search-results-content').innerHTML = 
                '<div class="alert alert-danger">An error occurred while searching</div>';
        });
}

function displaySearchResults(emails) {
    if (emails.length === 0) {
        document.getElementById('search-results-content').innerHTML = 
            '<div class="text-center text-muted">No results found</div>';
        return;
    }
    
    let html = '<div class="list-group">';
    emails.forEach(email => {
        html += `
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${email.subject}</h6>
                    <small>${email.created_at}</small>
                </div>
                <p class="mb-1">${email.body_preview}</p>
                <small class="text-muted">
                    ${email.direction === 'outgoing' ? 'Sent' : 'Received'} • 
                    <a href="#" onclick="viewThread('${email.thread_id}')">View in thread</a>
                </small>
            </div>
        `;
    });
    html += '</div>';
    
    document.getElementById('search-results-content').innerHTML = html;
}

function refreshConversations() {
    location.reload();
}

function startNewConversation() {
    // This would open a modal or redirect to a new conversation form
    alert('New conversation feature coming soon!');
}
</script>


