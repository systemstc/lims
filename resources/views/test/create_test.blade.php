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
                                                        <option value="">Select Sample</option>
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
                                                        <option value="">Select Group</option>
                                                    </select>
                                                </div>
                                                @error('txt_group_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Department --}}
                                        {{-- <div class="col-md-4">
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
                                        </div> --}}

                                        {{-- Test Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Test Name<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_name"
                                                        name="txt_name" value="{{ old('txt_name') }}"
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
                                                        id="txt_alias" value="{{ old('txt_alias') }}"
                                                        placeholder="Short Name of Test (Optional)">
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
                                                        name="txt_description" value="{{ old('txt_description') }}"
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

                                        {{-- Results Section --}}
                                        {{-- <div class="col-md-4" id="results-section">
                                            <div class="form-group">
                                                <label class="form-label">Results <b class="text-danger">*</b></label>
                                                <div id="results-wrapper">
                                                    @php
                                                        $oldResults = old('results')
                                                            ? old('results')
                                                            : [['name' => '']];
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

                                        {{-- CATEGORY --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_category_id">Result Type<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_category_id" class="form-select"
                                                        id="txt_category_id" required>
                                                        <option value="">Select Category</option>
                                                        <option value="NUMERIC"
                                                            {{ old('txt_category_id') == 'NUMERIC' ? 'selected' : '' }}>
                                                            Numeric</option>
                                                        <option value="QUALITATIVE SINGLE"
                                                            {{ old('txt_category_id') == 'QUALITATIVE SINGLE' ? 'selected' : '' }}>
                                                            Qualitative Single</option>
                                                        <option value="QUALITATIVE MULTI"
                                                            {{ old('txt_category_id') == 'QUALITATIVE MULTI' ? 'selected' : '' }}>
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
                                                        <option value="">Select Method Type</option>
                                                        <option value="SINGLE"
                                                            {{ old('txt_input_mode') == 'SINGLE' ? 'selected' : '' }}>
                                                            Single</option>
                                                        <option value="MULTI"
                                                            {{ old('txt_input_mode') == 'MULTI' ? 'selected' : '' }}>Multi
                                                        </option>
                                                        <option value="MULTI STAGE"
                                                            {{ old('txt_input_mode') == 'MULTI STAGE' ? 'selected' : '' }}>
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
                                                        name="txt_stages" value="{{ old('txt_stages') }}" min="1"
                                                        max="10">
                                                </div>
                                                @error('txt_stages')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

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
                                                        name="txt_charge" value="{{ old('txt_charge') }}"
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
                                                        name="txt_weight" value="{{ old('txt_weight') }}"
                                                        placeholder="Enter Weight (Optional)">
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
                                                        name="txt_unit" value="{{ old('txt_unit') }}"
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
                                                        name="txt_instrument" value="{{ old('txt_instrument') }}"
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
                                                        name="txt_remark" value="{{ old('txt_remark') }}"
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

        .remove-tag:hover {
            color: #ff6b6b;
        }
    </style>

    {{-- <script>
        $(document).ready(function() {
            let selectedSampleId = '';
            let selectedGroupId = '';

            $('#txt_sample_id').on('change', function() {
                selectedSampleId = $(this).val();
            });

            $('#txt_group_id').on('change', function() {
                selectedGroupId = $(this).val();
            });
            // Storage for selected items
            let selectedStandards = [];
            let selectedPrimaryTests = [];
            let selectedSecondaryTests = [];

            // Show/hide stages based on input mode
            $('#stages-wrapper').hide();
            $('#txt_input_mode').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'MULTI STAGE') {
                    $('#stages-wrapper').slideDown();
                    $('#txt_stages').attr('required', true);
                } else {
                    $('#stages-wrapper').slideUp();
                    $('#txt_stages').removeAttr('required');
                    $('#txt_stages').val('');
                }
            });

            if ($('#txt_input_mode').val() === 'MULTI STAGE') {
                $('#stages-wrapper').show();
                $('#txt_stages').attr('required', true);
            }

            // Results functionality (keep as is)
            // let resultsIndex = $('#results-wrapper .results-row').length || 0;

            // $(document).on('click', '.add-results', function() {
            //     let newRow = `
        //     <div class="row g-2 align-items-center results-row mb-2">
        //         <div class="col-md-10">
        //             <input type="text" name="results[${resultsIndex}][name]" class="form-control" placeholder="Result name" required>
        //         </div>
        //         <div class="col-md-2">
        //             <button type="button" class="btn btn-primary btn-sm add-results me-1">+</button>
        //             <button type="button" class="btn btn-danger btn-sm remove-results">-</button>
        //         </div>
        //     </div>`;
            //     $('#results-wrapper').append(newRow);
            //     resultsIndex++;
            // });

            // $(document).on('click', '.remove-results', function() {
            //     if ($('#results-wrapper .results-row').length > 1) {
            //         $(this).closest('.results-row').remove();
            //     }
            // });

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
                                    // Don't show already selected items
                                    if (!selectedStandards.find(s => s.id ==
                                            item.id)) {
                                        dropdown.append(
                                            `<div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}">${item.name}</div>`
                                        );
                                    }
                                });
                                dropdown.show();
                            } else {
                                // Show create option
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

                if (query.length < 1) {
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

                if (query.length < 1) {
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
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            addSelectedItem(type.replace('_', '-'), response.data.id, response.data
                                .name);
                            $(searchInput).val('');
                            $(createSection).hide();
                        } else {
                            alert('Error creating item: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error creating item. Please try again.');
                    }
                });
            }

            // Function to add selected items
            function addSelectedItem(type, id, name) {
                let container = '';
                let array = null;

                switch (type) {
                    case 'standards':
                        container = '#standards-selected-container';
                        array = selectedStandards;
                        break;
                    case 'primary-tests':
                        container = '#primary-tests-selected-container';
                        array = selectedPrimaryTests;
                        break;
                    case 'secondary-tests':
                        container = '#secondary-tests-selected-container';
                        array = selectedSecondaryTests;
                        break;
                }

                // Check if already selected
                if (array.find(item => item.id == id)) {
                    return;
                }

                // Add to array
                array.push({
                    id: id,
                    name: name
                });

                // Add to display
                const selectedItem = `
                    <span class="selected-item" data-id="${id}" data-type="${type}">
                        ${name}
                        <span class="remove-item" data-id="${id}" data-type="${type}">×</span>
                    </span>
                `;
                $(container).append(selectedItem);

                // Update hidden inputs
                updateHiddenInputs();
            }

            // Function to remove selected items
            $(document).on('click', '.remove-item', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');

                // Remove from display
                $(this).parent().remove();

                // Remove from array
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

                // Update hidden inputs
                updateHiddenInputs();
            });

            // Function to update hidden inputs
            function updateHiddenInputs() {
                // Remove existing hidden inputs
                $('input[name="standard_ids[]"]').remove();
                $('input[name="primary_test_ids[]"]').remove();
                $('input[name="secondary_test_ids[]"]').remove();

                // Add standards
                selectedStandards.forEach(function(item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'standard_ids[]',
                        value: item.id
                    }).appendTo('form');
                });

                // Add primary tests
                selectedPrimaryTests.forEach(function(item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'primary_test_ids[]',
                        value: item.id
                    }).appendTo('form');
                });

                // Add secondary tests
                selectedSecondaryTests.forEach(function(item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'secondary_test_ids[]',
                        value: item.id
                    }).appendTo('form');
                });
            }

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.form-control-wrap').length) {
                    $('.search-dropdown').hide();
                    $('#standards-create-section').hide();
                    $('#primary-tests-create-section').hide();
                    $('#secondary-tests-create-section').hide();
                }
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

            // Auto-trigger if old sample value exists
            let oldSampleId = "{{ old('txt_sample_id') }}";
            if (oldSampleId) {
                $('#txt_sample_id').val(oldSampleId).trigger('change');
            }

            // Load old selected values if validation failed
            @if (old('standard_ids'))
                @foreach (old('standard_ids') as $standardId)
                    // You would need to fetch the name for this ID via AJAX or pass it from controller
                    // For now, just add the ID
                    selectedStandards.push({
                        id: {{ $standardId }},
                        name: 'Standard {{ $standardId }}'
                    });
                @endforeach
                updateHiddenInputs();
            @endif

            @if (old('primary_test_ids'))
                @foreach (old('primary_test_ids') as $primaryTestId)
                    selectedPrimaryTests.push({
                        id: {{ $primaryTestId }},
                        name: 'Primary Test {{ $primaryTestId }}'
                    });
                @endforeach
                updateHiddenInputs();
            @endif

            @if (old('secondary_test_ids'))
                @foreach (old('secondary_test_ids') as $secondaryTestId)
                    selectedSecondaryTests.push({
                        id: {{ $secondaryTestId }},
                        name: 'Secondary Test {{ $secondaryTestId }}'
                    });
                @endforeach
                updateHiddenInputs();
            @endif
        });
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

            // Update hidden input
            $('#results-hidden').val(resultTags.join(','));
        }

        function removeResultTag(index) {
            resultTags.splice(index, 1);
            updateResultsView();
        }

        // Add tag on input
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

        // Remove tag (fix class name here)
        $(document).on('click', '.remove-tag', function() {
            let index = $(this).data('index');
            removeResultTag(index);
        });

        // Update hidden field before submit
        $('form').on('submit', function() {
            $('#results-hidden').val(resultTags.join(','));
        });

        // Prefill tags on edit
        $(document).ready(function() {
            let prefilled = $('#results-hidden').val();
            if (prefilled) {
                resultTags = prefilled.split(',').filter(Boolean);
                updateResultsView();
            }
        });
    </script> --}}


    <script>
        $(document).ready(function() {
            let selectedSampleId = '';
            let selectedGroupId = '';

            $('#txt_sample_id').on('change', function() {
                selectedSampleId = $(this).val();
            });

            $('#txt_group_id').on('change', function() {
                selectedGroupId = $(this).val();
            });

            // Storage for selected items
            let selectedStandards = [];
            let selectedPrimaryTests = [];
            let selectedSecondaryTests = []; // Now will store with primary test association
            let availableSecondaryTests = []; // Cache for available secondary tests

            // Show/hide stages based on input mode
            $('#stages-wrapper').hide();
            $('#txt_input_mode').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'MULTI STAGE') {
                    $('#stages-wrapper').slideDown();
                    $('#txt_stages').attr('required', true);
                } else {
                    $('#stages-wrapper').slideUp();
                    $('#txt_stages').removeAttr('required');
                    $('#txt_stages').val('');
                }
            });

            if ($('#txt_input_mode').val() === 'MULTI STAGE') {
                $('#stages-wrapper').show();
                $('#txt_stages').attr('required', true);
            }

            // Search functionality with timeout
            let searchTimeout;

            // Standards search and management (unchanged)
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

            // Primary tests search and management (enhanced to refresh secondary tests)
            $('#primary-test-search').on('input', function() {
                const query = $(this).val();
                const dropdown = $('.primary-tests-dropdown');
                const createSection = $('#primary-tests-create-section');

                clearTimeout(searchTimeout);

                if (query.length < 1) {
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

            // Enhanced Secondary tests search - now filters by selected primary tests
            $('#secondary-test-search').on('input', function() {
                const query = $(this).val();
                const dropdown = $('.secondary-tests-dropdown');
                const createSection = $('#secondary-tests-create-section');

                clearTimeout(searchTimeout);

                if (query.length < 1) {
                    dropdown.hide().empty();
                    createSection.hide();
                    return;
                }

                // Check if any primary tests are selected
                if (selectedPrimaryTests.length === 0) {
                    dropdown.empty().append(
                        '<div class="search-dropdown-item disabled" style="color: #999; cursor: not-allowed;">Please select primary tests first</div>'
                    );
                    dropdown.show();
                    createSection.hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    // Get primary test IDs
                    const primaryTestIds = selectedPrimaryTests.map(test => test.id);

                    $.ajax({
                        url: "{{ route('search_secondary_tests') }}",
                        type: "GET",
                        data: {
                            query: query,
                            primary_test_ids: primaryTestIds // Pass selected primary test IDs
                        },
                        success: function(data) {
                            dropdown.empty();
                            createSection.hide();

                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    // Check if this secondary test is already selected
                                    const isSelected = selectedSecondaryTests
                                        .some(s =>
                                            s.id == item.id && s
                                            .primary_test_id == item
                                            .primary_test_id
                                        );

                                    if (!isSelected) {
                                        // Show which primary test this secondary test belongs to
                                        const primaryTestName =
                                            selectedPrimaryTests.find(p => p
                                                .id == item.primary_test_id)
                                            ?.name || 'Unknown';
                                        dropdown.append(
                                            `<div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}" data-primary-id="${item.primary_test_id}">
                                        ${item.name} <small class="text-muted">(${primaryTestName})</small>
                                    </div>`
                                        );
                                    }
                                });

                                if (dropdown.children().length > 0) {
                                    dropdown.show();
                                } else {
                                    dropdown.hide();
                                }
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

            // Handle dropdown item selection (enhanced for secondary tests)
            $(document).on('click', '.search-dropdown-item', function() {
                // Skip if disabled
                if ($(this).hasClass('disabled')) return;

                const id = $(this).data('id');
                const name = $(this).data('name');
                const primaryId = $(this).data('primary-id');
                const dropdown = $(this).parent();

                if (dropdown.hasClass('standards-dropdown')) {
                    addSelectedItem('standards', id, name);
                    $('#standard-search').val('');
                } else if (dropdown.hasClass('primary-tests-dropdown')) {
                    addSelectedItem('primary-tests', id, name);
                    $('#primary-test-search').val('');
                    // Refresh available secondary tests when primary tests change
                    refreshSecondaryTestsAvailability();
                } else if (dropdown.hasClass('secondary-tests-dropdown')) {
                    addSelectedItem('secondary-tests', id, name, primaryId);
                    $('#secondary-test-search').val('');
                }

                dropdown.hide();
            });

            // Create new items (enhanced for secondary tests)
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

                // Show modal to select which primary test this secondary test belongs to
                showPrimaryTestSelectionModal(name);
            });

            // Function to show primary test selection modal for new secondary tests
            function showPrimaryTestSelectionModal(secondaryTestName) {
                if (selectedPrimaryTests.length === 0) {
                    alert('Please select at least one primary test first.');
                    return;
                }

                let modalHtml = `
            <div class="modal fade" id="primaryTestModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select Primary Test</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Create secondary test "<strong>${secondaryTestName}</strong>" for which primary test?</p>
                            <div class="primary-test-options">
        `;

                selectedPrimaryTests.forEach(function(primaryTest) {
                    modalHtml += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="selectedPrimaryTest" id="primary_${primaryTest.id}" value="${primaryTest.id}">
                    <label class="form-check-label" for="primary_${primaryTest.id}">
                        ${primaryTest.name}
                    </label>
                </div>
            `;
                });

                modalHtml += `
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmCreateSecondary">Create</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

                // Remove existing modal if any
                $('#primaryTestModal').remove();

                // Add modal to body
                $('body').append(modalHtml);

                // Show modal
                $('#primaryTestModal').modal('show');

                // Handle create button
                $('#confirmCreateSecondary').on('click', function() {
                    const selectedPrimaryId = $('input[name="selectedPrimaryTest"]:checked').val();
                    if (selectedPrimaryId) {
                        createNewSecondaryTest(secondaryTestName, selectedPrimaryId);
                        $('#primaryTestModal').modal('hide');
                    } else {
                        alert('Please select a primary test.');
                    }
                });
            }

            // Function to create new secondary test with primary test association
            function createNewSecondaryTest(name, primaryTestId) {
                $.ajax({
                    url: "{{ route('create_secondary_test') }}",
                    type: "POST",
                    data: {
                        name: name,
                        primary_test_id: primaryTestId,
                        sampleId: selectedSampleId,
                        groupId: selectedGroupId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            addSelectedItem('secondary-tests', response.data.id, response.data.name,
                                primaryTestId);
                            $('#secondary-test-search').val('');
                            $('#secondary-tests-create-section').hide();
                        } else {
                            alert('Error creating secondary test: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error creating secondary test. Please try again.');
                    }
                });
            }

            // Function to create new items via AJAX (enhanced)
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
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        name: name,
                        sampleId: selectedSampleId,
                        groupId: selectedGroupId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            addSelectedItem(type.replace('_', '-'), response.data.id, response.data
                                .name);
                            $(searchInput).val('');
                            $(createSection).hide();

                            // If primary test was created, refresh secondary tests availability
                            if (type === 'primary_tests') {
                                refreshSecondaryTestsAvailability();
                            }
                        } else {
                            alert('Error creating item: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error creating item. Please try again.');
                    }
                });
            }

            // Enhanced function to add selected items (supports primary test association for secondary tests)
            function addSelectedItem(type, id, name, primaryTestId = null) {
                let container = '';
                let array = null;

                switch (type) {
                    case 'standards':
                        container = '#standards-selected-container';
                        array = selectedStandards;
                        break;
                    case 'primary-tests':
                        container = '#primary-tests-selected-container';
                        array = selectedPrimaryTests;
                        break;
                    case 'secondary-tests':
                        container = '#secondary-tests-selected-container';
                        array = selectedSecondaryTests;
                        break;
                }

                // Check if already selected (for secondary tests, check both id and primary_test_id)
                if (type === 'secondary-tests') {
                    if (array.find(item => item.id == id && item.primary_test_id == primaryTestId)) {
                        return;
                    }
                } else {
                    if (array.find(item => item.id == id)) {
                        return;
                    }
                }

                // Add to array
                const itemData = {
                    id: id,
                    name: name
                };
                if (type === 'secondary-tests' && primaryTestId) {
                    itemData.primary_test_id = primaryTestId;
                }
                array.push(itemData);

                // Add to display (enhanced for secondary tests)
                let displayName = name;
                if (type === 'secondary-tests' && primaryTestId) {
                    const primaryTestName = selectedPrimaryTests.find(p => p.id == primaryTestId)?.name ||
                        'Unknown';
                    displayName = `${name} <span class="text-danger">(${primaryTestName})</span>`;
                }

                const selectedItem = `
            <span class="selected-item" data-id="${id}" data-type="${type}" ${primaryTestId ? `data-primary-id="${primaryTestId}"` : ''}>
                ${displayName}
                <span class="remove-item" data-id="${id}" data-type="${type}" ${primaryTestId ? `data-primary-id="${primaryTestId}"` : ''}>×</span>
            </span>
        `;
                $(container).append(selectedItem);

                // Update hidden inputs
                updateHiddenInputs();
            }

            // Enhanced function to remove selected items
            $(document).on('click', '.remove-item', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const primaryTestId = $(this).data('primary-id');

                // Remove from display
                $(this).parent().remove();

                // Remove from array
                switch (type) {
                    case 'standards':
                        selectedStandards = selectedStandards.filter(item => item.id != id);
                        break;
                    case 'primary-tests':
                        selectedPrimaryTests = selectedPrimaryTests.filter(item => item.id != id);
                        // Remove associated secondary tests when primary test is removed
                        selectedSecondaryTests = selectedSecondaryTests.filter(item => item
                            .primary_test_id != id);
                        // Refresh secondary tests display
                        refreshSecondaryTestsDisplay();
                        // Clear secondary tests availability
                        refreshSecondaryTestsAvailability();
                        break;
                    case 'secondary-tests':
                        if (primaryTestId) {
                            selectedSecondaryTests = selectedSecondaryTests.filter(item =>
                                !(item.id == id && item.primary_test_id == primaryTestId)
                            );
                        } else {
                            selectedSecondaryTests = selectedSecondaryTests.filter(item => item.id != id);
                        }
                        break;
                }

                // Update hidden inputs
                updateHiddenInputs();
            });

            // Function to refresh secondary tests display when primary tests change
            function refreshSecondaryTestsDisplay() {
                const container = $('#secondary-tests-selected-container');
                container.empty();

                selectedSecondaryTests.forEach(function(item) {
                    // Check if the primary test still exists
                    const primaryTest = selectedPrimaryTests.find(p => p.id == item.primary_test_id);
                    if (primaryTest) {
                        const displayName =
                            `${item.name} <small class="text-muted">(${primaryTest.name})</small>`;
                        const selectedItem = `
                    <span class="selected-item" data-id="${item.id}" data-type="secondary-tests" data-primary-id="${item.primary_test_id}">
                        ${displayName}
                        <span class="remove-item" data-id="${item.id}" data-type="secondary-tests" data-primary-id="${item.primary_test_id}">×</span>
                    </span>
                `;
                        container.append(selectedItem);
                    }
                });
            }

            // Function to refresh secondary tests availability
            function refreshSecondaryTestsAvailability() {
                // Clear the search input and hide dropdown
                $('#secondary-test-search').val('');
                $('.secondary-tests-dropdown').hide().empty();
                $('#secondary-tests-create-section').hide();

                // Show/hide secondary tests section based on primary tests selection
                if (selectedPrimaryTests.length === 0) {
                    $('#secondary-tests-section').addClass('opacity-50');
                    $('#secondary-test-search').attr('placeholder', 'Select primary tests first');
                } else {
                    $('#secondary-tests-section').removeClass('opacity-50');
                    $('#secondary-test-search').attr('placeholder', 'Search or enter secondary test name');
                }
            }

            // Enhanced function to update hidden inputs
            function updateHiddenInputs() {
                // Remove existing hidden inputs
                $('input[name="standard_ids[]"]').remove();
                $('input[name="primary_test_ids[]"]').remove();
                $('input[name="secondary_test_ids[]"]').remove();
                $('input[name="secondary_test_primary_ids[]"]').remove();

                // Add standards
                selectedStandards.forEach(function(item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'standard_ids[]',
                        value: item.id
                    }).appendTo('form');
                });

                // Add primary tests
                selectedPrimaryTests.forEach(function(item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'primary_test_ids[]',
                        value: item.id
                    }).appendTo('form');
                });

                // Add secondary tests with their primary test associations
                selectedSecondaryTests.forEach(function(item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'secondary_test_ids[]',
                        value: item.id
                    }).appendTo('form');

                    if (item.primary_test_id) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'secondary_test_primary_ids[]',
                            value: item.primary_test_id
                        }).appendTo('form');
                    }
                });
            }

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.form-control-wrap').length) {
                    $('.search-dropdown').hide();
                    $('#standards-create-section').hide();
                    $('#primary-tests-create-section').hide();
                    $('#secondary-tests-create-section').hide();
                }
            });

            // Sample -> Group AJAX (unchanged)
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

            // Auto-trigger if old sample value exists
            let oldSampleId = "{{ old('txt_sample_id') }}";
            if (oldSampleId) {
                $('#txt_sample_id').val(oldSampleId).trigger('change');
            }

            // Initialize secondary tests section state
            refreshSecondaryTestsAvailability();

            // Load old selected values if validation failed (enhanced)
            @if (old('standard_ids'))
                @foreach (old('standard_ids') as $standardId)
                    selectedStandards.push({
                        id: {{ $standardId }},
                        name: 'Standard {{ $standardId }}'
                    });
                @endforeach
                updateHiddenInputs();
            @endif

            @if (old('primary_test_ids'))
                @foreach (old('primary_test_ids') as $primaryTestId)
                    selectedPrimaryTests.push({
                        id: {{ $primaryTestId }},
                        name: 'Primary Test {{ $primaryTestId }}'
                    });
                @endforeach
                updateHiddenInputs();
                refreshSecondaryTestsAvailability();
            @endif

            @if (old('secondary_test_ids'))
                @php
                    $secondaryTestIds = old('secondary_test_ids');
                    $secondaryTestPrimaryIds = old('secondary_test_primary_ids', []);
                @endphp
                @foreach ($secondaryTestIds as $index => $secondaryTestId)
                    selectedSecondaryTests.push({
                        id: {{ $secondaryTestId }},
                        name: 'Secondary Test {{ $secondaryTestId }}',
                        primary_test_id: {{ $secondaryTestPrimaryIds[$index] ?? 'null' }}
                    });
                @endforeach
                updateHiddenInputs();
                refreshSecondaryTestsDisplay();
            @endif
        });
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
