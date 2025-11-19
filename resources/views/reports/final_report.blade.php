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
                            <a href="{{ route('test_results') }}" class="btn btn-outline-primary btn-sm">
                                <em class="icon ni ni-caret-left-fill"></em> Back
                            </a>
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

                                    {{-- Sample Information Table --}}
                                    <table class="table table-bordered table-sm mb-4">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Test Report No</th>
                                                <td>{{ $meta['report_no'] }}</td>
                                                <th width="20%" class="text-end">Date</th>
                                                <td width="20%">{{ $meta['date'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Name & Address of Customer</th>
                                                <td colspan="3">{{ $meta['customer_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sample Description</th>
                                                <td colspan="3" class="text-center">{{ $meta['sample_description'] }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    {{-- Test Results Section --}}
                                    <div class="text-center mb-3">
                                        <h5 class="text-decoration-underline">TEST RESULTS</h5>
                                    </div>

                                    {{-- Sample Details --}}
                                    <table class="table table-bordered table-sm mb-4">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Sample Characteristics</th>
                                                <td>{{ $meta['sample_characteristics'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date of Performance of Test(s)</th>
                                                <td>{{ $meta['date'] }} to {{ $meta['test_performance_date'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Laboratory Sample No.</th>
                                                <td>{{ $meta['report_no'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

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
                                                        $isArylAminesTest =
                                                            strpos(strtolower($testName), 'aryl amine') !== false;
                                                    @endphp

                                                    @if (!$isArylAminesTest)
                                                        <div class="test-section border rounded p-3 mb-3 bg-white draggable-item"
                                                            data-index="{{ $index }}" data-type="test"
                                                            data-test-number="{{ $item['test_number'] }}">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <strong class="h6 mb-1">{{ $testName }} - </strong>
                                                                    @if ($parent->test->standard->m15_method ?? false)
                                                                        <small
                                                                            class="fw-bold">({{ $parent->test->standard->m15_method }})</small>
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
                                                                    <div
                                                                        class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                                        <span>{{ $parent->tr07_unit }}</span>
                                                                        <strong>{{ $parent->tr07_result ?? '-' }}</strong>
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
                                                                            @foreach ($primaryResults->whereNotNull('m17_secondary_test_id') as $secondary)
                                                                                <div
                                                                                    class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                                                    <span
                                                                                        class="fst-italic">{{ $secondary->secondaryTest->m17_name ?? 'Secondary Parameter' }}
                                                                                        <strong>(
                                                                                            {{ $secondary->tr07_unit ?? '' }}
                                                                                            )</strong> :</span>
                                                                                    <strong>{{ $secondary->tr07_result ?? '-' }}</strong>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            <div
                                                                                class="d-flex justify-content-between border-bottom pb-1 mb-1 bg-light px-2 rounded">
                                                                                <span
                                                                                    class="fst-italic">{{ $primaryTest->m16_name ?? 'Primary Parameter' }}
                                                                                    <strong>(
                                                                                        {{ $primaryResults->first()->tr07_unit ?? '' }}
                                                                                        )</strong>:</span>
                                                                                <strong>{{ $primaryResults->first()->tr07_result ?? '-' }}</strong>
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
                                                                                {{ $custom->tr08_field_value }}
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

                                    {{-- Signatory Section Preview --}}
                                    <div class="mt-4 pt-3 border-top text-end">
                                        <div class="d-inline-block text-center">
                                            <div class="border-top border-dark pt-1" style="width: 200px;"></div>
                                            <div class="fw-bold mt-1">
                                                {{ $report->generator->m06_name ?? 'Lab Manager JNPT' }}</div>
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

@endsection
