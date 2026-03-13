@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    {{-- Page Header --}}
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h3 class="nk-block-title page-title">View Manuscript (#{{ $manuscript->m22_manuscript_id }})
                                </h3>
                                <div class="text-end">
                                    <a href="{{ route('view_manuscripts') }}" class="btn btn-primary">
                                        <em class="icon ni ni-caret-left-fill"></em>&nbsp; Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Page Content --}}
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <span class="sub-text">Sample</span>
                                        <span class="lead-text">{{ $manuscript->sample->m10_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="sub-text">Group</span>
                                        <span class="lead-text">{{ $manuscript->group->m11_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="sub-text">Test</span>
                                        <span class="lead-text">{{ $manuscript->test->m12_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="sub-text">Manuscript Name</span>
                                        <span class="lead-text">{{ $manuscript->m22_name }}</span>
                                    </div>

                                    @if (isset($standards) && $standards->count() > 0)
                                        <div class="col-md-12 mt-3">
                                            <span class="sub-text">Associated Standards</span>
                                            <div class="mt-1">
                                                @foreach ($standards as $standard)
                                                    <span
                                                        class="badge badge-dim bg-primary">{{ $standard->m15_method }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if (isset($test))
                                        @php
                                            $primaryTestIds = $test->m16_primary_test_id
                                                ? explode(',', $test->m16_primary_test_id)
                                                : [];
                                            $secondaryTestIds = $test->m17_secondary_test_id
                                                ? explode(',', $test->m17_secondary_test_id)
                                                : [];

                                            $primaryTests = \App\Models\PrimaryTest::whereIn(
                                                'm16_primary_test_id',
                                                $primaryTestIds,
                                            )->get();
                                            $secondaryTests = \App\Models\SecondaryTest::whereIn(
                                                'm17_secondary_test_id',
                                                $secondaryTestIds,
                                            )->get();
                                        @endphp

                                        @if ($primaryTests->count() > 0)
                                            <div class="col-md-12 mt-3">
                                                <span class="sub-text mb-2 d-block">Primary & Secondary Tests</span>
                                                <div class="card shadow-sm border">
                                                    <div class="card-inner p-3">
                                                        <div class="row g-3">
                                                            @foreach ($primaryTests as $pTest)
                                                                <div class="col-md-4">
                                                                    <div class="fw-bold text-primary mb-1">
                                                                        {{ $pTest->m16_name }}</div>
                                                                    @php
                                                                        $assoc =
                                                                            json_decode(
                                                                                $test->m17_secondary_test_associations,
                                                                                true,
                                                                            ) ?? [];
                                                                        $secondaryForPrimary = array_filter(
                                                                            $assoc,
                                                                            function ($a) use ($pTest) {
                                                                                return $a['primary_test_id'] ==
                                                                                    $pTest->m16_primary_test_id;
                                                                            },
                                                                        );
                                                                        $secIds = array_column(
                                                                            $secondaryForPrimary,
                                                                            'secondary_test_id',
                                                                        );
                                                                    @endphp
                                                                    <ul class="list-unstyled ms-3 mb-0"
                                                                        style="font-size: 0.9em;">
                                                                        @foreach ($secondaryTests as $sTest)
                                                                            @if (in_array($sTest->m17_secondary_test_id, $secIds))
                                                                                <li><em
                                                                                        class="icon ni ni-chevron-right text-muted"></em>
                                                                                    {{ $sTest->m17_name }}</li>
                                                                            @endif
                                                                        @endforeach
                                                                        @if (empty($secIds))
                                                                            <li class="text-soft fst-italic">No secondary
                                                                                tests</li>
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="col-md-12 mt-5">
                                        <hr>
                                        <h5 class="title mb-3">Manuscript Content</h5>
                                        <div class="card bg-lighter border" style="min-height: 300px;">
                                            {!! $manuscript->m22_content ?:
                                                '<span class="text-muted fst-italic">No content provided for this manuscript.</span>' !!}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div> {{-- .nk-block --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Calculate any formulas in the manuscript content on page load
            const container = document.querySelector('.card.bg-lighter.border');
            if (container) {
                SummernoteTableFormulaEngine.calculate(container);
            }
        });
    </script>
@endsection
