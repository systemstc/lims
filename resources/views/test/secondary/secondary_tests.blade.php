@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <!-- Title -->
                        <div class="nk-block-head d-flex justify-content-between align-items-center">
                            <h5 class="nk-block-title">Secondary Test Mast</h5>
                            <a href="{{ route('create_secondary') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em> &nbsp; Create Secondary Test
                            </a>
                        </div>

                        <!-- Table -->
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export table table-bordered table-hover"
                                    data-export-title="Export">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Sr. No</th>
                                            <th>Sample</th>
                                            <th>Group</th>
                                            {{-- <th>Test</th> --}}
                                            <th>1st Test</th>
                                            <th>2nd Test</th>
                                            <th>Unit</th>
                                            <th>Created By</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($secondaryTests as $key => $test)
                                            <tr class="text-center">
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $test->sample->m10_name ?? '' }}</td>
                                                <td>{{ $test->group->m11_name ?? '' }}</td>
                                                {{-- <td>{{ $test->test->m12_name ?? '' }}</td> --}}
                                                <td>{{ $test->primaryTest->m16_name ?? '' }}</td>
                                                <td>{{ $test->m17_name }}</td>
                                                <td>{{ $test->m17_unit }}</td>
                                                <td>{{ $test->user->tr01_name ?? '' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($test->created_at)->format('d M Y') }}</td>
                                                <td class="text-{{ $test->m17_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $test->m17_status }}</strong>
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
                                                                            <a href="{{ route('update_secondary_test', $test->m17_secondary_test_id) }}"
                                                                                class="edit-btn btn">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="btn eg-swal-av3"
                                                                                data-id="{{ $test->m17_secondary_test_id }}"
                                                                                data-status="{{ $test->m17_status }}">
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

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // To change the status 
                bindToggleStatus('.eg-swal-av3', "{{ route('delete_secondary_test') }}");
            });
        </script>
    @endsection
