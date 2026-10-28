@extends('layouts.app_back')

@section('content')
<style>
    .metric-card {
        border-radius: 8px;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
        user-select: none;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .metric-card.active-card {
        transform: translateY(-2px);
        box-shadow: 0 0 0 3px rgba(15, 57, 89, 0.25), 0 6px 14px rgba(0,0,0,0.12) !important;
        border-width: 2px !important;
        border-color: #0f3959 !important;
    }
    .metric-card.border-warning.active-card {
        box-shadow: 0 0 0 3px rgba(244, 189, 44, 0.35), 0 6px 14px rgba(0,0,0,0.12) !important;
        border-color: #f4bd1a !important;
    }
    .metric-card.border-success.active-card {
        box-shadow: 0 0 0 3px rgba(30, 224, 172, 0.35), 0 6px 14px rgba(0,0,0,0.12) !important;
        border-color: #1ee0ac !important;
    }
    .metric-card.border-info.active-card {
        box-shadow: 0 0 0 3px rgba(9, 148, 240, 0.35), 0 6px 14px rgba(0,0,0,0.12) !important;
        border-color: #0994f0 !important;
    }
    .metric-card.border-primary.active-card {
        box-shadow: 0 0 0 3px rgba(101, 118, 255, 0.35), 0 6px 14px rgba(0,0,0,0.12) !important;
        border-color: #6576ff !important;
    }
    .metric-card.border-danger.active-card {
        box-shadow: 0 0 0 3px rgba(232, 83, 71, 0.35), 0 6px 14px rgba(0,0,0,0.12) !important;
        border-color: #e85347 !important;
    }
    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .metric-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8094ae;
        font-weight: 600;
    }
    .badge-iso {
        background: #0f3959;
        color: #ffffff;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 7px;
        border-radius: 4px;
        letter-spacing: 0.3px;
        display: inline-block;
    }
    .table-contract th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #526484;
        background-color: #f8f9fc;
        border-top: 1px solid #dbdfea;
        white-space: nowrap;
    }
    .table-contract td {
        vertical-align: middle;
        font-size: 12.5px;
    }
</style>

<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="components-preview wide-xxl mx-auto">
                
                {{-- Header Block --}}
                <div class="nk-block-head nk-block-head-sm mb-3">
                    <div class="nk-block-between align-items-center">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title mb-0">Contract Review & Sample Feasibility</h3>
                            <div class="nk-block-des text-soft">
                                <p class="mb-0">Technical review of sample requests, testing capability, resource feasibility, and decision rules before lab allotment.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-light bg-white text-dark me-2" id="btnRefreshTable">
                                    <em class="ni ni-reload-alt"></em> Refresh
                                </button>
                                <a href="{{ route('view_registered_samples') }}" class="btn btn-outline-primary">
                                    <em class="ni ni-list-thumb"></em> All Registered Samples
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Metric Overview Cards --}}
                <div class="nk-block mb-3">
                    <div class="row g-2">
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card card-bordered metric-card bg-white active-card" data-status="all" onclick="setQuickFilter('all')">
                                <div class="card-inner p-2 p-sm-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="metric-label">Total Samples</div>
                                            <div class="metric-value text-dark" id="metric-total">{{ $metrics['total'] ?? 0 }}</div>
                                        </div>
                                        <div class="icon-circle bg-light text-primary">
                                            <em class="icon ni ni-layers"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card card-bordered metric-card bg-white border-warning" data-status="PENDING" onclick="setQuickFilter('PENDING')">
                                <div class="card-inner p-2 p-sm-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="metric-label text-warning">Pending Review</div>
                                            <div class="metric-value text-warning" id="metric-pending">{{ $metrics['pending'] ?? 0 }}</div>
                                        </div>
                                        <div class="icon-circle bg-warning-dim text-warning">
                                            <em class="icon ni ni-clock"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card card-bordered metric-card bg-white border-success" data-status="ACCEPTED" onclick="setQuickFilter('ACCEPTED')">
                                <div class="card-inner p-2 p-sm-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="metric-label text-success">Feasible / Accepted</div>
                                            <div class="metric-value text-success" id="metric-accepted">{{ $metrics['accepted'] ?? 0 }}</div>
                                        </div>
                                        <div class="icon-circle bg-success-dim text-success">
                                            <em class="icon ni ni-check-circle"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card card-bordered metric-card bg-white border-info" data-status="ACCEPTED_WITH_DEVIATION" onclick="setQuickFilter('ACCEPTED_WITH_DEVIATION')">
                                <div class="card-inner p-2 p-sm-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="metric-label text-info">Accepted w/ Dev.</div>
                                            <div class="metric-value text-info" id="metric-deviation">{{ $metrics['deviation'] ?? 0 }}</div>
                                        </div>
                                        <div class="icon-circle bg-info-dim text-info">
                                            <em class="icon ni ni-alert-circle"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card card-bordered metric-card bg-white border-primary" data-status="CLARIFICATION_REQUIRED" onclick="setQuickFilter('CLARIFICATION_REQUIRED')">
                                <div class="card-inner p-2 p-sm-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="metric-label text-primary">Clarification Req.</div>
                                            <div class="metric-value text-primary" id="metric-clarification">{{ $metrics['clarification'] ?? 0 }}</div>
                                        </div>
                                        <div class="icon-circle bg-primary-dim text-primary">
                                            <em class="icon ni ni-chat-circle"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card card-bordered metric-card bg-white border-danger" data-status="REJECTED" onclick="setQuickFilter('REJECTED')">
                                <div class="card-inner p-2 p-sm-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="metric-label text-danger">Not Feasible / Rej.</div>
                                            <div class="metric-value text-danger" id="metric-rejected">{{ $metrics['rejected'] ?? 0 }}</div>
                                        </div>
                                        <div class="icon-circle bg-danger-dim text-danger">
                                            <em class="icon ni ni-cross-circle"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar & Data Table Card --}}
                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner py-3 border-bottom bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-3">
                                    <div class="form-group mb-0">
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left">
                                                <em class="icon ni ni-filter"></em>
                                            </div>
                                            <select class="form-select" id="filterStatus">
                                                <option value="all" selected>All Review Statuses</option>
                                                <option value="PENDING">Pending Review Only</option>
                                                <option value="ACCEPTED">Accepted / Feasible</option>
                                                <option value="ACCEPTED_WITH_DEVIATION">Accepted With Deviation</option>
                                                <option value="CLARIFICATION_REQUIRED">Clarification Required</option>
                                                <option value="REJECTED">Rejected / Not Feasible</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="form-group mb-0">
                                        <select class="form-select" id="filterPriority">
                                            <option value="all">All Priorities</option>
                                            <option value="Normal">Normal</option>
                                            <option value="Tatkal">Tatkal (Urgent)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-group mb-0">
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left">
                                                <em class="icon ni ni-calendar"></em>
                                            </div>
                                            <input type="date" class="form-control" id="filterDate" placeholder="Date of Receipt">
                                        </div>
                                    </div>
                                </div>
                                @if(isset($isAdmin) && $isAdmin)
                                    <div class="col-6 col-md-2">
                                        <div class="form-group mb-0">
                                            <select class="form-select" id="filterRo">
                                                <option value="">All Regional Labs</option>
                                                @foreach($ros as $ro)
                                                    <option value="{{ $ro->m04_ro_id }}">{{ $ro->m04_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12 col-md-2 text-md-end">
                                    <button type="button" class="btn btn-outline-secondary w-100" id="btnResetFilters">
                                        <em class="ni ni-undo"></em> Reset Filters
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-contract mb-0" id="contractReviewsTable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 120px;">Reference & Job ID</th>
                                            <th style="min-width: 170px;">Customer Details</th>
                                            <th style="min-width: 190px;">Sample Description</th>
                                            <th style="width: 140px;">Tests Scope</th>
                                            <th style="width: 110px;">Priority & Target Date</th>
                                            <th style="width: 130px;">ISO Review Status</th>
                                            <th style="width: 100px;">Total Charges</th>
                                            <th style="width: 70px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Server-side DataTables --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Quick Accept Modal --}}
<div class="modal fade" id="quickAcceptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <h5 class="modal-title mb-0">ISO 17025 Contract Review Sign-Off</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAcceptForm">
                @csrf
                <input type="hidden" id="acceptSampleId" name="sample_id">
                <div class="modal-body py-3">
                    <div class="alert alert-success alert-pro border p-2 mb-3 fs-12px">
                        <em class="ni ni-check-circle me-1"></em> Confirming that all Clause 7.1 customer requirements, validated methods, equipment, environmental conditioning, and technical competence are satisfied for Sample <strong id="acceptSampleRef"></strong>.
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Decision Rule (Cl. 7.1.3)</label>
                        <select class="form-select" name="decision_rule">
                            <option value="SIMPLE_ACCEPTANCE" selected>Simple Acceptance (Binary Decision, 95% Confidence)</option>
                            <option value="GUARD_BAND_ILAC_G8">Guard-banded Acceptance (ILAC G8 / Guardband w=U)</option>
                            <option value="CUSTOMER_SPECIFIED">Customer Specified Rule</option>
                            <option value="NOT_APPLICABLE">Not Applicable (No Statement of Conformity)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Sample Physical Condition</label>
                        <select class="form-select" name="sample_condition">
                            <option value="INTACT_SEALED" selected>Intact & Sealed</option>
                            <option value="INTACT_UNSEALED">Intact (Unsealed)</option>
                            <option value="DAMAGED_ACCEPTABLE">Slightly Damaged (Acceptable for Testing)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Lab In-Charge Findings & Review Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remarks" rows="3" placeholder="Enter technical evaluation findings, method validation details, or review remarks..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="btnConfirmAccept">
                        <em class="ni ni-check-circle me-1"></em> Sign-Off & Accept Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Clarification Request Modal --}}
<div class="modal fade" id="clarificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <h5 class="modal-title mb-0">Request Customer Clarification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="clarificationForm">
                @csrf
                <input type="hidden" id="clarificationSampleId" name="sample_id">
                <div class="modal-body py-3">
                    <div class="alert alert-primary mb-3 p-2 fs-12px">
                        <em class="ni ni-info me-1"></em> This will record a technical clarification query under ISO/IEC 17025 Clause 7.1.2 into the sample audit trail.
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Clarification Subject / Issue <span class="text-danger">*</span></label>
                        <select class="form-select" id="clarificationIssueType" required>
                            <option value="TEST_METHOD_UNCLEAR">Test Standard Method Not Specified or Unclear</option>
                            <option value="SAMPLE_QUANTITY_INSUFFICIENT">Sample Quantity Received is Insufficient for Scope</option>
                            <option value="DECISION_RULE_MISSING">Conformity Statement Requested Without Stating Decision Rule</option>
                            <option value="PACKAGING_DAMAGED">Sample Packaging Damaged / Tampered Upon Receipt</option>
                            <option value="TAT_FEASIBILITY">Requested Turn-around Time Not Feasible by Lab</option>
                            <option value="OTHER">Other Technical Query</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Specific Query Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="clarificationRemarks" rows="4" placeholder="Detail the query to be sent to customer..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnConfirmClarification">
                        <em class="ni ni-send me-1"></em> Log Query & Set Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let contractTable = null;

    // Global quick filter helper
    window.setQuickFilter = function(status) {
        $('.metric-card').removeClass('active-card');
        $('.metric-card[data-status="' + status + '"]').addClass('active-card');
        $('#filterStatus').val(status);
        if (contractTable) {
            contractTable.ajax.reload();
        }
    };

    $(document).ready(function() {
        contractTable = $('#contractReviewsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            autoWidth: false,
            dom: '<"row justify-content-between align-items-center p-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-end"f>>rt<"row justify-content-between align-items-center p-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            ajax: {
                url: "{{ route('view_contract_reviews') }}",
                data: function(d) {
                    d.review_status = $('#filterStatus').val();
                    d.sample_type = $('#filterPriority').val();
                    d.from_date = $('#filterDate').val();
                    d.ro_id = $('#filterRo').length ? $('#filterRo').val() : '';
                },
                error: function(xhr, error, code) {
                    console.error('Contract Reviews AJAX Error:', xhr.responseText || error);
                }
            },
            columns: [
                { data: 'reference_info', name: 'tr04_reference_id' },
                { data: 'customer_info', name: 'customer.m07_name', orderable: false },
                { data: 'sample_info', name: 'tr04_sample_description' },
                { data: 'tests_requested', name: 'tests_requested', orderable: false, searchable: false },
                { data: 'service_tat', name: 'tr04_expected_date' },
                { data: 'review_status', name: 'tr04_contract_review_status', className: 'text-center' },
                { data: 'charges', name: 'tr04_total_charges' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
            language: {
                processing: '<div class="d-flex justify-content-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                emptyTable: "No contract review samples found matching your criteria",
                zeroRecords: "No matching records found"
            }
        });

        // Filter event listeners
        $('#filterStatus').on('change', function() {
            const val = $(this).val();
            $('.metric-card').removeClass('active-card');
            $('.metric-card[data-status="' + val + '"]').addClass('active-card');
            contractTable.ajax.reload();
        });

        $('#filterPriority, #filterDate, #filterRo').on('change', function() {
            contractTable.ajax.reload();
        });

        $('#btnResetFilters').on('click', function() {
            $('#filterStatus').val('all');
            $('#filterPriority').val('all');
            $('#filterDate').val('');
            if ($('#filterRo').length) $('#filterRo').val('');
            $('.metric-card').removeClass('active-card');
            $('.metric-card[data-status="all"]').addClass('active-card');
            contractTable.ajax.reload();
        });

        $('#btnRefreshTable').on('click', function() {
            contractTable.ajax.reload(null, false);
        });

        // Submit Quick Accept
        $('#quickAcceptForm').on('submit', function(e) {
            e.preventDefault();
            const sampleId = $('#acceptSampleId').val();
            const btn = $('#btnConfirmAccept');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: "{{ url('contract-reviews') }}/" + sampleId + "/decision",
                type: 'POST',
                data: $(this).serialize() + '&decision=ACCEPTED',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    btn.prop('disabled', false).html('<em class="ni ni-check-circle me-1"></em> Sign-Off & Accept Contract');
                    if (res.status === 'success' || res.success) {
                        $('#quickAcceptModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Contract Accepted!',
                            text: res.message,
                            timer: 1600,
                            showConfirmButton: false
                        });
                        contractTable.ajax.reload(null, false);
                    } else {
                        Swal.fire('Notice', res.message, 'warning');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<em class="ni ni-check-circle me-1"></em> Sign-Off & Accept Contract');
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to sign-off review', 'error');
                }
            });
        });

        // Submit Clarification Form
        $('#clarificationForm').on('submit', function(e) {
            e.preventDefault();
            const sampleId = $('#clarificationSampleId').val();
            const remarks = $('#clarificationIssueType').val() + ': ' + $('#clarificationRemarks').val();
            const btn = $('#btnConfirmClarification');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Logging...');

            $.ajax({
                url: "{{ url('contract-reviews') }}/" + sampleId + "/decision",
                type: 'POST',
                data: {
                    decision: 'CLARIFICATION_REQUIRED',
                    remarks: remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    btn.prop('disabled', false).html('<em class="ni ni-send me-1"></em> Log Query & Set Status');
                    $('#clarificationModal').modal('hide');
                    Swal.fire({
                        icon: 'info',
                        title: 'Clarification Logged',
                        text: 'Technical query logged under Clause 7.1.2 into the audit trail.',
                        timer: 1600,
                        showConfirmButton: false
                    });
                    contractTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<em class="ni ni-send me-1"></em> Log Query & Set Status');
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to log clarification query', 'error');
                }
            });
        });
    });

    // Global Action Helpers called by dropdown in DataTable
    window.quickAcceptContract = function(id, ref) {
        $('#acceptSampleId').val(id);
        $('#acceptSampleRef').text('#' + ref);
        $('#quickAcceptForm')[0].reset();
        $('#quickAcceptModal').modal('show');
    };

    window.quickClarificationModal = function(id, ref) {
        $('#clarificationSampleId').val(id);
        $('#clarificationForm')[0].reset();
        $('#clarificationModal').modal('show');
    };

    window.deleteContractRegistration = function(id, ref) {
        Swal.fire({
            title: 'Cancel Sample / Contract Review?',
            text: 'Are you sure you want to cancel sample registration #' + ref + '? This will void the contract review.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e85347',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Cancel Registration'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('contract-reviews') }}/" + id + "/delete",
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success' || res.success) {
                            Swal.fire('Cancelled!', res.message, 'success');
                            contractTable.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to cancel sample record.', 'error');
                    }
                });
            }
        });
    };
</script>
@endsection
