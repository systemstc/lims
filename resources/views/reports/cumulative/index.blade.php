@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Cumulative Report Generator</h3>
                            <div class="nk-block-des text-soft">
                                <p>Generate a single cumulative test report for multiple samples from the same customer.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <ul class="nk-block-tools g-3">
                                <li>
                                    <a href="{{ route('test_results') }}" class="btn btn-outline-primary btn-sm">
                                        <em class="icon ni ni-caret-left-fill"></em> Back to Test Results
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-icon">
                        <em class="icon ni ni-cross-circle"></em>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="card card-bordered card-stretch">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h5 class="card-title">Select Customer & Test</h5>
                                    </div>
                                    <form method="POST" action="{{ route('show_cumulative_report') }}">
                                        @csrf
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="customer_id">Customer <span class="text-danger">*</span></label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select js-select2" id="customer_id" name="customer_id" data-search="on" required>
                                                            <option value="">Select Customer</option>
                                                            @foreach ($customers as $customer)
                                                                <option value="{{ $customer->m07_customer_id }}">
                                                                    {{ $customer->m07_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="test_number">Test / Method <span class="text-danger">*</span></label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select js-select2" id="test_number" name="test_number" data-search="on" required>
                                                            <option value="">Select Test</option>
                                                            @foreach ($tests as $t)
                                                                <option value="{{ $t->m12_test_number }}">
                                                                    {{ $t->m12_name }} [{{ $t->m12_test_number }}]
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-lg btn-primary">
                                                    <em class="icon ni ni-forward-ios"></em> Proceed to Preview
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
