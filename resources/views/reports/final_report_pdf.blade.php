<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <title>Report {{ $meta['report_no'] }} - v{{ $report->tr09_version_number }}</title>

    <style>
        @page {
            margin: 120px 30px 100px 30px;
        }

        @page :first {
            margin-top: 50px;
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

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
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
            margin-bottom: 25px;
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
    </style>
</head>

<body>

    {{-- ===== FIRST PAGE CONTENT ===== --}}
    <div class="first-page">
        {{-- ===== FIRST PAGE DESCRIPTIVE HEADER ===== --}}
        <div class="first-page-header">
            <h3>TEST REPORT</h3>
            <table>
                <tbody>
                    <tr>
                        <th colspan="2">Test Report No : {{ $meta['report_no'] }}</th>
                        <th class="text-end">Date : {{ $meta['date'] }}</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Name & Address of Customer</th>
                        <td colspan="2">{{ $meta['customer_name'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">{{ $meta['customer_address'] }}</td>
                    </tr>
                    <tr>
                        <th>Sample forwarding letter No. & date</th>
                        <td colspan="2">Test Memo No. {{ $meta['reference'] }} dated {{ $meta['reference_date'] }}
                        </td>
                    </tr>
                    <tr>
                        <th>Buyers Name & address (Optional)</th>
                        <td colspan="2">{{ $meta['buyer'] }}</td>
                    </tr>
                    <tr>
                        <th>Customer Sample No.</th>
                        <th colspan="2" class="text-center">Sample Description</th>
                    </tr>
                    <tr>
                        <td>BE No. {{ $meta['be_no'] }} dtd. {{ $meta['date'] }}</td>
                        <td colspan="2" class="text-center">{{ $meta['sample_description'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ===== MAIN BODY ===== --}}
        <div class="container">
            <h4>TEST RESULTS</h4>

            {{-- Sample Description Section --}}
            <table>
                <tbody>
                    <tr>
                        <th style="width:30%">Sample Characteristics</th>
                        <td>{{ $meta['sample_characteristics'] }}</td>
                    </tr>
                    <tr>
                        <th>Date of Performance of Test(s)</th>
                        <td>{{ $meta['date'] }} to {{ $meta['test_performance_date'] }}</td>
                    </tr>
                    <tr>
                        <th>Laboratory Sample No.</th>
                        <td>{{ $meta['report_no'] }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- ==== Aryl Amines Section (FIRST PAGE ONLY) ==== --}}
            @php
                $arylAminesTest = null;
                foreach ($orderedItems as $item) {
                    if ($item['type'] === 'test') {
                        $results = $groupedResults[$item['test_number']] ?? collect();
                        $parent = $results->first();
                        if ($parent) {
                            $testName = $parent->test->m12_name ?? '';
                            if (strpos(strtolower($testName), 'aryl amine') !== false) {
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
                        {{-- <div style="float:right; font-weight:bold;">Not detected</div> --}}
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
                            {{-- <tr>
                                <td colspan="7" style="text-align:right; font-weight:bold; font-size:9px;">
                                    Note: Sum parameters | Nil
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>

                    <div class="aryl-footer">
                        <p>1. As per Section 6(2)(D) of the Environment (Protection) Act 1986 (29 of 1986) read with
                            Rule 13
                            of the Environment (Protection) Rules, 1986, handling of hazardous dyes which release any
                            one or
                            more of the 22 harmful amines are prohibited. Details of release of harmful amines, if any,
                            is
                            given above.</p>
                        <p>2. <strong>Detected:</strong> Any one or more of the banned amine(s) is/are detected under
                            test
                            condition and the sum parameter > 50 mg/kg, and it is concluded that azo dyes had been used
                            for
                            production or modification of the forwarded material, which are prohibited as per the above
                            mentioned Act.</p>
                        <p>3. <strong>Not Detected:</strong> Contents of banned amines ≤ 50 mg/kg...</p>
                    </div>
                </div>
            @endif
            {{-- ===== Main Test Results (Continuous Sub) ===== --}}
            <table>
                <thead>
                    <tr class="text-center">
                        <th style="width:6%;">#</th>
                        <th>Test / Parameter</th>
                        <th style="width:28%;">Result</th>
                    </tr>
                </thead>
                <tbody>
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
                                    {{-- === Main Test Header === --}}
                                    <tr class="table-primary">
                                        <td class="text-center">{{ $counter }}</td>
                                        <td>
                                            <strong>{{ $testName ?: 'Test #' . $item['test_number'] }}</strong>&nbsp; - &nbsp;
                                            @if ($parent->test->standard->m15_method ?? false)
                                                <small>({{ $parent->test->standard->m15_method }})</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $hasPrimary = $results
                                                    ->whereNotNull('m16_primary_test_id')
                                                    ->isNotEmpty();
                                            @endphp
                                            @if (!$hasPrimary)
                                                {{ $parent->tr07_result ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>

                                    @php $subCounter = 1; @endphp

                                    {{-- === Primary / Secondary Handling === --}}
                                    @if ($hasPrimary)
                                        @foreach ($results->groupBy('m16_primary_test_id') as $primaryId => $primaryResults)
                                            @php
                                                $primaryTest = $primaryResults->first()->primaryTest;
                                                $hasSecondary = $primaryResults
                                                    ->whereNotNull('m17_secondary_test_id')
                                                    ->isNotEmpty();
                                            @endphp

                                            @if ($hasSecondary)
                                                {{-- Secondary Tests (skip primary label) --}}
                                                @foreach ($primaryResults->whereNotNull('m17_secondary_test_id') as $secondary)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $counter }}.{{ $subCounter++ }}</td>
                                                        <td class="ps-4">
                                                            <em>{{ $secondary->secondaryTest->m17_name ?? 'Secondary Parameter' }}</em>
                                                        </td>
                                                        <td class="text-center">{{ $secondary->tr07_result ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                {{-- Primary Tests (no secondary) --}}
                                                <tr class="table-secondary">
                                                    <td class="text-center">{{ $counter }}.{{ $subCounter++ }}
                                                    </td>
                                                    <td class="ps-4">
                                                        <em>{{ $primaryTest->m16_name ?? 'Primary Parameter' }}</em>
                                                        @if ($primaryTest->m16_requirement)
                                                            <br><small>Requirement:
                                                                {{ $primaryTest->m16_requirement }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $primaryResults->first()->tr07_result ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif

                                    {{-- === Custom Fields (continuous numbering) === --}}
                                    @php
                                        $customFields = $groupedCustomFields[$item['test_number']] ?? collect();
                                    @endphp

                                    @if ($customFields->isNotEmpty())
                                        @foreach ($customFields as $custom)
                                            <tr class="table-secondary">
                                                <td class="text-center">{{ $counter }}.{{ $subCounter++ }}</td>
                                                <td class="ps-4">{{ $custom->tr08_field_name }}</td>
                                                <td class="text-center">
                                                    {{ $custom->tr08_field_value }}
                                                    @if ($custom->tr08_field_unit)
                                                        ({{ $custom->tr08_field_unit }})
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @php $counter++; @endphp
                                @else
                                    {{-- Skip Aryl Amines --}}
                                    @php $counter++; @endphp
                                @endif
                            @endif
                        @endif
                    @endforeach
                </tbody>
            </table>


        </div>
    </div>

    {{-- ===== Footer Page Script ===== --}}
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                $bold = $fontMetrics->get_font("DejaVu Sans", "bold");
                $size = 10;
                $pageWidth = $pdf->get_width();
                $pageHeight = $pdf->get_height();

                // --- Show header only on pages > 1 ---
                if ($PAGE_NUM > 1) {
                    // Header content - properly aligned
                    
                    $pdf->text(30, 50, "Report No: {{ $meta["report_no"] }}", $font, 9, [0,0,0]);
                    $pdf->text(400, 50, "Date: {{ $meta["date"] }}", $font, 9, [0,0,0]);
                    
                    $pdf->text(30, 65, "Customer: {{ $meta["customer_name"] }}", $font, 9, [0,0,0]);
                    
                    // Draw header border
                    $pdf->line(25, 90, $pageWidth - 25, 90, [0,0,0], 1);
                }

                // --- Footer for all pages ---
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                $pdf->text(($pageWidth - $textWidth) / 2, $pageHeight - 40, $pageText, $font, $size, [0,0,0]);

                // Add signatory on ALL pages including first
                $pdf->text($pageWidth - 150, $pageHeight - 55, "Authorized Signatory", $font, 9, [0.3,0.3,0.3]);
                $signerName = "{{ addslashes($report->generator->m06_name ?? "Lab Manager JNPT") }}";
                $pdf->text($pageWidth - 150, $pageHeight - 70, $signerName, $bold, 10, [0,0,0]);
            ');
        }
    </script>

</body>

</html>
