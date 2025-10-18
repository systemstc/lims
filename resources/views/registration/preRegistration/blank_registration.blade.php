@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Instant Sample Registration</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('blank_registration') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="dd_customer_type">Customer
                                                    Type<b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select class="form-control required" name="dd_customer_type"
                                                        id="dd_customer_type" required>
                                                        <option value="" disabled selected>Select
                                                            Customer
                                                            Type</option>
                                                        @foreach ($customerTypes as $customerType)
                                                            <option value="{{ $customerType->m09_customer_type_id }}">
                                                                {{ $customerType->m09_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 position-relative">
                                            <label class="form-label">Customer Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="txt_customer_name" name="txt_customer_name"
                                                class="form-control" autocomplete="off" required>
                                            <input type="hidden" id="selected_customer_id" name="selected_customer_id">
                                            <input type="hidden" id="selected_customer_address_id"
                                                name="selected_customer_address_id">
                                            <div id="customer-dropdown" class="custom-dropdown"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Customer Address</label>
                                            <select id="party-address" class="form-select"></select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <p><strong>Contact:</strong> <span id="party-contact-person">N/A</span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Phone:</strong> <span id="party-phone">N/A</span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Email:</strong> <span id="party-email">N/A</span></p>
                                        </div>
                                    </div>

                                    {{-- Department & Sample Type --}}
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Department <span class="text-danger">*</span></label>
                                            <select name="dd_department" class="form-control" required>
                                                <option value="">Select Department</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->m13_department_id }}">
                                                        {{ $department->m13_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Sample Type <span class="text-danger">*</span></label>
                                            <select name="dd_sample_type" class="form-control" required>
                                                <option value="">Select Sample Type</option>
                                                @foreach ($labSamples as $sample)
                                                    <option value="{{ $sample->m14_lab_sample_id }}">
                                                        {{ $sample->m14_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Priority & Reference --}}
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Priority</label>
                                            <select name="dd_priority_type" class="form-control" required>
                                                <option value="Normal">Normal</option>
                                                <option value="Tatkal">Tatkal</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Reference No</label>
                                            <input type="text" name="txt_reference" class="form-control" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Reference Date</label>
                                            <input type="date" name="txt_ref_date" class="form-control"
                                                value="{{ now()->format('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Register Instantly</button>
                                    </div>
                                </form>
                            </div> {{-- card-body --}}
                        </div> {{-- card --}}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .custom-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 9999;
            background: white;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        .custom-dropdown-item {
            padding: 6px 10px;
            cursor: pointer;
        }

        .custom-dropdown-item:hover,
        .custom-dropdown-item.active {
            background-color: #f1f1f1;
        }

        .dropdown-message {
            padding: 6px 10px;
            color: #888;
        }
    </style>

    <script>
        $(document).ready(function() {
            let searchTimeout = null;
            let activeIndex = -1; // for keyboard navigation
            const $customerInput = $('#txt_customer_name');
            const $dropdown = $('#customer-dropdown');
            const CUSTOMER_URL = "{{ route('search_customer') }}";

            // --- Live Search ---
            $customerInput.on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(searchTimeout);
                activeIndex = -1;

                if (query.length < 2) {
                    $dropdown.hide().empty();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    $dropdown.show().html('<div class="dropdown-message">Searching...</div>');

                    $.getJSON(CUSTOMER_URL, {
                        query
                    }, function(customers) {
                        $dropdown.empty();
                        if (customers.length) {
                            customers.forEach((c, index) => {
                                $('<div>')
                                    .addClass('custom-dropdown-item')
                                    .text(c.name)
                                    .data('customer', c)
                                    .appendTo($dropdown);
                            });
                        } else {
                            $dropdown.html(
                                '<div class="dropdown-message">No customers found.</div>'
                            );
                        }
                    }).fail(() => {
                        $dropdown.html(
                            '<div class="dropdown-message text-danger">Error fetching customers</div>'
                        );
                    });
                }, 300);
            });

            // --- Select Customer (click) ---
            $(document).on('click', '#customer-dropdown .custom-dropdown-item', function() {
                selectCustomer($(this).data('customer'));
            });

            // --- Keyboard navigation ---
            $customerInput.on('keydown', function(e) {
                const $items = $dropdown.find('.custom-dropdown-item');
                if (!$items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = (activeIndex + 1) % $items.length;
                    highlightItem($items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = (activeIndex - 1 + $items.length) % $items.length;
                    highlightItem($items);
                } else if (e.key === 'Enter' && activeIndex >= 0) {
                    e.preventDefault();
                    selectCustomer($items.eq(activeIndex).data('customer'));
                }
            });

            function highlightItem($items) {
                $items.removeClass('active');
                $items.eq(activeIndex).addClass('active');
            }

            function selectCustomer(customer) {
                if (!customer) return;
                $customerInput.val(customer.name);
                $('#selected_customer_id').val(customer.id);

                // populate addresses
                const $addressSelect = $('#party-address');
                $addressSelect.empty();

                let addresses = [];
                if (customer.default_address) {
                    addresses.push({
                        ...customer.default_address,
                        id: 'default'
                    });
                }
                if (customer.other_addresses?.length) {
                    addresses = addresses.concat(customer.other_addresses);
                }

                if (addresses.length) {
                    addresses.forEach(addr => {
                        $('<option>')
                            .val(addr.id)
                            .text(addr.address || 'No address')
                            .data('contact_person', addr.contact_person)
                            .data('phone', addr.phone)
                            .data('email', addr.email)
                            .appendTo($addressSelect);
                    });
                    updateContactDetails($addressSelect.find('option:selected'), customer.id);
                } else {
                    $addressSelect.append('<option value="">No address found</option>');
                    updateContactDetails($('<option>'), customer.id);
                }

                $dropdown.hide().empty();
            }

            $('#party-address').on('change', function() {
                updateContactDetails($(this).find('option:selected'), $('#selected_customer_id').val());
            });

            function updateContactDetails($option, customerId) {
                $('#party-contact-person').text($option.data('contact_person') || 'N/A');
                $('#party-phone').text($option.data('phone') || 'N/A');
                $('#party-email').text($option.data('email') || 'N/A');
                $('#selected_customer_address_id').val($option.val() || '');
            }

            // Hide dropdown if clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#txt_customer_name, #customer-dropdown').length) {
                    $dropdown.hide();
                }
            });
        });
    </script>
@endsection
