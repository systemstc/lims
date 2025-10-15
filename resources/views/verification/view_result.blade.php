@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block nk-block-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Verification for Sample: {{ $sample->tr04_reference_id }}</h5>
                        <a href="{{ route('view_result_verification') }}" class="btn btn-outline-primary btn-sm">
                            <em class="icon ni ni-back-alt-fill"></em>&nbsp; Back
                        </a>
                    </div>

                    <div class="card card-bordered mt-2">
                        <div class="card-inner">
                            <p><strong>Total Tests:</strong> {{ $sample->testResult->count() }}</p>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%">#</th>
                                            <th>Test / Manuscript</th>
                                            <th style="width:30%">Result</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedResults as $testNumber => $results)
                                            @php
                                                $parentResult = $results->first();
                                            @endphp

                                            {{-- Parent Test --}}
                                            <tr class="table-primary">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $parentResult->test?->m12_name ?? 'N/A' }}
                                                    @if ($parentResult->test?->standard?->m15_method)
                                                        <small
                                                            class="text-muted">({{ $parentResult->test->standard->m15_method }})</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($results->first()->m22_manuscript_id)
                                                        _
                                                    @else
                                                        {{ $parentResult->tr07_result ?? '-' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong
                                                        class="text-{{ $parentResult->tr07_result_status === 'RESULTED' ? 'warning' : 'success' }}">
                                                        {{ $parentResult->tr07_result_status === 'RESULTED' ? 'Verification Pending' : $parentResult->tr07_result_status }}
                                                    </strong>
                                                </td>
                                                <td>{{ $parentResult->tr07_test_date ? date('d-m-Y', strtotime($parentResult->tr07_test_date)) : '-' }}
                                                </td>
                                            </tr>

                                            {{-- Child Manuscripts --}}
                                            @foreach ($results as $mkey => $manuscriptResult)
                                                @if ($manuscriptResult->m22_manuscript_id)
                                                    <tr class="table-secondary">
                                                        <td>{{ $loop->parent->iteration }}.{{ $mkey + 1 }}</td>
                                                        <td class="ps-5">
                                                            <em class="icon ni ni-file-text text-info"></em>
                                                            {{ $manuscriptResult->manuscript?->m22_name ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $manuscriptResult->tr07_result ?? '-' }}</td>
                                                        <td>
                                                            <strong
                                                                class="text-{{ $manuscriptResult->tr07_result_status === 'RESULTED' ? 'warning' : 'success' }}">
                                                                {{ $manuscriptResult->tr07_result_status === 'RESULTED' ? 'Verification Pending' : $manuscriptResult->tr07_result_status }}
                                                            </strong>
                                                        </td>
                                                        <td>{{ $manuscriptResult->tr07_test_date ? date('d-m-Y', strtotime($manuscriptResult->tr07_test_date)) : '-' }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                            {{-- Remarks + Action --}}
                            <form action="{{ route('verify_result', $sample->tr04_sample_registration_id) }}"
                                method="POST" class="mt-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="remarks" class="form-label">Remarks (optional)</label>
                                    <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Enter any remarks..."></textarea>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" name="action" value="verify" class="btn btn-success btn-sm">
                                        <em class="icon ni ni-check"></em> Verify Results
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger ms-5 btn-sm">
                                        <em class="icon ni ni-cross"></em> Reject Results
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- nk-block -->
            </div>
        </div>
    </div>
@endsection
