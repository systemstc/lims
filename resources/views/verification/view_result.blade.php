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

                            @foreach ($groupedResults as $testNumber => $results)
                                @php $parentResult = $results->first(); @endphp

                                {{-- Parent Test Card --}}
                                <div class="card mt-3 shadow-sm border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-primary">
                                            {{ $parentResult->test?->m12_name ?? 'N/A' }}
                                            @if ($parentResult->test?->standard?->m15_method)
                                                <small class="text-muted">
                                                    ({{ $parentResult->test->standard->m15_method }})
                                                </small>
                                            @endif
                                        </h6>
                                    </div>

                                    <div class="card-body p-3">

                                        {{-- Iterate manuscripts and main test --}}
                                        @foreach ($results as $mkey => $res)
                                            <div class="">
                                                {{-- Section title --}}
                                                @if ($res->manuscript?->m22_name)
                                                    <h6 class="text-muted my-2">
                                                        <em class="icon ni ni-file-text text-info"></em>
                                                        {{ $res->manuscript?->m22_name }}
                                                    </h6>
                                                @endif

                                                {{-- If REVISED -> Show side-by-side comparison --}}
                                                @if ($res->tr07_result_status === 'REVISED' && isset($res->old_version))
                                                    <div class="row bg-light-subtle">
                                                        <div class="col-md-6">
                                                            <div class="p-2 bg-light rounded border">
                                                                <h6 class="text-danger mb-1">Old Result</h6>
                                                                <strong>Output:</strong>
                                                                <span
                                                                    class="text-danger">{{ $res->old_version->tr07_result ?? '-' }}</span><br>
                                                                <small class="text-muted">
                                                                    Date:
                                                                    {{ $res->old_version->tr07_test_date ? date('d-m-Y', strtotime($res->old_version->tr07_test_date)) : '-' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-2 bg-success-subtle rounded border">
                                                                <h6 class="text-success mb-1">Revised Result</h6>
                                                                <strong>Output:</strong>
                                                                <span
                                                                    class="text-{{ $res->tr07_result == $res->old_version->tr07_result ? 'danger' : 'success' }}">{{ $res->tr07_result ?? '-' }}</span><br>
                                                                <small class="text-muted">
                                                                    Date:
                                                                    {{ $res->tr07_test_date ? date('d-m-Y', strtotime($res->tr07_test_date)) : '-' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- Normal RESULTED row --}}
                                                    <div class="d-flex justify-content-between">
                                                        <div>Output: <strong>{{ $res->tr07_result ?? '-' }}</strong></div>
                                                        <div>
                                                            <span
                                                                class="fw-bold text-{{ $res->tr07_result_status === 'RESULTED' ? 'success' : 'warning' }}">
                                                                {{ $res->tr07_result_status }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            {{ $res->tr07_test_date ? date('d-m-Y', strtotime($res->tr07_test_date)) : '-' }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

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
                                    <button type="submit" name="action" value="reject" class="btn btn-danger ms-3 btn-sm">
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
