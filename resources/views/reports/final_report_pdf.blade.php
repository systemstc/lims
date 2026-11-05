<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ public_path('backAssets/css/dashlite.css') }}">
    <title>Report {{ $meta['report_no'] }} - v{{ $report->tr09_version_number }}</title>

    <style>
        @page {
            margin: 245pt 24pt 100pt 24pt;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
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
            top: -227pt;
            left: 0;
            right: 0;
            background: transparent;
        }

        /* Outer page border */
        .outer-border {
            position: fixed;
            top: -227pt;
            bottom: -82pt;
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
            margin-top: -75pt;
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
            bottom: -80pt;
            left: 0;
            right: 0;
            height: 52pt;
            background: transparent;
        }

        .footer-note {
            position: absolute;
            top: 2pt;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            color: #333;
        }

        .footer-disclaimer {
            position: absolute;
            top: 13pt;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            color: #333;
        }

        .footer-quote {
            position: absolute;
            top: 23pt;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            color: #c00000;
        }

        .footer-complaints {
            position: absolute;
            top: 34pt;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            color: #333;
        }

        .footer-outside {
            position: fixed;
            bottom: -90pt;
            left: 0;
            right: 0;
            height: 15pt;
        }

        .footer-service {
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            color: #24004d;
            margin-top: 3.5pt;
        }

        .footer-social {
            text-align: center;
            font-size: 6.5pt;
            color: #008000;
            margin-top: -2.5pt;
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

        .table-secondary {
            background-color: #f8f9fa;
        }

        .ps-4 {
            padding-left: 2.5rem !important;
        }

        .signature-qr-container {
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-qr-table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            margin-bottom: 5px;
        }

        .signature-qr-table tr,
        .signature-qr-table td {
            border: none !important;
            padding: 0 !important;
        }

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

    @php
        $onLetterhead = !empty($onLetterhead);
    @endphp

    @if (!$onLetterhead)
        <div class="outer-border"></div>

        {{-- ===== FOOTER (fixed, repeats every page) ===== --}}
        <div class="footer-content">
            <div class="footer-divider"></div>
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
    @endif

    @php
        $isCustom =
            $sample->m09_customer_type_id == 4 ||
            ($sample->customerType && str_contains(strtolower($sample->customerType->m09_name), 'custom'));
        $totalParts = count($reportParts);
    @endphp

    @foreach ($reportParts as $pIndex => $partData)
        @php
            $orderedItems = $partData['items'];
            $partHasAccredited = $partData['has_accredited_tests'];

            // Build remarks list specific to THIS part
            $partRemarksList = [];
            $partRemarksStarMap = [];
            $partRemarkCounter = 0;

            foreach ($orderedItems as $it) {
                if ($it['type'] === 'test') {
                    $tResults = $groupedResults[$it['test_number']] ?? collect();
                    foreach ($tResults as $res) {
                        $txt = trim($res->tr07_remarks ?? '');
                        if (empty($txt) && !empty($res->m24_remark_id)) {
                            $txt = trim($res->remarkRecord?->m24_remark_text ?? \App\Models\TestRemark::find($res->m24_remark_id)?->m24_remark_text ?? '');
                        }

                        if ($txt !== '') {
                            $resId = $res->tr07_test_result_id ? (string)$res->tr07_test_result_id : null;
                            $compositeKey = $it['test_number'] . '_' . ($res->m16_primary_test_id ?: '0') . '_' . ($res->m17_secondary_test_id ?: '0');

                            $existingStar = null;
                            if ($resId && isset($partRemarksStarMap[$resId])) {
                                $existingStar = $partRemarksStarMap[$resId];
                            } elseif (isset($partRemarksStarMap[$compositeKey])) {
                                $existingStar = $partRemarksStarMap[$compositeKey];
                            }

                            if (!$existingStar) {
                                $partRemarkCounter++;
                                $starStr = str_repeat('*', $partRemarkCounter);
                                if ($resId) {
                                    $partRemarksStarMap[$resId] = $starStr;
                                }
                                $partRemarksStarMap[$compositeKey] = $starStr;

                                $partRemarksList[] = [
                                    'stars' => $starStr,
                                    'text' => $txt,
                                    'test_name' => $res->test->m12_name ?? ('Test #' . $it['test_number']),
                                    'test_number' => $it['test_number'],
                                ];
                            } else {
                                if ($resId) {
                                    $partRemarksStarMap[$resId] = $existingStar;
                                }
                                $partRemarksStarMap[$compositeKey] = $existingStar;
                            }
                        }
                    }

                    $cFields = $groupedCustomFields[$it['test_number']] ?? collect();
                    foreach ($cFields as $cf) {
                        $cTxt = trim($cf->tr08_remarks ?? '');
                        if ($cTxt !== '') {
                            $cKey = 'cf_' . $cf->tr08_custom_field_id;
                            if (!isset($partRemarksStarMap[$cKey])) {
                                $partRemarkCounter++;
                                $starStr = str_repeat('*', $partRemarkCounter);
                                $partRemarksStarMap[$cKey] = $starStr;
                                $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $it['test_number']);
                                $partRemarksList[] = [
                                    'stars' => $starStr,
                                    'text' => $cTxt,
                                    'test_name' => $sampleTest?->test?->m12_name ?? ('Test #' . $it['test_number']),
                                    'test_number' => $it['test_number'],
                                ];
                            }
                        }
                    }
                }
            }

            $partCode = $partData['part_code'] ?? ($pIndex === 0 && !empty($partHasAccredited) ? 'A' : '');
            $reportNoSuffix = !empty($partCode) ? ' ' . $partCode : '';
            $reportNoStr = $meta['report_no'] . $reportNoSuffix;

            $swatchSrc = null;
            if (!empty($sample->tr04_attachment)) {
                $rawAttachment = $sample->tr04_attachment;
                $cleanAttachment = ltrim(str_replace('\\', '/', $rawAttachment), '/');
                $strippedAttachment = preg_replace('#^(public/|storage/)#', '', $cleanAttachment);

                $pathsToCheck = [
                    storage_path('app/public/' . $strippedAttachment),
                    storage_path('app/' . $cleanAttachment),
                    public_path('storage/' . $strippedAttachment),
                    public_path($cleanAttachment),
                    base_path('storage/app/public/' . $strippedAttachment),
                    base_path('public/storage/' . $strippedAttachment),
                ];
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

        {{-- ===== DYNAMIC ENGLISH HEADER (fixed, repeats every page) ===== --}}
        @if ($pIndex == 0 && !$onLetterhead)
            <div class="header-content">
                <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                    <tr>
                        <!-- Left Logos: TC logo + Swachh Bharat -->
                        <td style="width: 55pt; vertical-align: top; text-align: center; border: none; padding: 0;">
                            @php
                                $tcLogoPath = base_path('backAssets/images/logo.png');
                                $tcLogoSrc = file_exists($tcLogoPath)
                                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($tcLogoPath))
                                    : '';
                                $swachhLogoPath = base_path('backAssets/images/swach-bharat.png');
                                $swachhLogoSrc = file_exists($swachhLogoPath)
                                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($swachhLogoPath))
                                    : '';
                            @endphp
                            @if ($tcLogoSrc)
                                <img src="{{ $tcLogoSrc }}" style="width: 64pt; height: auto; display: block; margin: 8pt auto;">
                            @endif
                            @if ($swachhLogoSrc)
                                <img src="{{ $swachhLogoSrc }}" style="width: 62pt; height: auto; display: block; margin: -7pt auto 0 10pt;">
                            @endif
                        </td>

                        <!-- NABL Logo Slot (Reserved width so center text is always perfectly balanced) -->
                        <td style="width: 52pt; vertical-align: top; text-align: center; border: none; padding: 0 2pt;">
                            <!-- NABL Logo is drawn dynamically per page in CPDF page_script only on accredited parts -->
                        </td>

                        <!-- Center Text: Lab details from database -->
                        @php
                            $cleanWebsite = $sample->ro?->lab_website ? str_replace(['http://', 'https://'], '', $sample->ro->lab_website) : 'www.textilescommittee.nic.in';
                            $labName = !empty($sample->ro?->lab_name_en) ? strtoupper($sample->ro->lab_name_en) : 'TEXTILES COMMITTEE';
                            $ministry = $sample->ro?->ministry_en ?? 'Government of India, Ministry of Textiles';
                            $address = $sample->ro?->lab_address ?? 'P. Balu Road, Prabhadevi Chowk, Prabhadevi, Mumbai-400 025.';
                            $contact = $sample->ro?->lab_contact ?? '+91-22-6652 7541 / 545 / 550 / 607';
                            $email = $sample->ro?->lab_email ?? 'dlab.tc@nic.in / tclabmumbai@gmail.com';
                        @endphp
                        <td style="vertical-align: top; text-align: center; border: none; padding: 0 4pt; font-family: 'DejaVu Sans', Arial, sans-serif;">
                            <div style="font-size: 8.5pt; font-weight: bold; color: #0c023b; letter-spacing: 1.2px;">LABORATORY</div>
                            <div style="font-size: 13.5pt; font-weight: bold; color: #c00000; letter-spacing: 0.5px; margin: 1pt 0;">{{ $labName }}</div>
                            <div style="font-size: 9.5pt; font-weight: bold; color: #0c023b;">{{ $ministry }}</div>
                            <div style="font-size: 9pt; font-weight: bold; color: #008000; margin-top: 1pt;">Textile Laboratory &amp; Research Centre</div>
                            <div style="font-size: 8.5pt; color: #0c023b; line-height: 1.25; margin-top: 2pt;">{{ $address }}</div>
                            <div style="font-size: 8.5pt; color: #0c023b; margin-top: 2pt;">
                                <strong>Tel. :</strong> {{ $contact }}
                            </div>
                            <div style="font-size: 8pt; color: #c00000; margin-top: 1pt;">
                                * <strong>E-mail :</strong> {{ $email }}
                                * <strong>Website :</strong> {{ $cleanWebsite }}
                            </div>
                        </td>

                        <!-- Right Logos: 3TS + Beti Bachao -->
                        <td style="width: 70pt; vertical-align: top; text-align: center; border: none; padding: 0;">
                            @php
                                $threeTsPath = base_path('backAssets/images/3TS.png');
                                $threeTsSrc = file_exists($threeTsPath)
                                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($threeTsPath))
                                    : '';
                                $betiPath = base_path('backAssets/images/mss02.png');
                                $betiSrc = file_exists($betiPath)
                                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($betiPath))
                                    : '';
                            @endphp
                            @if ($threeTsSrc)
                                <img src="{{ $threeTsSrc }}" style="width: 80pt; height: auto; display: block; margin: 15pt 15pt auto;">
                            @endif
                            @if ($betiSrc)
                                <img src="{{ $betiSrc }}" style="width: 45pt; height: auto; display: block; margin: 8pt auto 0 auto;">
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Full-width Navy Divider Line -->
                <div style="border-bottom: 2px solid #1c276b; margin-top: 4pt; margin-bottom: 4pt;"></div>

                <!-- TEST REPORT and Format No -->
                <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                    <tr>
                        <td style="text-align: center; border: none; padding: 0;">
                            <span style="font-size: 14pt; font-weight: bold; color: #1c276b; text-decoration: underline; letter-spacing: 0.8px; font-family: 'DejaVu Sans', sans-serif;">TEST REPORT</span>
                        </td>
                        <td style="width: 130pt; text-align: left; border: none; padding: 0; vertical-align: middle;">
                            <span style="font-size: 9.5pt; font-weight: bold; color: transparent; font-family: 'DejaVu Sans', sans-serif;">&nbsp;</span>
                        </td>
                    </tr>
                </table>
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
                            <th colspan="4"
                                style="width: 100%; text-align: left; border: 1px solid #000; padding: 5px; font-size: 12px;">
                                <span style="float: right;">Date : {{ $meta['date'] }}</span>
                                Test Report No : {{ $reportNoStr }}
                            </th>
                        </tr>

                        <!-- Customer Name and Address -->
                        <tr>
                            <td
                                style="width: 35%; border: 1px solid #000; padding: 5px; font-weight: bold; background-color: #f5f5f5;">
                                Name &amp; Address of Customer :</td>
                            <td colspan="3" style="width: 65%; border: 1px solid #000; padding: 5px;">
                                {{ $meta['customer_name'] }}<br>{{ $meta['customer_address'] }}
                            </td>
                        </tr>

                        <!-- Sample Forwarding Letter -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Sample
                                forwarding letter No. &amp; date :</td>
                            <td colspan="2" style="border: 1px solid #000;">
                                Test Memo No. {{ $meta['reference'] }} dated
                                {{ \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d/m/Y') }}
                            </td>
                            <th rowspan="{{ $isCustom ? 7 : 6 }}"
                                style="vertical-align: top; width: 25%; border: 1px solid #000; text-align: center; background-color: #f9f9f9; padding: 0 !important; margin: 0;">
                                @if ($swatchSrc)
                                    <img src="{{ $swatchSrc }}" alt="Sample Swatch" style="width: 100%; height: {{ $isCustom ? '180px' : '155px' }}; display: block; margin: 0; padding: 0; border: 0;">
                                @else
                                    <div style="border: 1px dashed #999; margin: 4px; padding: 15px 5px; color: #777; font-size: 10px; text-align: center; background: #fff;">
                                        <strong>Sample<br>Swatch</strong><br>
                                        <span style="font-size: 8px; color: #999;">(Not Attached)</span>
                                    </div>
                                @endif
                            </th>
                        </tr>

                        <!-- Date of Receipt -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Date of receipt
                                of sample :</td>
                            <td colspan="2" style="border: 1px solid #000;">
                                {{ \Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}
                            </td>
                        </tr>

                        <!-- Customer Sample Reference -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Customer Sample
                                Reference :</td>
                            <td colspan="2" style="border: 1px solid #000;">
                                BE No. {{ $meta['be_no'] }}
                            </td>
                        </tr>

                        <!-- Customer Sample Description -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">
                                Customer's Sample Description :</td>
                            <td colspan="2" style="border: 1px solid #000;">
                                {{ $meta['sample_description'] }}
                            </td>
                        </tr>

                        <!-- Sample Characteristics -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Sample
                                Characteristics:</td>
                            <td colspan="2" style="border: 1px solid #000;">
                                {{ $meta['sample_characteristics'] }}
                            </td>
                        </tr>

                        <!-- Custom Field: Buyers Name (if custom) -->
                        @if ($isCustom)
                            <tr>
                                <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Buyers Name
                                    &amp; address (Optional) :</td>
                                <td colspan="2" style="border: 1px solid #000;">
                                    {{ $meta['buyer'] }}
                                </td>
                            </tr>
                        @endif

                        <!-- Date of Performance of Tests -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Date of
                                Performance of Tests:</td>
                            <td colspan="3" style="border: 1px solid #000;">
                                @php
                                    $perfDate = $meta['test_performance_date'];
                                    $receiptDate = \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y');
                                    $perfDateFormatted = \Carbon\Carbon::parse($perfDate)->format('d.m.Y');
                                @endphp
                                {{ $receiptDate }} to {{ $perfDateFormatted }}
                            </td>
                        </tr>

                        <!-- Laboratory Sample No -->
                        <tr>
                            <td style="border: 1px solid #000; font-weight: bold; background-color: #f5f5f5;">Laboratory
                                Sample No.</td>
                            <td colspan="3" style="border: 1px solid #000;">
                                {{ $meta['report_no'] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @php
            $arylAminesTest = null;
            $hasNonArylItems = false;
            foreach ($orderedItems as $it) {
                $testNumber = $it['test_number'] ?? null;
                if (!$testNumber) continue;
                $tResults = $groupedResults[$testNumber] ?? collect();
                $p = $tResults->first();
                $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $testNumber);
                $test = $p ? $p->test : ($sampleTest ? $sampleTest->test : null);
                $tName = $test?->m12_name ?? '';
                if (
                    $tName && (
                    strpos(strtolower($tName), 'banned amines') !== false ||
                    strpos(strtolower($tName), 'aryl amine') !== false ||
                    strpos(strtolower($tName), 'azo') !== false ||
                    strpos(strtolower($tName), 'amine') !== false)
                ) {
                    $arylAminesTest = [
                        'item' => $it,
                        'parent' => $p,
                        'results' => $tResults,
                        'test_number' => $testNumber
                    ];
                } else {
                    $hasNonArylItems = true;
                }
            }
        @endphp

        {{-- ===== SPECIAL FORMAT: BANNED AMINES (AZO FREE) ===== --}}
        @if ($arylAminesTest)
            @php
                $aParentOnly = $arylAminesTest['results']->whereNull('m16_primary_test_id')->whereNull('m17_secondary_test_id')->first();
                $aBannerResult = !empty($aParentOnly?->tr07_result) ? $aParentOnly->tr07_result : ($arylAminesTest['parent']->tr07_result ?? 'Not detected');
            @endphp
            <div class="aryl-section" style="margin-top: 4px; margin-bottom: 6px;">
                <div style="display: table; width: 100%; border: 1px solid #000; margin-bottom: 2px;">
                    <div style="display: table-cell; width: 75%; padding: 3px 5px; border-right: 1px solid #000; font-weight: bold; text-align: justify; font-size: 7.5pt; line-height: 1.15;">
                        Presence of dyes prohibited by the Government of India under Section 6(2) (D) of the
                        Environment (protection) Act, 1986 (29 of 1986) read with Rule 13 of the Environment
                        (Protection) Rules, 1986 vide Notifications S.O.108(E) dated 30th January, 1990 and
                        S.O. 243(E) dated 26th March 1997.
                    </div>
                    <div style="display: table-cell; width: 25%; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold; font-size: 9pt;">
                        {{ $aBannerResult }}
                    </div>
                </div>

                <div class="divider" style="margin: 1px 0; font-size: 7.5pt; text-align: center; letter-spacing: 1px;">
                    ****************************************************************************************************
                </div>

                <div style="font-size: 8pt; font-weight: bold; margin: 2px 2px;">
                    Details of release of individual aryl amines (mg/kg) on reductive cleavage with sodium di-thionite.
                </div>

                <table class="aryl-side-table" style="width: 100%; border-collapse: collapse; margin: 2px 0;">
                    <thead>
                        <tr style="text-align: center; background-color: #f9f9f9;">
                            <th style="width: 6%; border: 1px solid #000; padding: 2px; font-size: 7.5pt; font-weight: bold;">S.No</th>
                            <th style="width: 32%; border: 1px solid #000; padding: 2px; font-size: 7.5pt; font-weight: bold;">Name of the amines</th>
                            <th style="width: 12%; border: 1px solid #000; padding: 2px; font-size: 7.5pt; font-weight: bold;">Contents</th>
                            <th style="border: none; background: transparent; width: 2%;"></th>
                            <th style="width: 6%; border: 1px solid #000; padding: 2px; font-size: 7.5pt; font-weight: bold;">S.No</th>
                            <th style="width: 32%; border: 1px solid #000; padding: 2px; font-size: 7.5pt; font-weight: bold;">Name of the amines</th>
                            <th style="width: 12%; border: 1px solid #000; padding: 2px; font-size: 7.5pt; font-weight: bold;">Contents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $primaryTests = $arylAminesTest['results']->whereNotNull('m16_primary_test_id')->values();
                            $firstHalf = $primaryTests->take(ceil($primaryTests->count() / 2))->values();
                            $secondHalf = $primaryTests->slice(ceil($primaryTests->count() / 2))->values();
                            $startNumber = $firstHalf->count() + 1;
                            $maxRows = max($firstHalf->count(), $secondHalf->count());
                            $aTestNum = $arylAminesTest['test_number'];
                        @endphp
                        @for ($i = 0; $i < $maxRows; $i++)
                            <tr>
                                @if (isset($firstHalf[$i]))
                                    @php
                                        $r1 = $firstHalf[$i];
                                        $r1Id = $r1->tr07_test_result_id ? (string)$r1->tr07_test_result_id : null;
                                        $r1Key = $aTestNum . '_' . ($r1->m16_primary_test_id ?: '0') . '_0';
                                        $r1Star = ($r1Id && isset($partRemarksStarMap[$r1Id])) ? $partRemarksStarMap[$r1Id] : ($partRemarksStarMap[$r1Key] ?? '');
                                    @endphp
                                    <td style="text-align: center; border: 1px solid #000; padding: 1.5px 2px; font-size: 7pt;">{{ $i + 1 }}</td>
                                    <td style="border: 1px solid #000; padding: 1.5px 3px; font-size: 7pt;">{{ $r1->primaryTest->m16_name ?? '' }}</td>
                                    <td style="text-align: center; border: 1px solid #000; padding: 1.5px 2px; font-size: 7pt;">
                                        {{ $r1->tr07_result ?? 'Not detected' }}
                                        @if ($r1Star)<sup style="font-size: 6pt; font-weight: bold;">{{ $r1Star }}</sup>@endif
                                    </td>
                                @else
                                    <td style="border: 1px solid #000; padding: 1.5px;"></td>
                                    <td style="border: 1px solid #000; padding: 1.5px;"></td>
                                    <td style="border: 1px solid #000; padding: 1.5px;"></td>
                                @endif

                                <td style="border: none; background: transparent;"></td>

                                @if (isset($secondHalf[$i]))
                                    @php
                                        $r2 = $secondHalf[$i];
                                        $r2Id = $r2->tr07_test_result_id ? (string)$r2->tr07_test_result_id : null;
                                        $r2Key = $aTestNum . '_' . ($r2->m16_primary_test_id ?: '0') . '_0';
                                        $r2Star = ($r2Id && isset($partRemarksStarMap[$r2Id])) ? $partRemarksStarMap[$r2Id] : ($partRemarksStarMap[$r2Key] ?? '');
                                    @endphp
                                    <td style="text-align: center; border: 1px solid #000; padding: 1.5px 2px; font-size: 7pt;">{{ $startNumber + $i }}</td>
                                    <td style="border: 1px solid #000; padding: 1.5px 3px; font-size: 7pt;">{{ $r2->primaryTest->m16_name ?? '' }}</td>
                                    <td style="text-align: center; border: 1px solid #000; padding: 1.5px 2px; font-size: 7pt;">
                                        {{ $r2->tr07_result ?? 'Not detected' }}
                                        @if ($r2Star)<sup style="font-size: 6pt; font-weight: bold;">{{ $r2Star }}</sup>@endif
                                    </td>
                                @else
                                    <td style="border: 1px solid #000; padding: 1.5px;"></td>
                                    <td style="border: 1px solid #000; padding: 1.5px;"></td>
                                    <td style="border: 1px solid #000; padding: 1.5px;"></td>
                                @endif
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div class="aryl-footer" style="margin: 2px 4px; font-size: 7pt; text-align: justify; line-height: 1.15;">
                    <p style="margin: 0 0 1.5px 0;">1. As per Section 6(2)(D) of the Environment (Protection) Act 1986 (29 of 1986) read with Rule 13 of the Environment (Protection) Rules, 1986, handling of hazardous dyes which release any one or more of the 22 harmful amines are prohibited. Details of release of harmful amines, if any, is given above.</p>
                    <p style="margin: 0 0 1.5px 0;">2. <strong>Detected:</strong> Any one or more of the banned amine(s) is/are detected under test condition and the sum parameter &gt; 50 mg/kg, and it is concluded that azo dyes had been used for production or modification of the forwarded material, which are prohibited as per the above mentioned Act.</p>
                    <p style="margin: 0 0 1.5px 0;">3. <strong>Not Detected:</strong> Contents of banned amines ≤ 50 mg/kg.</p>
                </div>
            </div>
        @endif

        {{-- ===== STANDARD TEST RESULTS SECTION ===== --}}
        @if ($hasNonArylItems)
            @if (!empty($arylAminesTest))
                <div style="page-break-before: always;"></div>
            @endif
            <table class="test-table" style="width: 100%; border-collapse: collapse; margin-top: 0px;">
                <thead>
                    <tr>
                        <th colspan="3"
                            style="text-align: center; font-weight: bold; font-size: 11px; padding: 4px; border: 1px solid #000; background-color: #fff;">
                            TEST RESULTS
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 8%; border: 1px solid #000; padding: 4px; font-size: 10px;">Sr. No</th>
                        <th style="width: 67%; border: 1px solid #000; padding: 4px; font-size: 10px;">Test / Parameter</th>
                        <th style="width: 25%; border: 1px solid #000; padding: 4px; font-size: 10px;">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @php $testRowCounter = 1; @endphp
                    @foreach ($orderedItems as $item)
                        @php
                            $testNumber = $item['test_number'] ?? null;
                            if (!$testNumber) continue;
                            $testResults = $groupedResults[$testNumber] ?? collect();
                            $parentResult = $testResults->first();
                            $sampleTest = $sample->sampleTests->firstWhere('m12_test_number', $testNumber);
                            $test = $parentResult ? $parentResult->test : ($sampleTest ? $sampleTest->test : null);
                            $standardQualifier = $parentResult?->sampleTest?->tr05_standard_qualifier ?? $sampleTest?->tr05_standard_qualifier ?? '';
                            $testName = $test ? $test->m12_name : 'Test #' . $testNumber;
                            $isAryl = (strpos(strtolower($testName), 'banned amines') !== false || strpos(strtolower($testName), 'aryl amine') !== false || strpos(strtolower($testName), 'azo') !== false);
                            $isAryl = (strpos(strtolower($testName), 'banned amines') !== false || strpos(strtolower($testName), 'aryl amine') !== false || strpos(strtolower($testName), 'azo') !== false || strpos(strtolower($testName), 'amine') !== false);
                        @endphp
                        @if (!$isAryl)
                            @php
                                $testStandard = $parentResult && $parentResult->sampleTest && $parentResult->sampleTest->standard
                                    ? $parentResult->sampleTest->standard->m15_standard_name
                                    : ($sampleTest && $sampleTest->standard_method 
                                        ? $sampleTest->standard_method 
                                        : ($test && $test->standard ? $test->standard->m15_standard_name : null));
                                $parentOnly = $testResults->whereNull('m16_primary_test_id')->whereNull('m17_secondary_test_id')->first();
                                $customFieldsForTest = $groupedCustomFields[$testNumber] ?? collect();
                            @endphp
                            {{-- Parent Test Row --}}
                            <tr>
                                <td style="text-align: center; border: 1px solid #000; padding: 4px; font-size: 10px;">
                                    {{ $testRowCounter++ }}.
                                </td>
                                <td style="border: 1px solid #000; padding: 4px; font-size: 10px; font-weight: bold;">
                                    {{ $testName }}
                                    @if ($testStandard)
                                        <span style="font-weight: normal; color: #333;"> - ({{ $testStandard }}{{ !empty($standardQualifier) ? ' ' . $standardQualifier : '' }})</span>
                                    @endif
                                </td>
                                <td style="text-align: center; border: 1px solid #000; padding: 4px; font-size: 10px;">
                                    @if ($parentOnly && !empty($parentOnly->tr07_result))
                                        @php
                                            $pResId = $parentOnly->tr07_test_result_id ? (string)$parentOnly->tr07_test_result_id : null;
                                            $pCompKey = $testNumber . '_0_0';
                                            $pStar = ($pResId && isset($partRemarksStarMap[$pResId])) ? $partRemarksStarMap[$pResId] : ($partRemarksStarMap[$pCompKey] ?? '');
                                        @endphp
                                        {{ $parentOnly->tr07_result }}
                                        @if ($pStar)
                                            <sup style="font-size: 8px; font-weight: bold;">{{ $pStar }}</sup>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            {{-- Primary & Secondary Rows --}}
                            @php
                                $subResults = $testResults->filter(fn($r) => !empty($r->m16_primary_test_id) || !empty($r->m17_secondary_test_id));
                                $primaryGroups = $subResults->groupBy('m16_primary_test_id');
                                $renderedCustomIds = [];
                            @endphp
                            @foreach ($primaryGroups as $pId => $pRows)
                                @php
                                    $primRes = $pRows->first();
                                    $primName = $primRes->primaryTest?->m16_name ?? '';
                                    $primOnly = $pRows->whereNull('m17_secondary_test_id')->first();
                                @endphp
                                @if (!empty($primName))
                                    <tr>
                                        <td style="border: 1px solid #000;"></td>
                                        <td style="text-align: right; border: 1px solid #000; font-size: 10px; font-weight: 500;">
                                            {{ $primName }}
                                        </td>
                                        <td style="text-align: center; border: 1px solid #000; padding: 4px; font-size: 10px;">
                                            @if ($primOnly && !empty($primOnly->tr07_result))
                                                @php
                                                    $prResId = $primOnly->tr07_test_result_id ? (string)$primOnly->tr07_test_result_id : null;
                                                    $prCompKey = $testNumber . '_' . $pId . '_0';
                                                    $prStar = ($prResId && isset($partRemarksStarMap[$prResId])) ? $partRemarksStarMap[$prResId] : ($partRemarksStarMap[$prCompKey] ?? '');
                                                @endphp
                                                {{ $primOnly->tr07_result }}
                                                @if ($prStar)
                                                    <sup style="font-size: 8px; font-weight: bold;">{{ $prStar }}</sup>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($pRows->whereNotNull('m17_secondary_test_id') as $sRes)
                                    @php
                                        $secName = $sRes->secondaryTest?->m17_name ?? '';
                                        $sResId = $sRes->tr07_test_result_id ? (string)$sRes->tr07_test_result_id : null;
                                        $sCompKey = $testNumber . '_' . ($sRes->m16_primary_test_id ?: '0') . '_' . $sRes->m17_secondary_test_id;
                                        $sStar = ($sResId && isset($partRemarksStarMap[$sResId])) ? $partRemarksStarMap[$sResId] : ($partRemarksStarMap[$sCompKey] ?? '');
                                    @endphp
                                    <tr>
                                        <td style="border: 1px solid #000;"></td>
                                        <td style="text-align: right; border: 1px solid #000; font-size: 10px; color: #444;">
                                            {{ $secName }}
                                        </td>
                                        <td style="text-align: center; border: 1px solid #000; padding: 4px; font-size: 10px;">
                                            {{ $sRes->tr07_result }}
                                            @if ($sStar)
                                                <sup style="font-size: 8px; font-weight: bold;">{{ $sStar }}</sup>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Custom fields under this primary test --}}
                                @foreach ($customFieldsForTest->where('m16_primary_test_id', $pId) as $pcField)
                                    @php $renderedCustomIds[] = $pcField->tr08_custom_field_id; @endphp
                                    <tr>
                                        <td style="border: 1px solid #000;"></td>
                                        <td style="text-align: right; border: 1px solid #000; font-size: 10px; font-weight: 500;">
                                            {{ $pcField->tr08_field_name }}
                                        </td>
                                        <td style="text-align: center; border: 1px solid #000; padding: 4px; font-size: 10px; font-weight: bold;">
                                            {{ $pcField->tr08_field_value }}
                                            @if ($pcField->tr08_field_unit)
                                                ({{ $pcField->tr08_field_unit }})
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            {{-- Main/Remaining Custom Fields for this test --}}
                            @foreach ($customFieldsForTest->reject(fn($cf) => in_array($cf->tr08_custom_field_id, $renderedCustomIds)) as $cField)
                                <tr>
                                    <td style="border: 1px solid #000;"></td>
                                    <td style="text-align: right; border: 1px solid #000; font-size: 10px; font-weight: 500;">
                                        {{ $cField->tr08_field_name }}
                                    </td>
                                    <td style="text-align: center; border: 1px solid #000; padding: 4px; font-size: 10px; font-weight: bold;">
                                        {{ $cField->tr08_field_value }}
                                        @if ($cField->tr08_field_unit)
                                            ({{ $cField->tr08_field_unit }})
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ===== REMARKS SECTION (on last page of each part) ===== --}}
        @if (count($partRemarksList) > 0)
            <div style="border: 1px solid #000; padding: 4px 6px; margin-top: 6px; margin-bottom: 8px; background: #fff; page-break-inside: avoid;">
                <div style="font-weight: bold; font-size: 9.5pt; text-decoration: underline; margin-bottom: 2pt;">Remarks:</div>
                @foreach ($partRemarksList as $rem)
                    <div style="font-size: 8pt; line-height: 1.25; margin-bottom: 1.5pt; color: #111;">
                        <strong>{{ $rem['stars'] }}</strong> {{ $rem['text'] }}
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ===== SIGNATURE & QR BLOCK ===== --}}
        <div class="signature-qr-container">
            <table class="signature-qr-table">
                <tr>
<td style="width: 50%; vertical-align: bottom;">

    @if ($loop->last)
        @php
            $qrTrackerId = $sample->tr04_tracker_id
                ?? $sample->tr04_reference_id;

            $qrUrl = $qrTrackerId
                ? route('track_sample', ['trackerId' => $qrTrackerId])
                : url('/');

            $qrCodeSvg = null;

            try {
                $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)
                    ->margin(0)
                    ->generate($qrUrl);
            } catch (\Exception $e) {
                $qrCodeSvg = null;
            }
        @endphp

        @if ($qrCodeSvg)
            <div style="display: inline-block; text-align: left;">
                <img
                    src="data:image/svg+xml;base64,{{ base64_encode($qrCodeSvg) }}"
                    style="width: 70px; height: 70px; display: block;
                           border: none; margin-left: 30px;"
                    alt="QR Code">
            </div>
        @endif
    @endif

</td>
                    <td style="width: 50%; vertical-align: bottom; text-align: right; padding-right: 30px !important;">
                        <div style="font-weight: bold; font-size: 14px; color: #000;">
                            {{ $report->generatedBy->m06_name ?? ($sample->ro->m04_name ?? 'Authorized Signatory') }}
                        </div>
                        <div style="font-size: 12px; color: #333; margin-top: 2px;">Authorized Signatory</div>
                    </td>
                </tr>
            </table>

            @if ($loop->last)
                <div class="end-of-report">----------End of Report ----------</div>
            @endif
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
        $jsOnLetterhead = $onLetterhead ? 1 : 0;

        $jsPartAccredited = [];
        foreach ($reportParts as $pIdx => $p) {
            $hasAcc = !empty($p['has_accredited_tests']);
            $jsPartAccredited[$pIdx] = $hasAcc ? 1 : 0;
        }

        $jsNablLogoPath = str_replace('\\', '/', base_path('backAssets/images/accrediation.png'));
        $certNo = trim($sample->ro?->certificate_no ?? $sample->ro?->m04_nabl_cert_no ?? '');
        if (empty($certNo)) {
            $certNo = 'TC-11915';
        }

        $jsPartCodes = json_encode(
            array_values(
                array_map(
                    function ($p, $idx) {
                        $code = $p['part_code'] ?? ($idx === 0 && !empty($p['has_accredited_tests']) ? 'A' : '');
                        return !empty($code) ? ' ' . $code : '';
                    },
                    $reportParts,
                    array_keys($reportParts),
                ),
            ),
        );
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

                $partCodes = {!! $jsPartCodes !!};
                $partSuffix = $partCodes[$currentPartIndex] ?? "";
                $fullReportNo = "{!! $jsReportNo !!}" . $partSuffix;

                $partAccredited = {!! json_encode($jsPartAccredited) !!};
                $onLh = {!! $jsOnLetterhead !!};
                $hasAcc = $partAccredited[$currentPartIndex] ?? 0;

                // 1. DYNAMIC NABL LOGO RENDERING
                // Only drawn on accredited part pages on PLAIN paper (suppressed on pre-printed letterhead)!
                if (!$onLh && $hasAcc) {
                    $nablImg = "{!! $jsNablLogoPath !!}";
                    if (file_exists($nablImg)) {
                        $pdf->image($nablImg, 92, 28, 50, 55);
                        $cNo = "{!! $certNo !!}";
                        $cWidth = $fontMetrics->get_text_width($cNo, $bold, 7.5);
                        $pdf->text(117 - ($cWidth / 2), 83, $cNo, $bold, 7.5, [0,0,0]);
                    }
                }

                // 1b. DYNAMIC FORMAT NUMBER RENDERING
                // On plain paper: when NABL logo comes, Format No. 04/26/23; without NABL logo, Format No. 04/26B/03
                if (!$onLh) {
                    $fmtText = $hasAcc ? "Format No. 04/26/23" : "Format No. 04/26B/03";
                    $fmtWidth = $fontMetrics->get_text_width($fmtText, $bold, 10.3);
                    $pdf->text(571.28 - $fmtWidth, 149.3, $fmtText, $bold, 9.5, [0,0,0]);
                }

                // 2. CONTINUATION BOX (on all pages EXCEPT Part 1 Page 1)
                if (!($currentPartIndex == 0 && $partCurrentPage == 1)) {
                    // Outer table border (y: 170 to 237, height 67)
                    $pdf->rectangle(24, 170, 547, 67, [0,0,0], 0.8);

                    // Row dividers
                    $pdf->line(24, 191, 571, 191, [0,0,0], 0.8);
                    $pdf->line(24, 206, 571, 206, [0,0,0], 0.8);
                    $pdf->line(24, 221, 571, 221, [0,0,0], 0.8);

                    // Vertical column divider for rows 2, 3, 4
                    $pdf->line(230, 191, 230, 237, [0,0,0], 0.8);

                    // Row 1: Report No, Continued, Date
                    $pdf->text(35, 174, "Test Report No : " . $fullReportNo, $bold, 9, [0,0,0]);
                    $pdf->text(310, 174, "Continued ...........", $boldItalic, 9, [0,0,0]);
                    $pdf->text(465, 174, "Date : {!! $jsReportDate !!}", $bold, 9, [0,0,0]);

                    // Row 2: Sample forwarding letter
                    $lbl2 = {!! $jsIsCustom !!} ? "Sample forwarding letter No. & date :" : "Sample forwarding letter No. & date";
                    $val2 = {!! $jsIsCustom !!} ? "Test Memo No. {!! $jsReference !!} dated {!! $jsReferenceDateCustom !!}" : "{!! $jsReference !!} dtd. {!! $jsReferenceDate !!}";
                    $pdf->text(35, 192, $lbl2, $font, 9, [0,0,0]);
                    $pdf->text(235, 192, $val2, $font, 9, [0,0,0]);

                    // Row 3: Buyers Name
                    $lbl3 = {!! $jsIsCustom !!} ? "Buyers Name & address (Optional) :" : "Buyers Name & address";
                    $pdf->text(35, 207, $lbl3, $font, 9, [0,0,0]);
                    $pdf->text(235, 207, "{!! $jsBuyer !!}", $font, 9, [0,0,0]);

                    // Row 4: Customer Sample No
                    $lbl4 = {!! $jsIsCustom !!} ? "Customer Sample No. :" : "Customer Sample No :";
                    $val4 = {!! $jsIsCustom !!} ? "BE No. {!! $jsBeNo !!}" : "{!! $jsBeNo !!}";
                    $pdf->text(35, 222, $lbl4, $font, 9, [0,0,0]);
                    $pdf->text(235, 222, $val4, $font, 8.5, [0,0,0]);
                }

                // 3. PAGE NUMBER (bottom right corner)
                $pageText  = "Page " . $partCurrentPage . " of " . $partTotalPages;
                $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                $pdf->text($pageWidth - 90, $pageHeight - 112, $pageText, $font, $size, [0,0,0]);
            ');
        }
    </script>

</body>

</html>