@extends('layouts.app_back')

@section('content')
    <style>
        .completed-card {
            position: relative;
            background-image: repeating-linear-gradient(45deg,
                    rgba(0, 0, 0, 0.05),
                    rgba(0, 0, 0, 0.05) 10px,
                    transparent 10px,
                    transparent 20px);
            background-size: 20px 20px;
            opacity: 0.8;
        }

        .completed-card .card-inner {
            pointer-events: none;
        }

        .completed-card::after {
            content: "COMPLETED";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-20deg);
            font-size: 2rem;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.459);
            text-transform: uppercase;
            pointer-events: none;
        }
    </style>

    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Analyst Dashboard</h3>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Pending Tests -->
                        <div class="col-md-3">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-primary">Pending Samples</h6>
                                    <h2>{{ $pendingTests }}</h2>
                                </div>
                            </div>
                        </div>
                        <!-- Rejected Tests -->
                        <div class="col-md-3">
                            <a href="{{ route('rejected_samples') }}">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-danger">Rejected Samples</h6>
                                    <h2>{{ $rejectedTests }}</h2>
                                </div>
                            </div>
                            </a>
                        </div>
                        <!-- In Progress Tests -->
                        {{-- <div class="col-md-2">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-warning">In Progress</h6>
                                    <h2>{{ $inProgressTests }}</h2>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Completed Today -->
                        <div class="col-md-3">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-success">Completed Today</h6>
                                    <h2>{{ $completedTests }}</h2>
                                </div>
                            </div>
                        </div>

                        <!-- Total Tests -->
                        <div class="col-md-3">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-info">Total Samples</h6>
                                    <h2>{{ $totalSamples }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Allotted Samples -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h5 class="card-title">Recent Allotted Samples</h5>
                            <div class="row g-gs" id="sampleCardsContainer">
                                @if (isset($allottedSamples) && $allottedSamples->count() > 0)
                                    @foreach ($allottedSamples as $sample)
                                        <div class="col-md-4">
                                            <div
                                                class="card card-bordered h-100 @if ($sample->overall_status === 'COMPLETED') completed-card @endif">
                                                <div class="card-inner d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="card-title mb-0">
                                                            Sample ID: {{ $sample->registration->tr04_reference_id }}
                                                        </h6>
                                                        <span class="badge bg-info">
                                                            {{ $sample->test_count }}
                                                            Test{{ $sample->test_count > 1 ? 's' : '' }}
                                                        </span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <span class="text-muted">Overall Status:</span>
                                                        <strong
                                                            class=" ms-1
                                                                @if ($sample->overall_status === 'ALLOTED') text-primary
                                                                @elseif ($sample->overall_status === 'IN_PROGRESS') text-warning
                                                                @elseif ($sample->overall_status === 'COMPLETED') text-success
                                                                @else text-secondary @endif">
                                                            {{ $sample->overall_status }}
                                                        </strong>
                                                    </div>
                                                    @if ($sample->overall_status === 'COMPLETED' && $sample->latest_completed_at != '')
                                                        <p class="small text-muted mb-3">
                                                            Completed At:
                                                            {{ \Carbon\Carbon::parse($sample->latest_completed_at)->format('d M, Y h:i A') }}
                                                        </p>
                                                    @else
                                                        <p class="small text-muted mb-3">
                                                            Latest Allotment:
                                                            {{ \Carbon\Carbon::parse($sample->latest_allotment)->format('d M, Y h:i A') }}
                                                        </p>
                                                    @endif

                                                    <!-- Progress Bar -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <small class="text-muted fw-bold">Progress</small>
                                                            <small
                                                                class="fw-bold">{{ $sample->progress_percentage }}%</small>
                                                        </div>
                                                        <div class="progress"
                                                            style="height: 5px; background-color: #dad9d9; border-radius: 5px; overflow: hidden;">
                                                            <div class="progress-bar 
                                                                @if ($sample->progress_percentage < 50) bg-danger
                                                                @elseif($sample->progress_percentage < 100) bg-warning
                                                                @else bg-success @endif"
                                                                role="progressbar"
                                                                style="width: {{ $sample->progress_percentage }}%; border-radius: 5px;"
                                                                aria-valuenow="{{ $sample->progress_percentage }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="mt-auto d-flex gap-2">
                                                        @if ($sample->overall_status !== 'COMPLETED')
                                                            <a class="btn btn-outline-primary btn-md"
                                                                href="{{ route('view_sample_tests', $sample->tr04_sample_registration_id) }}">
                                                                <i class="ni ni-eye me-1"></i> View Tests
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <p class="text-muted text-center">No samples allotted yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script to hide alert after 3 seconds -->
    <script>
        setTimeout(() => {
            const alert = document.getElementById('session-alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endsection
