@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Edit Test</h4>
                            <a href="{{ route('view_tests') }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('update_test', $test->m12_test_id) }}" class="form-validate is-alter"
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
                                                        <option value="">Select Sample</option>
                                                        @foreach ($samples as $sample)
                                                            <option value="{{ $sample->m10_sample_id }}"
                                                                {{ old('txt_sample_id', $test->m10_sample_id) == $sample->m10_sample_id ? 'selected' : '' }}>
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
                                                        <option value="">Select Group</option>
                                                    </select>
                                                </div>
                                                @error('txt_group_id')
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
                                                        name="txt_name" value="{{ old('txt_name', $test->m12_name) }}"
                                                        placeholder="Enter Test Name" required>
                                                </div>
                                                @error('txt_name')
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
                                                        id="txt_alias" value="{{ old('txt_alias', $test->m12_alias) }}"
                                                        placeholder="Enter Short Name (Optional)">
                                                </div>
                                                @error('txt_alias')
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
                                                        name="txt_description"
                                                        value="{{ old('txt_description', $test->m12_description) }}"
                                                        placeholder="Enter Description (Optional)">
                                                </div>
                                                @error('txt_description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Standards Section --}}
                                        <div class="col-md-4" id="standards-section">
                                            <div class="form-group">
                                                <label class="form-label">Standards <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="standard-search" class="form-control"
                                                        placeholder="Search or enter standard name" autocomplete="off">
                                                    <div class="search-dropdown standards-dropdown" style="display: none;">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <div id="standards-selected-container"></div>
                                                    <div id="standards-create-section" class="mt-2"
                                                        style="display: none;">
                                                        <div class="alert alert-info">
                                                            <small>Standard not found. Click to create: <strong
                                                                    id="standard-create-name"></strong></small>
                                                            <button type="button" class="btn btn-sm btn-primary ms-2"
                                                                id="create-standard-btn">Create</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('standard_ids')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Primary Tests Section --}}
                                        <div class="col-md-4" id="primary-tests-section">
                                            <div class="form-group">
                                                <label class="form-label">Primary Tests <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="primary-test-search" class="form-control"
                                                        placeholder="Search or enter primary test name"
                                                        autocomplete="off">
                                                    <div class="search-dropdown primary-tests-dropdown"
                                                        style="display: none;"></div>
                                                </div>
                                                <div class="mt-2">
                                                    <div id="primary-tests-selected-container"></div>
                                                    <div id="primary-tests-create-section" class="mt-2"
                                                        style="display: none;">
                                                        <div class="alert alert-info">
                                                            <small>Primary test not found. Click to create: <strong
                                                                    id="primary-test-create-name"></strong></small>
                                                            <button type="button" class="btn btn-sm btn-primary ms-2"
                                                                id="create-primary-test-btn">Create</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('primary_test_ids')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Secondary Tests Section --}}
                                        <div class="col-md-4" id="secondary-tests-section">
                                            <div class="form-group">
                                                <label class="form-label">Secondary Tests</label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="secondary-test-search" class="form-control"
                                                        placeholder="Search or enter secondary test name"
                                                        autocomplete="off">
                                                    <div class="search-dropdown secondary-tests-dropdown"
                                                        style="display: none;"></div>
                                                </div>
                                                <div class="mt-2">
                                                    <div id="secondary-tests-selected-container"></div>
                                                    <div id="secondary-tests-create-section" class="mt-2"
                                                        style="display: none;">
                                                        <div class="alert alert-info">
                                                            <small>Secondary test not found. Click to create: <strong
                                                                    id="secondary-test-create-name"></strong></small>
                                                            <button type="button" class="btn btn-sm btn-primary ms-2"
                                                                id="create-secondary-test-btn">Create</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('secondary_test_ids')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Result Type --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_category_id">Result Type<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_category_id" class="form-select"
                                                        id="txt_category_id" required>
                                                        <option value="">Select Category</option>
                                                        <option value="NUMERIC"
                                                            {{ old('txt_category_id', $test->m12_category) == 'NUMERIC' ? 'selected' : '' }}>
                                                            Numeric</option>
                                                        <option value="QUALITATIVE SINGLE"
                                                            {{ old('txt_category_id', $test->m12_category) == 'QUALITATIVE SINGLE' ? 'selected' : '' }}>
                                                            Qualitative Single</option>
                                                        <option value="QUALITATIVE MULTI"
                                                            {{ old('txt_category_id', $test->m12_category) == 'QUALITATIVE MULTI' ? 'selected' : '' }}>
                                                            Qualitative Multi</option>
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
                                                        <option value="">Select Input Mode</option>
                                                        <option value="SINGLE"
                                                            {{ old('txt_input_mode', $test->m12_input_mode) == 'SINGLE' ? 'selected' : '' }}>
                                                            Single</option>
                                                        <option value="MULTI"
                                                            {{ old('txt_input_mode', $test->m12_input_mode) == 'MULTI' ? 'selected' : '' }}>
                                                            Multi
                                                        </option>
                                                        <option value="MULTI STAGE"
                                                            {{ old('txt_input_mode', $test->m12_input_mode) == 'MULTI STAGE' ? 'selected' : '' }}>
                                                            Multi Stage</option>
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
                                                        name="txt_stages"
                                                        value="{{ old('txt_stages', $test->m12_stages) }}"
                                                        placeholder="Enter Number of Stages">
                                                </div>
                                                @error('txt_stages')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Results Section --}}
                                        {{-- <div class="col-md-4" id="results-section">
                                            <div class="form-group">
                                                <label class="form-label">Results <b class="text-danger">*</b></label>
                                                <div id="results-wrapper">
                                                    @php
                                                        $oldResults = old('results');
                                                        if (!$oldResults && isset($test->m12_result)) {
                                                            $oldResults = json_decode($test->m12_result, true);
                                                        }
                                                        if (!is_array($oldResults) || empty($oldResults)) {
                                                            $oldResults = [['name' => '']];
                                                        }
                                                    @endphp
                                                    @foreach ($oldResults as $index => $result)
                                                        <div class="row g-2 align-items-center results-row mb-2">
                                                            <div class="col-md-10">
                                                                <input type="text"
                                                                    name="results[{{ $index }}][name]"
                                                                    class="form-control" placeholder="Result name"
                                                                    value="{{ $result['name'] ?? '' }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button"
                                                                    class="btn btn-primary btn-xs add-results">+</button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-xs remove-results">-</button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('results')
                                                    <span class="text-danger d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Results <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="results-input" class="form-control"
                                                        placeholder="Enter result name" autocomplete="off">
                                                </div>
                                                <div id="results-tags-container" class="mt-2"></div>
                                                <input type="hidden" name="results" id="results-hidden"
                                                    value="{{ old('results', $model->results ?? '') }}">
                                                @error('results')
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
                                                        name="txt_charge"
                                                        value="{{ old('txt_charge', $test->m12_charge) }}"
                                                        placeholder="Enter Testing Charges" required>
                                                </div>
                                                @error('txt_charge')
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
                                                        name="txt_weight"
                                                        value="{{ old('txt_weight', $test->m12_weight) }}"
                                                        placeholder="Enter Wright (Optional)">
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
                                                        name="txt_unit" value="{{ old('txt_unit', $test->m12_unit) }}"
                                                        placeholder="Enter Unit (Optional)">
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
                                                        name="txt_instrument"
                                                        value="{{ old('txt_instrument', $test->m12_instrument) }}"
                                                        placeholder="Enter Instrument Name (Optional)">
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
                                                        name="txt_remark"
                                                        value="{{ old('txt_remark', $test->m12_remark) }}"
                                                        placeholder="Enter Remark (Optional)">
                                                </div>
                                                @error('txt_remark')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Update Test</button>
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

    <style>
        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .search-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .search-dropdown-item:last-child {
            border-bottom: none;
        }

        .form-control-wrap {
            position: relative;
        }

        .selected-item {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 4px;
            font-size: 12px;
        }

        .selected-item .remove-item {
            margin-left: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .selected-item .remove-item:hover {
            color: #ff6b6b;
        }
    </style>

    <script>
        $(document).ready(function() {
            let selectedSampleId = '';
            let selectedGroupId = '';

            $('#txt_sample_id').on('change', function() {
                selectedSampleId = $(this).val();

            });
            let defaultGroupId = @json($test->m11_group_id ?? '');
            $('#txt_group_id').on('change', function() {
                selectedGroupId = $(this).val();
            });
            if (selectedGroupId === '') {
                selectedGroupId = defaultGroupId;
            }
            // Storage for selected items
            let selectedStandards = [];
            let selectedPrimaryTests = [];
            let selectedSecondaryTests = [];

            // Parse existing data from database - Fixed null handling
            @php
                $filteredStandardIds = array_filter(explode(',', $test->m15_standard_id ?? ''), function ($val) {
                    return !empty(trim($val));
                });
                $filteredPrimaryTestIds = array_filter(explode(',', $test->m16_primary_test_id ?? ''), function ($val) {
                    return !empty(trim($val));
                });
                $filteredSecondaryTestIds = array_filter(explode(',', $test->m17_secondary_test_id ?? ''), function ($val) {
                    return !empty(trim($val));
                });
            @endphp
            const existingStandardIds = @json(array_values($filteredStandardIds));
            const existingPrimaryTestIds = @json(array_values($filteredPrimaryTestIds));
            const existingSecondaryTestIds = @json(array_values($filteredSecondaryTestIds));

            console.log('Existing IDs:', {
                standards: existingStandardIds,
                primaryTests: existingPrimaryTestIds,
                secondaryTests: existingSecondaryTestIds
            });

            // Show/hide stages based on input mode
            function toggleStagesField() {
                var selectedValue = $('#txt_input_mode').val();
                if (selectedValue === 'MULTI STAGE') {
                    $('#stages-wrapper').slideDown();
                    $('#txt_stages').attr('required', true);
                } else {
                    $('#stages-wrapper').slideUp();
                    $('#txt_stages').removeAttr('required');
                }
            }

            $('#txt_input_mode').on('change', toggleStagesField);

            // Initialize on page load
            toggleStagesField();

            // Results functionality
            let resultsIndex = $('#results-wrapper .results-row').length || 0;

            $(document).on('click', '.add-results', function() {
                let newRow = `
                        <div class="row g-2 align-items-center results-row mb-2">
                            <div class="col-md-10">
                                <input type="text" name="results[${resultsIndex}][name]" class="form-control" placeholder="Result name" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm add-results me-1">+</button>
                                <button type="button" class="btn btn-danger btn-sm remove-results">-</button>
                            </div>
                        </div>`;
                $('#results-wrapper').append(newRow);
                resultsIndex++;
            });

            $(document).on('click', '.remove-results', function() {
                if ($('#results-wrapper .results-row').length > 1) {
                    $(this).closest('.results-row').remove();
                    reindexResults(); // Fixed: Call reindexResults here
                }
            });

            // Search functionality with timeout
            let searchTimeout;

            // Standards search and management
            $('#standard-search').on('input', function() {
                const query = $(this).val();
                const dropdown = $('.standards-dropdown');
                const createSection = $('#standards-create-section');

                clearTimeout(searchTimeout);

                if (query.length < 1) {
                    dropdown.hide().empty();
                    createSection.hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('search_standards') }}",
                        type: "GET",
                        data: {
                            query: query
                        },
                        success: function(data) {
                            dropdown.empty();
                            createSection.hide();

                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    if (!selectedStandards.find(s => s.id ==
                                            item.id)) {
                                        dropdown.append(
                                            `<div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                                        );
                                    }
                                });
                                dropdown.show();
                            } else {
                                $('#standard-create-name').text(query);
                                createSection.show();
                                dropdown.hide();
                            }
                        },
                        error: function() {
                            dropdown.hide();
                            createSection.hide();
                        }
                    });
                }, 300);
            });

            // Primary tests search and management
            $('#primary-test-search').on('input', function() {
                const query = $(this).val();
                const dropdown = $('.primary-tests-dropdown');
                const createSection = $('#primary-tests-create-section');

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    dropdown.hide().empty();
                    createSection.hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('search_primary_tests') }}",
                        type: "GET",
                        data: {
                            query: query
                        },
                        success: function(data) {
                            dropdown.empty();
                            createSection.hide();

                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    if (!selectedPrimaryTests.find(s => s.id ==
                                            item.id)) {
                                        dropdown.append(
                                            `<div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                                        );
                                    }
                                });
                                dropdown.show();
                            } else {
                                $('#primary-test-create-name').text(query);
                                createSection.show();
                                dropdown.hide();
                            }
                        },
                        error: function() {
                            dropdown.hide();
                            createSection.hide();
                        }
                    });
                }, 300);
            });

            // Secondary tests search and management
            $('#secondary-test-search').on('input', function() {
                const query = $(this).val();
                const dropdown = $('.secondary-tests-dropdown');
                const createSection = $('#secondary-tests-create-section');

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    dropdown.hide().empty();
                    createSection.hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('search_secondary_tests') }}",
                        type: "GET",
                        data: {
                            query: query
                        },
                        success: function(data) {
                            dropdown.empty();
                            createSection.hide();

                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    if (!selectedSecondaryTests.find(s => s
                                            .id == item.id)) {
                                        dropdown.append(
                                            `<div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                                        );
                                    }
                                });
                                dropdown.show();
                            } else {
                                $('#secondary-test-create-name').text(query);
                                createSection.show();
                                dropdown.hide();
                            }
                        },
                        error: function() {
                            dropdown.hide();
                            createSection.hide();
                        }
                    });
                }, 300);
            });

            // Handle dropdown item selection
            $(document).on('click', '.search-dropdown-item', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const dropdown = $(this).parent();

                if (dropdown.hasClass('standards-dropdown')) {
                    addSelectedItem('standards', id, name);
                    $('#standard-search').val('');
                } else if (dropdown.hasClass('primary-tests-dropdown')) {
                    addSelectedItem('primary-tests', id, name);
                    $('#primary-test-search').val('');
                } else if (dropdown.hasClass('secondary-tests-dropdown')) {
                    addSelectedItem('secondary-tests', id, name);
                    $('#secondary-test-search').val('');
                }

                dropdown.hide();
            });

            // Create new items
            $('#create-standard-btn').on('click', function() {
                const name = $('#standard-create-name').text();
                createNewItem('standards', name);
            });

            $('#create-primary-test-btn').on('click', function() {
                const name = $('#primary-test-create-name').text();
                createNewItem('primary_tests', name);
            });

            $('#create-secondary-test-btn').on('click', function() {
                const name = $('#secondary-test-create-name').text();
                createNewItem('secondary_tests', name);
            });

            // Function to create new items via AJAX
            function createNewItem(type, name) {
                let url = '';
                let searchInput = '';
                let createSection = '';

                switch (type) {
                    case 'standards':
                        url = "{{ route('create_standard') }}";
                        searchInput = '#standard-search';
                        createSection = '#standards-create-section';
                        break;
                    case 'primary_tests':
                        url = "{{ route('create_primary_test') }}";
                        searchInput = '#primary-test-search';
                        createSection = '#primary-tests-create-section';
                        break;
                    case 'secondary_tests':
                        url = "{{ route('create_secondary_test') }}";
                        searchInput = '#secondary-test-search';
                        createSection = '#secondary-tests-create-section';
                        break;
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        name: name,
                        sampleId: selectedSampleId,
                        groupId: selectedGroupId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            addSelectedItem(type.replace('_', '-'), response.data.id, response.data
                                .name);
                            $(searchInput).val('');
                            $(createSection).hide();

                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Item created successfully');
                            } else {
                                alert(response.message || 'Item created successfully');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Failed to create item');
                            } else {
                                alert(response.message || 'Failed to create item');
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to create item';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    }
                });
            }

            // Function to add selected items
            function addSelectedItem(type, id, name) {
                let container = '';
                let inputName = '';
                let selectedArray = null;

                switch (type) {
                    case 'standards':
                        container = '#standards-selected-container';
                        inputName = 'standard_ids';
                        selectedArray = selectedStandards;
                        break;
                    case 'primary-tests':
                        container = '#primary-tests-selected-container';
                        inputName = 'primary_test_ids';
                        selectedArray = selectedPrimaryTests;
                        break;
                    case 'secondary-tests':
                        container = '#secondary-tests-selected-container';
                        inputName = 'secondary_test_ids';
                        selectedArray = selectedSecondaryTests;
                        break;
                }

                // Check if already selected
                if (selectedArray.find(item => item.id == id)) {
                    return;
                }

                // Add to selected array
                selectedArray.push({
                    id: id,
                    name: name
                });

                // Create visual element
                const selectedItem = $(`
                <span class="selected-item" data-id="${id}" data-type="${type}">
                    ${name}
                    <span class="remove-item" title="Remove">&times;</span>
                    <input type="hidden" name="${inputName}[]" value="${id}">
                </span>
            `);

                $(container).append(selectedItem);
            }

            // Handle removing selected items
            $(document).on('click', '.remove-item', function() {
                const parent = $(this).parent();
                const id = parent.data('id');
                const type = parent.data('type');

                // Remove from appropriate array
                switch (type) {
                    case 'standards':
                        selectedStandards = selectedStandards.filter(item => item.id != id);
                        break;
                    case 'primary-tests':
                        selectedPrimaryTests = selectedPrimaryTests.filter(item => item.id != id);
                        break;
                    case 'secondary-tests':
                        selectedSecondaryTests = selectedSecondaryTests.filter(item => item.id != id);
                        break;
                }

                parent.remove();
            });

            // Hide dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.form-control-wrap').length) {
                    $('.search-dropdown').hide();
                    $('#standards-create-section').hide();
                    $('#primary-tests-create-section').hide();
                    $('#secondary-tests-create-section').hide();
                }
            });

            // Load existing data on page load
            function loadExistingData() {
                if (existingStandardIds && existingStandardIds.length > 0) {
                    // console.log(existingStandardIds); 
                    $.ajax({
                        url: "{{ route('get_standards_by_ids') }}",
                        type: "GET",
                        data: {
                            ids: existingStandardIds
                        },
                        success: function(data) {
                            data.forEach(function(item) {
                                addSelectedItem('standards', item.id, item.name);
                            });
                        },
                        error: function(xhr) {
                            console.error('Failed to load existing standards:', xhr);
                        }
                    });
                }


                // Load existing primary tests
                if (existingPrimaryTestIds && existingPrimaryTestIds.length > 0) {
                    $.ajax({
                        url: "{{ route('get_primary_tests_by_ids') }}",
                        type: "GET",
                        data: {
                            ids: existingPrimaryTestIds
                        },
                        success: function(data) {
                            data.forEach(function(item) {
                                addSelectedItem('primary-tests', item.id, item.name);
                            });
                        },
                        error: function(xhr) {
                            console.error('Failed to load existing primary tests:', xhr);
                        }
                    });
                }

                // Load existing secondary tests
                if (existingSecondaryTestIds && existingSecondaryTestIds.length > 0) {
                    $.ajax({
                        url: "{{ route('get_secondary_tests_by_ids') }}",
                        type: "GET",
                        data: {
                            ids: existingSecondaryTestIds
                        },
                        success: function(data) {
                            data.forEach(function(item) {
                                addSelectedItem('secondary-tests', item.id, item.name);
                            });
                        },
                        error: function(xhr) {
                            console.error('Failed to load existing secondary tests:', xhr);
                        }
                    });
                }
            }

            // Load existing data when page loads
            loadExistingData();

            // Handle sample change to load groups - Fixed route name
            $('#txt_sample_id').on('change', function() {
                const sampleId = $(this).val();
                const groupSelect = $('#txt_group_id');

                // Clear existing options
                groupSelect.empty().append('<option value="">-- Select Group --</option>');

                if (sampleId) {
                    $.ajax({
                        url: "{{ route('get_groups') }}",
                        type: "GET",
                        data: {
                            sample_id: sampleId
                        },
                        success: function(data) {
                            data.forEach(function(group) {
                                const isSelected =
                                    "{{ old('txt_group_id', $test->m11_group_id ?? '') }}" ==
                                    group.m11_group_id ? 'selected' : '';
                                groupSelect.append(
                                    `<option value="${group.m11_group_id}" ${isSelected}>${group.m11_name}</option>`
                                );
                            });
                        },
                        error: function() {
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Failed to load groups');
                            } else {
                                alert('Failed to load groups');
                            }
                        }
                    });
                }
            });

            // Trigger sample change on page load to populate groups
            if ($('#txt_sample_id').val()) {
                $('#txt_sample_id').trigger('change');
            }

            // Form validation before submit
            $('form').on('submit', function(e) {
                // Check if at least one standard is selected
                if (selectedStandards.length === 0) {
                    e.preventDefault();
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Please select at least one standard');
                    } else {
                        alert('Please select at least one standard');
                    }
                    return false;
                }

                // Check if at least one primary test is selected
                if (selectedPrimaryTests.length === 0) {
                    e.preventDefault();
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Please select at least one primary test');
                    } else {
                        alert('Please select at least one primary test');
                    }
                    return false;
                }

                // Check if results are properly filled
                let hasEmptyResults = false;
                $('#results-wrapper input[name*="[name]"]').each(function() {
                    if ($(this).val().trim() === '') {
                        hasEmptyResults = true;
                        return false;
                    }
                });

                if (hasEmptyResults) {
                    e.preventDefault();
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Please fill all result names or remove empty result fields');
                    } else {
                        alert('Please fill all result names or remove empty result fields');
                    }
                    return false;
                }

                return true;
            });

            // Auto-resize results index when removing items
            function reindexResults() {
                $('#results-wrapper .results-row').each(function(index) {
                    $(this).find('input[name*="[name]"]').attr('name', `results[${index}][name]`);
                });
                resultsIndex = $('#results-wrapper .results-row').length;
            }

            // Prevent form submission on Enter key in search fields
            $('#standard-search, #primary-test-search, #secondary-test-search').on('keydown', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            // Handle Enter key in search fields to select first item
            $('#standard-search, #primary-test-search, #secondary-test-search').on('keyup', function(e) {
                if (e.which === 13) {
                    const dropdown = $(this).siblings('.search-dropdown');
                    const firstItem = dropdown.find('.search-dropdown-item:first');
                    if (firstItem.length > 0) {
                        firstItem.click();
                    }
                }
            });

        }); // End of document ready
    </script>

    <script>
        let resultTags = [];

        function updateResultsView() {
            let $container = $('#results-tags-container');
            $container.empty();

            resultTags.forEach((tag, index) => {
                let $badge = $(`
            <span class="selected-item">
                ${tag}
                <span class="remove-tag ms-1" data-index="${index}" style="cursor:pointer;">&times;</span>
            </span>
        `);
                $container.append($badge);
            });

            $('#results-hidden').val(resultTags.join(','));
        }

        function removeResultTag(index) {
            resultTags.splice(index, 1);
            updateResultsView();
        }

        $('#results-input').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',' || e.key === 'Tab') {
                e.preventDefault();
                let value = $(this).val().trim();
                if (value && !resultTags.includes(value)) {
                    resultTags.push(value);
                    updateResultsView();
                }
                $(this).val('');
            }
        });

        $(document).on('click', '.remove-tag', function() {
            let index = $(this).data('index');
            removeResultTag(index);
        });

        $('form').on('submit', function() {
            $('#results-hidden').val(resultTags.join(','));
        });

        $(document).ready(function() {
            let prefilled = $('#results-hidden').val();
            if (prefilled) {
                resultTags = prefilled.split(',').filter(Boolean);
                updateResultsView();
            }
        });
    </script>
@endsection
