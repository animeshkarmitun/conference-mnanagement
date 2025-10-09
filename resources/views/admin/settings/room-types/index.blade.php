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
                        <li class="breadcrumb-item active">Room Types</li>
                    </ol>
                </div>
                <h4 class="page-title">Room Types</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Room Types</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add Room Type
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
                                    <th>Description</th>
                                    <th>Default Beds</th>
                                    <th>Base Price</th>
                                    <th>Amenities</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roomTypes as $roomType)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-bed text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $roomType->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($roomType->description, 50) ?: 'No description' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $roomType->default_beds }} bed{{ $roomType->default_beds > 1 ? 's' : '' }}</span>
                                        </td>
                                        <td>
                                            @if($roomType->base_price)
                                                <span class="text-success fw-bold">${{ number_format($roomType->base_price, 2) }}</span>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($roomType->amenities && count($roomType->amenities) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach(array_slice($roomType->amenities, 0, 3) as $amenity)
                                                        <span class="badge bg-light text-dark">{{ $amenity }}</span>
                                                    @endforeach
                                                    @if(count($roomType->amenities) > 3)
                                                        <span class="badge bg-light text-dark">+{{ count($roomType->amenities) - 3 }} more</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($roomType->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 align-items-center action-buttons">
                                                <a href="{{ route('admin.room-types.show', $roomType) }}" class="btn btn-info text-white" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.room-types.edit', $roomType) }}" class="btn btn-primary text-white" title="Edit Room Type">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.room-types.destroy', $roomType) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room type? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger text-white" title="Delete Room Type">
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
                                                <i class="fas fa-bed" style="font-size: 48px;"></i>
                                                <p class="mt-2">No room types found</p>
                                                <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary">Add First Room Type</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($roomTypes->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $roomTypes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
