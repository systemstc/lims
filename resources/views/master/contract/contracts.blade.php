@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Contracts</h4>
                                <a href="{{ route('create_contract') }}" class="btn btn-primary"><em
                                        class="icon ni ni-plus"></em>
                                    &nbsp; Create</a>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Contract</th>
                                            <th>Tests</th>
                                            <th>Contract With</th>
                                            <th>Charge</th>
                                            {{-- <th>Created By</th> --}}
                                            {{-- <th>Created At</th> --}}
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @dd($packages); --}}
                                        @foreach ($packages as $key => $package)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $package->m19_name }}</td>
                                                <td>
                                                    @foreach ($package->packageTests as $pkgTest)
                                                        <span class="badge bg-primary">
                                                            {{ $pkgTest->test->m12_name ?? 'N/A' }} -
                                                            {{ $pkgTest->standard->m15_method ?? 'N/A' }}
                                                        </span><br>
                                                    @endforeach
                                                </td>
                                                <td>{{ $package->customer->m07_name ?? ''}}</td>
                                                <td>{{ $package->m19_charges }}</td>
                                                <td>{{ $package->m19_exp_date }}</td>
                                                {{-- <td>{{ $package->user->tr01_name }}</td> --}}
                                                {{-- <td>{{ $package->created_at }}</td> --}}
                                                <td
                                                    class="text-{{ $package->m19_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $package->m19_status }}</strong>
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
                                                                        <li><a href="{{ route('update_contract', $package->m19_package_id) }}"
                                                                                class="edit-btn btn">
                                                                                <em
                                                                                    class="icon ni ni-edit"></em><span>Edit</span>
                                                                            </a>
                                                                        </li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $package->m19_package_id }}"
                                                                                data-status="{{ $package->m19_status }}"><em
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
            $(document).on('click', '.eg-swal-av3', function(e) {
                e.preventDefault();
                let packageId = $(this).data('id');
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
                            url: "{{ route('delete_package') }}",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: packageId
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
