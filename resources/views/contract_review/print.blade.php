<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Review Record - {{ $sample->tr04_reference_id ?? $sample->tr04_sample_registration_id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #111;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .print-container {
            max-width: 820px;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0 0 4px 0;
            font-size: 15pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h4 {
            margin: 0 0 3px 0;
            font-size: 11pt;
            font-weight: normal;
        }
        .header-title p {
            margin: 0;
            font-size: 9pt;
            color: #444;
        }
        .doc-control {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 12px;
            padding: 4px 8px;
            background: #f1f3f5;
            border: 1px solid #ccc;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            background: #e9ecef;
            padding: 4px 8px;
            margin: 14px 0 6px 0;
            border-left: 4px solid #0f3959;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #777;
            padding: 5px 7px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: left;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 10px;
        }
        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            width: 22%;
            color: #333;
        }
        .meta-val {
            width: 28%;
        }
        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            font-weight: bold;
            font-size: 9pt;
            border-radius: 3px;
            border: 1px solid #333;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-table td {
            padding: 10px 15px;
            vertical-align: top;
        }
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background: #0971fe;
            color: #fff;
            padding: 8px 18px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 11pt;
        }
        .btn-print:hover {
            background: #025cd5;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .print-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="print-container">
    
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            Print Contract Review Record (Ctrl + P)
        </button>
    </div>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                <img src="{{ asset('backAssets/images/logo-dark2x.png') }}" alt="Logo" style="max-height: 55px;" onerror="this.style.display='none'">
            </td>
            <td class="header-title">
                <h2>TEXTILES COMMITTEE</h2>
                <h4>{{ $sample->ro->ministry_en ?? 'MINISTRY OF TEXTILES, GOVERNMENT OF INDIA' }}</h4>
                <p>{{ $sample->ro->lab_name_en ?? ($sample->ro->m04_name ?? 'Laboratories Division') }} - {{ $sample->ro->lab_address ?? ($sample->ro->m04_address ?? '') }}</p>
                <p>{{ $sample->ro->certificate_no ? 'NABL Accreditation Certificate No: ' . $sample->ro->certificate_no : 'NABL ACCREDITED LABORATORY AS PER ISO/IEC 17025:2017' }}</p>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                <span style="font-size: 8pt; font-weight: bold; border: 1px solid #000; padding: 4px; display: inline-block;">
                    ISO/IEC 17025<br>CLAUSE 7.1
                </span>
            </td>
        </tr>
    </table>

    <div class="doc-control">
        <span>DOCUMENT: CONTRACT REVIEW & TECHNICAL EVALUATION RECORD</span>
        <span>FORMAT NO: TC/LAB/CR/01</span>
        <span>REV: 04</span>
    </div>

    {{-- Section 1: General & Customer Info --}}
    <div class="section-title">1. General & Customer Details</div>
    <table class="meta-table">
        <tr>
            <td class="meta-label">Sample Reference ID:</td>
            <td class="meta-val"><strong>{{ $sample->tr04_reference_id ?? $sample->tr04_sample_registration_id }}</strong></td>
            <td class="meta-label">Date of Receipt:</td>
            <td class="meta-val">{{ $sample->created_at ? $sample->created_at->format('d-M-Y H:i') : '—' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Customer Name:</td>
            <td class="meta-val">{{ $sample->customer->m07_name ?? '—' }}</td>
            <td class="meta-label">Target Completion:</td>
            <td class="meta-val"><strong>{{ $sample->tr04_expected_date ? \Carbon\Carbon::parse($sample->tr04_expected_date)->format('d-M-Y') : '—' }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Address & Location:</td>
            <td class="meta-val">{{ $sample->customerLocation->m08_address ?? ($sample->customer->m07_address ?? '—') }}</td>
            <td class="meta-label">Service Priority:</td>
            <td class="meta-val">
                <strong>{{ (strtolower($sample->tr04_sample_type ?? '') === 'tatkal' || ($sample->tr04_priority ?? 0) == 1) ? 'TATKAL (Urgent)' : 'Normal Priority' }}</strong>
            </td>
        </tr>
        <tr>
            <td class="meta-label">Customer GSTIN:</td>
            <td class="meta-val">{{ $sample->customerLocation->m08_gst ?? ($sample->customer->m07_gst ?? '—') }}</td>
            <td class="meta-label">Commercial Type:</td>
            <td class="meta-val">{{ $sample->tr04_commercial_type ?? 'Commercial' }}</td>
        </tr>
    </table>

    {{-- Section 2: Sample Description & Physical Condition --}}
    <div class="section-title">2. Sample Description & Condition at Receipt (Cl. 7.4)</div>
    <table class="meta-table">
        <tr>
            <td class="meta-label">Sample Description:</td>
            <td colspan="3">{{ $sample->tr04_sample_description ?: '—' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Fabric / Sample Mark:</td>
            <td class="meta-val">{{ $sample->tr04_sample_mark ?: '—' }}</td>
            <td class="meta-label">Sample Quantity / Count:</td>
            <td class="meta-val">{{ $sample->tr04_number_of_samples ?? 1 }} Sample(s)</td>
        </tr>
        <tr>
            <td class="meta-label">Condition at Receipt:</td>
            <td class="meta-val">
                @php
                    $condMap = [
                        'INTACT_SEALED' => 'Intact & Sealed',
                        'INTACT_UNSEALED' => 'Intact (Unsealed)',
                        'DAMAGED_ACCEPTABLE' => 'Slightly Damaged (Acceptable for testing)',
                        'DAMAGED_COMPROMISED' => 'Compromised / Leaking (Unfit)',
                        'INSUFFICIENT_QTY' => 'Quantity Insufficient for Scope',
                    ];
                @endphp
                <strong>{{ $condMap[$sample->tr04_sample_condition] ?? ($sample->tr04_sample_condition ?? 'Intact & Sealed') }}</strong>
            </td>
            <td class="meta-label">Storage Condition:</td>
            <td class="meta-val">
                <strong>{{ str_replace('_', ' ', $sample->tr04_storage_condition ?? 'AMBIENT') }}</strong>
            </td>
        </tr>
        <tr>
            <td class="meta-label">Decision Rule (Cl. 7.1.3):</td>
            <td colspan="3">
                <strong>{{ str_replace('_', ' ', $sample->tr04_decision_rule ?? 'SIMPLE_ACCEPTANCE') }}</strong>
            </td>
        </tr>
    </table>

    {{-- Section 3: ISO/IEC 17025 Clause 7.1 Feasibility Checklist --}}
    <div class="section-title">3. Clause 7.1 Technical Feasibility Evaluation Matrix</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">#</th>
                <th>ISO/IEC 17025:2017 Requirement Element</th>
                <th style="width: 90px; text-align: center;">Status</th>
                <th>Technical Verification Notes</th>
            </tr>
        </thead>
        <tbody>
            @php $chkIndex = 1; @endphp
            @foreach($checklist as $key => $item)
                <tr>
                    <td style="text-align: center;">{{ $chkIndex++ }}</td>
                    <td>{{ $item['title'] ?? ($item['question'] ?? $key) }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $item['status'] ?? 'YES' }}
                    </td>
                    <td style="font-size: 8.5pt;">{{ $item['remark'] ?? ($item['notes'] ?? '—') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Section 4: Requested Tests Scope & Standards --}}
    <div class="section-title">4. Test Parameters, Standard Methods & Laboratory Capability</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">#</th>
                <th>Test Parameter Name</th>
                <th>Standard Test Method</th>
                <th style="width: 100px; text-align: center;">Accredited Scope</th>
                <th style="width: 90px; text-align: right;">Fee (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sample->sampleTests as $index => $testItem)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $testItem->test->m12_name ?? 'Test #'.$testItem->m12_test_id }}</strong>
                        @if($testItem->tr05_remark)
                            <div style="font-size: 8pt; color: #555;">Note: {{ $testItem->tr05_remark }}</div>
                        @endif
                    </td>
                    <td>{{ $testItem->standard->m15_method ?? 'Standard Method' }}</td>
                    <td style="text-align: center;">
                        @if($testItem->is_lab_accredited)
                            <strong style="color: #155724;">NABL Scope</strong>
                            @if(!empty($testItem->accreditation_valid_till))
                                <div style="font-size: 7.5pt; color: #555;">(Till {{ \Carbon\Carbon::parse($testItem->accreditation_valid_till)->format('d-m-Y') }})</div>
                            @endif
                        @elseif($testItem->is_accreditation_expired)
                            <strong style="color: #856404;">Expired</strong>
                        @else
                            <span style="color: #6c757d;">Non-Accredited</span>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($testItem->test->m12_charge ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;"><em>No test parameters registered for this sample.</em></td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Testing Charges Total:</td>
                <td style="text-align: right; font-weight: bold;">₹ {{ number_format($sample->tr04_testing_charges ?? ($sample->tr04_total_charges / 1.18), 2) }}</td>
            </tr>
            @if(($sample->tr04_cgst ?? 0) > 0 || ($sample->tr04_sgst ?? 0) > 0)
                <tr>
                    <td colspan="4" style="text-align: right; font-size: 8.5pt;">CGST (9%) + SGST (9%):</td>
                    <td style="text-align: right; font-size: 8.5pt;">₹ {{ number_format(($sample->tr04_cgst ?? 0) + ($sample->tr04_sgst ?? 0), 2) }}</td>
                </tr>
            @elseif(($sample->tr04_igst ?? 0) > 0)
                <tr>
                    <td colspan="4" style="text-align: right; font-size: 8.5pt;">IGST (18%):</td>
                    <td style="text-align: right; font-size: 8.5pt;">₹ {{ number_format($sample->tr04_igst ?? 0, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Grand Total Fee:</td>
                <td style="text-align: right; font-weight: bold;">₹ {{ number_format($sample->tr04_total_charges ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Section 5: Review Decision & Authorization --}}
    <div class="section-title">5. Laboratory In-Charge Findings & Review Decision</div>
    <div style="border: 1px solid #777; padding: 10px; margin-bottom: 12px; background: #fafafa;">
        <div style="margin-bottom: 8px;">
            <strong>Review Decision: </strong>
            <span class="badge-status">
                {{ strtoupper($sample->tr04_contract_review_status ?? 'PENDING') }}
            </span>
            @if($sample->tr04_customer_agreed_deviation)
                <span style="font-size: 8.5pt; margin-left: 10px;">(Customer officially agreed to deviation per Cl. 7.1.4)</span>
            @endif
        </div>
        <div>
            <strong>Technical Findings & Review Remarks:</strong>
            <p style="margin: 4px 0 0 0; font-size: 9.5pt;">
                {{ $sample->tr04_contract_review_remarks ?: 'No review remarks recorded yet.' }}
            </p>
        </div>
    </div>

    {{-- Signatures --}}
    <table class="signature-table">
        <tr>
            <td style="width: 50%; border-top: 1px dashed #777;">
                <strong>Sample Receiving Officer:</strong><br>
                <span>Receiving & Custody Section</span><br>
                <span>Date: {{ $sample->created_at ? $sample->created_at->format('d-M-Y') : '—' }}</span>
            </td>
            <td style="width: 50%; border-top: 1px dashed #777; text-align: right;">
                <strong>Reviewed & Authorized By:</strong><br>
                @if($sample->tr04_contract_reviewed_at)
                    <strong>{{ $sample->tr04_contract_reviewed_by_name }}</strong><br>
                    <span>{{ $sample->tr04_contract_reviewed_by_role ?? 'Technical Manager' }}</span><br>
                    <span>Timestamp: {{ $sample->tr04_contract_reviewed_at->format('d-M-Y H:i') }}</span>
                @else
                    <strong>PENDING REVIEW SIGN-OFF</strong><br>
                    <span>Laboratory In-Charge / Technical Manager</span><br>
                    <span>Status: Awaiting Technical Review</span>
                @endif
            </td>
        </tr>
    </table>

</div>

</body>
</html>
