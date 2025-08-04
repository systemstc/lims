@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head d-flex justify-content-between align-items-center">
                            <h5 class="nk-block-title">Edit Secondary Test</h5>
                            <a href="{{ url()->previous() }}" class="btn btn-primary"><em
                                    class="icon ni ni-back-alt-fill"></em> &nbsp; Back</a>
                        </div>

                        <form action="{{ route('update_secondary_test', $editData->m17_secondary_test_id) }}"
                            method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Sample<b class="text-danger">*</b></label>
                                    <select class="form-select" name="txt_edit_sample_id" id="txt_sample_id">
                                        <option value="">-- Select Sample --</option>
                                        @foreach ($samples as $sample)
                                            <option value="{{ $sample->m10_sample_id }}"
                                                {{ $editData->m10_sample_id == $sample->m10_sample_id ? 'selected' : '' }}>
                                                {{ $sample->m10_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('txt_edit_sample_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Group<b class="text-danger">*</b></label>
                                    <select class="form-select" name="txt_edit_group_id" id="txt_group_id">
                                        <option value="">-- Select Group --</option>
                                    </select>
                                    @error('txt_edit_group_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Test<b class="text-danger">*</b></label>
                                    <select class="form-select" name="txt_edit_test_id" id="txt_test_id">
                                        <option value="">-- Select Test --</option>
                                    </select>
                                    @error('txt_edit_test_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Primary Test<b class="text-danger">*</b></label>
                                    <select class="form-select" name="txt_edit_primary_test_id" id="txt_primary_test_id">
                                        <option value="">-- Select Primary Test --</option>
                                    </select>
                                    @error('txt_edit_primary_test_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Secondary Test<b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" name="txt_edit_name"
                                        value="{{ $editData->m17_name }}">
                                    @error('txt_edit_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Unit</label>
                                    <input type="text" class="form-control" name="txt_edit_unit"
                                        value="{{ $editData->m17_unit }}">
                                    @error('txt_edit_unit')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let groupUrl = "{{ route('get_groups') }}";
            let testUrl = "{{ route('get_tests') }}";
            let primaryUrl = "{{ route('get_primary_tests') }}";

            function loadGroups(sampleId, selectedGroup = '', callback = null) {
                $('#txt_group_id').html('<option value="">Loading...</option>');
                $.get(groupUrl, {
                    sample_id: sampleId
                }, function(groups) {
                    let options = '<option value="">-- Select Group --</option>';
                    $.each(groups, function(i, group) {
                        options +=
                            `<option value="${group.m11_group_id}" ${selectedGroup == group.m11_group_id ? 'selected' : ''}>${group.m11_name}</option>`;
                    });
                    $('#txt_group_id').html(options);
                    if (callback) callback();
                });
            }

            function loadTests(groupId, selectedTest = '', callback = null) {
                $('#txt_test_id').html('<option value="">Loading...</option>');
                $.get(testUrl, {
                    group_id: groupId
                }, function(tests) {
                    let options = '<option value="">-- Select Test --</option>';
                    $.each(tests, function(i, test) {
                        options +=
                            `<option value="${test.m12_test_id}" ${selectedTest == test.m12_test_id ? 'selected' : ''}>${test.m12_name}</option>`;
                    });
                    $('#txt_test_id').html(options);
                    if (callback) callback();
                });
            }

            function loadPrimaryTests(testId, selectedPrimary = '') {
                $('#txt_primary_test_id').html('<option value="">Loading...</option>');
                $.get(primaryUrl, {
                    test_id: testId
                }, function(tests) {
                    let options = '<option value="">-- Select Primary Test --</option>';
                    $.each(tests, function(i, test) {
                        options +=
                            `<option value="${test.m16_primary_test_id}" ${selectedPrimary == test.m16_primary_test_id ? 'selected' : ''}>${test.m16_name}</option>`;
                    });
                    $('#txt_primary_test_id').html(options);
                });
            }

            // Preload in proper sequence
            loadGroups("{{ $editData->m10_sample_id }}", "{{ $editData->m11_group_id }}", function() {
                loadTests("{{ $editData->m11_group_id }}", "{{ $editData->m12_test_id }}", function() {
                    loadPrimaryTests("{{ $editData->m12_test_id }}",
                        "{{ $editData->m16_primary_test_id }}");
                });
            });

            $('#txt_sample_id').on('change', function() {
                loadGroups($(this).val());
                $('#txt_test_id, #txt_primary_test_id').html('<option value="">-- Select --</option>');
            });

            $('#txt_group_id').on('change', function() {
                loadTests($(this).val());
                $('#txt_primary_test_id').html('<option value="">-- Select --</option>');
            });

            $('#txt_test_id').on('change', function() {
                loadPrimaryTests($(this).val());
            });
        });
    </script>
@endsection
