<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sample Invoice Details Print | LIMS</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <script src="{{ asset('backAssets/js/jquery.js') }}"></script>

    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .row,
            .col-md-4,
            .col-md-6,
            .col-md-12 {
                margin: 0 !important;
                padding: 0 15px !important;
                display: block !important;
            }

            .invoice-head {
                display: flex !important;
                justify-content: space-between !important;
                margin-bottom: 1rem;
            }

            .invoice-contact,
            .invoice-desc {
                flex: 1;
            }

            .badge {
                border: 1px solid #ccc !important;
                color: #000 !important;
            }

            .badge.bg-success {
                background-color: #d4edda !important;
                color: #155724 !important;
            }

            .badge.bg-warning {
                background-color: #fff3cd !important;
                color: #856404 !important;
            }

            .badge.bg-primary {
                background-color: #d1ecf1 !important;
                color: #0c5460 !important;
            }

            .page-break {
                page-break-before: always !important;
            }

            .icon {
                font-size: 14px !important;
                margin-right: 5px !important;
            }
        }

        @media screen {
            body {
                background-color: #fff !important;
                padding: 20px !important;
            }
        }

        @media (min-width: 576px) {
            .nk-content {
                padding: 52px 22px;
            }
        }
    </style>
</head>

<body class="bg-white" onload="window.print()">

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="invoice-brand text-center mb-4">
                        <img src="{{ asset('backAssets/images/logo.png') }}" alt="Logo" style="max-height:60px;">
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            @php $firstsample = $samples->first(); @endphp
                            <div>
                                <h3 class="nk-block-title page-title">Customer Details:
                                    <strong class="text-primary small">
                                        {{ $firstsample['customer']['m07_name'] }}
                                    </strong>
                                </h3>
                                <ul class="list-inline text-soft small">
                                    <li>Contact Person: <strong>{{ $firstsample['customer']['m07_contact_person'] }}</strong></li>
                                    <li>GST: <strong>{{ $firstsample['customer']['m07_gst'] ?? 'N/A' }}</strong></li>
                                    <li>Location ID: <strong>{{ $firstsample['m08_customer_location_id'] ?? 'N/A' }}</strong></li>
                                    <li>Payment By: <strong>{{ $firstsample['tr04_payment_by'] ?? 'N/A' }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="invoice">
                            <div class="invoice-wrap">
                                @foreach($samples as $sample)
                                <div class="invoice-head border-bottom pb-3 mb-3">
                                    <div class="invoice-contact">
                                        <span class="overline-title">Sample Details</span>
                                        <div class="invoice-contact-info">
                                            <h4 class="title">#{{ $sample->tr04_reference_id }}</h4>
                                            <p><strong>Lab Sample:</strong> {{ $sample->labSample['m14_name'] ?? 'N/A' }}</p>
                                            <p><strong>Description:</strong> {{ $sample->tr04_sample_description ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="invoice-desc text-end">
                                        <ul class="list-plain">
                                            <li><strong>Grand Total:</strong> ₹{{ number_format($sample->tr04_total_charges, 2) }}</li>
                                        </ul>
                                    </div>
                                </div>
                                @endforeach

                                <div class="invoice-foot pt-3 mt-4 border-top">
                                    <div class="invoice-summary text-end">
                                        <ul class="list-plain">
                                            <li>
                                                <span><strong>Total Amount:</strong></span>
                                                <span><strong>₹{{ number_format($totalAmount, 2) }}</strong></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div> <!-- .invoice-wrap -->
                        </div> <!-- .invoice -->
                    </div> <!-- .nk-block -->
                </div> <!-- .components-preview -->
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backAssets/js/bundle.js') }}"></script>
<script src="{{ asset('backAssets/js/scripts.js') }}"></script>
</body>

</html>
