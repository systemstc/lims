<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cumulative Report {{ $meta['report_no'] }}</title>

    <style>
        @page {
            margin: 270px 30px 260px 30px;
        }

        @page :first {
            margin-top: 270px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        h4 {
            margin: 6px 0;
            text-align: center;
            text-decoration: underline;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        tr {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
            font-size: 9px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        /* ===== LETTERHEAD HEADER ===== */
        .header-content {
            position: fixed;
            top: -240px;
            left: 0;
            right: 0;
            height: 235px;
            background: transparent;
        }

        .logo-left {
            position: absolute;
            top: 5px;
            left: 5px;
            width: 80px;
        }

        .logo-right {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 80px;
        }

        .logo-left img,
        .logo-right img {
            width: 100%;
            height: auto;
        }

        .header-center {
            position: absolute;
            top: 0px;
            left: 85px;
            right: 85px;
            text-align: center;
            line-height: 1.25;
        }

        .header-eng-title {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .header-eng-big {
            font-size: 18px;
            font-weight: bold;
            color: #c00000;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .header-eng-ministry {
            font-size: 11px;
            color: #000;
            margin-bottom: 2px;
        }

        .header-eng-lab {
            font-size: 11px;
            font-weight: bold;
            color: #008000;
            margin-bottom: 2px;
        }

        .header-address {
            font-size: 9.5px;
            color: #000;
            margin-bottom: 2px;
        }

        .header-contact {
            font-size: 9.5px;
            color: #000;
            margin-bottom: 2px;
        }

        .header-email {
            font-size: 9.5px;
            color: #c00000;
        }

        .header-report-title {
            position: absolute;
            top: 145px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            color: #1c276b;
        }

        .header-divider {
            position: absolute;
            top: 140px;
            left: 0;
            right: 0;
            border-bottom: 1.5px solid #1c276b;
        }

        /* Outer page border */
        .outer-border {
            position: fixed;
            top: -240px;
            bottom: -235px;
            left: 0;
            right: 0;
            border: 1.5px solid #1c276b;
            z-index: -100;
        }

        /* Customer info block */
        .first-page-header {
            margin-top: -55px;
            margin-bottom: 15px;
        }

        .info-table td {
            border: none;
            padding: 2px 0;
            font-size: 10px;
        }

        .details-table th {
            text-align: left;
            font-weight: bold;
            width: 35%;
            background: #f9f9f9;
        }

        /* ===== FOOTER ===== */
        .footer-content {
            position: fixed;
            bottom: -228px;
            left: 0;
            right: 0;
            height: 110px;
            background: transparent;
        }

        .footer-note {
            position: absolute;
            top: 35px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-disclaimer {
            position: absolute;
            top: 48px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-quote {
            position: absolute;
            top: 61px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #c00000;
        }

        .footer-complaints {
            position: absolute;
            top: 74px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-service {
            position: absolute;
            top: 88px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }

        .footer-social {
            position: absolute;
            top: 102px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #333;
        }

        .footer-divider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            border-top: 1.5px solid #1c276b;
        }

        /* Results table specific styles */
        .results-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .results-table thead tr {
            page-break-inside: auto;
        }

        .end-of-report {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <div class="outer-border"></div>

    {{-- ===== LETTERHEAD HEADER ===== --}}
    <div class="header-content">
        {{-- LEFT LOGO --}}
        <div class="logo-left">
            @php
                $leftLogoPath = base_path('backAssets/images/logo.png');
                $leftLogoSrc = file_exists($leftLogoPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($leftLogoPath))
                    : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
            @endphp
            <img src="{{ $leftLogoSrc }}" alt="Logo">
        </div>

        {{-- RIGHT LOGO (NABL) --}}
        @if(isset($hasAccreditedTests) && $hasAccreditedTests)
        <div class="logo-right" style="text-align: center;">
            @php
                $rightLogoPath = base_path('backAssets/images/accrediation.png');
                $rightLogoSrc = file_exists($rightLogoPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($rightLogoPath))
                    : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
            @endphp
            <img src="{{ $rightLogoSrc }}" alt="Accreditation" style="width: 70px;">
            @if($firstReg->ro && $firstReg->ro->certificate_no)
            <div style="font-size: 8px; font-weight: bold; margin-top: 2px;">{{ $firstReg->ro->certificate_no }}</div>
            @endif
        </div>
        @endif

        {{-- CENTER TEXT --}}
        <div class="header-center">
            <div class="header-eng-title">LABORATORY</div>
            <div class="header-eng-big">{{ $firstReg->ro->lab_name_en ?? 'TEXTILES COMMITTEE' }}</div>
            <div class="header-eng-ministry">{{ $firstReg->ro->ministry_en ?? 'Government of India, Ministry of Textiles' }}</div>
            <div class="header-address">{{ $firstReg->ro->lab_address ?? 'P. Balu Road, Prabhadevi Chowk, Prabhadevi, Mumbai-400 025.' }}</div>
            <div class="header-contact">{{ $firstReg->ro->lab_contact ?? 'Tel.: +91-22-6652 7541 / 545 / 550 / 607' }}</div>
            <div class="header-email">{{ $firstReg->ro->lab_email ?? '* E-mail : dlab.tc@nic.in * Website : www.textilescommittee.nic.in' }}</div>
        </div>

        <div class="header-report-title">TEST REPORT</div>
        <div class="header-divider"></div>
    </div>

    {{-- ===== FIRST PAGE CONTENT ===== --}}
    <div class="first-page">
        <div class="first-page-header">
            <table class="info-table">
                <tbody>
                    <tr>
                        <td style="width: 50%;"><strong>TEST REPORT No. :</strong> {{ $meta['report_no'] }}</td>
                        <td style="width: 50%; text-align: right;"><strong>Date :</strong> {{ $meta['date'] }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="details-table">
                <tbody>
                    <tr>
                        <th>Name and Address of the Customer</th>
                        <td>
                            <strong>{{ $meta['customer_name'] }}</strong><br>
                            {{ $meta['customer_address'] }}
                        </td>
                    </tr>
                    <tr>
                        <th>Sample forwarding letter No. & Date</th>
                        <td>{{ $meta['forwarding_letter'] }}</td>
                    </tr>
                    <tr>
                        <th>Date of Receipt of sample</th>
                        <td>{{ $meta['receipt_date'] }}</td>
                    </tr>
                    <tr>
                        <th>Date of performance of tests</th>
                        <td>{{ $meta['performance_date'] }}</td>
                    </tr>
                    <tr>
                        <th>Customer sample No.</th>
                        <td>{{ $meta['sample_colour'] ?: 'Nil' }}</td>
                    </tr>
                    <tr>
                        <th>Sample description as declared by the customer</th>
                        <td>{{ $meta['sample_description'] }}</td>
                    </tr>
                    <tr>
                        <th>Sample Colour</th>
                        <td>{{ $meta['sample_colour'] ?: 'Nil' }}</td>
                    </tr>
                    <tr>
                        <th>Lab Sample No.</th>
                        <td><strong>{{ $meta['lab_sample_no_range'] }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="container">
            <h4 style="margin-bottom: 2px;">TEST RESULTS</h4>
            <div style="font-size: 10px; font-weight: bold; margin-bottom: 8px; text-align: center;">
                Test Name: {{ $test->m12_name }}
                @if(optional($firstReg->sampleTests->firstWhere('m12_test_number', $test->m12_test_number))->standard)
                    <small>({{ $firstReg->sampleTests->firstWhere('m12_test_number', $test->m12_test_number)->standard->m15_method }})</small>
                @endif
            </div>

            @php
                // Calculate total parameters/columns
                $simpleCount = count($simplePrimaries);
                $nestedCount = 0;
                foreach ($nestedPrimaries as $np) {
                    $nestedCount += count($np['secondaries']);
                }
                $paramColumnsCount = $simpleCount + $nestedCount;
            @endphp

            <table class="results-table">
                <thead>
                    @if ($nestedCount > 0)
                        {{-- 2-Row Header --}}
                        <tr>
                            <th rowspan="2" style="width: 8%;">Sample No.</th>
                            <th rowspan="2" style="width: 8%;">T.R. No.</th>
                            <th rowspan="2" style="width: 12%;">Sample Seal No.</th>
                            <th rowspan="2" style="width: 12%;">Sample Unique Code No.</th>
                            @foreach ($simplePrimaries as $sp)
                                <th rowspan="2">
                                    {{ $sp->m16_name }}
                                    @if(!empty($sp->m16_unit))
                                        <br><small>({{ $sp->m16_unit }})</small>
                                    @endif
                                </th>
                            @endforeach
                            @foreach ($nestedPrimaries as $np)
                                <th colspan="{{ count($np['secondaries']) }}">
                                    {{ $np['primary']->m16_name }}
                                    @if(!empty($np['primary']->m16_unit))
                                        <br><small>({{ $np['primary']->m16_unit }})</small>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($nestedPrimaries as $np)
                                @foreach ($np['secondaries'] as $sec)
                                    <th>
                                        {{ $sec->m17_name }}
                                        @if(!empty($sec->m17_unit))
                                            <br><small>({{ $sec->m17_unit }})</small>
                                        @endif
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                    @else
                        {{-- 1-Row Header --}}
                        <tr>
                            <th style="width: 8%;">Sample No.</th>
                            <th style="width: 8%;">T.R. No.</th>
                            <th style="width: 12%;">Sample Seal No.</th>
                            <th style="width: 12%;">Sample Unique Code No.</th>
                            @foreach ($simplePrimaries as $sp)
                                <th>
                                    {{ $sp->m16_name }}
                                    @if(!empty($sp->m16_unit))
                                        <br><small>({{ $sp->m16_unit }})</small>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @foreach ($tableData as $row)
                        <tr>
                            <td class="text-center">{{ $row['sample_registration_id'] }}</td>
                            <td class="text-center">{{ $row['tr_no'] }}</td>
                            <td class="text-center">{{ $row['seal_no'] ?: '---' }}</td>
                            <td class="text-center">{{ $row['unique_code'] ?: '---' }}</td>
                            
                            {{-- Simple parameters --}}
                            @foreach ($simplePrimaries as $sp)
                                <td class="text-center">
                                    {{ $row['results']['simple_' . $sp->m16_primary_test_id] ?? '-' }}
                                </td>
                            @endforeach
                            
                            {{-- Nested parameters --}}
                            @foreach ($nestedPrimaries as $np)
                                @foreach ($np['secondaries'] as $sec)
                                    <td class="text-center">
                                        {{ $row['results']['nested_' . $np['primary']->m16_primary_test_id . '_' . $sec->m17_secondary_test_id] ?? '-' }}
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="end-of-report">----------End of Report ----------</div>
        </div>
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer-content">
        <div class="footer-divider"></div>
        <div class="footer-note">Sample not drawn by Textiles Committee, Results relate only to the sample tested.</div>
        <div class="footer-disclaimer">This test report shall not be published in any form without the explicit written consent of the Textiles Committee.</div>
        <div class="footer-quote">Please quote Test Report No. and date for all future correspondence.</div>
        <div class="footer-complaints">Complaints if any, are to be received within 45 days from the date of issue of test report.</div>
        <div class="footer-service">Avail services of Textiles Committee -Most Reliable and Most Accurate</div>
        <div class="footer-social">"Follow us on <strong>f</strong> fb.com/textilescommittee, <strong>✉</strong> @ TexComIndia"</div>
    </div>

    {{-- ===== Footer Page Script (dompdf) ===== --}}
    @php
        $jsReportNo = addslashes($meta['report_no'] ?? '');
        $jsReportDate = addslashes($meta['date'] ?? '');
        $jsCustomer = addslashes($meta['customer_name'] ?? '');
        $jsSigner = addslashes($reportSigner);
    @endphp
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font       = $fontMetrics->get_font("DejaVu Sans", "normal");
                $bold       = $fontMetrics->get_font("DejaVu Sans", "bold");
                $size       = 10;
                $pageWidth  = $pdf->get_width();
                $pageHeight = $pdf->get_height();
 
                if ($PAGE_NUM > 1) {
                    $pdf->text(50, 160, "Report No: {!! $jsReportNo !!}", $font, 9, [0,0,0]);
                    $dateText  = "Date: {!! $jsReportDate !!}";
                    $textWidth = $fontMetrics->get_text_width($dateText, $font, 9);
                    $pdf->text($pageWidth - 55 - $textWidth, 160, $dateText, $font, 9);
                    $pdf->text(50, 175, "Customer: {!! $jsCustomer !!}", $font, 9, [0,0,0]);

                    $contText = "Continued ...........";
                    $contWidth = $fontMetrics->get_text_width($contText, $bold, 10);
                    $pdf->text($pageWidth - 50 - $contWidth, 145, $contText, $bold, 10, [0,0,0]);
                }

                $pageText  = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                $pdf->text(($pageWidth - $textWidth) / 2, $pageHeight - 130, $pageText, $font, $size, [0,0,0]);

                $pdf->text($pageWidth - 155, $pageHeight - 155, "Authorized Signatory", $font, 9, [0.3,0.3,0.3]);
                $pdf->text($pageWidth - 155, $pageHeight - 170, "{!! $jsSigner !!}", $bold, 10, [0,0,0]);
            ');
        }
    </script>

</body>

</html>
