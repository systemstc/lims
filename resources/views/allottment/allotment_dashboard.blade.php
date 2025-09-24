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
<button class="eg-swal-av2 btn btn-primary" >Alert</button>
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

        <!-- Test Search and Allotment Section -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Search Tests for Allotment</label>
                            <div class="input-group">
                                <input type="text" id="testSearchInput" class="form-control"
                                    placeholder="Enter test name (e.g., pH, Moisture, etc.)" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" onclick="searchTests()">
                                        <em class="icon ni ni-search"></em> Search
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Search for specific tests across all pending samples</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="testSearchStats" class="text-end" style="display: none;">
                            <span class="badge bg-info" id="testFoundCount">0</span> Tests found
                            <span class="badge bg-warning" id="samplesAffected">0</span> Samples affected
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Search Results Modal -->
        <div class="modal fade" tabindex="-1" id="testSearchModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Test Search Results</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div id="testSearchResults">
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
                                                        ? ($registration->allotted_tests / $registration->total_tests) *
                                                            100
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
                                                        {{-- <li>
                                                            <a href="#"
                                                                onclick="viewDetails({{ $registration->tr04_sample_registration_id }})">
                                                                <em class="icon ni ni-eye"></em>
                                                                <span>View Details</span>
                                                            </a>
                                                        </li> --}}
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

    </div>

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
                <form id="quickTransferForm" method="POST" action="{{ route('quick_allot_sample') }}">
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
        // Global variables
        let selectedSamples = new Set();

        // View details function
        function viewDetails(registrationId) {
            window.location.href = `/registration/view/${registrationId}`;
        }

        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAllSamples');
            const sampleCheckboxes = document.querySelectorAll('.sample-checkbox');

            sampleCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                if (selectAllCheckbox.checked) {
                    selectedSamples.add(checkbox.value);
                } else {
                    selectedSamples.delete(checkbox.value);
                }
            });

            updateBulkActions();
        }

        // Update bulk action buttons based on selection
        function updateBulkActions() {
            const sampleCheckboxes = document.querySelectorAll('.sample-checkbox:checked');
            selectedSamples.clear();

            sampleCheckboxes.forEach(checkbox => {
                selectedSamples.add(checkbox.value);
            });

            const count = selectedSamples.size;
            const bulkAllotBtn = document.getElementById('bulkAllotBtn');
            const bulkTransferBtn = document.getElementById('bulkTransferBtn');
            const selectedCountSpan = document.getElementById('selectedCount');

            selectedCountSpan.textContent = count;
            bulkAllotBtn.disabled = count === 0;
            bulkTransferBtn.disabled = count === 0;

            // Update select all checkbox state
            const selectAllCheckbox = document.getElementById('selectAllSamples');
            const allCheckboxes = document.querySelectorAll('.sample-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
            selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        }

        // Clear all selections
        function clearSelection() {
            document.querySelectorAll('.sample-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAllSamples').checked = false;
            selectedSamples.clear();
            updateBulkActions();
        }

        // Open bulk allot modal
        function openBulkAllotModal() {
            if (selectedSamples.size === 0) return;

            document.getElementById('bulkAllotSampleIds').value = Array.from(selectedSamples).join(',');

            // Populate sample list
            const sampleList = document.getElementById('bulkAllotSampleList');
            const sampleRefs = [];
            selectedSamples.forEach(sampleId => {
                const checkbox = document.querySelector(`input[value="${sampleId}"] `);
                const row = checkbox.closest('.nk-tb-item');
                const refElement = row.querySelector('.tb-lead');
                if (refElement) {
                    sampleRefs.push(refElement.textContent);
                }
            });

            sampleList.innerHTML = sampleRefs.length > 0 ?
                sampleRefs.map(ref => `<span class="badge bg-primary me-1">${ref}</span>`).join('') :
                'No samples selected';


            const bulkAllotModal = new bootstrap.Modal(document.getElementById('bulkAllotModal'));
            bulkAllotModal
                .show();
        }

        // Open bulk transfer modal
        function openBulkTransferModal() {
            if (selectedSamples.size === 0) return;

            document.getElementById('bulkTransferSampleIds').value = Array.from(selectedSamples).join(',');

            // Populate sample list
            const sampleList = document.getElementById('bulkTransferSampleList');
            const sampleRefs = [];
            selectedSamples.forEach(sampleId => {
                const checkbox = document.querySelector(`input[value="${sampleId}"] `);
                const row = checkbox.closest('.nk-tb-item');
                const refElement = row.querySelector('.tb-lead');
                if (refElement) {
                    sampleRefs.push(refElement.textContent);
                }
            });

            sampleList.innerHTML = sampleRefs.length > 0 ?
                sampleRefs.map(ref => `<span class="badge bg-warning me-1"> ${ref} </span>`).join('') :
                'No samples selected';

            const bulkTransferModal = new bootstrap.Modal(document.getElementById(
                'bulkTransferModal'));
            bulkTransferModal.show();
        }

        // Quick allot single sample
        function quickAllot(sampleId) {
            const row = document.querySelector(`input[value="${sampleId}"] `).closest('.nk-tb-item');
            const refElement = row.querySelector('.tb-lead');

            document.getElementById('quickAllotSampleId').value = sampleId;
            document.getElementById('quickAllotSampleRef').value = refElement ? refElement.textContent : `
                Sample $ {
                    sampleId
                }
                `;

            const quickAllotModal = new bootstrap.Modal(document.getElementById('quickAllotModal'));
            quickAllotModal.show();
        }

        // Quick transfer single sample
        function quickTransfer(sampleId) {
            const row = document.querySelector(`input[value="${sampleId}"]`).closest('.nk-tb-item');
            const refElement = row.querySelector('.tb-lead');

            document.getElementById('quickTransferSampleId').value = sampleId;
            document.getElementById('quickTransferSampleRef').value = refElement ? refElement.textContent :
                `Sample ${sampleId}`;

            const quickTransferModal = new bootstrap.Modal(document.getElementById('quickTransferModal'));
            quickTransferModal.show();
        }

        // Form validation and submission
        document.getElementById('bulkAllotForm').addEventListener('submit', function(e) {
            const empId = this.querySelector('select[name="emp_id"]').value;
            if (!empId) {
                e.preventDefault();
                alert('Please select an analyst');
                return false;
            }

            if (selectedSamples.size === 0) {
                e.preventDefault();
                alert('No samples selected');
                return false;
            }

            if (!confirm(
                    `Are you sure you want to allot ${selectedSamples.size} sample(s) to the selected analyst?`)) {
                e.preventDefault();
                return false;
            }
        });

        document.getElementById('bulkTransferForm').addEventListener('submit', function(e) {
            const roId = this.querySelector('select[name="ro_id"]').value;
            const reason = this.querySelector('select[name="reason"]').value;

            if (!roId || !reason) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }

            if (selectedSamples.size === 0) {
                e.preventDefault();
                alert('No samples selected');
                return false;
            }

            if (!confirm(
                    `Are you sure you want to transfer ${selectedSamples.size} sample(s) to the selected RO?`)) {
                e.preventDefault();
                return false;
            }
        });

        document.getElementById('quickAllotForm').addEventListener('submit', function(e) {
            const empId = this.querySelector('select[name="emp_id"]').value;
            if (!empId) {
                e.preventDefault();
                alert('Please select an analyst');
                return false;
            }

            if (!confirm('Are you sure you want to allot all tests in this sample to the selected analyst?')) {
                e.preventDefault();
                return false;
            }
        });

        document.getElementById('quickTransferForm').addEventListener('submit', function(e) {
            const roId = this.querySelector('select[name="ro_id"]').value;
            const reason = this.querySelector('select[name="reason"]').value;

            if (!roId || !reason) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }

            if (!confirm('Are you sure you want to transfer all tests in this sample to the selected RO?')) {
                e.preventDefault();
                return false;
            }
        });

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize bulk action state
            updateBulkActions();
        });

        // Clear selections when page reloads
        window.addEventListener('beforeunload', function() {
            clearSelection();
        });

        // Search and allot selected test from each sample// Test search functionality
        function searchTests() {
            const searchTerm = document.getElementById('testSearchInput').value.trim();

            if (!searchTerm) {
                alert('Please enter a test name to search');
                return;
            }

            // Show loading
            const searchBtn = event.target;
            const originalText = searchBtn.innerHTML;
            searchBtn.innerHTML = '<em class="icon ni ni-loader"></em> Searching...';
            searchBtn.disabled = true;

            fetch('{{ route('search_tests_allotment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        search_test: searchTerm
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayTestSearchResults(data);
                    } else {
                        alert(data.message || 'No tests found');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while searching');
                })
                .finally(() => {
                    searchBtn.innerHTML = originalText;
                    searchBtn.disabled = false;
                });
        }

        function displayTestSearchResults(data) {
            const resultsContainer = document.getElementById('testSearchResults');
            const statsContainer = document.getElementById('testSearchStats');
            let totalTests = 0;
            let totalSamples = 0;
            let resultsHTML = '';

            if (Object.keys(data.test_results).length === 0) {
                resultsHTML = '<div class="text-center py-4"><p class="text-muted">No unallotted tests found for "' + data
                    .search_term + '"</p></div>';
            } else {
                resultsHTML = '<div class="alert alert-info"><strong>Search Results for:</strong> ' + data.search_term +
                    '</div>';

                Object.entries(data.test_results).forEach(([testId, tests]) => {
                    const testName = tests[0].test_name;
                    totalTests += tests.length;
                    totalSamples += tests.length;

                    resultsHTML += `
                <div class="card border mb-3">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="mb-0">${testName}</h6>
                                <small class="text-muted">${tests.length} tests found</small>
                            </div>
                            <div class="col-auto">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input test-group-checkbox" 
                                           id="testGroup_${testId}" onchange="toggleTestGroup('${testId}')">
                                    <label class="custom-control-label" for="testGroup_${testId}">Select All</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">`;

                    tests.forEach(test => {
                        resultsHTML += `
                    <div class="col-md-6 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input test-checkbox test-group-${testId}" 
                                   id="test_${test.tr05_sample_test_id}" 
                                   value="${test.tr05_sample_test_id}"
                                   onchange="updateTestSelection()">
                            <label class="custom-control-label" for="test_${test.tr05_sample_test_id}">
                                <strong>${test.tr04_reference_id}</strong>
                                <br><small class="text-muted">Registered: ${new Date(test.registration.created_at).toLocaleDateString()}</small>
                            </label>
                        </div>
                    </div>`;
                    });

                    resultsHTML += '</div></div></div>';
                });
            }

            resultsContainer.innerHTML = resultsHTML;

            // Update stats
            document.getElementById('testFoundCount').textContent = totalTests;
            document.getElementById('samplesAffected').textContent = totalSamples;
            statsContainer.style.display = totalTests > 0 ? 'block' : 'none';

            // Show modal
            const testSearchModal = new bootstrap.Modal(document.getElementById('testSearchModal'));
            testSearchModal.show();

            updateTestSelection();
        }

        function toggleTestGroup(testId) {
            const groupCheckbox = document.getElementById('testGroup_' + testId);
            const testCheckboxes = document.querySelectorAll('.test-group-' + testId);

            testCheckboxes.forEach(checkbox => {
                checkbox.checked = groupCheckbox.checked;
            });

            updateTestSelection();
        }

        function updateTestSelection() {
            const selectedTests = document.querySelectorAll('.test-checkbox:checked');
            const allotBtn = document.getElementById('allotTestsBtn');
            const analystSelect = document.getElementById('testAllotmentAnalyst');

            allotBtn.disabled = selectedTests.length === 0 || !analystSelect.value;

            // Update group checkboxes
            document.querySelectorAll('.test-group-checkbox').forEach(groupCheckbox => {
                const testId = groupCheckbox.id.replace('testGroup_', '');
                const groupTests = document.querySelectorAll('.test-group-' + testId);
                const checkedTests = document.querySelectorAll('.test-group-' + testId + ':checked');

                groupCheckbox.checked = groupTests.length > 0 && groupTests.length === checkedTests.length;
                groupCheckbox.indeterminate = checkedTests.length > 0 && checkedTests.length < groupTests.length;
            });
        }

        function allotSelectedTests() {
            const selectedTestIds = Array.from(document.querySelectorAll('.test-checkbox:checked'))
                .map(checkbox => checkbox.value);
            const analystId = document.getElementById('testAllotmentAnalyst').value;
            const testName = document.getElementById('testSearchInput').value;

            if (selectedTestIds.length === 0) {
                alert('Please select at least one test');
                return;
            }

            if (!analystId) {
                alert('Please select an analyst');
                return;
            }

            if (!confirm(`Are you sure you want to allot ${selectedTestIds.length} test(s) to the selected analyst?`)) {
                return;
            }

            // Show loading
            const allotBtn = document.getElementById('allotTestsBtn');
            const originalText = allotBtn.innerHTML;
            allotBtn.innerHTML = '<em class="icon ni ni-loader"></em> Allotting...';
            allotBtn.disabled = true;

            fetch('{{ route('allot_specific_tests') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        test_name: testName,
                        test_ids: selectedTestIds.join(','),
                        emp_id: analystId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Close modal and refresh page
                        bootstrap.Modal.getInstance(document.getElementById('testSearchModal')).hide();
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to allot tests');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while allotting tests');
                })
                .finally(() => {
                    allotBtn.innerHTML = originalText;
                    allotBtn.disabled = false;
                });
        }

        // Enable allot button when analyst is selected
        document.addEventListener('DOMContentLoaded', function() {
            const analystSelect = document.getElementById('testAllotmentAnalyst');
            if (analystSelect) {
                analystSelect.addEventListener('change', updateTestSelection);
            }
        });
    </script>
@endsection
