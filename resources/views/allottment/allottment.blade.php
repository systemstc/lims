@extends('layouts.app_back')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">Allot Tests</h4>
                <p>Select employees for each test and allot them or transfer to another RO.</p>
            </div>
        </div>

        <!-- Bulk Actions Card -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <h5 class="card-title">Bulk Actions</h5>
                <div class="row gy-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Allot All Tests To:</label>
                            <div class="input-group">
                                <select id="bulk-employee" class="form-control form-select">
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->m06_employee_id }}">{{ $emp->m06_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary" onclick="allotAllTests()">
                                    <em class="icon ni ni-users"></em> Allot All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Transfer All Tests To RO:</label>
                            <div class="input-group">
                                <select id="bulk-ro" class="form-control form-select">
                                    <option value="">-- Select RO --</option>
                                    @foreach ($ros as $item)
                                        <option value="{{ $item->m04_ro_id }}">{{ $item->m04_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-warning" onclick="openTransferAllModal()">Transfer All
                                    Tests</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Allotment Card -->
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label" for="select-all">
                                Select All Tests
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="showBulkAllotModal()">
                                <em class="icon ni ni-user-add"></em> Allot Selected
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="showBulkTransferModal()">
                                <em class="icon ni ni-exchange"></em> Transfer Selected
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('create_allotment') }}" method="POST" id="individual-allotment-form">
                    @csrf
                    <input type="hidden" name="txt_sample_registration_id"
                        value="{{ $registration->tr04_sample_registration_id }}">

                    <div class="row gy-4">
                        <div class="col-12">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="header-checkbox" onchange="toggleAllCheckboxes()">
                                        </th>
                                        <th>#</th>
                                        <th>Test</th>
                                        <th>Standard</th>
                                        {{-- <th>Primary Tests</th>
                                        <th>Secondary Tests</th> --}}
                                        <th>Current Status</th>
                                        <th>Allot To</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tests as $index => $test)
                                        <tr>
                                            <td>
                                                @if ($test->tr05_status == 'TRANSFERRED')
                                                    -
                                                @else
                                                    <input type="checkbox" class="test-checkbox"
                                                        value="{{ $test->tr05_sample_test_id }}"
                                                        data-test-id="{{ $test->tr05_sample_test_id }}">
                                                @endif
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $test->test->m12_name }}</td>
                                            <td>{{ $test->standard->m15_method ?? '-' }}</td>
                                            {{-- <td>
                                                @if (!empty($test->primary_tests))
                                                    @foreach ($test->primary_tests as $pt)
                                                        <span class="badge bg-info">{{ $pt->m16_name }}</span>
                                                    @endforeach
                                                @else
                                                    <span>-</span>
                                                @endif

                                            </td>
                                            <td>
                                                @if (!empty($test->secondary_tests))
                                                    @foreach ($test->secondary_tests as $st)
                                                        <span class="badge bg-success">
                                                            {{ $st->m17_name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td> --}}
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'PENDING' => 'bg-warning',
                                                        'ALLOTED' => 'bg-info',
                                                        'IN_PROGRESS' => 'bg-primary',
                                                        'COMPLETED' => 'bg-success',
                                                        'VERIFIED' => 'bg-dark',
                                                        'REPORTED' => 'bg-secondary',
                                                        'TRANSFERRED' => 'bg-success',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $statusColors[$test->tr05_status] ?? 'bg-light' }}">
                                                    {{ ucfirst(strtolower($test->tr05_status)) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($test->tr05_status == 'TRANSFERRED')
                                                    -
                                                @else
                                                    <select name="allotments[{{ $test->tr05_sample_test_id }}]"
                                                        class="form-control form-select individual-select"
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
                                                @if ($test->tr05_status === 'TRANSFERRED' && $test->m04_transferred_to == Session::get('ro_id'))
                                                    <form action="{{ route('accept_transferred') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="test_id"
                                                            value="{{ $test->tr05_sample_test_id }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success">Accept</button>
                                                    </form>
                                                @elseif ($test->tr05_status == 'TRANSFERRED')
                                                    -
                                                @else
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-warning btn-sm"
                                                            onclick="showTransferModal({{ $test->tr05_sample_test_id }}, '{{ $test->test->m12_name }}')">
                                                            <em class="icon ni ni-exchange"></em>
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <em class="icon ni ni-save"></em> Save Individual Allotments
                            </button>
                        </div>
                    </div>
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
                            <label class="form-label">Select RO:</label>
                            <select name="ro_id" class="form-control form-select" required>
                                <option value="">-- Select RO --</option>
                                @foreach ($ros as $ro)
                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" required>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Remark (optional)</label>
                            <textarea name="remark" class="form-control"></textarea>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <p class="mb-0">Selected Tests: <span id="transfer-tests-count">0</span></p>
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
                            <label class="form-label">Test:</label>
                            <input type="text" class="form-control" id="transfer-test-name" readonly>
                        </div>
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
                            <input type="text" name="reason" class="form-control" required>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Remark (optional)</label>
                            <textarea name="remark" class="form-control"></textarea>
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

    <!-- Bulk Transfer All Modal -->
    <div class="modal fade" id="transferAllModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('transfer_tests') }}" id="bulkTransferForm">
                    @csrf
                    <input type="hidden" name="test_ids" id="bulk-test-ids">
                    <input type="hidden" name="ro_id" id="bulk-ro-id">

                    <div class="modal-header">
                        <h5 class="modal-title">Transfer All Tests</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" maxlength="255" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remark</label>
                            <textarea name="remark" class="form-control" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    <script>
        $(document).ready(function() {
            $('#select-all').on('change', function() {
                $('.test-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Bulk Allot All Tests
            $('#btn-allot-all').on('click', function() {
                let employeeId = $('#bulk-employee').val();
                if (!employeeId) {
                    alert('Please select an employee');
                    return;
                }

                $('.test-checkbox').prop('checked', true);
                $('.individual-select').val(employeeId);

                alert(
                    'All tests have been set to allot to the selected employee. Click "Save Individual Allotments" to confirm.'
                );
            });

            // Bulk Transfer All Tests
            $('#btn-transfer-all').on('click', function() {
                let roId = $('#bulk-ro').val();
                if (!roId) {
                    alert('Please select an RO');
                    return;
                }

                let allTestIds = $('.test-checkbox').map(function() {
                    return $(this).val();
                }).get();

                if (allTestIds.length === 0) {
                    alert('No tests available to transfer');
                    return;
                }

                $('#bulk-test-ids').val(allTestIds.join(','));
                $('#bulk-ro-id').val(roId);

                $('#transferAllModal').modal('show');
            });

            // Bulk Allot Selected Tests
            $('#btn-bulk-allot').on('click', function() {
                let checked = $('.test-checkbox:checked');
                if (!checked.length) {
                    alert('Please select at least one test');
                    return;
                }

                let testIds = checked.map(function() {
                    return $(this).val();
                }).get();

                $('#selected-test-ids').val(testIds.join(','));
                $('#selected-tests-count').text(testIds.length);

                $('#bulkAllotModal').modal('show');
            });

            // Bulk Transfer Selected Tests
            $('#btn-bulk-transfer').on('click', function() {
                let checked = $('.test-checkbox:checked');
                if (!checked.length) {
                    alert('Please select at least one test');
                    return;
                }

                let testIds = checked.map(function() {
                    return $(this).val();
                }).get();

                $('#transfer-test-ids').val(testIds.join(','));
                $('#transfer-tests-count').text(testIds.length);

                $('#bulkTransferModal').modal('show');
            });

            // Individual Transfer
            $('.btn-transfer').on('click', function() {
                let testId = $(this).data('id');
                let testName = $(this).data('name');

                $('#single-test-id').val(testId);
                $('#transfer-test-name').val(testName);

                $('#transferModal').modal('show');
            });
        });
    </script>
@endsection
