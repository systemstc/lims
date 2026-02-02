@extends('layouts.app_back')

@section('title', 'Transferred Samples')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Transferred Samples</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
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
                                            <span class="badge bg-{{ $sample->is_completed ? 'success' : 'warning' }}">
                                                {{ $sample->statuses }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($sample->report_available)
                                                <a href="{{ route('download_remote_report', $sample->report_id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-download"></i> Download Report
                                                </a>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#transferredTable').DataTable();
        });
    </script>
@endpush
