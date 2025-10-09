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
                        <li class="breadcrumb-item active">Edit Hotel</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Hotel</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit {{ $hotel->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hotels.update', $hotel) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Hotel Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $hotel->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" 
                                              rows="3" required>{{ old('address', $hotel->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="room_capacity" class="form-label">Room Capacity <span class="text-danger">*</span></label>
                                    <input type="number" name="room_capacity" id="room_capacity" class="form-control @error('room_capacity') is-invalid @enderror" 
                                           value="{{ old('room_capacity', $hotel->room_capacity) }}" min="1" required>
                                    @error('room_capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           value="{{ old('contact_email', $hotel->contact_email) }}">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                                           value="{{ old('contact_phone', $hotel->contact_phone) }}">
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" 
                                           value="{{ old('website', $hotel->website) }}" placeholder="https://example.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="check_in_time" class="form-label">Check-in Time</label>
                                    <input type="time" name="check_in_time" id="check_in_time" class="form-control @error('check_in_time') is-invalid @enderror" 
                                           value="{{ old('check_in_time', $hotel->check_in_time ? \Carbon\Carbon::parse($hotel->check_in_time)->format('H:i') : '15:00') }}">
                                    @error('check_in_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="check_out_time" class="form-label">Check-out Time</label>
                                    <input type="time" name="check_out_time" id="check_out_time" class="form-control @error('check_out_time') is-invalid @enderror" 
                                           value="{{ old('check_out_time', $hotel->check_out_time ? \Carbon\Carbon::parse($hotel->check_out_time)->format('H:i') : '11:00') }}">
                                    @error('check_out_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                              rows="4">{{ old('description', $hotel->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Amenities</label>
                                    <div class="row">
                                        @php
                                            $commonAmenities = ['WiFi', 'Parking', 'Pool', 'Gym', 'Restaurant', 'Bar', 'Spa', 'Business Center', 'Conference Room', 'Laundry Service'];
                                            $selectedAmenities = old('amenities', $hotel->amenities ?? []);
                                        @endphp
                                        @foreach($commonAmenities as $amenity)
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                                           id="amenity_{{ $loop->index }}" {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="amenity_{{ $loop->index }}">
                                                        {{ $amenity }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('amenities')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                               {{ old('is_active', $hotel->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Hotel
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Hotel
                                    </button>
                                    <a href="{{ route('admin.hotels.show', $hotel) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
