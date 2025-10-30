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
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title mb-0">All Registered Samples</h4>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                                    <em class="ni ni-back-alt-fill"></em> Back
                                </a>
                            </div>
                        </div>
                        <div class="nk-block">
                            <div class="card card-stretch">
                                <div class="card-inner-group">
                                    <div class="card-inner p-0">
                                        <table class="table" id="samples-table">
                                            <thead>
                                                <tr>
                                                    <th>Sample ID</th>
                                                    <th>Date</th>
                                                    <th>Sample Description</th>
                                                    <th>Sample Image</th>
                                                    <th>Priority</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Tests</th>
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

    <script>
        $(document).ready(function() {
            $('#samples-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('view_registered_samples') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'sample_id',
                        name: 'sample_id'
                    },
                    {
                        data: 'created_date',
                        name: 'created_at'
                    },
                    {
                        data: 'sample_description',
                        name: 'sample_description'
                    },
                    {
                        data: 'sanple_image',
                        name: 'sanple_image',
                        render: function(data, type, row) {
                            if (data) {
                                return `<button class="btn view-image" data-image="${data}">
                                <em class="icon ni ni-eye-fill"></em></button>`;
                            } else {
                                return `<span class="text-muted">No Image</span>`;
                            }
                        }
                    },

                    {
                        data: 'sample_type',
                        name: 'sample_type'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'total_tests',
                        name: 'total_tests'
                    },
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
    </script>
@endsection
