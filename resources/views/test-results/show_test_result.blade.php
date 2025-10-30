@extends('layouts.app_back')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    {{-- Page Header --}}
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $sampleInfo->tr04_reference_id }}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Sample ID: {{ $sampleInfo->tr04_reference_id }} | Total Tests: {{ $totalTests }}
                                    </p>
                                </div>
                            </div>
                                <a href="{{ route('test_results') }}" class="btn btn-outline-primary">
                                    <em class="icon ni ni-caret-left-fill"></em> Back
                                </a>
                        </div>
                    </div>

                    {{-- Main Layout --}}
                    <div class="nk-block">
                        <div class="row g-gs">
                            {{-- LEFT COLUMN: Tests --}}
                            <div class="col-lg-8">
                                @forelse($groupedResults as $testNumber => $results)
                                    @php
                                        $parentTest = $results->first();
                                    @endphp

                                    <div class="card card-bordered mb-4" id="test-{{ $parentTest->tr07_test_result_id }}">
                                        {{-- Card Header --}}
                                        <div class="card-inner border-bottom bg-light">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-md-1">
                                                    <span class="icon-circle icon-circle-lg bg-primary-dim">
                                                        <em class="icon ni ni-activity text-primary"></em>
                                                    </span>
                                                </div>
                                                <div class="col-md-7">
                                                    <h5 class="mb-1">{{ $parentTest->test->m12_name ?? 'Unknown Test' }}
                                                    </h5>
                                                    <span class="text-soft fs-sm">Test #{{ $testNumber }}</span>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <span class="badge bg-primary">
                                                        {{ $results->count() }}
                                                        {{ $results->count() > 1 ? 'Results' : 'Result' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Card Body: All Manuscripts / Results --}}
                                        @foreach ($results as $index => $testResult)
                                            <div class="card-inner {{ $index > 0 ? 'border-top' : '' }} ">
                                                <div class="row g-3 align-items-center mb-3">
                                                    <div class="col-md-8">
                                                        @if ($testResult->manuscript)
                                                            <div class="d-flex align-items-center">
                                                                <em class="icon ni ni-file-text text-info me-2"></em>
                                                                <div>
                                                                    <span
                                                                        class="fw-medium d-block">{{ $testResult->manuscript->m22_name }}</span>
                                                                    <span class="fs-sm text-soft">Manuscript ID:
                                                                        {{ $testResult->manuscript->m22_manuscript_id }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4 text-end">
                                                        @php $status = strtoupper($testResult->tr07_result_status); @endphp
                                                        <span
                                                            class="badge badge-dot has-bg 
                                                        @if ($status === 'REJECTED') bg-danger
                                                        @elseif ($status === 'VERIFIED') bg-info
                                                        @elseif ($status === 'AUTHORIZED') bg-success
                                                        @elseif ($status === 'REVISED') bg-secondary @endif">
                                                            {{ ucfirst(strtolower($status)) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Result Info --}}
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-3">
                                                        <span class="fs-sm text-soft">Version</span>
                                                        <span
                                                            class="fw-medium d-block">v{{ $testResult->tr07_current_version }}</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="fs-sm text-soft">Result</span>
                                                        <span
                                                            class="fw-bold text-primary d-block">{{ $testResult->tr07_result ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="fs-sm text-soft">Test Date</span>
                                                        <span class="d-block">
                                                            {{ $testResult->tr07_test_date ? \Carbon\Carbon::parse($testResult->tr07_test_date)->format('M d, Y') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="fs-sm text-soft">Performance Date</span>
                                                        <span class="d-block">
                                                            {{ $testResult->tr07_performance_date ? \Carbon\Carbon::parse($testResult->tr07_performance_date)->format('M d, Y') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                @empty
                                    <div class="card card-bordered">
                                        <div class="card-inner text-center py-5">
                                            <div class="icon-circle icon-circle-lg bg-primary-dim mb-3">
                                                <em class="icon ni ni-activity text-primary"></em>
                                            </div>
                                            <h5>No Test Results Found</h5>
                                            <p class="text-soft">No tests exist for this sample.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            {{-- RIGHT COLUMN: Sidebar --}}
                            <div class="col-lg-4">
                                {{-- Sample Overview --}}
                                <div class="card card-bordered mb-3">
                                    <div class="card-inner">
                                        <h6 class="card-title mb-3">Sample Overview</h6>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item px-0">
                                                <span class="text-soft fs-sm">Sample ID</span>
                                                <span class="d-block fw-medium">{{ $sampleInfo->tr04_reference_id }}</span>
                                            </li>
                                            <li class="list-group-item px-0">
                                                <span class="text-soft fs-sm">Total Outputs</span>
                                                <span class="d-block fw-medium">{{ $testResults->count() }}</span>
                                            </li>
                                            <li class="list-group-item px-0">
                                                <span class="text-soft fs-sm">Test Date Range</span>
                                                <span class="d-block fw-medium">
                                                    @php
                                                        $dates = $testResults->pluck('tr07_test_date')->filter();
                                                        $minDate = $dates->min();
                                                        $maxDate = $dates->max();
                                                    @endphp
                                                    @if ($minDate && $maxDate)
                                                        {{ \Carbon\Carbon::parse($minDate)->format('M d, Y') }}
                                                        @if ($minDate != $maxDate)
                                                            - {{ \Carbon\Carbon::parse($maxDate)->format('M d, Y') }}
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Status Summary --}}
                                <div class="card card-bordered mb-3">
                                    <div class="card-inner">
                                        <h6 class="card-title mb-3">Output Status Summary</h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="status-card bg-warning-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['DRAFT'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Draft</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="status-card bg-info-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['SUBMITTED'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Submitted</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="status-card bg-danger-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['REVISED'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Revised</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="status-card bg-success-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['VERIFIED'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Verified</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Navigation --}}
                                <div class="card card-bordered mb-3">
                                    <div class="card-inner">
                                        <h6 class="card-title mb-3">Quick Navigation</h6>
                                        <div class="accordion" id="testAccordion">
                                            @foreach ($groupedResults as $testNumber => $tests)
                                                <div class="accordion-item mb-2">
                                                    <h2 class="accordion-header" id="heading-{{ $testNumber }}">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-{{ $testNumber }}"
                                                            aria-expanded="false"
                                                            aria-controls="collapse-{{ $testNumber }}">
                                                            Test No: {{ $testNumber }}
                                                            <span
                                                                class="text-primary ms-2 fw-medium">{{ $tests->count() }}
                                                                results</span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-{{ $testNumber }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="heading-{{ $testNumber }}"
                                                        data-bs-parent="#testAccordion">
                                                        <div class="accordion-body p-0">
                                                            <div class="list-group list-group-flush">
                                                                @foreach ($tests as $test)
                                                                    <a href="#test-{{ $test->tr07_test_result_id }}"
                                                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <span
                                                                                class="fw-medium d-block">{{ $test->test->m12_name ?? 'Test #' . $test->tr07_test_result_id }}</span>
                                                                            <span class="fs-sm text-soft">
                                                                                v{{ $test->tr07_current_version }} •
                                                                                {{ $test->tr07_test_date ? \Carbon\Carbon::parse($test->tr07_test_date)->format('M d, Y') : 'N/A' }}
                                                                            </span>
                                                                        </div>
                                                                        <span
                                                                            class="fw-bold
                                                                        @if ($test->tr07_result_status === 'DRAFT') text-warning
                                                                        @elseif ($test->tr07_result_status === 'SUBMITTED') text-info
                                                                        @elseif ($test->tr07_result_status === 'REVISED') text-danger
                                                                        @elseif ($test->tr07_result_status === 'VERIFIED') text-success
                                                                        @else text-secondary @endif">
                                                                            {{ ucfirst(strtolower($test->tr07_result_status)) }}
                                                                        </span>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Sample Actions --}}
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <h6 class="card-title mb-3">Sample Actions</h6>
                                        <ul class="list-group list-group-flush">
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>Generate Final Report</span>
                                                <a href="{{ route('generate_report', $sampleInfo->tr04_reference_id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-file-docs"></em>
                                                </a>
                                            </li>
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>View Full Audit Trail</span>
                                                <a href="#" class="btn btn-sm btn-outline-info">
                                                    <em class="icon ni ni-eye"></em>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            {{-- End Sidebar --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
/* small highlight when navigating */
.ring-highlight {
  box-shadow: 0 0 0 4px rgba(59,130,246,0.15), 0 2px 8px rgba(0,0,0,0.06);
  transition: box-shadow 250ms ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // adjust this offset if you have a fixed header (in px)
    const SCROLL_OFFSET = 80;

    // attach to all anchor links that point to test cards
    document.querySelectorAll('a[href^="#test-"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const href = anchor.getAttribute('href'); // "#test-57"
            if (!href) return;

            const id = href.slice(1);
            const target = document.getElementById(id);
            if (!target) {
                // If target not present, fallback to updating hash only (no reload)
                history.replaceState(null, '', href);
                return;
            }

            // If the target is inside a Bootstrap collapse, open that collapse first
            const collapseEl = target.closest('.collapse');
            if (collapseEl && !collapseEl.classList.contains('show')) {
                // Use Bootstrap 5's Collapse API to show it, then scroll after it's fully shown
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);
                const onShown = function () {
                    collapseEl.removeEventListener('shown.bs.collapse', onShown);
                    doScrollAndHighlight(target, href);
                };
                collapseEl.addEventListener('shown.bs.collapse', onShown);
                bsCollapse.show();
            } else {
                doScrollAndHighlight(target, href);
            }
        }, { passive: false });
    });

    function doScrollAndHighlight(target, href) {
        // compute exact position with offset
        const top = target.getBoundingClientRect().top + window.pageYOffset - SCROLL_OFFSET;

        window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });

        // update URL hash without page jump / history entry
        try {
            history.replaceState(null, '', href);
        } catch (err) {
            // some older browsers may fail; ignore safely
        }

        // add highlight so it's visible to user
        target.classList.add('ring-highlight');
        setTimeout(() => target.classList.remove('ring-highlight'), 1400);
    }
});
</script>

@endsection
