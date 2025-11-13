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
                                            $currentStatus =
                                                $sample->testResult->where('tr07_is_current', 'YES')->first()
                                                    ->tr07_result_status ?? 'PENDING';
                                        @endphp
                                        <span
                                            class="fw-bold text-{{ $currentStatus === 'VERIFIED' ? 'success' : ($currentStatus === 'REJECTED' ? 'danger' : 'warning') }}">
                                            {{ $currentStatus }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Results Section -->
                    <div class="card card-bordered mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-dark">Test Results for Verification</h6>
                        </div>
                        <div class="card-body">
                            @foreach ($organizedResults as $testNumber => $testData)
                                @php
                                    $test = $testData['test'];
                                    $hasRevisions = false;
                                    // Check for revisions in all result types
                                    if ($testData['has_main_result']) {
                                        $hasRevisions = $testData['main_results']->contains(
                                            'tr07_result_status',
                                            'REVISED',
                                        );
                                    }
                                    if (!$hasRevisions && $testData['has_primary_tests']) {
                                        foreach ($testData['primary_results'] as $primaryData) {
                                            if (
                                                $primaryData['primary_result'] &&
                                                $primaryData['primary_result']->tr07_result_status === 'REVISED'
                                            ) {
                                                $hasRevisions = true;
                                                break;
                                            }
                                            if (
                                                $primaryData['secondary_results']->contains(
                                                    'tr07_result_status',
                                                    'REVISED',
                                                )
                                            ) {
                                                $hasRevisions = true;
                                                break;
                                            }
                                        }
                                    }
                                @endphp

                                <!-- Test Group -->
                                <div
                                    class="test-group border rounded p-3 mb-4 {{ $hasRevisions ? 'border-warning bg-warning bg-opacity-5' : 'border-light' }}">
                                    <!-- Test Header -->
                                    <div class="test-header mb-3 pb-2 border-bottom">
                                        <h6 class="text-primary mb-1">
                                            <strong>{{ $test->m12_name ?? 'N/A' }}</strong>
                                        </h6>
                                        @if ($test->standard?->m15_method)
                                            <small class="text-muted">
                                                Method: {{ $test->standard->m15_method }}
                                            </small>
                                        @endif
                                        @if ($test->m12_unit)
                                            <small class="text-muted ms-2">
                                                Unit: {{ $test->m12_unit }}
                                            </small>
                                        @endif
                                        @if ($hasRevisions)
                                            <small class="text-warning ms-2">
                                                <em class="icon ni ni-alert"></em> Has Revisions
                                            </small>
                                        @endif
                                    </div>

                                    <!-- Scenario 1: Main Test Only (No Primary Tests) -->
                                    @if (!$testData['has_primary_tests'] && $testData['has_main_result'])
                                        @foreach ($testData['main_results'] as $mainResult)
                                            <div class="result-row mb-3">
                                                <div class="result-row mb-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <strong class="text-dark">{{ $test->m12_name }}</strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            @if ($mainResult->tr07_result_status === 'REVISED' && isset($mainResult->old_version))
                                                                <div class="revision-comparison">
                                                                    <div class="d-flex align-items-center">
                                                                        <span
                                                                            class="text-danger text-decoration-line-through me-2">
                                                                            {{ $mainResult->old_version->tr07_result ?? 'N/A' }}
                                                                        </span>
                                                                        <em
                                                                            class="icon ni ni-arrow-right text-muted me-2"></em>
                                                                        <span class="text-success fw-bold">
                                                                            {{ $mainResult->tr07_result ?? 'N/A' }}
                                                                        </span>
                                                                        @if ($test->m12_unit)
                                                                            <small
                                                                                class="text-muted ms-1">({{ $test->m12_unit }})</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span
                                                                    class="fw-bold text-dark">{{ $mainResult->tr07_result ?? 'N/A' }}</span>
                                                                @if ($test->m12_unit)
                                                                    <small
                                                                        class="text-muted ms-1">({{ $test->m12_unit }})</small>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="col-md-2 text-end">
                                                            {{-- <span
                                                                class="badge bg-{{ $mainResult->tr07_result_status === 'RESULTED' ? 'success' : ($mainResult->tr07_result_status === 'REVISED' ? 'warning' : 'info') }}">
                                                                {{ $mainResult->tr07_result_status }}
                                                            </span>
                                                            <br> --}}
                                                            <small class="text-muted">
                                                                {{ $mainResult->tr07_test_date ? date('d M Y', strtotime($mainResult->tr07_test_date)) : 'N/A' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <!-- Scenario 2 & 3: Primary Tests (with or without Secondary Tests) -->
                                    @if ($testData['has_primary_tests'])
                                        @foreach ($testData['primary_results'] as $primaryData)
                                            @php
                                                $primaryTest = $primaryData['primary_test'];
                                                $primaryResult = $primaryData['primary_result'];
                                                $secondaryResults = $primaryData['secondary_results'];
                                            @endphp

                                            <div class="primary-test mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <strong
                                                            class="text-dark">{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
                                                        @if ($primaryTest->m16_requirement)
                                                            <br><small class="text-info">Required:
                                                                {{ $primaryTest->m16_requirement }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if ($primaryResult && $primaryResult->tr07_result_status === 'REVISED' && isset($primaryResult->old_version))
                                                            <div class="revision-comparison">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="text-danger text-decoration-line-through me-2">
                                                                        {{ $primaryResult->old_version->tr07_result ?? 'N/A' }}
                                                                    </span>
                                                                    <em class="icon ni ni-arrow-right text-muted me-2"></em>
                                                                    <span class="text-success fw-bold">
                                                                        {{ $primaryResult->tr07_result ?? 'N/A' }}
                                                                    </span>
                                                                    @if ($primaryTest->m16_unit)
                                                                        <small
                                                                            class="text-muted ms-1">({{ $primaryTest->m16_unit }})</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @elseif($primaryResult)
                                                            <span
                                                                class="fw-bold text-dark">{{ $primaryResult->tr07_result ?? 'N/A' }}</span>
                                                            @if ($primaryTest->m16_unit)
                                                                <small
                                                                    class="text-muted ms-1">({{ $primaryTest->m16_unit }})</small>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2 text-end">
                                                        @if ($primaryResult)
                                                            <small class="text-muted">
                                                                {{ $primaryResult->tr07_test_date ? date('d M Y', strtotime($primaryResult->tr07_test_date)) : 'N/A' }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Secondary Tests under this Primary -->
                                                @if ($secondaryResults->count() > 0)
                                                    @foreach ($secondaryResults as $secondaryResult)
                                                        <div class="secondary-test mt-2 ps-3 border-start border-success">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <span
                                                                        class="text-dark">{{ $secondaryResult->secondaryTest->m17_name ?? 'N/A' }}</span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if ($secondaryResult->tr07_result_status === 'REVISED' && isset($secondaryResult->old_version))
                                                                        <div class="revision-comparison">
                                                                            <div class="d-flex align-items-center">
                                                                                <span
                                                                                    class="text-danger text-decoration-line-through me-2">
                                                                                    {{ $secondaryResult->old_version->tr07_result ?? 'N/A' }}
                                                                                </span>
                                                                                <em
                                                                                    class="icon ni ni-arrow-right text-muted me-2"></em>
                                                                                <span class="text-success fw-bold">
                                                                                    {{ $secondaryResult->tr07_result ?? 'N/A' }}
                                                                                </span>
                                                                                @if ($secondaryResult->secondaryTest->m17_unit)
                                                                                    <small
                                                                                        class="text-muted ms-1">({{ $secondaryResult->secondaryTest->m17_unit }})</small>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <span
                                                                            class="fw-bold text-dark">{{ $secondaryResult->tr07_result ?? 'N/A' }}</span>
                                                                        @if ($secondaryResult->secondaryTest->m17_unit)
                                                                            <small
                                                                                class="text-muted ms-1">({{ $secondaryResult->secondaryTest->m17_unit }})</small>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-2 text-end">
                                                                    {{-- <span
                                                                        class="badge bg-{{ $secondaryResult->tr07_result_status === 'RESULTED' ? 'success' : ($secondaryResult->tr07_result_status === 'REVISED' ? 'warning' : 'info') }}">
                                                                        {{ $secondaryResult->tr07_result_status }}
                                                                    </span>
                                                                    <br> --}}
                                                                    <small class="text-muted">
                                                                        {{ $secondaryResult->tr07_test_date ? date('d M Y', strtotime($secondaryResult->tr07_test_date)) : 'N/A' }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    <!-- Custom Fields - Simple Approach -->
                                    @php
                                        $allCustomFields = \App\Models\CustomField::where(
                                            'tr04_reference_id',
                                            $sample->tr04_reference_id,
                                        )
                                            ->where('m12_test_number', $testNumber)
                                            ->whereIn('tr08_result_status', ['SUBMITTED', 'DRAFT', 'RESULTED'])
                                            ->get();
                                    @endphp

                                    @if ($allCustomFields->count() > 0)
                                        <div class="custom-fields">
                                            {{-- <h6 class="text-warning mb-2">
                                                <em class="icon ni ni-plus"></em> Custom Fields
                                            </h6> --}}

                                            @foreach ($allCustomFields as $customField)
                                                @php
                                                    $indentation = '';
                                                    $parentName = '';

                                                    if (!is_null($customField->m16_primary_test_id)) {
                                                        $indentation = 'ps-4';
                                                        // Find the primary test name
                                                        $primaryTest = $testData['primary_results']
                                                            ->where(
                                                                'primary_test.m16_primary_test_id',
                                                                $customField->m16_primary_test_id,
                                                            )
                                                            ->first();
                                                        $parentName = $primaryTest
                                                            ? $primaryTest['primary_test']->m16_name
                                                            : '';
                                                    }
                                                @endphp

                                                <div
                                                    class="custom-field mt-3 ps-3 border-start border-warning {{ $indentation }}">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <span
                                                                class="text-dark">{{ $customField->tr08_field_name }}</span>
                                                            @if ($parentName)
                                                                <small class="text-muted ms-2">(for
                                                                    {{ $parentName }})</small>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-4">
                                                            <span
                                                                class="fw-bold text-dark">{{ $customField->tr08_field_value }}</span>
                                                            @if ($customField->tr08_field_unit)
                                                                <small
                                                                    class="text-muted ms-1">({{ $customField->tr08_field_unit }})</small>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-2 text-end">
                                                            {{-- <span
                                                                class="badge bg-warning">{{ $customField->tr08_result_status }}</span>
                                                            <br> --}}
                                                            <small class="text-muted">
                                                                {{ $customField->tr08_test_date ? date('d M Y', strtotime($customField->tr08_test_date)) : 'N/A' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <!-- Verification Action Form -->
                    @if ($sample->testResult->where('tr07_is_current', 'YES')->whereIn('tr07_result_status', ['RESULTED', 'REVISED'])->count() > 0)
                        <div class="card card-bordered mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-dark">Verification Decision</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('verify_result', $sample->tr04_sample_registration_id) }}"
                                    method="POST" id="verificationForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="remarks" class="form-label">Remarks (optional)</label>
                                                <textarea name="remarks" id="remarks" class="form-control" rows="3"
                                                    placeholder="Enter verification remarks or observations...">{{ old('remarks') }}</textarea>
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

    <style>
        .test-group {
            background: #f8f9fa;
        }

        .primary-test {
            margin-left: 0;
            padding: 10px;
            border-radius: 5px;
            background: #f8f9fa;
        }

        .secondary-test {
            margin-left: 2rem;
            padding: 8px;
            border-radius: 3px;
            background: #ffffff;
        }

        .custom-field {
            margin-left: 1rem;
        }

        .border-start {
            border-left-width: 3px !important;
        }

        .revision-comparison {
            font-size: 0.9rem;
        }

        .btn-lg {
            padding: 12px 20px;
            font-weight: 500;
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

        // Add event listeners for the buttons
        document.addEventListener('DOMContentLoaded', function() {
            const verifyBtn = document.getElementById('verifyBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const form = document.getElementById('verificationForm');

            if (verifyBtn) {
                verifyBtn.addEventListener('click', async function() {
                    const confirmed = await confirmAction('verify');
                    if (confirmed) {
                        // Create a hidden input for the action
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'verify';
                        form.appendChild(actionInput);

                        // Submit the form
                        form.submit();
                    }
                });
            }

            if (rejectBtn) {
                rejectBtn.addEventListener('click', async function() {
                    const confirmed = await confirmAction('reject');
                    if (confirmed) {
                        // Create a hidden input for the action
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'reject';
                        form.appendChild(actionInput);

                        // Submit the form
                        form.submit();
                    }
                });
            }
        });
    </script>
@endsection
