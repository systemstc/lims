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
                                    <p>Manage and track test results</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                        data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            {{-- <li><a href="{{ route('create_test_result') }}" class="btn btn-primary"><em
                                                        class="icon ni ni-plus"></em><span>Add Test Result</span></a></li> --}}
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
                                        <h5>Filter</h5>
                                        <div class="row g-3 align-center">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label" for="status">Status</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select" id="status" name="status">
                                                            <option value="">All Status</option>
                                                            <option value="DRAFT"
                                                                {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft
                                                            </option>
                                                            <option value="SUBMITTED"
                                                                {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>
                                                                Submitted</option>
                                                            <option value="REVISED"
                                                                {{ request('status') == 'REVISED' ? 'selected' : '' }}>
                                                                Revised</option>
                                                            <option value="REJECTED"
                                                                {{ request('status') == 'REJECTED' ? 'selected' : '' }}>
                                                                Rejected</option>
                                                            <option value="VERIFIED"
                                                                {{ request('status') == 'VERIFIED' ? 'selected' : '' }}>
                                                                Verified</option>
                                                            <option value="AUTHORIZED"
                                                                {{ request('status') == 'AUTHORIZED' ? 'selected' : '' }}>
                                                                Authorized</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label" for="date-from">Date From</label>
                                                    <div class="form-control-wrap">
                                                        <input type="date" class="form-control" id="date-from"
                                                            name="date_from" value="{{ request('date_from') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label" for="date-to">Date To</label>
                                                    <div class="form-control-wrap">
                                                        <input type="date" class="form-control" id="date-to"
                                                            name="date_to" value="{{ request('date_to') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label" for="search">Search</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="search"
                                                            name="search" placeholder="Registration Number..."
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
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            @if ($testResults->count() > 0)
                                <table class="datatable-init-export nowrap table" data-export-title="Grouped Test Results">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Reference ID</th>
                                            <th>Statuses</th>
                                            <th>Last Test Date</th>
                                            <th>Total Tests</th>
                                            <th>Last Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($testResults as $key => $result)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $result->tr04_reference_id }}</td>
                                                <td>
                                                    @php
                                                        $statuses = explode(',', $result->statuses);
                                                    @endphp
                                                    @foreach ($statuses as $status)
                                                        @php
                                                            $color = match (strtoupper(trim($status))) {
                                                                'DRAFT' => 'warning',
                                                                'SUBMITTED' => 'info',
                                                                'AUTHORIZED' => 'success',
                                                                'REJECTED' => 'danger',
                                                                'VERIFIED' => 'primary',
                                                                default => 'secondary',
                                                            };
                                                        @endphp
                                                        <strong
                                                            class="text-{{ $color }} me-1">
                                                            {{ ucfirst(trim($status)) }}
                                                        </strong>
                                                    @endforeach
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($result->last_test_date)->format('d M, Y') }}
                                                </td>
                                                <td>{{ $result->total_tests }}</td>
                                                <td>{{ \Carbon\Carbon::parse($result->last_created_at)->format('d M, Y h:i A') }}
                                                </td>
                                                <td class="nk-tb-col nk-tb-col-tools text-center">
                                                    <ul class="nk-tb-actions gx-1 my-n1">
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown">
                                                                    <em class="icon ni ni-more-h"></em>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li>
                                                                            <a
                                                                                href="{{ route('show_sample_result', $result->tr04_reference_id) }}">
                                                                                <em class="icon ni ni-eye"></em>
                                                                                <span>View Details</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a
                                                                                href="#">
                                                                                <em class="icon ni ni-file-docs"></em>
                                                                                <span>Generate Report</span>
                                                                            </a>
                                                                            {{-- <a
                                                                                href="{{ route('test_results.report', $result->tr04_reference_id) }}">
                                                                                <em class="icon ni ni-file-docs"></em>
                                                                                <span>Generate Report</span>
                                                                            </a> --}}
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-4">
                                    <div class="icon-circle icon-circle-lg bg-primary-dim mb-3">
                                        <em class="icon ni ni-activity text-primary"></em>
                                    </div>
                                    <h5>No Test Results Found</h5>
                                    <p class="text-soft mb-3">
                                        No grouped results match your filters. Try adjusting your search or create a new
                                        result.
                                    </p>
                                    <a href="{{ route('create_test_result') }}" class="btn btn-primary">Add Test
                                        Result</a>
                                </div>
                            @endif
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
