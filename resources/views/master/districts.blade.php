@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-lg mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">District List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a href="#" onclick="window.history.back()" class="btn btn-primary">
                                        <em class="icon ni ni-chevron-left"></em> &nbsp; Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>District</th>
                                            <th>State</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($districts as $key => $district)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $district->m02_name }}</td>
                                                <td>{{ $district->state->m01_name ?? '-' }}</td>
                                                <td id="status-{{ $district->m02_district_id }}"
                                                    class="text-{{ $district->m02_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $district->m02_status }}</strong>
                                                </td>

                                                <td>
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
                                                                            <a href="#" class="btn edit-btn"
                                                                                data-id="{{ $district->m02_district_id }}"
                                                                                data-name="{{ $district->m02_name }}"
                                                                                data-state-id="{{ $district->m01_state_id }}"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalZoom">
                                                                                <em class="icon ni ni-edit"></em>
                                                                                <span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $district->m02_district_id }}"
                                                                                data-status="{{ $district->m02_status }}"><em
                                                                                    class="icon ni ni-trash"></em><span>Change
                                                                                    Status</span></a>
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

    <!-- Edit Modal -->
    <div class="modal fade zoom" tabindex="-1" id="modalZoom">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('update_district') }}" class="form-validate is-alter">
                    @csrf
                    <input type="hidden" name="district_id" id="district_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Update District</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="district_name">District Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="district_name" id="district_name"
                                        required>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label" for="state_id">State</label>
                                <div class="form-control-wrap">
                                    <select name="state_id" class="form-control" id="state_id" required>
                                        <option value="">-- Select State --</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->m01_state_id }}">{{ $state->m01_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
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
            $('.edit-btn').on('click', function() {
                $('#district_id').val($(this).data('id'));
                $('#district_name').val($(this).data('name'));
                $('#state_id').val($(this).data('state-id'));
            });

            //status change
            $('.eg-swal-av3').on('click', function(e) {
                e.preventDefault();

                let districtId = $(this).data('id');
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
                            url: '/district/change-status',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: districtId
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
                                        location.reload();
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
