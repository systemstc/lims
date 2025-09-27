{{-- Enhanced Lab Manager Pending Allotments Dashboard --}}
@extends('layouts.app_back')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">Pending Allotments</h4>
                <p>New samples received from registrations that need test allotment or transfer.</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-gs mb-4">
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">New Samples</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples just received from registration"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['new_samples'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Pending Tests</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Tests waiting for allotment"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['pending_tests'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Partially Allotted</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples with some tests allotted"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['partial_allotted'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Ready for Testing</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Tests fully allotted and ready"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['ready_for_testing'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Bulk Actions</label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" onclick="openBulkAllotModal()"
                                    id="bulkAllotBtn" disabled>
                                    <em class="icon ni ni-user-check"></em>
                                    Allot Selected
                                </button>
                                <button type="button" class="btn btn-warning" onclick="openBulkTransferModal()"
                                    id="bulkTransferBtn" disabled>
                                    <em class="icon ni ni-forward-arrow"></em>
                                    Transfer Selected
                                </button>
                            </div>
                            <small class="text-muted">Select samples below to enable bulk actions</small>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="form-group">
                            <label class="form-label">Selected Samples</label>
                            <div>
                                <span class="badge badge-primary" id="selectedCount">0</span> samples selected
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                    onclick="clearSelection()">Clear All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Select test to allot from all samples to a particular analyst --}}
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Select Test for Allotment</label>
                            <select id="testSelectionDropdown" class="form-select js-select2" data-search="on">
                                <option value="">Choose Test...</option>
                                @foreach ($availableTests as $test)
                                    <option value="{{ $test->m12_test_id }}">
                                        {{ $test->m12_name }} ({{ $test->test_count }} samples)
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select from available tests with pending samples</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="testSelectionStats" class="text-end" style="display: none;">
                            <span class="badge bg-info" id="selectedTestName">No test selected</span>
                            <span class="badge bg-warning" id="samplesAvailable">0</span> Samples available
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Samples Modal -->
        <div class="modal fade" tabindex="-1" id="testSamplesModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Test Samples for Allotment</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div id="testSamplesResults">
                            <!-- Results will be populated here -->
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Select Analyst for Allotment</label>
                            <select id="testAllotmentAnalyst" class="form-control form-select">
                                <option value="">Choose Analyst...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->m06_employee_id }}">
                                        {{ $employee->m06_name }} ({{ $employee->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-primary" onclick="allotSelectedTests()" id="allotTestsBtn"
                            disabled>
                            Allot Selected Tests
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <form method="GET" action="{{ route('view_allottment') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-control form-select">
                                <option value="">All Priorities</option>
                                <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent
                                </option>
                                <option value="Normal" {{ request('priority') == 'Normal' ? 'selected' : '' }}>Normal
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control form-select">
                                <option value="">All Status</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial
                                </option>
                                <option value="urgent" {{ request('status') == 'urgent' ? 'selected' : '' }}>Urgent (3+
                                    days)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Days Pending</label>
                            <select name="days_pending" class="form-control form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('days_pending') == '1' ? 'selected' : '' }}>1+ days
                                </option>
                                <option value="3" {{ request('days_pending') == '3' ? 'selected' : '' }}>3+ days
                                </option>
                                <option value="7" {{ request('days_pending') == '7' ? 'selected' : '' }}>7+ days
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="btn-group d-flex">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('view_allottment') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pending Samples Table -->
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner p-0">
                <div class="nk-tb-list nk-tb-ulist">
                    <div class="nk-tb-item nk-tb-head">
                        <div class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                <input type="checkbox" class="custom-control-input" id="selectAllSamples"
                                    onchange="toggleSelectAll()">
                                <label class="custom-control-label" for="selectAllSamples"></label>
                            </div>
                        </div>
                        <div class="nk-tb-col"><span class="sub-text">Registration ID</span></div>
                        <div class="nk-tb-col tb-col-lg"><span class="sub-text">Received Date</span></div>
                        <div class="nk-tb-col tb-col-md"><span class="sub-text">Test Progress</span></div>
                        <div class="nk-tb-col tb-col-md"><span class="sub-text">Priority</span></div>
                        <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                        <div class="nk-tb-col tb-col-md"><span class="sub-text">Days Pending</span></div>
                        <div class="nk-tb-col nk-tb-col-tools text-end">Actions</div>
                    </div>

                    @forelse($pendingRegistrations as $registration)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input sample-checkbox"
                                        id="sample-{{ $registration->tr04_sample_registration_id }}"
                                        value="{{ $registration->tr04_sample_registration_id }}"
                                        onchange="updateBulkActions()">
                                    <label class="custom-control-label"
                                        for="sample-{{ $registration->tr04_sample_registration_id }}"></label>
                                </div>
                            </div>

                            <div class="nk-tb-col">
                                <div class="user-card">
                                    <div class="user-info">
                                        <span class="tb-lead">{{ $registration->tr04_reference_id }}</span>
                                        @if ($registration->received_tests > 0)
                                            <span class="badge bg-info ms-2">
                                                <em class="icon ni ni-arrow-down"></em>
                                                {{ $registration->received_tests }}
                                            </span>
                                        @endif
                                        @if ($registration->transferred_tests > 0)
                                            <span class="badge bg-warning ms-2">
                                                <em class="icon ni ni-arrow-up"></em>
                                                {{ $registration->transferred_tests }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="nk-tb-col tb-col-lg">
                                <span class="tb-lead">{{ $registration->created_at->format('d M Y') }}</span>
                                <span class="tb-sub">{{ $registration->created_at->format('h:i A') }}</span>
                            </div>

                            <div class="nk-tb-col tb-col-md">
                                <div class="progress-wrap">
                                    <div class="progress-text">
                                        {{ $registration->allotted_tests }}/{{ $registration->total_tests }}
                                    </div>
                                    <div class="progress progress-md">
                                        @php
                                            $percentage =
                                                $registration->total_tests > 0
                                                    ? ($registration->allotted_tests / $registration->total_tests) * 100
                                                    : 0;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="nk-tb-col tb-col-md">
                                @php
                                    $priority = $registration->tr04_sample_type ?? 'Normal';
                                    $priorityClass = match (strtolower($priority)) {
                                        'urgent' => 'bg-danger',
                                        'normal' => 'bg-info',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $priorityClass }}">{{ ucfirst($priority) }}</span>
                            </div>

                            <div class="nk-tb-col tb-col-md">
                                @php
                                    $allottedPercentage =
                                        $registration->total_tests > 0
                                            ? ($registration->allotted_tests / $registration->total_tests) * 100
                                            : 0;
                                    [$status, $statusClass] = match (true) {
                                        $allottedPercentage == 0 => ['New', 'bg-warning'],
                                        $allottedPercentage == 100 => ['Complete', 'bg-success'],
                                        default => ['Partial', 'bg-info'],
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $status }}</span>
                            </div>

                            <div class="nk-tb-col tb-col-md">
                                @php
                                    $daysPending = $registration->created_at->floatDiffInDays(now());
                                    $daysPending = round($daysPending, 1);
                                    $urgencyClass = match (true) {
                                        $daysPending > 3 => 'text-danger fw-bold',
                                        $daysPending > 1 => 'text-warning',
                                        default => 'text-muted',
                                    };
                                @endphp
                                <span class="{{ $urgencyClass }}">
                                    {{ $daysPending }} days
                                    @if ($daysPending > 3)
                                        <em class="icon ni ni-alert-circle text-danger"></em>
                                    @endif
                                </span>
                            </div>

                            <div class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1">
                                    <li>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                data-bs-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a
                                                            href="{{ route('show_allotment', $registration->tr04_sample_registration_id) }}">
                                                            <em class="icon ni ni-user-check text-success"></em>
                                                            <span>Manage Allotment</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a style="cursor: pointer;"
                                                            onclick="quickAllot({{ $registration->tr04_sample_registration_id }})">
                                                            <em class="icon ni ni-spark text-primary"></em>
                                                            <span>Quick Allot</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a style="cursor: pointer;"
                                                            onclick="quickTransfer({{ $registration->tr04_sample_registration_id }})">
                                                            <em class="icon ni ni-forward-arrow text-warning"></em>
                                                            <span>Quick Transfer</span>
                                                        </a>
                                                    </li>
                                                    @if ($registration->received_tests > 0)
                                                        <li>
                                                            <a
                                                                href="{{ route('show_allotment', $registration->tr04_sample_registration_id) }}">
                                                                <em class="icon ni ni-check-circle text-success"></em>
                                                                <span>Accept Transfers</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @empty
                        <div class="nk-tb-item">
                            <div class="nk-tb-col text-center py-4" colspan="8">
                                <div class="text-muted">
                                    <em class="icon ni ni-inbox" style="font-size: 2rem;"></em>
                                    <p class="mt-2">No pending allotments found</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if ($pendingRegistrations->hasPages())
        <div class="card">
            <div class="card-inner d-flex justify-content-center">
                {{ $pendingRegistrations->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <!-- Bulk Allot Modal -->
    <div class="modal fade" tabindex="-1" id="bulkAllotModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Allot Samples</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <form id="bulkAllotForm" method="POST" action="{{ route('bulk_allot_sample') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="bulkAllotSampleIds" name="sample_ids">
                        <div class="form-group">
                            <label class="form-label">Select Analyst</label>
                            <select name="emp_id" class="form-control form-select" required>
                                <option value="">Choose Analyst...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->m06_employee_id }}">{{ $employee->m06_name }}
                                        ({{ $employee->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Selected Samples</label>
                            <div id="bulkAllotSampleList" class="border p-2 bg-light rounded">
                                <!-- Will be populated by JS -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-primary">Allot All Tests</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Transfer Modal -->
    <div class="modal fade" tabindex="-1" id="bulkTransferModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Transfer Samples</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <form id="bulkTransferForm" method="POST" action="{{ route('bulk_sample_transfer') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="bulkTransferSampleIds" name="sample_ids">
                        <div class="form-group">
                            <label class="form-label">Transfer to RO</label>
                            <select name="ro_id" class="form-control form-select" required>
                                <option value="">Choose RO...</option>
                                @foreach ($ros as $ro)
                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reason for Transfer</label>
                            <select name="reason" class="form-control form-select" required>
                                <option value="">Select Reason...</option>
                                <option value="Workload Distribution">Workload Distribution</option>
                                <option value="Equipment Not Available">Equipment Not Available</option>
                                <option value="Specialist Required">Specialist Required</option>
                                <option value="Capacity Issue">Capacity Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Selected Samples</label>
                            <div id="bulkTransferSampleList" class="border p-2 bg-light rounded">
                                <!-- Will be populated by JS -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-warning">Transfer All Tests</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Allot Modal -->
    <div class="modal fade" tabindex="-1" id="quickAllotModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Allot Sample</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <form id="quickAllotForm" method="POST" action="{{ route('quick_allot_sample') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="quickAllotSampleId" name="sample_id">
                        <div class="form-group">
                            <label class="form-label">Sample ID</label>
                            <input type="text" class="form-control" id="quickAllotSampleRef" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Select Analyst</label>
                            <select name="emp_id" class="form-control form-select" required>
                                <option value="">Choose Analyst...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->m06_employee_id }}">{{ $employee->m06_name }}
                                        ({{ $employee->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-primary">Allot All Tests</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Transfer Modal -->
    <div class="modal fade" tabindex="-1" id="quickTransferModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Transfer Sample</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <form id="quickTransferForm" method="POST" action="{{ route('quick_sample_transfer') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="quickTransferSampleId" name="sample_id">
                        <div class="form-group">
                            <label class="form-label">Sample ID</label>
                            <input type="text" class="form-control" id="quickTransferSampleRef" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Transfer to RO</label>
                            <select name="ro_id" class="form-control form-select" required>
                                <option value="">Choose RO...</option>
                                @foreach ($ros as $ro)
                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reason for Transfer</label>
                            <select name="reason" class="form-control form-select" required>
                                <option value="">Select Reason...</option>
                                <option value="Workload Distribution">Workload Distribution</option>
                                <option value="Equipment Not Available">Equipment Not Available</option>
                                <option value="Specialist Required">Specialist Required</option>
                                <option value="Capacity Issue">Capacity Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-warning">Transfer All Tests</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let selectedSamples = new Set();

            if (typeof $().tooltip === 'function') {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }

            function showFeedback(type, message, title = null) {
                if (typeof Swal !== 'undefined') {
                    const config = {
                        text: message,
                        icon: type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'success',
                        confirmButtonText: 'OK'
                    };
                    if (title) {
                        config.title = title;
                    }
                    Swal.fire(config);
                } else {
                    alert(message);
                }
            }

            // Toggle select all checkboxes
            window.toggleSelectAll = function() {
                const selectAllCheckbox = $('#selectAllSamples')[0];
                $('.sample-checkbox').prop('checked', selectAllCheckbox.checked).each(function() {
                    if (this.checked) {
                        selectedSamples.add(this.value);
                    } else {
                        selectedSamples.delete(this.value);
                    }
                });
                updateBulkActions();
            };

            // Handle individual checkbox selection
            window.updateBulkActions = function() {
                selectedSamples.clear();
                $('.sample-checkbox:checked').each(function() {
                    selectedSamples.add(this.value);
                });

                const count = selectedSamples.size;
                $('#selectedCount').text(count);
                $('#bulkAllotBtn, #bulkTransferBtn').prop('disabled', count === 0);

                // Handle select all checkbox state
                const totalCheckboxes = $('.sample-checkbox').length;
                const selectAllCheckbox = $('#selectAllSamples')[0];
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = count === totalCheckboxes;
                    selectAllCheckbox.indeterminate = count > 0 && count < totalCheckboxes;
                }
            };

            // Clear selection function
            window.clearSelection = function() {
                $('.sample-checkbox').prop('checked', false);
                $('#selectAllSamples').prop('checked', false).prop('indeterminate', false);
                selectedSamples.clear();
                updateBulkActions();
            };

            // Bulk modal functions
            window.openBulkAllotModal = function() {
                if (selectedSamples.size === 0) return;
                $('#bulkAllotSampleIds').val([...selectedSamples].join(','));
                populateSampleList('#bulkAllotSampleList', 'badge bg-primary me-1');
                new bootstrap.Modal($('#bulkAllotModal')[0]).show();
            };

            window.openBulkTransferModal = function() {
                if (selectedSamples.size === 0) return;
                $('#bulkTransferSampleIds').val([...selectedSamples].join(','));
                populateSampleList('#bulkTransferSampleList', 'badge bg-warning me-1');
                new bootstrap.Modal($('#bulkTransferModal')[0]).show();
            };

            function populateSampleList(containerSelector, badgeClass) {
                let sampleRefs = [];
                selectedSamples.forEach(id => {
                    const row = $(`input[value="${id}"]`).closest('.nk-tb-item');
                    const ref = row.find('.tb-lead').first().text().trim();
                    if (ref) sampleRefs.push(`<span class="${badgeClass}">${ref}</span>`);
                });
                $(containerSelector).html(sampleRefs.join('') || 'No samples selected');
            }

            // Quick action functions
            window.quickAllot = function(id) {
                const row = $(`input[value="${id}"]`).closest('.nk-tb-item');
                const refText = row.find('.tb-lead').first().text().trim();
                $('#quickAllotSampleId').val(id);
                $('#quickAllotSampleRef').val(refText || `Sample ${id}`);
                new bootstrap.Modal($('#quickAllotModal')[0]).show();
            };

            window.quickTransfer = function(id) {
                const row = $(`input[value="${id}"]`).closest('.nk-tb-item');
                const refText = row.find('.tb-lead').first().text().trim();
                $('#quickTransferSampleId').val(id);
                $('#quickTransferSampleRef').val(refText || `Sample ${id}`);
                new bootstrap.Modal($('#quickTransferModal')[0]).show();
            };

            // Form submissions with validation
            $('#bulkAllotForm').on('submit', function(e) {
                e.preventDefault();
                const empId = $(this).find('select[name="emp_id"]').val();
                if (!empId) {
                    showFeedback('error', 'Please select an analyst', 'Validation Error');
                    return;
                }
                if (selectedSamples.size === 0) {
                    showFeedback('error', 'No samples selected', 'Validation Error');
                    return;
                }
                this.submit();
            });

            $('#bulkTransferForm').on('submit', function(e) {
                e.preventDefault();
                const roId = $(this).find('select[name="ro_id"]').val();
                const reason = $(this).find('select[name="reason"]').val();
                if (!roId || !reason) {
                    showFeedback('error', 'Please fill in all required fields', 'Validation Error');
                    return;
                }
                if (selectedSamples.size === 0) {
                    showFeedback('error', 'No samples selected', 'Validation Error');
                    return;
                }
                this.submit();
            });

            $('#quickAllotForm').on('submit', function(e) {
                e.preventDefault();
                const empId = $(this).find('select[name="emp_id"]').val();
                if (!empId) {
                    showFeedback('error', 'Please select an analyst', 'Validation Error');
                    return;
                }
                this.submit();
            });

            $('#quickTransferForm').on('submit', function(e) {
                e.preventDefault();
                const roId = $(this).find('select[name="ro_id"]').val();
                const reason = $(this).find('select[name="reason"]').val();
                if (!roId || !reason) {
                    showFeedback('error', 'Please fill in all required fields', 'Validation Error');
                    return;
                }
                this.submit();
            });

            // Test selection dropdown change handler
            $('#testSelectionDropdown').on('change', function() {
                const testId = $(this).val();
                const testName = $(this).find('option:selected').text();

                if (testId) {
                    // Update stats display
                    $('#selectedTestName').text(testName.split(' (')[0]);
                    $('#testSelectionStats').show();

                    // Load test samples
                    loadTestSamples(testId);
                } else {
                    $('#testSelectionStats').hide();
                }
            });

            // Load test samples for selected test
            function loadTestSamples(testId) {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route('get_test_samples_allotment') }}', // You'll need to add this route
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        test_id: testId
                    }),
                    headers: csrfToken ? {
                        'X-CSRF-TOKEN': csrfToken
                    } : {},
                    success: function(data) {
                        if (data.success) {
                            displayTestSamples(data);
                            $('#samplesAvailable').text(data.samples.length);

                            // Show modal
                            new bootstrap.Modal($('#testSamplesModal')[0]).show();
                        } else {
                            showFeedback('warning', data.message || 'No samples found for this test',
                                'No Samples');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Load samples error:', error);
                        showFeedback('error',
                            'An error occurred while loading samples. Please try again.',
                            'Load Error');
                    }
                });
            }

            function displayTestSamples(data) {
                let resultsHTML = '';

                if (!data.samples || data.samples.length === 0) {
                    resultsHTML = `<div class="text-center py-4">
                            <p class="text-muted">No unallotted samples found for this test</p>
                        </div>`;
                } else {
                    resultsHTML = `
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Test:</strong> ${data.test_name}<br>
                                    <strong>Available Samples:</strong> ${data.samples.length}
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAllSamples">
                                    <label class="form-check-label" for="selectAllSamples">Select All</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">`;

                    $.each(data.samples, function(index, sample) {
                        const regDate = sample.created_at ?
                            new Date(sample.created_at).toLocaleDateString() : 'Unknown';

                        const referenceId = sample.registration ?
                            sample.registration.tr04_reference_id : 'Unknown Reference';

                        resultsHTML += `<div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input sample-checkbox" 
                                                value="${sample.tr05_sample_test_id}">
                                            <label class="form-check-label">
                                                <div>
                                                    <strong>${referenceId}</strong><br>
                                                    <small class="text-muted">Registered: ${regDate}</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                    resultsHTML += `</div>`;
                }
                $('#testSamplesResults').html(resultsHTML);

                // Reset analyst selection and button state
                $('#testAllotmentAnalyst').val('');
                updateTestSelection();
            }

            // Select all samples handler
            $(document).on('change', '#selectAllSamples', function() {
                $('.sample-checkbox').prop('checked', this.checked);
                updateTestSelection();
            });

            // Sample selection handlers
            $(document).on('change', '.sample-checkbox', function() {
                updateTestSelection();

                // Update "Select All" checkbox state
                const totalSamples = $('.sample-checkbox').length;
                const checkedSamples = $('.sample-checkbox:checked').length;

                $('#selectAllSamples').prop('indeterminate', checkedSamples > 0 && checkedSamples <
                    totalSamples);
                $('#selectAllSamples').prop('checked', checkedSamples === totalSamples);
            });

            function updateTestSelection() {
                const count = $('.sample-checkbox:checked').length;
                const analystSelected = $('#testAllotmentAnalyst').val();
                $('#allotTestsBtn').prop('disabled', count === 0 || !analystSelected);
            }

            $('#testAllotmentAnalyst').on('change', updateTestSelection);
            window.allotSelectedTests = function() {
                const selectedSamples = $('.sample-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                const analystId = $('#testAllotmentAnalyst').val();
                const selectedTestName = $('#testSelectionDropdown option:selected').text().split(' (')[0];

                if (!selectedSamples.length) {
                    showFeedback('warning', 'Please select at least one sample', 'Selection Required');
                    return;
                }
                if (!analystId) {
                    showFeedback('warning', 'Please select an analyst', 'Selection Required');
                    return;
                }

                const $btn = $('#allotTestsBtn');
                const originalText = $btn.html();
                $btn.html('<em class="icon ni ni-loader"></em> Allotting...').prop('disabled', true);

                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route('allot_specific_tests') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        test_name: selectedTestName,
                        test_ids: selectedSamples.join(','),
                        emp_id: analystId
                    }),
                    headers: csrfToken ? {
                        'X-CSRF-TOKEN': csrfToken
                    } : {},
                    success: function(data) {
                        if (data.success) {
                            showFeedback('success', data.message || 'Tests allotted successfully',
                                'Allotment Successful');

                            // Close modal and refresh
                            $('#testSamplesModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showFeedback('error', data.message || 'Failed to allot tests',
                                'Allotment Failed');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Allotment error:', error);
                        let errorMessage =
                            'An error occurred while allotting tests. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showFeedback('error', errorMessage, 'Allotment Error');
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            };

            updateBulkActions();
            $(window).on('beforeunload', function() {
                selectedSamples.clear();
            });

            // Check for Laravel session flash messages and display them with SweetAlert2
            @if (Session::has('message'))
                $(document).ready(function() {
                    const messageType = '{{ Session::get('type', 'info') }}';
                    const message = '{{ Session::get('message') }}';

                    const iconMap = {
                        'success': 'success',
                        'error': 'error',
                        'warning': 'warning',
                        'info': 'info'
                    };
                    showFeedback(messageType, message);
                });
            @endif
        });
    </script>
@endsection
