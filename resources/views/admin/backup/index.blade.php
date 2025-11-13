@extends('layouts.app')

@section('title', 'Backup Management')

@push('styles')
<style>
    /* Enhanced Progress Bar */
    .progress {
        border-radius: 10px;
        overflow: hidden;
        background-color: #e9ecef;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    }
    
    .progress-bar {
        transition: width 0.6s ease;
        background-color: #28a745;
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-image: repeating-linear-gradient(
            -45deg,
            rgba(255, 255, 255, .2) 0px,
            rgba(255, 255, 255, .2) 10px,
            transparent 10px,
            transparent 20px
        );
        background-size: 50px 50px;
        animation: move 2s linear infinite;
    }
    
    @keyframes move {
        0% { background-position: 0 0; }
        100% { background-position: 50px 50px; }
    }
    
    /* Enhanced Status Badges */
    .status-badge {
        position: relative;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .status-completed {
        background-color: #28a745;
        color: white;
    }
    
    .status-in-progress {
        background-color: #ffc107;
        color: white;
        animation: pulse 2s infinite;
    }
    
    .status-failed {
        background-color: #dc3545;
        color: white;
    }
    
    .status-pending {
        background-color: #6c757d;
        color: white;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    /* Enhanced Table Design */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .table th {
        background-color: #f8f9fa;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #495057;
        padding: 1rem 0.75rem;
    }
    
    .table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-color: #e9ecef;
    }
    
    /* Modern Cards */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
    
    /* Modern Buttons */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        border-width: 1px;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    /* Compact Layout */
    .container-fluid {
        padding: 1rem;
    }
    
    .row {
        margin-bottom: 1rem;
    }
    
    .row.g-3 {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }
    
    /* Modern Table */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }
    
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-color: #e9ecef;
        font-size: 0.875rem;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 991.98px) {
        .btn-group {
            flex-direction: column;
            width: 100%;
        }
        
        .btn-group .btn {
            border-radius: 8px !important;
            margin-bottom: 0.5rem;
            width: 100%;
        }
        
        .btn-group .btn:last-child {
            margin-bottom: 0;
        }
        
        .quick-actions .btn {
            min-width: auto;
            width: 100%;
            margin: 0.25rem 0;
        }
    }
    
    /* Loading States */
    .loading-skeleton {
        background-color: #f0f0f0;
        background-image: repeating-linear-gradient(
            90deg,
            #f0f0f0 0px,
            #f0f0f0 40px,
            #e0e0e0 40px,
            #e0e0e0 80px
        );
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Status Indicators */
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<!-- UI Enhancement Test -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-database me-2"></i>Backup Management
                    </h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                            <i class="fas fa-plus me-1"></i>Create Backup
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="refreshStats()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1" id="totalBackups">{{ $stats['total_backups'] }}</h5>
                                        <small class="opacity-75">Total Backups</small>
                                    </div>
                                    <i class="fas fa-database fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1" id="completedBackups">{{ $stats['completed_backups'] }}</h5>
                                        <small class="opacity-75">Completed</small>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1" id="successRate">{{ $stats['success_rate'] }}%</h5>
                                        <small class="opacity-75">Success Rate</small>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1" id="totalSize">{{ $stats['formatted_size'] }}</h5>
                                        <small class="opacity-75">Total Size</small>
                                    </div>
                                    <i class="fas fa-hdd fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-muted">
                                            <i class="fas fa-bolt me-2"></i>Quick Actions
                                        </h6>
                                        <span class="badge bg-success" id="statusIndicator">
                                            <i class="fas fa-circle me-1"></i>Ready
                                        </span>
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-lg-4 col-md-4 col-sm-6">
                                            <button type="button" class="btn btn-success w-100 btn-sm" onclick="createInstantBackup()" title="Create a quick full backup">
                                                <i class="fas fa-bolt me-1"></i>Instant Backup
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6">
                                            <button type="button" class="btn btn-warning w-100 btn-sm" onclick="showCleanupModal()" title="Clean up old backups">
                                                <i class="fas fa-broom me-1"></i>Cleanup
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6">
                                            <button type="button" class="btn btn-outline-secondary w-100 btn-sm" onclick="exportBackupList()" title="Export backup list">
                                                <i class="fas fa-download me-1"></i>Export
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Section -->
                    <div class="row mb-4" id="progressSection" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cog fa-spin me-2"></i>Backup in Progress
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span id="progressText">Creating backup...</span>
                                            <span id="progressPercent">0%</span>
                                        </div>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                                 id="progressBar" 
                                                 role="progressbar" 
                                                 style="width: 0%" 
                                                 aria-valuenow="0" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="progressMessage">Please wait while the backup is being created...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-hdd text-primary fa-2x mb-2"></i>
                                    <h6 class="mb-1">Used Space</h6>
                                    <h5 class="text-primary mb-1" id="usedSpace">{{ $storageInfo['formatted_used_space'] }}</h5>
                                    <small class="text-muted" id="usedSpacePercent">0% of available</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-files text-info fa-2x mb-2"></i>
                                    <h6 class="mb-1">File Count</h6>
                                    <h5 class="text-info mb-1" id="fileCount">{{ $storageInfo['file_count'] }}</h5>
                                    <small class="text-muted" id="fileCountTrend">No change</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                                    <h6 class="mb-1">Last Backup</h6>
                                    <h5 class="text-warning mb-1" id="lastBackup">{{ $stats['last_backup'] ? $stats['last_backup']->format('M d, Y') : 'Never' }}</h5>
                                    <small class="text-muted" id="lastBackupTime">{{ $stats['last_backup'] ? $stats['last_backup']->format('H:i:s') : 'No backups yet' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-weight text-success fa-2x mb-2"></i>
                                    <h6 class="mb-1">Last Size</h6>
                                    <h5 class="text-success mb-1" id="lastBackupSize">{{ $stats['last_backup_size'] ?? 'N/A' }}</h5>
                                    <small class="text-muted" id="sizeTrend">No data</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Recent Backups</h5>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showRestoreHistory()">
                                        <i class="fas fa-history me-1"></i>Restore History
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Desktop Table View -->
                                    <div class="table-responsive d-none d-lg-block">
                                        <table class="table table-hover" id="backupsTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Size</th>
                                                    <th>Created</th>
                                                    <th>Creator</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($backups as $backup)
                                                <tr id="backup-row-{{ $backup->id }}">
                                                    <td><strong>#{{ $backup->id }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-{{ $backup->backup_type === 'full' ? 'primary' : ($backup->backup_type === 'emergency' ? 'danger' : 'secondary') }}">
                                                            {{ ucfirst($backup->backup_type) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge status-{{ str_replace('_', '-', $backup->status) }}">
                                                            @if($backup->status === 'in_progress')
                                                                <i class="fas fa-spinner fa-spin me-1"></i>
                                                            @elseif($backup->status === 'completed')
                                                                <i class="fas fa-check-circle me-1"></i>
                                                            @elseif($backup->status === 'failed')
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                            @elseif($backup->status === 'pending')
                                                                <i class="fas fa-clock me-1"></i>
                                                            @endif
                                                            {{ ucfirst(str_replace('_', ' ', $backup->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $backup->formatted_file_size }}</td>
                                                    <td>
                                                        <small class="text-muted">{{ $backup->created_at->format('M d, Y') }}</small><br>
                                                        <small>{{ $backup->created_at->format('H:i:s') }}</small>
                                                    </td>
                                                    <td>{{ $backup->creator ? $backup->creator->first_name . ' ' . $backup->creator->last_name : 'System' }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if($backup->status === 'completed')
                                                            <button type="button" class="btn btn-outline-primary" onclick="downloadBackup({{ $backup->id }})" title="Download Backup">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-success" onclick="restoreBackup({{ $backup->id }})" title="Restore">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                            @endif
                                                            <button type="button" class="btn btn-outline-danger" onclick="deleteBackup({{ $backup->id }})" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="fas fa-database fa-3x mb-3 text-muted"></i><br>
                                                        No backups found
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Mobile Card View -->
                                    <div class="d-lg-none">
                                        @forelse($backups as $backup)
                                        <div class="card mb-3" id="backup-card-{{ $backup->id }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">Backup #{{ $backup->id }}</h6>
                                                    <span class="badge bg-{{ $backup->backup_type === 'full' ? 'primary' : ($backup->backup_type === 'emergency' ? 'danger' : 'secondary') }}">
                                                        {{ ucfirst($backup->backup_type) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="row mb-2">
                                                    <div class="col-6">
                                                        <small class="text-muted">Status:</small><br>
                                                        <span class="status-badge status-{{ str_replace('_', '-', $backup->status) }}">
                                                            @if($backup->status === 'in_progress')
                                                                <i class="fas fa-spinner fa-spin me-1"></i>
                                                            @elseif($backup->status === 'completed')
                                                                <i class="fas fa-check-circle me-1"></i>
                                                            @elseif($backup->status === 'failed')
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                            @elseif($backup->status === 'pending')
                                                                <i class="fas fa-clock me-1"></i>
                                                            @endif
                                                            {{ ucfirst(str_replace('_', ' ', $backup->status)) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Size:</small><br>
                                                        <strong>{{ $backup->formatted_file_size }}</strong>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted">Created:</small><br>
                                                        <small>{{ $backup->created_at->format('M d, Y H:i') }}</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Creator:</small><br>
                                                        <small>{{ $backup->creator ? $backup->creator->first_name . ' ' . $backup->creator->last_name : 'System' }}</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="btn-group w-100" role="group">
                                                    @if($backup->status === 'completed')
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="downloadBackup({{ $backup->id }})" title="Download Backup">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="restoreBackup({{ $backup->id }})" title="Restore">
                                                        <i class="fas fa-undo me-1"></i>Restore
                                                    </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteBackup({{ $backup->id }})" title="Delete">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-database fa-3x mb-3 text-muted"></i><br>
                                            <h5>No backups found</h5>
                                            <p>Create your first backup to get started.</p>
                                        </div>
                                        @endforelse
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

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBackupForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="backupType" class="form-label">Backup Type</label>
                        <select class="form-select" id="backupType" name="type" required>
                            <option value="">Select backup type...</option>
                            @foreach($backupTypes as $key => $description)
                            <option value="{{ $key }}">{{ $description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Creating a backup may take several minutes depending on your database size.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-database me-1"></i>Create Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore from Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="restoreForm">
                <input type="hidden" id="restoreBackupId" name="backup_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="restoreType" class="form-label">Restore Type</label>
                        <select class="form-select" id="restoreType" name="type" required>
                            <option value="">Select restore type...</option>
                            <option value="full">Full Restore (Complete database)</option>
                            <option value="selective">Selective Restore (Specific tables only)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tablesSection" style="display: none;">
                        <label for="restoreTables" class="form-label">Select Tables</label>
                        <select class="form-select" id="restoreTables" name="tables[]" multiple>
                            <!-- Tables will be loaded dynamically -->
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple tables</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This operation will overwrite current data. A safety backup will be created automatically.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-1"></i>Restore
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-broom me-2"></i>Cleanup Backups
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cleanupForm">
                <div class="modal-body">
                    <!-- Cleanup Type Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">What would you like to clean up?</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="cleanupFiles" name="cleanup_types[]" value="files">
                                            <label class="form-check-label fw-bold" for="cleanupFiles">
                                                <i class="fas fa-file me-2 text-primary"></i>Backup Files
                                            </label>
                                        </div>
                                        <small class="text-muted">Remove physical backup files from storage</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="cleanupDatabase" name="cleanup_types[]" value="database">
                                            <label class="form-check-label fw-bold" for="cleanupDatabase">
                                                <i class="fas fa-database me-2 text-info"></i>Database Records
                                            </label>
                                        </div>
                                        <small class="text-muted">Remove backup records from database</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Retention Period -->
                    <div class="mb-4">
                        <label for="retentionDays" class="form-label fw-bold">Retention Period</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <select class="form-select" id="retentionPeriod" name="retention_period" onchange="updateRetentionDays()">
                                    <option value="custom">Custom Period</option>
                                    <option value="7">Last 7 days</option>
                                    <option value="14">Last 2 weeks</option>
                                    <option value="30" selected>Last month</option>
                                    <option value="90">Last 3 months</option>
                                    <option value="180">Last 6 months</option>
                                    <option value="365">Last year</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control" id="retentionDays" name="days" value="30" min="1" max="365" placeholder="Enter days">
                            </div>
                        </div>
                        <div class="form-text">Backups older than this period will be deleted</div>
                    </div>

                    <!-- Additional Options -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Additional Options</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="cleanupFailed" name="cleanup_failed" value="1">
                            <label class="form-check-label" for="cleanupFailed">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Include failed backups
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="cleanupInProgress" name="cleanup_in_progress" value="1">
                            <label class="form-check-label" for="cleanupInProgress">
                                <i class="fas fa-spinner me-2 text-info"></i>Include incomplete backups
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="createSafetyBackup" name="create_safety_backup" value="1" checked>
                            <label class="form-check-label" for="createSafetyBackup">
                                <i class="fas fa-shield-alt me-2 text-success"></i>Create safety backup before cleanup
                            </label>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-4" id="cleanupPreview" style="display: none;">
                        <label class="form-label fw-bold">Cleanup Preview</label>
                        <div class="alert alert-light border">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Backups to be deleted:</span>
                                <span class="badge bg-danger" id="previewCount">0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>Total size to be freed:</span>
                                <span class="badge bg-info" id="previewSize">0 MB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. Make sure you have important backups stored elsewhere.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" onclick="previewCleanup()">
                        <i class="fas fa-eye me-1"></i>Preview
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-broom me-1"></i>Execute Cleanup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Backup Details Modal -->
<div class="modal fade" id="backupDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Backup Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="backupDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore History Modal -->
<div class="modal fade" id="restoreHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="restoreHistoryTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Backup Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Tables</th>
                                <th>Created</th>
                                <th>Creator</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Content will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Global variables
let availableTables = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadAvailableTables();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Create backup form
    document.getElementById('createBackupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createBackup();
    });

    // Restore form
    document.getElementById('restoreForm').addEventListener('submit', function(e) {
        e.preventDefault();
        executeRestore();
    });

    // Cleanup form
    document.getElementById('cleanupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        executeCleanup();
    });

    // Restore type change
    document.getElementById('restoreType').addEventListener('change', function() {
        const tablesSection = document.getElementById('tablesSection');
        if (this.value === 'selective') {
            tablesSection.style.display = 'block';
            loadTablesForRestore();
        } else {
            tablesSection.style.display = 'none';
        }
    });
}

// Create backup
function createBackup() {
    const form = document.getElementById('createBackupForm');
    const formData = new FormData(form);
    
    showProgressBar();
    startProgressAnimation();
    
    fetch('{{ route("admin.backup.create") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideProgressBar();
        if (data.success) {
            showAlert('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('createBackupModal')).hide();
            // Start polling for updates
            startStatusPolling();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideProgressBar();
        console.error('=== BACKUP ERROR DETAILS ===');
        console.error('Error object:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        console.error('Response status:', error.status);
        console.error('Response text:', error.responseText);
        console.error('============================');
        showAlert('error', 'An error occurred while creating backup. Check console for details.');
    });
}

// Create instant backup (quick full backup)
function createInstantBackup() {
    if (confirm('Create an instant full backup? This will backup the entire database immediately.')) {
        showProgressBar();
        startProgressAnimation();
        
        const formData = new FormData();
        formData.append('type', 'full');
        
        fetch('{{ route("admin.backup.create") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            hideProgressBar();
            console.log('Response data:', data);
            if (data.success) {
                showAlert('success', 'Instant backup created successfully!');
                // Immediately refresh the backup list to show the new backup
                setTimeout(() => {
                    refreshBackupList();
                    // Also refresh stats (which will also refresh the list, but that's ok - it ensures consistency)
                    fetch('{{ route("admin.backup.stats") }}')
                    .then(response => response.json())
                    .then(statsData => {
                        if (statsData.success) {
                            updateStats(statsData.backup_stats, statsData.storage_info);
                        }
                    })
                    .catch(error => {
                        console.error('Error refreshing stats:', error);
                    });
                }, 500); // Small delay to ensure backup is recorded in database
                // Start polling for updates to track progress
                startStatusPolling();
            } else {
                console.error('Backup failed with message:', data.message);
                showAlert('error', data.message || 'Backup failed for unknown reason');
            }
        })
        .catch(error => {
            hideProgressBar();
            console.error('=== BACKUP ERROR DETAILS ===');
            console.error('Error object:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            console.error('Response status:', error.status);
            console.error('Response text:', error.responseText);
            console.error('============================');
            showAlert('error', 'An error occurred while creating instant backup. Check console for details.');
        });
    }
}

// Download backup file
function downloadBackup(id) {
    // Create a download link and trigger it
    const downloadUrl = `{{ url('admin/backup') }}/${id}/download`;
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showAlert('success', 'Download started...');
}

// View backup details (kept for backward compatibility if needed elsewhere)
function viewBackup(id) {
    showLoading('Loading backup details...');
    
    fetch(`{{ url('admin/backup') }}/${id}`)
    .then(response => {
        console.log('View backup response status:', response.status);
        return response.json();
    })
    .then(data => {
        hideLoading();
        console.log('View backup response data:', data);
        if (data.success) {
            displayBackupDetails(data.backup);
            bootstrap.Modal.getInstance(document.getElementById('backupDetailsModal')).show();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('=== VIEW BACKUP ERROR DETAILS ===');
        console.error('Error object:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        console.error('==================================');
        showAlert('error', 'An error occurred while loading backup details. Check console for details.');
    });
}

// Display backup details
function displayBackupDetails(backup) {
    const content = document.getElementById('backupDetailsContent');
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>ID:</strong></td><td>${backup.id}</td></tr>
                    <tr><td><strong>Type:</strong></td><td>${backup.type}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${backup.status === 'completed' ? 'success' : 'warning'}">${backup.status}</span></td></tr>
                    <tr><td><strong>File Name:</strong></td><td>${backup.file_name}</td></tr>
                    <tr><td><strong>File Size:</strong></td><td>${backup.file_size}</td></tr>
                    <tr><td><strong>Checksum:</strong></td><td><code>${backup.checksum}</code></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Timing Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Created:</strong></td><td>${backup.created_at}</td></tr>
                    <tr><td><strong>Completed:</strong></td><td>${backup.completed_at || 'N/A'}</td></tr>
                    <tr><td><strong>Duration:</strong></td><td>${backup.duration || 'N/A'}</td></tr>
                    <tr><td><strong>Creator:</strong></td><td>${backup.creator}</td></tr>
                </table>
            </div>
        </div>
        ${backup.metadata ? `
        <div class="row mt-3">
            <div class="col-12">
                <h6>Metadata</h6>
                <pre class="bg-light p-3 rounded">${JSON.stringify(backup.metadata, null, 2)}</pre>
            </div>
        </div>
        ` : ''}
        ${backup.restore_records && backup.restore_records.length > 0 ? `
        <div class="row mt-3">
            <div class="col-12">
                <h6>Restore History</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Creator</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${backup.restore_records.map(restore => `
                        <tr>
                            <td>${restore.id}</td>
                            <td>${restore.type}</td>
                            <td><span class="badge bg-${restore.status === 'completed' ? 'success' : 'warning'}">${restore.status}</span></td>
                            <td>${restore.created_at}</td>
                            <td>${restore.creator}</td>
                        </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
        ` : ''}
    `;
}

// Restore backup
function restoreBackup(id) {
    console.log('Restore button clicked for backup ID:', id);
    
    // Check if modal element exists
    const modal = document.getElementById('restoreModal');
    if (!modal) {
        console.error('Restore modal not found');
        showAlert('error', 'Restore modal not found');
        return;
    }
    
    // Set the backup ID
    const backupIdInput = document.getElementById('restoreBackupId');
    if (!backupIdInput) {
        console.error('Restore backup ID input not found');
        showAlert('error', 'Restore form not properly configured');
        return;
    }
    
    backupIdInput.value = id;
    console.log('Backup ID set to:', id);
    
    // Show the modal
    try {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
        console.log('Restore modal shown successfully');
    } catch (error) {
        console.error('Error showing restore modal:', error);
        showAlert('error', 'Failed to show restore dialog');
    }
}

// Execute restore
function executeRestore() {
    console.log('Execute restore function called');
    
    const form = document.getElementById('restoreForm');
    if (!form) {
        console.error('Restore form not found');
        showAlert('error', 'Restore form not found');
        return;
    }
    
    const formData = new FormData(form);
    const backupId = document.getElementById('restoreBackupId').value;
    
    console.log('Restore form data:', {
        backupId: backupId,
        type: formData.get('type'),
        tables: formData.get('tables')
    });
    
    if (!backupId) {
        console.error('No backup ID found');
        showAlert('error', 'No backup selected');
        return;
    }
    
    if (!formData.get('type')) {
        console.error('No restore type selected');
        showAlert('error', 'Please select a restore type');
        return;
    }
    
    // Show loading with longer timeout message since restore can take time
    showLoading('Starting restore operation... This may take several minutes. Please wait...');
    
    // Increase timeout for restore operation (10 minutes)
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 600000); // 10 minutes
    
    fetch(`{{ url('admin/backup') }}/${document.getElementById('restoreBackupId').value}/restore`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Restore response status:', response.status);
        console.log('Restore response headers:', response.headers);
        
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Restore failed');
            });
        }
        
        return response.json();
    })
    .then(data => {
        hideLoading();
        console.log('Restore response data:', data);
        console.log('Data success:', data.success);
        console.log('Data message:', data.message);
        
        if (data.success) {
            console.log('Restore completed successfully');
            showAlert('success', data.message || 'Restore completed successfully!');
            console.log('Hiding restore modal');
            
            // Try to get the modal instance and hide it
            const modalElement = document.getElementById('restoreModal');
            if (modalElement) {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                    console.log('Modal hidden successfully');
                } else {
                    console.log('No modal instance found, trying to create new one');
                    const newModalInstance = new bootstrap.Modal(modalElement);
                    newModalInstance.hide();
                }
            } else {
                console.error('Restore modal element not found');
            }
            
            // Refresh backup list and stats
            setTimeout(() => {
                refreshBackupList();
                fetch('{{ route("admin.backup.stats") }}')
                .then(response => response.json())
                .then(statsData => {
                    if (statsData.success) {
                        updateStats(statsData.backup_stats, statsData.storage_info);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing stats:', error);
                });
                // Reload page to show updated status
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }, 500);
        } else {
            console.log('Restore failed, showing error alert');
            showAlert('error', data.message || 'Restore failed');
            
            // Close modal on error too
            const modalElement = document.getElementById('restoreModal');
            if (modalElement) {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                    console.log('Modal hidden after error');
                } else {
                    const newModalInstance = new bootstrap.Modal(modalElement);
                    newModalInstance.hide();
                }
            }
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        hideLoading();
        console.error('=== RESTORE ERROR DETAILS ===');
        console.error('Error object:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        console.error('=============================');
        
        if (error.name === 'AbortError' || error.message.includes('timeout')) {
            showAlert('error', 'Restore operation timed out. The restore may still be in progress. Please check the restore status manually or wait a few minutes and refresh the page.');
        } else {
            showAlert('error', 'Restore failed: ' + (error.message || 'Unknown error occurred'));
        }
        
        // Close modal on error too
        const modalElement = document.getElementById('restoreModal');
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
                console.log('Modal hidden after catch error');
            } else {
                const newModalInstance = new bootstrap.Modal(modalElement);
                newModalInstance.hide();
            }
        }
        
        // Refresh page after error to show current status
        setTimeout(() => {
            location.reload();
        }, 2000);
    });
}

// Delete backup
function deleteBackup(id) {
    if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        showLoading('Deleting backup...');
        
        fetch(`{{ route("admin.backup.index") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'An error occurred while deleting backup');
            console.error('Error:', error);
        });
    }
}

// Show cleanup modal
function showCleanupModal() {
    // Reset form to default values
    document.getElementById('cleanupForm').reset();
    document.getElementById('retentionPeriod').value = '30';
    document.getElementById('retentionDays').value = '30';
    document.getElementById('cleanupFiles').checked = true;
    document.getElementById('cleanupDatabase').checked = true;
    document.getElementById('createSafetyBackup').checked = true;
    document.getElementById('cleanupPreview').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('cleanupModal'));
    modal.show();
}

// Update retention days based on selection
function updateRetentionDays() {
    const select = document.getElementById('retentionPeriod');
    const input = document.getElementById('retentionDays');
    
    if (select.value !== 'custom') {
        input.value = select.value;
        input.disabled = true;
    } else {
        input.disabled = false;
        input.focus();
    }
}

// Preview cleanup operation
function previewCleanup() {
    const form = document.getElementById('cleanupForm');
    const formData = new FormData(form);
    
    // Validate form
    const cleanupTypes = formData.getAll('cleanup_types[]');
    if (cleanupTypes.length === 0) {
        showAlert('warning', 'Please select at least one cleanup type (Files or Database)');
        return;
    }
    
    showLoading('Analyzing backups for cleanup...');
    
    fetch('{{ route("admin.backup.cleanup.preview") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            document.getElementById('previewCount').textContent = data.backup_count || '0';
            document.getElementById('previewSize').textContent = data.total_size || '0 MB';
            document.getElementById('cleanupPreview').style.display = 'block';
            
            if (data.backup_count === 0) {
                showAlert('info', 'No backups found matching the cleanup criteria');
            }
        } else {
            showAlert('error', data.message || 'Failed to preview cleanup');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred while previewing cleanup');
        console.error('Error:', error);
    });
}

// Execute cleanup
function executeCleanup() {
    const form = document.getElementById('cleanupForm');
    const formData = new FormData(form);
    
    // Validate form
    const cleanupTypes = formData.getAll('cleanup_types[]');
    if (cleanupTypes.length === 0) {
        showAlert('warning', 'Please select at least one cleanup type (Files or Database)');
        return;
    }
    
    // Create confirmation message
    const types = cleanupTypes.join(' and ');
    const days = document.getElementById('retentionDays').value;
    const confirmMessage = `Are you sure you want to clean up ${types} older than ${days} days? This action cannot be undone.`;
    
    if (confirm(confirmMessage)) {
        showLoading('Executing cleanup operation...');
        
        fetch('{{ route("admin.backup.cleanup") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('cleanupModal'));
                if (modal) {
                    modal.hide();
                }
                location.reload();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'An error occurred while cleaning up backups');
            console.error('Error:', error);
        });
    }
}

// Show restore history
function showRestoreHistory() {
    showLoading('Loading restore history...');
    
    fetch('{{ route("admin.backup.restore.history") }}')
    .then(response => {
        // Check if response is ok (status 200-299)
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                } catch (e) {
                    if (e instanceof Error && e.message.includes('HTTP error')) {
                        throw e;
                    }
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text.substring(0, 200)}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            displayRestoreHistory(data.restores || []);
            
            // Get or create modal instance
            const modalElement = document.getElementById('restoreHistoryModal');
            if (!modalElement) {
                console.error('Restore history modal element not found');
                showAlert('error', 'Restore history modal not found');
                return;
            }
            
            // Try to get existing instance, or create a new one
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            modalInstance.show();
        } else {
            showAlert('error', data.message || 'Failed to load restore history');
        }
    })
    .catch(error => {
        hideLoading();
        const errorMessage = error.message || 'An error occurred while loading restore history';
        showAlert('error', errorMessage);
        console.error('Restore history error:', error);
    });
}

// Display restore history
function displayRestoreHistory(restores) {
    const tbody = document.querySelector('#restoreHistoryTable tbody');
    if (!tbody) {
        console.error('Restore history table tbody not found');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (!restores || restores.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No restore operations found</td></tr>';
        return;
    }
    
    restores.forEach(restore => {
        if (!restore) {
            return;
        }
        
        const row = document.createElement('tr');
        
        // Safely get status badge class
        let statusBadgeClass = 'secondary';
        if (restore.status === 'completed') {
            statusBadgeClass = 'success';
        } else if (restore.status === 'failed') {
            statusBadgeClass = 'danger';
        } else if (restore.status === 'in_progress') {
            statusBadgeClass = 'warning';
        } else if (restore.status === 'pending') {
            statusBadgeClass = 'info';
        }
        
        // Safely get type badge class
        const typeBadgeClass = restore.type === 'full' ? 'primary' : 'secondary';
        
        row.innerHTML = `
            <td>${restore.id || 'N/A'}</td>
            <td>${restore.backup_date || 'N/A'}</td>
            <td><span class="badge bg-${typeBadgeClass}">${restore.type || 'unknown'}</span></td>
            <td><span class="badge bg-${statusBadgeClass}">${restore.status || 'unknown'}</span></td>
            <td>${restore.tables_restored || 'All tables'}</td>
            <td>${restore.created_at || 'N/A'}</td>
            <td>${restore.creator || 'Unknown'}</td>
            <td>${restore.duration || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });
}

// Load available tables
function loadAvailableTables() {
    fetch('{{ route("admin.backup.tables") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            availableTables = data.tables;
        }
    })
    .catch(error => {
        console.error('Error loading tables:', error);
    });
}

// Load tables for restore
function loadTablesForRestore() {
    const select = document.getElementById('restoreTables');
    select.innerHTML = '';
    
    availableTables.forEach(table => {
        const option = document.createElement('option');
        option.value = table;
        option.textContent = table;
        select.appendChild(option);
    });
}

// Refresh statistics and backup list
function refreshStats() {
    showLoading('Refreshing statistics...');
    
    fetch('{{ route("admin.backup.stats") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStats(data.backup_stats, data.storage_info);
            // Refresh the backup list after updating stats
            refreshBackupList();
            hideLoading();
            showAlert('success', 'Statistics and backup list refreshed successfully');
        } else {
            hideLoading();
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred while refreshing statistics');
        console.error('Error:', error);
    });
}

// Update statistics display
function updateStats(backupStats, storageInfo) {
    document.getElementById('totalBackups').textContent = backupStats.total_backups;
    document.getElementById('completedBackups').textContent = backupStats.completed_backups;
    document.getElementById('successRate').textContent = backupStats.success_rate + '%';
    document.getElementById('totalSize').textContent = backupStats.formatted_size;
    document.getElementById('usedSpace').textContent = storageInfo.formatted_used_space;
    document.getElementById('fileCount').textContent = storageInfo.file_count;
    document.getElementById('lastBackup').textContent = backupStats.last_backup ? new Date(backupStats.last_backup).toLocaleString() : 'Never';
    document.getElementById('lastBackupSize').textContent = backupStats.last_backup_size || 'N/A';
}



// Progress bar functions
function showProgressBar() {
    document.getElementById('progressSection').style.display = 'block';
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('progressPercent').textContent = '0%';
    document.getElementById('progressText').textContent = 'Creating backup...';
    document.getElementById('progressMessage').textContent = 'Please wait while the backup is being created...';
    updateStatusIndicator('working');
}

function hideProgressBar() {
    document.getElementById('progressSection').style.display = 'none';
    updateStatusIndicator('ready');
}

function updateProgress(percent, text, message) {
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressPercent').textContent = percent + '%';
    document.getElementById('progressText').textContent = text;
    document.getElementById('progressMessage').textContent = message;
}

function startProgressAnimation() {
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        
        updateProgress(Math.floor(progress), 'Creating backup...', 'Please wait while the backup is being created...');
        
        if (progress >= 90) {
            clearInterval(interval);
        }
    }, 500);
}

// Status polling for real-time updates
let statusPollingInterval = null;
let lastBackupCount = 0;

function startStatusPolling() {
    if (statusPollingInterval) {
        clearInterval(statusPollingInterval);
    }
    
    // Get initial backup count
    fetch('{{ route("admin.backup.stats") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lastBackupCount = data.backup_stats.total_backups;
        }
    })
    .catch(error => {
        console.error('Error getting initial backup count:', error);
    });
    
    statusPollingInterval = setInterval(() => {
        fetch('{{ route("admin.backup.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStats(data.backup_stats, data.storage_info);
                
                // Refresh backup list if count changed or if backups completed
                if (data.backup_stats.total_backups !== lastBackupCount) {
                    refreshBackupList();
                    lastBackupCount = data.backup_stats.total_backups;
                }
                
                // Check if any backups are still in progress
                const hasInProgress = data.backup_stats.total_backups > 0 && 
                                    data.backup_stats.completed_backups < data.backup_stats.total_backups;
                
                // Refresh list when backup completes
                if (!hasInProgress && lastBackupCount > 0) {
                    refreshBackupList();
                    clearInterval(statusPollingInterval);
                    statusPollingInterval = null;
                    lastBackupCount = 0;
                }
            }
        })
        .catch(error => {
            console.error('Status polling error:', error);
        });
    }, 2000); // Poll every 2 seconds
}

function stopStatusPolling() {
    if (statusPollingInterval) {
        clearInterval(statusPollingInterval);
        statusPollingInterval = null;
    }
}

// Restore status polling
let restorePollingInterval = null;

function startRestoreStatusPolling(restoreId) {
    console.log('Starting restore status polling for restore ID:', restoreId);
    
    if (restorePollingInterval) {
        clearInterval(restorePollingInterval);
    }
    
    let pollCount = 0;
    const maxPolls = 600; // Poll for up to 10 minutes (600 * 1 second)
    
    // Show a notification that restore is in progress
    showAlert('info', 'Restore operation started. Please wait...');
    
    restorePollingInterval = setInterval(() => {
        pollCount++;
        
        // Check restore status via restore status endpoint
        fetch(`{{ url('admin/backup/restore/status') }}/${restoreId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.restore) {
                const restore = data.restore;
                console.log('Restore status:', restore.status, 'Poll count:', pollCount);
                
                if (restore.status === 'completed') {
                    clearInterval(restorePollingInterval);
                    restorePollingInterval = null;
                    showAlert('success', 'Restore completed successfully!');
                    // Refresh backup list and stats
                    setTimeout(() => {
                        refreshBackupList();
                        fetch('{{ route("admin.backup.stats") }}')
                        .then(response => response.json())
                        .then(statsData => {
                            if (statsData.success) {
                                updateStats(statsData.backup_stats, statsData.storage_info);
                            }
                        })
                        .catch(error => {
                            console.error('Error refreshing stats:', error);
                        });
                        // Refresh page after a delay
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }, 1000);
                } else if (restore.status === 'failed') {
                    clearInterval(restorePollingInterval);
                    restorePollingInterval = null;
                    showAlert('error', 'Restore failed: ' + (restore.error_message || 'Unknown error'));
                    // Refresh backup list and stats
                    setTimeout(() => {
                        refreshBackupList();
                        fetch('{{ route("admin.backup.stats") }}')
                        .then(response => response.json())
                        .then(statsData => {
                            if (statsData.success) {
                                updateStats(statsData.backup_stats, statsData.storage_info);
                            }
                        })
                        .catch(error => {
                            console.error('Error refreshing stats:', error);
                        });
                    }, 1000);
                } else if (restore.status === 'in_progress' || restore.status === 'pending') {
                    // Still in progress, continue polling
                    if (pollCount % 10 === 0) {
                        console.log('Restore still in progress...', pollCount, 'seconds elapsed');
                    }
                }
            } else {
                console.error('Failed to get restore status:', data.message);
            }
        })
        .catch(error => {
            console.error('Error polling restore status:', error);
            // Don't stop polling on network errors, just log them
            if (pollCount % 30 === 0) {
                console.warn('Network error while polling restore status, continuing...');
            }
        });
        
        // Stop polling after max attempts
        if (pollCount >= maxPolls) {
            clearInterval(restorePollingInterval);
            restorePollingInterval = null;
            showAlert('warning', 'Restore is taking longer than expected. Please check the restore history manually.');
        }
    }, 1000); // Poll every second
}

function stopRestoreStatusPolling() {
    if (restorePollingInterval) {
        clearInterval(restorePollingInterval);
        restorePollingInterval = null;
    }
}

// Auto-refresh backup list
function refreshBackupList() {
    console.log('Refreshing backup list...');
    // Fetch fresh backup data from index page
    fetch('{{ route("admin.backup.index") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(html => {
        // Extract the table content from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Get desktop table content
        const newTableContent = doc.querySelector('.table-responsive.d-none.d-lg-block');
        // Get mobile table content
        const newMobileContent = doc.querySelector('.d-lg-none');
        
        // Update desktop table
        if (newTableContent) {
            const currentTable = document.querySelector('.table-responsive.d-none.d-lg-block');
            if (currentTable) {
                // Update the tbody content
                const newTbody = newTableContent.querySelector('tbody');
                const currentTbody = currentTable.querySelector('tbody');
                if (newTbody && currentTbody) {
                    currentTbody.innerHTML = newTbody.innerHTML;
                    console.log('Desktop table refreshed');
                } else {
                    // Fallback: replace entire table
                    currentTable.innerHTML = newTableContent.innerHTML;
                    console.log('Desktop table refreshed (fallback)');
                }
            }
        }
        
        // Update mobile table
        if (newMobileContent) {
            const currentMobile = document.querySelector('.d-lg-none');
            if (currentMobile) {
                // Get all mobile cards
                const newCards = newMobileContent.querySelectorAll('.card.mb-3');
                const currentCards = currentMobile.querySelectorAll('.card.mb-3');
                
                if (newCards.length > 0) {
                    // Clear existing cards
                    currentMobile.innerHTML = '';
                    // Add new cards
                    newCards.forEach(card => {
                        currentMobile.appendChild(card.cloneNode(true));
                    });
                    console.log('Mobile table refreshed');
                } else {
                    // Fallback: replace entire content
                    currentMobile.innerHTML = newMobileContent.innerHTML;
                    console.log('Mobile table refreshed (fallback)');
                }
            }
        }
        
        console.log('Backup list refreshed successfully');
    })
    .catch(error => {
        console.error('Error refreshing backup list:', error);
        // Fallback: reload the page if refresh fails
        console.log('Attempting page reload as fallback...');
        // Don't reload automatically - just log the error
        // location.reload();
    });
}

// Attach event listeners to backup list buttons
function attachBackupListEventListeners() {
    // This function will be called after refreshing the backup list
    // to ensure all buttons have their click handlers attached
    // Note: Since we're using onclick attributes in the HTML, this is not strictly necessary
    // but it's here for future enhancements if needed
}

// Utility functions
function showLoading(message) {
    // Create a loading overlay
    const loadingHtml = `
        <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="bg-white p-4 rounded shadow text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted">${message}</div>
            </div>
        </div>
    `;
    
    // Remove any existing loading overlay
    const existing = document.getElementById('loadingOverlay');
    if (existing) {
        existing.remove();
    }
    
    // Add new loading overlay
    document.body.insertAdjacentHTML('beforeend', loadingHtml);
}

function hideLoading() {
    // Remove loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

function showAlert(type, message) {
    console.log('showAlert called with type:', type, 'message:', message);
    
    // Create a proper alert notification
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'success' ? 'alert-success' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    console.log('Alert HTML:', alertHtml);
    
    // Insert at the top of the page
    const container = document.querySelector('.container-fluid');
    console.log('Container found:', !!container);
    
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHtml);
        console.log('Alert inserted into container');
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
                console.log('Alert auto-removed');
            }
        }, 5000);
    } else {
        // Fallback to console and alert
        console.error('Container not found, using fallback alert');
        console.error('Alert:', message);
        alert(message);
    }
}

// New UI enhancement functions
function showCreateBackupModal() {
    // Show the create backup modal
    const modal = new bootstrap.Modal(document.getElementById('createBackupModal'));
    modal.show();
}

function exportBackupList() {
    showLoading('Exporting backup list...');
    
    // Create CSV content
    const table = document.getElementById('backupsTable');
    const rows = table.querySelectorAll('tbody tr');
    let csvContent = 'ID,Type,Status,Size,Created,Creator\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            const rowData = Array.from(cells).map(cell => {
                return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
            });
            csvContent += rowData.join(',') + '\n';
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'backup-list-' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    hideLoading();
    showAlert('success', 'Backup list exported successfully!');
}

function refreshStorageInfo() {
    showLoading('Refreshing storage information...');
    
    fetch('{{ route("admin.backup.stats") }}')
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            updateStats(data.backup_stats, data.storage_info);
            showAlert('success', 'Storage information refreshed!');
        } else {
            showAlert('error', 'Failed to refresh storage information');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error refreshing storage information');
        console.error('Error:', error);
    });
}

function updateStatusIndicator(status) {
    const indicator = document.getElementById('statusIndicator');
    const storageStatus = document.getElementById('storageStatus');
    
    switch(status) {
        case 'ready':
            if (indicator) {
                indicator.innerHTML = '<i class="fas fa-circle me-1"></i>System Ready';
                indicator.className = 'badge bg-primary me-2';
            }
            if (storageStatus) {
                storageStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i>Healthy';
                storageStatus.className = 'badge bg-success me-2';
            }
            break;
        case 'working':
            if (indicator) {
                indicator.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Working';
                indicator.className = 'badge bg-warning me-2';
            }
            if (storageStatus) {
                storageStatus.innerHTML = '<i class="fas fa-cog fa-spin me-1"></i>Processing';
                storageStatus.className = 'badge bg-warning me-2';
            }
            break;
        case 'error':
            if (indicator) {
                indicator.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Error';
                indicator.className = 'badge bg-danger me-2';
            }
            if (storageStatus) {
                storageStatus.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Warning';
                storageStatus.className = 'badge bg-danger me-2';
            }
            break;
    }
}

function fixBackupPaths() {
    if (confirm('Fix backup file paths? This will attempt to correct any incorrect file paths in the backup records.')) {
        showLoading('Fixing backup paths...');
        
        fetch('{{ route("admin.backup.fix.paths") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', data.message);
                // Refresh the page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'An error occurred while fixing backup paths');
            console.error('Error:', error);
        });
    }
}

// Load statistics
function loadStats() {
    fetch('{{ route("admin.backup.stats") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStats(data.backup_stats, data.storage_info);
        }
    })
    .catch(error => {
        console.error('Error loading stats:', error);
    });
}

// Load backup list
function loadBackups() {
    // This function can be used to refresh the backup list if needed
    // For now, the backup list is loaded with the page
    console.log('Backup list loaded');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadBackups();
    
    // Check if there are any in-progress backups on page load
    setTimeout(() => {
        fetch('{{ route("admin.backup.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const hasInProgress = data.backup_stats.total_backups > 0 && 
                                    data.backup_stats.completed_backups < data.backup_stats.total_backups;
                
                if (hasInProgress) {
                    startStatusPolling();
                }
            }
        })
        .catch(error => {
            console.error('Error checking backup status:', error);
        });
    }, 1000);
});
</script>

@endpush
