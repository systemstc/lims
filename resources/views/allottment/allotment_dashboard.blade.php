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
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Tests waiting for allotment"></em>
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
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Samples with some tests allotted"></em>
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
                            <div class="card-tools">
                                <em class="card-hint-icon icon ni ni-help" data-bs-toggle="tooltip"
                                    title="Tests fully allotted and ready"></em>
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

        <!-- Filters -->
        <div class="card card-bordered mb-4">
            <div class="card-inner">
                <form method="GET" action="{{ route('view_allottment') }}" class="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-control form-select">
                                <option value="">All Priorities</option>
                                <option value="Urgent" {{ request('priority') == 'Urgent' ? 'selected' : '' }}>Urgent
                                </option>
                                <option value="Normal" {{ request('priority') == 'Normal' ? 'selected' : '' }}>Normal
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control form-select">
                                <option value="">All Status</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial
                                </option>
                                <option value="urgent" {{ request('status') == 'urgent' ? 'selected' : '' }}>Urgent (3+
                                    days)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Days Pending</label>
                            <select name="days_pending" class="form-control form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('days_pending') == '1' ? 'selected' : '' }}>1+ days
                                </option>
                                <option value="3" {{ request('days_pending') == '3' ? 'selected' : '' }}>3+ days
                                </option>
                                <option value="7" {{ request('days_pending') == '7' ? 'selected' : '' }}>7+ days
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="btn-group d-flex">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('view_allottment') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pending Samples Table -->
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="nk-tb-list nk-tb-ulist">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col"><span class="sub-text">Registration ID</span></div>
                            <div class="nk-tb-col tb-col-lg"><span class="sub-text">Received Date</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Test Progress</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Priority</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                            <div class="nk-tb-col tb-col-md"><span class="sub-text">Days Pending</span></div>
                            <div class="nk-tb-col nk-tb-col-tools text-end">Actions</div>
                        </div>

                        @forelse($pendingRegistrations as $registration)
                        {{-- @dd($registration) --}}
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">#{{ $registration->tr04_reference_id }}</span>
                                            @if ($registration->received_tests > 0)
                                                <span class="badge bg-info ms-2">
                                                    <em class="icon ni ni-arrow-down"></em>
                                                    {{ $registration->received_tests }}
                                                </span>
                                            @endif
                                            @if ($registration->transferred_tests > 0)
                                                <span class="badge bg-warning ms-2">
                                                    <em class="icon ni ni-arrow-up"></em>
                                                    {{ $registration->transferred_tests }}
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
                                        [$status, $statusClass] = match (true) {
                                            $allottedPercentage == 0 => ['New', 'bg-warning'],
                                            $allottedPercentage == 100 => ['Complete', 'bg-success'],
                                            default => ['Partial', 'bg-info'],
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                </div>

                                <div class="nk-tb-col tb-col-md">
                                    @php
                                        $daysPending = $registration->created_at->floatDiffInDays(now());
                                        $daysPending = round($daysPending, 1);
                                        $urgencyClass = match (true) {
                                            $daysPending > 3 => 'text-danger fw-bold',
                                            $daysPending > 1 => 'text-warning',
                                            default => 'text-muted',
                                        };
                                    @endphp
                                    <span class="{{ $urgencyClass }}">
                                        {{ $daysPending }} days
                                        @if ($daysPending > 3)
                                            <em class="icon ni ni-alert-circle text-danger"></em>
                                        @endif
                                    </span>
                                </div>

                                <div class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li>
                                            <div class="dropdown">
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
                                                        @if ($registration->received_tests > 0)
                                                            <li>
                                                                <a
                                                                    href="{{ route('show_allotment', $registration->tr04_sample_registration_id) }}">
                                                                    <em class="icon ni ni-check-circle text-success"></em>
                                                                    <span>Accept Transfers</span>
                                                                </a>
                                                            </li>
                                                        @endif
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
                        @empty
                            <div class="nk-tb-item">
                                <div class="nk-tb-col text-center py-4">
                                    <div class="text-muted">
                                        <em class="icon ni ni-inbox" style="font-size: 2rem;"></em>
                                        <p class="mt-2">No pending allotments found</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
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

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
