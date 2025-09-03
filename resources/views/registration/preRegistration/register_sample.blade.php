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
                                {{-- <a href="{{ url()->previous() }}" class="btn btn-primary">
                                    <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                                </a> --}}
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
                                                                <select class="form-control required"
                                                                    name="dd_customer_type" id="dd_customer_type" required>
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
                                                    <!-- CUSTOMER -->
                                                    <div class="col-md-6">
                                                        <div class="form-group dropdown-container"
                                                            style="position: relative;">
                                                            <label class="form-label" for="txt_customer_name">
                                                                Customer Name<b class="text-danger">*</b>
                                                            </label>
                                                            <div class="form-control-wrap d-flex">
                                                                <input type="text" class="form-control required"
                                                                    id="txt_customer_name" name="txt_customer_name"
                                                                    placeholder="Start typing customer name..."
                                                                    autocomplete="off" required>
                                                                <a href="{{ route('create_customer', ['from' => 'registration']) }}"
                                                                    target="_blank"
                                                                    class="btn btn-sm btn-primary ml-2">+</a>
                                                            </div>
                                                            <div class="d-flex justify-content-around">
                                                                <span id="party-contact-person"></span>
                                                                <span id="party-phone"></span>
                                                                <span id="party-email"></span>
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
                                                            <div class="input-group">
                                                                <select id="party-address" class="form-control">
                                                                    <option value="">Loading addresses...</option>
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button type="button" data-type="customer"
                                                                        class="btn btn-outline-primary add-address-btn"
                                                                        disabled>
                                                                        +
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!-- BUYER -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_buyer_name">Buyer
                                                                Name</label>
                                                            <div class="form-control-wrap d-flex">
                                                                <input type="text" class="form-control"
                                                                    id="txt_buyer_name" name="txt_buyer_name"
                                                                    autocomplete="off">
                                                                <a href="{{ route('create_customer', ['from' => 'registration']) }}"
                                                                    target="_blank"
                                                                    class="btn btn-sm btn-primary ml-2">+</a>
                                                            </div>
                                                            <div class="d-flex justify-content-around">
                                                                <span id="buyer-contact-person"></span>
                                                                <span id="buyer-phone"></span>
                                                                <span id="buyer-email"></span>
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
                                                            <div class="input-group">
                                                                <select id="buyer-address" class="form-control">
                                                                    <option value="">Loading addresses...</option>
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button type="button" data-type="buyer"
                                                                        class="btn btn-outline-primary add-address-btn"
                                                                        disabled>
                                                                        +
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- THIRD PARTY -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_third_party">Third
                                                                Party</label>
                                                            <div class="form-control-wrap d-flex">
                                                                <input type="text" class="form-control"
                                                                    id="txt_third_party" name="txt_third_party"
                                                                    autocomplete="off">
                                                                <button type="button" class="btn btn-primary"
                                                                    data-url="{{ route('create_customer', ['from' => 'registration']) }}"
                                                                    onclick="openCustomerPopup(this)">
                                                                    +
                                                                </button>
                                                            </div>
                                                            <div class="d-flex justify-content-around">
                                                                <span id="third-contact-person"></span>
                                                                <span id="third-phone"></span>
                                                                <span id="third-email"></span>
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
                                                            <div class="input-group">
                                                                <select id="third-address" class="form-control">
                                                                    <option value="">Loading addresses...</option>
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button type="button" data-type="third"
                                                                        class="btn btn-outline-primary add-address-btn"
                                                                        disabled>
                                                                        +
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- CHA -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="txt_cha">CHA</label>
                                                            <div class="form-control-wrap d-flex">
                                                                <input type="text" class="form-control" id="txt_cha"
                                                                    name="txt_cha" autocomplete="off">
                                                                <a href="{{ route('create_customer', ['from' => 'registration']) }}"
                                                                    target="_blank"
                                                                    class="btn btn-sm btn-primary ml-2">+</a>
                                                            </div>
                                                            <div class="d-flex justify-content-around">
                                                                <span id="cha-contact-person"></span>
                                                                <span id="cha-phone"></span>
                                                                <span id="cha-email"></span>
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
                                                            <div class="input-group">
                                                                <select id="cha-address" class="form-control">
                                                                    <option value="">Loading addresses...</option>
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button type="button" data-type="cha"
                                                                        class="btn btn-outline-primary add-address-btn"
                                                                        disabled>
                                                                        +
                                                                    </button>
                                                                </div>
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
                                                                        <input type="radio"
                                                                            class="custom-control-input required"
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
                                                                        <input type="radio"
                                                                            class="custom-control-input required"
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
                                                                <select class="form-control required"
                                                                    name="dd_sample_type" id="dd_sample_type" required>
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
                                                    {{-- <div class="col-md-6">
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
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Sample Image <b
                                                                    class="text-danger">*</b></label><br>
                                                            <button type="button" class="btn btn-primary"
                                                                data-bs-toggle="modal" data-bs-target="#cameraModal">
                                                                📷 Take Sample Image
                                                            </button>
                                                            <input type="text" name="txt_sample_image"
                                                                id="txt_sample_image">
                                                            <div id="preview" class="mt-2"></div>
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
                                                            <label class="form-label" for="dd_test_type">Type<b
                                                                    class="text-danger">*</b></label>
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

                            <!-- Search Results Sidebar - Enhanced Dynamic Version -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="card-title">Search & Quick Actions</h5>
                                        </div>

                                        <!-- Search Input -->
                                        <div class="form-group mb-3">
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="global_search_input"
                                                    placeholder="Search samples, customers, tests...">
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
                                                        <div class="form-group mb-2">
                                                            <label class="form-label">Search Type</label>
                                                            <select class="form-select form-select-sm"
                                                                id="search_type_filter">
                                                                <option value="all">All Records</option>
                                                                <option value="customers">Customers</option>
                                                                <option value="samples">Samples</option>
                                                                <option value="tests">Tests</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label class="form-label">Date Range</label>
                                                            <input type="date" id="date_from"
                                                                class="form-control form-control-sm mb-1"
                                                                placeholder="From Date">
                                                            <input type="date" id="date_to"
                                                                class="form-control form-control-sm"
                                                                placeholder="To Date">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select form-select-sm" id="status_filter">
                                                                <option value="">All Status</option>
                                                                <option value="active">Active</option>
                                                                <option value="completed">Completed</option>
                                                                <option value="pending">Pending</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Search Results -->
                                        <div class="search-results mt-3" id="search_results_container">
                                            <div class="alert alert-light" id="search_placeholder">
                                                <em class="icon ni ni-info"></em>
                                                <span>Enter search terms above to find existing records</span>
                                            </div>

                                            <!-- Dynamic Results -->
                                            <div class="search-result-items" id="dynamic_search_results"
                                                style="display: none;">
                                            </div>

                                            <!-- No Results -->
                                            <div class="alert alert-warning" id="no_results_message"
                                                style="display: none;">
                                                <em class="icon ni ni-alert-circle"></em>
                                                <span>No records found matching your search criteria</span>
                                            </div>
                                        </div>

                                        <!-- Recent Records -->
                                        <div class="recent-records mt-4" id="recent_records_section">
                                            <h6 class="card-title d-flex justify-content-between align-items-center">
                                                Recent Records
                                                <button class="btn btn-sm btn-outline-secondary" id="refresh_recent">
                                                    <em class="icon ni ni-reload"></em>
                                                </button>
                                            </h6>
                                            <div class="list-group list-group-flush" id="recent_records_list">
                                                <!-- Populated dynamically -->
                                            </div>
                                        </div>

                                        <!-- Quick Actions -->
                                        <div class="quick-actions mt-4">
                                            <h6 class="card-title">Quick Actions</h6>
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-primary btn-sm" id="copy_last_sample">
                                                    <em class="icon ni ni-copy"></em> Copy Last Sample
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" id="add_new_customer">
                                                    <em class="icon ni ni-user-add"></em> Add New Customer
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" id="schedule_test">
                                                    <em class="icon ni ni-calendar"></em> Schedule Test
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle w-100"
                                                        type="button" data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em> More Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" id="export_data">
                                                                <em class="icon ni ni-download"></em> Export Data
                                                            </a></li>
                                                        <li><a class="dropdown-item" href="#" id="import_samples">
                                                                <em class="icon ni ni-upload"></em> Import Samples
                                                            </a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item" href="#" id="clear_form">
                                                                <em class="icon ni ni-trash"></em> Clear Form
                                                            </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stats -->
                                        <div class="stats-section mt-4">
                                            <div class="card card-bordered">
                                                <div class="card-inner">
                                                    <h6 class="card-title">Today's Stats</h6>
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <div class="stats-count">
                                                                <span class="amount" id="today_samples">-</span>
                                                                <span class="sub-text">Samples</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="stats-count">
                                                                <span class="amount" id="pending_tests">-</span>
                                                                <span class="sub-text">Pending</span>
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

    {{-- model to take image --}}
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Capture Sample Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="video" width="480" height="360" autoplay playsinline
                        style="border:1px solid #ccc; border-radius:8px;"></video>
                    <canvas id="canvas" width="480" height="360"
                        style="display:none; border:1px solid #ccc; border-radius:8px;"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" id="captureBtn" class="btn btn-primary">📸 Capture</button>
                    <button type="button" id="retakeBtn" class="btn btn-warning" style="display:none;">🔄
                        Retake</button>
                    <button type="button" id="saveImageBtn" class="btn btn-success" style="display:none;"
                        data-bs-dismiss="modal">✅ Use Image</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addAddressForm">
                    @csrf
                    <input type="hidden" name="txt_loc_customer_id" id="modal_customer_id">
                    <input type="hidden" name="type" id="modal_type">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                        <!-- Bootstrap 5 close button -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" name="txt_loc_contact_person" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="txt_loc_email">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="txt_loc_phone">
                            </div>
                            <div class="form-group">
                                <label>Pincode</label>
                                <input type="text" class="form-control" name="txt_loc_pincode">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State</label>
                                <select class="form-control" name="txt_loc_state_id" id="modal_state_id" required>
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->m01_state_id }}">{{ $state->m01_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>District</label>
                                <select class="form-control" name="txt_loc_district_id" id="modal_district_id" required>
                                    <option value="">Select District</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Full Address</label>
                                <textarea class="form-control" name="txt_loc_address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- Bootstrap 5 dismiss -->
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <style>
        /* Enhanced Sidebar Styles */
        .search-result-items {
            max-height: 300px;
            overflow-y: auto;
        }

        .search-result-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-result-item.selected {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }

        .result-meta {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .result-actions {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .search-result-item:hover .result-actions {
            opacity: 1;
        }

        .recent-records .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .stats-count .amount {
            font-size: 1.25rem;
            font-weight: 600;
            color: #364a63;
        }

        .stats-count .sub-text {
            font-size: 0.75rem;
            color: #8094ae;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quick-actions .btn:hover {
            transform: translateY(-1px);
        }

        .badge-priority {
            font-size: 0.65rem;
        }

        .badge-urgent {
            background-color: #ff6b6b;
        }

        .badge-normal {
            background-color: #51cf66;
        }

        /* Loading animation */
        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .loading-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
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
            // get districts while adding address on the basis of selected state
            $('#modal_state_id').on('change', function() {
                let stateId = $(this).val();
                let districtSelect = $('#modal_district_id');
                fetchDistricts(stateId, districtSelect);
            });

            // Svas Address
            $('#addAddressForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('create_customer_location') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addAddressModal').modal('hide');
                        toastr.success("Address added successfully!");
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error("Failed to save address");
                    }
                });
            });


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

                // Map each input to both DOM targets and a logical "type"
                const CUSTOMER_MAPPING = {
                    '#txt_customer_name': {
                        type: 'customer',
                        address: '#party-address',
                        contact: '#party-contact-person',
                        phone: '#party-phone',
                        email: '#party-email',
                        hiddenCustomer: '#selected_customer_id',
                        hiddenAddress: '#selected_customer_address_id',
                    },
                    '#txt_buyer_name': {
                        type: 'buyer',
                        address: '#buyer-address',
                        contact: '#buyer-contact-person',
                        phone: '#buyer-phone',
                        email: '#buyer-email',
                        hiddenCustomer: '#selected_buyer_id',
                        hiddenAddress: '#selected_buyer_address_id',
                    },
                    '#txt_third_party': {
                        type: 'third',
                        address: '#third-address',
                        contact: '#third-contact-person',
                        phone: '#third-phone',
                        email: '#third-email',
                        hiddenCustomer: '#selected_third_party_id',
                        hiddenAddress: '#selected_third_party_address_id',
                    },
                    '#txt_cha': {
                        type: 'cha',
                        address: '#cha-address',
                        contact: '#cha-contact-person',
                        phone: '#cha-phone',
                        email: '#cha-email',
                        hiddenCustomer: '#selected_cha_id',
                        hiddenAddress: '#selected_cha_address_id',
                    }
                };

                // --- Delegated click for ALL "+ Add" buttons (single handler) ---
                $(document).off('click.addAddress').on('click.addAddress', '.add-address-btn', function() {
                    const type = $(this).data('type'); // customer/buyer/third/cha
                    const customerId = $(this).data('customerId'); // set in fillPartyDetails
                    if (!customerId) return; // no customer picked yet
                    openAddAddressModal(customerId, type);
                });

                // --- Customer search with delegation (unchanged) ---
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
                        if (!customer || !customerActiveInput) return;

                        // Put chosen name into the correct text input
                        customerActiveInput.val(customer.name);

                        // Work out mapping + type for this input
                        const inputSelector = '#' + customerActiveInput.attr('id');
                        const mapping = CUSTOMER_MAPPING[inputSelector];
                        if (mapping) fillPartyDetails(mapping, customer, mapping.type);

                        $customerDropdown.hide().empty();
                    });

                function fillPartyDetails(mapping, customer, type) {
                    const $addressSelect = $(mapping.address); // existing <select>
                    const $addBtn = $(`.add-address-btn[data-type="${type}"]`); // correct "+ Add" button

                    // reset UI
                    $addressSelect.empty().append('<option value="">Loading addresses...</option>');
                    $addBtn.prop('disabled', true).removeData('customerId');

                    // build address list
                    let allAddresses = [];
                    if (customer.default_address) {
                        allAddresses.push({
                            ...customer.default_address,
                            id: 'default',
                            isDefault: true
                        });
                    }
                    if (customer.other_addresses?.length) {
                        allAddresses = allAddresses.concat(
                            customer.other_addresses.map(addr => ({
                                ...addr,
                                isDefault: false
                            }))
                        );
                    }

                    // populate select
                    $addressSelect.empty();
                    if (allAddresses.length > 0) {
                        allAddresses.forEach(addr => {
                            $('<option>')
                                .val(addr.id)
                                .text(addr.address || 'No address')
                                .data('contact_person', addr.contact_person)
                                .data('phone', addr.phone)
                                .data('email', addr.email)
                                .appendTo($addressSelect);
                        });
                        // trigger default (first) selection details
                        updateContactDetails($addressSelect.find('option:selected'), mapping, customer.id);
                    } else {
                        $addressSelect.append('<option value="">No addresses found</option>');
                        updateContactDetails($('<option>'), mapping, customer.id);
                    }

                    // enable "+ Add" button for THIS party and store the selected customerId on it
                    if (customer.id) {
                        $addBtn.prop('disabled', false).data('customerId', customer.id);
                    }

                    // on change, update details + hidden IDs
                    $addressSelect.off('change.party').on('change.party', function() {
                        updateContactDetails($(this).find('option:selected'), mapping, customer.id);
                    });

                    // also set hidden customer id immediately
                    $(mapping.hiddenCustomer).val(customer.id);
                }

                function updateContactDetails($option, mapping, customerId) {
                    $(mapping.contact).text($option.data('contact_person') || 'N/A');
                    $(mapping.phone).text($option.data('phone') || 'N/A');
                    $(mapping.email).text($option.data('email') || 'N/A');

                    // store selected address id
                    $(mapping.hiddenAddress).val($option.val() || '');
                    // customer id is already set in fillPartyDetails, but we can ensure:
                    $(mapping.hiddenCustomer).val(customerId || '');
                }

                // Example stub for modal call (replace with your modal logic)
                function openAddAddressModal(customerId, type) {
                    $('#modal_customer_id').val(customerId);
                    $('#modal_type').val(type);
                    $('#addAddressModal').modal('show');
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

    <script>
        $(document).ready(function() {
            initializeDynamicSidebar();
        });

        function initializeDynamicSidebar() {
            console.log('Initializing dynamic sidebar...');

            let searchTimeout;
            const SEARCH_DELAY = 500;

            // Global search input
            $('#global_search_input').on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    resetSearchResults();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performGlobalSearch(query);
                }, SEARCH_DELAY);
            });

            // Search button
            $('#global_search_btn').on('click', function(e) {
                e.preventDefault();
                const query = $('#global_search_input').val().trim();
                if (query.length >= 2) {
                    performGlobalSearch(query);
                }
            });

            // Filters
            $('#search_type_filter, #status_filter, #date_from, #date_to').on('change', function() {
                const query = $('#global_search_input').val().trim();
                if (query.length >= 2) {
                    performGlobalSearch(query);
                }
            });

            // Load initial data
            loadRecentRecords();
            loadTodayStats();

            initializeQuickActions();

            // Auto refresh every 30s
            setInterval(function() {
                loadRecentRecords();
                loadTodayStats();
            }, 30000);
        }

        // ADD THESE 3 MISSING FUNCTIONS TO YOUR SCRIPT

        // --------------------
        // GLOBAL SEARCH (Live)
        // --------------------
        function performGlobalSearch(query) {
            console.log('Performing global search for:', query);

            const searchType = $('#search_type_filter').val();
            const status = $('#status_filter').val();
            const dateFrom = $('#date_from').val();
            const dateTo = $('#date_to').val();

            showSearchLoading(true);

            $.ajax({
                url: '{{ route('global_search') }}',
                type: 'GET',
                data: {
                    query: query,
                    type: searchType,
                    status: status,
                    date_from: dateFrom,
                    date_to: dateTo
                },
                success: function(response) {
                    displaySearchResults(response);
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error);
                    resetSearchResults();
                    showNotification('Error fetching search results', 'danger');
                },
                complete: function() {
                    showSearchLoading(false);
                }
            });
        }

        // --------------------
        // RECENT RECORDS (Live)
        // --------------------
        function loadRecentRecords() {
            console.log('Loading recent records...');

            const $container = $('#recent_records_list');
            $container.html(`
        <div class="list-group-item px-0" id="recent_loading">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <span>Loading recent records...</span>
            </div>
        </div>
    `);

            $.ajax({
                url: '{{ route('recent_records') }}',
                type: 'GET',
                success: function(records) {
                    displayRecentRecords(records);
                },
                error: function() {
                    $container.html(`
                <div class="list-group-item px-0 text-center text-muted">
                    <em class="icon ni ni-alert-circle"></em>
                    <br><small>Error loading recent records</small>
                </div>
            `);
                }
            });
        }

        // --------------------
        // TODAY'S STATS (Live)
        // --------------------
        function loadTodayStats() {
            console.log('Loading today stats...');

            $.ajax({
                url: '{{ route('today_stats') }}',
                type: 'GET',
                success: function(stats) {
                    $('#today_samples').text(stats.today_samples || 0);
                    $('#pending_tests').text(stats.pending_tests || 0);
                },
                error: function() {
                    $('#today_samples').text('-');
                    $('#pending_tests').text('-');
                }
            });
        }

        // --------------------
        // DISPLAY FUNCTIONS
        // --------------------
        function displaySearchResults(results) {
            const $container = $('#dynamic_search_results');
            const $placeholder = $('#search_placeholder');
            const $noResults = $('#no_results_message');

            $placeholder.hide();
            $noResults.hide();

            if (!results || results.length === 0) {
                $container.hide();
                $noResults.show();
                return;
            }

            $container.empty().show();

            results.forEach(function(result) {
                const resultItem = createSearchResultItem(result);
                $container.append(resultItem);
            });
        }

        function createSearchResultItem(result) {
            const statusBadge = getStatusBadge(result.status, result.type);
            const priorityBadge = result.priority ? getPriorityBadge(result.priority) : '';
            const dateText = result.date ? `<small class="text-muted">${result.date}</small>` : '';
            const customerText = result.customer ? `<small class="text-info">Customer: ${result.customer}</small>` : '';

            return $(`
        <div class="search-result-item border rounded p-3 mb-2" data-type="${result.type}" data-id="${result.id}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">
                        <span class="badge badge-sm badge-outline-secondary me-2">${result.type}</span>
                        ${result.title}
                        ${priorityBadge}
                    </h6>
                    <p class="text-muted mb-1 small">${result.subtitle || ''}</p>
                    <div class="d-flex gap-2 flex-wrap">
                        ${statusBadge}
                        ${customerText}
                        ${dateText}
                    </div>
                </div>
                <div class="result-actions">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <em class="icon ni ni-more-v"></em>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item use-result" href="#" data-action="use">
                                <em class="icon ni ni-check"></em> Use This
                            </a></li>
                            <li><a class="dropdown-item copy-result" href="#" data-action="copy">
                                <em class="icon ni ni-copy"></em> Copy Details
                            </a></li>
                            <li><a class="dropdown-item view-result" href="#" data-action="view">
                                <em class="icon ni ni-eye"></em> View Details
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item add-favorite" href="#" data-action="favorite">
                                <em class="icon ni ni-star"></em> Add to Favorites
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `);
        }

        function displayRecentRecords(records) {
            const $container = $('#recent_records_list');

            if (!records || records.length === 0) {
                $container.html(`
            <div class="list-group-item px-0 text-center text-muted">
                <em class="icon ni ni-inbox"></em>
                <br><small>No recent records</small>
            </div>
        `);
                return;
            }

            $container.empty();

            records.forEach(function(record) {
                const item = $(`
            <div class="list-group-item list-group-item-action px-0 border-0 recent-record-item" data-type="${record.type.toLowerCase()}" data-id="${record.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <span class="badge badge-sm badge-outline-primary me-2">${record.type}</span>
                        <div class="fw-medium small">${record.title}</div>
                        <small class="text-muted">${record.date}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-light use-recent" title="Use This Record">
                        <em class="icon ni ni-arrow-right"></em>
                    </button>
                </div>
            </div>
        `);
                $container.append(item);
            });
        }

        // --------------------
        // UTILITY FUNCTIONS
        // --------------------
        function getStatusBadge(status, type) {
            const statusMap = {
                'active': 'success',
                'completed': 'success',
                'pending': 'warning',
                'cancelled': 'danger',
                'in_progress': 'info'
            };

            const badgeClass = statusMap[status] || 'secondary';
            return `<span class="badge badge-sm badge-${badgeClass}">${status || 'Unknown'}</span>`;
        }

        function getPriorityBadge(priority) {
            if (priority === 'urgent') {
                return `<span class="badge badge-sm badge-urgent ms-2">URGENT</span>`;
            }
            return '';
        }

        function showSearchLoading(show) {
            const $button = $('#global_search_btn');
            const $text = $('#search-btn-text');
            const $spinner = $('#search-loading');

            if (show) {
                $text.addClass('d-none');
                $spinner.removeClass('d-none');
                $button.prop('disabled', true);
            } else {
                $text.removeClass('d-none');
                $spinner.addClass('d-none');
                $button.prop('disabled', false);
            }
        }

        function resetSearchResults() {
            $('#search_placeholder').show();
            $('#dynamic_search_results').hide().empty();
            $('#no_results_message').hide();
        }


        function showNotification(message, type = 'info') {
            // Clear any existing toasts first
            if (typeof toastr !== 'undefined') {
                toastr.clear();
            }

            // Map types to NioApp.Toast compatible types
            const typeMap = {
                'info': 'info',
                'success': 'success',
                'warning': 'warning',
                'danger': 'error', // NioApp uses 'error' instead of 'danger'
                'error': 'error'
            };

            const toastType = typeMap[type] || 'info';

            // Use your existing NioApp.Toast system
            if (typeof NioApp !== 'undefined' && NioApp.Toast) {
                NioApp.Toast(message, toastType, {
                    position: 'top-right'
                });
            } else {
                // Fallback to console if NioApp is not available
                console.log(`[${toastType.toUpperCase()}] ${message}`);
            }
        }

        // --------------------
        // EVENT HANDLERS
        // --------------------
        function initializeQuickActions() {
            // Use result from search
            $(document).on('click', '.use-result', function(e) {
                e.preventDefault();
                const $item = $(this).closest('.search-result-item');
                const type = $item.data('type');
                const id = $item.data('id');

                useSearchResult(type, id);
            });

            // Copy result details
            $(document).on('click', '.copy-result', function(e) {
                e.preventDefault();
                const $item = $(this).closest('.search-result-item');
                const type = $item.data('type');
                const id = $item.data('id');

                copyResultDetails(type, id);
            });

            // View result details
            $(document).on('click', '.view-result', function(e) {
                e.preventDefault();
                const $item = $(this).closest('.search-result-item');
                const type = $item.data('type');
                const id = $item.data('id');

                viewResultDetails(type, id);
            });


            // Use recent record
            $(document).on('click', '.use-recent', function(e) {
                e.preventDefault();
                const $item = $(this).closest('.recent-record-item');
                const type = $item.data('type');
                const id = $item.data('id');

                useSearchResult(type, id);
            });

            // Quick action buttons
            $('#copy_last_sample').on('click', function(e) {
                e.preventDefault();
                copyLastSample();
            });

            $('#add_new_customer').on('click', function(e) {
                e.preventDefault();
                // Implement customer modal or redirect
                alert('Add new customer functionality - implement customer modal');
            });

            $('#schedule_test').on('click', function(e) {
                e.preventDefault();
                // Implement test scheduling
                alert('Schedule test functionality - implement scheduling modal');
            });

            $('#export_data').on('click', function(e) {
                e.preventDefault();
                exportData();
            });

            $('#import_samples').on('click', function(e) {
                e.preventDefault();
                // Implement import modal
                alert('Import samples functionality - implement import modal');
            });

            $('#clear_form').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to clear the form?')) {
                    clearForm();
                }
            });

            $('#refresh_recent').on('click', function(e) {
                e.preventDefault();
                loadRecentRecords();
                loadTodayStats();
                showNotification('Data refreshed successfully', 'success');
            });
        }

        // --------------------
        // ACTION FUNCTIONS
        // --------------------
        function useSearchResult(type, id) {
            console.log('Using result:', type, id);

            switch (type.toLowerCase()) {
                case 'customer':
                    loadCustomerIntoForm(id);
                    break;
                case 'sample':
                    copySampleData(id);
                    break;
                case 'test':
                    addTestToForm(id);
                    break;
                case 'contract':
                    loadContractIntoForm(id);
                    break;
                default:
                    showNotification(`Using ${type} is not implemented yet`, 'warning');
            }
        }

        function copyResultDetails(type, id) {
            console.log('Copying details for:', type, id);

            if (type === 'sample') {
                copySampleData(id);
            } else {
                showNotification(`Copy details for ${type} is not implemented yet`, 'warning');
            }
        }

        function viewResultDetails(type, id) {
            console.log('Viewing details for:', type, id);
            // Implement detail view modal
            showNotification(`View details for ${type} is not implemented yet`, 'warning');
        }

        function loadCustomerIntoForm(customerId) {
            // This would load customer data into the main form
            console.log('Loading customer:', customerId);
            showNotification('Customer loading functionality needs to be implemented', 'warning');
        }

        function copySampleData(sampleId) {
            $.ajax({
                url: '{{ route('get_sample_details') }}',
                type: 'GET',
                data: {
                    sample_id: sampleId
                },
                success: function(sampleData) {
                    console.log('Sample data:', sampleData);

                    // Populate form fields
                    if (sampleData.customer) {
                        $('#txt_customer_name').val(sampleData.customer.name);
                        $('#selected_customer_id').val(sampleData.customer.id);
                    }

                    $('#txt_reference').val(sampleData.reference_no || '');
                    $('#txt_ref_date').val(sampleData.reference_date || '');
                    $('#txt_description').val(sampleData.description || '');

                    if (sampleData.sample_type_id) {
                        $('#dd_sample_type').val(sampleData.sample_type_id);
                    }

                    if (sampleData.priority) {
                        $('#dd_priority_type').val(sampleData.priority);
                    }

                    if (sampleData.test_type) {
                        $('#dd_test_type').val(sampleData.test_type).trigger('change');
                    }

                    // Add tests to table
                    if (sampleData.tests && sampleData.tests.length > 0) {
                        setTimeout(() => {
                            sampleData.tests.forEach(test => {
                                if (!selectedTestIds.includes(test.id)) {
                                    addTestToTable({
                                        id: test.id,
                                        test_name: test.name,
                                        name: test.name,
                                        charge: test.charge,
                                        remark: test.remark,
                                        standard: {
                                            standard_id: test.standard_id,
                                            name: test.standard_method
                                        }
                                    });
                                    selectedTestIds.push(test.id);
                                }
                            });
                            calculateCharges();
                        }, 500);
                    }

                    showNotification('Sample data copied successfully', 'success');
                },
                error: function() {
                    showNotification('Error copying sample data', 'danger');
                }
            });
        }

        function addTestToForm(testId) {
            $.ajax({
                url: '{{ route('get_test_details') }}',
                type: 'GET',
                data: {
                    test_id: testId
                },
                success: function(testData) {
                    if (!selectedTestIds.includes(testData.id)) {
                        addTestToTable(testData);
                        selectedTestIds.push(testData.id);
                        showNotification('Test added successfully', 'success');
                    } else {
                        showNotification('Test is already added', 'warning');
                    }
                },
                error: function() {
                    showNotification('Error adding test', 'danger');
                }
            });
        }

        function loadContractIntoForm(contractId) {
            // Set contract type and select the contract
            $('#dd_test_type').val('CONTRACT').trigger('change');

            setTimeout(() => {
                $('#dd_contracts').val(contractId).trigger('change');
                showNotification('Contract loaded successfully', 'success');
            }, 1000);
        }

        function copyLastSample() {
            $.ajax({
                url: '{{ route('recent_records') }}',
                type: 'GET',
                success: function(records) {
                    const lastSample = records.find(r => r.type === 'Sample');
                    if (lastSample) {
                        copySampleData(lastSample.id);
                    } else {
                        showNotification('No recent samples found', 'warning');
                    }
                },
                error: function() {
                    showNotification('Error loading recent samples', 'danger');
                }
            });
        }

        function exportData() {
            // Implement data export functionality
            const formData = {
                customer: $('#txt_customer_name').val(),
                reference: $('#txt_reference').val(),
                description: $('#txt_description').val(),
                tests: selectedTestIds
            };

            console.log('Export data:', formData);
            showNotification('Export functionality needs to be implemented', 'warning');
        }

        function clearForm() {
            // Clear all form fields
            $('form')[0].reset();
            $('.table.table-tranx tbody').empty();
            selectedTestIds = [];

            // Clear customer details
            $('#party-address, #buyer-address, #third-address, #cha-address').empty();
            $('#party-contact-person, #party-phone, #party-email').empty();
            $('#buyer-contact-person, #buyer-phone, #buyer-email').empty();
            $('#third-contact-person, #third-phone, #third-email').empty();
            $('#cha-contact-person, #cha-phone, #cha-email').empty();

            // Clear hidden fields
            $('input[type="hidden"]').val('');

            // Reset charges
            calculateCharges();

            showNotification('Form cleared successfully', 'success');
        }
    </script>
    <script>
        $(document).ready(function() {
            let video = document.getElementById("video");
            let canvas = document.getElementById("canvas");
            let context = canvas.getContext("2d");
            let captureBtn = $("#captureBtn");
            let retakeBtn = $("#retakeBtn");
            let saveImageBtn = $("#saveImageBtn");
            let inputBase64 = $("#sample_image_base64");
            let streamRef = null;

            // When modal opens → start camera
            $('#cameraModal').on('shown.bs.modal', function() {
                navigator.mediaDevices.getUserMedia({
                        video: {
                            width: {
                                ideal: 1280
                            }, // HD resolution
                            height: {
                                ideal: 720
                            },
                            facingMode: "environment"
                        }
                    })
                    .then(function(stream) {
                        streamRef = stream;
                        video.srcObject = stream;
                    })
                    .catch(function(err) {
                        alert("Camera not available: " + err.message);
                    });
            });

            captureBtn.on("click", function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                $(canvas).show();
                $(video).hide();
                captureBtn.hide();
                retakeBtn.show();
                saveImageBtn.show();
            });

            // Retake
            retakeBtn.on("click", function() {
                $(canvas).hide();
                $(video).show();
                captureBtn.show();
                retakeBtn.hide();
                saveImageBtn.hide();
            });

            // Save image (to hidden input + show thumbnail)
            saveImageBtn.on("click", function() {
                let base64Image = canvas.toDataURL("image/jpeg", 0.9); // 90% quality JPEG
                console.log(base64Image);

                $("#txt_sample_image").val(base64Image);

                $("#preview").html(
                    `<img src="${base64Image}" width="200" class="img-thumbnail mt-2">`
                );
            });

        });
    </script>
    <script>
        function openCustomerPopup(el) {
            let url = el.dataset.url;
            window.open(
                url,
                "CustomerPopup",
                "width=900,height=600"
            );
        }
    </script>
@endsection
