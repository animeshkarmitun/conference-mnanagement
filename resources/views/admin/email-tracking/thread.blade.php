@extends('layouts.app')

@section('title', 'Email Conversation Thread')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-comments"></i> Email Conversation Thread
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-secondary btn-sm" onclick="window.close()">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="replyToThread()">
                            <i class="fas fa-reply"></i> Reply
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($thread->count() > 0)
                        <div class="conversation-thread">
                            @foreach($thread as $email)
                                <div class="email-message mb-4 {{ $email->isOutgoing() ? 'outgoing' : 'incoming' }}">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @if($email->isOutgoing())
                                                        <i class="fas fa-paper-plane text-primary"></i>
                                                    @else
                                                        <i class="fas fa-inbox text-success"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $email->sender_name }}</strong>
                                                    <small class="text-muted ml-2">{{ $email->created_at->format('M d, Y H:i') }}</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-{{ $email->isOutgoing() ? 'primary' : 'secondary' }} mr-2">
                                                    {{ ucfirst($email->direction) }}
                                                </span>
                                                <span class="badge badge-{{ $email->status === 'sent' ? 'success' : ($email->status === 'delivered' ? 'info' : 'warning') }}">
                                                    {{ ucfirst($email->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="email-meta mb-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>To:</strong> {{ $email->recipient_email }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>From:</strong> {{ $email->sender_email ?? $email->user?->email ?? 'System' }}
                                                    </div>
                                                </div>
                                                @if($email->conference)
                                                <div class="row mt-1">
                                                    <div class="col-md-6">
                                                        <strong>Conference:</strong> {{ $email->conference->name }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="email-content">
                                                <h6 class="mb-2">Subject: {{ $email->subject }}</h6>
                                                <div class="email-body" style="border: 1px solid #ddd; padding: 15px; background-color: #f9f9f9; border-radius: 5px;">
                                                    {!! nl2br(e($email->body)) !!}
                                                </div>
                                            </div>
                                            
                                            @if($email->metadata && count($email->metadata) > 0)
                                            <div class="email-metadata mt-3">
                                                <button class="btn btn-sm btn-outline-info" type="button" data-toggle="collapse" data-target="#metadata-{{ $email->id }}">
                                                    <i class="fas fa-info-circle"></i> View Metadata
                                                </button>
                                                <div class="collapse mt-2" id="metadata-{{ $email->id }}">
                                                    <div class="card card-body">
                                                        <pre class="mb-0">{{ json_encode($email->metadata, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Thread Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Thread Statistics</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-primary">{{ $thread->count() }}</h4>
                                                    <small class="text-muted">Total Messages</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-success">{{ $thread->where('direction', 'outgoing')->count() }}</h4>
                                                    <small class="text-muted">Outgoing</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-info">{{ $thread->where('direction', 'incoming')->count() }}</h4>
                                                    <small class="text-muted">Incoming</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-warning">{{ $thread->where('status', 'sent')->count() }}</h4>
                                                    <small class="text-muted">Sent</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>Thread Not Found</h4>
                            <p class="text-muted">This conversation thread could not be found or has been deleted.</p>
                            <button class="btn btn-secondary" onclick="window.close()">
                                <i class="fas fa-arrow-left"></i> Go Back
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-thread {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 10px;
}

.email-message.outgoing {
    margin-left: 10%;
}

.email-message.incoming {
    margin-right: 10%;
}

.email-message .card {
    transition: all 0.3s ease;
}

.email-message:hover .card {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.email-body {
    max-height: 300px;
    overflow-y: auto;
}

/* Custom scrollbar for conversation thread */
.conversation-thread::-webkit-scrollbar {
    width: 6px;
}

.conversation-thread::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.conversation-thread::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.conversation-thread::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
function replyToThread() {
    // This would open a reply modal or redirect to a reply form
    alert('Reply functionality coming soon!');
}

// Auto-scroll to bottom of conversation
document.addEventListener('DOMContentLoaded', function() {
    const thread = document.querySelector('.conversation-thread');
    if (thread) {
        thread.scrollTop = thread.scrollHeight;
    }
});
</script>
@endsection
