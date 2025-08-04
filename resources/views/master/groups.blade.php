@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Group List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a data-bs-toggle="modal" data-bs-target="#createGroup" class="btn btn-primary"><em
                                            class="icon ni ni-plus"></em> &nbsp; Create group</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Group Code</th>
                                            <th>Sample Name</th>
                                            <th>Group Name</th>
                                            <th>Group Charge</th>
                                            <th>Remark</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groups as $key => $group)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $group->m11_group_code }}</td>
                                                <td>{{ $group->sample->m10_name }}</td>
                                                <td>{{ $group->m11_name }}</td>
                                                <td>{{ $group->m11_group_charge }}</td>
                                                <td>{{ $group->m11_remark }}</td>
                                                <td>{{ $group->user->tr01_name }}</td>
                                                <td>{{ $group->created_at }}</td>
                                                <td
                                                    class="text-{{ $group->m11_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $group->m11_status }}</strong>
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
                                                                                data-id="{{ $group->m11_group_id }}"
                                                                                data-name="{{ $group->m11_name }}"
                                                                                data-remark="{{ $group->m11_remark }}"
                                                                                data-charge="{{ $group->m11_group_charge }}"
                                                                                data-sample="{{ $group->m10_sample_id }}"
                                                                                data-bs-target="#updateGroup">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $group->m11_group_id }}"
                                                                                data-status="{{ $group->m11_status }}"><em
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
    <div class="modal fade zoom" tabindex="-1" id="createGroup">
        <div class="modal-dialog modal-lg" role="document"> {{-- Enlarged modal --}}
            <div class="modal-content">
                <form method="POST" action="{{ route('create_group') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Group</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- Sample Dropdown --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_sample_id">Sample <b class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <select name="txt_sample_id" id="txt_sample_id"
                                        class="form-control @error('txt_sample_id') is-invalid @enderror">
                                        <option value="">-- Select Sample --</option>
                                        @foreach ($samples as $sample)
                                            <option value="{{ $sample->m10_sample_id }}"
                                                {{ old('txt_sample_id') == $sample->m10_sample_id ? 'selected' : '' }}>
                                                {{ $sample->m10_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('txt_sample_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Group Name --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_group_name">Group Name <b
                                        class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('txt_group_name') is-invalid @enderror"
                                        name="txt_group_name" id="txt_group_name" value="{{ old('txt_group_name') }}">
                                    @error('txt_group_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Group Charge --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_group_charge">Group Charge <b
                                        class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <input type="number" step="0.01"
                                        class="form-control @error('txt_group_charge') is-invalid @enderror"
                                        name="txt_group_charge" id="txt_group_charge"
                                        value="{{ old('txt_group_charge') }}">
                                    @error('txt_group_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Remark --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_remark">Remark</label>
                                <div class="form-control-wrap">
                                    <input class="form-control @error('txt_remark') is-invalid @enderror" name="txt_remark"
                                        id="txt_remark" value="{{ old('txt_remark') }}">
                                    @error('txt_remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div> <!-- .row -->
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
    <div class="modal fade zoom" tabindex="-1" id="updateGroup">
        <div class="modal-dialog modal-lg" role="document"> {{-- Large modal --}}
            <div class="modal-content">
                <form method="POST" action="{{ route('update_group') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Group</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>

                    <input type="hidden" name="txt_edit_group_id" id="txt_edit_group_id">

                    <div class="modal-body">
                        <div class="row">
                            {{-- Sample Dropdown --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_sample_id">Sample <b
                                        class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <select name="txt_edit_sample_id" id="txt_edit_sample_id"
                                        class="form-control @error('txt_edit_sample_id') is-invalid @enderror">
                                        <option value="">-- Select Sample --</option>
                                        @foreach ($samples as $sample)
                                            <option value="{{ $sample->m10_sample_id }}"
                                                {{ old('txt_edit_sample_id') == $sample->m10_sample_id ? 'selected' : '' }}>
                                                {{ $sample->m10_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('txt_edit_sample_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Group Name --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_group_name">Group Name <b
                                        class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <input type="text"
                                        class="form-control @error('txt_edit_group_name') is-invalid @enderror"
                                        name="txt_edit_group_name" id="txt_edit_group_name"
                                        value="{{ old('txt_edit_group_name') }}">
                                    @error('txt_edit_group_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Group Charge --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_group_charge">Group Charge <b
                                        class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <input type="number" step="0.01"
                                        class="form-control @error('txt_edit_group_charge') is-invalid @enderror"
                                        name="txt_edit_group_charge" id="txt_edit_group_charge"
                                        value="{{ old('txt_edit_group_charge') }}">
                                    @error('txt_edit_group_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Remark --}}
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_remark">Remark</label>
                                <div class="form-control-wrap">
                                    <input class="form-control @error('txt_edit_remark') is-invalid @enderror"
                                        name="txt_edit_remark" id="txt_edit_remark"
                                        value="{{ old('txt_edit_remark') }}">
                                    @error('txt_edit_remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div> <!-- .row -->
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @if ($errors->has('txt_group_name') || $errors->has('txt_remark'))
        <script>
            $(document).ready(function() {
                $('#createGroup').modal('show');
            });
        </script>
    @endif

    @if ($errors->has('txt_edit_group_name') || $errors->has('txt_edit_remark'))
        <script>
            $(document).ready(function() {
                $('#updateGroup').modal('show');
            });
        </script>
    @endif


    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                $('#txt_edit_group_id').val($(this).data('id'));
                $('#txt_edit_group_name').val($(this).data('name'));
                $('#txt_edit_group_charge').val($(this).data('charge'));
                $('#txt_edit_sample_id').val($(this).data('sample'));
                $('#txt_edit_remark').val($(this).data('remark'));
            });


            $(document).on('click', '.eg-swal-av3', function(e) {
                e.preventDefault();
                let groupId = $(this).data('id');
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
                            url: '/delete-group',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: groupId
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
