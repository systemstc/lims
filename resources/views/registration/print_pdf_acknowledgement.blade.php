<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sample Registration Acknowledgement</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #333; margin: 0; padding: 20px; background: #fff; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .divider { border-bottom: 2px solid #2c3e50; margin: 20px 0; }
        .section-title { font-size: 16px; font-weight: bold; color: #2c3e50; margin-bottom: 12px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: #f4f6f8; font-weight: bold; color: #2c3e50; font-size: 11px; text-transform: uppercase; }
        ul { list-style-type: none; padding: 0; margin: 0; }
        li { margin-bottom: 6px; }
        .lbl { font-weight: bold; width: 130px; display: inline-block; color: #444; }
    </style>
</head>
<body onload="window.print()">
    <!-- HEADER -->
    <table width="100%">
        <tr>
            <td width="20%">
                <img src="{{ asset('backAssets/images/logo.png') }}" alt="Logo" width="100" style="width: 100px; height: auto;">
            </td>
            <td width="80%" style="text-align: right;">
                <div style="font-size: 24px; text-transform: uppercase; color: #2c3e50; font-weight: bold; margin-bottom: 5px;">Sample Acknowledgement</div>
                <div style="font-size: 12px; color: #717374;">LABORATORIES</div>
                <div style="font-size: 16px; color: #494949; font-weight: bold;">TEXTILES COMMITTEE</div>
                <div style="font-size: 14px; color: #7f8c8d;">{{ $sample->ro->ministry_en }}</div>
                <div style="font-size: 14px; color: #7f8c8d;">{{ $sample->ro->lab_address ?? '' }}</div>
            </td>
        </tr>
    </table>
    
    <div class="divider"></div>

    <!-- META INFO -->
    <table width="100%" style="margin-bottom: 20px;">
        <tr>
            <td width="50%">
                <ul>
                    <li><span class="lbl">Registration ID:</span> <strong>#{{ $sample->tr04_sample_registration_id }}</strong></li>
                    <li><span class="lbl">Reference ID:</span> #{{ $sample->tr04_reference_id }}</li>
                    <li><span class="lbl">Tracker ID:</span> <strong>{{ $sample->tr04_tracker_id ?? 'N/A' }}</strong></li>
                </ul>
            </td>
            <td width="50%" style="text-align: right;">
                <ul>
                    <li><span class="lbl" style="text-align: right; margin-right: 10px;">Date:</span> {{ $sample->created_at->format('d M, Y h:i A') }}</li>
                    <li><span class="lbl" style="text-align: right; margin-right: 10px;">Status:</span> <strong>{{ ucfirst(strtolower($sample->tr04_status)) }}</strong></li>
                    <li><span class="lbl" style="text-align: right; margin-right: 10px;">Payment:</span> <strong>{{ $sample->tr04_payment_status }}</strong></li>
                </ul>
            </td>
        </tr>
        @if ($sample->package)
        <tr>
            <td colspan="2" style="padding-top: 5px;">
                <span class="lbl">Package:</span> <strong>{{ $sample->package['m19_name'] }}</strong>
            </td>
        </tr>
        @endif
    </table>

    <!-- PARTIES DETAILS -->
    @php
        $paymentByKey = strtolower($sample->tr04_payment_by ?? 'customer');
        $payer = $sample->parties[$paymentByKey] ?? $sample->parties['customer'];

        $reportToKeys = json_decode($sample->tr04_report_to, true) ?? ['customer'];
    @endphp
    <table width="100%" style="margin-bottom: 25px;">
        <tr>
            <!-- Billed To (Payer) -->
            <td width="50%" style="padding-right: 20px;">
                <div class="section-title">Billed To ({{ ucfirst(str_replace('_', ' ', $paymentByKey)) }})</div>
                <div style="font-weight: bold; font-size: 15px; margin-bottom: 5px;">{{ $payer['name'] }}</div>
                <div style="color: #666; margin-bottom: 5px;">{{ $payer['contact_person'] }}</div>
                <div style="margin-bottom: 5px;">{{ $payer['address'] }}
                   {{ $payer['district'] ? ', ' . $payer['district'] : '' }}
                   {{ $payer['state'] ? ', ' . $payer['state'] : '' }}
                </div>
                @if($payer['phone']) <div style="margin-bottom: 3px;">Ph: {{ $payer['phone'] }}</div> @endif
                @if($payer['email']) <div style="margin-bottom: 3px;">Email: {{ $payer['email'] }}</div> @endif
                @if($payer['gst']) <div style="font-weight: bold; margin-top: 5px;">GST: {{ $payer['gst'] }}</div> @endif
            </td>
            
            <!-- Report To -->
            <td width="50%" style="padding-left: 20px;">
                @foreach ($reportToKeys as $index => $rKey)
                    @php 
                        $rk = strtolower($rKey);
                        $rp = $sample->parties[$rk] ?? null;
                    @endphp
                    @if($rp)
                        <div class="section-title" @if($index > 0) style="margin-top: 20px; border-bottom-color: #ccc;" @endif>
                            Report To ({{ ucfirst(str_replace('_', ' ', $rk)) }})
                        </div>
                        <div style="font-weight: bold; font-size: 15px; margin-bottom: 5px;">{{ $rp['name'] }}</div>
                        <div style="color: #666; margin-bottom: 5px;">{{ $rp['contact_person'] }}</div>
                        <div style="margin-bottom: 5px;">{{ $rp['address'] }}
                           {{ $rp['district'] ? ', ' . $rp['district'] : '' }}
                           {{ $rp['state'] ? ', ' . $rp['state'] : '' }}
                        </div>
                        @if($rp['phone']) <div style="margin-bottom: 3px;">Ph: {{ $rp['phone'] }}</div> @endif
                        @if($rp['email']) <div style="margin-bottom: 3px;">Email: {{ $rp['email'] }}</div> @endif
                        @if($rp['gst']) <div style="font-weight: bold; margin-top: 5px;">GST: {{ $rp['gst'] }}</div> @endif
                    @endif
                @endforeach
            </td>
        </tr>
    </table>
    
    <!-- SAMPLE INFO -->
    <table width="100%" style="background: #f8f9fa; border: 1px solid #eaeaea; margin-bottom: 30px;">
        <tr>
            <td width="65%" style="padding: 15px;">
                <div class="section-title" style="border-bottom-color: #ccc;">Sample Information</div>
                <ul>
                    <li><span class="lbl">Lab Sample:</span> {{ $sample->labSample['m14_name'] ?? 'N/A' }}</li>
                    @if ($sample->tr04_sample_mark)
                        <li><span class="lbl">Sample Mark:</span> {{ $sample->tr04_sample_mark }}</li>
                    @endif
                    @if ($sample->tr04_be_no)
                        <li><span class="lbl">BE Number:</span> {{ $sample->tr04_be_no }}</li>
                    @endif
                    <li><span class="lbl">Description:</span> {{ $sample->tr04_sample_description ?? 'N/A' }}</li>
                    <li><span class="lbl">Received Via:</span> {{ ucfirst(str_replace('_', ' ', $sample->tr04_received_via)) }}</li>
                    <li><span class="lbl">Sample Type:</span> {{ $sample->tr04_sample_type }}</li>
                </ul>
            </td>
            <td width="35%" style="padding: 15px; text-align: center; vertical-align: middle;">
                @if ($sample->tr04_attachment)
                    @php
                        $imagePath = storage_path('app/public/' . $sample->tr04_attachment);
                        if (file_exists($imagePath)) {
                            $imageData = base64_encode(file_get_contents($imagePath));
                            $src = 'data:image/jpeg;base64,'.$imageData;
                        } else {
                            $src = asset('storage/' . $sample->tr04_attachment);
                        }
                    @endphp
                    <img src="{{ $src }}" alt="Sample Image" width="220" style="width: 220px; height: auto; border: 1px solid #ddd; padding: 4px; background: #fff;">
                @endif
            </td>
        </tr>
    </table>
    
    @php
        $isPackageBased = !empty($sample->m19_package_id) || (!empty($sample->tr04_charge_type) && strtolower($sample->tr04_charge_type) !== 'individual');
        $packageName = $sample->package->m19_name ?? (!empty($sample->tr04_charge_type) ? ucfirst(strtolower($sample->tr04_charge_type)) : 'Package');
        $packageCharge = $sample->package->m19_charges ?? $sample->tr04_testing_charges;
        $packageType = !empty($sample->tr04_charge_type) ? ucfirst(strtolower($sample->tr04_charge_type)) : ($sample->package->m19_type ?? 'Package');
    @endphp

    <!-- TESTS TABLE -->
    <div class="section-title">Test Details</div>
    @if ($isPackageBased)
        <div style="background-color: #eef2f5; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 10px; margin-bottom: 10px; font-size: 11px; color: #1e293b;">
            <strong>{{ $packageType }} Applied:</strong> {{ $packageName }} &nbsp;&nbsp;|&nbsp;&nbsp; 
            <strong>Package Charges:</strong> &#8377;{{ number_format($packageCharge, 2) }}
        </div>
    @endif
    <table class="data-table" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th width="15%">Test ID</th>
                <th width="35%">Test Name & Description</th>
                <th width="25%">Standard/Method</th>
                <th width="15%">Status</th>
                <th width="10%" style="text-align: right;">Charge</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sample->sampleTests as $sampleTest)
                <tr>
                    <td>{{ $sampleTest['test']['m12_test_id'] }}</td>
                    <td>
                        <strong>{{ $sampleTest['test']['m12_name'] }}</strong>
                        @if ($sampleTest['test']['m12_description'])
                            <br><span style="color: #666; font-size: 11px;">{{ $sampleTest['test']['m12_description'] }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $sampleTest['standard']['m15_method'] ?? '_' }}
                        @if (!empty($sampleTest['standard']['accreditationForCurrentRo']) && $sampleTest['standard']['accreditationForCurrentRo']['m21_is_accredited'] === 'YES')
                            <br><span style="font-size: 10px; color: #155724;">Accredited till {{ \Carbon\Carbon::parse($sampleTest['standard']['accreditationForCurrentRo']['m21_valid_till'])->format('d M Y') }}</span>
                        @endif
                    </td>
                    <td>{{ ucfirst(strtolower(str_replace('_', ' ', $sampleTest['tr05_status']))) }}</td>
                    <td style="text-align: right;">&#8377;{{ number_format($isPackageBased ? 0 : ($sampleTest['test']['m12_charge'] ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No tests found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TOTALS & QR -->
    <table width="100%">
        <tr>
            <td width="60%" style="text-align: center; vertical-align: middle;">
                <div style="display: inline-block; padding: 10px; border: 1px dashed #ccc; margin-top: 15px;">
                    {!! QrCode::size(90)->generate(route('track_sample', ['trackerId' => $sample->tr04_tracker_id])) !!}
                    <div style="font-size: 11px; margin-top: 5px; font-weight: bold;">Scan to Track</div>
                    <div style="font-size: 12px; font-weight: bold;">{{ $sample->tr04_tracker_id }}</div>
                </div>
            </td>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td style="text-align: right; padding: 5px 15px 5px 0;">
                            Testing Charges:
                            @if ($isPackageBased)
                                <br><span style="font-size: 10px; color: #2563eb; font-weight: bold;">({{ $packageType }}: {{ $packageName }})</span>
                            @endif
                        </td>
                        <td style="text-align: right; padding: 5px 0; width: 100px;">&#8377;{{ number_format($sample->tr04_testing_charges, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right; padding: 5px 15px 5px 0;">Additional Charges:</td>
                        <td style="text-align: right; padding: 5px 0;">&#8377;{{ number_format($sample->tr04_additional_charges ?? 0, 2) }}</td>
                    </tr>
                    
                    @php
                        $taxableAmount = $sample->tr04_testing_charges + $sample->tr04_additional_charges;
                        $cgstRate = ($taxableAmount > 0 && $sample->tr04_cgst > 0) ? ($sample->tr04_cgst / $taxableAmount) * 100 : 0;
                        $sgstRate = ($taxableAmount > 0 && $sample->tr04_sgst > 0) ? ($sample->tr04_sgst / $taxableAmount) * 100 : 0;
                        $igstRate = ($taxableAmount > 0 && $sample->tr04_igst > 0) ? ($sample->tr04_igst / $taxableAmount) * 100 : 0;
                    @endphp
                    
                    @if($sample->tr04_cgst > 0 || $sample->tr04_sgst > 0)
                        <tr>
                            <td style="text-align: right; padding: 5px 15px 5px 0;">CGST ({{ round($cgstRate) }}%):</td>
                            <td style="text-align: right; padding: 5px 0;">&#8377;{{ number_format($sample->tr04_cgst, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: right; padding: 5px 15px 5px 0;">SGST ({{ round($sgstRate) }}%):</td>
                            <td style="text-align: right; padding: 5px 0;">&#8377;{{ number_format($sample->tr04_sgst, 2) }}</td>
                        </tr>
                    @elseif($sample->tr04_igst > 0)
                        <tr>
                            <td style="text-align: right; padding: 5px 15px 5px 0;">IGST ({{ round($igstRate) }}%):</td>
                            <td style="text-align: right; padding: 5px 0;">&#8377;{{ number_format($sample->tr04_igst, 2) }}</td>
                        </tr>
                    @endif
                    
                    <tr>
                        <td style="border-top: 2px solid #2c3e50; text-align: right; padding: 10px 15px 10px 0; font-weight: bold; font-size: 15px;">Grand Total:</td>
                        <td style="border-top: 2px solid #2c3e50; text-align: right; padding: 10px 0; font-weight: bold; font-size: 15px;">&#8377;{{ number_format($sample->tr04_total_charges, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <div style="margin-top: 40px; font-size: 11px; color: #888; text-align: center;">
        This is a computer-generated document. No signature is required.
    </div>
</body>
</html>
