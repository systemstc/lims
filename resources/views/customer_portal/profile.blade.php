@extends('layouts.app_back')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>My Profile</h4>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name <b class="text-danger">*</b></label>
                            <input type="text" name="m07_name" class="form-control" value="{{ old('m07_name', $customer->m07_name) }}" required>
                            @error('m07_name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $customer->m07_email }}" readonly disabled>
                            <small class="text-muted">Email cannot be changed here. Contact support.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="{{ $customer->m07_phone }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address <b class="text-danger">*</b></label>
                            <textarea name="m07_address" class="form-control" rows="3" required>{{ old('m07_address', $customer->m07_address) }}</textarea>
                            @error('m07_address')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
