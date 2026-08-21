@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head d-flex justify-content-between align-items-center mb-3">
                            <h5 class="nk-block-title">Edit Secondary Test</h5>
                            <a href="{{ url()->previous() }}" class="btn btn-primary"><em
                                    class="icon ni ni-back-alt-fill"></em> &nbsp; Back</a>
                        </div>

                        <div class="card card-bordered">
                            <div class="card-inner">
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <strong><em class="icon ni ni-alert-circle"></em> Please fix the following errors:</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('update_secondary_test', $editData->m17_secondary_test_id) }}"
                                    class="form-validate is-alter" method="POST">
                                    @csrf
                                    <div class="row g-4">
                                        {{-- Sample --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_sample_id">Sample <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control form-select @error('txt_edit_sample_id') is-invalid @enderror"
                                                        name="txt_edit_sample_id" id="txt_sample_id" required>
                                                        <option value="">-- Select Sample --</option>
                                                        @foreach ($samples as $sample)
                                                            <option value="{{ $sample->m10_sample_id }}"
                                                                {{ old('txt_edit_sample_id', $editData->m10_sample_id) == $sample->m10_sample_id ? 'selected' : '' }}>
                                                                {{ $sample->m10_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_edit_sample_id')
                                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Group --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_group_id">Group <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control form-select @error('txt_edit_group_id') is-invalid @enderror"
                                                        name="txt_edit_group_id" id="txt_group_id" required>
                                                        <option value="">-- Select Group --</option>
                                                    </select>
                                                </div>
                                                @error('txt_edit_group_id')
                                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Primary Test --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_primary_test_id">Primary Test <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control form-select @error('txt_edit_primary_test_id') is-invalid @enderror"
                                                        name="txt_edit_primary_test_id" id="txt_primary_test_id" required>
                                                        <option value="">-- Select Primary Test --</option>
                                                    </select>
                                                </div>
                                                @error('txt_edit_primary_test_id')
                                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Secondary Test Name --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_name">Secondary Test <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('txt_edit_name') is-invalid @enderror"
                                                        name="txt_edit_name" id="txt_edit_name"
                                                        value="{{ old('txt_edit_name', $editData->m17_name) }}" required>
                                                </div>
                                                @error('txt_edit_name')
                                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Unit --}}
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_unit">Unit</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('txt_edit_unit') is-invalid @enderror"
                                                        name="txt_edit_unit" id="txt_edit_unit"
                                                        value="{{ old('txt_edit_unit', $editData->m17_unit) }}">
                                                </div>
                                                @error('txt_edit_unit')
                                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <button class="btn btn-primary" type="submit">Update Secondary Test</button>
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

    <script>
        $(document).ready(function() {
            let groupUrl = "{{ route('get_groups') }}";
            let primaryUrl = "{{ route('get_primary_tests') }}";

            const initialSample = "{{ old('txt_edit_sample_id', $editData->m10_sample_id) }}";
            const initialGroup = "{{ old('txt_edit_group_id', $editData->m11_group_id ?? $editData->m11_group_code ?? ($editData->primaryTest->m11_group_code ?? '')) }}";
            const initialPrimary = "{{ old('txt_edit_primary_test_id', $editData->m16_primary_test_id) }}";

            function loadGroups(sampleId, selectedGroup = '', callback = null) {
                if (!sampleId) {
                    $('#txt_group_id').html('<option value="">-- Select Group --</option>');
                    $('#txt_primary_test_id').html('<option value="">-- Select Primary Test --</option>');
                    return;
                }
                $('#txt_group_id').html('<option value="">Loading...</option>');
                $.get(groupUrl, {
                    sample_id: sampleId
                }, function(groups) {
                    let options = '<option value="">-- Select Group --</option>';
                    let matchedGroupCode = '';
                    $.each(groups, function(i, group) {
                        let isSelected = (selectedGroup !== '' && (selectedGroup == group.m11_group_code || selectedGroup == group.m11_group_id));
                        if (isSelected) {
                            matchedGroupCode = group.m11_group_code;
                        }
                        options +=
                            `<option value="${group.m11_group_code}" ${isSelected ? 'selected' : ''}>${group.m11_name}</option>`;
                    });
                    $('#txt_group_id').html(options);
                    if (callback) callback(matchedGroupCode || selectedGroup);
                }).fail(function() {
                    $('#txt_group_id').html('<option value="">-- Select Group --</option>');
                });
            }

            function loadPrimaryTests(groupId, selectedPrimary = '') {
                if (!groupId) {
                    $('#txt_primary_test_id').html('<option value="">-- Select Primary Test --</option>');
                    return;
                }
                $('#txt_primary_test_id').html('<option value="">Loading...</option>');
                $.get(primaryUrl, {
                    group_id: groupId
                }, function(tests) {
                    let options = '<option value="">-- Select Primary Test --</option>';
                    $.each(tests, function(i, test) {
                        let isSelected = (selectedPrimary !== '' && selectedPrimary == test.m16_primary_test_id);
                        options +=
                            `<option value="${test.m16_primary_test_id}" ${isSelected ? 'selected' : ''}>${test.m16_name}</option>`;
                    });
                    $('#txt_primary_test_id').html(options);
                }).fail(function() {
                    $('#txt_primary_test_id').html('<option value="">-- Select Primary Test --</option>');
                });
            }

            // Preload existing values in sequence
            if (initialSample) {
                loadGroups(initialSample, initialGroup, function(groupCode) {
                    let targetGroup = groupCode || initialGroup;
                    if (targetGroup) {
                        loadPrimaryTests(targetGroup, initialPrimary);
                    }
                });
            }

            $('#txt_sample_id').on('change', function() {
                loadGroups($(this).val());
                $('#txt_primary_test_id').html('<option value="">-- Select Primary Test --</option>');
            });

            $('#txt_group_id').on('change', function() {
                loadPrimaryTests($(this).val());
            });
        });
    </script>
@endsection
