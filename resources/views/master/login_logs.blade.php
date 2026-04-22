@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Login Activity Logs</h4>
                    <div class="nk-block-des d-flex justify-content-between align-items-center">
                        <p>Track all successful and failed login attempts across the system.</p>
                        <a href="#" onclick="history.back(); return false;" class="btn btn-primary">
                            <em class="icon ni ni-chevron-left"></em> &nbsp; Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="nowrap table" id="login-logs-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User/Email</th>
                                <th>IP Address</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Status</th>
                                <th>Browser/Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#login-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('view_login_logs') }}",
                    error: function(xhr) {
                        console.error('DataTables AJAX Error:', xhr.responseText);
                    }
                },
                order: [[3, 'desc']], // Sort by Login Time by default
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_info',
                        name: 'tr00_email'
                    },
                    {
                        data: 'ip_address',
                        name: 'tr00_ip_address'
                    },
                    {
                        data: 'login_at',
                        name: 'tr00_login_at'
                    },
                    {
                        data: 'logout_at',
                        name: 'tr00_logout_at'
                    },
                    {
                        data: 'status',
                        name: 'tr00_successful'
                    },
                    {
                        data: 'browser',
                        name: 'tr00_user_agent'
                    }
                ],
                responsive: true,
                autoWidth: false,
                language: {
                    search: "",
                    searchPlaceholder: "Type in to Search",
                    lengthMenu: "<span class='d-none d-sm-inline-block'>Show</span> _MENU_",
                    info: "_START_ - _END_ of _TOTAL_",
                    infoEmpty: "No records found",
                    infoFiltered: "( Total _MAX_ )",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Prev"
                    }
                }
            });
        });
    </script>
@endsection
