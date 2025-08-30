<script src="{{ asset('backAssets/js/bundle.js') }}"></script>
<script src="{{ asset('backAssets/js/scripts.js') }}"></script>
<script src="{{ asset('backAssets/js/libs/datatable-btns.js') }}"></script>
<script src="{{ asset('backAssets/js/example-toastr.js') }}"></script>
<script src="{{ asset('backAssets/js/example-sweetalert.js') }}"></script>
<script src="{{ asset('backAssets/js/charts/chart-ecommerce.js') }}"></script>
<script>
    // Validation
    function validateField(field, value, inputId, model) {
        if (value.length === 0) {
            $("#" + inputId + "-msg").text("");
            return;
        }
        $.ajax({
            url: "{{ route('validate_field') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                field: field,
                value: value,
                model: model,
            },
            success: function(res) {
                if (res.exists) {
                    $("#" + inputId + "-msg").text(
                        "This " + inputId.replace("txt_", "").charAt(0).toUpperCase() +
                        inputId.replace("txt_", "").slice(1) + " already exists!"
                    );
                } else {
                    $("#" + inputId + "-msg").text("");
                }
            }
        });
    }

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
</script>
