@php
    $pKey = $pKey ?? $loop->index;
    $hasSecondaryTests = $primaryTest->secondaryTests && $primaryTest->secondaryTests->isNotEmpty();
    $isRevision = $isRevision ?? false;
@endphp

<tr class="primary-test-row {{ $isRevision ? 'revision-highlight' : '' }}" data-test-id="{{ $test->m12_test_id }}"
    data-test-number="{{ $test->m12_test_number }}" data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
    <td>{{ $key + 1 }}.{{ $pKey + 1 }}</td>
    <td>
        <strong>{{ $primaryTest->m16_name ?? 'N/A' }}</strong>
        @if ($primaryTest->m16_requirement)
            <br><small class="text-info">Requirement: {{ $primaryTest->m16_requirement }}</small>
        @endif
    </td>
    <td class="{{ $isRevision ? 'text-danger fw-bold text-center' : '' }}">
        @if ($isRevision && $existingPrimaryResult)
            {{ $existingPrimaryResult->tr07_result ?? 'N/A' }}
            @if ($existingPrimaryResult->tr07_unit)
                <br><small class="text-muted">({{ $existingPrimaryResult->tr07_unit }})</small>
            @endif
        @endif
    </td>
    <td>
        <div class="input-group input-group-sm">
            <input type="hidden"
                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][test_id]"
                value="{{ $test->m12_test_number }}">
            <input type="hidden"
                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][primary_test_id]"
                value="{{ $primaryTest->m16_primary_test_id }}">
            @if (!$isRevision)
                <input type="hidden"
                    name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result_id]"
                    value="{{ $existingPrimaryResult->tr07_test_result_id ?? '' }}">
            @endif
            <input type="text"
                class="form-control form-control-sm {{ $isRevision ? 'border-primary bg-light' : 'border-0 bg-light' }}"
                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][result]"
                value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.result', $existingPrimaryResult->tr07_result ?? '') }}"
                placeholder="Enter {{ $isRevision ? 'revised ' : '' }}result value" autocomplete="off"
                {{ $isRevision ? 'required' : '' }}>
            <input type="text"
                class="form-control form-control-sm {{ $isRevision ? 'border-primary bg-light' : 'border-0 bg-light' }}"
                style="max-width: 80px;"
                name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][unit]"
                value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.unit', $existingPrimaryResult->tr07_unit ?? ($primaryTest->m16_unit ?? '')) }}"
                placeholder="Unit">
        </div>
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
                <em class="icon ni ni-plus"></em> Secondary
            </button>
        @endif
        <button type="button" class="btn btn-outline-warning btn-sm add-custom-field"
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
        <tr class="secondary-test-row {{ $isRevision ? 'revision-highlight' : '' }}"
            data-test-number="{{ $test->m12_test_number }}"
            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
            data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}">
            <td>{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->index + 1 }}</td>
            <td>{{ $secondaryTest->m17_name ?? 'N/A' }}</td>
            <td class="{{ $isRevision ? 'text-danger fw-bold text-center' : '' }}">
                @if ($isRevision && $existingSecondaryResult)
                    {{ $existingSecondaryResult->tr07_result ?? 'N/A' }}
                    @if ($existingSecondaryResult->tr07_unit)
                        <br><small class="text-muted">({{ $existingSecondaryResult->tr07_unit }})</small>
                    @endif
                @endif
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="hidden"
                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][test_id]"
                        value="{{ $test->m12_test_number }}">
                    <input type="hidden"
                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][primary_test_id]"
                        value="{{ $primaryTest->m16_primary_test_id }}">
                    <input type="hidden"
                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][secondary_test_id]"
                        value="{{ $secondaryTest->m17_secondary_test_id }}">
                    @if (!$isRevision)
                        <input type="hidden"
                            name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result_id]"
                            value="{{ $existingSecondaryResult->tr07_test_result_id ?? '' }}">
                    @endif
                    <input type="text"
                        class="form-control form-control-sm {{ $isRevision ? 'border-primary bg-light' : 'border-0 bg-light' }}"
                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][result]"
                        value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.result', $existingSecondaryResult->tr07_result ?? '') }}"
                        placeholder="Enter {{ $isRevision ? 'revised ' : '' }}result value" autocomplete="off"
                        {{ $isRevision ? 'required' : '' }}>
                    <input type="text"
                        class="form-control form-control-sm {{ $isRevision ? 'border-primary bg-light' : 'border-0 bg-light' }}"
                        style="max-width: 80px;"
                        name="results[{{ $test->m12_test_number }}][primary_tests][{{ $primaryTest->m16_primary_test_id }}][secondary_tests][{{ $secondaryTest->m17_secondary_test_id }}][unit]"
                        value="{{ old('results.' . $test->m12_test_number . '.primary_tests.' . $primaryTest->m16_primary_test_id . '.secondary_tests.' . $secondaryTest->m17_secondary_test_id . '.unit', $existingSecondaryResult->tr07_unit ?? ($secondaryTest->m17_unit ?? '')) }}"
                        placeholder="Unit">
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-test-row" data-type="secondary"
                    data-test-number="{{ $test->m12_test_number }}"
                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                    data-id="{{ $secondaryTest->m17_secondary_test_id }}">
                    <em class="icon ni ni-trash"></em>
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm add-custom-field"
                    data-test-id="{{ $test->m12_test_id }}" data-test-number="{{ $test->m12_test_number }}"
                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                    data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}" data-type="secondary">
                    <em class="icon ni ni-plus"></em> Custom
                </button>
            </td>
        </tr>

        <!-- Custom Fields for Secondary Tests in Revision -->
        @if ($isRevision)
            @foreach ($rejectedCustomFields->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->where('m17_secondary_test_id', $secondaryTest->m17_secondary_test_id) as $customField)
                <tr class="custom-field-row revision-highlight" data-test-number="{{ $test->m12_test_number }}"
                    data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}"
                    data-secondary-test-id="{{ $secondaryTest->m17_secondary_test_id }}">
                    <td>{{ $key + 1 }}.{{ $pKey + 1 }}.{{ $loop->parent->index + 1 }}.C{{ $loop->iteration }}
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm custom-field-input"
                            name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][secondary_{{ $secondaryTest->m17_secondary_test_id }}][{{ $customField->tr08_custom_field_id }}][name]"
                            value="{{ $customField->tr08_field_name }}" placeholder="Custom Field Name" required>
                        <input type="hidden"
                            name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][secondary_{{ $secondaryTest->m17_secondary_test_id }}][{{ $customField->tr08_custom_field_id }}][custom_field_id]"
                            value="{{ $customField->tr08_custom_field_id }}">
                    </td>
                    <td class="text-danger fw-bold text-center">
                        {{ $customField->tr08_field_value }}
                        @if ($customField->tr08_field_unit)
                            <br><small class="text-muted">({{ $customField->tr08_field_unit }})</small>
                        @endif
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control form-control-sm custom-field-input"
                                name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][secondary_{{ $secondaryTest->m17_secondary_test_id }}][{{ $customField->tr08_custom_field_id }}][value]"
                                value="{{ $customField->tr08_field_value }}" placeholder="Enter revised value"
                                required>
                            <input type="text" class="form-control form-control-sm custom-field-input"
                                style="max-width: 80px;"
                                name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][secondary_{{ $secondaryTest->m17_secondary_test_id }}][{{ $customField->tr08_custom_field_id }}][unit]"
                                value="{{ $customField->tr08_field_unit ?? '' }}" placeholder="Unit">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-custom-field"
                            data-field-id="custom_field_{{ $customField->tr08_custom_field_id }}">
                            <em class="icon ni ni-trash"></em>
                        </button>
                    </td>
                </tr>
            @endforeach
        @endif
    @endif
@endforeach

<!-- Custom Fields for Primary Tests in Revision -->
@if ($isRevision)
    @foreach ($rejectedCustomFields->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->whereNull('m17_secondary_test_id') as $customField)
        <tr class="custom-field-row revision-highlight" data-test-number="{{ $test->m12_test_number }}"
            data-primary-test-id="{{ $primaryTest->m16_primary_test_id }}">
            <td>{{ $key + 1 }}.{{ $pKey + 1 }}.C{{ $loop->iteration }}</td>
            <td>
                <input type="text" class="form-control form-control-sm custom-field-input"
                    name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][{{ $customField->tr08_custom_field_id }}][name]"
                    value="{{ $customField->tr08_field_name }}" placeholder="Custom Field Name" required>
                <input type="hidden"
                    name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][{{ $customField->tr08_custom_field_id }}][custom_field_id]"
                    value="{{ $customField->tr08_custom_field_id }}">
            </td>
            <td class="text-danger fw-bold text-center">
                {{ $customField->tr08_field_value }}
                @if ($customField->tr08_field_unit)
                    <br><small class="text-muted">({{ $customField->tr08_field_unit }})</small>
                @endif
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm custom-field-input"
                        name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][{{ $customField->tr08_custom_field_id }}][value]"
                        value="{{ $customField->tr08_field_value }}" placeholder="Enter revised value" required>
                    <input type="text" class="form-control form-control-sm custom-field-input"
                        style="max-width: 80px;"
                        name="custom_fields[{{ $test->m12_test_number }}][primary_{{ $primaryTest->m16_primary_test_id }}][{{ $customField->tr08_custom_field_id }}][unit]"
                        value="{{ $customField->tr08_field_unit ?? '' }}" placeholder="Unit">
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger remove-custom-field"
                    data-field-id="custom_field_{{ $customField->tr08_custom_field_id }}">
                    <em class="icon ni ni-trash"></em>
                </button>
            </td>
        </tr>
    @endforeach
@endif
