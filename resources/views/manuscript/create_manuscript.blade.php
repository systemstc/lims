@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    {{-- Page Header --}}
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h3 class="nk-block-title page-title">Create Manuscript</h3>
                                <div class="text-end">
                                    <a href="{{ route('view_manuscripts') }}" class="btn btn-primary">
                                        <em class="icon ni ni-caret-left-fill"></em>&nbsp; Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Page Content --}}
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('create_manuscript') }}" method="POST" id="createManuscriptForm">
                                    @csrf

                                    <div class="row g-3">

                                        {{-- Sample --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Sample</label>
                                            <div class="form-control-wrap">
                                                <select id="sample_id" name="txt_sample_id" class="form-select" required>
                                                    <option value="">Select Sample</option>
                                                    @foreach ($samples as $sample)
                                                        <option value="{{ $sample->m10_sample_id }}">
                                                            {{ $sample->m10_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('txt_sample_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Group --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Group</label>
                                            <div class="form-control-wrap">
                                                <select id="group_id" name="txt_group_id" class="form-select" required>
                                                    <option value="">Select Group</option>
                                                </select>
                                                @error('txt_group_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Test --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Test</label>
                                            <div class="form-control-wrap">
                                                <select id="test_id" name="txt_test_id" class="form-select"
                                                    data-search="on" required>
                                                    <option value="">Select Test</option>
                                                </select>
                                                @error('txt_test_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Standards --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Standards (Optional)</label>
                                            <div class="form-control-wrap">
                                                <select id="standard_ids" name="standard_ids[]"
                                                    class="form-select js-select2" multiple
                                                    data-placeholder="Select standards...">
                                                </select>
                                                <small class="text-muted d-block mt-1">Leave empty if manuscript applies to
                                                    all standards or test has no standards.</small>
                                                @error('standard_ids')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Primary Tests Section --}}
                                        <div class="col-md-4" id="primary-tests-section">
                                            <div class="form-group">
                                                <label class="form-label">Primary Tests (Optional)</label>
                                                <div class="form-control-wrap position-relative">
                                                    <input type="text" id="primary-test-search" class="form-control"
                                                        placeholder="Search or enter primary test name" autocomplete="off">
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
                                                        placeholder="Select primary test first" autocomplete="off" disabled>
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

                                        {{-- Manuscript Name --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Manuscript Name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="txt_name" class="form-control"
                                                    placeholder="Enter manuscript name" required>
                                                @error('txt_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Manuscript Content --}}
                                        <div class="col-md-12 mt-4">
                                            <label class="form-label">Manuscript Content Template</label>
                                            <div class="form-control-wrap">
                                                <textarea class="summernote-basic form-control" name="m22_content" id="m22_content" rows="10"
                                                    placeholder="Write the manuscript report template here..."></textarea>
                                                @error('m22_content')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Submit --}}
                                        <div class="col-md-4 text-end mt-5">
                                            <button type="submit" class="btn btn-primary">
                                                <em class="icon ni ni-save"></em>
                                                <span>Create</span>
                                            </button>
                                        </div>
                                        {{-- Existing Manuscripts Section --}}
                                        <div class="col-md-12">
                                            <div id="existing_manuscripts" class="mt-4"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> {{-- .nk-block --}}
                </div>
            </div>
        </div>
    </div>

    {{-- JS Section --}}
    <script>
        const sampleSelect = document.getElementById('sample_id');
        const groupSelect = document.getElementById('group_id');
        const testSelect = document.getElementById('test_id');
        const manuscriptSection = document.getElementById('existing_manuscripts');

        // Load groups when sample changes
        sampleSelect.addEventListener('change', function() {
            const sampleId = this.value;
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            testSelect.innerHTML = '<option value="">Select Test</option>';
            manuscriptSection.innerHTML = '';

            if (!sampleId) return;

            fetch(`{{ route('get_groups') }}?sample_id=${sampleId}`)
                .then(res => res.json())
                .then(groups => {
                    groups.forEach(g => {
                        groupSelect.innerHTML +=
                            `<option value="${g.m11_group_code}">${g.m11_name}</option>`;
                    });
                })
                .catch(err => console.error('Error loading groups:', err));
        });

        // Load tests when group changes
        groupSelect.addEventListener('change', function() {
            const groupId = this.value;
            testSelect.innerHTML = '<option value="">Select Test</option>';
            manuscriptSection.innerHTML = '';

            if (!groupId) return;

            fetch(`{{ route('get_tests') }}?group_id=${groupId}`)
                .then(res => res.json())
                .then(tests => {
                    tests.forEach(t => {
                        testSelect.innerHTML +=
                            `<option value="${t.m12_test_number}">${t.m12_name}</option>`;
                    });
                })
                .catch(err => console.error('Error loading tests:', err));
        });

        // Load existing manuscripts and standards when test changes
        testSelect.addEventListener('change', function() {
            const testId = this.value;
            manuscriptSection.innerHTML = '';
            const standardSelect = document.getElementById('standard_ids');

            if (window.jQuery && $(standardSelect).hasClass('js-select2')) {
                $(standardSelect).empty().trigger('change');
            } else {
                standardSelect.innerHTML = '';
            }

            if (!testId) return;

            // Fetch standards for this test
            fetch(`{{ route('get_test_standards') }}?test_id=${testId}`)
                .then(res => res.json())
                .then(standards => {
                    let optionsHTML = '';
                    standards.forEach(s => {
                        optionsHTML += `<option value="${s.m15_standard_id}">${s.m15_method}</option>`;
                    });

                    if (window.jQuery && $(standardSelect).hasClass('js-select2')) {
                        $(standardSelect).html(optionsHTML).trigger('change');
                    } else {
                        standardSelect.innerHTML = optionsHTML;
                    }
                })
                .catch(err => console.error('Error loading standards:', err));

            // Fetch existing manuscripts
            fetch(`{{ route('get_manuscripts') }}?test_id=${testId}`)
                .then(res => res.json())
                .then(manuscripts => {
                    if (manuscripts.length > 0) {
                        let html = `
                            <div class="alert alert-info">
                                <strong>${manuscripts.length}</strong> existing manuscript(s) found for this test:
                            </div>
                            <div class="card card-bordered">
                                <div class="card-inner p-2">
                                    <ul class="list-group list-group-flush">
                        `;
                        manuscripts.forEach(m => {
                            html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>${m.m22_name}</span>
                                </li>`;
                        });
                        html += `
                                    </ul>
                                </div>
                            </div>
                        `;
                        manuscriptSection.innerHTML = html;
                    } else {
                        manuscriptSection.innerHTML = `
                            <div class="alert alert-success">
                                No existing manuscripts found for this test. You can create a new one.
                            </div>`;
                    }
                })
                .catch(err => console.error('Error loading manuscripts:', err));
        });
        // Primary & Secondary Tests Logic
        let selectedPrimaryTests = [];
        let selectedSecondaryTests = [];
        let currentPrimaryTestForSecondary = null;
        let searchTimeout;

        const searchRoutes = {
            'primary_tests': "{{ route('search_primary_tests') }}"
        };

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
                searchInput.prop('disabled', false).attr('placeholder', 'Search or enter secondary test name');
            } else {
                searchInput.prop('disabled', true).attr('placeholder', 'Select primary test first').val('');
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

        $(document).on('click', '.search-dropdown-item:not(.disabled)', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const type = $(this).data('type');
            const dropdown = $(this).parent();

            if (dropdown.hasClass('primary-tests-dropdown')) {
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

        $('#create-primary-test-btn').on('click', function() {
            const name = $('#primary-test-search').val();
            createNewItem('primary_tests', name);
        });

        $('#create-secondary-test-btn').on('click', function() {
            const name = $('#secondary-test-search').val();
            if (currentPrimaryTestForSecondary) {
                createNewSecondaryTest(name, currentPrimaryTestForSecondary);
            }
        });

        $(document).on('click', '.remove-item', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const primaryId = $(this).data('primary-id');
            removeSelectedItem(type, id, primaryId);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.form-control-wrap').length) {
                $('.search-dropdown').hide();
                $('#primary-tests-create-section, #secondary-tests-create-section').hide();
            }
        });

        $('#createManuscriptForm').on('submit', function() {
            updateHiddenInputs();
        });

        function handleSearch(type, query) {
            clearTimeout(searchTimeout);
            const dropdownClassMap = {
                'primary_tests': '.primary-tests-dropdown'
            };
            const dropdown = $(dropdownClassMap[type]);
            const createSection = $(`#${type.replace('_', '-')}-create-section`);
            const selectedGroupId = $('#group_id').val();

            if (query.length < 1) {
                dropdown.hide().empty();
                createSection.hide();
                return;
            }

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
                                const isSelected = getSelectedArray(type).find(s => s.id == item
                                    .id);
                                if (!isSelected) {
                                    hasResults = true;
                                    dropdown.append(`
                                        <div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}" data-type="${type}">
                                            ${item.name}
                                        </div>
                                    `);
                                }
                            });

                            if (hasResults) dropdown.show();
                            else {
                                dropdown.hide();
                                showCreateOption(type, query);
                            }
                        } else {
                            dropdown.hide();
                            showCreateOption(type, query);
                        }
                    },
                    error: function() {
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
                                const isSelected = selectedSecondaryTests.find(s => s.id == item
                                    .id && s.primary_test_id ==
                                    currentPrimaryTestForSecondary);
                                if (!isSelected) {
                                    hasResults = true;
                                    dropdown.append(`
                                        <div class="search-dropdown-item" data-id="${item.id}" data-name="${item.name}" data-type="secondary_tests">
                                            ${item.name}
                                        </div>
                                    `);
                                }
                            });

                            if (hasResults) dropdown.show();
                            else {
                                dropdown.hide();
                                showCreateOption('secondary_tests', query);
                            }
                        } else {
                            dropdown.hide();
                            showCreateOption('secondary_tests', query);
                        }
                    }
                });
            }, 300);
        }

        function showCreateOption(type, query) {
            $(`#${type.replace('_', '-')}-create-section`).show();
            $(`#${type.replace('_', '-')}-create-name`).text(query);
        }

        function getSelectedArray(type) {
            return type === 'primary_tests' ? selectedPrimaryTests : selectedSecondaryTests;
        }

        function addSelectedItem(type, data) {
            const array = getSelectedArray(type);

            if (type === 'secondary_tests') {
                if (array.find(item => item.id == data.id && item.primary_test_id == data.primary_test_id)) return;
            } else {
                if (array.find(item => item.id == data.id)) return;
            }

            array.push(data);
            displaySelectedItems(type);

            if (type === 'primary_tests') updatePrimaryTestSelector();
            updateHiddenInputs();
        }

        function removeSelectedItem(type, id, primaryId = null) {
            if (type === 'secondary_tests' && primaryId) {
                selectedSecondaryTests = selectedSecondaryTests.filter(item => !(item.id == id && item.primary_test_id ==
                    primaryId));
            } else if (type === 'primary_tests') {
                selectedPrimaryTests = selectedPrimaryTests.filter(item => item.id != id);
                selectedSecondaryTests = selectedSecondaryTests.filter(item => item.primary_test_id != id);
                updatePrimaryTestSelector();
                displaySelectedItems('secondary_tests');
            }
            displaySelectedItems(type);
            updateHiddenInputs();
        }

        function displaySelectedItems(type) {
            const container = $(`#${type.replace('_', '-')}-selected-container`);
            container.empty();

            if (type === 'primary_tests') {
                selectedPrimaryTests.forEach(item => {
                    container.append(`
                        <span class="selected-item primary-test" data-id="${item.id}" data-type="primary_tests">
                            ${item.name} <span class="remove-item" data-id="${item.id}" data-type="primary_tests">×</span>
                        </span>
                    `);
                });
            } else if (type === 'secondary_tests') {
                const grouped = {};
                selectedSecondaryTests.forEach(item => {
                    const primaryTest = selectedPrimaryTests.find(p => p.id == item.primary_test_id);
                    const primaryName = primaryTest ? primaryTest.name : 'Unknown Primary Test';
                    if (!grouped[item.primary_test_id]) grouped[item.primary_test_id] = {
                        primaryName,
                        tests: []
                    };
                    grouped[item.primary_test_id].tests.push(item);
                });

                Object.values(grouped).forEach(group => {
                    container.append(`
                        <div class="primary-test-group col-md-4">
                            <div class="primary-test-title">${group.primaryName}</div>
                            <div class="secondary-tests">
                                ${group.tests.map(test => `
                                        <span class="selected-item secondary-test" data-id="${test.id}" data-type="secondary_tests" data-primary-id="${test.primary_test_id}">
                                            ${test.name} <span class="remove-item" data-id="${test.id}" data-type="secondary_tests" data-primary-id="${test.primary_test_id}">×</span>
                                        </span>
                                    `).join('')}
                            </div>
                        </div>
                    `);
                });
            }
        }

        function updatePrimaryTestSelector() {
            const selector = $('#primary-test-selector');
            selector.empty().append('<option value="">Select Primary Test</option>');

            if (selectedPrimaryTests.length > 0) {
                selectedPrimaryTests.forEach(test => selector.append(`<option value="${test.id}">${test.name}</option>`));
                selector.show();
                $('#secondary-tests-section').removeClass('disabled');
            } else {
                selector.hide();
                $('#secondary-tests-section').addClass('disabled');
                $('#secondary-test-search').prop('disabled', true).attr('placeholder', 'Select primary test first');
                currentPrimaryTestForSecondary = null;
            }
        }

        function createNewItem(type, name) {
            const selectedSampleId = $('#sample_id').val();
            const selectedGroupId = $('#group_id').val();

            if (!selectedSampleId || !selectedGroupId) {
                alert('Please select sample and group first.');
                return;
            }

            $.ajax({
                url: "{{ route('create_primary_test') }}",
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
                    } else alert(`Error creating: ` + response.message);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || `Error creating primary test. Please try again.`);
                }
            });
        }

        function createNewSecondaryTest(name, primaryTestId) {
            const selectedSampleId = $('#sample_id').val();
            const selectedGroupId = $('#group_id').val();

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
                    } else alert('Error creating secondary test: ' + response.message);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error creating secondary test. Please try again.');
                }
            });
        }

        function updateHiddenInputs() {
            $('input[name="primary_test_ids[]"], input[name="secondary_test_ids[]"], input[name="secondary_test_primary_ids[]"]')
                .remove();

            selectedPrimaryTests.forEach(item => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'primary_test_ids[]',
                    value: item.id
                }).appendTo('#createManuscriptForm');
            });

            selectedSecondaryTests.forEach(item => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'secondary_test_ids[]',
                    value: item.id
                }).appendTo('#createManuscriptForm');
                $('<input>').attr({
                    type: 'hidden',
                    name: 'secondary_test_primary_ids[]',
                    value: item.primary_test_id
                }).appendTo('#createManuscriptForm');
            });
        }
    </script>
@endsection
