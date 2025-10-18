<!DOCTYPE html>
<html lang="en" class="js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Combined Invoice | LIMS</title>

    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">

    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 0;
            color: #333;
        }

        .invoice-container {
            background: #fff;
            max-width: 900px;
            margin: auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.06);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .invoice-header .logo img {
            max-height: 60px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            margin: 0;
            font-size: 24px;
        }

        .invoice-title span {
            font-size: 14px;
            color: #777;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
            font-size: 16px;
        }

        .details-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .details-table th, .details-table td {
            border: 1px solid #eee;
            padding: 12px;
            font-size: 14px;
            text-align: left;
        }

        .details-table th {
            background-color: #f3f3f3;
            font-weight: bold;
        }

        .totals-table td {
            padding: 10px;
            font-size: 15px;
        }

        .totals-table tr td:first-child {
            text-align: right;
        }

        .totals-table tr.total td {
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .customer-info {
            margin-bottom: 30px;
        }

        .customer-info ul {
            list-style: none;
            padding: 0;
            font-size: 14px;
        }

        .customer-info li {
            margin-bottom: 5px;
        }

        @media print {
            body {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="logo">
                <img src="{{ asset('backAssets/images/logo.png') }}" alt="Company Logo">
            </div>
            <div class="invoice-title">
                <h2>COMBINED INVOICE</h2>
                <span>Date: {{ date('d M, Y') }}</span>
            </div>
        </div>

        @php $firstsample = $samples->first(); @endphp

        <div class="customer-info">
            <div class="section-title">Billed To:</div>
            <ul>
                <li><strong>{{ $firstsample['customer']['m07_name'] }}</strong></li>
                <li>Contact Person: {{ $firstsample['customer']['m07_contact_person'] }}</li>
                <li>GST: {{ $firstsample['customer']['m07_gst'] ?? 'N/A' }}</li>
                <li>Location ID: {{ $firstsample['m08_customer_location_id'] ?? 'N/A' }}</li>
                <li>Payment By: {{ $firstsample['tr04_payment_by'] ?? 'N/A' }}</li>
            </ul>
        </div>

        <div class="section-title">Selected Sample Details</div>
        <table class="details-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reference ID</th>
                    <th>Lab Sample</th>
                    <th>Description</th>
                    <th>Total Charges (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($samples as $index => $sample)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>#{{ $sample->tr04_reference_id }}</td>
                        <td>{{ $sample->labSample['m14_name'] ?? 'N/A' }}</td>
                        <td>{{ $sample->tr04_sample_description ?? 'N/A' }}</td>
                        <td>{{ number_format($sample->tr04_total_charges, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr class="total">
                <td>Total Amount:</td>
                <td>₹{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
