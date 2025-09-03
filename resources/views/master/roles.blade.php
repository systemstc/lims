@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">State List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a data-bs-toggle="modal" data-bs-target="#createRole" class="btn btn-primary"><em
                                            class="icon ni ni-plus"></em> &nbsp; Create Role</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $key => $role)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $role->m03_name }}</td>
                                                <td class="text-{{ $role->m03_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $role->m03_status }}</strong>
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
                                                                                data-name="{{ $role->m03_name }}"
                                                                                data-id="{{ $role->m03_role_id }}"
                                                                                data-bs-target="#updateRole"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit
                                                                                </span></a></li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $role->m03_role_id }}"
                                                                                data-status="{{ $role->m03_status }}"><em
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
    <div class="modal fade zoom" tabindex="-1" id="createRole">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_role') }}" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="district_id" id="district_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Role</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="txt_role">Role Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_role" id="txt_role" required>
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
    <div class="modal fade zoom" tabindex="-1" id="updateRole">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('update_role') }}" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="district_id" id="district_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Role</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <input type="hidden" name="txt_edit_id" id="txt_edit_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="txt_edit_role">Role Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="txt_edit_role" id="txt_edit_role"
                                    required>
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
                $('#txt_edit_role').val($(this).data('name'));
            });
            // To change the status 
            bindToggleStatus('.eg-swal-av3', "{{ route('change_role_status') }}");
        });
    </script>
@endsection
