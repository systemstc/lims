@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                            <h4 class="nk-block-title mb-0">Edit Customer</h4>
                            <a href="{{ route('customers') }}" class="btn btn-primary">
                                <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                            </a>
                        </div>
                    </div>

                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                <form action="{{ route('update_customer', $customer['m07_customer_id']) }}" method="POST"
                                    class="form-validate is-alter">
                                    @csrf
                                    <div class="row g-gs">
                                        {{-- Primary Customer Details --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_customer_type_id">Customer Type<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_edit_customer_type_id" id="txt_edit_customer_type_id"
                                                        class="form-select" required
                                                        data-placeholder="-- Select Customer Type --">
                                                        <option value=""></option>
                                                        @foreach ($customerTypes as $type)
                                                            <option value="{{ $type->m09_customer_type_id }}"
                                                                {{ old('txt_edit_customer_type_id', $customer['m09_customer_type_id']) == $type->m09_customer_type_id ? 'selected' : '' }}>
                                                                {{ $type->m09_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_edit_customer_type_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_name">Customer Name<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_name"
                                                        name="txt_edit_name"
                                                        value="{{ old('txt_edit_name', $customer['m07_name']) }}" required>
                                                </div>
                                                @error('txt_edit_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_email">Email<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="email" class="form-control" id="txt_edit_email"
                                                        name="txt_edit_email"
                                                        value="{{ old('txt_edit_email', $customer['m07_email']) }}"
                                                        required>
                                                </div>
                                                @error('txt_edit_email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_phone">Phone<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"
                                                                id="txt_edit_phone_addon">+91</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="txt_edit_phone"
                                                            name="txt_edit_phone"
                                                            value="{{ old('txt_edit_phone', $customer['m07_phone']) }}"
                                                            required>
                                                    </div>
                                                </div>
                                                @error('txt_edit_phone')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_gst">GST Number</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_gst"
                                                        name="txt_edit_gst"
                                                        value="{{ old('txt_edit_gst', $customer['m07_gst']) }}">
                                                </div>
                                                @error('txt_edit_gst')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_iec">IEC Code</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_iec"
                                                        name="txt_edit_iec"
                                                        value="{{ old('txt_edit_iec', $customer['m07_iec_code']) }}">
                                                </div>
                                                @error('txt_edit_iec')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_contact_person">Contact Person<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_contact_person"
                                                        name="txt_edit_contact_person"
                                                        value="{{ old('txt_edit_contact_person', $customer['m07_contact_person']) }}"
                                                        required>
                                                </div>
                                                @error('txt_edit_contact_person')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_state_id">State<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_edit_state_id" id="txt_edit_state_id"
                                                        class="form-select" required
                                                        data-placeholder="-- Select State --">
                                                        <option value=""></option>
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->m01_state_id }}"
                                                                {{ old('txt_edit_state_id', $customer['m01_state_id']) == $state->m01_state_id ? 'selected' : '' }}>
                                                                {{ $state->m01_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('txt_edit_state_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_district_id">District<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <select name="txt_edit_district_id" id="txt_edit_district_id"
                                                        class="form-select" required
                                                        data-placeholder="-- Select District --">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                @error('txt_edit_district_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_pincode">Pincode<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="txt_edit_pincode"
                                                        name="txt_edit_pincode"
                                                        value="{{ old('txt_edit_pincode', $customer['m07_pincode']) }}"
                                                        required>
                                                </div>
                                                @error('txt_edit_pincode')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="form-label" for="txt_edit_address">Address<b
                                                        class="text-danger">*</b></label>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-location"></em>
                                                    </div>
                                                    <input type="text" class="form-control" id="txt_edit_address"
                                                        name="txt_edit_address"
                                                        value="{{ old('txt_edit_address', $customer['m07_address']) }}"
                                                        required>
                                                </div>
                                                @error('txt_edit_address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    {{-- Locations Section --}}
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <h5>Locations & Contact Persons</h5>
                                        <button type="button" class="btn btn-sm btn-primary" id="add-location-btn">
                                            <em class="icon ni ni-plus-circle-fill"></em> &nbsp; Add New Location
                                        </button>
                                    </div>

                                    <div id="customer-locations" class="mt-2">
                                        @php
                                            $existingLocations = old('locations', $customer['locations'] ?? []);
                                        @endphp
                                        @foreach ($existingLocations as $index => $location)
                                            <div class="row g-gs align-items-end mb-5 border rounded p-2 location-block"
                                                data-index="{{ $index }}">
                                                <input type="hidden" name="locations[{{ $index }}][id]"
                                                    value="{{ $location['m08_customer_location_id'] ?? '' }}">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Contact Person</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                name="locations[{{ $index }}][contact_person]"
                                                                value="{{ old("locations.$index.contact_person", $location['m08_contact_person'] ?? '') }}">
                                                        </div>
                                                        @error("locations.$index.contact_person")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control"
                                                            name="locations[{{ $index }}][email]"
                                                            value="{{ old("locations.$index.email", $location['m08_email'] ?? '') }}">
                                                        @error("locations.$index.email")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Phone</label>
                                                        <input type="text" class="form-control"
                                                            name="locations[{{ $index }}][phone]"
                                                            value="{{ old("locations.$index.phone", $location['m08_phone'] ?? '') }}">
                                                        @error("locations.$index.phone")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">GST</label>
                                                        <input type="text" class="form-control"
                                                            name="locations[{{ $index }}][gst]"
                                                            value="{{ old("locations.$index.gst", $location['m08_gst'] ?? '') }}">
                                                        @error("locations.$index.gst")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">State</label>
                                                        <select name="locations[{{ $index }}][state_id]"
                                                            class="form-select state-select"
                                                            data-index="{{ $index }}">
                                                            <option value=""></option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->m01_state_id }}"
                                                                    {{ old("locations.$index.state_id", $location['m01_state_id'] ?? '') == $state->m01_state_id ? 'selected' : '' }}>
                                                                    {{ $state->m01_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("locations.$index.state_id")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">District</label>
                                                        <select name="locations[{{ $index }}][district_id]"
                                                            class="form-select district-select"
                                                            data-index="{{ $index }}"
                                                            data-selected-district="{{ old("locations.$index.district_id", $location['m02_district_id'] ?? '') }}">
                                                            <option value=""></option>
                                                        </select>
                                                        @error("locations.$index.district_id")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Pincode</label>
                                                        <input type="text" class="form-control"
                                                            name="locations[{{ $index }}][pincode]"
                                                            value="{{ old("locations.$index.pincode", $location['m08_pincode'] ?? '') }}">
                                                        @error("locations.$index.pincode")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" class="form-control"
                                                            name="locations[{{ $index }}][address]"
                                                            value="{{ old("locations.$index.address", $location['m08_address'] ?? '') }}">
                                                        @error("locations.$index.address")
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <button type="button"
                                                            class="btn btn-danger btn-icon remove-location-btn"
                                                            title="Remove Location">
                                                            <em class="icon ni ni-trash-fill"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-lg btn-primary">Update Customer</button>
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

    {{-- HIDDEN TEMPLATE FOR NEW LOCATIONS --}}
    <script type="text/template" id="location-template">
        <div class="row g-3 align-items-end mb-2 border rounded p-2 location-block" data-index="__INDEX__">
            <input type="hidden" name="locations[__INDEX__][id]" value="">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Contact Person<b class="text-danger">*</b></label>
                    <input type="text" class="form-control" name="locations[__INDEX__][contact_person]" required>
                    <span class="text-danger error-message" data-field="contact_person"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Email<b class="text-danger">*</b></label>
                    <input type="email" class="form-control" name="locations[__INDEX__][email]" required>
                    <span class="text-danger error-message" data-field="email"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Phone<b class="text-danger">*</b></label>
                    <input type="text" class="form-control" name="locations[__INDEX__][phone]" required>
                    <span class="text-danger error-message" data-field="phone"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">GST</label>
                    <input type="text" class="form-control" name="locations[__INDEX__][gst]">
                    <span class="text-danger error-message" data-field="gst"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">State<b class="text-danger">*</b></label>
                    <select name="locations[__INDEX__][state_id]" class="form-select state-select" data-index="__INDEX__" required>
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
                    <label class="form-label">District<b class="text-danger">*</b></label>
                    <select name="locations[__INDEX__][district_id]" class="form-select district-select" data-index="__INDEX__" required>
                        <option value=""></option>
                    </select>
                    <span class="text-danger error-message" data-field="district_id"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Pincode<b class="text-danger">*</b></label>
                    <input type="text" class="form-control" name="locations[__INDEX__][pincode]" required>
                    <span class="text-danger error-message" data-field="pincode"></span>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">Address<b class="text-danger">*</b></label>
                    <input type="text" class="form-control" name="locations[__INDEX__][address]" required>
                    <span class="text-danger error-message" data-field="address"></span>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-icon remove-location-btn" title="Remove Location">
                        <em class="icon ni ni-trash-fill"></em>
                    </button>
                </div>
            </div>
        </div>
    </script>

    <script>
        $(document).ready(function() {
            // Global variables
            let locationIndex = {{ count(old('locations', $customer['locations'] ?? [])) }};

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
                const mainStateSelect = $('#txt_edit_state_id');
                if (mainStateSelect.val()) {
                    const mainDistrictSelect = $('#txt_edit_district_id');
                    const initialMainDistrictId =
                        "{{ old('txt_edit_district_id', $customer['m02_district_id'] ?? '') }}";
                    fetchDistricts(mainStateSelect.val(), mainDistrictSelect, initialMainDistrictId);
                }

                // Location districts
                $('.state-select').each(function() {
                    const stateId = $(this).val();
                    if (stateId) {
                        const index = $(this).data('index');
                        const districtSelect = $(`.district-select[data-index='${index}']`);
                        const selectedDistrictId = districtSelect.data('selected-district');
                        fetchDistricts(stateId, districtSelect, selectedDistrictId);
                    }
                });
            }

            // Event handlers
            $('#txt_edit_state_id').on('change', function() {
                fetchDistricts($(this).val(), $('#txt_edit_district_id'));
            });

            $('#customer-locations').on('change', '.state-select', function() {
                const index = $(this).data('index');
                const districtSelect = $(`.district-select[data-index='${index}']`);
                fetchDistricts($(this).val(), districtSelect);
            });

            // Add new location
            $('#add-location-btn').on('click', function() {
                const newIndex = locationIndex++;
                const template = $('#location-template').html().replace(/__INDEX__/g, newIndex);
                $('#customer-locations').append(template);
            });

            // Remove location
            $('#customer-locations').on('click', '.remove-location-btn', function() {
                $(this).closest('.location-block').remove();
            });

            // Initialize on page load
            initializeAllDistricts();
        });
    </script>
@endsection
