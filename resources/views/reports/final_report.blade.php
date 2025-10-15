@extends('layouts.app_back')

@section('title', 'Final Test Report')

@section('content')
    <div class="container my-4">
        <div class="card">
            <div class="card-body">

                <div class="text-center mb-3">
                    <h4 class="mb-0 text-decoration-underline">TEST REPORT</h4>
                </div>

                {{-- report / sample info --}}
                <div class="row mb-2">
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered mb-0">
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
                                    <td class="px-1" colspan="2">{{ $meta['customer_address'] }}</td>
                                </tr>
                                <tr>
                                    <th>Sample forwarding letter No. & date</th>
                                    <td colspan="2">Test Memo No. {{ $meta['reference'] }} dated
                                        {{ $meta['reference_date'] }}</td>
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
                </div>
                <h5 class="mt-3 text-center">TEST RESULTS</h5>
                {{-- sample desc --}}
                <table class="table table-sm table-bordered">
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
                <div class="text-center my-3">
                    <strong class="bold fs-6">
                        *****************************************************************************************************************************************************
                    </strong>
                </div>
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark text-center">
                        <tr>
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
                                        <span class="text-muted">({{ $parent->test->standard->m15_method }})</span>
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
                                        <td class="ps-4"><i class="bi bi-file-earmark-text me-1"></i>
                                            {{ $child->manuscript?->m22_name ?? 'Manuscript' }}</td>
                                        <td class="text-center">{{ $child->tr07_result ?? '-' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

                <div class="text-end mt-4">
                    <strong>{{ $report->generator->m06_name ?? 'Laboratory Incharge' }}</strong><br />
                    <small class="text-muted">Authorized Signatory</small>
                </div>
            </div>
        </div>
    </div>
@endsection
