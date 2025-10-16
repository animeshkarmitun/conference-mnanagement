@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('conferences.index') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotel Management</a></li>
                        <li class="breadcrumb-item active">Hotel Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Hotel Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">{{ $hotel->name }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Hotel
                            </a>
                            <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Hotel Name:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $hotel->name }}
                                </div>
                            </div>


                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Address:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $hotel->address }}
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Room Capacity:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $hotel->room_capacity }} rooms
                                </div>
                            </div>

                            @if($hotel->contact_email || $hotel->contact_phone)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Contact:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($hotel->contact_email)
                                        <div><i class="fas fa-envelope"></i> {{ $hotel->contact_email }}</div>
                                    @endif
                                    @if($hotel->contact_phone)
                                        <div><i class="fas fa-phone"></i> {{ $hotel->contact_phone }}</div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($hotel->website)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Website:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <a href="{{ $hotel->website }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-globe"></i> {{ $hotel->website }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($hotel->check_in_time || $hotel->check_out_time)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Check-in/out Times:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($hotel->check_in_time)
                                        <div><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($hotel->check_in_time)->format('g:i A') }}</div>
                                    @endif
                                    @if($hotel->check_out_time)
                                        <div><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($hotel->check_out_time)->format('g:i A') }}</div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($hotel->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            @if($hotel->description)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Description:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $hotel->description }}
                                </div>
                            </div>
                            @endif

                            @if($hotel->amenities && count($hotel->amenities) > 0)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Amenities:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($hotel->amenities as $amenity)
                                            <span class="badge bg-light text-dark">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Quick Stats</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Rooms:</span>
                                        <strong>{{ $hotel->rooms->count() }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Available Rooms:</span>
                                        <strong>{{ $hotel->rooms->where('is_available', true)->count() }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Room Types:</span>
                                        <strong>{{ $hotel->rooms->pluck('room_type')->unique()->count() }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Rooms</h5>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                                    <i class="fas fa-plus"></i> Add Room
                                </button>
                            </div>
                            
                            @if($hotel->rooms->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Room Number</th>
                                            <th>Type</th>
                                            <th>Beds</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hotel->rooms as $room)
                                            <tr>
                                                <td>{{ $room->room_number }}</td>
                                                <td>{{ $room->roomType->name ?? ($room->room_type ?? 'Standard') }}</td>
                                                <td>{{ $room->beds ?? 1 }}</td>
                                                <td>
                                                    @if($room->is_available)
                                                        <span class="badge bg-success">Available</span>
                                                    @else
                                                        <span class="badge bg-danger">Unavailable</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 align-items-center action-buttons">
                                                        <button type="button" class="btn btn-sm btn-info text-white" onclick="editRoom({{ $room->id }})" title="Edit Room">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if($room->getRoomAllocationsCount() == 0)
                                                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger text-white" title="Delete Room">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-secondary text-white" disabled title="Cannot delete - Room is assigned">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-bed" style="font-size: 48px;"></i>
                                    <p class="mt-2">No rooms found</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                                        Add First Room
                                    </button>
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

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addRoomForm" action="{{ route('admin.rooms.store') }}" method="POST">
                @csrf
                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" id="room_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_type_id" class="form-label">Room Type</label>
                                <select name="room_type_id" id="room_type_id" class="form-select">
                                    <option value="">Select Room Type</option>
                                    @foreach(\App\Models\RoomType::where('is_active', true)->orderBy('name')->get() as $roomType)
                                        <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="beds" class="form-label">Number of Beds</label>
                                <input type="number" name="beds" id="beds" class="form-control" min="1" max="10" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_available" id="is_available" checked>
                                    <label class="form-check-label" for="is_available">
                                        Available
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomModalLabel">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoomForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" id="edit_room_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_room_type_id" class="form-label">Room Type</label>
                                <select name="room_type_id" id="edit_room_type_id" class="form-select">
                                    <option value="">Select Room Type</option>
                                    @foreach(\App\Models\RoomType::where('is_active', true)->orderBy('name')->get() as $roomType)
                                        <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_beds" class="form-label">Number of Beds</label>
                                <input type="number" name="beds" id="edit_beds" class="form-control" min="1" max="10">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_available" id="edit_is_available">
                                    <label class="form-check-label" for="edit_is_available">
                                        Available
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<script>
// Room type data for auto-populating beds
const roomTypes = @json($roomTypes ?? []);

function editRoom(roomId) {
    // Get room data via AJAX
    fetch(`/admin/rooms/${roomId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const room = data.room;
                document.getElementById('editRoomForm').action = `/admin/rooms/${roomId}`;
                document.getElementById('edit_room_number').value = room.room_number;
                document.getElementById('edit_room_type_id').value = room.room_type_id || '';
                document.getElementById('edit_beds').value = room.beds || 1;
                document.getElementById('edit_description').value = room.description || '';
                document.getElementById('edit_is_available').checked = room.is_available;
                
                // Show modal
                new bootstrap.Modal(document.getElementById('editRoomModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading room data');
        });
}

// Auto-populate beds when room type is selected
function updateBedsFromRoomType(selectElement, bedsElement) {
    const selectedRoomTypeId = selectElement.value;
    if (selectedRoomTypeId) {
        const roomType = roomTypes.find(rt => rt.id == selectedRoomTypeId);
        if (roomType && roomType.default_beds) {
            bedsElement.value = roomType.default_beds;
        }
    }
}

// Add event listeners when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add room modal
    const addRoomTypeSelect = document.getElementById('room_type_id');
    const addBedsInput = document.getElementById('beds');
    if (addRoomTypeSelect && addBedsInput) {
        addRoomTypeSelect.addEventListener('change', function() {
            updateBedsFromRoomType(this, addBedsInput);
        });
    }
    
    // Edit room modal
    const editRoomTypeSelect = document.getElementById('edit_room_type_id');
    const editBedsInput = document.getElementById('edit_beds');
    if (editRoomTypeSelect && editBedsInput) {
        editRoomTypeSelect.addEventListener('change', function() {
            updateBedsFromRoomType(this, editBedsInput);
        });
    }
});
</script>
@endsection
