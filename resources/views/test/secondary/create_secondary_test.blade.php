@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Secondary Test</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('create_secondary') }}" class="form-validate is-alter"
                                    method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Sample<b class="text-danger">*</b></label>
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
                                                <label class="form-label">Group<b class="text-danger">*</b></label>
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
                                                <label class="form-label">Test<b class="text-danger">*</b></label>
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

                                        {{-- Primary Test --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Primary Test<b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_primary_test_id" class="form-select"
                                                        id="txt_primary_test_id" required>
                                                        <option value="">-- Select Primary Test --</option>
                                                    </select>
                                                </div>
                                                @error('txt_primary_test_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Parameter --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Secondary Test<b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="txt_name"
                                                        value="{{ old('txt_name') }}" required>
                                                </div>
                                                @error('txt_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Unit --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Unit</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" name="txt_unit" class="form-control"
                                                        value="{{ old('txt_unit') }}">
                                                </div>
                                                @error('txt_unit')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sample → Group --}}
        <script>
            $(document).ready(function() {
                $('#txt_sample_id').on('change', function() {
                    let sampleId = $(this).val();
                    $('#txt_group_id').html('<option value="">Loading...</option>');

                    $.get("{{ route('get_groups') }}", {
                        sample_id: sampleId
                    }, function(groups) {
                        let options = '<option value="">-- Select Group --</option>';
                        $.each(groups, function(i, group) {
                            options +=
                                `<option value="${group.m11_group_code}" ${group.m11_group_code == "{{ old('txt_group_id') }}" ? 'selected' : ''}>${group.m11_name}</option>`;
                        });
                        $('#txt_group_id').html(options).trigger('change');
                    });
                });

                // $('#txt_group_id').on('change', function() {
                //     let groupId = $(this).val();
                //     $('#txt_test_id').html('<option value="">Loading...</option>');

                //     $.get("{{ route('get_tests') }}", {
                //         group_id: groupId
                //     }, function(tests) {
                //         let options = '<option value="">-- Select Test --</option>';
                //         $.each(tests, function(i, test) {
                //             options +=
                //                 `<option value="${test.m12_test_id}" ${test.m12_test_id == "{{ old('txt_test_id') }}" ? 'selected' : ''}>${test.m12_name}</option>`;
                //         });
                //         $('#txt_test_id').html(options).trigger('change');
                //     });
                // });

                $('#txt_group_id').on('change', function() {
                    let groupId = $(this).val();
                    $('#txt_primary_test_id').html('<option value="">Loading...</option>');

                    $.get("{{ route('get_primary_tests') }}", {
                        group_id: groupId
                    }, function(tests) {
                        let options = '<option value="">-- Select Primary Test --</option>';
                        $.each(tests, function(i, test) {
                            options +=
                                `<option value="${test.m16_primary_test_id}">${test.m16_name}</option>`;
                        });
                        $('#txt_primary_test_id').html(options);
                    });
                });

                // Trigger default selection
                @if (old('txt_sample_id'))
                    $('#txt_sample_id').val("{{ old('txt_sample_id') }}").trigger('change');
                @endif
            });
        </script>
    </div>
@endsection
