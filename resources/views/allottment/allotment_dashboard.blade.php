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
                                    title="Samples just received from registration today"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['samples_received_today'] ?? 0 }}</span>
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
                                <h6 class="title">Pending Samples</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples waiting for allotment"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['pending_samples'] ?? 0 }}</span>
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
                                <h6 class="title">Tested Today</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples tested today"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['tested_today'] ?? 0 }}</span>
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
                                <h6 class="title">Reported Today</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples Reported today"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['reported_samples'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-bordered shadow-sm mb-4">
            <div class="card-inner p-2">
                <div class="row align-items-end g-3">
                    <!-- Left Column - Bulk Actions -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-dark mb-2">Bulk Actions</label>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button type="button" class="btn btn-primary" onclick="openBulkAllotModal()" id="bulkAllotBtn"
                                disabled>
                                <em class="icon ni ni-user-check"></em>
                                <span class="ms-1">Allot Selected</span>
                            </button>
                            <button type="button" class="btn btn-warning text-dark" onclick="openBulkTransferModal()"
                                id="bulkTransferBtn" disabled>
                                <em class="icon ni ni-send"></em>
                                <span class="ms-1">Transfer Selected</span>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-4">
                            <em class="icon ni ni-info text-primary"></em>
                            Select samples below to enable bulk actions.
                        </small>
                    </div>

                    <!-- Right Column - Test Selection -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark mb-2">Test based Allotment</label>
                        <select id="testSelectionDropdown" class="form-select js-select2" data-search="on">
                            <option value="">Choose Test...</option>
                            @foreach ($availableTests as $test)
                                <option value="{{ $test->m12_test_id }}">
                                    {{ $test->m12_name }} ({{ $test->test_count }} samples)
                                </option>
                            @endforeach
                        </select>
                        <div id="testSelectionStats" class="mt-2 d-flex align-items-center gap-2" style="display: none;">
                            <span class="badge bg-info" id="selectedTestName">No test selected</span>
                            <span class="badge bg-warning text-dark" id="samplesAvailable">0</span>
                            <span class="text-muted">samples available</span>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Selected Samples Counter -->
                <div class="row align-items-center">
                    <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <label class="form-label fw-semibold text-dark mb-0">Selected Samples:</label>
                            <span class="badge bg-primary fs-6" id="selectedCount">0</span>
                            <span class="text-muted">samples selected</span>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                            <em class="icon ni ni-cross"></em> Clear All
                        </button>
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
                                <option value="Tatkal" {{ request('priority') == 'Tatkal' ? 'selected' : '' }}>Tatkal
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
                                <option value="tatkal" {{ request('status') == 'tatkal' ? 'selected' : '' }}>Tatkal (3+
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

        <!-- Tabbed Tables Section -->
        <div class="card card-bordered">
            <div class="card-inner">
                <ul class="nav nav-tabs" id="samplesTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="unallotted-samples-tab" data-bs-toggle="tab"
                            data-bs-target="#unallotted-samples-pane" type="button" role="tab"
                            aria-controls="unallotted-samples-pane" aria-selected="true">
                            <em class="icon ni ni-alert-circle"></em>&nbsp; Unallotted Samples
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="all-samples-tab" data-bs-toggle="tab"
                            data-bs-target="#all-samples-pane" type="button" role="tab"
                            aria-controls="all-samples-pane" aria-selected="false">
                            <em class="icon ni ni-layers"></em>&nbsp; All Samples
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="samplesTabContent">
                    <!-- Unallotted Samples Tab -->
                    <div class="tab-pane fade show active" id="unallotted-samples-pane" role="tabpanel"
                        aria-labelledby="unallotted-samples-tab">
                        <div class="nk-tb-list nk-tb-ulist mt-3">
                            <table id="unallottedSamplesTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="selectAllUnallotted" onchange="toggleSelectAll('unallotted')">
                                                <label class="custom-control-label" for="selectAllUnallotted"></label>
                                            </div>
                                        </th>
                                        <th>Registration ID</th>
                                        <th>Received Date</th>
                                        <th>Test Progress</th>
                                        <th>Priority</th>
                                        <th>Days Pending</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unallottedOrPartial as $registration)
                                        <tr>
                                            <td>
                                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                                    <input type="checkbox"
                                                        class="custom-control-input sample-checkbox-unallotted"
                                                        id="sample-unallotted-{{ $registration->tr04_sample_registration_id }}"
                                                        value="{{ $registration->tr04_sample_registration_id }}"
                                                        onchange="updateBulkActions('unallotted')">
                                                    <label class="custom-control-label"
                                                        for="sample-unallotted-{{ $registration->tr04_sample_registration_id }}"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span
                                                            class="tb-lead">{{ $registration->tr04_reference_id }}</span>
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
                                            </td>
                                            <td>
                                                <span
                                                    class="tb-lead">{{ $registration->created_at->format('d M Y') }}</span>
                                                <span
                                                    class="tb-sub">{{ $registration->created_at->format('h:i A') }}</span>
                                            </td>
                                            <td>
                                                <div class="progress-wrap">
                                                    <div class="progress-text">
                                                        {{ $registration->allotted_tests }}/{{ $registration->total_tests }}
                                                    </div>
                                                    <div class="progress progress-md">
                                                        @php
                                                            $percentage =
                                                                $registration->total_tests > 0
                                                                    ? ($registration->allotted_tests /
                                                                            $registration->total_tests) *
                                                                        100
                                                                    : 0;
                                                        @endphp
                                                        <div class="progress-bar" style="width: {{ $percentage }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $priority = $registration->tr04_sample_type ?? 'Normal';
                                                    $priorityClass = match (strtolower($priority)) {
                                                        'tatkal' => 'text-danger',
                                                        'normal' => 'text-info',
                                                        default => 'text-secondary',
                                                    };
                                                @endphp
                                                <strong class="{{ $priorityClass }}">{{ $priority }}</strong>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                @php
                                                    $allottedPercentage =
                                                        $registration->total_tests > 0
                                                            ? ($registration->allotted_tests /
                                                                    $registration->total_tests) *
                                                                100
                                                            : 0;
                                                    [$status, $statusClass] = match (true) {
                                                        $allottedPercentage == 0 => ['New', 'text-warning'],
                                                        $allottedPercentage == 100 => ['Complete', 'text-success'],
                                                        default => ['Partial', 'text-info'],
                                                    };
                                                @endphp
                                                <span class="fw-bold {{ $statusClass }}">{{ $status }}</span>
                                            </td>
                                            <td>
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
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
                                                                            href="{{ route('edit_sample', $registration->tr04_sample_registration_id) }}">
                                                                            <em class="icon ni ni-edit text-primary"></em>
                                                                            <span>Edit</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('show_allotment', $registration->tr04_sample_registration_id) }}">
                                                                            <em
                                                                                class="icon ni ni-user-check text-success"></em>
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
                                                                            <em
                                                                                class="icon ni ni-forward-arrow text-warning"></em>
                                                                            <span>Quick Transfer</span>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('manuscript_template', $registration->tr04_sample_registration_id) }}">
                                                                            <em
                                                                                class="icon ni ni-clipboad-check text-success"></em>
                                                                            <span>Manuscript</span>
                                                                        </a>
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
                        </div>
                    </div>

                    <!-- All Samples Tab -->
                    <div class="tab-pane fade" id="all-samples-pane" role="tabpanel" aria-labelledby="all-samples-tab">
                        <div class="nk-tb-list nk-tb-ulist mt-3">
                            <table id="allSamplesTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            {{-- <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="selectAllAllSamples" onchange="toggleSelectAll('all')">
                                                <label class="custom-control-label" for="selectAllAllSamples"></label>
                                            </div> --}}
                                        </th>
                                        <th>Registration ID</th>
                                        <th>Received Date</th>
                                        <th>Test Progress</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Days Pending</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allSamples as $registration)
                                        <tr>
                                            <td>
                                                {{-- <div class="custom-control custom-control-sm custom-checkbox notext">
                                                    <input type="checkbox"
                                                        class="custom-control-input sample-checkbox-all"
                                                        id="sample-all-{{ $registration->tr04_sample_registration_id }}"
                                                        value="{{ $registration->tr04_sample_registration_id }}"
                                                        onchange="updateBulkActions('all')">
                                                    <label class="custom-control-label"
                                                        for="sample-all-{{ $registration->tr04_sample_registration_id }}"></label>
                                                </div> --}}
                                            </td>
                                            <td>
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <span
                                                            class="tb-lead">{{ $registration->tr04_reference_id }}</span>
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
                                            </td>
                                            <td>
                                                <span
                                                    class="tb-lead">{{ $registration->created_at->format('d M Y') }}</span>
                                                <span
                                                    class="tb-sub">{{ $registration->created_at->format('h:i A') }}</span>
                                            </td>
                                            <td>
                                                <div class="progress-wrap">
                                                    <div class="progress-text">
                                                        {{ $registration->allotted_tests }}/{{ $registration->total_tests }}
                                                    </div>
                                                    <div class="progress progress-md">
                                                        @php
                                                            $percentage =
                                                                $registration->total_tests > 0
                                                                    ? ($registration->allotted_tests /
                                                                            $registration->total_tests) *
                                                                        100
                                                                    : 0;
                                                        @endphp
                                                        <div class="progress-bar" style="width: {{ $percentage }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $priority = $registration->tr04_sample_type ?? 'Normal';
                                                    $priorityClass = match (strtolower($priority)) {
                                                        'tatkal' => 'text-danger',
                                                        'normal' => 'text-info',
                                                        default => 'text-secondary',
                                                    };
                                                @endphp
                                                <span
                                                    class="fw-bold  {{ $priorityClass }}">{{ ucfirst($priority) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $allottedPercentage =
                                                        $registration->total_tests > 0
                                                            ? ($registration->allotted_tests /
                                                                    $registration->total_tests) *
                                                                100
                                                            : 0;
                                                    [$status, $statusClass] = match (true) {
                                                        $allottedPercentage == 0 => ['New', 'text-warning'],
                                                        $allottedPercentage == 100 => ['Completed', 'text-success'],
                                                        default => ['Partial', 'text-info'],
                                                    };
                                                @endphp
                                                <span class="fw-bold  {{ $statusClass }}">{{ $status }}</span>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
                                                        <div class="dropdown">
                                                            <a href="#"
                                                                class="dropdown-toggle btn btn-icon btn-trigger"
                                                                data-bs-toggle="dropdown">
                                                                <em class="icon ni ni-more-h"></em>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <ul class="link-list-opt no-bdr">
                                                                    @if ($allottedPercentage == 100)
                                                                        <li> <a
                                                                                href="{{ route('manuscript_template', $registration->tr04_sample_registration_id) }}">
                                                                                <em
                                                                                    class="icon ni ni-check-circle text-success"></em>
                                                                                <span>Manuscript</span>
                                                                            </a></li>
                                                                    @else
                                                                        <li>
                                                                            <a
                                                                                href="{{ route('show_allotment', $registration->tr04_sample_registration_id) }}">
                                                                                <em
                                                                                    class="icon ni ni-user-check text-success"></em>
                                                                                <span>Manage Allotment</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a style="cursor: pointer;"
                                                                                onclick="quickAllot({{ $registration->tr04_sample_registration_id }})">
                                                                                <em
                                                                                    class="icon ni ni-spark text-primary"></em>
                                                                                <span>Quick Allot</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a style="cursor: pointer;"
                                                                                onclick="quickTransfer({{ $registration->tr04_sample_registration_id }})">
                                                                                <em
                                                                                    class="icon ni ni-forward-arrow text-warning"></em>
                                                                                <span>Quick Transfer</span>
                                                                            </a>
                                                                        </li>
                                                                    @endif
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
                        </div>
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
                    <div id="testSamplesResults"></div>
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
                        <input type="hidden" id="bulkAllotCurrentTab" name="current_tab">
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
                        <input type="hidden" id="bulkTransferCurrentTab" name="current_tab">
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
            let selectedSamples = {
                'all': new Set(),
                'unallotted': new Set()
            };
            let currentTab = 'unallotted';
            let tablesInitialized = {
                all: false,
                unallotted: false
            };

            // CRITICAL: Override Dashlite's tab management
            const tabElement = $('#samplesTab');

            function cleanupTabClasses() {
                // Remove problematic Dashlite classes from nav-items
                $('#samplesTab .nav-item').removeClass('active current-page');

                // Ensure only active button has active class
                $('#samplesTab .nav-link').removeClass('active');
                const activePane = $('.tab-pane.active');
                if (activePane.length) {
                    const activeId = activePane.attr('aria-labelledby');
                    $(`#${activeId}`).addClass('active');
                }
            }

            // Clean up on initial load
            cleanupTabClasses();

            // Manual tab click handling
            $('#samplesTab button[data-bs-toggle="tab"]').on('click', function(e) {
                e.preventDefault();

                // Remove active classes from all
                $('#samplesTab .nav-link').removeClass('active');
                $('#samplesTab .nav-item').removeClass('active current-page');
                $('.tab-pane').removeClass('active show');

                // Add active to clicked tab
                $(this).addClass('active');

                // Get target and show it
                const target = $(this).attr('data-bs-target');
                $(target).addClass('active show');

                // Update current tab
                currentTab = target === '#all-samples-pane' ? 'all' : 'unallotted';

                // Initialize table on first view
                if (target === '#unallotted-samples-pane' && !tablesInitialized.unallotted) {
                    initializeUnallottedTable();
                    tablesInitialized.unallotted = true;
                } else if (target === '#all-samples-pane' && !tablesInitialized.all) {
                    initializeAllSamplesTable();
                    tablesInitialized.all = true;
                }

                updateBulkActions(currentTab);
            });

            // Monitor for Dashlite re-applying classes and clean them up
            new MutationObserver(function() {
                setTimeout(cleanupTabClasses, 10);
            }).observe(tabElement[0], {
                attributes: true,
                subtree: true,
                attributeFilter: ['class']
            });

            function initializeAllSamplesTable() {
                $('#allSamplesTable').DataTable({
                    pageLength: 20,
                    ordering: true,
                    searching: true,
                    lengthChange: true,
                    info: true,
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        },
                        {
                            orderable: false,
                            targets: 7
                        }
                    ]
                });
            }

            function initializeUnallottedTable() {
                $('#unallottedSamplesTable').DataTable({
                    pageLength: 20,
                    ordering: true,
                    searching: true,
                    lengthChange: true,
                    info: true,
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        },
                        {
                            orderable: false,
                            targets: 7
                        }
                    ]
                });
            }

            function showFeedback(type, message, title = null) {
                if (typeof Swal !== 'undefined') {
                    const config = {
                        text: message,
                        icon: type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'success',
                        confirmButtonText: 'OK'
                    };
                    if (title) config.title = title;
                    Swal.fire(config);
                } else {
                    alert(message);
                }
            }

            window.toggleSelectAll = function(tab) {
                const checkboxClass = tab === 'all' ? '.sample-checkbox-all' : '.sample-checkbox-unallotted';
                const selectAllId = tab === 'all' ? '#selectAllAllSamples' : '#selectAllUnallotted';
                const selectAllCheckbox = $(selectAllId)[0];

                $(checkboxClass).prop('checked', selectAllCheckbox.checked).each(function() {
                    if (this.checked) {
                        selectedSamples[tab].add(this.value);
                    } else {
                        selectedSamples[tab].delete(this.value);
                    }
                });
                updateBulkActions(tab);
            };

            window.updateBulkActions = function(tab) {
                const checkboxClass = tab === 'all' ? '.sample-checkbox-all' : '.sample-checkbox-unallotted';
                const selectAllId = tab === 'all' ? '#selectAllAllSamples' : '#selectAllUnallotted';

                selectedSamples[tab].clear();
                $(checkboxClass + ':checked').each(function() {
                    selectedSamples[tab].add(this.value);
                });

                const count = selectedSamples[tab].size;
                $('#selectedCount').text(count);
                $('#bulkAllotBtn, #bulkTransferBtn').prop('disabled', count === 0);

                const totalCheckboxes = $(checkboxClass).length;
                const selectAllCheckbox = $(selectAllId)[0];
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = count === totalCheckboxes;
                    selectAllCheckbox.indeterminate = count > 0 && count < totalCheckboxes;
                }
            };

            window.clearSelection = function() {
                $('.sample-checkbox-all, .sample-checkbox-unallotted').prop('checked', false);
                $('#selectAllAllSamples, #selectAllUnallotted').prop('checked', false).prop('indeterminate',
                    false);
                selectedSamples['all'].clear();
                selectedSamples['unallotted'].clear();
                updateBulkActions(currentTab);
            };

            window.openBulkAllotModal = function() {
                if (selectedSamples[currentTab].size === 0) return;
                $('#bulkAllotSampleIds').val([...selectedSamples[currentTab]].join(','));
                $('#bulkAllotCurrentTab').val(currentTab);
                populateSampleList('#bulkAllotSampleList', 'badge bg-primary me-1', currentTab);
                new bootstrap.Modal($('#bulkAllotModal')[0]).show();
            };

            window.openBulkTransferModal = function() {
                if (selectedSamples[currentTab].size === 0) return;
                $('#bulkTransferSampleIds').val([...selectedSamples[currentTab]].join(','));
                $('#bulkTransferCurrentTab').val(currentTab);
                populateSampleList('#bulkTransferSampleList', 'badge bg-warning me-1', currentTab);
                new bootstrap.Modal($('#bulkTransferModal')[0]).show();
            };

            function populateSampleList(containerSelector, badgeClass, tab) {
                let sampleRefs = [];
                selectedSamples[tab].forEach(id => {
                    const checkboxClass = tab === 'all' ? '.sample-checkbox-all' :
                        '.sample-checkbox-unallotted';
                    const row = $(`input[value="${id}"].${checkboxClass.substring(1)}`).closest('tr');
                    const ref = row.find('.tb-lead').first().text().trim();
                    if (ref) sampleRefs.push(`<span class="${badgeClass}">${ref}</span>`);
                });
                $(containerSelector).html(sampleRefs.join('') || 'No samples selected');
            }

            window.quickAllot = function(id) {
                const row = $(`input[value="${id}"]`).closest('tr');
                const refText = row.find('.tb-lead').first().text().trim();
                $('#quickAllotSampleId').val(id);
                $('#quickAllotSampleRef').val(refText || `Sample ${id}`);
                new bootstrap.Modal($('#quickAllotModal')[0]).show();
            };

            window.quickTransfer = function(id) {
                const row = $(`input[value="${id}"]`).closest('tr');
                const refText = row.find('.tb-lead').first().text().trim();
                $('#quickTransferSampleId').val(id);
                $('#quickTransferSampleRef').val(refText || `Sample ${id}`);
                new bootstrap.Modal($('#quickTransferModal')[0]).show();
            };

            $('#bulkAllotForm').on('submit', function(e) {
                e.preventDefault();
                const empId = $(this).find('select[name="emp_id"]').val();
                if (!empId) {
                    showFeedback('error', 'Please select an analyst', 'Validation Error');
                    return;
                }
                if (selectedSamples[currentTab].size === 0) {
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
                if (selectedSamples[currentTab].size === 0) {
                    showFeedback('error', 'No samples selected', 'Validation Error');
                    return;
                }
                this.submit();
            });

            $('#quickAllotForm, #quickTransferForm').on('submit', function(e) {
                e.preventDefault();
                const empId = $(this).find('select[name="emp_id"]').val();
                const roId = $(this).find('select[name="ro_id"]').val();
                const reason = $(this).find('select[name="reason"]').val();

                if ($(this).attr('id') === 'quickAllotForm' && !empId) {
                    showFeedback('error', 'Please select an analyst', 'Validation Error');
                    return;
                }
                if ($(this).attr('id') === 'quickTransferForm' && (!roId || !reason)) {
                    showFeedback('error', 'Please fill in all required fields', 'Validation Error');
                    return;
                }
                this.submit();
            });

            $('#testSelectionDropdown').on('change', function() {
                const testId = $(this).val();
                const testName = $(this).find('option:selected').text();

                if (testId) {
                    $('#selectedTestName').text(testName.split(' (')[0]);
                    $('#testSelectionStats').show();
                    loadTestSamples(testId);
                } else {
                    $('#testSelectionStats').hide();
                }
            });

            function loadTestSamples(testId) {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route('get_test_samples_allotment') }}',
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
                    resultsHTML =
                        `<div class="text-center py-4"><p class="text-muted">No unallotted samples found for this test</p></div>`;
                } else {
                    resultsHTML = `
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Test:</strong> ${data.test_name}<br>
                                    <strong>Available Samples:</strong> ${data.samples.length}
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAllTestSamples">
                                    <label class="form-check-label" for="selectAllTestSamples">Select All</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">`;

                    $.each(data.samples, function(index, sample) {
                        const regDate = sample.created_at ? new Date(sample.created_at)
                            .toLocaleDateString() : 'Unknown';
                        const referenceId = sample.registration ? sample.registration.tr04_reference_id :
                            'Unknown Reference';

                        resultsHTML += `<div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input test-sample-checkbox" value="${sample.tr05_sample_test_id}">
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
                $('#testAllotmentAnalyst').val('');
                updateTestSelection();
            }

            $(document).on('change', '#selectAllTestSamples', function() {
                $('.test-sample-checkbox').prop('checked', this.checked);
                updateTestSelection();
            });

            $(document).on('change', '.test-sample-checkbox', function() {
                updateTestSelection();
                const totalSamples = $('.test-sample-checkbox').length;
                const checkedSamples = $('.test-sample-checkbox:checked').length;
                $('#selectAllTestSamples').prop('indeterminate', checkedSamples > 0 && checkedSamples <
                    totalSamples);
                $('#selectAllTestSamples').prop('checked', checkedSamples === totalSamples);
            });

            function updateTestSelection() {
                const count = $('.test-sample-checkbox:checked').length;
                const analystSelected = $('#testAllotmentAnalyst').val();
                $('#allotTestsBtn').prop('disabled', count === 0 || !analystSelected);
            }

            $('#testAllotmentAnalyst').on('change', updateTestSelection);

            window.allotSelectedTests = function() {
                const selectedTests = $('.test-sample-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                const analystId = $('#testAllotmentAnalyst').val();
                const selectedTestName = $('#testSelectionDropdown option:selected').text().split(' (')[0];

                if (!selectedTests.length) {
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
                        test_ids: selectedTests.join(','),
                        emp_id: analystId
                    }),
                    headers: csrfToken ? {
                        'X-CSRF-TOKEN': csrfToken
                    } : {},
                    success: function(data) {
                        if (data.success) {
                            showFeedback('success', data.message || 'Tests allotted successfully',
                                'Allotment Successful');
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

            if (typeof $().tooltip === 'function') {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }

            updateBulkActions('all');

            @if (Session::has('message'))
                const messageType = '{{ Session::get('type', 'info') }}';
                const message = '{{ Session::get('message') }}';
                showFeedback(messageType, message);
            @endif
        });
    </script>
@endsection
