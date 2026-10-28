@extends('layouts.app_back')

@section('content')
    @php
        $manuscriptsList = isset($sampleManuscripts) && $sampleManuscripts->isNotEmpty() 
            ? $sampleManuscripts 
            : collect();

        // If sampleManuscripts is empty in DB but tr04_manuscript exists on sample, create fallback item
        if ($manuscriptsList->isEmpty() && !empty($sample->tr04_manuscript)) {
            $ext = strtolower(pathinfo($sample->tr04_manuscript, PATHINFO_EXTENSION));
            $fileType = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? 'image' : ($ext === 'pdf' ? 'pdf' : 'other');
            $manuscriptsList->push((object)[
                'tr11_manuscript_id' => 1,
                'file_path'          => $sample->tr04_manuscript,
                'original_name'      => $sample->tr04_manuscript,
                'analyst_name'       => $sampleTests->first()?->allotedTo?->m06_name ?? 'Analyst',
                'm06_analyst_id'     => $sampleTests->first()?->m06_alloted_to,
                'm12_test_ids'       => $sampleTests->pluck('m12_test_id')->implode(','),
                'page_number'        => 1,
                'total_pages'        => 1,
                'file_type'          => $fileType,
            ]);
        }

        // Helper to resolve URL for any manuscript item
        $resolveUrl = function($item) {
            $fileName = is_object($item) ? ($item->file_path ?? '') : (string)$item;
            if (empty($fileName)) return '';
            return route('serve_manuscript_file', ['filename' => basename($fileName)]);
        };

        $resolveDownloadUrl = function($item) {
            $fileName = is_object($item) ? ($item->file_path ?? '') : (string)$item;
            if (empty($fileName)) return '';
            return route('serve_manuscript_file', ['filename' => basename($fileName)]);
        };

        // Group manuscripts by analyst
        $manuscriptsByAnalyst = $manuscriptsList->groupBy(fn($m) => $m->m06_analyst_id ?? 0);

        // Detect if sample-wide results were entered by DEO
        $isSampleDeoEntry = $currentResults->contains(function ($r) {
            $creatorRole = strtolower($r->creator?->role?->m03_name ?? '');
            $updaterRole = strtolower($r->updater?->role?->m03_name ?? '');
            return $creatorRole === 'deo' || $updaterRole === 'deo';
        });

        // Prepare JSON dataset of all manuscripts for the JS viewer modal
        $allManuscriptsJson = $manuscriptsList->map(function($m) use ($resolveUrl, $resolveDownloadUrl) {
            return [
                'id'           => $m->tr11_manuscript_id ?? 0,
                'url'          => $resolveUrl($m),
                'download_url' => $resolveDownloadUrl($m),
                'fileName'     => $m->file_path,
                'originalName' => $m->original_name ?? $m->file_path,
                'analystName'  => $m->analyst_name ?? 'Analyst',
                'analystId'    => $m->m06_analyst_id ?? null,
                'pageNumber'   => $m->page_number ?? 1,
                'totalPages'   => $m->total_pages ?? 1,
                'fileType'     => $m->file_type ?? 'image',
            ];
        })->values()->toJson();
    @endphp

    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title text-primary">Sample Result Verification</h3>
                        <div class="nk-block-des text-soft">
                            <p>Verify or Reject results for Sample ID: <span class="fw-bold text-dark">{{ $sample->tr04_reference_id }}</span></p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="{{ route('view_result_verification') }}" class="btn btn-outline-light btn-white">
                                <em class="icon ni ni-arrow-left"></em><span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($sample->tr04_is_under_clarification)
                <div class="alert alert-pro alert-warning bg-warning-dim border-warning shadow-none mb-3 py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-1 border-bottom border-warning border-opacity-25 mb-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <em class="icon ni ni-alert-circle text-warning fs-18px"></em>
                            <span class="fw-bold text-dark fs-14px">Clarification Required</span>
                            <span class="badge badge-dim bg-danger text-danger">Returned by {{ $sample->tr04_sent_back_by_role }} ({{ $sample->tr04_sent_back_by_name ?? 'Authority' }})</span>
                        </div>
                        @if ($sample->tr04_sent_back_at)
                            <span class="text-muted small">
                                <em class="icon ni ni-clock me-1"></em>{{ \Carbon\Carbon::parse($sample->tr04_sent_back_at)->format('d M, Y h:i A') }}
                            </span>
                        @endif
                    </div>
                    <div class="text-dark fs-13px ps-1 pt-1" style="line-height: 1.5;">
                        <strong class="text-warning-dark me-1"><em class="icon ni ni-chat-fill text-warning me-1"></em>Remark:</strong>
                        <span>{{ $sample->tr04_latest_clarification_remark }}</span>
                    </div>
                </div>
            @endif

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Sample Overview Card -->
                    <div class="col-12">
                        <div class="card card-bordered shadow-sm">
                            <div class="card-inner py-3">
                                <div class="row g-3 align-center">
                                    <div class="col-md-3">
                                        <div class="profile-stats">
                                            <span class="sub-text">Sample Type</span>
                                            <span class="h5">{{ $sample->labSample->m14_name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="profile-stats">
                                            <span class="sub-text">Priority</span>
                                            <span class="badge badge-dim bg-{{ ($sample->tr04_sample_type ?? 'NORMAL') == 'TATKAL' ? 'danger' : 'info' }}">
                                                {{ $sample->tr04_sample_type ?? 'NORMAL' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="profile-stats">
                                            <span class="sub-text">Registration Date</span>
                                            <span class="h5">{{ date('d M Y', strtotime($sample->created_at)) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="profile-stats">
                                            <span class="sub-text">Total Tests</span>
                                            <span class="h5">{{ $sampleTests->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        @if ($manuscriptsList->isNotEmpty())
                                            @if ($manuscriptsList->count() === 1)
                                                <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" onclick="openManuscriptGalleryModal(0)">
                                                    <em class="icon ni ni-file-text me-1"></em> <span>View Manuscript (1 Sheet)</span>
                                                </button>
                                            @else
                                                <div class="dropdown d-inline-block">
                                                    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <em class="icon ni ni-files me-1"></em> <span>View Manuscripts ({{ $manuscriptsList->count() }} Sheets)</span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 260px;">
                                                        <div class="dropdown-header text-muted small fw-bold">ALL UPLOADED SHEETS & ANALYSTS</div>
                                                        <a class="dropdown-item py-2 fw-bold text-primary" href="javascript:void(0)" onclick="openManuscriptGalleryModal(0)">
                                                            <em class="icon ni ni-eye me-2"></em> View All Sheets in Full Gallery
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        @foreach ($manuscriptsList as $mIdx => $mItem)
                                                            <a class="dropdown-item py-1 fs-12px" href="javascript:void(0)" onclick="openManuscriptGalleryModal({{ $mIdx }})">
                                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                                    <span>
                                                                        <em class="icon ni ni-file-text me-1 text-muted"></em>
                                                                        <strong>{{ $mItem->analyst_name ?? 'Analyst' }}</strong> 
                                                                        @if(($mItem->total_pages ?? 1) > 1)
                                                                            <span class="text-muted">(Pg {{ $mItem->page_number }}/{{ $mItem->total_pages }})</span>
                                                                        @endif
                                                                    </span>
                                                                    <span class="badge bg-light text-muted">{{ strtoupper($mItem->file_type ?? 'IMG') }}</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge badge-dim bg-light text-muted">
                                                <em class="icon ni ni-info me-1"></em> Electronic Data Only
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($sampleTests as $key => $sampleTest)
                        @php
                            $test = $sampleTest->test;
                            $primaryTests = $test->primaryTests;
                            $currentTestResults = $currentResults->where('m12_test_number', $test->m12_test_number);
                            $historicalTestResults = $historicalResults->where('m12_test_number', $test->m12_test_number);
                            $analystId = $sampleTest->m06_alloted_to;
                            $analystName = $sampleTest->allotedTo->m06_name ?? 'Analyst';

                            // Find manuscripts specifically belonging to this test's allotted analyst or test ID
                            $testManuscripts = $manuscriptsList->filter(function($m) use ($analystId, $test) {
                                if ($analystId && ($m->m06_analyst_id ?? null) == $analystId) {
                                    return true;
                                }
                                if (!empty($m->m12_test_ids)) {
                                    $ids = explode(',', $m->m12_test_ids);
                                    return in_array((string)$test->m12_test_id, $ids) || in_array((string)$test->m12_test_number, $ids);
                                }
                                return false;
                            })->values();

                            // Fallback to sample-wide manuscripts if analyst-specific one doesn't exist
                            if ($testManuscripts->isEmpty() && $manuscriptsList->isNotEmpty()) {
                                $testManuscripts = $manuscriptsList;
                            }

                            // Check if this test was entered via DEO
                            $isTestDeoEntry = $currentTestResults->contains(function ($r) {
                                $creatorRole = strtolower($r->creator?->role?->m03_name ?? '');
                                $updaterRole = strtolower($r->updater?->role?->m03_name ?? '');
                                return $creatorRole === 'deo' || $updaterRole === 'deo';
                            }) || $isSampleDeoEntry;

                            // Check DB manuscript content
                            $dbManuscriptRaw = $currentTestResults->first()->tr07_manuscript_content ?? '';
                            $cleanDbManuscript = trim(strip_tags($dbManuscriptRaw));
                            $hasDbManuscript = !empty($cleanDbManuscript) || str_contains($dbManuscriptRaw, '<img') || str_contains($dbManuscriptRaw, '<table');

                            // Separate individual page sheets from combined PDFs
                            $testPages = $testManuscripts->filter(function($m) {
                                $name = strtolower($m->original_name ?? $m->file_path ?? '');
                                return !($m->file_type === 'pdf' && str_contains($name, 'combined'));
                            })->values();

                            if ($testPages->isEmpty()) {
                                $testPages = $testManuscripts;
                            }

                            // Determine display mode:
                            // If entered via DEO (or DB manuscript empty) and paper manuscript is uploaded: show uploaded manuscript
                            // Else if entered by analyst into DB: show DB manuscript
                            $hasPaper = $testManuscripts->isNotEmpty();
                            $showUploadedManuscript = ($isTestDeoEntry || !$hasDbManuscript) && $hasPaper;
                            $isRevised = $currentTestResults->contains('tr07_result_status', 'REVISED');
                            
                            // Distribute different pages across test cards (Test 1 -> Page 1, Test 2 -> Page 2...)
                            $totalPagesCount = $testPages->count();
                            $assignedPageIdx = $totalPagesCount > 1 ? ($key % $totalPagesCount) : 0;
                            $cardPaper = $testPages->get($assignedPageIdx) ?? $testPages->first();
                            $cardUrl = $cardPaper ? $resolveUrl($cardPaper) : '';
                            $cardDownload = $cardPaper ? $resolveDownloadUrl($cardPaper) : '';
                            $cardType = $cardPaper->file_type ?? 'image';
                            $cardGlobalIdx = $manuscriptsList->search(fn($m) => ($m->file_path ?? '') === ($cardPaper->file_path ?? ''));
                            $cardGlobalIdx = $cardGlobalIdx !== false ? $cardGlobalIdx : 0;
                        @endphp

                        <div class="col-12">
                            <div class="card card-bordered border-{{ $isRevised ? 'warning' : 'primary' }}">
                                <div class="card-header bg-lighter d-flex justify-content-between align-items-center py-2">
                                    <h6 class="mb-0">
                                        <span class="badge bg-primary me-2">{{ $key + 1 }}</span>
                                        {{ $test->m12_name }} 
                                        @if($test->standard?->m15_method)
                                            <span class="text-soft ms-2 small">({{ $test->standard->m15_method }})</span>
                                        @endif
                                        @if($isRevised) <span class="badge badge-outline-warning ms-2">Revised</span> @endif
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-dim bg-secondary">
                                            <em class="icon ni ni-user me-1"></em>Analyst: <strong>{{ $analystName }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-inner p-0">
                                    <div class="row g-0">
                                        <!-- Result Table Section -->
                                        <div class="col-lg-7 border-end">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th width="80" class="text-center">Sr. No</th>
                                                            <th>Test Specification / Field</th>
                                                            <th width="200" class="text-center">Result (Original &rarr; Revised)</th>
                                                            <th width="100" class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $mainCurrent = $currentTestResults->filter(fn($r) => empty($r->m16_primary_test_id) && empty($r->m17_secondary_test_id))->first();
                                                            $mainHistorical = $historicalTestResults->filter(fn($r) => empty($r->m16_primary_test_id) && empty($r->m17_secondary_test_id))->sortByDesc('tr07_test_result_id')->first();
                                                        @endphp

                                                        @if ($primaryTests->isEmpty())
                                                            <tr>
                                                                <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                                                <td class="fw-bold">{{ $test->m12_name }}</td>
                                                                <td class="text-center">
                                                                    @if ($mainHistorical && (string)$mainHistorical->tr07_result !== (string)$mainCurrent->tr07_result)
                                                                        <div class="comparison-box">
                                                                            <span class="text-danger text-decoration-line-through me-1">{!! $mainHistorical->tr07_result ?? 'N/A' !!}</span>
                                                                            <em class="icon ni ni-arrow-right small text-muted"></em>
                                                                            <span class="text-success fw-bold ms-1">{!! $mainCurrent->tr07_result ?? 'N/A' !!}</span>
                                                                            @if ($mainCurrent && $mainCurrent->tr07_unit) <small class="text-muted">({{ $mainCurrent->tr07_unit }})</small> @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="fw-bold">{!! $mainCurrent->tr07_result ?? 'N/A' !!}</span>
                                                                        @if ($mainCurrent && $mainCurrent->tr07_unit) <small class="text-muted">({{ $mainCurrent->tr07_unit }})</small> @endif
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($mainCurrent)
                                                                        <span class="badge badge-dim bg-{{ $mainCurrent->tr07_result_status === 'REVISED' ? 'warning' : 'success' }}">
                                                                            {{ $mainCurrent->tr07_result_status }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr class="table-lighter">
                                                                <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                                                <td class="fw-bold">{{ $test->m12_name }}</td>
                                                                <td colspan="2" class="text-muted text-center italic small">Hierarchy follows below</td>
                                                            </tr>
                                                            @foreach ($primaryTests as $pKey => $primaryTest)
                                                                @include('verification.partials.verification_primary_row', [
                                                                    'key' => $key,
                                                                    'pKey' => $pKey,
                                                                    'test' => $test,
                                                                    'primaryTest' => $primaryTest,
                                                                    'currentResults' => $currentTestResults,
                                                                    'historicalResults' => $historicalTestResults,
                                                                    'customFields' => $customFields->where('m12_test_number', $test->m12_test_number),
                                                                    'historicalCustomFields' => $historicalCustomFields->where('m12_test_number', $test->m12_test_number)
                                                                ])
                                                            @endforeach
                                                        @endif

                                                        <!-- Main Test Custom Fields -->
                                                        @foreach ($customFields->where('m12_test_number', $test->m12_test_number)->filter(fn($f) => empty($f->m16_primary_test_id)) as $cf)
                                                            @php
                                                                $hCf = $historicalCustomFields->where('m12_test_number', $test->m12_test_number)->filter(fn($f) => $f->tr08_field_name === $cf->tr08_field_name && empty($f->m16_primary_test_id))->first();
                                                            @endphp
                                                            <tr class="custom-field-row table-light">
                                                                <td class="text-center small text-muted">{{ $key + 1 }}.C{{ $loop->iteration }}</td>
                                                                <td class="ps-3 small text-muted">{{ $cf->tr08_field_name }}</td>
                                                                <td class="text-center">
                                                                    @if ($hCf && (string)$hCf->tr08_field_value !== (string)$cf->tr08_field_value)
                                                                        <div class="comparison-box">
                                                                            <span class="text-danger text-decoration-line-through me-1">{{ $hCf->tr08_field_value ?? 'N/A' }}</span>
                                                                            <em class="icon ni ni-arrow-right small text-muted"></em>
                                                                            <span class="text-success fw-bold ms-1">{{ $cf->tr08_field_value ?? 'N/A' }}</span>
                                                                        </div>
                                                                    @else
                                                                        {{ $cf->tr08_field_value }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center small text-muted">Custom</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Unified Laboratory Manuscript Workstation Section -->
                                        <div class="col-lg-5 d-flex flex-column bg-lighter">
                                            <div class="p-3 h-100 d-flex flex-column">
                                                @if ($showUploadedManuscript)
                                                    <div class="manuscript-workstation h-100 d-flex flex-column shadow-sm rounded-3 overflow-hidden border">
                                                        <!-- Workstation Header -->
                                                        <div class="workstation-header px-3 py-2 d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="workstation-icon-chip">
                                                                    <em class="icon ni ni-file-docs"></em>
                                                                </div>
                                                                <div>
                                                                    <div class="workstation-title">MANUSCRIPT WORKSTATION</div>
                                                                    <div class="workstation-subtitle">
                                                                        <em class="icon ni ni-user me-1"></em>{{ $cardPaper->analyst_name ?? $analystName }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if ($totalPagesCount > 1)
                                                                    <div class="sheet-counter-chip" id="cardActiveBadge_{{ $key }}">
                                                                        <span class="counter-dot"></span>
                                                                        <span>Sheet <strong id="cardActivePageNum_{{ $key }}">{{ $cardPaper->page_number ?? ($assignedPageIdx + 1) }}</strong> of {{ $totalPagesCount }}</span>
                                                                    </div>
                                                                @else
                                                                    <div class="sheet-counter-chip single">
                                                                        <em class="icon ni ni-file-text me-1"></em> Single Sheet
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Multi-Page Segmented Tab Strip (Visible if > 1 sheets) -->
                                                        @if ($totalPagesCount > 1)
                                                            <div class="workstation-nav-bar px-2 py-1-5 d-flex align-items-center justify-content-between gap-1">
                                                                <button type="button" class="sheet-nav-arrow" onclick="navigateCardStep({{ $key }}, -1)" title="Previous Sheet">
                                                                    <em class="icon ni ni-chevron-left"></em>
                                                                </button>
                                                                
                                                                <div class="sheet-pill-track d-flex align-items-center gap-1 overflow-auto flex-grow-1 mx-1 px-1" id="sheetPillTrack_{{ $key }}">
                                                                    @foreach ($testPages as $pIdx => $pItem)
                                                                        @php
                                                                            $gIdx = $manuscriptsList->search(fn($m) => ($m->file_path ?? '') === ($pItem->file_path ?? ''));
                                                                            $isCardActive = ($pIdx === $assignedPageIdx);
                                                                            $pageNum = $pItem->page_number ?? ($pIdx + 1);
                                                                        @endphp
                                                                        <button type="button" 
                                                                            class="sheet-pill-btn {{ $isCardActive ? 'active' : '' }}" 
                                                                            data-test-key="{{ $key }}" 
                                                                            data-page-index="{{ $pIdx }}" 
                                                                            data-page-num="{{ $pageNum }}"
                                                                            data-total-pages="{{ $totalPagesCount }}"
                                                                            data-url="{{ $resolveUrl($pItem) }}" 
                                                                            data-type="{{ $pItem->file_type ?? 'image' }}" 
                                                                            data-download="{{ $resolveDownloadUrl($pItem) }}" 
                                                                            data-filename="{{ $pItem->file_path }}"
                                                                            data-global-idx="{{ $gIdx !== false ? $gIdx : 0 }}"
                                                                            onclick="selectCardManuscriptPage(this)"
                                                                            title="View Sheet {{ $pageNum }}">
                                                                            <span class="sheet-num-badge">{{ $pageNum }}</span>
                                                                            <span class="sheet-text-label">Sheet {{ $pageNum }}</span>
                                                                        </button>
                                                                    @endforeach
                                                                </div>

                                                                <button type="button" class="sheet-nav-arrow" onclick="navigateCardStep({{ $key }}, 1)" title="Next Sheet">
                                                                    <em class="icon ni ni-chevron-right"></em>
                                                                </button>
                                                            </div>
                                                        @endif

                                                        <!-- Dark Stage Inspection Canvas -->
                                                        <div class="workstation-canvas flex-grow-1 position-relative d-flex align-items-center justify-content-center p-3" id="cardManuscriptBox_{{ $key }}">
                                                            @if ($cardType === 'image')
                                                                <div class="canvas-image-stage w-100 h-100 d-flex align-items-center justify-content-center cursor-pointer" id="cardThumbWrapper_{{ $key }}" onclick="openManuscriptGalleryModal({{ $cardGlobalIdx }})">
                                                                    <img id="cardImagePreview_{{ $key }}" 
                                                                        src="{{ $cardUrl }}" 
                                                                        alt="Manuscript Sheet - Page {{ $assignedPageIdx + 1 }}" 
                                                                        class="workstation-img img-fluid">
                                                                    <div class="canvas-glass-badge">
                                                                        <em class="icon ni ni-zoom-in me-1"></em>
                                                                        <span id="cardOverlayZoomBadge_{{ $key }}">Click to Zoom &bull; Sheet {{ $cardPaper->page_number ?? ($assignedPageIdx + 1) }}</span>
                                                                    </div>
                                                                </div>
                                                            @elseif ($cardType === 'pdf')
                                                                <div class="w-100 h-100" style="min-height: 280px;" id="cardPdfEmbedWrap_{{ $key }}">
                                                                    <embed id="cardPdfPreview_{{ $key }}" src="{{ $cardUrl }}#toolbar=0" type="application/pdf" width="100%" height="100%" style="border: 0; min-height: 280px; border-radius: 4px;">
                                                                </div>
                                                            @else
                                                                <div class="text-center p-4 text-white">
                                                                    <em class="icon ni ni-file-text display-4 text-warning opacity-75 mb-2"></em>
                                                                    <h6 class="text-white fs-13px mb-1">Uploaded Manuscript Document</h6>
                                                                    <p class="text-white-50 small mb-0">{{ $cardPaper->file_path }}</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Workstation Footer Actions -->
                                                        <div class="workstation-footer px-3 py-2 d-flex align-items-center justify-content-between">
                                                            <button type="button" class="btn btn-sm btn-action-inspect" id="cardInspectBtn_{{ $key }}" onclick="openManuscriptGalleryModal({{ $cardGlobalIdx }})">
                                                                <em class="icon ni ni-maximize me-1"></em> High-Res Inspection
                                                            </button>
                                                            <a href="{{ $cardDownload }}" id="cardDownloadBtn_{{ $key }}" target="_blank" download class="btn btn-sm btn-action-download">
                                                                <em class="icon ni ni-download me-1"></em> Download Original
                                                            </a>
                                                        </div>
                                                    </div>

                                                @elseif ($hasDbManuscript)
                                                    <div class="manuscript-notebook-card h-100 d-flex flex-column shadow-sm rounded-3 overflow-hidden border">
                                                        <!-- Clean Notebook Header -->
                                                        <div class="workstation-notebook-header px-3 py-2 d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="workstation-notebook-icon">
                                                                    <em class="icon ni ni-calc"></em>
                                                                </div>
                                                                <div>
                                                                    <div class="workstation-notebook-title">DIGITAL CALCULATION NOTEBOOK</div>
                                                                    <div class="workstation-notebook-subtitle">
                                                                        <em class="icon ni ni-user me-1 text-primary"></em>{{ $analystName }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="badge badge-dim bg-success border border-success border-opacity-30">
                                                                <em class="icon ni ni-check-circle me-1"></em> Analyst Entry
                                                            </span>
                                                        </div>

                                                        <!-- Clean Paper Notebook Canvas -->
                                                        <div class="workstation-notebook-canvas flex-grow-1 p-3 overflow-auto" style="min-height: 260px; max-height: 400px;">
                                                            <div class="manuscript-notebook-content">
                                                                {!! $dbManuscriptRaw !!}
                                                            </div>
                                                        </div>

                                                        @if ($hasPaper)
                                                            @php
                                                                $pGlobalIdx = $manuscriptsList->search(fn($m) => ($m->file_path ?? '') === ($firstPaper->file_path ?? ''));
                                                            @endphp
                                                            <div class="workstation-notebook-footer px-3 py-2 d-flex justify-content-end align-items-center">
                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openManuscriptGalleryModal({{ $pGlobalIdx !== false ? $pGlobalIdx : 0 }})">
                                                                    <em class="icon ni ni-clip me-1"></em> View Attached Paper Sheet
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <!-- Clean Empty State Card -->
                                                    <div class="manuscript-empty-card h-100 d-flex flex-column shadow-sm rounded-3 overflow-hidden border">
                                                        <div class="workstation-empty-header px-3 py-2 d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="workstation-icon-chip bg-light border text-muted">
                                                                    <em class="icon ni ni-file-text"></em>
                                                                </div>
                                                                <div>
                                                                    <div class="workstation-title text-dark">MANUSCRIPT WORKSTATION</div>
                                                                    <div class="workstation-subtitle text-muted">No Documents Attached</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="workstation-empty-canvas flex-grow-1 d-flex flex-column align-items-center justify-content-center p-4 text-muted" style="min-height: 240px;">
                                                            <em class="icon ni ni-file-text display-4 text-muted opacity-25 mb-2"></em>
                                                            <p class="small text-muted mb-0">No calculation data or paper sheet available for this test.</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Final Verification Actions -->
                    <div class="col-12 mt-4">
                        <div class="card card-bordered card-stretch shadow-sm">
                            <div class="card-header bg-lighter border-bottom py-2 px-4 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-14px">
                                    <em class="icon ni ni-shield-check text-primary me-1"></em> Final Verification & Custody Routing Actions
                                </span>
                                <span class="text-muted small">Sample Ref: <strong class="text-dark">{{ $sample->tr04_reference_id }}</strong></span>
                            </div>
                            <div class="card-inner p-4">
                                <form action="{{ route('verify_result', $sample->tr04_sample_registration_id) }}" method="POST" id="verificationForm">
                                    @csrf
                                    <div class="row g-4 align-items-stretch">
                                        <!-- Column 1: Clarification / Verification Remarks -->
                                        <div class="col-lg-5 col-md-5 d-flex flex-column">
                                            <div class="form-group mb-0 flex-grow-1 d-flex flex-column">
                                                <label class="form-label fw-bold d-flex justify-content-between align-items-center mb-1">
                                                    <span>Clarification / Remarks</span>
                                                    <span class="text-danger small fw-normal">* Mandatory if rejecting</span>
                                                </label>
                                                <div class="form-control-wrap flex-grow-1">
                                                    <textarea name="remarks" id="verificationRemarks" class="form-control h-100 @error('remarks') is-invalid @enderror" style="min-height: 120px; resize: none;" placeholder="State clear verification notes, test observations, or specific reasons for clarification / rejection..."></textarea>
                                                    @error('remarks')
                                                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <span class="text-muted fs-11px mt-1"><em class="icon ni ni-info me-1"></em>Logged permanently in sample custody history.</span>
                                            </div>
                                        </div>

                                        <!-- Column 2: Rejection Routing Panel -->
                                        <div class="col-lg-4 col-md-4">
                                            <div class="p-3 rounded border bg-light h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <label class="form-label fw-bold text-danger d-flex align-items-center gap-1 mb-2 fs-13px">
                                                        <em class="icon ni ni-undo"></em> If Rejecting, Send Back To:
                                                    </label>
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-6">
                                                            <div class="border rounded p-2 bg-white h-100">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" name="target_role" id="targetAnalyst" value="Analyst" checked onchange="toggleReassignBox(this.value)">
                                                                    <label class="custom-control-label fw-bold text-dark w-100 cursor-pointer" for="targetAnalyst">
                                                                        Analyst
                                                                        <span class="d-block text-muted fw-normal fs-11px mt-1" style="line-height: 1.2;">(Analytical / Re-test)</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="border rounded p-2 bg-white h-100">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" name="target_role" id="targetDEO" value="DEO" onchange="toggleReassignBox(this.value)">
                                                                    <label class="custom-control-label fw-bold text-dark w-100 cursor-pointer" for="targetDEO">
                                                                        DEO
                                                                        <span class="d-block text-muted fw-normal fs-11px mt-1" style="line-height: 1.2;">(Typo / Data Entry)</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="pt-2 border-top mt-1" id="reassignAnalystWrapper">
                                                    <label class="form-label text-muted fs-12px mb-1 fw-medium" for="reassigned_analyst_id">Re-assign To Different Analyst (Optional)</label>
                                                    <div class="form-control-wrap">
                                                        <select name="reassigned_analyst_id" id="reassigned_analyst_id" class="form-select form-select-sm">
                                                            <option value="">Keep Existing Allotted Analyst</option>
                                                            @foreach ($analysts as $analyst)
                                                                <option value="{{ $analyst->m06_employee_id }}">{{ $analyst->m06_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Column 3: Execute Action Buttons -->
                                        <div class="col-lg-3 col-md-3 d-flex flex-column justify-content-between">
                                            <div>
                                                <label class="form-label fw-bold text-dark mb-2 fs-13px">Action Execution:</label>
                                                <div class="d-flex flex-column gap-2">
                                                    <button type="button" onclick="submitVerification('verify')" class="btn btn-success py-2 w-100 d-flex align-items-center justify-content-center shadow-sm">
                                                        <em class="icon ni ni-check-circle fs-20px me-2"></em>
                                                        <div class="text-start">
                                                            <div class="fw-bold fs-14px">Verify All Results</div>
                                                            <div class="fs-11px opacity-75 fw-normal">Forward to Issuing Officer</div>
                                                        </div>
                                                    </button>
                                                    <button type="button" onclick="submitVerification('reject')" class="btn btn-danger py-2 w-100 d-flex align-items-center justify-content-center shadow-sm">
                                                        <em class="icon ni ni-cross-circle fs-20px me-2"></em>
                                                        <div class="text-start">
                                                            <div class="fw-bold fs-14px">Reject & Send Back</div>
                                                            <div class="fs-11px opacity-75 fw-normal">Route for re-testing/correction</div>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-center text-muted fs-11px mt-2">
                                                <em class="icon ni ni-shield-alert me-1"></em>Action is final & audit logged.
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="action" id="actionField">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Zooming / Inspecting Multi-Analyst, Multi-Page Paper Manuscripts -->
    <div class="modal fade" id="paperManuscriptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 94vw;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2 px-3 align-items-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <em class="icon ni ni-file-text fs-18px text-warning"></em>
                        <h6 class="modal-title text-white fs-14px mb-0">
                            Manuscript Raw Sheet &bull; <span class="fw-normal text-muted" id="modalFileName">{{ $sample->tr04_reference_id }}</span>
                        </h6>
                        <span class="badge bg-warning text-dark ms-2 fs-11px" id="modalAnalystBadge">Analyst</span>
                        <span class="badge bg-info text-white fs-11px" id="modalPageBadge">Page 1 of 1</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Multi-page Navigator buttons -->
                        <div class="btn-group btn-group-sm me-2" id="modalPageNavBtns">
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="navigateManuscriptGallery(-1)" id="btnPrevPage" title="Previous Page">
                                <em class="icon ni ni-chevron-left me-1"></em> Prev
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="navigateManuscriptGallery(1)" id="btnNextPage" title="Next Page">
                                Next <em class="icon ni ni-chevron-right ms-1"></em>
                            </button>
                        </div>

                        <!-- Zoom & Rotate Controls -->
                        <div id="imageControlGroup" class="d-none btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="zoomManuscript(1.2)" title="Zoom In"><em class="icon ni ni-zoom-in"></em></button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="zoomManuscript(0.8)" title="Zoom Out"><em class="icon ni ni-zoom-out"></em></button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="rotateManuscript(90)" title="Rotate 90°"><em class="icon ni ni-reload"></em></button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="resetManuscriptTransform()" title="Reset"><em class="icon ni ni-undo"></em></button>
                        </div>
                        <a href="#" id="modalDownloadBtn" target="_blank" download class="btn btn-sm btn-primary">
                            <em class="icon ni ni-download me-1"></em> Download
                        </a>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-dark position-relative" style="min-height: 75vh; max-height: 85vh; overflow: auto; display: flex; align-items: center; justify-content: center;">
                    <div id="modalImageContainer" class="p-3 w-100 h-100 text-center" style="overflow: auto;">
                        <img id="modalManuscriptImage" src="" alt="Paper Manuscript Sheet" class="img-fluid rounded shadow" style="max-height: 80vh; object-fit: contain; transition: transform 0.2s ease;">
                    </div>
                    <div id="modalPdfContainer" class="w-100 d-none" style="height: 82vh;">
                        <embed id="modalManuscriptPdf" src="" type="application/pdf" width="100%" height="100%" style="border: 0;">
                    </div>
                    <div id="modalGenericContainer" class="p-5 text-center text-white d-none">
                        <em class="icon ni ni-file-text display-3 mb-3 text-warning"></em>
                        <h5 class="text-white">Uploaded Document</h5>
                        <p class="text-muted">This file cannot be previewed directly in browser. Please download the document to inspect.</p>
                    </div>
                </div>
                <!-- Thumbnail bar if multiple sheets exist -->
                <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 overflow-auto" id="modalThumbStrip" style="max-width: 75vw;"></div>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .comparison-box {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .table-lighter { background-color: #f9fbfe; }
        .manuscript-viewer {
            box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
            line-height: 1.6;
            font-size: 14px;
        }
        .italic { font-style: italic; }
        .overline-title {
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .cursor-pointer { cursor: pointer; }
        .manuscript-thumb-wrapper:hover img {
            transform: scale(1.02);
        }
        .modal-thumb-item {
            width: 48px;
            height: 38px;
            object-fit: cover;
            border-radius: 3px;
            border: 2px solid #dee2e6;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .modal-thumb-item.active {
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13,110,253,0.3);
        }

        /* ==========================================================================
           HIGH-END LABORATORY MANUSCRIPT WORKSTATION STYLES
           ========================================================================== */
        .manuscript-workstation {
            background: #0f172a;
            border: 1px solid #1e293b !important;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .manuscript-workstation:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14);
            border-color: #334155 !important;
        }

        /* Workstation Header */
        .workstation-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }
        .workstation-icon-chip {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            background: rgba(9, 113, 254, 0.15);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            border: 1px solid rgba(56, 189, 248, 0.25);
            flex-shrink: 0;
        }
        .workstation-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #f1f5f9;
            line-height: 1.2;
        }
        .workstation-subtitle {
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.2;
        }

        /* Active Sheet Counter Chip */
        .sheet-counter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            background: rgba(56, 189, 248, 0.12);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.25);
            white-space: nowrap;
        }
        .sheet-counter-chip .counter-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #38bdf8;
            box-shadow: 0 0 6px #38bdf8;
            animation: pulseGlow 2s infinite ease-in-out;
        }
        .sheet-counter-chip.single {
            background: rgba(148, 163, 184, 0.12);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, 0.25);
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.5; transform: scale(0.9); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        /* Page Navigation Strip */
        .workstation-nav-bar {
            background: #0b1120;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .sheet-nav-arrow {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            width: 26px;
            height: 26px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
            padding: 0;
        }
        .sheet-nav-arrow:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        .sheet-pill-track {
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        .sheet-pill-track::-webkit-scrollbar {
            height: 3px;
        }
        .sheet-pill-track::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }

        /* Sheet Pill Buttons */
        .sheet-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
            flex-shrink: 0;
            line-height: 1.2;
        }
        .sheet-pill-btn .sheet-num-badge {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }
        .sheet-pill-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.22);
            transform: translateY(-1px);
        }
        .sheet-pill-btn.active {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%) !important;
            border-color: #38bdf8 !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.5);
        }
        .sheet-pill-btn.active .sheet-num-badge {
            background: #ffffff;
            color: #1e3a8a;
        }

        /* Canvas Stage */
        .workstation-canvas {
            background: radial-gradient(ellipse at center, #1e293b 0%, #090d16 100%);
            min-height: 270px;
            overflow: hidden;
        }
        .canvas-image-stage {
            position: relative;
            max-height: 290px;
            width: 100%;
        }
        .workstation-img {
            max-height: 275px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border-radius: 5px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
            transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.25s ease;
        }
        .canvas-image-stage:hover .workstation-img {
            transform: scale(1.02);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(56, 189, 248, 0.35);
        }

        /* Floating Glass Zoom Badge */
        .canvas-glass-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #f8fafc;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            pointer-events: none;
            transition: all 0.2s ease;
        }
        .canvas-image-stage:hover .canvas-glass-badge {
            background: rgba(2, 132, 199, 0.92);
            border-color: #38bdf8;
            box-shadow: 0 4px 16px rgba(2, 132, 199, 0.5);
        }

        /* Footer Actions */
        .workstation-footer {
            background: #0f172a;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-action-inspect {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #ffffff;
            font-weight: 600;
            font-size: 12px;
            padding: 4px 13px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
            transition: all 0.15s ease;
        }
        .btn-action-inspect:hover {
            background: linear-gradient(135deg, #0369a1 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.5);
            transform: translateY(-1px);
        }
        .btn-action-download {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
            font-weight: 500;
            font-size: 12px;
            padding: 4px 13px;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .btn-action-download:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* ==========================================================================
           DIGITAL CALCULATION NOTEBOOK (HTML MANUSCRIPT) - CLEAN DOCUMENT STYLING
           ========================================================================== */
        .manuscript-notebook-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .manuscript-notebook-card:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1 !important;
        }

        .workstation-notebook-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
            border-bottom: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        .workstation-notebook-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            background: rgba(16, 185, 129, 0.12) !important;
            color: #059669 !important;
            border: 1px solid rgba(16, 185, 129, 0.25) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .workstation-notebook-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0f172a !important;
            line-height: 1.2;
        }

        .workstation-notebook-subtitle {
            font-size: 11px;
            color: #64748b !important;
            line-height: 1.2;
        }

        /* Clean White Paper Canvas (NO Dark Gradient) */
        .workstation-notebook-canvas {
            background: #ffffff !important;
            background-image: none !important;
            min-height: 260px;
            border-radius: 0;
        }

        /* Pristine Document Typography & Tables for HTML Manuscripts */
        .manuscript-notebook-content {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
            color: #0f172a !important;
        }

        .manuscript-notebook-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 1rem !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
        }

        .manuscript-notebook-content th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 12.5px !important;
            padding: 8px 10px !important;
            border: 1px solid #cbd5e1 !important;
            text-align: left !important;
        }

        .manuscript-notebook-content td {
            padding: 7px 10px !important;
            color: #1e293b !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 13px !important;
            vertical-align: middle !important;
            background-color: #ffffff !important;
        }

        .manuscript-notebook-content tr:nth-child(even) td {
            background-color: #f8fafc !important;
        }

        .manuscript-notebook-content tr:hover td {
            background-color: #f1f5f9 !important;
        }

        .manuscript-notebook-content p,
        .manuscript-notebook-content div,
        .manuscript-notebook-content span,
        .manuscript-notebook-content strong,
        .manuscript-notebook-content em,
        .manuscript-notebook-content b {
            color: #0f172a !important;
        }

        .workstation-notebook-footer {
            background: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        /* Empty State Clean Card */
        .manuscript-empty-card {
            background: #f8fafc !important;
            border: 1px dashed #cbd5e1 !important;
            border-radius: 8px;
        }

        .workstation-empty-header {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .workstation-empty-canvas {
            background: #f8fafc !important;
            background-image: none !important;
        }
    </style>

    <script>
        const manuscriptGalleryData = {!! $allManuscriptsJson !!};
        let currentGalleryIndex = 0;
        let currentZoom = 1;
        let currentRotation = 0;

        function selectCardManuscriptPage(btn) {
            const testKey = btn.dataset.testKey;
            const url = btn.dataset.url;
            const type = btn.dataset.type;
            const download = btn.dataset.download;
            const globalIdx = parseInt(btn.dataset.globalIdx, 10);
            const pageNum = btn.dataset.pageNum || (parseInt(btn.dataset.pageIndex, 10) + 1);
            const totalPages = btn.dataset.totalPages || '';

            // Toggle active pill inside this card's track
            document.querySelectorAll(`.sheet-pill-btn[data-test-key="${testKey}"]`).forEach(b => {
                b.classList.remove('active');
            });
            btn.classList.add('active');

            // Scroll pill into view smoothly if partially out of viewport
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });

            // Update sheet counter in workstation header
            const pageNumEl = document.getElementById(`cardActivePageNum_${testKey}`);
            if (pageNumEl) {
                pageNumEl.textContent = pageNum;
            }

            // Update floating zoom badge on canvas
            const overlayBadge = document.getElementById(`cardOverlayZoomBadge_${testKey}`);
            if (overlayBadge) {
                overlayBadge.textContent = `Click to Zoom • Sheet ${pageNum}`;
            }

            // Swap preview content
            const imgEl = document.getElementById(`cardImagePreview_${testKey}`);
            const pdfEl = document.getElementById(`cardPdfPreview_${testKey}`);
            const inspectBtn = document.getElementById(`cardInspectBtn_${testKey}`);
            const downloadBtn = document.getElementById(`cardDownloadBtn_${testKey}`);
            const thumbWrap = document.getElementById(`cardThumbWrapper_${testKey}`);

            if (type === 'image' && imgEl) {
                imgEl.src = url;
            } else if (type === 'pdf' && pdfEl) {
                pdfEl.src = `${url}#toolbar=0`;
            }

            if (thumbWrap) {
                thumbWrap.setAttribute('onclick', `openManuscriptGalleryModal(${globalIdx})`);
            }
            if (inspectBtn) {
                inspectBtn.setAttribute('onclick', `openManuscriptGalleryModal(${globalIdx})`);
            }
            if (downloadBtn) {
                downloadBtn.href = download;
            }
        }

        function navigateCardStep(testKey, delta) {
            const pills = Array.from(document.querySelectorAll(`.sheet-pill-btn[data-test-key="${testKey}"]`));
            if (!pills.length) return;
            const currentIdx = pills.findIndex(p => p.classList.contains('active'));
            let nextIdx = (currentIdx === -1 ? 0 : currentIdx) + delta;
            nextIdx = Math.max(0, Math.min(nextIdx, pills.length - 1));
            if (nextIdx !== currentIdx && pills[nextIdx]) {
                selectCardManuscriptPage(pills[nextIdx]);
            }
        }

        function openManuscriptGalleryModal(index) {
            const modalEl = document.getElementById('paperManuscriptModal');
            if (!modalEl) return;

            currentGalleryIndex = Math.min(Math.max(index, 0), Math.max(manuscriptGalleryData.length - 1, 0));
            loadGalleryItem(currentGalleryIndex);

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        function loadGalleryItem(index) {
            if (!manuscriptGalleryData || manuscriptGalleryData.length === 0) return;
            const item = manuscriptGalleryData[index];
            if (!item) return;

            const imgContainer = document.getElementById('modalImageContainer');
            const pdfContainer = document.getElementById('modalPdfContainer');
            const genericContainer = document.getElementById('modalGenericContainer');
            const imgEl = document.getElementById('modalManuscriptImage');
            const pdfEl = document.getElementById('modalManuscriptPdf');
            const downloadBtn = document.getElementById('modalDownloadBtn');
            const ctrlGroup = document.getElementById('imageControlGroup');
            const titleEl = document.getElementById('modalFileName');
            const analystBadge = document.getElementById('modalAnalystBadge');
            const pageBadge = document.getElementById('modalPageBadge');

            if (titleEl) titleEl.textContent = item.fileName || 'Manuscript';
            if (analystBadge) analystBadge.textContent = item.analystName || 'Analyst';
            if (pageBadge) pageBadge.textContent = `Page ${item.pageNumber || (index + 1)} of ${item.totalPages || manuscriptGalleryData.length}`;
            if (downloadBtn) downloadBtn.href = item.download_url || item.url;

            resetManuscriptTransform();

            if (item.fileType === 'image') {
                if (imgEl) imgEl.src = item.url;
                if (imgContainer) imgContainer.classList.remove('d-none');
                if (pdfContainer) pdfContainer.classList.add('d-none');
                if (genericContainer) genericContainer.classList.add('d-none');
                if (ctrlGroup) ctrlGroup.classList.remove('d-none');
            } else if (item.fileType === 'pdf') {
                if (pdfEl) pdfEl.src = item.url;
                if (imgContainer) imgContainer.classList.add('d-none');
                if (pdfContainer) pdfContainer.classList.remove('d-none');
                if (genericContainer) genericContainer.classList.add('d-none');
                if (ctrlGroup) ctrlGroup.classList.add('d-none');
            } else {
                if (imgContainer) imgContainer.classList.add('d-none');
                if (pdfContainer) pdfContainer.classList.add('d-none');
                if (genericContainer) genericContainer.classList.remove('d-none');
                if (ctrlGroup) ctrlGroup.classList.add('d-none');
            }

            // Update Prev / Next button states
            const prevBtn = document.getElementById('btnPrevPage');
            const nextBtn = document.getElementById('btnNextPage');
            if (prevBtn) prevBtn.disabled = index === 0;
            if (nextBtn) nextBtn.disabled = index >= manuscriptGalleryData.length - 1;

            renderThumbStrip(index);
        }

        function navigateManuscriptGallery(direction) {
            const newIndex = currentGalleryIndex + direction;
            if (newIndex >= 0 && newIndex < manuscriptGalleryData.length) {
                currentGalleryIndex = newIndex;
                loadGalleryItem(currentGalleryIndex);
            }
        }

        function renderThumbStrip(activeIdx) {
            const strip = document.getElementById('modalThumbStrip');
            if (!strip) return;
            if (manuscriptGalleryData.length <= 1) {
                strip.innerHTML = '<span class="text-muted fs-12px"><em class="icon ni ni-shield-check text-success me-1"></em> ISO/IEC 17025 Raw Data Audit Traceability</span>';
                return;
            }

            strip.innerHTML = '';
            manuscriptGalleryData.forEach((item, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `btn btn-xs ${i === activeIdx ? 'btn-primary' : 'btn-outline-secondary'}`;
                btn.innerHTML = `${item.analystName.split(' ')[0]} - Pg ${item.pageNumber}`;
                btn.onclick = () => {
                    currentGalleryIndex = i;
                    loadGalleryItem(i);
                };
                strip.appendChild(btn);
            });
        }

        function zoomManuscript(factor) {
            currentZoom = Math.min(Math.max(currentZoom * factor, 0.4), 4);
            applyManuscriptTransform();
        }

        function rotateManuscript(deg) {
            currentRotation = (currentRotation + deg) % 360;
            applyManuscriptTransform();
        }

        function resetManuscriptTransform() {
            currentZoom = 1;
            currentRotation = 0;
            applyManuscriptTransform();
        }

        function applyManuscriptTransform() {
            const imgEl = document.getElementById('modalManuscriptImage');
            if (imgEl) {
                imgEl.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
            }
        }

        function toggleReassignBox(target) {
            const box = document.getElementById('reassignAnalystWrapper');
            if (box) {
                box.style.display = target === 'DEO' ? 'none' : 'block';
            }
        }

        function submitVerification(action) {
            const remarksInput = document.querySelector('textarea[name="remarks"]');
            const remarks = remarksInput ? remarksInput.value.trim() : '';
            const targetRole = document.querySelector('input[name="target_role"]:checked')?.value || 'Analyst';

            if (action === 'reject') {
                if (!remarks || remarks.length < 3) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mandatory Remark Required',
                        text: 'A clear clarification remark is strictly mandatory when rejecting or sending back results so the recipient can take immediate corrective action.',
                        confirmButtonColor: '#e85347'
                    });
                    if (remarksInput) {
                        remarksInput.focus();
                        remarksInput.classList.add('is-invalid');
                    }
                    return;
                }
            }

            if (remarksInput) remarksInput.classList.remove('is-invalid');

            const actionText = action === 'verify' ? 'VERIFY' : 'REJECT & SEND BACK';
            const actionClass = action === 'verify' ? 'text-success' : 'text-danger';
            const targetText = action === 'reject' ? `<br><small class="text-muted">Target Custody: <b>${targetRole}</b></small>` : '';

            Swal.fire({
                title: 'Confirm Verification Action',
                html: `Are you sure you want to <b class="${actionClass}">${actionText}</b> all results for this sample?${targetText}`,
                icon: action === 'verify' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'verify' ? '#1ee0ac' : '#e85347',
                cancelButtonColor: '#8094ae',
                confirmButtonText: `Yes, ${actionText}`
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('actionField').value = action;
                    document.getElementById('verificationForm').submit();
                }
            });
        }
    </script>
@endsection
