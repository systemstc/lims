@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Simples for Result Entry</h4>
                                {{-- <a data-bs-toggle="modal" data-bs-target="#createDepartment" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em> &nbsp; Enter measurement
                                </a> --}}
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sample ID</th>
                                            <th>Tests</th>
                                            <th>Priority</th>
                                            <th>Delay Time</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($samples as $key => $sample)
                                            @if (optional($sample->registration->testResult->first())->tr07_result_status != 'RESULTED')
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sample->reference_id }}</td>
                                                    <td>
                                                        <strong>Total:</strong> {{ $sample->total_tests }} <br>
                                                        <span class="text-success">Completed:</span>
                                                        {{ $sample->completed_tests }} <br>
                                                        <span class="text-warning">Pending:</span>
                                                        {{ $sample->pending_tests }}
                                                    </td>
                                                    <td>
                                                        <strong
                                                            class="{{ $sample->priority == 'Urgent' ? 'text-danger' : '-info' }}">
                                                            {{ $sample->priority }}
                                                    </strong>
                                                    </td>
                                                    @php
                                                        $delay = $sample->delay_days ?? 0;
                                                        if ($delay > 3) {
                                                            $cappedDelay = min($delay, 30);
                                                            $red = 50 + intval(($cappedDelay / 30) * 205);
                                                            $color = "rgb($red, 0, 0)";
                                                        } else {
                                                            $color = 'rgb(255, 200, 0)';
                                                        }
                                                    @endphp
                                                    <td style="color: {{ $color }}; font-weight: bold;">
                                                        {{ $sample->delay_days }} days
                                                    </td>
                                                    <td>{{ $sample->created_at?->format('d-m-Y') }}</td>
                                                    <td>
                                                        @if ($sample->pending_tests == 0)
                                                            <span class="badge bg-success">All Completed</span>
                                                        @else
                                                            <span class="badge bg-warning">In Progress</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($sample->pending_tests == 0)
                                                            <a href="{{ route('template_manuscript', $sample->sample_id) }}"
                                                                class="btn btn-sm btn-outline-primary">
                                                                Result Entry
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
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
