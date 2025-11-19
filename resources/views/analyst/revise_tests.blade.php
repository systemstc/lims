@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">

                        <!-- Header Section -->
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <div>
                                <h4 class="fw-bold text-uppercase mb-0 text-danger">
                                    <em class="icon ni ni-edit"></em> Revised Test Result Entry
                                </h4>
                                <small class="text-muted">Re-entering rejected test results - Sample:
                                    <strong>{{ $sample->tr04_reference_id }}</strong>
                                </small>
                                <div class="mt-1">
                                    <span class="badge bg-danger">REVISION REQUIRED</span>
                                    <small class="text-muted ms-2">Previous results were rejected and need revision</small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('rejected_samples') }}" class="btn btn-outline-primary btn-sm">
                                    <em class="icon ni ni-caret-left-fill"></em> Back to Rejected
                                </a>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('revise_test', $sample->tr04_reference_id) }}" method="POST"
                            enctype="multipart/form-data" id="reviseResultForm">
                            @csrf

                            <input type="hidden" name="registration_id" value="{{ $sample->tr04_reference_id }}">

                            <!-- Report Information -->
                            <div class="card shadow-sm border-danger mb-4">
                                <div class="card-header bg-danger text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase">
                                        <em class="icon ni ni-clipboard"></em> Revised Test Report Details
                                    </h6>
                                </div>
                                <div class="card-body px-4 py-3">
                                    <table class="table table-sm table-borderless align-middle mb-0 small w-100">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-muted w-25">Test Report No:</td>
                                                <td class="text-dark fw-semibold w-25">
                                                    {{ $sample->tr04_reference_id ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted w-25">Revised Date:</td>
                                                <td class="w-25">
                                                    <input type="date"
                                                        class="form-control form-control-sm bg-light border-danger"
                                                        name="test_date"
                                                        value="{{ old('test_date', $testDate ?? date('Y-m-d')) }}" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">No. of Samples:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ $sample->tr04_number_of_samples ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted">Sample Characteristics:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ $sample->labSample->m14_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Date of Performance of Tests:</td>
                                                <td>
                                                    <input type="date"
                                                        class="form-control form-control-sm bg-light border-danger"
                                                        name="performance_date"
                                                        value="{{ old('performance_date', $performanceDate ?? '') }}"
                                                        required>
                                                </td>
                                                <td class="fw-bold text-muted">Original Allotment Date:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ $sampleTests->first()->tr05_alloted_at ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">QAO / JQAO / Analyst:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ optional($sampleTests->first()?->allotedTo)->m06_name ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted">Technical Manager:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ Session::get('role') === 'Manager' ? Session::get('name') : optional($sampleTests->first()?->allotedBy)->m06_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Test Results -->
                            <div class="card border-danger shadow-sm mb-4">
                                <div class="card-header bg-danger text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase">
                                        <em class="icon ni ni-layers"></em> Revised Test Results
                                        <small class="opacity-75">(Previous rejected results shown in red)</small>
                                    </h6>
                                </div>
                                <div class="">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 10%">Sr. No.</th>
                                                <th>Test Name</th>
                                                <th style="width: 20%">Previous Result</th>
                                                <th style="width: 35%">Revised Result / Entry</th>
                                                <th style="width: 15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="testResultsBody">
                                            @foreach ($sampleTests as $key => $sampleTest)
                                                @php
                                                    $test = $sampleTest->test;
                                                    $primaryTests = $test->primaryTests ?? collect();
                                                    $existingTestResults = $rejectedResults->where(
                                                        'm12_test_number',
                                                        $test->m12_test_number,
                                                    );
                                                    $existingMainTestResult = $existingTestResults
                                                        ->whereNull('m16_primary_test_id')
                                                        ->whereNull('m17_secondary_test_id')
                                                        ->first();
                                                @endphp

                                                <!-- Main Test Row -->
                                                <tr class="bg-light fw-bold test-main-row"
                                                    data-test-id="{{ $test->m12_test_id }}"
                                                    data-test-number="{{ $test->m12_test_number }}">
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $test->m12_name ?? 'N/A' }}</strong>
                                                        @if ($sampleTest->standard)
                                                            - ( {{ $sampleTest->standard->m15_method ?? 'N/A' }} )
                                                        @endif
                                                    </td>
                                                    <td class="text-danger fw-bold text-center">
                                                        @if ($existingMainTestResult)
                                                            {{ $existingMainTestResult->tr07_result ?? 'N/A' }}
                                                            @if ($existingMainTestResult->tr07_unit)
                                                                <br><small
                                                                    class="text-muted">({{ $existingMainTestResult->tr07_unit }})</small>
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($primaryTests->isEmpty())
                                                            <!-- Input field for main test when no primary tests -->
                                                            <div class="input-group input-group-sm">
                                                                <input type="text"
                                                                    class="form-control form-control-sm bg-light border-primary"
                                                                    name="results[{{ $test->m12_test_number }}][test][result]"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') }}"
                                                                    placeholder="Enter revised result" autocomplete="off"
                                                                    required>
                                                                <input type="text"
                                                                    class="form-control form-control-sm bg-light border-primary"
                                                                    style="max-width: 80px;"
                                                                    name="results[{{ $test->m12_test_number }}][test][unit]"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.unit', $existingMainTestResult->tr07_unit ?? ($test->m12_unit ?? '')) }}"
                                                                    placeholder="Unit">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][test_id]"
                                                                    value="{{ $test->m12_test_number }}">
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($primaryTests->isNotEmpty())
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm add-primary-test"
                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                data-test-number="{{ $test->m12_test_number }}"
                                                                data-primary-tests="{{ $primaryTests->toJson() }}">
                                                                <em class="icon ni ni-plus"></em> Primary
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-outline-warning btn-sm add-custom-field"
                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                data-test-number="{{ $test->m12_test_number }}"
                                                                data-type="test">
                                                                <em class="icon ni ni-plus"></em> Custom
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Existing Primary Tests from Rejected Results -->
                                                @foreach ($existingTestResults->whereNotNull('m16_primary_test_id')->whereNull('m17_secondary_test_id') as $pKey => $existingPrimaryResult)
                                                    @php
                                                        $primaryTest = $primaryTests
                                                            ->where(
                                                                'm16_primary_test_id',
                                                                $existingPrimaryResult->m16_primary_test_id,
                                                            )
                                                            ->first();
                                                    @endphp
                                                    @if ($primaryTest)
                                                        @include('testresult.partials.primary_test_row', [
                                                            'test' => $test,
                                                            'primaryTest' => $primaryTest,
                                                            'existingPrimaryResult' => $existingPrimaryResult,
                                                            'key' => $key,
                                                            'pKey' => $pKey,
                                                            'existingResults' => $existingTestResults,
                                                            'isRevision' => true,
                                                        ])
                                                    @endif
                                                @endforeach

                                                <!-- Custom Fields for Main Test -->
                                                @foreach ($rejectedCustomFields->where('m12_test_number', $test->m12_test_number)->whereNull('m16_primary_test_id')->whereNull('m17_secondary_test_id') as $customField)
                                                    <tr class="custom-field-row revision-highlight"
                                                        data-test-number="{{ $test->m12_test_number }}">
                                                        <td>{{ $key + 1 }}.C{{ $loop->iteration }}</td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm custom-field-input"
                                                                name="custom_fields[{{ $test->m12_test_number }}][{{ $customField->tr08_custom_field_id }}][name]"
                                                                value="{{ $customField->tr08_field_name }}"
                                                                placeholder="Custom Field Name" required>
                                                            <input type="hidden"
                                                                name="custom_fields[{{ $test->m12_test_number }}][{{ $customField->tr08_custom_field_id }}][custom_field_id]"
                                                                value="{{ $customField->tr08_custom_field_id }}">
                                                        </td>
                                                        <td class="text-danger fw-bold text-center">
                                                            {{ $customField->tr08_field_value }}
                                                            @if ($customField->tr08_field_unit)
                                                                <br><small
                                                                    class="text-muted">({{ $customField->tr08_field_unit }})</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text"
                                                                    class="form-control form-control-sm custom-field-input"
                                                                    name="custom_fields[{{ $test->m12_test_number }}][{{ $customField->tr08_custom_field_id }}][value]"
                                                                    value="{{ $customField->tr08_field_value }}"
                                                                    placeholder="Enter revised value" required>
                                                                <input type="text"
                                                                    class="form-control form-control-sm custom-field-input"
                                                                    style="max-width: 80px;"
                                                                    name="custom_fields[{{ $test->m12_test_number }}][{{ $customField->tr08_custom_field_id }}][unit]"
                                                                    value="{{ $customField->tr08_field_unit ?? '' }}"
                                                                    placeholder="Unit">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-custom-field"
                                                                data-field-id="custom_field_{{ $customField->tr08_custom_field_id }}">
                                                                <em class="icon ni ni-trash"></em>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- New Custom Fields Section -->
                            <div class="card border-info shadow-sm mb-4">
                                <div class="card-header bg-info text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase">
                                        <em class="icon ni ni-plus"></em> Additional Custom Fields
                                        <small class="opacity-75">(Optional new fields)</small>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="newCustomFieldsContainer">
                                        <!-- New custom fields will be added here dynamically -->
                                    </div>
                                    <button type="button" class="btn btn-outline-info btn-sm mt-2"
                                        id="addNewCustomField">
                                        <em class="icon ni ni-plus"></em> Add New Custom Field
                                    </button>
                                </div>
                            </div>

                            <!-- Remarks Section -->
                            <div class="card border-secondary shadow-sm mb-4">
                                <div class="card-header bg-secondary text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase">
                                        <em class="icon ni ni-edit"></em> Revision Remarks
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <textarea name="remarks" class="form-control" rows="3"
                                        placeholder="Add any remarks about the revisions made (what was changed and why)...">{{ old('remarks') }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        <small>
                                            <em class="icon ni ni-info text-warning"></em>
                                            All fields marked with previous results require revision.
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <button type="submit" name="action" value="DRAFT"
                                            class="btn btn-outline-primary">
                                            <em class="icon ni ni-file-text"></em> Save as Draft
                                        </button>
                                        @if (Session::get('role') === 'DEO')
                                            <button type="submit" name="action" value="RESULTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Submit Revisions
                                            </button>
                                        @else
                                            <button type="submit" name="action" value="SUBMITTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Submit Revisions
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Test Selection Modal -->
    <div class="modal fade" id="primaryTestModal" tabindex="-1" aria-labelledby="primaryTestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="primaryTestModalLabel">Select Primary Test</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Primary Test Name</th>
                                    <th>Unit</th>
                                    <th>Requirement</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="primaryTestList">
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-danger {
            border-color: #dc3545 !important;
        }

        .border-primary {
            border-color: #0d6efd !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .test-main-row {
            background-color: #e3f2fd !important;
            border-left: 4px solid #0d6efd !important;
        }

        .primary-test-row {
            background-color: #f3e5f5 !important;
            border-left: 4px solid #9c27b0 !important;
        }

        .secondary-test-row {
            background-color: #fafafa !important;
            border-left: 4px solid #607d8b !important;
        }

        .custom-field-row {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
        }

        .custom-field-input {
            background-color: #fffdf6 !important;
            border: 1px dashed #ffc107 !important;
        }

        .revision-highlight {
            animation: pulse-revision 2s infinite;
        }

        @keyframes pulse-revision {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: #fff3cd;
            }

            100% {
                background-color: transparent;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentTestNumber = null;
            let currentTestId = null;
            let customFieldCounter = {{ $rejectedCustomFields->count() }};
            let usedPrimaryTests = new Map();
            let usedSecondaryTests = new Map();

            // Initialize used tests from existing rejected results
            @foreach ($rejectedResults as $result)
                @if ($result->m16_primary_test_id && !$result->m17_secondary_test_id)
                    if (!usedPrimaryTests.has('{{ $result->m12_test_number }}')) {
                        usedPrimaryTests.set('{{ $result->m12_test_number }}', new Set());
                    }
                    usedPrimaryTests.get('{{ $result->m12_test_number }}').add(
                        '{{ $result->m16_primary_test_id }}');
                @endif

                @if ($result->m17_secondary_test_id)
                    if (!usedSecondaryTests.has('{{ $result->m16_primary_test_id }}')) {
                        usedSecondaryTests.set('{{ $result->m16_primary_test_id }}', new Set());
                    }
                    usedSecondaryTests.get('{{ $result->m16_primary_test_id }}').add(
                        '{{ $result->m17_secondary_test_id }}');
                @endif
            @endforeach

            // Event Delegation for dynamically added buttons
            document.getElementById('testResultsBody').addEventListener('click', function(e) {
                // Add Primary Test Button
                if (e.target.closest('.add-primary-test')) {
                    const button = e.target.closest('.add-primary-test');
                    currentTestNumber = button.getAttribute('data-test-number');
                    currentTestId = button.getAttribute('data-test-id');
                    const primaryTestsData = JSON.parse(button.getAttribute('data-primary-tests'));
                    showPrimaryTestModal(primaryTestsData);
                }

                // Add Secondary Test Button
                if (e.target.closest('.add-secondary-test')) {
                    const button = e.target.closest('.add-secondary-test');
                    const testNumber = button.getAttribute('data-test-number');
                    const primaryTestId = button.getAttribute('data-primary-test-id');
                    const secondaryTests = JSON.parse(button.getAttribute('data-secondary-tests') || '[]');
                    addSecondaryTestDropdown(testNumber, primaryTestId, secondaryTests);
                }

                // Add Custom Field Button
                if (e.target.closest('.add-custom-field')) {
                    const button = e.target.closest('.add-custom-field');
                    const testId = button.getAttribute('data-test-id');
                    const testNumber = button.getAttribute('data-test-number');
                    const primaryTestId = button.getAttribute('data-primary-test-id');
                    const secondaryTestId = button.getAttribute('data-secondary-test-id');
                    const type = button.getAttribute('data-type');
                    addCustomField(testNumber, primaryTestId, secondaryTestId, type);
                }

                // Remove Test Row Button
                if (e.target.closest('.remove-test-row')) {
                    const button = e.target.closest('.remove-test-row');
                    const type = button.getAttribute('data-type');
                    const id = button.getAttribute('data-id');
                    const testNumber = button.getAttribute('data-test-number');
                    const primaryTestId = button.getAttribute('data-primary-test-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This test will be removed from revision.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (type === 'primary') {
                                // Remove primary test and all its secondary tests
                                document.querySelectorAll(`[data-primary-test-id="${id}"]`)
                                    .forEach(row => row.remove());
                                // Remove from used primary tests
                                if (usedPrimaryTests.has(testNumber)) {
                                    usedPrimaryTests.get(testNumber).delete(id);
                                }
                                // Remove associated secondary tests from used map
                                usedSecondaryTests.delete(id);
                            } else if (type === 'secondary') {
                                // Remove only the specific secondary test
                                document.querySelectorAll(`[data-secondary-test-id="${id}"]`)
                                    .forEach(row => row.remove());
                                // Remove from used secondary tests
                                if (usedSecondaryTests.has(primaryTestId)) {
                                    usedSecondaryTests.get(primaryTestId).delete(id);
                                }
                            }
                            Swal.fire('Removed!', 'The test has been removed from revision.',
                                'success');
                        }
                    });
                }
            });

            // Remove Custom Field Button (event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-custom-field')) {
                    const button = e.target.closest('.remove-custom-field');
                    const fieldId = button.getAttribute('data-field-id');
                    const fieldToRemove = document.getElementById(fieldId);

                    Swal.fire({
                        title: 'Remove this field?',
                        text: 'This custom field will be deleted permanently.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed && fieldToRemove) {
                            fieldToRemove.remove();
                            Swal.fire('Deleted!', 'The custom field has been removed.', 'success');
                        }
                    });
                }
            });

            // Add New Custom Field
            document.getElementById('addNewCustomField').addEventListener('click', function() {
                addNewCustomField();
            });

            function showPrimaryTestModal(primaryTests) {
                const primaryTestList = document.getElementById('primaryTestList');
                primaryTestList.innerHTML = '';

                const usedSet = usedPrimaryTests.get(currentTestNumber) || new Set();
                const availablePrimaryTests = primaryTests.filter(test =>
                    !usedSet.has(test.m16_primary_test_id.toString())
                );

                if (availablePrimaryTests.length === 0) {
                    primaryTestList.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No more primary tests available for this test.
                        </td>
                    </tr>
                `;
                } else {
                    availablePrimaryTests.forEach(test => {
                        const hasSecondaryTests = test.secondaryTests && test.secondaryTests.length > 0;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>
                            <strong>${test.m16_name}</strong>
                            ${hasSecondaryTests ? 
                                '<br><small class="text-info">Has secondary tests available</small>' : 
                                ''
                            }
                            ${test.m16_requirement ? 
                                `<br><small class="text-warning">Requirement: ${test.m16_requirement}</small>` : 
                                ''
                            }
                        </td>
                        <td>
                            <small>${test.m16_unit || 'N/A'}</small>
                        </td>
                        <td>
                            <small>${test.m16_requirement || 'N/A'}</small>
                        </td>
                        <td>
                            <button type="button" 
                                class="btn btn-primary btn-sm select-primary-test"
                                data-primary-test-id="${test.m16_primary_test_id}"
                                data-primary-test-name="${test.m16_name}"
                                data-primary-test-unit="${test.m16_unit || ''}"
                                data-has-secondary="${hasSecondaryTests}"
                                data-secondary-tests='${JSON.stringify(test.secondaryTests || [])}'>
                                Select
                            </button>
                        </td>
                    `;
                        primaryTestList.appendChild(row);
                    });
                }

                // Add event listeners for modal buttons
                $('#primaryTestModal').on('shown.bs.modal', function() {
                    document.querySelectorAll('.select-primary-test').forEach(button => {
                        button.addEventListener('click', function() {
                            const primaryTestId = this.getAttribute('data-primary-test-id');
                            const primaryTestName = this.getAttribute(
                                'data-primary-test-name');
                            const primaryTestUnit = this.getAttribute(
                                'data-primary-test-unit');
                            const hasSecondary = this.getAttribute('data-has-secondary') ===
                                'true';
                            const secondaryTests = JSON.parse(this.getAttribute(
                                'data-secondary-tests') || '[]');

                            addPrimaryTestRow(currentTestNumber, currentTestId,
                                primaryTestId, primaryTestName,
                                primaryTestUnit, hasSecondary, secondaryTests);
                            $('#primaryTestModal').modal('hide');
                        });
                    });
                });

                $('#primaryTestModal').modal('show');
            }

            function addPrimaryTestRow(testNumber, testId, primaryTestId, primaryTestName, primaryTestUnit,
                hasSecondaryTests, secondaryTests) {
                const rowId = `primary_test_${testNumber}_${primaryTestId}`;

                const primaryTestHtml = `
            <tr id="${rowId}" class="primary-test-row revision-highlight"
                data-test-id="${testId}"
                data-test-number="${testNumber}"
                data-primary-test-id="${primaryTestId}">
                <td>${getNextSerialNumber(testNumber)}</td>
                <td>
                    <strong>${primaryTestName}</strong>
                </td>
                <td class="text-muted text-center">-</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="hidden"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][test_id]"
                            value="${testNumber}">
                        <input type="hidden"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][primary_test_id]"
                            value="${primaryTestId}">
                        <input type="text"
                            class="form-control form-control-sm border-primary bg-light"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][result]"
                            placeholder="Enter revised result"
                            autocomplete="off" required>
                        <input type="text"
                            class="form-control form-control-sm border-primary bg-light"
                            style="max-width: 80px;"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][unit]"
                            value="${primaryTestUnit}"
                            placeholder="Unit">
                    </div>
                </td>
                <td>
                    <button type="button"
                        class="btn btn-outline-danger btn-sm remove-test-row"
                        data-type="primary"
                        data-test-number="${testNumber}"
                        data-id="${primaryTestId}">
                        <em class="icon ni ni-trash"></em>
                    </button>
                    ${hasSecondaryTests ? `
                            <button type="button"
                                class="btn btn-outline-success btn-sm add-secondary-test"
                                data-test-number="${testNumber}"
                                data-primary-test-id="${primaryTestId}"
                                data-secondary-tests='${JSON.stringify(secondaryTests)}'>
                                <em class="icon ni ni-plus"></em> Secondary
                            </button>
                            ` : ''}
                    <button type="button"
                        class="btn btn-outline-warning btn-sm add-custom-field"
                        data-test-id="${testId}"
                        data-test-number="${testNumber}"
                        data-primary-test-id="${primaryTestId}"
                        data-type="primary">
                        <em class="icon ni ni-plus"></em> Custom
                    </button>
                </td>
            </tr>
            `;

                // Insert after the main test row
                const mainTestRow = document.querySelector(`.test-main-row[data-test-number="${testNumber}"]`);
                mainTestRow.insertAdjacentHTML('afterend', primaryTestHtml);

                // Mark this primary test as used
                if (!usedPrimaryTests.has(testNumber)) {
                    usedPrimaryTests.set(testNumber, new Set());
                }
                usedPrimaryTests.get(testNumber).add(primaryTestId.toString());

                // Store secondary tests for this primary test
                if (hasSecondaryTests) {
                    window.secondaryTestsData = window.secondaryTestsData || {};
                    window.secondaryTestsData[primaryTestId] = secondaryTests;
                }
            }

            function addSecondaryTestDropdown(testNumber, primaryTestId, secondaryTests) {
                const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                const dropdownId = `secondary_dropdown_${primaryTestId}`;

                // Remove existing dropdown if any
                const existingDropdown = document.getElementById(dropdownId);
                if (existingDropdown) {
                    existingDropdown.remove();
                }

                // Get available secondary tests
                const availableSecondaryTests = getAvailableSecondaryTests(primaryTestId, secondaryTests);

                if (availableSecondaryTests.length === 0) {
                    Swal.fire('Info', 'No more secondary tests available for this primary test.', 'info');
                    return;
                }

                const dropdownHtml = `
            <tr id="${dropdownId}" class="secondary-dropdown">
                <td colspan="5">
                    <div class="p-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" id="secondary_test_select_${primaryTestId}">
                                    <option value="">Select Secondary Test</option>
                                    ${availableSecondaryTests.map(test => 
                                        `<option value="${test.m17_secondary_test_id}" data-unit="${test.m17_unit || ''}">${test.m17_name}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" 
                                    class="form-control form-control-sm" 
                                    id="secondary_result_${primaryTestId}" 
                                    placeholder="Enter result">
                            </div>
                            <div class="col-md-2">
                                <input type="text" 
                                    class="form-control form-control-sm" 
                                    id="secondary_unit_${primaryTestId}" 
                                    placeholder="Unit">
                            </div>
                            <div class="col-md-3">
                                <button type="button" 
                                    class="btn btn-success btn-sm add-selected-secondary"
                                    data-test-number="${testNumber}"
                                    data-primary-test-id="${primaryTestId}">
                                    Add It
                                </button>
                                <button type="button" 
                                    class="btn btn-secondary btn-sm cancel-secondary-dropdown"
                                    data-primary-test-id="${primaryTestId}">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            `;

                primaryTestRow.insertAdjacentHTML('afterend', dropdownHtml);

                // Update unit when secondary test is selected
                document.getElementById(`secondary_test_select_${primaryTestId}`).addEventListener('change',
                    function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const unit = selectedOption.getAttribute('data-unit') || '';
                        document.getElementById(`secondary_unit_${primaryTestId}`).value = unit;
                    });

                // Add event listeners for the dropdown buttons
                document.querySelector(`.add-selected-secondary[data-primary-test-id="${primaryTestId}"]`)
                    .addEventListener('click', function() {
                        const selectedSecondaryId = document.getElementById(
                            `secondary_test_select_${primaryTestId}`).value;
                        const resultValue = document.getElementById(`secondary_result_${primaryTestId}`).value;
                        const unitValue = document.getElementById(`secondary_unit_${primaryTestId}`).value;

                        if (!selectedSecondaryId) {
                            Swal.fire('Error', 'Please select a secondary test', 'error');
                            return;
                        }

                        addSecondaryTestRow(testNumber, primaryTestId, selectedSecondaryId, resultValue,
                            unitValue, secondaryTests);
                        document.getElementById(dropdownId).remove();
                    });

                document.querySelector(`.cancel-secondary-dropdown[data-primary-test-id="${primaryTestId}"]`)
                    .addEventListener('click', function() {
                        document.getElementById(dropdownId).remove();
                    });
            }

            function addSecondaryTestRow(testNumber, primaryTestId, secondaryTestId, resultValue = '', unitValue =
                '', secondaryTests) {
                const secondaryTest = secondaryTests.find(test => test.m17_secondary_test_id.toString() ===
                    secondaryTestId);
                if (!secondaryTest) return;

                const secondaryRowId = `secondary_${primaryTestId}_${secondaryTestId}`;
                const serialNumber = getNextSecondarySerialNumber(testNumber, primaryTestId);

                const secondaryTestHtml = `
            <tr id="${secondaryRowId}" class="secondary-test-row revision-highlight"
                data-test-number="${testNumber}"
                data-primary-test-id="${primaryTestId}"
                data-secondary-test-id="${secondaryTestId}">
                <td>${serialNumber}</td>
                <td>${secondaryTest.m17_name}</td>
                <td class="text-muted text-center">-</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="hidden"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][test_id]"
                            value="${testNumber}">
                        <input type="hidden"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][primary_test_id]"
                            value="${primaryTestId}">
                        <input type="hidden"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][secondary_test_id]"
                            value="${secondaryTestId}">
                        <input type="text"
                            class="form-control form-control-sm border-primary bg-light"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][result]"
                            value="${resultValue}"
                            placeholder="Enter revised result"
                            autocomplete="off" required>
                        <input type="text"
                            class="form-control form-control-sm border-primary bg-light"
                            style="max-width: 80px;"
                            name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][unit]"
                            value="${unitValue}"
                            placeholder="Unit">
                    </div>
                </td>
                <td>
                    <button type="button"
                        class="btn btn-outline-danger btn-sm remove-test-row"
                        data-type="secondary"
                        data-test-number="${testNumber}"
                        data-primary-test-id="${primaryTestId}"
                        data-id="${secondaryTestId}">
                        <em class="icon ni ni-trash"></em>
                    </button>
                    <button type="button"
                        class="btn btn-outline-warning btn-sm add-custom-field"
                        data-test-id="${currentTestId}"
                        data-test-number="${testNumber}"
                        data-primary-test-id="${primaryTestId}"
                        data-secondary-test-id="${secondaryTestId}"
                        data-type="secondary">
                        <em class="icon ni ni-plus"></em> Custom
                    </button>
                </td>
            </tr>
            `;

                const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                primaryTestRow.insertAdjacentHTML('afterend', secondaryTestHtml);

                // Mark this secondary test as used
                if (!usedSecondaryTests.has(primaryTestId)) {
                    usedSecondaryTests.set(primaryTestId, new Set());
                }
                usedSecondaryTests.get(primaryTestId).add(secondaryTestId);
            }

            function addNewCustomField() {
                customFieldCounter++;
                const fieldId = `new_custom_field_${customFieldCounter}`;

                const customFieldHtml = `
            <div class="row g-2 mb-2 align-items-center custom-field-row p-2 rounded" id="${fieldId}">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="new_custom_fields[${fieldId}][test_number]" required>
                        <option value="">Select Test</option>
                        @foreach ($sampleTests as $sampleTest)
                        <option value="{{ $sampleTest->test->m12_test_number }}">
                            {{ $sampleTest->test->m12_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" 
                        name="new_custom_fields[${fieldId}][name]" 
                        placeholder="Field Name" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" 
                        name="new_custom_fields[${fieldId}][value]" 
                        placeholder="Field Value" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm" 
                        name="new_custom_fields[${fieldId}][unit]" 
                        placeholder="Unit">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-new-custom-field" 
                        data-field-id="${fieldId}">
                        <em class="icon ni ni-trash"></em>
                    </button>
                </div>
            </div>
            `;

                document.getElementById('newCustomFieldsContainer').insertAdjacentHTML('beforeend',
                customFieldHtml);

                // Add remove event listener
                document.querySelector(`#${fieldId} .remove-new-custom-field`).addEventListener('click',
            function() {
                    const fieldToRemove = document.getElementById(fieldId);
                    if (fieldToRemove) {
                        fieldToRemove.remove();
                    }
                });
            }

            function addCustomField(testNumber, primaryTestId = null, secondaryTestId = null, type = 'test') {
                customFieldCounter++;
                const fieldId = `custom_field_${customFieldCounter}`;

                let serialNumber = '';
                let insertAfter = null;

                if (type === 'secondary' && secondaryTestId) {
                    const secondaryTestRow = document.querySelector(
                    `[data-secondary-test-id="${secondaryTestId}"]`);
                    if (secondaryTestRow) {
                        const currentSerial = secondaryTestRow.cells[0].textContent;
                        serialNumber = currentSerial + '.C';
                        insertAfter = secondaryTestRow;
                    }
                } else if (type === 'primary' && primaryTestId) {
                    const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                    if (primaryTestRow) {
                        const currentSerial = primaryTestRow.cells[0].textContent;
                        serialNumber = currentSerial + '.C';
                        insertAfter = primaryTestRow;
                    }
                } else {
                    const testRow = document.querySelector(`.test-main-row[data-test-number="${testNumber}"]`);
                    if (testRow) {
                        const currentSerial = testRow.cells[0].textContent;
                        serialNumber = currentSerial + '.C';
                        insertAfter = testRow;
                    }
                }

                let namePrefix = `custom_fields[${testNumber}]`;
                if (primaryTestId) {
                    namePrefix += `[primary_${primaryTestId}]`;
                }
                if (secondaryTestId) {
                    namePrefix += `[secondary_${secondaryTestId}]`;
                }
                namePrefix += `[${fieldId}]`;

                const customFieldHtml = `
            <tr id="${fieldId}" class="custom-field-row revision-highlight" 
                data-test-number="${testNumber}" 
                ${primaryTestId ? `data-primary-test-id="${primaryTestId}"` : ''}
                ${secondaryTestId ? `data-secondary-test-id="${secondaryTestId}"` : ''}>
                <td>
                    <small>${serialNumber}</small>
                </td>
                <td>
                    <input type="text" 
                        class="form-control form-control-sm custom-field-input" 
                        name="${namePrefix}[name]" 
                        placeholder="Custom Field Name" required>
                </td>
                <td class="text-muted text-center">-</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" 
                            class="form-control form-control-sm custom-field-input" 
                            name="${namePrefix}[value]" 
                            placeholder="Enter revised value" required>
                        <input type="text" 
                            class="form-control form-control-sm custom-field-input" 
                            style="max-width: 80px;"
                            name="${namePrefix}[unit]" 
                            placeholder="Unit">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-custom-field" data-field-id="${fieldId}">
                        <em class="icon ni ni-trash"></em>
                    </button>
                </td>
            </tr>
            `;

                if (insertAfter) {
                    insertAfter.insertAdjacentHTML('afterend', customFieldHtml);
                }
            }

            // Helper functions
            function getNextSerialNumber(testNumber) {
                const testRows = document.querySelectorAll(`[data-test-number="${testNumber}"]`);
                const primaryTestRows = Array.from(testRows).filter(row =>
                    row.classList.contains('primary-test-row')
                );
                return `${parseInt(testNumber)}.${primaryTestRows.length + 1}`;
            }

            function getNextSecondarySerialNumber(testNumber, primaryTestId) {
                const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                const primarySerial = primaryTestRow.cells[0].textContent;
                const secondaryRows = document.querySelectorAll(
                    `[data-primary-test-id="${primaryTestId}"].secondary-test-row`);
                return `${primarySerial}.${secondaryRows.length + 1}`;
            }

            function getAvailableSecondaryTests(primaryTestId, secondaryTests) {
                const usedSet = usedSecondaryTests.get(primaryTestId) || new Set();
                return secondaryTests.filter(test =>
                    !usedSet.has(test.m17_secondary_test_id.toString())
                );
            }

            // Form submission validation
            document.getElementById('reviseResultForm').addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('input[required]');
                let allFilled = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        allFilled = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!allFilled) {
                    e.preventDefault();
                    Swal.fire('Error', 'Please fill all required fields marked with previous results.',
                        'error');
                }
            });

        });
    </script>
@endsection
