@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Specification</h4>
                            <a href="{{ route('view_specification') }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <strong><em class="icon ni ni-alert-circle"></em> Please fix the following errors:</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                <form action="{{ route('create_specification') }}" class="form-validate is-alter" method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Specification Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Specification Name <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('txt_name') is-invalid @enderror" id="txt_name" name="txt_name"
                                                        value="{{ old('txt_name') }}" required>
                                                </div>
                                                @error('txt_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Charge --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_charges">Charge <b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="number" step="0.01" min="0" class="form-control @error('txt_charges') is-invalid @enderror" id="txt_charges"
                                                        name="txt_charges" value="{{ old('txt_charges') }}" required>
                                                </div>
                                                @error('txt_charges')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dynamic Tests + Standards --}}
                                    <h5 class="mt-4">Tests & Standards</h5>
                                    @error('tests')
                                        <span class="text-danger small d-block mb-2">{{ $message }}</span>
                                    @enderror
                                    <table class="table table-bordered mt-1" id="test-standard-table">
                                        <thead>
                                            <tr>
                                                <th>Test</th>
                                                <th>Standard</th>
                                                <th width="80px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-control-wrap">
                                                        <select name="tests[0][test_id]" class="form-control form-select js-select2 test-select" data-search="on" required>
                                                            <option value="">-- Select Test --</option>
                                                            @foreach ($tests as $test)
                                                                <option value="{{ $test->m12_test_id }}">{{ $test->m12_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-control-wrap">
                                                        <select name="tests[0][standard_id]"
                                                            class="form-control standard-select" required>
                                                            <option value="">-- Select Standard --</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger remove-row">X</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-sm btn-success" id="add-row">+ Add Test</button>

                                    <div class="col-md-12 mt-4">
                                        <button type="submit" class="btn btn-primary">Save Specification</button>
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
            let rowIndex = 1;
            const STANDARD_URL = "{{ route('get_standards_by_test') }}";

            function updateTestOptions() {
                let selectedTestIds = [];
                $('.test-select').each(function() {
                    let val = $(this).val();
                    if (val) selectedTestIds.push(String(val));
                });

                $('.test-select').each(function() {
                    let currentVal = String($(this).val() || '');
                    $(this).find('option').each(function() {
                        let optionVal = String($(this).attr('value') || '');
                        if (optionVal && optionVal !== currentVal && selectedTestIds.includes(optionVal)) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2();
                    }
                });
            }

            /** Add Rows **/
            $('#add-row').on('click', function() {
                const row = `
        <tr>
            <td>
                <div class="form-control-wrap">
                    <select name="tests[${rowIndex}][test_id]" class="form-control form-select js-select2 test-select" data-search="on" required>
                        <option value="">-- Select Test --</option>
                        @foreach ($tests as $test)
                            <option value="{{ $test->m12_test_id }}">{{ $test->m12_name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-control-wrap">
                    <select name="tests[${rowIndex}][standard_id]" class="form-control standard-select" required>
                        <option value="">-- Select Standard --</option>
                    </select>
                </div>
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
        </tr>`;
                $('#test-standard-table tbody').append(row);
                $('#test-standard-table tbody tr:last-child .js-select2').select2();
                rowIndex++;
                updateTestOptions();
            });

            /** Remove Rows **/
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateTestOptions();
            });

            $(document).on('change', '.test-select', function() {
                let testId = $(this).val();
                let $standardSelect = $(this).closest('tr').find('.standard-select');
                $standardSelect.empty().append('<option value="">Loading...</option>');

                if (testId) {
                    $.get(STANDARD_URL, {
                        test_id: testId
                    }, function(data) {
                        $standardSelect.empty().append('<option value="">-- Select Standard --</option>');
                        $.each(data, function(key, standard) {
                            $standardSelect.append(
                                `<option value="${standard.id}">${standard.name}</option>`
                            );
                        });
                    });
                } else {
                    $standardSelect.empty().append('<option value="">-- Select Standard --</option>');
                }
                updateTestOptions();
            });

            updateTestOptions();
        });
    </script>
@endsection
