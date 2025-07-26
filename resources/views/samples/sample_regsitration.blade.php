@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    {{-- <div class="nk-block-head nk-block-head-lg wide-sm">
                        <div class="nk-block-head-content">
                            <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em
                                        class="icon ni ni-arrow-left"></em><span>Components</span></a></div>
                            <h2 class="nk-block-title fw-normal">Form Elements - Dynamic Search</h2>
                        </div>
                    </div> --}}
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="row gy-4">
                                    <div class="col-sm-8"> {{-- Adjusted column size for main content --}}
                                        <div class="form-group mb-4">
                                            <label for="nameSearchInput" class="form-label">Search Name (for Dynamic Select & Sidebar)</label> {{-- Clarified label --}}
                                            <div class="form-control-wrap">
                                                <input type="text" id="nameSearchInput" class="form-control form-control-lg"
                                                    placeholder="Type to search...">
                                            </div>
                                        </div>

                                        {{-- ORIGINAL DYNAMIC SELECT2: Remains as is, updated by AJAX --}}
                                        <div class="form-group">
                                            <label class="form-label">Select Name (Dynamic AJAX Results)</label>
                                            <div class="form-control-wrap">
                                                {{-- Kept original ID and classes --}}
                                                <select class="form-select w-full js-select2" data-search="on"
                                                    id="dynamicSelect2">
                                                    <option value="">Start typing to see results</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- NEW PRE-LOADED SELECT2: Populated from $customers --}}
                                        <div class="form-group">
                                            <label class="form-label">Select Customer (Pre-loaded from Database)</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select w-full js-select2" data-search="on" id="preloadedCustomerSelect"> {{-- NEW UNIQUE ID --}}
                                                    <option value="">Select a customer</option> {{-- Default placeholder --}}
                                                    @isset($customers)
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Sidebar for JSON Response --}}
                                    <div class="col-sm-4"> {{-- Column for the sidebar --}}
                                        <div class="card card-bordered" style="min-height: 300px;">
                                            <div class="card-inner">
                                                <h5 class="card-title">Search Results (from AJAX)</h5>
                                                <div id="jsonResponseSidebar">
                                                    @if (isset($searchResults) && !empty($searchResults))
                                                        <ul class="list-group">
                                                            @foreach($searchResults as $result)
                                                                <li class="list-group-item">
                                                                    <strong>ID:</strong> {{ $result['id'] }}<br>
                                                                    <strong>Name:</strong> {{ $result['name'] }}<br>
                                                                    <strong>Match Type:</strong> {{ $result['match_type'] }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No search results to display yet.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // INITIALIZE ORIGINAL DYNAMIC SELECT2 (ID: dynamicSelect2)
            // This setup allows its options to be cleared and re-added by AJAX.
            $('#dynamicSelect2').select2({
                placeholder: "Select a name",
                allowClear: true,
                minimumResultsForSearch: -1 // Hides the search box within Select2's dropdown
            });

            // INITIALIZE NEW PRE-LOADED SELECT2 (ID: preloadedCustomerSelect)
            // This one is populated by Blade/PHP when the page loads.
            $('#preloadedCustomerSelect').select2({
                placeholder: "Select a customer",
                allowClear: true,
                // data-search="on" class on the select is generally handled by the Nio-Dash-Lite scripts
                // if you want to explicitly ensure the search box is always visible:
                // minimumResultsForSearch: 0
            });


            let typingTimer; // Timer identifier for debouncing
            const doneTypingInterval = 500; // Time in milliseconds to wait after user stops typing

            // Event listener for the custom search input field
            $('#nameSearchInput').on('keyup', function() {
                clearTimeout(typingTimer); // Clear any existing timer
                const searchTerm = $(this).val(); // Get the current value from the input

                // Only make an API call if the search term is not empty
                if (searchTerm.length > 0) {
                    // Set a new timer to execute the AJAX call after the user stops typing
                    typingTimer = setTimeout(function() {
                        $.ajax({
                            // The URL to your Laravel route for searching names
                            url: "{{ route('search.names') }}",
                            method: 'GET',
                            data: {
                                query: searchTerm
                            }, // Send the search term as a 'query' parameter
                            success: function(data) {
                                // Logic to update the ORIGINAL DYNAMIC SELECT2 (#dynamicSelect2)
                                $('#dynamicSelect2').empty();
                                $('#dynamicSelect2').append(new Option('Select a name', ''));

                                if (data && Array.isArray(data) && data.length > 0) {
                                    $.each(data, function(index, item) {
                                        
                                            $('#dynamicSelect2').append(new Option(item.name, item.id));

                                       // Use item.NAME for text, item.id for value
                                    });
                                } else {
                                    $('#dynamicSelect2').append(new Option(
                                        'No results found', ''));
                                }
                                $('#dynamicSelect2').trigger('change'); // Trigger Select2 to update its display

                                // Update the sidebar with the JSON response
                                updateSidebar(data);
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX Error: ", status, error);
                                console.log(xhr.responseText);
                                // Reset original dynamic select on error
                                $('#dynamicSelect2').empty().append(new Option(
                                    'Error loading results', '')).trigger('change');
                                // Clear sidebar on error
                                updateSidebar([]);
                            }
                        });
                    }, doneTypingInterval);
                } else {
                    // If the search term is empty, clear the ORIGINAL dynamic dropdown and sidebar
                    $('#dynamicSelect2').empty().append(new Option('Start typing to see results', ''))
                        .trigger('change');
                    updateSidebar([]); // Clear sidebar
                }
            });

            // Function to update the sidebar content (remains the same)
            function updateSidebar(data) {
                const sidebar = $('#jsonResponseSidebar');
                sidebar.empty(); // Clear previous content

                if (data && Array.isArray(data) && data.length > 0) {
                    const ul = $('<ul class="list-group"></ul>');
                    $.each(data, function(index, item) {
                        const li = $(`
                            <li class="list-group-item">
                                <strong>ID:</strong> ${item.id}<br>
                                <strong>Name:</strong> ${item.name}<br>
                                <strong>Match Type:</strong> ${item.match_type}
                            </li>
                        `);
                        ul.append(li);
                    });
                    sidebar.append(ul);
                } else {
                    sidebar.append('<p>No search results to display yet.</p>');
                }
            }
        });
    </script>
@endsection