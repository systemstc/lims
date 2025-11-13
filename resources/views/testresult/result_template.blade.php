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
                                <h4 class="fw-bold text-uppercase mb-0">Test Result Entry</h4>
                                <small class="text-muted">Laboratory Information Management System</small>
                            </div>
                            <div>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">
                                    <em class="icon ni ni-caret-left-fill"></em> Back
                                </a>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('create_test_result') }}" method="POST" enctype="multipart/form-data"
                            id="testResultForm">
                            @csrf

                            @if (isset($sampleTests) && $sampleTests->isNotEmpty())
                                <input type="hidden" name="registration_id"
                                    value="{{ $sampleTests->first()->registration->tr04_reference_id ?? '' }}">
                            @endif

                            <!-- Report Information -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-primary text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase"><em class="icon ni ni-clipboard"></em> Test Report
                                        Details</h6>
                                </div>
                                <div class="card-body px-4 py-3">
                                    <table class="table table-sm table-borderless align-middle mb-0 small w-100">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-muted w-25">Test Report No:</td>
                                                <td class="text-dark fw-semibold w-25">
                                                    {{ optional($sampleTests->first()?->registration)->tr04_reference_id ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted w-25">Date:</td>
                                                <td class="w-25">
                                                    <input type="date"
                                                        class="form-control form-control-sm bg-light border-0 shadow-none"
                                                        name="test_date"
                                                        value="{{ old('test_date', $testDate ?? date('Y-m-d')) }}" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">No. of Samples:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ optional($sampleTests->first()?->registration)->tr04_number_of_samples ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted">Sample Characteristics:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ optional($sampleTests->first()?->registration->labSample)->m14_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Date of Performance of Tests:</td>
                                                <td>
                                                    <input type="date"
                                                        class="form-control form-control-sm bg-light border-0 shadow-none"
                                                        name="performance_date"
                                                        value="{{ old('performance_date', $performanceDate ?? '') }}"
                                                        required>
                                                </td>
                                                <td class="fw-bold text-muted">Date of Allotment of Sample:</td>
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
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-primary text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase"><em class="icon ni ni-layers"></em> Test Results</h6>
                                </div>
                                <div class="">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 10%">Sr. No.</th>
                                                <th>Test Name</th>
                                                <th style="width: 35%">Result / Entry</th>
                                                <th style="width: 15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="testResultsBody">
                                            @foreach ($sampleTests as $key => $sampleTest)
                                                @php
                                                    $test = $sampleTest->test;
                                                    $primaryTests = $test->primaryTests ?? collect();
                                                    $existingTestResults = $existingResults->where(
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
                                                    <td>
                                                        @if ($primaryTests->isEmpty())
                                                            <!-- Input field for main test when no primary tests -->
                                                            <div class="input-group input-group-sm">
                                                                <input type="text"
                                                                    class="form-control form-control-sm bg-light"
                                                                    name="results[{{ $test->m12_test_number }}][test][result]"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') }}"
                                                                    placeholder="Enter result value" autocomplete="off">
                                                                <input type="text"
                                                                    class="form-control form-control-sm bg-light"
                                                                    style="max-width: 80px;"
                                                                    name="results[{{ $test->m12_test_number }}][test][unit]"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.unit', $existingMainTestResult->tr07_unit ?? ($test->m12_unit ?? '')) }}"
                                                                    placeholder="Unit">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][test_id]"
                                                                    value="{{ $test->m12_test_number }}">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][result_id]"
                                                                    value="{{ $existingMainTestResult->tr07_test_result_id ?? '' }}">
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

                                                <!-- Existing Primary Tests from Database -->
                                                @foreach ($existingTestResults->whereNotNull('m16_primary_test_id')->whereNull('m17_secondary_test_id') as $existingPrimaryResult)
                                                    @php
                                                        $primaryTest = $primaryTests
                                                            ->where(
                                                                'm16_primary_test_id',
                                                                $existingPrimaryResult->m16_primary_test_id,
                                                            )
                                                            ->first();
                                                    @endphp
                                                    @if ($primaryTest)
                                                        <!-- Primary Test Row -->
                                                        <tr class="primary-test-row"
                                                            data-test-id="{{ $test->m12_test_id }}"
                                                            data-test-number="{{ $test->m12_test_number }}"
                                                            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
                                                            <td>{{ $key + 1 }}.{{ $loop->index + 1 }}</td>
                                                            <td>
                                                                <strong>{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
                                                                @if ($primaryTest->m16_requirement)
                                                                    <br><small class="text-info">Requirement:
                                                                        {{ $primaryTest->m16_requirement }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="input-group input-group-sm">
                                                                    <input type="hidden"
                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][test_id]"
                                                                        value="{{ $test->m12_test_number }}">
                                                                    <input type="hidden"
                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][primary_test_id]"
                                                                        value="{{ $primaryTest->m16_primary_test_id }}">
                                                                    <input type="hidden"
                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result_id]"
                                                                        value="{{ $existingPrimaryResult->tr07_test_result_id ?? '' }}">
                                                                    <input type="text"
                                                                        class="form-control form-control-sm border-0 bg-light"
                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                        value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result', $existingPrimaryResult->tr07_result ?? '') }}"
                                                                        placeholder="Enter result value"
                                                                        autocomplete="off">
                                                                    <input type="text"
                                                                        class="form-control form-control-sm border-0 bg-light"
                                                                        style="max-width: 80px;"
                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][unit]"
                                                                        value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.unit', $existingPrimaryResult->tr07_unit ?? ($primaryTest->m16_unit ?? '')) }}"
                                                                        placeholder="Unit">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-outline-danger btn-sm remove-test-row"
                                                                    data-type="primary"
                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                    data-id="{{ $primaryTest->m16_primary_test_id }}">
                                                                    <em class="icon ni ni-trash"></em>
                                                                </button>
                                                                @if ($primaryTest->secondaryTests && $primaryTest->secondaryTests->isNotEmpty())
                                                                    <button type="button"
                                                                        class="btn btn-outline-success btn-sm add-secondary-test"
                                                                        data-test-number="{{ $test->m12_test_number }}"
                                                                        data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                        data-secondary-tests='{{ $primaryTest->secondaryTests->toJson() }}'>
                                                                        <em class="icon ni ni-plus"></em> Secondary
                                                                    </button>
                                                                @endif
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm add-custom-field"
                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                    data-type="primary">
                                                                    <em class="icon ni ni-plus"></em> Custom
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <!-- Existing Secondary Tests for this Primary Test -->
                                                        @foreach ($existingTestResults->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->whereNotNull('m17_secondary_test_id') as $existingSecondaryResult)
                                                            @php
                                                                $secondaryTest = $primaryTest->secondaryTests
                                                                    ->where(
                                                                        'm17_secondary_test_id',
                                                                        $existingSecondaryResult->m17_secondary_test_id,
                                                                    )
                                                                    ->first();
                                                            @endphp
                                                            @if ($secondaryTest)
                                                                <tr class="secondary-test-row"
                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                    data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}">
                                                                    <td>{{ $key + 1 }}.{{ $loop->parent->index + 1 }}.{{ $loop->index + 1 }}
                                                                    </td>
                                                                    <td>{{ $secondaryTest->m17_name ?? 'N/A' }}</td>
                                                                    <td>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="hidden"
                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][test_id]"
                                                                                value="{{ $test->m12_test_number }}">
                                                                            <input type="hidden"
                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][primary_test_id]"
                                                                                value="{{ $primaryTest->m16_primary_test_id }}">
                                                                            <input type="hidden"
                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][secondary_test_id]"
                                                                                value="{{ $secondaryTest->m17_secondary_test_id }}">
                                                                            <input type="hidden"
                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result_id]"
                                                                                value="{{ $existingSecondaryResult->tr07_test_result_id ?? '' }}">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm border-0 bg-light"
                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result', $existingSecondaryResult->tr07_result ?? '') }}"
                                                                                placeholder="Enter result value"
                                                                                autocomplete="off">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm border-0 bg-light"
                                                                                style="max-width: 80px;"
                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][unit]"
                                                                                value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.unit', $existingSecondaryResult->tr07_unit ?? ($secondaryTest->m17_unit ?? '')) }}"
                                                                                placeholder="Unit">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button"
                                                                            class="btn btn-outline-danger btn-sm remove-test-row"
                                                                            data-type="secondary"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                            data-id="{{ $secondaryTest->m17_secondary_test_id }}">
                                                                            <em class="icon ni ni-trash"></em>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-outline-primary btn-sm add-custom-field"
                                                                            data-test-id="{{ $test->m12_test_id }}"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                            data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}"
                                                                            data-type="secondary">
                                                                            <em class="icon ni ni-plus"></em> Custom
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Signature Section -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body px-4 py-3">
                                    <div class="d-flex justify-content-between text-center flex-wrap">
                                        <div class="signature-box flex-fill me-3">
                                            <div class="signature-line mb-3"></div>
                                            <strong>Signature of QAO / JQAO</strong>
                                        </div>
                                        <div class="signature-box flex-fill ms-3">
                                            <div class="signature-line mb-3"></div>
                                            <strong>Signature of Technical Manager</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    @if (!isset($existingResults) || $existingResults->isEmpty())
                                        <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                                            <em class="icon ni ni-printer"></em> Print / Save as PDF
                                        </button>
                                    @endif
                                    <div class="btn-group">
                                        @if (optional($existingResults->first())->tr07_result_status != 'SUBMITTED')
                                            <button type="submit" name="action" value="DRAFT"
                                                class="btn btn-outline-primary">
                                                <em class="icon ni ni-file-text"></em> Save as Draft
                                            </button>
                                        @endif
                                        @if (Session::get('role') === 'DEO')
                                            <button type="submit" name="action" value="RESULTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Save & Complete
                                            </button>
                                        @else
                                            <button type="submit" name="action" value="SUBMITTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Save & Complete
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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            font-size: 0.9rem;
            color: #222;
        }

        .table td,
        .table th {
            padding: 0.55rem 0.75rem;
            vertical-align: middle;
        }

        .signature-line {
            height: 60px;
            border-bottom: 1px solid #444;
            margin: 0 auto 10px;
            width: 80%;
        }

        .card-header {
            border-bottom: 1px solid #ddd;
        }

        .custom-field-row {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
        }

        .custom-field-input {
            background-color: #fffdf6 !important;
            border: 1px dashed #ffc107 !important;
        }

        .test-main-row {
            background-color: #e3f2fd !important;
        }

        .primary-test-row {
            background-color: #f3e5f5 !important;
        }

        .secondary-test-row {
            background-color: #fafafa !important;
        }

        .btn-group-sm {
            display: flex;
            gap: 0.25rem;
        }

        .secondary-dropdown {
            background-color: #f8f9fa;
            border-left: 3px solid #6c757d;
        }

        @media print {

            .btn,
            .nk-header,
            .nk-footer,
            .card:last-child {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .card {
                border: 1px solid #333 !important;
                box-shadow: none !important;
                page-break-inside: auto !important;
            }

            table {
                border-collapse: collapse !important;
                width: 100%;
                page-break-inside: auto !important;
                page-break-before: auto !important;
                page-break-after: auto !important;
            }

            thead {
                display: table-header-group !important;
            }

            tfoot {
                display: table-footer-group !important;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            input::placeholder,
            textarea::placeholder {
                color: transparent !important;
            }

            input,
            textarea {
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentTestNumber = null;
            let currentTestId = null;
            let customFieldCounter = {{ $existingCustomFields->count() }};
            let usedPrimaryTests = new Map();
            let usedSecondaryTests = new Map();

            // Initialize used tests from existing results
            @foreach ($existingResults as $result)
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

            // Load existing custom fields
            @foreach ($existingCustomFields as $customField)
                addExistingCustomField(
                    'existing_custom_field_{{ $customField->tr08_custom_field_id }}',
                    '{{ $customField->m12_test_number }}',
                    '{{ $customField->m16_primary_test_id ?? '' }}',
                    '{{ $customField->m17_secondary_test_id ?? '' }}',
                    '{{ !empty($customField->m17_secondary_test_id) ? 'secondary' : (!empty($customField->m16_primary_test_id) ? 'primary' : 'test') }}',
                    '{{ addslashes($customField->tr08_field_name) }}',
                    '{{ addslashes($customField->tr08_field_value) }}',
                    '{{ addslashes($customField->tr08_field_unit ?? '') }}',
                    '{{ $customField->tr08_custom_field_id }}'
                );
            @endforeach

            // Add Primary Test Button Click
            document.querySelectorAll('.add-primary-test').forEach(button => {
                button.addEventListener('click', function() {
                    currentTestNumber = this.getAttribute('data-test-number');
                    currentTestId = this.getAttribute('data-test-id');
                    const primaryTestsData = JSON.parse(this.getAttribute('data-primary-tests'));

                    showPrimaryTestModal(primaryTestsData);
                });
            });

            // Add Custom Field for Main Test
            document.querySelectorAll('.add-custom-field[data-type="test"]').forEach(button => {
                button.addEventListener('click', function() {
                    const testNumber = this.getAttribute('data-test-number');
                    addCustomField(testNumber, null, null, 'test');
                });
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
                            <td colspan="3" class="text-center text-muted">
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

                $('#primaryTestModal').modal('show');
            }

            // Select Primary Test
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('select-primary-test')) {
                    const primaryTestId = e.target.getAttribute('data-primary-test-id');
                    const primaryTestName = e.target.getAttribute('data-primary-test-name');
                    const primaryTestUnit = e.target.getAttribute('data-primary-test-unit');
                    const hasSecondary = e.target.getAttribute('data-has-secondary') === 'true';
                    const secondaryTests = JSON.parse(e.target.getAttribute('data-secondary-tests') ||
                    '[]');

                    addPrimaryTestRow(currentTestNumber, currentTestId, primaryTestId, primaryTestName,
                        primaryTestUnit, hasSecondary, secondaryTests);
                    $('#primaryTestModal').modal('hide');
                }
            });

            function addPrimaryTestRow(testNumber, testId, primaryTestId, primaryTestName, primaryTestUnit,
                hasSecondaryTests, secondaryTests) {
                const rowId = `primary_test_${testNumber}_${primaryTestId}`;

                const primaryTestHtml = `
                <tr id="${rowId}" class="primary-test-row"
                    data-test-id="${testId}"
                    data-test-number="${testNumber}"
                    data-primary-test-id="${primaryTestId}">
                    <td>${getNextSerialNumber(testNumber)}</td>
                    <td>
                        <strong>${primaryTestName}</strong>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][test_id]"
                                value="${testNumber}">
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][primary_test_id]"
                                value="${primaryTestId}">
                            <input type="text"
                                class="form-control form-control-sm border-0 bg-light"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][result]"
                                placeholder="Enter result value"
                                autocomplete="off">
                            <input type="text"
                                class="form-control form-control-sm border-0 bg-light"
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

                // Add event listeners for the new buttons
                addRowEventListeners(rowId);
            }

            function addSecondaryTestDropdown(testNumber, primaryTestId, secondaryTests) {
                const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                const dropdownId = `secondary_dropdown_${primaryTestId}`;

                // Get available secondary tests
                const availableSecondaryTests = getAvailableSecondaryTests(primaryTestId, secondaryTests);

                if (availableSecondaryTests.length === 0) {
                    Swal.fire('Info', 'No more secondary tests available for this primary test.', 'info');
                    return;
                }

                const dropdownHtml = `
                <tr id="${dropdownId}" class="secondary-dropdown">
                    <td colspan="4">
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
                <tr id="${secondaryRowId}" class="secondary-test-row"
                    data-test-number="${testNumber}"
                    data-primary-test-id="${primaryTestId}"
                    data-secondary-test-id="${secondaryTestId}">
                    <td>${serialNumber}</td>
                    <td>${secondaryTest.m17_name}</td>
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
                                class="form-control form-control-sm border-0 bg-light"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][result]"
                                value="${resultValue}"
                                placeholder="Enter result value"
                                autocomplete="off">
                            <input type="text"
                                class="form-control form-control-sm border-0 bg-light"
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

                // Add event listeners
                addRowEventListeners(secondaryRowId);
            }

            // Custom Fields Functionality
            function addExistingCustomField(fieldId, testNumber, primaryTestId, secondaryTestId, type, fieldName,
                fieldValue, fieldUnit, customFieldId) {
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
                if (primaryTestId && primaryTestId !== 'NULL' && primaryTestId !== '') {
                    namePrefix += `[primary_${primaryTestId}]`;
                }
                if (secondaryTestId && secondaryTestId !== 'NULL' && secondaryTestId !== '') {
                    namePrefix += `[secondary_${secondaryTestId}]`;
                }
                namePrefix += `[${fieldId}]`;

                const customFieldHtml = `
                <tr id="${fieldId}" class="custom-field-row" 
                    data-test-number="${testNumber}" 
                    ${primaryTestId && primaryTestId !== 'NULL' && primaryTestId !== '' ? `data-primary-test-id="${primaryTestId}"` : ''}
                    ${secondaryTestId && secondaryTestId !== 'NULL' && secondaryTestId !== '' ? `data-secondary-test-id="${secondaryTestId}"` : ''}>
                    <td>
                        <small>${serialNumber}</small>
                    </td>
                    <td>
                        <input type="text" 
                            class="form-control form-control-sm custom-field-input" 
                            name="${namePrefix}[name]" 
                            value="${fieldName}" 
                            placeholder="Custom Field Name" required>
                        <input type="hidden" 
                            name="${namePrefix}[custom_field_id]" 
                            value="${customFieldId}">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" 
                                class="form-control form-control-sm custom-field-input" 
                                name="${namePrefix}[value]" 
                                value="${fieldValue}"
                                placeholder="Enter value" required>
                            <input type="text" 
                                class="form-control form-control-sm custom-field-input" 
                                style="max-width: 80px;"
                                name="${namePrefix}[unit]" 
                                value="${fieldUnit || ''}"
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

                    const removeButton = document.querySelector(`#${fieldId} .remove-custom-field`);
                    if (removeButton) {
                        removeButton.addEventListener('click', function() {
                            const fieldToRemove = document.getElementById(fieldId);
                            if (fieldToRemove) {
                                fieldToRemove.remove();
                            }
                        });
                    }
                }
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
                <tr id="${fieldId}" class="custom-field-row" 
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
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" 
                                class="form-control form-control-sm custom-field-input" 
                                name="${namePrefix}[value]" 
                                placeholder="Enter value" required>
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

                    const removeButton = document.querySelector(`#${fieldId} .remove-custom-field`);
                    if (removeButton) {
                        removeButton.addEventListener('click', function() {
                            const fieldToRemove = document.getElementById(fieldId);
                            if (fieldToRemove) {
                                fieldToRemove.remove();
                            }
                        });
                    }
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

            function addRowEventListeners(rowId) {
                const row = document.getElementById(rowId);
                if (!row) return;

                // Add secondary test button
                const addSecondaryBtn = row.querySelector('.add-secondary-test');
                if (addSecondaryBtn) {
                    addSecondaryBtn.addEventListener('click', function() {
                        const testNumber = this.getAttribute('data-test-number');
                        const primaryTestId = this.getAttribute('data-primary-test-id');
                        const secondaryTests = JSON.parse(this.getAttribute('data-secondary-tests') ||
                        '[]');
                        addSecondaryTestDropdown(testNumber, primaryTestId, secondaryTests);
                    });
                }

                // Remove button
                const removeBtn = row.querySelector('.remove-test-row');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        const type = this.getAttribute('data-type');
                        const id = this.getAttribute('data-id');
                        const testNumber = this.getAttribute('data-test-number');
                        const primaryTestId = this.getAttribute('data-primary-test-id');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'This test will be removed permanently.',
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
                                Swal.fire('Removed!', 'The test has been deleted.', 'success');
                            }
                        });
                    });
                }

                // Custom field button
                const customFieldBtn = row.querySelector('.add-custom-field');
                if (customFieldBtn) {
                    customFieldBtn.addEventListener('click', function() {
                        const testId = this.getAttribute('data-test-id');
                        const testNumber = this.getAttribute('data-test-number');
                        const primaryTestId = this.getAttribute('data-primary-test-id');
                        const secondaryTestId = this.getAttribute('data-secondary-test-id');
                        const type = this.getAttribute('data-type');

                        addCustomField(testNumber, primaryTestId, secondaryTestId, type);
                    });
                }
            }

            // Remove custom field functionality
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

            // Initialize event listeners for existing rows
            document.querySelectorAll('.primary-test-row, .secondary-test-row').forEach(row => {
                addRowEventListeners(row.id);
            });

        });
    </script>
@endsection
