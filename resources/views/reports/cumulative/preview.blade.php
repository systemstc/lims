@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Cumulative Report Preview</h3>
                            <div class="nk-block-des text-soft">
                                <p>Review matching samples, add seal/unique numbers, and configure report metadata.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <ul class="nk-block-tools g-3">
                                <li>
                                    <a href="{{ route('cumulative_reports') }}" class="btn btn-outline-primary btn-sm">
                                        <em class="icon ni ni-caret-left-fill"></em> Change Customer/Test
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <form method="POST" action="{{ route('generate_cumulative_report') }}" target="_blank">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->m07_customer_id }}">
                        <input type="hidden" name="test_number" value="{{ $test->m12_test_number }}">

                        <!-- Metadata Configuration -->
                        <div class="card card-bordered mb-4">
                            <div class="card-inner">
                                <h5 class="card-title text-primary"><em class="icon ni ni-setting-alt-fill"></em> Report Metadata Configuration</h5>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label" for="report_prefix">Report Number Prefix</label>
                                            <input type="text" class="form-control" id="report_prefix" name="report_prefix" value="TC/LH/SF/" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label" for="report_year">Report Year</label>
                                            <input type="text" class="form-control" id="report_year" name="report_year" value="{{ date('Y') - 1 }}-{{ date('Y') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label" for="lab_sample_prefix">Lab Sample Prefix</label>
                                            <input type="text" class="form-control" id="lab_sample_prefix" name="lab_sample_prefix" value="CFO-" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label" for="date">Report Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="forwarding_letter_no_date">Forwarding Letter No. & Date</label>
                                            <input type="text" class="form-control" id="forwarding_letter_no_date" name="forwarding_letter_no_date" 
                                                value="{{ $referenceNo ? 'Test Memo No. ' . $referenceNo : 'Nil' }} Dt. {{ $referenceDate ? \Carbon\Carbon::parse($referenceDate)->format('d.m.Y') : date('d.m.Y') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="receipt_date">Date of Receipt of Sample</label>
                                            <input type="date" class="form-control" id="receipt_date" name="receipt_date" value="{{ $receivedDate }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="sample_colour">Sample Colour</label>
                                            <input type="text" class="form-control" id="sample_colour" name="sample_colour" placeholder="Raw / White / Dyed / etc.">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="performance_date_start">Test Performance Start Date</label>
                                            <input type="date" class="form-control" id="performance_date_start" name="performance_date_start" value="{{ $performanceDateStart }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="performance_date_end">Test Performance End Date</label>
                                            <input type="date" class="form-control" id="performance_date_end" name="performance_date_end" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label" for="sample_description">Sample Description</label>
                                            <input type="text" class="form-control" id="sample_description" name="sample_description" 
                                                value="{{ $registrations->first()->tr04_sample_description ?: ($registrations->first()->labSample->m14_name ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sample List & Custom Inputs -->
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <h5 class="card-title text-primary"><em class="icon ni ni-list-index"></em> Select Samples and Fill Details</h5>
                                <p class="text-soft mb-3">Only selected samples will be compiled into the cumulative report PDF.</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;" class="text-center">
                                                    <div class="custom-control custom-control-sm custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="check-all" checked>
                                                        <label class="custom-control-label" for="check-all"></label>
                                                    </div>
                                                </th>
                                                <th>Sample No</th>
                                                <th>T.R. No</th>
                                                <th>Reference ID</th>
                                                <th>Sample Seal No. (Nullable)</th>
                                                <th>Sample Unique Code (Nullable)</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($registrations as $reg)
                                                @php
                                                    $trNo = intval(substr($reg->tr04_reference_id, -4));
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-control-sm custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input sample-checkbox" 
                                                                id="chk-{{ $reg->tr04_sample_registration_id }}" 
                                                                name="sample_ids[]" 
                                                                value="{{ $reg->tr04_sample_registration_id }}" checked>
                                                            <label class="custom-control-label" for="chk-{{ $reg->tr04_sample_registration_id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td><strong>{{ $reg->tr04_sample_registration_id }}</strong></td>
                                                    <td>{{ $trNo }}</td>
                                                    <td><span class="badge badge-dim bg-outline-secondary">{{ $reg->tr04_reference_id }}</span></td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                            name="seal_no[{{ $reg->tr04_sample_registration_id }}]" 
                                                            placeholder="e.g. AE64 7037">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                            name="unique_code[{{ $reg->tr04_sample_registration_id }}]" 
                                                            placeholder="e.g. Unique Code">
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-dim bg-success">VERIFIED</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-lg btn-success">
                                        <em class="icon ni ni-file-pdf"></em> Generate Cumulative PDF Report
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Check / Uncheck All
            $('#check-all').on('change', function() {
                $('.sample-checkbox').prop('checked', this.checked);
            });

            // If any checkbox is unchecked, check-all should be unchecked
            $('.sample-checkbox').on('change', function() {
                if ($('.sample-checkbox:checked').length === $('.sample-checkbox').length) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
                }
            });
        });
    </script>
    @endpush
@endsection
