<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sample Details Print | LIMS</title>
    <!-- DashLite Styles -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <script src="{{ asset('backAssets/js/jquery.js') }}"></script>
    <style>
        /* Print-specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin: 0 !important;
            }

            .col-md-4 {
                flex: 0 0 33.3333% !important;
                max-width: 33.3333% !important;
                padding: 0 15px !important;
            }

            .col-md-6 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
                padding: 0 15px !important;
            }

            .col-md-12 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                padding: 0 15px !important;
            }

            /* Fix invoice header layout for side-by-side display */
            .invoice-head {
                display: table !important;
                width: 100% !important;
                table-layout: fixed !important;
            }

            .invoice-contact {
                display: table-cell !important;
                width: 60% !important;
                vertical-align: top !important;
                padding-right: 20px !important;
            }

            .invoice-desc {
                display: table-cell !important;
                width: 40% !important;
                vertical-align: top !important;
                padding-left: 20px !important;
            }

            .badge {
                background-color: #e5e5e5 !important;
                color: #000 !important;
                border: 1px solid #ccc !important;
            }

            .badge.bg-success,
            .badge.badge-success {
                background-color: #d4edda !important;
                color: #155724 !important;
                border-color: #c3e6cb !important;
            }

            .badge.bg-warning,
            .badge.badge-warning {
                background-color: #fff3cd !important;
                color: #856404 !important;
                border-color: #faeeba !important;
            }

            .badge.bg-primary,
            .badge.badge-primary {
                background-color: #d1ecf1 !important;
                color: #0c5460 !important;
                border-color: #bee5eb !important;
            }

            .badge.badge-info {
                background-color: #d1ecf1 !important;
                color: #0c5460 !important;
                border-color: #bee5eb !important;
            }

            .text-primary {
                color: #526484 !important;
            }

            .card {
                border: 1px solid #dbdfea !important;
                box-shadow: none !important;
                margin-bottom: 1rem !important;
            }

            /*
            .table {
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                border: 1px solid #dbdfea !important;
                padding: 8px !important;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa !important;
            }

            .overline-title {
                font-size: 11px !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                color: #8094ae !important;
            }
*/
            .page-break {
                page-break-before: always !important;
            }

            /* Ensure icons don't break */
            .icon {
                font-size: 14px !important;
                margin-right: 5px !important;
            }
        }

        */
        /* Screen styles */
        @media screen {
            body {
                background-color: #fff !important;
                padding: 20px !important;
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
                        <!-- Logo Section -->
                        <div class="invoice-brand text-center mb-4">
                            <img src="{{ asset('backAssets/images/logo.png') }}" alt="Logo"
                                style="max-height:60px;">
                        </div>

                        <!-- Header Section -->
                        <div class="nk-block nk-block-lg">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Sample Details
                                    <strong class="text-primary small">#{{ $sample->tr04_reference_id }}</strong>
                                </h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                        <li>Created At: <span
                                                class="text-base">{{ $sample->created_at->format('d M, Y h:i A') }}</span>
                                        </li>
                                        <li>Status:
                                            <span
                                                class="badge badge-dot 
                                                @if ($sample->tr04_status == 'ACTIVE') bg-success 
                                                @elseif($sample->tr04_status == 'PENDING') bg-warning 
                                                @else bg-secondary @endif">
                                                {{ ucfirst(strtolower($sample->tr04_status)) }}
                                            </span>
                                        </li>
                                        <li>Progress:
                                            <span
                                                class="badge bg-primary">{{ ucfirst(strtolower($sample->tr04_progress)) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="nk-block">
                            <div class="invoice">
                                <div class="invoice-wrap">
                                    <!-- Main Header with Customer Details -->
                                    <div class="invoice-head">
                                        <div class="invoice-contact">
                                            <span class="overline-title">Customer Details</span>
                                            <div class="invoice-contact-info">
                                                <h4 class="title">{{ $sample->parties['customer']['name'] }}</h4>
                                                <ul class="list-plain">
                                                    <li><em
                                                            class="icon ni ni-user-fill"></em><span>{{ $sample->parties['customer']['contact_person'] }}</span>
                                                    </li>
                                                    <li><em class="icon ni ni-map-pin-fill"></em><span>{{ $sample->parties['customer']['address'] }}
                                                            &nbsp;&nbsp;{{ $sample->parties['customer']['district'] }},
                                                            {{ $sample->parties['customer']['state'] }}</span></li>
                                                    <li><em
                                                            class="icon ni ni-call-fill"></em><span>{{ $sample->parties['customer']['phone'] }}</span>
                                                    </li>
                                                    <li><em
                                                            class="icon ni ni-mail-fill"></em><span>{{ $sample->parties['customer']['email'] }}</span>
                                                    </li>
                                                    @if ($sample->parties['customer']['gst'])
                                                        <li><em class="icon ni ni-file-text-fill"></em><span>GST:
                                                                {{ $sample->parties['customer']['gst'] }}</span></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="invoice-desc">
                                            <ul class="list-plain">
                                                <li class="invoice-id"><span>Registration
                                                        ID</span>:<span>#{{ $sample->tr04_sample_registration_id }}</span>
                                                </li>
                                                <li class="invoice-date"><span>Reference
                                                        No</span>:<span>{{ $sample->tr04_reference_no ?? 'N/A' }}</span>
                                                </li>
                                                <li class="invoice-date"><span>Reference
                                                        Date</span>:<span>{{ $sample->tr04_reference_date ? \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d M, Y') : 'N/A' }}</span>
                                                </li>
                                                <li class="invoice-date"><span>Tracker
                                                        ID</span>:<span>{{ $sample->tr04_tracker_id ?? 'N/A' }}</span>
                                                </li>
                                                <li class="invoice-date"><span>Sample
                                                        Type</span>:<span>{{ $sample->tr04_sample_type }}</span></li>
                                                <li class="invoice-date"><span>Expected
                                                        Date</span>:<span>{{ $sample->tr04_expected_date ? \Carbon\Carbon::parse($sample->tr04_expected_date)->format('d M, Y') : 'N/A' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div><!-- .invoice-head -->

                                    <!-- Additional Party Details -->
                                    @if ($sample->parties['buyer']['name'] || $sample->parties['third_party']['name'] || $sample->parties['cha']['name'])
                                        <div class="invoice-bills mb-4">
                                            <div class="row">
                                                @if ($sample->parties['buyer']['name'])
                                                    <div class="col-md-4">
                                                        <div class="invoice-contact">
                                                            <span class="overline-title">Buyer Details</span>
                                                            <div class="invoice-contact-info">
                                                                <h6 class="title">
                                                                    {{ $sample->parties['buyer']['name'] }}</h6>
                                                                <ul class="list-plain small">
                                                                    <li><em
                                                                            class="icon ni ni-user"></em><span>{{ $sample->parties['buyer']['contact_person'] }}</span>
                                                                    </li>
                                                                    <li><em
                                                                            class="icon ni ni-call"></em><span>{{ $sample->parties['buyer']['phone'] }}</span>
                                                                    </li>
                                                                    <li><em
                                                                            class="icon ni ni-mail"></em><span>{{ $sample->parties['buyer']['email'] }}</span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($sample->parties['third_party']['name'])
                                                    <div class="col-md-4">
                                                        <div class="invoice-contact">
                                                            <span class="overline-title">Third Party Details</span>
                                                            <div class="invoice-contact-info">
                                                                <h6 class="title">
                                                                    {{ $sample->parties['third_party']['name'] }}</h6>
                                                                <ul class="list-plain small">
                                                                    <li><em
                                                                            class="icon ni ni-user"></em><span>{{ $sample->parties['third_party']['contact_person'] }}</span>
                                                                    </li>
                                                                    <li><em
                                                                            class="icon ni ni-call"></em><span>{{ $sample->parties['third_party']['phone'] }}</span>
                                                                    </li>
                                                                    <li><em
                                                                            class="icon ni ni-mail"></em><span>{{ $sample->parties['third_party']['email'] }}</span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($sample->parties['cha']['name'])
                                                    <div class="col-md-4">
                                                        <div class="invoice-contact">
                                                            <span class="overline-title">CHA Details</span>
                                                            <div class="invoice-contact-info">
                                                                <h6 class="title">
                                                                    {{ $sample->parties['cha']['name'] }}</h6>
                                                                <ul class="list-plain small">
                                                                    <li><em
                                                                            class="icon ni ni-user"></em><span>{{ $sample->parties['cha']['contact_person'] }}</span>
                                                                    </li>
                                                                    <li><em
                                                                            class="icon ni ni-call"></em><span>{{ $sample->parties['cha']['phone'] }}</span>
                                                                    </li>
                                                                    <li><em
                                                                            class="icon ni ni-mail"></em><span>{{ $sample->parties['cha']['email'] }}</span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Sample Information Cards -->
                                    <div class="invoice-bills mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border">
                                                    <div class="card-inner">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <h6 class="card-title mb-3">Sample Information</h6>
                                                                <ul class="list-plain">
                                                                    <li><strong>Lab Sample:</strong>
                                                                        {{ $sample->labSample['m14_name'] ?? 'N/A' }}
                                                                    </li>
                                                                    <li><strong>Description:</strong>
                                                                        {{ $sample->tr04_sample_description ?? 'N/A' }}
                                                                    </li>
                                                                    <li><strong>Received Via:</strong>
                                                                        {{ ucfirst(str_replace('_', ' ', $sample->tr04_received_via)) }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-6">
                                                                @if ($sample->tr04_attachment)
                                                                    <img src="{{ asset('storage/' . $sample->tr04_attachment) }}"
                                                                        alt="Sample Image" class="img-thumbnail"
                                                                        style="width: 100%; max-width: 200px; height: auto; object-fit: cover;">
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border">
                                                    <div class="card-inner">
                                                        <h6 class="card-title mb-3">Payment & Report Information</h6>
                                                        <ul class="list-plain">
                                                            <li><strong>Payment By:</strong>
                                                                {{ ucfirst(str_replace('_', ' ', $sample->tr04_payment_by)) }}
                                                            </li>
                                                            <li><strong>Report To:</strong>
                                                                {{ ucfirst(str_replace('_', ' ', $sample->tr04_report_to)) }}
                                                            </li>
                                                            <li><strong>Payment Status:</strong>
                                                                <span
                                                                    class="badge 
                                                                    @if ($sample->tr04_payment == 'COMPLETED') bg-success 
                                                                    @elseif($sample->tr04_payment == 'PENDING') bg-warning 
                                                                    @else bg-secondary @endif">
                                                                    {{ $sample->tr04_payment }}
                                                                </span>
                                                            </li>
                                                            @if ($sample->package)
                                                                <li><strong>Package:</strong>
                                                                    {{ $sample->package['m19_name'] }}</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Test Details Table -->
                                    <div class="invoice-bills">
                                        <h5 class="mb-3">Test Details</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="w-150px">Test ID</th>
                                                        <th class="w-60">Test Name & Description</th>
                                                        <th>Standard/Method</th>
                                                        <th>Unit</th>
                                                        <th>Charge</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($sample->sampleTests as $sampleTest)
                                                        <tr>
                                                            <td>{{ $sampleTest['test']['m12_test_id'] }}</td>
                                                            <td>
                                                                <strong>{{ $sampleTest['test']['m12_name'] }}</strong>
                                                                @if ($sampleTest['test']['m12_description'])
                                                                    <br><small
                                                                        class="text-muted">{{ $sampleTest['test']['m12_description'] }}</small>
                                                                @endif
                                                                @if ($sampleTest['test']['m12_alias'])
                                                                    <br><span
                                                                        class="badge bg-outline-info">{{ $sampleTest['test']['m12_alias'] }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $sampleTest['standard']['m15_method'] ?? '_' }}
                                                                @if (!empty($sampleTest['standard']['accreditationForCurrentRo']) &&
                                                                        $sampleTest['standard']['accreditationForCurrentRo']['m21_is_accredited'] === 'YES')
                                                                    <br><span
                                                                        class="badge bg-outline-success badge-xs">
                                                                        Accredited (till
                                                                        {{ \Carbon\Carbon::parse($sampleTest['standard']['accreditationForCurrentRo']['m21_valid_till'])->format('d M Y') }})
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $sampleTest['test']['m12_unit'] ?? '_' }}</td>
                                                            <td>&#8377;{{ number_format($sampleTest['test']['m12_charge'], 2) }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="bold 
                                                                @if ($sampleTest['tr05_status'] == 'COMPLETED') text-success 
                                                                @elseif($sampleTest['tr05_status'] == 'PENDING') text-warning 
                                                                @elseif($sampleTest['tr05_status'] == 'IN_PROGRESS') text-info 
                                                                @elseif($sampleTest['tr05_status'] == 'TRANSFERRED') text-primary 
                                                                @else text-secondary @endif">
                                                                    {{ ucfirst(strtolower(str_replace('_', ' ', $sampleTest['tr05_status']))) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">No tests found</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <!-- QR Code Cell -->
                                                        <td rowspan ='3'><small class="my-auto text-muted">Scan to
                                                                view
                                                                registration >>></small></td>
                                                        <td
                                                            rowspan="{{ $sample->tr04_additional_charges > 0 ? 3 : 2 }}">
                                                            <div class="d-flex flex-column align-items-center p-2">
                                                                {!! QrCode::size(100)->generate(route('view_registration_pdf', $sample->tr04_sample_registration_id)) !!}
                                                                <strong class="text-dark fw-semibold">
                                                                    {{ $sample->tr04_tracker_id }}</strong>
                                                            </div>
                                                        </td>

                                                        <!-- Testing Charges -->
                                                        <td class="py-2" colspan="2">
                                                            <strong>Testing Charges</strong>
                                                        </td>
                                                        <td class="text-end py-2">
                                                            <span class="fw-semibold">&#8377;
                                                                {{ number_format($sample->tr04_testing_charges, 2) }}</span>
                                                        </td>
                                                    </tr>
                                                    @if ($sample->tr04_additional_charges > 0)
                                                        <tr>
                                                            <td class="py-2" colspan="2">
                                                                <strong>Additional Charges</strong>
                                                            </td>
                                                            <td class="text-end py-2">
                                                                <span class="fw-semibold">&#8377;
                                                                    {{ number_format($sample->tr04_additional_charges, 2) }}</span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="py-2" colspan="2">
                                                            
                                                            <strong>GST Type & Rate:</strong>
                                                            
                                                        </td>
                                                        <td class="text-end py-2">
                                                            <span class="fw-semibold">&#8377;
                                                                @if(!empty($roGst->cgst) && !empty($roGst->sgst))
                                                                CGST {{ $roGst->cgst }}% + SGST {{ $roGst->sgst }}%
                                                                @elseif(!empty($roGst->igst))
                                                                IGST {{ $roGst->igst }}%
                                                                @else
                                                                N/A
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-white">
                                                        <td class=" py-3" colspan="2">
                                                        </td>
                                                        <td class=" py-3" colspan="2">
                                                            <strong class="fs-6 text-dark">Grand Total</strong>
                                                        </td>
                                                        <td class="text-end py-3">
                                                            <strong class="text-primary fs-5">&#8377;
                                                                {{ number_format($sample->tr04_total_charges, 2) }}</strong>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            @if ($sample->tr04_details)
                                                <div class="nk-notes ff-italic fs-12px text-soft mt-3">
                                                    <strong>Lab Info:</strong> Laboratory of Textiles
                                                    {{ $sample->ro->m04_name ?? '' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div><!-- .invoice-bills -->
                                </div><!-- .invoice-wrap -->
                            </div><!-- .invoice -->
                        </div><!-- .nk-block -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('backAssets/js/bundle.js') }}"></script>
    <script src="{{ asset('backAssets/js/scripts.js') }}"></script>
</body>

</html>
