<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ public_path('backAssets/css/dashlite.css') }}">
    <title>Report {{ $meta['report_no'] }} - v{{ $report->tr09_version_number }}</title>

    <style>
        @page {
            margin: 270px 30px 260px 30px;
        }

        @page :first {
            margin-top: 270px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        h3,
        h4,
        h5 {
            margin: 4px 0;
            text-align: center;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        tr {
            page-break-inside: avoid;
        }

        tbody {
            page-break-inside: avoid;
            display: table-row-group;
        }

        tbody.test-block {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .table-primary,
        .table-secondary {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        small {
            font-size: 10px;
            color: #555;
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

        /* Two logo placeholders */
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

        /* Actual <img> tags inside logo placeholders */
        .logo-left img,
        .logo-right img {
            width: 100%;
            height: auto;
        }

        /* Center header text block */
        .header-center {
            position: absolute;
            top: 0px;
            left: 55px;
            right: 55px;
            text-align: center;
            line-height: 1.2;
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
            font-size: 10px;
            color: #000;
            margin-bottom: 2px;
        }

        .header-contact {
            font-size: 10px;
            color: #000;
            margin-bottom: 2px;
        }

        .header-email {
            font-size: 10px;
            color: #c00000;
        }

        /* Format No aligned right */
        .header-format-no {
            position: absolute;
            top: 155px;
            right: 5px;
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }

        /* TEST REPORT title row */
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

        /* Continued ... aligned right */
        .header-continued {
            position: absolute;
            top: 205px;
            right: 5px;
            font-size: 11px;
            font-weight: bold;
            color: #000;
            font-style: italic;
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

        /* Aryl Section Styles */
        .aryl-section {
            page-break-inside: avoid;
            margin-bottom: 15px;
            font-size: 10px;
        }

        .aryl-legal-header {
            font-size: 10.5px;
            line-height: 1.3;
            margin: 6px 0;
            text-align: justify;
        }

        .aryl-legal-header strong {
            font-weight: bold;
        }

        .aryl-side-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin: 6px 0;
        }

        .aryl-side-table th,
        .aryl-side-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            line-height: 1.25;
        }

        .aryl-side-table th {
            background: #f9f9f9;
            font-weight: bold;
        }

        .aryl-footer {
            font-size: 9.5px;
            margin-top: 4px;
            text-align: justify;
        }

        .table-primary {
            font-weight: bold;
        }

        /* First page specific styles */
        .first-page-header {
            margin-top: -55px;
            /* Pulls the table up to reduce empty space from header */
            margin-bottom: 15px;
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

        .footer-iso {
            position: absolute;
            top: 5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #000;
        }

        .footer-note {
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-disclaimer {
            position: absolute;
            top: 38px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-quote {
            position: absolute;
            top: 51px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #c00000;
        }

        .footer-complaints {
            position: absolute;
            top: 64px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-service {
            position: absolute;
            top: 78px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }

        .footer-social {
            position: absolute;
            top: 92px;
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

        /* Signatory section */
        .signatory-section {
            margin-top: 30px;
            text-align: right;
            padding-right: 50px;
        }

        .signatory-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-top: 40px;
        }

        .signatory-name {
            font-weight: bold;
            margin-top: 5px;
        }

        .signatory-title {
            font-size: 10px;
            color: #555;
        }

        .divider {
            text-align: center;
            margin: 6px 0;
            font-weight: bold;
            font-size: 9px;
        }

        /* Nested table styles */
        .table-secondary {
            background-color: #f8f9fa;
        }

        .ps-4 {
            padding-left: 2.5rem !important;
        }

        /* End of Report */
        .end-of-report {
            text-align: center;
            margin-top: 16px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 11px;
        }

        /* Page number */
        .page-number {
            position: absolute;
            bottom: -255px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="outer-border"></div>

    {{-- ===== LETTERHEAD HEADER (fixed, repeats every page) ===== --}}
    <div class="header-content">

        {{-- LEFT LOGO PLACEHOLDER --}}
        <div class="logo-left">
            @php
                $leftLogoPath = base_path('backAssets/images/logo.png');
                $leftLogoSrc = file_exists($leftLogoPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($leftLogoPath))
                    : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
            @endphp
            <img src="{{ $leftLogoSrc }}" alt="Textiles Committee of India">
        </div>

        {{-- RIGHT LOGO PLACEHOLDER (NABL) --}}
        @if(isset($hasAccreditedTests) && $hasAccreditedTests)
        <div class="logo-right" style="text-align: center;">
            @php
                $rightLogoPath = base_path('backAssets/images/accrediation.png');
                $rightLogoSrc = file_exists($rightLogoPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($rightLogoPath))
                    : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
            @endphp
            <img src="{{ $rightLogoSrc }}" alt="Accreditation Logo" style="width: 70px;">
            @if($sample->ro && $sample->ro->certificate_no)
            <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">{{ $sample->ro->certificate_no }}</div>
            @endif
        </div>
        @endif

        {{-- CENTER HEADER --}}
        <div class="header-center">
            <div class="header-eng-title">LABORATORIES</div>
            <div class="header-eng-big">{{ $sample->ro->lab_name_en ?? 'TEXTILES COMMITTEE' }}</div>
            <div class="header-eng-ministry">{{ $sample->ro->ministry_en ?? 'Government of India, Ministry of Textiles' }}</div>
            <div class="header-address">{{ $sample->ro->lab_address ?? 'P. Balu Road, Prabhadevi Chowk, Prabhadevi, Mumbai-400 025.' }}</div>
            <div class="header-contact">{{ $sample->ro->lab_contact ?? 'Tel.: +91-22-6652 7541 / 545 / 550 / 607' }}</div>
            <div class="header-email">{{ $sample->ro->lab_email ?? '* E-mail : dlab.tc@nic.in / tclabmumbai@gmail.com * Website : www.textilescommittee.nic.in' }}</div>
        </div>

        {{-- Format No. top-right --}}
        {{-- <div class="header-format-no">Format No. 04/26/23</div> --}}
  
        {{-- TEST REPORT title --}}
        <div class="header-report-title">TEST REPORT</div>

        @if(isset($hasAccreditedTests) && $hasAccreditedTests && $sample->tr04_ulr_no)
        <div style="position: absolute; top: 165px; left: 0; right: 0; text-align: center; font-size: 11px; font-weight: bold;">
            ULR Number : {{ $sample->tr04_ulr_no }}
        </div>
        @endif

        {{-- Continued text is rendered on pages > 1 via dompdf script below --}}

        {{-- Bottom divider line --}}
        <div class="header-divider"></div>
    </div>

    {{-- ===== FIRST PAGE CONTENT ===== --}}
    <div class="first-page">

        {{-- ===== FIRST PAGE DESCRIPTIVE HEADER ===== --}}
        @php
            $isCustom = ($sample->m09_customer_type_id == 4) || ($sample->customerType && str_contains(strtolower($sample->customerType->m09_name), 'custom'));
        @endphp

        {{-- ===== FIRST PAGE DESCRIPTIVE HEADER ===== --}}
        <div class="first-page-header">
            <table>
                <tbody>
                    <tr>
                        <th colspan="2" style="width: 80%;">
                            Test Report No : {{ $meta['report_no'] }}
                            <span style="float: right;">
                                Date : {{ $meta['date'] }}
                            </span>
                        </th>
                        @if(!$isCustom)
                        <th rowspan="3" class="text-center"
                            style="vertical-align: middle; width: 20%; padding: 2px;">
                            @php
                                $swatchHtml = 'Sample <br> Swatch';
                                if (!empty($sample->tr04_attachment)) {
                                    $swatchPath = storage_path('app/public/' . $sample->tr04_attachment);
                                    if (file_exists($swatchPath)) {
                                        $ext = pathinfo($swatchPath, PATHINFO_EXTENSION);
                                        $swatchSrc =
                                            'data:image/' .
                                            ($ext === 'jpg' ? 'jpeg' : $ext) .
                                            ';base64,' .
                                            base64_encode(file_get_contents($swatchPath));
                                        $swatchHtml =
                                            '<img src="' .
                                            $swatchSrc .
                                            '" style="max-width: 70px; max-height: 48px; border: 1px solid #ccc;" alt="Swatch">';
                                    }
                                }
                            @endphp
                            {!! $swatchHtml !!}
                        </th>
                        @endif
                    </tr>
                    @if($isCustom)
                        <tr>
                            <th style="width: 30%;">Name &amp; Address of Customer :</th>
                            <td colspan="2" style="width: 70%;">{{ $meta['customer_name'] }}<br>{{ $meta['customer_address'] }}</td>
                        </tr>
                        <tr>
                            <th>Sample forwarding letter No. &amp; date :</th>
                            <td colspan="2">Test Memo No. {{ $meta['reference'] }} dated {{ \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date of receipt of sample :</th>
                            <td colspan="2">{{ Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Buyers Name &amp; address (Optional) :</th>
                            <td colspan="2">{{ $meta['buyer'] }}</td>
                        </tr>
                        <tr>
                            <th>Customer Sample No. :</th>
                            <td>BE No. {{ $meta['be_no'] }}</td>
                            <th class="text-center" style="width: 20%;">Lab. Sample No.</th>
                        </tr>
                        <tr>
                            <th>Sample Description :</th>
                            <td>{{ $meta['sample_description'] }}</td>
                            <td class="text-center">{{ $meta['report_no'] }}</td>
                        </tr>
                    @else
                        <tr>
                            <th rowspan="2" style="width: 30%;">Name &amp; Address of Customer</th>
                            <td style="width: 50%;">{{ $meta['customer_name'] }}</td>
                        </tr>
                        <tr>
                            <td>{{ $meta['customer_address'] }}</td>
                        </tr>
                        <tr>
                            <th>Sample forwarding letter No. &amp; date</th>
                            <td colspan="2">{{ $meta['reference'] }} dtd. {{ \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date of receipt of sample</th>
                            <td>{{ Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}</td>
                            <th rowspan="2" class="text-center" style="vertical-align: middle; width: 20%;">Lab. Sample No.</th>
                        </tr>
                        <tr>
                            <th>Customer Sample No :</th>
                            <td>{{ $meta['be_no'] }}</td>
                        </tr>
                        <tr>
                            <th>Sample Description:</th>
                            <td>{{ $meta['sample_description'] }}</td>
                            <td rowspan="3" class="text-center" style="vertical-align: middle;">{{ $meta['report_no'] }}</td>
                        </tr>
                        <tr>
                            <th>Sample Characteristics:</th>
                            <td>{{ $meta['sample_characteristics'] }}</td>
                        </tr>
                        <tr>
                            <th>Date of Performance of Tests:</th>
                            <td>
                                {{ \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }}
                                to
                                {{ $sample->testResult->first() && $sample->testResult->first()->tr07_performance_date ? \Carbon\Carbon::parse($sample->testResult->first()->tr07_performance_date)->format('d.m.Y') : \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- ===== MAIN BODY ===== --}}
        <div class="container">
            <h4>TEST RESULTS</h4>

            @if($isCustom)
            {{-- Sample Description Section --}}
            <table>
                <tbody>
                    <tr>
                        <th style="width:30%">Sample Characteristics</th>
                        <td>{{ $meta['sample_characteristics'] }}</td>
                    </tr>
                    <tr>
                        <th>Date of Performance of Test(s)</th>
                        <td>{{ \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }} to {{ $sample->testResult->first() && $sample->testResult->first()->tr07_performance_date ? \Carbon\Carbon::parse($sample->testResult->first()->tr07_performance_date)->format('d.m.Y') : \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }}</td>
                    </tr>
                    <tr>
                        <th>Laboratory Sample No.</th>
                        <td>{{ $meta['report_no'] }}</td>
                    </tr>
                </tbody>
            </table>
            @else
            <table>
                <tbody>
                    @if(isset($hasAccreditedTests) && $hasAccreditedTests && $sample->tr04_ulr_no)
                    <tr>
                        <th style="width:30%">ULR No.</th>
                        <td>{{ $sample->tr04_ulr_no }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th style="width:30%">Laboratory Sample No.</th>
                        <td>{{ $meta['report_no'] }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- ==== Aryl Amines Section (FIRST PAGE ONLY) ==== --}}
            @php
                $arylAminesTest = null;
                foreach ($orderedItems as $item) {
                    if ($item['type'] === 'test') {
                        $results = $groupedResults[$item['test_number']] ?? collect();
                        $parent = $results->first();
                        if ($parent) {
                            $testName = $parent->test->m12_name ?? '';
                            if (strpos(strtolower($testName), 'Banned amines') !== false) {
                                $arylAminesTest = ['parent' => $parent, 'results' => $results];
                                break;
                            }
                        }
                    }
                }
            @endphp

            @if ($arylAminesTest)
                <div class="aryl-section">
                    <div class="aryl-legal-header">
                        <strong>Presence of dyes prohibited by the Government of India under Section 6(2)(D) of the
                            Environment (Protection) Act, 1986 (29 of 1986) read with Rule 13 of the Environment
                            (Protection) Rules, 1986 vide Notifications S.O.108(E) dated 30th January, 1990 and
                            S.O.243(E) dated 26th March 1997.</strong>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="divider">
                        ****************************************************************************************************
                    </div>

                    <div style="font-size:10px; font-weight:bold; margin:4px 0;">
                        Details of release of individual aryl amines (mg/kg) on reductive cleavage with sodium
                        di-thionite.
                    </div>

                    <table class="aryl-side-table">
                        <thead>
                            <tr class="text-center">
                                <th style="width:6%;">Sr. No</th>
                                <th style="width:32%;">Name of the amines</th>
                                <th style="width:12%;">Contents</th>
                                <th style="border:none; background:transparent; width:2%;"></th>
                                <th style="width:6%;">Sr. No</th>
                                <th style="width:32%;">Name of the amines</th>
                                <th style="width:12%;">Contents</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $primaryTests = $arylAminesTest['results']->whereNotNull('m16_primary_test_id');
                                $firstHalf = $primaryTests->take(ceil($primaryTests->count() / 2))->values();
                                $secondHalf = $primaryTests->slice(ceil($primaryTests->count() / 2))->values();
                                $startNumber = $firstHalf->count() + 1;
                                $maxRows = max($firstHalf->count(), $secondHalf->count());
                            @endphp
                            @for ($i = 0; $i < $maxRows; $i++)
                                <tr>
                                    @if (isset($firstHalf[$i]))
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $firstHalf[$i]->primaryTest->m16_name ?? '' }}</td>
                                        <td class="text-center">{{ $firstHalf[$i]->tr07_result ?? 'Not detected' }}
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif

                                    <td style="border:none; background:transparent;"></td>

                                    @if (isset($secondHalf[$i]))
                                        <td class="text-center">{{ $startNumber + $i }}</td>
                                        <td>{{ $secondHalf[$i]->primaryTest->m16_name ?? '' }}</td>
                                        <td class="text-center">{{ $secondHalf[$i]->tr07_result ?? 'Not detected' }}
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div class="aryl-footer">
                        <p>1. As per Section 6(2)(D) of the Environment (Protection) Act 1986 (29 of 1986) read with
                            Rule 13 of the Environment (Protection) Rules, 1986, handling of hazardous dyes which
                            release
                            any one or more of the 22 harmful amines are prohibited. Details of release of harmful
                            amines,
                            if any, is given above.</p>
                        <p>2. <strong>Detected:</strong> Any one or more of the banned amine(s) is/are detected under
                            test condition and the sum parameter &gt; 50 mg/kg, and it is concluded that azo dyes had
                            been used
                            for production or modification of the forwarded material, which are prohibited as per the
                            above
                            mentioned Act.</p>
                        <p>3. <strong>Not Detected:</strong> Contents of banned amines ≤ 50 mg/kg...</p>
                    </div>
                </div>
            @endif

            {{-- ===== Main Test Results Table ===== --}}
            <table>
                <thead>
                    <tr class="text-center">
                        <th style="width:6%;">#</th>
                        <th>Test / Parameter</th>
                        <th style="width:28%;">Result</th>
                    </tr>
                </thead>

                @php $counter = 1; @endphp
                @foreach ($orderedItems as $item)
                    @if ($item['type'] === 'test')
                        @php
                            $results = $groupedResults[$item['test_number']] ?? collect();
                            $parent = $results->first();
                        @endphp

                        @if ($parent)
                            @php
                                $testName = $parent->test->m12_name ?? '';
                                $isArylAminesTest = strpos(strtolower($testName), 'aryl amine') !== false;
                            @endphp

                            @if (!$isArylAminesTest)
                                <tbody class="test-block">
                                    <tr class="table-primary">
                                        <td class="text-center">{{ $counter }}</td>
                                        <td>
                                            <strong>{{ $testName ?: 'Test #' . $item['test_number'] }}</strong>&nbsp;-&nbsp;
                                            @php
                                                $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $item['test_number']);
                                                $standardMethod = optional($sampleTest->standard)->m15_method ?? optional($parent->test->standard)->m15_method ?? null;
                                            @endphp
                                            @if ($standardMethod)
                                                <small>({{ $standardMethod }})</small>
                                            @endif
                                            {{ $parent->tr07_unit }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $hasPrimary = $results
                                                    ->whereNotNull('m16_primary_test_id')
                                                    ->isNotEmpty();
                                            @endphp
                                            @if (!$hasPrimary)
                                                {{ $parent->tr07_result ?? '' }}
                                            @endif
                                        </td>
                                    </tr>

                                    @php $subCounter = 1; @endphp

                                    @if ($hasPrimary)
                                        @foreach ($results->groupBy('m16_primary_test_id') as $primaryId => $primaryResults)
                                            @php
                                                $primaryTest = $primaryResults->first()->primaryTest;
                                                $hasSecondary = $primaryResults
                                                    ->whereNotNull('m17_secondary_test_id')
                                                    ->isNotEmpty();
                                            @endphp

                                            @if ($hasSecondary)
                                                <tr class="table-secondary">
                                                    <td class="text-center"></td>
                                                    <td class="text-end">
                                                        <em>
                                                            {{ $primaryTest->m16_name ?? 'Primary Parameter' }}
                                                            @if (!empty($primaryResults->first()->tr07_unit))
                                                                <i>({{ $primaryResults->first()->tr07_unit }})</i>
                                                            @endif
                                                        </em>
                                                    </td>
                                                    <td class="text-center"></td>
                                                </tr>

                                                @foreach ($primaryResults->whereNotNull('m17_secondary_test_id') as $secondary)
                                                    <tr>
                                                        <td class="text-center"></td>
                                                        <td class="text-end">
                                                            <em>
                                                                {{ $secondary->secondaryTest->m17_name ?? 'Secondary Parameter' }}
                                                                @if (!empty($secondary->tr07_unit))
                                                                    <i>({{ $secondary->tr07_unit }})</i>
                                                                @endif
                                                            </em>
                                                        </td>
                                                        <td class="text-center">{{ $secondary->tr07_result ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="table-secondary">
                                                    <td class="text-center"></td>
                                                    <td class="text-end">
                                                        <em>
                                                            {{ $primaryTest->m16_name ?? 'Primary Parameter' }}
                                                            @if (!empty($primaryResults->first()->tr07_unit))
                                                                <i>({{ $primaryResults->first()->tr07_unit }})</i>
                                                            @endif
                                                        </em>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $primaryResults->first()->tr07_result ?? '-' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif

                                    @php
                                        $customFields = $groupedCustomFields[$item['test_number']] ?? collect();
                                    @endphp

                                    @if ($customFields->isNotEmpty())
                                        @foreach ($customFields as $custom)
                                            <tr class="table-secondary">
                                                <td class="text-center"></td>
                                                <td class="text-end">
                                                    <em>
                                                        {{ $custom->tr08_field_name }}
                                                        @if (!empty($custom->tr08_field_unit))
                                                            <i>({{ $custom->tr08_field_unit }})</i>
                                                        @endif
                                                    </em>
                                                </td>
                                                <td class="text-center">{{ $custom->tr08_field_value }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                @php $counter++; @endphp
                            @else
                                @php $counter++; @endphp
                            @endif
                        @endif
                    @endif
                @endforeach
            </table>

            {{-- ===== END OF REPORT (after last table row on every/last page) ===== --}}
            <div class="end-of-report">----------End of Report ----------</div>

        </div>
    </div>

    {{-- ===== FOOTER (fixed, repeats every page) ===== --}}
    <div class="footer-content">
        <div class="footer-divider"></div>
        <!-- <div class="footer-iso">** ISO: 17025 Accredited Testing Laboratory **</div> -->
        <div class="footer-note">Sample not drawn by Textiles Committee, Results relate only to the sample tested.</div>
        <div class="footer-disclaimer">This test report shall not be published in any form without the explicit written
            consent of the Textiles Committee.</div>
        <div class="footer-quote">Please quote Test Report No. and date for all future correspondence.</div>
        <div class="footer-complaints">Complaints if any, are to be received within 45 days from the date of issue of
            test report.</div>
        <div class="footer-service">Avail services of Textiles Committee -Most Reliable and Most Accurate</div>
        <div class="footer-social">"Follow us on <strong>f</strong> fb.com/textilescommittee, <strong>✉</strong> @
            TexComIndia"</div>
    </div>

    {{-- ===== Page Number ===== --}}
    {{-- <div class="page-number">Page <span class="page-count"></span></div> --}}

    {{-- ===== Footer Page Script (dompdf) ===== --}}
    @php
        $jsReportNo = addslashes($meta['report_no'] ?? '');
        $jsReportDate = addslashes($meta['date'] ?? '');
        $jsCustomer = addslashes($meta['customer_name'] ?? '');
        $jsSigner = addslashes($report->generator->m06_name ?? 'Manager');
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
                    // Right-aligned around X=$pageWidth - 35, Y=20.
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
