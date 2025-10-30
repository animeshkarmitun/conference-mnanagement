@extends('layouts.app')

@section('title', 'Email Tracking Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Email Tracking Dashboard</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.email-tracking.export', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <button class="btn btn-warning btn-sm" onclick="cleanupEmails()">
                            <i class="fas fa-trash"></i> Cleanup Old Emails
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="conference_filter">Conference:</label>
                            <select id="conference_filter" class="form-control" onchange="filterEmails()">
                                <option value="">All Conferences</option>
                                @foreach($conferences as $conference)
                                    <option value="{{ $conference->id }}" {{ $conferenceId == $conference->id ? 'selected' : '' }}>
                                        {{ $conference->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="days_filter">Time Period:</label>
                            <select id="days_filter" class="form-control" onchange="filterEmails()">
                                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
                                <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
                                <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
                                <option value="365" {{ $days == 365 ? 'selected' : '' }}>Last year</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="type_filter">Email Type:</label>
                            <select id="type_filter" class="form-control" onchange="filterEmails()">
                                <option value="">All Types</option>
                                @php
                                    $selectedType = $type ?? '';
                                @endphp
                                @foreach(\App\Models\Email::getTypeOptions() as $t => $label)
                                    <option value="{{ $t }}" {{ $selectedType === $t ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="recipient_filter">Recipient Email:</label>
                            <input id="recipient_filter" type="text" class="form-control" value="{{ $recipientEmail ?? '' }}" placeholder="e.g. user@example.com" onkeydown="if(event.key==='Enter') filterEmails()" autocomplete="off" autocapitalize="off" spellcheck="false" />
                        </div>
                        <div class="col-md-2">
                            <label for="role_filter">User Role:</label>
                            <select id="role_filter" class="form-control" onchange="filterEmails()">
                                <option value="">All Roles</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ ($role ?? '') === $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="subject_filter">Subject:</label>
                            <input id="subject_filter" type="text" class="form-control" value="{{ $subject ?? '' }}" placeholder="Search subject..." onkeydown="if(event.key==='Enter') filterEmails()" />
                        </div>
                        <div class="col-md-2">
                            <label for="status_filter">Status:</label>
                            <select id="status_filter" class="form-control" onchange="filterEmails()">
                                <option value="">All Statuses</option>
                                @foreach(['pending','sent','delivered','opened','bounced','failed'] as $s)
                                    <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort_by">Sort By:</label>
                            <select id="sort_by" class="form-control" onchange="filterEmails()">
                                <option value="sent_at" {{ ($sortBy ?? 'sent_at') === 'sent_at' ? 'selected' : '' }}>Sent At</option>
                                <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>Created At</option>
                                <option value="status" {{ ($sortBy ?? '') === 'status' ? 'selected' : '' }}>Status</option>
                                <option value="email_type" {{ ($sortBy ?? '') === 'email_type' ? 'selected' : '' }}>Type</option>
                                <option value="recipient" {{ ($sortBy ?? '') === 'recipient' ? 'selected' : '' }}>Recipient</option>
                                <option value="id" {{ ($sortBy ?? '') === 'id' ? 'selected' : '' }}>ID</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="sort_dir">Order:</label>
                            <select id="sort_dir" class="form-control" onchange="filterEmails()">
                                <option value="desc" {{ ($sortDir ?? 'desc') === 'desc' ? 'selected' : '' }}>Desc</option>
                                <option value="asc" {{ ($sortDir ?? '') === 'asc' ? 'selected' : '' }}>Asc</option>
                            </select>
                        </div>
                        <div class="col-md-2 mt-md-0 mt-2 d-flex align-items-end">
                            <button class="btn btn-primary mt-4" onclick="refreshStats()">
                                <i class="fas fa-sync"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-envelope"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Emails</span>
                                    <span class="info-box-number" id="total-emails">{{ $stats['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-paper-plane"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sent</span>
                                    <span class="info-box-number" id="sent-emails">{{ $stats['sent'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Delivered</span>
                                    <span class="info-box-number" id="delivered-emails">{{ $stats['delivered'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-eye"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Opened</span>
                                    <span class="info-box-number" id="opened-emails">{{ $stats['opened'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bounced</span>
                                    <span class="info-box-number" id="bounced-emails">{{ $stats['bounced'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-dark"><i class="fas fa-times-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Failed</span>
                                    <span class="info-box-number" id="failed-emails">{{ $stats['failed'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Delivery Rate</h3>
                                </div>
                                <div class="card-body">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $stats['delivery_rate'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $stats['delivery_rate'] }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Open Rate</h3>
                                </div>
                                <div class="card-body">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: {{ $stats['open_rate'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $stats['open_rate'] }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bounce Rate</h3>
                                </div>
                                <div class="card-body">
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width: {{ $stats['bounce_rate'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $stats['bounce_rate'] }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Type Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Email Statistics by Type</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Total</th>
                                                    <th>Sent</th>
                                                    <th>Delivered</th>
                                                    <th>Opened</th>
                                                    <th>Delivery Rate</th>
                                                    <th>Open Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statsByType as $type => $typeStats)
                                                    @if($typeStats['total'] > 0)
                                                    <tr>
                                                        <td>{{ $typeStats['label'] }}</td>
                                                        <td>{{ $typeStats['total'] }}</td>
                                                        <td>{{ $typeStats['sent'] }}</td>
                                                        <td>{{ $typeStats['delivered'] }}</td>
                                                        <td>{{ $typeStats['opened'] }}</td>
                                                        <td>{{ $typeStats['delivery_rate'] }}%</td>
                                                        <td>{{ $typeStats['open_rate'] }}%</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Email List</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Recipient</th>
                                                    <th>Subject</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Sent At</th>
                                                    <th>Conference</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($emails as $email)
                                                <tr>
                                                    <td>{{ $email->id }}</td>
                                                    <td>
                                                        <div>{{ $email->recipient_name }}</div>
                                                        <small class="text-muted">{{ $email->recipient_email }}</small>
                                                    </td>
                                                    <td>{{ Str::limit($email->subject, 50) }}</td>
                                                    <td>
                                                        <span class="badge badge-info text-black">{{ $email->email_type }}</span>
                                                    </td>
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
                                                        <span class="badge badge-{{ $statusClass }} text-black">{{ ucfirst($email->status) }}</span>
                                                    </td>
                                                    <td>{{ $email->sent_at?->format('M d, Y H:i') }}</td>
                                                    <td>{{ $email->conference?->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.email-tracking.show', $email) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($email->status === 'failed' || $email->status === 'bounced')
                                                            <button class="btn btn-sm btn-warning" onclick="resendEmail({{ $email->id }})">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-danger" onclick="deleteEmail({{ $email->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center">
                                        {{ $emails->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cleanup Old Emails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This will permanently delete email records older than the specified number of days.</p>
                <div class="form-group">
                    <label for="cleanup_days">Delete emails older than (days):</label>
                    <input type="number" class="form-control" id="cleanup_days" value="90" min="30" max="365">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmCleanup()">Cleanup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterEmails() {
    const conferenceId = document.getElementById('conference_filter').value;
    const days = document.getElementById('days_filter').value;
    const type = document.getElementById('type_filter').value;
    const recipient = document.getElementById('recipient_filter').value;
    const role = document.getElementById('role_filter').value;
    const subject = document.getElementById('subject_filter') ? document.getElementById('subject_filter').value : '';
    const status = document.getElementById('status_filter') ? document.getElementById('status_filter').value : '';
    const sortBy = document.getElementById('sort_by') ? document.getElementById('sort_by').value : '';
    const sortDir = document.getElementById('sort_dir') ? document.getElementById('sort_dir').value : '';
    
    const params = new URLSearchParams();
    if (conferenceId) params.append('conference_id', conferenceId);
    if (days) params.append('days', days);
    if (type) params.append('type', type);
    if (recipient) params.append('recipient_email', recipient);
    if (role) params.append('role', role);
    if (subject) params.append('subject', subject);
    if (status) params.append('status', status);
    if (sortBy) params.append('sort_by', sortBy);
    if (sortDir) params.append('sort_dir', sortDir);
    
    window.location.href = '{{ route("admin.email-tracking.index") }}?' + params.toString();
}

function refreshStats() {
    // Reuse the same navigation logic so the whole dashboard reflects current filters
    filterEmails();
}

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
    if (confirm('Are you sure you want to delete this email record?')) {
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
                location.reload();
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

function cleanupEmails() {
    const modalElement = document.getElementById('cleanupModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}

function confirmCleanup() {
    const daysInput = document.getElementById('cleanup_days');
    const days = parseInt(daysInput && daysInput.value ? daysInput.value : '90', 10);
    
    fetch('{{ route("admin.email-tracking.cleanup") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ days }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            const modalElement = document.getElementById('cleanupModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during cleanup');
    });
}
</script>
@endpush
