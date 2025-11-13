@php
    $pKey = $pKey ?? $loop->index;
    $hasSecondaryTests = $primaryTest->secondaryTests && $primaryTest->secondaryTests->isNotEmpty();
@endphp

<tr class="primary-test-row" data-test-id="{{ $test->m12_test_id }}" data-test-number="{{ $test->m12_test_number }}"
    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
    <td>{{ $key + 1 }}.{{ $pKey + 1 }}</td>
    <td>
        <strong>{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
        @if ($primaryTest->m16_requirement)
            <br><small class="text-info">Requirement: {{ $primaryTest->m16_requirement }}</small>
        @endif
    </td>
    <td>
        <input type="hidden"
            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][test_id]"
            value="{{ $test->m12_test_number }}">
        <input type="hidden"
            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][primary_test_id]"
            value="{{ $primaryTest->m16_primary_test_id }}">
        <input type="hidden"
            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result_id]"
            value="{{ $existingPrimaryResult->tr07_test_result_id ?? '' }}">
        <input type="text" class="form-control form-control-sm border-0 bg-light"
            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
            value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result', $existingPrimaryResult->tr07_result ?? '') }}"
            placeholder="Enter result value" autocomplete="off">
    </td>
    <td>
        <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="primary"
            data-test-number="{{ $test->m12_test_number }}" data-id="{{ $primaryTest->m16_primary_test_id }}">
            <em class="icon ni ni-trash"></em>
        </button>
        @if ($hasSecondaryTests)
            <button type="button" class="btn btn-outline-success btn-sm add-secondary-test"
                data-test-number="{{ $test->m12_test_number }}"
                data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                data-secondary-tests='{{ $primaryTest->secondaryTests->toJson() }}'>
                <em class="icon ni ni-plus"></em> Add Secondary
            </button>
        @endif
        <button type="button" class="btn btn-outline-primary btn-sm add-custom-field"
            data-test-id="{{ $test->m12_test_id }}" data-test-number="{{ $test->m12_test_number }}"
            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}" data-type="primary">
            <em class="icon ni ni-plus"></em> Custom
        </button>
    </td>
</tr>

<!-- Existing Secondary Tests -->
@foreach ($existingResults->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->whereNotNull('m17_secondary_test_id') as $existingSecondaryResult)
    @php
        $secondaryTest = $primaryTest->secondaryTests
            ->where('m17_secondary_test_id', $existingSecondaryResult->m17_secondary_test_id)
            ->first();
    @endphp
    @if ($secondaryTest)
        <tr class="secondary-test-row" data-test-number="{{ $test->m12_test_number }}"
            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
            data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}">
            <td>{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->index + 1 }}</td>
            <td>{{ $secondaryTest->m17_name ?? 'N/A' }}</td>
            <td>
                <input type="hidden"
                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][test_id]"
                    value="{{ $test->m12_test_number }}">
                <input type="hidden"
                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][primary_test_id]"
                    value="{{ $primaryTest->m16_primary_test_id }}">
                <input type="hidden"
                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][secondary_test_id]"
                    value="{{ $secondaryTest->m17_secondary_test_id }}">
                <input type="hidden"
                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result_id]"
                    value="{{ $existingSecondaryResult->tr07_test_result_id ?? '' }}">
                <input type="text" class="form-control form-control-sm border-0 bg-light"
                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                    value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result', $existingSecondaryResult->tr07_result ?? '') }}"
                    placeholder="Enter result value" autocomplete="off">
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="secondary"
                    data-test-number="{{ $test->m12_test_number }}"
                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                    data-id="{{ $secondaryTest->m17_secondary_test_id }}">
                    <em class="icon ni ni-trash"></em>
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm add-custom-field"
                    data-test-id="{{ $test->m12_test_id }}" data-test-number="{{ $test->m12_test_number }}"
                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                    data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}" data-type="secondary">
                    <em class="icon ni ni-plus"></em> Custom
                </button>
            </td>
        </tr>
    @endif
@endforeach
