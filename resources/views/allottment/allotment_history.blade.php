@extends('layouts.app_back')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                <h4 class="nk-block-title">Test History & Transfer Log</h4>
                <button type="button" class="btn btn-secondary" onclick="window.close()">
                    <em class="icon ni ni-cross"></em> Close
                </button>
            </div>
        </div>

        <!-- Test Information -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Test Name</label>
                            <div class="form-control-wrap">
                                <strong>{{ $test->test->m12_name }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Registration ID</label>
                            <div class="form-control-wrap">
                                <strong>#{{ $test->tr04_sample_registration_id }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Current Status</label>
                            <div class="form-control-wrap">
                                <span class="badge {{ $test->getStatusBadgeClass() }}">
                                    {{ $test->statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Assignment -->
        @if ($test->m06_alloted_to)
            <div class="card card-bordered mb-4">
                <div class="card-header">
                    <h6 class="card-title">Current Assignment</h6>
                </div>
                <div class="card-inner">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-user-check text-success me-2"></em>
                                <div>
                                    <strong>{{ $test->allotedTo->m06_name }}</strong>
                                    <small class="d-block text-muted">
                                        Allotted on:
                                        {{ $test->tr05_alloted_at ? $test->tr05_alloted_at->format('d M Y, h:i A') : 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if ($test->tr05_priority && $test->tr05_priority !== 'NORMAL')
                                <span class="badge {{ $test->getPriorityBadgeClass() }}">
                                    {{ $test->tr05_priority }} Priority
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Transfer History -->
        <div class="card card-bordered">
            <div class="card-header">
                <h6 class="card-title">Transfer History</h6>
            </div>
            <div class="card-inner">
                @if ($history->count() > 0)
                    <div class="timeline">
                        @foreach ($history as $transfer)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $transfer->m06_received_by ? 'success' : 'warning' }}">
                                    <em class="icon ni ni-{{ $transfer->m06_received_by ? 'check' : 'clock' }}"></em>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">
                                            Transfer: {{ $transfer->fromRo->m04_name ?? 'Unknown' }}
                                            → {{ $transfer->toRo->m04_name ?? 'Unknown' }}
                                        </h6>
                                        <span class="badge bg-{{ $transfer->m06_received_by ? 'success' : 'warning' }}">
                                            {{ $transfer->m06_received_by ? 'Completed' : 'Pending' }}
                                        </span>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Initiated By:</small>
                                            <strong>{{ $transfer->transferredBy->m06_name ?? 'System' }}</strong>
                                            <small class="d-block text-muted">
                                                {{ $transfer->tr06_transferred_at->format('d M Y, h:i A') }}
                                            </small>
                                        </div>
                                        @if ($transfer->m06_received_by)
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Received By:</small>
                                                <strong>{{ $transfer->receivedBy->m06_name ?? 'Unknown' }}</strong>
                                                <small class="d-block text-muted">
                                                    {{ $transfer->tr06_received_at ? $transfer->tr06_received_at->format('d M Y, h:i A') : 'N/A' }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($transfer->tr06_reason)
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Reason:</small>
                                            <p class="mb-1">{{ $transfer->tr06_reason }}</p>
                                        </div>
                                    @endif

                                    @if ($transfer->tr06_remark)
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Remarks:</small>
                                            <p class="mb-0 text-muted">{{ $transfer->tr06_remark }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <em class="icon ni ni-info" style="font-size: 2rem; color: #ccc;"></em>
                        <p class="text-muted mt-2">No transfer history found for this test</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e3e7fe;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -18px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #6c5ce7;
        }

        .bg-warning {
            background-color: #f39c12 !important;
        }

        .bg-success {
            background-color: #27ae60 !important;
        }
    </style>
@endsection
