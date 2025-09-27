@extends('layouts.app_back')
@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="components-preview wide-xl mx-auto">

                        <!-- Header Section -->
                        <div class="nk-block nk-block-lg">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="nk-block-head-content">
                                    <h3 class="nk-block-title page-title">Sample Details
                                        <strong class="text-primary small">#{{ $sample->tr04_reference_id }}</strong>
                                    </h3>
                                    <div class="nk-block-des text-soft">
                                        <ul class="list-inline">
                                            <li>Created At:
                                                <span
                                                    class="text-base">{{ $sample->created_at->format('d M, Y h:i A') }}</span>
                                            </li>
                                            <li>Status:
                                                <span
                                                    class="badge badge-dot 
                                                    @if ($sample->tr04_status == 'ACTIVE') bg-success 
                                                    @elseif($sample->tr04_status == 'PENDING') bg-warning 
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst(strtolower($sample->tr04_status)) }}
                                                </span>
                                            </li>
                                            <li>Progress:
                                                <span class="badge bg-primary">
                                                    {{ ucfirst(strtolower($sample->sampleTests[0]->tr05_status ?? 'N/A')) }}
                                                </span>
                                            </li>
                                            <li>
                                                @if ($sample->sampleTests[0]->tr05_status == 'Completed')
                                                    <a class="btn btn-success btn-sm"
                                                        href="{{ route('update_analysis', $sample->sampleTests[0]->tr05_sample_test_id ?? 0) }}">
                                                        <em class="icon ni ni-check-circle-cut"></em> &nbsp; Mark it
                                                        Complete
                                                    </a>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('view_analyst_dashboard') }}" class="btn btn-sm btn-primary">
                                        <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="nk-block">
                            <div class="invoice">
                                <div class="invoice-wrap">
                                    <!-- Test Details Table -->
                                    <div class="invoice-bills">
                                        <h5 class="mb-3">Test Details</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Test ID</th>
                                                        <th>Test Name & Description</th>
                                                        <th>Standard/Method</th>
                                                        <th>Unit</th>
                                                        <th>Primary Test</th>
                                                        <th>Secondary Test</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($sample->sampleTests as $sampleTest)
                                                        <tr>
                                                            <td>{{ $sampleTest->test->m12_test_id ?? 'N/A' }}</td>
                                                            <td>
                                                                <strong>{{ $sampleTest->test->m12_name ?? 'N/A' }}</strong>
                                                                @if (!empty($sampleTest->test->m12_description))
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        Description:
                                                                        {{ $sampleTest->test->m12_description }}
                                                                    </small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $sampleTest->standard->m15_method ?? 'N/A' }}
                                                                @if (($sampleTest->standard->m15_accredited ?? 'NO') === 'YES')
                                                                    <br>
                                                                    <span
                                                                        class="badge bg-success badge-xxxs">Accredited</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $sampleTest->test->m12_unit ?? 'N/A' }}</td>
                                                            <td>{{ $sampleTest->primary_tests[0]->m16_name ?? 'N/A' }}</td>
                                                            <td>{{ $sampleTest->secondary_tests[0]->m17_name ?? 'N/A' }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge 
                                                                    @if ($sampleTest->tr05_status == 'COMPLETED') bg-success 
                                                                    @elseif($sampleTest->tr05_status == 'PENDING') bg-warning 
                                                                    @elseif($sampleTest->tr05_status == 'IN_PROGRESS') bg-info 
                                                                    @else bg-secondary @endif">
                                                                    {{ ucfirst(strtolower(str_replace('_', ' ', $sampleTest->tr05_status ?? 'N/A'))) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($sampleTest->tr05_status == 'ALLOTED')
                                                                    <a href="{{ route('update_analysis', $sampleTest->tr05_sample_test_id) }}"
                                                                        class="btn btn-success btn-xs">Start</a>
                                                                @elseif ($sampleTest->tr05_status == 'IN_PROGRESS')
                                                                    <a href="{{ route('update_analysis', $sampleTest->tr05_sample_test_id) }}"
                                                                        class="btn btn-warning btn-xs">Complete</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">No tests found</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- .invoice-bills -->
                                </div><!-- .invoice-wrap -->
                            </div><!-- .invoice -->
                        </div><!-- .nk-block -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
