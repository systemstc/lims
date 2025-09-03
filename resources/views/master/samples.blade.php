@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Sample List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a data-bs-toggle="modal" data-bs-target="#createSample" class="btn btn-primary"><em
                                            class="icon ni ni-plus"></em> &nbsp; Create Sample</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sample</th>
                                            <th>Remark</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($samples as $key => $sample)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $sample->m10_name }}</td>
                                                <td>{{ $sample->m10_remark }}</td>
                                                <td>{{ $sample->user->tr01_name }}</td>
                                                <td>{{ $sample->created_at }}</td>
                                                <td
                                                    class="text-{{ $sample->m10_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $sample->m10_status }}</strong>
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
                                                                                data-name="{{ $sample->m10_name }}"
                                                                                data-remark="{{ $sample->m10_remark }}"
                                                                                data-id="{{ $sample->m10_sample_id }}"
                                                                                data-bs-target="#updateSample"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit
                                                                                </span></a></li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $sample->m10_sample_id }}"
                                                                                data-status="{{ $sample->m10_status }}"><em
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
    <div class="modal fade zoom" tabindex="-1" id="createSample">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_sample') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Sample</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="txt_sample_name">Sample Name<b class="text-danger">*</b></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control @error('txt_sample_name') is-invalid @enderror"
                                    name="txt_sample_name" id="txt_sample_name" value="{{ old('txt_sample_name') }}">
                                @error('txt_sample_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="txt_remark">Remark</label>
                            <div class="form-control-wrap">
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

    {{-- Edit & Update Model --}}
    <div class="modal fade zoom" tabindex="-1" id="updateSample">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('update_sample') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Sample</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <input type="hidden" name="txt_edit_sample_id" id="txt_edit_sample_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="txt_edit_sample_name">Sample Name</label>
                            <div class="form-control-wrap">
                                <input type="text"
                                    class="form-control @error('txt_edit_sample_name') is-invalid @enderror"
                                    name="txt_edit_sample_name" id="txt_edit_sample_name"
                                    value="{{ old('txt_edit_sample_name') }}">
                                @error('txt_edit_sample_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="txt_edit_remark">Remark</label>
                            <div class="form-control-wrap">
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

    @if ($errors->has('txt_sample_name') || $errors->has('txt_remark'))
        <script>
            $(document).ready(function() {
                $('#createSample').modal('show');
            });
        </script>
    @endif

    @if ($errors->has('txt_edit_sample_name') || $errors->has('txt_edit_remark'))
        <script>
            $(document).ready(function() {
                $('#updateSample').modal('show');
            });
        </script>
    @endif


    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                $('#txt_edit_sample_id').val($(this).data('id'));
                $('#txt_edit_sample_name').val($(this).data('name'));
                $('#txt_edit_remark').val($(this).data('remark'));
            });

            // To change the status 
            bindToggleStatus('.eg-swal-av3', "{{ route('delete_sample') }}");
        });
    </script>
@endsection
