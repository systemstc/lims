@extends('layouts.app_back')

@section('content')
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
                                    <h6 class="text-primary">Pending Tests</h6>
                                    <h2>{{ $pendingTests }}</h2>
                                </div>
                            </div>
                        </div>

                        <!-- In Progress Tests -->
                        <div class="col-md-3">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-warning">In Progress</h6>
                                    <h2>{{ $inProgressTests }}</h2>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Today -->
                        <div class="col-md-3">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-success">Completed Today</h6>
                                    <h2>{{ $completedTests }}</h2>
                                </div>
                            </div>
                        </div>

                        <!-- Total Samples -->
                        <div class="col-md-3">
                            <div class="card card-bordered text-center">
                                <div class="card-inner">
                                    <h6 class="text-info">Total Tests</h6>
                                    <h2>{{ $totalSamples }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Allotted Tests -->
                <div class="nk-block mt-5">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h5 class="card-title">Recent Allotted Tests</h5>
                            <div class="row g-gs" id="testCardsContainer">
                                @if (isset($allottedTests) && $allottedTests->count() > 0)
                                    @foreach ($allottedTests as $test)
                                        <div class="col-md-4">
                                            <div class="card card-bordered h-100">
                                                <div class="card-inner">
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="card-title">Sample ID:
                                                            {{ $test->tr04_sample_registration_id }}
                                                        </h6>
                                                        <a class="btn btn-primary btn-sm py-0 px-2"
                                                            href="{{ route('create_analysis', $test->tr05_sample_test_id) }}">Start</a>
                                                    </div>
                                                    <p>Status:
                                                        <span
                                                            class="badge 
                                                            @if ($test->tr05_status === 'ALLOTED') bg-primary 
                                                            @elseif ($test->tr05_status === 'IN_PROGRESS') bg-warning 
                                                            @elseif ($test->tr05_status === 'COMPLETED') bg-success 
                                                            @else bg-secondary @endif">
                                                            {{ $test->tr05_status }}
                                                        </span>
                                                    </p>
                                                    <p>Allotted At: {{ $test->created_at->format('d M Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <p class="text-muted text-center">No tests allotted yet.</p>
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
