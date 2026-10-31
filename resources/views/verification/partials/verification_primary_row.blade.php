@php
    $pKey = $pKey ?? $loop->index;

    // --- Has secondary test RESULTS actually been entered? ---
    // (not just whether secondary tests are *defined* in the master)
    $enteredSecondaryResults = $currentResults->filter(
        fn($r) => (string)$r->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id
                  && !empty($r->m17_secondary_test_id)
    );
    $hasEnteredSecondaryResults = $enteredSecondaryResults->isNotEmpty();

    // --- Direct result entered on the primary (no secondary) ---
    $current = $currentResults->filter(
        fn($r) => (string)$r->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id
                  && empty($r->m17_secondary_test_id)
    )->first();

    $historical = $historicalResults->filter(
        fn($r) => (string)$r->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id
                  && empty($r->m17_secondary_test_id)
    )->sortByDesc('tr07_test_result_id')->first();

    // --- Only show this primary row when there is something to display ---
    // Show if: direct result exists OR secondary results exist
    $hasAnyResult = $current !== null || $hasEnteredSecondaryResults;

    // Custom fields for this primary (not tied to a secondary)
    $primaryCustomFields = $customFields->filter(
        fn($f) => (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id
                  && empty($f->m17_secondary_test_id)
    );
    $hasPrimaryCustomFields = $primaryCustomFields->isNotEmpty();
@endphp

{{-- Only render the row when a result has actually been entered --}}
@if ($hasAnyResult || $hasPrimaryCustomFields)
<tr class="primary-test-row bg-light" data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
    <td class="fw-bold">{{ $key + 1 }}.{{ $pKey + 1 }}</td>
    <td>
        <strong>{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
        @if ($primaryTest->m16_requirement)
            <br><small class="text-info">Requirement: {{ $primaryTest->m16_requirement }}</small>
        @endif
    </td>
    <td class="text-center">
        @if ($hasEnteredSecondaryResults)
            {{--
                Secondary results exist → show "Calculated from secondary tests"
                only if no direct result was also entered on the primary itself.
                If the analyst entered BOTH, prefer showing the direct result.
            --}}
            @if ($current)
                {{-- Direct result present alongside secondary results --}}
                @if ($historical && $current && (string)$historical->tr07_result !== (string)$current->tr07_result)
                    <div class="comparison-box">
                        <span class="text-danger text-decoration-line-through me-1">{!! $historical->tr07_result ?? 'N/A' !!}</span>
                        <em class="icon ni ni-arrow-right small text-muted"></em>
                        <span class="text-success fw-bold ms-1">{!! $current->tr07_result ?? 'N/A' !!}</span>
                        @if ($current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                    </div>
                @else
                    <span class="fw-bold">{!! $current->tr07_result ?? 'N/A' !!}</span>
                    @if ($current && $current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                @endif
            @else
                <span class="text-muted small italic">Calculated from secondary tests</span>
            @endif
        @elseif ($current)
            {{-- Only direct result, no secondary results entered --}}
            @if ($historical && (string)$historical->tr07_result !== (string)$current->tr07_result)
                <div class="comparison-box">
                    <span class="text-danger text-decoration-line-through me-1">{!! $historical->tr07_result ?? 'N/A' !!}</span>
                    <em class="icon ni ni-arrow-right small text-muted"></em>
                    <span class="text-success fw-bold ms-1">{!! $current->tr07_result ?? 'N/A' !!}</span>
                    @if ($current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                </div>
            @else
                <span class="fw-bold">{!! $current->tr07_result ?? 'N/A' !!}</span>
                @if ($current && $current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
            @endif
        @else
            {{-- No result entered, but has custom fields — show dash --}}
            <span class="text-muted small">—</span>
        @endif
    </td>
    <td class="text-center">
        @if ($current)
            <span class="badge badge-dim bg-{{ $current->tr07_result_status === 'REVISED' ? 'warning' : 'success' }}">
                {{ $current->tr07_result_status }}
            </span>
        @endif
    </td>
</tr>
@endif

<!-- Secondary Tests (only those with actual entered results) -->
@foreach ($enteredSecondaryResults as $currentSecondary)
    @php
        $secondaryTest = collect($primaryTest->secondaryTests ?? [])->filter(
            fn($st) => (string)$st->m17_secondary_test_id === (string)$currentSecondary->m17_secondary_test_id
        )->first();
        $historicalSecondary = $historicalResults->filter(
            fn($r) => (string)$r->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id
                      && (string)$r->m17_secondary_test_id === (string)$currentSecondary->m17_secondary_test_id
        )->sortByDesc('tr07_test_result_id')->first();
    @endphp
    @if ($secondaryTest)
        <tr class="secondary-test-row">
            <td class="ps-3">{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->iteration }}</td>
            <td class="ps-3">{{ $secondaryTest->m17_name ?? 'N/A' }}</td>
            <td class="text-center">
                @if ($historicalSecondary && (string)$historicalSecondary->tr07_result !== (string)$currentSecondary->tr07_result)
                    <div class="comparison-box">
                        <span class="text-danger text-decoration-line-through me-1">{!! $historicalSecondary->tr07_result ?? 'N/A' !!}</span>
                        <em class="icon ni ni-arrow-right small text-muted"></em>
                        <span class="text-success fw-bold ms-1">{!! $currentSecondary->tr07_result ?? 'N/A' !!}</span>
                        @if ($currentSecondary->tr07_unit) <small class="text-muted">({{ $currentSecondary->tr07_unit }})</small> @endif
                    </div>
                @else
                    <span class="fw-bold">{!! $currentSecondary->tr07_result ?? 'N/A' !!}</span>
                    @if ($currentSecondary->tr07_unit) <small class="text-muted">({{ $currentSecondary->tr07_unit }})</small> @endif
                @endif
            </td>
            <td class="text-center">
                <span class="badge badge-dim bg-{{ $currentSecondary->tr07_result_status === 'REVISED' ? 'warning' : 'success' }}">
                    {{ $currentSecondary->tr07_result_status }}
                </span>
            </td>
        </tr>

        <!-- Custom Fields for Secondary -->
        @foreach ($customFields->filter(fn($f) => (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && (string)$f->m17_secondary_test_id === (string)$secondaryTest->m17_secondary_test_id) as $cf)
            @php
                $hCf = $historicalCustomFields->filter(fn($f) => $f->tr08_field_name === $cf->tr08_field_name && (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && (string)$f->m17_secondary_test_id === (string)$secondaryTest->m17_secondary_test_id)->first();
            @endphp
            <tr class="custom-field-row table-light">
                <td class="ps-4 small text-muted">{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->parent->iteration }}.C{{ $loop->iteration }}</td>
                <td class="ps-4 small text-muted">{{ $cf->tr08_field_name }}</td>
                <td class="text-center">
                    @if ($hCf && (string)$hCf->tr08_field_value !== (string)$cf->tr08_field_value)
                        <div class="comparison-box">
                            <span class="text-danger text-decoration-line-through me-1">{!! $hCf->tr08_field_value ?? 'N/A' !!}</span>
                            <em class="icon ni ni-arrow-right small text-muted"></em>
                            <span class="text-success fw-bold ms-1">{!! $cf->tr08_field_value ?? 'N/A' !!}</span>
                        </div>
                    @else
                        {!! $cf->tr08_field_value !!}
                    @endif
                </td>
                <td class="text-center small text-muted">Custom</td>
            </tr>
        @endforeach
    @endif
@endforeach

<!-- Custom Fields for Primary (not tied to a secondary) -->
@foreach ($primaryCustomFields as $cf)
    @php
        $hCf = $historicalCustomFields->filter(fn($f) => $f->tr08_field_name === $cf->tr08_field_name && (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && empty($f->m17_secondary_test_id))->first();
    @endphp
    <tr class="custom-field-row table-light">
        <td class="ps-4 small text-muted">{{ $key + 1 }}.{{ $pKey + 1 }}.C{{ $loop->iteration }}</td>
        <td class="ps-4 small text-muted">{{ $cf->tr08_field_name }}</td>
        <td class="text-center">
            @if ($hCf && (string)$hCf->tr08_field_value !== (string)$cf->tr08_field_value)
                <div class="comparison-box">
                    <span class="text-danger text-decoration-line-through me-1">{!! $hCf->tr08_field_value ?? 'N/A' !!}</span>
                    <em class="icon ni ni-arrow-right small text-muted"></em>
                    <span class="text-success fw-bold ms-1">{!! $cf->tr08_field_value ?? 'N/A' !!}</span>
                </div>
            @else
                {!! $cf->tr08_field_value !!}
            @endif
        </td>
        <td class="text-center small text-muted">Custom</td>
    </tr>
@endforeach
