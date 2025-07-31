@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Add Customer</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Goto Sample Reg.
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('create_customer') }}" class="form-validate is-alter" method="POST">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Primary Customer Details --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_customer_type_id">Customer Type<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_customer_type_id" class="form-select"
                                                        id="txt_customer_type_id" required
                                                        data-placeholder="-- Select Customer Type --">
                                                        <option value=""></option>
                                                        @foreach ($customerTypes as $customerType)
                                                            <option value="{{ $customerType->m09_customer_type_id }}"
                                                                {{ old('txt_customer_type_id') == $customerType->m09_customer_type_id ? 'selected' : '' }}>
                                                                {{ $customerType->m09_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_customer_type_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <input type="hidden" name="txt_ro_id" id="txt_ro_id"
                                            value="{{ Session::get('ro_id') }}">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_name">Name of Customer<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_name"
                                                        name="txt_name" value="{{ old('txt_name') }}" required>
                                                </div>
                                                @error('txt_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_email">Email address<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="email" class="form-control" id="txt_email"
                                                        name="txt_email" value="{{ old('txt_email') }}" required>
                                                </div>
                                                @error('txt_email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_phone">Phone<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="txt_phone_addon">+91</span>
                                                        </div>
                                                        <input type="text" name="txt_phone" class="form-control"
                                                            id="txt_phone" value="{{ old('txt_phone') }}" required>
                                                    </div>
                                                </div>
                                                @error('txt_phone')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_gst">GST Number</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_gst" name="txt_gst"
                                                        value="{{ old('txt_gst') }}">
                                                </div>
                                                @error('txt_gst')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_iec">IEC Code</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_iec" name="txt_iec"
                                                        value="{{ old('txt_iec') }}">
                                                </div>
                                                @error('txt_iec')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_contact_person">Contact Person<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_contact_person"
                                                        name="txt_contact_person" value="{{ old('txt_contact_person') }}"
                                                        required>
                                                </div>
                                                @error('txt_contact_person')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_state_id">State<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_state_id" class="form-select" id="txt_state_id"
                                                        required data-placeholder="-- Select State --">
                                                        <option value=""></option>
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->m01_state_id }}"
                                                                {{ old('txt_state_id') == $state->m01_state_id ? 'selected' : '' }}>
                                                                {{ $state->m01_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_state_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_district_id">District<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_district_id" class="form-select"
                                                        id="txt_district_id" required
                                                        data-placeholder="-- Select District --">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                @error('txt_district_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_pincode">Pincode<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_pincode"
                                                        name="txt_pincode" value="{{ old('txt_pincode') }}" required>
                                                </div>
                                                @error('txt_pincode')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_address">Address<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-location"></em>
                                                    </div>
                                                    <input type="text" class="form-control" id="txt_address"
                                                        name="txt_address" value="{{ old('txt_address') }}" required>
                                                </div>
                                                @error('txt_address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Contact persons and multiple locations section --}}
                                    <div id="contacts-wrapper" class="border rounded p-3 mt-4">
                                        <p class="mb-2">
                                            <strong>Note <span class="text-danger">*</span>:</strong> If the customer
                                            operates from multiple office locations or has more than one contact person,
                                            please add them below. Each entry should include both the contact person's
                                            details and their associated office address.
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Contact Persons & Office Locations</h6>
                                            <button type="button" class="btn btn-secondary" id="add-contact-btn">
                                                <em class="icon ni ni-plus-circle-fill"></em> Add More
                                            </button>
                                        </div>

                                        @php
                                            $contact_blocks = old('contacts', []);
                                        @endphp

                                        <div id="contact-container">
                                            @foreach ($contact_blocks as $index => $contact)
                                                <div class="contact-block-item border-top pt-3 mt-3"
                                                    data-index="{{ $index }}">
                                                    <div class="row g-gs">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Contact Person</label>
                                                                <input type="text" class="form-control"
                                                                    name="contacts[{{ $index }}][name]"
                                                                    value="{{ $contact['name'] ?? '' }}"
                                                                    placeholder="Enter contact person's Name">
                                                                @error('contacts.' . $index . '.name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" class="form-control"
                                                                    name="contacts[{{ $index }}][email]"
                                                                    value="{{ $contact['email'] ?? '' }}"
                                                                    placeholder="Email">
                                                                @error('contacts.' . $index . '.email')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Phone Number</label>
                                                                <input type="text"
                                                                    name="contacts[{{ $index }}][phone]"
                                                                    class="form-control"
                                                                    value="{{ $contact['phone'] ?? '' }}"
                                                                    placeholder="Phone">
                                                                @error('contacts.' . $index . '.phone')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">GST Number</label>
                                                                <input type="text" class="form-control"
                                                                    name="contacts[{{ $index }}][gst]"
                                                                    value="{{ $contact['gst'] ?? '' }}"
                                                                    placeholder="GST Number">
                                                                @error('contacts.' . $index . '.gst')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">State</label>
                                                                <select class="form-select contact-state-select"
                                                                    name="contacts[{{ $index }}][state_id]"
                                                                    data-index="{{ $index }}"
                                                                    data-placeholder="-- Select State --">
                                                                    <option value=""></option>
                                                                    @foreach ($states as $state)
                                                                        <option value="{{ $state->m01_state_id }}"
                                                                            {{ isset($contact['state_id']) && $contact['state_id'] == $state->m01_state_id ? 'selected' : '' }}>
                                                                            {{ $state->m01_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('contacts.' . $index . '.state_id')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">District</label>
                                                                <select class="form-select contact-district-select"
                                                                    name="contacts[{{ $index }}][district_id]"
                                                                    data-index="{{ $index }}"
                                                                    data-old-district="{{ $contact['district_id'] ?? '' }}"
                                                                    data-placeholder="-- Select District --">
                                                                    <option value=""></option>
                                                                </select>
                                                                @error('contacts.' . $index . '.district_id')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Pincode</label>
                                                                <input type="text" class="form-control"
                                                                    name="contacts[{{ $index }}][pincode]"
                                                                    value="{{ $contact['pincode'] ?? '' }}"
                                                                    placeholder="Pincode">
                                                                @error('contacts.' . $index . '.pincode')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Address</label>
                                                                <input name="contacts[{{ $index }}][address]"
                                                                    placeholder="Address" class="form-control"
                                                                    value="{{ $contact['address'] ?? '' }}">
                                                                @error('contacts.' . $index . '.address')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-contact-btn">
                                                                <em class="icon ni ni-trash-fill"></em> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-lg btn-primary">Save Customer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Template for new contact blocks --}}
    <script type="text/template" id="contact-template">
        <div class="contact-block-item border-top pt-3 mt-3" data-index="__INDEX__">
            <div class="row g-gs">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control" name="contacts[__INDEX__][name]"
                            placeholder="Enter contact person's Name">
                        <span class="text-danger error-message" data-field="name"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="contacts[__INDEX__][email]"
                            placeholder="Email">
                        <span class="text-danger error-message" data-field="email"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="contacts[__INDEX__][phone]" class="form-control"
                            placeholder="Phone">
                        <span class="text-danger error-message" data-field="phone"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">GST Number</label>
                        <input type="text" class="form-control" name="contacts[__INDEX__][gst]"
                            placeholder="GST Number">
                        <span class="text-danger error-message" data-field="gst"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <select class="form-select contact-state-select" name="contacts[__INDEX__][state_id]"
                            data-index="__INDEX__" data-placeholder="-- Select State --">
                            <option value=""></option>
                            @foreach ($states as $state)
                                <option value="{{ $state->m01_state_id }}">{{ $state->m01_name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-message" data-field="state_id"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">District</label>
                        <select class="form-select contact-district-select" name="contacts[__INDEX__][district_id]"
                            data-index="__INDEX__" data-placeholder="-- Select District --">
                            <option value=""></option>
                        </select>
                        <span class="text-danger error-message" data-field="district_id"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" class="form-control" name="contacts[__INDEX__][pincode]"
                            placeholder="Pincode">
                        <span class="text-danger error-message" data-field="pincode"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input name="contacts[__INDEX__][address]" placeholder="Address" class="form-control">
                        <span class="text-danger error-message" data-field="address"></span>
                    </div>
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-danger btn-sm remove-contact-btn">
                        <em class="icon ni ni-trash-fill"></em> Remove
                    </button>
                </div>
            </div>
        </div>
    </script>

    <script>
        $(document).ready(function() {
            // Global variables
            let contactIndex = {{ count($contact_blocks) }};

            // Utility function to fetch districts
            function fetchDistricts(stateId, districtSelect, selectedDistrictId = null) {
                if (stateId) {
                    districtSelect.html('<option value="">Loading...</option>');
                    $.ajax({
                        url: '{{ route('get_districts') }}',
                        type: 'GET',
                        data: {
                            state_id: stateId
                        },
                        success: function(response) {
                            let options = '<option value=""></option>';
                            $.each(response, function(index, district) {
                                const isSelected = selectedDistrictId == district
                                    .m02_district_id ? 'selected' : '';
                                options +=
                                    `<option value="${district.m02_district_id}" ${isSelected}>${district.m02_name}</option>`;
                            });
                            districtSelect.html(options);
                        },
                        error: function() {
                            districtSelect.html('<option value="">-- Error --</option>');
                        }
                    });
                } else {
                    districtSelect.html('<option value=""></option>');
                }
            }

            // Initialize districts on page load
            function initializeAllDistricts() {
                // Main form district
                const mainStateSelect = $('#txt_state_id');
                if (mainStateSelect.val()) {
                    const mainDistrictSelect = $('#txt_district_id');
                    const initialMainDistrictId = "{{ old('txt_district_id') }}";
                    fetchDistricts(mainStateSelect.val(), mainDistrictSelect, initialMainDistrictId);
                }

                // Contact districts
                $('.contact-state-select').each(function() {
                    const stateId = $(this).val();
                    if (stateId) {
                        const index = $(this).data('index');
                        const districtSelect = $(`.contact-district-select[data-index='${index}']`);
                        const selectedDistrictId = districtSelect.data('old-district');
                        fetchDistricts(stateId, districtSelect, selectedDistrictId);
                    }
                });
            }

            // Event handlers
            $('#txt_state_id').on('change', function() {
                fetchDistricts($(this).val(), $('#txt_district_id'));
            });

            $('#contact-container').on('change', '.contact-state-select', function() {
                const index = $(this).data('index');
                const districtSelect = $(`.contact-district-select[data-index='${index}']`);
                fetchDistricts($(this).val(), districtSelect);
            });

            // Add new contact block
            $('#add-contact-btn').on('click', function() {
                const newIndex = contactIndex++;
                const template = $('#contact-template').html().replace(/__INDEX__/g, newIndex);
                $('#contact-container').append(template);
            });

            // Remove contact block
            $('#contact-container').on('click', '.remove-contact-btn', function() {
                $(this).closest('.contact-block-item').remove();
            });

            // Initialize on page load
            initializeAllDistricts();
        });
    </script>
@endsection
