<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manuscript Datasheet - {{ $registration->tr04_reference_id }}</title>
    <style>
        @page {
            margin-top: 65px;
            margin-bottom: 45px;
            margin-left: 25px;
            margin-right: 25px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9.5px;
            color: #000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        /* ===== REPEATING HEADER ===== */
        header {
            position: fixed;
            top: -55px;
            left: 0;
            right: 0;
            height: 52px;
            text-align: center;
        }

        .header-doc-code {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header-title-main {
            font-size: 11.5px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .header-title-ro {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .header-title-sub {
            font-size: 9.5px;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ===== COMMON TABLE STYLES ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            vertical-align: top;
        }

        /* ===== INFO BLOCK TABLE ===== */
        .info-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 0px;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 2.5px 5px;
            font-size: 9px;
            line-height: 1.25;
        }

        .info-label {
            font-weight: bold;
            color: #111;
        }

        .info-banner {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            letter-spacing: 1.5px;
            padding: 3px 0 !important;
            border-top: 1.5px solid #000 !important;
            border-bottom: 1.5px solid #000 !important;
            background-color: #f7f7f7;
        }

        /* ===== MAIN TEST RESULTS TABLE ===== */
        .main-test-table {
            width: 100%;
            border-collapse: collapse;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .main-test-table > thead > tr > th {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 9px;
            font-weight: bold;
            background-color: #fafafa;
        }

        .main-test-table > tbody > tr {
            page-break-inside: avoid !important;
        }

        .main-test-table > tbody > tr > td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 9px;
        }

        .test-title {
            font-weight: bold;
            font-size: 9.5px;
            margin-bottom: 3px;
            color: #000;
        }

        .test-method {
            font-weight: normal;
            color: #222;
        }

        /* ===== SUMMERNOTE MANUSCRIPT CONTENT ===== */
        .summernote-content {
            font-size: 9px;
            line-height: 1.25;
            margin-top: 3px;
            padding-left: 12px;
        }

        .summernote-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 4px 0 !important;
        }

        .summernote-content table td,
        .summernote-content table th {
            border: 1px solid #444 !important;
            padding: 2px 4px !important;
            font-size: 8.5px !important;
            vertical-align: middle !important;
        }

        .summernote-content p {
            margin: 2px 0 !important;
        }

        .summernote-content ul,
        .summernote-content ol {
            margin: 2px 0 2px 16px !important;
            padding: 0 !important;
        }

        .summernote-content img {
            max-width: 95% !important;
            height: auto !important;
            margin: 3px 0 !important;
        }

        /* Hide inputs if any exist in raw summernote html */
        .summernote-content input[type="text"],
        .summernote-content input[type="number"],
        .summernote-content button {
            display: none !important;
        }

        /* ===== BLANK ENTRY FOR TESTS WITHOUT MANUSCRIPT ===== */
        .blank-entry-block {
            margin-top: 8px;
            margin-bottom: 6px;
            padding-left: 14px;
        }

        .blank-entry-line {
            height: 20px;
            border-bottom: 1px dotted #888;
            margin-bottom: 6px;
            width: 96%;
        }

        /* Right column for observations/calculations */
        .calc-column {
            width: 22%;
            text-align: right;
            vertical-align: top;
            padding: 4px 5px !important;
        }

        .calc-unit {
            font-weight: bold;
            font-size: 8.5px;
            text-align: right;
            color: #111;
            margin-bottom: 4px;
        }

        .calc-blank-space {
            min-height: 35px;
        }
    </style>
</head>
<body>

    <!-- REPEATING HEADER ON EVERY PAGE -->
    <header>
        <div class="header-doc-code">04/01/01/03</div>
        <div class="header-title-main">TEXTILES COMMITTEE LABORATORIES</div>
        <div class="header-title-ro">{{ $roName }}</div>
        <div class="header-title-sub">MANUSCRIPT / DATASHEET</div>
    </header>

    <!-- PAGE 1: SAMPLE INFORMATION BLOCK -->
    <table class="info-table">
        <tr>
            <td style="width: 24%;" class="info-label">Test Report No:</td>
            <td style="width: 44%; font-weight: bold;">{{ $registration->tr04_reference_id }}</td>
            <td style="width: 8%;" class="info-label">Date:</td>
            <td style="width: 24%;">{{ $testDate ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Date of allotment of sample:</td>
            <td colspan="3">{{ $allotmentDate ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Sample Characteristics:</td>
            <td colspan="3">{{ $sampleCharacteristics }}</td>
        </tr>
        <tr>
            <td class="info-label">Date of Performance of Tests:</td>
            <td colspan="3">{{ $performanceDate ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">No. of samples:</td>
            <td colspan="3">{{ $numberOfSamples }}</td>
        </tr>
        <tr>
            <td class="info-label">QAO/JQAO/analyst:</td>
            <td colspan="3">{{ $analystName ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Technical Manager:</td>
            <td colspan="3">{{ $managerName ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="4" class="info-banner">
                TEST RESULTS
            </td>
        </tr>
        <tr>
            <td class="info-label">Sample Mark:</td>
            <td colspan="3">{{ $sampleMark }}</td>
        </tr>
        <tr>
            <td class="info-label">Laboratory Sample No.:</td>
            <td colspan="3" style="font-weight: bold;">{{ $registration->tr04_reference_id }}</td>
        </tr>
    </table>

    <!-- MAIN TEST RESULTS TABLE -->
    <table class="main-test-table">
        <thead>
            <tr>
                <th style="width: 78%; text-align: left;">
                    <span style="display: inline-block; width: 32px;">SNo.</span> Test Name
                </th>
                <th style="width: 22%; text-align: center;">
                    &nbsp;
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($testsList as $index => $item)
            <tr>
                <!-- Test Name & Manuscript Content / Calculation Area -->
                <td style="width: 78%; vertical-align: top;">
                    <div class="test-title">
                        <span style="display: inline-block; width: 22px;">{{ $index + 1 }}.</span>
                        {{ $item['test_name'] }}
                        @if(!empty($item['standard_method']))
                            <span class="test-method">({{ $item['standard_method'] }})</span>
                        @endif
                    </div>

                    @if(!empty($item['manuscript_content']))
                        <div class="summernote-content">
                            {!! $item['manuscript_content'] !!}
                        </div>
                    @else
                        <!-- Blank line / space for manual handwritten entry by analyst -->
                        <div class="blank-entry-block">
                            <div class="blank-entry-line"></div>
                            <div class="blank-entry-line"></div>
                        </div>
                    @endif
                </td>

                <!-- Right Column for Manual Calculation and Unit -->
                <td class="calc-column">
                    @if(!empty($item['units']))
                        <div class="calc-unit">{{ $item['units'] }}</div>
                    @endif
                    <div class="calc-blank-space">&nbsp;</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center; padding: 20px; color: #666;">
                    No tests found for this sample registration.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- REPEATING FOOTER WITH SIGNATURES & DYNAMIC PAGE NUMBERING -->
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                $size = 8.5;
                $pageWidth = $pdf->get_width();
                $pageHeight = $pdf->get_height();
                $y = $pageHeight - 25;

                // Left: Signature of QAO/JQAO
                $pdf->text(25, $y, "Signature of QAO/JQAO", $font, $size);

                // Center: Page X of Y
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                $pdf->text(($pageWidth - $textWidth) / 2, $y, $pageText, $font, $size);

                // Right: Signature of Technical Manager
                $tmText = "Signature of Technical Manager";
                $tmWidth = $fontMetrics->get_text_width($tmText, $font, $size);
                $pdf->text($pageWidth - 25 - $tmWidth, $y, $tmText, $font, $size);
            ');
        }
    </script>

</body>
</html>
