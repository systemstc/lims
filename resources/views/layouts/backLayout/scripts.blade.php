<script src="{{ asset('backAssets/js/bundle.js') }}"></script>
<script src="{{ asset('backAssets/js/scripts.js') }}"></script>
<link rel="stylesheet" href="{{ asset('backAssets/css/editors/summernote.css') }}">
<script src="{{ asset('backAssets/js/libs/editors/summernote.js') }}"></script>
<script src="{{ asset('backAssets/js/editors.js') }}"></script>
<script src="{{ asset('backAssets/js/libs/datatable-btns.js') }}"></script>
<script src="{{ asset('backAssets/js/example-toastr.js') }}"></script>
<script src="{{ asset('backAssets/js/example-sweetalert.js') }}"></script>
<script src="{{ asset('backAssets/js/charts/custom-charts.js') }}"></script>
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

    // Global Form Submission Lock
    document.addEventListener('DOMContentLoaded', function() {
        // Handle standard form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;

            // Allow if explicitly marked to bypass
            if (form.dataset.bypassLock === 'true') return;

            // If already submitting, STOP EVERYTHING
            if (form.dataset.isSubmitting === 'true') {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
                return false;
            }

            // If form is valid (checkValidity is native)
            // Note: If novalidate is present, checkValidity() might still return true or be ignored.
            if (form.checkValidity && !form.checkValidity()) {
                // Let the browser show validation errors
                return;
            }

            // Lock the form
            form.dataset.isSubmitting = 'true';

            // Visual feedback - Disable all submit buttons and related anchors
            const submitters = form.querySelectorAll(
                'button[type="submit"], input[type="submit"], a[href="#finish"]');
            submitters.forEach(function(btn) {
                // Store original text/width to avoid layout jump if possible (optional)
                if (!btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;

                btn.classList.add('disabled');
                btn.classList.add('submitting');
                // For input[type=submit] or button, set disabled
                if (btn.tagName !== 'A') btn.disabled = true;
                // For anchors, pointer-events usually does the trick via CSS class, but we can enforce
                btn.style.pointerEvents = 'none';

                // Optional: Change text to processing if it's not an icon-only button
                // if (btn.innerText.trim().length > 0) {
                //    btn.innerText = 'Processing...'; 
                // }
            });
        }, true); // Use capture phase to catch it early

        // Re-enable on page show (bfcache/back button)
        window.addEventListener('pageshow', function(event) {
            const forms = document.querySelectorAll('form[data-is-submitting="true"]');
            forms.forEach(function(form) {
                delete form.dataset.isSubmitting;
                const submitters = form.querySelectorAll('.submitting');
                submitters.forEach(function(btn) {
                    btn.classList.remove('disabled');
                    btn.classList.remove('submitting');
                    if (btn.tagName !== 'A') btn.disabled = false;
                    btn.style.pointerEvents = '';
                    // if(btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
                });
            });
        });
    });

    // Script to teggle DashLite dark/light mode
    document.addEventListener('DOMContentLoaded', function() {
        // Load saved theme
        const savedTheme = localStorage.getItem('theme-mode') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }

        document.querySelectorAll('.dark-switch').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const isDark = document.body.classList.contains('dark-mode');
                const newTheme = isDark ? 'light' : 'dark';

                // Save to localStorage
                localStorage.setItem('theme-mode', newTheme);

                // Reload the page to apply theme
                window.location.reload();
            });
        });
    });
</script>
