@extends('layouts.app_back')

@section('title', 'Generate Report')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Generate Report</h3>
                            <div class="nk-block-des text-soft">
                                <h6 class="mb-0">Sample: <strong
                                        class="fw-bold text-primary">#{{ $sample->tr04_reference_id }}</strong></h6>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('test_results') }}" class="btn btn-outline-primary btn-sm">
                                <em class="icon ni ni-caret-left-fill"></em> Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="alert alert-info">
                                <em class="icon ni ni-info"></em>
                                <strong>Use the up/down buttons</strong> to arrange tests in your preferred order for the
                                report.
                            </div>

                            {{-- Report Meta Information --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th width="40%">Customer</th>
                                            <td>{{ $meta['customer_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Report No</th>
                                            <td>{{ $meta['report_no'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sample Description</th>
                                            <td>{{ $meta['sample_description'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th width="40%">Date</th>
                                            <td>{{ $meta['date'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reference</th>
                                            <td>{{ $meta['reference'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sample Characteristics</th>
                                            <td>{{ $meta['sample_characteristics'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- Test List with Reorder Buttons --}}
                            <div class="mb-4">
                                <h6 class="title mb-3">Arrange Test Order</h6>
                                <div class="list-group">
                                    @foreach ($orderedItems as $index => $item)
                                        @if ($item['type'] === 'test')
                                            @php
                                                $results = $groupedResults[$item['test_number']] ?? collect();
                                                $parent = $results->first();
                                            @endphp
                                            <div class="list-group-item d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $parent->test->m12_name ?? 'Test #' . $item['test_number'] }}
                                                        <span></span></strong>
                                                    <small class="text-muted d-block">Test
                                                        #{{ $item['test_number'] }}</small>
                                                </div>
                                                <div class="badge bg-primary me-3">{{ $results->count() }} results</div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('move_test_up', ['id' => $sample->tr04_reference_id, 'index' => $index]) }}"
                                                        class="btn btn-outline-primary {{ $index == 0 ? 'disabled' : '' }}">
                                                        <em class="icon ni ni-arrow-up"></em>
                                                    </a>
                                                    <a href="{{ route('move_test_down', ['id' => $sample->tr04_reference_id, 'index' => $index]) }}"
                                                        class="btn btn-outline-primary {{ $index == count($orderedItems) - 1 ? 'disabled' : '' }}">
                                                        <em class="icon ni ni-arrow-down"></em>
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            @php
                                                $customFields = $groupedCustomFields[$item['test_number']] ?? collect();
                                            @endphp
                                            <div class="list-group-item d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <strong>Additional Data</strong>
                                                    <small class="text-muted d-block">Custom
                                                        #{{ $item['test_number'] }}</small>
                                                </div>
                                                <div class="badge bg-warning me-3">{{ $customFields->count() }} fields
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('move_test_up', ['id' => $sample->tr04_reference_id, 'index' => $index]) }}"
                                                        class="btn btn-outline-warning {{ $index == 0 ? 'disabled' : '' }}">
                                                        <em class="icon ni ni-arrow-up"></em>
                                                    </a>
                                                    <a href="{{ route('move_test_down', ['id' => $sample->tr04_reference_id, 'index' => $index]) }}"
                                                        class="btn btn-outline-warning {{ $index == count($orderedItems) - 1 ? 'disabled' : '' }}">
                                                        <em class="icon ni ni-arrow-down"></em>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('generate_report', ['id' => $sample->tr04_reference_id, 'generate_pdf' => true]) }}"
                                    class="btn btn-primary">
                                    <em class="icon ni ni-file-docs"></em> Generate PDF Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
