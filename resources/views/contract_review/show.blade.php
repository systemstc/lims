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
    .check-yes {
        color: #1ee0ac;
        font-weight: bold;
    }
    .check-no {
        color: #e85347;
        font-weight: bold;
    }
    .check-na {
        color: #8094ae;
        font-weight: bold;
    }
    .meta-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #8094ae;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .meta-value {
        font-size: 13px;
        font-weight: 600;
        color: #364a63;
    }
    .signature-box {
        border: 2px dashed #dbdfea;
        border-radius: 6px;
        padding: 12px;
        background: #fafbfd;
    }
</style>

<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="components-preview mx-auto">
                
                {{-- Top Navigation & Action Bar --}}
                <div class="nk-block-head nk-block-head-sm mb-3">
                    <div class="nk-block-between align-items-center">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title mb-0">
                                Contract Review & Technical Verification
                                <span class="text-primary fs-18px ms-2">#{{ $sample->tr04_reference_id ?? $sample->tr04_sample_registration_id }}</span>
                            </h3>
                            <div class="nk-block-des text-soft">
                                <p class="mb-0">Technical Evaluation Record & Resource Feasibility Assessment</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="d-flex align-items-center flex-wrap">
                                <a href="{{ route('view_contract_reviews') }}" class="btn btn-outline-light bg-white text-dark me-2 mb-1">
                                    <em class="ni ni-arrow-left"></em> Back to Reviews
                                </a>
                                <a href="{{ route('print_contract_review', $sample->tr04_sample_registration_id) }}" target="_blank" class="btn btn-outline-secondary me-2 mb-1">
                                    <em class="ni ni-printer"></em> Print Review Sheet
                                </a>
                                <a href="{{ route('edit_contract_details', $sample->tr04_sample_registration_id) }}" class="btn btn-primary mb-1">
                                    <em class="ni ni-edit"></em> Modify Sample & Tests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Alert Banner --}}
                <div class="nk-block mb-3">
                    @php
                        $status = strtoupper($sample->tr04_contract_review_status ?? 'PENDING');
                        $statusClass = 'alert-warning';
                        $statusIcon = 'ni-clock';
                        $statusText = 'Pending Lab Review';
                        if ($status === 'ACCEPTED') {
                            $statusClass = 'alert-success';
                            $statusIcon = 'ni-check-circle-fill';
                            $statusText = 'Contract Accepted & Feasible';
                        } elseif ($status === 'ACCEPTED_WITH_DEVIATION' || $status === 'ACCEPTED_DEVIATION') {
                            $statusClass = 'alert-info';
                            $statusIcon = 'ni-alert-circle-fill';
                            $statusText = 'Accepted with Technical Deviation';
                        } elseif ($status === 'CLARIFICATION_REQUIRED' || $sample->tr04_is_under_clarification) {
                            $statusClass = 'alert-primary';
                            $statusIcon = 'ni-chat-circle-fill';
                            $statusText = 'Clarification Required from Customer';
                        } elseif ($status === 'REJECTED') {
                            $statusClass = 'alert-danger';
                            $statusIcon = 'ni-cross-circle-fill';
                            $statusText = 'Contract Rejected / Not Feasible';
                        }
                    @endphp
                    <div class="alert {{ $statusClass }} alert-pro border">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center me-3 my-1">
                                <em class="icon ni {{ $statusIcon }} fs-24px me-3"></em>
                                <div>
                                    <h6 class="mb-0 fs-14px">Contract Review Status: <span class="fw-bold">{{ $statusText }}</span></h6>
                                    <div class="fs-12px mt-1">
                                        @if($sample->tr04_contract_reviewed_at)
                                            Reviewed by <strong>{{ $sample->tr04_contract_reviewed_by_name ?? 'Laboratory In-Charge' }}</strong> 
                                            ({{ $sample->tr04_contract_reviewed_by_role ?? 'Technical Manager' }}) on 
                                            {{ $sample->tr04_contract_reviewed_at->format('d M Y, h:i A') }}
                                        @else
                                            Sample is pending technical evaluation and resource feasibility check by Laboratory In-Charge.
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center my-1">
                                <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#quickDecisionModal">
                                    <em class="ni ni-exchange me-1"></em> Quick Decision
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sample & Customer Metadata Cards --}}
                <div class="nk-block mb-3">
                    <div class="row g-3">
                        {{-- Customer & Receipt Details --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner-group">
                                    <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                                        <h6 class="card-title fs-13px mb-0 text-primary">
                                            <em class="ni ni-building me-1"></em> 1. Customer & General Contract Info
                                        </h6>
                                        <span class="badge bg-outline-primary">Client ID: #{{ $sample->customer->m07_customer_id ?? '—' }}</span>
                                    </div>
                                    <div class="card-inner py-3">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="meta-label">Customer / Organization</div>
                                                <div class="meta-value">{{ $sample->customer->m07_name ?? '—' }}</div>
                                                <div class="text-muted fs-11px">{{ $sample->customer->m07_contact_person ?? '' }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Location / Address</div>
                                                <div class="meta-value">{{ $sample->customerLocation->m08_address ?? ($sample->customer->m07_address ?? '—') }}</div>
                                                <div class="text-muted fs-11px">GSTIN: {{ $sample->customerLocation->m08_gst ?? ($sample->customer->m07_gst ?? 'Unregistered / Exempt') }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Receipt Date & Time</div>
                                                <div class="meta-value">{{ $sample->created_at ? $sample->created_at->format('d-M-Y H:i') : '—' }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Target Completion Date</div>
                                                <div class="meta-value {{ $sample->tr04_expected_date ? 'text-primary' : '' }}">
                                                    {{ $sample->tr04_expected_date ? \Carbon\Carbon::parse($sample->tr04_expected_date)->format('d-M-Y') : '—' }}
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Service Priority</div>
                                                <div class="meta-value">
                                                    @if(strtolower($sample->tr04_sample_type ?? '') === 'tatkal' || ($sample->tr04_priority ?? 0) == 1)
                                                        <span class="badge bg-danger"><em class="ni ni-zap me-1"></em>TATKAL (Urgent)</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">Normal Priority</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Regional Office / Laboratory</div>
                                                <div class="meta-value">{{ $sample->ro->lab_name_en ?? ($sample->ro->m04_name ?? '—') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sample Physical Condition & Matrix --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner-group">
                                    <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                                        <h6 class="card-title fs-13px mb-0 text-primary">
                                            <em class="ni ni-package me-1"></em> 2. Sample Description & Physical Condition
                                        </h6>
                                        <span class="badge bg-outline-info">Cl. 7.4 Handling</span>
                                    </div>
                                    <div class="card-inner py-3">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="meta-label">Sample Description (Customer Declaration)</div>
                                                <div class="meta-value text-dark">{{ $sample->tr04_sample_description ?: '—' }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Sample Matrix / Group</div>
                                                <div class="meta-value">{{ $sample->labSample->m14_name ?? ($sample->department->m13_name ?? '—') }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Fabric / Batch / Sample Mark</div>
                                                <div class="meta-value">{{ $sample->tr04_sample_mark ?: '—' }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Physical Condition at Receipt</div>
                                                <div class="meta-value">
                                                    @php
                                                        $cond = $sample->tr04_sample_condition ?? 'INTACT_SEALED';
                                                    @endphp
                                                    @if($cond === 'INTACT_SEALED')
                                                        <span class="badge bg-success"><em class="ni ni-check me-1"></em>Intact & Sealed</span>
                                                    @elseif($cond === 'INTACT_UNSEALED')
                                                        <span class="badge bg-warning"><em class="ni ni-alert me-1"></em>Intact (Unsealed)</span>
                                                    @elseif($cond === 'DAMAGED_ACCEPTABLE')
                                                        <span class="badge bg-warning"><em class="ni ni-alert-circle me-1"></em>Damaged (Acceptable)</span>
                                                    @else
                                                        <span class="badge bg-danger"><em class="ni ni-cross me-1"></em>Compromised / Damaged</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Laboratory Storage Condition</div>
                                                <div class="meta-value">
                                                    <span class="badge bg-light text-dark border">
                                                        <em class="ni ni-snowflake me-1"></em>{{ str_replace('_', ' ', $sample->tr04_storage_condition ?? 'AMBIENT') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Sample Count / Quantity</div>
                                                <div class="meta-value">{{ $sample->tr04_number_of_samples ?? 1 }} Sample(s) / Unit</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="meta-label">Decision Rule (Cl. 7.1.3)</div>
                                                <div class="meta-value text-primary fs-12px">
                                                    {{ str_replace('_', ' ', $sample->tr04_decision_rule ?? 'SIMPLE_ACCEPTANCE') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ISO/IEC 17025 Clause 7.1 Feasibility Checklist --}}
                <div class="nk-block mb-3">
                    <div class="card card-bordered">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                            <h6 class="card-title fs-13px mb-0 text-primary">
                                <em class="ni ni-check-round-cut me-1"></em> 3. ISO/IEC 17025:2017 Clause 7.1 Review Checklist & Capability Matrix
                            </h6>
                            <span class="badge bg-light text-dark">Technical Evaluation</span>
                        </div>
                        <div class="card-inner p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr class="fs-12px">
                                            <th style="width: 50px;" class="text-center">#</th>
                                            <th style="min-width: 320px;">Clause 7.1 Requirement & Verification Element</th>
                                            <th style="width: 140px;" class="text-center">Feasibility Status</th>
                                            <th>Technical Findings / Verification Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-13px">
                                        @php $chkIndex = 1; @endphp
                                        @foreach($checklist as $key => $item)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $chkIndex++ }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item['title'] ?? ($item['question'] ?? $key) }}</div>
                                                    <div class="text-muted fs-11px">{{ $item['description'] ?? 'ISO/IEC 17025 compliance checkpoint' }}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if(($item['status'] ?? 'YES') === 'YES')
                                                        <span class="badge bg-success-dim text-success px-2 py-1">
                                                            <em class="ni ni-check-circle me-1"></em> SATISFACTORY
                                                        </span>
                                                    @elseif(($item['status'] ?? 'YES') === 'NO')
                                                        <span class="badge bg-danger-dim text-danger px-2 py-1">
                                                            <em class="ni ni-cross-circle me-1"></em> DEFICIENT
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted px-2 py-1">
                                                            <em class="ni ni-minus-circle me-1"></em> NOT APPLICABLE
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-muted fs-12px">
                                                    {{ $item['remark'] ?? ($item['notes'] ?? '—') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requested Test Parameters & Standard Methods --}}
                <div class="nk-block mb-3">
                    <div class="card card-bordered">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                            <h6 class="card-title fs-13px mb-0 text-primary">
                                <em class="ni ni-flask me-1"></em> 4. Requested Test Parameters, Standard Methods & Laboratory Capability
                            </h6>
                            <a href="{{ route('edit_contract_details', $sample->tr04_sample_registration_id) }}" class="btn btn-xs btn-outline-primary">
                                <em class="ni ni-edit me-1"></em> Modify Tests / Standards
                            </a>
                        </div>
                        <div class="card-inner p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr class="fs-12px">
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th>Test Parameter Name</th>
                                            <th>Standard Test Method</th>
                                            <th>
                                                Accreditation Scope
                                                @if(!empty($sample->ro))
                                                    <span class="text-muted fw-normal fs-11px d-block">({{ $sample->ro->lab_name_en ?? ($sample->ro->m04_name ?? 'Lab Scope') }})</span>
                                                @endif
                                            </th>
                                            <th class="text-end" style="width: 120px;">Fee (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-13px">
                                        @forelse($sample->sampleTests as $index => $testItem)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $testItem->test->m12_name ?? 'Test #'.$testItem->m12_test_id }}</div>
                                                    @if($testItem->tr05_remark)
                                                        <div class="text-info fs-11px"><em class="ni ni-info me-1"></em>{{ $testItem->tr05_remark }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-outline-secondary fs-11px">
                                                        {{ $testItem->standard->m15_method ?? 'Standard Method' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($testItem->is_lab_accredited)
                                                        <span class="badge bg-success-dim text-success fs-11px">
                                                            <em class="ni ni-shield-check me-1"></em> NABL Scope
                                                        </span>
                                                        @if(!empty($testItem->accreditation_valid_till))
                                                            <div class="text-soft fs-11px mt-1">
                                                                <em class="ni ni-calendar me-1"></em>Valid till {{ \Carbon\Carbon::parse($testItem->accreditation_valid_till)->format('d M, Y') }}
                                                            </div>
                                                        @endif
                                                    @elseif($testItem->is_accreditation_expired)
                                                        <span class="badge bg-warning-dim text-warning fs-11px">
                                                            <em class="ni ni-alert-circle me-1"></em> Scope Expired
                                                        </span>
                                                        <div class="text-danger fs-10px mt-1">
                                                            Expired on {{ \Carbon\Carbon::parse($testItem->accreditation_valid_till)->format('d M, Y') }}
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted fs-11px border">
                                                            <em class="ni ni-na me-1"></em> Non-Accredited
                                                        </span>
                                                        <div class="text-soft fs-10px mt-1">Out of NABL scope for this lab</div>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold">
                                                    ₹ {{ number_format($testItem->test->m12_charge ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">
                                                    <em>No individual test parameters registered for this sample.</em>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Basic Testing Total:</td>
                                            <td class="text-end fw-bold">₹ {{ number_format($sample->tr04_testing_charges ?? ($sample->tr04_total_charges / 1.18), 2) }}</td>
                                        </tr>
                                        @if(($sample->tr04_cgst ?? 0) > 0 || ($sample->tr04_sgst ?? 0) > 0)
                                            <tr>
                                                <td colspan="4" class="text-end text-muted fs-12px">CGST (9%) + SGST (9%):</td>
                                                <td class="text-end text-muted fs-12px">₹ {{ number_format(($sample->tr04_cgst ?? 0) + ($sample->tr04_sgst ?? 0), 2) }}</td>
                                            </tr>
                                        @elseif(($sample->tr04_igst ?? 0) > 0)
                                            <tr>
                                                <td colspan="4" class="text-end text-muted fs-12px">IGST (18%):</td>
                                                <td class="text-end text-muted fs-12px">₹ {{ number_format($sample->tr04_igst ?? 0, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td colspan="4" class="text-end fw-bold fs-14px">Grand Total Fee (Incl. Taxes):</td>
                                            <td class="text-end fw-bold fs-14px text-primary">₹ {{ number_format($sample->tr04_total_charges ?? 0, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Review Findings & Technical Sign-Off Block --}}
                <div class="nk-block mb-3">
                    <div class="card card-bordered">
                        <div class="card-inner py-2 card-head-iso d-flex align-items-center justify-content-between">
                            <h6 class="card-title fs-13px mb-0 text-primary">
                                <em class="ni ni-property-add me-1"></em> 5. Laboratory In-Charge Findings & Review Sign-Off
                            </h6>
                            <span class="badge bg-outline-info">ISO/IEC 17025 Cl. 7.1 Record</span>
                        </div>
                        <div class="card-inner py-3">
                            <div class="row g-3">
                                <div class="col-lg-7">
                                    <div class="meta-label">Technical Findings, Deviation Justifications & Review Remarks</div>
                                    <div class="p-3 bg-light rounded border text-dark fs-13px" style="min-height: 95px;">
                                        {!! nl2br(e($sample->tr04_contract_review_remarks ?: 'No review remarks recorded yet.')) !!}
                                    </div>
                                    @if($sample->tr04_customer_agreed_deviation)
                                        <div class="mt-2 text-warning fs-12px fw-bold">
                                            <em class="ni ni-alert-fill me-1"></em> Customer has officially agreed to method/parameter deviations per Clause 7.1.4.
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-5">
                                    <div class="signature-box h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            @if($sample->tr04_contract_reviewed_at)
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="meta-label mb-0">Reviewed & Authorized By</span>
                                                    <span class="badge bg-success-dim text-success"><em class="ni ni-check-shield me-1"></em>Signed</span>
                                                </div>
                                                <div class="fw-bold fs-14px text-dark">
                                                    {{ $sample->tr04_contract_reviewed_by_name }}
                                                </div>
                                                <div class="text-muted fs-12px">
                                                    {{ $sample->tr04_contract_reviewed_by_role ?? 'Technical Manager' }}
                                                </div>
                                                <div class="text-muted fs-11px mt-1">
                                                    {{ $sample->ro->lab_name_en ?? ($sample->ro->m04_name ?? 'Textiles Committee Laboratory') }}
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="meta-label mb-0">Review Status</span>
                                                    <span class="badge bg-warning-dim text-warning"><em class="ni ni-clock me-1"></em>Pending Review</span>
                                                </div>
                                                <div class="text-muted fs-13px">
                                                    Awaiting review and sign-off by Laboratory In-Charge / Technical Manager.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="pt-2 border-top mt-2">
                                            <div class="d-flex justify-content-between align-items-center fs-11px text-muted">
                                                <span>Review Timestamp:</span>
                                                <span class="fw-bold text-dark">
                                                    {{ $sample->tr04_contract_reviewed_at ? $sample->tr04_contract_reviewed_at->format('d M Y, h:i A') : 'Pending Final Sign-Off' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Command Bar --}}
                <div class="nk-block mb-4">
                    <div class="card card-bordered bg-light">
                        <div class="card-inner py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="my-1 me-3">
                                    <span class="fw-bold text-dark fs-13px">Lab In-Charge Actions:</span>
                                    <span class="text-muted fs-12px ms-1">Update feasibility, accept, reject or request customer clarification</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap">
                                    <button type="button" class="btn btn-outline-danger me-2 my-1 btn-quick-action" data-action="REJECTED">
                                        <em class="ni ni-cross-circle me-1"></em> Reject Contract
                                    </button>
                                    <button type="button" class="btn btn-outline-primary me-2 my-1 btn-quick-action" data-action="CLARIFICATION_REQUIRED">
                                        <em class="ni ni-chat-circle me-1"></em> Request Clarification
                                    </button>
                                    <button type="button" class="btn btn-outline-info me-2 my-1 btn-quick-action" data-action="ACCEPTED_WITH_DEVIATION">
                                        <em class="ni ni-alert-circle me-1"></em> Accept w/ Deviation
                                    </button>
                                    <button type="button" class="btn btn-success me-2 my-1 btn-quick-action" data-action="ACCEPTED">
                                        <em class="ni ni-check-circle me-1"></em> Accept Contract (Feasible)
                                    </button>
                                    <a href="{{ route('edit_contract_details', $sample->tr04_sample_registration_id) }}" class="btn btn-primary my-1">
                                        <em class="ni ni-edit me-1"></em> Full Edit Station
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal for Quick Decision / Sign-Off --}}
<div class="modal fade" id="quickDecisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <h5 class="modal-title mb-0">Record ISO 17025 Contract Review Decision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="decisionForm">
                @csrf
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Review Decision <span class="text-danger">*</span></label>
                            <select class="form-select" name="decision" id="modalDecisionSelect" required>
                                <option value="ACCEPTED" {{ $sample->tr04_contract_review_status === 'ACCEPTED' ? 'selected' : '' }}>ACCEPTED - Lab Capable & Resource Feasible</option>
                                <option value="ACCEPTED_WITH_DEVIATION" {{ in_array($sample->tr04_contract_review_status, ['ACCEPTED_WITH_DEVIATION', 'ACCEPTED_DEVIATION']) ? 'selected' : '' }}>ACCEPTED_WITH_DEVIATION - Accepted with Technical Deviation</option>
                                <option value="CLARIFICATION_REQUIRED" {{ $sample->tr04_contract_review_status === 'CLARIFICATION_REQUIRED' ? 'selected' : '' }}>CLARIFICATION_REQUIRED - Clarification Needed from Customer</option>
                                <option value="REJECTED" {{ $sample->tr04_contract_review_status === 'REJECTED' ? 'selected' : '' }}>REJECTED - Not Feasible / Parameters Outside Scope</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Decision Rule (Cl. 7.1.3)</label>
                            <select class="form-select" name="decision_rule">
                                <option value="SIMPLE_ACCEPTANCE" {{ ($sample->tr04_decision_rule ?? '') === 'SIMPLE_ACCEPTANCE' ? 'selected' : '' }}>Simple Acceptance (Binary Decision)</option>
                                <option value="GUARD_BAND_ILAC_G8" {{ ($sample->tr04_decision_rule ?? '') === 'GUARD_BAND_ILAC_G8' ? 'selected' : '' }}>Guard-banded Acceptance (ILAC G8)</option>
                                <option value="CUSTOMER_SPECIFIED" {{ ($sample->tr04_decision_rule ?? '') === 'CUSTOMER_SPECIFIED' ? 'selected' : '' }}>Customer Specified Rule</option>
                                <option value="NOT_APPLICABLE" {{ ($sample->tr04_decision_rule ?? '') === 'NOT_APPLICABLE' ? 'selected' : '' }}>Not Applicable (No Conformity)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sample Condition at Receipt</label>
                            <select class="form-select" name="sample_condition">
                                <option value="INTACT_SEALED" {{ ($sample->tr04_sample_condition ?? '') === 'INTACT_SEALED' ? 'selected' : '' }}>Intact & Sealed</option>
                                <option value="INTACT_UNSEALED" {{ ($sample->tr04_sample_condition ?? '') === 'INTACT_UNSEALED' ? 'selected' : '' }}>Intact (Unsealed)</option>
                                <option value="DAMAGED_ACCEPTABLE" {{ ($sample->tr04_sample_condition ?? '') === 'DAMAGED_ACCEPTABLE' ? 'selected' : '' }}>Damaged (Acceptable for Testing)</option>
                                <option value="DAMAGED_COMPROMISED" {{ ($sample->tr04_sample_condition ?? '') === 'DAMAGED_COMPROMISED' ? 'selected' : '' }}>Compromised / Leaking</option>
                                <option value="INSUFFICIENT_QTY" {{ ($sample->tr04_sample_condition ?? '') === 'INSUFFICIENT_QTY' ? 'selected' : '' }}>Quantity Inadequate</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Storage Condition</label>
                            <select class="form-select" name="storage_condition">
                                <option value="AMBIENT" {{ ($sample->tr04_storage_condition ?? '') === 'AMBIENT' ? 'selected' : '' }}>Ambient Room Temperature</option>
                                <option value="STANDARD_ATMOSPHERE" {{ ($sample->tr04_storage_condition ?? '') === 'STANDARD_ATMOSPHERE' ? 'selected' : '' }}>Conditioning (20±2°C / 65±4% RH)</option>
                                <option value="COLD_STORAGE" {{ ($sample->tr04_storage_condition ?? '') === 'COLD_STORAGE' ? 'selected' : '' }}>Cold Storage (2°C - 8°C)</option>
                                <option value="DESICCATOR" {{ ($sample->tr04_storage_condition ?? '') === 'DESICCATOR' ? 'selected' : '' }}>Desiccator</option>
                            </select>
                        </div>
                        <div class="col-12" id="modalDeviationCheckbox" style="{{ in_array($sample->tr04_contract_review_status, ['ACCEPTED_WITH_DEVIATION', 'ACCEPTED_DEVIATION']) ? '' : 'display:none;' }}">
                            <div class="custom-control custom-checkbox bg-warning-dim p-2 rounded border border-warning">
                                <input type="checkbox" class="custom-control-input" id="chkCustomerAgreedModal" name="customer_agreed_deviation" value="1" {{ $sample->tr04_customer_agreed_deviation ? 'checked' : '' }}>
                                <label class="custom-control-label fw-bold text-dark fs-12px" for="chkCustomerAgreedModal">
                                    Customer has officially confirmed acceptance of deviation / non-standard method (Cl. 7.1.4)
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Findings & Review Remarks <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="remarks" rows="3" placeholder="Enter technical evaluation findings, method validation details, or review remarks..." required>{{ $sample->tr04_contract_review_remarks }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveDecision">
                        <em class="ni ni-check-circle me-1"></em> Save Review & Sign-Off
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#modalDecisionSelect').on('change', function() {
            if ($(this).val() === 'ACCEPTED_WITH_DEVIATION' || $(this).val() === 'ACCEPTED_DEVIATION') {
                $('#modalDeviationCheckbox').slideDown(150);
            } else {
                $('#modalDeviationCheckbox').slideUp(150);
            }
        });

        $('.btn-quick-action').on('click', function() {
            const action = $(this).data('action');
            $('#modalDecisionSelect').val(action).trigger('change');
            $('#quickDecisionModal').modal('show');
        });

        $('#decisionForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSaveDecision');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            $.ajax({
                url: "{{ route('contract_review_decision', $sample->tr04_sample_registration_id) }}",
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    btn.prop('disabled', false).html('<em class="ni ni-check-circle me-1"></em> Save Review & Sign-Off');
                    if (res.status === 'success' || res.success) {
                        $('#quickDecisionModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Review Updated!',
                            text: res.message,
                            timer: 1600,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Notice', res.message, 'warning');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<em class="ni ni-check-circle me-1"></em> Save Review & Sign-Off');
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to update contract review.', 'error');
                }
            });
        });
    });
</script>
@endsection
