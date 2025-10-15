@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Samples Ready for Verification</h4>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table"
                                    data-export-title="Samples Ready for Verification">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Reference ID</th>
                                            <th>Tests</th>
                                            <th>Priority</th>
                                            <th>Resulted At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($samples as $key => $sample)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $sample->reference_id ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>Total:</strong> {{ $sample->total_tests ?? 0 }} <br>
                                                    <span class="text-success">Completed: {{ $sample->completed_tests ?? 0 }}</span>
                                                     <br>
                                                    <span class="text-warning">Pending: {{ $sample->pending_tests ?? 0 }}</span>
                                                    
                                                </td>
                                                <td>
                                                    <strong
                                                        class="{{ ($sample->priority ?? '') == 'Urgent' ? 'text-danger' : 'text-info' }}">
                                                        {{ $sample->priority ?? 'Normal' }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    {{ optional($sample->registration->testResult->first()?->created_at)->format('d-m-Y') ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if (($sample->pending_tests ?? 0) == 0)
                                                        <strong class="text-success">All Completed</strong>
                                                    @else
                                                        <strong class="text-warning">In Progress</strong>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sample->allResulted)
                                                        <a href="{{ route('verify_result', $sample->sample_id) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            Verify Results
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>
@endsection
