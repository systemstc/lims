@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $testResult->tr07_test_name }}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Patient ID: {{ $testResult->tr07_patient_id }} | Test Date:
                                        {{ $testResult->tr07_test_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <div class="toggle-expand-content">
                                        <ul class="nk-block-tools g-3">
                                            <li><a href="{{ route('test-results.index') }}"
                                                    class="btn btn-outline-light"><em
                                                        class="icon ni ni-arrow-left"></em><span>Back</span></a></li>
                                            @if ($testResult->tr07_status != 'finalized')
                                                <li><a href="{{ route('test-results.edit', $testResult->tr07_test_result_id) }}"
                                                        class="btn btn-outline-primary"><em
                                                            class="icon ni ni-edit"></em><span>Edit</span></a></li>
                                            @else
                                                <li><a href="#" class="btn btn-outline-info" data-bs-toggle="modal"
                                                        data-bs-target="#reviseModal"><em
                                                            class="icon ni ni-pen2"></em><span>Create Revision</span></a>
                                                </li>
                                            @endif
                                            <li><a href="{{ route('test-results.generate-report', $testResult->tr07_test_result_id) }}"
                                                    class="btn btn-primary"><em
                                                        class="icon ni ni-file-docs"></em><span>Generate Report</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-gs">
                            <!-- Main Content -->
                            <div class="col-lg-8">
                                <!-- Status Card -->
                                <div class="card card-bordered mb-4">
                                    <div class="card-inner">
                                        <div class="row g-4 align-center">
                                            <div class="col-md-3">
                                                <div class="media media-middle">
                                                    <div class="media-object">
                                                        @if ($testResult->tr07_status == 'draft')
                                                            <span
                                                                class="badge badge-lg badge-dot has-bg bg-warning">Draft</span>
                                                        @elseif($testResult->tr07_status == 'finalized')
                                                            <span
                                                                class="badge badge-lg badge-dot has-bg bg-success">Finalized</span>
                                                        @else
                                                            <span
                                                                class="badge badge-lg badge-dot has-bg bg-info">Revised</span>
                                                        @endif
                                                    </div>
                                                    <div class="media-content">
                                                        <span class="fw-medium">Status</span>
                                                        <span class="fs-sm text-soft d-block">Current:
                                                            v{{ $testResult->tr07_current_version }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <span class="fs-sm text-soft">Test Type</span>
                                                        <span
                                                            class="d-block fw-medium">{{ ucfirst($testResult->tr07_test_type) }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="fs-sm text-soft">Created By</span>
                                                        <span
                                                            class="d-block fw-medium">{{ $testResult->creator->name ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                @if ($testResult->tr07_status == 'draft')
                                                    <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal"
                                                        data-bs-target="#finalizeModal">
                                                        <em class="icon ni ni-check"></em><span>Finalize Result</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Version Details -->
                                @if ($testResult->currentVersion)
                                    <div class="card card-bordered">
                                        <div class="card-inner">
                                            <div class="card-head">
                                                <h5 class="card-title">Current Results (Version
                                                    {{ $testResult->tr07_current_version }})</h5>
                                                <div class="card-opt">
                                                    <span
                                                        class="fs-sm text-soft">{{ $testResult->currentVersion->tr07_changed_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </div>

                                            <!-- Test Values -->
                                            @if ($testResult->currentVersion->tr07_test_values && count($testResult->currentVersion->tr07_test_values) > 0)
                                                <div class="mb-4">
                                                    <h6 class="title">Test Parameters</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Parameter</th>
                                                                    <th>Value</th>
                                                                    <th>Unit</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($testResult->currentVersion->tr07_test_values as $value)
                                                                    <tr>
                                                                        <td class="fw-medium">
                                                                            {{ $value['parameter'] ?? 'N/A' }}</td>
                                                                        <td>{{ $value['value'] ?? 'N/A' }}</td>
                                                                        <td>{{ $value['unit'] ?? '-' }}</td>
                                                                        <td>
                                                                            @php
                                                                                $status = $value['status'] ?? 'normal';
                                                                                $statusClass =
                                                                                    [
                                                                                        'normal' => 'success',
                                                                                        'high' => 'warning',
                                                                                        'low' => 'info',
                                                                                        'critical' => 'danger',
                                                                                    ][$status] ?? 'secondary';
                                                                            @endphp
                                                                            <span
                                                                                class="badge badge-sm badge-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Findings -->
                                            <div class="mb-4">
                                                <h6 class="title">Findings</h6>
                                                <div class="bg-lighter p-3 rounded">
                                                    <p class="mb-0">
                                                        {{ $testResult->currentVersion->tr07_findings ?: 'No findings recorded.' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Interpretation -->
                                            @if ($testResult->currentVersion->tr07_interpretation)
                                                <div class="mb-4">
                                                    <h6 class="title">Interpretation</h6>
                                                    <div class="bg-lighter p-3 rounded">
                                                        <p class="mb-0">
                                                            {{ $testResult->currentVersion->tr07_interpretation }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Recommendations -->
                                            @if ($testResult->currentVersion->tr07_recommendations)
                                                <div class="mb-4">
                                                    <h6 class="title">Recommendations</h6>
                                                    <div class="bg-lighter p-3 rounded">
                                                        <p class="mb-0">
                                                            {{ $testResult->currentVersion->tr07_recommendations }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Change Reason -->
                                            @if ($testResult->currentVersion->tr07_change_reason && $testResult->tr07_current_version > 1)
                                                <div class="alert alert-info">
                                                    <h6 class="alert-heading">Change Reason
                                                        (v{{ $testResult->tr07_current_version }})</h6>
                                                    <p class="mb-0">{{ $testResult->currentVersion->tr07_change_reason }}
                                                    </p>
                                                    <hr>
                                                    <p class="mb-0 fs-sm">Changed by:
                                                        {{ $testResult->currentVersion->changedBy->name ?? 'N/A' }} on
                                                        {{ $testResult->currentVersion->tr07_changed_at->format('M d, Y H:i') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Sidebar -->
                            <div class="col-lg-4">
                                <!-- Version History -->
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h6 class="card-title">Version History</h6>
                                            <div class="card-opt">
                                                <a href="#" class="link link-sm" data-bs-toggle="modal"
                                                    data-bs-target="#compareModal">Compare Versions</a>
                                            </div>
                                        </div>

                                        @if ($testResult->versions->count() > 0)
                                            <div class="timeline">
                                                @foreach ($testResult->versions as $version)
                                                    <div class="timeline-item">
                                                        <div class="timeline-date">
                                                            @if ($version->tr07_is_current)
                                                                <span class="badge badge-success badge-sm">Current</span>
                                                            @endif
                                                            <span
                                                                class="fs-sm">v{{ $version->tr07_version_number }}</span>
                                                        </div>
                                                        <div class="timeline-content">
                                                            <h6 class="title">
                                                                {{ $version->tr07_change_reason ?: 'Initial version' }}
                                                            </h6>
                                                            <p class="fs-sm text-soft mb-1">
                                                                By: {{ $version->changedBy->name ?? 'N/A' }}<br>
                                                                {{ $version->tr07_changed_at->format('M d, Y H:i') }}
                                                            </p>
                                                            <a href="{{ route('test-results.view-version', [$testResult->tr07_test_result_id, $version->tr07_version_number]) }}"
                                                                class="btn btn-sm btn-outline-light">View</a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-soft">No version history available.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="card card-bordered mt-4">
                                    <div class="card-inner">
                                        <h6 class="card-title">Quick Actions</h6>
                                        <ul class="list-group list-group-flush">
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>Generate PDF Report</span>
                                                <a href="{{ route('test-results.generate-report', $testResult->tr07_test_result_id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-file-docs"></em>
                                                </a>
                                            </li>
                                            <li
                                                class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>View Audit Trail</span>
                                                <a href="{{ route('test-results.audit') }}?test_result_id={{ $testResult->tr07_test_result_id }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <em class="icon ni ni-eye"></em>
                                                </a>
                                            </li>
                                            @if ($testResult->tr07_status != 'finalized')
                                                <li
                                                    class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                    <span>Delete Result</span>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteResult({{ $testResult->tr07_test_result_id }})">
                                                        <em class="icon ni ni-trash"></em>
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Finalize Modal -->
    <div class="modal fade" id="finalizeModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalize Test Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('test-results.finalize', $testResult->tr07_test_result_id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Reason for Finalization</label>
                            <textarea class="form-control" name="finalize_reason" rows="3"
                                placeholder="Enter reason for finalizing this result..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> Once finalized, this result cannot be edited. Only revisions can be
                            created.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Finalize Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Revise Modal -->
    <div class="modal fade" id="reviseModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Revision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('test-results.revise', $testResult->tr07_test_result_id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Revision Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="revision_reason" rows="2"
                                placeholder="Enter reason for creating this revision..." required></textarea>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="form-label">Updated Findings</label>
                            <textarea class="form-control" name="findings" rows="4">{{ $testResult->currentVersion->tr07_findings ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Updated Interpretation</label>
                            <textarea class="form-control" name="interpretation" rows="3">{{ $testResult->currentVersion->tr07_interpretation ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Updated Recommendations</label>
                            <textarea class="form-control" name="recommendations" rows="3">{{ $testResult->currentVersion->tr07_recommendations ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">Create Revision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Compare Versions Modal -->
    <div class="modal fade" id="compareModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compare Versions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('test-results.compare-versions', $testResult->tr07_test_result_id) }}"
                    method="GET">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Version 1</label>
                                    <select class="form-select" name="version1" required>
                                        @foreach ($testResult->versions as $version)
                                            <option value="{{ $version->tr07_version_number }}">
                                                v{{ $version->tr07_version_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Version 2</label>
                                    <select class="form-select" name="version2" required>
                                        @foreach ($testResult->versions as $version)
                                            <option value="{{ $version->tr07_version_number }}"
                                                {{ $version->tr07_is_current ? 'selected' : '' }}>
                                                v{{ $version->tr07_version_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Compare</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this test result? This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteResult(id) {
            $('#deleteForm').attr('action', '{{ route('test-results.destroy', '') }}/' + id);
            $('#deleteModal').modal('show');
        }
    </script>
@endsection
