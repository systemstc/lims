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
                                <button type="button" class="btn btn-dim btn-outline-danger btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#sendBackModal">
                                    <em class="icon ni ni-undo me-1"></em> <span>Send Back to Verification Officer</span>
                                </button>
                                <a href="{{ route('test_results') }}" class="btn btn-outline-primary btn-sm fw-bold" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem; display: inline-flex; align-items: center; white-space: nowrap;">
                                    <em class="icon ni ni-arrow-left me-1"></em> <span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            {{-- <div class="alert alert-info">
                                <em class="icon ni ni-info"></em>
                                <strong>Drag and drop sections</strong> to arrange tests in your preferred order for the report.
                            </div> --}}

                            {{-- Report Preview with Reordering --}}
                            <div class="mb-4">

                                <div id="report-preview" class="border rounded p-4 bg-light">
                                    {{-- Report Header --}}
                                    <div class="text-center mb-4">
                                        <h4 class="text-decoration-underline">TEST REPORT</h4>
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
                                            @if ($item['type'] === 'test')
                                                @php
                                                    $results = $groupedResults[$item['test_number']] ?? collect();
                                                    $parent = $results->first();
                                                @endphp

                                                @if ($parent)
                                                    @php
                                                        $testName =
                                                            $parent->test->m12_name ?? 'Test #' . $item['test_number'];
                                                        $isArylAminesTest = (strpos(strtolower($testName), 'banned amines') !== false || strpos(strtolower($testName), 'aryl amine') !== false || strpos(strtolower($testName), 'azo') !== false);
                                                    @endphp

                                                    @if (!$isArylAminesTest)
                                                        <div class="test-section border rounded p-3 mb-3 bg-white draggable-item"
                                                            data-index="{{ $index }}" data-type="test"
                                                            data-test-number="{{ $item['test_number'] }}">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <strong class="h6 mb-1">{{ $testName }} - </strong>
                                                                    @php
                                                                        $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $item['test_number']);
                                                                        $standardMethod = optional($sampleTest)->standard_method ?? optional($sampleTest->standard)->m15_method ?? optional($parent->test->standard)->m15_method ?? null;
                                                                    @endphp
                                                                    @if ($standardMethod)
                                                                        <small class="fw-bold">({{ $standardMethod }})</small>
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
                                                                @endphp

                                                                 @if (!$hasPrimary)
                                                                    @php
                                                                        $parentKey = $parent->tr07_test_result_id ? (string)$parent->tr07_test_result_id : null;
                                                                        $parentCompKey = $item['test_number'] . '_0_0';
                                                                        $parentStar = ($parentKey ? ($remarksStarMap[$parentKey] ?? null) : null) ?? ($remarksStarMap[$parentCompKey] ?? null);
                                                                    @endphp
                                                                    <div
                                                                        class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                                        <span>{{ $parent->tr07_unit }}</span>
                                                                        <strong>
                                                                            {!! $parent->tr07_result ?? '-' !!}
                                                                            @if ($parentStar)
                                                                                <sup class="text-danger fw-bold ms-1">{{ $parentStar }}</sup>
                                                                            @endif
                                                                        </strong>
                                                                    </div>
                                                                @else
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
                                                                        $groupedCustomFields[$item['test_number']] ??
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
                                                            data-test-number="{{ $item['test_number'] }}">
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
                                                @endif
                                            @else
                                                {{-- Custom Data Section --}}
                                                @php
                                                    $customFields =
                                                        $groupedCustomFields[$item['test_number']] ?? collect();
                                                @endphp
                                                <div class="test-section border rounded p-3 mb-3 bg-info bg-opacity-10 draggable-item"
                                                    data-index="{{ $index }}" data-type="custom"
                                                    data-test-number="{{ $item['test_number'] }}">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <strong class="h6 mb-1">Additional Data</strong>
                                                            <small class="text-muted d-block">Custom Fields</small>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-info me-2">{{ $customFields->count() }}
                                                                fields</span>
                                                            <span class="drag-handle text-muted cursor-grab">
                                                                <em class="icon ni ni-menu"></em>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="ms-3">
                                                        @foreach ($customFields as $custom)
                                                            <div
                                                                class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                                <span>{{ $custom->tr08_field_name }}:</span>
                                                                <strong>
                                                                    {{ $custom->tr08_field_value }}
                                                                    @if ($custom->tr08_field_unit)
                                                                        ({{ $custom->tr08_field_unit }})
                                                                    @endif
                                                                </strong>
                                                            </div>
                                                        @endforeach
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
                                            <div class="fw-bold mt-1">
                                                {{ optional($report->generator)->m06_name ?? 'Lab Manager JNPT' }}</div>
                                            <small class="text-muted">Authorized Signatory</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-outline-primary" id="reset-order">
                                        <em class="icon ni ni-refresh"></em> Reset to Default Order
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ route('generate_report_preview', ['id' => $sample->tr04_reference_id]) }}"
                                        class="btn btn-info me-2" target="_blank">
                                        <em class="icon ni ni-eye"></em> Preview PDF
                                    </a>
                                    <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true, 'generate_partial' => true]) }}"
                                        class="btn btn-warning me-2">
                                        <em class="icon ni ni-file-docs"></em> Partial Report
                                    </a>
                                    <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true]) }}"
                                        class="btn btn-primary">
                                        <em class="icon ni ni-file-docs"></em> Generate PDF Report
                                    </a>
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
    </script>

    <style>
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
