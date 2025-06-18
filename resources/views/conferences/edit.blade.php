@extends('layouts.app')

@section('title', 'Edit Conference')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Conference</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('conferences.update', $conference) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Title</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $conference->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $conference->start_date) }}" required>
                            @error('start_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $conference->end_date) }}" required>
                            @error('end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="venue_id" class="form-label">Venue</label>
                            <select class="form-control @error('venue_id') is-invalid @enderror" id="venue_id" name="venue_id" required>
                                <option value="">Select Venue</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id', $conference->venue_id) == $venue->id ? 'selected' : '' }}>
                                        {{ $venue->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('venue_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Conference</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 