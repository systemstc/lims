@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Package</h4>
                            <a href="{{ route('view_package') }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('create_package') }}" method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Package Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Package Name<b class="text-danger">*</b></label>
                                                <input type="text" class="form-control" name="txt_name" required>
                                            </div>
                                        </div>

                                        {{-- Exclusive Azo Charge --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Charge</label>
                                                <input type="number" class="form-control" name="txt_charges">
                                            </div>
                                        </div>


                                        {{-- Description --}}
                                        {{-- <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="m19_description"></textarea>
                                            </div>
                                        </div> --}}
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
                                            <tr>
                                                <td>
                                                    <select name="tests[0][test_id]" class="form-control test-select"
                                                        required>
                                                        <option value="">-- Select Test --</option>
                                                        @foreach ($tests as $test)
                                                            <option value="{{ $test->m12_test_id }}">{{ $test->m12_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="tests[0][standard_id]"
                                                        class="form-control standard-select" required>
                                                        <option value="">-- Select Standard --</option>
                                                    </select>
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
                                        <button type="submit" class="btn btn-primary">Save Package</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rowIndex = 1;

        // Add new row
        $('#add-row').click(function() {
            let newRow = `<tr>
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
            $('#test-standard-table tbody').append(newRow);
            rowIndex++;
        });

        // Remove row
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        // Load standards via AJAX when test is selected
        $(document).on('change', '.test-select', function() {
            let testId = $(this).val();
            let $standardSelect = $(this).closest('tr').find('.standard-select');
            $standardSelect.empty().append('<option value="">Loading...</option>');

            if (testId) {
                $.get("{{ route('get_standards_by_test') }}", {
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
        });
    </script>
@endsection
