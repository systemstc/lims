@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
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

                            @if (Session::has('message'))
                                <div class="alert alert-{{ Session::get('type') === 'error' ? 'danger' : (Session::get('type') === 'warning' ? 'warning' : 'success') }} alert-dismissible fade show shadow-sm mb-4" role="alert">
                                    <strong>{{ ucfirst(Session::get('type')) }}!</strong> {{ Session::get('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            @endif

                            <input type="hidden" name="registration_id" value="{{ $sample->tr04_reference_id }}">
                            <input type="hidden" name="action" id="submitAction" value="SUBMITTED">

                            @if ($errors->any())
                                <div class="alert alert-danger alert-icon border-danger shadow-sm mb-4">
                                    <em class="icon ni ni-cross-circle"></em>
                                    <strong>Submission Failed!</strong> Please check the following errors:
                                    <ul class="mt-2 mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

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
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold text-uppercase mb-0 text-danger">
                                        <em class="icon ni ni-layers"></em> Revised Test Results & Manuscripts
                                    </h5>
                                </div>
                                <div id="testResultsContainer">
                                    @foreach ($sampleTests as $key => $sampleTest)
                                        @php
                                            $test = $sampleTest->test;
                                            $primaryTests = $test->primaryTests ?? collect();
                                            $existingTestResults = $rejectedResults->filter(function($r) use ($test) {
                                                return (string)$r->m12_test_number === (string)$test->m12_test_number;
                                            });
                                            $existingMainTestResult = $existingTestResults
                                                ->whereNull('m16_primary_test_id')
                                                ->whereNull('m17_secondary_test_id')
                                                ->first();

                                            $manuscriptContent = $existingTestResults->whereNotNull('tr07_manuscript_content')->first()->tr07_manuscript_content 
                                                ?? $existingTestResults->pluck('tr07_manuscript_content')->filter()->first()
                                                ?? '';
                                            
                                            $currentTestCustomFields = $rejectedCustomFields->filter(function($f) use ($test) {
                                                return (string)$f->m12_test_number === (string)$test->m12_test_number;
                                            });
                                        @endphp

                                        <div class="nk-block nk-block-lg card border-danger shadow-sm mb-4 test-card"
                                            data-main-sr="{{ $key + 1 }}">
                                            <div class="card-header bg-light border-bottom">
                                                <h5 class="title nk-block-title mb-0 text-dark">
                                                    {{ $key + 1 }}. {{ $test->m12_name ?? 'N/A' }}
                                                    <span class="text-muted fw-normal fs-6">
                                                        @if ($sampleTest->standard)
                                                            ({{ $sampleTest->standard->m15_method ?? 'N/A' }})
                                                        @else
                                                            (No standard for this test)
                                                        @endif
                                                    </span>
                                                </h5>
                                                <small class="text-muted d-block mt-1">Write your manuscript part and calculation below:</small>
                                            </div>
                                            <div class="card-inner p-2">
                                                <!-- Summernote Editor -->
                                                <div class="mb-4">
                                                    <textarea class="summernote-basic form-control" name="test_calculation[{{ $test->m12_test_number }}]">{!! old('test_calculation.' . $test->m12_test_number, $manuscriptContent) !!}</textarea>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                    <h6 class="title mb-0 text-danger">Revised Output <small class="text-muted fw-normal">(Previous rejected results shown in red)</small></h6>
                                                </div>

                                                <table class="table table-bordered table-sm align-middle mb-0 w-100" id="results_table_{{ $test->m12_test_number }}">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 10%">Sr. No.</th>
                                                            <th>Test Name</th>
                                                            <th style="width: 20%">Previous Result</th>
                                                            <th style="width: 35%">Revised Result / Entry</th>
                                                            <th style="width: 15%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="test-results-body">
                                                        <!-- Main Test Row -->
                                                        <tr class="bg-light fw-bold test-main-row"
                                                            data-test-id="{{ $test->m12_test_id }}"
                                                            data-test-number="{{ $test->m12_test_number }}">
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>
                                                                <strong>{{ $test->m12_name ?? 'N/A' }}</strong>
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
                                                                        <button type="button" class="btn btn-outline-light btn-sm open-raw-entry" data-target-name="results[{{ $test->m12_test_number }}][test][result]" data-label="{{ $test->m12_name ?? 'Final Result' }}"><em class="icon ni ni-calc"></em></button>
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
                                                        @foreach ($primaryTests as $pKey => $primaryTest)
                                                            @php
                                                                $existingPrimaryResult = $existingTestResults
                                                                    ->filter(fn($r) => (string)$r->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && empty($r->m17_secondary_test_id))
                                                                    ->first();

                                                                $hasResultsOrChildren = $existingPrimaryResult || 
                                                                    $existingTestResults->filter(fn($r) => (string)$r->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && !empty($r->m17_secondary_test_id))->isNotEmpty() ||
                                                                    $currentTestCustomFields->filter(fn($f) => (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id)->isNotEmpty();
                                                            @endphp
                                                            
                                                            @if ($hasResultsOrChildren)
                                                                @include('testresult.partials.primary_test_row', [
                                                                    'test' => $test,
                                                                    'primaryTest' => $primaryTest,
                                                                    'existingPrimaryResult' => $existingPrimaryResult,
                                                                    'key' => $key,
                                                                    'pKey' => $pKey,
                                                                    'existingResults' => $existingTestResults,
                                                                    'rejectedCustomFields' => $currentTestCustomFields,
                                                                    'isRevision' => true,
                                                                ])
                                                            @endif
                                                        @endforeach

                                                        <!-- Custom Fields for Main Test -->
                                                        @foreach ($currentTestCustomFields->whereNull('m16_primary_test_id')->whereNull('m17_secondary_test_id') as $customField)
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
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
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
                                        <button type="submit" onclick="setAction('DRAFT')"
                                            class="btn btn-outline-primary">
                                            <em class="icon ni ni-file-text"></em> Save as Draft
                                        </button>
                                        @if (Session::get('role') === 'DEO')
                                            <button type="submit" onclick="setAction('RESULTED')"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Submit Revisions
                                            </button>
                                        @else
                                            <button type="submit" onclick="setAction('SUBMITTED')"
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

    <!-- Modals -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Test Result Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('testresult_upload', $sample->tr04_reference_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Document</label>
                            <input type="file" name="result_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="primaryTestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Primary Test to Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Test Name</th><th>Unit</th><th>Action</th></tr>
                            </thead>
                            <tbody id="primaryTestList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rawEntryModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Result Calculation (Formula)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="formulaHeader" class="mb-3"></div>
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-auto"><label class="form-label mb-0">Readings:</label></div>
                        <div class="col-auto" style="width: 80px;">
                            <input type="number" id="numberOfReadings" class="form-control form-control-sm" value="1" min="1">
                        </div>
                    </div>
                    <div id="readingsContainer" class="mb-3" style="max-height: 300px; overflow-y: auto;"></div>
                    <div class="alert alert-info py-2 d-flex justify-content-between align-items-center">
                        <strong>Average: <span id="calculatedResult">0.000</span></strong>
                        <button type="button" class="btn btn-primary btn-sm" id="applyCalculatedResult">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            console.log("Revision Script Initialized");

            window.setAction = function(action) {
                $('#submitAction').val(action);
            };

            // Summernote
            $('.summernote-basic').summernote({
                height: 150
            });



            // Event Delegation
            $(document).on('click', '.add-primary-test', function() {
                const testNumber = $(this).data('test-number');
                let primaryTests = $(this).data('primary-tests');
                if (typeof primaryTests === 'string') primaryTests = JSON.parse(primaryTests);
                if (!Array.isArray(primaryTests)) primaryTests = Object.values(primaryTests || {});
                
                const tableBody = $(`#results_table_${testNumber} tbody`);
                let html = '';
                primaryTests.forEach(pt => {
                    if (tableBody.find(`.primary-test-row[data-primary-test-id="${pt.m16_primary_test_id}"]`).length === 0) {
                        html += `<tr><td>${pt.m16_name}</td><td>${pt.m16_unit || ''}</td>
                            <td><button type="button" class="btn btn-primary btn-xs select-pt-btn" data-pt='${JSON.stringify(pt)}' data-tn="${testNumber}">Select</button></td></tr>`;
                    }
                });
                $('#primaryTestList').html(html || '<tr><td colspan="3" class="text-center">No more tests to add</td></tr>');
                $('#primaryTestModal').modal('show');
            });

            $(document).on('click', '.select-pt-btn', function() {
                const pt = $(this).data('pt');
                const tn = $(this).data('tn');
                const tableBody = $(`#results_table_${tn} tbody`);
                const row = `<tr class="primary-test-row" data-primary-test-id="${pt.m16_primary_test_id}" data-tn="${tn}">
                    <td class="serial-col"></td>
                    <td class="ps-3 fw-bold">${pt.m16_name}</td>
                    <td>
                        <div class="input-group input-group-sm ${pt.secondaryTests && pt.secondaryTests.length > 0 ? 'd-none' : ''}">
                            <input type="text" class="form-control web-sync-val" data-sync="print_res_p_${pt.m16_primary_test_id}" name="results[${tn}][primary_tests][${pt.m16_primary_test_id}][result]" placeholder="Result">
                            <input type="text" class="form-control web-sync-val" data-sync="print_unt_p_${pt.m16_primary_test_id}" name="results[${tn}][primary_tests][${pt.m16_primary_test_id}][unit]" value="${pt.m16_unit || ''}" style="max-width:70px;">
                        </div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="primary" data-id="${pt.m16_primary_test_id}"><em class="icon ni ni-trash"></em></button>
                            ${pt.secondaryTests && pt.secondaryTests.length > 0 ? `<button type="button" class="btn btn-outline-success btn-sm add-secondary-test" data-test-number="${tn}" data-primary-test-id="${pt.m16_primary_test_id}" data-secondary-tests='${JSON.stringify(pt.secondaryTests)}'>+ Sec</button>` : ''}
                            <button type="button" class="btn btn-outline-warning btn-sm add-custom-field" data-test-number="${tn}" data-primary-test-id="${pt.m16_primary_test_id}" data-type="primary">+ C</button>
                        </div>
                    </td>
                </tr>`;
                tableBody.append(row);
                updateSerials(tn);
                $('#primaryTestModal').modal('hide');
            });

            $(document).on('click', '.add-secondary-test', function() {
                const tn = $(this).data('test-number');
                const pid = $(this).data('primary-test-id');
                let sts = $(this).data('secondary-tests');
                if (typeof sts === 'string') sts = JSON.parse(sts);
                if (!Array.isArray(sts)) sts = Object.values(sts || {});

                const tableBody = $(`#results_table_${tn} tbody`);
                let html = '';
                sts.forEach(st => {
                    if (tableBody.find(`.secondary-test-row[data-secondary-test-id="${st.m17_secondary_test_id}"]`).length === 0) {
                        html += `<tr><td>${st.m17_name}</td><td>${st.m17_unit || ''}</td>
                            <td><button type="button" class="btn btn-primary btn-xs select-st-btn" data-st='${JSON.stringify(st)}' data-tn="${tn}" data-pid="${pid}">Select</button></td></tr>`;
                    }
                });
                $('#primaryTestList').html(html || '<tr><td colspan="3" class="text-center">No more secondary tests</td></tr>');
                $('#primaryTestModal').modal('show');
            });

            $(document).on('click', '.select-st-btn', function() {
                const st = $(this).data('st');
                const tn = $(this).data('tn');
                const pid = $(this).data('pid');
                const tableBody = $(`#results_table_${tn} tbody`);
                const row = `<tr class="secondary-test-row" data-secondary-test-id="${st.m17_secondary_test_id}" data-primary-test-id="${pid}">
                    <td class="serial-col"></td>
                    <td class="ps-4 text-muted">- ${st.m17_name}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="results[${tn}][primary_tests][${pid}][secondary_tests][${st.m17_secondary_test_id}][result]" placeholder="Result">
                            <input type="text" class="form-control" name="results[${tn}][primary_tests][${pid}][secondary_tests][${st.m17_secondary_test_id}][unit]" value="${st.m17_unit || ''}" style="max-width:70px;">
                        </div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="secondary" data-id="${st.m17_secondary_test_id}"><em class="icon ni ni-trash"></em></button>
                            <button type="button" class="btn btn-outline-warning btn-sm add-custom-field" data-test-number="${tn}" data-primary-test-id="${pid}" data-secondary-test-id="${st.m17_secondary_test_id}" data-type="secondary">+ C</button>
                        </div>
                    </td>
                </tr>`;
                $(`.primary-test-row[data-primary-test-id="${pid}"]`).after(row);
                $(`.primary-test-row[data-primary-test-id="${pid}"] .input-group`).addClass('d-none');
                updateSerials(tn);
                $('#primaryTestModal').modal('hide');
            });

            // Combined Custom Field Listener
            $(document).on('click', '.add-custom-field, #addNewCustomField', function() {
                console.log("Add Custom Field Clicked");
                const isNewBtn = $(this).attr('id') === 'addNewCustomField';
                const type = $(this).data('type') || (isNewBtn ? 'new' : 'test');
                const tn = $(this).data('test-number') || '';
                const pid = $(this).data('primary-test-id');
                const sid = $(this).data('secondary-test-id');
                
                Swal.fire({ 
                    title: 'Enter Field Name', 
                    input: 'text', 
                    inputPlaceholder: 'e.g., pH Value',
                    showCancelButton: true 
                }).then(res => {
                    if (res.isConfirmed && res.value) {
                        const name = res.value;
                        const ts = Date.now();
                        let prefix = `custom_fields[${tn}]`;
                        
                        if (type === 'primary') { prefix += `[primary_${pid}]`; }
                        else if (type === 'secondary') { prefix += `[primary_${pid}][secondary_${sid}]`; }
                        
                        const row = `<tr class="custom-field-row">
                            <td class="serial-col"></td><td class="ps-4 small text-muted">${name}</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="hidden" name="${prefix}[new_${ts}][name]" value="${name}">
                                    <input type="text" class="form-control" name="${prefix}[new_${ts}][value]" placeholder="Value">
                                    <input type="text" class="form-control" name="${prefix}[new_${ts}][unit]" style="max-width:60px;" placeholder="Unit">
                                </div>
                            </td>
                            <td><button type="button" class="btn btn-link btn-sm text-danger remove-test-row" data-type="custom"><em class="icon ni ni-trash"></em></button></td>
                        </tr>`;
                        
                        if (isNewBtn) {
                           $('#newCustomFieldsContainer').append(`<div class="mb-2 p-2 border rounded bg-light"><strong>${name}</strong>${row}</div>`);
                        } else {
                           $(this).closest('tr').after(row);
                        }
                        
                        if (tn) updateSerials(tn);
                    }
                });
            });

            $(document).on('click', '.remove-test-row, .remove-custom-field', function() {
                const row = $(this).closest('tr');
                const type = $(this).data('type');
                const id = $(this).data('id');
                const sync = $(this).data('sync') || $(this).data('field-id');
                const tn = row.data('tn') || row.closest('table').attr('id')?.replace('results_table_', '');

                Swal.fire({ title: 'Are you sure?', text: "Remove this item?", icon: 'warning', showCancelButton: true }).then(res => {
                    if (res.isConfirmed) {
                        if (type === 'primary') { 
                            $(`.secondary-test-row[data-primary-test-id="${id}"]`).remove();
                        }
                        if (type === 'secondary') {
                            const pId = row.data('primary-test-id');
                            const tbody = row.closest('tbody');
                            if (tbody.find(`.secondary-test-row[data-primary-test-id="${pId}"]`).length <= 1) {
                                tbody.find(`.primary-test-row[data-primary-test-id="${pId}"] .input-group`).removeClass('d-none');
                            }
                        }
                        row.remove();
                        if (tn) updateSerials(tn);
                    }
                });
            });

            function updateSerials(tn) {
                const table = $(`#results_table_${tn}`);
                if (!table.length) return;
                const mainSr = table.closest('.test-card').data('main-sr');
                let count = 0;
                table.find('tbody tr.primary-test-row').each(function() {
                    count++; 
                    $(this).find('.serial-col').text(`${mainSr}.${count}`);
                });
            }

            // Formula Logic
            let activeInput = null;
            $(document).on('click', '.open-raw-entry', function() {
                activeInput = $(`input[name="${$(this).data('target-name')}"]`);
                $('#formulaHeader').text($(this).data('label'));
                $('#numberOfReadings').val(1).trigger('input');
                $('#rawEntryModal').modal('show');
            });

            $('#numberOfReadings').on('input', function() {
                const n = $(this).val();
                let h = '';
                for(let i=1; i<=n; i++) h += `<div class="row mb-1"><div class="col-2">#${i}</div><div class="col"><input type="number" class="form-control form-control-sm r-val"></div></div>`;
                $('#readingsContainer').html(h);
            });

            $(document).on('input', '.r-val', function() {
                let s = 0, c = 0;
                $('.r-val').each(function() { if($(this).val()) { s += parseFloat($(this).val()); c++; } });
                $('#calculatedResult').text(c ? (s/c).toFixed(3) : '0.000');
            });

            $('#applyCalculatedResult').click(function() {
                if (activeInput) activeInput.val($('#calculatedResult').text()).trigger('input');
                $('#rawEntryModal').modal('hide');
            });

            // Initial setup
            $('[id^="results_table_"]').each(function() {
                const tn = this.id.replace('results_table_', '');
                updateSerials(tn);
            });
        });
    </script>
@endsection
