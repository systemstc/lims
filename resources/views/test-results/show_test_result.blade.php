@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                {{-- Page Header --}}
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Test Results</h3>
                            <div class="nk-block-des text-soft">
                                <h6 class="mb-0">Sample: <strong
                                        class="fw-bold text-primary">#{{ $sampleInfo->tr04_reference_id }}</strong></h6>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('test_results') }}" class="btn btn-outline-primary btn-sm">
                                <em class="icon ni ni-caret-left-fill"></em> Back
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Main Layout --}}
                <div class="nk-block">
                    {{-- Summary Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner text-center p-3">
                                    <div class="icon-circle icon-circle-md bg-primary-dim mx-auto mb-2">
                                        <em class="icon ni ni-activity text-primary"></em>
                                    </div>
                                    <h5 class="mb-1">{{ $totalTests }}</h5>
                                    <span class="text-soft fs-sm">Total Tests</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner text-center p-3">
                                    <div class="icon-circle icon-circle-md bg-success-dim mx-auto mb-2">
                                        <em class="icon ni ni-check-circle text-success"></em>
                                    </div>
                                    <h5 class="mb-1">{{ $testResults->count() }}</h5>
                                    <span class="text-soft fs-sm">Results</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner text-center p-3">
                                    <div class="icon-circle icon-circle-md bg-warning-dim mx-auto mb-2">
                                        <em class="icon ni ni-edit text-warning"></em>
                                    </div>
                                    <h5 class="mb-1">{{ $customFields->count() }}</h5>
                                    <span class="text-soft fs-sm">Custom Fields</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner text-center p-3">
                                    <div class="icon-circle icon-circle-md bg-info-dim mx-auto mb-2">
                                        <em class="icon ni ni-file-text text-info"></em>
                                    </div>
                                    <h5 class="mb-1">{{ $statusCounts['VERIFIED'] ?? 0 }}</h5>
                                    <span class="text-soft fs-sm">Verified</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        {{-- LEFT COLUMN: Tests --}}
                        <div class="col-xxl-9">

                            {{-- Tests Accordion --}}
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="accordion accordion-md" id="testResultsAccordion">
                                        @forelse($groupedResults as $testNumber => $results)
                                            @php
                                                $parentTest = $results->first();
                                                $testCustomFields = $groupedCustomFields[$testNumber] ?? collect();
                                                $hasCustomFields = $testCustomFields->isNotEmpty();
                                                $totalEntries = $results->count() + ($hasCustomFields ? 1 : 0);
                                            @endphp

                                            <div class="accordion-item border-0 mb-3">
                                                <div class="accordion-header" id="heading-{{ $testNumber }}">
                                                    <button class="accordion-button collapsed rounded" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-{{ $testNumber }}" aria-expanded="false"
                                                        aria-controls="collapse-{{ $testNumber }}">
                                                        <div class="d-flex align-items-center w-100">
                                                            <div class="flex-grow-1 text-start">
                                                                <h6 class="mb-1">
                                                                    {{ $parentTest->test->m12_name ?? 'Test #' . $testNumber }}
                                                                </h6>
                                                                <small class="text-muted">Test #{{ $testNumber }} •
                                                                    {{ $totalEntries }} entries</small>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>

                                                <div id="collapse-{{ $testNumber }}" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-{{ $testNumber }}"
                                                    data-bs-parent="#testResultsAccordion">
                                                    <div class="accordion-body p-0">
                                                        {{-- Test Results --}}
                                                        <div class="p-3 bg-light border-bottom">
                                                            <h6 class="text-primary mb-3">
                                                                <em class="icon ni ni-activity me-1"></em>
                                                                Test Results
                                                            </h6>

                                                            @foreach ($results->groupBy('m16_primary_test_id') as $primaryTestId => $primaryResults)
                                                                @php
                                                                    $primaryTest = $primaryResults->first()->primaryTest;
                                                                    $hasPrimaryTest = !empty($primaryTestId);
                                                                @endphp

                                                                @if ($hasPrimaryTest)
                                                                    <div class="mb-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <em class="icon ni ni-list text-primary me-2"></em>
                                                                            <strong class="text-dark">{{ $primaryTest->m16_name ?? 'Primary Test' }}</strong>
                                                                            @if ($primaryTest->m16_requirement)
                                                                                <small class="text-muted ms-2">({{ $primaryTest->m16_requirement }})</small>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                {{-- Group results in pairs for two columns per row --}}
                                                                @php
                                                                    $allResults = [];
                                                                    foreach ($primaryResults->groupBy('m17_secondary_test_id') as $secondaryTestId => $secondaryResults) {
                                                                        $allResults[] = [
                                                                            'hasSecondaryTest' => !empty($secondaryTestId),
                                                                            'testResult' => $secondaryResults->first(),
                                                                            'secondaryTest' => $secondaryResults->first()->secondaryTest,
                                                                        ];
                                                                    }

                                                                    // Split results into chunks of 2 for rows
                                                                    $resultChunks = array_chunk($allResults, 2);
                                                                @endphp

                                                                @foreach ($resultChunks as $resultRow)
                                                                    <div class="row g-2 mb-3">
                                                                        @foreach ($resultRow as $resultData)
                                                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                                                <div class="card card-sm bg-white border">
                                                                                    <div class="card-body p-3">
                                                                                        @if ($resultData['hasSecondaryTest'] && $resultData['secondaryTest'])
                                                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                                <strong class="text-dark fs-14">{{ $resultData['secondaryTest']->m17_name }}</strong>
                                                                                                <strong class="text-success fw-bold">Verified</strong>
                                                                                            </div>
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <span class="text-muted fs-12">Result:</span>
                                                                                                <strong class="text-primary">{{ $resultData['testResult']->tr07_result ?? 'N/A' }}</strong>
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                                <span class="text-dark fs-14">Test Result</span>
                                                                                                <div>
                                                                                                    <strong class="text-primary me-2">{{ $resultData['testResult']->tr07_result ?? 'N/A' }}</strong>
                                                                                                    <strong class="text-success fw-bold">Verified</strong>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach

                                                                        {{-- Add empty column if odd number of results --}}
                                                                        @if (count($resultRow) == 1)
                                                                            <div class="col-xl-6 col-lg-6 col-md-6"></div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            @endforeach
                                                        </div>

                                                        {{-- Custom Fields --}}
                                                        @if ($hasCustomFields)
                                                            <div class="p-3 bg-light border-top">
                                                                <h6 class="text-info mb-3">
                                                                    <em class="icon ni ni-plus-circle-fill me-1"></em>
                                                                    Additional
                                                                </h6>
                                                                @php
                                                                    $customFieldChunks = array_chunk($testCustomFields->toArray(), 2);
                                                                @endphp
                                                                
                                                                @foreach ($customFieldChunks as $customFieldRow)
                                                                    <div class="row g-2 mb-3">
                                                                        @foreach ($customFieldRow as $customField)
                                                                            <div class="col-xl-6 col-lg-6 col-md-6">
                                                                                <div class="card card-sm bg-white border">
                                                                                    <div class="card-body p-3">
                                                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                            <strong class="text-dark fs-14">{{ $customField['tr08_field_name'] }}</strong>
                                                                                            <strong class="text-info fw-bold">Verified</strong>
                                                                                        </div>
                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                            <span class="text-muted fs-12">Value:</span>
                                                                                            <div>
                                                                                                <strong class="text-primary">{{ $customField['tr08_field_value'] }}</strong>
                                                                                                @if ($customField['tr08_field_unit'])
                                                                                                    <small class="text-muted ms-1">({{ $customField['tr08_field_unit'] }})</small>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach

                                                                        {{-- Add empty column if odd number of custom fields --}}
                                                                        @if (count($customFieldRow) == 1)
                                                                            <div class="col-xl-6 col-lg-6 col-md-6"></div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        {{-- Test Metadata --}}
                                                        <div class="p-3 bg-light border-top">
                                                            <div class="row text-center g-3">
                                                                <div class="col-md-4">
                                                                    <small class="text-muted d-block">Performance Date</small>
                                                                    <span class="fw-medium fs-14">
                                                                        {{ $parentTest->tr07_performance_date ? \Carbon\Carbon::parse($parentTest->tr07_performance_date)->format('M d, Y') : 'N/A' }}
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <small class="text-muted d-block">Test Date</small>
                                                                    <span class="fw-medium fs-14">
                                                                        {{ $parentTest->tr07_test_date ? \Carbon\Carbon::parse($parentTest->tr07_test_date)->format('M d, Y') : 'N/A' }}
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <small class="text-muted d-block">Version</small>
                                                                    <span class="fw-medium fs-14">v{{ $parentTest->tr07_current_version }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5">
                                                <div class="icon-circle icon-circle-lg bg-primary-dim mx-auto mb-3">
                                                    <em class="icon ni ni-activity text-primary"></em>
                                                </div>
                                                <h5>No Test Results Found</h5>
                                                <p class="text-soft">No verified test results exist for this sample.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Sidebar --}}
                        <div class="col-xxl-3">
                            {{-- Quick Actions --}}
                            <div class="card card-bordered mb-4">
                                <div class="card-inner">
                                    <h6 class="card-title mb-3">Quick Actions</h6>
                                    <div class="d-grid gap-2 mb-3">
                                        <a href="{{ route('generate_report', $sampleInfo->tr04_reference_id) }}" class="btn btn-primary">
                                            <em class="icon ni ni-file-docs me-1"></em>
                                            Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Test Navigation --}}
                            <div class="card card-bordered mb-4">
                                <div class="card-inner">
                                    <h6 class="card-title mb-3">Test Navigation</h6>
                                    <div class="nav-scroll-container" style="max-height: 400px; overflow-y: auto;">
                                        <div class="nav flex-column nav-pills nav-pills-sm">
                                            @foreach ($groupedResults as $testNumber => $tests)
                                                <a class="nav-link text-start mb-2" href="#heading-{{ $testNumber }}"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $testNumber }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-truncate">
                                                            <em class="icon ni ni-activity me-1 fs-12"></em>
                                                            Test #{{ $testNumber }}
                                                        </span>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @foreach ($groupedCustomFields as $testNumber => $fields)
                                                @if (!isset($groupedResults[$testNumber]))
                                                    <a class="nav-link text-start mb-2" href="#heading-{{ $testNumber }}"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $testNumber }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-truncate">
                                                                <em class="icon ni ni-edit me-1 fs-12"></em>
                                                                Custom #{{ $testNumber }}
                                                            </span>
                                                            <span class="badge bg-warning fs-10">{{ $fields->count() }}</span>
                                                        </div>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sample Info --}}
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <h6 class="card-title mb-3">Sample Information</h6>
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item px-0 py-2">
                                            <small class="text-muted d-block">Sample ID</small>
                                            <strong>{{ $sampleInfo->tr04_reference_id }}</strong>
                                        </div>
                                        <div class="list-group-item px-0 py-2">
                                            <small class="text-muted d-block">Total Tests</small>
                                            <strong>{{ $totalTests }}</strong>
                                        </div>
                                        <div class="list-group-item px-0 py-2">
                                            <small class="text-muted d-block">Total Results</small>
                                            <strong>{{ $testResults->count() }}</strong>
                                        </div>
                                        <div class="list-group-item px-0 py-2">
                                            <small class="text-muted d-block">Custom Fields</small>
                                            <strong>{{ $customFields->count() }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-scroll-container::-webkit-scrollbar {
            width: 4px;
        }

        .nav-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .nav-scroll-container::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 2px;
        }

        .nav-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .card-sm {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-sm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .fs-12 {
            font-size: 0.75rem;
        }

        .fs-14 {
            font-size: 0.875rem;
        }

        @media (max-width: 1399.98px) {
            .col-xxl-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .col-xxl-3 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-top: 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .row.g-2 .col-xl-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for navigation links
            document.querySelectorAll('.nav-pills .nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-bs-target');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        // Collapse all other accordion items
                        document.querySelectorAll('.accordion-collapse.show').forEach(collapse => {
                            if (collapse.id !== targetId.replace('#', '')) {
                                bootstrap.Collapse.getInstance(collapse)?.hide();
                            }
                        });

                        // Show the target accordion
                        const bsCollapse = new bootstrap.Collapse(targetElement);
                        bsCollapse.show();

                        // Scroll to the accordion
                        setTimeout(() => {
                            const accordionHeader = document.querySelector(
                                `[data-bs-target="${targetId}"]`);
                            if (accordionHeader) {
                                const offsetTop = accordionHeader.getBoundingClientRect()
                                    .top + window.pageYOffset - 100;
                                window.scrollTo({
                                    top: offsetTop,
                                    behavior: 'smooth'
                                });
                            }
                        }, 350);
                    }
                });
            });

            // Add active class to navigation pills
            document.querySelectorAll('.accordion-button').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-bs-target');
                    document.querySelectorAll('.nav-pills .nav-link').forEach(navLink => {
                        navLink.classList.remove('active');
                        if (navLink.getAttribute('data-bs-target') === targetId) {
                            navLink.classList.add('active');
                        }
                    });
                });
            });
        });
    </script>
@endsection