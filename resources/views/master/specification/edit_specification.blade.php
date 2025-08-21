@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Edit Specification</h4>
                            <a href="{{ route('view_specification') }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('update_specification', $specification->m19_package_id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Specification Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Specification Name <b
                                                        class="text-danger">*</b></label>
                                                <input type="text"
                                                    class="form-control @error('txt_name') is-invalid @enderror"
                                                    id="txt_name" name="txt_name"
                                                    value="{{ old('txt_name', $specification->m19_name) }}" required>
                                                @error('txt_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Charge --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_charges">Charge <b
                                                        class="text-danger">*</b></label>
                                                <input type="number"
                                                    class="form-control @error('txt_charges') is-invalid @enderror"
                                                    id="txt_charges" name="txt_charges"
                                                    value="{{ old('txt_charges', $specification->m19_charges) }}" required>
                                                @error('txt_charges')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dynamic Tests + Standards --}}
                                    <h5 class="mt-4">Tests & Standards</h5>
                                    <table class="table table-bordered mt-1" id="test-standard-table">
                                        <thead>
                                            <tr>
                                                <th>Test</th>
                                                <th>Standard</th>
                                                <th width="80px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($specification->packageTests as $index => $pkgTest)
                                                <tr>
                                                    <td>
                                                        <select name="tests[{{ $index }}][test_id]"
                                                            class="form-control test-select" required>
                                                            <option value="">-- Select Test --</option>
                                                            @foreach ($tests as $test)
                                                                <option value="{{ $test->m12_test_id }}"
                                                                    {{ $pkgTest->test->m12_test_id == $test->m12_test_id ? 'selected' : '' }}>
                                                                    {{ $test->m12_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="tests[{{ $index }}][standard_id]"
                                                            class="form-control standard-select"
                                                            data-selected="{{ $pkgTest->standard->m15_standard_id }}"
                                                            required>
                                                            <option value="">-- Select Standard --</option>
                                                        </select>

                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-row">X</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-sm btn-success" id="add-row">+ Add Test</button>

                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-primary">Update Specification</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        $(function() {
            let rowIndex = {{ $specification->packageTests->count() }};
            const STANDARD_URL = "{{ route('get_standards_by_test') }}";

            /** Add Rows **/
            $('#add-row').on('click', function() {
                const row = `
        <tr>
            <td>
                <select name="tests[${rowIndex}][test_id]" class="form-control test-select" required>
                    <option value="">-- Select Test --</option>
                    @foreach ($tests as $test)
                        <option value="{{ $test->m12_test_id }}">{{ $test->m12_name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="tests[${rowIndex}][standard_id]" class="form-control standard-select" required>
                    <option value="">-- Select Standard --</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
        </tr>`;
                $('#test-standard-table tbody').append(row);
                rowIndex++;
            });

            /** Remove Rows **/
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            $(document).on('change', '.test-select', function() {
                const testId = $(this).val();
                const $standard = $(this).closest('tr').find('.standard-select');
                const selectedStandard = $standard.data('selected'); // store old value
                $standard.html('<option>Loading...</option>');

                if (testId) {
                    $.getJSON(STANDARD_URL, {
                        test_id: testId
                    }, data => {
                        $standard.html('<option value="">-- Select Standard --</option>');
                        console.log(data)
                        data.forEach(s => {
                            const isSelected = (s.id == selectedStandard) ? 'selected' : '';
                            $standard.append(
                                `<option value="${s.id}" ${isSelected}>${s.name}</option>`
                            );
                        });
                    });
                } else {
                    $standard.html('<option value="">-- Select Standard --</option>');
                }
            });


            /** Auto-trigger change on page load to populate standards for existing rows **/
            $('.test-select').each(function() {
                if ($(this).val()) {
                    $(this).trigger('change');
                }
            });
        });
    </script>
@endsection
