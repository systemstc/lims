@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Lab Sample List</h4>
                                <a data-bs-toggle="modal" data-bs-target="#createSample" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em> &nbsp; Create
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
                                            <th>Sample</th>
                                            <th>Lab Sample</th>
                                            <th>Remark</th>
                                            <th>Order By</th>
                                            <th>Sample No</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($labSamples as $key => $labSample)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $labSample->ro->m04_name }}</td>
                                                <td>{{ $labSample->sample->m10_name }}</td>
                                                <td>{{ $labSample->m14_name }}</td>
                                                <td>{{ $labSample->m14_remark }}</td>
                                                <td>{{ $labSample->m14_order_by }}</td>
                                                <td>{{ $labSample->m14_sample_no }}</td>
                                                <td>{{ $labSample->user->tr01_name }}</td>
                                                <td>{{ $labSample->created_at }}</td>
                                                <td
                                                    class="text-{{ $labSample->m14_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $labSample->m14_status }}</strong>
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
                                                                                data-name="{{ $labSample->m14_name }}"
                                                                                data-sample="{{ $labSample->m14_sample_no }}"
                                                                                data-order="{{ $labSample->m14_order_by }}"
                                                                                data-remark="{{ $labSample->m14_remark }}"
                                                                                data-id="{{ $labSample->m14_lab_sample_id }}"
                                                                                data-sample-id="{{ $labSample->m10_sample_id }}"
                                                                                data-bs-target="#updateSample">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="btn eg-swal-av3"
                                                                                data-id="{{ $labSample->m14_lab_sample_id }}"
                                                                                data-status="{{ $labSample->m14_status }}">
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

    {{-- Create Lab Sample Modal --}}
    <div class="modal fade zoom" tabindex="-1" id="createSample">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_lab_sample') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create Lab Sample</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
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

                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_name">Lab Sample<b class="text-danger">*</b></label>
                                <input type="text" class="form-control @error('txt_name') is-invalid @enderror"
                                    name="txt_name" id="txt_name" value="{{ old('txt_name') }}">
                                @error('txt_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_order_by">Order By<b class="text-danger">*</b></label>
                                <input type="text" class="form-control @error('txt_order_by') is-invalid @enderror"
                                    name="txt_order_by" id="txt_order_by" value="{{ old('txt_order_by') }}">
                                @error('txt_order_by')
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

    {{-- Edit Lab Sample Modal --}}
    <div class="modal fade zoom" tabindex="-1" id="updateSample">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('update_lab_sample') }}" class="form-validate is-alter">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Lab Sample</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <input type="hidden" name="txt_edit_id" id="txt_edit_id">
                    <div class="modal-body">
                        <input type="hidden" name="txt_edit_sample_id" id="txt_edit_sample_id_hidden">
                        <div class="row g-3">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_sample_id">Sample <b
                                        class="text-danger">*</b></label>
                                <div class="form-control-wrap">
                                    <select id="txt_edit_sample_id"
                                        class="form-control @error('txt_edit_sample_id') is-invalid @enderror" disabled>
                                        <option value="">-- Select Sample --</option>
                                        @foreach ($samples as $sample)
                                            <option value="{{ $sample->m10_sample_id }}">
                                                {{ $sample->m10_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('txt_edit_sample_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_name">Lab Sample <b
                                        class="text-danger">*</b></label>
                                <input type="text" class="form-control @error('txt_edit_name') is-invalid @enderror"
                                    name="txt_edit_name" id="txt_edit_name" value="{{ old('txt_edit_name') }}">
                                @error('txt_edit_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_sample_no">Sample No <b
                                        class="text-danger">*</b></label>
                                <input type="text"
                                    class="form-control @error('txt_edit_sample_no') is-invalid @enderror"
                                    name="txt_edit_sample_no" id="txt_edit_sample_no"
                                    value="{{ old('txt_edit_sample_no') }}">
                                @error('txt_edit_sample_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="txt_edit_order_by">Order By<b
                                        class="text-danger">*</b></label>
                                <input type="text"
                                    class="form-control @error('txt_edit_order_by') is-invalid @enderror"
                                    name="txt_edit_order_by" id="txt_edit_order_by"
                                    value="{{ old('txt_edit_order_by') }}">
                                @error('txt_edit_order_by')
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


    {{-- Validation Trigger Script --}}
    @if ($errors->has('txt_name') || $errors->has('txt_sample_no') || $errors->has('txt_remark'))
        <script>
            $(document).ready(function() {
                $('#createSample').modal('show');
            });
        </script>
    @endif

    @if ($errors->has('txt_edit_name') || $errors->has('txt_edit_sample_no') || $errors->has('txt_edit_remark'))
        <script>
            $(document).ready(function() {
                $('#updateSample').modal('show');
            });
        </script>
    @endif


    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                $('#txt_edit_id').val($(this).data('id'));
                $('#txt_edit_name').val($(this).data('name'));
                $('#txt_edit_sample_no').val($(this).data('sample'));
                $('#txt_edit_remark').val($(this).data('remark'));
                $('#txt_edit_order_by').val($(this).data('order'));
                $('#txt_edit_sample_id').val($(this).data('sample-id'));
                $('#txt_edit_sample_id_hidden').val($(this).data('sample-id'));
            });

            $(document).on('click', '.eg-swal-av3', function(e) {
                e.preventDefault();
                let sampleId = $(this).data('id');
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
                            url: "{{ route('delete_lab_sample') }}",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: sampleId,
                                status: newStatus
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
