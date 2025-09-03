@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Test List</h4>
                                <a href="{{ route('create_test') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em>
                                    &nbsp; Create test</a>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export table table-bordered table-hover"
                                    data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sample</th>
                                            <th>Group</th>
                                            <th>Test</th>
                                            {{-- <th>Description</th> --}}
                                            <th>Alias</th>
                                            <th>Unit</th>
                                            <th>Charge</th>
                                            <th>Instr.</th>
                                            <th>Weight</th>
                                            {{-- <th>Dept. </th> --}}
                                            {{-- <th>Remark</th> --}}
                                            <th>Created By</th>
                                            {{-- <th>Created At</th> --}}
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tests as $key => $test)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $test->sample->m10_name }}</td>
                                                <td>{{ $test->group->m11_name }}</td>
                                                <td>{{ $test->m12_name }}</td>
                                                {{-- <td>{{ $test->m12_description }}</td> --}}
                                                <td>{{ $test->m12_alias }}</td>
                                                <td>{{ $test->m12_unit }}</td>
                                                <td>{{ $test->m12_charge }}</td>
                                                <td>{{ $test->m12_instrument }}</td>
                                                <td>{{ $test->m12_weight }}</td>
                                                {{-- <td>{{ $test->department->m13_name }}</td> --}}
                                                {{-- <td>{{ $test->m12_remark }}</td> --}}
                                                <td>{{ $test->user->tr01_name }}</td>
                                                {{-- <td>{{ $test->created_at }}</td> --}}
                                                <td
                                                    class="text-{{ $test->m12_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $test->m12_status }}</strong>
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
                                                                        <li><a href="{{ route('update_test', $test->m12_test_id) }}"
                                                                                class="edit-btn btn">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $test->m12_test_id }}"
                                                                                data-status="{{ $test->m12_status }}"><em
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
            bindToggleStatus('.eg-swal-av3', "{{ route('delete_test') }}");
        });
    </script>
@endsection
