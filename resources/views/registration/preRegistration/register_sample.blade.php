@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title mb-0">Register Sample</h4>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">
                                    <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Main Form Section -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-inner">
                                        <form action="{{ route('register_sample') }}" method="POST"
                                            class="nk-wizard nk-wizard-simple is-alter" id="wizard-01"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="nk-wizard-head">
                                                <h5>Customer Details</h5>
                                            </div>
                                            <div class="nk-wizard-content">
                                                <div class="row gy-3">
                                                    <div class="col-md-7">
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
                                                                        <option
                                                                            value="{{ $customerType->m09_customer_type_id }}">
                                                                            {{ $customerType->m09_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group dropdown-container"
                                                            style="position: relative;">
                                                            <label class="form-label" for="txt_customer_name">
                                                                Customer Name<b class="text-danger">*</b>
                                                            </label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control required"
                                                                    id="txt_customer_name" name="txt_customer_name"
                                                                    placeholder="Start typing customer name..."
                                                                    autocomplete="off" required>
                                                            </div>
                                                            <div class="d-flex justify-content-around"><span
                                                                    id="party-contact-person"></span><span
                                                                    id="party-phone"></span><span id="party-email"></span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="selected_customer_id"
                                                        name="selected_customer_id" value="">
                                                    <input type="hidden" id="selected_customer_address_id"
                                                        name="selected_customer_address_id" value="">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="party-address">Address</label>
                                                            <div class="form-control-wrap">
                                                                <span id="party-address"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_buyer_name">Buyer
                                                                Name</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" data-msg="Required"
                                                                    class="form-control" id="txt_buyer_name"
                                                                    name="txt_buyer_name" autocomplete="off">
                                                            </div>
                                                            <div class="d-flex justify-content-around"><span
                                                                    id="buyer-contact-person"></span><span
                                                                    id="buyer-phone"></span><span id="buyer-phone"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="selected_buyer_id" name="selected_buyer_id"
                                                        value="">
                                                    <input type="hidden" id="selected_buyer_address_id"
                                                        name="selected_buyer_address_id" value="">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="buyer-address">Address</label>
                                                            <div class="form-control-wrap">
                                                                <span id="buyer-address"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_third_party">Third
                                                                Party</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" data-msg="Required"
                                                                    class="form-control" id="txt_third_party"
                                                                    name="txt_third_party" autocomplete="off">
                                                            </div>
                                                            <div class="d-flex justify-content-around"><span
                                                                    id="third-contact-person"></span><span
                                                                    id="third-phone"></span><span id="third-phone"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="selected_third_party_id"
                                                        name="selected_third_party_id" value="">
                                                    <input type="hidden" id="selected_third_party_address_id"
                                                        name="selected_third_party_address_id" value="">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="third-address">Address</label>
                                                            <div class="form-control-wrap">
                                                                <span id="third-address"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_cha">CHA</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" data-msg="Required"
                                                                    class="form-control" id="txt_cha" name="txt_cha"
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="d-flex justify-content-around"><span
                                                                    id="cha-contact-person"></span><span
                                                                    id="cha-phone"></span><span id="cha-phone"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="selected_cha_id" name="selected_cha_id"
                                                        value="">
                                                    <input type="hidden" id="selected_cha_address_id"
                                                        name="selected_cha_address_id" value="">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="cha-address">Address</label>
                                                            <div class="form-control-wrap">
                                                                <span id="cha-address"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="preview-block">
                                                            <span class="preview-title overline-title d-block">Payment
                                                                By<b class="text-danger">*</b></span>
                                                            <div class="row">
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input required"
                                                                            id="firstParty" name="txt_payment_by"
                                                                            value="first_party" required>
                                                                        <label class="custom-control-label"
                                                                            for="firstParty">First Party</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="secondParty" name="txt_payment_by"
                                                                            value="second_party">
                                                                        <label class="custom-control-label"
                                                                            for="secondParty">Second Party</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="thirdParty" name="txt_payment_by"
                                                                            value="third_party">
                                                                        <label class="custom-control-label"
                                                                            for="thirdParty">Third Party</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="cha" name="txt_payment_by"
                                                                            value="cha">
                                                                        <label class="custom-control-label"
                                                                            for="cha">CHA</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="preview-block">
                                                            <span class="preview-title overline-title d-block">Report
                                                                To<b class="text-danger">*</b></span>
                                                            <div class="row">
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input required"
                                                                            id="reportFirstParty" name="txt_report_to"
                                                                            value="first_party" required>
                                                                        <label class="custom-control-label"
                                                                            for="reportFirstParty">First Party</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="reportSecondParty" name="txt_report_to"
                                                                            value="second_party">
                                                                        <label class="custom-control-label"
                                                                            for="reportSecondParty">Second Party</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="reportThirdParty" name="txt_report_to"
                                                                            value="third_party">
                                                                        <label class="custom-control-label"
                                                                            for="reportThirdParty">Third Party</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="reportCha" name="txt_report_to"
                                                                            value="cha">
                                                                        <label class="custom-control-label"
                                                                            for="reportCha">CHA</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- STEP 2: SAMPLE INFORMATION -->
                                            <div class="nk-wizard-head">
                                                <h5>Sample Information</h5>
                                            </div>
                                            <div class="nk-wizard-content">
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_reference">Reference
                                                                No</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_reference" name="txt_reference"
                                                                    autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_ref_date">Reference
                                                                Date</label>
                                                            <div class="form-control-wrap">
                                                                <input type="date" class="form-control"
                                                                    name="txt_ref_date" id="txt_ref_date"
                                                                    autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="preview-block">
                                                            <span
                                                                class="preview-title overline-title mt-1 d-block">Received
                                                                Through</span>
                                                            <div class="row">
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="byCourier" name="txt_received_via"
                                                                            value="by_courier">
                                                                        <label class="custom-control-label"
                                                                            for="byCourier">By Courier</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="byPost" name="txt_received_via"
                                                                            value="by_post">
                                                                        <label class="custom-control-label"
                                                                            for="byPost">By Post</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            id="byHand" name="txt_received_via"
                                                                            value="by_post">
                                                                        <label class="custom-control-label"
                                                                            for="byHand">By</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_details">Details</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_details" name="txt_details"
                                                                    autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="dd_sample_type">Sample
                                                                Type<b class="text-danger">*</b></label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-control required" name="dd_sample_type"
                                                                    id="dd_sample_type" required>
                                                                    <option value="" selected disabled>Select Type
                                                                    </option>
                                                                    @foreach ($labSamples as $labSample)
                                                                        <option
                                                                            value="{{ $labSample->m14_lab_sample_id }}">
                                                                            {{ $labSample->m14_name }}</option>
                                                                    @endforeach

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="dd_priority_type">Sample
                                                                Type</label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-select" name="dd_priority_type"
                                                                    id="dd_priority_type">
                                                                    <option value="Normal" selected>Normal</option>
                                                                    <option value="Urgent">Urgent</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label"
                                                                for="txt_attachment">Attachment</label>
                                                            <div class="form-control-wrap">
                                                                <div class="form-file">
                                                                    <input type="file" class="form-file-input"
                                                                        name="txt_attachment" id="txt_attachment">
                                                                    <label class="form-file-label"
                                                                        for="txt_attachment">Choose
                                                                        file</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_description">Sample
                                                                Description<b class="text-danger">*</b></label>
                                                            <div class="form-control-wrap">
                                                                <input class="form-control required" id="txt_description"
                                                                    name="txt_description" autocomplete="off" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- STEP 3: TEST INFORMATION -->
                                            <div class="nk-wizard-head">
                                                <h5>Test Information</h5>
                                            </div>
                                            <div class="nk-wizard-content">
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="dd_test_type">Type<b class="text-danger">*</b></label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-control required" name="dd_test_type"
                                                                    id="dd_test_type" required>
                                                                    <option value="" selected disabled>Select Test
                                                                        Type</option>
                                                                    <option value="CONTRACT">Contract</option>
                                                                    <option value="CUSTOM">Custom</option>
                                                                    <option value="GENERAL">General</option>
                                                                    <option value="PACKAGE">Package</option>
                                                                    <option value="SPECIFICATION">Specification</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6" id="contract-wrapper" style="display:none;">
                                                        <div class="form-group">
                                                            <label class="form-label" for="dd_contracts">Select
                                                            </label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-select" id="dd_contracts"
                                                                    name="dd_contracts">
                                                                    <option value="">Select Contract</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6" id="azo-wrapper" style="display:none;">
                                                        <div class="form-group">
                                                            <label class="form-label" for="dd_charge_type">Charge
                                                                Type</label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-select" id="dd_charge_type">
                                                                    <option value="inc_azo">Including Azo</option>
                                                                    <option value="exc_azo">Excluding Azo</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="dd_group">Group</label>
                                                            <div class="form-control-wrap">
                                                                <select class="form-control" name="dd_group"
                                                                    id="dd_group">
                                                                    <option value="" selected disabled>Select Group
                                                                    </option>
                                                                    @foreach ($groups as $group)
                                                                        <option value="{{ $group->m11_group_id }}">
                                                                            {{ $group->m11_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_standard">Test</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_standard" name="txt_standard"
                                                                    autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_due_date">Expected
                                                                Completion Date</label>
                                                            <div class="form-control-wrap">
                                                                <input type="date" class="form-control"
                                                                    id="txt_due_date" name="txt_due_date"
                                                                    value="{{ \Carbon\Carbon::now()->addDays(3)->format('Y-m-d') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_testing_charges">Testing
                                                                Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_testing_charges" name="txt_testing_charges"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label"
                                                                for="txt_aditional_charges">Aditional Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="txt_aditional_charges" value="0"
                                                                    name="txt_aditional_charges" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_total_charges">Total
                                                                Charges</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" readonly
                                                                    id="txt_total_charges" name="txt_total_charges">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="nk-block nk-block-lg">
                                                            <div class="card card-preview">
                                                                <table class="table table-tranx">
                                                                    <thead>
                                                                        <tr class="tb-tnx-head">
                                                                            <th class="tb-tnx-id"><span
                                                                                    class="">Test ID</span></th>
                                                                            <th class="tb-tnx-id"><span
                                                                                    class="">Test Name</span></th>
                                                                            <th class="tb-tnx-id"><span
                                                                                    class="">Method</span></th>
                                                                            <th class="tb-tnx-id"><span
                                                                                    class="">Charge</span></th>
                                                                            <th class="tb-tnx-id"><span
                                                                                    class="">Remark</span></th>
                                                                            <th class="tb-tnx-action">
                                                                                <span>&nbsp;</span>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>
                                                            </div><!-- .card -->
                                                        </div><!-- nk-block -->
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Results Sidebar -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="card-title">Search Results</h5>
                                        </div>

                                        <!-- Search Input -->
                                        <div class="form-group mb-3">
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="search_input"
                                                    placeholder="Search samples, customers, tests...">
                                                <button class="btn btn-primary btn-sm mt-2 w-100" id="search_btn">
                                                    <em class="icon ni ni-search"></em> Search
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Search Filters -->
                                        <div class="accordion" id="searchFilters">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#filterOptions">
                                                        <em class="icon ni ni-filter"></em> &nbsp; Filters
                                                    </button>
                                                </h2>
                                                <div id="filterOptions" class="accordion-collapse collapse"
                                                    data-bs-parent="#searchFilters">
                                                    <div class="accordion-body">
                                                        <div class="form-group">
                                                            <label class="form-label">Search Type</label>
                                                            <select class="form-select form-select-sm">
                                                                <option value="all">All Records</option>
                                                                <option value="customers">Customers</option>
                                                                <option value="samples">Samples</option>
                                                                <option value="tests">Tests</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label">Date Range</label>
                                                            <input type="date"
                                                                class="form-control form-control-sm mb-1"
                                                                placeholder="From Date">
                                                            <input type="date" class="form-control form-control-sm"
                                                                placeholder="To Date">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Search Results Area -->
                                        <div class="search-results mt-3">
                                            <div class="alert alert-light">
                                                <em class="icon ni ni-info"></em>
                                                <span>Enter search terms above to find existing records</span>
                                            </div>

                                            <!-- Sample Results (Hidden by default) -->
                                            <div class="search-result-items" style="display: none;">
                                                <div class="search-result-item border rounded p-2 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1">Sample #S001</h6>
                                                            <small class="text-muted">Customer: ABC Corp</small>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-primary">Select</button>
                                                    </div>
                                                </div>

                                                <div class="search-result-item border rounded p-2 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1">Sample #S002</h6>
                                                            <small class="text-muted">Customer: XYZ Ltd</small>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-primary">Select</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recent Records -->
                                        <div class="recent-records mt-6">
                                            <h6 class="card-title">Recent Records</h6>
                                            <div class="list-group list-group-flush">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <small class="text-muted">Last Sample:</small>
                                                        <br><span class="fw-bold">#S001 - Water Analysis</span>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-secondary">View</button>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <small class="text-muted">Last Customer:</small>
                                                        <br><span class="fw-bold">ABC Corporation</span>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-secondary">Use</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Actions -->
                                        <div class="quick-actions mt-6">
                                            <h6 class="card-title">Quick Actions</h6>
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <em class="icon ni ni-copy"></em> Copy Last Sample
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <em class="icon ni ni-user-add"></em> Add New Customer
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <em class="icon ni ni-calendar"></em> Schedule Test
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- .nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>


    <!-- Standards Modal -->
    <div class="modal fade" id="standardModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose Standard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul id="standard-list" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Reusable dropdown style */
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

    <script>
        $(document).ready(function() {
            console.log('Document ready - initializing form...');

            /** =========================
             *  GLOBAL VARIABLES
             ========================= **/
            let selectedTestIds = [];
            let searchTimeout;
            let customerActiveInput = null;
            let currentStandardCell = null;

            /** =========================
             *  IMMEDIATE INITIALIZATION
             ========================= **/
            // Initialize all events immediately - no waiting for wizard
            initializeAllEvents();

            /** =========================
             *  Shared Dropdown Utility
             ========================= **/
            function positionDropdown($input, $dropdown) {
                const offset = $input.offset();
                $dropdown.css({
                    top: offset.top + $input.outerHeight(),
                    left: offset.left,
                    width: $input.outerWidth()
                }).show();
            }

            function createDropdown(id) {
                // Remove existing dropdown if it exists
                $('#' + id).remove();
                return $('<div>', {
                    id: id,
                    class: 'custom-dropdown'
                }).appendTo('body').hide();
            }

            /** =========================
             *  INITIALIZE ALL EVENTS
             ========================= **/
            function initializeAllEvents() {
                console.log('Initializing all events...');

                // Initialize immediately and also on any wizard step change
                initializeTestTypeEvents();
                initializeCustomerEvents();
                initializeTestSearchEvents();
                initializeStandardEvents();

                // Also reinitialize on any content change (wizard navigation)
                $(document).on('click',
                    '.nk-wizard-head, .wizard-step, [data-toggle="pill"], [data-bs-toggle="pill"]',
                    function() {
                        console.log('Wizard navigation detected, reinitializing...');
                        setTimeout(function() {
                            initializeTestTypeEvents();
                        }, 200);
                    });

                // Reinitialize when wizard content becomes visible
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' || mutation.type === 'attributes') {
                            if ($('#dd_test_type').is(':visible') && $('#dd_test_type').length) {
                                setTimeout(initializeTestTypeEvents, 100);
                            }
                        }
                    });
                });

                if (document.querySelector('.nk-wizard')) {
                    observer.observe(document.querySelector('.nk-wizard'), {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['style', 'class']
                    });
                }
            }

            /** =========================
             *  TEST TYPE & CONTRACT EVENTS - FIXED
             ========================= **/
            function initializeTestTypeEvents() {
                console.log('Initializing test type events...');

                // Ensure elements exist before binding
                if (!$('#dd_test_type').length) {
                    console.log('Test type dropdown not found, retrying...');
                    setTimeout(initializeTestTypeEvents, 500);
                    return;
                }

                console.log('Test type dropdown found, binding events...');

                // Remove existing handlers to prevent duplicates - use namespace
                $('#dd_test_type').off('change.testtype');
                $('#dd_contracts').off('change.contracts');

                // Test type change handler with immediate debug
                $('#dd_test_type').on('change.testtype', function() {
                    let type = $(this).val();
                    console.log('Test type changed to:', type);

                    // Show immediate feedback
                    $('#contract-wrapper').show();
                    $('#dd_contracts').html('<option value="">Loading...</option>');

                    // Clear previous data
                    $('.table.table-tranx tbody').empty();
                    selectedTestIds = [];
                    calculateCharges();

                    if (type === "CONTRACT" || type === "PACKAGE" || type === "SPECIFICATION" || type ===
                        "CUSTOM") {
                        console.log('Loading contracts for type:', type);
                        loadContractsByType(type);
                        if (type === "PACKAGE") {
                            $('#azo-wrapper').show(); // show azo selector
                        } else {
                            $('#azo-wrapper').hide(); // hide for other types
                        }

                    } else {
                        console.log('Hiding contract wrapper for type:', type);
                        $('#contract-wrapper').hide();
                        $('#dd_contracts').html('<option value="">Select Contract</option>');
                    }
                });

                // Contract selection handler
                $('#dd_contracts').on('change.contracts', function() {
                    let contractId = $(this).val();
                    let testType = $('#dd_test_type').val();

                    console.log('Contract selected:', contractId, 'Type:', testType);

                    if (!contractId) {
                        $('.table.table-tranx tbody').empty();
                        selectedTestIds = [];
                        calculateCharges();
                        return;
                    }

                    loadTestsByContract(contractId, testType);
                });

                console.log('Test type events bound successfully');
            }

            function loadContractsByType(type) {
                console.log('Loading contracts for type:', type);

                const ajaxUrl = "{{ route('get_packages') }}"; // Use your actual route

                console.log('Using AJAX URL:', ajaxUrl);

                $.ajax({
                    url: ajaxUrl,
                    type: "GET",
                    data: {
                        type: type
                    },
                    dataType: 'json',
                    timeout: 10000, // 10 second timeout
                    beforeSend: function() {
                        console.log('Sending AJAX request for contracts...');
                        $('#dd_contracts').html('<option value="">Loading contracts...</option>');
                    },
                    success: function(response) {
                        console.log('Contracts response received:', response);

                        let options = '<option value="">Select ' + type.toLowerCase() + '</option>';

                        // Handle different response formats
                        let contracts = response.data || response.contracts || response || [];

                        if (Array.isArray(contracts) && contracts.length > 0) {
                            $.each(contracts, function(i, contract) {
                                // Handle different object structures
                                const id = contract.id;
                                const name = contract.name;

                                if (id && name) {
                                    options += `<option value="${id}">${name}</option>`;
                                }
                            });
                            console.log('Added', contracts.length, 'contract options');
                        } else {
                            options += '<option value="">No ' + type.toLowerCase() + 's found</option>';
                            console.log('No contracts found in response');
                        }

                        $('#dd_contracts').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error loading contracts:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            url: ajaxUrl,
                            readyState: xhr.readyState,
                            statusText: xhr.statusText
                        });

                        $('#dd_contracts').html('<option value="">Error loading contracts</option>');

                        // Show detailed error to user
                        let errorMsg = 'Error loading contracts: ';
                        if (xhr.status === 404) {
                            errorMsg += 'Route not found (404). Please check if the route exists.';
                        } else if (xhr.status === 500) {
                            errorMsg += 'Server error (500). Please check server logs.';
                        } else if (status === 'timeout') {
                            errorMsg += 'Request timed out. Please try again.';
                        } else {
                            errorMsg += `${status} - ${error}`;
                        }

                        alert(errorMsg);

                        // Try alternative: populate with static data for testing
                        $('#dd_contracts').html(`
                    <option value="">Select ${type.toLowerCase()}</option>
                    <option value="1">Test ${type} 1</option>
                    <option value="2">Test ${type} 2</option>
                `);
                    }
                });
            }

            function loadTestsByContract(contractId, testType) {
                console.log('Loading tests for contract:', contractId, 'type:', testType);

                $('.table.table-tranx tbody').empty();
                selectedTestIds = [];

                const ajaxUrl = "{{ route('get_tests_by_package') }}"; // Use your actual route

                $.ajax({
                    url: ajaxUrl,
                    type: "GET",
                    data: {
                        contract_id: contractId,
                        package_id: contractId, // Some APIs might expect this name
                        type: testType
                    },
                    dataType: 'json',
                    timeout: 10000,
                    beforeSend: function() {
                        console.log('Sending AJAX request for tests...');
                        $('.table.table-tranx tbody').html(
                            '<tr><td colspan="6" class="text-center">Loading tests...</td></tr>'
                        );
                    },
                    success: function(response) {
                        console.log('Tests response received:', response);
                        $('.table.table-tranx tbody').empty();

                        // Adjust depending on your backend
                        let packageData = Array.isArray(response) ? response[0] : response;
                        let tests = packageData.tests || [];

                        if (Array.isArray(tests) && tests.length > 0) {
                            let packageId = packageData.id; // assume all belong to same package
                            let packageName = packageData.name;
                            if (packageData.type === "PACKAGE") {
                                let selectedChargeType = $('#dd_charge_type').val() || 'exc_azo';
                                packageCharge = (selectedChargeType === 'inc_azo') ?
                                    packageData.inc_azo_charge :
                                    packageData.exc_azo_charge;
                            } else {
                                packageCharge = packageData.charge;
                            }


                            // Add package row once
                            if (!$(`#package-${packageId}`).length) {
                                let packageRow = `
                                    <tr class="package-charge-row" id="package-${packageId}">
                                        <td colspan="4"><strong>Package: ${packageName}</strong></td>
                                        <td></td>
                                        <td class="package-charge" data-charge="${packageCharge}">${packageCharge}</td>
                                    </tr>
                                `;
                                $('.table.table-tranx tbody').append(packageRow);
                            }

                            // Now add tests under it (no charges here)
                            // Replace the test row generation part with:
                            tests.forEach(testItem => {
                                let test = testItem.test;
                                let standard = testItem.standard;

                                if (test && !selectedTestIds.includes(test.id)) {
                                    selectedTestIds.push(test.id);

                                    let row = `
                                        <tr class="test-row" data-package="${packageId}" data-id="${test.id}">
                                            <td>${test.id}</td>
                                            <td>${test.name}</td>
                                            <td>
                                                <a href="#" class="choose-standard" data-test-id="${test.id}">
                                                    ${standard ? standard.method : 'Click to choose'}
                                                </a>
                                                <input type="hidden" name="tests[${test.id}][test_id]" value="${test.id}">
                                                <input type="hidden" name="tests[${test.id}][standard_id]" class="standard-id" value="${standard ? standard.id : ''}">
                                                <input type="hidden" name="tests[${test.id}][package_id]" value="${packageId}">
                                            </td>
                                            <td>-</td>
                                            <td>
                                                <input type="text" name="tests[${test.id}][remark]" class="form-control form-control-sm" placeholder="Enter remark">
                                            </td>
                                            <td>
                                                <a href="#" class="delete-test text-danger" title="Delete">
                                                    <em class="icon ni ni-trash"></em>
                                                </a>
                                            </td>
                                        </tr>
                                    `;
                                    $(`#package-${packageId}`).after(row);
                                }
                            });

                            calculateCharges();
                        } else {
                            $('.table.table-tranx tbody').html(
                                '<tr><td colspan="6" class="text-center text-muted">No tests found for this selection</td></tr>'
                            );
                            console.log('No tests found in response');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error loading tests:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            url: ajaxUrl
                        });

                        $('.table.table-tranx tbody').html(
                            '<tr><td colspan="6" class="text-center text-danger">Error loading tests</td></tr>'
                        );

                        let errorMsg = 'Error loading tests: ';
                        if (xhr.status === 404) {
                            errorMsg += 'Route not found. Please check if the route exists.';
                        } else if (xhr.status === 500) {
                            errorMsg += 'Server error. Please check server logs.';
                        } else {
                            errorMsg += `${status} - ${error}`;
                        }

                        alert(errorMsg);
                    }
                });
            }
            $(document).off('change.azo', '#dd_charge_type').on('change.azo', '#dd_charge_type', function() {
                $('#dd_contracts').trigger('change');
            });



            /** =========================
             *  CUSTOMER SEARCH EVENTS (EXISTING - WORKING)
             ========================= **/
            function initializeCustomerEvents() {
                const CUSTOMER_INPUTS = ['#txt_customer_name', '#txt_buyer_name', '#txt_third_party', '#txt_cha'];
                const CUSTOMER_URL = '{{ route('search_customer') }}';
                let $customerDropdown = createDropdown('customer-dropdown');

                const CUSTOMER_MAPPING = {
                    '#txt_customer_name': {
                        address: '#party-address',
                        contact: '#party-contact-person',
                        phone: '#party-phone',
                        email: '#party-email'
                    },
                    '#txt_buyer_name': {
                        address: '#buyer-address',
                        contact: '#buyer-contact-person',
                        phone: '#buyer-phone',
                        email: '#buyer-email'
                    },
                    '#txt_third_party': {
                        address: '#third-address',
                        contact: '#third-contact-person',
                        phone: '#third-phone',
                        email: '#third-email'
                    },
                    '#txt_cha': {
                        address: '#cha-address',
                        contact: '#cha-contact-person',
                        phone: '#cha-phone',
                        email: '#cha-email'
                    }
                };

                // Customer search with delegation
                $(document).off('input.customer').on('input.customer', CUSTOMER_INPUTS.join(','), function() {
                    const query = $(this).val().trim();
                    customerActiveInput = $(this);
                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        $customerDropdown.hide().empty();
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        positionDropdown(customerActiveInput, $customerDropdown);
                        $customerDropdown.html('<div class="dropdown-message">Searching...</div>');

                        $.getJSON(CUSTOMER_URL, {
                            query
                        }, function(customers) {
                            $customerDropdown.empty();
                            if (customers.length) {
                                customers.forEach(c => $('<div>')
                                    .addClass('custom-dropdown-item')
                                    .data('customer', c)
                                    .text(c.name)
                                    .appendTo($customerDropdown));
                            } else {
                                $customerDropdown.html(
                                    '<div class="dropdown-message">No customers found.</div>'
                                );
                            }
                        });
                    }, 300);
                });

                $(document).off('click.customer').on('click.customer', '#customer-dropdown .custom-dropdown-item',
                    function() {
                        const customer = $(this).data('customer');
                        if (customer && customerActiveInput) {
                            customerActiveInput.val(customer.name);
                            const mapping = CUSTOMER_MAPPING['#' + customerActiveInput.attr('id')];
                            if (mapping) fillPartyDetails(mapping, customer);
                            $customerDropdown.hide().empty();
                        }
                    });

                function fillPartyDetails(mapping, customer) {
                    const $addressSelect = $('<select class="form-select form-select-md"></select>');
                    let allAddresses = [];

                    // Add default address with special identifier
                    if (customer.default_address) {
                        allAddresses.push({
                            ...customer.default_address,
                            id: 'default',
                            isDefault: true
                        });
                    }

                    // Add other addresses
                    if (customer.other_addresses?.length) {
                        allAddresses = allAddresses.concat(customer.other_addresses.map(addr => ({
                            ...addr,
                            isDefault: false
                        })));
                    }

                    if (allAddresses.length > 0) {
                        allAddresses.forEach((addr, index) => {
                            $('<option>')
                                .val(index)
                                .text(addr.address || 'No address')
                                .data('contact_person', addr.contact_person)
                                .data('phone', addr.phone)
                                .data('email', addr.email)
                                .data('address_id', addr.id)
                                .data('is_default', addr.isDefault)
                                .appendTo($addressSelect);
                        });
                        updateContactDetails($addressSelect.find('option:selected'), mapping, customer.id);
                    } else {
                        $addressSelect.append($('<option>').text('No addresses').val(''));
                    }

                    $(mapping.address).empty().append($addressSelect);
                    $addressSelect.on('change', function() {
                        updateContactDetails($(this).find('option:selected'), mapping, customer.id);
                    });
                }

                function updateContactDetails($option, mapping, customerId) {
                    $(mapping.contact).text($option.data('contact_person') || 'N/A');
                    $(mapping.phone).text($option.data('phone') || 'N/A');
                    $(mapping.email).text($option.data('email') || 'N/A');

                    // Store customer and address IDs based on input type
                    const inputId = Object.keys(CUSTOMER_MAPPING).find(key => CUSTOMER_MAPPING[key] === mapping);

                    if (inputId === '#txt_customer_name') {
                        $('#selected_customer_id').val(customerId);
                        $('#selected_customer_address_id').val($option.data('address_id'));
                    } else if (inputId === '#txt_buyer_name') {
                        $('#selected_buyer_id').val(customerId);
                        $('#selected_buyer_address_id').val($option.data('address_id'));
                    } else if (inputId === '#txt_third_party') {
                        $('#selected_third_party_id').val(customerId);
                        $('#selected_third_party_address_id').val($option.data('address_id'));
                    } else if (inputId === '#txt_cha') {
                        $('#selected_cha_id').val(customerId);
                        $('#selected_cha_address_id').val($option.data('address_id'));
                    }
                }
            }

            /** =========================
             *  TEST SEARCH EVENTS (EXISTING - WORKING)
             ========================= **/
            function initializeTestSearchEvents() {
                const TEST_INPUT = '#txt_standard';
                const TEST_URL = '{{ route('search_test') }}';
                let $testDropdown = createDropdown('test-dropdown');

                $(document).off('input.testsearch').on('input.testsearch', TEST_INPUT, function() {
                    const query = $(this).val().trim();
                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        $testDropdown.hide().empty();
                        return;
                    }

                    const groupId = $('#dd_group').val();
                    if (!groupId) {
                        alert('Please select a group first.');
                        $(TEST_INPUT).val('');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        positionDropdown($(this), $testDropdown);
                        $testDropdown.html('<div class="dropdown-message">Searching...</div>');

                        $.getJSON(TEST_URL, {
                            query,
                            group_id: groupId
                        }, function(tests) {
                            const filtered = tests.filter(t => !selectedTestIds.includes(t
                                .id));
                            $testDropdown.empty();
                            if (filtered.length) {
                                filtered.forEach(test => $('<div>')
                                    .addClass('custom-dropdown-item')
                                    .data('test', test)
                                    .text(test.test_name)
                                    .appendTo($testDropdown));
                            } else {
                                $testDropdown.html(
                                    '<div class="dropdown-message">No tests found.</div>'
                                );
                            }
                        });
                    }, 300);
                });

                $(document).off('click.testsearch').on('click.testsearch', '#test-dropdown .custom-dropdown-item',
                    function() {
                        const test = $(this).data('test');
                        if (test) {
                            addTestToTable(test);
                            selectedTestIds.push(test.id);
                            $(TEST_INPUT).val('');
                            $testDropdown.hide().empty();
                        }
                    });
            }

            /** =========================
             *  STANDARDS EVENTS (EXISTING)
             ========================= **/
            function initializeStandardEvents() {
                $(document).off('click.standards').on('click.standards', '.choose-standard', function(e) {
                    e.preventDefault();
                    currentStandardCell = $(this);
                    let testId = $(this).data('test-id');
                    loadStandards(testId);
                    $('#standardModal').modal('show');
                });

                $(document).off('click.standardselect').on('click.standardselect', '.standard-item', function() {
                    let standardName = $(this).text().trim();
                    let standardId = $(this).data('id');
                    let testId = currentStandardCell.data('test-id');

                    if (currentStandardCell && standardId) {
                        currentStandardCell.text(standardName);
                        // Update the hidden input for this specific test
                        $(`input[name="tests[${testId}][standard_id]"]`).val(standardId);
                        $('#standardModal').modal('hide');
                    }
                });
            }

            function loadStandards(testId) {
                $('#standard-list').html('<li class="list-group-item">Loading...</li>');

                $.ajax({
                    url: '{{ route('get_standards_by_test') }}',
                    type: 'GET',
                    data: {
                        test_id: testId
                    },
                    success: function(standards) {
                        $('#standard-list').empty();
                        if (standards.length) {
                            standards.forEach(function(std) {
                                $('#standard-list').append(`
                            <li class="list-group-item standard-item" data-id="${std.id}" style="cursor: pointer;">
                                ${std.name}
                            </li>
                        `);
                            });
                        } else {
                            $('#standard-list').html(
                                '<li class="list-group-item text-muted">No standards found.</li>');
                        }
                    },
                    error: function() {
                        $('#standard-list').html(
                            '<li class="list-group-item text-danger">Error loading standards.</li>');
                    }
                });
            }

            /** =========================
             *  TABLE & CALCULATION FUNCTIONS
             ========================= **/
            function addTestToTable(test) {
                const row = `
        <tr data-id="${test.id}">
            <td>${test.id}</td>
            <td>${test.test_name || test.name}</td>
            <td>
                <a href="#" class="choose-standard" data-test-id="${test.id}">
                    ${test.standard?.name || 'Click to choose'}
                </a>
                <input type="hidden" name="tests[${test.id}][test_id]" value="${test.id}">
                <input type="hidden" name="tests[${test.id}][standard_id]" class="standard-id" value="${test.standard?.standard_id || ''}">
            </td>
            <td class="test-charge" data-charge="${test.charge || 0}">${test.charge || 0}</td>
            <td>
                <input type="text" name="tests[${test.id}][remark]" class="form-control form-control-sm" placeholder="Enter remark" value="${test.remark || ''}">
            </td>
            <td>
                <a href="#" class="delete-test text-danger" title="Delete">
                    <em class="icon ni ni-trash"></em>
                </a>
            </td>
        </tr>
    `;
                $(".table.table-tranx tbody").append(row);
                calculateCharges();
            }

            $(document).off('click.deletetest').on('click.deletetest', '.delete-test', function(e) {
                e.preventDefault();
                let testId = $(this).closest("tr").data("id");
                selectedTestIds = selectedTestIds.filter(id => id !== testId);
                $(this).closest("tr").remove();
                calculateCharges();
            });

            function calculateCharges() {
                let total = 0;
                let priority = $('#dd_priority_type').val();
                let testType = $('#dd_test_type').val(); // GENERAL, CONTRACT, CUSTOM, SPECIFICATION, PACKAGE

                if (testType === 'GENERAL') {
                    // General: sum row-wise test charges
                    $(".test-charge").each(function() {
                        total += parseFloat($(this).data("charge")) || 0;
                    });
                } else {
                    // Contract / Custom / Specification / Package
                    $(".package-charge").each(function() {
                        total += parseFloat($(this).data("charge")) || 0;
                    });
                }

                // Set testing charges
                $("#txt_testing_charges").val(total.toFixed(2));

                // Add additional charges
                let additional = parseFloat($('#txt_aditional_charges').val()) || 0;
                let finalTotal = total + additional;

                // Apply priority logic
                if (priority === 'Urgent') {
                    finalTotal += (total * 0.50);
                }

                $("#txt_total_charges").val(finalTotal.toFixed(2));
            }


            // Calculation event handlers
            $(document).off('change.priority').on('change.priority', '#dd_priority_type', calculateCharges);
            $(document).off('input.additional').on('input.additional', '#txt_aditional_charges', calculateCharges);

            /** =========================
             *  DROPDOWN HIDE ON OUTSIDE CLICK
             ========================= **/
            $(document).off('click.hidedrops').on('click.hidedrops', function(e) {
                if (!$(e.target).closest('#customer-dropdown, input[type="text"]').length) {
                    $('#customer-dropdown').hide();
                }
                if (!$(e.target).closest('#test-dropdown, #txt_standard').length) {
                    $('#test-dropdown').hide();
                }
            });

            console.log('All events initialized successfully');
        });
    </script>
@endsection
