<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ public_path('backAssets/css/dashlite.css') }}">
    <title>Report {{ $meta['report_no'] }} - v{{ $report->tr09_version_number }}</title>

    <style>
        @page {
            margin: 290px 30px 135px 30px;
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
            margin-top: 0;
        }

        h3,
        h4,
        h5 {
            margin: 0;
            text-align: center;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        tr {
            page-break-inside: avoid !important;
        }

        tbody {
            page-break-inside: avoid !important;
            display: table-row-group;
        }

        tbody.test-block {
            page-break-inside: avoid !important;
            page-break-after: auto;
        }

        .table-primary,
        .table-secondary {
            page-break-inside: avoid !important;
        }

        thead {
            display: table-header-group;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
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
            top: -250px;
            left: 0;
            right: 0;
            height: 250px;
            background: transparent;
        }

        /* Two logo placeholders */
        .logo-left {
            position: absolute;
            top: 1px;
            left: 5px;
            width: 100px;
        }

        .logo-right {
            position: absolute;
            top: 1px;
            right: 5px;
            width: 100px;
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
            line-height: 1.15;
        }

        .header-eng-title {
            font-size: 10.5px;
            font-weight: 500;
            color: #0c023b;
            letter-spacing: 1.5px;
            margin-top: -2px;
        }

        .header-eng-big {
            font-size: 15px;
            font-weight: bold;
            color: #c00000;
            letter-spacing: 1px;
        }

        .header-eng-ministry {
            font-size: 12px;
            color: #0c023b;
            font-weight: bold;
        }

        .header-eng-lab {
            font-size: 10px;
            font-weight: bold;
            color: #008000;
        }

        .header-address {
            font-size: 9px;
            color: #0c023b;
        }

        .header-contact {
            font-size: 9.5px;
            color: #0c023b;
            margin-bottom: 1px;
        }

        .header-email {
            font-size: 9.5px;
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
            top: 122px;
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
            top: 118px;
            left: 0;
            right: 0;
            border-bottom: 1.5px solid #1c276b;
        }

        /* Outer page border */
        .outer-border {
            position: fixed;
            top: -260px;
            bottom: -100px;
            left: 0;
            right: 0;
            border: 1.5px solid #1c276b;
            z-index: -100;
        }

        /* Aryl Section Styles */
        .aryl-section {
            margin-bottom: 2px;
            font-size: 8px;
        }

        .end-of-report {
            text-align: center;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 2px;
            font-size: 12px;
        }

        .aryl-legal-header {
            font-size: 8px;
            line-height: 1.12;
            margin: 2px 0;
            text-align: justify;
        }

        .aryl-legal-header strong {
            font-weight: bold;
        }

        .aryl-side-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 2px 0;
        }

        .aryl-side-table th,
        .aryl-side-table td {
            border: 1px solid #000;
            padding: 1px 2px;
            line-height: 1.05;
        }

        .aryl-side-table th {
            background: #f9f9f9;
            font-weight: bold;
        }

        .aryl-footer {
            font-size: 10px;
            margin-top: 2px;
            text-align: justify;
        }

        .aryl-footer p {
            margin: 0 0 1.5px 0;
            line-height: 1.1;
        }

        .table-primary {
            font-weight: bold;
        }

        /* First page specific styles */
        .first-page-header {
            margin-top: -95px;
            margin-bottom: 0px;
        }

        .first-page-header table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }

        .first-page-header th,
        .first-page-header td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            background: #ffffff;
        }

        /* ===== FOOTER ===== */
        .footer-content {
            position: fixed;
            bottom: -103px;
            left: 0;
            right: 0;
            height: 70px;
            background: transparent;
        }

        /* .footer-iso {
            position: absolute;
            top: 5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #000;
        } */

        .footer-note {
            position: absolute;
            top: 4px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-disclaimer {
            position: absolute;
            top: 18px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-quote {
            position: absolute;
            top: 31px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #c00000;
        }

        .footer-complaints {
            position: absolute;
            top: 45px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }

        .footer-service {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #24004d;
            margin-top: 5px;
        }

        .footer-social {
            text-align: center;
            font-size: 10px;
            color: #008000;
        }

        .footer-outside {
            position: fixed;
            bottom: -120px;
            left: 0;
            right: 0;
            height: 25px;
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
            margin-top: 25px;
            margin-bottom: 2px;
            font-weight: bold;
            font-size: 12px;
        }

        /* Page number */
        .page-number {
            position: absolute;
            bottom: -130px;
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
    </div>

    <div class="footer-outside">
        <div class="footer-service">Avail services of Textiles Committee -Most Reliable and Most Accurate</div>
        <div class="footer-social">
            "Follow us on
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMTg3N0YyIj48cGF0aCBkPSJNMjQgMTIuMDczYzAtNi42MjctNS4zNzMtMTItMTItMTJzLTEyIDUuMzczLTEyIDEyYzAgNS45OSA0LjM4OCAxMC45NTQgMTAuMTI1IDExLjg1NHYtOC4zODVINy4wNzh2LTMuNDdoMy4wNDdWOS40M2MwLTMuMDA3IDEuNzkyLTQuNjY5IDQuNTMzLTQuNjY5IDEuMzEyIDAgMi42ODYuMjM1IDIuNjg2LjIzNXYyLjk1M0gxNS44M2MtMS40OTEgMC0xLjk1Ni45MjUtMS45NTYgMS44NzR2Mi4yNWgzLjMyOGwtLjUzMiAzLjQ3aC0yLjc5NnY4LjM4NUMxOS42MTIgMjMuMDI3IDI0IDE4LjA2MiAyNCAxMi4wNzN6Ii8+PC9zdmc+"
                style="width: 4px; height: 4px; vertical-align: baseline; margin: 4px;">
            fb.com/textilescommittee,
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjMDAwMDAwIj48cGF0aCBkPSJNMTguOTAxIDEuMTUzaDMuNjhsLTguMDQgOS4xOUwyNCAyMi44NDZoLTcuNDA2bC01LjgtNy41ODQtNi42MzggNy41ODRILjQ3NGw4LjYtOS44M0wwIDEuMTU0aDcuNTk0bDUuMjQzIDYuOTMyWk0xNy42MSAyMC42NDRoMi4wMzlMNi40ODYgMy4yNEg0LjI5OFoiLz48L3N2Zz4="
                style="width: 4px; height: 4px; vertical-align: baseline; margin: 4px;">
            @TexComIndia"
        </div>
    </div>

    @php
        $isCustom =
            $sample->m09_customer_type_id == 4 ||
            ($sample->customerType && str_contains(strtolower($sample->customerType->m09_name), 'custom'));
        $totalParts = count($reportParts);
        $romanMap = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
        $totalRoman = $romanMap[$totalParts] ?? $totalParts;
    @endphp

    @foreach ($reportParts as $pIndex => $partData)
        @php
            $orderedItems = $partData['items'];
            $partHasAccredited = $partData['has_accredited_tests'];

            $currentRoman = $romanMap[$pIndex + 1] ?? $pIndex + 1;

            $reportNoStr = $meta['report_no'] . ' Part ' . $currentRoman . ' of ' . $totalRoman;

            $swatchSrc = null;
            if (!empty($sample->tr04_attachment)) {
                $pathsToCheck = [storage_path('app/public/' . $sample->tr04_attachment)];
                foreach ($pathsToCheck as $swatchPath) {
                    if (file_exists($swatchPath) && is_file($swatchPath)) {
                        $ext = strtolower(pathinfo($swatchPath, PATHINFO_EXTENSION));
                        $imgType = in_array($ext, ['jpg', 'jpeg']) ? 'jpeg' : ($ext === 'png' ? 'png' : 'jpeg');
                        $swatchSrc =
                            'data:image/' . $imgType . ';base64,' . base64_encode(file_get_contents($swatchPath));
                        break;
                    }
                }
            }
        @endphp

        @if ($pIndex > 0)
            <div style="page-break-before: always;"></div>
        @endif

        <script type="text/php">
            if (isset($pdf)) {
                if (!isset($GLOBALS['part_starts'])) {
                    $GLOBALS['part_starts'] = [];
                }
                $GLOBALS['part_starts'][{{ $pIndex }}] = $PAGE_NUM;
            }
        </script>

        {{-- ===== LETTERHEAD HEADER (fixed, repeats every page) ===== --}}
        @if ($pIndex == 0)
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

                {{-- CENTER HEADER --}}
                <div class="header-center">
                    <div class="header-eng-title">LABORATORY</div>
                    <div class="header-eng-big">
                        {{ strtoupper($sample->ro->lab_name_en) ?? 'TEXTILES COMMITTEE' }}
                    </div>
                    <div class="header-eng-ministry">
                        {{ $sample->ro->ministry_en ?? 'Government of India, Ministry of Textiles' }}
                    </div>
                    <div class="header-address" style="font-size: 12px;">
                        <strong style="color: #008000; font-size: 14px;">Textile Laboratory & Research Centre</strong>
                        <br>
                        {{ $sample->ro->lab_address ?? 'P. Balu Road, Prabhadevi Chowk, Prabhadevi, Mumbai-400 025.' }}
                    </div>
                    <div class="header-contact">Tel. :
                        {{ $sample->ro->lab_contact ?? '+91-22-6652 7541 / 545 / 550 / 607' }}</div>
                    <div class="header-email">* Email :
                        {{ $sample->ro->lab_email ?? 'dlab.tc@nic.in / tclabmumbai@gmail.com' }} * Website :
                        www.textilescommittee.nic.in</div>
                </div>

                {{-- TEST REPORT title --}}
                <div class="header-report-title">TEST REPORT</div>

                {{-- Bottom divider line --}}
                <div class="header-divider"></div>
            </div>
        @endif

        {{-- RIGHT LOGO PLACEHOLDER (NABL) - Positioned fixed per part so it does not corrupt normal flow baseline --}}
        @if ($partHasAccredited)
            <div style="position: fixed; top: -245px; right: 5px; width: 100px; text-align: center; z-index: 10;">
                @php
                    $rightLogoPath = base_path('backAssets/images/accrediation.png');
                    $rightLogoSrc = file_exists($rightLogoPath)
                        ? 'data:image/png;base64,' . base64_encode(file_get_contents($rightLogoPath))
                        : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
                @endphp
                <img src="{{ $rightLogoSrc }}" alt="Accreditation Logo" style="width: 70px;">
                @if ($sample->ro && $sample->ro->certificate_no)
                    <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">{{ $sample->ro->certificate_no }}
                    </div>
                @endif
            </div>
        @endif



        {{-- ===== FIRST PAGE CONTENT ===== --}}

        {{-- ===== FIRST PAGE DESCRIPTIVE HEADER ===== --}}
@if ($pIndex == 0)
<div class="first-page-header">
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <!-- Header Row with Report No and Date -->
            <tr>
                <th colspan="4" style="width: 100%; text-align: left; border: 1px solid #000; padding: 5px; font-size: 12px;">
                    <span style="float: right;">Date : {{ $meta['date'] }}</span>
                    Test Report No : {{ $reportNoStr }}
                </th>
            </tr>
            
            <!-- Customer Name and Address -->
            <tr>
                <td style="width: 45%; border: 1px solid #000; padding: 5px; font-weight: bold; background-color: #f5f5f5;">Name &amp; Address of Customer :</td>
                <td colspan="3" style="width: 50%; border: 1px solid #000; padding: 5px;">
                    {{ $meta['customer_name'] }}<br>{{ $meta['customer_address'] }}
                </td>
            </tr>

            <!-- Sample Forwarding Letter -->
            <tr>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Sample forwarding letter No. &amp; date :</td>
                <td colspan="2" style="border: 1px solid #000;">
                    Test Memo No. {{ $meta['reference'] }} dated {{ \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d/m/Y') }}
                </td>
                <th rowspan="4" 
                    style="vertical-align: middle; width: 10%; border: 1px solid #000; text-align: center; background-color: #f9f9f9; min-height: 150px;">
                    <div style="border: 2px dashed #999; display: flex; align-items: center; justify-content: center; {{ $swatchSrc ? 'background: url(\'' . $swatchSrc . '\') no-repeat center center; background-size: contain;' : '' }}">
                        @if (!$swatchSrc)
                            <div style="color:#999; font-size:11px; text-align: center;">
                                <div style="font-size: 24px; margin-bottom: 5px;">🖼️</div>
                                <div>Sample<br>Swatch</div>
                                <div style="font-size: 8px; color: #ccc;">(Placeholder)</div>
                            </div>
                        @endif
                    </div>
                </th>
            </tr>

            <!-- Date of Receipt -->
            <tr>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Date of receipt of sample :</td>
                <td colspan="2" style="border: 1px solid #000;">
                    {{ Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}
                </td>
            </tr>

            <!-- Buyer Name (Customs Only) -->
            @if ($isCustom)
            <tr>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Buyers Name &amp; address (Optional) :</td>
                <td colspan="2" style="border: 1px solid #000;">
                    {{ $meta['buyer'] }}
                </td>
            </tr>
            @endif

            <!-- Customer Sample No / BE No -->
            <tr>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">
                    @if ($isCustom)
                        Customer Sample No. :
                    @else
                        Customer Sample No :
                    @endif
                </td>
                <td style="border: 1px solid #000; width: 50%;">
                    BE No. {{ $meta['be_no'] }}
                </td>
            </tr>

            <!-- Sample Description and Lab Sample No -->
            <tr>
                <td style="border: 1px solid #000;  font-weight: bold; background-color: #f5f5f5;">Sample Description :</td>
                <td style="border: 1px solid #000; ">
                    {{ $meta['sample_description'] }}
                </td>
            </tr>

            <!-- Sample Characteristics -->
            <tr>
                <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Sample Characteristics:</td>
                <td colspan="2" style="border: 1px solid #000;">
                    {{ $meta['sample_characteristics'] }}
                </td>
                <td rowspan="2" style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; background-color: #f5f5f5; width: 30%;">
                    Lab. Sample No. 
                    {{ $meta['report_no'] }}
                </td>
            </tr>

            <!-- Date of Performance of Tests -->
            <tr>
                <td style="border: 1px solid #000; padding: 5px; font-weight: bold; background-color: #f5f5f5;">Date of Performance of Tests:</td>
                <td colspan="2" style="border: 1px solid #000; padding: 5px;">
                    {{ \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }} to
                    {{ $sample->testResult->first() && $sample->testResult->first()->tr07_performance_date ? \Carbon\Carbon::parse($sample->testResult->first()->tr07_performance_date)->format('d.m.Y') : \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y') }}
                </td>
            </tr>

            <!-- ULR No (Conditional) -->
            @if ($partHasAccredited && $sample->tr04_ulr_no)
            <tr>
                <td style="width:30%; border: 1px solid #000; padding: 5px; font-weight: bold; background-color: #f5f5f5;">ULR No.</td>
                <td colspan="3" style="border: 1px solid #000; padding: 5px;">{{ $sample->tr04_ulr_no }}</td>
            </tr>
            @endif

            <!-- Sample Mark (Customs Only) -->
            @if ($isCustom)
            <tr>
                <td style="width:30%; border: 1px solid #000; padding: 5px; font-weight: bold; background-color: #f5f5f5;">Sample Mark</td>
                <td colspan="3" style="border: 1px solid #000; padding: 5px;">{{ $meta['sample_characteristics'] }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endif


        {{-- ===== MAIN BODY ===== --}}
        <div class="container">


            <h4 style="margin-bottom: 5px; font-size: 13px;">TEST RESULTS</h4>

            {{-- ==== Aryl Amines Section (FIRST PAGE ONLY) ==== --}}
            @php
                $arylAminesTest = null;
                foreach ($orderedItems as $item) {
                    if ($item['type'] === 'test') {
                        $results = $groupedResults[$item['test_number']] ?? collect();
                        $parent = $results->first();
                        if ($parent) {
                            $testName = $parent->test->m12_name ?? '';
                            if (
                                strpos(strtolower($testName), 'banned amines') !== false ||
                                strpos(strtolower($testName), 'aryl amine') !== false ||
                                strpos(strtolower($testName), 'azo') !== false
                            ) {
                                $arylAminesTest = ['parent' => $parent, 'results' => $results];
                                break;
                            }
                        }
                    }
                }
            @endphp

            @if ($arylAminesTest)
                <div class="aryl-section">
                    <div style="display: table; width: 100%; border: 1px solid #000; margin-bottom: 2px;">
                        <div
                            style="display: table-cell; width: 75%; padding: 2px 4px; border-right: 1px solid #000; font-weight: bold; text-align: justify; font-size: 10px; line-height: 1.12;">
                            Presence of dyes prohibited by the Government of India under Section 6(2) (D) of the
                            Environment (protection) Act, 1986 (29 of 1986) read with Rule 13 of the Environment
                            (Protection) Rules, 1986 vide Notifications S.O.108(E) dated 30th January, 1990 and
                            S.O. 243(E) dated 26th March 1997.
                        </div>
                        <div
                            style="display: table-cell; width: 25%; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold; font-size: 10px;">
                            {{ $arylAminesTest['parent']->tr07_result ?? 'Not detected' }}
                        </div>
                    </div>

                    <div class="divider" style="margin: 1px 0; font-size: 10px;">
                        ****************************************************************************************************
                    </div>

                    <div style="font-size:10px; font-weight:bold; margin:1px 5px 0px 5px;">
                        Details of release of individual aryl amines (mg/kg) on reductive cleavage with sodium
                        di-thionite.
                    </div>

                    <table class="aryl-side-table">
                        <thead>
                            <tr class="text-center">
                                <th style="width:6%;">S.No</th>
                                <th style="width:32%;">Name of the amines</th>
                                <th style="width:12%;">Contents</th>
                                <th style="border:none; background:transparent; width:1%;"></th>
                                <th style="width:6%;">S.No</th>
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

                    <div class="aryl-footer" style="margin: 0 10px">
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

            @php
                $hasNonArylItems = false;
                foreach ($orderedItems as $chkItem) {
                    if ($chkItem['type'] === 'test') {
                        $results = $groupedResults[$chkItem['test_number']] ?? collect();
                        $parent = $results->first();
                        if ($parent) {
                            $testName = $parent->test->m12_name ?? '';
                            if (
                                strpos(strtolower($testName), 'banned amines') === false &&
                                strpos(strtolower($testName), 'aryl amine') === false &&
                                strpos(strtolower($testName), 'azo') === false
                            ) {
                                $hasNonArylItems = true;
                                break;
                            }
                        }
                    } else {
                        $hasNonArylItems = true;
                        break;
                    }
                }
            @endphp

            @if ($hasNonArylItems)
                @if (!empty($arylAminesTest))
                    <div style="page-break-before: always;"></div>
                @endif
                {{-- ===== Main Test Results Table ===== --}}
                <table class="main-table">
                    <thead>
                        <tr class="text-center">
                            <th style="width:8%;">Sr. No</th>
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
                                    $isArylAminesTest =
                                        strpos(strtolower($testName), 'banned amines') !== false ||
                                        strpos(strtolower($testName), 'aryl amine') !== false ||
                                        strpos(strtolower($testName), 'azo') !== false;
                                @endphp

                                @if (!$isArylAminesTest)
                                    <tbody class="test-block">
                                        <tr class="table-primary">
                                            <td class="text-center">{{ $counter }}.</td>
                                            <td>
                                                <strong>{{ $testName ?: 'Test #' . $item['test_number'] }}</strong>&nbsp;-&nbsp;
                                                @php
                                                    $sampleTest = $sample->sampleTests->firstWhere(
                                                        'm12_test_number',
                                                        $item['test_number'],
                                                    );
                                                    $standardMethod =
                                                        optional($sampleTest->standard)->m15_method ??
                                                        (optional($parent->test->standard)->m15_method ?? null);
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
                                                    {!! $parent->tr07_result ?? '' !!}
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
                                                            <td class="text-center">{!! $secondary->tr07_result ?? '-' !!}
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
                                                            {!! $primaryResults->first()->tr07_result ?? '-' !!}</td>
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
                                                    <td class="text-center">{!! $custom->tr08_field_value !!}</td>
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
            @endif

            {{-- ===== SIGNATURE PART ===== --}}
            <div class="signature-part"
                style="text-align: right; margin-top: 40px; margin-right: 15px; page-break-inside: avoid;">
                <div style="font-weight: bold; font-size: 14px;">{{ $report->generator->m06_name ?? 'Manager' }}</div>
                <div style="font-size: 13px; color: #333;">Authorized Signatory</div>
            </div>

            {{-- ===== END OF REPORT (after last table row on every/last page) ===== --}}
            <div class="end-of-report">----------End of Report ----------</div>

        </div>
    @endforeach

    {{-- ===== Footer Page Script (dompdf) ===== --}}
    @php
        $jsReportNo = addslashes($meta['report_no'] ?? '');
        $jsReportDate = addslashes($meta['date'] ?? '');
        $jsReference = addslashes($meta['reference'] ?? '');
        $jsReferenceDate = addslashes(\Carbon\Carbon::parse($sample->tr04_reference_date)->format('d.m.Y'));
        $jsReferenceDateCustom = addslashes(\Carbon\Carbon::parse($sample->tr04_reference_date)->format('d/m/Y'));
        $jsBuyer = addslashes($meta['buyer'] ?? '_');
        $jsBeNo = addslashes($meta['be_no'] ?? '_');
        $jsIsCustom = $isCustom ? 1 : 0;
    @endphp
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font       = $fontMetrics->get_font("DejaVu Sans", "normal");
                $bold       = $fontMetrics->get_font("DejaVu Sans", "bold");
                $boldItalic = $fontMetrics->get_font("DejaVu Sans", "bold_oblique");
                $size       = 10;
                $pageWidth  = $pdf->get_width();
                $pageHeight = $pdf->get_height();

                // Calculate per-part page number
                $partStarts = $GLOBALS["part_starts"] ?? [0 => 1];
                $partStarts[] = $PAGE_COUNT + 1; // Dummy end marker
                
                $currentPartIndex = 0;
                $partKeys = array_keys($partStarts);
                for ($i = 0; $i < count($partKeys) - 1; $i++) {
                    if ($PAGE_NUM >= $partStarts[$partKeys[$i]] && $PAGE_NUM < $partStarts[$partKeys[$i+1]]) {
                        $currentPartIndex = $partKeys[$i];
                        break;
                    }
                }
                
                $partStartPage = $partStarts[$currentPartIndex];
                $partEndPage = $partStarts[$currentPartIndex + 1] - 1;
                $partTotalPages = $partEndPage - $partStartPage + 1;
                $partCurrentPage = $PAGE_NUM - $partStartPage + 1;

                $numParts = count($GLOBALS["part_starts"] ?? [0 => 1]);
                $romanMap = [1 => "I", 2 => "II", 3 => "III", 4 => "IV"];
                $currentRoman = $romanMap[$currentPartIndex + 1] ?? ($currentPartIndex + 1);
                $totalRoman = $romanMap[$numParts] ?? $numParts;

                $partSuffix = " Part " . $currentRoman . " of " . $totalRoman;
                $fullReportNo = "{!! $jsReportNo !!}" . $partSuffix;

                // Draw tabular header on all pages EXCEPT Part 1 Page 1
                if (!($currentPartIndex == 0 && $partCurrentPage == 1)) {
                    // Outer table border
                    $pdf->rectangle(24, 142, 547, 67, [0,0,0], 0.8);

                    // Row dividers
                    $pdf->line(24, 163, 572, 163, [0,0,0], 0.8);
                    $pdf->line(24, 178, 572, 178, [0,0,0], 0.8);
                    $pdf->line(24, 193, 572, 193, [0,0,0], 0.8);

                    // Vertical column divider for rows 2, 3, 4
                    $pdf->line(230, 163, 230, 209, [0,0,0], 0.8);

                    // Row 1: Report No, Continued, Date
                    $pdf->text(35, 146, "Test Report No : " . $fullReportNo, $bold, 9, [0,0,0]);
                    $pdf->text(310, 146, "Continued ...........", $boldItalic, 9, [0,0,0]);
                    $pdf->text(465, 146, "Date : {!! $jsReportDate !!}", $bold, 9, [0,0,0]);

                    // Row 2: Sample forwarding letter
                    $lbl2 = {!! $jsIsCustom !!} ? "Sample forwarding letter No. & date :" : "Sample forwarding letter No. & date";
                    $val2 = {!! $jsIsCustom !!} ? "Test Memo No. {!! $jsReference !!} dated {!! $jsReferenceDateCustom !!}" : "{!! $jsReference !!} dtd. {!! $jsReferenceDate !!}";
                    $pdf->text(35, 164, $lbl2, $font, 9, [0,0,0]);
                    $pdf->text(235, 164, $val2, $font, 9, [0,0,0]);

                    // Row 3: Buyers Name
                    $lbl3 = {!! $jsIsCustom !!} ? "Buyers Name & address (Optional) :" : "Buyers Name & address";
                    $pdf->text(35, 179, $lbl3, $font, 9, [0,0,0]);
                    $pdf->text(235, 179, "{!! $jsBuyer !!}", $font, 9, [0,0,0]);

                    // Row 4: Customer Sample No
                    $lbl4 = {!! $jsIsCustom !!} ? "Customer Sample No. :" : "Customer Sample No :";
                    $val4 = {!! $jsIsCustom !!} ? "BE No. {!! $jsBeNo !!}" : "{!! $jsBeNo !!}";
                    $pdf->text(35, 194, $lbl4, $font, 9, [0,0,0]);
                    $pdf->text(235, 194, $val4, $font, 8.5, [0,0,0]);
                }

                $pageText  = "Page " . $partCurrentPage . " of " . $partTotalPages;
                $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                
                // Page counting (bottom right corner)
                $pdf->text($pageWidth - 90, $pageHeight - 112, $pageText, $font, $size, [0,0,0]);
            ');
        }
    </script>

</body>

</html>
