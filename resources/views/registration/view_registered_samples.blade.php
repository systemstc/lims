@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title mb-0">All Registered Samples</h4>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">
                                    <em class="icon ni ni-back-alt-fill"></em> &nbsp; Back
                                </a>
                            </div>
                        </div>
                        <div class="nk-block">
                            <div class="card card-stretch">
                                <div class="card-inner-group">
                                    <div class="card-inner p-0">
                                        <table class="table" id="samples-table">
                                            <thead>
                                                <tr>
                                                    <th>Sample ID</th>
                                                    <th>Date</th>
                                                    <th>Sample Description</th>
                                                    <th>Customer Type</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Tests</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- DataTables will populate this --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#samples-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('view_registered_samples') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'sample_id',
                        name: 'sample_id'
                    },
                    {
                        data: 'created_date',
                        name: 'created_at'
                    },
                    {
                        data: 'sample_description',
                        name: 'sample_description'
                    },
                    {
                        data: 'customer_type',
                        name: 'customer_type'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'total_tests',
                        name: 'total_tests'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: "No registered samples found",
                    zeroRecords: "No matching records found"
                }
            });
        });
    </script>
@endsection
