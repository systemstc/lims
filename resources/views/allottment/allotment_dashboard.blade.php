{{-- Lab Manager Pending Allotments Dashboard --}}
@extends('layouts.app_back')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">Pending Allotments</h4>
                <p>New samples received from registrations that need test allotment or transfer.</p>
            </div>
        </div>
        {{-- @dd(Session::all()) --}}
        <!-- Statistics Cards -->
        <div class="row g-gs mb-4">
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">New Samples</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples just received from registration"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['new_samples'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Pending Tests</h6>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['pending_tests'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Partially Allotted</h6>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['partial_allotted'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Ready for Testing</h6>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $stats['ready_for_testing'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Samples Table -->
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="nk-tb-list nk-tb-ulist">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col"><span class="sub-text">Registration</span></div>
                            <div class="nk-tb-col tb-col-lg"><span class="sub-text">Received Date</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Tests</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Priority</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Days Pending</span></div>
                            <div class="nk-tb-col nk-tb-col-tools text-end">Actions</div>
                        </div>
                        {{-- @dd($pendingRegistrations) --}}
                        @foreach ($pendingRegistrations as $registration)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">{{ $registration->tr04_sample_registration_id }}</span>
                                            {{-- Show if tests were received --}}
                                            @if ($registration->received_tests > 0)
                                                <span class="badge bg-info ms-2">
                                                    Received ({{ $registration->received_tests }})
                                                </span>
                                            @endif

                                            {{-- Show if tests were transferred out --}}
                                            @if ($registration->transferred_tests > 0)
                                                <span class="badge bg-danger ms-2">
                                                    Transferred ({{ $registration->transferred_tests }})
                                                </span>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="nk-tb-col tb-col-lg">
                                    <span class="tb-lead">{{ $registration->created_at->format('d M Y') }}</span>
                                    <span class="tb-sub">{{ $registration->created_at->format('h:i A') }}</span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <div class="progress-wrap">
                                        <div class="progress-text">
                                            {{ $registration->allotted_tests }}/{{ $registration->total_tests }}
                                        </div>
                                        <div class="progress progress-md">
                                            @php
                                                $percentage =
                                                    $registration->total_tests > 0
                                                        ? ($registration->allotted_tests / $registration->total_tests) *
                                                            100
                                                        : 0;
                                            @endphp
                                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    @php
                                        $priority = $registration->tr04_sample_type ?? 'Normal';
                                        $priorityClass = match (strtolower($priority)) {
                                            'urgent' => 'bg-danger',
                                            'normal' => 'bg-info',
                                            default => 'bg-secondary',
                                        };
                                    @endphp

                                    <span class="badge {{ $priorityClass }}">{{ ucfirst($priority) }}</span>

                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    @php
                                        $allottedPercentage =
                                            $registration->total_tests > 0
                                                ? ($registration->allotted_tests / $registration->total_tests) * 100
                                                : 0;
                                        if ($allottedPercentage == 0) {
                                            $status = 'New';
                                            $statusClass = 'bg-warning';
                                        } elseif ($allottedPercentage == 100) {
                                            $status = 'Complete';
                                            $statusClass = 'bg-success';
                                        } else {
                                            $status = 'Partial';
                                            $statusClass = 'bg-info';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    @php
                                        $daysPending = $registration->created_at->floatDiffInDays(now());
                                        $daysPending = round($daysPending, 1);
                                        $urgencyClass =
                                            $daysPending > 3
                                                ? 'text-danger'
                                                : ($daysPending > 1
                                                    ? 'text-warning'
                                                    : 'text-muted');
                                    @endphp
                                    <span class="{{ $urgencyClass }}">
                                        {{ $daysPending }} days
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                    data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a
                                                                href="{{ route('show_allotment', $registration->tr04_sample_registration_id) }}">
                                                                <em class="icon ni ni-user-check"></em>
                                                                <span>Manage Allotment</span>
                                                            </a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="#"
                                                                onclick="viewDetails({{ $registration->tr04_sample_registration_id }})">
                                                                <em class="icon ni ni-eye"></em>
                                                                <span>View Details</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($pendingRegistrations->hasPages())
            <div class="card">
                <div class="card-inner">
                    {{ $pendingRegistrations->links() }}
                </div>
            </div>
        @endif
    </div>

    <script>
        // View details function
        function viewDetails(registrationId) {
            window.location.href = `/registration/view/${registrationId}`;
        }
    </script>
@endsection
