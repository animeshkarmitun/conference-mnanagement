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
        background: linear-gradient(45deg, #28a745, #20c997);
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
        background-image: linear-gradient(
            -45deg,
            rgba(255, 255, 255, .2) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255, 255, 255, .2) 50%,
            rgba(255, 255, 255, .2) 75%,
            transparent 75%,
            transparent
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
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    
    .status-in-progress {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
        animation: pulse 2s infinite;
    }
    
    .status-failed {
        background: linear-gradient(135deg, #dc3545, #e83e8c);
        color: white;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #6c757d, #495057);
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
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
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
    
    /* Enhanced Cards */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1.5rem;
        font-weight: 600;
    }
    
    /* Enhanced Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn:hover::before {
        left: 100%;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        border: none;
        color: white;
    }
    
    .btn-info {
        background: linear-gradient(135deg, #17a2b8, #6f42c1);
        border: none;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
        border: none;
    }
    
    /* Quick Actions Enhancement */
    .quick-actions {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .quick-actions .btn {
        margin: 0.25rem;
        min-width: 120px;
    }
    
    /* Storage Info Cards */
    .storage-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .storage-metric {
        text-align: center;
        padding: 1rem;
    }
    
    .storage-metric h6 {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.8;
        margin-bottom: 0.5rem;
    }
    
    .storage-metric h4 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
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
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Floating Action Button Enhancement */
    .fab {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        transition: all 0.3s ease;
        animation: float 3s ease-in-out infinite;
    }
    
    .fab:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(40, 167, 69, 0.6);
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    /* FAB Tooltip */
    .fab-container {
        position: relative;
    }
    
    .fab-tooltip {
        position: absolute;
        right: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    
    .fab-tooltip::after {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-left-color: rgba(0, 0, 0, 0.8);
    }
    
    .fab-container:hover .fab-tooltip {
        opacity: 1;
        visibility: visible;
    }
</style>
@endpush

@section('content')
<!-- UI Enhancement Test -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>UI Enhanced!</strong> The backup management interface has been updated with modern design elements.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-database me-2"></i>Backup Management
                    </h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" onclick="createInstantBackup()" title="Create a quick full backup">
                            <i class="fas fa-bolt me-1"></i>Instant Backup
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                            <i class="fas fa-plus me-1"></i>Create Backup
                        </button>
                        <button type="button" class="btn btn-info" onclick="refreshStats()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="totalBackups">{{ $stats['total_backups'] }}</h4>
                                            <p class="mb-0">Total Backups</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-database fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="completedBackups">{{ $stats['completed_backups'] }}</h4>
                                            <p class="mb-0">Completed</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="successRate">{{ $stats['success_rate'] }}%</h4>
                                            <p class="mb-0">Success Rate</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="totalSize">{{ $stats['formatted_size'] }}</h4>
                                            <p class="mb-0">Total Size</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-hdd fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="quick-actions">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-rocket me-2"></i>Quick Actions
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2" id="statusIndicator">
                                            <i class="fas fa-circle me-1"></i>System Ready
                                        </span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshStats()" title="Refresh all data">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <button type="button" class="btn btn-success w-100" onclick="createInstantBackup()" title="Create a quick full backup">
                                            <i class="fas fa-bolt me-2"></i>
                                            <span class="d-none d-md-inline">Instant Backup</span>
                                            <span class="d-md-none">Backup</span>
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <button type="button" class="btn btn-warning w-100" onclick="showCleanupModal()" title="Clean up old backups">
                                            <i class="fas fa-broom me-2"></i>
                                            <span class="d-none d-md-inline">Cleanup</span>
                                            <span class="d-md-none">Clean</span>
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <button type="button" class="btn btn-info w-100" onclick="testConnection()" title="Test backup system">
                                            <i class="fas fa-stethoscope me-2"></i>
                                            <span class="d-none d-md-inline">System Test</span>
                                            <span class="d-md-none">Test</span>
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <button type="button" class="btn btn-secondary w-100" onclick="testSimpleBackup()" title="Test simple backup">
                                            <i class="fas fa-vial me-2"></i>
                                            <span class="d-none d-md-inline">Simple Test</span>
                                            <span class="d-md-none">Simple</span>
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="showCreateBackupModal()" title="Create custom backup">
                                            <i class="fas fa-plus me-2"></i>
                                            <span class="d-none d-md-inline">New Backup</span>
                                            <span class="d-md-none">New</span>
                                        </button>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <button type="button" class="btn btn-outline-info w-100" onclick="exportBackupList()" title="Export backup list">
                                            <i class="fas fa-download me-2"></i>
                                            <span class="d-none d-md-inline">Export</span>
                                            <span class="d-md-none">Export</span>
                                        </button>
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

                    <!-- Enhanced Storage Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-database me-2"></i>Storage Information
                                        </h5>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2" id="storageStatus">
                                                <i class="fas fa-check-circle me-1"></i>Healthy
                                            </span>
                                            <button type="button" class="btn btn-outline-light btn-sm" onclick="refreshStorageInfo()" title="Refresh storage info">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-3 col-md-6">
                                            <div class="storage-metric">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <i class="fas fa-hdd text-primary me-2"></i>
                                                    <h6 class="mb-0">Used Space</h6>
                                                </div>
                                                <h4 id="usedSpace" class="text-primary">{{ $storageInfo['formatted_used_space'] }}</h4>
                                                <small class="text-muted" id="usedSpacePercent">0% of available</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="storage-metric">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <i class="fas fa-files text-info me-2"></i>
                                                    <h6 class="mb-0">File Count</h6>
                                                </div>
                                                <h4 id="fileCount" class="text-info">{{ $storageInfo['file_count'] }}</h4>
                                                <small class="text-muted" id="fileCountTrend">No change</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="storage-metric">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    <h6 class="mb-0">Last Backup</h6>
                                                </div>
                                                <h4 id="lastBackup" class="text-warning">{{ $stats['last_backup'] ? $stats['last_backup']->format('M d, Y') : 'Never' }}</h4>
                                                <small class="text-muted" id="lastBackupTime">{{ $stats['last_backup'] ? $stats['last_backup']->format('H:i:s') : 'No backups yet' }}</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="storage-metric">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <i class="fas fa-weight text-success me-2"></i>
                                                    <h6 class="mb-0">Last Size</h6>
                                                </div>
                                                <h4 id="lastBackupSize" class="text-success">{{ $stats['last_backup_size'] ?? 'N/A' }}</h4>
                                                <small class="text-muted" id="sizeTrend">No data</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Storage Usage Bar -->
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">Storage Usage</h6>
                                            <span class="text-muted" id="storageUsageText">0% used</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-gradient" id="storageUsageBar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
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
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showRestoreHistory()">
                                            <i class="fas fa-history me-1"></i>Restore History
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="showCleanupModal()">
                                            <i class="fas fa-broom me-1"></i>Cleanup
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Desktop Table View -->
                                    <div class="table-responsive d-none d-lg-block">
                                        <table class="table table-striped table-hover" id="backupsTable">
                                            <thead class="table-dark">
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
                                                    <td>{{ $backup->creator->first_name }} {{ $backup->creator->last_name }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-info" onclick="viewBackup({{ $backup->id }})" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if($backup->status === 'completed')
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
                                                        <small>{{ $backup->creator->first_name }} {{ $backup->creator->last_name }}</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="btn-group w-100" role="group">
                                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="viewBackup({{ $backup->id }})" title="View Details">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </button>
                                                    @if($backup->status === 'completed')
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

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cleanup Old Backups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cleanupForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="retentionDays" class="form-label">Retention Period (Days)</label>
                        <input type="number" class="form-control" id="retentionDays" name="days" value="30" min="1" max="365" required>
                        <div class="form-text">Backups older than this number of days will be deleted</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This action cannot be undone. Make sure you have important backups stored elsewhere.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-broom me-1"></i>Cleanup
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
                // Start polling for updates
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

// View backup details
function viewBackup(id) {
    showLoading('Loading backup details...');
    
    fetch(`{{ route("admin.backup.index") }}/${id}`)
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            displayBackupDetails(data.backup);
            bootstrap.Modal.getInstance(document.getElementById('backupDetailsModal')).show();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred while loading backup details');
        console.error('Error:', error);
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
    document.getElementById('restoreBackupId').value = id;
    bootstrap.Modal.getInstance(document.getElementById('restoreModal')).show();
}

// Execute restore
function executeRestore() {
    const form = document.getElementById('restoreForm');
    const formData = new FormData(form);
    
    showLoading('Starting restore operation...');
    
    fetch(`{{ route("admin.backup.index") }}/${document.getElementById('restoreBackupId').value}/restore`, {
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
            bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
            location.reload();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred while restoring backup');
        console.error('Error:', error);
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
    bootstrap.Modal.getInstance(document.getElementById('cleanupModal')).show();
}

// Execute cleanup
function executeCleanup() {
    const form = document.getElementById('cleanupForm');
    const formData = new FormData(form);
    
    if (confirm('Are you sure you want to delete old backups? This action cannot be undone.')) {
        showLoading('Cleaning up old backups...');
        
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
                bootstrap.Modal.getInstance(document.getElementById('cleanupModal')).hide();
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
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            displayRestoreHistory(data.restores);
            bootstrap.Modal.getInstance(document.getElementById('restoreHistoryModal')).show();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred while loading restore history');
        console.error('Error:', error);
    });
}

// Display restore history
function displayRestoreHistory(restores) {
    const tbody = document.querySelector('#restoreHistoryTable tbody');
    tbody.innerHTML = '';
    
    if (restores.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No restore operations found</td></tr>';
        return;
    }
    
    restores.forEach(restore => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${restore.id}</td>
            <td>${restore.backup_date}</td>
            <td><span class="badge bg-${restore.type === 'full' ? 'primary' : 'secondary'}">${restore.type}</span></td>
            <td><span class="badge bg-${restore.status === 'completed' ? 'success' : 'warning'}">${restore.status}</span></td>
            <td>${restore.tables_restored}</td>
            <td>${restore.created_at}</td>
            <td>${restore.creator}</td>
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

// Refresh statistics
function refreshStats() {
    showLoading('Refreshing statistics...');
    
    fetch('{{ route("admin.backup.stats") }}')
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            updateStats(data.backup_stats, data.storage_info);
            showAlert('success', 'Statistics refreshed successfully');
        } else {
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

// Test backup system connection
function testConnection() {
    showLoading('Testing backup system...');
    
    fetch('{{ route("admin.backup.test.connection") }}')
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            displayTestResults(data.results);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred while testing connection');
        console.error('Error:', error);
    });
}

// Display test results
function displayTestResults(results) {
    let message = 'Backup System Test Results:\n\n';
    
    for (const [key, value] of Object.entries(results)) {
        if (key === 'database_config') {
            message += `${key}:\n`;
            for (const [configKey, configValue] of Object.entries(value)) {
                message += `  ${configKey}: ${configValue}\n`;
            }
        } else {
            message += `${key}: ${value}\n`;
        }
    }
    
    showAlert('info', message);
}

// Test simple backup functionality
function testSimpleBackup() {
    showLoading('Running simple backup test...');
    
    fetch('{{ route("admin.backup.test.simple") }}')
    .then(response => {
        console.log('Simple test response status:', response.status);
        return response.json();
    })
    .then(data => {
        hideLoading();
        console.log('Simple test response data:', data);
        if (data.success) {
            displayTestResults(data.results);
        } else {
            console.error('Simple test failed:', data.message);
            console.error('Simple test error:', data.error);
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('=== SIMPLE TEST ERROR DETAILS ===');
        console.error('Error object:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        console.error('==================================');
        showAlert('error', 'An error occurred while running simple test. Check console for details.');
    });
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

function startStatusPolling() {
    if (statusPollingInterval) {
        clearInterval(statusPollingInterval);
    }
    
    statusPollingInterval = setInterval(() => {
        fetch('{{ route("admin.backup.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStats(data.backup_stats, data.storage_info);
                // Check if any backups are still in progress
                const hasInProgress = data.backup_stats.total_backups > 0 && 
                                    data.backup_stats.completed_backups < data.backup_stats.total_backups;
                
                if (!hasInProgress) {
                    clearInterval(statusPollingInterval);
                    statusPollingInterval = null;
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

// Auto-refresh backup list
function refreshBackupList() {
    fetch('{{ route("admin.backup.index") }}')
    .then(response => response.text())
    .then(html => {
        // Extract the table content from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableContent = doc.querySelector('.table-responsive');
        const newMobileContent = doc.querySelector('.d-lg-none');
        
        if (newTableContent) {
            document.querySelector('.table-responsive').innerHTML = newTableContent.innerHTML;
        }
        if (newMobileContent) {
            document.querySelector('.d-lg-none').innerHTML = newMobileContent.innerHTML;
        }
    })
    .catch(error => {
        console.error('Error refreshing backup list:', error);
    });
}

// Utility functions
function showLoading(message) {
    // You can implement a loading overlay here
    console.log('Loading:', message);
}

function hideLoading() {
    // Hide loading overlay
    console.log('Loading complete');
}

function showAlert(type, message) {
    // You can implement a toast notification system here
    alert(message);
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
            indicator.innerHTML = '<i class="fas fa-circle me-1"></i>System Ready';
            indicator.className = 'badge bg-primary me-2';
            storageStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i>Healthy';
            storageStatus.className = 'badge bg-success me-2';
            break;
        case 'working':
            indicator.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Working';
            indicator.className = 'badge bg-warning me-2';
            storageStatus.innerHTML = '<i class="fas fa-cog fa-spin me-1"></i>Processing';
            storageStatus.className = 'badge bg-warning me-2';
            break;
        case 'error':
            indicator.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Error';
            indicator.className = 'badge bg-danger me-2';
            storageStatus.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Warning';
            storageStatus.className = 'badge bg-danger me-2';
            break;
    }
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

<!-- Enhanced Floating Action Button -->
<div class="fab-container">
    <button type="button" class="fab" onclick="createInstantBackup()" title="Create Instant Backup">
        <i class="fas fa-bolt"></i>
    </button>
    <div class="fab-tooltip">Create Instant Backup</div>
</div>
@endpush
