@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Customer Type List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a data-bs-toggle="modal" data-bs-target="#createCustomerType"
                                        class="btn btn-primary"><em class="icon ni ni-plus"></em> &nbsp; Add More</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>Sr. No</th>
                                            {{-- <th>Customer Code</th> --}}
                                            <th>Lab ID</th>
                                            <th>Customer Type</th>
                                            {{-- <th>Sample No</th> --}}
                                            <th>Inv.Amount%</th>
                                            <th>Type</th>
                                            <th>Remarks</th>
                                            <th>User Name</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customerTypes as $key => $customerType)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $customerType->m04_ro_id }}</td>
                                                <td>{{ $customerType->m09_name }}</td>
                                                <td>{{ $customerType->m09_amount_percent }}</td>
                                                <td><b>{{ $customerType->m09_type }}</b></td>
                                                <td>{{ $customerType->m09_remark }}</td>
                                                <td>{{ $customerType->tr01_created_by }}</td>
                                                <td>{{ $customerType->created_at }}</td>
                                                <td
                                                    class="text-{{ $customerType->m09_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $customerType->m09_status }}</strong>
                                                </td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                    <ul class="nk-tb-actions gx-1 my-n1">
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown"><em
                                                                        class="icon ni ni-more-h"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a data-bs-toggle="modal" class="edit-btn btn"
                                                                                data-name="{{ $customerType->m09_name }}"
                                                                                data-amount="{{ $customerType->m09_amount_percent }}"
                                                                                data-category="{{ $customerType->m09_type }}"
                                                                                data-remark="{{ $customerType->m09_remark }}"
                                                                                data-id="{{ $customerType->m09_customer_type_id }}"
                                                                                data-bs-target="#editCustomerType"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit
                                                                                </span></a></li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $customerType->m09_customer_type_id }}"
                                                                                data-status="{{ $customerType->m09_status }}"><em
                                                                                    class="icon ni ni-trash"></em><span>Change
                                                                                    Status</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>

    {{-- Create Model --}}
    <div class="modal fade zoom" tabindex="-1" id="createCustomerType">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_customer_type') }}" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="txt_customer_type_id" id="txt_customer_type_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Customer Type</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_name">Customer Type</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="txt_name" id="txt_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_invoice_amount">Invoice Amount</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" name="txt_invoice_amount"
                                            id="txt_invoice_amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_categoery_type">Category Type</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control" name="txt_categoery_type" id="txt_categoery_type"
                                            required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="INTERNAL">INTERNAL</option>
                                            <option value="EXTERNAL">EXTERNAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_remark">Remark</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="txt_remark" id="txt_remark">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Edit & Update Model --}}
    <div class="modal fade zoom" tabindex="-1" id="editCustomerType">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="customerTypeForm" action="{{ route('update_customer_type') }}" method="POST"
                    class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="txt_edit_customer_type_id" id="txt_edit_customer_type_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalTitle">Edit Customer Type</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_edit_name">Customer Type</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="txt_edit_name"
                                            id="txt_edit_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_edit_invoice_amount">Invoice Amount</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" name="txt_edit_invoice_amount"
                                            id="txt_edit_invoice_amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_edit_categoery_type">Category Type</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control" name="txt_edit_categoery_type"
                                            id="txt_edit_categoery_type" required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="INTERNAL">INTERNAL</option>
                                            <option value="EXTERNAL">EXTERNAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_edit_remark">Remark</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="txt_edit_remark"
                                            id="txt_edit_remark">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="formSubmitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                $('#txt_edit_customer_type_id').val($(this).data('id'));
                $('#txt_edit_name').val($(this).data('name'));
                $('#txt_edit_invoice_amount').val($(this).data('amount'));
                $('#txt_edit_categoery_type').val($(this).data('category'));
                $('#txt_edit_remark').val($(this).data('remark'));
            });
            // To change the status 
            bindToggleStatus('.eg-swal-av3', "{{ route('delete_customer_type') }}");
        });
    </script>
@endsection
