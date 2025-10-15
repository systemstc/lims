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
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0 fw-semibold">Test Details</h5>
                                            @php
                                                $testStatus = $sample->sampleTests[0]->tr05_status ?? 'INCOMPLETE';
                                            @endphp

                                            @if ($testStatus === 'COMPLETED')
                                                <a href="{{ route('template_manuscript', $sample->tr04_sample_registration_id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-file-text"></em> View Manuscript
                                                </a>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary disabled-btn">
                                                    <em class="icon ni ni-file-text"></em> View Manuscript
                                                </button>
                                            @endif

                                        </div>

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
                                                                <strong
                                                                    class="
                                                                    @if ($sampleTest->tr05_status == 'COMPLETED') text-success 
                                                                    @elseif($sampleTest->tr05_status == 'PENDING') text-warning 
                                                                    @elseif($sampleTest->tr05_status == 'IN_PROGRESS') text-info 
                                                                    @else text-secondary @endif">
                                                                    {{ ucfirst(strtolower(str_replace('_', ' ', $sampleTest->tr05_status ?? 'N/A'))) }}
                                                                </strong>
                                                            </td>
                                                            <td>
                                                                @if ($sampleTest->tr05_status == 'ALLOTED')
                                                                    <a href="{{ route('update_analysis', $sampleTest->tr05_sample_test_id) }}"
                                                                        class="btn btn-success btn-xs confirm-status"
                                                                        data-action="start">
                                                                        <em class="icon ni ni-play"></em>&nbsp; Start
                                                                    </a>
                                                                @elseif ($sampleTest->tr05_status == 'IN_PROGRESS')
                                                                    <a href="{{ route('update_analysis', $sampleTest->tr05_sample_test_id) }}"
                                                                        class="btn btn-warning btn-xs confirm-status"
                                                                        data-action="complete">
                                                                        <em class="icon ni ni-check-circle-cut"></em>&nbsp;
                                                                        Complete
                                                                    </a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm before updating test status
            document.querySelectorAll('.confirm-status').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // stop default redirect

                    const url = this.getAttribute('href');
                    const action = this.dataset.action;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: action === 'start' ?
                            "Do you want to mark this test as In Progress?" :
                            "Do you want to mark this test as Completed?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect only if confirmed
                            window.location.href = url;
                        }
                    });
                });
            });

            // Your existing disabled button alert
            document.querySelectorAll('.disabled-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Action Not Allowed',
                        text: 'Please complete the test before viewing the manuscript.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#6576ff',
                    });
                });
            });
        });
    </script>
@endsection
