@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
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
                                <form action="{{ route('create_specification') }}" method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Specification Name --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Specification Name<b
                                                        class="text-danger">*</b></label>
                                                <input type="text" class="form-control" id="txt_name" name="txt_name"
                                                    required>
                                            </div>
                                        </div>

                                        {{-- Charge --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_charges">Charge<b
                                                        class="text-danger">*</b></label>
                                                <input type="number" class="form-control" id="txt_charges"
                                                    name="txt_charges" required>
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
                                            <tr>
                                                <td class="position-relative">
                                                    <input type="text" class="form-control test-search-input"
                                                        placeholder="Search Test..." autocomplete="off">
                                                    <input type="hidden" name="tests[0][test_id]" class="test-id-field">
                                                    <div class="custom-dropdown test-dropdown"></div>
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

    {{-- Styles --}}
    <style>
        .custom-dropdown {
            position: absolute;
            z-index: 1050;
            display: none;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-height: 250px;
            overflow-y: auto;
            margin-top: 2px;
            width: 100%;
        }

        .custom-dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .custom-dropdown-item:last-child {
            border-bottom: none;
        }

        .custom-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-message {
            padding: 10px;
            text-align: center;
            color: #6c757d;
        }
    </style>

    {{-- Scripts --}}
    <script>
        $(function() {
            let rowIndex = 1;
            const TEST_URL = "{{ route('search_test') }}";
            const STANDARD_URL = "{{ route('get_standards_by_test') }}";
            let searchTimeout;

            /** Add Rows **/
            $('#add-row').on('click', function() {
                const row = `
        <tr>
            <td class="position-relative">
                <input type="text" class="form-control test-search-input" placeholder="Search Test..." autocomplete="off">
                <input type="hidden" name="tests[${rowIndex}][test_id]" class="test-id-field">
                <div class="custom-dropdown test-dropdown"></div>
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

            /** Search Tests **/
            $(document).on('input', '.test-search-input', function() {
                const query = $(this).val().trim();
                const $input = $(this);
                const $dropdown = $input.siblings('.test-dropdown');
                clearTimeout(searchTimeout);

                if (query.length < 2) return $dropdown.hide().empty();

                searchTimeout = setTimeout(() => {
                    $dropdown.html('<div class="dropdown-message">Searching...</div>').show();

                    $.getJSON(TEST_URL, {
                        query
                    }, tests => {
                        $dropdown.empty();
                        if (!tests.length) {
                            return $dropdown.html(
                                '<div class="dropdown-message">No tests found.</div>');
                        }
                        tests.forEach(t => $('<div>')
                            .addClass('custom-dropdown-item')
                            .text(t.test_name)
                            .data('test', t)
                            .appendTo($dropdown));
                    });
                }, 300);
            });

            /** Select Test **/
            $(document).on('click', '.custom-dropdown-item', function() {
                const t = $(this).data('test');
                const $row = $(this).closest('td');
                $row.find('.test-search-input').val(t.test_name);
                $row.find('.test-id-field').val(t.id);

                const $standard = $row.closest('tr').find('.standard-select');
                $standard.html('<option>Loading...</option>');

                $.getJSON(STANDARD_URL, {
                    test_id: t.id
                }, data => {
                    $standard.html('<option value="">-- Select Standard --</option>');
                    data.forEach(s => $standard.append(
                        `<option value="${s.id}">${s.name}</option>`));
                });

                $(this).parent().hide();
            });

            /** Hide dropdown on outside click **/
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.test-search-input, .custom-dropdown').length) {
                    $('.test-dropdown').hide();
                }
            });
        });
    </script>
@endsection
