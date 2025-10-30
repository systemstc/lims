@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <h4 class="nk-block-title">Manuscripts</h4>
                                <div class="text-end">
                                    <a href="{{ route('create_manuscript') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>&nbsp; Create
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card shadow-lg">
                                        <div class="card-body">
                                            <h5 class="card-title text-center mb-4">Upload Manuscripts CSV</h5>
                                            <form action="{{ route('manuscripts_import') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="file" name="csv_file" class="form-control" required
                                                        accept=".csv">
                                                    <button type="submit" class="btn btn-primary">Upload & Import</button>
                                                </div>
                                            </form>
                                            @if (session('success'))
                                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="card card-bordered card-preview mt-4">
                            <div class="card-inner">
                                <table class="datatable-init-export wrap table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sample ID</th>
                                            <th>Group Code</th>
                                            <th>Test Number</th>
                                            <th>Name</th>
                                            <th>Created By</th>
                                            {{-- <th>Created At</th> --}}
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($manuscripts as $key => $m)
                                            <tr>
                                                <td>{{ $m->m22_manuscript_id }}</td>
                                                <td>{{ $m->m10_sample_id }}</td>
                                                <td>{{ $m->m11_group_code }}</td>
                                                <td>{{ $m->m12_test_number }}</td>
                                                <td>{{ $m->m22_name }}</td>
                                                <td>{{ $m->tr01_created_by == -1 ? 'ADMIN' : $m->user->tr01_created_by }}
                                                </td>
                                                {{-- <td>{{ $m->created_at }}</td> --}}
                                                <td class="text-{{ $m->m22_status == 'ACTIVE' ? 'success' : 'danger' }}">
                                                    <strong>{{ $m->m22_status }}</strong>
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
    </div>
@endsection
