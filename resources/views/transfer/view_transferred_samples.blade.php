@extends('layouts.app_back')

@section('title', 'Transferred Samples')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-bordered card-preview">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Transferred Samples</h4>
                </div>
                <div class="card-inner">
                    <div class="table">
                        <table class="datatable-init-export wrap table" data-export-title="Export" id="transferredTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sample ID</th>
                                    <th>Customer</th>
                                    <th>Transferred To (RO)</th>
                                    <th>Date</th>
                                    <th>Tests</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedSamples as $sample)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sample->tr04_reference_id }}</td>
                                        <td>{{ $sample->customer_name }}</td>
                                        <td>{{ $sample->to_ro_name }}</td>
                                        <td>{{ $sample->transferred_at->format('d M, Y h:i A') }}</td>
                                        <td>{{ $sample->tests }}</td>
                                        <td>
                                            <span
                                                class="text-{{ $sample->is_completed ? 'success' : 'warning' }}"><strong>{{ strtoupper($sample->statuses) }}</strong></span>
                                        </td>
                                        <td>
                                            @if ($sample->report_available)
                                                <a href="{{ route('download_remote_report', $sample->report_id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <em class="icon ni ni-download"></em> Download Report
                                                </a>
                                            @elseif ($sample->statuses === 'Transferred')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="cancelTransfer({{ $sample->tr04_sample_registration_id }}, {{ $sample->to_ro_id }}, '{{ $sample->tr04_reference_id }}')">
                                                    <em class="icon ni ni-cross"></em> Cancel
                                                </button>
                                            @elseif ($sample->statuses === 'Cancelled')
                                                <span class="text-danger fw-bold small">Cancelled</span>
                                            @else
                                                <span class="text-muted small">Pending Report</span>
                                            @endif
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#transferredTable').DataTable();
        });

        function cancelTransfer(sampleId, toRoId, referenceId) {
            Swal.fire({
                title: 'Cancel Transfer?',
                text: "Are you sure you want to cancel the transfer for sample " + referenceId + "? Tests will be returned to your allotment queue.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('cancel_transfer') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            sample_registration_id: sampleId,
                            to_ro_id: toRoId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Cancelled!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Something went wrong.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                xhr.responseJSON?.message || 'Failed to cancel transfer.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endsection
