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

    $priKey = $current?->tr07_test_result_id ? (string)$current->tr07_test_result_id : null;
    $priCompKey = $test->m12_test_number . '_' . $primaryTest->m16_primary_test_id . '_0';
    $priStar = ($priKey ? ($remarksStarMap[$priKey] ?? null) : null) ?? ($remarksStarMap[$priCompKey] ?? null);
    $priRemarkText = ($priKey ? ($remarksTextMap[$priKey] ?? null) : null) ?? ($remarksTextMap[$priCompKey] ?? null);

    $isPrimaryRejected = $current && $current->tr07_result_status === 'REJECTED';
    $allSecondaryRejected = $hasEnteredSecondaryResults && $enteredSecondaryResults->every(fn($r) => $r->tr07_result_status === 'REJECTED');
    $isPrimaryRowRejected = $isPrimaryRejected || ($hasEnteredSecondaryResults && !$current && $allSecondaryRejected);
@endphp

{{-- Only render the row when a result has actually been entered --}}
@if ($hasAnyResult || $hasPrimaryCustomFields)
<tr class="primary-test-row {{ $isPrimaryRowRejected ? 'rejected-test-row table-danger' : 'bg-light' }}" data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
    <td class="fw-bold {{ $isPrimaryRowRejected ? 'test-sr-no' : '' }}">{{ $key + 1 }}.{{ $pKey + 1 }}</td>
    <td class="{{ $isPrimaryRowRejected ? 'test-name-text' : '' }}">
        <strong class="{{ $isPrimaryRowRejected ? 'text-decoration-line-through text-danger' : '' }}">{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
        @if ($primaryTest->m16_requirement)
            <br><small class="text-info">Requirement: {{ $primaryTest->m16_requirement }}</small>
        @endif
    </td>
    <td class="text-center {{ $isPrimaryRowRejected ? 'test-result-text' : '' }}">
        @if ($hasEnteredSecondaryResults)
            {{--
                Secondary results exist → show "Calculated from secondary tests"
                only if no direct result was also entered on the primary itself.
                If the analyst entered BOTH, prefer showing the direct result.
            --}}
            @if ($current)
                {{-- Direct result present alongside secondary results --}}
                @php
                    $priHistClean = trim(strip_tags((string)($historical->tr07_result ?? '')));
                    $priCurrClean = trim(strip_tags((string)($current->tr07_result ?? '')));
                @endphp
                @if ($historical && $current && $priHistClean !== '' && $priHistClean !== $priCurrClean)
                    <div class="comparison-box">
                        <span class="text-danger text-decoration-line-through me-1">{!! $historical->tr07_result ?? 'N/A' !!}</span>
                        <em class="icon ni ni-arrow-right small text-muted"></em>
                        <span class="text-success fw-bold ms-1">{!! $current->tr07_result ?? 'N/A' !!}</span>
                        @if ($current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                        @if ($priStar) <sup class="text-danger fw-bold ms-1" title="Remark: {{ $priRemarkText }}">{{ $priStar }}</sup> @endif
                    </div>
                @else
                    <span class="fw-bold {{ $isPrimaryRejected ? 'text-decoration-line-through text-danger' : '' }}">{!! $current->tr07_result ?? 'N/A' !!}</span>
                    @if ($current && $current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                    @if ($priStar) <sup class="text-danger fw-bold ms-1" title="Remark: {{ $priRemarkText }}">{{ $priStar }}</sup> @endif
                @endif
            @else
                <span class="text-muted small italic {{ $allSecondaryRejected ? 'text-decoration-line-through text-danger' : '' }}">Calculated from secondary tests</span>
            @endif
        @elseif ($current)
            {{-- Only direct result, no secondary results entered --}}
            @php
                $priHistClean = trim(strip_tags((string)($historical->tr07_result ?? '')));
                $priCurrClean = trim(strip_tags((string)($current->tr07_result ?? '')));
            @endphp
            @if ($historical && $priHistClean !== '' && $priHistClean !== $priCurrClean)
                <div class="comparison-box">
                    <span class="text-danger text-decoration-line-through me-1">{!! $historical->tr07_result ?? 'N/A' !!}</span>
                    <em class="icon ni ni-arrow-right small text-muted"></em>
                    <span class="text-success fw-bold ms-1">{!! $current->tr07_result ?? 'N/A' !!}</span>
                    @if ($current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                    @if ($priStar) <sup class="text-danger fw-bold ms-1" title="Remark: {{ $priRemarkText }}">{{ $priStar }}</sup> @endif
                </div>
            @else
                <span class="fw-bold {{ $isPrimaryRejected ? 'text-decoration-line-through text-danger' : '' }}">{!! $current->tr07_result ?? 'N/A' !!}</span>
                @if ($current && $current->tr07_unit) <small class="text-muted">({{ $current->tr07_unit }})</small> @endif
                @if ($priStar) <sup class="text-danger fw-bold ms-1" title="Remark: {{ $priRemarkText }}">{{ $priStar }}</sup> @endif
            @endif
        @else
            {{-- No result entered, but has custom fields — show dash --}}
            <span class="text-muted small">—</span>
        @endif
    </td>
    <td class="text-center">
        @if ($current)
            @php
                $priBadge = match($current->tr07_result_status) {
                    'REJECTED' => 'bg-danger text-danger',
                    'REVISED' => 'bg-warning text-warning',
                    default => 'bg-success text-success',
                };
            @endphp
            <span class="badge badge-dim {{ $priBadge }}">
                @if ($current->tr07_result_status === 'REJECTED')
                    <em class="icon ni ni-cross-circle me-1"></em>
                @endif
                {{ $current->tr07_result_status }}
            </span>
        @elseif ($hasEnteredSecondaryResults && $allSecondaryRejected)
            <span class="badge badge-dim bg-danger text-danger">
                <em class="icon ni ni-cross-circle me-1"></em>REJECTED
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
        $secKey = $currentSecondary?->tr07_test_result_id ? (string)$currentSecondary->tr07_test_result_id : null;
        $secCompKey = $test->m12_test_number . '_' . $primaryTest->m16_primary_test_id . '_' . $currentSecondary->m17_secondary_test_id;
        $secStar = ($secKey ? ($remarksStarMap[$secKey] ?? null) : null) ?? ($remarksStarMap[$secCompKey] ?? null);
        $secRemarkText = ($secKey ? ($remarksTextMap[$secKey] ?? null) : null) ?? ($remarksTextMap[$secCompKey] ?? null);
        $isSecondaryRejected = $currentSecondary && $currentSecondary->tr07_result_status === 'REJECTED';
    @endphp
    @if ($secondaryTest)
        <tr class="secondary-test-row {{ $isSecondaryRejected ? 'rejected-test-row table-danger' : '' }}">
            <td class="ps-3 {{ $isSecondaryRejected ? 'test-sr-no' : '' }}">{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->iteration }}</td>
            <td class="ps-3 {{ $isSecondaryRejected ? 'test-name-text' : '' }}">
                <span class="{{ $isSecondaryRejected ? 'text-decoration-line-through text-danger' : '' }}">{{ $secondaryTest->m17_name ?? 'N/A' }}</span>
            </td>
            <td class="text-center {{ $isSecondaryRejected ? 'test-result-text' : '' }}">
                @php
                    $secHistClean = trim(strip_tags((string)($historicalSecondary->tr07_result ?? '')));
                    $secCurrClean = trim(strip_tags((string)($currentSecondary->tr07_result ?? '')));
                @endphp
                @if ($historicalSecondary && $secHistClean !== '' && $secHistClean !== $secCurrClean)
                    <div class="comparison-box">
                        <span class="text-danger text-decoration-line-through me-1">{!! $historicalSecondary->tr07_result ?? 'N/A' !!}</span>
                        <em class="icon ni ni-arrow-right small text-muted"></em>
                        <span class="text-success fw-bold ms-1">{!! $currentSecondary->tr07_result ?? 'N/A' !!}</span>
                        @if ($currentSecondary->tr07_unit) <small class="text-muted">({{ $currentSecondary->tr07_unit }})</small> @endif
                        @if ($secStar) <sup class="text-danger fw-bold ms-1" title="Remark: {{ $secRemarkText }}">{{ $secStar }}</sup> @endif
                    </div>
                @else
                    <span class="fw-bold {{ $isSecondaryRejected ? 'text-decoration-line-through text-danger' : '' }}">{!! $currentSecondary->tr07_result ?? 'N/A' !!}</span>
                    @if ($currentSecondary->tr07_unit) <small class="text-muted">({{ $currentSecondary->tr07_unit }})</small> @endif
                    @if ($secStar) <sup class="text-danger fw-bold ms-1" title="Remark: {{ $secRemarkText }}">{{ $secStar }}</sup> @endif
                @endif
            </td>
            <td class="text-center">
                @php
                    $secBadge = match($currentSecondary->tr07_result_status) {
                        'REJECTED' => 'bg-danger text-danger',
                        'REVISED' => 'bg-warning text-warning',
                        default => 'bg-success text-success',
                    };
                @endphp
                <span class="badge badge-dim {{ $secBadge }}">
                    @if ($currentSecondary->tr07_result_status === 'REJECTED')
                        <em class="icon ni ni-cross-circle me-1"></em>
                    @endif
                    {{ $currentSecondary->tr07_result_status }}
                </span>
            </td>
        </tr>

        <!-- Custom Fields for Secondary -->
        @foreach ($customFields->filter(fn($f) => (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && (string)$f->m17_secondary_test_id === (string)$secondaryTest->m17_secondary_test_id) as $cf)
            @php
                $hCf = $historicalCustomFields->filter(fn($f) => $f->tr08_field_name === $cf->tr08_field_name && (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && (string)$f->m17_secondary_test_id === (string)$secondaryTest->m17_secondary_test_id)->first();
                $isCfRejected = $cf->tr08_result_status === 'REJECTED';
            @endphp
            <tr class="custom-field-row {{ $isCfRejected ? 'rejected-test-row table-danger' : 'table-light' }}">
                <td class="ps-4 small text-muted {{ $isCfRejected ? 'test-sr-no' : '' }}">{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->parent->iteration }}.C{{ $loop->iteration }}</td>
                <td class="ps-4 small text-muted {{ $isCfRejected ? 'test-name-text' : '' }}">
                    <span class="{{ $isCfRejected ? 'text-decoration-line-through text-danger' : '' }}">{{ $cf->tr08_field_name }}</span>
                </td>
                <td class="text-center {{ $isCfRejected ? 'test-result-text' : '' }}">
                    @php
                        $hCfClean = trim(strip_tags((string)($hCf->tr08_field_value ?? '')));
                        $cfClean = trim(strip_tags((string)($cf->tr08_field_value ?? '')));
                    @endphp
                    @if ($hCf && $hCfClean !== '' && $hCfClean !== $cfClean)
                        <div class="comparison-box">
                            <span class="text-danger text-decoration-line-through me-1">{!! $hCf->tr08_field_value ?? 'N/A' !!}</span>
                            <em class="icon ni ni-arrow-right small text-muted"></em>
                            <span class="text-success fw-bold ms-1">{!! $cf->tr08_field_value ?? 'N/A' !!}</span>
                        </div>
                    @else
                        <span class="{{ $isCfRejected ? 'text-decoration-line-through text-danger' : '' }}">{!! $cf->tr08_field_value !!}</span>
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $cfStatus = $cf->tr08_result_status ?: 'SUBMITTED';
                        $cfBadge = match($cfStatus) {
                            'REJECTED' => 'bg-danger text-danger',
                            'REVISED' => 'bg-warning text-warning',
                            default => 'bg-success text-success',
                        };
                    @endphp
                    <span class="badge badge-dim {{ $cfBadge }}">
                        @if ($cfStatus === 'REJECTED')
                            <em class="icon ni ni-cross-circle me-1"></em>
                        @endif
                        {{ $cfStatus }}
                    </span>
                </td>
            </tr>
        @endforeach
    @endif
@endforeach

<!-- Custom Fields for Primary (not tied to a secondary) -->
@foreach ($primaryCustomFields as $cf)
    @php
        $hCf = $historicalCustomFields->filter(fn($f) => $f->tr08_field_name === $cf->tr08_field_name && (string)$f->m16_primary_test_id === (string)$primaryTest->m16_primary_test_id && empty($f->m17_secondary_test_id))->first();
        $isCfRejected = $cf->tr08_result_status === 'REJECTED';
    @endphp
    <tr class="custom-field-row {{ $isCfRejected ? 'rejected-test-row table-danger' : 'table-light' }}">
        <td class="ps-4 small text-muted {{ $isCfRejected ? 'test-sr-no' : '' }}">{{ $key + 1 }}.{{ $pKey + 1 }}.C{{ $loop->iteration }}</td>
        <td class="ps-4 small text-muted {{ $isCfRejected ? 'test-name-text' : '' }}">
            <span class="{{ $isCfRejected ? 'text-decoration-line-through text-danger' : '' }}">{{ $cf->tr08_field_name }}</span>
            @if ($isCfRejected)
                <small class="text-danger d-block fw-normal"><em class="icon ni ni-cross-circle me-1"></em>Previously Rejected</small>
            @endif
        </td>
        <td class="text-center {{ $isCfRejected ? 'test-result-text' : '' }}">
            @php
                $hCfClean = trim(strip_tags((string)($hCf->tr08_field_value ?? '')));
                $cfClean = trim(strip_tags((string)($cf->tr08_field_value ?? '')));
            @endphp
            @if ($hCf && $hCfClean !== '' && $hCfClean !== $cfClean)
                <div class="comparison-box">
                    <span class="text-danger text-decoration-line-through me-1">{!! $hCf->tr08_field_value ?? 'N/A' !!}</span>
                    <em class="icon ni ni-arrow-right small text-muted"></em>
                    <span class="text-success fw-bold ms-1">{!! $cf->tr08_field_value ?? 'N/A' !!}</span>
                </div>
            @else
                <span class="{{ $isCfRejected ? 'text-decoration-line-through text-danger' : '' }}">{!! $cf->tr08_field_value !!}</span>
            @endif
        </td>
        <td class="text-center">
            @php
                $cfStatus = $cf->tr08_result_status ?: 'SUBMITTED';
                $cfBadge = match($cfStatus) {
                    'REJECTED' => 'bg-danger text-danger',
                    'REVISED' => 'bg-warning text-warning',
                    default => 'bg-success text-success',
                };
            @endphp
            <span class="badge badge-dim {{ $cfBadge }}">
                @if ($cfStatus === 'REJECTED')
                    <em class="icon ni ni-cross-circle me-1"></em>
                @endif
                {{ $cfStatus }}
            </span>
        </td>
    </tr>
@endforeach
