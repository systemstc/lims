@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Standard (Methods) List</h4>
                                <a href="{{ route('create_standard_main') }}" class="btn btn-primary"><em
                                        class="icon ni ni-plus"></em>
                                    &nbsp; Create standard</a>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sample</th>
                                            <th>Group</th>
                                            <th>Standard</th>
                                            {{-- <th>Description</th> --}}
                                            {{-- <th>Alias</th>
                                            <th>Unit</th>
                                            <th>Charge</th>
                                            <th>Instrument</th>
                                            <th>Weight</th>
                                            <th>Department </th> --}}
                                            {{-- <th>Remark</th> --}}
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($standards as $key => $standard)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $standard->sample->m10_name ?? '' }}</td>
                                                <td>{{ $standard->group->m11_name ?? '' }}</td>
                                                <td>{{ $standard->m15_method }}</td>
                                                {{-- <td>{{ $standard->m12_description }}</td> --}}
                                                {{-- <td>{{ $standard->m12_alias }}</td>
                                                <td>{{ $standard->m12_unit }}</td>
                                                <td>{{ $standard->m12_charge }}</td>
                                                <td>{{ $standard->m12_instrument }}</td>
                                                <td>{{ $standard->m12_weight }}</td>
                                                <td>{{ $standard->department->m13_name }}</td> --}}
                                                {{-- <td>{{ $standard->m12_remark }}</td> --}}
                                                <td>{{ $standard->user->tr01_name }}</td>
                                                <td>{{ $standard->created_at }}</td>
                                                <td
                                                    class="text-{{ $standard->m15_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $standard->m15_status }}</strong>
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
                                                                        <li><a href="{{ route('update_standard', $standard->m15_standard_id) }}"
                                                                                class="edit-btn btn">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $standard->m15_standard_id }}"
                                                                                data-status="{{ $standard->m15_status }}"><em
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
            bindToggleStatus('.eg-swal-av3', "{{ route('delete_standard') }}");
        });
    </script>
@endsection
