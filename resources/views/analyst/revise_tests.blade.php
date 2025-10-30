@extends('layouts.app_back')

@section('content')
<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block nk-block-lg">

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Revised Entries for Sample:
                        <strong class="text-primary">#{{ $sample->tr04_reference_id }}</strong>
                    </h5>
                    <a href="{{ route('rejected_samples') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-caret-left-fill"></em> Back
                    </a>
                </div>

                {{-- Main Card --}}
                <div class="card card-bordered mt-2 shadow-sm">
                    <div class="card-inner">
                        <form action="{{ route('revise_test', $sample->tr04_reference_id) }}" method="POST">
                            @csrf

                            {{-- Summary Info --}}
                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Total Outputs</label>
                                    <input type="text" class="form-control form-control-sm"
                                           value="{{ $sample->testResult->count() }}" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Test Date <span class="text-danger">*</span></label>
                                    <input type="date" name="txt_test_date" class="form-control form-control-sm" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Performance Date</label>
                                    <input type="date" name="txt_performance_date" class="form-control form-control-sm">
                                </div>
                            </div>

                            {{-- Table --}}
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width:5%">#</th>
                                            <th>Test / Manuscript</th>
                                            <th>Old Result</th>
                                            <th>Old Remarks</th>
                                            <th>New Result</th>
                                            <th>New Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedResults as $testNumber => $results)
                                            @php 
                                                $parent = $results->first();
                                                $hasChild = $results->count() > 1 && $results->whereNotNull('m22_manuscript_id')->count() > 0;
                                            @endphp

                                            {{-- Parent Row --}}
                                            <tr class="table-primary">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $parent->test?->m12_name ?? 'N/A' }}</strong>
                                                    @if ($parent->test?->standard?->m15_method)
                                                        <small class="text-muted">
                                                            ({{ $parent->test->standard->m15_method }})
                                                        </small>
                                                    @endif
                                                </td>

                                                @if($hasChild)
                                                    {{-- Parent has manuscripts: no editable fields --}}
                                                    <td colspan="4" class="text-center text-muted fst-italic">
                                                        -
                                                    </td>
                                                @else
                                                    {{-- No child manuscripts: show input fields --}}
                                                    <td class="text-danger fw-bold text-center">
                                                        {{ $parent->tr07_result ?? '-' }}
                                                    </td>
                                                    <td>{{ $parent->tr07_remarks ?? '—' }}</td>
                                                    <td>
                                                        <input type="text"
                                                               name="txt_result_{{ $parent->tr07_test_result_id }}"
                                                               class="form-control form-control-sm"
                                                               placeholder="Enter new result" required>
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                               name="txt_remarks_{{ $parent->tr07_test_result_id }}"
                                                               class="form-control form-control-sm"
                                                               placeholder="Enter revised remarks">
                                                    </td>
                                                @endif
                                            </tr>

                                            {{-- Child Manuscripts --}}
                                            @foreach ($results as $mkey => $manuscript)
                                                @if ($manuscript->m22_manuscript_id)
                                                    <tr class="table-secondary">
                                                        <td>{{ $loop->parent->iteration }}.{{ $mkey + 1 }}</td>
                                                        <td class="ps-5">
                                                            <em class="icon ni ni-file-text text-info"></em>
                                                            {{ $manuscript->manuscript?->m22_name ?? 'N/A' }}
                                                        </td>
                                                        <td class="text-danger fw-bold text-center">
                                                            {{ $manuscript->tr07_result ?? '-' }}
                                                        </td>
                                                        <td>{{ $manuscript->tr07_remarks ?? '—' }}</td>
                                                        <td>
                                                            <input type="text"
                                                                   name="txt_result_{{ $manuscript->tr07_test_result_id }}"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Enter new result" required>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                   name="txt_remarks_{{ $manuscript->tr07_test_result_id }}"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Enter revised remarks">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Submit --}}
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <em class="icon ni ni-save"></em> Save All Revisions
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
