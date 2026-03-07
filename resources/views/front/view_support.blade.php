@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Support ticket</h4>
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary">
                                    <em class="icon ni ni-back-alt-fill"></em>&nbsp; Back.
                                </a>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export wrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>Sr. No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Message</th>
                                            <th>Organization</th>
                                            <th>Laboratory</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($supports as $key => $support)
                                            <tr class="nk-tb-item">
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $support->tr11_first_name }} {{ $support->tr11_last_name ?? '' }}</td>
                                                <td><b>{{ $support->tr11_email }}</b></td>
                                                <td><b>{{ $support->tr11_phone }}</b></td>
                                                <td>{{ $support->tr11_message }}</td>
                                                <td><span class="badge bg-success">{{ $support->tr11_organization }}</span>
                                                </td>
                                                <td>{{ $support->tr11_laboratory }}</td>
                                                <td>{{ date('d-m-Y', strtotime($support->created_at)) }}</td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                    {{-- <ul class="nk-tb-actions gx-1">
                                                        <li class="nk-tb-action-hidden">
                                                            <a href="{{ route('view_wallet', $customer->m07_customer_id) }}"
                                                                class="btn btn-trigger btn-icon" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="Wallet">
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
                                                    </ul> --}}
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
