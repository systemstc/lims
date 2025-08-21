@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Primary Test</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('create_primary') }}" class="form-validate is-alter"
                                    method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_sample_id">Sample<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_sample_id" class="form-select" id="txt_sample_id"
                                                        required>
                                                        <option value="">-- Select Sample --</option>
                                                        @foreach ($samples as $sample)
                                                            <option value="{{ $sample->m10_sample_id }}"
                                                                {{ old('txt_sample_id') == $sample->m10_sample_id ? 'selected' : '' }}>
                                                                {{ $sample->m10_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_sample_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Group --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_group_id">Group<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_group_id" class="form-select" id="txt_group_id"
                                                        required>
                                                        <option value="">-- Select Group --</option>
                                                    </select>
                                                </div>
                                                @error('txt_group_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Test --}}
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_test_id">Test<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_test_id" class="form-select" id="txt_test_id"
                                                        required>
                                                        <option value="">-- Select Test --</option>
                                                    </select>
                                                </div>
                                                @error('txt_test_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        {{-- Parameter --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Parameter<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_name"
                                                        name="txt_name" value="{{ old('txt_name') }}" required>
                                                </div>
                                                @error('txt_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Unit --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_unit">Unit</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" name="txt_unit" class="form-control"
                                                        id="txt_unit" value="{{ old('txt_unit') }}">
                                                </div>
                                                @error('txt_unit')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Requirement --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_requirement">Requirement</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_requirement"
                                                        name="txt_requirement" value="{{ old('txt_requirement') }}">
                                                </div>
                                                @error('txt_requirement')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Remark --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_remark">Remark</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_remark"
                                                        name="txt_remark" value="{{ old('txt_remark') }}">
                                                </div>
                                                @error('txt_remark')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AJAX for Sample -> Group --}}
    <script>
        $(document).ready(function() {
            const groupDropdown = $('#txt_group_id');

            $('#txt_sample_id').on('change', function() {
                let sampleId = $(this).val();
                groupDropdown.empty().append('<option value="">Loading...</option>');

                if (sampleId) {
                    $.ajax({
                        url: "{{ route('get_groups') }}",
                        type: "GET",
                        data: {
                            sample_id: sampleId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            groupDropdown.empty().append(
                                '<option value="">-- Select Group --</option>');
                            $.each(data, function(key, group) {
                                let selected = (group.m11_group_id ==
                                    "{{ old('txt_group_id') }}") ? 'selected' : '';
                                groupDropdown.append(
                                    `<option value="${group.m11_group_id}" ${selected}>${group.m11_name}</option>`
                                    );
                            });
                        },
                        error: function() {
                            groupDropdown.empty().append(
                                '<option value="">Error loading groups</option>');
                        }
                    });
                } else {
                    groupDropdown.empty().append('<option value="">-- Select Group --</option>');
                }
            });

            let oldSampleId = "{{ old('txt_sample_id') }}";
            if (oldSampleId) {
                $('#txt_sample_id').val(oldSampleId).trigger('change');
            }
        });
    </script>
{{-- 
    <script>
        $(document).ready(function() {
            const testDropdown = $('#txt_test_id');

            $('#txt_group_id').on('change', function() {
                let groupId = $(this).val();
                testDropdown.empty().append('<option value="">Loading...</option>');

                if (groupId) {
                    $.ajax({
                        url: "{{ route('get_tests') }}",
                        type: "GET",
                        data: {
                            group_id: groupId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            testDropdown.empty().append(
                                '<option value="">-- Select Test --</option>');
                            $.each(data, function(index, test) {
                                let selected = (test.m12_test_id ==
                                    "{{ old('txt_test_id') }}") ? 'selected' : '';
                                testDropdown.append(
                                    `<option value="${test.m12_test_id}" ${selected}>${test.m12_name}</option>`
                                    );
                            });
                        },
                        error: function() {
                            testDropdown.empty().append(
                                '<option value="">Error loading tests</option>');
                        }
                    });
                } else {
                    testDropdown.empty().append('<option value="">-- Select Test --</option>');
                }
            });

            let oldGroupId = "{{ old('txt_group_id') }}";
            if (oldGroupId) {
                $('#txt_group_id').val(oldGroupId).trigger('change');
            }
        });
    </script> --}}
@endsection
