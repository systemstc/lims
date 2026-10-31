@extends('layouts.app_back')

@section('content')
    @php
        $sampleReg = $sampleTests->first()?->registration;
        $refId = $sampleReg?->tr04_reference_id ?? '';
        $regPkId = $sampleTests->first()?->tr04_sample_registration_id ?? $sampleReg?->tr04_sample_registration_id;
        $defaultTargetRole = Session::get('role') === 'DEO' ? 'Analyst' : (Session::get('role') === 'Analyst' ? 'Manager' : 'RC');

        $firstResultStatus = optional($existingResults->first())->tr07_result_status;
        $isResultSubmitted = in_array($firstResultStatus, ['SUBMITTED', 'RESULTED', 'COMPLETED', 'VERIFIED', 'REPORTED', 'FINALIZED']);

        // Helper to resolve URL for any manuscript item
        // Helper to resolve URL for any manuscript item
        $resolveManuscriptUrl = function($fileName, $ext = null) {
            if (empty($fileName)) return '';
            return route('serve_manuscript_file', ['filename' => basename($fileName)]);
        };

        $resolveManuscriptDownloadUrl = function($fileName) {
            if (empty($fileName)) return '';
            return route('serve_manuscript_file', ['filename' => basename($fileName)]);
        };

        // Retrieve all manuscripts from SampleManuscript
        $allManuscripts = isset($sampleManuscripts) && $sampleManuscripts->isNotEmpty()
            ? $sampleManuscripts
            : collect();

        if ($allManuscripts->isEmpty() && $regPkId) {
            try {
                \App\Models\SampleManuscript::ensureManuscriptSchemaExists();
                $allManuscripts = \App\Models\SampleManuscript::where('tr04_sample_registration_id', $regPkId)
                    ->where('is_current', 1)
                    ->orderBy('page_number')
                    ->orderBy('tr11_manuscript_id')
                    ->get();
            } catch (\Throwable $mEx) {
                // Ignore fallback
            }
        }

        // If no records in tr11, check sample registration tr04_manuscript
        if ($allManuscripts->isEmpty() && !empty($sampleReg?->tr04_manuscript)) {
            $fName = $sampleReg->tr04_manuscript;
            $mExt = strtolower(pathinfo($fName, PATHINFO_EXTENSION));
            $mType = in_array($mExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? 'image' : ($mExt === 'pdf' ? 'pdf' : 'other');
            $allManuscripts->push((object)[
                'tr11_manuscript_id' => 1,
                'file_path'          => $fName,
                'original_name'      => $fName,
                'analyst_name'       => $sampleTests->first()?->allotedTo?->m06_name ?? 'Analyst',
                'm06_analyst_id'     => $sampleTests->first()?->m06_alloted_to,
                'page_number'        => 1,
                'total_pages'        => 1,
                'file_type'          => $mType,
            ]);
        }

        // Check if any sample tests have additional tr05_manuscript not in allManuscripts
        foreach ($sampleTests as $st) {
            if (!empty($st->tr05_manuscript)) {
                $already = $allManuscripts->contains(fn($m) => ($m->file_path ?? '') === $st->tr05_manuscript);
                if (!$already) {
                    $mExt = strtolower(pathinfo($st->tr05_manuscript, PATHINFO_EXTENSION));
                    $mType = in_array($mExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? 'image' : ($mExt === 'pdf' ? 'pdf' : 'other');
                    $allManuscripts->push((object)[
                        'tr11_manuscript_id' => 'st_' . $st->tr05_sample_test_id,
                        'file_path'          => $st->tr05_manuscript,
                        'original_name'      => $st->tr05_manuscript,
                        'analyst_name'       => $st->allotedTo?->m06_name ?? 'Analyst',
                        'm06_analyst_id'     => $st->m06_alloted_to,
                        'page_number'        => 1,
                        'total_pages'        => 1,
                        'file_type'          => $mType,
                    ]);
                }
            }
        }

        // Filter into images and PDFs (excluding any entries without actual files)
        $manuscriptImages = $allManuscripts->filter(function($m) {
            $ext = strtolower(pathinfo($m->file_path ?? '', PATHINFO_EXTENSION));
            return !empty($m->file_path) && (($m->file_type ?? '') === 'image' || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']));
        })->values();

        $manuscriptPdfs = $allManuscripts->filter(function($m) {
            $ext = strtolower(pathinfo($m->file_path ?? '', PATHINFO_EXTENSION));
            return !empty($m->file_path) && (($m->file_type ?? '') === 'pdf' || $ext === 'pdf');
        })->values();

        $hasManuscript = $allManuscripts->isNotEmpty();
        $primaryManuscript = $manuscriptImages->first() ?? $manuscriptPdfs->first() ?? $allManuscripts->first();
        $manuscriptFile = $primaryManuscript ? $primaryManuscript->file_path : null;
        $ext = $manuscriptFile ? strtolower(pathinfo($manuscriptFile, PATHINFO_EXTENSION)) : '';
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
        $fileUrl = $manuscriptFile ? $resolveManuscriptUrl($manuscriptFile, $ext) : '';
        $downloadUrl = $manuscriptFile ? $resolveManuscriptDownloadUrl($manuscriptFile) : '';
    @endphp

    <div class="container-fluid px-2 px-md-3">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto" id="mainContainerWrapper">
                    <!-- Header Section -->
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title text-uppercase">
                                    Test Result Entry
                                    @if ($refId)
                                        <span class="badge bg-primary-dim text-primary fw-bold fs-13px ms-2">#{{ $refId }}</span>
                                    @endif
                                </h3>
                                <div class="nk-block-des text-soft">
                                    <p class="text-muted fs-13px mb-0">Laboratory Information Management System</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if ($regPkId)
                                        <a href="{{ route('download_manuscript', $regPkId) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;">
                                            <em class="icon ni ni-download me-1"></em> Download Manuscript
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-dim btn-outline-danger btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#sendBackModal">
                                        <em class="icon ni ni-undo me-1"></em> <span>Send Back to {{ $defaultTargetRole }}</span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;" id="toggleManuscriptSplitBtn">
                                        <em class="icon ni ni-layout-split me-1"></em> <span id="splitBtnLabel">Hide Manuscript Preview</span>
                                    </button>
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;">
                                        <em class="icon ni ni-arrow-left me-1"></em> <span>Back</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($sampleReg && $sampleReg->tr04_is_under_clarification)
                        <!-- Clarification Required Alert Banner -->
                        <div class="alert alert-pro alert-warning bg-warning-dim border-warning shadow-none mb-3 py-2 px-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-1 border-bottom border-warning border-opacity-25 mb-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <em class="icon ni ni-alert-circle text-warning fs-18px"></em>
                                    <span class="fw-bold text-dark fs-14px">Clarification Required</span>
                                    <span class="badge badge-dim bg-danger text-danger">Returned by {{ $sampleReg->tr04_sent_back_by_role }} ({{ $sampleReg->tr04_sent_back_by_name ?? 'Authority' }})</span>
                                </div>
                                @if ($sampleReg->tr04_sent_back_at)
                                    <span class="text-muted small">
                                        <em class="icon ni ni-clock me-1"></em>{{ \Carbon\Carbon::parse($sampleReg->tr04_sent_back_at)->format('d M, Y h:i A') }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-dark fs-13px ps-1 pt-1" style="line-height: 1.5;">
                                <strong class="text-warning-dark me-1"><em class="icon ni ni-chat-fill text-warning me-1"></em>Remark:</strong>
                                <span>{{ $sampleReg->tr04_latest_clarification_remark }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="nk-block nk-block-lg">
                        <!-- Split View Row: Form (left) + Manuscript Preview (right) -->
                        <div class="row g-3" id="splitViewRow">
                            <div class="col-12 col-lg-6" id="resultFormCol">

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
                                <div class="card-body px-3 py-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0 small w-100">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold text-muted" style="width: 25%; min-width: 130px;">Test Report No:</td>
                                                    <td class="text-dark fw-semibold" style="width: 25%; min-width: 140px;">
                                                        {{ optional($sampleTests->first()?->registration)->tr04_reference_id ?? 'N/A' }}
                                                    </td>
                                                    <td class="fw-bold text-muted" style="width: 25%; min-width: 130px;">Date:</td>
                                                    <td style="width: 25%; min-width: 140px;">
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
                            </div>

                            <!-- Test Results -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center rounded-top">
                                    <h6 class="mb-0 text-uppercase"><em class="icon ni ni-layers me-1"></em> Test Results</h6>

                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-light btn-xs fw-bold text-primary shadow-sm" id="quickToggleSplitBtn">
                                            <em class="icon ni ni-layout-split me-1"></em> Toggle Preview
                                        </button>
                                        @if ($hasManuscript)
                                            <button type="button" class="btn btn-success btn-xs fw-bold"
                                                data-bs-toggle="modal" data-bs-target="#pdfModal">
                                                <em class="icon ni ni-eye me-1"></em> View
                                            </button>
                                            <button type="button" class="btn btn-warning btn-xs fw-bold"
                                                data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                                <em class="icon ni ni-upload me-1"></em> Upload New
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-warning btn-xs fw-bold text-dark"
                                                data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                                <em class="icon ni ni-upload me-1"></em> Upload Manuscript
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0" id="testResultsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 55px; text-align: center;">#</th>
                                                <th style="min-width: 170px;">Test Name</th>
                                                <th style="min-width: 280px;">Result / Entry</th>
                                                <th style="width: 140px; text-align: center;">Action</th>
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

                                                    $testNameLower = strtolower($test->m12_name ?? '');
                                                    $isFibreBlend = str_contains($testNameLower, 'fibre blend') || 
                                                                    str_contains($testNameLower, 'fiber blend') || 
                                                                    str_contains($testNameLower, 'blend composition');

                                                    $selectedStdIds = !empty($sampleTest->m15_standard_id)
                                                        ? array_filter(array_map('trim', explode(',', $sampleTest->m15_standard_id)))
                                                        : [];
                                                    
                                                    $currentStandardNames = '';
                                                    if (!empty($selectedStdIds) && isset($allStandards)) {
                                                        $matchedStds = $allStandards->whereIn('m15_standard_id', $selectedStdIds);
                                                        if ($matchedStds->isNotEmpty()) {
                                                            $currentStandardNames = $matchedStds->pluck('m15_method')->implode(' & ');
                                                        }
                                                    }
                                                    if (empty($currentStandardNames)) {
                                                        $currentStandardNames = $sampleTest->standard_method 
                                                            ?? (optional($sampleTest->standard)->m15_method 
                                                            ?? (optional($test->standard)->m15_method ?? ''));
                                                    }
                                                @endphp

                                                <!-- Main Test Row -->
                                                <tr class="bg-light fw-bold test-main-row"
                                                    data-test-id="{{ $test->m12_test_id }}"
                                                    data-test-number="{{ $test->m12_test_number }}">
                                                    <td class="text-center align-middle">{{ $key + 1 }}</td>
                                                    <td class="align-middle">
                                                        <div class="d-flex flex-column">
                                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                                <strong class="text-dark">{{ $test->m12_name ?? 'N/A' }}</strong>
                                                                @if ($manuscriptImages->count() > 1)
                                                                    @php
                                                                        $testAssignedPg = ($key % $manuscriptImages->count());
                                                                    @endphp
                                                                    <button type="button" class="btn btn-xs btn-dim btn-outline-info py-0 px-1 btn-quick-jump-sheet" data-page-idx="{{ $testAssignedPg }}" title="Jump manuscript preview to Page {{ $testAssignedPg + 1 }} for this test">
                                                                        <em class="icon ni ni-file-text me-1"></em>Pg {{ $testAssignedPg + 1 }}
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="mt-1 d-flex align-items-center flex-wrap gap-1">
                                                                <span class="badge bg-light text-secondary border standard-method-badge" id="std-badge-{{ $sampleTest->tr05_sample_test_id }}" title="Standard Test Method">
                                                                    <em class="icon ni ni-property-alt text-primary me-1"></em>
                                                                    <span class="standard-method-text">{{ Str::limit($currentStandardNames, 30) ?: 'No standard assigned' }}</span>
                                                                </span>
                                                                <button type="button" 
                                                                    class="btn btn-xs btn-outline-primary py-0 px-2 btn-edit-standard"
                                                                    title="Change Standard Method"
                                                                    data-sample-test-id="{{ $sampleTest->tr05_sample_test_id }}"
                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                    data-test-name="{{ $test->m12_name ?? 'Test' }}"
                                                                    data-is-fibre="{{ $isFibreBlend ? '1' : '0' }}"
                                                                    data-current-standards="{{ $sampleTest->m15_standard_id ?? '' }}">
                                                                    <em class="icon ni ni-edit me-1"></em>Change
                                                                </button>
                                                            </div>

                                                            <input type="hidden" 
                                                                name="sample_tests[{{ $sampleTest->tr05_sample_test_id }}][standard_id]" 
                                                                id="std-input-{{ $sampleTest->tr05_sample_test_id }}"
                                                                value="{{ $sampleTest->m15_standard_id ?? '' }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($primaryTests->isEmpty())
                                                            <!-- Input field for main test when no primary tests -->
                                                            <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][test_id]"
                                                                    value="{{ $test->m12_test_number }}">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][result]"
                                                                    class="wysiwyg-hidden-val"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') }}">
                                                                @php
                                                                    $mainRemarkVal = old('results.' . $test->m12_test_number . '.test.remarks', $existingMainTestResult->tr07_remarks ?? ($existingMainTestResult?->remarkRecord?->m24_remark_text ?? (\App\Models\TestRemark::find($existingMainTestResult->m24_remark_id ?? 0)?->m24_remark_text ?? '')));
                                                                @endphp
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][remarks]"
                                                                    class="row-remark-text"
                                                                    value="{{ $mainRemarkVal }}">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][remark_id]"
                                                                    class="row-remark-id"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.remark_id', $existingMainTestResult->m24_remark_id ?? '') }}">
                                                                <div class="form-control form-control-sm bg-light wysiwyg-editor-input @error('results.' . $test->m12_test_number . '.test.result') is-invalid @enderror"
                                                                    contenteditable="true"
                                                                    data-placeholder="Enter result value">{!! old('results.' . $test->m12_test_number . '.test.result', $existingMainTestResult->tr07_result ?? '') !!}</div>
                                                                <span class="input-group-text bg-white text-danger fw-bold row-remark-star px-1" style="{{ empty($mainRemarkVal) ? 'display:none;' : '' }} font-size:13px; cursor:pointer;" title="Remark attached">*</span>
                                                                <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                @if ($test->m23_formula_id)
                                                                    <button type="button"
                                                                        class="btn btn-outline-light btn-xs open-raw-entry px-2"
                                                                        data-target-name="results[{{ $test->m12_test_number }}][test][result]"
                                                                        data-label="{{ $test->m12_name ?? 'Final Result' }}"
                                                                        title="Formula / Raw Entry">
                                                                        <em class="icon ni ni-calc"></em>
                                                                    </button>
                                                                @endif
                                                                <button type="button"
                                                                    class="btn btn-xs {{ !empty($mainRemarkVal) ? 'btn-info text-white' : 'btn-outline-light' }} btn-open-remark px-2"
                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                    data-test-name="{{ $test->m12_name }}"
                                                                    data-field-prefix="results[{{ $test->m12_test_number }}][test]"
                                                                    title="{{ !empty($mainRemarkVal) ? 'Remark: ' . $mainRemarkVal : 'Add / View Remark' }}">
                                                                    <em class="icon ni ni-notes"></em>
                                                                </button>
                                                                <input type="text"
                                                                    class="form-control form-control-sm bg-light text-center px-1"
                                                                    style="max-width: 75px; min-width: 55px;"
                                                                    name="results[{{ $test->m12_test_number }}][test][unit]"
                                                                    value="{{ old('results.' . $test->m12_test_number . '.test.unit', $existingMainTestResult->tr07_unit ?? ($test->m12_unit ?? '')) }}"
                                                                    placeholder="Unit">
                                                                <input type="hidden"
                                                                    name="results[{{ $test->m12_test_number }}][test][result_id]"
                                                                    value="{{ $existingMainTestResult->tr07_test_result_id ?? '' }}">
                                                            </div>
                                                            @error('results.' . $test->m12_test_number . '.test.result')
                                                                <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                            @enderror
                                                        @else
                                                            <span class="badge bg-light text-secondary border px-2 py-1"><em class="icon ni ni-layers me-1"></em> Primary Tests Configured</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle" style="width: 140px;">
                                                        <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                                                            @if ($primaryTests->isNotEmpty())
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-xs py-1 px-2 fw-semibold add-primary-test text-nowrap"
                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                    data-primary-tests="{{ $primaryTests->toJson() }}"
                                                                    title="Add Primary Tests">
                                                                    <em class="icon ni ni-plus"></em> Primary
                                                                </button>
                                                            @endif
                                                            <button type="button"
                                                                class="btn btn-outline-warning btn-xs py-1 px-2 fw-semibold add-custom-field text-nowrap"
                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                data-test-number="{{ $test->m12_test_number }}"
                                                                data-type="test"
                                                                title="Add Custom Field">
                                                                <em class="icon ni ni-plus"></em> Custom
                                                            </button>
                                                        </div>
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
                                                            <tr id="primary_test_{{ $test->m12_test_number }}_{{ $primaryTest->m16_primary_test_id }}"
                                                                class="primary-test-row"
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
                                                                    <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
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
                                                                            $priRemarkVal = old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.remarks', $existingPrimaryResult->tr07_remarks ?? ($existingPrimaryResult?->remarkRecord?->m24_remark_text ?? (\App\Models\TestRemark::find($existingPrimaryResult->m24_remark_id ?? 0)?->m24_remark_text ?? '')));
                                                                        @endphp
                                                                        <input type="hidden"
                                                                            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][remarks]"
                                                                            class="row-remark-text"
                                                                            value="{{ $priRemarkVal }}">
                                                                        <input type="hidden"
                                                                            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][remark_id]"
                                                                            class="row-remark-id"
                                                                            value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.remark_id', $existingPrimaryResult->m24_remark_id ?? '') }}">
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
                                                                        <span class="input-group-text bg-white text-danger fw-bold row-remark-star px-1" style="{{ empty($priRemarkVal) ? 'display:none;' : '' }} font-size:13px; cursor:pointer;" title="Remark attached">*</span>
                                                                        <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                        <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                        <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                        @if ($primaryTest->m23_formula_id)
                                                                            <button type="button"
                                                                                class="btn btn-outline-light btn-xs open-raw-entry px-2"
                                                                                data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                                                                                data-label="{{ $primaryTest->m16_name ?? 'Primary Result' }}"
                                                                                title="Formula / Raw Entry">
                                                                                <em class="icon ni ni-calc"></em>
                                                                            </button>
                                                                        @endif
                                                                        <button type="button"
                                                                            class="btn btn-xs {{ !empty($priRemarkVal) ? 'btn-info text-white' : 'btn-outline-light' }} btn-open-remark px-2"
                                                                            data-test-id="{{ $test->m12_test_id }}"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-test-name="{{ $primaryTest->m16_name ?? $test->m12_name }}"
                                                                            data-field-prefix="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}]"
                                                                            title="{{ !empty($priRemarkVal) ? 'Remark: ' . $priRemarkVal : 'Add / View Remark' }}">
                                                                            <em class="icon ni ni-notes"></em>
                                                                        </button>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm border-0 bg-light text-center px-1"
                                                                            style="max-width: 75px; min-width: 55px;"
                                                                            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][unit]"
                                                                            value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.unit', $existingPrimaryResult->tr07_unit ?? ($primaryTest->m16_unit ?? '')) }}"
                                                                            placeholder="Unit">
                                                                    </div>
                                                                    @error('results.' . $test->m12_test_number .
                                                                        '.primary_tests.' . $primaryTest->m16_primary_test_id .
                                                                        '.result')
                                                                        <span
                                                                            class="text-danger small d-block mt-1">{{ $message }}</span>
                                                                    @enderror
                                                                </td>
                                                                <td class="text-center align-middle" style="width: 140px;">
                                                                    <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                                                                        <button type="button"
                                                                            class="btn btn-outline-danger btn-xs py-1 px-2 remove-test-row"
                                                                            data-type="primary"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                            title="Remove Primary Test">
                                                                            <em class="icon ni ni-trash"></em>
                                                                        </button>
                                                                        @if ($primaryTest->secondaryTests && $primaryTest->secondaryTests->isNotEmpty())
                                                                            <button type="button"
                                                                                class="btn btn-outline-success btn-xs py-1 px-2 fw-semibold add-secondary-test text-nowrap"
                                                                                data-test-number="{{ $test->m12_test_number }}"
                                                                                data-test-id="{{ $test->m12_test_id }}"
                                                                                data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                data-secondary-tests='{{ $primaryTest->secondaryTests->toJson() }}'
                                                                                title="Add Secondary Tests">
                                                                                <em class="icon ni ni-plus"></em> Sec
                                                                            </button>
                                                                        @endif
                                                                        <button type="button"
                                                                            class="btn btn-outline-warning btn-xs py-1 px-2 fw-semibold add-custom-field text-nowrap"
                                                                            data-test-id="{{ $test->m12_test_id }}"
                                                                            data-test-number="{{ $test->m12_test_number }}"
                                                                            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                            data-type="primary"
                                                                            title="Add Custom Field">
                                                                            <em class="icon ni ni-plus"></em> Custom
                                                                        </button>
                                                                    </div>
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
                                                                        <td class="text-center align-middle">{{ $key + 1 }}.{{ $loop->parent->index + 1 }}.{{ $loop->index + 1 }}
                                                                        </td>
                                                                        <td class="align-middle">{{ $secondaryTest->m17_name ?? 'N/A' }}</td>
                                                                        <td>
                                                                            <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
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
                                                                                <input type="hidden"
                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][remarks]"
                                                                                    class="row-remark-text"
                                                                                    @php
                                                                                        $secRemarkVal = old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.remarks', $existingSecondaryResult->tr07_remarks ?? ($existingSecondaryResult?->remarkRecord?->m24_remark_text ?? (\App\Models\TestRemark::find($existingSecondaryResult->m24_remark_id ?? 0)?->m24_remark_text ?? '')));
                                                                                    @endphp
                                                                                    value="{{ $secRemarkVal }}">
                                                                                <input type="hidden"
                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][remark_id]"
                                                                                    class="row-remark-id"
                                                                                    value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.remark_id', $existingSecondaryResult->m24_remark_id ?? '') }}">
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
                                                                                <span class="input-group-text bg-white text-danger fw-bold row-remark-star px-1" style="{{ empty($secRemarkVal) ? 'display:none;' : '' }} font-size:13px; cursor:pointer;" title="Remark attached">*</span>
                                                                                <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                                                                                <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                                                                                <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                                                                                @if ($secondaryTest->m23_formula_id)
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-light btn-xs open-raw-entry px-2"
                                                                                        data-target-name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                                                                                        data-label="{{ $secondaryTest->m17_name ?? 'Secondary Result' }}"
                                                                                        title="Formula / Raw Entry">
                                                                                        <em class="icon ni ni-calc"></em>
                                                                                    </button>
                                                                                @endif
                                                                                <button type="button"
                                                                                    class="btn btn-xs {{ !empty($secRemarkVal) ? 'btn-info text-white' : 'btn-outline-light' }} btn-open-remark px-2"
                                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                                    data-test-name="{{ $secondaryTest->m17_name ?? $test->m12_name }}"
                                                                                    data-field-prefix="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}]"
                                                                                    title="{{ !empty($secRemarkVal) ? 'Remark: ' . $secRemarkVal : 'Add / View Remark' }}">
                                                                                    <em class="icon ni ni-notes"></em>
                                                                                </button>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm border-0 bg-light text-center px-1"
                                                                                    style="max-width: 75px; min-width: 55px;"
                                                                                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][unit]"
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
                                                                                    class="text-danger small d-block mt-1">{{ $message }}</span>
                                                                            @enderror
                                                                        </td>
                                                                        <td class="text-center align-middle" style="width: 140px;">
                                                                            <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger btn-xs py-1 px-2 remove-test-row"
                                                                                    data-type="secondary"
                                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                    data-id="{{ $secondaryTest->m17_secondary_test_id }}"
                                                                                    title="Remove Secondary Test">
                                                                                    <em class="icon ni ni-trash"></em>
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="btn btn-outline-warning btn-xs py-1 px-2 fw-semibold add-custom-field text-nowrap"
                                                                                    data-test-id="{{ $test->m12_test_id }}"
                                                                                    data-test-number="{{ $test->m12_test_number }}"
                                                                                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                                                                                    data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}"
                                                                                    data-type="secondary"
                                                                                    title="Add Custom Field">
                                                                                    <em class="icon ni ni-plus"></em> Custom
                                                                                </button>
                                                                            </div>
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

                                <!-- Action Buttons (Pinned at bottom of form column, perfectly aligned) -->
                                <div class="card form-sticky-action-bar">
                                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap" style="height: auto !important; min-height: 54px !important;">
                                        <div class="my-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm fw-bold d-inline-flex align-items-center shadow-sm text-nowrap" style="height: 36px !important;" data-bs-toggle="modal" data-bs-target="#sendBackModal">
                                                <em class="icon ni ni-undo me-1"></em> <span>Send Back to {{ $defaultTargetRole }}</span>
                                            </button>
                                        </div>
                                        <div class="d-flex align-items-center my-1" style="height: auto !important;">
                                            @if (optional($existingResults->first())->tr07_result_status != 'SUBMITTED')
                                                <button type="button" onclick="submitForm('DRAFT')" id="btnSaveDraft"
                                                    class="btn btn-outline-primary btn-sm fw-bold d-inline-flex align-items-center shadow-sm text-nowrap me-2" style="height: 36px !important;">
                                                    <em class="icon ni ni-file-text me-1"></em> <span>Save as Draft</span>
                                                </button>
                                            @endif
                                            @if (Session::get('role') === 'DEO')
                                                <button type="button" onclick="openResultPreviewModal('RESULTED')" id="btnPreviewComplete"
                                                    class="btn btn-primary btn-sm fw-bold d-inline-flex align-items-center shadow-sm text-nowrap" style="height: 36px !important;">
                                                    <em class="icon ni ni-eye me-1"></em> <span>Preview</span>
                                                </button>
                                            @else
                                                <button type="button" onclick="openResultPreviewModal('SUBMITTED')" id="btnPreviewComplete"
                                                    class="btn btn-primary btn-sm fw-bold d-inline-flex align-items-center shadow-sm text-nowrap" style="height: 36px !important;">
                                                    <em class="icon ni ni-eye me-1"></em> <span>Preview</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    let pendingCompletionAction = '{{ Session::get('role') === 'DEO' ? 'RESULTED' : 'SUBMITTED' }}';

                                    function syncAllWysiwygEditors() {
                                        $('.wysiwyg-editor-input').each(function() {
                                            const $hidden = $(this).closest('.input-group').find('.wysiwyg-hidden-val');
                                            if ($hidden.length) {
                                                $hidden.val(this.innerHTML);
                                            }
                                        });
                                    }

                                    function escapeHtml(text) {
                                        if (!text) return '';
                                        const map = {
                                            '&': '&amp;',
                                            '<': '&lt;',
                                            '>': '&gt;',
                                            '"': '&quot;',
                                            "'": '&#039;'
                                        };
                                        return text.toString().replace(/[&<>"']/g, m => map[m]);
                                    }

                                    function validateResultForm() {
                                        const form = document.getElementById('testResultForm');
                                        syncAllWysiwygEditors();

                                        const resultInputs = form.querySelectorAll('.wysiwyg-hidden-val, input[name$="[value]"]');
                                        let isValid = true;
                                        let firstInvalid = null;

                                        resultInputs.forEach(input => {
                                            const inputGroup = input.closest('.input-group');
                                            if (inputGroup && (inputGroup.classList.contains('d-none') || inputGroup.style.display === 'none')) return;
                                            if (input.closest('.d-none')) return;

                                            const trRow = input.closest('tr');
                                            if (trRow && (trRow.classList.contains('d-none') || trRow.style.display === 'none')) return;
                                            
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

                                        // Validate custom field names
                                        form.querySelectorAll('input.custom-field-input').forEach(input => {
                                            const trRow = input.closest('tr');
                                            if (trRow && (trRow.classList.contains('d-none') || trRow.style.display === 'none')) return;
                                            if (!input.value.trim()) {
                                                isValid = false;
                                                input.classList.add('is-invalid');
                                                if (!firstInvalid) firstInvalid = input;
                                            } else {
                                                input.classList.remove('is-invalid');
                                            }
                                        });

                                        return { isValid, firstInvalid };
                                    }

                                    function submitForm(actionValue) {
                                        const form = document.getElementById('testResultForm');
                                        syncAllWysiwygEditors();

                                        if (actionValue === 'DRAFT') {
                                            // Save as Draft: Incomplete entries allowed!
                                            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                                            
                                            document.getElementById('formAction').value = 'DRAFT';
                                            
                                            const btnDraft = document.getElementById('btnSaveDraft');
                                            if (btnDraft) {
                                                btnDraft.disabled = true;
                                                btnDraft.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving Draft...';
                                            }
                                            form.submit();
                                            return;
                                        }

                                        // Direct completion submission
                                        const validation = validateResultForm();
                                        if (!validation.isValid) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Incomplete Test Results',
                                                text: 'Please fill in all selected test result values and custom fields before completing.'
                                            });
                                            if (validation.firstInvalid) {
                                                validation.firstInvalid.focus();
                                                validation.firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            }
                                            return;
                                        }

                                        document.getElementById('formAction').value = actionValue;
                                        form.submit();
                                    }

                                    function openResultPreviewModal(finalActionValue) {
                                        pendingCompletionAction = finalActionValue || '{{ Session::get('role') === 'DEO' ? 'RESULTED' : 'SUBMITTED' }}';

                                        // Validate that all results are filled before previewing for completion
                                        const validation = validateResultForm();
                                        if (!validation.isValid) {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Result Entry Incomplete',
                                                html: 'Please fill in all test results and required fields before previewing.<br><br><small class="text-muted">If you cannot complete all tests now, you can click <strong>Save as Draft</strong> to save your progress and complete later.</small>'
                                            });
                                            if (validation.firstInvalid) {
                                                validation.firstInvalid.focus();
                                                validation.firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            }
                                            return;
                                        }

                                        // Populate Preview Modal Content
                                        populatePreviewModalContent();

                                        // Show Modal
                                        const modalEl = document.getElementById('testResultPreviewModal');
                                        if (modalEl) {
                                            const previewModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                            previewModal.show();
                                        }
                                    }

                                    function populatePreviewModalContent() {
                                        // Meta details
                                        const testDateVal = document.querySelector('input[name="test_date"]')?.value || 'N/A';
                                        const perfDateVal = document.querySelector('input[name="performance_date"]')?.value || 'N/A';
                                        $('#modalPreviewTestDate').text(testDateVal);
                                        $('#modalPreviewPerfDate').text(perfDateVal);

                                        const remarksVal = document.querySelector('textarea[name="remarks"]')?.value || '';
                                        if (remarksVal.trim()) {
                                            $('#previewRemarksContainer').removeClass('d-none');
                                            $('#previewRemarksText').text(remarksVal);
                                        } else {
                                            $('#previewRemarksContainer').addClass('d-none');
                                        }

                                        // Table rows
                                        const $tbody = $('#previewResultsTableBody');
                                        $tbody.empty();

                                        const rows = document.querySelectorAll('#testResultsBody tr');
                                        let lastMainTestName = '';
                                        let lastStandardMethod = '';

                                        rows.forEach(tr => {
                                            if (tr.classList.contains('d-none') || tr.style.display === 'none') return;

                                            if (tr.classList.contains('test-main-row')) {
                                                const serial = tr.cells[0]?.textContent.trim() || '';
                                                const testNameEl = tr.cells[1]?.querySelector('strong');
                                                const testName = testNameEl ? testNameEl.textContent.trim() : tr.cells[1]?.textContent.trim();
                                                const stdMethodEl = tr.cells[1]?.querySelector('.standard-method-text');
                                                const stdMethod = stdMethodEl ? stdMethodEl.textContent.trim() : '';

                                                lastMainTestName = testName;
                                                lastStandardMethod = stdMethod;

                                                const wysiwygDiv = tr.cells[2]?.querySelector('.wysiwyg-editor-input');
                                                const unitInput = tr.cells[2]?.querySelector('input[name$="[unit]"]');
                                                
                                                if (wysiwygDiv) {
                                                    const resultVal = wysiwygDiv.innerHTML.trim() || '-';
                                                    const unitVal = unitInput ? unitInput.value.trim() : '-';

                                                    $tbody.append(`
                                                        <tr class="fw-semibold bg-white border-bottom">
                                                            <td class="text-center align-middle">${serial}</td>
                                                            <td class="align-middle">
                                                                <strong class="text-dark">${escapeHtml(testName)}</strong>
                                                                ${stdMethod ? `<br><small class="text-muted"><em class="icon ni ni-property-alt me-1 text-primary"></em>${escapeHtml(stdMethod)}</small>` : ''}
                                                            </td>
                                                            <td class="align-middle text-muted small"><em>Main Test</em></td>
                                                            <td class="align-middle text-primary fw-bold">${resultVal}</td>
                                                            <td class="text-center align-middle">${escapeHtml(unitVal) || '-'}</td>
                                                        </tr>
                                                    `);
                                                } else {
                                                    $tbody.append(`
                                                        <tr class="table-light border-bottom">
                                                            <td class="text-center align-middle fw-bold">${serial}</td>
                                                            <td class="align-middle" colspan="4">
                                                                <strong class="text-dark">${escapeHtml(testName)}</strong>
                                                                ${stdMethod ? `<span class="badge bg-light text-secondary border ms-2"><em class="icon ni ni-property-alt text-primary me-1"></em>${escapeHtml(stdMethod)}</span>` : ''}
                                                                <span class="badge bg-light text-primary border ms-2"><em class="icon ni ni-layers me-1"></em>Configured with Sub-tests</span>
                                                            </td>
                                                        </tr>
                                                    `);
                                                }
                                            } else if (tr.classList.contains('primary-test-row')) {
                                                const serial = tr.cells[0]?.textContent.trim() || '';
                                                const paramNameEl = tr.cells[1]?.querySelector('strong');
                                                const paramName = paramNameEl ? paramNameEl.textContent.trim() : tr.cells[1]?.textContent.trim();
                                                const reqEl = tr.cells[1]?.querySelector('small');
                                                const requirement = reqEl ? reqEl.textContent.trim() : '';

                                                const wysiwygDiv = tr.cells[2]?.querySelector('.wysiwyg-editor-input');
                                                const unitInput = tr.cells[2]?.querySelector('input[name$="[unit]"]');

                                                const resultVal = wysiwygDiv ? (wysiwygDiv.innerHTML.trim() || '-') : '-';
                                                const unitVal = unitInput ? unitInput.value.trim() : '-';

                                                $tbody.append(`
                                                    <tr class="bg-white border-bottom">
                                                        <td class="text-center align-middle">${serial}</td>
                                                        <td class="align-middle text-muted ps-3 small">
                                                            <em>Sub-test of ${escapeHtml(lastMainTestName)}</em>
                                                        </td>
                                                        <td class="align-middle">
                                                            <span class="badge badge-dim bg-primary me-1">Primary</span>
                                                            <strong class="text-dark">${escapeHtml(paramName)}</strong>
                                                            ${requirement ? `<br><small class="text-info">${escapeHtml(requirement)}</small>` : ''}
                                                        </td>
                                                        <td class="align-middle text-primary fw-bold">${resultVal}</td>
                                                        <td class="text-center align-middle">${escapeHtml(unitVal) || '-'}</td>
                                                    </tr>
                                                `);
                                            } else if (tr.classList.contains('secondary-test-row')) {
                                                const serial = tr.cells[0]?.textContent.trim() || '';
                                                const secName = tr.cells[1]?.textContent.trim() || '';
                                                const wysiwygDiv = tr.cells[2]?.querySelector('.wysiwyg-editor-input');
                                                const unitInput = tr.cells[2]?.querySelector('input[name$="[unit]"]');

                                                const resultVal = wysiwygDiv ? (wysiwygDiv.innerHTML.trim() || '-') : '-';
                                                const unitVal = unitInput ? unitInput.value.trim() : '-';

                                                $tbody.append(`
                                                    <tr class="bg-white border-bottom">
                                                        <td class="text-center align-middle">${serial}</td>
                                                        <td class="align-middle text-muted ps-4 small">
                                                            <em>Sub-test of ${escapeHtml(lastMainTestName)}</em>
                                                        </td>
                                                        <td class="align-middle ps-3">
                                                            <span class="badge badge-dim bg-info me-1">Secondary</span>
                                                            <span>${escapeHtml(secName)}</span>
                                                        </td>
                                                        <td class="align-middle text-primary fw-bold">${resultVal}</td>
                                                        <td class="text-center align-middle">${escapeHtml(unitVal) || '-'}</td>
                                                    </tr>
                                                `);
                                            } else if (tr.classList.contains('custom-field-row')) {
                                                const serial = tr.cells[0]?.textContent.trim() || '';
                                                const nameInput = tr.cells[1]?.querySelector('input.custom-field-input');
                                                const fieldName = nameInput ? nameInput.value.trim() : (tr.cells[1]?.textContent.trim() || 'Custom Field');

                                                const wysiwygDiv = tr.cells[2]?.querySelector('.wysiwyg-editor-input');
                                                const valInput = tr.cells[2]?.querySelector('input[name$="[value]"]');
                                                const unitInput = tr.cells[2]?.querySelector('input[name$="[unit]"]');

                                                const resultVal = wysiwygDiv ? wysiwygDiv.innerHTML.trim() : (valInput ? valInput.value.trim() : '-');
                                                const unitVal = unitInput ? unitInput.value.trim() : '-';

                                                $tbody.append(`
                                                    <tr class="bg-white border-bottom">
                                                        <td class="text-center align-middle">${serial}</td>
                                                        <td class="align-middle text-muted ps-3 small">
                                                            <em>Custom Field for ${escapeHtml(lastMainTestName)}</em>
                                                        </td>
                                                        <td class="align-middle">
                                                            <span class="badge badge-dim bg-warning text-dark me-1">Custom</span>
                                                            <span>${escapeHtml(fieldName)}</span>
                                                        </td>
                                                        <td class="align-middle text-dark fw-bold">${resultVal || '-'}</td>
                                                        <td class="text-center align-middle">${escapeHtml(unitVal) || '-'}</td>
                                                    </tr>
                                                `);
                                            }
                                        });
                                    }

                                    function confirmCompleteSubmission() {
                                        const btn = document.getElementById('btnModalConfirmComplete');
                                        if (btn) {
                                            btn.disabled = true;
                                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Completing...';
                                        }

                                        const modalEl = document.getElementById('testResultPreviewModal');
                                        if (modalEl) {
                                            const previewModal = bootstrap.Modal.getInstance(modalEl);
                                            if (previewModal) previewModal.hide();
                                        }

                                        const form = document.getElementById('testResultForm');
                                        document.getElementById('formAction').value = pendingCompletionAction;
                                        form.submit();
                                    }
                                </script>

                        </form>
                            </div> <!-- /#resultFormCol -->

                            <!-- RIGHT SIDE: Uploaded Manuscript Live Preview -->
                            <div class="col-12 col-lg-6" id="manuscriptPreviewCol">
                                <div class="card shadow-sm border-0 manuscript-preview-card" id="manuscriptPreviewCard">
                                    <!-- Header of Preview Panel -->
                                    <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center rounded-top flex-shrink-0" style="min-height: 48px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <em class="icon ni ni-file-docs text-primary fs-5"></em>
                                            <span class="fw-bold text-uppercase fs-12px tracking-wider">Manuscript</span>
                                            <!-- Document Type Switcher (if both Images and PDF exist) -->
                                            @if ($manuscriptImages->isNotEmpty() && $manuscriptPdfs->isNotEmpty())
                                                <div class="btn-group btn-group-sm ms-2">
                                                    <button type="button" class="btn btn-xs btn-primary active doc-type-tab" data-target="imagesView">
                                                        <em class="icon ni ni-images me-1"></em> Photos ({{ $manuscriptImages->count() }})
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-outline-light doc-type-tab" data-target="pdfView">
                                                        <em class="icon ni ni-file-pdf me-1"></em> PDF ({{ $manuscriptPdfs->count() }})
                                                    </button>
                                                </div>
                                            @elseif ($manuscriptImages->count() > 1)
                                                <span class="badge bg-primary text-white ms-1 px-2 py-1">
                                                    {{ $manuscriptImages->count() }} Pages
                                                </span>
                                            @elseif ($manuscriptImages->count() === 1)
                                                <span class="badge bg-success text-white ms-1 px-2 py-1">
                                                    {{ strtoupper($ext ?: 'IMG') }}
                                                </span>
                                            @elseif ($manuscriptPdfs->isNotEmpty())
                                                <span class="badge bg-danger text-white ms-1 px-2 py-1">
                                                    PDF
                                                </span>
                                            @elseif ($manuscriptFile)
                                                <span class="badge bg-info text-white ms-1 px-2 py-1">
                                                    {{ strtoupper($ext) }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark ms-1 px-2 py-1">No File</span>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            @if ($hasManuscript)
                                                @if ($manuscriptImages->count() > 1)
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button" class="btn btn-xs btn-outline-light d-inline-flex align-items-center gap-1 px-2" data-bs-toggle="dropdown" title="Download Manuscript Pages" id="previewDownloadDropdown">
                                                            <em class="icon ni ni-download"></em> <span class="d-none d-sm-inline">Download</span> <em class="icon ni ni-chevron-down" style="font-size: 10px;"></em>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 220px; z-index: 1055;">
                                                            <div class="dropdown-header text-muted small fw-bold">DOWNLOAD MANUSCRIPT PAGES</div>
                                                            @foreach ($manuscriptImages as $mIdx => $mItem)
                                                                <a class="dropdown-item py-1 small" href="{{ $resolveManuscriptDownloadUrl($mItem->file_path) }}" target="_blank" download>
                                                                    <em class="icon ni ni-download me-1 text-primary"></em> Page {{ $mIdx + 1 }} ({{ $mItem->analyst_name ?? 'Analyst' }})
                                                                </a>
                                                            @endforeach
                                                            @if ($manuscriptPdfs->isNotEmpty())
                                                                <div class="dropdown-divider"></div>
                                                                @foreach ($manuscriptPdfs as $pdfItem)
                                                                    <a class="dropdown-item py-1 small" href="{{ $resolveManuscriptDownloadUrl($pdfItem->file_path) }}" target="_blank" download>
                                                                        <em class="icon ni ni-file-pdf me-1 text-danger"></em> {{ $pdfItem->original_name ?? 'PDF Document' }}
                                                                    </a>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="{{ $downloadUrl }}" target="_blank" download class="btn btn-xs btn-outline-light px-2 d-inline-flex align-items-center gap-1" title="Download Manuscript">
                                                        <em class="icon ni ni-download"></em> <span class="d-none d-sm-inline">Download</span>
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-xs btn-outline-light px-2 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#pdfModal" title="Full Screen View">
                                                    <em class="icon ni ni-expand"></em>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-xs btn-outline-light px-2 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal" title="Upload New Copy">
                                                <em class="icon ni ni-upload"></em>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-danger px-2 d-inline-flex align-items-center" id="closeSplitPreviewBtn" title="Close Preview">
                                                <em class="icon ni ni-cross"></em>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Preview Toolbar (Single-line, Slate theme, Perfectly aligned) -->
                                    <div class="manuscript-preview-toolbar px-3 py-1 d-flex justify-content-between align-items-center flex-shrink-0" style="background: #0f172a; border-bottom: 1px solid #334155; min-height: 42px;">
                                        @if ($manuscriptImages->isNotEmpty())
                                            <div class="d-flex align-items-center gap-2" id="imagePageNavigation" style="flex-shrink: 0;">
                                                @if ($manuscriptImages->count() > 1)
                                                    <!-- Prev / Next Controls -->
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-light btn-xs px-2" id="prevPageBtn" title="Previous Page">
                                                            <em class="icon ni ni-chevron-left me-1"></em> Prev
                                                        </button>
                                                        <button type="button" class="btn btn-outline-light btn-xs px-2" id="nextPageBtn" title="Next Page">
                                                            Next <em class="icon ni ni-chevron-right ms-1"></em>
                                                        </button>
                                                    </div>

                                                    <!-- Page Dropdown Quick Jump -->
                                                    <div class="d-inline-flex align-items-center">
                                                        <select class="form-select form-select-xs bg-dark text-white border-secondary py-0 px-2" id="pageSelectDropdown" style="width: auto; cursor: pointer; border-radius: 4px;">
                                                            @foreach ($manuscriptImages as $pIdx => $pItem)
                                                                <option value="{{ $pIdx }}">Page {{ $pIdx + 1 }} of {{ $manuscriptImages->count() }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <!-- View Mode Toggle -->
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-xs btn-primary active px-2" id="viewModeAllBtn" title="Continuous scroll through all pages">
                                                            <em class="icon ni ni-view-col me-1"></em> All
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-outline-light px-2" id="viewModeSingleBtn" title="Single page view">
                                                            <em class="icon ni ni-file me-1"></em> Single
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-white-50 small fw-bold"><em class="icon ni ni-img me-1 text-primary"></em> Page 1 of 1</span>
                                                @endif
                                            </div>

                                            <!-- Zoom & Rotate Controls -->
                                            <div class="btn-group btn-group-sm ms-auto" style="flex-shrink: 0;">
                                                <button type="button" class="btn btn-outline-light btn-xs py-1 px-2" id="imgZoomOutBtn" title="Zoom Out"><em class="icon ni ni-minus"></em></button>
                                                <button type="button" class="btn btn-outline-light btn-xs py-1 px-2 disabled text-white fw-bold" id="zoomPercentBadge" style="opacity: 1; min-width: 44px; background: rgba(255,255,255,0.08); font-size: 11px;">100%</button>
                                                <button type="button" class="btn btn-outline-light btn-xs py-1 px-2" id="imgZoomInBtn" title="Zoom In"><em class="icon ni ni-plus"></em></button>
                                                <button type="button" class="btn btn-outline-light btn-xs py-1 px-2" id="imgRotateBtn" title="Rotate 90°"><em class="icon ni ni-reload"></em></button>
                                                <button type="button" class="btn btn-outline-light btn-xs py-1 px-2" id="imgResetBtn" title="Reset Zoom/Rotate"><em class="icon ni ni-undo"></em></button>
                                            </div>
                                        @elseif ($manuscriptPdfs->isNotEmpty())
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-white-50 small fw-bold"><em class="icon ni ni-file-pdf me-1 text-danger"></em> PDF Document Viewer</span>
                                                @if ($manuscriptPdfs->count() > 1)
                                                    <select class="form-select form-select-xs bg-dark text-white border-secondary" id="pdfSelectorDropdown" style="width: auto; font-size: 12px;">
                                                        @foreach ($manuscriptPdfs as $pdfIdx => $pdfItem)
                                                            <option value="{{ $resolveManuscriptUrl($pdfItem->file_path) }}" data-download="{{ $resolveManuscriptDownloadUrl($pdfItem->file_path) }}">
                                                                {{ $pdfItem->original_name ?? ('PDF ' . ($pdfIdx + 1)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                            <div class="ms-auto">
                                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-light btn-xs">
                                                    <em class="icon ni ni-external me-1"></em> Open in Tab
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-white-50 small fw-bold"><em class="icon ni ni-file-text me-1 text-primary"></em> Manuscript Viewer</span>
                                        @endif
                                    </div>

                                    <!-- Preview Body (Independently Scrollable Container) -->
                                    <div class="card-body p-0 bg-light flex-grow-1" style="overflow: hidden; display: flex; flex-direction: column;">
                                        @if ($hasManuscript)
                                            <!-- Container for Multiple or Single Images -->
                                            <div id="imagesViewPane" class="manuscript-scroll-pane flex-grow-1 p-2" style="{{ $manuscriptImages->isEmpty() ? 'display: none;' : '' }} background: #1e2227; overflow-y: auto;">
                                                @if ($manuscriptImages->isNotEmpty())
                                                    @foreach ($manuscriptImages as $imgIdx => $imgItem)
                                                        @php
                                                            $pUrl = $resolveManuscriptUrl($imgItem->file_path);
                                                            $pDownload = $resolveManuscriptDownloadUrl($imgItem->file_path);
                                                        @endphp
                                                        <div class="manuscript-page-card mb-3 shadow rounded bg-dark border border-secondary" id="manuscriptPage_{{ $imgIdx }}" data-page-index="{{ $imgIdx }}">
                                                            <div class="d-flex justify-content-between align-items-center px-3 py-1 bg-black bg-opacity-50 text-white border-bottom border-secondary small">
                                                                <span class="fw-bold">
                                                                    <em class="icon ni ni-file-text me-1 text-primary"></em> Page {{ $imgIdx + 1 }} of {{ $manuscriptImages->count() }}
                                                                    @if (!empty($imgItem->analyst_name))
                                                                        <span class="text-white-50 fw-normal ms-1">({{ $imgItem->analyst_name }})</span>
                                                                    @endif
                                                                </span>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <a href="{{ $pDownload }}" target="_blank" download class="btn btn-xs btn-outline-light py-0 px-2" title="Download this page">
                                                                        <em class="icon ni ni-download me-1"></em> Download
                                                                    </a>
                                                                    <a href="{{ $pUrl }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2" title="Open full resolution">
                                                                        <em class="icon ni ni-external me-1"></em> Full View
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="p-2 text-center manuscript-img-wrapper" style="overflow: auto; min-height: 400px; display: flex; align-items: center; justify-content: center;">
                                                                <img src="{{ $pUrl }}" alt="Manuscript Page {{ $imgIdx + 1 }}" class="img-fluid rounded shadow-sm mx-auto d-block manuscript-zoomable-img" style="max-width: 100%; transition: transform 0.2s ease; transform-origin: center center; cursor: grab;">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <!-- Container for PDF Documents -->
                                            <div id="pdfViewPane" class="manuscript-scroll-pane flex-grow-1" style="{{ $manuscriptImages->isNotEmpty() ? 'display: none;' : '' }} height: 100%; overflow-y: auto;">
                                                @if ($manuscriptPdfs->isNotEmpty())
                                                    @php
                                                        $primaryPdfUrl = $resolveManuscriptUrl($manuscriptPdfs->first()->file_path);
                                                    @endphp
                                                    <iframe id="manuscriptPdfIframe" src="{{ $primaryPdfUrl }}#toolbar=1&navpanes=0" width="100%" height="100%" style="border: 0; min-height: 550px; height: 100%;"></iframe>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-center py-5 px-3 my-auto">
                                                <em class="icon ni ni-file-text display-4 text-muted mb-3 d-block"></em>
                                                <h6 class="text-dark fw-bold mb-1">No Manuscript Uploaded Yet</h6>
                                                <p class="text-muted small mb-3">Analyst paper manuscript has not been uploaded for this sample registration.</p>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadManuscriptDirectModal">
                                                    <em class="icon ni ni-upload me-1"></em> Upload Manuscript Now
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /#splitViewRow -->
                    </div> <!-- /.nk-block -->
                </div> <!-- /#mainContainerWrapper -->
            </div> <!-- /.nk-content-body -->
        </div> <!-- /.nk-content-inner -->
    </div> <!-- /.container-fluid -->

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
                <div class="modal-body p-0 bg-dark" style="max-height: 80vh; overflow-y: auto;">
                    @if ($hasManuscript)
                        @if ($manuscriptImages->isNotEmpty())
                            <div class="p-3 text-center">
                                @foreach ($manuscriptImages as $mIdx => $mItem)
                                    @php
                                        $modalImgUrl = $resolveManuscriptUrl($mItem->file_path);
                                        $modalImgDown = $resolveManuscriptDownloadUrl($mItem->file_path);
                                    @endphp
                                    <div class="mb-4 pb-2 border-bottom border-secondary text-center">
                                        <div class="d-flex justify-content-between align-items-center mb-2 px-2 text-white small">
                                            <span class="fw-bold">
                                                <em class="icon ni ni-file-text text-primary me-1"></em> Page {{ $mIdx + 1 }} of {{ $manuscriptImages->count() }}
                                                @if (!empty($mItem->analyst_name))
                                                    <span class="text-white-50 ms-1">({{ $mItem->analyst_name }})</span>
                                                @endif
                                            </span>
                                            <a href="{{ $modalImgDown }}" target="_blank" download class="btn btn-xs btn-outline-light">
                                                <em class="icon ni ni-download me-1"></em> Download Page {{ $mIdx + 1 }}
                                            </a>
                                        </div>
                                        <img src="{{ $modalImgUrl }}" alt="Manuscript Photo Pg {{ $mIdx + 1 }}" class="img-fluid rounded shadow-sm mx-auto d-block" style="max-height: 75vh; object-fit: contain;">
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($manuscriptPdfs->isNotEmpty())
                            @php
                                $modalPdfUrl = $resolveManuscriptUrl($manuscriptPdfs->first()->file_path);
                            @endphp
                            <div class="p-0" style="height: 75vh;">
                                <iframe src="{{ $modalPdfUrl }}#toolbar=1" width="100%" height="100%" style="border: 0; min-height: 75vh;"></iframe>
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
                            <div>
                                @if ($manuscriptImages->count() > 1)
                                    <span class="text-muted small fw-bold">{{ $manuscriptImages->count() }} Manuscript Pages Loaded</span>
                                @else
                                    <a href="{{ $downloadUrl }}" target="_blank" download class="btn btn-primary btn-sm">
                                        <em class="icon ni ni-download me-1"></em> Download File
                                    </a>
                                @endif
                            </div>
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
                <form action="{{ route('testresult_upload', optional($sampleTests->first()?->registration)->tr04_reference_id ?? '') }}" method="POST" enctype="multipart/form-data" id="multiManuscriptUploadForm">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white"><em class="icon ni ni-upload me-1"></em> Upload Paper Manuscript</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Upload single or multi-page photos/PDF of the raw calculation paper for Sample Ref: <strong>{{ optional($sampleTests->first()?->registration)->tr04_reference_id ?? '' }}</strong>.</p>
                        
                        @php
                            $allottedAnalysts = $sampleTests->map(fn($st) => $st->allotedTo)->filter()->unique('m06_employee_id');
                        @endphp
                        @if ($allottedAnalysts->count() > 1)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Belongs to Analyst Allotment</label>
                                <select name="analyst_id" class="form-select form-select-sm">
                                    <option value="">All Tests / General Sample Manuscript</option>
                                    @foreach ($allottedAnalysts as $emp)
                                        <option value="{{ $emp->m06_employee_id }}">{{ $emp->m06_name }} ({{ $sampleTests->where('m06_alloted_to', $emp->m06_employee_id)->count() }} tests)</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">If different analysts completed separate paper sheets, select the respective analyst.</small>
                            </div>
                        @elseif ($allottedAnalysts->count() === 1)
                            <input type="hidden" name="analyst_id" value="{{ $allottedAnalysts->first()->m06_employee_id }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                <span>Select Document(s) / Photo(s)</span>
                                <span class="badge bg-light text-primary">Multi-Page Supported</span>
                            </label>
                            <input type="file" name="result_files[]" id="manuscriptUploadInput" class="form-control" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" multiple required onchange="handleManuscriptFileSelect(this)">
                            <div class="form-text text-muted small mt-1">
                                <em class="icon ni ni-info me-1"></em>You can select multiple page photos at once (Page 1, 2, 3...). High-res images are automatically compressed before storage to prevent file size errors.
                            </div>
                        </div>

                        <div id="fileSelectionSummary" class="border rounded p-2 bg-light d-none mb-3">
                            <div class="fw-bold text-dark fs-12px mb-1" id="fileCountBadge">Selected Files:</div>
                            <ul class="list-unstyled mb-0 small text-muted" id="fileListNames" style="max-height: 120px; overflow-y: auto;"></ul>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small text-muted">Optional Remarks / Note</label>
                            <input type="text" name="remarks" class="form-control form-control-sm" placeholder="e.g. Raw titration sheet, instrument printout...">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="uploadSubmitBtn"><em class="icon ni ni-upload me-1"></em> Upload Manuscript</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Test Standard Modal -->
    <div class="modal fade" id="changeStandardModal" tabindex="-1" aria-labelledby="changeStandardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h6 class="modal-title fs-14px fw-bold text-white mb-0" id="changeStandardModalLabel">
                        <em class="icon ni ni-property-alt me-1"></em> Change Test Standard
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center">
                        <em class="icon ni ni-flask fs-20px me-2 text-info"></em>
                        <div>
                            <div class="fw-bold fs-13px" id="modalTestName">Test Parameter Name</div>
                            <div class="text-muted fs-11px" id="modalStandardTip">Select the standard test method followed.</div>
                        </div>
                    </div>

                    <input type="hidden" id="modalSampleTestId" value="">
                    <input type="hidden" id="modalTestId" value="">
                    <input type="hidden" id="modalIsFibre" value="0">

                    <!-- Single Standard Selection (Normal Tests) -->
                    <div id="singleStandardContainer" class="form-group mb-3">
                        <label class="form-label fw-semibold fs-12px" for="singleStandardSelect">
                            Select Standard Test Method <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm" id="singleStandardSelect">
                            <!-- Populated dynamically -->
                        </select>
                        <div class="form-text fs-11px text-muted mt-1">
                            Choose from the associated standard methods configured for this test.
                        </div>
                    </div>

                    <!-- Multiple Standard Selection (% Fibre Blend Composition) -->
                    <div id="multipleStandardContainer" class="form-group mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold fs-12px mb-0">
                                Select Associated Standards (Multiple Allowed) <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-primary-dim text-primary fs-11px">Multi-Selection</span>
                        </div>
                        <p class="text-muted fs-11px mb-2">
                            Check all associated standard methods used for determining this composition:
                        </p>
                        
                        <!-- Search filter for standards -->
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white"><em class="icon ni ni-search"></em></span>
                            <input type="text" class="form-control form-control-sm" id="filterFibreStandards" placeholder="Type to filter standards...">
                        </div>

                        <div class="border rounded p-2 bg-white" style="max-height: 240px; overflow-y: auto;" id="multipleStandardsChecklist">
                            <!-- Populated dynamically with checkboxes -->
                        </div>

                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-primary fw-semibold"><span id="selectedCountText">0</span> standard(s) selected</small>
                            <small class="text-muted text-truncate ms-2" style="max-width: 260px;" id="fibreSelectedPreview"></small>
                        </div>
                    </div>

                    <div class="alert alert-light border py-2 px-3 small text-muted d-flex align-items-center mb-0">
                        <em class="icon ni ni-info me-2 text-primary fs-18px"></em>
                        <div class="fs-11px">
                            Updating the standard takes effect immediately in registration records and will appear on the final report sheet.
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnSaveStandard">
                        <span class="btn-text"><em class="icon ni ni-check me-1"></em> Update Standard</span>
                        <span class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Remark Modal & Memory Library -->
    <div class="modal fade" id="testRemarkModal" tabindex="-1" aria-labelledby="testRemarkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-info text-white py-2 px-3">
                    <h6 class="modal-title fs-14px fw-bold text-white mb-0" id="testRemarkModalLabel">
                        <em class="icon ni ni-notes me-1"></em> <span id="modalRemarkHeading">Test Remark</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div id="modalRemarkSubmittedNotice" class="alert alert-warning py-2 px-3 small mb-3 d-flex align-items-center" style="display: none;">
                        <em class="icon ni ni-alert-circle fs-18px me-2 text-warning"></em>
                        <div>
                            <strong>Submitted Result (Locked)</strong>
                            <div class="fs-11px text-muted">Test results have been submitted and finalized. Remarks are in read-only mode and cannot be modified.</div>
                        </div>
                    </div>

                    <input type="hidden" id="modalRemarkRowIndex" value="">
                    <input type="hidden" id="modalRemarkTestId" value="">
                    <input type="hidden" id="modalRemarkTestNumber" value="">
                    <input type="hidden" id="modalRemarkId" value="">

                    <!-- Past Remark Suggestions Memory -->
                    <div class="mb-3" id="remarkSuggestionsWrapper">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold fs-12px mb-0 text-secondary">
                                <em class="icon ni ni-histroy text-info me-1"></em> Past Remarks for This Test:
                            </label>
                            <span id="suggestionsSpinner" class="spinner-border spinner-border-sm text-info d-none" role="status" aria-hidden="true"></span>
                        </div>
                        <div id="remarkSuggestionsBox" class="border rounded p-2 bg-light d-flex flex-wrap gap-1" style="max-height: 120px; overflow-y: auto;">
                            <span class="text-muted fs-11px">Loading suggestions...</span>
                        </div>
                        <div class="fs-11px text-muted mt-1">
                            Click any past suggestion above to quickly load it, or enter a new remark below.
                        </div>
                    </div>

                    <!-- Remark Input -->
                    <div class="form-group mb-2">
                        <label class="form-label fw-semibold fs-12px" for="modalRemarkInput">
                            Remark Text
                        </label>
                        <textarea class="form-control form-control-sm" id="modalRemarkInput" rows="3" placeholder="Enter specific observation, deviation, or condition for this test..."></textarea>
                        <div class="fs-11px text-muted mt-1">
                            This remark will be printed on the last page of the final report before the QR code with sequential star notation (*, **, etc.).
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnDeleteRemark">
                        <em class="icon ni ni-trash me-1"></em> Remove Remark
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-info text-white fw-bold" id="btnApplyRemark">
                            <em class="icon ni ni-check me-1"></em> Apply Remark
                        </button>
                    </div>
                </div>
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
        #mainContainerWrapper {
            max-width: 100% !important;
            width: 100% !important;
        }

        .wysiwyg-editor-input {
            min-width: 110px !important;
            min-height: 31px !important;
            white-space: nowrap !important;
            overflow-x: auto !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            flex: 1 1 auto !important;
        }
        .wysiwyg-editor-input:focus {
            outline: 2px solid #0965f1;
            background-color: #ffffff !important;
        }
        .wysiwyg-editor-input:empty:before {
            content: attr(data-placeholder);
            color: #94a3b8;
        }

        #testResultsTable .input-group {
            flex-wrap: nowrap !important;
        }

        .btn-xs {
            padding: 0.22rem 0.45rem !important;
            font-size: 0.72rem !important;
            line-height: 1.2 !important;
            border-radius: 4px !important;
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
            padding: 0.5rem 0.65rem;
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

        /* =========================================================
           SPLIT VIEW: Independent Scrolling for Form & Manuscript
           ========================================================= */
        .manuscript-preview-card {
            border-radius: 8px !important;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08) !important;
            display: flex;
            flex-direction: column;
        }

        .manuscript-page-card {
            transition: box-shadow 0.2s ease;
        }
        .manuscript-page-card:hover {
            box-shadow: 0 4px 14px rgba(0,0,0,0.25) !important;
        }

        /* Override DashLite spacer classes that set height: 0.75rem on .gap-2 and height: 0.375rem on .gap-1 */
        .d-flex.gap-1, .d-inline-flex.gap-1,
        .d-flex.gap-2, .d-inline-flex.gap-2,
        .d-flex.gap-3, .d-inline-flex.gap-3,
        .btn.gap-1, .btn.gap-2, .btn.gap-3 {
            height: auto !important;
        }

        /* Sticky Action Bar at Bottom of Form Column */
        .form-sticky-action-bar {
            position: sticky !important;
            bottom: 0 !important;
            flex-shrink: 0 !important;
            height: auto !important;
            min-height: 56px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 -4px 18px rgba(0, 0, 0, 0.12) !important;
            margin-top: 14px !important;
            margin-bottom: 0 !important;
            z-index: 50 !important;
        }
        .form-sticky-action-bar .card-body {
            height: auto !important;
            min-height: 54px !important;
        }
        .form-sticky-action-bar .btn {
            height: 36px !important;
            display: inline-flex !important;
            align-items: center !important;
            font-size: 0.8125rem !important;
            line-height: 1.5 !important;
        }

        #previewDownloadDropdown::after,
        .manuscript-preview-card .dropdown-toggle::after {
            display: none !important;
        }

        .manuscript-preview-card .btn-xs,
        .manuscript-preview-toolbar .btn-xs {
            height: 28px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            line-height: 1 !important;
        }

        .manuscript-preview-toolbar .form-select-xs {
            height: 28px !important;
            font-size: 11px !important;
            line-height: 1 !important;
        }

        .manuscript-preview-toolbar {
            white-space: nowrap !important;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .manuscript-preview-toolbar::-webkit-scrollbar {
            display: none;
        }

        @media (min-width: 992px) {
            body.split-active-mode {
                overflow: hidden !important;
            }

            .split-active-mode .nk-content {
                padding-top: 8px !important;
                padding-bottom: 4px !important;
            }

            .split-active-mode .nk-footer {
                display: none !important;
            }

            .split-active #splitViewRow {
                display: flex !important;
                align-items: stretch !important;
                margin-left: -0.5rem;
                margin-right: -0.5rem;
                overflow: hidden !important;
            }

            /* LEFT COLUMN: Test Result Entry Form */
            .split-active #resultFormCol {
                height: 100% !important;
                max-height: 100% !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding-right: 12px !important;
                padding-bottom: 12px !important;
                scrollbar-width: thin;
                scrollbar-color: #94a3b8 #f1f5f9;
            }

            .split-active #resultFormCol::-webkit-scrollbar {
                width: 7px;
            }

            .split-active #resultFormCol::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }

            .split-active #resultFormCol::-webkit-scrollbar-thumb {
                background: #94a3b8;
                border-radius: 4px;
            }

            .split-active #resultFormCol::-webkit-scrollbar-thumb:hover {
                background: #64748b;
            }

            /* RIGHT COLUMN: Manuscript Live Preview */
            .split-active #manuscriptPreviewCol {
                height: 100% !important;
                max-height: 100% !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                padding-left: 6px !important;
                padding-right: 0 !important;
            }

            .split-active .manuscript-preview-card {
                height: 100% !important;
                max-height: 100% !important;
                min-height: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                margin-bottom: 0 !important;
                overflow: hidden !important;
            }

            .split-active .manuscript-preview-card .card-header,
            .split-active .manuscript-preview-card .manuscript-preview-toolbar {
                flex-shrink: 0 !important;
            }

            .split-active .manuscript-preview-card .card-body {
                flex: 1 1 0 !important;
                min-height: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                padding: 0 !important;
            }

            .split-active .manuscript-scroll-pane {
                flex: 1 1 0 !important;
                min-height: 0 !important;
                overflow-y: auto !important;
                overflow-x: auto !important;
                scrollbar-width: thin;
                scrollbar-color: #475569 #1e2227;
            }

            .split-active .manuscript-scroll-pane::-webkit-scrollbar {
                width: 7px;
                height: 7px;
            }

            .split-active .manuscript-scroll-pane::-webkit-scrollbar-track {
                background: #1e2227;
            }

            .split-active .manuscript-scroll-pane::-webkit-scrollbar-thumb {
                background: #475569;
                border-radius: 4px;
            }

            .split-active .manuscript-scroll-pane::-webkit-scrollbar-thumb:hover {
                background: #64748b;
            }
        }

        @media (max-width: 991.98px) {
            #resultFormCol {
                margin-bottom: 1.5rem;
            }
            .manuscript-preview-card {
                min-height: 500px;
            }
            .manuscript-scroll-pane {
                max-height: 550px;
                overflow-y: auto;
            }
        }

        @media print {

            .btn,
            .nk-header,
            .nk-footer,
            #manuscriptPreviewCol,
            #floatingShowPreviewBtn,
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

            const isResultSubmitted = {{ $isResultSubmitted ? 'true' : 'false' }};
            let currentActiveRemarkRow = null;

            // Function to recalculate sequential star notation (*, **, ***) for all rows with remarks
            window.recalculateRemarksStars = function() {
                let count = 0;
                document.querySelectorAll('tr').forEach(row => {
                    const remarkInput = row.querySelector('.row-remark-text');
                    if (!remarkInput) return;

                    const remarkVal = (remarkInput.value || '').trim();
                    const starElem = row.querySelector('.row-remark-star');
                    const btn = row.querySelector('.btn-open-remark');

                    if (remarkVal !== '') {
                        count++;
                        const starStr = '*'.repeat(count);
                        if (starElem) {
                            starElem.textContent = starStr;
                            starElem.style.display = '';
                            starElem.setAttribute('title', 'Remark: ' + remarkVal + ' (' + starStr + ')');
                        }
                        if (btn) {
                            btn.classList.remove('btn-outline-light');
                            btn.classList.add('btn-info', 'text-white');
                            btn.setAttribute('title', 'Remark: ' + remarkVal + ' (' + starStr + ')');
                        }
                    } else {
                        if (starElem) {
                            starElem.textContent = '';
                            starElem.style.display = 'none';
                        }
                        if (btn) {
                            btn.classList.remove('btn-info', 'text-white');
                            btn.classList.add('btn-outline-light');
                            btn.setAttribute('title', 'Add / View Remark');
                        }
                    }
                });
            };

            // Run initial recalculation
            recalculateRemarksStars();

            // Handle clicking the Remark button
            $(document).on('click', '.btn-open-remark', function() {
                const btn = $(this);
                const row = btn.closest('tr');
                currentActiveRemarkRow = row;

                const testId = btn.data('test-id') || '';
                const testNumber = btn.data('test-number') || '';
                const testName = btn.data('test-name') || 'Test';

                const currentRemark = (row.find('.row-remark-text').val() || '').trim();
                const currentRemarkId = row.find('.row-remark-id').val() || '';

                $('#modalRemarkHeading').text(`Remark - ${testName}`);
                $('#modalRemarkTestId').val(testId);
                $('#modalRemarkTestNumber').val(testNumber);
                $('#modalRemarkId').val(currentRemarkId);
                $('#modalRemarkInput').val(currentRemark);

                // Enforce read-only state if results are submitted
                if (isResultSubmitted) {
                    $('#modalRemarkSubmittedNotice').show();
                    $('#modalRemarkInput').prop('disabled', true);
                    $('#btnApplyRemark').hide();
                    $('#btnDeleteRemark').hide();
                    $('#remarkSuggestionsWrapper').hide();
                } else {
                    $('#modalRemarkSubmittedNotice').hide();
                    $('#modalRemarkInput').prop('disabled', false);
                    $('#btnApplyRemark').show();
                    if (currentRemark) {
                        $('#btnDeleteRemark').show();
                    } else {
                        $('#btnDeleteRemark').hide();
                    }
                    $('#remarkSuggestionsWrapper').show();

                    // Load past remark suggestions from memory
                    loadRemarkSuggestions(testId, testNumber);
                }

                $('#testRemarkModal').modal('show');
            });

            // Load suggestions via AJAX
            function loadRemarkSuggestions(testId, testNumber) {
                $('#suggestionsSpinner').removeClass('d-none');
                $('#remarkSuggestionsBox').html('<span class="text-muted fs-11px">Fetching suggestions from memory...</span>');

                $.ajax({
                    url: '{{ route('get_remark_suggestions') }}',
                    method: 'GET',
                    data: { test_id: testId, test_number: testNumber },
                    dataType: 'json',
                    success: function(res) {
                        $('#suggestionsSpinner').addClass('d-none');
                        if (res.success && res.suggestions && res.suggestions.length > 0) {
                            let html = '';
                            res.suggestions.forEach(item => {
                                const badgeClass = item.is_my_remark ? 'btn-primary' : (item.is_my_ro ? 'btn-outline-primary' : 'btn-outline-secondary');
                                const safeText = $('<div>').text(item.text).html();
                                const tip = item.is_my_remark ? 'Previously used by you' : (item.is_my_ro ? 'Used in your lab' : 'Past remark');
                                html += `<button type="button" class="btn btn-xs ${badgeClass} text-start suggestion-pill text-truncate" style="max-width: 100%;" data-id="${item.id}" data-text="${safeText}" title="${tip}">
                                    <em class="icon ni ni-arrow-right me-1"></em> ${safeText}
                                </button>`;
                            });
                            $('#remarkSuggestionsBox').html(html);
                        } else {
                            $('#remarkSuggestionsBox').html('<span class="text-muted fs-11px">No previous remarks recorded for this test yet. Enter one below!</span>');
                        }
                    },
                    error: function() {
                        $('#suggestionsSpinner').addClass('d-none');
                        $('#remarkSuggestionsBox').html('<span class="text-muted fs-11px">Could not load suggestions.</span>');
                    }
                });
            }

            // Click suggestion pill to insert into textarea
            $(document).on('click', '.suggestion-pill', function() {
                const text = $(this).data('text');
                const remId = $(this).data('id');
                $('#modalRemarkInput').val(text);
                $('#modalRemarkId').val(remId);
                $('#btnDeleteRemark').show();
            });

            // Save / Apply Remark
            $('#btnApplyRemark').on('click', function() {
                if (!currentActiveRemarkRow) return;

                const text = ($('#modalRemarkInput').val() || '').trim();
                let remId = $('#modalRemarkId').val() || '';
                const testId = $('#modalRemarkTestId').val();
                const testNumber = $('#modalRemarkTestNumber').val();

                currentActiveRemarkRow.find('.row-remark-text').val(text);
                currentActiveRemarkRow.find('.row-remark-id').val(remId);

                // Auto-save new custom remark to memory library if text provided
                if (text && !remId) {
                    $.ajax({
                        url: '{{ route('save_remark_library') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            test_id: testId,
                            test_number: testNumber,
                            remark_text: text
                        },
                        success: function(res) {
                            if (res.success && res.remark_id) {
                                currentActiveRemarkRow.find('.row-remark-id').val(res.remark_id);
                            }
                        }
                    });
                }

                recalculateRemarksStars();
                $('#testRemarkModal').modal('hide');
            });

            // Remove Remark
            $('#btnDeleteRemark').on('click', function() {
                if (!currentActiveRemarkRow) return;
                currentActiveRemarkRow.find('.row-remark-text').val('');
                currentActiveRemarkRow.find('.row-remark-id').val('');
                recalculateRemarksStars();
                $('#testRemarkModal').modal('hide');
            });

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

            // Delegated click handler for adding custom field on any row (test, primary, secondary)
            $(document).on('click', '.add-custom-field', function(e) {
                e.preventDefault();
                const testNumber = $(this).attr('data-test-number');
                const primaryTestId = $(this).attr('data-primary-test-id') || null;
                const secondaryTestId = $(this).attr('data-secondary-test-id') || null;
                const type = $(this).attr('data-type') || (secondaryTestId ? 'secondary' : (primaryTestId ? 'primary' : 'test'));

                addCustomField(testNumber, primaryTestId, secondaryTestId, type);
            });

            // --- Independent Scrolling Layout Calculation ---
            function adjustSplitLayoutHeight() {
                const $wrapper = $('#mainContainerWrapper');
                const isSplit = $wrapper.hasClass('split-active') && !$('#manuscriptPreviewCol').hasClass('d-none');
                
                if (!isSplit || window.innerWidth < 992) {
                    $('#splitViewRow').css({ 'height': '', 'max-height': '' });
                    $('body').removeClass('split-active-mode');
                    return;
                }

                $('body').addClass('split-active-mode');
                
                // Ensure outer window scroll is 0
                if (window.scrollY > 0) {
                    window.scrollTo(0, 0);
                }

                const rowEl = document.getElementById('splitViewRow');
                if (!rowEl) return;

                const rect = rowEl.getBoundingClientRect();
                // 50px clearance guarantees form sticky action bar is fully elevated & completely visible
                const availableHeight = Math.floor(window.innerHeight - rect.top - 50);
                const finalHeight = Math.max(availableHeight, 380);

                $('#splitViewRow').css({
                    'height': finalHeight + 'px',
                    'max-height': finalHeight + 'px'
                });
            }

            $(window).on('resize orientationchange', function() {
                adjustSplitLayoutHeight();
            });

            // --- Side-by-Side Manuscript Preview Toggle ---
            function setSplitPreview(show) {
                const $formCol = $('#resultFormCol');
                const $previewCol = $('#manuscriptPreviewCol');
                const $btn = $('#toggleManuscriptSplitBtn');
                const $quickBtn = $('#quickToggleSplitBtn');
                const $wrapper = $('#mainContainerWrapper');

                if (show) {
                    $previewCol.removeClass('d-none');
                    $formCol.removeClass('col-12').addClass('col-12 col-lg-6');
                    $wrapper.addClass('split-active');
                    $('#splitBtnLabel').text('Hide Manuscript Preview');
                    $btn.removeClass('btn-outline-primary').addClass('btn-primary');
                    if ($quickBtn.length) {
                        $quickBtn.removeClass('btn-light text-primary').addClass('btn-secondary text-white');
                    }
                    localStorage.setItem('lims_manuscript_split_preview', 'show');
                    setTimeout(adjustSplitLayoutHeight, 50);
                    setTimeout(adjustSplitLayoutHeight, 250);
                } else {
                    $previewCol.addClass('d-none');
                    $formCol.removeClass('col-lg-6').addClass('col-12');
                    $wrapper.removeClass('split-active');
                    $('#splitBtnLabel').text('Show Manuscript Preview');
                    $btn.removeClass('btn-primary').addClass('btn-outline-primary');
                    if ($quickBtn.length) {
                        $quickBtn.removeClass('btn-secondary text-white').addClass('btn-light text-primary');
                    }
                    localStorage.setItem('lims_manuscript_split_preview', 'hide');
                    adjustSplitLayoutHeight();
                }
            }

            $(document).on('click', '#toggleManuscriptSplitBtn, #quickToggleSplitBtn', function(e) {
                e.preventDefault();
                const isCurrentlyHidden = $('#manuscriptPreviewCol').hasClass('d-none');
                setSplitPreview(isCurrentlyHidden);
            });

            $(document).on('click', '#closeSplitPreviewBtn', function(e) {
                e.preventDefault();
                setSplitPreview(false);
            });

            // Initial Split View State
            const hasManuscriptFile = {{ $hasManuscript ? 'true' : 'false' }};
            const savedSplitState = localStorage.getItem('lims_manuscript_split_preview');
            if (savedSplitState === 'hide') {
                setSplitPreview(false);
            } else if (savedSplitState === 'show' || hasManuscriptFile) {
                setSplitPreview(true);
            } else {
                setSplitPreview(false);
            }
            setTimeout(adjustSplitLayoutHeight, 80);
            setTimeout(adjustSplitLayoutHeight, 300);
            setTimeout(adjustSplitLayoutHeight, 700);
            $(window).on('load', function() {
                setTimeout(adjustSplitLayoutHeight, 100);
            });

            // --- Multi-Image & Multi-Page Manuscript Navigation ---
            let currentManuscriptPageIndex = 0;
            let isSinglePageMode = false;
            const totalManuscriptPages = {{ $manuscriptImages->count() }};

            function goToManuscriptPage(idx) {
                if (idx < 0 || idx >= totalManuscriptPages) return;
                currentManuscriptPageIndex = idx;

                // Sync page select dropdown
                $('#pageSelectDropdown').val(idx);

                // Update Prev / Next button state
                $('#prevPageBtn').prop('disabled', idx === 0);
                $('#nextPageBtn').prop('disabled', idx >= totalManuscriptPages - 1);

                if (isSinglePageMode) {
                    $('.manuscript-page-card').hide();
                    $(`#manuscriptPage_${idx}`).fadeIn(150);
                } else {
                    $('.manuscript-page-card').show();
                    const targetEl = document.getElementById(`manuscriptPage_${idx}`);
                    if (targetEl) {
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }

            $(document).on('change', '#pageSelectDropdown', function() {
                const idx = parseInt($(this).val(), 10);
                goToManuscriptPage(idx);
            });

            $(document).on('click', '.btn-quick-jump-sheet', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const pIdx = parseInt($(this).data('page-idx'), 10);
                if ($('#manuscriptPreviewCol').is(':hidden')) {
                    $('#toggleManuscriptSplitBtn').trigger('click');
                }
                goToManuscriptPage(pIdx);
            });

            $(document).on('click', '#prevPageBtn', function(e) {
                e.preventDefault();
                if (currentManuscriptPageIndex > 0) {
                    goToManuscriptPage(currentManuscriptPageIndex - 1);
                }
            });

            $(document).on('click', '#nextPageBtn', function(e) {
                e.preventDefault();
                if (currentManuscriptPageIndex < totalManuscriptPages - 1) {
                    goToManuscriptPage(currentManuscriptPageIndex + 1);
                }
            });

            $(document).on('click', '#viewModeAllBtn', function(e) {
                e.preventDefault();
                isSinglePageMode = false;
                $(this).addClass('btn-primary active').removeClass('btn-outline-light');
                $('#viewModeSingleBtn').removeClass('btn-primary active').addClass('btn-outline-light');
                $('.manuscript-page-card').show();
                goToManuscriptPage(currentManuscriptPageIndex);
            });

            $(document).on('click', '#viewModeSingleBtn', function(e) {
                e.preventDefault();
                isSinglePageMode = true;
                $(this).addClass('btn-primary active').removeClass('btn-outline-light');
                $('#viewModeAllBtn').removeClass('btn-primary active').addClass('btn-outline-light');
                $('.manuscript-page-card').hide();
                $(`#manuscriptPage_${currentManuscriptPageIndex}`).show();
            });

            // Document switcher between Photos and PDF
            $(document).on('click', '.doc-type-tab', function(e) {
                e.preventDefault();
                $('.doc-type-tab').removeClass('active btn-primary').addClass('btn-outline-light');
                $(this).addClass('active btn-primary').removeClass('btn-outline-light');
                const target = $(this).data('target');
                if (target === 'imagesView') {
                    $('#imagesViewPane').show();
                    $('#pdfViewPane').hide();
                    $('#imagePageNavigation').show();
                } else {
                    $('#imagesViewPane').hide();
                    $('#pdfViewPane').show();
                    $('#imagePageNavigation').hide();
                }
            });

            // PDF Dropdown switcher
            $(document).on('change', '#pdfSelectorDropdown', function() {
                const newUrl = $(this).val();
                if (newUrl) {
                    $('#manuscriptPdfIframe').attr('src', newUrl + '#toolbar=1&navpanes=0');
                }
            });

            // Image Zoom and Rotate Controls
            let imgZoomLevel = 1.0;
            let imgRotationDeg = 0;

            function updateImgTransform() {
                $('.manuscript-zoomable-img, #manuscriptSideImg').css({
                    'transform': `scale(${imgZoomLevel}) rotate(${imgRotationDeg}deg)`,
                    'transition': 'transform 0.2s ease'
                });
                $('#zoomPercentBadge').text(Math.round(imgZoomLevel * 100) + '%');
            }

            $(document).on('click', '#imgZoomInBtn', function(e) {
                e.preventDefault();
                imgZoomLevel = Math.min(imgZoomLevel + 0.25, 4.0);
                updateImgTransform();
            });

            $(document).on('click', '#imgZoomOutBtn', function(e) {
                e.preventDefault();
                imgZoomLevel = Math.max(imgZoomLevel - 0.25, 0.5);
                updateImgTransform();
            });

            $(document).on('click', '#imgRotateBtn', function(e) {
                e.preventDefault();
                imgRotationDeg = (imgRotationDeg + 90) % 360;
                updateImgTransform();
            });

            $(document).on('click', '#imgResetBtn', function(e) {
                e.preventDefault();
                imgZoomLevel = 1.0;
                imgRotationDeg = 0;
                updateImgTransform();
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
                    <td class="text-center align-middle">${getNextSerialNumber(testNumber)}</td>
                    <td class="align-middle">
                        <strong>${primaryTestName}</strong>
                    </td>
                    <td>
                        <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
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
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][remarks]"
                                class="row-remark-text"
                                value="">
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][remark_id]"
                                class="row-remark-id"
                                value="">
                            <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter result value">${defaultResultVal}</div>
                            <span class="input-group-text bg-white text-danger fw-bold row-remark-star px-1" style="display:none; font-size:13px; cursor:pointer;" title="Remark attached">*</span>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                            ${formulaId ? `
                                <button type="button"
                                    class="btn btn-outline-light btn-xs open-raw-entry px-2"
                                    data-target-name="results[${testNumber}][primary_tests][${primaryTestId}][result]"
                                    data-label="${primaryTestName}"
                                    title="Formula / Raw Entry">
                                    <em class="icon ni ni-calc"></em>
                                </button>
                            ` : ''}
                            <button type="button"
                                class="btn btn-xs btn-outline-light btn-open-remark px-2"
                                data-test-id="${testId}"
                                data-test-number="${testNumber}"
                                data-test-name="${primaryTestName}"
                                data-field-prefix="results[${testNumber}][primary_tests][${primaryTestId}]"
                                title="Add / Select Remark">
                                <em class="icon ni ni-notes"></em>
                            </button>
                            <input type="text"
                                class="form-control form-control-sm border-0 bg-light text-center px-1"
                                style="max-width: 75px; min-width: 55px;"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][unit]"
                                value="${primaryTestUnit}"
                                placeholder="Unit">
                        </div>
                    </td>
                    <td class="text-center align-middle" style="width: 140px;">
                        <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                            <button type="button"
                                class="btn btn-outline-danger btn-xs py-1 px-2 remove-test-row"
                                data-type="primary"
                                data-test-number="${testNumber}"
                                data-id="${primaryTestId}"
                                title="Remove Primary Test">
                                <em class="icon ni ni-trash"></em>
                            </button>
                            ${hasSecondaryTests ? `
                                <button type="button"
                                    class="btn btn-outline-success btn-xs py-1 px-2 fw-semibold add-secondary-test text-nowrap"
                                    data-test-number="${testNumber}"
                                    data-test-id="${testId}"
                                    data-primary-test-id="${primaryTestId}"
                                    data-secondary-tests='${JSON.stringify(secondaryTests)}'
                                    title="Add Secondary Tests">
                                    <em class="icon ni ni-plus"></em> Sec
                                </button>
                            ` : ''}
                            <button type="button"
                                class="btn btn-outline-warning btn-xs py-1 px-2 fw-semibold add-custom-field text-nowrap"
                                data-test-id="${testId}"
                                data-test-number="${testNumber}"
                                data-primary-test-id="${primaryTestId}"
                                data-type="primary"
                                title="Add Custom Field">
                                <em class="icon ni ni-plus"></em> Custom
                            </button>
                        </div>
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
                    <td class="text-center align-middle">${serialNumber}</td>
                    <td class="align-middle">${secondaryTest.m17_name}</td>
                    <td>
                        <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
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
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][remarks]"
                                class="row-remark-text"
                                value="">
                            <input type="hidden"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][remark_id]"
                                class="row-remark-id"
                                value="">
                            <div class="form-control form-control-sm border-0 bg-light wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter result value">${finalResultVal}</div>
                            <span class="input-group-text bg-white text-danger fw-bold row-remark-star px-1" style="display:none; font-size:13px; cursor:pointer;" title="Remark attached">*</span>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                            ${formulaId ? `
                                <button type="button"
                                    class="btn btn-outline-light btn-xs open-raw-entry px-2"
                                    data-target-name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][result]"
                                    data-label="${secondaryTest.m17_name}"
                                    title="Formula / Raw Entry">
                                    <em class="icon ni ni-calc"></em>
                                </button>
                            ` : ''}
                            <button type="button"
                                class="btn btn-xs btn-outline-light btn-open-remark px-2"
                                data-test-id="${testId}"
                                data-test-number="${testNumber}"
                                data-test-name="${secondaryTest.m17_name}"
                                data-field-prefix="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}]"
                                title="Add / Select Remark">
                                <em class="icon ni ni-notes"></em>
                            </button>
                            <input type="text"
                                class="form-control form-control-sm border-0 bg-light text-center px-1"
                                style="max-width: 75px; min-width: 55px;"
                                name="results[${testNumber}][primary_tests][${primaryTestId}][secondary_tests][${secondaryTestId}][unit]"
                                value="${unitValue}"
                                placeholder="Unit">
                        </div>
                    </td>
                    <td class="text-center align-middle" style="width: 140px;">
                        <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                            <button type="button"
                                class="btn btn-outline-danger btn-xs py-1 px-2 remove-test-row"
                                data-type="secondary"
                                data-test-number="${testNumber}"
                                data-primary-test-id="${primaryTestId}"
                                data-id="${secondaryTestId}"
                                title="Remove Secondary Test">
                                <em class="icon ni ni-trash"></em>
                            </button>
                            <button type="button"
                                class="btn btn-outline-warning btn-xs py-1 px-2 fw-semibold add-custom-field text-nowrap"
                                data-test-id="${currentTestId}"
                                data-test-number="${testNumber}"
                                data-primary-test-id="${primaryTestId}"
                                data-secondary-test-id="${secondaryTestId}"
                                data-type="secondary"
                                title="Add Custom Field">
                                <em class="icon ni ni-plus"></em> Custom
                            </button>
                        </div>
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

                if (type === 'secondary' && secondaryTestId && secondaryTestId !== 'NULL' && secondaryTestId !== '') {
                    const secondaryTestRow = document.querySelector(`[data-secondary-test-id="${secondaryTestId}"]`);
                    if (secondaryTestRow) {
                        const currentSerial = secondaryTestRow.cells[0].textContent.trim();
                        const existingCfs = document.querySelectorAll(`.custom-field-row[data-secondary-test-id="${secondaryTestId}"]`);
                        serialNumber = `${currentSerial}.C${existingCfs.length + 1}`;
                        insertAfter = existingCfs.length > 0 ? existingCfs[existingCfs.length - 1] : secondaryTestRow;
                    }
                } else if (type === 'primary' && primaryTestId && primaryTestId !== 'NULL' && primaryTestId !== '') {
                    const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                    if (primaryTestRow) {
                        const currentSerial = primaryTestRow.cells[0].textContent.trim();
                        const existingCfs = document.querySelectorAll(`.custom-field-row[data-primary-test-id="${primaryTestId}"]:not([data-secondary-test-id])`);
                        serialNumber = `${currentSerial}.C${existingCfs.length + 1}`;
                        insertAfter = existingCfs.length > 0 ? existingCfs[existingCfs.length - 1] : primaryTestRow;
                    }
                } else {
                    const testRow = document.querySelector(`.test-main-row[data-test-number="${testNumber}"]`);
                    if (testRow) {
                        const currentSerial = testRow.cells[0].textContent.trim();
                        const existingCfs = document.querySelectorAll(`.custom-field-row[data-test-number="${testNumber}"]:not([data-primary-test-id]):not([data-secondary-test-id])`);
                        serialNumber = `${currentSerial}.C${existingCfs.length + 1}`;
                        insertAfter = existingCfs.length > 0 ? existingCfs[existingCfs.length - 1] : testRow;
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
                    <td class="text-center align-middle">
                        <small class="fw-bold">${serialNumber}</small>
                    </td>
                    <td class="align-middle">
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
                        <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
                            <input type="hidden" 
                                name="${namePrefix}[value]" 
                                class="wysiwyg-hidden-val"
                                value="${fieldValue || ''}">
                            <div class="form-control form-control-sm custom-field-input wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter value">${fieldValue || ''}</div>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                            <input type="text" 
                                class="form-control form-control-sm custom-field-input text-center px-1" 
                                style="max-width: 75px; min-width: 55px;"
                                name="${namePrefix}[unit]" 
                                value="${fieldUnit || ''}"
                                placeholder="Unit">
                        </div>
                    </td>
                    <td class="text-center align-middle" style="width: 140px;">
                        <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                            <button type="button" class="btn btn-xs py-1 px-2 btn-outline-danger remove-custom-field" data-field-id="${fieldId}" title="Remove Custom Field">
                                <em class="icon ni ni-trash"></em>
                            </button>
                        </div>
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
                    const secondaryTestRow = document.querySelector(`[data-secondary-test-id="${secondaryTestId}"]`);
                    if (secondaryTestRow) {
                        const currentSerial = secondaryTestRow.cells[0].textContent.trim();
                        const existingCfs = document.querySelectorAll(`.custom-field-row[data-secondary-test-id="${secondaryTestId}"]`);
                        serialNumber = `${currentSerial}.C${existingCfs.length + 1}`;
                        insertAfter = existingCfs.length > 0 ? existingCfs[existingCfs.length - 1] : secondaryTestRow;
                    }
                } else if (type === 'primary' && primaryTestId) {
                    const primaryTestRow = document.querySelector(`[data-primary-test-id="${primaryTestId}"]`);
                    if (primaryTestRow) {
                        const currentSerial = primaryTestRow.cells[0].textContent.trim();
                        const existingCfs = document.querySelectorAll(`.custom-field-row[data-primary-test-id="${primaryTestId}"]:not([data-secondary-test-id])`);
                        serialNumber = `${currentSerial}.C${existingCfs.length + 1}`;
                        insertAfter = existingCfs.length > 0 ? existingCfs[existingCfs.length - 1] : primaryTestRow;
                    }
                } else {
                    const testRow = document.querySelector(`.test-main-row[data-test-number="${testNumber}"]`);
                    if (testRow) {
                        const currentSerial = testRow.cells[0].textContent.trim();
                        const existingCfs = document.querySelectorAll(`.custom-field-row[data-test-number="${testNumber}"]:not([data-primary-test-id]):not([data-secondary-test-id])`);
                        serialNumber = `${currentSerial}.C${existingCfs.length + 1}`;
                        insertAfter = existingCfs.length > 0 ? existingCfs[existingCfs.length - 1] : testRow;
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
                    <td class="text-center align-middle">
                        <small class="fw-bold">${serialNumber}</small>
                    </td>
                    <td class="align-middle">
                        <input type="text" 
                            class="form-control form-control-sm custom-field-input" 
                            name="${namePrefix}[name]" 
                            placeholder="Custom Field Name" required>
                    </td>
                    <td>
                        <div class="input-group input-group-sm flex-nowrap align-items-stretch" style="min-width: 250px;">
                            <input type="hidden" 
                                name="${namePrefix}[value]" 
                                class="wysiwyg-hidden-val"
                                value="">
                            <div class="form-control form-control-sm custom-field-input wysiwyg-editor-input"
                                contenteditable="true"
                                data-placeholder="Enter value"></div>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="superscript" title="Superscript (Ctrl + .)"><span style="font-weight:bold; font-size:11px;">x<sup>2</sup></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-wysiwyg-cmd px-2" data-cmd="subscript" title="Subscript (Ctrl + ,)"><span style="font-weight:bold; font-size:11px;">x<sub>2</sub></span></button>
                            <button type="button" class="btn btn-outline-light btn-xs btn-open-symbols px-2" title="Insert Symbol"><span style="font-weight:bold; font-size:11px;">Ω</span></button>
                            <input type="text" 
                                class="form-control form-control-sm custom-field-input text-center px-1" 
                                style="max-width: 75px; min-width: 55px;"
                                name="${namePrefix}[unit]" 
                                placeholder="Unit">
                        </div>
                    </td>
                    <td class="text-center align-middle" style="width: 140px;">
                        <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-nowrap">
                            <button type="button" class="btn btn-xs py-1 px-2 btn-outline-danger remove-custom-field" data-field-id="${fieldId}" title="Remove Custom Field">
                                <em class="icon ni ni-trash"></em>
                            </button>
                        </div>
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
                                if (window.recalculateRemarksStars) window.recalculateRemarksStars();
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

            // Standard Changing Logic - Strictly Associated Methods Only
            const testConfiguredStandards = {
                @foreach ($sampleTests as $st)
                    @if ($st->test)
                        @php
                            $associatedStandards = $st->test->standardsList ? $st->test->standardsList->keyBy('m15_standard_id') : collect();
                            // If sample test has a standard relation that is not in the list, add it
                            if ($st->standard && !$associatedStandards->has($st->standard->m15_standard_id)) {
                                $associatedStandards->put($st->standard->m15_standard_id, $st->standard);
                            }
                            if (!empty($st->m15_standard_id)) {
                                $currIds = array_filter(array_map('trim', explode(',', (string)$st->m15_standard_id)));
                                foreach ($currIds as $cId) {
                                    if (!$associatedStandards->has($cId) && isset($allStandards)) {
                                        $match = $allStandards->firstWhere('m15_standard_id', $cId);
                                        if ($match) {
                                            $associatedStandards->put($cId, $match);
                                        }
                                    }
                                }
                            }
                        @endphp
                        "{{ $st->tr05_sample_test_id }}": [
                            @foreach ($associatedStandards as $std)
                                { id: "{{ $std->m15_standard_id }}", name: @json($std->m15_method) },
                            @endforeach
                        ],
                        "test_{{ $st->test->m12_test_id }}": [
                            @foreach ($associatedStandards as $std)
                                { id: "{{ $std->m15_standard_id }}", name: @json($std->m15_method) },
                            @endforeach
                        ],
                    @endif
                @endforeach
            };

            function isFibreBlendTest(testName) {
                if (!testName) return false;
                const name = testName.toLowerCase();
                return name.includes('fibre blend') || name.includes('fiber blend') || name.includes('blend composition');
            }

            function escapeHtml(text) {
                if (!text) return '';
                return $('<div>').text(text).html();
            }

            function updateFibreCount() {
                const checked = $('#multipleStandardsChecklist .fibre-std-checkbox:checked');
                const count = checked.length;
                $('#selectedCountText').text(count);
                const names = [];
                checked.each(function() {
                    names.push($(this).data('name'));
                });
                if (names.length > 0) {
                    $('#fibreSelectedPreview').text(names.join(' & ')).show();
                } else {
                    $('#fibreSelectedPreview').hide();
                }
            }

            $(document).on('change', '.fibre-std-checkbox', updateFibreCount);

            $(document).on('input', '#filterFibreStandards', function() {
                const q = $(this).val().toLowerCase().trim();
                $('#multipleStandardsChecklist .fibre-std-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(q));
                });
            });

            $(document).on('click', '.btn-edit-standard', function() {
                const $btn = $(this);
                const sampleTestId = $btn.data('sample-test-id');
                const testId = $btn.data('test-id');
                const testName = $btn.data('test-name') || 'Test';
                const isFibre = $btn.data('is-fibre') == '1' || isFibreBlendTest(testName);
                const currentStandards = String($btn.attr('data-current-standards') || '').trim();
                const currentStdIds = currentStandards ? currentStandards.split(',').map(s => s.trim()) : [];

                $('#modalSampleTestId').val(sampleTestId);
                $('#modalTestId').val(testId);
                $('#modalIsFibre').val(isFibre ? '1' : '0');
                $('#modalTestName').text(testName);
                $('#filterFibreStandards').val('');

                const configured = testConfiguredStandards[sampleTestId] || testConfiguredStandards['test_' + testId] || [];

                if (isFibre) {
                    $('#singleStandardContainer').hide();
                    $('#multipleStandardContainer').show();
                    $('#modalStandardTip').text('Select multiple standard methods associated with this fibre blend composition test.');

                    let checklistHtml = '';

                    if (configured.length > 0) {
                        configured.forEach(s => {
                            const checked = currentStdIds.includes(String(s.id)) ? 'checked' : '';
                            checklistHtml += `
                                <div class="form-check mb-2 fibre-std-item">
                                    <input class="form-check-input fibre-std-checkbox" type="checkbox" value="${s.id}" id="chk_std_${s.id}" data-name="${escapeHtml(s.name)}" ${checked}>
                                    <label class="form-check-label fs-12px fw-medium" for="chk_std_${s.id}">
                                        ${escapeHtml(s.name)}
                                    </label>
                                </div>
                            `;
                        });
                    } else {
                        checklistHtml = '<div class="alert alert-warning py-2 px-3 fs-12px mb-0">No associated standards found for this test.</div>';
                    }

                    $('#multipleStandardsChecklist').html(checklistHtml);
                    updateFibreCount();
                } else {
                    $('#multipleStandardContainer').hide();
                    $('#singleStandardContainer').show();
                    $('#modalStandardTip').text('Select the standard method followed for this test.');

                    let selectHtml = '<option value="">-- Select Standard Method --</option>';

                    if (configured.length > 0) {
                        configured.forEach(s => {
                            const selected = currentStdIds.includes(String(s.id)) ? 'selected' : '';
                            selectHtml += `<option value="${s.id}" ${selected}>${escapeHtml(s.name)}</option>`;
                        });
                    } else {
                        selectHtml += '<option value="" disabled>No associated standards configured for this test</option>';
                    }

                    $('#singleStandardSelect').html(selectHtml);
                }

                $('#changeStandardModal').modal('show');
            });

            $('#btnSaveStandard').on('click', function() {
                const sampleTestId = $('#modalSampleTestId').val();
                const testId = $('#modalTestId').val();
                const isFibre = $('#modalIsFibre').val() === '1';

                let standardId = '';
                let standardNames = '';

                if (isFibre) {
                    const checked = $('#multipleStandardsChecklist .fibre-std-checkbox:checked');
                    const ids = [];
                    const names = [];
                    checked.each(function() {
                        ids.push($(this).val());
                        names.push($(this).data('name'));
                    });
                    standardId = ids.join(',');
                    standardNames = names.join(' & ');
                } else {
                    standardId = $('#singleStandardSelect').val() || '';
                    standardNames = $('#singleStandardSelect option:selected').text();
                    if (!standardId || standardId === '') {
                        standardNames = 'No standard assigned';
                    }
                }

                const $btn = $(this);
                $btn.prop('disabled', true);
                $btn.find('.spinner-border').removeClass('d-none');
                $btn.find('.btn-text').addClass('opacity-50');

                $.ajax({
                    url: "{{ route('update_test_standard') }}",
                    type: "POST",
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        sample_test_id: sampleTestId,
                        standard_id: standardId,
                        registration_id: "{{ $registrationId ?? ($sampleTests->first()?->registration?->tr04_reference_id ?? '') }}"
                    },
                    success: function(res) {
                        $btn.prop('disabled', false);
                        $btn.find('.spinner-border').addClass('d-none');
                        $btn.find('.btn-text').removeClass('opacity-50');

                        if (res && res.success) {
                            const displayName = res.standard_names || standardNames || 'No standard assigned';
                            const shortName = displayName.length > 30 ? (displayName.substring(0, 30) + '...') : displayName;
                            $(`#std-badge-${sampleTestId} .standard-method-text`).text(shortName);
                            $(`#std-badge-${sampleTestId}`).attr('title', displayName);
                            $(`#std-input-${sampleTestId}`).val(res.standard_id || standardId);
                            $(`.btn-edit-standard[data-sample-test-id="${sampleTestId}"]`).attr('data-current-standards', res.standard_id || standardId);

                            $('#changeStandardModal').modal('hide');

                            // Refresh live preview iframe if split preview is active
                            const liveIframe = document.getElementById('liveReportPreviewIframe');
                            if (liveIframe && liveIframe.contentWindow && !$('#splitPreviewContainer').hasClass('d-none')) {
                                try {
                                    liveIframe.contentWindow.location.reload();
                                } catch (e) {}
                            }

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Standard Updated',
                                    text: `Updated method: ${displayName}`,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        } else {
                            const errMsg = (res && res.message) ? res.message : 'Failed to update standard.';
                            Swal.fire('Error', errMsg, 'error');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        $btn.find('.spinner-border').addClass('d-none');
                        $btn.find('.btn-text').removeClass('opacity-50');
                        let msg = 'Error updating standard.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.statusText) {
                            msg += ' (' + xhr.status + ': ' + xhr.statusText + ')';
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

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

    <!-- Send Back for Clarification Modal -->
    <div class="modal fade" id="sendBackModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('sample.custody.send_back') }}" method="POST" id="custodySendBackForm">
                    @csrf
                    <input type="hidden" name="sample_id" value="{{ $regPkId }}">
                    <input type="hidden" name="to_role" value="{{ $defaultTargetRole }}">
                    <input type="hidden" name="context" value="{{ Session::get('role') === 'DEO' ? 'deo' : (Session::get('role') === 'Analyst' ? 'analyst' : 'allotment') }}">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white">
                            <em class="icon ni ni-undo me-1"></em> Send Back to {{ $defaultTargetRole }} for Clarification
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning py-2 mb-3">
                            <em class="icon ni ni-alert-circle me-1"></em>
                            @if(Session::get('role') === 'DEO')
                                Sending back sample <strong>{{ $refId }}</strong> to the <strong>Analyst</strong> will reset test status to in-progress so the analyst can correct and re-submit the manuscript calculation sheet.
                            @elseif(Session::get('role') === 'Analyst')
                                Sending back sample <strong>{{ $refId }}</strong> to the <strong>Manager</strong> will return test(s) to the unallotted pool so the manager can re-allot or correct the sample.
                            @else
                                Sending back sample <strong>{{ $refId }}</strong> to <strong>{{ $defaultTargetRole }}</strong> for clarification and correction.
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark" for="custodyRemarkTextarea">
                                Mandatory Clarification Remark <span class="text-danger">*</span>
                            </label>
                            <div class="form-control-wrap">
                                <textarea name="clarification_remark" id="custodyRemarkTextarea" class="form-control" rows="4" required minlength="3" placeholder="{{ Session::get('role') === 'DEO' ? 'Explain the manuscript ambiguity, missing values, illegible handwriting, or calculation discrepancies...' : (Session::get('role') === 'Analyst' ? 'Explain reason for sending back to Manager (e.g. sample damaged, insufficient quantity, wrong allotment)...' : 'Enter clear, actionable clarification remarks...') }}"></textarea>
                            </div>
                            <small class="text-muted">{{ Session::get('role') === 'DEO' ? 'The Analyst will see this exact message prominently on their screen.' : 'The recipient will see this clarification remark.' }}</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm fw-bold">
                            <em class="icon ni ni-send me-1"></em> Send Back to {{ $defaultTargetRole }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Result Entry Preview & Complete Modal -->
    <div class="modal fade" id="testResultPreviewModal" tabindex="-1" aria-labelledby="testResultPreviewModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title text-white fs-15px mb-0 d-flex align-items-center" id="testResultPreviewModalLabel">
                        <em class="icon ni ni-eye me-2"></em> Test Result Entry Preview
                        <span class="badge bg-light text-primary fw-bold ms-3">Report #{{ $sampleReg?->tr04_reference_id ?? 'N/A' }}</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- Verification Notice -->
                    <div class="alert alert-fill alert-light alert-icon border mb-3 py-2 px-3">
                        <em class="icon ni ni-alert-circle text-primary fs-5"></em>
                        <div class="small">
                            <strong>DEO Verification Check:</strong> Please review all entered results, units, and test methods below. Clicking <strong>Complete</strong> will mark result entry as finished and route this sample to the <strong>Verification Officer</strong>.
                        </div>
                    </div>

                    <!-- Report Meta Card -->
                    <div class="card bg-light border-0 shadow-none mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="row g-2 small">
                                <div class="col-md-3 col-6"><span class="text-muted">Test Report No:</span> <strong class="text-dark d-block">{{ $sampleReg?->tr04_reference_id ?? 'N/A' }}</strong></div>
                                <div class="col-md-3 col-6"><span class="text-muted">Test Date:</span> <strong class="text-dark d-block" id="modalPreviewTestDate">-</strong></div>
                                <div class="col-md-3 col-6"><span class="text-muted">Performance Date:</span> <strong class="text-dark d-block" id="modalPreviewPerfDate">-</strong></div>
                                <div class="col-md-3 col-6"><span class="text-muted">No. of Samples:</span> <strong class="text-dark d-block">{{ $sampleReg?->tr04_number_of_samples ?? '1' }}</strong></div>
                                <div class="col-md-3 col-6"><span class="text-muted">Sample Description:</span> <strong class="text-dark d-block">{{ optional($sampleReg?->labSample)->m14_name ?? 'N/A' }}</strong></div>
                                <div class="col-md-3 col-6"><span class="text-muted">Analyst:</span> <strong class="text-dark d-block">{{ optional($sampleTests->first()?->allotedTo)->m06_name ?? 'N/A' }}</strong></div>
                                <div class="col-md-6 col-12"><span class="text-muted">Technical Manager:</span> <strong class="text-dark d-block">{{ Session::get('role') === 'Manager' ? Session::get('name') : optional($sampleTests->first()?->allotedBy)->m06_name ?? 'N/A' }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Results Table -->
                    <h6 class="fs-13px text-uppercase text-muted fw-bold mb-2">Entered Test Results & Standards</h6>
                    <div class="table-responsive border rounded bg-white shadow-sm" style="max-height: 52vh; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0" id="previewResultsTable">
                            <thead class="table-dark small sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th style="width: 35%;">Test Name & Standard Method</th>
                                    <th style="width: 25%;">Parameter / Breakdown</th>
                                    <th style="width: 25%;">Result Value</th>
                                    <th class="text-center" style="width: 15%;">Unit</th>
                                </tr>
                            </thead>
                            <tbody id="previewResultsTableBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div id="previewRemarksContainer" class="mt-2 p-2 bg-light rounded d-none">
                        <small class="text-muted fw-bold">Remarks:</small> <span id="previewRemarksText" class="text-dark small"></span>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" data-bs-dismiss="modal">
                            <em class="icon ni ni-arrow-left me-1"></em> Back to Edit
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="submitForm('DRAFT')">
                            <em class="icon ni ni-file-text me-1"></em> Save as Draft
                        </button>
                        <button type="button" class="btn btn-success btn-sm fw-bold px-4 shadow-sm" id="btnModalConfirmComplete" onclick="confirmCompleteSubmission()">
                            <em class="icon ni ni-check-circle me-1"></em> Complete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleManuscriptFileSelect(input) {
            const summaryBox = document.getElementById('fileSelectionSummary');
            const countBadge = document.getElementById('fileCountBadge');
            const listEl = document.getElementById('fileListNames');
            if (!summaryBox || !listEl) return;

            if (input.files && input.files.length > 0) {
                summaryBox.classList.remove('d-none');
                countBadge.textContent = `Selected ${input.files.length} File(s) / Page(s):`;
                listEl.innerHTML = '';
                for (let i = 0; i < input.files.length; i++) {
                    const f = input.files[i];
                    const sizeKb = Math.round(f.size / 1024);
                    const li = document.createElement('li');
                    li.className = 'd-flex justify-content-between py-1 border-bottom border-light';
                    li.innerHTML = `<span><em class="icon ni ni-file-text me-1 text-primary"></em><strong>Page ${i + 1}:</strong> ${f.name}</span><span class="text-muted">${sizeKb} KB</span>`;
                    listEl.appendChild(li);
                }
            } else {
                summaryBox.classList.add('d-none');
            }
        }

        // Client-side image compression before sending to server (prevents any upload limit errors)
        function compressClientImage(file, maxWidth = 1920, maxHeight = 1920, quality = 0.8) {
            if (!file.type.match(/image.*/)) {
                return Promise.resolve(file); // PDFs and other documents returned as-is
            }
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth || height > maxHeight) {
                            if (width > height) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            } else {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob && blob.size < file.size) {
                                const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                                const compressedFile = new File([blob], cleanName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                resolve(file);
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        }

        document.getElementById('multiManuscriptUploadForm')?.addEventListener('submit', async function(e) {
            if (this.dataset.compressed === 'true') {
                return;
            }
            e.preventDefault();
            const fileInput = document.getElementById('manuscriptUploadInput');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                this.dataset.compressed = 'true';
                this.submit();
                return;
            }

            const submitBtn = document.getElementById('uploadSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Compressing & Uploading...';
            }

            try {
                if (window.DataTransfer) {
                    const dt = new DataTransfer();
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const originalFile = fileInput.files[i];
                        const compressed = await compressClientImage(originalFile);
                        dt.items.add(compressed);
                    }
                    fileInput.files = dt.files;
                }
            } catch (err) {
                console.warn('Pre-upload compression fallback:', err);
            }

            this.dataset.compressed = 'true';
            this.submit();
        });
    </script>
@endsection
