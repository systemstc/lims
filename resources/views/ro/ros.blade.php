@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Regional Offices</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a data-bs-toggle="modal" data-bs-target="#createRo" class="btn btn-primary"><em
                                            class="icon ni ni-plus"></em> &nbsp; Create Ro User</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ro Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ros as $key => $ro)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $ro->m04_name }}</td>
                                                <td>{{ $ro->m04_email }}</td>
                                                <td>{{ $ro->m04_phone }}</td>
                                                <td class="text-{{ $ro->m04_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $ro->m04_status }}</strong>
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
                                                                                data-name="{{ $ro->m04_name }}"
                                                                                data-email="{{ $ro->m04_email }}"
                                                                                data-phone="{{ $ro->m04_phone }}"
                                                                                data-id="{{ $ro->m04_ro_id }}"
                                                                                data-bs-target="#updateRo"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit
                                                                                </span></a></li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $ro->m04_ro_id }}"
                                                                                data-status="{{ $ro->m04_status }}"><em
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
    <div class="modal fade zoom" tabindex="-1" id="createRo">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_ro') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Regional Office Login</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="txt_name">Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_name" id="txt_name" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="txt_email">Email</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_email" id="txt_email" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="txt_phone">Phone Number</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_phone" id="txt_phone" autocomplete="off" required>
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
    <div class="modal fade zoom" tabindex="-1" id="updateRo">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('update_ro') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Regional Office Details</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <input type="hidden" name="txt_edit_id" id="txt_edit_id">
                    <div class="modal-body row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="txt_edit_name">Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_edit_name" id="txt_edit_name"
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="txt_edit_email">Email</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_edit_email" id="txt_edit_email"
                                   autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="txt_edit_phone">Phone number</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_edit_phone" id="txt_edit_phone"
                                   autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                $('#txt_edit_id').val($(this).data('id'));
                $('#txt_edit_name').val($(this).data('name'));
                $('#txt_edit_email').val($(this).data('email'));
                $('#txt_edit_phone').val($(this).data('phone'));
            });


            $(document).on('click', '.eg-swal-av3', function(e) {
                e.preventDefault();
                let roId = $(this).data('id');
                let currentStatus = $(this).data('status');
                let newStatus = currentStatus === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Change status to ${newStatus}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/change-status-ro',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: roId
                            },
                            success: function(data) {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();

                                    });
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
