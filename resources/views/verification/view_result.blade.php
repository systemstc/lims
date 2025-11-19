@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block nk-block-lg">

                    <!-- Header Section -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">Result Verification</h4>
                            <p class="text-muted mb-0">Sample Reference: {{ $sample->tr04_reference_id }}</p>
                        </div>
                        <a href="{{ route('view_result_verification') }}" class="btn btn-outline-primary btn-sm">
                            <em class="icon ni ni-back-alt-fill"></em> Back
                        </a>
                    </div>

                    <!-- Sample Information -->
                    <div class="card card-bordered mt-3">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 border-end">
                                    <small class="text-muted">Sample Type</small>
                                    <p class="mb-0 fw-bold">{{ $sample->labSample->m14_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3 border-end">
                                    <small class="text-muted">No. of Samples</small>
                                    <p class="mb-0 fw-bold">{{ $sample->tr04_number_of_samples }}</p>
                                </div>
                                <div class="col-md-3 border-end">
                                    <small class="text-muted">Total Tests</small>
                                    <p class="mb-0 fw-bold">{{ $organizedResults->count() }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Status</small>
                                    <p class="mb-0">
                                        @php
                                            $currentStatus = $sample->testResult->where('tr07_is_current', 'YES')->first()->tr07_result_status ?? 'PENDING';
                                        @endphp
                                        <span class="badge bg-{{ $currentStatus === 'VERIFIED' ? 'success' : ($currentStatus === 'REJECTED' ? 'danger' : 'warning') }}">
                                            {{ $currentStatus }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Results Section - Tabular View -->
                    <div class="card card-bordered mt-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark">Test Results for Verification</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showRevisionsOnly">
                                <label class="form-check-label" for="showRevisionsOnly">Show revisions only</label>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0" id="verificationTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="30%">Test Name</th>
                                            <th width="15%">Type</th>
                                            <th width="20%">Result</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Test Date</th>
                                            <th width="5%" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($organizedResults as $testNumber => $testData)
                                            @php
                                                $test = $testData['test'];
                                                $hasRevisions = false;
                                                
                                                // Check for revisions in all result types
                                                if ($testData['has_main_result']) {
                                                    $hasRevisions = $testData['main_results']->contains('tr07_result_status', 'REVISED');
                                                }
                                                if (!$hasRevisions && $testData['has_primary_tests']) {
                                                    foreach ($testData['primary_results'] as $primaryData) {
                                                        if ($primaryData['primary_result'] && $primaryData['primary_result']->tr07_result_status === 'REVISED') {
                                                            $hasRevisions = true;
                                                            break;
                                                        }
                                                        if ($primaryData['secondary_results']->contains('tr07_result_status', 'REVISED')) {
                                                            $hasRevisions = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <!-- Main Test Results -->
                                            @if (!$testData['has_primary_tests'] && $testData['has_main_result'])
                                                @foreach ($testData['main_results'] as $mainResult)
                                                    <tr class="{{ $mainResult->tr07_result_status === 'REVISED' ? 'table-warning revision-row' : '' }}">
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <strong>{{ $test->m12_name }}</strong>
                                                                {{-- @if ($mainResult->tr07_result_status === 'REVISED')
                                                                    <span class="badge bg-warning ms-2">Revised</span>
                                                                @endif --}}
                                                            </div>
                                                            @if ($test->standard?->m15_method)
                                                                <small class="text-muted d-block">Method: {{ $test->standard->m15_method }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary">Main Test</span>
                                                        </td>
                                                        <td>
                                                            @if ($mainResult->tr07_result_status === 'REVISED' && isset($mainResult->old_version))
                                                                <div class="revision-comparison">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="text-danger text-decoration-line-through me-2">
                                                                            {{ $mainResult->old_version->tr07_result ?? 'N/A' }}
                                                                        </span>
                                                                        <em class="icon ni ni-arrow-right text-muted me-2"></em>
                                                                        <span class="text-success fw-bold">
                                                                            {{ $mainResult->tr07_result ?? 'N/A' }}
                                                                        </span>
                                                                        @if ($mainResult->tr07_unit)
                                                                            <small class="text-muted ms-1">({{ $mainResult->tr07_unit }})</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="fw-bold text-dark">{{ $mainResult->tr07_result ?? 'N/A' }}</span>
                                                                @if ($mainResult->tr07_unit)
                                                                    <small class="text-muted ms-1">({{ $mainResult->tr07_unit }})</small>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold text-{{ $mainResult->tr07_result_status === 'RESULTED' ? 'success' : ($mainResult->tr07_result_status === 'REVISED' ? 'warning' : 'info') }}">
                                                                {{ $mainResult->tr07_result_status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                {{ $mainResult->tr07_test_date ? date('d M Y', strtotime($mainResult->tr07_test_date)) : 'N/A' }}
                                                            </small>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                                                data-test-name="{{ $test->m12_name }}"
                                                                data-result="{{ $mainResult->tr07_result ?? 'N/A' }}"
                                                                data-unit="{{ $mainResult->tr07_unit ?? '' }}"
                                                                data-status="{{ $mainResult->tr07_result_status }}"
                                                                data-date="{{ $mainResult->tr07_test_date ? date('d M Y', strtotime($mainResult->tr07_test_date)) : 'N/A' }}"
                                                                data-method="{{ $test->standard->m15_method ?? 'N/A' }}"
                                                                data-type="Main Test">
                                                                <em class="icon ni ni-eye"></em>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                            <!-- Primary Tests -->
                                            @if ($testData['has_primary_tests'])
                                                @foreach ($testData['primary_results'] as $primaryData)
                                                    @php
                                                        $primaryTest = $primaryData['primary_test'];
                                                        $primaryResult = $primaryData['primary_result'];
                                                        $secondaryResults = $primaryData['secondary_results'];
                                                    @endphp

                                                    @if($primaryResult)
                                                    <tr class="{{ $primaryResult->tr07_result_status === 'REVISED' ? 'table-warning revision-row' : '' }}">
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <strong>{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
                                                                {{-- @if ($primaryResult->tr07_result_status === 'REVISED')
                                                                    <span class="badge bg-warning ms-2">Revised</span>
                                                                @endif --}}
                                                            </div>
                                                            @if ($primaryTest->m16_requirement)
                                                                <small class="text-info d-block">Required: {{ $primaryTest->m16_requirement }}</small>
                                                            @endif
                                                            <small class="text-muted d-block">Parent: {{ $test->m12_name }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">Primary Test</span>
                                                        </td>
                                                        <td>
                                                            @if ($primaryResult && $primaryResult->tr07_result_status === 'REVISED' && isset($primaryResult->old_version))
                                                                <div class="revision-comparison">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="text-danger text-decoration-line-through me-2">
                                                                            {{ $primaryResult->old_version->tr07_result ?? 'N/A' }}
                                                                        </span>
                                                                        <em class="icon ni ni-arrow-right text-muted me-2"></em>
                                                                        <span class="text-success fw-bold">
                                                                            {{ $primaryResult->tr07_result ?? 'N/A' }}
                                                                        </span>
                                                                        @if ($primaryResult->tr07_unit)
                                                                            <small class="text-muted ms-1">({{ $primaryResult->tr07_unit }})</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($primaryResult)
                                                                <span class="fw-bold text-dark">{{ $primaryResult->tr07_result ?? 'N/A' }}</span>
                                                                @if ($primaryResult->tr07_unit)
                                                                    <small class="text-muted ms-1">({{ $primaryResult->tr07_unit }})</small>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($primaryResult)
                                                            <span class="fw-bold text-{{ $primaryResult->tr07_result_status === 'RESULTED' ? 'success' : ($primaryResult->tr07_result_status === 'REVISED' ? 'warning' : 'info') }}">
                                                                {{ $primaryResult->tr07_result_status }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($primaryResult)
                                                                <small class="text-muted">
                                                                    {{ $primaryResult->tr07_test_date ? date('d M Y', strtotime($primaryResult->tr07_test_date)) : 'N/A' }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                                                data-test-name="{{ $primaryTest->m16_name ?? 'N/A' }}"
                                                                data-result="{{ $primaryResult->tr07_result ?? 'N/A' }}"
                                                                data-unit="{{ $primaryResult->tr07_unit ?? '' }}"
                                                                data-status="{{ $primaryResult->tr07_result_status }}"
                                                                data-date="{{ $primaryResult->tr07_test_date ? date('d M Y', strtotime($primaryResult->tr07_test_date)) : 'N/A' }}"
                                                                data-requirement="{{ $primaryTest->m16_requirement ?? 'N/A' }}"
                                                                data-type="Primary Test"
                                                                data-parent="{{ $test->m12_name }}">
                                                                <em class="icon ni ni-eye"></em>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endif

                                                    <!-- Secondary Tests -->
                                                    @if ($secondaryResults->count() > 0)
                                                        @foreach ($secondaryResults as $secondaryResult)
                                                            <tr class="{{ $secondaryResult->tr07_result_status === 'REVISED' ? 'table-warning revision-row' : '' }}">
                                                                <td>
                                                                    <div class="d-flex align-items-center ps-3">
                                                                        <span class="text-dark">{{ $secondaryResult->secondaryTest->m17_name ?? 'N/A' }}</span>
                                                                        {{-- @if ($secondaryResult->tr07_result_status === 'REVISED')
                                                                            <span class="badge bg-warning ms-2">Revised</span>
                                                                        @endif --}}
                                                                    </div>
                                                                    <small class="text-muted d-block ps-3">Parent: {{ $primaryTest->m16_name ?? 'N/A' }}</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-secondary">Secondary Test</span>
                                                                </td>
                                                                <td>
                                                                    @if ($secondaryResult->tr07_result_status === 'REVISED' && isset($secondaryResult->old_version))
                                                                        <div class="revision-comparison">
                                                                            <div class="d-flex align-items-center">
                                                                                <span class="text-danger text-decoration-line-through me-2">
                                                                                    {{ $secondaryResult->old_version->tr07_result ?? 'N/A' }}
                                                                                </span>
                                                                                <em class="icon ni ni-arrow-right text-muted me-2"></em>
                                                                                <span class="text-success fw-bold">
                                                                                    {{ $secondaryResult->tr07_result ?? 'N/A' }}
                                                                                </span>
                                                                                @if ($secondaryResult->secondaryTest->m17_unit)
                                                                                    <small class="text-muted ms-1">({{ $secondaryResult->secondaryTest->m17_unit }})</small>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <span class="fw-bold text-dark">{{ $secondaryResult->tr07_result ?? 'N/A' }}</span>
                                                                        @if ($secondaryResult->tr07_unit)
                                                                            <small class="text-muted ms-1">({{ $secondaryResult->tr07_unit }})</small>
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="fw-bold text-{{ $secondaryResult->tr07_result_status === 'RESULTED' ? 'success' : ($secondaryResult->tr07_result_status === 'REVISED' ? 'warning' : 'info') }}">
                                                                        {{ $secondaryResult->tr07_result_status }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <small class="text-muted">
                                                                        {{ $secondaryResult->tr07_test_date ? date('d M Y', strtotime($secondaryResult->tr07_test_date)) : 'N/A' }}
                                                                    </small>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                                                        data-test-name="{{ $secondaryResult->secondaryTest->m17_name ?? 'N/A' }}"
                                                                        data-result="{{ $secondaryResult->tr07_result ?? 'N/A' }}"
                                                                        data-unit="{{ $secondaryResult->tr07_unit ?? $secondaryResult->secondaryTest->m17_unit ?? '' }}"
                                                                        data-status="{{ $secondaryResult->tr07_result_status }}"
                                                                        data-date="{{ $secondaryResult->tr07_test_date ? date('d M Y', strtotime($secondaryResult->tr07_test_date)) : 'N/A' }}"
                                                                        data-type="Secondary Test"
                                                                        data-parent="{{ $primaryTest->m16_name ?? 'N/A' }}">
                                                                        <em class="icon ni ni-eye"></em>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif

                                            <!-- Custom Fields -->
                                            @php
                                                $allCustomFields = \App\Models\CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
                                                    ->where('m12_test_number', $testNumber)
                                                    ->whereIn('tr08_result_status', ['SUBMITTED', 'DRAFT', 'RESULTED'])
                                                    ->get();
                                            @endphp

                                            @if ($allCustomFields->count() > 0)
                                                @foreach ($allCustomFields as $customField)
                                                    @php
                                                        $parentName = '';
                                                        if (!is_null($customField->m16_primary_test_id)) {
                                                            // Find the primary test name
                                                            $primaryTest = $testData['primary_results']
                                                                ->where('primary_test.m16_primary_test_id', $customField->m16_primary_test_id)
                                                                ->first();
                                                            $parentName = $primaryTest ? $primaryTest['primary_test']->m16_name : '';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center ps-3">
                                                                <span class="text-dark">{{ $customField->tr08_field_name }}</span>
                                                            </div>
                                                            @if ($parentName)
                                                                <small class="text-muted d-block ps-3">For: {{ $parentName }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning">Custom Field</span>
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold text-dark">{{ $customField->tr08_field_value }}</span>
                                                            @if ($customField->tr08_field_unit)
                                                                <small class="text-muted ms-1">({{ $customField->tr08_field_unit }})</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="text-warning fw-bold">{{ $customField->tr08_result_status }}</span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                {{ $customField->tr08_test_date ? date('d M Y', strtotime($customField->tr08_test_date)) : 'N/A' }}
                                                            </small>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                                                data-test-name="{{ $customField->tr08_field_name }}"
                                                                data-result="{{ $customField->tr08_field_value }}"
                                                                data-unit="{{ $customField->tr08_field_unit ?? '' }}"
                                                                data-status="{{ $customField->tr08_result_status }}"
                                                                data-date="{{ $customField->tr08_test_date ? date('d M Y', strtotime($customField->tr08_test_date)) : 'N/A' }}"
                                                                data-type="Custom Field"
                                                                data-parent="{{ $parentName }}">
                                                                <em class="icon ni ni-eye"></em>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Action Form -->
                    @if ($sample->testResult->where('tr07_is_current', 'YES')->whereIn('tr07_result_status', ['RESULTED', 'REVISED'])->count() > 0)
                        <div class="card card-bordered mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-dark">Verification Decision</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('verify_result', $sample->tr04_sample_registration_id) }}" method="POST" id="verificationForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="remarks" class="form-label">Remarks (optional)</label>
                                                <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Enter verification remarks or observations...">{{ old('remarks') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="verification-actions mt-5">
                                                <div class="d-grid gap-2">
                                                    <button type="button" id="verifyBtn" class="btn btn-success">
                                                        <em class="icon ni ni-check"></em>
                                                        Verify Results
                                                    </button>

                                                    <button type="button" id="rejectBtn" class="btn btn-danger">
                                                        <em class="icon ni ni-cross"></em>
                                                        Reject Results
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mt-4 text-center">
                            <em class="icon ni ni-check-circle"></em>
                            <strong>All results have been verified.</strong> No pending verification required.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Result Details Modal -->
    <div class="modal fade" id="resultDetailsModal" tabindex="-1" aria-labelledby="resultDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultDetailsModalLabel">Test Result Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Test Name:</div>
                        <div class="col-sm-8" id="detail-test-name"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Type:</div>
                        <div class="col-sm-8" id="detail-type"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Parent Test:</div>
                        <div class="col-sm-8" id="detail-parent"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Result:</div>
                        <div class="col-sm-8" id="detail-result"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Status:</div>
                        <div class="col-sm-8" id="detail-status"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Test Date:</div>
                        <div class="col-sm-8" id="detail-date"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Method/Requirement:</div>
                        <div class="col-sm-8" id="detail-method"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 0.75rem 0.5rem;
        }
        
        .revision-comparison {
            font-size: 0.9rem;
        }
        
        .badge {
            font-size: 0.7rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
        
        .ps-3 {
            padding-left: 1rem !important;
        }
        
        .view-details {
            padding: 0.25rem 0.5rem;
        }
        
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
    </style>

    <script>
        async function confirmAction(action) {
            const actionText = action === 'verify' ? 'verify' : 'reject';
            const actionColor = action === 'verify' ? '#3085d6' : '#d33';

            const result = await Swal.fire({
                title: `Are you sure you want to ${actionText} all test results?`,
                text: "This action will finalize the verification process.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: actionColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${actionText} all!`,
                cancelButtonText: 'Cancel',
                reverseButtons: true
            });

            return result.isConfirmed;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const verifyBtn = document.getElementById('verifyBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const form = document.getElementById('verificationForm');
            const showRevisionsOnly = document.getElementById('showRevisionsOnly');
            const verificationTable = document.getElementById('verificationTable');
            const viewDetailButtons = document.querySelectorAll('.view-details');
            const resultDetailsModal = new bootstrap.Modal(document.getElementById('resultDetailsModal'));

            // Filter revisions
            if (showRevisionsOnly) {
                showRevisionsOnly.addEventListener('change', function() {
                    const rows = verificationTable.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        if (this.checked) {
                            if (row.classList.contains('revision-row')) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        } else {
                            row.style.display = '';
                        }
                    });
                });
            }

            // View details button handlers
            viewDetailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const testName = this.getAttribute('data-test-name');
                    const result = this.getAttribute('data-result');
                    const unit = this.getAttribute('data-unit');
                    const status = this.getAttribute('data-status');
                    const date = this.getAttribute('data-date');
                    const method = this.getAttribute('data-method') || this.getAttribute('data-requirement') || 'N/A';
                    const type = this.getAttribute('data-type');
                    const parent = this.getAttribute('data-parent') || 'N/A';
                    
                    document.getElementById('detail-test-name').textContent = testName;
                    document.getElementById('detail-type').textContent = type;
                    document.getElementById('detail-parent').textContent = parent;
                    document.getElementById('detail-result').textContent = unit ? `${result} ${unit}` : result;
                    document.getElementById('detail-status').innerHTML = `<span class="badge bg-${status === 'RESULTED' ? 'success' : (status === 'REVISED' ? 'warning' : 'info')}">${status}</span>`;
                    document.getElementById('detail-date').textContent = date;
                    document.getElementById('detail-method').textContent = method;
                    
                    resultDetailsModal.show();
                });
            });

            // Verification buttons
            if (verifyBtn) {
                verifyBtn.addEventListener('click', async function() {
                    const confirmed = await confirmAction('verify');
                    if (confirmed) {
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'verify';
                        form.appendChild(actionInput);
                        form.submit();
                    }
                });
            }

            if (rejectBtn) {
                rejectBtn.addEventListener('click', async function() {
                    const confirmed = await confirmAction('reject');
                    if (confirmed) {
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'reject';
                        form.appendChild(actionInput);
                        form.submit();
                    }
                });
            }
        });
    </script>
@endsection