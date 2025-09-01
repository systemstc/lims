@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Test Results Management</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Manage and track test results with version control</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                        data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li><a href="{{ route('create_test_result') }}" class="btn btn-primary"><em
                                                        class="icon ni ni-plus"></em><span>Add Test Result</span></a></li>
                                            <li><a href="{{ route('test_results_audit') }}"
                                                    class="btn btn-outline-light"><em
                                                        class="icon ni ni-eye"></em><span>Audit Trail</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="nk-block">
                        <div class="card card-bordered card-stretch">
                            <div class="card-inner-group">
                                <div class="card-inner">
                                    <form method="GET" action="{{ route('test_results') }}">
                                        <div class="row g-3 align-center">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="test-type">Test Type</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select js-select2" id="test-type"
                                                            name="test_type" data-placeholder="All Types">
                                                            <option value="">All Types</option>
                                                            @foreach ($testTypes as $type)
                                                                <option value="{{ $type }}"
                                                                    {{ request('test_type') == $type ? 'selected' : '' }}>
                                                                    {{ ucfirst($type) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="status">Status</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select" id="status" name="status">
                                                            <option value="">All Status</option>
                                                            <option value="draft"
                                                                {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                                            </option>
                                                            <option value="finalized"
                                                                {{ request('status') == 'finalized' ? 'selected' : '' }}>
                                                                Finalized</option>
                                                            <option value="revised"
                                                                {{ request('status') == 'revised' ? 'selected' : '' }}>
                                                                Revised</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="date-from">Date From</label>
                                                    <div class="form-control-wrap">
                                                        <input type="date" class="form-control" id="date-from"
                                                            name="date_from" value="{{ request('date_from') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="date-to">Date To</label>
                                                    <div class="form-control-wrap">
                                                        <input type="date" class="form-control" id="date-to"
                                                            name="date_to" value="{{ request('date_to') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label" for="search">Search</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="search"
                                                            name="search" placeholder="Test name, Patient ID..."
                                                            value="{{ request('search') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div class="form-control-wrap">
                                                        <button type="submit" class="btn btn-primary w-100"><em
                                                                class="icon ni ni-search"></em></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="nk-block">
                        <div class="card card-bordered card-stretch">
                            <div class="card-inner-group">
                                @if ($testResults->count() > 0)
                                    <div class="card-inner p-0">
                                        <div class="nk-tb-list nk-tb-ulist">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col nk-tb-col-check">
                                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="uid">
                                                        <label class="custom-control-label" for="uid"></label>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col"><span class="sub-text">Test Details</span></div>
                                                <div class="nk-tb-col tb-col-md"><span class="sub-text">Patient ID</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-lg"><span class="sub-text">Test Date</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md"><span class="sub-text">Version</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md"><span class="sub-text">Created By</span>
                                                </div>
                                                <div class="nk-tb-col nk-tb-col-tools text-end">
                                                </div>
                                            </div>

                                            @foreach ($testResults as $result)
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col nk-tb-col-check">
                                                        <div
                                                            class="custom-control custom-control-sm custom-checkbox notext">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="uid{{ $result->tr07_test_result_id }}">
                                                            <label class="custom-control-label"
                                                                for="uid{{ $result->tr07_test_result_id }}"></label>
                                                        </div>
                                                    </div>
                                                    <div class="nk-tb-col">
                                                        <div class="user-card">
                                                            <div class="user-info">
                                                                <span class="tb-lead">{{ $result->tr07_test_name }}</span>
                                                                <span
                                                                    class="fs-12px text-soft">{{ ucfirst($result->tr07_test_type) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        <span class="tb-amount">{{ $result->tr07_patient_id }}</span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        <span
                                                            class="tb-amount">{{ $result->tr07_test_date->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        <span
                                                            class="badge badge-sm badge-outline-primary">v{{ $result->tr07_current_version }}</span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        @if ($result->tr07_status == 'draft')
                                                            <span
                                                                class="badge badge-sm badge-dot has-bg bg-warning d-none d-sm-inline-flex">Draft</span>
                                                        @elseif($result->tr07_status == 'finalized')
                                                            <span
                                                                class="badge badge-sm badge-dot has-bg bg-success d-none d-sm-inline-flex">Finalized</span>
                                                        @else
                                                            <span
                                                                class="badge badge-sm badge-dot has-bg bg-info d-none d-sm-inline-flex">Revised</span>
                                                        @endif
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        <span class="fs-12px">{{ $result->creator->name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="nk-tb-col nk-tb-col-tools">
                                                        <ul class="nk-tb-actions gx-1">
                                                            <li class="nk-tb-action-hidden">
                                                                <a href="{{ route('test-results.show', $result->tr07_test_result_id) }}"
                                                                    class="btn btn-trigger btn-icon"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="View">
                                                                    <em class="icon ni ni-eye"></em>
                                                                </a>
                                                            </li>
                                                            @if ($result->tr07_status != 'finalized')
                                                                <li class="nk-tb-action-hidden">
                                                                    <a href="{{ route('test-results.edit', $result->tr07_test_result_id) }}"
                                                                        class="btn btn-trigger btn-icon"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="Edit">
                                                                        <em class="icon ni ni-edit"></em>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <div class="drodown">
                                                                    <a href="#"
                                                                        class="dropdown-toggle btn btn-icon btn-trigger"
                                                                        data-bs-toggle="dropdown"><em
                                                                            class="icon ni ni-more-h"></em></a>
                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <ul class="link-list-opt no-bdr">
                                                                            <li><a
                                                                                    href="{{ route('test-results.show', $result->tr07_test_result_id) }}"><em
                                                                                        class="icon ni ni-eye"></em><span>View
                                                                                        Details</span></a></li>
                                                                            <li><a
                                                                                    href="{{ route('test-results.generate-report', $result->tr07_test_result_id) }}"><em
                                                                                        class="icon ni ni-file-docs"></em><span>Generate
                                                                                        Report</span></a></li>
                                                                            @if ($result->tr07_status != 'finalized')
                                                                                <li><a
                                                                                        href="{{ route('test-results.edit', $result->tr07_test_result_id) }}"><em
                                                                                            class="icon ni ni-edit"></em><span>Edit</span></a>
                                                                                </li>
                                                                                <li class="divider"></li>
                                                                                {{-- <li><a href="#"
                                                                                        onclick="deleteResult({{ $result->tr07_test_result_id }})"
                                                                                        class="text-danger"><em
                                                                                            class="icon ni ni-trash"></em><span>Delete</span></a>
                                                                                </li> --}}
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card-inner">
                                        <div class="nk-block-between-md g-3">
                                            <div class="g">
                                                {{ $testResults->appends(request()->query())->links() }}
                                            </div>
                                            <div class="g">
                                                <div
                                                    class="pagination-goto d-flex justify-content-center justify-content-md-start gx-3">
                                                    <div>Page</div>
                                                    <div>
                                                        <select class="form-select js-select2" data-search="on"
                                                            data-dropdown="xs center"
                                                            onchange="window.location.href='{{ route('test_results') }}?page=' + this.value + '&{{ http_build_query(request()->except('page')) }}'">
                                                            @for ($i = 1; $i <= $testResults->lastPage(); $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ $testResults->currentPage() == $i ? 'selected' : '' }}>
                                                                    {{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div>of {{ $testResults->lastPage() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card-inner">
                                        <div class="text-center py-4">
                                            <div class="icon-circle icon-circle-lg bg-primary-dim mb-3">
                                                <em class="icon ni ni-activity text-primary"></em>
                                            </div>
                                            <h5>No Test Results Found</h5>
                                            <p class="text-soft">No test results match your search criteria. Try adjusting
                                                your filters or create a new test result.</p>
                                            <a href="{{ route('create_test_result') }}" class="btn btn-primary">Add
                                                First Test Result</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this test result? This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // function deleteResult(id) {
            // $('#deleteForm').attr('action', '{{ route('destroy', '') }}/' + id);
        //     $('#deleteModal').modal('show');
        // }

        // Auto-submit form on filter change
        // $(document).ready(function() {
        //     $('#test-type, #status').change(function() {
        //         this.form.submit();
        //     });
        // });
    </script>
@endsection
