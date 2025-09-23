@extends('layouts.app')

@section('title', 'Gmail Conversations')

@push('styles')
<style>
    .conversation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .message-bubble {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .message-bubble-reply {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Gmail API cache note -->
    <div class="alert alert-warning mb-4" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        Gmail may cache results for up to a minute. If you don't see new emails immediately, please wait and try again.
    </div>

    <!-- Connection Status and Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gmail Conversations</h3>
                    <div class="card-tools">
                        @if(isset($needsConnection) && $needsConnection)
                            <span class="badge bg-warning text-dark me-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Gmail Not Connected
                            </span>
                            <a href="{{ route('google.redirect') }}" class="btn btn-primary btn-sm">
                                <i class="fab fa-google me-1"></i>
                                Connect Gmail
                            </a>
                        @else
                            <span class="badge bg-success me-2">
                                <i class="fas fa-circle me-1"></i>
                                Connected to Gmail
                            </span>
                            <button onclick="window.location.href='{{ route('gmail.index') }}'" class="btn btn-primary btn-sm">
                                <i class="fas fa-sync-alt me-1"></i>
                                Refresh
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search/filter form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('gmail.index') }}">
                        <div class="row g-3">
                            <!-- Participant Dropdown -->
                            <div class="col-md-4">
                                <label for="participant_select" class="form-label">Select Participant</label>
                                <select id="participant_select" name="participant" class="form-select">
                                    <option value="">All Participants</option>
                                    @if(isset($participants) && $participants->count() > 0)
                                        @foreach($participants as $participant)
                                            <option value="{{ $participant->user->email }}" 
                                                    data-name="{{ $participant->user->first_name }} {{ $participant->user->last_name }}"
                                                    data-conference="{{ $participant->conference->name ?? 'N/A' }}"
                                                    {{ (($selectedParticipant ?? request('participant')) == $participant->user->email) ? 'selected' : '' }}>
                                                {{ $participant->user->first_name }} {{ $participant->user->last_name }} ({{ $participant->user->email }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <!-- Email Search Input -->
                            <div class="col-md-6">
                                <label for="email_search" class="form-label">Or Search Email</label>
                                <input type="text" 
                                       id="email_search"
                                       name="q" 
                                       value="{{ $searchQuery ?? '' }}" 
                                       placeholder="Search (e.g. from:someone, after:2024/07/01)" 
                                       class="form-control" />
                            </div>
                            
                            <!-- Search Button -->
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>
                                    Search
                                </button>
                            </div>
                            
                            <!-- Clear Button -->
                            @if (!empty($searchQuery) || !empty($selectedParticipant ?? request('participant')))
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <a href="{{ route('gmail.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times me-1"></i>
                                        Clear
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <input type="hidden" name="maxResults" value="{{ $maxResults }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Participant Info -->
    @if(!empty($selectedParticipant))
        @php
            $selectedParticipantData = $participants->firstWhere('user.email', $selectedParticipant);
        @endphp
        @if($selectedParticipantData)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">
                                    <strong>Viewing conversations for: {{ $selectedParticipantData->user->first_name }} {{ $selectedParticipantData->user->last_name }}</strong>
                                </p>
                                <p class="mb-0 small">
                                    {{ $selectedParticipantData->user->email }} • {{ $selectedParticipantData->conference->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ route('gmail.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-times me-1"></i>Clear Filter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Conversations List -->
    @if (isset($needsConnection) && $needsConnection)
        <!-- Connect Gmail State -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mx-auto w-24 h-24 bg-light rounded-circle d-flex align-items-center justify-content-center mb-4">
                            <i class="fab fa-google text-3xl text-muted"></i>
                        </div>
                        <h3 class="h5 mb-2">Connect Your Gmail Account</h3>
                        <p class="text-muted mb-4">Connect your Gmail account to view participant conversations and email communications.</p>
                        <a href="{{ route('google.redirect') }}" class="btn btn-primary">
                            <i class="fab fa-google me-2"></i>
                            Connect Gmail Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif (isset($threads) && count($threads))
        <div class="row">
            <div class="col-12">
                @foreach ($threads as $thread)
                    <div class="card conversation-card mb-4">
                        <div class="card-body">
                            <!-- Conversation Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-envelope text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">
                                            {{ app('App\\Services\\GoogleService')->getHeader($thread['messages'][0], 'Subject') ?: 'No Subject' }}
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            Thread ID: <code>{{ substr($thread['id'], 0, 8) }}...</code>
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">
                                        {{ count($thread['messages']) }} messages
                                    </span>
                                    <button onclick="toggleThread('{{ $thread['id'] }}')" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-chevron-down" id="icon-{{ $thread['id'] }}"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Conversation Snippet -->
                            <div class="mb-3">
                                <p class="text-muted">{{ $thread['snippet'] }}</p>
                            </div>

                            <!-- Messages (Collapsible) -->
                            <div id="messages-{{ $thread['id'] }}" class="d-none">
                                @foreach ($thread['messages'] as $index => $message)
                                    <div class="message-bubble rounded p-3 text-white mb-3 {{ $index % 2 == 0 ? '' : 'message-bubble-reply ms-5' }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span class="fw-medium">
                                                    {{ app('App\\Services\\GoogleService')->getHeader($message, 'From') }}
                                                </span>
                                            </div>
                                            <span class="small opacity-75">
                                                {{ $message->getInternalDate() ? date('M j, Y g:i A', $message->getInternalDate() / 1000) : 'Unknown date' }}
                                            </span>
                                        </div>
                                        <div class="small">
                                            <p class="fw-bold mb-1">
                                                {{ app('App\\Services\\GoogleService')->getHeader($message, 'Subject') }}
                                            </p>
                                            <p class="mb-0">{{ $message->getSnippet() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $thread['messages'][0]->getInternalDate() ? date('M j, Y', $thread['messages'][0]->getInternalDate() / 1000) : 'Unknown date' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-user me-1"></i>
                                        {{ app('App\\Services\\GoogleService')->getHeader($thread['messages'][0], 'From') }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('gmail.reply', $thread['id']) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-reply me-1"></i>
                                        Reply
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-archive me-1"></i>
                                        Archive
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination or Load More -->
        @if (!empty($nextPageToken))
        <div class="row">
            <div class="col-12 text-center">
                <form method="GET" action="{{ route('gmail.index') }}">
                    <input type="hidden" name="pageToken" value="{{ $nextPageToken }}">
                    <input type="hidden" name="maxResults" value="{{ $maxResults }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>
                        Load More Conversations
                    </button>
                </form>
            </div>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mx-auto w-24 h-24 bg-light rounded-circle d-flex align-items-center justify-content-center mb-4">
                            <i class="fas fa-envelope text-3xl text-muted"></i>
                        </div>
                        <h3 class="h5 mb-2">No conversations found</h3>
                        <p class="text-muted mb-4">Connect your Gmail account to view participant conversations and email communications.</p>
                        <a href="{{ route('google.redirect') }}" class="btn btn-primary">
                            <i class="fab fa-google me-2"></i>
                            Connect Gmail Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleThread(threadId) {
        const messagesDiv = document.getElementById(`messages-${threadId}`);
        const icon = document.getElementById(`icon-${threadId}`);
        
        if (messagesDiv.classList.contains('d-none')) {
            messagesDiv.classList.remove('d-none');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            messagesDiv.classList.add('d-none');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    function refreshConversations() {
        window.location.reload();
    }

    // Participant dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const participantSelect = document.getElementById('participant_select');
        const emailSearch = document.getElementById('email_search');
        
        if (participantSelect && emailSearch) {
            participantSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const participantEmail = selectedOption.value;
                
                if (participantEmail) {
                    // Auto-populate email search with participant email (both sent and received)
                    emailSearch.value = `from:${participantEmail} OR to:${participantEmail}`;
                    
                    // Show participant info
                    const participantName = selectedOption.getAttribute('data-name');
                    const conference = selectedOption.getAttribute('data-conference');
                    
                    // You could add a small info display here if needed
                    console.log(`Selected: ${participantName} (${participantEmail}) - ${conference}`);
                } else {
                    // Clear email search when "All Participants" is selected
                    emailSearch.value = '';
                }
            });
            
            // Auto-submit form when participant is selected (optional)
            participantSelect.addEventListener('change', function() {
                if (this.value) {
                    // Uncomment the line below to auto-submit when participant is selected
                    // this.form.submit();
                }
            });
        }
    });
</script>
@endpush