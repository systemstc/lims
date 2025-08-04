@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Test</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('create_test') }}" class="form-validate is-alter" method="POST">
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

                                        {{-- Department --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_department_id">Department<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_department_id" class="form-select"
                                                        id="txt_department_id" required>
                                                        <option value="">-- Select Department --</option>
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department->m13_department_id }}"
                                                                {{ old('txt_department_id') == $department->m13_department_id ? 'selected' : '' }}>
                                                                {{ $department->m13_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_department_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Test Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Test Name<b
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
                                        {{-- CATEGORY --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_category_id">Category<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_category_id" class="form-select" id="txt_category_id"
                                                        required>
                                                        <option value="">-- Select Category --</option>
                                                        <option value="NUMERIC"
                                                            {{ old('txt_category_id') == 'NUMERIC' ? 'selected' : '' }}>
                                                            NUMERIC</option>
                                                        <option value="QUALITATIVE SINGLE"
                                                            {{ old('txt_category_id') == 'QUALITATIVE SINGLE' ? 'selected' : '' }}>
                                                            QUALITATIVE SINGLE</option>
                                                        <option value="QUALITATIVE MULTI"
                                                            {{ old('txt_category_id') == 'QUALITATIVE MULTI' ? 'selected' : '' }}>
                                                            QUALITATIVE MULTI</option>
                                                    </select>
                                                </div>
                                                @error('txt_category_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Input Mode --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_input_mode">Input Mode<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_input_mode" class="form-select" id="txt_input_mode"
                                                        required>
                                                        <option value="">-- Select Method Type --</option>
                                                        <option value="SINGLE"
                                                            {{ old('txt_input_mode') == 'SINGLE' ? 'selected' : '' }}>
                                                            SINGLE</option>
                                                        <option value="MULTI"
                                                            {{ old('txt_input_mode') == 'MULTI' ? 'selected' : '' }}>MULTI
                                                        </option>
                                                        <option value="MULTI STAGE"
                                                            {{ old('txt_input_mode') == 'MULTI STAGE' ? 'selected' : '' }}>
                                                            MULTI STAGE</option>
                                                    </select>
                                                </div>
                                                @error('txt_input_mode')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Test Stages --}}
                                        <div class="col-md-4" id="stages-wrapper" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_stages">Number of Stages<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="number" class="form-control" id="txt_stages"
                                                        name="txt_stages" value="{{ old('txt_stages') }}" min="1"
                                                        max="10">
                                                </div>
                                                @error('txt_stages')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Dynamic Stages Configuration --}}
                                        <div class="col-md-12" id="stages-config-wrapper" style="display: none;">
                                            <div class="card border">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Configure Stages</h6>
                                                </div>
                                                <div class="card-body" id="stages-config-content">
                                                    <!-- Dynamic stage configuration will be inserted here -->
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Output Matrix --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Output Matrix <b
                                                        class="text-danger">*</b></label>
                                                <div id="output-matrix-wrapper">
                                                    @php
                                                        $oldOutputs = old('txt_output_matrix')
                                                            ? json_decode(old('txt_output_matrix'), true)
                                                            : [['name' => '', 'value' => '']];
                                                    @endphp

                                                    @foreach ($oldOutputs as $index => $output)
                                                        <div class="row g-2 align-items-center output-row mb-2">
                                                            <div class="col-md-5">
                                                                <input type="text"
                                                                    name="txt_output_matrix[{{ $index }}][name]"
                                                                    class="form-control" placeholder="Output Name"
                                                                    value="{{ $output['name'] ?? '' }}" required>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text"
                                                                    name="txt_output_matrix[{{ $index }}][value]"
                                                                    class="form-control" placeholder="Output Value"
                                                                    value="{{ $output['value'] ?? '' }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button"
                                                                    class="btn btn-danger remove-output">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-primary mt-2" id="add-output">Add
                                                    Output</button>
                                                @error('txt_output_matrix')
                                                    <span class="text-danger d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Charge --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_charge">Charge<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_charge"
                                                        name="txt_charge" value="{{ old('txt_charge') }}" required>
                                                </div>
                                                @error('txt_charge')
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
                                                <label class="form-label" for="txt_alias">Alias</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" name="txt_alias" class="form-control"
                                                        id="txt_alias" value="{{ old('txt_alias') }}">
                                                </div>
                                                @error('txt_alias')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Weight --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_weight">Weight</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_weight"
                                                        name="txt_weight" value="{{ old('txt_weight') }}">
                                                </div>
                                                @error('txt_weight')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Unit --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_unit">Unit</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_unit"
                                                        name="txt_unit" value="{{ old('txt_unit') }}">
                                                </div>
                                                @error('txt_unit')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Instrument --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_instrument">Instrument</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_instrument"
                                                        name="txt_instrument" value="{{ old('txt_instrument') }}">
                                                </div>
                                                @error('txt_instrument')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Remark --}}
                                        <div class="col-md-8">
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
                                            <button type="submit" class="btn btn-primary">Save Test</button>
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

            function toggleStagesField() {
                const selectedValue = $('#txt_input_mode').val();
                if (selectedValue === 'MULTI STAGE') {
                    $('#stages-wrapper').show();
                    $('#txt_stages').attr('required', true);
                } else {
                    $('#stages-wrapper').hide();
                    $('#stages-config-wrapper').hide();
                    $('#txt_stages').removeAttr('required').val('');
                    $('#stages-config-content').empty();
                }
            }

            function generateStageConfiguration() {
                const stageCount = parseInt($('#txt_stages').val());
                const configContent = $('#stages-config-content');

                if (stageCount && stageCount > 0 && stageCount <= 50) {
                    $('#stages-config-wrapper').show();
                    configContent.empty();

                    for (let i = 1; i <= stageCount; i++) {
                        const stageHtml = `
                            <div class="stage-config mb-4 p-3 border rounded">
                                <h6 class="mb-3 text-primary">Stage ${i} Configuration</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Stage ${i} Name <b class="text-danger">*</b></label>
                                            <input type="text" name="stages[${i-1}][name]" class="form-control" 
                                                   placeholder="Enter stage ${i} name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Input Variables <b class="text-danger">*</b></label>
                                            <input name="stages[${i-1}][inputs]" class="form-control" 
                                                      placeholder="Enter input variables (comma separated)" 
                                                      rows="3" required>
                                            <small class="text-muted">Enter input variables separated by commas</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Output Variables</label>
                                            <input name="stages[${i-1}][outputs]" class="form-control" 
                                                      placeholder="Enter output variables (comma separated)" 
                                                      rows="3">
                                            <small class="text-muted">Enter output variables separated by commas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        configContent.append(stageHtml);
                    }
                } else {
                    $('#stages-config-wrapper').hide();
                    configContent.empty();
                }
            }

            // Event handlers
            toggleStagesField(); // check on page load
            $('#txt_input_mode').on('change', toggleStagesField);

            $('#txt_stages').on('input', function() {
                generateStageConfiguration();
            });

            // Dynamic stage inputs/outputs management - REMOVED since we're using simple text areas

            // For output matrix
            let outputIndex = $('#output-matrix-wrapper .output-row').length || 0;

            $('#add-output').click(function() {
                let newRow = `
                <div class="row g-2 align-items-center output-row mb-2">
                    <div class="col-md-5">
                        <input type="text" name="txt_output_matrix[${outputIndex}][name]" class="form-control" placeholder="Output Name" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="txt_output_matrix[${outputIndex}][value]" class="form-control" placeholder="Output Value" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-output">Remove</button>
                    </div>
                </div>`;
                $('#output-matrix-wrapper').append(newRow);
                outputIndex++;
            });

            $(document).on('click', '.remove-output', function() {
                $(this).closest('.output-row').remove();
            });

            // Sample -> Group AJAX
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

            // Load old stages data if form was resubmitted
            let oldStages = "{{ old('txt_stages') }}";
            if (oldStages) {
                $('#txt_stages').val(oldStages);
                generateStageConfiguration();
            }
        });
    </script>
@endsection
