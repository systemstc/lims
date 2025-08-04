@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Department List</h4>
                                <a data-bs-toggle="modal" data-bs-target="#createDepartment" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em> &nbsp; Create Department
                                </a>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Lab ID</th>
                                            <th>Department</th>
                                            <th>Sample No</th>
                                            <th>Remark</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departments as $key => $department)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $department->m04_ro_id }}</td>
                                                <td>{{ $department->m13_name }}</td>
                                                <td>{{ $department->m13_sample_no }}</td>
                                                <td>{{ $department->m13_remark }}</td>
                                                <td>{{ $department->user->tr01_name }}</td>
                                                <td>{{ $department->created_at }}</td>
                                                <td
                                                    class="text-{{ $department->m13_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $department->m13_status }}</strong>
                                                </td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                    <ul class="nk-tb-actions gx-1 my-n1">
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown">
                                                                    <em class="icon ni ni-more-h"></em>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li>
                                                                            <a data-bs-toggle="modal" class="edit-btn btn"
                                                                                data-name="{{ $department->m13_name }}"
                                                                                data-sample="{{ $department->m13_sample_no }}"
                                                                                data-remark="{{ $department->m13_remark }}"
                                                                                data-id="{{ $department->m13_department_id }}"
                                                                                data-bs-target="#updateDepartment">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="btn eg-swal-av3"
                                                                                data-id="{{ $department->m13_department_id }}"
                                                                                data-status="{{ $department->m13_status }}">
                                                                                <em class="icon ni ni-trash"></em><span>Change
                                                                                    Status</span>
                                                                            </a>
                                                                        </li>
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

    {{-- Create Department Modal --}}
    <div class="modal fade zoom" tabindex="-1" id="createDepartment">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_department') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Department</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_department_name">Department Name<b
                                        class="text-danger">*</b></label>
                                <input type="text"
                                    class="form-control @error('txt_department_name') is-invalid @enderror"
                                    name="txt_department_name" id="txt_department_name"
                                    value="{{ old('txt_department_name') }}">
                                @error('txt_department_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_sample_no">Sample No<b class="text-danger">*</b></label>
                                <input type="text" class="form-control @error('txt_sample_no') is-invalid @enderror"
                                    name="txt_sample_no" id="txt_sample_no" value="{{ old('txt_sample_no') }}">
                                @error('txt_sample_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12">
                                <label class="form-label" for="txt_remark">Remark</label>
                                <textarea class="form-control @error('txt_remark') is-invalid @enderror" name="txt_remark" id="txt_remark">{{ old('txt_remark') }}</textarea>
                                @error('txt_remark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

    {{-- Edit Department Modal --}}
    <div class="modal fade zoom" tabindex="-1" id="updateDepartment">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('update_department') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Department</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <input type="hidden" name="txt_edit_department_id" id="txt_edit_department_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_department_name">Department Name</label>
                                <input type="text"
                                    class="form-control @error('txt_edit_department_name') is-invalid @enderror"
                                    name="txt_edit_department_name" id="txt_edit_department_name"
                                    value="{{ old('txt_edit_department_name') }}">
                                @error('txt_edit_department_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_sample_no">Sample No</label>
                                <input type="text"
                                    class="form-control @error('txt_edit_sample_no') is-invalid @enderror"
                                    name="txt_edit_sample_no" id="txt_edit_sample_no"
                                    value="{{ old('txt_edit_sample_no') }}">
                                @error('txt_edit_sample_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12">
                                <label class="form-label" for="txt_edit_remark">Remark</label>
                                <textarea class="form-control @error('txt_edit_remark') is-invalid @enderror" name="txt_edit_remark"
                                    id="txt_edit_remark">{{ old('txt_edit_remark') }}</textarea>
                                @error('txt_edit_remark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

    {{-- Validation Trigger Scripts --}}
    @if ($errors->has('txt_department_name') || $errors->has('txt_sample_no') || $errors->has('txt_remark'))
        <script>
            $(document).ready(function() {
                $('#createDepartment').modal('show');
            });
        </script>
    @endif

    @if ($errors->has('txt_edit_department_name') || $errors->has('txt_edit_sample_no') || $errors->has('txt_edit_remark'))
        <script>
            $(document).ready(function() {
                $('#updateDepartment').modal('show');
            });
        </script>
    @endif

    {{-- Script --}}
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                $('#txt_edit_department_id').val($(this).data('id'));
                $('#txt_edit_department_name').val($(this).data('name'));
                $('#txt_edit_sample_no').val($(this).data('sample'));
                $('#txt_edit_remark').val($(this).data('remark'));
            });

            $(document).on('click', '.eg-swal-av3', function(e) {
                e.preventDefault();
                let departmentId = $(this).data('id');
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
                            url: '/delete-department',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: departmentId
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
