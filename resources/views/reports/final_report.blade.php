@extends('layouts.app_back')

@section('title', 'Report Preview')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Report Preview - Drag to Reorder</h3>
                            <div class="nk-block-des text-soft">
                                <h6 class="mb-0">Sample: <strong
                                        class="fw-bold text-primary">#{{ $sample->tr04_reference_id }}</strong></h6>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'letterhead' => 1]) }}"
                                    class="btn btn-warning btn-sm fw-bold text-dark" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;">
                                    <em class="icon ni ni-printer me-1"></em> <span>Download on Letterhead</span>
                                </a>
                                <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true]) }}"
                                    class="btn btn-primary btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;">
                                    <em class="icon ni ni-download me-1"></em> <span>Download Full PDF</span>
                                </a>
                                <button type="button" class="btn btn-dim btn-outline-danger btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#sendBackModal">
                                    <em class="icon ni ni-undo me-1"></em> <span>Send Back</span>
                                </button>
                                <a href="{{ route('test_results') }}" class="btn btn-outline-secondary btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;">
                                    <em class="icon ni ni-arrow-left me-1"></em> <span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            {{-- Report Preview with Reordering --}}
                            <div class="mb-4">
                                {{-- Paper / Print Mode Selector & Action Toolbar --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 p-3 bg-white border rounded shadow-sm">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-bold text-dark fs-13px" style="white-space: nowrap;">
                                            <em class="icon ni ni-printer me-1 text-primary"></em> Paper / Print Mode:
                                        </span>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-primary active fw-bold" id="btn-mode-plain" onclick="switchPreviewMode('plain')">
                                                <em class="icon ni ni-file-text me-1"></em> Complete Report (Plain Paper)
                                            </button>
                                            <button type="button" class="btn btn-outline-primary fw-bold" id="btn-mode-letterhead" onclick="switchPreviewMode('letterhead')">
                                                <em class="icon ni ni-layout me-1"></em> Print on Letterhead (Stationery)
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Top Action Buttons (Dynamic based on selected mode) --}}
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        {{-- Buttons shown when Plain Paper mode is active --}}
                                        <div id="top-actions-plain" class="d-flex align-items-center gap-2">
                                            <a href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id]) }}"
                                                class="btn btn-outline-info btn-sm fw-bold" target="_blank" style="white-space: nowrap;">
                                                <em class="icon ni ni-eye me-1"></em> Preview Full PDF
                                            </a>
                                            <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true]) }}"
                                                class="btn btn-primary btn-sm fw-bold" style="white-space: nowrap;">
                                                <em class="icon ni ni-download me-1"></em> Download Full Report
                                            </a>
                                        </div>

                                        {{-- Buttons shown when Letterhead mode is active --}}
                                        <div id="top-actions-letterhead" class="d-none align-items-center gap-2">
                                            <a href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id, 'letterhead' => 1]) }}"
                                                class="btn btn-warning btn-sm fw-bold text-dark" target="_blank" style="white-space: nowrap;">
                                                <em class="icon ni ni-eye me-1"></em> Preview on Letterhead
                                            </a>
                                            <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'letterhead' => 1]) }}"
                                                class="btn btn-success btn-sm fw-bold" style="white-space: nowrap;">
                                                <em class="icon ni ni-printer me-1"></em> Download for Letterhead
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div id="report-preview" class="border rounded p-4 bg-white position-relative shadow-sm">
                                    {{-- Letterhead Notice Banner (Shown when letterhead mode is selected) --}}
                                    <div id="letterhead-notice-banner" class="alert alert-warning py-2 px-3 d-none mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <em class="icon ni ni-printer me-1 fw-bold"></em>
                                            <strong>Letterhead Mode Active:</strong> Pre-printed letterhead graphics (logos, headers, border, footers) are suppressed so only dynamic test results print directly onto your pre-printed stationery sheet.
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id, 'letterhead' => 1]) }}" target="_blank" class="btn btn-sm btn-warning text-dark fw-bold">
                                                <em class="icon ni ni-eye me-1"></em> Preview Letterhead PDF
                                            </a>
                                            <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'letterhead' => 1]) }}" class="btn btn-sm btn-success fw-bold">
                                                <em class="icon ni ni-download me-1"></em> Download Letterhead PDF
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Official Dynamic English Letterhead Header (Configurable per RO from DB) --}}
                                    @php
                                        $cleanWebsite = $sample->ro?->lab_website ? str_replace(['http://', 'https://'], '', $sample->ro->lab_website) : 'www.textilescommittee.nic.in';
                                        $labName = !empty($sample->ro?->lab_name_en) ? strtoupper($sample->ro->lab_name_en) : 'TEXTILES COMMITTEE';
                                        $ministry = $sample->ro?->ministry_en ?? 'Government of India, Ministry of Textiles';
                                        $address = $sample->ro?->lab_address ?? 'P. Balu Road, Prabhadevi Chowk, Prabhadevi, Mumbai-400 025.';
                                        $contact = $sample->ro?->lab_contact ?? '+91-22-6652 7541 / 545 / 550 / 607';
                                        $email = $sample->ro?->lab_email ?? 'dlab.tc@nic.in / tclabmumbai@gmail.com';
                                        $certNo = $sample->ro?->certificate_no ?? $sample->ro?->m04_nabl_cert_no ?? 'TC-11915';
                                    @endphp
                                    <div id="preview-header-block" class="mb-3 position-relative preprinted-elem p-3 border rounded shadow-sm bg-white">
                                        <div class="row align-items-center g-2">
                                            <!-- Left Logos: TC logo + Swachh Bharat -->
                                            <div class="col-auto text-center" style="width: 80px;">
                                                <img src="{{ asset('backAssets/images/logo.png') }}" alt="Textiles Committee" style="max-width: 60px; height: auto; display: block; margin: 0 auto;">
                                                <img src="{{ asset('backAssets/images/swach-bharat.png') }}" alt="Swachh Bharat" style="max-width: 58px; height: auto; display: block; margin: 6px auto 0 auto;">
                                            </div>

                                            <!-- NABL Logo Slot (Shown dynamically when accredited tests are present) -->
                                            <div class="col-auto text-center" style="width: 75px;">
                                                @if ($hasAccreditedTests)
                                                    <img src="{{ asset('backAssets/images/accrediation.png') }}" alt="NABL Accredited" style="max-width: 56px; height: auto; display: block; margin: 0 auto;">
                                                    <div style="font-size: 10px; font-weight: bold; color: #000; margin-top: 2px;">{{ $certNo }}</div>
                                                @endif
                                            </div>

                                            <!-- Center Text: Dynamic Lab Details from Database -->
                                            <div class="col text-center px-2">
                                                <div style="font-size: 11px; font-weight: bold; color: #0c023b; letter-spacing: 1.5px;">LABORATORY</div>
                                                <div style="font-size: 17px; font-weight: 800; color: #c00000; letter-spacing: 0.8px; margin: 2px 0;">{{ $labName }}</div>
                                                <div style="font-size: 13px; font-weight: bold; color: #0c023b;">{{ $ministry }}</div>
                                                <div style="font-size: 12px; font-weight: bold; color: #008000; margin-top: 2px;">Textile Laboratory &amp; Research Centre</div>
                                                <div style="font-size: 11px; color: #0c023b; line-height: 1.3; margin-top: 3px;">{{ $address }}</div>
                                                <div style="font-size: 11px; color: #0c023b; margin-top: 2px;">
                                                    <strong>Tel. :</strong> {{ $contact }}
                                                </div>
                                                <div style="font-size: 10.5px; color: #c00000; margin-top: 2px;">
                                                    * <strong>E-mail :</strong> {{ $email }}
                                                    * <strong>Website :</strong> {{ $cleanWebsite }}
                                                </div>
                                            </div>

                                            <!-- Right Logos: 3TS + Beti Bachao -->
                                            <div class="col-auto text-center" style="width: 95px;">
                                                <img src="{{ asset('backAssets/images/3TS.png') }}" alt="3TS" style="max-width: 88px; height: auto; display: block; margin: 0 auto;">
                                                <img src="{{ asset('backAssets/images/mss02.png') }}" alt="Beti Bachao Beti Padhao" style="max-width: 48px; height: auto; display: block; margin: 6px auto 0 auto;">
                                            </div>
                                        </div>

                                        <!-- Navy Divider Line -->
                                        <div style="border-bottom: 2px solid #1c276b; margin-top: 8px; margin-bottom: 6px;"></div>

                                        <!-- TEST REPORT and Format No -->
                                        @php
                                            $isArylTestPresent = false;
                                            if (isset($orderedItems)) {
                                                foreach ($orderedItems as $chkItem) {
                                                    $chkNum = $chkItem['test_number'] ?? null;
                                                    if (!$chkNum) continue;
                                                    $chkResults = $groupedResults[$chkNum] ?? collect();
                                                    $chkParent = $chkResults->first();
                                                    $chkSampleTest = $sample->sampleTests->firstWhere('m12_test_number', $chkNum);
                                                    $chkName = $chkParent?->test?->m12_name ?? $chkSampleTest?->test?->m12_name ?? '';
                                                    if ($chkName && (
                                                        stripos($chkName, 'banned amines') !== false ||
                                                        stripos($chkName, 'aryl amine') !== false ||
                                                        stripos($chkName, 'azo') !== false ||
                                                        stripos($chkName, 'amine') !== false
                                                    )) {
                                                        $isArylTestPresent = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            $displayFormatNoWeb = ($hasAccreditedTests || $isArylTestPresent) ? 'Format No. 04/26/23' : 'Format No. 04/26B/03';
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div style="width: 140px;">&nbsp;</div>
                                            <div class="flex-grow-1 text-center">
                                                <span style="font-size: 18px; font-weight: bold; color: #1c276b; text-decoration: underline; letter-spacing: 1px;">TEST REPORT</span>
                                            </div>
                                            <div style="width: 140px; text-align: right; white-space: nowrap;">
                                                <span style="font-size: 12px; font-weight: bold; color: #000;">{{ $displayFormatNoWeb }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $isCustom = ($sample->m09_customer_type_id == 4) || ($sample->customerType && str_contains(strtolower($sample->customerType->m09_name), 'custom'));

                                        // Build sequential remarks list across all test results in preview order
                                        $allRemarksList = [];
                                        $remarksStarMap = [];
                                        $remarkCounter = 0;

                                        foreach ($orderedItems as $it) {
                                            if ($it['type'] === 'test') {
                                                $tResults = $groupedResults[$it['test_number']] ?? collect();
                                                foreach ($tResults as $res) {
                                                    $txt = trim($res->tr07_remarks ?? '');
                                                    if (empty($txt) && !empty($res->m24_remark_id)) {
                                                        $txt = trim($res->remarkRecord?->m24_remark_text ?? \App\Models\TestRemark::find($res->m24_remark_id)?->m24_remark_text ?? '');
                                                    }

                                                    if ($txt !== '') {
                                                        $resId = $res->tr07_test_result_id ? (string)$res->tr07_test_result_id : null;
                                                        $compositeKey = $it['test_number'] . '_' . ($res->m16_primary_test_id ?: '0') . '_' . ($res->m17_secondary_test_id ?: '0');

                                                        $existingStar = null;
                                                        if ($resId && isset($remarksStarMap[$resId])) {
                                                            $existingStar = $remarksStarMap[$resId];
                                                        } elseif (isset($remarksStarMap[$compositeKey])) {
                                                            $existingStar = $remarksStarMap[$compositeKey];
                                                        }

                                                        if (!$existingStar) {
                                                            $remarkCounter++;
                                                            $starStr = str_repeat('*', $remarkCounter);
                                                            if ($resId) {
                                                                $remarksStarMap[$resId] = $starStr;
                                                            }
                                                            $remarksStarMap[$compositeKey] = $starStr;

                                                            $allRemarksList[] = [
                                                                'stars' => $starStr,
                                                                'text' => $txt,
                                                                'test_name' => $res->test->m12_name ?? ('Test #' . $it['test_number']),
                                                                'test_number' => $it['test_number'],
                                                            ];
                                                        } else {
                                                            if ($resId) {
                                                                $remarksStarMap[$resId] = $existingStar;
                                                            }
                                                            $remarksStarMap[$compositeKey] = $existingStar;
                                                        }
                                                    }
                                                }

                                                $cFields = $groupedCustomFields[$it['test_number']] ?? collect();
                                                foreach ($cFields as $cf) {
                                                    $cTxt = trim($cf->tr08_remarks ?? '');
                                                    if ($cTxt !== '') {
                                                        $cKey = 'cf_' . $cf->tr08_custom_field_id;
                                                        if (!isset($remarksStarMap[$cKey])) {
                                                            $remarkCounter++;
                                                            $starStr = str_repeat('*', $remarkCounter);
                                                            $remarksStarMap[$cKey] = $starStr;
                                                            $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $it['test_number']);
                                                            $allRemarksList[] = [
                                                                'stars' => $starStr,
                                                                'text' => $cTxt,
                                                                'test_name' => $sampleTest?->test?->m12_name ?? ('Test #' . $it['test_number']),
                                                                'test_number' => $it['test_number'],
                                                            ];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp

                                    {{-- Sample Information Table --}}
                                    @if($isCustom)
                                        <table class="table table-bordered table-sm mb-4">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Test Report No</th>
                                                    <td>{{ $meta['report_no'] }}</td>
                                                    <th width="20%" class="text-end">Date</th>
                                                    <td width="20%">{{ $meta['date'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name &amp; Address of Customer</th>
                                                    <td colspan="3">{{ $meta['customer_name'] }}<br>{{ $meta['customer_address'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sample forwarding letter No. &amp; date</th>
                                                    <td colspan="3">Test Memo No. {{ $meta['reference'] }} dated {{ \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date of receipt of sample</th>
                                                    <td colspan="3">{{ Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Buyers Name &amp; address (Optional)</th>
                                                    <td colspan="3">{{ $meta['buyer'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Customer Sample No.</th>
                                                    <td>BE No. {{ $meta['be_no'] }}</td>
                                                    <th class="text-end">Lab. Sample No.</th>
                                                    <td>{{ $meta['report_no'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sample Description</th>
                                                    <td colspan="3">{{ $meta['sample_description'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @else
                                        <table class="table table-bordered table-sm mb-4">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Test Report No</th>
                                                    <td>{{ $meta['report_no'] }}</td>
                                                    <th width="20%" class="text-end">Date</th>
                                                    <td width="20%">{{ $meta['date'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Name &amp; Address of Customer</th>
                                                    <td colspan="3">{{ $meta['customer_name'] }}<br>{{ $meta['customer_address'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sample forwarding letter No. &amp; date</th>
                                                    <td colspan="3">{{ $meta['reference'] }} dtd. {{ \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d.m.Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date of receipt of sample</th>
                                                    <td>{{ Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}</td>
                                                    <th class="text-end">Lab. Sample No.</th>
                                                    <td>{{ $meta['report_no'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Customer Sample No</th>
                                                    <td colspan="3">{{ $meta['be_no'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sample Description</th>
                                                    <td colspan="3">{{ $meta['sample_description'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sample Characteristics</th>
                                                    <td colspan="3">{{ $meta['sample_characteristics'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date of Performance of Tests</th>
                                                    <td colspan="3">
                                                        {{ \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }} to {{ $sample->testResult->first() && $sample->testResult->first()->tr07_performance_date ? \Carbon\Carbon::parse($sample->testResult->first()->tr07_performance_date)->format('d.m.Y') : \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif

                                    {{-- Test Results Section --}}
                                    <div class="text-center mb-3">
                                        <h5 class="text-decoration-underline">TEST RESULTS</h5>
                                    </div>

                                    {{-- Sample Details --}}
                                    @if($isCustom)
                                        <table class="table table-bordered table-sm mb-4">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Sample Characteristics</th>
                                                    <td>{{ $meta['sample_characteristics'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date of Performance of Test(s)</th>
                                                    <td>{{ \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }} to {{ $sample->testResult->first() && $sample->testResult->first()->tr07_performance_date ? \Carbon\Carbon::parse($sample->testResult->first()->tr07_performance_date)->format('d.m.Y') : \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Laboratory Sample No.</th>
                                                    <td>{{ $meta['report_no'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @else
                                        <table class="table table-bordered table-sm mb-4">
                                            <tbody>
                                                @if(isset($hasAccreditedTests) && $hasAccreditedTests && $sample->tr04_ulr_no)
                                                <tr>
                                                    <th width="30%">ULR No.</th>
                                                    <td>{{ $sample->tr04_ulr_no }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th width="30%">Laboratory Sample No.</th>
                                                    <td>{{ $meta['report_no'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif

                                    {{-- Reorderable Test Sections --}}
                                    <div id="sortable-tests">
                                        @foreach ($orderedItems as $index => $item)
                                            @php
                                                $testNumber = $item['test_number'] ?? null;
                                                if (!$testNumber) continue;
                                                $results = $groupedResults[$testNumber] ?? collect();
                                                $parent = $results->first();
                                                $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $testNumber);
                                                $testObj = $parent?->test ?? $sampleTest?->test;
                                                $testName = $testObj?->m12_name ?? ('Test #' . $testNumber);
                                                $standardMethod = optional($sampleTest)->standard_method ?? optional($sampleTest?->standard)->m15_method ?? optional($testObj?->standard)->m15_method ?? null;
                                                $isArylAminesTest = (strpos(strtolower($testName), 'banned amines') !== false || strpos(strtolower($testName), 'aryl amine') !== false || strpos(strtolower($testName), 'azo') !== false || strpos(strtolower($testName), 'amine') !== false);
                                            @endphp

                                            @if (!$isArylAminesTest)
                                                <div class="test-section border rounded p-3 mb-3 bg-white draggable-item"
                                                    data-index="{{ $index }}" data-type="test"
                                                    data-test-number="{{ $testNumber }}">
                                                    <div
                                                        class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <strong class="h6 mb-1">{{ $testName }}</strong>
                                                            @if ($standardMethod)
                                                                <small class="fw-bold text-muted ms-1">({{ $standardMethod }})</small>
                                                            @endif
                                                        </div>
                                                                <div class="d-flex align-items-center">
                                                                    {{-- <span class="badge bg-primary me-2">{{ $results->count() }} results</span> --}}
                                                                    <span class="drag-handle text-muted cursor-grab">
                                                                        <em class="icon ni ni-menu"></em>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            {{-- Test Results Preview --}}
                                                            <div class="ms-3">
                                                                @php
                                                                    $hasPrimary = $results
                                                                        ->whereNotNull('m16_primary_test_id')
                                                                        ->isNotEmpty();
                                                                    $parentResultClean = trim(strip_tags((string)($parent?->tr07_result ?? '')));
                                                                    $hasParentResult = !empty($parentResultClean);
                                                                @endphp

                                                                @if ($hasParentResult || (!$hasPrimary && $parent))
                                                                    @php
                                                                        $parentKey = $parent?->tr07_test_result_id ? (string)$parent->tr07_test_result_id : null;
                                                                        $parentCompKey = $testNumber . '_0_0';
                                                                        $parentStar = ($parentKey ? ($remarksStarMap[$parentKey] ?? null) : null) ?? ($remarksStarMap[$parentCompKey] ?? null);
                                                                    @endphp
                                                                    <div
                                                                        class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                                        <span>{{ $parent->tr07_unit ?? '' }}</span>
                                                                        <strong>
                                                                             {!! $parent->tr07_result ?? '-' !!}
                                                                             @if ($parentStar)
                                                                                 <sup class="text-danger fw-bold ms-1">{{ $parentStar }}</sup>
                                                                             @endif
                                                                        </strong>
                                                                    </div>
                                                                @endif

                                                                @if ($hasPrimary)
                                                                    @php $subCounter = 1; @endphp
                                                                    @foreach ($results->groupBy('m16_primary_test_id') as $primaryId => $primaryResults)
                                                                        @php
                                                                            $primaryTest = $primaryResults->first()
                                                                                ->primaryTest;
                                                                            $hasSecondary = $primaryResults
                                                                                ->whereNotNull('m17_secondary_test_id')
                                                                                ->isNotEmpty();
                                                                        @endphp

                                                                        @if ($hasSecondary)
                                                                            {{-- Primary Test Header (No result shown here) --}}
                                                                            <div
                                                                                class="d-flex justify-content-between border-bottom pb-1 mb-1 bg-light px-2 rounded">
                                                                                <span
                                                                                    class="fst-italic">{{ $primaryTest->m16_name ?? 'Primary Parameter' }}
                                                                                    @if (!empty($primaryResults->first()->tr07_unit))
                                                                                        <strong>({{ $primaryResults->first()->tr07_unit }})</strong>
                                                                                    @endif
                                                                                    :
                                                                                </span>
                                                                                <strong></strong>
                                                                            </div>

                                                                            {{-- Secondary Tests --}}
                                                                            @foreach ($primaryResults->whereNotNull('m17_secondary_test_id') as $secondary)
                                                                                @php
                                                                                    $secKey = $secondary->tr07_test_result_id ? (string)$secondary->tr07_test_result_id : null;
                                                                                    $secCompKey = $item['test_number'] . '_' . ($secondary->m16_primary_test_id ?: '0') . '_' . $secondary->m17_secondary_test_id;
                                                                                    $secStar = ($secKey ? ($remarksStarMap[$secKey] ?? null) : null) ?? ($remarksStarMap[$secCompKey] ?? null);
                                                                                @endphp
                                                                                <div
                                                                                    class="d-flex justify-content-between border-bottom pb-1 mb-1 ms-3">
                                                                                    <span
                                                                                        class="fst-italic">{{ $secondary->secondaryTest->m17_name ?? 'Secondary Parameter' }}
                                                                                        @if (!empty($secondary->tr07_unit))
                                                                                            <strong>({{ $secondary->tr07_unit }})</strong>
                                                                                        @endif
                                                                                        :
                                                                                    </span>
                                                                                    <strong>
                                                                                        {!! $secondary->tr07_result ?? '-' !!}
                                                                                        @if ($secStar)
                                                                                            <sup class="text-danger fw-bold ms-1">{{ $secStar }}</sup>
                                                                                        @endif
                                                                                    </strong>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            @php
                                                                                $priItem = $primaryResults->first();
                                                                                $priKey = $priItem?->tr07_test_result_id ? (string)$priItem->tr07_test_result_id : null;
                                                                                $priCompKey = $item['test_number'] . '_' . ($primaryId ?: '0') . '_0';
                                                                                $priStar = ($priKey ? ($remarksStarMap[$priKey] ?? null) : null) ?? ($remarksStarMap[$priCompKey] ?? null);
                                                                            @endphp
                                                                            <div
                                                                                class="d-flex justify-content-between border-bottom pb-1 mb-1 bg-light px-2 rounded">
                                                                                <span
                                                                                    class="fst-italic">{{ $primaryTest->m16_name ?? 'Primary Parameter' }}
                                                                                    <strong>(
                                                                                        {{ $primaryResults->first()->tr07_unit ?? '' }}
                                                                                        )</strong>:</span>
                                                                                <strong>
                                                                                    {!! $primaryResults->first()->tr07_result ?? '-' !!}
                                                                                    @if ($priStar)
                                                                                        <sup class="text-danger fw-bold ms-1">{{ $priStar }}</sup>
                                                                                    @endif
                                                                                </strong>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @endif

                                                                {{-- Custom Fields for this test --}}
                                                                @php
                                                                    $customFields =
                                                                        $groupedCustomFields[$testNumber] ??
                                                                        collect();
                                                                @endphp

                                                                @if ($customFields->isNotEmpty())
                                                                    @foreach ($customFields as $custom)
                                                                        <div
                                                                            class="d-flex justify-content-between border-bottom pb-1 mb-1 bg-light px-2 rounded">
                                                                            <span>{{ $custom->tr08_field_name }}:</span>
                                                                            <strong>
                                                                                {!! $custom->tr08_field_value !!}
                                                                                @if ($custom->tr08_field_unit)
                                                                                    ({{ $custom->tr08_field_unit }})
                                                                                @endif
                                                                            </strong>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- Aryl Amines Test (Special Format) --}}
                                                        <div class="test-section border rounded p-3 mb-3 bg-warning bg-opacity-10 draggable-item"
                                                            data-index="{{ $index }}" data-type="test"
                                                            data-test-number="{{ $testNumber }}">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <strong class="h6 mb-1">{{ $testName }}</strong>
                                                                    <small class="text-muted d-block">Special Format - Aryl
                                                                        Amines</small>
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="badge bg-warning me-2">{{ $results->count() }}
                                                                        amines</span>
                                                                    <span class="drag-handle text-muted cursor-grab">
                                                                        <em class="icon ni ni-menu"></em>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-warning small mb-0">
                                                                <strong>Note:</strong> This test uses a special two-column
                                                                format in the final report.
                                                            </div>
                                                        </div>
                                                    @endif
                                        @endforeach
                                    </div>

                                    {{-- Remarks Section Preview (Printed before QR code) --}}
                                    @if (!empty($allRemarksList))
                                        <div class="card card-bordered bg-white p-3 my-3">
                                            <h6 class="card-title text-decoration-underline mb-2 fs-13px fw-bold">Remarks:</h6>
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($allRemarksList as $rem)
                                                    <li class="mb-1 fs-12px">
                                                        <strong class="text-danger fw-bold me-1">{{ $rem['stars'] }}</strong>
                                                        <span>{{ $rem['text'] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Signatory Section Preview --}}
                                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-end">
                                        <div class="text-start">
                                            @php
                                                $qrTrackerId = $sample->tr04_tracker_id ?? $sample->tr04_reference_id;
                                                $qrUrl = $qrTrackerId ? route('track_sample', ['trackerId' => $qrTrackerId]) : url('/');
                                                $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->margin(0)->generate($qrUrl);
                                            @endphp
                                            <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodeSvg) }}"
                                                style="width: 70px; height: 70px; display: block;" alt="QR Code">
                                        </div>
                                        <div class="d-inline-block text-center">
                                            <div class="border-top border-dark pt-1" style="width: 200px;"></div>
                                            @php
                                                $signatoryName = $issuerName
                                                    ?? session('name')
                                                    ?? optional($report->generator)->m06_name
                                                    ?? optional($report->generatedBy)->m06_name
                                                    ?? (!empty($report->m06_generated_by) ? \App\Models\Employee::where('m06_employee_id', $report->m06_generated_by)->value('m06_name') : null)
                                                    ?? 'Authorized Signatory';
                                            @endphp
                                            <div class="fw-bold mt-1">
                                                {{ $signatoryName }}</div>
                                            <small class="text-muted">Authorized Signatory</small>
                                        </div>
                                    </div>

                                    {{-- Pre-printed Footer Preview --}}
                                    <div class="preprinted-elem border-top border-2 border-primary pt-3 mt-4 text-center">
                                        <div style="font-size: 11px; font-weight: bold; color: #333;">Sample not drawn by Textiles Committee, Results relate only to the sample tested.</div>
                                        <div style="font-size: 11px; font-weight: bold; color: #333;">This test report shall not be published in any form without the explicit written consent of the Textiles Committee.</div>
                                        <div style="font-size: 11.5px; font-weight: bold; color: #c00000;">Please quote Test Report No. and date for all future correspondence.</div>
                                        <div style="font-size: 11px; font-weight: bold; color: #333;">Complaints if any, are to be received within 45 days from the date of issue of test report.</div>
                                        <div style="font-size: 12px; font-weight: bold; color: #24004d; margin-top: 4px;">Avail services of Textiles Committee -Most Reliable and Most Accurate</div>
                                        <div style="font-size: 11px; color: #008000;">"Follow us on fb.com/textilescommittee, @TexComIndia"</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary" id="reset-order" style="white-space: nowrap;">
                                        <em class="icon ni ni-refresh"></em> Reset to Default Order
                                    </button>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    {{-- Preview PDF Dropdown --}}
                                    <div class="btn-group">
                                        <a href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id]) }}"
                                            class="btn btn-info" target="_blank" style="white-space: nowrap;">
                                            <em class="icon ni ni-eye me-1"></em> Preview PDF (Full)
                                        </a>
                                        <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id]) }}" target="_blank">
                                                    <em class="icon ni ni-file-text me-1 text-primary"></em> Preview Full Report (Plain Paper)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id, 'letterhead' => 1]) }}" target="_blank">
                                                    <em class="icon ni ni-layout me-1 text-warning"></em> Preview on Letterhead (Stationery)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Partial Report Dropdown --}}
                                    <div class="btn-group">
                                        <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'generate_partial' => true]) }}"
                                            class="btn btn-warning" style="white-space: nowrap;">
                                            <em class="icon ni ni-file-docs me-1"></em> Partial Report
                                        </a>
                                        <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'generate_partial' => true]) }}">
                                                    <em class="icon ni ni-file-text me-1 text-primary"></em> Partial (Full Report)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'generate_partial' => true, 'letterhead' => 1]) }}">
                                                    <em class="icon ni ni-layout me-1 text-warning"></em> Partial (On Letterhead)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Final PDF Report Download Dropdown --}}
                                    <div class="btn-group">
                                        <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true]) }}"
                                            class="btn btn-primary" style="white-space: nowrap;">
                                            <em class="icon ni ni-download me-1"></em> Download Full Report
                                        </a>
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true]) }}">
                                                    <em class="icon ni ni-file-text me-1 text-primary"></em> Download Full Report (Plain Paper)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'letterhead' => 1]) }}">
                                                    <em class="icon ni ni-printer me-1 text-success"></em> Download for Letterhead (Pre-printed Paper)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize drag and drop
            const sortable = Sortable.create(document.getElementById('sortable-tests'), {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                forceFallback: false,
                onStart: function(evt) {
                    document.body.classList.add('dragging-active');
                },
                onEnd: function(evt) {
                    document.body.classList.remove('dragging-active');
                    updateTestOrder();
                }
            });

            function updateTestOrder() {
                const order = [];
                document.querySelectorAll('.draggable-item').forEach((item, index) => {
                    order.push({
                        index: index,
                        type: item.dataset.type,
                        test_number: item.dataset.testNumber
                    });
                });

                // Send AJAX request to update order
                fetch('{{ route('update_test_order', ['id' => $sample->tr04_reference_id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            NioApp.Toast('Test order updated successfully', 'success', {
                                position: 'top-right',
                                icon: 'ni ni-check-circle',
                                time: 3000
                            });
                        } else {
                            NioApp.Toast(data.message || 'Error updating test order', 'error', {
                                position: 'top-right',
                                icon: 'ni ni-cross-circle',
                                time: 5000
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        NioApp.Toast('Network error: Could not update test order', 'error', {
                            position: 'top-right',
                            icon: 'ni ni-cross-circle',
                            time: 5000
                        });
                    });
            }

            // Reset order button
            document.getElementById('reset-order').addEventListener('click', function() {
                if (confirm('Are you sure you want to reset to the default order?')) {
                    fetch('{{ route('reset_test_order', ['id' => $sample->tr04_reference_id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                NioApp.Toast('Order reset successfully. Page will reload...',
                                    'success', {
                                        position: 'top-right',
                                        icon: 'ni ni-check-circle',
                                        time: 2000,
                                        onShown: function() {
                                            setTimeout(() => {
                                                location.reload();
                                            }, 1500);
                                        }
                                    });
                            } else {
                                NioApp.Toast(data.message || 'Error resetting test order', 'error', {
                                    position: 'top-right',
                                    icon: 'ni ni-cross-circle',
                                    time: 5000
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            NioApp.Toast('Network error: Could not reset test order', 'error', {
                                position: 'top-right',
                                icon: 'ni ni-cross-circle',
                                time: 5000
                            });
                        });
                }
            });
        });

        function switchPreviewMode(mode) {
            const previewContainer = document.getElementById('report-preview');
            const btnPlain = document.getElementById('btn-mode-plain');
            const btnLetterhead = document.getElementById('btn-mode-letterhead');
            const topPlain = document.getElementById('top-actions-plain');
            const topLetterhead = document.getElementById('top-actions-letterhead');
            const letterheadBanner = document.getElementById('letterhead-notice-banner');
            const descPlain = document.getElementById('mode-desc-plain');
            const descLetterhead = document.getElementById('mode-desc-letterhead');
            const preprintedElems = document.querySelectorAll('.preprinted-elem');

            if (mode === 'letterhead') {
                btnLetterhead.classList.add('btn-primary', 'active');
                btnLetterhead.classList.remove('btn-outline-primary');
                btnPlain.classList.remove('btn-primary', 'active');
                btnPlain.classList.add('btn-outline-primary');

                if (topPlain) {
                    topPlain.classList.remove('d-flex');
                    topPlain.classList.add('d-none');
                }
                if (topLetterhead) {
                    topLetterhead.classList.remove('d-none');
                    topLetterhead.classList.add('d-flex');
                }
                if (letterheadBanner) {
                    letterheadBanner.classList.remove('d-none');
                }

                if (descPlain) descPlain.classList.add('d-none');
                if (descLetterhead) descLetterhead.classList.remove('d-none');

                if (previewContainer) previewContainer.classList.add('letterhead-preview-active');
                preprintedElems.forEach(el => el.classList.add('dimmed-stationery'));
            } else {
                btnPlain.classList.add('btn-primary', 'active');
                btnPlain.classList.remove('btn-outline-primary');
                btnLetterhead.classList.remove('btn-primary', 'active');
                btnLetterhead.classList.add('btn-outline-primary');

                if (topLetterhead) {
                    topLetterhead.classList.remove('d-flex');
                    topLetterhead.classList.add('d-none');
                }
                if (topPlain) {
                    topPlain.classList.remove('d-none');
                    topPlain.classList.add('d-flex');
                }
                if (letterheadBanner) {
                    letterheadBanner.classList.add('d-none');
                }

                if (descLetterhead) descLetterhead.classList.add('d-none');
                if (descPlain) descPlain.classList.remove('d-none');

                if (previewContainer) previewContainer.classList.remove('letterhead-preview-active');
                preprintedElems.forEach(el => el.classList.remove('dimmed-stationery'));
            }
        }
    </script>

    <style>
        .letterhead-preview-active {
            border: 2px dashed #f59e0b !important;
            background-color: #fffdf7 !important;
        }

        .dimmed-stationery {
            opacity: 0.28 !important;
            filter: grayscale(85%) !important;
            border-color: #e5e7eb !important;
        }

        .draggable-item {
            transition: all 0.3s ease;
            cursor: default;
        }

        .draggable-item:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .drag-handle {
            cursor: grab;
            padding: 6px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .drag-handle:hover {
            background-color: #e9ecef;
            transform: scale(1.1);
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        /* Sortable.js classes */
        .sortable-ghost {
            opacity: 0.6;
            background: rgba(13, 110, 253, 0.1);
            border: 2px dashed #0d6efd;
            border-radius: 8px;
        }

        .sortable-chosen {
            transform: rotate(2deg);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sortable-drag {
            opacity: 0.9;
            transform: rotate(3deg) scale(1.02);
        }

        body.dragging-active {
            cursor: grabbing;
        }

        body.dragging-active * {
            cursor: grabbing !important;
        }
    </style>

    <!-- Modal: Send Back to Verification Officer -->
    <div class="modal fade" id="sendBackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('sample.custody.send_back') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sample_id" value="{{ $sample->tr04_reference_id }}">
                    <input type="hidden" name="to_role" value="Verification Officer">
                    <input type="hidden" name="context" value="reporting">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white"><em class="icon ni ni-undo me-1"></em> Send Back to Verification Officer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning py-2 mb-3">
                            <em class="icon ni ni-alert-circle me-1"></em>
                            Sample <strong>#{{ $sample->tr04_reference_id }}</strong> will be reverted to <strong>RESULTED</strong> and sent back to the <strong>Verification Officer</strong> for review.
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-bold">Mandatory Clarification Remark <span class="text-danger">*</span></label>
                            <textarea name="clarification_remark" class="form-control" rows="4" placeholder="Detail the issues found in the report, values, units, or remarks that require clarification..." required minlength="3"></textarea>
                            <small class="text-muted">The Verification Officer will see this exact message prominently upon opening the sample.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <em class="icon ni ni-send me-1"></em> Send Back
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
