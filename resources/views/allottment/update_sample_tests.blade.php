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

                                            <!-- Package Information -->
                                            @if($sample->m19_package_id && $package)
                                                <div class="alert alert-info mb-4">
                                                    <div class="d-flex align-items-center">
                                                        <em class="icon ni ni-info-fill me-2"></em>
                                                        <div>
                                                            <strong>Package Applied:</strong> {{ $package->m19_name }}
                                                            <br>
                                                            <small>Package Charges: ₹{{ number_format($package->m19_charges, 2) }} 
                                                            (Fixed - will not change even if package tests are removed)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Section 1: Tests -->
                                            <div class="section mb-2">
                                                <h5 class="section-title mb-3">Tests</h5>
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
                                                                            {{ old('dd_group', $sample->m11_group_code ?? '') == ($group->m11_group_code ?? $group->id) ? 'selected' : '' }}>
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
                                                                <th style="width: 20%">Test Name</th>
                                                                <th style="width: 20%">Standard / Method</th>
                                                                <th style="width: 10%">Unit</th>
                                                                <th style="width: 15%">Charge (₹)</th>
                                                                <th style="width: 15%">Status</th>
                                                                <th style="width: 10%">Type</th>
                                                                <th style="width: 10%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="testsTableBody">
                                                            @php
                                                                $packageTestIds = $packageTests->pluck('m12_test_id')->toArray() ?? [];
                                                            @endphp
                                                            @forelse ($sampleTests as $test)
                                                                @php
                                                                    $isPackageTest = in_array($test->m12_test_id, $packageTestIds);
                                                                @endphp
                                                                <tr class="test-row {{ $isPackageTest ? 'table-success package-test' : '' }}" 
                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                    data-is-package="{{ $isPackageTest ? '1' : '0' }}">
                                                                    <td>
                                                                        <span class="test-name-display">
                                                                            {{ $test->test->m12_name ?? 'N/A' }}
                                                                        </span>
                                                                        <input type="hidden" name="txt_test_ids[]"
                                                                            value="{{ $test->m12_test_id }}">
                                                                        <input type="hidden" name="txt_is_package[]"
                                                                            value="{{ $isPackageTest ? '1' : '0' }}">
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
                                                                        @if($isPackageTest)
                                                                            <input type="text" name="txt_charge[]"
                                                                                class="form-control form-control-sm charge-field package-charge"
                                                                                value="0" readonly
                                                                                title="Included in package">
                                                                        @else
                                                                            <input type="text" name="txt_charge[]"
                                                                                class="form-control form-control-sm charge-field"
                                                                                value="{{ $test->test->m12_charge ?? '0' }}" readonly>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="txt_status[]"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ $test->tr05_status ?? '' }}" readonly>
                                                                    </td>
                                                                    <td>
                                                                        @if($isPackageTest)
                                                                            <strong class="text-success fw-bold">Package</strong>
                                                                        @else
                                                                            <strong class="text-secondary fw-bold">Regular</strong>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-danger btn-delete-row"
                                                                            title="Delete row"
                                                                            data-is-package="{{ $isPackageTest ? '1' : '0' }}">
                                                                            <em class="icon ni ni-trash"></em>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr class="empty-state">
                                                                    <td colspan="7" class="py-4 text-center text-muted">
                                                                        No tests assigned yet. Select a group and search to add
                                                                        tests.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Section 2: Additional Charges -->
                                            <div class="section mb-2">
                                                <h5 class="section-title mb-3">Additional Charges</h5>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label d-flex justify-content-between align-items-center">
                                                                <span>Additional Items & Charges</span>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    id="addAdditionalItem">
                                                                    <i class="icon ni ni-plus"></i> Add
                                                                </button>
                                                            </label>

                                                            <div id="additionalItemsContainer">
                                                                @if(isset($sample->additional) && count($sample->additional) > 0)
                                                                    @foreach ($sample->additional as $index => $aditional)
                                                                        <div class="row mb-2 additional-item-row">
                                                                            <div class="col-md-6">
                                                                                <input type="text" class="form-control"
                                                                                    name="additional_items[{{ $index }}][item]"
                                                                                    placeholder="Additional Item"
                                                                                    value="{{ $aditional->item ?? '' }}"
                                                                                    autocomplete="off">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <input type="number" step="0.01"
                                                                                    class="form-control additional-charge-input"
                                                                                    name="additional_items[{{ $index }}][charge]"
                                                                                    placeholder="Charge" value="{{ $aditional->price ?? 0 }}">
                                                                            </div>
                                                                            <div class="col-md-2 d-flex align-items-center">
                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger btn-sm removeItem">
                                                                                    <em class="icon ni ni-trash"></em>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="row mb-2 additional-item-row">
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control"
                                                                                name="additional_items[0][item]"
                                                                                placeholder="Additional Item"
                                                                                autocomplete="off">
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="number" step="0.01"
                                                                                class="form-control additional-charge-input"
                                                                                name="additional_items[0][charge]"
                                                                                placeholder="Charge" value="0">
                                                                        </div>
                                                                        <div class="col-md-2 d-flex align-items-center">
                                                                            <button type="button"
                                                                                class="btn btn-outline-danger btn-sm removeItem">
                                                                                <em class="icon ni ni-trash"></em>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 3: Calculation -->
                                            <div class="section">
                                                <h5 class="section-title mb-3">Tax & Charges</h5>
                                                <div class="row g-2">
                                                    <!-- Charges Breakdown -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_package_charges">Package Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control package-charge"
                                                                    id="txt_package_charges" name="txt_package_charges"
                                                                    readonly value="{{ $package ? $package->m19_charges : 0 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_regular_testing_charges">Regular Testing
                                                                Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_regular_testing_charges" name="txt_regular_testing_charges"
                                                                    readonly value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_total_testing_charges">Total Testing Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_total_testing_charges" name="txt_total_testing_charges"
                                                                    readonly value="0">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Sample Type and Additional Charges -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_sample_type">Sample Type</label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-control" name="txt_sample_type" id="tr04_sample_type">
                                                                    <option value="normal" {{ old('txt_sample_type', $sample->tr04_sample_type ?? '') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                                                    <option value="tatkal" {{ old('txt_sample_type', $sample->tr04_sample_type ?? '') == 'Tatkal' ? 'selected' : '' }}>Tatkal (+50%)</option>
                                                                </select>
                                                                <small class="form-text text-muted" id="tatkal_note" style="display: none;">
                                                                    +50% charges applied on total testing charges
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_aditional_charges">Additional
                                                                Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_aditional_charges" value="0"
                                                                    name="txt_aditional_charges" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_subtotal_charges">Sub Total
                                                                (Before GST)</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" readonly
                                                                    id="txt_subtotal_charges" value="0">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- GST Calculation -->
                                                    <input type="hidden" id="igst" value="{{ $roGst->igst ?? 0 }}">
                                                    <input type="hidden" id="cgst" value="{{ $roGst->cgst ?? 0 }}">
                                                    <input type="hidden" id="sgst" value="{{ $roGst->sgst ?? 0 }}">

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_gst_type">GST Type & Rate</label>
                                                            <div class="form-control-wrap">
                                                                <input class="form-control" type="text" id="txt_gst_type"
                                                                    readonly value="Not Calculated">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_gst_amount">GST Amount</label>
                                                            <div class="form-control-wrap">
                                                                <input class="form-control" type="text" id="txt_gst_amount"
                                                                    readonly value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_total_charges">Grand Total
                                                                (Including GST)</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" readonly
                                                                    id="txt_total_charges" name="txt_total_charges" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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

        .section {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            background-color: #fff;
        }

        .section-title {
            color: #1e40af;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }

        .charge-field {
            text-align: right;
            font-weight: 500;
        }

        .package-test {
            background-color: #f0f9ff !important;
            border-left: 4px solid #0d6efd;
        }

        .package-charge {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .calculation-row {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
    </style>

    <script>
        $(document).ready(function() {
            let selectedTestIds = [];
            let currentStandardRow = null;
            let searchTimeout;
            let additionalItemCounter = {{ isset($sample->additional) && count($sample->additional) > 0 ? count($sample->additional) : 1 }};
            let packageCharge = {{ $package ? $package->m19_charges : 0 }};
            let packageTestIds = @json($packageTestIds ?? []);

            // Initialize with existing test IDs
            @foreach ($sampleTests as $test)
                selectedTestIds.push({{ $test->m12_test_id }});
            @endforeach

            initializeEvents();
            calculateCharges();

            function initializeEvents() {
                handleTestSearch();
                handleStandardSelection();
                handleDeleteRows();
                handleAdditionalItems();
                handleSampleTypeChange();
            }

            function handleSampleTypeChange() {
                $('#tr04_sample_type').on('change', function() {
                    const isTatkal = $(this).val() === 'tatkal';
                    $('#tatkal_note').toggle(isTatkal);
                    calculateCharges();
                });

                // Initialize tatkal note visibility
                const isTatkal = $('#tr04_sample_type').val() === 'tatkal';
                $('#tatkal_note').toggle(isTatkal);
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
                                    const isPackageTest = packageTestIds.includes(test.id);
                                    $('<div>')
                                        .addClass('custom-dropdown-item')
                                        .data('test', test)
                                        .html(`<strong>${test.test_name}</strong>
                                               ${isPackageTest ? '<span class="badge bg-success ms-1">Package</span>' : ''}
                                               <br><small class="text-muted">
                                               Charge: ₹${isPackageTest ? '0 (Package)' : (test.charge || '0')} | Unit: ${test.unit || 'N/A'}</small>`)
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
                        calculateCharges();
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
                    const isPackage = $(this).data('is-package');

                    let message = 'Are you sure you want to remove this test?';
                    if (isPackage === '1') {
                        message = 'This is a package test. Package charges will remain the same even after removal. Are you sure you want to remove this test?';
                    }

                    if (confirm(message)) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            if (testId) {
                                selectedTestIds = selectedTestIds.filter(id => id !== testId);
                            }
                            checkEmptyState();
                            calculateCharges();
                        });
                    }
                });
            }

            function handleAdditionalItems() {
                // Add additional item row
                $(document).on('click', '#addAdditionalItem', function() {
                    const newRow = `
                        <div class="row mb-2 additional-item-row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" 
                                    name="additional_items[${additionalItemCounter}][item]" 
                                    placeholder="Additional Item" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <input type="number" step="0.01" class="form-control additional-charge-input" 
                                    name="additional_items[${additionalItemCounter}][charge]" 
                                    placeholder="Charge" value="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-outline-danger btn-sm removeItem">
                                    <i class="icon ni ni-minus"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    $('#additionalItemsContainer').append(newRow);
                    additionalItemCounter++;
                });

                // Remove additional item row
                $(document).on('click', '.removeItem', function() {
                    if ($('.additional-item-row').length > 1) {
                        $(this).closest('.additional-item-row').remove();
                        calculateCharges();
                    } else {
                        alert('At least one additional item row is required.');
                    }
                });

                // Update charges when additional charge inputs change
                $(document).on('input', '.additional-charge-input', function() {
                    calculateCharges();
                });
            }

            function addTestToTable(test) {
                removeEmptyState();

                const isPackageTest = packageTestIds.includes(test.id);

                const row = $(`
                    <tr class="test-row ${isPackageTest ? 'table-success package-test' : ''}" 
                        data-test-id="${test.id}" 
                        data-is-package="${isPackageTest ? '1' : '0'}">
                        <td>
                            <span class="test-name-display">
                                ${test.test_name}
                                ${isPackageTest ? '<span class="badge bg-success ms-1">Package</span>' : ''}
                            </span>
                            <input type="hidden" name="txt_test_ids[]" value="${test.id}">
                            <input type="hidden" name="txt_is_package[]" value="${isPackageTest ? '1' : '0'}">
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
                            ${isPackageTest ? 
                                '<input type="text" name="txt_charge[]" class="form-control form-control-sm charge-field package-charge" value="0" readonly title="Included in package">' :
                                `<input type="text" name="txt_charge[]" class="form-control form-control-sm charge-field" value="${test.charge || '0'}" readonly>`
                            }
                        </td>
                        <td>
                            <input type="text" name="txt_status[]" class="form-control form-control-sm" readonly value="PENDING">
                        </td>
                        <td>
                            ${isPackageTest ? 
                                '<strong class="text-success fw-bold">Package</strong>' :
                                '<strong class="text-secondary fw-bold">Regular</strong>'
                            }
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" 
                                title="Delete row"
                                data-is-package="${isPackageTest ? '1' : '0'}">
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

            function calculateCharges() {
                // Package charges remain fixed regardless of package tests
                const fixedPackageCharges = packageCharge;
                
                // Calculate regular testing charges (excluding package tests)
                let regularTestingCharges = 0;
                $('.test-row').each(function() {
                    const isPackage = $(this).data('is-package');
                    if (!isPackage) {
                        const charge = parseFloat($(this).find('.charge-field').val()) || 0;
                        regularTestingCharges += charge;
                    }
                });
                
                // Calculate total testing charges (Package + Regular)
                const totalTestingCharges = fixedPackageCharges + regularTestingCharges;
                
                // Apply Tatkal charges if selected
                const sampleType = $('#tr04_sample_type').val();
                let finalTestingCharges = totalTestingCharges;
                let tatkalCharges = 0;
                
                if (sampleType === 'tatkal') {
                    tatkalCharges = totalTestingCharges * 0.5; // 50% extra
                    finalTestingCharges = totalTestingCharges + tatkalCharges;
                }
                
                // Calculate additional charges
                let additionalCharges = 0;
                $('.additional-charge-input').each(function() {
                    additionalCharges += parseFloat($(this).val()) || 0;
                });
                
                // Calculate subtotal (After Tatkal + Additional)
                const subtotalCharges = finalTestingCharges + additionalCharges;
                
                // Get GST rates
                const igst = parseFloat($('#igst').val()) || 0;
                const cgst = parseFloat($('#cgst').val()) || 0;
                const sgst = parseFloat($('#sgst').val()) || 0;
                
                // Determine GST type and rate
                let gstType = "Not Applicable";
                let gstRate = 0;
                
                if (igst > 0) {
                    gstType = `IGST (${igst}%)`;
                    gstRate = igst;
                } else if (cgst > 0 && sgst > 0) {
                    gstType = `CGST (${cgst}%) + SGST (${sgst}%)`;
                    gstRate = cgst + sgst;
                }
                
                // Calculate GST amount
                const gstAmount = subtotalCharges * (gstRate / 100);
                
                // Calculate grand total
                const grandTotal = subtotalCharges + gstAmount;
                
                // Update form fields
                $('#txt_package_charges').val(fixedPackageCharges.toFixed(2));
                $('#txt_regular_testing_charges').val(regularTestingCharges.toFixed(2));
                $('#txt_total_testing_charges').val(totalTestingCharges.toFixed(2));
                $('#txt_aditional_charges').val(additionalCharges.toFixed(2));
                $('#txt_subtotal_charges').val(subtotalCharges.toFixed(2));
                $('#txt_gst_type').val(gstType);
                $('#txt_gst_amount').val(gstAmount.toFixed(2));
                $('#txt_total_charges').val(grandTotal.toFixed(2));
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
                            <td colspan="7" class="py-4 text-center text-muted">
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