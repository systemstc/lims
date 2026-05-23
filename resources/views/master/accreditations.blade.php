@extends('layouts.app_back')
@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">NABL Accredited Parameters List</h4>
                                <div class="nk-block-des d-flex justify-content-end">
                                    <a data-bs-toggle="modal" data-bs-target="#createAccreditation"
                                        class="btn btn-primary"><em class="icon ni ni-plus"></em> &nbsp; Add More</a>
                                </div>
                            </div>
                        </div>

                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table" data-export-title="Export">
                                    <thead>
                                        <tr>
                                            <th>Sr. No</th>
                                            <th>Ro</th>
                                            <th>Standard</th>
                                            <th>Test/Parameter</th>
                                            <th>Accreditation Status</th>
                                            <th>Issue Date</th>
                                            <th>Expire Date</th>
                                            <th>Added By</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accreditations as $key => $accreditation)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $accreditation->ro->m04_name ?? '_' }}</td>
                                                <td>{{ $accreditation->standard->m15_method ?? '_' }}</td>
                                                <td>{{ $accreditation->test->m12_name ?? '_' }}</td>
                                                <td>{{ $accreditation->m21_is_accredited }}</td>
                                                <td><b>{{ $accreditation->m21_accreditation_date }}</b></td>
                                                <td>{{ $accreditation->m21_valid_till }}</td>
                                                <td>{{ $accreditation->employee->m06_name ?? 'ADMIN' }}</td>
                                                <td>{{ $accreditation->created_at }}</td>
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
                                                                        <li><a class="btn eg-swal-av3"
                                                                                data-id="{{ $accreditation->m21_accreditation_id }}"
                                                                                data-status="{{ $accreditation->m21_is_accredited }}"><em
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

    {{-- Create Accreditation Modal --}}
    <div class="modal fade zoom" tabindex="-1" id="createAccreditation">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('create_accreditation') }}" class="form-validate is-alter">
                    @csrf

                    {{-- Modal Header --}}
                    <div class="modal-header">
                        <h5 class="modal-title">Create Accreditation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Test --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="dd_test">Test Parameter</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2" data-search="on" data-dropdown-parent="#createAccreditation" name="dd_test" id="dd_test" required>
                                            <option value="" disabled selected>Select Test</option>
                                            @foreach ($tests as $test)
                                                <option value="{{ $test->m12_test_id }}">
                                                    {{ $test->m12_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Standard --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="dd_standard">Standard</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2" data-search="on" data-dropdown-parent="#createAccreditation" name="dd_standard" id="dd_standard" required>
                                            <option value="" disabled selected>Select Standard</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Accredited --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_accredited">Accredited</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="txt_accredited" id="txt_accredited"
                                            value="Yes" readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- Accreditation Date --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_acc_date">Accreditation Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control" name="txt_acc_date" id="txt_acc_date"
                                            value="{{ now()->format('Y-m-d') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- Accreditation Till --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="txt_accredited_till">Accreditation Till</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control" name="txt_accredited_till"
                                            id="txt_accredited_till" required>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dd_test').on('change', function() {
            var testId = $(this).val();
            var standardSelect = $('#dd_standard');
            
            // Clear current options
            standardSelect.empty();
            standardSelect.append('<option value="" disabled selected>Select Standard</option>');
            
            if (testId) {
                $.ajax({
                    url: '{{ route("get_standards_by_test") }}',
                    type: 'GET',
                    data: { test_id: testId },
                    success: function(response) {
                        $.each(response, function(index, standard) {
                            standardSelect.append('<option value="' + standard.id + '">' + standard.name + '</option>');
                        });
                        // Re-initialize select2 if available
                        if (standardSelect.hasClass('select2-hidden-accessible')) {
                            standardSelect.select2('destroy').select2();
                        } else {
                            standardSelect.select2();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching standards');
                    }
                });
            }
        });
    });
</script>
@endsection
