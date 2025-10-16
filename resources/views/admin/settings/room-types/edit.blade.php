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
                        <li class="breadcrumb-item active">Edit Room Type</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Room Type</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit {{ $roomType->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.room-types.update', $roomType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Room Type Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $roomType->name) }}" required maxlength="50">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="default_beds" class="form-label">Default Beds <span class="text-danger">*</span></label>
                                    <input type="number" name="default_beds" id="default_beds" class="form-control @error('default_beds') is-invalid @enderror" 
                                           value="{{ old('default_beds', $roomType->default_beds) }}" min="1" max="10" required>
                                    @error('default_beds')
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
                                              rows="4">{{ old('description', $roomType->description) }}</textarea>
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
                                            $commonAmenities = ['WiFi', 'TV', 'Air Conditioning', 'Mini Bar', 'Coffee Maker', 'Work Desk', 'Sofa', 'Balcony', 'Jacuzzi', 'Room Service', 'Safe', 'Iron', 'Hair Dryer'];
                                            $selectedAmenities = old('amenities', $roomType->amenities ?? []);
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
                                               {{ old('is_active', $roomType->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Room Type
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Room Type
                                    </button>
                                    <a href="{{ route('admin.room-types.show', $roomType) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('admin.room-types.index') }}" class="btn btn-secondary">
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
