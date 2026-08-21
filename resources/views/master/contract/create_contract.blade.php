@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Create Contract</h4>
                            <a href="{{ route('view_contract') }}" class="btn btn-primary">
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
                                <form action="{{ route('create_contract') }}" class="form-validate is-alter" method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Contract Name --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Contract Name<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_name" name="txt_name"
                                                        value="{{ old('txt_name') }}" required>
                                                </div>
                                                @error('txt_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Contract With --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_contract_with">Contract With<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="hidden" name="txt_customer_id" id="txt_customer_id" value="{{ old('txt_customer_id') }}">
                                                    <input type="text" class="form-control" autocomplete="off"
                                                        id="txt_contract_with" name="txt_contract_with"
                                                        value="{{ old('txt_contract_with') }}" placeholder="Type to search customer..." required>
                                                </div>
                                                @error('txt_customer_id')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                                @error('txt_contract_with')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Expiry Date --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_exp_date">Expiry Date<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="date" class="form-control" id="txt_exp_date"
                                                        name="txt_exp_date" value="{{ old('txt_exp_date') }}" required>
                                                </div>
                                                @error('txt_exp_date')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Charge --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_charges">Charge<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="number" step="0.01" min="0" class="form-control"
                                                        id="txt_charges" name="txt_charges" value="{{ old('txt_charges') }}"
                                                        required>
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
                                            @php
                                                $oldTests = old('tests', [['test_id' => '', 'standard_id' => '']]);
                                            @endphp
                                            @foreach ($oldTests as $index => $oldTest)
                                                <tr>
                                                    <td>
                                                        <div class="form-control-wrap">
                                                            <select name="tests[{{ $index }}][test_id]" class="form-control form-select js-select2 test-select @error('tests.'.$index.'.test_id') is-invalid @enderror" data-search="on"
                                                                required>
                                                                <option value="">-- Select Test --</option>
                                                                @foreach ($tests as $test)
                                                                    <option value="{{ $test->m12_test_id }}"
                                                                        {{ isset($oldTest['test_id']) && $oldTest['test_id'] == $test->m12_test_id ? 'selected' : '' }}>
                                                                        {{ $test->m12_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @error('tests.'.$index.'.test_id')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <div class="form-control-wrap">
                                                            <select name="tests[{{ $index }}][standard_id]"
                                                                class="form-control standard-select @error('tests.'.$index.'.standard_id') is-invalid @enderror"
                                                                data-old-standard="{{ $oldTest['standard_id'] ?? '' }}" required>
                                                                <option value="">-- Select Standard --</option>
                                                            </select>
                                                        </div>
                                                        @error('tests.'.$index.'.standard_id')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
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

    {{-- Styles --}}
    <style>
        .custom-dropdown {
            position: absolute;
            z-index: 1050;
            display: none;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-height: 300px;
            overflow-y: auto;
            margin-top: 2px;
            width: 100%;
        }

        .custom-dropdown-item {
            padding: 10px 15px;
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
            padding: 15px;
            text-align: center;
            color: #6c757d;
        }
    </style>

    {{-- Scripts --}}
    <script>
        $(function() {
            let rowIndex = {{ count(old('tests', [['test_id' => '', 'standard_id' => '']])) }};

            /** ========================
             * Helpers
             ======================== */
            const createDropdown = id => $('<div>', {
                id,
                class: 'custom-dropdown'
            }).appendTo('body').hide();
            const positionDropdown = ($input, $dropdown) => {
                const offset = $input.offset();
                $dropdown.css({
                    top: offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: $input.outerWidth()
                }).show();
            };

            function loadStandardsForSelect($testSelect, selectedStandardId = null) {
                const testId = $testSelect.val();
                const $standard = $testSelect.closest('tr').find('.standard-select');
                if (testId) {
                    $standard.html('<option>Loading...</option>');
                    $.getJSON("{{ route('get_standards_by_test') }}", {
                        test_id: testId
                    }, data => {
                        let options = '<option value="">-- Select Standard --</option>';
                        data.forEach(s => {
                            const isSelected = (selectedStandardId && selectedStandardId == s.id) ? 'selected' : '';
                            options += `<option value="${s.id}" ${isSelected}>${s.name}</option>`;
                        });
                        $standard.html(options);
                    });
                } else {
                    $standard.html('<option value="">-- Select Standard --</option>');
                }
            }

            // Initialize standards for any pre-selected test options
            $('.test-select').each(function() {
                if ($(this).val()) {
                    const oldStandard = $(this).closest('tr').find('.standard-select').data('old-standard');
                    loadStandardsForSelect($(this), oldStandard);
                }
            });

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

            /** ========================
             * Test & Standards Rows
             ======================== */
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

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateTestOptions();
            });

            $(document).on('change', '.test-select', function() {
                loadStandardsForSelect($(this));
                updateTestOptions();
            });

            updateTestOptions();

            /** ========================
             * Customer Search Dropdown
             ======================== */
            const CUSTOMER_URL = "{{ route('search_customer') }}";
            const $dropdown = createDropdown('customer-dropdown');
            let $activeInput = null,
                searchTimeout;

            $('#txt_contract_with').on('input', function() {
                const query = $(this).val().trim();
                $activeInput = $(this);
                clearTimeout(searchTimeout);

                if (query.length < 2) return $dropdown.hide().empty();

                searchTimeout = setTimeout(() => {
                    positionDropdown($activeInput, $dropdown);
                    $dropdown.html('<div class="dropdown-message">Searching...</div>');

                    $.getJSON(CUSTOMER_URL, {
                        query
                    }, customers => {
                        $dropdown.empty();
                        if (!customers.length) {
                            return $dropdown.html(
                                '<div class="dropdown-message">No customers found.</div>'
                                );
                        }
                        customers.forEach(c =>
                            $('<div>')
                            .addClass('custom-dropdown-item')
                            .text(c.name)
                            .data('customer', c)
                            .appendTo($dropdown)
                        );
                    });
                }, 300);
            });

            $(document).on('click', '.custom-dropdown-item', function() {
                const c = $(this).data('customer');
                $activeInput.val(c.name);
                $('#txt_customer_id').val(c.id);
                $dropdown.hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#txt_contract_with, .custom-dropdown').length) {
                    $dropdown.hide();
                }
            });
        });
    </script>
@endsection
