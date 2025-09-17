@extends('layouts.app')

@section('title', 'Email Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Email Details #{{ $email->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.email-tracking.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        @if($email->status === 'failed' || $email->status === 'bounced')
                            <button class="btn btn-warning btn-sm" onclick="resendEmail({{ $email->id }})">
                                <i class="fas fa-redo"></i> Resend
                            </button>
                        @endif
                        <button class="btn btn-danger btn-sm" onclick="deleteEmail({{ $email->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Email Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Email Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Recipient:</strong></td>
                                            <td>
                                                {{ $email->recipient_name }}<br>
                                                <small class="text-muted">{{ $email->recipient_email }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Subject:</strong></td>
                                            <td>{{ $email->subject }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ $email->email_type }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @php
                                                    $statusClass = match($email->status) {
                                                        'sent' => 'success',
                                                        'delivered' => 'primary',
                                                        'opened' => 'warning',
                                                        'bounced' => 'danger',
                                                        'failed' => 'dark',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($email->status) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Conference:</strong></td>
                                            <td>{{ $email->conference?->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sender:</strong></td>
                                            <td>{{ $email->user?->first_name . ' ' . $email->user?->last_name ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Template:</strong></td>
                                            <td>{{ $email->template_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Message ID:</strong></td>
                                            <td>{{ $email->message_id ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Email Body -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4 class="card-title">Email Content</h4>
                                </div>
                                <div class="card-body">
                                    <div class="email-content" style="border: 1px solid #ddd; padding: 15px; background-color: #f9f9f9; max-height: 400px; overflow-y: auto;">
                                        {!! nl2br(e($email->body)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline and Metadata -->
                        <div class="col-md-4">
                            <!-- Timeline -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Timeline</h4>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="time-label">
                                            <span class="bg-blue">{{ $email->created_at->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <div>
                                            <i class="fas fa-plus bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">{{ $email->created_at->format('H:i') }}</span>
                                                <h3 class="timeline-header">Email Created</h3>
                                                <div class="timeline-body">
                                                    Email record created and queued for sending
                                                </div>
                                            </div>
                                        </div>

                                        @if($email->sent_at)
                                        <div>
                                            <i class="fas fa-paper-plane bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time">{{ $email->sent_at->format('H:i') }}</span>
                                                <h3 class="timeline-header">Email Sent</h3>
                                                <div class="timeline-body">
                                                    Email successfully sent to recipient
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($email->delivered_at)
                                        <div>
                                            <i class="fas fa-check-circle bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">{{ $email->delivered_at->format('H:i') }}</span>
                                                <h3 class="timeline-header">Email Delivered</h3>
                                                <div class="timeline-body">
                                                    Email successfully delivered to recipient's inbox
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($email->opened_at)
                                        <div>
                                            <i class="fas fa-eye bg-yellow"></i>
                                            <div class="timeline-item">
                                                <span class="time">{{ $email->opened_at->format('H:i') }}</span>
                                                <h3 class="timeline-header">Email Opened</h3>
                                                <div class="timeline-body">
                                                    Recipient opened the email
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($email->bounced_at)
                                        <div>
                                            <i class="fas fa-exclamation-triangle bg-red"></i>
                                            <div class="timeline-item">
                                                <span class="time">{{ $email->bounced_at->format('H:i') }}</span>
                                                <h3 class="timeline-header">Email Bounced</h3>
                                                <div class="timeline-body">
                                                    Email bounced back from recipient's server
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata -->
                            @if($email->metadata)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4 class="card-title">Metadata</h4>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 rounded">{{ json_encode($email->metadata, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif

                            <!-- Error Information -->
                            @if($email->error_message)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4 class="card-title text-danger">Error Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-danger">
                                        {{ $email->error_message }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function resendEmail(emailId) {
    if (confirm('Are you sure you want to resend this email?')) {
        fetch(`{{ route('admin.email-tracking.resend', '') }}/${emailId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email resent successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resending the email');
        });
    }
}

function deleteEmail(emailId) {
    if (confirm('Are you sure you want to delete this email record? This action cannot be undone.')) {
        fetch(`{{ route('admin.email-tracking.destroy', '') }}/${emailId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Email record deleted successfully');
                window.location.href = '{{ route("admin.email-tracking.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the email');
        });
    }
}
</script>
@endpush
