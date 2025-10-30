<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Final Test Report - {{ $report->tr09_version_number }}</title>
    <style>
        @page {
            margin: 80px 30px 100px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

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

        .table-primary {
            background-color: #d9eaf7;
            font-weight: bold;
        }

        .table-secondary {
            background-color: #f2f2f2;
        }

        .divider {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            font-size: 10px;
        }

        small {
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>
    <header>
        <h4>TEST REPORT</h4>
    </header>

    <div class="container">
        {{-- Report / Sample info --}}
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
                    <td colspan="2">Test Memo No. {{ $meta['reference'] }} dated {{ $meta['reference_date'] }}</td>
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

        <h5>TEST RESULTS</h5>

        {{-- Sample description --}}
        <table>
            <tbody>
                <tr>
                    <th style="width:30%">Sample Characteristics</th>
                    <td>{{ $meta['sample_characteristics'] }}</td>
                </tr>
                <tr>
                    <th>Date of Performance of Test (s)</th>
                    <td>{{ $meta['date'] }} to {{ $meta['test_performance_date'] }}</td>
                </tr>
                <tr>
                    <th>Laboratory Sample No.</th>
                    <td>{{ $meta['report_no'] }}</td>
                </tr>
            </tbody>
        </table>

        <div class="divider">
            ****************************************************************************************************
        </div>

        {{-- Parent & Child Test Results --}}
        <table>
            <thead>
                <tr class="text-center">
                    <th style="width:6%;">#</th>
                    <th>Test / Parameter</th>
                    <th style="width:28%;">Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedResults as $testNumber => $results)
                    @php $parent = $results->first(); @endphp
                    <tr class="table-primary">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $parent->test?->m12_name ?? 'N/A' }}
                            @if ($parent->test?->standard?->m15_method)
                                <small>({{ $parent->test->standard->m15_method }})</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($results->first()->m22_manuscript_id)
                                _
                            @else
                                {{ $parent->tr07_result ?? '-' }}
                            @endif
                        </td>
                    </tr>

                    @foreach ($results as $mkey => $child)
                        @if ($child->m22_manuscript_id)
                            <tr class="table-secondary">
                                <td class="text-center">{{ $loop->parent->iteration }}.{{ $mkey + 1 }}</td>
                                <td class="ps-4">🗎 {{ $child->manuscript?->m22_name ?? 'Manuscript' }}</td>
                                <td class="text-center">{{ $child->tr07_result ?? '-' }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                if ($PAGE_NUM > 0) {
                    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                    $boldFont = $fontMetrics->get_font("DejaVu Sans", "bold");
                    $pageWidth = $pdf->get_width();
                    $pageHeight = $pdf->get_height();
                    
                    // Page number (centered)
                    $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                    $size = 10;
                    $textWidth = $fontMetrics->get_text_width($pageText, $font, $size);
                    $x = ($pageWidth - $textWidth) / 2;
                    $y = $pageHeight - 50;
                    $pdf->text($x, $y, $pageText, $font, $size, array(0, 0, 0));
                    
                    // Signature block (right-aligned)
                    $signerName = "' . addslashes($report->generator->m06_name ?? 'Authorized Signatory') . '";
                    $signerTitle = "Authorized Signatory";
                    
                    $nameWidth = $fontMetrics->get_text_width($signerName, $boldFont, $size);
                    $titleWidth = $fontMetrics->get_text_width($signerTitle, $font, 9);
                    
                    $xName = $pageWidth - $nameWidth - 30;
                    $xTitle = $pageWidth - $titleWidth - 30;
                    
                    $yName = $pageHeight - 75;
                    $yTitle = $pageHeight - 60;
                    
                    $pdf->text($xName, $yName, $signerName, $boldFont, $size, array(0, 0, 0));
                    $pdf->text($xTitle, $yTitle, $signerTitle, $font, 9, array(0.33, 0.33, 0.33));
                }
            ');
        }
    </script>

</body>

</html>
