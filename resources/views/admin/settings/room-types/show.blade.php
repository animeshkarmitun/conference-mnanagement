@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('conferences.index') }}">Admin</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.room-types.index') }}">Room Types</a></li>
                        <li class="breadcrumb-item active">Room Type Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Room Type Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">{{ $roomType->name }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.room-types.edit', $roomType) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Room Type
                            </a>
                            <a href="{{ route('admin.room-types.index') }}" class="btn btn-secondary">
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
                                    <strong>Room Type Name:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $roomType->name }}
                                </div>
                            </div>

                            @if($roomType->description)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Description:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $roomType->description }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Default Beds:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <span class="badge bg-secondary">{{ $roomType->default_beds }} bed{{ $roomType->default_beds > 1 ? 's' : '' }}</span>
                                </div>
                            </div>


                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($roomType->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            @if($roomType->amenities && count($roomType->amenities) > 0)
                            <div class="row mb-4">
                                <div class="col-sm-3">
                                    <strong>Amenities:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($roomType->amenities as $amenity)
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
                                        <strong>{{ $roomType->rooms->count() }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Available Rooms:</span>
                                        <strong>{{ $roomType->rooms->where('is_available', true)->count() }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Hotels Using:</span>
                                        <strong>{{ $roomType->rooms->pluck('hotel_id')->unique()->count() }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($roomType->rooms->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Rooms of This Type</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Hotel</th>
                                            <th>Room Number</th>
                                            <th>Beds</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roomType->rooms as $room)
                                            <tr>
                                                <td>{{ $room->hotel->name ?? 'N/A' }}</td>
                                                <td>{{ $room->room_number }}</td>
                                                <td>{{ $room->beds ?? $roomType->default_beds }}</td>
                                                <td>
                                                    @if($room->is_available)
                                                        <span class="badge bg-success">Available</span>
                                                    @else
                                                        <span class="badge bg-danger">Unavailable</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
