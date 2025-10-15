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
                                <a href="{{ url()->previous() }}" class="btn btn-primary">
                                    <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                                </a>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>Ref. No</th>
                                            <th>Received From</th>
                                            <th>Priority</th>
                                            <th>Received At</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($samples as $key => $sample)
                                            <tr>
                                                <td>{{ $sample->registration->tr04_tracker_id }}</td>
                                                <td>{{ $sample->transferredBy?->m04_name }}</td>
                                                <td><b
                                                        class="text-{{ $sample->tr05_priority === 'NORMAL' ? 'success' : 'danger' }}">{{ $sample->tr05_priority }}</b>
                                                </td>
                                                <td>{{ $sample->tr05_transferred_at }}</td>
                                                <td>
                                                    @if ($sample->tr05_status == 'TRANSFERRED')
                                                        <b class="badge badge-dot blink text-danger"> PENDING</b>
                                                    @elseif($sample->tr05_status == 'RECEIVED_ACCEPTED')
                                                    <b class="badge badge-dot text-primary">ACCEPTED</b>
                                                    @else
                                                        <b>{{ $sample->tr05_status }}</b>
                                                    @endif
                                                </td>
                                                <td>{{ $sample->tr05_remark ?? 'N/A' }}</td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                    <ul class="nk-tb-actions gx-1 my-n1">
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown"><em
                                                                        class="icon ni ni-more-h"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li>
                                                                            <a href="#" class="accept-sample-btn btn"
                                                                                data-id="{{ $sample->tr04_sample_registration_id }}">
                                                                                <em
                                                                                    class="icon ni ni-check-circle-cut text-success"></em>
                                                                                Accept
                                                                            </a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $sample->tr04_sample_registration_id }}"
                                                                                data-status="{{ $sample->tr05_status }}"><em
                                                                                    class="icon ni ni-alert text-danger"></em>Edit
                                                                                & Accept</a>
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
        $(document).on('click', '.accept-sample-btn', function(e) {
            e.preventDefault();
            let sampleId = $(this).data('id');
            Swal.fire({
                title: 'Accept Sample?',
                text: 'This will accept the transferred sample and create a new registration.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Accept',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('accept_sample', ':id') }}".replace(':id', sampleId),
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire(
                                response.status.charAt(0).toUpperCase() + response.status
                                .slice(1),
                                response.message,
                                response.status === 'success' ? 'success' : 'warning'
                            ).then(() => window.location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Something went wrong!', 'error');
                        }
                    });

                }
            })
        });
    </script>
@endsection
