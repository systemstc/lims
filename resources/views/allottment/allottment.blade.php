@extends('layouts.app_back')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head">
                <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                    <h4 class="nk-block-title">Manage Test Allotments</h4>
                    <p>Registration ID: <strong>#{{ $registration->tr04_reference_id }}</strong></p>
                    <a href="{{ route('view_allottment') }}" class="btn btn-primary">
                        <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back.
                    </a>
                </div>
            </div>
        </div>
        {{-- @dd($registration) --}}
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
                            <img src="{{ asset('storage/' . $registration->tr04_attachment) }}" alt="Sample Image"
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">
                                <input type="checkbox" id="select-all" class="form-check-input me-2">
                                Select All Available Tests
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Bulk Allot To:</label>
                            <select id="bulk-employee" class="form-control form-select">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->m06_employee_id }}">{{ $emp->m06_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Bulk Transfer To:</label>
                            <select id="bulk-ro" class="form-control form-select">
                                <option value="">-- Select RO --</option>
                                @foreach ($ros as $ro)
                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-primary" onclick="bulkAllotSelected()">
                                <em class="icon ni ni-user-check"></em> Allot Selected
                            </button>
                            <button type="button" class="btn btn-warning" onclick="bulkTransferSelected()">
                                <em class="icon ni ni-exchange"></em> Transfer Selected
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="header-checkbox" onchange="toggleAllCheckboxes()">
                                    </th>
                                    <th>#</th>
                                    <th>Test Name</th>
                                    <th>Standard/Method</th>
                                    <th>Current Status</th>
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
                                            @else
                                                <select name="allotments[{{ $test->tr05_sample_test_id }}]"
                                                    class="form-control form-select form-select-sm individual-select"
                                                    data-test-id="{{ $test->tr05_sample_test_id }}">
                                                    <option value="">-- Select Employee --</option>
                                                    @foreach ($employees as $emp)
                                                        <option value="{{ $emp->m06_employee_id }}"
                                                            {{ $test->m06_alloted_to == $emp->m06_employee_id ? 'selected' : '' }}>
                                                            {{ $emp->m06_name }}
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
    </div>

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
                                    <option value="{{ $emp->m06_employee_id }}">{{ $emp->m06_name }}</option>
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
@endsection
