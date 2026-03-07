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

                            <!-- ========================================== -->
                            <!-- WEB VIEW (Screen Only)                     -->
                            <!-- ========================================== -->
                            <div class="d-print-none">
                                <!-- Header Section -->
                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <div>
                                        <h4 class="fw-bold text-uppercase mb-0">Manuscript / Datasheet</h4>
                                        {{-- <small class="text-muted">Laboratory Information Management System</small> --}}
                                    </div>
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
                                                            class="form-control form-control-sm bg-light border-0 shadow-none web-sync-val @error('test_date') is-invalid @enderror"
                                                            data-sync="print_test_date" name="test_date"
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
                                                            class="form-control form-control-sm bg-light border-0 shadow-none web-sync-val @error('performance_date') is-invalid @enderror"
                                                            data-sync="print_performance_date" name="performance_date"
                                                            value="{{ old('performance_date', $performanceDate ?? '') }}"
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
                                        <!-- Button to Open Modal -->
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#uploadModal">
                                            <em class="icon ni ni-upload"></em> Upload Manuscript
                                        </button>
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
                                                </h5>
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
                                                                        <input type="text"
                                                                            class="form-control form-control-sm bg-light web-sync-val @error('results.' . $test->m12_test_number . '.test.result') is-invalid @enderror"
                                                                            data-sync="print_res_t_{{ $test->m12_test_number }}"
                                                                            name="results[{{ $test->m12_test_number }}][test][result]"
                                                                            value="{{ old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') }}"
                                                                            placeholder="Enter result value"
                                                                            autocomplete="off">
                                                                        <button type="button"
                                                                            class="btn btn-outline-light btn-sm open-raw-entry"
                                                                            data-target-name="results[{{ $test->m12_test_number }}][test][result]"
                                                                            data-label="{{ $test->m12_name ?? 'Final Result' }}">
                                                                            <em class="icon ni ni-calc"></em>
                                                                        </button>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm bg-light web-sync-val"
                                                                            data-sync="print_unt_t_{{ $test->m12_test_number }}"
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
                                                                            <input type="text"
                                                                                class="form-control form-control-sm border-0 bg-light"
                                                                                name="results[{{ $test->m12_test_number }}][custom_fields][{{ $cf->tr08_custom_field_id }}][value]"
                                                                                value="{{ $cf->tr08_field_value }}"
                                                                                placeholder="Value">
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
                                                                        $existingTestResults->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->isNotEmpty())
                                                                    <tr class="primary-test-row"
                                                                        data-test-id="{{ $test->m12_test_id }}"
                                                                        data-test-number="{{ $test->m12_test_number }}"
                                                                        data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
                                                                        <td class="serial-col">1.{{ $pIndex + 1 }}</td>
                                                                        <td class="fw-bold text-muted align-middle ps-3">
                                                                            {{ $primaryTest->m16_name ?? 'N/A' }}
                                                                        </td>
                                                                        <td>
                                                                            <div
                                                                                class="input-group input-group-sm {{ $hasSecondary ? 'd-none' : '' }}">
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
                                                                                    class="form-control form-control-sm border-0 bg-light @error('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result') is-invalid @enderror"
                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                                    value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result', $existingPrimaryResult->tr07_result ?? '') }}"
                                                                                    placeholder="Enter result value"
                                                                                    autocomplete="off">
                                                                                <button type="button"
                                                                                    class="btn btn-outline-light btn-sm open-raw-entry"
                                                                                    data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                                    data-label="{{ $primaryTest->m16_name ?? '' }}">
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

                                                                            @if ($existingSecondaryResult)
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
                                                                                            <input type="text"
                                                                                                class="form-control form-control-sm border-0 bg-light @error('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result') is-invalid @enderror"
                                                                                                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                                value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result', $existingSecondaryResult->tr07_result ?? '') }}"
                                                                                                placeholder="Enter result value"
                                                                                                autocomplete="off">
                                                                                            <button type="button"
                                                                                                class="btn btn-outline-light btn-sm open-raw-entry"
                                                                                                data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                                data-label="{{ $secondaryTest->m17_name ?? '' }}">
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
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm border-0 bg-light"
                                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][value]"
                                                                                                    value="{{ $cf->tr08_field_value }}"
                                                                                                    placeholder="Value">
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
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm border-0 bg-light"
                                                                                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][custom_fields][{{ $cf->tr08_custom_field_id }}][value]"
                                                                                        value="{{ $cf->tr08_field_value }}"
                                                                                        placeholder="Value">
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
        </div>
    </div>
    </div>
    <!-- END WEB VIEW -->

    <!-- ============================================== -->
    <!-- PRINT VIEW (Strict PDF Format)                 -->
    <!-- ============================================== -->
    <div class="d-none d-print-block">
        <table class="print-main-table mx-auto w-100"
            style="font-family: Arial, sans-serif; font-size: 13px; border-collapse: collapse; border: none; max-width: 900px;">
            <thead class="print-header">
                <tr>
                    <th style="padding-bottom: 20px;">
                        <div class="position-relative text-center mx-auto" style="font-weight: normal;">
                            <div class="position-absolute" style="right: 0; top: 0; font-size: 13px; font-weight: bold;">
                                <div style="text-decoration: underline;">04/24(Q)/02</div>
                            </div>
                            <div style="font-size: 16px; font-weight: bold; margin-bottom: 2px; margin-top: 10px;">
                                TEXTILES COMMITTEE LABORATORIES</div>
                            <div style="font-size: 14px; font-weight: bold;">MUMBAI</div>

                            <div
                                style="display: inline-block; border-top: 1px solid #000; border-bottom: 2px solid #000; padding: 2px 0; margin-top: 8px; width: 400px;">
                                <div style="font-size: 14px; font-weight: bold; letter-spacing: 0.5px;">
                                    MANUSCRIPT / DATASHEET</div>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>
                        <!-- Print Form Table 1 (Details) -->
                        <table
                            style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-family: Arial, sans-serif; font-size: 13px; margin-bottom: 0;">
                            <tbody>
                                <tr>
                                    <td
                                        style="width: 25%; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Test Report No:</td>
                                    <td
                                        style="width: 50%; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px; font-weight: bold;">
                                        {{ optional($manuscripts->first()?->registration)->tr04_reference_id ?? '' }}
                                    </td>
                                    <td
                                        style="width: 10%; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Date:</td>
                                    <td
                                        style="width: 15%; border-bottom: 1px solid #000; padding: 5px 8px; font-weight: bold;">
                                        <span id="print_test_date"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Date of allotment of sample:</td>
                                    <td colspan="3" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                                        {{ $manuscripts->first()->tr05_alloted_at ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Sample Characteristics:</td>
                                    <td colspan="3" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                                        {{ optional($manuscripts->first()?->registration->labSample)->m14_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Date of Performance of Tests:</td>
                                    <td colspan="3"
                                        style="border-bottom: 1px solid #000; padding: 5px 8px; font-weight: bold;">
                                        <span id="print_performance_date"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        No of samples:</td>
                                    <td colspan="3" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                                        {{ optional($manuscripts->first()?->registration)->tr04_number_of_samples ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        QAO/JQAO/Analyst:</td>
                                    <td colspan="3" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                                        {{ optional($manuscripts->first()?->allotedTo)->m06_name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Technical Manager:</td>
                                    <td colspan="3" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                                        {{ Session::get('role') === 'Manager' ? Session::get('name') : optional($manuscripts->first()?->allotedBy)->m06_name ?? '' }}
                                    </td>
                                </tr>
                                <!-- Test Results Banner -->
                                <tr>
                                    <td colspan="4" class="text-center fw-bold text-dark w-100"
                                        style="border-bottom: 1px solid #000; padding: 6px; letter-spacing: 2px;">
                                        TEST RESULTS
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Print Form Table 2 (Variables) -->
                        <table
                            style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none; font-family: Arial, sans-serif; font-size: 13px; table-layout: fixed;">
                            <colgroup>
                                <col class="serial-col-print">
                                <col class="name-col-print">
                                <col class="result-col-print">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td colspan="2"
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Sample Mark
                                    </td>
                                    <td style="border-bottom: 1px solid #000; padding: 5px 8px; text-align: right;">
                                        --
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px;">
                                        Laboratory Sample No.
                                    </td>
                                    <td
                                        style="border-bottom: 1px solid #000; padding: 5px 8px; text-align: right; font-weight: bold;">
                                        {{ optional($manuscripts->first()?->registration)->tr04_reference_id ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: left;">
                                        S.No
                                    </td>
                                    <td
                                        style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: left;">
                                        Test Name
                                    </td>
                                    <td
                                        style="border-bottom: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: right;">
                                        Results
                                    </td>
                                </tr>

                                @foreach ($manuscripts as $key => $manuscript)
                                    @php
                                        $test = $manuscript->test;
                                        $primaryTests = $test->primaryTests ?? collect();
                                        $existingMainTestResult = $existingResults
                                            ->where('m12_test_number', $manuscript->m12_test_number)
                                            ->whereNull('m16_primary_test_id')
                                            ->whereNull('m17_secondary_test_id')
                                            ->first();
                                    @endphp
                                    <!-- Main Test Row -->
                                    <tr>
                                        <td
                                            style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 5px 8px; vertical-align: top; font-weight: bold;">
                                            {{ $key + 1 }}
                                        </td>
                                        <td
                                            style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 5px 8px; vertical-align: top;">
                                            <div style="font-weight: bold; font-size: 14px;">
                                                {{ $manuscript->test->m12_name ?? 'N/A' }}
                                                ({{ $manuscript->standard->m15_method ?? 'N/A' }})
                                            </div>
                                            <!-- Summernote/Calculation output -->
                                            <div id="print_calc_{{ $manuscript->m12_test_number }}"
                                                style="font-size: 13px; margin-top: 5px;"></div>
                                        </td>
                                        <td
                                            style="border-bottom: 1px solid #000; padding: 5px 8px; vertical-align: bottom; text-align: right; font-weight: bold;">
                                            @if ($primaryTests->isEmpty())
                                                <span
                                                    id="print_res_t_{{ $manuscript->m12_test_number }}">{{ $existingMainTestResult->tr07_result ?? '' }}</span>
                                                <span
                                                    id="print_unt_t_{{ $manuscript->m12_test_number }}">{{ $existingMainTestResult->tr07_unit ?? ($test->m12_unit ?? '') }}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Parameter Rows (Primary/Secondary) -->
                                    @foreach ($primaryTests as $pIndex => $primaryTest)
                                        @php
                                            $existingPrimaryResult = $existingResults
                                                ->where('m12_test_number', $manuscript->m12_test_number)
                                                ->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)
                                                ->whereNull('m17_secondary_test_id')
                                                ->first();
                                        @endphp
                                        <tr id="print_tr_p_{{ $primaryTest->m16_primary_test_id }}">
                                            <td
                                                style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 8px;">
                                            </td>
                                            <td
                                                style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 8px 3px 25px;">
                                                {{ $primaryTest->m16_name ?? 'N/A' }}
                                            </td>
                                            <td
                                                style="border-bottom: 1px solid #000; padding: 3px 8px; text-align: right; font-weight: bold;">
                                                <span
                                                    id="print_res_p_{{ $primaryTest->m16_primary_test_id }}">{{ $existingPrimaryResult->tr07_result ?? '' }}</span>
                                                <span
                                                    id="print_unt_p_{{ $primaryTest->m16_primary_test_id }}">{{ $existingPrimaryResult->tr07_unit ?? ($primaryTest->m16_unit ?? '') }}</span>
                                            </td>
                                        </tr>

                                        <!-- Custom Fields for Primary -->
                                        @php
                                            $pCustomResults = $existingResults
                                                ->where('m12_test_number', $manuscript->m12_test_number)
                                                ->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)
                                                ->whereNull('m17_secondary_test_id')
                                                ->whereNotNull('tr07_custom_label');
                                        @endphp
                                        @foreach ($pCustomResults as $pcr)
                                            <tr>
                                                <td
                                                    style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 2px 8px;">
                                                </td>
                                                <td
                                                    style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 2px 8px 2px 40px; font-size: 11px;">
                                                    {{ $pcr->tr07_custom_label }}
                                                </td>
                                                <td
                                                    style="border-bottom: 1px solid #000; padding: 2px 8px; text-align: right; font-weight: bold; font-size: 11px;">
                                                    <span
                                                        id="print_res_c_{{ $pcr->tr07_test_result_id }}">{{ $pcr->tr07_result }}</span>
                                                    {{ $pcr->tr07_unit }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Secondary Tests -->
                                        @foreach ($primaryTest->secondaryTests as $sIndex => $secondaryTest)
                                            @php
                                                $existingSecondaryResult = $existingResults
                                                    ->where('m12_test_number', $manuscript->m12_test_number)
                                                    ->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)
                                                    ->where(
                                                        'm17_secondary_test_id',
                                                        $secondaryTest->m17_secondary_test_id,
                                                    )
                                                    ->first();
                                            @endphp
                                            <tr id="print_tr_s_{{ $secondaryTest->m17_secondary_test_id }}">
                                                <td
                                                    style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 2px 8px;">
                                                </td>
                                                <td
                                                    style="border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 2px 8px 2px 40px; color: #333;">
                                                    - {{ $secondaryTest->m17_name ?? 'N/A' }}
                                                </td>
                                                <td
                                                    style="border-bottom: 1px solid #000; padding: 2px 8px; text-align: right; font-weight: bold;">
                                                    <span
                                                        id="print_res_s_{{ $secondaryTest->m17_secondary_test_id }}">{{ $existingSecondaryResult->tr07_result ?? '' }}</span>
                                                    <span
                                                        id="print_unt_s_{{ $secondaryTest->m17_secondary_test_id }}">{{ $existingSecondaryResult->tr07_unit ?? '' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>

            <tfoot class="print-footer">
                <tr>
                    <td style="padding-top: 50px; padding-bottom: 20px; border-top: none !important;">
                        <div class="d-flex justify-content-between align-items-end"
                            style="font-family: Arial, sans-serif; font-size: 13px; width: 100%;">
                            <div style="text-align: left; width: 35%;">
                                <div style="border-top: 1.5px solid #000; width: 100%; margin-bottom: 5px;">
                                </div>
                                Signature of QAO/JQAO
                            </div>
                            <div style="text-align: center; width: 25%; padding-bottom: 5px; font-weight: bold;">
                                Page <span class="page-number"></span> of <span class="total-pages"></span>
                            </div>
                            <div style="text-align: right; width: 35%;">
                                <div style="border-top: 1.5px solid #000; width: 100%; margin-bottom: 5px;">
                                </div>
                                Signature of Technical Manager
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- END WEB VIEW -->

    <script>
        function submitForm(actionValue) {
            document.getElementById('formAction').value = actionValue;
            document.getElementById('manuscriptForm').submit();
        }

        $(document).ready(function() {
            // Set today's date
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            document.getElementById('print_today_date').textContent = dd + '/' + mm + '/' + yyyy;

            // Page numbering estimation
            const printTable = document.querySelector('.print-main-table');
            if (printTable) {
                const height = printTable.offsetHeight;
                const pageHeight = 1050; // Estimated A4 height in pixels
                const totalPages = Math.ceil(height / pageHeight) || 1;
                document.querySelectorAll('.total-pages').forEach(el => el.textContent = totalPages);
            }

            // Basic input sync
            document.querySelectorAll('.web-sync-val').forEach(function(el) {
                var targetId = el.getAttribute('data-sync');
                var targetEl = document.getElementById(targetId);
                if (targetEl) {
                    if (el.type === 'date' && el.value) {
                        var parts = el.value.split('-');
                        if (parts.length === 3) {
                            targetEl.textContent = parts[2] + '/' + parts[1] + '/' + parts[0];
                        } else {
                            targetEl.textContent = el.value;
                        }
                    } else {
                        targetEl.textContent = el.value;
                    }
                }
            });

            // Summernote sync
            document.querySelectorAll('.summernote-basic').forEach(function(textarea) {
                var name = textarea.getAttribute('name');
                var idMatch = name.match(/\[(\d+)\]/);
                if (idMatch) {
                    var targetEl = document.getElementById('print_calc_' + idMatch[1]);
                    if (targetEl) {
                        var val = textarea.value;
                        try {
                            val = $(textarea).summernote('code');
                        } catch (e) {}
                        targetEl.innerHTML = val;
                    }
                }
            });

            // Sync dynamic primary/secondary result values
            $('.primary-test-row input[name*="[result]"]').each(function() {
                const name = $(this).attr('name');
                const pIdMatch = name.match(/\[primary_tests\]\[(\d+)\]\[result\]/);
                if (pIdMatch) {
                    $(`#print_res_p_${pIdMatch[1]}`).text($(this).val());
                }
            });

            $('.primary-test-row input[name*="[unit]"]').each(function() {
                const name = $(this).attr('name');
                const pIdMatch = name.match(/\[primary_tests\]\[(\d+)\]\[unit\]/);
                if (pIdMatch) {
                    $(`#print_unt_p_${pIdMatch[1]}`).text($(this).val());
                }
            });

            $('.secondary-test-row input[name*="[result]"]').each(function() {
                const name = $(this).attr('name');
                const sIdMatch = name.match(/\[secondary_tests\]\[(\d+)\]\[result\]/);
                if (sIdMatch) {
                    $(`#print_res_s_${sIdMatch[1]}`).text($(this).val());
                }
            });

            $('.secondary-test-row input[name*="[unit]"]').each(function() {
                const name = $(this).attr('name');
                const sIdMatch = name.match(/\[secondary_tests\]\[(\d+)\]\[unit\]/);
                if (sIdMatch) {
                    $(`#print_unt_s_${sIdMatch[1]}`).text($(this).val());
                }
            });

            // Sync custom fields
            $('.custom-field-row').each(function() {
                const row = $(this);
                const value = row.find('input[name*="[value]"]').val();
                const unit = row.find('input[name*="[unit]"]').val();
                const label = row.find('input[name*="[custom_label]"]').val() || row.find('td:eq(1)').text()
                    .trim();

                // For custom fields added dynamically, we need to ensure they exist in print view
                // This is already handled by the JavaScript that adds them, but a final sync helps
            });
        });

        $(document).ready(function() {
            // Add Primary Test
            $('.add-primary-test').on('click', function() {
                const testId = $(this).data('test-id');
                const testNumber = $(this).data('test-number');
                const primaryTests = $(this).data('primary-tests');
                const tableBody = $(`#results_table_${testNumber} tbody`);

                let modalHtml = '';
                primaryTests.forEach(pt => {
                    modalHtml += `
                                                <tr>
                                                    <td>${pt.m16_name}</td>
                                                    <td>${pt.m16_unit || '-'}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-xs select-primary-test" 
                                                            data-pt='${JSON.stringify(pt)}' 
                                                            data-test-number="${testNumber}">
                                                            Select
                                                        </button>
                                                    </td>
                                                </tr>`;
                });

                $('#primaryTestList').html(modalHtml);
                $('#primaryTestModal').modal('show');
            });

            $(document).on('click', '.select-primary-test', function() {
                const pt = $(this).data('pt');
                const testNumber = $(this).data('test-number');
                const tableBody = $(`#results_table_${testNumber} tbody`);

                // Check if already exists
                if (tableBody.find(`.primary-test-row[data-primary-test-id="${pt.m16_primary_test_id}"]`)
                    .length > 0) {
                    Swal.fire('Already Added', 'This primary test is already in the list.', 'info');
                    return;
                }

                // Hide placeholder row only for this first addition
                const mainRow = tableBody.find('.test-main-row');
                // We keep the main row visible if it has the "Add Primary" button
                // but we hide the "N/A" text or similar if it's there.

                const rowHtml = `
                                            <tr class="primary-test-row" data-test-number="${testNumber}" data-primary-test-id="${pt.m16_primary_test_id}">
                                                <td class="serial-col"></td>
                                                <td class="fw-bold text-muted align-middle ps-3">${pt.m16_name}</td>
                                                <td>
                                                    <div class="result-input-group input-group input-group-sm ${pt.secondary_tests && pt.secondary_tests.length > 0 ? 'd-none' : ''}">
                                                        <input type="hidden" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][test_id]" value="${testNumber}">
                                                        <input type="hidden" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][primary_test_id]" value="${pt.m16_primary_test_id}">
                                                        <input type="text" class="form-control form-control-sm border-0 bg-light web-sync-val" name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][result]" placeholder="Enter result value">
                                                        <button type="button" class="btn btn-outline-light btn-sm open-raw-entry" data-target-name="results[${testNumber}][primary_tests][${pt.m16_primary_test_id}][result]" data-label="${pt.m16_name}"><em class="icon ni ni-calc"></em></button>
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

                // Print view removal - we don't need to add to print view anymore

                // We keep modal open to allow multiple selections
                // $('#primaryTestModal').modal('hide'); 

                // Show a small toast instead of hiding modal
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: `${pt.m16_name} added`
                });

                updateSerialNumbers(testNumber);
            });

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
                        let printTarget = `print_custom_t_${testNumber}`;

                        if (type === 'primary') {
                            namePrefix += `[primary_tests][${pId}]`;
                            labelClass = 'ps-4 text-muted';
                            attr += ` data-primary-test-id="${pId}"`;
                            printTarget = `print_custom_p_${pId}`;
                        } else if (type === 'secondary') {
                            namePrefix += `[primary_tests][${pId}][secondary_tests][${sId}]`;
                            labelClass = 'ps-5 text-muted';
                            attr +=
                                ` data-primary-test-id="${pId}" data-secondary-test-id="${sId}"`;
                            printTarget = `print_custom_s_${sId}`;
                        }

                        const syncId = `custom_${timestamp}`;
                        const rowHtml = `
                                                    <tr class="${rowClass}" ${attr}>
                                                        <td class="serial-col"></td>
                                                        <td class="${labelClass}">${fieldName}</td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <input type="hidden" name="${namePrefix}[custom_fields][new_${timestamp}][label]" value="${fieldName}">
                                                                <input type="text" class="form-control form-control-sm border-0 bg-light web-sync-val" data-sync="print_res_${syncId}" name="${namePrefix}[custom_fields][new_${timestamp}][value]" placeholder="Value">
                                                                <input type="text" class="form-control form-control-sm border-0 bg-light web-sync-val" data-sync="print_unt_${syncId}" style="max-width: 80px;" name="${namePrefix}[custom_fields][new_${timestamp}][unit]" placeholder="Unit">
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

                        // Add to print view
                        $(`#${printTarget}`).append(`
                                                    <div class="print-custom-row" id="print_row_${syncId}" style="font-size: 10px; font-weight: normal; margin-top: 1px;">
                                                        ${fieldName}: 
                                                        <span id="print_res_${syncId}" style="font-weight: bold;"></span>
                                                        <span id="print_unt_${syncId}"></span>
                                                    </div>
                                                `);

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
                        // Remove from print
                        if (syncId) $(`#print_row_${syncId}`).remove();
                        if (type === 'primary') {
                            $(`#print_tr_p_${id}`).remove();
                            $(`#print_after_p_${id}`).remove();
                            // Also remove any secondary tests belonging to this primary
                            tableBody.find(`.secondary-test-row[data-primary-test-id="${id}"]`)
                                .each(function() {
                                    const sId = $(this).data('secondary-test-id');
                                    $(`#print_tr_s_${sId}`).remove();
                                    $(this).remove();
                                });
                            // If no more primary, show main?
                            if (tableBody.find('.primary-test-row').length === 0) {
                                tableBody.find('.test-main-row').removeClass('d-none');
                            }
                        }
                        if (type === 'secondary') {
                            $(`#print_tr_s_${id}`).remove();
                        }
                        updateSerialNumbers(testNumber);
                    }
                });
            });

            function updateSerialNumbers(testNumber) {
                const tableBody = $(`#results_table_${testNumber} tbody`);
                const mainSr = tableBody.closest('.test-card').data('main-sr');

                let pCount = 0;
                tableBody.find('.primary-test-row').each(function() {
                    pCount++;
                    const pRow = $(this);
                    const pId = pRow.data('primary-test-id');
                    const pSr = `${mainSr}.${pCount}`;
                    pRow.find('td:first').text(pSr);

                    // Sub-rows (Custom fields and Secondary tests)
                    let subCount = 0;
                    let hasSecondary = false;
                    pRow.nextAll().each(function() {
                        const subRow = $(this);

                        if (subRow.hasClass('primary-test-row'))
                            return false; // stop at next primary test

                        if (subRow.hasClass('custom-field-row')) {
                            if (subRow.data('primary-test-id') == pId && !subRow.data(
                                    'secondary-test-id')) {
                                subCount++;
                                subRow.find('td:first').text(`${pSr}.C${subCount}`);
                            }
                        }

                        if (subRow.hasClass('secondary-test-row')) {
                            if (subRow.data('primary-test-id') == pId) {
                                subCount++;
                                hasSecondary = true;
                                const sSr = `${pSr}.${subCount}`;
                                subRow.find('td:first').text(sSr);

                                // Custom fields for secondary
                                let sCfCount = 0;
                                const sId = subRow.data('secondary-test-id');
                                subRow.nextAll().each(function() {
                                    const scRow = $(this);
                                    if (scRow.hasClass('primary-test-row') || scRow
                                        .hasClass('secondary-test-row')) return false;
                                    if (scRow.hasClass('custom-field-row') && scRow.data(
                                            'secondary-test-id') == sId) {
                                        sCfCount++;
                                        scRow.find('td:first').text(`${sSr}.C${sCfCount}`);
                                    }
                                });
                            }
                        }
                    });
                });

                // If no primary tests, we might need to show main row if it's supposed to be visible
                if (pCount === 0) {
                    // Only show main row if it's not permanently hidden by Blade (i.e., when no primary tests exist at all in DB)
                    if (!tableBody.find('.test-main-row').hasClass('d-none') || tableBody.find(
                            '.test-main-row .input-group').length === 0) {
                        tableBody.find('.test-main-row td:first').text(mainSr);
                    }

                    let mCfCount = 0;
                    tableBody.find('.custom-field-row').each(function() {
                        const row = $(this);
                        if (!row.data('primary-test-id')) {
                            mCfCount++;
                            row.find('td:first').text(`${mainSr}.C${mCfCount}`);
                        }
                    });
                }
            }

            // Initial serial number application
            $('[id^="results_table_"]').each(function() {
                const testNumber = this.id.replace('results_table_', '');
                updateSerialNumbers(testNumber);
            });
            // Add Secondary Test
            $(document).on('click', '.add-secondary-test', function() {
                const testNumber = $(this).data('test-number');
                const pId = $(this).data('primary-test-id');
                const secondaryTests = $(this).data('secondary-tests');
                const tableBody = $(`#results_table_${testNumber} tbody`);

                let modalHtml = '';
                secondaryTests.forEach(st => {
                    modalHtml += `
                                                <tr>
                                                    <td>${st.m17_name}</td>
                                                    <td>${st.m17_unit || '-'}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-xs select-secondary-test" 
                                                            data-st='${JSON.stringify(st)}' 
                                                            data-test-number="${testNumber}"
                                                            data-primary-test-id="${pId}">
                                                            Select
                                                        </button>
                                                    </td>
                                                </tr>`;
                });

                $('#primaryTestList').html(modalHtml);
                $('.modal-title').text('Select Secondary Test to Add');
                $('#primaryTestModal').modal('show');
            });

            $(document).on('click', '.select-secondary-test', function() {
                const st = $(this).data('st');
                const testNumber = $(this).data('test-number');
                const pId = $(this).data('primary-test-id');
                const tableBody = $(`#results_table_${testNumber} tbody`);

                // Check if already exists
                if (tableBody.find(
                        `.secondary-test-row[data-secondary-test-id="${st.m17_secondary_test_id}"]`)
                    .length > 0) {
                    Swal.fire('Already Added', 'This secondary test is already in the list.', 'info');
                    return;
                }

                const rowHtml = `
                                            <tr class="secondary-test-row" data-test-number="${testNumber}" data-primary-test-id="${pId}" data-secondary-test-id="${st.m17_secondary_test_id}">
                                                <td class="serial-col"></td>
                                                <td class="ps-4 text-muted align-middle">${st.m17_name}</td>
                                                <td>
                                                    <div class="result-input-group input-group input-group-sm">
                                                        <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][test_id]" value="${testNumber}">
                                                        <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][primary_test_id]" value="${pId}">
                                                        <input type="hidden" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][secondary_test_id]" value="${st.m17_secondary_test_id}">
                                                        <input type="text" class="form-control form-control-sm border-0 bg-light web-sync-val" name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][result]" placeholder="Enter result value">
                                                        <button type="button" class="btn btn-outline-light btn-sm open-raw-entry" data-target-name="results[${testNumber}][primary_tests][${pId}][secondary_tests][${st.m17_secondary_test_id}][result]" data-label="${st.m17_name}"><em class="icon ni ni-calc"></em></button>
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

                // Insert after the primary test row or its last secondary/custom row
                let lastRow = tableBody.find(`.primary-test-row[data-primary-test-id="${pId}"]`);
                tableBody.find(`tr[data-primary-test-id="${pId}"]`).each(function() {
                    lastRow = $(this);
                });
                lastRow.after(rowHtml);

                // Print view removal - we don't need to add to print view anymore

                // We keep modal open to allow multiple selections
                // $('#primaryTestModal').modal('hide');

                // Show a small toast instead of hiding modal
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: `${st.m17_name} added`
                });

                updateSerialNumbers(testNumber);
            });
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

        $('#numberOfReadings, #aggregationType').on('change', function() {
            generateReadingRows();
        });

        function generateReadingRows() {
            const count = $('#numberOfReadings').val();
            let html = '';
            for (let i = 1; i <= count; i++) {
                html +=
                    `<tr><td>Reading ${i}</td><td><input type="number" class="form-control form-control-sm reading-val" value="0"></td></tr>`;
            }
            $('#readingsBody').html(html);
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

        $('#applyCalculatedResult').on('click', function() {
            const val = $('#calculatedResult').text();
            if (currentTargetInput && currentTargetInput.length > 0) {
                currentTargetInput.val(val).trigger('input');
                $('#rawEntryModal').modal('hide');
            }
        });
    </script>

    </form>
    </div>
    </div>
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
    </style>
    </style>
@endsection
