<!-- resources/views/allottment/update_sample_tests.blade.php -->

@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title mb-0">Update Sample Tests</h4>
                                <a href="{{ route('view_allottment') }}" class="btn btn-outline-primary btn-sm">
                                    <em class="icon ni ni-caret-left-fill"></em> Back
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-inner">
                                        <form id="updateTestsForm" method="POST"
                                            action="{{ route('edit_sample', $sample->tr04_sample_registration_id) }}">
                                            @csrf

                                            <div class="row gy-3 mb-4">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label" for="dd_group">Group</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-control" name="dd_group" id="dd_group">
                                                                <option value="">-- Select Group --</option>
                                                                @foreach ($groups as $group)
                                                                    <option
                                                                        value="{{ $group->m11_group_code ?? $group->id }}"
                                                                        {{ old('dd_group') == ($group->m11_group_code ?? $group->id) ? 'selected' : '' }}>
                                                                        {{ $group->m11_name ?? $group->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label class="form-label" for="txt_search_test">Search Test</label>
                                                        <div class="form-control-wrap" style="position: relative;">
                                                            <input type="text" class="form-control" id="txt_search_test"
                                                                placeholder="Select group first, then search for tests..."
                                                                autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle" id="testsTable">
                                                    <thead class="table-light sticky-top">
                                                        <tr>
                                                            <th style="width: 25%">Test Name</th>
                                                            <th style="width: 25%">Standard / Method</th>
                                                            <th style="width: 15%">Unit</th>
                                                            <th style="width: 20%">Status</th>
                                                            <th style="width: 15%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="testsTableBody">
                                                        @forelse ($sampleTests as $test)
                                                            <tr class="test-row" data-test-id="{{ $test->m12_test_id }}">
                                                                <td>
                                                                    <span
                                                                        class="test-name-display">{{ $test->test->m12_name ?? 'N/A' }}</span>
                                                                    <input type="hidden" name="txt_test_ids[]"
                                                                        value="{{ $test->m12_test_id }}">
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-link p-0 text-primary choose-standard-btn"
                                                                        data-test-id="{{ $test->m12_test_id }}">
                                                                        @if ($test->standard && $test->m15_standard_id)
                                                                            {{ $test->standard->m15_method ?? 'Standard Selected' }}
                                                                        @else
                                                                            Click to choose standard
                                                                        @endif
                                                                    </button>
                                                                    <input type="hidden" name="txt_standard_ids[]"
                                                                        class="standard-id-input"
                                                                        value="{{ $test->m15_standard_id }}">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="txt_unit[]"
                                                                        class="form-control form-control-sm unit-field"
                                                                        value="{{ $test->test->m12_unit ?? '' }}" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="txt_status[]"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $test->tr05_status ?? '' }}" readonly>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger btn-delete-row"
                                                                        title="Delete row">
                                                                        <em class="icon ni ni-trash"></em>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr class="empty-state">
                                                                <td colspan="5" class="py-4 text-center text-muted">
                                                                    No tests assigned yet. Select a group and search to add
                                                                    tests.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <div class="d-flex">
                                                    <a href="{{ route('view_allottment') }}"
                                                        class="btn btn-secondary btn-sm">Cancel</a>
                                                    <button type="submit" class="btn btn-success ms-3 btn-sm">
                                                        <em class="icon ni ni-check-circle"></em> Update Tests
                                                    </button>
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
        </div>
    </div>

    <!-- Standard Selection Modal -->
    <div class="modal fade" id="standardModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Standard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul id="standard-list" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-dropdown {
            position: absolute;
            z-index: 1050;
            display: none;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-height: 300px;
            overflow-y: auto;
            margin-top: 2px;
            width: 100%;
        }

        .custom-dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-dropdown-item:last-child {
            border-bottom: none;
        }

        .custom-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-message {
            padding: 15px;
            text-align: center;
            color: #6c757d;
        }

        .test-row:hover {
            background-color: #f8f9fa;
        }

        .choose-standard-btn {
            text-decoration: none;
            border: none;
            background: none;
            cursor: pointer;
            text-align: left;
        }

        .choose-standard-btn:hover {
            text-decoration: underline;
            color: #0d6efd !important;
        }

        .table-warning {
            background-color: #fff3cd !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            let selectedTestIds = [];
            let currentStandardRow = null;
            let searchTimeout;

            // Initialize with existing test IDs
            @foreach ($sampleTests as $test)
                selectedTestIds.push({{ $test->m12_test_id }});
            @endforeach

            initializeEvents();

            function initializeEvents() {
                handleTestSearch();
                handleStandardSelection();
                handleDeleteRows();
            }

            function handleTestSearch() {
                let $dropdown = createDropdown('test-dropdown');
                let $searchInput = $('#txt_search_test');

                $searchInput.off('input.test').on('input.test', function() {
                    const query = $(this).val().trim();
                    const groupId = $('#dd_group').val();

                    if (query.length < 2) {
                        $dropdown.hide().empty();
                        return;
                    }

                    if (!groupId) {
                        alert('Please select a group first.');
                        $searchInput.val('');
                        return;
                    }

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        positionDropdown($searchInput, $dropdown);
                        $dropdown.html('<div class="dropdown-message">Searching...</div>');

                        $.getJSON('{{ route('search_test') }}', {
                            query: query,
                            group_id: groupId
                        }, function(tests) {
                            const filtered = tests.filter(t => !selectedTestIds.includes(t
                                .id));
                            $dropdown.empty();

                            if (filtered.length) {
                                filtered.forEach(test => {
                                    console.log(test)
                                    $('<div>')
                                        .addClass('custom-dropdown-item')
                                        .data('test', test)
                                        .text(test.test_name)
                                        .appendTo($dropdown);
                                });
                            } else {
                                $dropdown.html(
                                    '<div class="dropdown-message">No tests found.</div>'
                                );
                            }
                        }).fail(function() {
                            $dropdown.html(
                                '<div class="dropdown-message">Error searching tests.</div>'
                            );
                        });
                    }, 300);
                });

                $(document).on('click', '#test-dropdown .custom-dropdown-item', function() {
                    const test = $(this).data('test');
                    if (test) {
                        addTestToTable(test);
                        selectedTestIds.push(test.id);
                        $searchInput.val('');
                        $dropdown.hide().empty();
                    }
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#test-dropdown, #txt_search_test').length) {
                        $dropdown.hide();
                    }
                });
            }

            function handleStandardSelection() {
                // Handle click on the standard button
                $(document).on('click', '.choose-standard-btn', function(e) {
                    e.preventDefault();
                    currentStandardRow = $(this).closest('tr');
                    const testId = $(this).data('test-id');
                    loadStandards(testId);
                    $('#standardModal').modal('show');
                });

                // Handle selection from modal
                $(document).on('click', '.standard-item', function() {
                    const standardName = $(this).text().trim();
                    const standardId = $(this).data('id');

                    if (currentStandardRow && standardId) {
                        // Update the button text with the selected standard
                        currentStandardRow.find('.choose-standard-btn').text(standardName);
                        currentStandardRow.find('.standard-id-input').val(standardId);
                        $('#standardModal').modal('hide');
                    }
                });
            }

            function handleDeleteRows() {
                $(document).on('click', '.btn-delete-row', function() {
                    const row = $(this).closest('tr');
                    const testId = row.data('test-id');

                    if (confirm('Are you sure you want to remove this test?')) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if (testId) {
                                selectedTestIds = selectedTestIds.filter(id => id !== testId);
                            }
                            checkEmptyState();
                        });
                    }
                });
            }

            function addTestToTable(test) {
                removeEmptyState();

                const row = $(`
                    <tr class="test-row" data-test-id="${test.id}">
                        <td>
                            <span class="test-name-display">${test.test_name}</span>
                            <input type="hidden" name="txt_test_ids[]" value="${test.id}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link p-0 text-primary choose-standard-btn" 
                                data-test-id="${test.id}">
                                Click to choose standard
                            </button>
                            <input type="hidden" name="txt_standard_ids[]" class="standard-id-input" value="">
                        </td>
                        <td>
                            <input type="text" name="txt_unit[]" class="form-control form-control-sm unit-field" 
                                value="${test.unit || ''}" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_status[]" class="form-control form-control-sm" readonly value="PENDING">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-delete-row" title="Delete row">
                                <em class="icon ni ni-trash"></em>
                            </button>
                        </td>
                    </tr>
                `);

                $('#testsTableBody').append(row);
            }

            function loadStandards(testId) {
                $('#standard-list').html('<li class="list-group-item">Loading standards...</li>');

                $.ajax({
                    url: '{{ route('get_standards_by_test') }}',
                    type: 'GET',
                    data: {
                        test_id: testId
                    },
                    success: function(standards) {
                        $('#standard-list').empty();
                        if (standards.length) {
                            standards.forEach(function(std) {
                                $('#standard-list').append(`
                                    <li class="list-group-item standard-item" data-id="${std.id}" style="cursor: pointer;">
                                        ${std.name}
                                    </li>
                                `);
                            });
                        } else {
                            $('#standard-list').html(
                                '<li class="list-group-item text-muted">No standards found for this test.</li>'
                            );
                        }
                    },
                    error: function() {
                        $('#standard-list').html(
                            '<li class="list-group-item text-danger">Error loading standards.</li>'
                        );
                    }
                });
            }

            function createDropdown(id) {
                $('#' + id).remove();
                return $('<div>', {
                    id: id,
                    class: 'custom-dropdown'
                }).appendTo('body').hide();
            }

            function positionDropdown($input, $dropdown) {
                const offset = $input.offset();
                $dropdown.css({
                    top: offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: $input.outerWidth()
                }).show();
            }

            function checkEmptyState() {
                if ($('#testsTableBody .test-row').length === 0) {
                    $('#testsTableBody').html(`
                        <tr class="empty-state">
                            <td colspan="5" class="py-4 text-center text-muted">
                                No tests assigned yet. Select a group and search to add tests.
                            </td>
                        </tr>
                    `);
                }
            }

            function removeEmptyState() {
                $('#testsTableBody .empty-state').remove();
            }
        });
    </script>
@endsection
