@extends('layouts.app_back')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <!-- INVOICE WRAPPER -->
                    <div class="card shadow-sm bg-white p-4" style="page-break-inside: avoid;">
                        <!-- HEADER -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mb-1">INVOICE</h4>
                                <p class="text-muted small mb-0">Sample Reference: <strong>#{{ $sample->tr04_reference_id }}</strong></p>
                                <p class="text-muted small">Date: {{ $sample->created_at->format('d M, Y') }}</p>
                            </div>
                            <div>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm no-print">
                                    <em class="icon ni ni-back-alt-fill"></em> Back
                                </a>
                                <button onclick="window.print()" class="btn btn-outline-primary btn-sm no-print">
                                    <em class="icon ni ni-printer-fill"></em> Print
                                </button>
                            </div>
                        </div>

                        <div id="invoice-section" class="card shadow-sm bg-white p-4" style="page-break-inside: avoid;">


                        <!-- CUSTOMER DETAILS -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Customer Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Name:</strong> {{ $sample->parties['customer']['name'] }}</p>
                                    <p class="mb-1"><strong>Contact Person:</strong> {{ $sample->parties['customer']['contact_person'] }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ $sample->parties['customer']['phone'] }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $sample->parties['customer']['email'] }}</p>
                                    <p class="mb-1"><strong>GST:</strong> {{ $sample->parties['customer']['gst'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Address:</strong> {{ $sample->parties['customer']['address'] }}</p>
                                    <p class="mb-1"><strong>District:</strong> {{ $sample->parties['customer']['district'] }}</p>
                                    <p class="mb-1"><strong>State:</strong> {{ $sample->parties['customer']['state'] }}</p>
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="badge 
                                            @if ($sample->tr04_status == 'ACTIVE') bg-success 
                                            @elseif($sample->tr04_status == 'PENDING') bg-warning 
                                            @else bg-secondary @endif">
                                            {{ ucfirst(strtolower($sample->tr04_status)) }}
                                        </span>
                                    </p>
                                    <p class="mb-1"><strong>Progress:</strong> 
                                        <span class="badge bg-primary">{{ ucfirst(strtolower($sample->tr04_progress)) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- SAMPLE DETAILS -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Sample Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Lab Sample:</strong> {{ $sample->labSample['m14_name'] ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Description:</strong> {{ $sample->tr04_sample_description ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Sample Type:</strong> {{ $sample->tr04_sample_type }}</p>
                                    <p class="mb-1"><strong>Received Via:</strong> {{ $sample->tr04_received_via }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Reference No:</strong> {{ $sample->tr04_reference_no ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Reference Date:</strong> {{ $sample->tr04_reference_date ? \Carbon\Carbon::parse($sample->tr04_reference_date)->format('d M, Y') : 'N/A' }}</p>
                                    <p class="mb-1"><strong>Expected Date:</strong> {{ $sample->tr04_expected_date ? \Carbon\Carbon::parse($sample->tr04_expected_date)->format('d M, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- TEST DETAILS -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Test Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Test ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Method</th>
                                            <th>Unit</th>
                                            <th>Charge (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sample->sampleTests as $test)
                                            <tr>
                                                <td>{{ $test['test']['m12_test_id'] }}</td>
                                                <td>{{ $test['test']['m12_name'] }}</td>
                                                <td>{{ $test['test']['m12_description'] ?? '-' }}</td>
                                                <td>{{ $test['standard']['m15_method'] ?? '-' }}</td>
                                                <td>{{ $test['test']['m12_unit'] ?? '-' }}</td>
                                                <td>{{ number_format($test['test']['m12_charge'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- CHARGES SUMMARY -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">Charges Summary</h6>
                            <table class="table table-borderless w-50 ms-auto">
                                <tr>
                                    <td>Testing Charges</td>
                                    <td class="text-end">₹{{ number_format($sample->tr04_testing_charges, 2) }}</td>
                                </tr>
                                @if ($sample->tr04_additional_charges > 0)
                                <tr>
                                    <td>Additional Charges</td>
                                    <td class="text-end">₹{{ number_format($sample->tr04_additional_charges, 2) }}</td>
                                </tr>
                                @endif
                                    <tr>
                                                        <td>
                                                            
                                                            <label>GST Type & Rate:</label>
                                                            
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="fw-semibold">
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
                                <tr class="border-top fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">₹{{ number_format($sample->tr04_total_charges, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- FOOTER OR NOTE -->
                        @if ($sample->tr04_details)
                            <div class="text-muted small mt-4">
                                <strong>Note:</strong> {{ $sample->tr04_details }}
                            </div>
                        @endif

                    </div>

                    </div><!-- end card -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
