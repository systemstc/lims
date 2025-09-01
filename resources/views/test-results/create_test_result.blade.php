@extends('layouts.app_back')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Add New Test Result</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Enter textile testing results with standards comparison</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ route('test_results') }}" class="btn btn-outline-light">
                                    <em class="icon ni ni-arrow-left"></em><span>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-lg-9">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <form action="{{ route('store') }}" method="POST" id="testResultForm">
                                            @csrf

                                            <!-- Basic Information -->
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <h5 class="title">Sample Information</h5>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Sample Code <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="sample_code"
                                                            value="{{ old('sample_code') }}" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Test Date <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="test_date"
                                                            value="{{ old('test_date', date('Y-m-d')) }}" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Sample Type</label>
                                                        <select class="form-select" name="sample_id" id="sample_id"
                                                            required>
                                                            <option value="">Select Sample</option>
                                                            @foreach ($samples as $sample)
                                                                <option value="{{ $sample->m13_sample_id }}">
                                                                    {{ $sample->m13_sample_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Group</label>
                                                    <select class="form-select" name="group_id" id="group_id" required>
                                                        <option value="">Select Group</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Test</label>
                                                    <select class="form-select" name="test_id" id="test_id" required>
                                                        <option value="">Select Test</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <hr class="preview-hr">

                                            <!-- Test Parameters -->
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <h5 class="title">Test Parameters & Results</h5>
                                                </div>
                                                <div class="col-12">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Parameter</th>
                                                                <th>Requirement</th>
                                                                <th>Unit</th>
                                                                <th>Result</th>
                                                                <th>Remark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="test-parameters-body">
                                                            <!-- Auto loaded from standards -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <hr class="preview-hr">

                                            <!-- Remarks -->
                                            <div class="row g-4">
                                                <div class="col-md-12">
                                                    <label class="form-label">Overall Remark</label>
                                                    <textarea class="form-control" name="overall_remark" rows="3" placeholder="Any observations or notes...">{{ old('overall_remark') }}</textarea>
                                                </div>
                                            </div>

                                            <hr class="preview-hr">

                                            <!-- Actions -->
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between">
                                                    <a href="{{ route('test_results') }}" class="btn btn-outline-light">
                                                        <em class="icon ni ni-arrow-left"></em><span>Cancel</span>
                                                    </a>
                                                    <div>
                                                        <button type="submit" name="action" value="draft"
                                                            class="btn btn-outline-primary me-2">
                                                            <em class="icon ni ni-save"></em> Save as Draft
                                                        </button>
                                                        <button type="submit" name="action" value="finalize"
                                                            class="btn btn-primary">
                                                            <em class="icon ni ni-check"></em> Save & Finalize
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="col-lg-3">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <h6 class="title">Instructions</h6>
                                        <ul class="list-unstyled">
                                            <li>➡ Select Sample → Group → Test</li>
                                            <li>➡ Parameters & requirements will load automatically</li>
                                            <li>➡ Enter results against each parameter</li>
                                            <li>➡ Draft can be edited later, Finalized is locked</li>
                                        </ul>
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
                // Load groups based on sample
                $('#sample_id').change(function() {
                    let sampleId = $(this).val();
                    $('#group_id').html('<option value="">Loading...</option>');
                    $.get("{{ route('fetch_groups') }}", {
                        sample_id: sampleId
                    }, function(data) {
                        $('#group_id').html('<option value="">Select Group</option>');
                        data.forEach(g => {
                            $('#group_id').append(
                                `<option value="${g.m11_group_id}">${g.m11_group_name}</option>`
                                );
                        });
                    });
                });

                // Load tests based on group
                $('#group_id').change(function() {
                    let groupId = $(this).val();
                    $('#test_id').html('<option value="">Loading...</option>');
                    $.get("{{ route('fetch_tests') }}", {
                        group_id: groupId
                    }, function(data) {
                        $('#test_id').html('<option value="">Select Test</option>');
                        data.forEach(t => {
                            $('#test_id').append(
                                `<option value="${t.m12_test_id}">${t.m12_test_name}</option>`
                                );
                        });
                    });
                });

                // Load parameters & standards when test is selected
                $('#test_id').change(function() {
                    let testId = $(this).val();
                    $('#test-parameters-body').html('<tr><td colspan="5">Loading...</td></tr>');
                    $.get("{{ route('fetch_standards') }}", {
                        test_id: testId
                    }, function(data) {
                        $('#test-parameters-body').empty();
                        data.forEach((p, i) => {
                            $('#test-parameters-body').append(`
                    <tr>
                        <td>${p.parameter_name}</td>
                        <td>${p.requirement || '-'}</td>
                        <td>${p.unit || '-'}</td>
                        <td><input type="text" name="results[${i}][value]" class="form-control"></td>
                        <td><input type="text" name="results[${i}][remark]" class="form-control"></td>
                        <input type="hidden" name="results[${i}][parameter_id]" value="${p.m16_primary_test_id}">
                    </tr>
                `);
                        });
                    });
                });

                // Confirm before finalize
                $('#testResultForm').submit(function(e) {
                    let action = $('button[type=submit][clicked=true]').val();
                    if (action === 'finalize' && !confirm("Finalize results? You cannot edit after this.")) {
                        e.preventDefault();
                    }
                });
                $('form button[type=submit]').click(function() {
                    $('button[type=submit]', $(this).parents('form')).removeAttr('clicked');
                    $(this).attr('clicked', 'true');
                });
            });
        </script>
    @endsection
