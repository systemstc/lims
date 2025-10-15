@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xl mx-auto">
                    <div class="nk-block nk-block-lg">

                        <!-- Header Section -->
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <div>
                                <h4 class="fw-bold text-uppercase mb-0">Manuscript / Datasheet</h4>
                                {{-- <small class="text-muted">Laboratory Information Management System</small> --}}
                            </div>
                            <div>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">
                                    <em class="icon ni ni-caret-left-fill"></em> Back
                                </a>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('create_test_result') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @if (isset($manuscripts) && $manuscripts->isNotEmpty())
                                <input type="hidden" name="registration_id"
                                    value="{{ $manuscripts->first()->registration->tr04_reference_id ?? '' }}">
                            @endif

                            <!-- Report Information -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-primary text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase"><em class="icon ni ni-clipboard"></em> Test Report
                                        Details</h6>
                                </div>
                                <div class="card-body px-4 py-3">
                                    <table class="table table-sm table-borderless align-middle mb-0 small w-100">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-muted w-25">Test Report No:</td>
                                                <td class="text-dark fw-semibold w-25">
                                                    {{ optional($manuscripts->first()?->registration)->tr04_reference_id ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted w-25">Date:</td>
                                                <td class="w-25">
                                                    <input type="date"
                                                        class="form-control form-control-sm bg-light border-0 shadow-none"
                                                        name="test_date"
                                                        value="{{ old('test_date', $testDate ?? date('Y-m-d')) }}" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">No. of Samples:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ optional($manuscripts->first()?->registration)->tr04_number_of_samples ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted">Sample Characteristics:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ optional($manuscripts->first()?->registration->labSample)->m14_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Date of Performance of Tests:</td>
                                                <td>
                                                    <input type="date"
                                                        class="form-control form-control-sm bg-light border-0 shadow-none"
                                                        name="performance_date"
                                                        value="{{ old('performance_date', $performanceDate ?? '') }}"
                                                        required>
                                                </td>
                                                <td class="fw-bold text-muted">Date of Allotment of Sample:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ $manuscripts->first()->tr05_alloted_at ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">QAO / JQAO / Analyst:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ optional($manuscripts->first()?->allotedTo)->m06_name ?? 'N/A' }}
                                                </td>
                                                <td class="fw-bold text-muted">Technical Manager:</td>
                                                <td class="text-dark fw-semibold">
                                                    {{ Session::get('role') === 'Manager' ? Session::get('name') : optional($manuscripts->first()?->allotedBy)->m06_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Test Results -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-primary text-white py-2 px-3 rounded-top">
                                    <h6 class="mb-0 text-uppercase"><em class="icon ni ni-layers"></em> Test Results</h6>
                                </div>
                                <div class="card-body px-4 py-3">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 10%">Sr. No.</th>
                                                <th>Test Name</th>
                                                <th style="width: 35%">Result / Entry</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($manuscripts as $key => $manuscript)
                                                @if ($manuscript->manuscript && $manuscript->manuscript->count() > 0)
                                                    <tr class="bg-light fw-bold">
                                                        <td>{{ $key + 1 }}</td>
                                                        <td colspan="2">{{ $manuscript->test->m12_name ?? 'N/A' }} - (
                                                            {{ $manuscript->standard->m15_method ?? 'N/A' }} )</td>
                                                    </tr>

                                                    @foreach ($manuscript->manuscript as $mKey => $manu)
                                                        @php
                                                            $existingResult = $existingResults
                                                                ->where('m12_test_number', $manuscript->m12_test_number)
                                                                ->where('m22_manuscript_id', $manu->m22_manuscript_id)
                                                                ->first();
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $key + 1 }}.{{ $mKey + 1 }}</td>
                                                            <td>{{ $manu->m22_name }}</td>
                                                            <td>
                                                                <input type="hidden"
                                                                    name="manuscript_data[{{ $key }}][{{ $mKey }}][test_id]"
                                                                    value="{{ $manuscript->m12_test_number }}">
                                                                <input type="hidden"
                                                                    name="manuscript_data[{{ $key }}][{{ $mKey }}][manuscript_id]"
                                                                    value="{{ $manu->m22_manuscript_id }}">
                                                                <input type="hidden"
                                                                    name="manuscript_data[{{ $key }}][{{ $mKey }}][result_id]"
                                                                    value="{{ $existingResult->tr07_test_result_id ?? '' }}">
                                                                <input type="text"
                                                                    class="form-control form-control-sm border-0 bg-light"
                                                                    name="manuscript_data[{{ $key }}][{{ $mKey }}][result]"
                                                                    value="{{ old('manuscript_data.' . $key . '.' . $mKey . '.result', $existingResult->tr07_result ?? '') }}"
                                                                    placeholder="Enter result value">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @php
                                                        $existingResult = $existingResults
                                                            ->where('m12_test_number', $manuscript->m12_test_number)
                                                            ->whereNull('m22_manuscript_id')
                                                            ->first();
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $manuscript->test->m12_name ?? 'N/A' }} - (
                                                            {{ $manuscript->standard->m15_method ?? 'N/A' }} )</td>
                                                        <td>
                                                            <input type="hidden"
                                                                name="test_data[{{ $key }}][test_id]"
                                                                value="{{ $manuscript->m12_test_number }}">
                                                            <input type="hidden"
                                                                name="test_data[{{ $key }}][result_id]"
                                                                value="{{ $existingResult->tr07_test_result_id ?? '' }}">
                                                            <input type="text"
                                                                class="form-control form-control-sm border-0 bg-light"
                                                                name="test_data[{{ $key }}][result]"
                                                                value="{{ old('test_data.' . $key . '.result', $existingResult->tr07_result ?? '') }}"
                                                                placeholder="Enter result value">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Signature Section -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body px-4 py-3">
                                    <div class="d-flex justify-content-between text-center flex-wrap">
                                        <div class="signature-box flex-fill me-3">
                                            <div class="signature-line mb-3"></div>
                                            <strong>Signature of QAO / JQAO</strong>
                                        </div>
                                        <div class="signature-box flex-fill ms-3">
                                            <div class="signature-line mb-3"></div>
                                            <strong>Signature of Technical Manager</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    @if (!isset($existingResults) || $existingResults->isEmpty())
                                        <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                                            <em class="icon ni ni-printer"></em> Print / Save as PDF
                                        </button>
                                    @endif
                                    <div class="btn-group">
                                        @if (optional($existingResults->first())->tr07_result_status != 'SUBMITTED')
                                            <button type="submit" name="action" value="DRAFT"
                                                class="btn btn-outline-primary">
                                                <em class="icon ni ni-file-text"></em> Save as Draft
                                            </button>
                                        @endif
                                        @if (Session::get('role') === 'DEO')
                                            <button type="submit" name="action" value="RESULTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Save & Complete
                                            </button>
                                        @else
                                            <button type="submit" name="action" value="SUBMITTED"
                                                class="btn btn-primary">
                                                <em class="icon ni ni-check-circle"></em> Save & Complete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            font-size: 0.9rem;
            color: #222;
        }

        .table td,
        .table th {
            padding: 0.55rem 0.75rem;
            vertical-align: middle;
        }

        .signature-line {
            height: 60px;
            border-bottom: 1px solid #444;
            margin: 0 auto 10px;
            width: 80%;
        }

        .card-header {
            border-bottom: 1px solid #ddd;
        }

        @media print {

            .btn,
            .nk-header,
            .nk-footer,
            .card:last-child {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .card {
                border: 1px solid #333 !important;
                box-shadow: none !important;
                page-break-inside: auto !important;
            }

            table {
                border-collapse: collapse !important;
                width: 100%;
                page-break-inside: auto !important;
                page-break-before: auto !important;
                page-break-after: auto !important;
            }

            thead {
                display: table-header-group !important;
            }

            tfoot {
                display: table-footer-group !important;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            input::placeholder,
            textarea::placeholder {
                color: transparent !important;
            }

            input,
            textarea {
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
