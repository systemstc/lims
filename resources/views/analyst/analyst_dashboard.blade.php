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
    <style>
        .heatmap-container {
            overflow-x: auto;
        }

        .heatmap-grid {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 300px;
        }

        .heatmap-row {
            display: flex;
            gap: 2px;
        }

        .heatmap-cell {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            border-radius: 4px;
            position: relative;
        }

        .heatmap-header .heatmap-cell,
        .week-header {
            background: transparent;
            font-weight: 600;
            color: #526484;
        }

        .day-header {
            font-size: 9px !important;
        }

        .day-cell {
            background-color: #f5f6fa;
            border: 1px solid #e5e9f2;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .day-cell:hover {
            transform: scale(1.1);
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .day-cell.on-time {
            background-color: #0cab0c;
            border-color: #0cab0c;
            color: white;
        }

        .day-cell.near-deadline {
            background-color: #f59a2c;
            border-color: #f59a2c;
            color: white;
        }

        .day-cell.overdue {
            background-color: #e85347;
            border-color: #e85347;
            color: white;
        }

        .day-cell.reported {
            background-color: #9a9b9c;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .day-cell.empty {
            background-color: transparent;
            border-color: transparent;
        }

        .day-number {
            font-size: 9px;
            font-weight: 500;
        }

        .sample-count {
            position: absolute;
            bottom: 1px;
            right: 1px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 2px;
            width: 12px;
            height: 12px;
            font-size: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
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

                        <div class="nk-block-head-content">
                            <form action="{{ route('view_analyst_dashboard') }}" method="GET" class="d-flex gap-2">
                                <select name="month" class="form-control" onchange="this.form.submit()">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}"
                                            {{ request('month', date('n')) == $i ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <select name="year" class="form-control" onchange="this.form.submit()">
                                    @for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}"
                                            {{ request('year', date('Y')) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </form>
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

            </div>
        </div>

        <!-- Charts Section -->
        <div class="nk-block mt-4">
            <div class="row g-gs">
                <div class="col-lg-4">
                    <div class="card card-bordered h-100">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Weekly Efficiency (Tests Completed)</h6>
                                </div>
                            </div>
                            <div class="nk-ck-sm">
                                <canvas class="analyst-weekly-chart" id="weeklyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Calendar Heatmap Section -->
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start pb-3 g-2">
                                    <div class="card-title">
                                        <h6 class="title">Monthly Sample Allotments</h6>
                                        {{-- <p class="text-muted small">Overview of samples allotted to you in
                                            {{ DateTime::createFromFormat('!m', $month ?? date('n'))->format('F') }}
                                            {{ $year ?? date('Y') }}</p> --}}
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="rounded-circle bg-success d-inline-block"
                                                        style="width: 8px; height: 8px;"></span>
                                                    <small class="text-muted">Completed</small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="rounded-circle bg-secondary d-inline-block"
                                                        style="width: 8px; height: 8px;"></span>
                                                    <small class="text-muted">In Progress</small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="rounded-circle bg-warning d-inline-block"
                                                        style="width: 8px; height: 8px;"></span>
                                                    <small class="text-muted">Near Deadline</small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="rounded-circle bg-danger d-inline-block"
                                                        style="width: 8px; height: 8px;"></span>
                                                    <small class="text-muted">Overdue</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="heatmap-container d-flex justify-content-center">
                                    <div class="heatmap-grid">
                                        @php
                                            // $heatmapData passed from controller
                                            $heatMonth = $month ?? date('n');
                                            $heatYear = $year ?? date('Y');
                                            $daysInMonth = count($heatmapData);
                                            $weeks = ceil($daysInMonth / 7);
                                        @endphp

                                        <!-- Day headers -->
                                        <div class="heatmap-row heatmap-header">
                                            <div class="heatmap-cell"></div>
                                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                                <div class="heatmap-cell day-header">
                                                    <small>{{ $day }}</small>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Heatmap cells -->
                                        @for ($week = 0; $week < $weeks; $week++)
                                            <div class="heatmap-row">
                                                <div class="heatmap-cell week-header">
                                                    <small>W{{ $week + 1 }}</small>
                                                </div>
                                                @for ($day = 0; $day < 7; $day++)
                                                    @php
                                                        $dayIndex = $week * 7 + $day;
                                                        $dayData = $heatmapData[$dayIndex] ?? null;
                                                    @endphp
                                                    @if ($dayData && $dayData['day'] <= $daysInMonth)
                                                        <div class="heatmap-cell day-cell {{ $dayData['status_class'] }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $dayData['tooltip'] }}"
                                                            onclick="showDateDetails({{ $dayData['day'] }}, {{ $heatMonth }}, {{ $heatYear }})"
                                                            style="cursor: pointer;">
                                                            <div class="day-number">{{ $dayData['day'] }}</div>
                                                            @if ($dayData['sample_count'] > 0)
                                                                <div class="sample-count">
                                                                    {{ $dayData['sample_count'] }}</div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="heatmap-cell day-cell empty"></div>
                                                    @endif
                                                @endfor
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-bordered h-100">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Top 5 Test Types</h6>
                                </div>
                            </div>
                            <div class="nk-ck-sm">
                                <canvas class="analyst-doughnut-chart" id="testDistChart"></canvas>
                            </div>
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
                                                    <small class="fw-bold">{{ $sample->progress_percentage }}%</small>
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
    <!-- Date Details Modal -->
    <div class="modal fade" id="dateDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sample Details - <span id="modalDateTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Test</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody id="modalSamplesTable">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noSamplesMessage" class="text-center py-4" style="display: none;">
                        <p class="text-muted">No samples found for this date.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Weekly Trend Chart
        const trendData = @json($trendData);

        // Use vanilla JS for date formatting
        const trendLabels = trendData.map(item => {
            const d = new Date(item.date);
            return d.toLocaleDateString('en-US', {
                weekday: 'short',
                day: 'numeric'
            });
        });
        const sampleCounts = trendData.map(item => item.samples_count);
        const testCounts = trendData.map(item => item.tests_count);

        new Chart(document.getElementById('weeklyTrendChart'), {
            type: 'bar',
            data: {
                labels: trendLabels,
                datasets: [{
                        label: 'Completed Samples',
                        data: sampleCounts,
                        backgroundColor: 'rgba(54, 162, 235, 1)', // Blue
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Total Tests',
                        data: testCounts,
                        backgroundColor: 'rgba(75, 192, 192, 1)', // Green
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        borderRadius: 3,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e5e9f2'
                        },
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });

        // Test Distribution Chart
        const distData = @json($testDistribution);
        const distLabels = distData.map(item => item.m12_name);
        const distCounts = distData.map(item => item.count);

        new Chart(document.getElementById('testDistChart'), {
            type: 'doughnut',
            data: {
                labels: distLabels,
                datasets: [{
                    data: distCounts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                }
            }
        });

        // Existing alert script
        setTimeout(() => {
            const alert = document.getElementById('session-alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        // Calendar Drilldown Script
        function showDateDetails(day, month, year) {
            // Set title
            const date = new Date(year, month - 1, day);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var elDateTitle = document.getElementById('modalDateTitle');
            if (elDateTitle) elDateTitle.textContent = date.toLocaleDateString(undefined, options);

            // Show modal & loading state
            const tbody = document.getElementById('modalSamplesTable');
            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"></div> Loading...</td></tr>';
            document.getElementById('noSamplesMessage').style.display = 'none';

            new bootstrap.Modal(document.getElementById('dateDetailsModal')).show();

            // AJAX Call
            $.ajax({
                url: '{{ route('analyst_date_samples') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    day: day,
                    month: month,
                    year: year
                },
                success: function(response) {
                    if (response.success && response.samples.length > 0) {
                        let html = '';
                        response.samples.forEach(sample => {
                            let statusBadge = '';
                            switch (sample.status) {
                                case 'COMPLETED':
                                    statusBadge = '<span class="badge bg-success">Completed</span>';
                                    break;
                                case 'IN_PROGRESS':
                                    statusBadge =
                                        '<span class="badge bg-warning text-dark">In Progress</span>';
                                    break;
                                case 'ALLOTED':
                                    statusBadge = '<span class="badge bg-info">Allotted</span>';
                                    break;
                                default:
                                    statusBadge =
                                        `<span class="badge bg-secondary">${sample.status}</span>`;
                            }

                            let dateBadge = sample.days_remaining;
                            if (dateBadge.includes('overdue')) {
                                dateBadge =
                                    `<span class="text-danger fw-bold">${sample.days_remaining}</span>`;
                            } else if (dateBadge.includes('days left')) {
                                dateBadge =
                                    `<span class="text-success">${sample.days_remaining}</span>`;
                            }

                            html += `
                                <tr>
                                    <td><span class="fw-bold">${sample.reference_id}</span></td>
                                    <td>${sample.test_name}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <div>${sample.expected_date}</div>
                                        <small>${dateBadge}</small>
                                    </td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = '';
                        document.getElementById('noSamplesMessage').style.display = 'block';
                    }
                },
                error: function() {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="text-center text-danger">Failed to load data.</td></tr>';
                }
            });
        }
    </script>
@endsection
