@extends('layouts.app_back')

@section('content')
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title text-primary">Sample Result Verification</h3>
                        <div class="nk-block-des text-soft">
                            <p>Verify or Reject results for Sample ID: <span class="fw-bold text-dark">{{ $sample->tr04_reference_id }}</span></p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="{{ route('view_result_verification') }}" class="btn btn-outline-light btn-white">
                                <em class="icon ni ni-arrow-left"></em><span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- Sample Overview Card -->
                    <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner py-3">
                                <div class="row g-3 align-center">
                                    <div class="col-md-3">
                                        <div class="profile-stats">
                                            <span class="sub-text">Sample Type</span>
                                            <span class="h5">{{ $sample->labSample->m14_name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-stats">
                                            <span class="sub-text">Priority</span>
                                            <span class="badge badge-dim bg-{{ ($sample->tr04_sample_type ?? 'NORMAL') == 'TATKAL' ? 'danger' : 'info' }}">
                                                {{ $sample->tr04_sample_type ?? 'NORMAL' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-stats">
                                            <span class="sub-text">Registration Date</span>
                                            <span class="h5">{{ date('d M Y', strtotime($sample->created_at)) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-stats">
                                            <span class="sub-text">Total Tests</span>
                                            <span class="h5">{{ $sampleTests->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($sampleTests as $key => $sampleTest)
                        @php
                            $test = $sampleTest->test;
                            $primaryTests = $test->primaryTests;
                            $currentTestResults = $currentResults->where('m12_test_number', $test->m12_test_number);
                            $historicalTestResults = $historicalResults->where('m12_test_number', $test->m12_test_number);
                            
                            // Get manuscript from any result record for this test
                            $manuscript = $currentTestResults->first()->tr07_manuscript_content ?? 'No manuscript data available.';
                            $isRevised = $currentTestResults->contains('tr07_result_status', 'REVISED');
                        @endphp

                        <div class="col-12">
                            <div class="card card-bordered border-{{ $isRevised ? 'warning' : 'primary' }}">
                                <div class="card-header bg-lighter d-flex justify-content-between align-items-center py-2">
                                    <h6 class="mb-0">
                                        <span class="badge bg-primary me-2">{{ $key + 1 }}</span>
                                        {{ $test->m12_name }} 
                                        @if($test->standard?->m15_method)
                                            <span class="text-soft ms-2 small">({{ $test->standard->m15_method }})</span>
                                        @endif
                                        @if($isRevised) <span class="badge badge-outline-warning ms-2">Revised</span> @endif
                                    </h6>
                                    <small class="text-muted">Analyst: {{ $sampleTest->allotedTo->m06_name ?? 'N/A' }}</small>
                                </div>
                                <div class="card-inner p-0">
                                    <div class="row g-0">
                                        <!-- Result Table Section -->
                                        <div class="col-lg-7 border-end">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th width="80" class="text-center">Sr. No</th>
                                                            <th>Test Specification / Field</th>
                                                            <th width="200" class="text-center">Result (Original &rarr; Revised)</th>
                                                            <th width="100" class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $mainCurrent = $currentTestResults->filter(fn($r) => empty($r->m16_primary_test_id) && empty($r->m17_secondary_test_id))->first();
                                                            $mainHistorical = $historicalTestResults->filter(fn($r) => empty($r->m16_primary_test_id) && empty($r->m17_secondary_test_id))->sortByDesc('tr07_test_result_id')->first();
                                                        @endphp

                                                        @if ($primaryTests->isEmpty())
                                                            <tr>
                                                                <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                                                <td class="fw-bold">{{ $test->m12_name }}</td>
                                                                <td class="text-center">
                                                                    @if ($mainHistorical && (string)$mainHistorical->tr07_result !== (string)$mainCurrent->tr07_result)
                                                                        <div class="comparison-box">
                                                                            <span class="text-danger text-decoration-line-through me-1">{{ $mainHistorical->tr07_result ?? 'N/A' }}</span>
                                                                            <em class="icon ni ni-arrow-right small text-muted"></em>
                                                                            <span class="text-success fw-bold ms-1">{{ $mainCurrent->tr07_result ?? 'N/A' }}</span>
                                                                            @if ($mainCurrent->tr07_unit) <small class="text-muted">({{ $mainCurrent->tr07_unit }})</small> @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="fw-bold">{{ $mainCurrent->tr07_result ?? 'N/A' }}</span>
                                                                        @if ($mainCurrent && $mainCurrent->tr07_unit) <small class="text-muted">({{ $mainCurrent->tr07_unit }})</small> @endif
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($mainCurrent)
                                                                        <span class="badge badge-dim bg-{{ $mainCurrent->tr07_result_status === 'REVISED' ? 'warning' : 'success' }}">
                                                                            {{ $mainCurrent->tr07_result_status }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr class="table-lighter">
                                                                <td class="text-center fw-bold">{{ $key + 1 }}</td>
                                                                <td class="fw-bold">{{ $test->m12_name }}</td>
                                                                <td colspan="2" class="text-muted text-center italic small">Hierarchy follows below</td>
                                                            </tr>
                                                            @foreach ($primaryTests as $pKey => $primaryTest)
                                                                @include('verification.partials.verification_primary_row', [
                                                                    'key' => $key,
                                                                    'pKey' => $pKey,
                                                                    'test' => $test,
                                                                    'primaryTest' => $primaryTest,
                                                                    'currentResults' => $currentTestResults,
                                                                    'historicalResults' => $historicalTestResults,
                                                                    'customFields' => $customFields->where('m12_test_number', $test->m12_test_number),
                                                                    'historicalCustomFields' => $historicalCustomFields->where('m12_test_number', $test->m12_test_number)
                                                                ])
                                                            @endforeach
                                                        @endif

                                                        <!-- Main Test Custom Fields -->
                                                        @foreach ($customFields->where('m12_test_number', $test->m12_test_number)->filter(fn($f) => empty($f->m16_primary_test_id)) as $cf)
                                                            @php
                                                                $hCf = $historicalCustomFields->where('m12_test_number', $test->m12_test_number)->filter(fn($f) => $f->tr08_field_name === $cf->tr08_field_name && empty($f->m16_primary_test_id))->first();
                                                            @endphp
                                                            <tr class="custom-field-row table-light">
                                                                <td class="text-center small text-muted">{{ $key + 1 }}.C{{ $loop->iteration }}</td>
                                                                <td class="ps-3 small text-muted">{{ $cf->tr08_field_name }}</td>
                                                                <td class="text-center">
                                                                    @if ($hCf && (string)$hCf->tr08_field_value !== (string)$cf->tr08_field_value)
                                                                        <div class="comparison-box">
                                                                            <span class="text-danger text-decoration-line-through me-1">{{ $hCf->tr08_field_value ?? 'N/A' }}</span>
                                                                            <em class="icon ni ni-arrow-right small text-muted"></em>
                                                                            <span class="text-success fw-bold ms-1">{{ $cf->tr08_field_value ?? 'N/A' }}</span>
                                                                        </div>
                                                                    @else
                                                                        {{ $cf->tr08_field_value }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center small text-muted">Custom</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Manuscript Content Section -->
                                        <div class="col-lg-5 bg-lighter">
                                            <div class="p-3">
                                                <h6 class="overline-title text-soft mb-2">Manuscript & Calculations</h6>
                                                <div class="manuscript-viewer border bg-white p-3 rounded" style="max-height: 400px; overflow-y: auto; font-family: serif;">
                                                    {!! $manuscript !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Final Verification Actions -->
                    <div class="col-12 mt-4">
                        <div class="card card-bordered card-stretch">
                            <div class="card-inner">
                                <form action="{{ route('verify_result', $sample->tr04_sample_registration_id) }}" method="POST" id="verificationForm">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">General Remarks</label>
                                                <div class="form-control-wrap">
                                                    <textarea name="remarks" class="form-control no-resize" rows="3" placeholder="Add any general observations or reasons for rejection..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label text-danger">Re-assign To (If Rejecting)</label>
                                                <div class="form-control-wrap">
                                                    <select name="reassigned_analyst_id" class="form-select js-select2">
                                                        <option value="">Default Analyst</option>
                                                        @foreach ($analysts as $analyst)
                                                            <option value="{{ $analyst->m06_employee_id }}">{{ $analyst->m06_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-text text-muted small">Only used if rejecting.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-center justify-content-end">
                                            <div class="d-flex flex-column w-100 gap-2">
                                                <button type="button" onclick="submitVerification('verify')" class="btn btn-lg btn-success">
                                                    <em class="icon ni ni-check-circle-fill"></em> <span>Verify All Results</span>
                                                </button>
                                                <button type="button" onclick="submitVerification('reject')" class="btn btn-lg btn-danger">
                                                    <em class="icon ni ni-cross-circle-fill"></em> <span>Reject All Results</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="action" id="actionField">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .comparison-box {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .table-lighter { background-color: #f9fbfe; }
        .manuscript-viewer {
            box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
            line-height: 1.6;
            font-size: 14px;
        }
        .italic { font-style: italic; }
        .overline-title {
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
    </style>

    <script>
        function submitVerification(action) {
            const actionText = action === 'verify' ? 'VERIFY' : 'REJECT';
            const actionClass = action === 'verify' ? 'text-success' : 'text-danger';
            
            Swal.fire({
                title: 'Confirm Verification Action',
                html: `Are you sure you want to <b class="${actionClass}">${actionText}</b> all results for this sample?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'verify' ? '#1ee0ac' : '#e85347',
                cancelButtonColor: '#8094ae',
                confirmButtonText: `Yes, ${actionText} All`
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('actionField').value = action;
                    document.getElementById('verificationForm').submit();
                }
            });
        }
    </script>
@endsection
