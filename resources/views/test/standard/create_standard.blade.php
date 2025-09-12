@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Standard</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('create_standard_main') }}" class="form-validate is-alter"
                                    method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_sample_id">Sample <b
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
                                                <label class="form-label" for="txt_group_id">Group <b
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

                                        {{-- Charge --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_method">Method <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_method"
                                                        name="txt_method" value="{{ old('txt_method') }}" required>
                                                </div>
                                                @error('txt_method')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Accreditation --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_is_accredated">Accreditation <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_is_accredated" class="form-select"
                                                        id="txt_is_accredated" required>
                                                        <option value="NO"
                                                            {{ old('txt_is_accredated') == 'NO' ? 'selected' : '' }}>No
                                                        </option>
                                                        <option value="YES"
                                                            {{ old('txt_is_accredated') == 'YES' ? 'selected' : '' }}>Yes
                                                        </option>
                                                    </select>
                                                </div>
                                                @error('txt_is_accredated')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Description --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_description">Description</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_description"
                                                        name="txt_description" value="{{ old('txt_description') }}">
                                                </div>
                                                @error('txt_description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Alias --}}
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

                                        {{-- Accreditation Date --}}
                                        <div class="col-md-3" id="accreditation_expiry_wrapper" style="display:none;">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_acc_exp">Accreditation Expiry <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="date" class="form-control" name="txt_acc_exp"
                                                        id="txt_acc_exp">
                                                </div>
                                                @error('txt_acc_exp')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Weight --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_detection_limit">Detection
                                                    Limit</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_detection_limit"
                                                        name="txt_detection_limit"
                                                        value="{{ old('txt_detection_limit') }}">
                                                </div>
                                                @error('txt_detection_limit')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Unit --}}
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
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

            // Auto-trigger if old sample value exists (for form resubmission)
            let oldSampleId = "{{ old('txt_sample_id') }}";
            if (oldSampleId) {
                $('#txt_sample_id').val(oldSampleId).trigger('change');
            }
        });

        // Show/Hide Accreditation Expiry based on selection
        $('#txt_is_accredated').on('change', function() {
            if ($(this).val() === 'YES') {
                $('#accreditation_expiry_wrapper').show();
                $('#txt_acc_exp').prop('required', true);
            } else {
                $('#accreditation_expiry_wrapper').hide();
                $('#txt_acc_exp').prop('required', false).val('');
            }
        });
        $('#txt_is_accredated').trigger('change');
    </script>
@endsection
