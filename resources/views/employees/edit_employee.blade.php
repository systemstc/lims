@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="title nk-block-title">Update Employee</h4>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('update_employee', $employee->m06_employee_id) }}" class="form-validate is-alter" method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_ro_id">Regional Office</label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_ro_id" class="form-control" id="txt_ro_id" required>
                                                        <option value="">-- Select Ro --</option>
                                                        @foreach ($ros as $ro)
                                                            <option value="{{ $ro->m04_ro_id }}" {{ $ro->m04_ro_id == old('txt_ro_id', $employee->m04_ro_id) ? 'selected' : '' }}>
                                                                {{ $ro->m04_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('txt_ro_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_role_id">Role</label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_role_id" class="form-control" id="txt_role_id"
                                                        required>
                                                        <option value="">-- Select Role --</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->m03_role_id }}" {{ $role->m03_role_id == old('txt_role_id', $employee->m03_role_id) ? 'selected' : '' }}>
                                                                {{ $role->m03_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('txt_role_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Full Name</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_name"
                                                        name="txt_name" value="{{ old('txt_name', $employee->m06_name) }}" required>
                                                </div>
                                                @error('txt_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_email">Email address</label>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-mail"></em>
                                                    </div>
                                                    <input type="text" class="form-control" id="txt_email"
                                                        name="txt_email" value="{{ old('txt_email', $employee->m06_email) }}" required>
                                                </div>
                                                @error('txt_email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_phone">Phone</label>
                                                <div class="form-control-wrap">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="txt_phone">+91</span>
                                                        </div>
                                                        <input type="text" name="txt_phone" class="form-control"
                                                            value="{{ old('txt_phone', $employee->m06_phone) }}" required>
                                                    </div>
                                                    @error('txt_phone')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_state_id">State</label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_state_id" class="form-control" id="txt_state_id"
                                                        required>
                                                        <option value="">-- Select State --</option>
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->m01_state_id }}" {{ $state->m01_state_id == old('txt_state_id', $employee->m01_state_id) ? 'selected' : '' }}>
                                                                {{ $state->m01_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('txt_state_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_district_id">District</label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_district_id" class="form-control" id="txt_district_id"
                                                        required>
                                                        <option value="">-- Select District --</option>
                                                        @if($employee->district)
                                                            <option value="{{ $employee->m02_district_id }}" selected>{{ $employee->district->m02_name }}</option>
                                                        @endif
                                                    </select>
                                                    @error('txt_district_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_emp_id">Employee ID (Govt ID) - Optional</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_emp_id"
                                                        name="txt_emp_id" value="{{ old('txt_emp_id', $employee->m06_emp_id) }}">
                                                </div>
                                                @error('txt_emp_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mt-4">
                                                <button type="submit" class="btn btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div><!-- .nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>

    <script>
        $('#txt_state_id').on('change', function() {
            var stateId = $(this).val();
            $('#txt_district_id').html('<option value="">Loading...</option>');

            if (stateId) {
                $.ajax({
                    url: '{{ route('get_districts') }}',
                    type: 'GET',
                    data: {
                        state_id: stateId
                    },
                    success: function(response) {
                        let options = '<option value="">-- Select District --</option>';
                        $.each(response, function(key, district) {
                            options += '<option value="' + district.m02_district_id + '">' +
                                district.m02_name + '</option>';
                        });
                        $('#txt_district_id').html(options);
                    },
                    error: function() {
                        $('#txt_district_id').html(
                            '<option value="">-- Error loading districts --</option>');
                    }
                });
            } else {
                $('#txt_district_id').html('<option value="">-- Select District --</option>');
            }
        });
    </script>
@endsection
