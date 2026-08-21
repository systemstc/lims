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
                            <input type="hidden" name="action" id="formAction" value="">

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
                                                        class="form-control form-control-sm bg-light border-0 shadow-none @error('test_date') is-invalid @enderror"
                                                        name="test_date"
                                                        min="{{ date('Y-m-d', strtotime('-15 days')) }}"
                                                        max="{{ date('Y-m-d') }}"
                                                        value="{{ old('test_date', $testDate ?? date('Y-m-d')) }}" required>
                                                    @error('test_date')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
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
                                                        class="form-control form-control-sm bg-light border-0 shadow-none @error('performance_date') is-invalid @enderror"
                                                        name="performance_date"
                                                        min="{{ date('Y-m-d', strtotime('-15 days')) }}"
                                                        max="{{ date('Y-m-d') }}"
                                                        value="{{ old('performance_date', $performanceDate ?? date('Y-m-d')) }}"
                                                        required>
                                                    @error('performance_date')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
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
                                <div class="card border-0 shadow-sm mb-4">
                                    <div
                                        class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center rounded-top">
                                        <h6 class="mb-0 text-uppercase"><em class="icon ni ni-layers"></em> Test Results
                                        </h6>

                                        @php
                                            $manuscriptFile = $sampleTests->first()?->registration?->tr04_manuscript;
                                            $refId = $sampleTests->first()?->registration?->tr04_reference_id;
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($manuscriptFile)
                                                <button type="button" class="btn btn-success btn-sm fw-bold"
                                                    data-bs-toggle="modal" data-bs-target="#pdfModal">
                                                    <em class="icon ni ni-eye me-1"></em> View Manuscript
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold"
                                                    data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                                    <em class="icon ni ni-upload me-1"></em> Upload New Copy
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm fw-bold"
                                                    data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                                    <em class="icon ni ni-upload me-1"></em> Upload Manuscript
                                                </button>
                                            @endif
                                        </div>
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
                                                                    <input type="hidden"
                                                                        name="results[{{ $test->m12_test_number }}][test][test_id]"
                                                                        value="{{ $test->m12_test_number }}">
                                                                    <input type="hidden"
                                                                        name="results[{{ $test->m12_test_number }}][test][result]"
                                                                        class="wysiwyg-hidden-val"
                                                                        value="{{ old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') }}">
                                                                    <div class="form-control form-control-sm bg-light wysiwyg-editor-input @error('results.' . $test->m12_test_number . '.test.result') is-invalid @enderror"
                                                                        contenteditable="true"
                                                                        data-placeholder="Enter result value">{!! old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') !!}</div>
                                                                    <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                    <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                    <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                    @if ($test->m23_formula_id)
                                                                        <button type="button"
                                                                            class="btn btn-outline-light btn-sm open-raw-entry"
                                                                            data-target-name="results[{{ $test->m12_test_number }}][test][result]"
                                                                            data-label="{{ $test->m12_name ?? 'Final Result' }}"
                                                                            title="Formula / Raw Entry">
                                                                            <em class="icon ni ni-calc"></em>
                                                                        </button>
                                                                    @endif
                                                                    <input type="text"
                                                                        class="form-control form-control-sm bg-light"
                                                                        style="max-width: 80px;"
                                                                        name="results[{{ $test->m12_test_number }}][test][unit]"
                                                                        value="{{ old('results.' . $test->m12_test_number . '.test.unit', $existingMainTestResult->tr07_unit ?? ($test->m12_unit ?? '')) }}"
                                                                        placeholder="Unit">
                                                                    <input type="hidden"
                                                                        name="results[{{ $test->m12_test_number }}][test][result_id]"
                                                                        value="{{ $existingMainTestResult->tr07_test_result_id ?? '' }}">
                                                                </div>
                                                                @error('results.' . $test->m12_test_number . '.test.result')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
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
                                                                        @php
                                                                            $testNameLower = strtolower($test->m12_name ?? '');
                                                                            $isAzoTest = str_contains($testNameLower, 'banned amines') || str_contains($testNameLower, 'aryl amine') || str_contains($testNameLower, 'azo');
                                                                            $primaryDefaultVal = old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result', $existingPrimaryResult->tr07_result ?? ($isAzoTest ? 'Not Detected' : ''));
                                                                        @endphp
                                                                        <input type="hidden"
                                                                            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                            class="wysiwyg-hidden-val"
                                                                            value="{{ $primaryDefaultVal }}">
                                                                        <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input @error('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result') is-invalid @enderror"
                                                                            contenteditable="true"
                                                                            data-placeholder="Enter result value">{!! $primaryDefaultVal !!}</div>
                                                                        <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                        <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                        <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                        @if ($primaryTest->m23_formula_id)
                                                                            <button type="button"
                                                                                class="btn btn-outline-light btn-sm open-raw-entry"
                                                                                data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                                data-label="{{ $primaryTest->m16_name ?? 'Primary Result' }}"
                                                                                title="Formula / Raw Entry">
                                                                                <em class="icon ni ni-calc"></em>
                                                                            </button>
                                                                        @endif
                                                                        <input type="text"
                                                                            class="form-control form-control-sm border-0 bg-light"
                                                                            style="max-width: 80px;"
                                                                            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][unit]"
                                                                            value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.unit', $existingPrimaryResult->tr07_unit ?? ($primaryTest->m16_unit ?? '')) }}"
                                                                            placeholder="Unit">
                                                                    </div>
                                                                    @error('results.' . $test->m12_test_number .
                                                                        '.primary_tests.' . $primaryTest->m16_primary_test_id .
                                                                        '.result')
                                                                        <span
                                                                            class="text-danger small">{{ $message }}</span>
                                                                    @enderror
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
                                                                            data-test-id="{{ $test->m12_test_id }}"
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
                                                                                @php
                                                                                    $testNameLower = strtolower($test->m12_name ?? '');
                                                                                    $isAzoTest = str_contains($testNameLower, 'banned amines') || str_contains($testNameLower, 'aryl amine') || str_contains($testNameLower, 'azo');
                                                                                    $secondaryDefaultVal = old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result', $existingSecondaryResult->tr07_result ?? ($isAzoTest ? 'Not Detected' : ''));
                                                                                @endphp
                                                                                <input type="hidden"
                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                    class="wysiwyg-hidden-val"
                                                                                    value="{{ $secondaryDefaultVal }}">
                                                                                <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input @error('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result') is-invalid @enderror"
                                                                                    contenteditable="true"
                                                                                    data-placeholder="Enter result value">{!! $secondaryDefaultVal !!}</div>
                                                                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                                <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                                @if ($secondaryTest->m23_formula_id)
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-light btn-sm open-raw-entry"
                                                                                        data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                        data-label="{{ $secondaryTest->m17_name ?? 'Secondary Result' }}"
                                                                                        title="Formula / Raw Entry">
                                                                                        <em class="icon ni ni-calc"></em>
                                                                                    </button>
                                                                                @endif
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm border-0 bg-light"
                                                                                    style="max-width: 80px;"
                                                                                    value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.unit', $existingSecondaryResult->tr07_unit ?? ($secondaryTest->m17_unit ?? '')) }}"
                                                                                    placeholder="Unit">
                                                                            </div>
                                                                            @error('results.' . $test->m12_test_number .
                                                                                '.primary_tests.' .
                                                                                $primaryTest->m16_primary_test_id .
                                                                                '.secondary_tests.' .
                                                                                $secondaryTest->m17_secondary_test_id .
                                                                                '.result')
                                                                                <span
                                                                                    class="text-danger small">{{ $message }}</span>
                                                                            @enderror
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

                                 @error('action')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <!-- Action Buttons -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-end align-items-center">
                                        <div class="btn-group">
                                            @if (optional($existingResults->first())->tr07_result_status != 'SUBMITTED')
                                                <button type="button" onclick="submitForm('DRAFT')"
                                                    class="btn btn-outline-primary">
                                                    <em class="icon ni ni-file-text"></em> Save as Draft
                                                </button>
                                            @endif
                                            @if (Session::get('role') === 'DEO')
                                                <button type="button" onclick="submitForm('RESULTED')"
                                                    class="btn btn-primary">
                                                    <em class="icon ni ni-check-circle"></em> Save & Complete
                                                </button>
                                            @else
                                                <button type="button" onclick="submitForm('SUBMITTED')"
                                                    class="btn btn-primary">
                                                    <em class="icon ni ni-check-circle"></em> Save & Complete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function submitForm(actionValue) {
                                        const form = document.getElementById('testResultForm');
                                        
                                        // Sync all WYSIWYG editors to hidden inputs first
                                        $('.wysiwyg-editor-input').each(function() {
                                            const $hidden = $(this).closest('.input-group').find('.wysiwyg-hidden-val');
                                            if ($hidden.length) {
                                                $hidden.val(this.innerHTML);
                                            }
                                        });

                                        const resultInputs = form.querySelectorAll('.wysiwyg-hidden-val, input[name$="[value]"]');
                                        
                                        let isValid = true;
                                        let firstInvalid = null;

                                        resultInputs.forEach(input => {
                                            const inputGroup = input.closest('.input-group');
                                            if (inputGroup && inputGroup.classList.contains('d-none')) return;
                                            if (input.closest('.d-none')) return;
                                            
                                            const wysiwygDiv = inputGroup ? inputGroup.querySelector('.wysiwyg-editor-input') : null;
                                            const textValue = wysiwygDiv ? wysiwygDiv.innerText.trim() : input.value.trim();

                                            if (!textValue) {
                                                isValid = false;
                                                if (wysiwygDiv) {
                                                    wysiwygDiv.classList.add('is-invalid');
                                                    if (!firstInvalid) firstInvalid = wysiwygDiv;
                                                } else {
                                                    input.classList.add('is-invalid');
                                                    if (!firstInvalid) firstInvalid = input;
                                                }
                                            } else {
                                                if (wysiwygDiv) wysiwygDiv.classList.remove('is-invalid');
                                                input.classList.remove('is-invalid');
                                            }
                                        });

                                        if (!isValid) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Validation Error',
                                                text: 'Please fill in all selected test result values and custom fields before saving.'
                                            });
                                            if (firstInvalid) firstInvalid.focus();
                                            return;
                                        }

                                        document.getElementById('formAction').value = actionValue;
                                        form.submit();
                                    }
                                </script>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Test Selection Modal -->
    <div class="modal fade" id="primaryTestModal" tabindex="-1" aria-labelledby="primaryTestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title text-white fs-15px" id="primaryTestModalLabel">
                        <em class="icon ni ni-layers me-1"></em> Select Primary Tests
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 bg-light p-2 rounded border">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="selectAllPrimaryTests">
                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="selectAllPrimaryTests">
                                Select All Available Primary Tests
                            </label>
                        </div>
                        <span class="badge bg-primary rounded-pill" id="selectedPrimaryCount">0 selected</span>
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover align-middle mb-0 small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 8%" class="text-center">Select</th>
                                    <th>Primary Test Name</th>
                                    <th style="width: 20%">Unit</th>
                                </tr>
                            </thead>
                            <tbody id="primaryTestList">
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnAppendSelectedPrimary">
                        <em class="icon ni ni-plus-circle me-1"></em> Add Selected Tests
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Test Selection Modal -->
    <div class="modal fade" id="secondaryTestModal" tabindex="-1" aria-labelledby="secondaryTestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2 px-3">
                    <h5 class="modal-title text-white fs-15px" id="secondaryTestModalLabel">
                        <em class="icon ni ni-plus-circle me-1"></em> Select Secondary Tests
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 bg-light p-2 rounded border">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="selectAllSecondaryTests">
                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="selectAllSecondaryTests">
                                Select All Available Secondary Tests
                            </label>
                        </div>
                        <span class="badge bg-success rounded-pill" id="selectedSecondaryCount">0 selected</span>
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover align-middle mb-0 small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 8%" class="text-center">Select</th>
                                    <th>Secondary Test Name</th>
                                    <th style="width: 20%">Unit</th>
                                </tr>
                            </thead>
                            <tbody id="secondaryTestList">
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm" id="btnAppendSelectedSecondary">
                        <em class="icon ni ni-plus-circle me-1"></em> Add Selected Secondary Tests
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Entry Modal -->
    <div class="modal fade" id="rawEntryModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Raw Data Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="rawEntyLoading" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <form id="rawEntryForm">
                        <input type="hidden" id="raw_formula_id" name="formula_id">
                        <input type="hidden" id="raw_reference_id" name="reference_id">
                        <input type="hidden" id="raw_test_id" name="test_id">
                        <input type="hidden" id="raw_test_number" name="test_number">
                        <input type="hidden" id="raw_primary_test_id" name="primary_test_id">
                        <input type="hidden" id="raw_secondary_test_id" name="secondary_test_id">
                        <input type="hidden" id="raw_target_input">

                        <div class="alert alert-info py-2" id="formulaExpressionDisplay">
                            <!-- Formula will be shown here -->
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Number of Readings</label>
                                <input type="number" id="numberOfReadings" class="form-control" value="1"
                                    min="1" max="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aggregation Method</label>
                                <select id="aggregationType" class="form-select">
                                    <option value="NONE">None (Use First)</option>
                                    <option value="AVERAGE">Average</option>
                                    <option value="MAX">Max</option>
                                    <option value="MIN">Min</option>
                                    <option value="SD">Standard Deviation</option>
                                    <option value="CV">Coefficient of Variation (%)</option>
                                </select>
                            </div>
                        </div>

                        <div id="variableInputsContainer" class="table-responsive mb-3">
                            <!-- Table will be generated here -->
                            <table class="table table-bordered table-sm" id="readingsTable">
                                <thead class="table-light">
                                    <tr id="readingsHeaderRow">
                                        <!-- Headers generated by JS -->
                                    </tr>
                                </thead>
                                <tbody id="readingsBody">
                                    <!-- Rows generated by JS -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Calculated Result: <span id="calculatedResult"
                                            class="text-primary fw-bold fs-5">0</span></h6>
                                    <small class="text-muted" id="aggregationLabel"></small>
                                </div>
                                <button type="button" class="btn btn-primary" id="saveRawEntryBtn">Save & Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF / Image Manuscript Modal -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="pdfModalLabel"><em class="icon ni ni-file-text me-1"></em> Test Result Paper Manuscript</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-dark">
                    @if (isset($sampleTests) && $sampleTests->first()?->registration?->tr04_manuscript)
                        @php
                            $manuscriptPath = $sampleTests->first()->registration->tr04_manuscript;
                            $ext = strtolower(pathinfo($manuscriptPath, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            $fullLocalPath = storage_path('app/public/test_results/' . $manuscriptPath);
                            $publicLocalPath = public_path('storage/test_results/' . $manuscriptPath);

                            if ($isImage) {
                                if (file_exists($fullLocalPath)) {
                                    $fileUrl = 'data:image/' . ($ext === 'jpg' ? 'jpeg' : $ext) . ';base64,' . base64_encode(file_get_contents($fullLocalPath));
                                } elseif (file_exists($publicLocalPath)) {
                                    $fileUrl = 'data:image/' . ($ext === 'jpg' ? 'jpeg' : $ext) . ';base64,' . base64_encode(file_get_contents($publicLocalPath));
                                } else {
                                    $fileUrl = asset('public/storage/test_results/' . $manuscriptPath);
                                }
                            } elseif ($ext === 'pdf') {
                                if (file_exists($fullLocalPath)) {
                                    $fileUrl = 'data:application/pdf;base64,' . base64_encode(file_get_contents($fullLocalPath));
                                } elseif (file_exists($publicLocalPath)) {
                                    $fileUrl = 'data:application/pdf;base64,' . base64_encode(file_get_contents($publicLocalPath));
                                } else {
                                    $fileUrl = asset('public/storage/test_results/' . $manuscriptPath);
                                }
                            } else {
                                $fileUrl = asset('public/storage/test_results/' . $manuscriptPath);
                            }
                            $downloadUrl = asset('public/storage/test_results/' . $manuscriptPath);
                        @endphp

                        @if ($isImage)
                            <div class="p-3 text-center" style="max-height: 75vh; overflow-y: auto;">
                                <img src="{{ $fileUrl }}" alt="Manuscript Photo" class="img-fluid rounded shadow-sm mx-auto d-block" style="max-height: 70vh; object-fit: contain;">
                            </div>
                        @elseif ($ext === 'pdf')
                            <div class="p-0" style="height: 75vh;">
                                <embed src="{{ $fileUrl }}" type="application/pdf" width="100%" height="100%" style="border: 0; min-height: 70vh;">
                            </div>
                        @else
                            <div class="p-4 text-center text-white">
                                <em class="icon ni ni-file-text display-3 mb-2"></em>
                                <p class="mb-3">Document format (.{{ $ext }}) cannot be previewed directly in browser.</p>
                                <a href="{{ $downloadUrl }}" target="_blank" download class="btn btn-light btn-sm">
                                    <em class="icon ni ni-download me-1"></em> Download Document
                                </a>
                            </div>
                        @endif

                        <!-- Controls Footer -->
                        <div class="p-3 bg-white border-top d-flex justify-content-between align-items-center">
                            <a href="{{ $downloadUrl }}" target="_blank" download class="btn btn-primary btn-sm">
                                <em class="icon ni ni-download me-1"></em> Download File
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                <em class="icon ni ni-upload me-1"></em> Upload New Copy
                            </button>
                        </div>
                    @else
                        <div class="text-center py-5 bg-white">
                            <em class="icon ni ni-file-text display-4 text-muted"></em>
                            <p class="text-muted mt-3 mb-3">No paper manuscript file uploaded for this sample yet.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                <em class="icon ni ni-upload me-1"></em> Upload Paper Manuscript Photo / PDF
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Manuscript Direct Modal -->
    <div class="modal fade" id="uploadManuscriptDirectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('testresult_upload', optional($sampleTests->first()?->registration)->tr04_reference_id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white"><em class="icon ni ni-upload me-1"></em> Upload Paper Manuscript</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Upload photo or PDF of the calculation paper completed by the analyst for reference: <strong>{{ optional($sampleTests->first()?->registration)->tr04_reference_id ?? '' }}</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select File (JPG, PNG, WEBP, PDF)</label>
                            <input type="file" name="result_file" class="form-control form-control-lg" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><em class="icon ni ni-upload me-1"></em> Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scientific Symbol & Multiplier Picker Popup -->
    <div id="symbolPickerPopup" class="card shadow-lg border position-fixed d-none" style="z-index: 1060; width: 330px; border-radius: 8px; overflow: hidden; font-size: 13px;">
        <div class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-13px"><em class="icon ni ni-spark"></em> Scientific Symbols & Units</span>
            <button type="button" class="btn-close btn-close-white p-1" id="closeSymbolPicker" aria-label="Close" style="font-size: 10px;"></button>
        </div>
        <div class="card-body p-3 bg-white" style="max-height: 400px; overflow-y: auto;">
            <div class="sym-section-title">Math & Science</div>
            <div class="symbol-grid mb-3">
                <button type="button" class="sym-btn btn-insert-sym" data-char="±">±</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="≤">≤</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="≥">≥</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="<">&lt;</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char=">">&gt;</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="≈">≈</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="≠">≠</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="×">×</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="÷">÷</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="°">°</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="℃">℃</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="℉">℉</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="‰">‰</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="%">%</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="√">√</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="∞">∞</button>
            </div>
            <div class="sym-section-title">Greek & Lab Units</div>
            <div class="symbol-grid mb-3">
                <button type="button" class="sym-btn btn-insert-sym" data-char="µ">µ</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="Ω">Ω</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="α">α</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="β">β</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="γ">γ</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="δ">δ</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="λ">λ</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="π">π</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="σ">σ</button>
                <button type="button" class="sym-btn btn-insert-sym" data-char="Å">Å</button>
            </div>
            <div class="sym-section-title">Quick Multipliers</div>
            <div class="symbol-grid-wide">
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="10<sup>-3</sup>">10⁻³</button>
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="10<sup>-6</sup>">10⁻⁶</button>
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="10<sup>3</sup>">10³</button>
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="10<sup>6</sup>">10⁶</button>
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="mm<sup>2</sup>">mm²</button>
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="cm<sup>2</sup>">cm²</button>
                <button type="button" class="sym-btn sym-btn-info btn-insert-html" data-html="cm<sup>3</sup>">cm³</button>
            </div>
        </div>
    </div>

    <script>
        let activeWysiwygInput = null;
        let activeSavedRange = null;

        function saveWysiwygSelection() {
            const sel = window.getSelection();
            if (sel.rangeCount > 0) {
                const range = sel.getRangeAt(0);
                if (activeWysiwygInput && activeWysiwygInput.contains(range.commonAncestorContainer)) {
                    activeSavedRange = range.cloneRange();
                }
            }
        }

        function restoreWysiwygSelection() {
            if (!activeWysiwygInput) return;
            activeWysiwygInput.focus();
            const sel = window.getSelection();
            if (activeSavedRange && activeWysiwygInput.contains(activeSavedRange.commonAncestorContainer)) {
                sel.removeAllRanges();
                sel.addRange(activeSavedRange);
            } else {
                setWysiwygCaretAtEnd(activeWysiwygInput);
            }
        }

        function setWysiwygCaretAtEnd(el) {
            el.focus();
            if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
                const range = document.createRange();
                range.selectNodeContents(el);
                range.collapse(false);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                activeSavedRange = range.cloneRange();
            }
        }

        function updateWysiwygButtonState() {
            if (!activeWysiwygInput) return;
            const sel = window.getSelection();
            if (sel.rangeCount === 0) return;
            
            const node = sel.anchorNode;
            if (!node || !activeWysiwygInput.contains(node)) return;
            
            const parent = node.nodeType === 3 ? node.parentElement : node;
            const isSup = !!(parent && parent.closest('sup'));
            const isSub = !!(parent && parent.closest('sub'));

            const $group = $(activeWysiwygInput).closest('.input-group');
            const $supBtn = $group.find('.btn-wysiwyg-cmd[data-cmd="superscript"]');
            const $subBtn = $group.find('.btn-wysiwyg-cmd[data-cmd="subscript"]');

            if (isSup) {
                $supBtn.addClass('btn-primary text-white').removeClass('btn-outline-light');
            } else {
                $supBtn.removeClass('btn-primary text-white').addClass('btn-outline-light');
            }

            if (isSub) {
                $subBtn.addClass('btn-primary text-white').removeClass('btn-outline-light');
            } else {
                $subBtn.removeClass('btn-primary text-white').addClass('btn-outline-light');
            }
        }

        // Track focus on any WYSIWYG input
        $(document).on('focus keyup mouseup click input', '.wysiwyg-editor-input', function() {
            activeWysiwygInput = this;
            saveWysiwygSelection();
            updateWysiwygButtonState();
            const html = $(this).html();
            $(this).closest('.input-group').find('.wysiwyg-hidden-val').val(html);
        });

        // Prevent button clicks from stealing focus
        $(document).on('mousedown', '.btn-wysiwyg-cmd, .btn-open-symbols, #symbolPickerPopup button', function(e) {
            e.preventDefault();
        });

        // Handle direct x² (Superscript) and x₂ (Subscript) buttons
        $(document).on('click', '.btn-wysiwyg-cmd', function(e) {
            e.preventDefault();
            const $input = $(this).closest('.input-group').find('.wysiwyg-editor-input');
            activeWysiwygInput = $input[0];
            if (!activeWysiwygInput) return;

            restoreWysiwygSelection();
            const cmd = $(this).data('cmd');
            
            const sel = window.getSelection();
            if (sel.rangeCount > 0) {
                const node = sel.anchorNode;
                const parent = node ? (node.nodeType === 3 ? node.parentElement : node) : null;
                const inSup = !!(parent && parent.closest('sup'));
                const inSub = !!(parent && parent.closest('sub'));

                if (cmd === 'superscript' && inSup) {
                    document.execCommand('superscript', false, null);
                    const supEl = parent.closest('sup');
                    if (supEl) {
                        const emptySpan = document.createElement('span');
                        emptySpan.innerHTML = '&#8203;';
                        if (supEl.nextSibling) {
                            supEl.parentNode.insertBefore(emptySpan, supEl.nextSibling);
                        } else {
                            supEl.parentNode.appendChild(emptySpan);
                        }
                        const range = document.createRange();
                        range.setStartAfter(emptySpan);
                        range.setEndAfter(emptySpan);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                } else if (cmd === 'subscript' && inSub) {
                    document.execCommand('subscript', false, null);
                    const subEl = parent.closest('sub');
                    if (subEl) {
                        const emptySpan = document.createElement('span');
                        emptySpan.innerHTML = '&#8203;';
                        if (subEl.nextSibling) {
                            subEl.parentNode.insertBefore(emptySpan, subEl.nextSibling);
                        } else {
                            subEl.parentNode.appendChild(emptySpan);
                        }
                        const range = document.createRange();
                        range.setStartAfter(emptySpan);
                        range.setEndAfter(emptySpan);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                } else {
                    if (cmd === 'superscript' && inSub) {
                        document.execCommand('subscript', false, null);
                    } else if (cmd === 'subscript' && inSup) {
                        document.execCommand('superscript', false, null);
                    }
                    document.execCommand(cmd, false, null);
                }
            } else {
                document.execCommand(cmd, false, null);
            }

            $(activeWysiwygInput).trigger('input');
            saveWysiwygSelection();
            updateWysiwygButtonState();
        });

        // Handle Ω (Symbol Picker) button
        $(document).on('click', '.btn-open-symbols', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $input = $(this).closest('.input-group').find('.wysiwyg-editor-input');
            const targetDiv = $input[0];
            const $popup = $('#symbolPickerPopup');

            if (!$popup.hasClass('d-none') && activeWysiwygInput === targetDiv) {
                $popup.addClass('d-none');
                return;
            }

            activeWysiwygInput = targetDiv;
            if (activeWysiwygInput) {
                setWysiwygCaretAtEnd(activeWysiwygInput);

                // Position popup relative to viewport
                const rect = this.getBoundingClientRect();
                $popup.removeClass('d-none');
                
                const popupHeight = $popup.outerHeight() || 280;
                const popupWidth = $popup.outerWidth() || 310;
                
                let top = rect.bottom + 6;
                if (top + popupHeight > window.innerHeight - 10) {
                    top = rect.top - popupHeight - 6;
                }
                if (top < 10) top = 10;

                let left = rect.left;
                if (left + popupWidth > window.innerWidth - 20) {
                    left = window.innerWidth - popupWidth - 20;
                }
                if (left < 10) left = 10;

                $popup.css({
                    top: top + 'px',
                    left: left + 'px'
                });
            }
        });

        // Insert Symbol from popup
        $(document).on('click', '#symbolPickerPopup .btn-insert-sym', function(e) {
            e.preventDefault();
            if (!activeWysiwygInput) return;
            restoreWysiwygSelection();

            const char = $(this).data('char');
            if (!document.execCommand('insertText', false, char)) {
                const sel = window.getSelection();
                if (sel.rangeCount) {
                    const range = sel.getRangeAt(0);
                    range.deleteContents();
                    const textNode = document.createTextNode(char);
                    range.insertNode(textNode);
                    range.setStartAfter(textNode);
                    range.setEndAfter(textNode);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
            $(activeWysiwygInput).trigger('input');
            saveWysiwygSelection();
        });

        // Insert HTML from popup
        $(document).on('click', '#symbolPickerPopup .btn-insert-html', function(e) {
            e.preventDefault();
            if (!activeWysiwygInput) return;
            restoreWysiwygSelection();

            const html = $(this).data('html');
            if (!document.execCommand('insertHTML', false, html)) {
                const sel = window.getSelection();
                if (sel.rangeCount) {
                    const range = sel.getRangeAt(0);
                    range.deleteContents();
                    const el = document.createElement("div");
                    el.innerHTML = html;
                    const frag = document.createDocumentFragment();
                    let node, lastNode;
                    while ((node = el.firstChild)) {
                        lastNode = frag.appendChild(node);
                    }
                    range.insertNode(frag);
                    if (lastNode) {
                        range.setStartAfter(lastNode);
                        range.setEndAfter(lastNode);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                }
            }
            $(activeWysiwygInput).trigger('input');
            saveWysiwygSelection();
        });

        // Close Symbol Picker
        $(document).on('click', '#closeSymbolPicker', function() {
            $('#symbolPickerPopup').addClass('d-none');
        });

        // Close symbol picker when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#symbolPickerPopup, .btn-open-symbols').length) {
                $('#symbolPickerPopup').addClass('d-none');
            }
        });

        // Keyboard shortcuts inside WYSIWYG
        $(document).on('keydown', '.wysiwyg-editor-input', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $(this).blur();
            } else if (e.ctrlKey && (e.key === '.' || (e.shiftKey && e.key === '+') || e.key === '+')) {
                e.preventDefault();
                document.execCommand('superscript', false, null);
                $(this).trigger('input');
            } else if (e.ctrlKey && (e.key === ',' || e.key === '=')) {
                e.preventDefault();
                document.execCommand('subscript', false, null);
                $(this).trigger('input');
            }
        });
    </script>

    <style>
        .wysiwyg-editor-input {
            min-width: 120px;
            min-height: 31px;
            white-space: pre-wrap;
            overflow-x: auto;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .wysiwyg-editor-input:focus {
            outline: 2px solid #0965f1;
            background-color: #ffffff !important;
        }
        .wysiwyg-editor-input:empty:before {
            content: attr(data-placeholder);
            color: #94a3b8;
        }
        .symbol-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .symbol-grid-wide {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .sym-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .sym-btn:hover {
            background-color: #0965f1;
            color: #ffffff;
            border-color: #0965f1;
        }
        .sym-btn-info {
            width: 44px;
            font-size: 11px;
        }
        .sym-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 6px;
            margin-bottom: 4px;
        }

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

            let currentPrimaryModalData = [];
            let currentSecondaryModalData = [];
            let currentSecondaryContext = {};

            function showPrimaryTestModal(primaryTests) {
                const primaryTestList = document.getElementById('primaryTestList');
                primaryTestList.innerHTML = '';
                document.getElementById('selectAllPrimaryTests').checked = false;
                document.getElementById('selectedPrimaryCount').textContent = '0 selected';

                const usedSet = usedPrimaryTests.get(currentTestNumber) || new Set();
                const availablePrimaryTests = primaryTests.filter(test =>
                    !usedSet.has(test.m16_primary_test_id.toString())
                );
                currentPrimaryModalData = availablePrimaryTests;

                if (availablePrimaryTests.length === 0) {
                    primaryTestList.innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                No more primary tests available for this test.
                            </td>
                        </tr>
                    `;
                } else {
                    availablePrimaryTests.forEach(test => {
                        const hasSecondaryTests = test.secondary_tests && test.secondary_tests.length > 0;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input chk-primary-test" data-id="${test.m16_primary_test_id}">
                            </td>
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
                        `;
                        primaryTestList.appendChild(row);
                    });
                }

                $('#primaryTestModal').modal('show');
            }

            // Select All Primary Tests Checkbox
            $(document).on('change', '#selectAllPrimaryTests', function() {
                const isChecked = this.checked;
                $('.chk-primary-test').prop('checked', isChecked);
                updatePrimarySelectedCount();
            });

            $(document).on('change', '.chk-primary-test', function() {
                updatePrimarySelectedCount();
            });

            function updatePrimarySelectedCount() {
                const count = $('.chk-primary-test:checked').length;
                document.getElementById('selectedPrimaryCount').textContent = `${count} selected`;
                const total = $('.chk-primary-test').length;
                $('#selectAllPrimaryTests').prop('checked', total > 0 && count === total);
            }

            // Append Selected Primary Tests
            $(document).on('click', '#btnAppendSelectedPrimary', function() {
                const selectedCheckboxes = document.querySelectorAll('.chk-primary-test:checked');
                if (selectedCheckboxes.length === 0) {
                    Swal.fire('Notice', 'Please select at least one primary test to add.', 'info');
                    return;
                }

                let addedCount = 0;
                selectedCheckboxes.forEach(chk => {
                    const pId = chk.getAttribute('data-id');
                    const test = currentPrimaryModalData.find(t => t.m16_primary_test_id.toString() === pId.toString());
                    if (test) {
                        const hasSecondaryTests = test.secondary_tests && test.secondary_tests.length > 0;
                        addPrimaryTestRow(currentTestNumber, currentTestId, test.m16_primary_test_id, test.m16_name,
                            test.m16_unit || '', test.m23_formula_id || '', hasSecondaryTests, test.secondary_tests || []);
                        addedCount++;
                    }
                });

                $('#primaryTestModal').modal('hide');
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: `Added ${addedCount} primary test(s)`
                });
            });

            function addPrimaryTestRow(testNumber, testId, primaryTestId, primaryTestName, primaryTestUnit,
                formulaId, hasSecondaryTests, secondaryTests) {
                const rowId = `primary_test_${testNumber}_${primaryTestId}`;

                const mainTestRow = document.querySelector(`.test-main-row[data-test-number="${testNumber}"]`);
                const mainTestName = mainTestRow ? mainTestRow.cells[1].textContent.trim().toLowerCase() : '';
                const pNameLower = (primaryTestName || '').toLowerCase();
                const isAzo = mainTestName.includes('banned amines') || mainTestName.includes('aryl amine') || mainTestName.includes('azo') || pNameLower.includes('azo') || pNameLower.includes('amine');
                const defaultResultVal = isAzo ? 'Not Detected' : '';

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
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][result]"
                                class="wysiwyg-hidden-val"
                                value="${defaultResultVal}">
                            <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter result value">${defaultResultVal}</div>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                            ${formulaId ? `
                                <button type="button"
                                    class="btn btn-outline-light btn-sm open-raw-entry"
                                    data-target-name="results[${testNumber}][primary_tests][${primaryTestId}][result]"
                                    data-label="${primaryTestName}"
                                    title="Formula / Raw Entry">
                                    <em class="icon ni ni-calc"></em>
                                </button>
                            ` : ''}
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
                                data-test-id="${testId}"
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
                if (mainTestRow) {
                    mainTestRow.insertAdjacentHTML('afterend', primaryTestHtml);
                }

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

            // Handle Secondary Test Modal
            $(document).on('click', '.add-secondary-test', function() {
                const testNumber = $(this).data('test-number');
                const testId = $(this).data('test-id');
                const primaryTestId = $(this).data('primary-test-id');
                const secondaryTests = $(this).data('secondary-tests') || [];

                currentSecondaryContext = { testNumber, testId, primaryTestId, secondaryTests };
                showSecondaryTestModal(primaryTestId, secondaryTests);
            });

            function showSecondaryTestModal(primaryTestId, secondaryTests) {
                const secondaryTestList = document.getElementById('secondaryTestList');
                secondaryTestList.innerHTML = '';
                document.getElementById('selectAllSecondaryTests').checked = false;
                document.getElementById('selectedSecondaryCount').textContent = '0 selected';

                const availableSecondaryTests = getAvailableSecondaryTests(primaryTestId, secondaryTests);
                currentSecondaryModalData = availableSecondaryTests;

                if (availableSecondaryTests.length === 0) {
                    Swal.fire('Info', 'No more secondary tests available for this primary test.', 'info');
                    return;
                }

                availableSecondaryTests.forEach(test => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input chk-secondary-test" data-id="${test.m17_secondary_test_id}">
                        </td>
                        <td>
                            <strong>${test.m17_name}</strong>
                        </td>
                        <td>
                            <small>${test.m17_unit || 'N/A'}</small>
                        </td>
                    `;
                    secondaryTestList.appendChild(row);
                });

                $('#secondaryTestModal').modal('show');
            }

            // Select All Secondary Tests Checkbox
            $(document).on('change', '#selectAllSecondaryTests', function() {
                const isChecked = this.checked;
                $('.chk-secondary-test').prop('checked', isChecked);
                updateSecondarySelectedCount();
            });

            $(document).on('change', '.chk-secondary-test', function() {
                updateSecondarySelectedCount();
            });

            function updateSecondarySelectedCount() {
                const count = $('.chk-secondary-test:checked').length;
                document.getElementById('selectedSecondaryCount').textContent = `${count} selected`;
                const total = $('.chk-secondary-test').length;
                $('#selectAllSecondaryTests').prop('checked', total > 0 && count === total);
            }

            // Append Selected Secondary Tests
            $(document).on('click', '#btnAppendSelectedSecondary', function() {
                const selectedCheckboxes = document.querySelectorAll('.chk-secondary-test:checked');
                if (selectedCheckboxes.length === 0) {
                    Swal.fire('Notice', 'Please select at least one secondary test to add.', 'info');
                    return;
                }

                let addedCount = 0;
                const { testNumber, testId, primaryTestId, secondaryTests } = currentSecondaryContext;

                selectedCheckboxes.forEach(chk => {
                    const sId = chk.getAttribute('data-id');
                    const test = currentSecondaryModalData.find(t => t.m17_secondary_test_id.toString() === sId.toString());
                    if (test) {
                        addSecondaryTestRow(testNumber, primaryTestId, test.m17_secondary_test_id.toString(), '',
                            test.m17_unit || '', test.m23_formula_id || '', secondaryTests, testId);
                        addedCount++;
                    }
                });

                $('#secondaryTestModal').modal('hide');
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: `Added ${addedCount} secondary test(s)`
                });
            });

            function addSecondaryTestRow(testNumber, primaryTestId, secondaryTestId, resultValue = '', unitValue =
                '', formulaId, secondaryTests, testId) {
                const secondaryTest = secondaryTests.find(test => test.m17_secondary_test_id.toString() ===
                    secondaryTestId);
                if (!secondaryTest) return;

                const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                const mainTestRow = primaryTestRow ? primaryTestRow.closest('tbody').querySelector('.test-main-row') : null;
                const mainTestName = mainTestRow ? mainTestRow.cells[1].textContent.trim().toLowerCase() : '';
                const isAzo = mainTestName.includes('banned amines') || mainTestName.includes('aryl amine') || mainTestName.includes('azo');
                const finalResultVal = resultValue || (isAzo ? 'Not Detected' : '');

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
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][result]"
                                class="wysiwyg-hidden-val"
                                value="${finalResultVal}">
                            <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter result value">${finalResultVal}</div>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                            ${formulaId ? `
                                <button type="button"
                                    class="btn btn-outline-light btn-sm open-raw-entry"
                                    data-target-name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][result]"
                                    data-label="${secondaryTest.m17_name}"
                                    title="Formula / Raw Entry">
                                    <em class="icon ni ni-calc"></em>
                                </button>
                            ` : ''}
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

                if (primaryTestRow) {
                    primaryTestRow.insertAdjacentHTML('afterend', secondaryTestHtml);
                }

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
                            <input type="hidden" 
                                name="${namePrefix}[value]" 
                                class="wysiwyg-hidden-val"
                                value="${fieldValue || ''}">
                            <div class="form-control form-control-sm custom-field-input wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter value">${fieldValue || ''}</div>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
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
                            <input type="hidden" 
                                name="${namePrefix}[value]" 
                                class="wysiwyg-hidden-val"
                                value="">
                            <div class="form-control form-control-sm custom-field-input wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter value"></div>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
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
                        const testId = this.getAttribute('data-test-id');
                        const primaryTestId = this.getAttribute('data-primary-test-id');
                        const secondaryTests = JSON.parse(this.getAttribute('data-secondary-tests') ||
                            '[]');
                        addSecondaryTestDropdown(testNumber, primaryTestId, secondaryTests, testId);
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


            // --- Raw Entry Logic ---
            const rawEntryModal = new bootstrap.Modal(document.getElementById('rawEntryModal'));
            let currentRowResults = [];

            function renderReadingsGrid(existingData = null) {
                if (!currentFormulaData) return;

                const numReadings = parseInt(document.getElementById('numberOfReadings').value) || 1;
                const variables = currentFormulaData.variables;
                const headerRow = document.getElementById('readingsHeaderRow');
                const body = document.getElementById('readingsBody');

                // Header
                let headerHtml = '<th style="width: 50px;">#</th>';
                variables.forEach(v => {
                    headerHtml += `<th>${v.m24_label} (${v.m24_variable_key})</th>`;
                });
                headerHtml += '<th style="width: 100px;">Result</th>';
                headerRow.innerHTML = headerHtml;

                // Body
                let bodyHtml = '';

                // If existingData exists, it might be an object, ensure array for easier access if indices are keys
                let dataArray = existingData;
                if (existingData && !Array.isArray(existingData)) {
                    dataArray = Object.values(existingData);
                }

                for (let i = 0; i < numReadings; i++) {
                    const rowData = dataArray && dataArray[i] ? dataArray[i] : {};

                    bodyHtml += `<tr data-row-index="${i}">`;
                    bodyHtml += `<td>${i + 1}</td>`;
                    variables.forEach(v => {
                        const val = rowData[v.m24_variable_key] !== undefined ? rowData[v
                            .m24_variable_key] : '';

                        bodyHtml += `
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="any" class="form-control formula-variable" 
                                        data-row="${i}"
                                        data-key="${v.m24_variable_key}" 
                                        name="variables[${i}][${v.m24_variable_key}]"
                                        value="${val}"
                                        placeholder="${v.m24_unit || ''}"
                                        ${v.m24_is_required ? 'required' : ''}>
                                </div>
                            </td>`;
                    });
                    bodyHtml += `<td class="text-end fw-bold row-result" id="row_result_${i}">-</td>`;
                    bodyHtml += `</tr>`;
                }
                body.innerHTML = bodyHtml;

                // Re-attach listeners or rely on delegation? Delegation is set on container below.
                calculateResult(); // Recalculate based on newly filled inputs
            }

            // Calculation Logic
            function calculateResult() {
                if (!currentFormulaData) return;

                const numReadings = parseInt(document.getElementById('numberOfReadings').value) || 1;
                const expressionBase = currentFormulaData.expression;
                currentRowResults = [];
                let allRowsValid = true;

                // Variables sorted by key length
                const variableKeys = currentFormulaData.variables.map(v => v.m24_variable_key)
                    .sort((a, b) => b.length - a.length);

                for (let i = 0; i < numReadings; i++) {
                    let expression = expressionBase;
                    let rowFilled = true;

                    // Replace variables
                    variableKeys.forEach(key => {
                        const input = document.querySelector(
                            `.formula-variable[data-row="${i}"][data-key="${key}"]`);
                        let value = parseFloat(input ? input.value : 0);
                        if (isNaN(value)) {
                            value = 0;
                            // Check required
                            if (input && input.hasAttribute('required') && input.value === '') rowFilled =
                                false;
                        }
                        expression = expression.replaceAll(key, value);
                    });

                    let result = 0;
                    if (rowFilled) {
                        try {
                            const sanitizedExpression = expression.replace(/[^0-9\.\+\-\*\/\(\)\%\s]/g, '');
                            result = new Function('return ' + sanitizedExpression)();
                            if (isNaN(result) || !isFinite(result)) result = 0;
                            // Round row result
                            result = Math.round((result + Number.EPSILON) * 10000) / 10000;
                        } catch (e) {
                            result = 0;
                            rowFilled = false;
                        }
                    } else {
                        allRowsValid =
                            false; // Mark overall invalid if needed, but we might just ignore this row for aggregation? 
                        // Actually, for stats, we usually need all inputs. Let's assume 0 for missing.
                    }

                    document.getElementById(`row_result_${i}`).innerText = rowFilled ? result : '-';
                    if (rowFilled) currentRowResults.push(result);
                }

                computeGlobalResult();
            }

            function computeGlobalResult() {
                const aggregation = document.getElementById('aggregationType').value;
                const resultSpan = document.getElementById('calculatedResult');
                const validResults = currentRowResults; // Use all valid calculated rows

                if (validResults.length === 0) {
                    resultSpan.innerText = '0';
                    return;
                }

                let final = 0;
                let label = '';

                switch (aggregation) {
                    case 'AVERAGE':
                        const sum = validResults.reduce((a, b) => a + b, 0);
                        final = sum / validResults.length;
                        label = `Average of ${validResults.length} readings`;
                        break;
                    case 'MAX':
                        final = Math.max(...validResults);
                        label = 'Maximum value';
                        break;
                    case 'MIN':
                        final = Math.min(...validResults);
                        label = 'Minimum value';
                        break;
                    case 'SD':
                        if (validResults.length > 1) {
                            const mean = validResults.reduce((a, b) => a + b, 0) / validResults.length;
                            const sqDiff = validResults.map(v => Math.pow(v - mean, 2));
                            const avgSqDiff = sqDiff.reduce((a, b) => a + b, 0) / (validResults.length -
                                1); // Sample SD
                            final = Math.sqrt(avgSqDiff);
                        } else {
                            final = 0;
                        }
                        label = 'Standard Deviation';
                        break;
                    case 'CV':
                        if (validResults.length > 1) {
                            const meanCv = validResults.reduce((a, b) => a + b, 0) / validResults.length;
                            const sqDiffCv = validResults.map(v => Math.pow(v - meanCv, 2));
                            const avgSqDiffCv = sqDiffCv.reduce((a, b) => a + b, 0) / (validResults.length - 1);
                            const sd = Math.sqrt(avgSqDiffCv);
                            if (meanCv !== 0) final = (sd / meanCv) * 100;
                        } else {
                            final = 0;
                        }
                        label = 'Coefficient of Variation (%)';
                        break;
                    default: // NONE
                        final = validResults[0] || 0;
                        label = 'First reading value';
                }

                // Global rounding
                final = Math.round((final + Number.EPSILON) * 1000) / 1000;
                resultSpan.innerText = isNaN(final) ? 'Error' : final;
                document.getElementById('aggregationLabel').innerText = label;
            }

            document.addEventListener('click', function(e) {
                if (e.target.closest('.raw-entry-btn')) {
                    const btn = e.target.closest('.raw-entry-btn');
                    const formulaId = btn.getAttribute('data-formula-id');
                    const referenceId = btn.getAttribute('data-reference-id');
                    const testId = btn.getAttribute('data-test-id');
                    const testNumber = btn.getAttribute('data-test-number');
                    const primaryTestId = btn.getAttribute('data-primary-test-id');
                    const secondaryTestId = btn.getAttribute('data-secondary-test-id');
                    const targetInputName = btn.getAttribute('data-target-input');

                    // Set hidden fields
                    document.getElementById('raw_formula_id').value = formulaId;
                    document.getElementById('raw_reference_id').value = referenceId;
                    document.getElementById('raw_test_id').value = testId;
                    document.getElementById('raw_test_number').value = testNumber;
                    document.getElementById('raw_primary_test_id').value = primaryTestId || '';
                    document.getElementById('raw_secondary_test_id').value = secondaryTestId || '';
                    document.getElementById('raw_target_input').value = targetInputName;

                    // Reset UI
                    document.getElementById('formulaExpressionDisplay').innerText = 'Loading formula...';
                    document.getElementById('readingsBody').innerHTML = ''; // Clear table
                    document.getElementById('calculatedResult').innerText = '0';
                    document.getElementById('aggregationLabel').innerText = '';
                    document.getElementById('numberOfReadings').value = 1;
                    document.getElementById('aggregationType').value = 'NONE';

                    document.getElementById('rawEntyLoading').classList.remove('d-none');
                    document.getElementById('rawEntryForm').style.opacity = '0.5';

                    rawEntryModal.show();

                    // Parallel Fetch: Formula details AND Existing Entry
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('reference_id', referenceId);
                    formData.append('test_id', testId);
                    formData.append('test_number', testNumber);
                    formData.append('primary_test_id', primaryTestId || '');
                    formData.append('secondary_test_id', secondaryTestId || '');

                    Promise.all([
                            fetch(`{{ url('test-results/get-formula-details') }}/${formulaId}`).then(
                                r => r.json()),
                            fetch(`{{ route('get_raw_entry') }}`, {
                                method: 'POST',
                                body: formData
                            }).then(r => r.json())
                        ])
                        .then(([formulaData, rawEntryData]) => {
                            currentFormulaData = formulaData;
                            document.getElementById('formulaExpressionDisplay').innerText =
                                `Formula: ${formulaData.expression}`;

                            let existingVariables = null;

                            if (rawEntryData) {
                                // Populate from existing
                                if (rawEntryData.tr12_type) {
                                    document.getElementById('aggregationType').value = rawEntryData
                                        .tr12_type;
                                }

                                existingVariables = rawEntryData
                                    .variables; // Expected to be array of objects or similar structure

                                // Determine number of readings based on existing data
                                // The saved structure is likely: variables: [ {key: val, key2: val}, {key: val...} ] (array of rows)
                                // OR array of objects where keys are variables? The save sends `variables[rowIndex][key]`
                                // PHP `json_encode` of that will be object { "0": {key:val}, "1": {key:val} } or array if indices are sequential.

                                let numReadings = 0;
                                if (existingVariables) {
                                    // Convert to array if object
                                    if (!Array.isArray(existingVariables) &&
                                        typeof existingVariables === 'object') {
                                        existingVariables = Object.values(existingVariables);
                                    }
                                    numReadings = existingVariables.length;
                                }

                                if (numReadings > 0) {
                                    document.getElementById('numberOfReadings').value = numReadings;
                                }
                            }

                            renderReadingsGrid(existingVariables);

                            document.getElementById('rawEntyLoading').classList.add('d-none');
                            document.getElementById('rawEntryForm').style.opacity = '1';
                        })
                        .catch(error => {
                            console.error('Error fetching details:', error);
                            Swal.fire('Error', 'Failed to load details.', 'error');
                            rawEntryModal.hide();
                        });
                }
            });

            // Listeners for changes
            document.getElementById('numberOfReadings').addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
                if (this.value > 20) this.value = 20; // Safety limit
                renderReadingsGrid();
            });

            document.getElementById('aggregationType').addEventListener('change', function() {
                calculateResult();
            });

            // Real-time Calculation delegation
            document.getElementById('readingsBody').addEventListener('input', function(e) {
                if (e.target.classList.contains('formula-variable')) {
                    calculateResult();
                }
            });

            // Save Raw Entry
            document.getElementById('saveRawEntryBtn').addEventListener('click', function() {
                const calculatedValue = document.getElementById('calculatedResult').innerText;

                if (calculatedValue === 'Error' || calculatedValue === '...') {
                    Swal.fire('Error', 'Please ensure all variables are filled correctly.', 'error');
                    return;
                }

                const formData = new FormData(document.getElementById('rawEntryForm'));
                // Add calculated output manually
                formData.append('calculated_output', calculatedValue);
                // Collect variables as JSON object manual override to ensure structure if needed, 
                // but FormData handles array notation `variables[KEY]` well for PHP.
                // However, our controller expects `variables` to be valid JSON if we just json_encode raw input?
                // The controller does: 'tr12_variables' => json_encode($request->variables)
                // If inputs are named "variables[key]", PHP receives an associative array. json_encode(array) -> JSON object. This works.

                // Add CSRF token
                formData.append('_token', '{{ csrf_token() }}');

                // Add aggregation type
                formData.append('aggregation_type', document.getElementById('aggregationType').value);

                fetch('{{ route('save_raw_entry') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', 'Raw entry saved successfully.', 'success');

                            // Update the Main/Primary/Secondary result input
                            const targetInputName = document.getElementById('raw_target_input').value;
                            const targetInput = document.querySelector(
                                `input[name="${targetInputName}"]`);
                            if (targetInput) {
                                targetInput.value = calculatedValue;
                                // Also trigger change if needed
                            }

                            rawEntryModal.hide();
                        } else {
                            Swal.fire('Error', data.message || 'Failed to save.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An error occurred while saving.', 'error');
                    });
            });

            // ==========================================================
            // VALIDATION FAILURE PERSISTENCE LOGIC
            // ==========================================================

            // Restore dynamically added rows from validation errors
            @if (old('results'))
                const oldResults = @json(old('results'));
                if (oldResults) {
                    Object.keys(oldResults).forEach(testNum => {
                        const testData = oldResults[testNum];
                        if (testData.primary_tests) {
                            Object.keys(testData.primary_tests).forEach(primaryTestId => {
                                const ptData = testData.primary_tests[primaryTestId];

                                // Check if row already exists
                                if (!document.querySelector(
                                        `.primary-test-row[data-test-number="${testNum}"][data-primary-test-id="${primaryTestId}"]`
                                    )) {
                                    // Find details
                                    const btn = document.querySelector(
                                        `.add-primary-test[data-test-number="${testNum}"]`);
                                    if (btn) {
                                        try {
                                            const ptList = JSON.parse(btn.getAttribute(
                                                'data-primary-tests') || '[]');
                                            const ptDetails = ptList.find(pt => pt
                                                .m16_primary_test_id.toString() ===
                                                primaryTestId.toString());
                                            if (ptDetails) {
                                                const hasSec = ptDetails.secondary_tests &&
                                                    ptDetails.secondary_tests.length > 0;
                                                const testId = ptData.test_id || btn.getAttribute(
                                                    'data-test-id');
                                                addPrimaryTestRow(testNum, testId, primaryTestId,
                                                    ptDetails.m16_name, ptDetails.m16_unit ||
                                                    '', ptDetails.m23_formula_id || '', hasSec,
                                                    ptDetails.secondary_tests || []);
                                            }
                                        } catch (e) {}
                                    }
                                }

                                // Set values securely after a tiny timeout to ensure DOM is ready
                                setTimeout(() => {
                                    const resInput = document.querySelector(
                                        `input[name="results[${testNum}][primary_tests][${primaryTestId}][result]"]`
                                    );
                                    if (resInput) resInput.value = ptData.result || '';
                                    const unitInput = document.querySelector(
                                        `input[name="results[${testNum}][primary_tests][${primaryTestId}][unit]"]`
                                    );
                                    if (unitInput) unitInput.value = ptData.unit || '';
                                }, 50);

                                // Secondary tests
                                if (ptData.secondary_tests) {
                                    Object.keys(ptData.secondary_tests).forEach(secId => {
                                        const stData = ptData.secondary_tests[secId];
                                        if (!document.querySelector(
                                                `.secondary-test-row[data-test-number="${testNum}"][data-primary-test-id="${primaryTestId}"][data-secondary-test-id="${secId}"]`
                                            )) {
                                            const btn = document.querySelector(
                                                `.add-primary-test[data-test-number="${testNum}"]`
                                            );
                                            if (btn) {
                                                try {
                                                    const ptList = JSON.parse(btn
                                                        .getAttribute(
                                                            'data-primary-tests') ||
                                                        '[]');
                                                    const ptDetails = ptList.find(pt => pt
                                                        .m16_primary_test_id
                                                        .toString() === primaryTestId
                                                        .toString());
                                                    if (ptDetails && ptDetails
                                                        .secondary_tests) {
                                                        const stDetails = ptDetails
                                                            .secondary_tests.find(st => st
                                                                .m17_secondary_test_id
                                                                .toString() === secId
                                                                .toString());
                                                        if (stDetails) {
                                                            const testId = stData.test_id ||
                                                                btn.getAttribute(
                                                                    'data-test-id');
                                                            addSecondaryTestRow(testNum,
                                                                primaryTestId, secId,
                                                                stData.result || '',
                                                                stData.unit || stDetails
                                                                .m17_unit || '',
                                                                stDetails
                                                                .m23_formula_id || '',
                                                                ptDetails
                                                                .secondary_tests || [],
                                                                testId);
                                                        }
                                                    }
                                                } catch (e) {}
                                            }
                                        } else {
                                            // Row exists normally, just repopulate
                                            setTimeout(() => {
                                                const resInput = document
                                                    .querySelector(
                                                        `input[name="results[${testNum}][primary_tests][${primaryTestId}][secondary_tests][${secId}][result]"]`
                                                    );
                                                if (resInput) resInput.value =
                                                    stData.result || '';
                                                const unitInput = document
                                                    .querySelector(
                                                        `input[name="results[${testNum}][primary_tests][${primaryTestId}][secondary_tests][${secId}][unit]"]`
                                                    );
                                                if (unitInput) unitInput.value =
                                                    stData.unit || '';
                                            }, 50);
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            @endif

            // Restore dynamically added custom fields from validation errors
            @if (old('custom_fields'))
                const oldCustomFields = @json(old('custom_fields'));
                if (oldCustomFields) {
                    Object.keys(oldCustomFields).forEach(testNum => {
                        const level1 = oldCustomFields[testNum];
                        if (typeof level1 === 'object') {
                            Object.keys(level1).forEach(key1 => {
                                if (key1.startsWith('custom_field_') || key1.startsWith('new_')) {
                                    const cfData = level1[key1];
                                    if (!document.getElementById(key1)) {
                                        addExistingCustomField(key1, testNum, '', '', 'test', cfData
                                            .name || cfData.label || '', cfData.value || '',
                                            cfData.unit || '', cfData.custom_field_id || '');
                                    }
                                } else if (key1.startsWith('primary_')) {
                                    const pId = key1.replace('primary_', '');
                                    const level2 = level1[key1];
                                    if (typeof level2 === 'object') {
                                        Object.keys(level2).forEach(key2 => {
                                            if (key2.startsWith('custom_field_') || key2
                                                .startsWith('new_')) {
                                                const cfData = level2[key2];
                                                if (!document.getElementById(key2)) {
                                                    setTimeout(() => {
                                                        addExistingCustomField(key2,
                                                            testNum, pId, '',
                                                            'primary', cfData
                                                            .name || cfData
                                                            .label || '', cfData
                                                            .value || '', cfData
                                                            .unit || '', cfData
                                                            .custom_field_id ||
                                                            '');
                                                    }, 100);
                                                }
                                            } else if (key2.startsWith('secondary_')) {
                                                const sId = key2.replace('secondary_', '');
                                                const level3 = level2[key2];
                                                if (typeof level3 === 'object') {
                                                    Object.keys(level3).forEach(key3 => {
                                                        if (key3.startsWith(
                                                                'custom_field_') ||
                                                            key3.startsWith('new_')
                                                        ) {
                                                            const cfData = level3[
                                                                key3];
                                                            if (!document
                                                                .getElementById(
                                                                    key3)) {
                                                                setTimeout(() => {
                                                                    addExistingCustomField
                                                                        (key3,
                                                                            testNum,
                                                                            pId,
                                                                            sId,
                                                                            'secondary',
                                                                            cfData
                                                                            .name ||
                                                                            cfData
                                                                            .label ||
                                                                            '',
                                                                            cfData
                                                                            .value ||
                                                                            '',
                                                                            cfData
                                                                            .unit ||
                                                                            '',
                                                                            cfData
                                                                            .custom_field_id ||
                                                                            ''
                                                                        );
                                                                }, 150);
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            @endif
            // Display Laravel Validation Errors for both static and dynamically restored fields
            @if ($errors->any())
                const validationErrors = @json($errors->toArray());
                setTimeout(() => {
                    Object.keys(validationErrors).forEach(field => {
                        // Convert laravel dot notation to html array notation
                        // e.g., results.1039.test.result -> results[1039][test][result]
                        let inputName = field;
                        if (field.includes('.')) {
                            const parts = field.split('.');
                            inputName = parts[0];
                            for (let i = 1; i < parts.length; i++) {
                                inputName += `[${parts[i]}]`;
                            }
                        }

                        const inputElements = document.querySelectorAll(`[name="${inputName}"]`);
                        inputElements.forEach(inputElement => {
                            inputElement.classList.add('is-invalid');

                            // Find a good place to append the error message
                            let container = inputElement.closest('td') || inputElement
                                .parentElement;
                            // Avoid adding multiple spans if Blade already rendered one
                            if (container && !container.innerHTML.includes(validationErrors[
                                    field][0])) {
                                const errorSpan = document.createElement('span');
                                errorSpan.className =
                                    'text-danger small d-block mt-1 validation-error-msg';
                                errorSpan.innerText = validationErrors[field][0];

                                // If it's an input group, append after the group to keep layout clean
                                const inputGroup = inputElement.closest('.input-group');
                                if (inputGroup && inputGroup.parentElement) {
                                    inputGroup.parentElement.appendChild(errorSpan);
                                } else {
                                    container.appendChild(errorSpan);
                                }
                            }
                        });
                    });
                }, 500); // short delay to ensure dynamic rows are fully rendered
            @endif

        });
    </script>

    @if (Session::has('swal_message') || Session::has('message'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (Session::has('swal_message'))
                    Swal.fire({
                        title: "{{ Session::get('swal_title', 'Notification') }}",
                        text: "{{ Session::get('swal_message') }}",
                        icon: "{{ Session::get('swal_icon', 'success') }}",
                        confirmButtonText: 'OK',
                        confirmButtonColor: "{{ Session::get('swal_icon') === 'error' ? '#e85347' : '#28a745' }}"
                    });
                @elseif (Session::has('message'))
                    Swal.fire({
                        title: "{{ Session::get('type') === 'error' ? 'Error' : 'Success' }}",
                        text: "{{ Session::get('message') }}",
                        icon: "{{ Session::get('type', 'success') }}",
                        confirmButtonText: 'OK',
                        confirmButtonColor: "{{ Session::get('type') === 'error' ? '#e85347' : '#28a745' }}"
                    });
                @endif
            });
        </script>
    @endif
@endsection
