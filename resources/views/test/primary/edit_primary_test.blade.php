@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Edit Primary Test</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('update_primary_test', $primaryTest->m16_primary_test_id) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="txt_edit_id"
                                        value="{{ $primaryTest->m16_primary_test_id }}">

                                    <div class="row g-gs">
                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_sample_id">Sample <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_edit_sample_id" id="txt_edit_sample_id"
                                                        class="form-select" required>
                                                        <option value="">-- Select Sample --</option>
                                                        @foreach ($samples as $sample)
                                                            <option value="{{ $sample->m10_sample_id }}"
                                                                {{ old('txt_edit_sample_id', $primaryTest->m10_sample_id) == $sample->m10_sample_id ? 'selected' : '' }}>
                                                                {{ $sample->m10_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_edit_sample_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Group --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_group_id">Group <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_edit_group_id" id="txt_edit_group_id"
                                                        class="form-select" required>
                                                        <option value="">-- Select Group --</option>
                                                        {{-- Options will be populated by JS --}}
                                                    </select>
                                                </div>
                                                @error('txt_edit_group_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Test --}}
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_test_id">Test <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_edit_test_id" id="txt_edit_test_id"
                                                        class="form-select" required>
                                                        <option value="">-- Select Test --</option>
                                                    </select>
                                                </div>
                                                @error('txt_edit_test_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        {{-- Parameter Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_name">Parameter Name <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_name"
                                                        name="txt_edit_name"
                                                        value="{{ old('txt_edit_name', $primaryTest->m16_name) }}" required>
                                                </div>
                                                @error('txt_edit_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Unit --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_unit">Unit</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_unit"
                                                        name="txt_edit_unit"
                                                        value="{{ old('txt_edit_unit', $primaryTest->m16_unit) }}">
                                                </div>
                                                @error('txt_edit_unit')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Requirement --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_requirement">Requirement</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_requirement"
                                                        name="txt_edit_requirement"
                                                        value="{{ old('txt_edit_requirement', $primaryTest->m16_requirement) }}">
                                                </div>
                                                @error('txt_edit_requirement')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Remark --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_remark">Remark</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_remark"
                                                        name="txt_edit_remark"
                                                        value="{{ old('txt_edit_remark', $primaryTest->m16_remark) }}">
                                                </div>
                                                @error('txt_edit_remark')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-4">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Update Primary
                                                    Test</button>
                                            </div>
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

    {{-- AJAX Dropdown Logic --}}
    <script>
        $(document).ready(function() {
            const groupDropdown = $('#txt_edit_group_id');
            const testDropdown = $('#txt_edit_test_id');
            const sampleId = $('#txt_edit_sample_id').val();
            const selectedGroupId = "{{ old('txt_edit_group_id', $primaryTest->m11_group_id) }}";
            const selectedTestId = "{{ old('txt_edit_test_id', $primaryTest->m12_test_id) }}";

            function loadGroups(sampleId) {
                $.ajax({
                    url: "{{ route('get_groups') }}",
                    type: "GET",
                    data: {
                        sample_id: sampleId
                    },
                    success: function(data) {
                        groupDropdown.empty().append('<option value="">-- Select Group --</option>');
                        $.each(data, function(_, group) {
                            const selected = group.m11_group_id == selectedGroupId ?
                                'selected' : '';
                            groupDropdown.append(
                                `<option value="${group.m11_group_id}" ${selected}>${group.m11_name}</option>`
                                );
                        });
                        groupDropdown.trigger('change');
                    }
                });
            }

            function loadTests(groupId) {
                $.ajax({
                    url: "{{ route('get_tests') }}",
                    type: "GET",
                    data: {
                        group_id: groupId
                    },
                    success: function(data) {
                        testDropdown.empty().append('<option value="">-- Select Test --</option>');
                        $.each(data, function(_, test) {
                            const selected = test.m12_test_id == selectedTestId ? 'selected' :
                                '';
                            testDropdown.append(
                                `<option value="${test.m12_test_id}" ${selected}>${test.m12_name}</option>`
                                );
                        });
                    }
                });
            }

            $('#txt_edit_sample_id').on('change', function() {
                loadGroups($(this).val());
            });

            $('#txt_edit_group_id').on('change', function() {
                loadTests($(this).val());
            });

            if (sampleId) loadGroups(sampleId);
        });
    </script>
@endsection
