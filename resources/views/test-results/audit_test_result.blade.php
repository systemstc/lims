@extends('layouts.app_back')

@push('styles')
    <style>
        #audit-trail-table_wrapper .dataTables_processing {
            z-index: 1050;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 8px;
        }
        #audit-trail-table td {
            vertical-align: middle;
        }
        .user-card .user-info .tb-lead {
            font-size: 0.875rem;
            font-weight: 600;
        }
        .badge.bg-primary {
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Audit Trail</h3>
                            <div class="nk-block-des text-soft">
                                <p>Complete audit trail of all test result changes, verification, and activities (Powered by Yajra DataTables)</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <div class="toggle-expand-content">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <a href="{{ route('test_results') }}" class="btn btn-outline-light">
                                                <em class="icon ni ni-arrow-left"></em><span>Back to Results</span>
                                            </a>
                                        </li>
                                        <li>
                                            <button class="btn btn-outline-primary" onclick="exportAuditLog()">
                                                <em class="icon ni ni-download"></em><span>Export CSV</span>
                                            </button>
                                        </li>
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
                                <form id="audit-filter-form" onsubmit="return false;">
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-3 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="search-input">Sample Ref / Test / ID</label>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-left">
                                                        <em class="icon ni ni-search"></em>
                                                    </div>
                                                    <input type="text" class="form-control" id="search-input"
                                                        name="search" value="{{ request('search', request('test_result_id')) }}"
                                                        placeholder="Ref ID, Tracker, or Test...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="action">Action</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select" id="action" name="action">
                                                        <option value="">All Actions</option>
                                                        <option value="CREATED">Initial Entry (Created)</option>
                                                        <option value="UPDATED">Updated / Revised</option>
                                                        <option value="VERIFIED">Verified (QA)</option>
                                                        <option value="REPORTED">Reported / Released</option>
                                                        <option value="REJECTED">Sent Back (Clarification)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="user-id">User / Analyst</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select" id="user-id" name="user_id">
                                                        <option value="">All Users</option>
                                                        @foreach ($employees ?? [] as $emp)
                                                            <option value="{{ $emp->m06_employee_id }}">
                                                                {{ $emp->m06_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="date-from">Date From</label>
                                                <div class="form-control-wrap">
                                                    <input type="date" class="form-control" id="date-from"
                                                        name="date_from" value="{{ request('date_from') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="date-to">Date To</label>
                                                <div class="form-control-wrap">
                                                    <input type="date" class="form-control" id="date-to"
                                                        name="date_to" value="{{ request('date_to') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-md-12 text-end">
                                            <div class="form-group">
                                                <label class="form-label d-none d-lg-block">&nbsp;</label>
                                                <div class="form-control-wrap d-flex gap-1 justify-content-end flex-nowrap">
                                                    <button type="button" id="filter-submit-btn" class="btn btn-icon btn-primary flex-shrink-0" title="Search Audit Logs">
                                                        <em class="icon ni ni-search"></em>
                                                    </button>
                                                    <button type="button" id="reset-filters-btn" class="btn btn-icon btn-outline-secondary flex-shrink-0" title="Reset Filters">
                                                        <em class="icon ni ni-reload"></em>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Log Table (Yajra DataTables) -->
                <div class="nk-block">
                    <div class="card card-bordered card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-3">
                                <table class="table table-bordered table-striped nowrap align-middle" id="audit-trail-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Date / Time</th>
                                            <th>Test Result</th>
                                            <th>Action</th>
                                            <th>User / Analyst</th>
                                            <th>Version</th>
                                            <th>Details & Diff</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Populated asynchronously by Yajra DataTables --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Total Actions</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="icon ni ni-activity text-primary"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ $totalActions ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Today's Activity</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="icon ni ni-calendar text-info"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ $todayActivity ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">This Week</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="icon ni ni-bar-chart text-success"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ $thisWeek ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">This Month</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="icon ni ni-growth text-warning"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ $thisMonth ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var auditTable = $('#audit-trail-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('test_results_audit') }}",
                    type: 'GET',
                    data: function(d) {
                        d.search_query = $('#search-input').val();
                        d.action = $('#action').val();
                        d.user_id = $('#user-id').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                    }
                },
                columns: [
                    { data: 'created_at_formatted', name: 'tr10_created_at' },
                    { data: 'test_result_info', name: 'tr07_test_result_id' },
                    { data: 'action_badge', name: 'tr10_action' },
                    { data: 'user_info', name: 'user.m06_name' },
                    { data: 'version_badge', name: 'tr09_version_id', searchable: false },
                    { data: 'details', name: 'tr10_field_changed' },
                    { data: 'ip_address', name: 'tr10_ip_address' }
                ],
                order: [[0, 'desc']],
                pageLength: 20,
                lengthMenu: [
                    [10, 20, 50, 100],
                    [10, 20, 50, 100]
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: "No audit logs found",
                    zeroRecords: "No matching audit logs found"
                }
            });

            // Live filter triggers
            $('#action, #user-id, #date-from, #date-to').on('change', function() {
                auditTable.draw();
            });

            // Filter button trigger
            $('#filter-submit-btn').on('click', function() {
                auditTable.draw();
            });

            // Debounced typing search
            var searchTimer;
            $('#search-input').on('keyup input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    auditTable.draw();
                }, 350);
            });

            // Reset filters
            $('#reset-filters-btn').on('click', function(e) {
                e.preventDefault();
                $('#search-input').val('');
                $('#action').val('').trigger('change');
                $('#user-id').val('').trigger('change');
                $('#date-from').val('');
                $('#date-to').val('');
                auditTable.draw();
            });
        });

        function exportAuditLog() {
            var params = new URLSearchParams();
            params.append('export', '1');
            var s = $('#search-input').val();
            if (s) params.append('search', s);
            var a = $('#action').val();
            if (a) params.append('action', a);
            var u = $('#user-id').val();
            if (u) params.append('user_id', u);
            var df = $('#date-from').val();
            if (df) params.append('date_from', df);
            var dt = $('#date-to').val();
            if (dt) params.append('date_to', dt);

            var exportUrl = "{{ route('test_results_audit') }}?" + params.toString();
            window.open(exportUrl, '_blank');
        }
    </script>
@endpush
