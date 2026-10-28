@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between align-items-center">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Manage Test Allotments</h3>
                            <div class="nk-block-des text-soft">
                                <p>Registration ID: <span class="fw-bold text-primary">#{{ $registration->tr04_reference_id }}</span> &bull; Allot tests to employees or transfer to other ROs</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#sendBackToRCModal">
                                    <em class="icon ni ni-undo me-1"></em> <span>Send Back to RC</span>
                                </button>
                                <a href="{{ route('view_allottment') }}" class="btn btn-primary btn-sm">
                                    <em class="icon ni ni-arrow-left me-1"></em> <span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

        @if ($registration->tr04_is_under_clarification)
            <!-- Clarification Alert Banner -->
            <div class="alert alert-pro alert-warning bg-warning-dim border-warning shadow-none mb-4 py-2 px-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-1 border-bottom border-warning border-opacity-25 mb-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <em class="icon ni ni-alert-circle text-warning fs-18px"></em>
                        <span class="fw-bold text-dark fs-14px">Clarification Required</span>
                        <span class="badge badge-dim bg-danger text-danger">Returned by {{ $registration->tr04_sent_back_by_role }} ({{ $registration->tr04_sent_back_by_name ?? 'Authority' }})</span>
                    </div>
                    @if ($registration->tr04_sent_back_at)
                        <span class="text-muted small">
                            <em class="icon ni ni-clock me-1"></em>{{ \Carbon\Carbon::parse($registration->tr04_sent_back_at)->format('d M, Y h:i A') }}
                        </span>
                    @endif
                </div>
                <div class="text-dark fs-13px ps-1 pt-1" style="line-height: 1.5;">
                    <strong class="text-warning-dark me-1"><em class="icon ni ni-chat-fill text-warning me-1"></em>Remark:</strong>
                    <span>{{ $registration->tr04_latest_clarification_remark }}</span>
                </div>
            </div>
        @endif
        <div class="nk-block nk-block-lg">
            <!-- Registration Summary -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="row g-4">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Department</label>
                            <div class="form-control-wrap">
                                <span class="badge bg-primary">
                                    {{ $registration->department->m13_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Priority</label>
                            <div class="form-control-wrap">
                                <span
                                    class="badge {{ $registration->tr04_sample_type === 'Tatkal' ? 'bg-danger' : 'bg-info' }}">
                                    {{ $registration->tr04_sample_type ?? 'Normal' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <div class="form-control-wrap">
                                <span class="badge bg-success">{{ $registration->tr04_status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Total Tests</label>
                            <div class="form-control-wrap">
                                <span class="fw-bold">{{ $tests->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Received Date</label>
                            <div class="form-control-wrap">
                                <span>{{ $registration->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        @if ($registration->tr04_attachment)
                            <img src="{{ asset('public/storage/' . $registration->tr04_attachment) }}" alt="Sample Image"
                                class="img-thumbnail"
                                style="width: 100%; max-width: 200px; height: auto; object-fit: cover;">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label mt-2">
                                <input type="checkbox" id="select-all" class="form-check-input me-2">
                                Select All Available Tests
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8 text-end">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" onclick="bulkAllotSelected()">
                                <em class="icon ni ni-user-check"></em> Allot Selected
                            </button>
                            <button type="button" class="btn btn-warning" onclick="bulkTransferSelected()">
                                <em class="icon ni ni-exchange"></em> Transfer Selected
                            </button>
                            <button type="button" class="btn btn-danger" onclick="bulkRevertSelected()">
                                <em class="icon ni ni-undo"></em> Revert Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests Table -->
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('create_allotment') }}" method="POST" id="individual-allotment-form">
                    @csrf
                    <input type="hidden" name="txt_sample_registration_id"
                        value="{{ $registration->tr04_sample_registration_id }}">

                    <div class="">
                        <table class="table table-orders">
                            <thead class="tb-odr-head">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="header-checkbox" onchange="toggleAllCheckboxes()">
                                    </th>
                                    <th>#</th>
                                    <th>Test Name</th>
                                    <th>Standard/Method</th>
                                    <th>Current Status</th>
                                    <th>Remarks</th>
                                    <th>Assigned To</th>
                                    <th>Allot To</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tests as $index => $test)
                                    <tr class="{{ $test->tr05_status === 'TRANSFERRED' ? 'table-warning' : '' }}">
                                        <td>
                                            @if ($test->canBeAllotted || $test->canBeTransferred)
                                                <input type="checkbox" class="test-checkbox"
                                                    value="{{ $test->tr05_sample_test_id }}"
                                                    data-test-id="{{ $test->tr05_sample_test_id }}">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $test->test->m12_name }}</strong>
                                            @if ($test->tr05_priority && $test->tr05_priority !== 'NORMAL')
                                                <span class="badge {{ $test->getPriorityBadgeClass() }} ms-1">
                                                    {{ $test->tr05_priority }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $test->standard->m15_method ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $test->getStatusBadgeClass() }}">
                                                {{ $test->statusLabel }}
                                            </span>
                                            @if ($test->isTransferPending)
                                                <small class="d-block text-muted mt-1">
                                                    To: {{ $test->transferredToRo->m04_name ?? 'Unknown' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $test->tr05_remark }}</td>
                                        <td>
                                            @if ($test->allotedTo)
                                                <span class="text-success">
                                                    <em class="icon ni ni-user-check"></em>
                                                    {{ $test->allotedTo->m06_name }}
                                                </span>
                                                @if ($test->tr05_alloted_at)
                                                    <small class="d-block text-muted">
                                                        {{ $test->tr05_alloted_at->format('d M Y, h:i A') }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($test->tr05_status === 'TRANSFERRED' && !$test->canAcceptTransfer(Session::get('ro_id')))
                                                <span class="text-muted">Transferred</span>
                                            @elseif($test->canAcceptTransfer(Session::get('ro_id')))
                                                <span class="text-info">Pending Acceptance</span>
                                            @elseif(isset($test->has_result) && $test->has_result)
                                                <span class="text-success fw-bold"><em class="icon ni ni-check-circle"></em> Result Submitted</span>
                                            @elseif(in_array($test->tr05_status, ['ALLOTED', 'IN_PROGRESS']))
                                                @php
                                                    $allottedEmp = $employees->firstWhere('m06_employee_id', $test->m06_alloted_to);
                                                @endphp
                                                <span class="text-primary fw-bold">
                                                    <em class="icon ni ni-user-check"></em> Allotted to {{ $allottedEmp ? $allottedEmp->m06_name : 'Unknown' }}
                                                </span>
                                            @else
                                                <select name="allotments[{{ $test->tr05_sample_test_id }}]"
                                                    class="form-control form-select form-select-sm individual-select"
                                                    data-test-id="{{ $test->tr05_sample_test_id }}">
                                                    <option value="">-- Select Employee --</option>
                                                    @foreach ($employees as $emp)
                                                        <option value="{{ $emp->m06_employee_id }}"
                                                            {{ $test->m06_alloted_to == $emp->m06_employee_id ? 'selected' : '' }}>
                                                            {{ $emp->m06_name }} [{{ $emp->short_name }}]
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if ($test->canAcceptTransfer(Session::get('ro_id')))
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="acceptTransfer({{ $test->tr05_sample_test_id }})">
                                                        <em class="icon ni ni-check"></em> Accept
                                                    </button>
                                                @elseif($test->canBeTransferred)
                                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                                        onclick="showTransferModal({{ $test->tr05_sample_test_id }}, '{{ addslashes($test->test->m12_name) }}')">
                                                        <em class="icon ni ni-exchange"></em> Transfer
                                                    </button>
                                                @endif
                                                @if ($test->transfers->isNotEmpty())
                                                    <button type="button" class="btn btn-outline-info btn-sm"
                                                        onclick="showHistory({{ $test->tr05_sample_test_id }})">
                                                        <em class="icon ni ni-clock"></em> History
                                                    </button>
                                                @endif
                                                @if (in_array($test->tr05_status, ['ALLOTED', 'IN_PROGRESS']) && (!$test->has_result))
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="revertAllotment({{ $test->tr05_sample_test_id }})">
                                                        <em class="icon ni ni-undo"></em> Revert
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <em class="icon ni ni-inbox" style="font-size: 2rem;"></em>
                                                <p class="mt-2">No tests found for this registration</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($tests->where('canBeAllotted', true)->isNotEmpty())
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <em class="icon ni ni-save"></em> Save Individual Allotments
                                </button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div><!-- .nk-block -->
            </div><!-- .nk-content-body -->
        </div><!-- .nk-content-inner -->
    </div><!-- .container-fluid -->

    <!-- Bulk Allot Modal -->
    <div class="modal fade" id="bulkAllotModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Allot Selected Tests</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulk-allot-form" method="POST" action="{{ route('allot_tests') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="selected-test-ids" name="test_ids">
                        <div class="form-group">
                            <label class="form-label">Select Employee:</label>
                            <select name="emp_id" class="form-control form-select" required>
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->m06_employee_id }}">{{ $emp->m06_name }} [{{ $emp->short_name }}]</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info mt-3">
                            <p class="mb-0">Selected Tests: <span id="selected-tests-count">0</span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Allot Tests</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Transfer Modal -->
    <div class="modal fade" id="bulkTransferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Selected Tests</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulk-transfer-form" method="POST" action="{{ route('transfer_tests') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="transfer-test-ids" name="test_ids">
                        <div class="form-group">
                            <label class="form-label">Transfer to RO:</label>
                            <select name="ro_id" class="form-control form-select" required>
                                <option value="">-- Select RO --</option>
                                @foreach ($ros as $ro)
                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" required maxlength="255"
                                placeholder="Enter reason for transfer">
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Additional Remarks (optional)</label>
                            <textarea name="remark" class="form-control" rows="3" maxlength="500"
                                placeholder="Any additional comments..."></textarea>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <p class="mb-1"><strong>Selected Tests: <span id="transfer-tests-count">0</span></strong>
                            </p>
                            <small>These tests will be transferred to the selected RO and current allotments will be
                                cleared.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Transfer Tests</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Individual Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Test</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="single-transfer-form" method="POST" action="{{ route('transfer_tests') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="single-test-id" name="test_ids">
                        <div class="form-group">
                            <label class="form-label">Test Name:</label>
                            <input type="text" class="form-control" id="transfer-test-name" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Transfer to RO:</label>
                            <select name="ro_id" class="form-control form-select" required>
                                <option value="">-- Select RO --</option>
                                @foreach ($ros as $ro)
                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" required maxlength="255">
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Additional Remarks (optional)</label>
                            <textarea name="remark" class="form-control" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Transfer Test</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Accept Transfer Form -->
    <form id="accept-transfer-form" method="POST" action="{{ route('accept_transferred') }}" style="display: none;">
        @csrf
        <input type="hidden" id="accept-test-id" name="test_id">
    </form>

    <!-- Revert Allotment Form -->
    <form id="revert-allotment-form" method="POST" action="{{ route('revert_allotment') }}" style="display: none;">
        @csrf
        <input type="hidden" id="revert-test-id" name="test_id">
    </form>

    <!-- Bulk Revert Form -->
    <form id="bulk-revert-form" method="POST" action="{{ route('bulk_revert_allotment') }}" style="display: none;">
        @csrf
        <input type="hidden" id="bulk-revert-test-ids" name="test_ids">
    </form>

    <script>
        $(document).ready(function() {
            // Select All functionality
            $('#select-all').on('change', function() {
                $('.test-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Update select-all when individual checkboxes change
            $(document).on('change', '.test-checkbox', function() {
                const total = $('.test-checkbox').length;
                const checked = $('.test-checkbox:checked').length;
                $('#select-all').prop('checked', total === checked);
            });
        });

        // Bulk allot selected tests
        function bulkAllotSelected() {
            const selectedTests = getSelectedTests();
            if (selectedTests.length === 0) {
                alert('Please select at least one test');
                return;
            }

            $('#selected-test-ids').val(selectedTests.join(','));
            $('#selected-tests-count').text(selectedTests.length);
            $('#bulkAllotModal').modal('show');
        }

        // Bulk transfer selected tests
        function bulkTransferSelected() {
            const selectedTests = getSelectedTests();
            if (selectedTests.length === 0) {
                alert('Please select at least one test');
                return;
            }

            $('#transfer-test-ids').val(selectedTests.join(','));
            $('#transfer-tests-count').text(selectedTests.length);
            $('#bulkTransferModal').modal('show');
        }

        // Bulk revert selected tests
        function bulkRevertSelected() {
            const selectedTests = getSelectedTests();
            if (selectedTests.length === 0) {
                alert('Please select at least one test to revert.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `This will revert the allotment for ${selectedTests.length} selected test(s) and make them unassigned.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, revert them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#bulk-revert-test-ids').val(selectedTests.join(','));
                    $('#bulk-revert-form').submit();
                }
            });
        }

        // Show individual transfer modal
        function showTransferModal(testId, testName) {
            $('#single-test-id').val(testId);
            $('#transfer-test-name').val(testName);
            $('#transferModal').modal('show');
        }

        // Accept transfer
        function acceptTransfer(testId) {
            if (confirm('Are you sure you want to accept this transferred test?')) {
                $('#accept-test-id').val(testId);
                $('#accept-transfer-form').submit();
            }
        }

        // Revert Allotment
        function revertAllotment(testId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will revert the test allotment and make it unassigned. The analyst will no longer have access to this test.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Revert it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#revert-test-id').val(testId);
                    $('#revert-allotment-form').submit();
                }
            });
        }

        // Get selected test IDs
        function getSelectedTests() {
            return $('.test-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
        }

        // Toggle all checkboxes
        function toggleAllCheckboxes() {
            const headerChecked = $('#header-checkbox').prop('checked');
            $('.test-checkbox').prop('checked', headerChecked);
        }

        // Show transfer history
        function showHistory(testId) {
            // Implementation for showing transfer history
            window.open(`{{ route('history', ':id') }}`.replace(':id', testId), '_blank');
        }
    </script>

    <!-- Send Back to RC Modal -->
    <div class="modal fade" id="sendBackToRCModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('sample.custody.send_back') }}" method="POST" id="sendBackToRCForm">
                    @csrf
                    <input type="hidden" name="sample_id" value="{{ $registration->tr04_sample_registration_id }}">
                    <input type="hidden" name="to_role" value="RC">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white">
                            <em class="icon ni ni-undo me-1"></em> Send Back to Registration Counter (RC)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger alert-icon bg-danger-dim border-danger mb-3">
                            <em class="icon ni ni-alert-circle"></em>
                            <div class="alert-content">
                                <span class="fw-bold">Mandatory Clarification Remark</span><br>
                                <span class="small">State clear instructions or missing details that need correction at the Registration Counter.</span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold text-dark">
                                Sample Reference: <span class="badge bg-primary-dim text-primary fw-bold">#{{ $registration->tr04_reference_id }}</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark" for="rcRemarkTextarea">
                                Clarification Remark <span class="text-danger">*</span>
                            </label>
                            <div class="form-control-wrap">
                                <textarea name="clarification_remark" id="rcRemarkTextarea" class="form-control" rows="4" required minlength="3" placeholder="Explain what needs to be verified or changed by RC (e.g. wrong sample type, missing customer info, incorrect test package)..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm fw-bold">
                            <em class="icon ni ni-send me-1"></em> Confirm & Return to RC
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
