@extends('layouts.app_back')

@section('content')
<style>
    .card-head-iso {
        background: #f1f4f8;
        border-bottom: 1px solid #dce2eb;
    }
    .badge-iso-title {
        background: #0f3959;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
        letter-spacing: 0.3px;
    }
    .table-tests th {
        background-color: #f8f9fc;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #526484;
    }
    .table-tests td {
        vertical-align: middle;
        font-size: 12.5px;
    }
    .summary-box {
        background: #f8f9fc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 2px solid #cbd5e1;
        font-size: 16px;
        font-weight: 700;
        color: #0971fe;
    }
</style>

<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="components-preview mx-auto">
                
                {{-- Header Block --}}
                <div class="nk-block-head nk-block-head-sm mb-3">
                    <div class="nk-block-between align-items-center">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title mb-0">
                                Laboratory In-Charge Modification & Contract Review Station
                                <span class="text-primary fs-18px ms-2">#{{ $sample->tr04_reference_id ?? $sample->tr04_sample_registration_id }}</span>
                            </h3>
                            <div class="nk-block-des text-soft">
                                <p class="mb-0">Modify sample parameters, review test feasibility, adjust standard methods, and record ISO 17025 decision.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('show_contract', $sample->tr04_sample_registration_id) }}" class="btn btn-outline-light bg-white text-dark me-2">
                                    <em class="ni ni-eye"></em> View Inspection Sheet
                                </a>
                                <a href="{{ route('view_contract_reviews') }}" class="btn btn-outline-primary">
                                    <em class="ni ni-arrow-left"></em> Reviews Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-icon alert-dismissible">
                        <em class="icon ni ni-cross-circle"></em>
                        <strong>Please check the errors below:</strong>
                        <ul class="mb-0 mt-1 fs-12px">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-icon alert-dismissible">
                        <em class="icon ni ni-check-circle"></em>
                        {{ session('success') }}
                        <button class="close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('edit_contract_details', $sample->tr04_sample_registration_id) }}" method="POST" id="editContractForm">
                    @csrf

                    {{-- Section 1: Customer Profile & Sample Metadata --}}
                    <div class="card card-bordered mb-3">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                            <h6 class="card-title fs-13px mb-0 text-primary">
                                <em class="ni ni-package me-1"></em> 1. Sample Details & Physical Receipt Verification (ISO Cl. 7.1 & 7.4)
                            </h6>
                            <span class="badge bg-outline-info">Client: {{ $sample->customer->m07_name ?? '—' }}</span>
                        </div>
                        <div class="card-inner py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Sample Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="tr04_sample_description" rows="2" placeholder="Enter customer declared sample description..." required>{{ old('tr04_sample_description', $sample->tr04_sample_description) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fabric / Sample / Batch Mark</label>
                                    <textarea class="form-control" name="tr04_sample_mark" rows="2" placeholder="Identification marks on sample (optional)...">{{ old('tr04_sample_mark', $sample->tr04_sample_mark) }}</textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Number of Samples / Qty</label>
                                    <input type="number" class="form-control" name="tr04_number_of_samples" value="{{ old('tr04_number_of_samples', $sample->tr04_number_of_samples ?? 1) }}" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Service Priority <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tr04_sample_type" id="selectServiceType" required>
                                        <option value="Normal" {{ (old('tr04_sample_type', $sample->tr04_sample_type) !== 'Tatkal') ? 'selected' : '' }}>Normal Priority (Standard TAT)</option>
                                        <option value="Tatkal" {{ (old('tr04_sample_type', $sample->tr04_sample_type) === 'Tatkal') ? 'selected' : '' }}>Tatkal Priority (1.5x Fee Surcharge)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Target Completion Date</label>
                                    <input type="date" class="form-control" name="tr04_expected_date" value="{{ old('tr04_expected_date', $sample->tr04_expected_date ? \Carbon\Carbon::parse($sample->tr04_expected_date)->format('Y-m-d') : '') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Commercial Type</label>
                                    <input type="text" class="form-control" name="tr04_commercial_type" value="{{ old('tr04_commercial_type', $sample->tr04_commercial_type ?? 'Commercial') }}" placeholder="e.g. Commercial / Export">
                                </div>

                                {{-- Sample Receipt Condition & Storage --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Physical Condition at Receipt (Cl. 7.4.3) <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tr04_sample_condition" required>
                                        <option value="INTACT_SEALED" {{ old('tr04_sample_condition', $sample->tr04_sample_condition) === 'INTACT_SEALED' ? 'selected' : '' }}>Intact & Sealed</option>
                                        <option value="INTACT_UNSEALED" {{ old('tr04_sample_condition', $sample->tr04_sample_condition) === 'INTACT_UNSEALED' ? 'selected' : '' }}>Intact but Unsealed</option>
                                        <option value="DAMAGED_ACCEPTABLE" {{ old('tr04_sample_condition', $sample->tr04_sample_condition) === 'DAMAGED_ACCEPTABLE' ? 'selected' : '' }}>Slightly Damaged (Acceptable for Testing)</option>
                                        <option value="DAMAGED_COMPROMISED" {{ old('tr04_sample_condition', $sample->tr04_sample_condition) === 'DAMAGED_COMPROMISED' ? 'selected' : '' }}>Compromised / Leaking (Unfit)</option>
                                        <option value="INSUFFICIENT_QTY" {{ old('tr04_sample_condition', $sample->tr04_sample_condition) === 'INSUFFICIENT_QTY' ? 'selected' : '' }}>Quantity Inadequate for Scope</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Laboratory Storage Condition <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tr04_storage_condition" required>
                                        <option value="AMBIENT" {{ old('tr04_storage_condition', $sample->tr04_storage_condition) === 'AMBIENT' ? 'selected' : '' }}>Ambient Room Temp (25±5°C)</option>
                                        <option value="STANDARD_ATMOSPHERE" {{ old('tr04_storage_condition', $sample->tr04_storage_condition) === 'STANDARD_ATMOSPHERE' ? 'selected' : '' }}>Standard Conditioning (20±2°C / 65±4% RH)</option>
                                        <option value="COLD_STORAGE" {{ old('tr04_storage_condition', $sample->tr04_storage_condition) === 'COLD_STORAGE' ? 'selected' : '' }}>Cold Storage (2°C - 8°C)</option>
                                        <option value="DESICCATOR" {{ old('tr04_storage_condition', $sample->tr04_storage_condition) === 'DESICCATOR' ? 'selected' : '' }}>Desiccator (Moisture Sensitive)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Decision Rule (Cl. 7.1.3) <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tr04_decision_rule" required>
                                        <option value="SIMPLE_ACCEPTANCE" {{ old('tr04_decision_rule', $sample->tr04_decision_rule) === 'SIMPLE_ACCEPTANCE' ? 'selected' : '' }}>Simple Acceptance (Binary Decision, 95% Conf.)</option>
                                        <option value="GUARD_BAND_ILAC_G8" {{ old('tr04_decision_rule', $sample->tr04_decision_rule) === 'GUARD_BAND_ILAC_G8' ? 'selected' : '' }}>Guard-banded Acceptance (ILAC G8, w=U)</option>
                                        <option value="CUSTOMER_SPECIFIED" {{ old('tr04_decision_rule', $sample->tr04_decision_rule) === 'CUSTOMER_SPECIFIED' ? 'selected' : '' }}>Customer Specified Rule (Documented)</option>
                                        <option value="NOT_APPLICABLE" {{ old('tr04_decision_rule', $sample->tr04_decision_rule) === 'NOT_APPLICABLE' ? 'selected' : '' }}>Not Applicable (No Statement of Conformity)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: ISO/IEC 17025 Clause 7.1 Feasibility Verification Checklist --}}
                    <div class="card card-bordered mb-3">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                            <h6 class="card-title fs-13px mb-0 text-primary">
                                <em class="ni ni-check-round-cut me-1"></em> 2. ISO/IEC 17025:2017 Clause 7.1 Feasibility & Capability Evaluation
                            </h6>
                            <span class="badge bg-light text-dark">Clause 7.1.1 - 7.1.8</span>
                        </div>
                        <div class="card-inner p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light fs-11px">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th style="min-width: 320px;">Clause 7.1 Checkpoint & Requirement</th>
                                            <th style="width: 200px;" class="text-center">Feasibility Status</th>
                                            <th>Verification Notes & Findings</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-12px">
                                        @php $i = 1; @endphp
                                        @foreach($checklist as $key => $item)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $i++ }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $item['title'] ?? ($item['question'] ?? $key) }}</span>
                                                    <input type="hidden" name="checklist[{{ $key }}][question]" value="{{ $item['title'] ?? ($item['question'] ?? $key) }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="custom-control custom-radio custom-control-sm me-3">
                                                            <input type="radio" class="custom-control-input" id="chk_{{ $key }}_yes" name="checklist[{{ $key }}][status]" value="YES" {{ ($item['status'] ?? 'YES') === 'YES' ? 'checked' : '' }}>
                                                            <label class="custom-control-label text-success fw-bold" for="chk_{{ $key }}_yes">YES</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-sm me-3">
                                                            <input type="radio" class="custom-control-input" id="chk_{{ $key }}_no" name="checklist[{{ $key }}][status]" value="NO" {{ ($item['status'] ?? '') === 'NO' ? 'checked' : '' }}>
                                                            <label class="custom-control-label text-danger fw-bold" for="chk_{{ $key }}_no">NO</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-sm">
                                                            <input type="radio" class="custom-control-input" id="chk_{{ $key }}_na" name="checklist[{{ $key }}][status]" value="NA" {{ ($item['status'] ?? '') === 'NA' ? 'checked' : '' }}>
                                                            <label class="custom-control-label text-muted" for="chk_{{ $key }}_na">N/A</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="checklist[{{ $key }}][remark]" value="{{ $item['remark'] ?? ($item['notes'] ?? '') }}" placeholder="Enter verification notes or findings (optional)...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Test Scope, Standards & Fee Recalculation Station --}}
                    <div class="card card-bordered mb-3">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between flex-wrap">
                            <h6 class="card-title fs-13px mb-0 text-primary my-1">
                                <em class="ni ni-flask me-1"></em> 3. Test Parameters, Standard Test Methods & Fee Adjustment
                            </h6>
                            <span class="text-muted fs-11px my-1">Add, replace or remove tests requested by customer</span>
                        </div>
                        <div class="card-inner py-3">
                            
                            {{-- Add New Test Panel --}}
                            <div class="card card-bordered bg-light p-3 mb-3">
                                <h6 class="fs-12px text-uppercase text-primary fw-bold mb-2">
                                    <em class="ni ni-plus-circle me-1"></em> Add Test Parameter to Sample Scope
                                </h6>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label fs-11px mb-1 fw-bold">Select Test Parameter</label>
                                        <select class="form-select form-select-sm" id="newTestSelect">
                                            <option value="">-- Choose Test Parameter --</option>
                                            @foreach($tests as $t)
                                                <option value="{{ $t->m12_test_id }}" data-price="{{ $t->m12_charge ?? $t->m12_charges ?? 0 }}">
                                                    {{ $t->m12_name }} (₹ {{ number_format($t->m12_charge ?? $t->m12_charges ?? 0, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fs-11px mb-1 fw-bold">Standard Method</label>
                                        <select class="form-select form-select-sm" id="newStandardSelect" disabled>
                                            <option value="">-- Select Test First --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-sm btn-primary w-100" id="btnAddTestRow">
                                            <em class="ni ni-plus"></em> Add to Scope
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Selected Tests Table --}}
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-tests mb-0" id="testsScopeTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th style="min-width: 200px;">Test Parameter Name</th>
                                            <th style="min-width: 200px;">Standard Test Method</th>
                                            <th style="min-width: 140px;" class="text-center">Lab Accreditation</th>
                                            <th style="min-width: 160px;">Remarks / Deviations</th>
                                            <th style="width: 110px;" class="text-end">Unit Fee (₹)</th>
                                            <th style="width: 50px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="testsScopeTbody">
                                        @forelse($sample->sampleTests as $idx => $st)
                                            @php
                                                $testObj = $st->test;
                                                $testStdIds = !empty($testObj?->m15_standard_id)
                                                    ? array_filter(array_map('trim', explode(',', $testObj->m15_standard_id)))
                                                    : [];
                                                $rowStandards = !empty($testStdIds)
                                                    ? $standards->whereIn('m15_standard_id', $testStdIds)->values()
                                                    : collect();

                                                // Ensure currently assigned standard is included
                                                if ($st->m15_standard_id && !$rowStandards->contains('m15_standard_id', $st->m15_standard_id)) {
                                                    $currStd = $standards->firstWhere('m15_standard_id', $st->m15_standard_id)
                                                        ?? \App\Models\Standard::find($st->m15_standard_id);
                                                    if ($currStd) {
                                                        $rowStandards->push($currStd);
                                                    }
                                                }

                                                // Fallback if test has no configured standards in master
                                                if ($rowStandards->isEmpty()) {
                                                    $rowStandards = $standards;
                                                }

                                                $selectedStdId = $st->m15_standard_id ?? ($rowStandards->first()?->m15_standard_id);
                                            @endphp
                                            <tr class="test-row" data-id="{{ $st->m12_test_id }}">
                                                <td class="text-center row-index">{{ $idx + 1 }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $testObj->m12_name ?? 'Test #'.$st->m12_test_id }}</span>
                                                    <input type="hidden" name="test_ids[]" value="{{ $st->m12_test_id }}">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm standard-select" name="standard_ids[]">
                                                        @if($rowStandards->count() > 1)
                                                            <option value="">-- Choose Standard Method --</option>
                                                        @endif
                                                        @foreach($rowStandards as $std)
                                                            @php
                                                                $stdMapItem = collect($testStandardsMap[$st->m12_test_id] ?? [])->firstWhere('m15_standard_id', $std->m15_standard_id);
                                                                $isStdAcc = $stdMapItem['is_accredited'] ?? false;
                                                                $isStdExp = $stdMapItem['is_expired'] ?? false;
                                                                $accTag = $isStdAcc ? ' [NABL Scope]' : ($isStdExp ? ' [Scope Expired]' : ' [Non-Accredited]');
                                                            @endphp
                                                            <option value="{{ $std->m15_standard_id }}" {{ $selectedStdId == $std->m15_standard_id ? 'selected' : '' }} data-accredited="{{ $isStdAcc ? '1' : '0' }}" data-expired="{{ $isStdExp ? '1' : '0' }}">
                                                                {{ $std->m15_method }}{{ $accTag }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center align-middle acc-col">
                                                    @if($st->is_lab_accredited)
                                                        <span class="badge bg-success-dim text-success fs-11px acc-badge">
                                                            <em class="ni ni-shield-check me-1"></em> NABL Scope
                                                        </span>
                                                        @if(!empty($st->accreditation_valid_till))
                                                            <div class="text-soft fs-10px mt-1">Till {{ \Carbon\Carbon::parse($st->accreditation_valid_till)->format('d M, Y') }}</div>
                                                        @endif
                                                    @elseif($st->is_accreditation_expired)
                                                        <span class="badge bg-warning-dim text-warning fs-11px acc-badge">
                                                            <em class="ni ni-alert-circle me-1"></em> Scope Expired
                                                        </span>
                                                        <div class="text-danger fs-10px mt-1">Expired {{ \Carbon\Carbon::parse($st->accreditation_valid_till)->format('d M, Y') }}</div>
                                                    @else
                                                        <span class="badge bg-light text-muted fs-11px border acc-badge">
                                                            Non-Accredited
                                                        </span>
                                                        <div class="text-soft fs-10px mt-1">Out of NABL scope</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="test_remarks[]" value="{{ $st->tr05_remark ?? '' }}" placeholder="Deviation note or test spec...">
                                                </td>
                                                <td class="text-end fw-bold test-price" data-price="{{ $testObj->m12_charge ?? $testObj->m12_charges ?? 0 }}">
                                                    ₹ {{ number_format($testObj->m12_charge ?? $testObj->m12_charges ?? 0, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-trigger btn-icon text-danger btn-remove-test" title="Remove Test">
                                                        <em class="icon ni ni-trash"></em>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="emptyTestsRow">
                                                <td colspan="7" class="text-center py-3 text-muted">
                                                    <em>No test parameters assigned yet. Please add tests using the panel above.</em>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Financial & Tax Recalculation Card --}}
                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <div class="summary-box">
                                        <h6 class="fs-12px text-uppercase text-muted fw-bold mb-2">Live Fee & Tax Calculation Preview</h6>
                                        <div class="summary-item">
                                            <span>Base Testing Subtotal:</span>
                                            <span class="fw-bold" id="calcSubtotal">₹ 0.00</span>
                                        </div>
                                        <div class="summary-item" id="tatkalSurchargeRow" style="display: none;">
                                            <span>Tatkal Priority Surcharge (50%):</span>
                                            <span class="fw-bold text-danger" id="calcTatkal">₹ 0.00</span>
                                        </div>
                                        <div class="summary-item" id="intraGstRow">
                                            <span>CGST (9%) + SGST (9%):</span>
                                            <span class="fw-bold text-muted" id="calcIntraGst">₹ 0.00</span>
                                        </div>
                                        <div class="summary-item" id="interGstRow" style="display: none;">
                                            <span>IGST (18%):</span>
                                            <span class="fw-bold text-muted" id="calcInterGst">₹ 0.00</span>
                                        </div>
                                        <div class="summary-total">
                                            <span>Grand Total Payable:</span>
                                            <span id="calcGrandTotal">₹ 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Section 4: Laboratory In-Charge Review Findings & Decision --}}
                    <div class="card card-bordered mb-4">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                            <h6 class="card-title fs-13px mb-0 text-primary">
                                <em class="ni ni-property-add me-1"></em> 4. ISO 17025 Review Decision & Lab In-Charge Sign-Off
                            </h6>
                            <span class="badge bg-outline-info">Clause 7.1.4</span>
                        </div>
                        <div class="card-inner py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Contract Review Decision <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tr04_contract_review_status" id="selectReviewStatus" required>
                                        <option value="ACCEPTED" {{ old('tr04_contract_review_status', $sample->tr04_contract_review_status) === 'ACCEPTED' ? 'selected' : '' }}>
                                            ACCEPTED - Laboratory Capable, Validated Method & Resources Available
                                        </option>
                                        <option value="ACCEPTED_WITH_DEVIATION" {{ in_array(old('tr04_contract_review_status', $sample->tr04_contract_review_status), ['ACCEPTED_WITH_DEVIATION', 'ACCEPTED_DEVIATION']) ? 'selected' : '' }}>
                                            ACCEPTED_WITH_DEVIATION - Accepted with Agreed Technical Deviation
                                        </option>
                                        <option value="CLARIFICATION_REQUIRED" {{ old('tr04_contract_review_status', $sample->tr04_contract_review_status) === 'CLARIFICATION_REQUIRED' ? 'selected' : '' }}>
                                            CLARIFICATION_REQUIRED - Clarification Needed from Customer
                                        </option>
                                        <option value="REJECTED" {{ old('tr04_contract_review_status', $sample->tr04_contract_review_status) === 'REJECTED' ? 'selected' : '' }}>
                                            REJECTED - Not Feasible / Parameters Outside Scope of Competence
                                        </option>
                                        <option value="PENDING" {{ old('tr04_contract_review_status', $sample->tr04_contract_review_status) === 'PENDING' ? 'selected' : '' }}>
                                            PENDING - Under Technical Review
                                        </option>
                                    </select>
                                    <div class="form-text fs-11px">Decision will be stamped with your login credentials into the audit trail.</div>
                                </div>

                                <div class="col-md-6" id="deviationCheckboxContainer" style="{{ in_array(old('tr04_contract_review_status', $sample->tr04_contract_review_status), ['ACCEPTED_WITH_DEVIATION', 'ACCEPTED_DEVIATION']) ? '' : 'display: none;' }}">
                                    <label class="form-label fw-bold text-warning">Customer Agreement on Deviation (Cl. 7.1.4)</label>
                                    <div class="custom-control custom-checkbox bg-warning-dim p-2 rounded border border-warning">
                                        <input type="checkbox" class="custom-control-input" id="chkAgreedDeviation" name="tr04_customer_agreed_deviation" value="1" {{ old('tr04_customer_agreed_deviation', $sample->tr04_customer_agreed_deviation) ? 'checked' : '' }}>
                                        <label class="custom-control-label fw-bold text-dark fs-12px" for="chkAgreedDeviation">
                                            Customer has officially confirmed agreement to method / scope deviations.
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Technical Findings, Deviation Justifications & Review Remarks <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="tr04_contract_review_remarks" rows="3" placeholder="Enter laboratory capability assessment, validated test method status, calibrated equipment confirmation, or reasons for deviation/clarification..." required>{{ old('tr04_contract_review_remarks', $sample->tr04_contract_review_remarks) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Submit Button Bar --}}
                    <div class="card card-bordered bg-light">
                        <div class="card-inner py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="my-1 me-3">
                                    <span class="fw-bold text-dark fs-13px">Commitment:</span>
                                    <span class="text-muted fs-12px ms-1">Updates sample metadata, syncs test parameters, recalculates tax, and signs-off review.</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap">
                                    <a href="{{ route('show_contract', $sample->tr04_sample_registration_id) }}" class="btn btn-outline-light bg-white text-dark me-2 my-1">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary my-1" id="btnSubmitReview">
                                        <em class="ni ni-check-circle-cut me-1"></em> Save Review & Update Sample Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const isIntraState = {{ (substr($ro->gst_no ?? ($ro->m04_gst ?? ''), 0, 2) === substr($sample->customerLocation->m08_gst ?? ($sample->customer->m07_gst ?? ''), 0, 2)) ? 'true' : 'false' }};
    const testStandardsMap = @json($testStandardsMap ?? []);

    function populateStandardOptions(selectElem, standardsList, selectedId) {
        selectElem.empty();
        if (!standardsList || standardsList.length === 0) {
            selectElem.append('<option value="">-- No standard configured --</option>');
            selectElem.prop('disabled', false);
            return;
        }

        if (standardsList.length > 1) {
            selectElem.append('<option value="">-- Choose Standard Method --</option>');
        }

        standardsList.forEach(std => {
            const isSel = (selectedId && std.m15_standard_id == selectedId) || (standardsList.length === 1 && !selectedId);
            const isAcc = std.is_accredited;
            const isExp = std.is_expired;
            const tag = isAcc ? ' [NABL Scope]' : (isExp ? ' [Scope Expired]' : ' [Non-Accredited]');
            selectElem.append(`<option value="${std.m15_standard_id}" ${isSel ? 'selected' : ''} data-accredited="${isAcc ? '1' : '0'}" data-expired="${isExp ? '1' : '0'}">${std.m15_method}${tag}</option>`);
        });

        selectElem.prop('disabled', false);
    }

    $(document).ready(function() {
        recalculateTotals();

        // Show/hide deviation agreement checkbox based on select
        $('#selectReviewStatus').on('change', function() {
            if ($(this).val() === 'ACCEPTED_WITH_DEVIATION' || $(this).val() === 'ACCEPTED_DEVIATION') {
                $('#deviationCheckboxContainer').slideDown(150);
            } else {
                $('#deviationCheckboxContainer').slideUp(150);
            }
        });

        // Dynamic badge update when standard selection changes
        $(document).on('change', '.standard-select', function() {
            const $row = $(this).closest('tr');
            const $selectedOpt = $(this).find('option:selected');
            const isAcc = $selectedOpt.data('accredited') == 1;
            const isExp = $selectedOpt.data('expired') == 1;

            let badgeHtml = '';
            if (isAcc) {
                badgeHtml = '<span class="badge bg-success-dim text-success fs-11px acc-badge"><em class="ni ni-shield-check me-1"></em> NABL Scope</span>';
            } else if (isExp) {
                badgeHtml = '<span class="badge bg-warning-dim text-warning fs-11px acc-badge"><em class="ni ni-alert-circle me-1"></em> Scope Expired</span>';
            } else {
                badgeHtml = '<span class="badge bg-light text-muted fs-11px border acc-badge">Non-Accredited</span><div class="text-soft fs-10px mt-1">Out of NABL scope</div>';
            }
            $row.find('.acc-col').html(badgeHtml);
        });

        // Priority change triggers recalculation
        $('#selectServiceType').on('change', function() {
            recalculateTotals();
        });

        // When a test is selected in the add test box, dynamically filter standards to ONLY those related to this test
        $('#newTestSelect').on('change', function() {
            const testId = $(this).val();
            const stdSelect = $('#newStandardSelect');

            if (!testId) {
                stdSelect.html('<option value="">-- Select Test First --</option>').prop('disabled', true);
                return;
            }

            const relatedStandards = testStandardsMap[testId] || [];

            if (relatedStandards.length === 0) {
                // Fallback AJAX if not preloaded in testStandardsMap
                $.ajax({
                    url: "{{ route('contract_standards_by_test') }}",
                    type: 'GET',
                    data: { test_id: testId },
                    success: function(res) {
                        const stList = res.standards || [];
                        populateStandardOptions(stdSelect, stList, null);
                    },
                    error: function() {
                        populateStandardOptions(stdSelect, [], null);
                    }
                });
            } else {
                populateStandardOptions(stdSelect, relatedStandards, null);
            }
        });

        // Add Test button click
        $('#btnAddTestRow').on('click', function() {
            const testSelect = $('#newTestSelect');
            const stdSelect = $('#newStandardSelect');
            const testId = testSelect.val();
            const testName = testSelect.find(':selected').text().split(' (₹')[0].trim();
            const testPrice = parseFloat(testSelect.find(':selected').data('price')) || 0;
            const chosenStdId = stdSelect.val();

            if (!testId) {
                Swal.fire('Notice', 'Please select a test parameter first.', 'info');
                return;
            }

            // Check if already in table
            if ($('#testsScopeTbody tr.test-row[data-id="' + testId + '"]').length > 0) {
                Swal.fire('Notice', 'This test parameter is already in the scope.', 'warning');
                return;
            }

            $('#emptyTestsRow').remove();

            // Build standard options strictly restricted to this test
            const relatedStandards = testStandardsMap[testId] || [];
            let stdOptions = '';
            let initialAcc = false;
            let initialExp = false;

            if (relatedStandards.length === 0) {
                stdOptions = '<option value="">-- No standard configured --</option>';
            } else {
                if (relatedStandards.length > 1) {
                    stdOptions += '<option value="">-- Choose Standard Method --</option>';
                }
                relatedStandards.forEach(std => {
                    const isSelected = (chosenStdId && std.m15_standard_id == chosenStdId) || (relatedStandards.length === 1 && !chosenStdId);
                    if (isSelected) {
                        initialAcc = std.is_accredited;
                        initialExp = std.is_expired;
                    }
                    const tag = std.is_accredited ? ' [NABL Scope]' : (std.is_expired ? ' [Scope Expired]' : ' [Non-Accredited]');
                    stdOptions += `<option value="${std.m15_standard_id}" ${isSelected ? 'selected' : ''} data-accredited="${std.is_accredited ? '1' : '0'}" data-expired="${std.is_expired ? '1' : '0'}">${std.m15_method}${tag}</option>`;
                });
            }

            let initialBadge = '<span class="badge bg-light text-muted fs-11px border acc-badge">Non-Accredited</span><div class="text-soft fs-10px mt-1">Out of NABL scope</div>';
            if (initialAcc) {
                initialBadge = '<span class="badge bg-success-dim text-success fs-11px acc-badge"><em class="ni ni-shield-check me-1"></em> NABL Scope</span>';
            } else if (initialExp) {
                initialBadge = '<span class="badge bg-warning-dim text-warning fs-11px acc-badge"><em class="ni ni-alert-circle me-1"></em> Scope Expired</span>';
            }

            const rowCount = $('#testsScopeTbody tr.test-row').length + 1;

            const newRow = `
                <tr class="test-row" data-id="${testId}">
                    <td class="text-center row-index">${rowCount}</td>
                    <td>
                        <span class="fw-bold text-dark">${testName}</span>
                        <input type="hidden" name="test_ids[]" value="${testId}">
                    </td>
                    <td>
                        <select class="form-select form-select-sm standard-select" name="standard_ids[]">
                            ${stdOptions}
                        </select>
                    </td>
                    <td class="text-center align-middle acc-col">${initialBadge}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="test_remarks[]" value="" placeholder="Deviation note or test spec...">
                    </td>
                    <td class="text-end fw-bold test-price" data-price="${testPrice}">
                        ₹ ${testPrice.toFixed(2)}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-trigger btn-icon text-danger btn-remove-test" title="Remove Test">
                            <em class="icon ni ni-trash"></em>
                        </button>
                    </td>
                </tr>
            `;

            $('#testsScopeTbody').append(newRow);
            testSelect.val('');
            stdSelect.html('<option value="">-- Select Test First --</option>').prop('disabled', true);
            reindexRows();
            recalculateTotals();
        });

        // Remove test row
        $(document).on('click', '.btn-remove-test', function() {
            if ($('#testsScopeTbody tr.test-row').length <= 1) {
                Swal.fire('Notice', 'A sample registration must have at least one test parameter.', 'warning');
                return;
            }
            $(this).closest('tr').remove();
            reindexRows();
            recalculateTotals();
        });

        // Form submit handler with validation
        $('#editContractForm').on('submit', function(e) {
            if ($('#testsScopeTbody tr.test-row').length === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Please add at least one test parameter.', 'error');
                return false;
            }
            $('#btnSubmitReview').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving Review...');
        });
    });

    function reindexRows() {
        $('#testsScopeTbody tr.test-row').each(function(index) {
            $(this).find('.row-index').text(index + 1);
        });
    }

    function recalculateTotals() {
        let subtotal = 0;
        $('#testsScopeTbody tr.test-row').each(function() {
            const price = parseFloat($(this).find('.test-price').data('price')) || 0;
            subtotal += price;
        });

        const isTatkal = $('#selectServiceType').val() === 'Tatkal';
        let tatkalAmount = 0;
        let taxableAmount = subtotal;

        if (isTatkal) {
            tatkalAmount = subtotal * 0.5;
            taxableAmount = subtotal + tatkalAmount;
            $('#tatkalSurchargeRow').show();
            $('#calcTatkal').text('₹ ' + tatkalAmount.toFixed(2));
        } else {
            $('#tatkalSurchargeRow').hide();
        }

        $('#calcSubtotal').text('₹ ' + subtotal.toFixed(2));

        let cgst = 0, sgst = 0, igst = 0;
        if (isIntraState) {
            cgst = taxableAmount * 0.09;
            sgst = taxableAmount * 0.09;
            $('#intraGstRow').show();
            $('#interGstRow').hide();
            $('#calcIntraGst').text('₹ ' + (cgst + sgst).toFixed(2));
        } else {
            igst = taxableAmount * 0.18;
            $('#intraGstRow').hide();
            $('#interGstRow').show();
            $('#calcInterGst').text('₹ ' + igst.toFixed(2));
        }

        const grandTotal = taxableAmount + cgst + sgst + igst;
        $('#calcGrandTotal').text('₹ ' + grandTotal.toFixed(2));
    }
</script>
@endsection
