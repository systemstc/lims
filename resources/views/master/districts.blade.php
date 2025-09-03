@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
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
                    <table class="nowrap table" id="districts-table">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
            $('#districts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('view_districts') }}",
                    dataSrc: function(json) {
                        console.log('AJAX response:', json);
                        return json.data;
                    },
                    error: function(xhr) {
                        console.error('DataTables AJAX Error:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'district_name',
                        name: 'm02_name'
                    },
                    {
                        data: 'state_name',
                        name: 'state.m01_name'
                    },
                    {
                        data: 'status',
                        name: 'm02_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '.edit-btn', function() {
                $('#district_id').val($(this).data('id'));
                $('#district_name').val($(this).data('name'));
                $('#state_id').val($(this).data('state-id'));
            });

            // To change the status 
            bindToggleStatus('.eg-swal-av3', "{{ route('change_district_status') }}");
        });
    </script>
@endsection
