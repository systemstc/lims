@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">

                        @php
                            $roName = 'MUMBAI';
                            if (Session::has('ro_id')) {
                                $ro = \App\Models\Ro::find(Session::get('ro_id'));
                                if ($ro) {
                                    $roName = strtoupper(str_replace('RO ', '', $ro->m04_name));
                                }
                            } else {
                                $employee = \App\Models\Employee::with('district')
                                    ->where('tr01_user_id', Session::get('tr01_user_id'))
                                    ->first();
                                if ($employee && $employee->district) {
                                    $roName = strtoupper(str_replace('RO ', '', $employee->district->m04_name));
                                }
                            }
                        @endphp
                        <form id="manuscriptForm" action="{{ route('create_test_result') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" id="formAction" value="">

                            @if (isset($manuscripts) && $manuscripts->isNotEmpty())
                                <input type="hidden" name="registration_id"
                                    value="{{ $manuscripts->first()->registration->tr04_reference_id ?? '' }}">
                            @endif

                                    <div>
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">
                                            <em class="icon ni ni-caret-left-fill"></em> Back
                                        </a>
                                    </div>
                                </div>
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
                                                        {{ optional($manuscripts->first()?->registration)->tr04_reference_id ?? 'N/A' }}
                                                    </td>
                                                    <td class="fw-bold text-muted w-25">Date:</td>
                                                    <td class="w-25">
                                                        <input type="date"
                                                            class="form-control form-control-sm bg-light border-0 shadow-none @error('test_date') is-invalid @enderror"
                                                            name="test_date"
                                                            min="{{ date('Y-m-d', strtotime('-15 days')) }}"
                                                            max="{{ date('Y-m-d') }}"
                                                            value="{{ old('test_date', $testDate ?? date('Y-m-d')) }}"
                                                            required>
                                                        @error('test_date')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-muted">No. of Samples:</td>
                                                    <td class="text-dark fw-semibold">
                                                        {{ optional($manuscripts->first()?->registration)->tr04_number_of_samples ?? 'N/A' }}
                                                    </td>
                                                    <td class="fw-bold text-muted">Sample Characteristics:</td>
                                                    <td class="text-dark fw-semibold">
                                                        {{ optional($manuscripts->first()?->registration->labSample)->m14_name ?? 'N/A' }}
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
                                                        {{ $manuscripts->first()->tr05_alloted_at ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-muted">QAO / JQAO / Analyst:</td>
                                                    <td class="text-dark fw-semibold">
                                                        {{ optional($manuscripts->first()?->allotedTo)->m06_name ?? 'N/A' }}
                                                    </td>
                                                    <td class="fw-bold text-muted">Technical Manager:</td>
                                                    <td class="text-dark fw-semibold">
                                                        {{ Session::get('role') === 'Manager' ? Session::get('name') : optional($manuscripts->first()?->allotedBy)->m06_name ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                 <!-- Test Results & Manuscript -->
                                <div class="mt-4 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold text-uppercase mb-0 text-primary">
                                            <em class="icon ni ni-layers"></em> Test Results & Manuscripts
                                        </h5>
                                        @php
                                            $manuscriptFile = $manuscripts->first()?->registration?->tr04_manuscript;
                                        @endphp
                                        <div class="d-flex gap-2 align-items-center">
                                            @if ($manuscriptFile)
                                                <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal"
                                                    data-bs-target="#pdfModal">
                                                    <em class="icon ni ni-eye me-1"></em> View Manuscript
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal"
                                                    data-bs-target="#uploadManuscriptDirectModal">
                                                    <em class="icon ni ni-upload me-1"></em> Upload New Copy
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal"
                                                    data-bs-target="#uploadManuscriptDirectModal">
                                                    <em class="icon ni ni-upload me-1"></em> Upload Manuscript
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @foreach ($manuscripts as $key => $manuscript)
                                        <div class="nk-block nk-block-lg card border-0 shadow-sm mb-4 test-card"
                                            data-main-sr="{{ $key + 1 }}">
                                            <div class="card-header bg-light border-bottom">
                                                <h5 class="title nk-block-title mb-0">
                                                    {{ $key + 1 }}. {{ $manuscript->test->m12_name ?? 'N/A' }}
                                                    <span class="text-muted fw-normal fs-6">
                                                        @if ($manuscript->standard)
                                                            ({{ $manuscript->standard->m15_method }})
                                                        @elseif(!empty($manuscript->test->standardsList) && count($manuscript->test->standardsList) > 0)
                                                            (No standard selected -
                                                            {{ count($manuscript->test->standardsList) }} available)
                                                        @else
                                                            (No standard for this test)
                                                        @endif
                                                    </span>
                                                    @if(!empty($manuscript->test->standardsList) && count($manuscript->test->standardsList) > 0)
                                                        <a href="#" class="choose-standard ms-2 text-decoration-none" data-manuscript-id="{{ $manuscript->m12_test_id }}" data-test-id="{{ $manuscript->test->m12_test_id }}">
                                                            <small><em class="icon ni ni-edit"></em> <span class="standard-label">{{ optional($manuscript->standard)->m15_method ?? 'Choose standard' }}</span></small>
                                                        </a>
                                                    @endif
                                                </h5>
                                                <input type="hidden" name="sample_tests[{{ $manuscript->m12_test_id }}][standard_id]" value="{{ old('sample_tests.' . $manuscript->m12_test_id . '.standard_id', optional($manuscript->standard)->m15_standard_id ?? '') }}">
                                                <small class="text-muted d-block mt-1">Write your manuscript part and
                                                    calculation below:</small>
                                            </div>
                                            <div class="card-inner p-2">
                                                @php
                                                    $existingTestResultForManuscript = $existingResults
                                                        ->where('m12_test_number', $manuscript->m12_test_number)
                                                        ->whereNull('m16_primary_test_id')
                                                        ->whereNull('m17_secondary_test_id')
                                                        ->first();
                                                    $manuscriptContent =
                                                        $existingTestResultForManuscript->tr07_manuscript_content ??
                                                        ($manuscript->m22_content ?? '');
                                                @endphp
                                                 <!-- Summernote Editor -->
                                                 <div class="mb-4">
                                                     <textarea class="summernote-basic form-control" name="test_calculation[{{ $manuscript->m12_test_number }}]">{!! old('test_calculation.' . $manuscript->m12_test_number, $manuscriptContent) !!}</textarea>
                                                 </div>

                                                <!-- Final Result Inputs -->
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                    <h6 class="title mb-0 text-dark">Final Exact Output</h6>
                                                </div>
                                                <table class="table table-bordered table-sm align-middle mb-0 w-100"
                                                    id="results_table_{{ $manuscript->m12_test_number }}">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 10%">Sr. No.</th>
                                                            <th style="width: 30%">Parameter / Variable</th>
                                                            <th style="width: 45%">Result Value</th>
                                                            <th style="width: 15%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $test = $manuscript->test;
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

                                                        @if ($primaryTests->isEmpty())
                                                            <!-- Main Test Row without primary tests -->
                                                            <tr class="test-main-row"
                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                data-test-number="{{ $test->m12_test_number }}">
                                                                <td>1</td>
                                                                <td class="fw-bold text-muted align-middle">
                                                                    {{ $test->m12_name ?? 'Final Result' }}</td>
                                                                <td>
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
                                                                        <button type="button"
                                                                            class="btn btn-outline-light btn-sm open-raw-entry"
                                                                            data-target-name="results[{{ $test->m12_test_number }}][test][result]"
                                                                            data-label="{{ $test->m12_name ?? 'Final Result' }}"
                                                                            title="Formula / Raw Entry">
                                                                            <em class="icon ni ni-calc"></em>
                                                                        </button>
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
                                                                    @error('results.' . $test->m12_test_number .
                                                                        '.test.result')
                                                                        <span
                                                                            class="text-danger small">{{ $message }}</span>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-warning btn-sm add-custom-field"
                                                                        data-test-id="{{ $test->m12_test_id }}"
                                                                        data-test-number="{{ $test->m12_test_number }}"
                                                                        data-type="test">
                                                                        <em class="icon ni ni-plus"></em> C
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <!-- Custom Fields for Main Test -->
                                                            @foreach ($customFields->where('m12_test_number', $test->m12_test_number)->whereNull('m16_primary_test_id')->whereNull('m17_secondary_test_id') as $cfIndex => $cf)
                                                                <tr class="custom-field-row"
                                                                    data-test-number="{{ $test->m12_test_number }}">
                                                                    <td>{{ $key + 1 }}.C{{ $cfIndex + 1 }}</td>
                                                                    <td class="ps-3 fw-bold text-dark">
                                                                        {{ $cf->tr08_field_name }}
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="hidden"
                                                                                name="results[{{ $test->m12_test_number }}][custom_fields][{{ $cf->tr08_custom_field_id }}][custom_field_id]"
                                                                                value="{{ $cf->tr08_custom_field_id }}">
                                                                            <input type="hidden"
                                                                                name="results[{{ $test->m12_test_number }}][custom_fields][{{ $cf->tr08_custom_field_id }}][value]"
                                                                                class="wysiwyg-hidden-val"
                                                                                value="{{ $cf->tr08_field_value }}">
                                                                            <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                                                                contenteditable="true"
                                                                                data-placeholder="Value">{!! $cf->tr08_field_value !!}</div>
                                                                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                            <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                            <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm border-0 bg-light"
                                                                                style="max-width: 80px;"
                                                                                name="results[{{ $test->m12_test_number }}][custom_fields][{{ $cf->tr08_custom_field_id }}][unit]"
                                                                                value="{{ $cf->tr08_field_unit }}"
                                                                                placeholder="Unit">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button"
                                                                            class="btn btn-outline-danger btn-sm remove-test-row"
                                                                            data-type="custom"
                                                                            data-id="{{ $cf->tr08_custom_field_id }}">
                                                                            <em class="icon ni ni-trash"></em>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <!-- Main Row (Always visible, contains add buttons) -->
                                                            <tr class="test-main-row"
                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                data-test-number="{{ $test->m12_test_number }}">
                                                                <td class="fw-bold align-middle">1</td>
                                                                <td class="fw-bold text-dark align-middle">
                                                                    {{ $test->m12_name ?? 'Test' }}
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button"
                                                                            class="btn btn-outline-primary btn-sm add-primary-test"
                                                                            data-test-id="{{ $test->m12_test_id }}"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-primary-tests="{{ $primaryTests->toJson() }}">
                                                                            <em class="icon ni ni-plus"></em> Add Primary
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-outline-warning btn-sm add-custom-field"
                                                                            data-test-id="{{ $test->m12_test_id }}"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-type="test">
                                                                            <em class="icon ni ni-plus"></em> Add Custom
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                                <td></td>
                                                            </tr>

                                                            <!-- Primary Test Rows (Only those with existing results) -->
                                                            @foreach ($primaryTests as $pIndex => $primaryTest)
                                                                @php
                                                                    $existingPrimaryResult = $existingTestResults
                                                                        ->where(
                                                                            'm16_primary_test_id',
                                                                            $primaryTest->m16_primary_test_id,
                                                                        )
                                                                        ->whereNull('m17_secondary_test_id')
                                                                        ->first();
                                                                    $hasSecondary =
                                                                        $primaryTest->secondaryTests &&
                                                                        $primaryTest->secondaryTests->isNotEmpty();
                                                                @endphp

                                                                @if (
                                                                    $existingPrimaryResult ||
                                                                        $existingTestResults->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->isNotEmpty() ||
                                                                        $existingTestResults->isEmpty() ||
                                                                        old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id) !== null)
                                                                    <tr class="primary-test-row"
                                                                        data-test-id="{{ $test->m12_test_id }}"
                                                                        data-test-number="{{ $test->m12_test_number }}"
                                                                        data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
                                                                        <td class="serial-col">1.{{ $pIndex + 1 }}</td>
                                                                        <td class="fw-bold text-muted align-middle ps-3">
                                                                            {{ $primaryTest->m16_name ?? 'N/A' }}
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group input-group-sm {{ $hasSecondary ? 'd-none' : '' }}">
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
                                                                                <button type="button"
                                                                                    class="btn btn-outline-light btn-sm open-raw-entry"
                                                                                    data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                                    data-label="{{ $primaryTest->m16_name ?? '' }}"
                                                                                    title="Formula / Raw Entry">
                                                                                    <em class="icon ni ni-calc"></em>
                                                                                </button>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm border-0 bg-light"
                                                                                    style="max-width: 80px;"
                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][unit]"
                                                                                    value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.unit', $existingPrimaryResult->tr07_unit ?? ($primaryTest->m16_unit ?? '')) }}"
                                                                                    placeholder="Unit">
                                                                            </div>
                                                                            @error('results.' . $test->m12_test_number .
                                                                                '.primary_tests.' .
                                                                                $primaryTest->m16_primary_test_id . '.result')
                                                                                <span
                                                                                    class="text-danger small">{{ $message }}</span>
                                                                            @enderror
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger btn-sm remove-test-row"
                                                                                    data-type="primary"
                                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                                    data-id="{{ $primaryTest->m16_primary_test_id }}">
                                                                                    <em class="icon ni ni-trash"></em>
                                                                                </button>
                                                                                @if ($hasSecondary)
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-success btn-sm add-secondary-test"
                                                                                        data-test-number="{{ $test->m12_test_number }}"
                                                                                        data-test-id="{{ $test->m12_test_id }}"
                                                                                        data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                        data-secondary-tests='{{ $primaryTest->secondaryTests->toJson() }}'>
                                                                                        <em class="icon ni ni-plus"></em>
                                                                                        Sec
                                                                                    </button>
                                                                                @endif
                                                                                <button type="button"
                                                                                    class="btn btn-outline-primary btn-sm add-custom-field"
                                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                    data-type="primary">
                                                                                    <em class="icon ni ni-plus"></em> C
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>

                                                                    @if ($hasSecondary)
                                                                        @foreach ($primaryTest->secondaryTests as $sIndex => $secondaryTest)
                                                                            @php
                                                                                $existingSecondaryResult = $existingTestResults
                                                                                    ->where(
                                                                                        'm16_primary_test_id',
                                                                                        $primaryTest->m16_primary_test_id,
                                                                                    )
                                                                                    ->where(
                                                                                        'm17_secondary_test_id',
                                                                                        $secondaryTest->m17_secondary_test_id,
                                                                                    )
                                                                                    ->first();
                                                                            @endphp

                                                                            @if ($existingSecondaryResult || $existingTestResults->isEmpty() || old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id) !== null)
                                                                                <tr class="secondary-test-row"
                                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                    data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}">
                                                                                    <td class="serial-col">
                                                                                        1.{{ $pIndex + 1 }}.{{ $sIndex + 1 }}
                                                                                    </td>
                                                                                    <td
                                                                                        class="ps-4 text-muted align-middle">
                                                                                        {{ $secondaryTest->m17_name ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td>
                                                                                        <div
                                                                                            class="input-group input-group-sm">
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
                                                                                            <button type="button"
                                                                                                class="btn btn-outline-light btn-sm open-raw-entry"
                                                                                                data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                                data-label="{{ $secondaryTest->m17_name ?? '' }}"
                                                                                                title="Formula / Raw Entry">
                                                                                                <em
                                                                                                    class="icon ni ni-calc"></em>
                                                                                            </button>
                                                                                            <input type="text"
                                                                                                class="form-control form-control-sm border-0 bg-light"
                                                                                                style="max-width: 80px;"
                                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][unit]"
                                                                                                value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.unit', $existingSecondaryResult->tr07_unit ?? ($secondaryTest->m17_unit ?? '')) }}"
                                                                                                placeholder="Unit">
                                                                                        </div>
                                                                                        @error('results.' .
                                                                                            $test->m12_test_number .
                                                                                            '.primary_tests.' .
                                                                                            $primaryTest->m16_primary_test_id .
                                                                                            '.secondary_tests.' .
                                                                                            $secondaryTest->m17_secondary_test_id
                                                                                            . '.result')
                                                                                            <span
                                                                                                class="text-danger small">{{ $message }}</span>
                                                                                        @enderror
                                                                                    </td>
                                                                                    <td>
                                                                                        <div
                                                                                            class="btn-group btn-group-sm">
                                                                                            <button type="button"
                                                                                                class="btn btn-outline-danger btn-sm remove-test-row"
                                                                                                data-type="secondary"
                                                                                                data-id="{{ $secondaryTest->m17_secondary_test_id }}">
                                                                                                <em
                                                                                                    class="icon ni ni-trash"></em>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn btn-outline-primary btn-sm add-custom-field"
                                                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                                                data-test-number="{{ $test->m12_test_number }}"
                                                                                                data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                                data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}"
                                                                                                data-type="secondary">
                                                                                                <em
                                                                                                    class="icon ni ni-plus"></em>
                                                                                                C
                                                                                            </button>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>

                                                                                <!-- Custom Fields for Secondary Test -->
                                                                                @foreach ($customFields->where('m12_test_number', $test->m12_test_number)->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->where('m17_secondary_test_id', $secondaryTest->m17_secondary_test_id) as $cf)
                                                                                    <tr class="custom-field-row"
                                                                                        data-test-number="{{ $test->m12_test_number }}"
                                                                                        data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                        data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}">
                                                                                        <td class="serial-col"></td>
                                                                                        <td
                                                                                            class="ps-5 text-muted align-middle">
                                                                                            {{ $cf->tr08_field_name }}
                                                                                        </td>
                                                                                        <td>
                                                                                            <div
                                                                                                class="input-group input-group-sm">
                                                                                                <input type="hidden"
                                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][custom_field_id]"
                                                                                                    value="{{ $cf->tr08_custom_field_id }}">
                                                                                                <input type="hidden"
                                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][value]"
                                                                                                    class="wysiwyg-hidden-val"
                                                                                                    value="{{ $cf->tr08_field_value }}">
                                                                                                <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                                                                                    contenteditable="true"
                                                                                                    data-placeholder="Value">{!! $cf->tr08_field_value !!}</div>
                                                                                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                                                <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm border-0 bg-light"
                                                                                                    style="max-width: 80px;"
                                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][unit]"
                                                                                                    value="{{ $cf->tr08_field_unit }}"
                                                                                                    placeholder="Unit">
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <button type="button"
                                                                                                class="btn btn-outline-danger btn-sm remove-test-row"
                                                                                                data-type="custom"
                                                                                                data-id="{{ $cf->tr08_custom_field_id }}">
                                                                                                <em
                                                                                                    class="icon ni ni-trash"></em>
                                                                                            </button>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                    <!-- Custom Fields for Primary Test -->
                                                                    @foreach ($customFields->where('m12_test_number', $test->m12_test_number)->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->whereNull('m17_secondary_test_id') as $cf)
                                                                        <tr class="custom-field-row"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
                                                                            <td class="serial-col"></td>
                                                                            <td class="ps-4 text-muted align-middle">
                                                                                {{ $cf->tr08_field_name }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group input-group-sm">
                                                                                    <input type="hidden"
                                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][custom_field_id]"
                                                                                        value="{{ $cf->tr08_custom_field_id }}">
                                                                                    <input type="hidden"
                                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][value]"
                                                                                        class="wysiwyg-hidden-val"
                                                                                        value="{{ $cf->tr08_field_value }}">
                                                                                    <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                                                                        contenteditable="true"
                                                                                        data-placeholder="Value">{!! $cf->tr08_field_value !!}</div>
                                                                                    <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                                    <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                                    <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm border-0 bg-light"
                                                                                        style="max-width: 80px;"
                                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][unit]"
                                                                                        value="{{ $cf->tr08_field_unit }}"
                                                                                        placeholder="Unit">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger btn-sm remove-test-row"
                                                                                    data-type="custom"
                                                                                    data-id="{{ $cf->tr08_custom_field_id }}">
                                                                                    <em class="icon ni ni-trash"></em>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                    </div>
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
                    <button type="button" onclick="submitForm('DRAFT')" class="btn btn-outline-primary">
                        <em class="icon ni ni-file-text"></em> Save as Draft
                    </button>
                @endif
                @if (Session::get('role') === 'DEO')
                    <button type="button" onclick="submitForm('RESULTED')" class="btn btn-primary">
                        <em class="icon ni ni-check-circle"></em> Save & Complete
                    </button>
                @else
                    <button type="button" onclick="submitForm('SUBMITTED')" class="btn btn-primary">
                        <em class="icon ni ni-check-circle"></em> Save & Complete
                    </button>
                @endif
            </div>
    <!-- Action Buttons End -->
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

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#symbolPickerPopup, .btn-open-symbols, .wysiwyg-editor-input').length) {
                $('#symbolPickerPopup').addClass('d-none');
            }
        });

        // ContentEditable Input Events & Sync
        $(document).on('input', '.wysiwyg-editor-input', function() {
            const html = this.innerHTML;
            const $hidden = $(this).closest('.input-group').find('.wysiwyg-hidden-val');
            if ($hidden.length) {
                $hidden.val(html);
            }
            const syncTarget = $(this).data('sync');
            if (syncTarget) {
                const targetEl = document.getElementById(syncTarget);
                if (targetEl) {
                    targetEl.innerHTML = html;
                }
            }
            if (this.innerText.trim() !== '') {
                $(this).removeClass('is-invalid');
            }
        });

        // Prevent newline on Enter and handle keyboard shortcuts
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

        // Clean plain text paste
        $(document).on('paste', '.wysiwyg-editor-input', function(e) {
            e.preventDefault();
            const text = (e.originalEvent || e).clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text.replace(/[\r\n]+/g, ' '));
            $(this).trigger('input');
        });

        function submitForm(actionValue) {
            const form = document.getElementById('manuscriptForm');
            
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



        $(document).ready(function() {
            // Focus and highlight empty date field
            const $testDate = $('input[name="test_date"]');
            const $perfDate = $('input[name="performance_date"]');
            
            if (!$testDate.val()) {
                $testDate.addClass('is-invalid shadow-sm border-danger').focus();
            } else if (!$perfDate.val()) {
                $perfDate.addClass('is-invalid shadow-sm border-danger').focus();
            }

            // Remove highlight on input
            $testDate.on('change input', function() { $(this).removeClass('is-invalid shadow-sm border-danger'); });
            $perfDate.on('change input', function() { $(this).removeClass('is-invalid shadow-sm border-danger'); });

            let currentPrimaryModalData = [];
            let currentPrimaryContext = {};
            let currentSecondaryModalData = [];
            let currentSecondaryContext = {};

            // Add Primary Test
            $('.add-primary-test').on('click', function() {
                const testId = $(this).data('test-id');
                const testNumber = $(this).data('test-number');
                const primaryTests = $(this).data('primary-tests');
                const tableBody = $(`#results_table_${testNumber} tbody`);

                currentPrimaryContext = { testId, testNumber, primaryTests, tableBody };
                const availablePrimaryTests = primaryTests.filter(pt =>
                    tableBody.find(`.primary-test-row[data-primary-test-id="${pt.m16_primary_test_id}"]`).length === 0
                );
                currentPrimaryModalData = availablePrimaryTests;

                const primaryTestList = document.getElementById('primaryTestList');
                primaryTestList.innerHTML = '';
                $('#selectAllPrimaryTests').prop('checked', false);
                $('#selectedPrimaryCount').text('0 selected');

                if (availablePrimaryTests.length === 0) {
                    primaryTestList.innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                No more primary tests available for this test.
                            </td>
                        </tr>
                    `;
                } else {
                    availablePrimaryTests.forEach(pt => {
                        const hasSecondary = pt.secondary_tests && pt.secondary_tests.length > 0;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input chk-primary-test" data-id="${pt.m16_primary_test_id}">
                            </td>
                            <td>
                                <strong>${pt.m16_name}</strong>
                                ${hasSecondary ? '<br><small class="text-info">Has secondary tests available</small>' : ''}
                            </td>
                            <td>
                                <small>${pt.m16_unit || 'N/A'}</small>
                            </td>
                        `;
                        primaryTestList.appendChild(row);
                    });
                }

                $('#primaryTestModal').modal('show');
            });

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
                $('#selectedPrimaryCount').text(`${count} selected`);
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
                const { testNumber, tableBody } = currentPrimaryContext;

                selectedCheckboxes.forEach(chk => {
                    const pId = chk.getAttribute('data-id');
                    const pt = currentPrimaryModalData.find(t => t.m16_primary_test_id.toString() === pId.toString());
                    if (pt && tableBody.find(`.primary-test-row[data-primary-test-id="${pt.m16_primary_test_id}"]`).length === 0) {
                        appendPrimaryTestRowToManuscript(testNumber, pt, tableBody);
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

                updateSerialNumbers(testNumber);
            });

            function appendPrimaryTestRowToManuscript(testNumber, pt, tableBody) {
                const testMainRow = tableBody.find('.test-main-row');
                const mainTestName = testMainRow.length ? testMainRow.find('td:nth-child(2)').text().trim().toLowerCase() : '';
                const ptNameLower = (pt.m16_name || '').toLowerCase();
                const isAzo = mainTestName.includes('banned amines') || mainTestName.includes('aryl amine') || mainTestName.includes('azo') || ptNameLower.includes('azo') || ptNameLower.includes('amine');
                const defaultResultVal = isAzo ? 'Not Detected' : '';

                const rowHtml = `
                    <tr class="primary-test-row" data-test-number="${testNumber}" data-primary-test-id="${pt.m16_primary_test_id}">
                        <td class="serial-col"></td>
                        <td class="fw-bold text-muted align-middle ps-3">${pt.m16_name}</td>
                        <td>
                            <div class="input-group input-group-sm ${pt.secondary_tests && pt.secondary_tests.length > 0 ? 'd-none' : ''}">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][test_id]" value="${testNumber}">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][primary_test_id]" value="${pt.m16_primary_test_id}">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][result]" class="wysiwyg-hidden-val" value="${defaultResultVal}">
                                <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input" contenteditable="true" data-placeholder="Enter result value">${defaultResultVal}</div>
                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                <button type="button" class="btn btn-outline-light btn-sm open-raw-entry" data-target-name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][result]" data-label="${pt.m16_name}" title="Formula / Raw Entry"><em class="icon ni ni-calc"></em></button>
                                <input type="text" class="form-control form-control-sm border-0 bg-light web-sync-val" style="max-width: 80px;" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][unit]" value="${pt.m16_unit || ''}" placeholder="Unit">
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="primary" data-test-number="${testNumber}" data-id="${pt.m16_primary_test_id}">
                                    <em class="icon ni ni-trash"></em>
                                </button>
                                ${pt.secondary_tests && pt.secondary_tests.length > 0 ? `
                                    <button type="button" class="btn btn-outline-success btn-sm add-secondary-test" 
                                        data-test-number="${testNumber}" 
                                        data-primary-test-id="${pt.m16_primary_test_id}" 
                                        data-secondary-tests='${JSON.stringify(pt.secondary_tests)}'>
                                        <em class="icon ni ni-plus"></em> Sec
                                    </button>` : ''}
                                <button type="button" class="btn btn-outline-primary btn-sm add-custom-field" 
                                    data-test-number="${testNumber}" 
                                    data-primary-test-id="${pt.m16_primary_test_id}" 
                                    data-type="primary">
                                    <em class="icon ni ni-plus"></em> C
                                </button>
                            </div>
                        </td>
                    </tr>`;

                tableBody.append(rowHtml);
            }

            // Add Custom Field
            $(document).on('click', '.add-custom-field', function() {
                const type = $(this).data('type');
                const testNumber = $(this).data('test-number');
                const pId = $(this).data('primary-test-id');
                const sId = $(this).data('secondary-test-id');
                const tableBody = $(`#results_table_${testNumber} tbody`);

                Swal.fire({
                    title: 'Custom Field Name',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Add',
                    showLoaderOnConfirm: true,
                    preConfirm: (name) => {
                        if (!name) {
                            Swal.showValidationMessage('Name is required');
                        }
                        return name;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const fieldName = result.value;
                        const timestamp = Date.now();
                        let namePrefix = `results[${testNumber}]`;
                        let rowClass = 'custom-field-row';
                        let labelClass = 'ps-3 fw-bold text-dark';
                        let attr = `data-test-number="${testNumber}"`;

                        if (type === 'primary') {
                            namePrefix += `[primary_tests][${pId}]`;
                            labelClass = 'ps-4 text-muted';
                            attr += ` data-primary-test-id="${pId}"`;
                        } else if (type === 'secondary') {
                            namePrefix += `[primary_tests][${pId}][secondary_tests][${sId}]`;
                            labelClass = 'ps-5 text-muted';
                            attr +=
                                ` data-primary-test-id="${pId}" data-secondary-test-id="${sId}"`;
                        }

                        const syncId = `custom_${timestamp}`;
                        const rowHtml = `
                            <tr class="${rowClass}" ${attr}>
                                <td class="serial-col"></td>
                                <td class="${labelClass}">${fieldName}</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="hidden" name="${namePrefix}[custom_fields][new_${timestamp}][label]" value="${fieldName}">
                                        <input type="hidden" name="${namePrefix}[custom_fields][new_${timestamp}][value]" class="wysiwyg-hidden-val" value="">
                                        <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input" contenteditable="true" data-placeholder="Value"></div>
                                        <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                        <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                        <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                        <input type="text" class="form-control form-control-sm border-0 bg-light" style="max-width: 80px;" name="${namePrefix}[custom_fields][new_${timestamp}][unit]" placeholder="Unit">
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="custom" data-sync-id="${syncId}">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </td>
                            </tr>`;

                        if (type === 'test') {
                            tableBody.append(rowHtml);
                        } else if (type === 'primary') {
                            $(`.primary-test-row[data-primary-test-id="${pId}"]`).after(rowHtml);
                        } else if (type === 'secondary') {
                            $(`.secondary-test-row[data-primary-test-id="${pId}"][data-secondary-test-id="${sId}"]`)
                                .after(rowHtml);
                        }

                        updateSerialNumbers(testNumber);
                    }
                });
            });

            // Remove Row
            $(document).on('click', '.remove-test-row', function() {
                const row = $(this).closest('tr');
                const testNumber = row.data('test-number');
                const syncId = $(this).data('sync-id');
                const type = $(this).data('type');
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#fe453e',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        updateSerialNumbers(testNumber);
                    }
                });
            });

            function updateSerialNumbers(testNumber) {
                let pCount = 0;
                $(`#results_table_${testNumber} tbody .primary-test-row`).each(function() {
                    pCount++;
                    $(this).find('.serial-col').text(`1.${pCount}`);
                    
                    const pId = $(this).data('primary-test-id');
                    let sCount = 0;
                    $(`#results_table_${testNumber} tbody .secondary-test-row[data-primary-test-id="${pId}"]`).each(function() {
                        sCount++;
                        $(this).find('.serial-col').text(`1.${pCount}.${sCount}`);
                    });
                });
            }

            // Standards Modal
            $(document).on('click', '.select-standard-btn', function() {
                const testId = $(this).data('test-id');
                loadStandards(testId);
                $('#standardModal').modal('show');
            });

            function loadStandards(testId) {
                $.ajax({
                    url: `{{ url('get-standards') }}/${testId}`,
                    type: 'GET',
                    success: function(standards) {
                        $('#standard-list').empty();
                        standards.forEach(function(std) {
                            $('#standard-list').append(`
                                <li class="list-group-item standard-item" data-id="${std.id}" style="cursor: pointer;">
                                    ${std.name}
                                </li>
                            `);
                        });
                    },
                    error: function() {
                        $('#standard-list').html('<li class="list-group-item text-danger">Error loading standards.</li>');
                    }
                });
            }

            // Add Secondary Test
            $(document).on('click', '.add-secondary-test', function() {
                const testNumber = $(this).data('test-number');
                const pId = $(this).data('primary-test-id');
                const secondaryTests = $(this).data('secondary-tests') || [];
                const tableBody = $(`#results_table_${testNumber} tbody`);

                currentSecondaryContext = { testNumber, pId, secondaryTests, tableBody };
                const availableSecondaryTests = secondaryTests.filter(st =>
                    tableBody.find(`.secondary-test-row[data-secondary-test-id="${st.m17_secondary_test_id}"]`).length === 0
                );
                currentSecondaryModalData = availableSecondaryTests;

                const secondaryTestList = document.getElementById('secondaryTestList');
                secondaryTestList.innerHTML = '';
                $('#selectAllSecondaryTests').prop('checked', false);
                $('#selectedSecondaryCount').text('0 selected');

                if (availableSecondaryTests.length === 0) {
                    Swal.fire('Info', 'No more secondary tests available for this primary test.', 'info');
                    return;
                }

                availableSecondaryTests.forEach(st => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input chk-secondary-test" data-id="${st.m17_secondary_test_id}">
                        </td>
                        <td>
                            <strong>${st.m17_name}</strong>
                        </td>
                        <td>
                            <small>${st.m17_unit || 'N/A'}</small>
                        </td>
                    `;
                    secondaryTestList.appendChild(row);
                });

                $('#secondaryTestModal').modal('show');
            });

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
                $('#selectedSecondaryCount').text(`${count} selected`);
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
                const { testNumber, pId, tableBody } = currentSecondaryContext;

                selectedCheckboxes.forEach(chk => {
                    const sId = chk.getAttribute('data-id');
                    const st = currentSecondaryModalData.find(t => t.m17_secondary_test_id.toString() === sId.toString());
                    if (st && tableBody.find(`.secondary-test-row[data-secondary-test-id="${st.m17_secondary_test_id}"]`).length === 0) {
                        appendSecondaryTestRowToManuscript(testNumber, pId, st, tableBody);
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

                updateSerialNumbers(testNumber);
            });

            function appendSecondaryTestRowToManuscript(testNumber, pId, st, tableBody) {
                const testMainRow = tableBody.find('.test-main-row');
                const mainTestName = testMainRow.length ? testMainRow.find('td:nth-child(2)').text().trim().toLowerCase() : '';
                const isAzo = mainTestName.includes('banned amines') || mainTestName.includes('aryl amine') || mainTestName.includes('azo');
                const defaultResultVal = isAzo ? 'Not Detected' : '';

                const rowHtml = `
                    <tr class="secondary-test-row" data-test-number="${testNumber}" data-primary-test-id="${pId}" data-secondary-test-id="${st.m17_secondary_test_id}">
                        <td class="serial-col"></td>
                        <td class="ps-4 text-muted align-middle">- ${st.m17_name}</td>
                        <td>
                            <div class="result-input-group input-group input-group-sm">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][test_id]" value="${testNumber}">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][primary_test_id]" value="${pId}">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][secondary_test_id]" value="${st.m17_secondary_test_id}">
                                <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][result]" class="wysiwyg-hidden-val" value="${defaultResultVal}">
                                <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input" contenteditable="true" data-placeholder="Enter result value">${defaultResultVal}</div>
                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                <button type="button" class="btn btn-outline-light btn-sm btn-wysiwyg-cmd" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                <button type="button" class="btn btn-outline-light btn-sm btn-open-symbols" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                <button type="button" class="btn btn-outline-light btn-sm open-raw-entry" data-target-name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][result]" data-label="${st.m17_name}" title="Formula / Raw Entry"><em class="icon ni ni-calc"></em></button>
                                <input type="text" class="form-control form-control-sm border-0 bg-light web-sync-val" style="max-width: 80px;" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][unit]" value="${st.m17_unit || ''}" placeholder="Unit">
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="secondary" data-test-number="${testNumber}" data-id="${st.m17_secondary_test_id}">
                                    <em class="icon ni ni-trash"></em>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm add-custom-field" 
                                    data-test-number="${testNumber}" 
                                    data-primary-test-id="${pId}" 
                                    data-secondary-test-id="${st.m17_secondary_test_id}" 
                                    data-type="secondary">
                                    <em class="icon ni ni-plus"></em> C
                                </button>
                            </div>
                        </td>
                    </tr>`;

                let lastRow = tableBody.find(`.primary-test-row[data-primary-test-id="${pId}"]`);
                tableBody.find(`tr[data-primary-test-id="${pId}"]`).each(function() {
                    lastRow = $(this);
                });
                lastRow.after(rowHtml);

                // Hide primary test input group since it now has secondary tests
                const primaryInputGroup = tableBody.find(`.primary-test-row[data-primary-test-id="${pId}"] .input-group`);
                if (primaryInputGroup.length) {
                    primaryInputGroup.addClass('d-none');
                    const primaryInput = primaryInputGroup.find('.wysiwyg-hidden-val');
                    if (primaryInput.length) primaryInput.removeClass('is-invalid');
                }
            }
        });


        // Raw Entry / Formula Logic
        let currentTargetInput = null;
        $(document).on('click', '.open-raw-entry', function() {
            const targetName = $(this).data('target-name');
            const label = $(this).data('label');
            currentTargetInput = $(`input[name="${targetName}"]`);

            $('#formulaHeader').html(
                `<h6 class="text-primary">${label}</h6><p class="small text-muted">Enter readings to calculate result</p>`
            );
            $('#rawEntryModal').modal('show');
            generateReadingRows();
        });

        $(document).on('input change', '#numberOfReadings', function() {
            generateReadingRows();
        });

        $(document).on('change', '#aggregationType', function() {
            calculateFormula();
        });

        function generateReadingRows() {
            const count = parseInt($('#numberOfReadings').val()) || 1;
            const currentRows = $('#readingsBody tr');
            const currentCount = currentRows.length;
            
            if (count > currentCount) {
                // Add new rows
                for (let i = currentCount + 1; i <= count; i++) {
                    $('#readingsBody').append(`<tr><td>Reading ${i}</td><td><input type="number" class="form-control form-control-sm reading-val" value="0"></td></tr>`);
                }
            } else if (count < currentCount && count > 0) {
                // Remove excess rows
                for (let i = currentCount; i > count; i--) {
                    $('#readingsBody tr:last-child').remove();
                }
            }
            calculateFormula();
        }

        $(document).on('input', '.reading-val', function() {
            calculateFormula();
        });

        function calculateFormula() {
            const type = $('#aggregationType').val();
            let values = [];
            $('.reading-val').each(function() {
                values.push(parseFloat($(this).val()) || 0);
            });

            let result = 0;
            if (values.length > 0) {
                if (type === 'AVERAGE') {
                    const sum = values.reduce((a, b) => a + b, 0);
                    result = sum / values.length;
                } else if (type === 'MAX') {
                    result = Math.max(...values);
                } else if (type === 'MIN') {
                    result = Math.min(...values);
                } else if (type === 'SD') {
                    const mean = values.reduce((a, b) => a + b, 0) / values.length;
                    const sqDiffs = values.map(v => Math.pow(v - mean, 2));
                    const avgSqDiff = sqDiffs.reduce((a, b) => a + b, 0) / values.length;
                    result = Math.sqrt(avgSqDiff);
                }
            }
            $('#calculatedResult').text(result.toFixed(2));
        }

        $(document).on('click', '#applyCalculatedResult', function() {
            const val = $('#calculatedResult').text();
            if (currentTargetInput && currentTargetInput.length > 0) {
                currentTargetInput.val(val);
                const $wysiwyg = currentTargetInput.closest('.input-group').find('.wysiwyg-editor-input');
                if ($wysiwyg.length) {
                    $wysiwyg.html(val).trigger('input');
                } else {
                    currentTargetInput.trigger('input');
                }
                $('#rawEntryModal').modal('hide');
            }
        });
        // Display Laravel Validation Errors for both static and dynamically restored fields
        @if($errors->any())
            const validationErrors = @json($errors->toArray());
            setTimeout(() => {
                Object.keys(validationErrors).forEach(field => {
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
                        const inputGroup = inputElement.closest('.result-input-group') || inputElement.closest('.input-group');
                        const wysiwygDiv = inputGroup ? inputGroup.querySelector('.wysiwyg-editor-input') : null;
                        if (wysiwygDiv) {
                            wysiwygDiv.classList.add('is-invalid');
                        } else {
                            inputElement.classList.add('is-invalid');
                        }
                        
                        let container = inputElement.closest('td') || inputElement.parentElement;
                        if (container && !container.innerHTML.includes(validationErrors[field][0])) {
                            const errorSpan = document.createElement('span');
                            errorSpan.className = 'text-danger small d-block mt-1 validation-error-msg';
                            errorSpan.innerText = validationErrors[field][0];
                            
                            if (inputGroup && inputGroup.parentElement) {
                                inputGroup.parentElement.appendChild(errorSpan);
                            } else {
                                container.appendChild(errorSpan);
                            }
                        }
                    });
                });
            }, 500); 
        @endif
    </script>

    </form>
    </div>
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
                    @if (isset($manuscripts) && $manuscripts->first()?->registration?->tr04_manuscript)
                        @php
                            $manuscriptPath = $manuscripts->first()->registration->tr04_manuscript;
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
                <form action="{{ route('testresult_upload', optional($manuscripts->first()?->registration)->tr04_reference_id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="redirect_to_result" value="1">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white"><em class="icon ni ni-upload me-1"></em> Upload Paper Manuscript</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Upload photo or PDF of the calculation paper completed by the analyst for reference: <strong>{{ optional($manuscripts->first()?->registration)->tr04_reference_id ?? '' }}</strong>.</p>
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

    @php
        $id = optional($manuscripts->first()?->registration)->tr04_reference_id;
    @endphp
    <!-- Upload Modal - Fixed Version -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Test Result Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('testresult_upload', $id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Document</label>
                            <input type="file" name="result_file" class="form-control" required
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Allowed formats: PDF, JPG, JPEG, PNG, DOC, DOCX</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <em class="icon ni ni-upload"></em> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Standard Selection Modal -->
    <div class="modal fade" id="standardModal" tabindex="-1" aria-labelledby="standardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="standardModalLabel">Select Standard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group" id="standard-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Test Selection Modal -->
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
                                <tr>
                                    <th>Test Name</th>
                                    <th>Unit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="primaryTestList">
                                <!-- Dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Entry (Formula) Modal -->
    <div class="modal fade" id="rawEntryModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Result Calculation (Formula)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="formulaHeader" class="mb-4">
                        <!-- Formula info populated here -->
                    </div>

                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-auto">
                            <label class="form-label mb-0">Number of Readings:</label>
                        </div>
                        <div class="col-auto" style="width: 100px;">
                            <input type="number" id="numberOfReadings" class="form-control form-control-sm"
                                value="1" min="1" max="50">
                        </div>
                        <div class="col-auto ms-auto">
                            <label class="form-label mb-0">Aggregation Type:</label>
                        </div>
                        <div class="col-auto">
                            <select id="aggregationType" class="form-select form-select-sm">
                                <option value="AVERAGE">Average</option>
                                <option value="MAX">Maximum</option>
                                <option value="MIN">Minimum</option>
                                <option value="SD">Std. Deviation</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr id="readingsHeaderRow"></tr>
                            </thead>
                            <tbody id="readingsBody"></tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center py-2">
                        <div>
                            <strong>Calculated Result:</strong>
                            <span id="calculatedResult" class="fs-4 ms-2">0</span>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="applyCalculatedResult">
                            Apply Result
                        </button>
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
        .wysiwyg-editor-input {
            min-height: 31px;
            max-height: 31px;
            line-height: 1.5;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            outline: none;
            cursor: text;
            text-align: left;
        }
        .wysiwyg-editor-input:focus {
            background-color: #fff !important;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .wysiwyg-editor-input[data-placeholder]:empty:before {
            content: attr(data-placeholder);
            color: #999;
            pointer-events: none;
        }
        .wysiwyg-editor-input sup {
            font-size: 75%;
            vertical-align: super;
            line-height: 0;
        }
        .wysiwyg-editor-input sub {
            font-size: 75%;
            vertical-align: sub;
            line-height: 0;
        }
        #symbolPickerPopup {
            box-shadow: 0 10px 30px rgba(0,0,0,0.18) !important;
            border-radius: 8px !important;
            font-family: inherit;
            border: 1px solid #dbdfea !important;
        }
        #symbolPickerPopup .sym-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #8094ae;
            margin-bottom: 6px;
        }
        #symbolPickerPopup .symbol-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        #symbolPickerPopup .symbol-grid-wide {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        #symbolPickerPopup .sym-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            width: 32px;
            padding: 0;
            font-size: 14px;
            font-weight: 600;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #364a63;
            background-color: #f5f6fa;
            border: 1px solid #e5e9f2;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
            box-sizing: border-box;
            text-decoration: none;
            margin: 2px;
        }
        #symbolPickerPopup .sym-btn:hover {
            background-color: #0965f1;
            color: #ffffff;
            border-color: #0965f1;
            box-shadow: 0 2px 5px rgba(9,101,241,0.25);
        }
        #symbolPickerPopup .sym-btn-info {
            background-color: #eef6ff;
            color: #0965f1;
            border-color: #cce4ff;
            padding: 0 8px;
            width: auto;
            min-width: 42px;
            height: 30px;
            font-size: 13px;
        }
        #symbolPickerPopup .sym-btn-info:hover {
            background-color: #0965f1;
            color: #ffffff;
            border-color: #0965f1;
        }
        .btn-wysiwyg-cmd, .btn-open-symbols {
            padding-left: 6px !important;
            padding-right: 6px !important;
        }
    </style>

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
