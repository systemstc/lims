@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Primary Test List</h4>
                                <a href="{{ route('create_primary') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em> &nbsp; Create Primary Test
                                </a>
                            </div>
                        </div>
                        {{-- <form action="{{ route('import_all_primary') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Select CSV File</label>
                                <input type="file" name="csv_file" class="form-control" required accept=".csv">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Upload & Import</button>
                        </form> --}}
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export table table-bordered table-hover"
                                    data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sample</th>
                                            <th>Group</th>
                                            {{-- <th>Test</th> --}}
                                            <th>Parameter</th>
                                            <th>Unit</th>
                                            <th>Requirement</th>
                                            <th>Remark</th>
                                            <th>Created By</th>
                                            {{-- <th>Created At</th> --}}
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($primaryTests as $key => $primary)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $primary->sample->m10_name ?? '' }}</td>
                                                <td>{{ $primary->group->m11_name ?? '' }}</td>
                                                {{-- <td>{{ $primary->test->m12_name ?? ''}}</td> --}}
                                                <td>{{ $primary->m16_name }}</td>
                                                <td>{{ $primary->m16_unit }}</td>
                                                <td>{{ $primary->m16_requirement }}</td>
                                                <td>{{ $primary->m16_remark }}</td>
                                                <td>{{ $primary->tr01_created_by == -1 ? 'ADMIN' : $primary->user->tr01_name }}</td>
                                                {{-- <td>{{ $primary->created_at }}</td> --}}
                                                <td
                                                    class="text-{{ $primary->m16_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $primary->m16_status }}</strong>
                                                </td>
                                                <td>
                                                    <ul class="nk-tb-actions gx-1 my-n1">
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown"><em
                                                                        class="icon ni ni-more-h"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="{{ route('update_primary_test', $primary->m16_primary_test_id) }}"
                                                                                class="btn"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span></a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $primary->m16_primary_test_id }}"
                                                                                data-status="{{ $primary->m16_status }}"><em
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

    <script>
        $(document).ready(function() {
            // To change the status 
            bindToggleStatus('.eg-swal-av3', "{{ route('delete_primary_test') }}");
        });
    </script>
@endsection
