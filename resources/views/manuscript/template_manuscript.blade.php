@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>MANUSCRIPT / DATASHEET</h5>
                                </div>
                                <div class="text-end">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-light">
                                        <em class="icon ni ni-arrow-left"></em> Back
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form
                            action="{{ isset($existingResults) && $existingResults->isNotEmpty() ? route('create_test_result') : route('create_test_result') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Hidden inputs for IDs --}}
                            @if (isset($manuscripts) && $manuscripts->isNotEmpty())
                                <input type="hidden" name="registration_id"
                                    value="{{ $manuscripts->first()->registration->tr04_reference_id ?? '' }}">
                            @endif

                            <!-- Header Information Table -->
                            <div class="card card-bordered shadow-sm mb-3">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="bg-light fw-bold w-25">Test Report No:</td>
                                                <td class="w-25">
                                                    <span>{{ optional($manuscripts->first()->registration)->tr04_reference_id ?? 'N/A' }}</span>
                                                </td>
                                                <td class="bg-light fw-bold w-25">Date:</td>
                                                <td class="w-25">
                                                    <input type="date" class="form-control form-control-sm border-0"
                                                        name="test_date"
                                                        value="{{ old('test_date', $testDate ?? date('Y-m-d')) }}" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light fw-bold">No of samples:</td>
                                                <td>
                                                    <span>{{ optional($manuscripts->first()->registration)->tr04_number_of_samples ?? 'N/A' }}</span>
                                                </td>
                                                <td class="bg-light fw-bold">Sample Characteristics:</td>
                                                <td>
                                                    <span>{{ optional($manuscripts->first()->registration->labSample)->m14_name ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light fw-bold">Date of Performance of Tests:</td>
                                                <td>
                                                    <input type="date" class="form-control form-control-sm border-0"
                                                        name="performance_date"
                                                        value="{{ old('performance_date', $performanceDate ?? '') }}"
                                                        required>
                                                </td>
                                                <td class="bg-light fw-bold">Date of allotment of sample:</td>
                                                <td>
                                                    <span>{{ $manuscripts->first()->tr05_alloted_at ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-light fw-bold">QAO/JQAO/Analyst:</td>
                                                <td>
                                                    <span>{{ optional($manuscripts->first()->allotedTo)->m06_name ?? 'N/A' }}</span>
                                                </td>
                                                <td class="bg-light fw-bold">Technical Manager:</td>
                                                <td>
                                                   <span>{{ Session::get('role') === 'Manager' ? Session::get('name') : optional($manuscripts->first()->allotedBy)->m06_name ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Test Results Table -->
                            <div class="card card-bordered mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0 text-white">T E S T &nbsp;&nbsp; R E S U L T S</h6>
                                </div>
                                <div class="nk-block nk-block-lg">
                                    <div class="card card-preview">
                                        <table class="table table-tranx">
                                            <thead>
                                                <tr class="tb-tnx-head">
                                                    <th class="tb-tnx-id"><span class="">Sr. No.</span></th>
                                                    <th class="tb-tnx-info">
                                                        <span class="tb-tnx-desc d-none d-sm-inline-block">
                                                            <span>Test Name</span>
                                                        </span>
                                                    </th>
                                                    <th class="tb-tnx-amount">
                                                        <span class="tb-tnx-total">Entry</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($manuscripts as $key => $manuscript)
                                                    {{-- If manuscript(s) exist --}}
                                                    @if ($manuscript->manuscript && $manuscript->manuscript->count() > 0)
                                                        {{-- Print the parent test name (just as a heading row) --}}
                                                        <tr class="tb-tnx-item bg-light">
                                                            <td class="tb-tnx-id">
                                                                <a href="#"><span>{{ $key + 1 }}</span></a>
                                                            </td>
                                                            <td class="tb-tnx-info" colspan="2">
                                                                <div class="tb-tnx-desc fw-bold">
                                                                    <span
                                                                        class="title">{{ $manuscript->test->m12_name ?? 'N/A' }}</span>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        {{-- Print each manuscript as a sub-test --}}
                                                        @foreach ($manuscript->manuscript as $mKey => $manu)
                                                            @php
                                                                $existingResult = $existingResults
                                                                    ->where(
                                                                        'm12_test_number',
                                                                        $manuscript->m12_test_number,
                                                                    )
                                                                    ->where(
                                                                        'm22_manuscript_id',
                                                                        $manu->m22_manuscript_id,
                                                                    )
                                                                    ->first();
                                                            @endphp
                                                            <tr class="tb-tnx-item">
                                                                <td class="tb-tnx-id">
                                                                    <a
                                                                        href="#"><span>{{ $key + 1 }}.{{ $mKey + 1 }}</span></a>
                                                                </td>
                                                                <td class="tb-tnx-info">
                                                                    <div class="tb-tnx-desc">
                                                                        <span class="title">{{ $manu->m22_name }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="tb-tnx-amount">
                                                                    <div class="tb-tnx-total">
                                                                        {{-- Hidden inputs for tracking --}}
                                                                        <input type="hidden"
                                                                            name="manuscript_data[{{ $key }}][{{ $mKey }}][test_id]"
                                                                            value="{{ $manuscript->m12_test_number }}">
                                                                        <input type="hidden"
                                                                            name="manuscript_data[{{ $key }}][{{ $mKey }}][manuscript_id]"
                                                                            value="{{ $manu->m22_manuscript_id }}">
                                                                        @if ($existingResult)
                                                                            <input type="hidden"
                                                                                name="manuscript_data[{{ $key }}][{{ $mKey }}][result_id]"
                                                                                value="{{ $existingResult->tr07_test_result_id }}">
                                                                        @endif

                                                                        {{-- Result input --}}
                                                                        <input type="text"
                                                                            class="form-control form-control-sm border-0"
                                                                            name="manuscript_data[{{ $key }}][{{ $mKey }}][result]"
                                                                            value="{{ old('manuscript_data.' . $key . '.' . $mKey . '.result', $existingResult->tr07_result ?? '') }}"
                                                                            placeholder="Enter result value">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        {{-- No manuscript → print test directly --}}
                                                        @php
                                                            $existingResult = $existingResults
                                                                ->where('m12_test_number', $manuscript->m12_test_number)
                                                                ->whereNull('m22_manuscript_id')
                                                                ->first();
                                                        @endphp
                                                        <tr class="tb-tnx-item">
                                                            <td class="tb-tnx-id">
                                                                <a href="#"><span>{{ $key + 1 }}</span></a>
                                                            </td>
                                                            <td class="tb-tnx-info">
                                                                <div class="tb-tnx-desc">
                                                                    <span
                                                                        class="title">{{ $manuscript->test->m12_name ?? 'N/A' }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="tb-tnx-amount">
                                                                <div class="tb-tnx-total">
                                                                    {{-- Hidden input for test ID --}}
                                                                    <input type="hidden"
                                                                        name="test_data[{{ $key }}][test_id]"
                                                                        value="{{ $manuscript->m12_test_number }}">
                                                                    @if ($existingResult)
                                                                        <input type="hidden"
                                                                            name="test_data[{{ $key }}][result_id]"
                                                                            value="{{ $existingResult->tr07_test_result_id }}">
                                                                    @endif

                                                                    {{-- Result input --}}
                                                                    <input type="text"
                                                                        class="form-control form-control-sm border-0"
                                                                        name="test_data[{{ $key }}][result]"
                                                                        value="{{ old('test_data.' . $key . '.result', $existingResult->tr07_result ?? '') }}"
                                                                        placeholder="Enter result value">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div><!-- .card -->
                                </div><!-- nk-block -->
                            </div>

                            <!-- Signature Section -->
                            <div class="card card-bordered mb-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-center py-4" width="50%">
                                                    <div class="mb-4"
                                                        style="height: 60px; border-bottom: 1px solid #000; margin-bottom: 10px;">
                                                    </div>
                                                    <strong>Signature of QAO/JQAO</strong>
                                                </td>
                                                <td class="text-center py-4" width="50%">
                                                    <div class="mb-4"
                                                        style="height: 60px; border-bottom: 1px solid #000; margin-bottom: 10px;">
                                                    </div>
                                                    <strong>Signature of Technical Manager</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label">Additional Notes/Remarks</label>
                                                <textarea class="form-control" name="remarks" rows="2"
                                                    placeholder="Enter any additional observations or notes...">{{ old('remarks', $remarks ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        {{-- Print Button --}}
                                        @if (!isset($existingResults) || $existingResults->isEmpty())  
                                        <button type="button" class="btn btn-dark" onclick="window.print()">
                                            <em class="icon ni ni-printer"></em> Print / Save as PDF
                                        </button>
                                        @endif
                                        <div class="btn-group">
                                            @if (optional($existingResults->first())->tr07_result_status != 'SUBMITTED')
                                            <button type="submit" name="action" value="DRAFT"
                                                class="btn btn-outline-primary">
                                                <em class="icon ni ni-file-text"></em> Save as Draft
                                            </button>    
                                            @endif
                                            <button type="submit" name="action" value="SUBMITTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Save & Complete
                                            </button>
                                            @if (isset($existingResults) && $existingResults->isNotEmpty())
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-file-pdf"></em> Export
                                                    </button>
                                                    {{-- <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('manuscripts.export', ['id' => $manuscripts[0]->registration->tr04_reference_id, 'format' => 'pdf']) }}">
                                                                <em class="icon ni ni-file-pdf"></em> Export as PDF
                                                            </a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('manuscripts.export', ['id' => $manuscripts[0]->registration->tr04_reference_id, 'format' => 'excel']) }}">
                                                                <em class="icon ni ni-file-excel"></em> Export as Excel
                                                            </a></li>
                                                    </ul> --}}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .table-bordered> :not(caption)>*>* {
            border-width: 1px;
            border-color: #333;
        }

        .form-control.border-0:focus,
        .form-select.border-0:focus {
            box-shadow: none;
            border-color: transparent;
        }

        .table td {
            vertical-align: top;
            padding: 0.75rem 0.5rem;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 0.75rem 0.5rem;
        }

        .card-header.bg-primary {
            background-color: #007bff !important;
            border-bottom: 1px solid #333;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .form-control-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .small {
            font-size: 0.875rem;
        }

        @media print {

            .btn,
            .alert,
            .card.card-bordered:last-child {
                display: none !important;
            }

            .nk-header {
                display: none !important;
            }

            .nk-footer {
                display: none !important;
            }

            .card {
                border: 1px solid #333 !important;
                box-shadow: none !important;
            }

            .table-bordered> :not(caption)>*>* {
                border-width: 1px !important;
                border-color: #333 !important;
            }
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .nk-block-title {
                font-size: 1.25rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 0.5rem;
                width: 100%;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
@endsection
