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
                                <form action="{{ route('create_test') }}" class="form-validate is-alter" method="POST"
                                    id="test-form">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_sample_id">Sample<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_sample_id" class="form-control" id="txt_sample_id"
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
                                                    <select name="txt_group_id" class="form-control" id="txt_group_id"
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
                                        <div class="col-md-4 position-relative">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Test Name <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_name"
                                                        name="txt_name" value="{{ old('txt_name') }}"
                                                        placeholder="Enter Test Name" autocomplete="off" required>
                                                    <small id="test-name-feedback" class="text-danger d-none mt-1">Test
                                                        already exists!</small>
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

                                        {{-- Result Type --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_category_id">Result Type<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_category_id" class="form-control" id="txt_category_id"
                                                        required>
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

                                        {{-- Standards Section --}}
                                        <div class="col-md-4" id="standards-section">
                                            <div class="form-group">
                                                <label class="form-label">Standards <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="standard-search" class="form-control"
                                                        placeholder="Search or enter standard name" autocomplete="off">
                                                    <div class="search-dropdown standards-dropdown"
                                                        style="display: none;"></div>
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
                                                    <select id="primary-test-selector" class="form-control mb-2"
                                                        style="display: none;">
                                                        <option value="">Select Primary Test</option>
                                                    </select>
                                                    <input type="text" id="secondary-test-search" class="form-control"
                                                        placeholder="Select primary test first" autocomplete="off"
                                                        disabled>
                                                    <div class="search-dropdown secondary-tests-dropdown"
                                                        style="display: none;"></div>
                                                    <div class="mt-2">
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
                                                </div>
                                                @error('secondary_test_ids')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="secondary-tests-selected-container" class="row g-gs mt-0"></div>

                                        {{-- Lab Sample Checkboxes --}}
                                        <div class="col-md-12">
                                            <div class="preview-block">
                                                <span class="preview-title overline-title mb-2 d-block">Test Applicable
                                                    On</span>
                                                <div class="row">
                                                    @foreach ($labSamples as $sample)
                                                        <div class="col-md-2 mb-2">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="labSample{{ $sample->m14_lab_sample_id }}"
                                                                    name="lab_sample_ids[]"
                                                                    value="{{ $sample->m14_lab_sample_id }}"
                                                                    {{ in_array($sample->m14_lab_sample_id, old('lab_sample_ids', [])) ? 'checked' : '' }}>
                                                                <label class="custom-control-label"
                                                                    for="labSample{{ $sample->m14_lab_sample_id }}">
                                                                    {{ $sample->m14_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('lab_sample_ids')
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
                                                    <select name="txt_input_mode" class="form-control"
                                                        id="txt_input_mode" required>
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
                                                        max="10" required>
                                                </div>
                                                @error('txt_stages')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Results --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Results <b class="text-danger">*</b></label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="results-input" class="form-control"
                                                        placeholder="Enter result name" autocomplete="off">
                                                </div>
                                                <div id="results-tags-container" class="mt-2"></div>
                                                <input type="hidden" name="results" id="results-hidden"
                                                    value="{{ old('results') }}">
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
                                                        name="txt_weight" value="{{ old('txt_weight', 0) }}"
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

    {{-- Standard Creation Modal --}}
    <div class="modal fade" id="standardModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Standard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="standard-form">
                        <div class="form-group mb-3">
                            <label class="form-label">Standard Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal-standard-name"
                                placeholder="Enter standard name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Accredited <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="accredited-yes"
                                        name="accredited" value="YES">
                                    <label class="custom-control-label" for="accredited-yes">Yes</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="accredited-no"
                                        name="accredited" value="NO">
                                    <label class="custom-control-label" for="accredited-no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="standard-description" rows="3" placeholder="Enter description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-create-standard">Create Standard</button>
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

        .search-dropdown-item.disabled {
            color: #999;
            cursor: not-allowed;
        }

        .form-control-wrap {
            position: relative;
        }

        .selected-item {
            display: inline-block;
            color: white;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 4px;
            font-size: 12px;
        }

        .selected-item.standard-accredited {
            background: #198754;
        }

        .selected-item.standard-normal {
            background: #007bff;
        }

        .selected-item.primary-test {
            background: #6f42c1;
        }

        .selected-item.secondary-test {
            background: #fd7e14;
        }

        .selected-item .remove-item {
            margin-left: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .selected-item .remove-item:hover {
            color: #ff6b6b;
        }

        .primary-test-group {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
        }

        .primary-test-title {
            font-weight: bold;
            color: #6f42c1;
            margin-bottom: 4px;
        }

        #secondary-tests-section.disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize variables
            let selectedSampleId = '';
            let selectedGroupId = '';
            let selectedStandards = [];
            let selectedPrimaryTests = [];
            let selectedSecondaryTests = [];
            let currentPrimaryTestForSecondary = null;
            let searchTimeout;
            let resultTags = [];
            const searchRoutes = {
                'standards': "{{ route('search_standards') }}",
                'primary_tests': "{{ route('search_primary_tests') }}",
                'secondary_tests': "{{ route('search_secondary_tests') }}"
            };

            // Restore old values on page load
            initializeOldValues();

            // Sample and Group handling
            $('#txt_sample_id').on('change', function() {
                selectedSampleId = $(this).val();
                loadGroups();
            });

            $('#txt_group_id').on('change', function() {
                selectedGroupId = $(this).val();
            });

            // Input mode and stages handling
            $('#txt_input_mode').on('change', function() {
                if ($(this).val() === 'MULTI STAGE') {
                    $('#stages-wrapper').slideDown();
                    $('#txt_stages').attr('required', true);
                } else {
                    $('#stages-wrapper').slideUp();
                    $('#txt_stages').removeAttr('required').val('');
                }
            });

            // Initialize stages visibility
            if ($('#txt_input_mode').val() === 'MULTI STAGE') {
                $('#stages-wrapper').show();
                $('#txt_stages').attr('required', true);
            }

            // Test name existence check
            $('#txt_name').on('keyup', function() {
                const testName = $(this).val().trim();
                if (testName.length >= 2) {
                    checkTestExists(testName);
                } else {
                    $('#test-name-feedback').addClass('d-none');
                }
            });

            // Standards search functionality
            $('#standard-search').on('input', function() {
                const query = $(this).val().trim();
                handleSearch('standards', query);
            });

            // Primary tests search functionality
            $('#primary-test-search').on('input', function() {
                const query = $(this).val().trim();
                handleSearch('primary_tests', query);
            });

            // Primary test selector for secondary tests
            $('#primary-test-selector').on('change', function() {
                currentPrimaryTestForSecondary = $(this).val();
                const searchInput = $('#secondary-test-search');

                if (currentPrimaryTestForSecondary) {
                    searchInput.prop('disabled', false).attr('placeholder',
                        'Search or enter secondary test name');
                } else {
                    searchInput.prop('disabled', true).attr('placeholder', 'Select primary test first').val(
                        '');
                    $('.secondary-tests-dropdown').hide();
                    $('#secondary-tests-create-section').hide();
                }
            });

            // Secondary tests search functionality
            $('#secondary-test-search').on('input', function() {
                if (!currentPrimaryTestForSecondary) return;

                const query = $(this).val().trim();
                handleSecondaryTestSearch(query);
            });

            // Results functionality
            $('#results-input').on('keydown', function(e) {
                if (['Enter', ',', 'Tab'].includes(e.key)) {
                    e.preventDefault();
                    const value = $(this).val().trim();
                    if (value && !resultTags.includes(value)) {
                        resultTags.push(value);
                        updateResultsView();
                        $(this).val('');
                    }
                }
            });

            // Event handlers for dropdown selections
            $(document).on('click', '.search-dropdown-item:not(.disabled)', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const type = $(this).data('type');
                const dropdown = $(this).parent();

                if (dropdown.hasClass('standards-dropdown')) {
                    const accredited = $(this).data('accredited') || 'NO';
                    addSelectedItem('standards', {
                        id,
                        name,
                        accredited
                    });
                    $('#standard-search').val('');
                } else if (dropdown.hasClass('primary-tests-dropdown')) {
                    addSelectedItem('primary_tests', {
                        id,
                        name
                    });
                    $('#primary-test-search').val('');
                    updatePrimaryTestSelector();
                } else if (dropdown.hasClass('secondary-tests-dropdown')) {
                    addSelectedItem('secondary_tests', {
                        id,
                        name,
                        primary_test_id: currentPrimaryTestForSecondary
                    });
                    $('#secondary-test-search').val('');
                }
                dropdown.hide();
            });

            // Create buttons
            $('#create-standard-btn').on('click', function() {
                const name = $('#standard-search').val();
                showStandardModal(name);
            });

            $('#create-primary-test-btn').on('click', function() {
                const name = $('#primary-test-search').val();
                console.log(name);
                createNewItem('primary_tests', name);
            });

            $('#create-secondary-test-btn').on('click', function() {
                const name = $('#secondary-test-search').val();
                if (currentPrimaryTestForSecondary) {
                    createNewSecondaryTest(name, currentPrimaryTestForSecondary);
                }
            });

            // Standard modal handling
            $('#confirm-create-standard').on('click', function() {
                const name = $('#modal-standard-name').val();
                const accredited = $('input[name="accredited"]:checked').val();
                const description = $('#standard-description').val();

                if (!accredited) {
                    alert('Please select whether the standard is accredited or not.');
                    return;
                }

                createNewStandard(name, accredited, description);
            });

            // Remove items
            $(document).on('click', '.remove-item', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const primaryId = $(this).data('primary-id');

                removeSelectedItem(type, id, primaryId);
            });

            // Remove result tags
            $(document).on('click', '.remove-tag', function() {
                const index = $(this).data('index');
                resultTags.splice(index, 1);
                updateResultsView();
            });

            // Hide dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.form-control-wrap').length) {
                    $('.search-dropdown').hide();
                    $('#standards-create-section, #primary-tests-create-section, #secondary-tests-create-section')
                        .hide();
                }
            });

            // Form submission
            $('#test-form').on('submit', function() {
                updateHiddenInputs();
            });

            // Functions
            function initializeOldValues() {
                // Initialize results
                const oldResults = $('#results-hidden').val();
                if (oldResults) {
                    resultTags = oldResults.split(',').filter(Boolean);
                    updateResultsView();
                }

                // Initialize standards
                @if (old('standard_ids'))
                    const standardIds = @json(old('standard_ids'));
                    const standardData = @json(old('standard_data', []));
                    standardIds.forEach((id, index) => {
                        const raw = standardData[index] || {};
                        const data = {
                            name: raw.name || `Standard ${id}`,
                            accredited: raw.accredited || 'NO'
                        };
                        selectedStandards.push({
                            id: parseInt(id),
                            ...data
                        });
                    });
                    displaySelectedItems('standards');
                @endif

                // Initialize primary tests
                @if (old('primary_test_ids'))
                    const primaryTestIds = @json(old('primary_test_ids'));
                    const primaryTestData = @json(old('primary_test_data', []));
                    primaryTestIds.forEach((id, index) => {
                        const data = primaryTestData[index] || {
                            name: `Primary Test ${id}`
                        };
                        selectedPrimaryTests.push({
                            id: parseInt(id),
                            ...data
                        });
                    });
                    displaySelectedItems('primary_tests');
                    updatePrimaryTestSelector();
                @endif

                // Initialize secondary tests
                @if (old('secondary_test_ids'))
                    const secondaryTestIds = @json(old('secondary_test_ids'));
                    const secondaryTestPrimaryIds = @json(old('secondary_test_primary_ids', []));
                    const secondaryTestData = @json(old('secondary_test_data', []));
                    secondaryTestIds.forEach((id, index) => {
                        const primaryId = secondaryTestPrimaryIds[index];
                        const data = secondaryTestData[index] || {
                            name: `Secondary Test ${id}`
                        };
                        selectedSecondaryTests.push({
                            id: parseInt(id),
                            primary_test_id: parseInt(primaryId),
                            ...data
                        });
                    });
                    displaySelectedItems('secondary_tests');
                @endif

                // Auto-trigger sample change if old value exists
                const oldSampleId = "{{ old('txt_sample_id') }}";
                if (oldSampleId) {
                    selectedSampleId = oldSampleId;
                    loadGroups();
                }
            }

            function loadGroups() {
                const groupDropdown = $('#txt_group_id');
                groupDropdown.empty().append('<option value="">Loading...</option>');

                if (selectedSampleId) {
                    $.ajax({
                        url: "{{ route('get_groups') }}",
                        type: "GET",
                        data: {
                            sample_id: selectedSampleId
                        },
                        success: function(data) {
                            groupDropdown.empty().append(
                                '<option value="">-- Select Group --</option>');
                            const oldGroupId = "{{ old('txt_group_id') }}";

                            data.forEach(group => {
                                const selected = (group.m11_group_id == oldGroupId) ?
                                    'selected' : '';
                                groupDropdown.append(
                                    `<option value="${group.m11_group_id}" ${selected}>${group.m11_name}</option>`
                                );
                            });

                            if (oldGroupId) {
                                selectedGroupId = oldGroupId;
                            }
                        },
                        error: function() {
                            groupDropdown.empty().append(
                                '<option value="">Error loading groups</option>');
                        }
                    });
                } else {
                    groupDropdown.empty().append('<option value="">-- Select Group --</option>');
                }
            }

            function checkTestExists(testName) {
                $.ajax({
                    url: "{{ route('check_test_exists') }}",
                    method: 'GET',
                    data: {
                        name: testName
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('#test-name-feedback').removeClass('d-none').text(
                                'This Test already exists!');
                        } else {
                            $('#test-name-feedback').addClass('d-none');
                        }
                    }
                });
            }

            function handleSearch(type, query) {
                clearTimeout(searchTimeout);
                const dropdownClassMap = {
                    'standards': '.standards-dropdown',
                    'primary_tests': '.primary-tests-dropdown',
                };

                const dropdown = $(dropdownClassMap[type]);
                // const dropdown = $(`.${type}-dropdown`);
                const createSection = $(`#${type.replace('_', '-')}-create-section`);

                if (query.length < 1) {
                    dropdown.hide().empty();
                    createSection.hide();
                    return;
                }

                if (!searchRoutes[type]) {
                    console.error('No search route defined for type:', type);
                    return;
                }

                // Show loading
                dropdown.empty().append('<div class="search-dropdown-item disabled">Searching...</div>').show();

                searchTimeout = setTimeout(() => {
                    $.ajax({
                        url: searchRoutes[type],
                        type: "GET",
                        data: {
                            query,
                            selectedGroupId
                        },
                        success: function(data) {
                            dropdown.empty();
                            createSection.hide();

                            if (data.length > 0) {
                                let hasResults = false;
                                data.forEach(item => {
                                    const isSelected = getSelectedArray(type).find(s =>
                                        s.id == item.id);
                                    if (!isSelected) {
                                        hasResults = true;
                                        console.log(item)
                                        dropdown.append(
                                            `<div class="search-dropdown-item"
                                        data-id="${item.id}"
                                        data-name="${item.name}"
                                        data-type="${type}"
                                        data-accredited="${item.accredited || 'NO'}">
                                        ${item.name}
                                    </div>`
                                        );
                                    }
                                });

                                if (hasResults) {
                                    dropdown.show();
                                } else {
                                    dropdown.hide();
                                    showCreateOption(type, query);
                                }
                            } else {
                                dropdown.hide();
                                showCreateOption(type, query);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Search error:', error);
                            dropdown.hide();
                            showCreateOption(type, query);
                        }
                    });
                }, 300);
            }

            function handleSecondaryTestSearch(query) {
                clearTimeout(searchTimeout);
                const dropdown = $('.secondary-tests-dropdown');
                const createSection = $('#secondary-tests-create-section');

                if (query.length < 1) {
                    dropdown.hide().empty();
                    createSection.hide();
                    return;
                }

                // Show loading
                dropdown.empty().append('<div class="search-dropdown-item disabled">Searching...</div>').show();

                searchTimeout = setTimeout(() => {
                    $.ajax({
                        url: "{{ route('search_secondary_tests') }}",
                        type: "GET",
                        data: {
                            query,
                            primary_test_id: currentPrimaryTestForSecondary
                        },
                        success: function(data) {
                            dropdown.empty();
                            createSection.hide();

                            if (data.length > 0) {
                                let hasResults = false;
                                data.forEach(item => {
                                    const isSelected = selectedSecondaryTests.find(s =>
                                        s.id == item.id && s.primary_test_id ==
                                        currentPrimaryTestForSecondary
                                    );
                                    if (!isSelected) {
                                        hasResults = true;
                                        dropdown.append(
                                            `<div class="search-dropdown-item" 
                                        data-id="${item.id}" 
                                        data-name="${item.name}" 
                                        data-type="secondary_tests">
                                        ${item.name}
                                    </div>`
                                        );
                                    }
                                });

                                if (hasResults) {
                                    dropdown.show();
                                } else {
                                    dropdown.hide();
                                    showCreateOption('secondary_tests', query);
                                }
                            } else {
                                dropdown.hide();
                                showCreateOption('secondary_tests', query);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Search error:', error);
                            dropdown.hide();
                            showCreateOption('secondary_tests', query);
                        }
                    });
                }, 300);
            }

            function showCreateOption(type, query) {
                const createSection = $(`#${type.replace('_', '-')}-create-section`);
                const createNameElement = $(`#${type.replace('_', '-')}-create-name`);

                createNameElement.text(query);
                createSection.show();
            }

            function getSelectedArray(type) {
                switch (type) {
                    case 'standards':
                        return selectedStandards;
                    case 'primary_tests':
                        return selectedPrimaryTests;
                    case 'secondary_tests':
                        return selectedSecondaryTests;
                    default:
                        return [];
                }
            }

            function addSelectedItem(type, data) {
                const array = getSelectedArray(type);

                // Check if already selected
                if (type === 'secondary_tests') {
                    if (array.find(item => item.id == data.id && item.primary_test_id == data.primary_test_id)) {
                        return;
                    }
                } else {
                    if (array.find(item => item.id == data.id)) {
                        return;
                    }
                }

                array.push(data);
                displaySelectedItems(type);

                if (type === 'primary_tests') {
                    updatePrimaryTestSelector();
                }

                updateHiddenInputs();
            }

            function removeSelectedItem(type, id, primaryId = null) {
                if (type === 'secondary_tests' && primaryId) {
                    selectedSecondaryTests = selectedSecondaryTests.filter(item =>
                        !(item.id == id && item.primary_test_id == primaryId)
                    );
                } else if (type === 'primary_tests') {
                    selectedPrimaryTests = selectedPrimaryTests.filter(item => item.id != id);
                    // Remove associated secondary tests
                    selectedSecondaryTests = selectedSecondaryTests.filter(item => item.primary_test_id != id);
                    updatePrimaryTestSelector();
                    displaySelectedItems('secondary_tests');
                } else if (type === 'standards') {
                    selectedStandards = selectedStandards.filter(item => item.id != id);
                }

                displaySelectedItems(type);
                updateHiddenInputs();
            }

            function displaySelectedItems(type) {
                const containers = {
                    'standards': '#standards-selected-container',
                    'primary_tests': '#primary-tests-selected-container',
                    'secondary_tests': '#secondary-tests-selected-container'
                };

                const container = $(containers[type]);
                container.empty();

                if (type === 'standards') {
                    selectedStandards.forEach(item => {
                        const cssClass = item.accredited === 'YES' ? 'standard-accredited' :
                            'standard-normal';
                        const selectedItem = `
                    <span class="selected-item ${cssClass}" data-id="${item.id}" data-type="standards">
                        ${item.name}
                        <span class="remove-item" data-id="${item.id}" data-type="standards">×</span>
                    </span>
                `;
                        container.append(selectedItem);
                    });
                } else if (type === 'primary_tests') {
                    selectedPrimaryTests.forEach(item => {
                        const selectedItem = `
                    <span class="selected-item primary-test" data-id="${item.id}" data-type="primary_tests">
                        ${item.name}
                        <span class="remove-item" data-id="${item.id}" data-type="primary_tests">×</span>
                    </span>
                `;
                        container.append(selectedItem);
                    });
                } else if (type === 'secondary_tests') {
                    // Group by primary test
                    const grouped = {};
                    selectedSecondaryTests.forEach(item => {
                        const primaryTest = selectedPrimaryTests.find(p => p.id == item.primary_test_id);
                        const primaryName = primaryTest ? primaryTest.name : 'Unknown Primary Test';

                        if (!grouped[item.primary_test_id]) {
                            grouped[item.primary_test_id] = {
                                primaryName,
                                tests: []
                            };
                        }
                        grouped[item.primary_test_id].tests.push(item);
                    });

                    Object.values(grouped).forEach(group => {
                        const groupDiv = `
                    <div class="primary-test-group col-md-4">
                        <div class="primary-test-title">${group.primaryName}</div>
                        <div class="secondary-tests">
                            ${group.tests.map(test => `
                                                    <span class="selected-item secondary-test" data-id="${test.id}" data-type="secondary_tests" data-primary-id="${test.primary_test_id}">
                                                        ${test.name}
                                                        <span class="remove-item" data-id="${test.id}" data-type="secondary_tests" data-primary-id="${test.primary_test_id}">×</span>
                                                    </span>
                                                `).join('')}
                        </div>
                    </div>
                `;
                        container.append(groupDiv);
                    });
                }
            }

            function updatePrimaryTestSelector() {
                const selector = $('#primary-test-selector');
                selector.empty().append('<option value="">Select Primary Test</option>');

                if (selectedPrimaryTests.length > 0) {
                    selectedPrimaryTests.forEach(test => {
                        selector.append(`<option value="${test.id}">${test.name}</option>`);
                    });
                    selector.show();
                    $('#secondary-tests-section').removeClass('disabled');
                } else {
                    selector.hide();
                    $('#secondary-tests-section').addClass('disabled');
                    $('#secondary-test-search').prop('disabled', true).attr('placeholder',
                        'Select primary test first');
                    currentPrimaryTestForSecondary = null;
                }
            }

            function showStandardModal(name) {
                // console.log('Setting modal name to:', name);
                $('#modal-standard-name').val(name);
                $('#standard-description').val('');
                $('input[name="accredited"]').prop('checked', false);

                // Ensure modal is fully loaded before setting value
                $('#standardModal').on('shown.bs.modal', function() {
                    $('#modal-standard-name').val(name);
                });

                $('#standardModal').modal('show');
            }

            function createNewStandard(name, accredited, description) {
                if (!selectedSampleId || !selectedGroupId) {
                    alert('Please select sample and group first.');
                    return;
                }

                $.ajax({
                    url: "{{ route('create_standard') }}",
                    type: "POST",
                    data: {
                        name,
                        accredited,
                        description,
                        sampleId: selectedSampleId,
                        groupId: selectedGroupId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log(response);
                            // addSelectedItem('standards', {
                            //     id: response.data.id,
                            //     name: response.data.name,
                            //     accredited: response.data.accredited
                            // });
                            addSelectedItem('standards', {
                                id: response.data.id,
                                name: response.data.name,
                                accredited: (response.data.accredited && response.data
                                    .accredited.toUpperCase() === 'YES') ? 'YES' : 'NO'
                            });
                            $('#standard-search').val('');
                            $('#standards-create-section').hide();
                            $('#standardModal').modal('hide');
                        } else {
                            alert('Error creating standard: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error creating standard. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    }
                });
            }

            function createNewItem(type, name) {
                if (!selectedSampleId || !selectedGroupId) {
                    alert('Please select sample and group first.');
                    return;
                }

                const routes = {
                    'primary_tests': "{{ route('create_primary_test') }}"
                };

                $.ajax({
                    url: routes[type],
                    type: "POST",
                    data: {
                        name,
                        sampleId: selectedSampleId,
                        groupId: selectedGroupId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            addSelectedItem(type, {
                                id: response.data.id,
                                name: response.data.name
                            });
                            $(`#${type.replace('_', '-')}-search`).val('');
                            $(`#${type.replace('_', '-')}-create-section`).hide();
                        } else {
                            alert(`Error creating ${type.replace('_', ' ')}: ` + response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = `Error creating ${type.replace('_', ' ')}. Please try again.`;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    }
                });
            }

            function createNewSecondaryTest(name, primaryTestId) {
                if (!selectedSampleId || !selectedGroupId) {
                    alert('Please select sample and group first.');
                    return;
                }

                $.ajax({
                    url: "{{ route('create_secondary_test') }}",
                    type: "POST",
                    data: {
                        name,
                        primary_test_id: primaryTestId,
                        sampleId: selectedSampleId,
                        groupId: selectedGroupId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            addSelectedItem('secondary_tests', {
                                id: response.data.id,
                                name: response.data.name,
                                primary_test_id: primaryTestId
                            });
                            $('#secondary-test-search').val('');
                            $('#secondary-tests-create-section').hide();
                        } else {
                            alert('Error creating secondary test: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error creating secondary test. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    }
                });
            }

            function updateResultsView() {
                const container = $('#results-tags-container');
                container.empty();

                resultTags.forEach((tag, index) => {
                    const badge = `
                <span class="selected-item" style="background: #17a2b8;">
                    ${tag}
                    <span class="remove-tag ms-1" data-index="${index}" style="cursor:pointer;">&times;</span>
                </span>
            `;
                    container.append(badge);
                });

                $('#results-hidden').val(resultTags.join(','));
            }

            function updateHiddenInputs() {
                // Remove existing hidden inputs
                $('input[name="standard_ids[]"], input[name="primary_test_ids[]"], input[name="secondary_test_ids[]"], input[name="secondary_test_primary_ids[]"]')
                    .remove();
                $('input[name="standard_data[]"], input[name="primary_test_data[]"], input[name="secondary_test_data[]"]')
                    .remove();

                // Add standards
                selectedStandards.forEach(item => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'standard_ids[]',
                        value: item.id
                    }).appendTo('#test-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'standard_data[]',
                        value: JSON.stringify(item)
                    }).appendTo('#test-form');
                });

                // Add primary tests
                selectedPrimaryTests.forEach(item => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'primary_test_ids[]',
                        value: item.id
                    }).appendTo('#test-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'primary_test_data[]',
                        value: JSON.stringify(item)
                    }).appendTo('#test-form');
                });

                // Add secondary tests
                selectedSecondaryTests.forEach(item => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'secondary_test_ids[]',
                        value: item.id
                    }).appendTo('#test-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'secondary_test_primary_ids[]',
                        value: item.primary_test_id
                    }).appendTo('#test-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'secondary_test_data[]',
                        value: JSON.stringify(item)
                    }).appendTo('#test-form');
                });
            }
        });
    </script>
@endsection
