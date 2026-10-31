@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Audit Trail</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Complete audit trail of all test result changes and activities</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <div class="toggle-expand-content">
                                        <ul class="nk-block-tools g-3">
                                            <li><a href="{{ route('test_results') }}" class="btn btn-outline-light"><em
                                                        class="icon ni ni-arrow-left"></em><span>Back to Results</span></a>
                                            </li>
                                            <li>
                                                <button class="btn btn-outline-primary" onclick="exportAuditLog()">
                                                    <em class="icon ni ni-download"></em><span>Export</span>
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
                                    <form method="GET" action="{{ route('test_results_audit') }}">
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
                                                            <option value="CREATED"
                                                                {{ strtoupper(request('action')) == 'CREATED' ? 'selected' : '' }}>
                                                                Initial Entry (Created)</option>
                                                            <option value="UPDATED"
                                                                {{ strtoupper(request('action')) == 'UPDATED' ? 'selected' : '' }}>
                                                                Updated / Revised</option>
                                                            <option value="VERIFIED"
                                                                {{ strtoupper(request('action')) == 'VERIFIED' ? 'selected' : '' }}>
                                                                Verified (QA)</option>
                                                            <option value="REPORTED"
                                                                {{ strtoupper(request('action')) == 'REPORTED' ? 'selected' : '' }}>
                                                                Reported / Released</option>
                                                            <option value="REJECTED"
                                                                {{ strtoupper(request('action')) == 'REJECTED' ? 'selected' : '' }}>
                                                                Sent Back (Clarification)</option>
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
                                                                <option value="{{ $emp->m06_employee_id }}"
                                                                    {{ request('user_id') == $emp->m06_employee_id ? 'selected' : '' }}>
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
                                                    <div class="form-control-wrap d-flex gap-2 justify-content-end">
                                                        <button type="submit" class="btn btn-primary" title="Search Audit Logs">
                                                            <em class="icon ni ni-search"></em>
                                                        </button>
                                                        <a href="{{ route('test_results_audit') }}"
                                                            class="btn btn-outline-secondary" title="Reset Filters">
                                                            <em class="icon ni ni-reload"></em>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Log Table -->
                    <div class="nk-block">
                        <div class="card card-bordered card-stretch">
                            <div class="card-inner-group">
                                @if ($auditLogs->count() > 0)
                                    <div class="card-inner p-0">
                                        <div class="nk-tb-list">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span class="sub-text">Date/Time</span></div>
                                                <div class="nk-tb-col"><span class="sub-text">Test Result</span></div>
                                                <div class="nk-tb-col tb-col-md"><span class="sub-text">Action</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-lg"><span class="sub-text">User</span></div>
                                                <div class="nk-tb-col tb-col-lg"><span class="sub-text">Version</span>
                                                </div>
                                                <div class="nk-tb-col"><span class="sub-text">Details</span></div>
                                                <div class="nk-tb-col tb-col-md"><span class="sub-text">IP Address</span>
                                                </div>
                                            </div>

                                            @foreach ($auditLogs as $log)
                                                @php
                                                    $logCreatedAt = $log->tr10_created_at ?? $log->tr07_created_at;
                                                    $logCarbon = $logCreatedAt ? (\Carbon\Carbon::parse($logCreatedAt)) : null;
                                                    $actionStr = strtolower($log->tr10_action ?? $log->tr07_action ?? 'action');
                                                    $actionColor = $actionColors[$actionStr] ?? 'secondary';
                                                    $fieldChanged = $log->tr10_field_changed ?? $log->tr07_field_changed;
                                                    $changeReason = $log->tr10_change_reason ?? $log->tr07_change_reason;
                                                    $oldVal = $log->tr10_old_value ?? $log->tr07_old_value;
                                                    $newVal = $log->tr10_new_value ?? $log->tr07_new_value;
                                                    $ipAddr = $log->tr10_ip_address ?? $log->tr07_ip_address ?? 'N/A';
                                                    $userId = $log->tr10_user_id ?? $log->tr07_user_id;
                                                    $userName = $log->user?->m06_name ?? $log->user?->name ?? ($userId ? "User #{$userId}" : "System");
                                                @endphp
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col">
                                                        <div class="user-card">
                                                            <div class="user-info">
                                                                <span
                                                                    class="tb-lead">{{ $logCarbon ? $logCarbon->format('M d, Y') : 'N/A' }}</span>
                                                                <span
                                                                    class="fs-12px text-soft">{{ $logCarbon ? $logCarbon->format('H:i:s') : '' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-tb-col">
                                                        @if ($log->testResult)
                                                            <div class="user-card">
                                                                <div class="user-info">
                                                                    @if ($log->testResult->tr04_reference_id)
                                                                        <a href="{{ route('sample_history', ['identifier' => $log->testResult->tr04_reference_id]) }}"
                                                                            class="badge bg-primary text-white mb-1" target="_blank"
                                                                            title="View Complete Sample History Track">
                                                                            <em class="icon ni ni-route me-1"></em>#{{ $log->testResult->tr04_reference_id }}
                                                                        </a>
                                                                    @endif
                                                                    <div class="tb-lead text-dark fw-bold">
                                                                        {{ $log->testResult->tr07_test_name }}
                                                                    </div>
                                                                    <span class="fs-11px text-soft">Result ID: #{{ $log->tr07_test_result_id }}</span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-danger">Test Result #{{ $log->tr07_test_result_id }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        @php
                                                            $actionColors = [
                                                                'created' => 'success',
                                                                'updated' => 'info',
                                                                'verified' => 'primary',
                                                                'reported' => 'dark',
                                                                'finalized' => 'dark',
                                                                'revised' => 'warning',
                                                                'rejected' => 'danger',
                                                                'deleted' => 'danger',
                                                            ];
                                                            $actionColor =
                                                                $actionColors[$actionStr] ?? 'secondary';
                                                        @endphp
                                                        <span
                                                            class="badge badge-sm badge-dot has-bg bg-{{ $actionColor }} d-none d-sm-inline-flex">
                                                            {{ ucfirst($actionStr) }}
                                                        </span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        <div class="user-card">
                                                            <div class="user-info">
                                                                <span
                                                                    class="tb-lead">{{ $userName }}</span>
                                                                <span class="fs-12px text-soft">ID:
                                                                    {{ $userId ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        @if ($log->version)
                                                            <span
                                                                class="badge badge-sm badge-outline-primary">v{{ $log->version->tr07_version_number }}</span>
                                                        @else
                                                            <span class="text-soft">-</span>
                                                        @endif
                                                    </div>
                                                    <div class="nk-tb-col">
                                                        <div class="tb-lead-sub">
                                                            @if ($fieldChanged)
                                                                <span
                                                                    class="text-primary">{{ $fieldChanged }}</span>
                                                            @endif

                                                            @if ($changeReason)
                                                                <div class="fs-12px text-soft mt-1"
                                                                    title="{{ $changeReason }}">
                                                                    {{ Str::limit($changeReason, 50) }}
                                                                </div>
                                                            @endif

                                                            @if ($oldVal || $newVal)
                                                                <div class="fs-11px mt-1">
                                                                    @if ($oldVal)
                                                                        <span class="text-danger">From:
                                                                            {{ Str::limit($oldVal, 20) }}</span><br>
                                                                    @endif
                                                                    @if ($newVal)
                                                                        <span class="text-success">To:
                                                                            {{ Str::limit($newVal, 20) }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        <span
                                                            class="fs-12px text-soft">{{ $ipAddr }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="card-inner">
                                        <div class="nk-block-between-md g-3">
                                            <div class="g">
                                                {{ $auditLogs->appends(request()->query())->links() }}
                                            </div>
                                            <div class="g">
                                                <div
                                                    class="pagination-goto d-flex justify-content-center justify-content-md-start gx-3">
                                                    <div>Page</div>
                                                    <div>
                                                        <select class="form-select form-select-sm js-select2"
                                                            data-search="on" data-dropdown="xs center"
                                                            onchange="window.location.href='{{ route('test-results.audit') }}?page=' + this.value + '&{{ http_build_query(request()->except('page')) }}'">
                                                            @for ($i = 1; $i <= $auditLogs->lastPage(); $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ $auditLogs->currentPage() == $i ? 'selected' : '' }}>
                                                                    {{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div>of {{ $auditLogs->lastPage() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card-inner">
                                        <div class="text-center py-4">
                                            <div class="icon-circle icon-circle-lg bg-info-dim mb-3">
                                                <em class="icon ni ni-eye text-info"></em>
                                            </div>
                                            <h5>No Audit Logs Found</h5>
                                            <p class="text-soft">No audit logs match your search criteria. Try adjusting
                                                your filters.</p>
                                        </div>
                                    </div>
                                @endif
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
                                            <span class="amount">{{ $totalActions ?? $auditLogs->total() }}</span>
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
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="auditDetailModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="auditDetailContent">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        function exportAuditLog() {
            // Get current filter parameters
            const params = new URLSearchParams(window.location.search);
            params.append('export', '1');

            // Create export URL
            const exportUrl = '{{ route('test-results.audit') }}?' + params.toString();

            // Open in new tab or download
            window.open(exportUrl, '_blank');
        }

        function viewAuditDetail(auditId) {
            $('#auditDetailContent').html(
                '<div class="text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Loading...</p></div>'
            );
            $('#auditDetailModal').modal('show');

            // Load audit detail via AJAX
            $.get(`/audit/${auditId}/detail`)
                .done(function(data) {
                    $('#auditDetailContent').html(data);
                })
                .fail(function() {
                    $('#auditDetailContent').html(
                        '<div class="alert alert-danger">Failed to load audit details.</div>');
                });
        }

        // Auto-refresh every 30 seconds if on current page
        setInterval(function() {
            if (document.hasFocus()) {
                // Only refresh if no filters are applied or if showing recent data
                const hasFilters =
                    '{{ request()->hasAny(['test_result_id', 'action', 'user_id', 'date_from', 'date_to']) }}';
                if (!hasFilters) {
                    // Silently check for new records
                    $.get('{{ route('test-results.audit') }}?ajax=1')
                        .done(function(data) {
                            if (data.hasNewRecords) {
                                // Show notification
                                NioApp.Toast('New audit logs available. Refresh to view.', 'info', {
                                    position: 'top-right'
                                });
                            }
                        });
                }
            }
        }, 30000);
    </script>
@endsection
