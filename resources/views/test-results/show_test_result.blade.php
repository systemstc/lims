@extends('layouts.app_back')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    {{-- Page header --}}
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $sampleInfo->tr04_reference_id }}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Sample ID: {{ $sampleInfo->tr04_reference_id }} | Total Tests:
                                        {{ $testResults->count() }}</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('test_results') }}" class="btn btn-outline-light">
                                    <em class="icon ni ni-arrow-left"></em><span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Main Layout: Tests on left, Sidebar on right --}}
                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-lg-8">
                                @forelse($groupedResults as $testNumber => $results)
                                    @php
                                        $parentTest = $results->first();
                                    @endphp
                                    <div class="card card-bordered mb-4" id="test-{{ $testNumber }}">
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
                                                        {{ $results->count() > 1 ? 'Manuscripts' : 'Result' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Child Manuscripts / Results --}}
                                        @foreach ($results as $index => $testResult)
                                            <div class="card-inner {{ $index > 0 ? 'border-top' : '' }}">
                                                <div class="row g-3 align-items-center mb-3">
                                                    <div class="col-md-8">
                                                        @if ($testResult->manuscript)
                                                            {{-- Has Manuscript --}}
                                                            <div class="d-flex align-items-center">
                                                                <em class="icon ni ni-file-text text-info me-2"></em>
                                                                <div>
                                                                    <span
                                                                        class="fw-medium d-block">{{ $testResult->manuscript->m22_name }}</span>
                                                                    <span class="fs-sm text-soft">Manuscript ID:
                                                                        {{ $testResult->manuscript->m22_manuscript_id }}</span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            {{-- Direct Test Result (No Manuscript) --}}
                                                            <div class="d-flex align-items-center">
                                                                <em class="icon ni ni-check-circle text-success me-2"></em>
                                                                <div>
                                                                    <span class="fw-medium d-block">Direct Result</span>
                                                                    <span class="fs-sm text-soft">No manuscript
                                                                        attached</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        @if ($testResult->tr07_result_status == 'DRAFT')
                                                            <span
                                                                class="badge badge-dot has-bg bg-warning d-inline-flex align-items-center">Draft</span>
                                                        @elseif ($testResult->tr07_result_status == 'SUBMITTED')
                                                            <span
                                                                class="badge badge-dot has-bg bg-success d-inline-flex align-items-center">Submitted</span>
                                                        @else
                                                            <span
                                                                class="badge badge-dot has-bg bg-info d-inline-flex align-items-center">Revised</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Result Details Grid --}}
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-3">
                                                        <span class="fs-sm text-soft">Version</span>
                                                        <span
                                                            class="fw-medium d-block">v{{ $testResult->tr07_current_version }}</span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="fs-sm text-soft">Result</span>
                                                        <span
                                                            class="d-block fw-bold text-primary">{{ $testResult->tr07_result ?? 'N/A' }}</span>
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

                                                {{-- Actions --}}
                                                <div class="d-flex justify-content-end">
                                                    @if ($testResult->tr07_result_status == 'DRAFT')
                                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#finalizeModal{{ $testResult->tr07_test_result_id }}">
                                                            <em class="icon ni ni-check"></em><span>Finalize</span>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#reviseModal{{ $testResult->tr07_test_result_id }}">
                                                            <em class="icon ni ni-pen2"></em><span>Revise</span>
                                                        </button>
                                                    @endif
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

                            {{-- RIGHT COLUMN: Single Sidebar for Sample --}}
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
                                                <span class="text-soft fs-sm">Total Tests</span>
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

                                {{-- Test Status Summary --}}
                                <div class="card card-bordered mb-3">
                                    <div class="card-inner">
                                        <h6 class="card-title mb-3">Test Status Summary</h6>
                                        @php
                                            $statusCounts = $testResults
                                                ->groupBy(function ($item) {
                                                    return strtolower($item->tr07_status);
                                                })
                                                ->map->count();
                                        @endphp
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="status-card bg-warning-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['draft'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Draft</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="status-card bg-success-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['finalized'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Finalized</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="status-card bg-info-dim p-3 rounded text-center">
                                                    <h4 class="mb-1">{{ $statusCounts['revised'] ?? 0 }}</h4>
                                                    <span class="fs-sm">Revised</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- All Tests Quick Links --}}
                                <div class="card card-bordered mb-3">
                                    <div class="card-inner">
                                        <h6 class="card-title mb-3">Quick Navigation</h6>
                                        <div class="list-group list-group-flush">
                                            @foreach ($testResults as $test)
                                                <a href="#test-{{ $test->tr07_test_result_id }}"
                                                    class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-medium d-block">
                                                            {{ $test->tr07_test_name ?? 'Test #' . $test->tr07_test_result_id }}
                                                        </span>
                                                        <span class="fs-sm text-soft">
                                                            v{{ $test->tr07_current_version }} •
                                                            {{ $test->tr07_test_date ? \Carbon\Carbon::parse($test->tr07_test_date)->format('M d') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    @if (strtolower($test->tr07_status) == 'draft')
                                                        <span class="badge badge-sm badge-warning">Draft</span>
                                                    @elseif (strtolower($test->tr07_status) == 'finalized')
                                                        <span class="badge badge-sm badge-success">Final</span>
                                                    @else
                                                        <span class="badge badge-sm badge-info">Revised</span>
                                                    @endif
                                                </a>
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
                                                <span>Generate Complete Report</span>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-file-docs"></em>
                                                </a>
                                            </li>
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>Export All Tests</span>
                                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                                    <em class="icon ni ni-download"></em>
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

                        </div>
                    </div>

                    {{-- Shared Delete Modal --}}
                    <div class="modal fade" id="deleteModal" tabindex="-1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this test result? This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <form id="deleteForm" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteResult(id) {
            var base = "{{ route('destroy', '') }}";
            var action = base.endsWith('/') ? base + id : base + '/' + id;
            document.getElementById('deleteForm').setAttribute('action', action);
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Smooth scroll for quick navigation
        document.querySelectorAll('a[href^="#test-"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
@endsection
