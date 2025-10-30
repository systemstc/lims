@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">Customer List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a href="{{ route('create_customer') }}" class="btn btn-primary"><em
                                            class="icon ni ni-plus"></em> &nbsp; Create Customer</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>Sr. No</th>
                                            {{-- <th>Customer Code</th> --}}
                                            <th>Customer Name</th>
                                            <th>Customer Type</th>
                                            <th>GST</th>
                                            <th>District</th>
                                            <th>Other Locations</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $key => $customer)
                                            <tr class="nk-tb-item">
                                                <td>{{ $key + 1 }}</td>
                                                {{-- <td>{{ $customer->m07_customer_code }}</td> --}}
                                                <td>{{ $customer->m07_name }}</td>
                                                <td>{{ $customer->customerType->m09_name ?? '' }}</td>
                                                <td><b>{{ $customer->m07_gst }}</b></td>
                                                <td>{{ $customer->district->m02_name }}</td>
                                                <td><span class="badge bg-success">{{ count($customer->locations) }}</span>
                                                </td>
                                                <td>{{ $customer->created_at }}</td>
                                                <td
                                                    class="text-{{ $customer->m07_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $customer->m07_status }}</strong>
                                                </td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                    <ul class="nk-tb-actions gx-1">
                                                        <li class="nk-tb-action-hidden">
                                                            <a href="{{ route('view_wallet', $customer->m07_customer_id) }}" class="btn btn-trigger btn-icon"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Wallet">
                                                                <em class="icon ni ni-wallet-fill"></em>
                                                            </a>
                                                        </li>
                                                        <li class="me-n1">
                                                            <div class="dropdown">
                                                                <a href="#"
                                                                    class="dropdown-toggle btn btn-icon btn-trigger"
                                                                    data-bs-toggle="dropdown"><em
                                                                        class="icon ni ni-more-h"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="{{ route('update_customer', $customer->m07_customer_id) }}"
                                                                                class="edit-btn btn"><em
                                                                                    class="icon ni ni-edit"></em><span>Edit
                                                                                </span></a></li>
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $customer->m07_customer_id }}"
                                                                                data-status="{{ $customer->m07_status }}"><em
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
            bindToggleStatus('.eg-swal-av3', "{{ route('delet_customer') }}");
        });
    </script>
@endsection
