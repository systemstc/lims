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
                                                <label class="form-label" for="txt_customer_type_id">Customer Type<b class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_customer_type_id" class="form-select" id="txt_customer_type_id" required
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
                                                            value="{{ old('txt_phone') }}" required>
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
                                                                {{ $state->m01_name }}</option>
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
                                                        {{-- Populated via AJAX --}}
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
                                            <button type="button" class="btn btn-secondary"
                                                onclick="addContactBlock()">+ Add More</button>
                                        </div>

                                        @php
                                            // Render old contact blocks only if they exist from a previous submission
                                            $contact_blocks = old('contacts', []);
                                        @endphp

                                        @foreach ($contact_blocks as $index => $contact)
                                            <div class="contact-block-item border-top pt-3 mt-3">
                                                <div class="row g-gs">
                                                    <div class="col-md-4">
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
                                                    <div class="col-md-4">
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
                                                    <div class="col-md-4">
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
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">State</label>
                                                            <select class="form-select contact-state-select"
                                                                name="contacts[{{ $index }}][state_id]"
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
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">District</label>
                                                            <select class="form-select contact-district-select"
                                                                name="contacts[{{ $index }}][district_id]"
                                                                data-old-district="{{ $contact['district_id'] ?? '' }}"
                                                                data-placeholder="-- Select District --">
                                                                {{-- Populated via AJAX --}}
                                                            </select>
                                                            @error('contacts.' . $index . '.district_id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
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
                                                    <div class="col-md-8">
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
                                                    <div class="col-md-4 d-flex align-items-end">
                                                        <div class="form-group">
                                                            <button type="button"
                                                                class="btn btn-danger remove-contact-block">Remove</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Save Customer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div><!-- .nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>

    {{-- Hidden Template for new contact blocks --}}
    <div id="contact-template" style="display: none;">
        <div class="contact-block-item border-top pt-3 mt-3">
            <div class="row g-gs">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control" name="contacts[__INDEX__][name]"
                            placeholder="Enter contact person's Name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="contacts[__INDEX__][email]"
                            placeholder="Email">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="contacts[__INDEX__][phone]" class="form-control"
                            placeholder="Phone">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <select class="form-select contact-state-select" name="contacts[__INDEX__][state_id]"
                            data-placeholder="-- Select State --">
                            <option value=""></option>
                            @foreach ($states as $state)
                                <option value="{{ $state->m01_state_id }}">{{ $state->m01_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">District</label>
                        <select class="form-select contact-district-select" name="contacts[__INDEX__][district_id]"
                            data-placeholder="-- Select District --">
                            {{-- Populated via AJAX --}}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" class="form-control" name="contacts[__INDEX__][pincode]"
                            placeholder="Pincode">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input name="contacts[__INDEX__][address]" placeholder="Address" class="form-control">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group">
                        <button type="button" class="btn btn-danger remove-contact-block">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Initialize the index for new contact blocks based on what's already rendered from old input.
            let contactIndex = {{ count($contact_blocks) }};

            window.addContactBlock = function() {
                // Clone the hidden template
                const template = document.getElementById('contact-template').innerHTML;
                const newBlockHtml = template.replace(/__INDEX__/g, contactIndex);

                // Append the new block to the wrapper
                $('#contacts-wrapper').append(newBlockHtml);

                // Re-initialize select2 on the new elements if you are using it
                // $('#contacts-wrapper').find('.form-select').select2();

                contactIndex++;
            }

            // Add event listener for removing a contact block
            $(document).on('click', '.remove-contact-block', function() {
                $(this).closest('.contact-block-item').remove();
            });

            // Function to fetch districts
            function fetchDistricts(stateId, districtSelect, oldDistrictId = null) {
                if (stateId) {
                    districtSelect.html('<option value="">Loading...</option>').trigger('change');
                    $.ajax({
                        url: '{{ route('get_districts') }}',
                        type: 'GET',
                        data: {
                            state_id: stateId
                        },
                        success: function(response) {
                            let options = '<option value=""></option>';
                            $.each(response, function(key, district) {
                                options +=
                                    `<option value="${district.m02_district_id}">${district.m02_name}</option>`;
                            });
                            districtSelect.html(options);

                            // If there's an old district value, select it
                            if (oldDistrictId) {
                                districtSelect.val(oldDistrictId);
                            }
                            districtSelect.trigger('change'); // Notify select2 of update
                        },
                        error: function() {
                            districtSelect.html('<option value="">-- Error --</option>').trigger(
                                'change');
                        }
                    });
                } else {
                    districtSelect.html('<option value=""></option>').trigger('change');
                }
            }

            // --- Event handler for the MAIN form's state dropdown ---
            $('#txt_state_id').on('change', function() {
                const stateId = $(this).val();
                const districtSelect = $('#txt_district_id');
                fetchDistricts(stateId, districtSelect);
            });

            // --- Event handler for DYNAMIC contact block state dropdowns ---
            $(document).on('change', '.contact-state-select', function() {
                const stateId = $(this).val();
                const districtSelect = $(this).closest('.contact-block-item').find(
                    '.contact-district-select');
                fetchDistricts(stateId, districtSelect);
            });

            // --- On page load, trigger changes for any pre-selected values (from validation fail) ---
            // For main form
            if ($('#txt_state_id').val()) {
                const oldDistrictId = "{{ old('txt_district_id') }}";
                fetchDistricts($('#txt_state_id').val(), $('#txt_district_id'), oldDistrictId);
            }

            // For dynamic contact blocks that were re-rendered after a validation error
            $('.contact-block-item').each(function() {
                const stateSelect = $(this).find('.contact-state-select');
                const districtSelect = $(this).find('.contact-district-select');
                const stateId = stateSelect.val();
                const oldDistrictId = districtSelect.data('old-district');

                if (stateId) {
                    fetchDistricts(stateId, districtSelect, oldDistrictId);
                }
            });
        });
    </script>
@endsection
