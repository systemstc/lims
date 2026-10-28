@extends('layouts.app_back')
@section('content')
    <style>
        .blink {
            animation: blinker 1.2s linear infinite;
        }

        @keyframes blinker {
            40% {
                opacity: 0;
            }
        }
    </style>
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title mb-0">All Registered Samples</h4>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('view_contract_reviews') }}" class="btn btn-primary me-2">
                                        <em class="ni ni-shield-check me-1"></em> Contract Reviews
                                    </a>
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-light">
                                        <em class="ni ni-back-alt-fill"></em> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="nk-block">
                            <div class="card card-stretch">
                                <div class="card-inner-group">
                                    <div class="card-inner p-0">
                                        <table class="table table-bordered" id="samples-table">
                                            <thead>
                                                <tr>
                                                    <th>Registration ID</th>
                                                    <th>Date</th>
                                                    <th>Customer Name</th>
                                                    <th>BE Number</th>
                                                    <th>Amount</th>
                                                    <th>Payment Status</th>
                                                    <th>Status</th>
                                                    {{-- <th>Tests</th> --}}
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- DataTables will populate this --}}
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
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sample Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="sampleImagePreview" src="" alt="Sample Image" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <!-- Dispatch Modal -->
    <div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dispatch Sample</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="dispatchForm">
                    @csrf
                    <input type="hidden" id="dispatchSampleId" name="sample_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Mode of Dispatch <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select class="form-select" name="mode" required>
                                    <option value="" selected disabled>Select Mode</option>
                                    <option value="By Post">By Post</option>
                                    <option value="By Courier">By Courier</option>
                                    <option value="In Person">In Person</option>
                                    <option value="By Email">By Email</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Person Name <small>(Optional)</small></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="person_name" placeholder="Enter Person Name">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Date of Dispatch</label>
                            <div class="form-control-wrap">
                                <input type="date" class="form-control" name="dispatch_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Post/Article Number <small>(Optional)</small></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="post_article_no" placeholder="Enter Tracking Number">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Dispatch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DataTables Export Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            $('#samples-table').DataTable({
                processing: true,
                serverSide: true,
                // Added p-3 to container rows to prevent overflow and provide spacing from edges
                dom: '<"row justify-content-between align-items-center p-3"<"col-md-6 text-start"B><"col-md-6"f>>rt<"row justify-content-between align-items-center mt-3 p-3"<"col-md-6"i><"col-md-6"p>>',
                buttons: [{
                    extend: 'copy',
                    className: 'btn btn-white btn-outline-light'
                }, {
                    extend: 'csv',
                    className: 'btn btn-white btn-outline-light'
                }, {
                    extend: 'excel',
                    className: 'btn btn-white btn-outline-light'
                }, {
                    extend: 'pdf',
                    className: 'btn btn-white btn-outline-light'
                }, {
                    extend: 'print',
                    className: 'btn btn-white btn-outline-light'
                }],
                ajax: {
                    url: "{{ route('view_registered_samples') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'registration_id',
                        name: 'tr04_reference_id'
                    },
                    {
                        data: 'created_date',
                        name: 'created_at'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer.m07_name'
                    },
                    {
                        data: 'be_no',
                        name: 'tr04_be_no'
                    },
                    {
                        data: 'amount',
                        name: 'tr04_total_charges'
                    },
                    {
                        data: 'payment_status',
                        name: 'tr04_payment_status'
                    },
                    {
                        data: 'status',
                        name: 'tr04_progress'
                    },
                    // {
                    //    data: 'total_tests',
                    //    name: 'total_tests',
                    //    searchable: false
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: "No registered samples found",
                    zeroRecords: "No matching records found"
                }
            });
            $(document).on('click', '.view-image', function() {
                var imageUrl = $(this).data('image');
                $('#sampleImagePreview').attr('src', imageUrl);
                $('#imageModal').modal('show');
            });
        });

        const upgradeToTatkalRoute = "{{ route('upgrade_to_tatkal') }}";
        const dispatchRoute = "{{ route('dispatch_sample') }}";

        $(document).on('click', '.upgrade-to-tatkal', function(e) {
            e.preventDefault();
            let recordId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure to Upgrade it to Tatkal?',
                text: 'Upgrade the Priority?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, upgrade it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: upgradeToTatkalRoute,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: recordId,
                        },
                        success: function(data) {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sample priority upgraded to Tatkal!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });

        // Open Dispatch Modal
        $(document).on('click', '.dispatch-btn', function(e) {
            e.preventDefault();
            let sampleId = $(this).data('id');
            $('#dispatchSampleId').val(sampleId);
            $('#dispatchModal').modal('show');
        });

        // Handle Dispatch Form Submission
        $('#dispatchForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            
            $.ajax({
                url: dispatchRoute,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#dispatchModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sample Dispatched!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#samples-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to dispatch sample. Please try again.', 'error');
                }
            });
        });

        function openForwardToManagerModal(sampleId, refId, remark) {
            document.getElementById('forwardSampleId').value = sampleId;
            document.getElementById('forwardSampleRefDisplay').innerText = refId;
            document.getElementById('forwardReasonDisplay').innerText = `"${remark}"`;
            document.getElementById('forwardRemarkTextarea').value = '';
            const modalEl = document.getElementById('forwardToManagerModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    </script>

    <!-- Clarify and Forward to Manager Modal -->
    <div class="modal fade" id="forwardToManagerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('sample.custody.forward') }}" method="POST" id="forwardToManagerForm">
                    @csrf
                    <input type="hidden" name="sample_id" id="forwardSampleId" value="">
                    <input type="hidden" name="to_role" value="Manager">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">
                            <em class="icon ni ni-send me-1"></em> Clarify & Forward to Manager
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border border-warning mb-3">
                            <span class="fw-bold text-dark d-block mb-1">Reason for Return by Manager:</span>
                            <span id="forwardReasonDisplay" class="text-dark font-monospace fs-12px"></span>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold text-dark">
                                Sample Reference: <span id="forwardSampleRefDisplay" class="badge bg-primary-dim text-primary fw-bold"></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-bold text-dark" for="forwardRemarkTextarea">
                                Clarification Notes (Optional)
                            </label>
                            <div class="form-control-wrap">
                                <textarea name="remark" id="forwardRemarkTextarea" class="form-control" rows="3" placeholder="Provide any additional clarifications or adjustments made..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold">
                            <em class="icon ni ni-send me-1"></em> Forward to Manager
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
