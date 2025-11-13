@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Rejected Samples</h3>
                        </div>
                    </div>
                </div>

                <!-- Recent Allotted Samples -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h5 class="card-title">Rejected Samples</h5>
                            <div class="row g-gs" id="sampleCardsContainer">
                                {{-- @dd($rejectedSamples) --}}
                                @if (isset($rejectedSamples) && $rejectedSamples->count() > 0)
                                    @foreach ($rejectedSamples as $sample)
                                        <div class="col-md-4">
                                            <div class="card card-bordered h-100">
                                                <div class="card-inner d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="card-title mb-0">
                                                            Sample ID: {{ $sample->registration->tr04_reference_id }}
                                                        </h6>
                                                        <span class="badge bg-info">
                                                            {{ $sample->total_tests }}
                                                            Test{{ $sample->total_tests > 1 ? 's' : '' }}
                                                        </span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <span class="text-muted">Overall Status:</span>
                                                        <strong
                                                            class=" ms-1
                                                                @if ($sample->overall_status === 'REJECTED') text-danger
                                                                @elseif ($sample->overall_status === 'ALLOTED') text-primary
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
                                                            <div class="progress">
                                                                <div class="progress-bar 
                                                                    @if ($sample->overall_status === 'REJECTED') bg-danger
                                                                    @elseif ($sample->overall_status === 'REVISED') bg-success
                                                                    @else bg-secondary @endif"
                                                                    role="progressbar"
                                                                    style="width: {{ $sample->progress_percentage }}%;"
                                                                    aria-valuenow="{{ $sample->progress_percentage }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>


                                                    <div class="mt-auto d-flex gap-2">
                                                        @if ($sample->overall_status !== 'COMPLETED')
                                                            <a class="btn btn-outline-primary btn-md"
                                                                href="{{ route('revise_test', $sample->registration->tr04_reference_id) }}">
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
                                        <p class="text-muted text-center">No samples rejected yet.</p>
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
