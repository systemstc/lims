@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title page-title">Dashboard</h4>
                        </div><!-- .nk-block-head-content -->
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <div class="toggle-expand-content" data-content="filters">
                                    <form method="GET" action="{{ route('dashboard') }}"
                                        class="row g-3 align-items-center">
                                        @php
                                            $month = $month ?? date('n');
                                            $year = $year ?? date('Y');
                                        @endphp

                                        <div class="col-auto">
                                            <select name="month" class="form-select">
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ $month == $i ? 'selected' : '' }}>
                                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <select name="year" class="form-select">
                                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        @if (Session::get('role') === 'ADMIN' && $ros->count() > 0)
                                            <div class="col-auto">
                                                <select name="ro_id" class="form-select">
                                                    <option value="">All ROs</option>
                                                    @foreach ($ros as $ro)
                                                        <option value="{{ $ro->m04_ro_id }}"
                                                            {{ $roId == $ro->m04_ro_id ? 'selected' : '' }}>
                                                            {{ $ro->m02_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary">Apply</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Total Revenue Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card is-dark h-100">
                                <div class="nk-ecwg nk-ecwg1">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title">Total Revenue</h6>
                                            </div>
                                            <div class="card-tools">
                                                <a href="#" class="link">View Report</a>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="amount">
                                                ₹{{ number_format($dashboardData['total_revenue']['current'], 2) }}</div>
                                            <div class="info">
                                                <strong>₹{{ number_format($dashboardData['total_revenue']['last_month'], 2) }}</strong>
                                                in last month
                                            </div>
                                        </div>
                                        <div class="data">
                                            <h6 class="sub-title">This week so far</h6>
                                            <div class="data-group">
                                                <div class="amount">
                                                    ₹{{ number_format($dashboardData['total_revenue']['this_week'], 2) }}
                                                </div>
                                                <div class="info text-end">
                                                    {{-- @dd($dashboardData['total_revenue']['revenue_change']) --}}
                                                    <span
                                                        class="change {{ $dashboardData['total_revenue']['revenue_change'] >= 0 ? 'up text-danger' : 'down text-info' }}">
                                                        <em
                                                            class="icon ni ni-arrow-long-{{ $dashboardData['total_revenue']['revenue_change'] >= 0 ? 'up' : 'down' }}"></em>
                                                        {{ number_format(abs($dashboardData['total_revenue']['revenue_change']), 2) }}%
                                                    </span><br>
                                                    <span>vs. last week</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                    <div class="nk-ck-wrap mt-auto overflow-hidden rounded-bottom">
                                        <div class="nk-ecwg1-ck">
                                            <canvas class="ecommerce-line-chart-s1" id="totalSales"></canvas>
                                        </div>
                                    </div>
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <!-- Average Revenue Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card h-100">
                                <div class="nk-ecwg nk-ecwg2">
                                    <div class="card-inner">
                                        <div class="card-title-group mt-n1">
                                            <div class="card-title">
                                                <h6 class="title">Average Revenue</h6>
                                            </div>
                                            <div class="card-tools me-n1">
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li><a href="#" class="active"><span>15 Days</span></a>
                                                            </li>
                                                            <li><a href="#"><span>30 Days</span></a></li>
                                                            <li><a href="#"><span>3 Months</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="data">
                                            <div class="data-group">
                                                <div class="amount">
                                                    ₹{{ number_format($dashboardData['average_revenue']['current'], 2) }}
                                                </div>
                                                <div class="info text-end">
                                                    <span
                                                        class="change {{ $dashboardData['average_revenue']['change'] >= 0 ? 'up text-danger' : 'down text-info' }}">
                                                        <em
                                                            class="icon ni ni-arrow-long-{{ $dashboardData['average_revenue']['change'] >= 0 ? 'up' : 'down' }}"></em>
                                                        {{ number_format(abs($dashboardData['average_revenue']['change']), 2) }}%
                                                    </span><br>
                                                    <span>vs. last month</span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="sub-title">Samples over time</h6>
                                    </div><!-- .card-inner -->
                                    <div class="nk-ecwg2-ck">
                                        <canvas class="ecommerce-bar-chart-s1" id="averargeOrder"></canvas>
                                    </div>
                                </div><!-- .nk-ecwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <!-- Samples and Customers Cards -->
                        <div class="col-xxl-4">
                            <div class="row g-gs">
                                <div class="col-xxl-12 col-md-6">
                                    <div class="card">
                                        <div class="nk-ecwg nk-ecwg3">
                                            <div class="card-inner pb-2">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Samples</h6>
                                                    </div>
                                                </div>
                                                <div class="data">
                                                    <div class="data-group">
                                                        <div class="amount">{{ $dashboardData['samples']['count'] }}</div>
                                                        <div class="info text-end">
                                                            <span
                                                                class="change {{ $dashboardData['samples']['change'] >= 0 ? 'up text-danger' : 'down text-info' }}">
                                                                <em
                                                                    class="icon ni ni-arrow-long-{{ $dashboardData['samples']['change'] >= 0 ? 'up' : 'down' }}"></em>
                                                                {{ number_format(abs($dashboardData['samples']['change']), 2) }}%
                                                            </span><br>
                                                            <span>vs. last month</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- .card-inner -->
                                            <div class="nk-ck-wrap mt-auto overflow-hidden rounded-bottom">
                                                <div class="nk-ecwg3-ck">
                                                    <canvas class="ecommerce-line-chart-s1" id="totalOrders"></canvas>
                                                </div>
                                            </div>
                                        </div><!-- .nk-ecwg -->
                                    </div><!-- .card -->
                                </div><!-- .col -->
                                <div class="col-xxl-12 col-md-6">
                                    <div class="card">
                                        <div class="nk-ecwg nk-ecwg3">
                                            <div class="card-inner pb-2">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Customers</h6>
                                                    </div>
                                                </div>
                                                <div class="data">
                                                    <div class="data-group">
                                                        <div class="amount">{{ $dashboardData['customers']['count'] }}
                                                        </div>
                                                        <div class="info text-end">
                                                            <span
                                                                class="change {{ $dashboardData['customers']['change'] >= 0 ? 'up text-danger' : 'down text-info' }}">
                                                                <em
                                                                    class="icon ni ni-arrow-long-{{ $dashboardData['customers']['change'] >= 0 ? 'up' : 'down' }}"></em>
                                                                {{ number_format(abs($dashboardData['customers']['change']), 2) }}%
                                                            </span><br>
                                                            <span>vs. last month</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- .card-inner -->
                                            <div class="nk-ck-wrap mt-auto overflow-hidden rounded-bottom">
                                                <div class="nk-ecwg3-ck">
                                                    <canvas class="ecommerce-line-chart-s1" id="totalCustomers"></canvas>
                                                </div>
                                            </div>
                                        </div><!-- .nk-ecwg -->
                                    </div><!-- .card -->
                                </div><!-- .col -->
                            </div><!-- .row -->
                        </div><!-- .col -->

                        <!-- Recent Samples -->
                        <div class="col-xxl-8">
                            <div class="card card-full">
                                <div class="card-inner">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Recent Samples</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-list mt-n2">
                                    <div class="nk-tb-item nk-tb-head">
                                        <div class="nk-tb-col"><span>Sample ID</span></div>
                                        <div class="nk-tb-col tb-col-sm"><span>Customer</span></div>
                                        <div class="nk-tb-col tb-col-md"><span>Date</span></div>
                                        <div class="nk-tb-col"><span>Amount</span></div>
                                        <div class="nk-tb-col"><span class="d-none d-sm-inline">Status</span></div>
                                    </div>
                                    @foreach ($dashboardData['recent_samples'] as $sample)
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col">
                                                <span class="tb-lead"><a
                                                        href="#">{{ $sample->tr04_reference_id }}</a></span>
                                            </div>
                                            <div class="nk-tb-col tb-col-sm">
                                                <div class="user-card">
                                                    <div class="user-avatar sm bg-purple-dim">
                                                        <span>{{ getInitials($sample->customer->m07_name ?? 'N/A') }}</span>
                                                    </div>
                                                    <div class="user-name">
                                                        <span
                                                            class="tb-lead">{{ $sample->customer->m07_name ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="nk-tb-col tb-col-md">
                                                <span class="tb-sub">{{ $sample->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="nk-tb-col">
                                                <span
                                                    class="tb-sub tb-amount">{{ number_format($sample->tr04_total_charges, 2) }}
                                                    <span>₹</span></span>
                                            </div>
                                            <div class="nk-tb-col">
                                                @php
                                                    $statusClass =
                                                        [
                                                            'PAID' => 'bg-success',
                                                            'PENDING' => 'bg-warning',
                                                            'DUE' => 'bg-danger',
                                                            'CANCELED' => 'bg-danger',
                                                        ][$sample->tr04_payment_status] ?? 'bg-secondary';
                                                @endphp
                                                <span
                                                    class="badge badge-dot badge-dot-xs {{ $statusClass }}">{{ $sample->tr04_payment_status }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div><!-- .card -->
                        </div>

                        <!-- Top Customers -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Top Customers</h6>
                                        </div>
                                        <div class="card-tools">
                                            <div class="dropdown">
                                                <a href="#"
                                                    class="dropdown-toggle link link-light link-sm dropdown-indicator"
                                                    data-bs-toggle="dropdown">Weekly</a>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#"><span>Daily</span></a></li>
                                                        <li><a href="#" class="active"><span>Weekly</span></a></li>
                                                        <li><a href="#"><span>Monthly</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="nk-top-products">
                                        @foreach ($dashboardData['top_customers'] as $index => $customer)
                                            <li class="item">
                                                <div class="user-avatar sm bg-purple-dim me-2">
                                                    <span>{{ getInitials($customer->customer->m07_name ?? 'N/A') }}</span>
                                                </div>
                                                <div class="info">
                                                    <div class="title">{{ $customer->customer->m07_name ?? 'N/A' }}</div>
                                                    <div class="price"></div>
                                                </div>
                                                <div class="total">
                                                    <div class="amount">₹{{ number_format($customer->total_amount, 2) }}
                                                    </div>
                                                    <div class="count">{{ $customer->sample_count }} Samples</div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div><!-- .card-inner -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <!-- Laboratory Statistics -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Laboratory Statistics</h6>
                                        </div>
                                    </div>
                                    <ul class="nk-store-statistics">
                                        <li class="item">
                                            <div class="info">
                                                <div class="title">Sample Received</div>
                                                <div class="count">{{ $dashboardData['lab_stats']['samples_received'] }}
                                                </div>
                                            </div>
                                            <em class="icon bg-primary-dim ni ni-bag"></em>
                                        </li>
                                        <li class="item">
                                            <div class="info">
                                                <div class="title">Customers</div>
                                                <div class="count">{{ $dashboardData['lab_stats']['customers'] }}</div>
                                            </div>
                                            <em class="icon bg-info-dim ni ni-users"></em>
                                        </li>
                                        <li class="item">
                                            <div class="info">
                                                <div class="title">Sample Reported</div>
                                                <div class="count">{{ $dashboardData['lab_stats']['samples_reported'] }}
                                                </div>
                                            </div>
                                            <em class="icon bg-pink-dim ni ni-box"></em>
                                        </li>
                                        <li class="item">
                                            <div class="info">
                                                <div class="title">Test Performed</div>
                                                <div class="count">{{ $dashboardData['lab_stats']['test_performed'] }}
                                                </div>
                                            </div>
                                            <em class="icon bg-purple-dim ni ni-server"></em>
                                        </li>
                                    </ul>
                                </div><!-- .card-inner -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <!-- Sample Sources -->
                        <div class="col-xxl-5 col-lg-6">
                            <div class="card card-full overflow-hidden">
                                <div class="nk-ecwg nk-ecwg4 h-100">
                                    <div class="card-inner flex-grow-1">
                                        <div class="card-title-group mb-4">
                                            <div class="card-title">
                                                <h6 class="title">Sample Sources</h6>
                                            </div>
                                            {{-- <div class="card-tools">
                                                <div class="dropdown">
                                                    <a href="#"
                                                        class="dropdown-toggle link link-light link-sm dropdown-indicator"
                                                        data-bs-toggle="dropdown">30 Days</a>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li><a href="#"><span>15 Days</span></a></li>
                                                            <li><a href="#" class="active"><span>30 Days</span></a>
                                                            </li>
                                                            <li><a href="#"><span>3 Months</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <div class="data-group">
                                            <div class="nk-ecwg4-ck">
                                                <canvas class="ecommerce-doughnut-s1" id="trafficSources"></canvas>
                                            </div>
                                            <ul class="nk-ecwg4-legends">
                                                @php
                                                    $colors = [
                                                        '#9cabff',
                                                        '#ffa9ce',
                                                        '#b8acff',
                                                        '#f9db7b',
                                                        '#7bffa1',
                                                        '#ff7b7b',
                                                    ];
                                                @endphp
                                                @foreach ($dashboardData['sample_sources'] as $index => $source)
                                                    <li>
                                                        <div class="title">
                                                            <span class="dot dot-lg sq"
                                                                style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                                                            <span
                                                                class="ms-4">{{ $source->customerType->m09_name ?? 'Unknown' }}</span>
                                                        </div>
                                                        <div class="amount amount-xs">{{ $source->count }}</div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div><!-- .card-inner -->
                                    <div class="card-inner card-inner-md bg-light">
                                        <div class="card-note">
                                            <em class="icon ni ni-info-fill"></em>
                                            <span>Sample sources distribution for the selected period.</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <!-- Laboratory Visitors -->
                        {{-- <div class="col-xxl-4 col-lg-6">
                            <div class="card h-100">
                                <div class="nk-ecwg nk-ecwg5">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start pb-3 g-2">
                                            <div class="card-title">
                                                <h6 class="title">Laboratory Visitors</h6>
                                            </div>
                                            <div class="card-tools">
                                                <em class="card-hint icon ni ni-help" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" title="Customers of this month"></em>
                                            </div>
                                        </div>
                                        <div class="data-group">
                                            <div class="data">
                                                <div class="title">Monthly</div>
                                                <div class="amount amount-sm">{{ $dashboardData['customers']['count'] }}
                                                </div>
                                                <div
                                                    class="change {{ $dashboardData['customers']['change'] >= 0 ? 'up' : 'down' }}">
                                                    <em
                                                        class="icon ni ni-arrow-long-{{ $dashboardData['customers']['change'] >= 0 ? 'up' : 'down' }}"></em>
                                                    {{ number_format(abs($dashboardData['customers']['change']), 2) }}%
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="title">Weekly</div>
                                                <div class="amount amount-sm">-</div>
                                                <div class="change down"><em class="icon ni ni-arrow-long-down"></em>0%
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="title">Daily (Avg)</div>
                                                <div class="amount amount-sm">
                                                    {{ $dashboardData['samples']['count'] > 0 ? round($dashboardData['customers']['count'] / max(1, date('d')), 1) : 0 }}
                                                </div>
                                                <div class="change up"><em class="icon ni ni-arrow-long-up"></em>0%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-ecwg5-ck">
                                            <canvas class="ecommerce-line-chart-s4" id="storeVisitors"></canvas>
                                        </div>
                                        <div class="chart-label-group">
                                            <div class="chart-label">
                                                {{ DateTime::createFromFormat('!m', $month)->format('01 M, Y') }}</div>
                                            <div class="chart-label">
                                                {{ DateTime::createFromFormat('!m', $month)->format('t M, Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div> --}}
                        <!-- Sample Progress Heatmap -->
                        <div class="col-xxl-4 col-lg-6">
                            <div class="card h-100">
                                <div class="nk-ecwg nk-ecwg5">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start pb-3 g-2">
                                            <div class="card-title">
                                                <h6 class="title">Sample Progress Heatmap</h6>
                                            </div>
                                            <a href="{{ route('view_sample_status_by_month') }}" class="nav-link">
                                                More
                                            </a>
                                            <div class="card-tools">
                                                <em class="card-hint icon ni ni-help" data-bs-toggle="tooltip"
                                                    data-bs-placement="left"
                                                    title="Click on any date to view sample details"></em>
                                            </div>
                                        </div>

                                        <!-- Status Legend -->
                                        <div class="heatmap-legend mb-3">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="legend-item d-flex align-items-center">
                                                    <span class="legend-color bg-success me-1"
                                                        style="width: 12px; height: 12px; border-radius: 2px;"></span>
                                                    <small class="text-soft">On Time</small>
                                                </div>
                                                <div class="legend-item d-flex align-items-center">
                                                    <span class="legend-color bg-warning me-1"
                                                        style="width: 12px; height: 12px; border-radius: 2px;"></span>
                                                    <small class="text-soft">Near Deadline</small>
                                                </div>
                                                <div class="legend-item d-flex align-items-center">
                                                    <span class="legend-color bg-danger me-1"
                                                        style="width: 12px; height: 12px; border-radius: 2px;"></span>
                                                    <small class="text-soft">Overdue</small>
                                                </div>
                                                <div class="legend-item d-flex align-items-center">
                                                    <span class="legend-color bg-light me-1 border"
                                                        style="width: 12px; height: 12px; border-radius: 2px;"></span>
                                                    <small class="text-soft">Reported</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Heatmap Grid -->
                                        <div class="heatmap-container">
                                            <div class="heatmap-grid">
                                                @php
                                                    $heatmapData = $dashboardData['heatmap_data'] ?? [];
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
                                                                    onclick="showDateDetails({{ $dayData['day'] }}, {{ $month }}, {{ $year }})"
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

                                        <div class="chart-label-group mt-3">
                                            <div class="chart-label">
                                                {{ \Carbon\Carbon::create($year, $month, 1)->format('d M, Y') }}
                                            </div>
                                            <div class="chart-label">
                                                {{ \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->format('d M, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div>
                    </div><!-- .row -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>

    <!-- Date Details Modal -->
    <div class="modal fade" id="dateDetailsModal" tabindex="-1" aria-labelledby="dateDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateDetailsModalLabel">Sample Details - <span id="modalDateTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="30%">Sample ID</th>
                                    <th width="25%">Status</th>
                                    <th width="25%">Expected Date</th>
                                    <th width="20%">Days Remaining</th>
                                </tr>
                            </thead>
                            <tbody id="modalSamplesTable">
                                <!-- Sample data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noSamplesMessage" class="text-center py-4" style="display: none;">
                        <div class="text-muted">
                            <em class="icon ni ni-info-fill fs-2"></em>
                            <p class="mt-2">No samples found for this date</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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
            text-decoration: line-through;
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

        .heatmap-legend {
            border-bottom: 1px solid #e5e9f2;
            padding-bottom: 12px;
        }

        .legend-color {
            display: inline-block;
        }
    </style>
    <script>
        // Function to show date details modal
        function showDateDetails(day, month, year) {
            // Show loading state
            document.getElementById('modalSamplesTable').innerHTML = `
        <tr>
            <td colspan="4" class="text-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading samples...
            </td>
        </tr>
    `;
            document.getElementById('noSamplesMessage').style.display = 'none';

            // Set modal title
            const dateStr = `${day.toString().padStart(2, '0')} ${getMonthName(month)} ${year}`;
            document.getElementById('modalDateTitle').textContent = dateStr;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('dateDetailsModal'));
            modal.show();

            // Fetch sample details via AJAX
            fetchDateSamples(day, month, year);
        }

        // Function to fetch samples for the selected date
        function fetchDateSamples(day, month, year) {
            const url = '{{ route('dashboard.date.samples') }}';
            const csrfToken = '{{ csrf_token() }}';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        day: day,
                        month: month,
                        year: year
                    })
                })
                .then(response => response.json())
                .then(data => {
                    displaySamplesInModal(data.samples, day, month, year);
                })
                .catch(error => {
                    console.error('Error fetching samples:', error);
                    document.getElementById('modalSamplesTable').innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-danger">
                    Error loading samples. Please try again.
                </td>
            </tr>
        `;
                });
        }

        // Function to display samples in modal
        function displaySamplesInModal(samples, day, month, year) {
            const tableBody = document.getElementById('modalSamplesTable');
            const noSamplesMessage = document.getElementById('noSamplesMessage');

            if (!samples || samples.length === 0) {
                tableBody.innerHTML = '';
                noSamplesMessage.style.display = 'block';
                return;
            }

            noSamplesMessage.style.display = 'none';

            let html = '';
            samples.forEach(sample => {
                const statusClass = getStatusClass(sample.tr04_progress);
                const expectedDate = sample.tr04_expected_date ? new Date(sample.tr04_expected_date) : null;
                const isReported = sample.tr04_progress === 'REPORTED';
                const daysRemaining = expectedDate ? calculateDaysRemaining(expectedDate) : 'N/A';
                const daysRemainingClass = getDaysRemainingClass(daysRemaining);

                html += `
            <tr>
                <td>
                    <strong>${sample.tr04_reference_id}</strong>
                </td>
                <td>
                    <strong class="${statusClass}">${formatStatus(sample.tr04_progress)}</strong>
                </td>
                <td>
                    ${expectedDate ? expectedDate.toLocaleDateString() : 'Not set'}
                </td>
                <td>
                    ${isReported ? 
                        '<span class="text-success"><em class="icon ni ni-check-circle"></em> Completed</span>' :
                        (typeof daysRemaining === 'number' ? 
                            `<span class="${daysRemainingClass}">${daysRemaining} days</span>` : 
                            'N/A'
                        )
                    }
                </td>
            </tr>
        `;
            });

            tableBody.innerHTML = html;
        }

        // Helper function to get month name
        function getMonthName(month) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return months[month - 1];
        }

        // Helper function to get status badge class
        function getStatusClass(status) {
            const statusClasses = {
                'REGISTERED': 'text-primary',
                'ALLOTED': 'text-info',
                'IN_PROGRESS': 'text-warning',
                'RESULT_ENTRY': 'text-purple',
                'VERIFIED': 'text-success',
                'REPORTED': 'text-secondary'
            };
            return statusClasses[status] || 'text-dark';
        }

        // Helper function to format status for display
        function formatStatus(status) {
            return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        // Helper function to calculate days remaining
        function calculateDaysRemaining(expectedDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            expectedDate.setHours(0, 0, 0, 0);

            const diffTime = expectedDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            return diffDays;
        }

        // Helper function to get days remaining color class
        function getDaysRemainingClass(daysRemaining) {
            if (daysRemaining === 'N/A') return 'text-muted';
            if (daysRemaining < 0) return 'text-danger';
            if (daysRemaining <= 3) return 'text-warning';
            return 'text-success';
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <!-- Custom Chart Data -->
    <script>
        // Override the default chart data with your custom data
        window.totalSales = {
            labels: {!! json_encode($dashboardData['chart_data']['revenue']['labels']) !!},
            dataUnit: '₹',
            lineTension: 0.3,
            datasets: [{
                label: "Revenue",
                color: "#9d72ff",
                background: "rgba(157, 114, 255, 0.25)",
                data: {!! json_encode($dashboardData['chart_data']['revenue']['current']) !!}
            }]
        };

        window.averargeOrder = {
            labels: {!! json_encode($dashboardData['chart_data']['average_revenue']['labels']) !!},
            dataUnit: '₹',
            lineTension: 0.1,
            datasets: [{
                label: "Avg Revenue",
                color: "#b695ff",
                background: "#b695ff",
                data: {!! json_encode($dashboardData['chart_data']['average_revenue']['data']) !!}
            }]
        };

        window.totalOrders = {
            labels: {!! json_encode($dashboardData['chart_data']['samples']['labels']) !!},
            dataUnit: 'Samples',
            lineTension: 0.3,
            datasets: [{
                label: "Samples",
                color: "#7de1f8",
                background: "rgba(125, 225, 248, 0.25)",
                data: {!! json_encode($dashboardData['chart_data']['samples']['data']) !!}
            }]
        };

        window.totalCustomers = {
            labels: {!! json_encode($dashboardData['chart_data']['customers']['labels']) !!},
            dataUnit: 'Customers',
            lineTension: 0.3,
            datasets: [{
                label: "Customers",
                color: "#83bcff",
                background: "rgba(131, 188, 255, 0.25)",
                data: {!! json_encode($dashboardData['chart_data']['customers']['data']) !!}
            }]
        };

        window.trafficSources = {
            labels: {!! json_encode($dashboardData['chart_data']['sample_sources']['labels']) !!},
            dataUnit: 'Samples',
            legend: false,
            datasets: [{
                borderColor: "#fff",
                background: {!! json_encode($dashboardData['chart_data']['sample_sources']['colors']) !!},
                data: {!! json_encode($dashboardData['chart_data']['sample_sources']['data']) !!}
            }]
        };

        window.storeVisitors = {
            labels: {!! json_encode($dashboardData['chart_data']['customers']['labels']) !!},
            dataUnit: 'Visitors',
            lineTension: 0.4,
            datasets: [{
                label: "Visitors",
                color: "#9d72ff",
                dash: [0, 0],
                background: "rgba(157, 114, 255, 0.15)",
                data: {!! json_encode($dashboardData['chart_data']['customers']['data']) !!}
            }]
        };

        console.log('Custom chart data loaded successfully');
    </script>

    <!-- Manual Chart Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                // Initialize charts manually
                if (typeof ecommerceLineS1 === 'function') {
                    ecommerceLineS1('#totalSales', window.totalSales);
                    ecommerceLineS1('#totalOrders', window.totalOrders);
                    ecommerceLineS1('#totalCustomers', window.totalCustomers);
                    console.log('Line charts initialized with custom data');
                }

                if (typeof ecommerceBarS1 === 'function') {
                    ecommerceBarS1('#averargeOrder', window.averargeOrder);
                    console.log('Bar chart initialized with custom data');
                }

                if (typeof ecommerceDoughnutS1 === 'function') {
                    ecommerceDoughnutS1('#trafficSources', window.trafficSources);
                    console.log('Doughnut chart initialized with custom data');
                }

                if (typeof ecommerceLineS4 === 'function') {
                    ecommerceLineS4('#storeVisitors', window.storeVisitors);
                    console.log('Store visitors chart initialized with custom data');
                }
            }, 100);
        });
    </script>
@endsection
