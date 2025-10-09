@extends('layouts.app')

@section('content')
<style>
.action-buttons .btn {
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.action-buttons .btn i {
    font-size: 14px;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('conferences.index') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="#">Settings</a></li>
                        <li class="breadcrumb-item active">Hotel Management</li>
                    </ol>
                </div>
                <h4 class="page-title">Hotel Management</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Hotels</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add Hotel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Rooms</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotels as $hotel)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-hotel text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $hotel->name }}</h6>
                                                    @if($hotel->website)
                                                        <small class="text-muted">
                                                            <a href="{{ $hotel->website }}" target="_blank" class="text-decoration-none">
                                                                <i class="fas fa-globe"></i> Website
                                                            </a>
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($hotel->address, 50) }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                @if($hotel->contact_email)
                                                    <div><i class="fas fa-envelope"></i> {{ $hotel->contact_email }}</div>
                                                @endif
                                                @if($hotel->contact_phone)
                                                    <div><i class="fas fa-phone"></i> {{ $hotel->contact_phone }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $hotel->rooms->count() }} rooms</span>
                                        </td>
                                        <td>
                                            @if($hotel->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 align-items-center action-buttons">
                                                <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-info text-white" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-primary text-white" title="Edit Hotel">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this hotel? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger text-white" title="Delete Hotel">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-hotel" style="font-size: 48px;"></i>
                                                <p class="mt-2">No hotels found</p>
                                                <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary">Add First Hotel</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($hotels->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $hotels->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
