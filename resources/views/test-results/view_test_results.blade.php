@extends('layouts.app_back')

@section('content')
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
                                        <li><a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                                                <em class="icon ni ni-caret-left-fill"></em> Back
                                            </a></li>
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
                                        {{-- <div class="col-lg-3">
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
                                            </div> --}}
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
                                                    <input type="date" class="form-control" id="date-to" name="date_to"
                                                        value="{{ request('date_to') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="search">Search</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" class="form-control" id="search" name="search"
                                                        placeholder="Registration Number..."
                                                        value="{{ request('search') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="form-control-wrap">
                                                    <button type="submit" class="btn btn-primary"><em
                                                            class="icon ni ni-search"></em>Search</button>
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
                                        <th>Status</th>
                                        <th>Last Test Date</th>
                                        <th>Total Outputs</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($testResults as $key => $result)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $result->tr04_reference_id }}</td>
                                            <td><strong
                                                    class="text-{{ $result->result_status === 'VERIFIED' ? 'primary' : 'success' }} fw-bold">{{ $result->result_status }}</strong>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($result->last_test_date)->format('d M, Y') }}
                                            </td>
                                            <td>{{ $result->total_tests }}</td>
                                            <td>{{ \Carbon\Carbon::parse($result->created_at)->format('d M, Y h:i A') }}
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
                                                                        @if (optional($result->registration)->tr04_progress != 'DISPATCHED')
                                                                            <a
                                                                                href="{{ route('generate_report', $result->tr04_reference_id) }}">
                                                                                <em class="icon ni ni-file-docs"></em>
                                                                                <span>Generate Report</span>
                                                                            </a>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" class="text-danger" onclick="openSendBackModal('{{ $result->tr04_reference_id }}')">
                                                                            <em class="icon ni ni-undo text-danger"></em>
                                                                            <span>Send Back for Clarification</span>
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
                        @else
                            <div class="text-center py-4">
                                <div class="icon-circle icon-circle-lg bg-primary-dim mb-3">
                                    <em class="icon ni ni-activity text-primary"></em>
                                </div>
                                <h5>No Test Results Found</h5>
                                <p class="text-soft mb-3">
                                    No grouped results match your filters. Try adjusting your search.
                                </p>
                                <!-- <a href="{{ route('create_test_result') }}" class="btn btn-primary">Add Test
                                    Result</a> -->
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Send Back to Verification Officer Modal -->
    <div class="modal fade" id="sendBackToVerificationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('sample.custody.send_back') }}" method="POST" id="sendBackToVerificationForm">
                    @csrf
                    <input type="hidden" name="sample_id" id="modalSampleId" value="">
                    <input type="hidden" name="to_role" value="Verification Officer">
                    <input type="hidden" name="context" value="reporting">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white">
                            <em class="icon ni ni-undo me-1"></em> Send Back to Verification Officer
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger alert-icon bg-danger-dim border-danger mb-3">
                            <em class="icon ni ni-alert-circle"></em>
                            <div class="alert-content">
                                <span class="fw-bold">Mandatory Clarification Remark</span><br>
                                <span class="small">Explain clearly why this sample is being sent back to the <b>Verification Officer</b> so they can take corrective action.</span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold text-dark">
                                Sample Reference: <span id="modalSampleRefDisplay" class="badge bg-primary-dim text-primary fw-bold"></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark" for="verificationRemarkTextarea">
                                Clarification Remark <span class="text-danger">*</span>
                            </label>
                            <div class="form-control-wrap">
                                <textarea name="clarification_remark" id="verificationRemarkTextarea" class="form-control" rows="4" required minlength="3" placeholder="State clear reasons or instructions for the Verification Officer..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm fw-bold">
                            <em class="icon ni ni-send me-1"></em> Confirm & Send Back
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openSendBackModal(refId) {
            document.getElementById('modalSampleId').value = refId;
            document.getElementById('modalSampleRefDisplay').innerText = refId;
            document.getElementById('verificationRemarkTextarea').value = '';
            const modalEl = document.getElementById('sendBackToVerificationModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    </script>
@endsection
