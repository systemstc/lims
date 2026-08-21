@extends('layouts.app_back')

@section('content')
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-xxl mx-auto">
                    <div class="nk-block nk-block-lg">
                        
                        <!-- Header Section -->
                        <div class="nk-block-head nk-block-head-sm">
                            <div class="nk-block-head-content d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="nk-block-title page-title">Samples for Result Entry (DEO)</h3>
                                    <div class="nk-block-des text-soft">
                                        <p>Select any sample from search dropdown or view completed samples below for result entry.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Dropdown Card (ALL SAMPLES appear here) -->
                        <div class="card card-bordered mb-4 shadow-sm border-primary" style="border-left-width: 4px !important;">
                            <div class="card-inner py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8 col-lg-9 mb-2 mb-md-0">
                                        <label class="form-label fw-bold text-primary mb-1">
                                            <em class="icon ni ni-search me-1"></em> Search & Select Any Sample Number
                                        </label>
                                        <div class="form-control-wrap">
                                            <select id="deoSampleSelect" class="form-select js-select2" data-search="on" data-placeholder="Type last digits or select any sample (e.g. REG-2026-00123)...">
                                                <option value=""></option>
                                                @foreach ($allSamples as $s)
                                                    @php
                                                        $isCompleted = $s->completed_tests > 0;
                                                        $hasActionable = $s->actionable_tests_count > 0;
                                                        $statusLabel = $hasActionable 
                                                            ? ($isCompleted ? 'Analyst Completed (' . $s->completed_tests . '/' . $s->total_tests . ')' : 'Pending Analyst (' . $s->completed_tests . '/' . $s->total_tests . ')') 
                                                            : 'Results Already Present in DB';
                                                        $msLabel = $s->manuscript ? 'Manuscript Uploaded' : 'No Manuscript';
                                                    @endphp
                                                    <option value="{{ $s->sample_id }}"
                                                        data-ref-id="{{ $s->reference_id }}"
                                                        data-manuscript="{{ $s->manuscript ? 1 : 0 }}"
                                                        data-actionable="{{ $s->actionable_tests_count }}"
                                                        data-result-url="{{ route('template_manuscript', $s->sample_id) }}">
                                                        {{ $s->reference_id }} — [ {{ $statusLabel }} | {{ $msLabel }} ]
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="text-muted mt-1 d-block">
                                            <em class="icon ni ni-info me-1"></em> All samples (completed or pending analyst) appear in this dropdown search.
                                        </small>
                                    </div>
                                    <div class="col-md-4 col-lg-3 text-md-end align-self-end">
                                        <button type="button" id="btnSelectSampleProceed" class="btn btn-primary w-100 py-2 disabled" disabled>
                                            <em class="icon ni ni-arrow-right me-1"></em> Open Result Entry
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Header -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="nk-block-title mb-0">Completed Samples Table <small class="text-muted fs-13px">(Shown by default)</small></h5>
                            <div class="form-group mb-0" style="max-width: 250px;">
                                <input type="text" id="tableFilterInput" class="form-control form-control-sm" placeholder="Filter table rows...">
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="card card-bordered card-preview shadow-sm">
                            <div class="card-inner">
                                <table class="datatable-init-export nowrap table table-hover align-middle mb-0" data-export-title="Completed Samples" id="deoSampleTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Sample Reference ID</th>
                                            <th>Tests Summary</th>
                                            <th>Priority</th>
                                            <th>Delay Time</th>
                                            <th>Created At</th>
                                            <th>Manuscript Photo</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($samples as $key => $sample)
                                            <tr class="sample-row" data-sample-id="{{ $sample->sample_id }}" data-search="{{ strtolower($sample->reference_id) }}">
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-14px">{{ $sample->reference_id }}</span>
                                                </td>
                                                <td>
                                                    <strong>Total:</strong> {{ $sample->total_tests }} <br>
                                                    <span class="text-success fw-bold">Completed:</span> {{ $sample->completed_tests }} <br>
                                                    <span class="text-warning">Pending:</span> {{ $sample->pending_tests }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $sample->priority == 'Tatkal' ? 'badge-dim bg-danger' : 'badge-dim bg-info' }}">
                                                        {{ $sample->priority }}
                                                    </span>
                                                </td>
                                                @php
                                                    $delay = $sample->delay_days ?? 0;
                                                    if ($delay > 3) {
                                                        $cappedDelay = min($delay, 30);
                                                        $red = 50 + intval(($cappedDelay / 30) * 205);
                                                        $color = "rgb($red, 0, 0)";
                                                    } else {
                                                        $color = 'rgb(255, 180, 0)';
                                                    }
                                                @endphp
                                                <td style="color: {{ $color }}; font-weight: bold;">
                                                    {{ $sample->delay_days }} days
                                                </td>
                                                <td>{{ $sample->created_at?->format('d-m-Y') }}</td>
                                                <td>
                                                    @if ($sample->manuscript)
                                                        <span class="badge badge-dim bg-success">
                                                            <em class="icon ni ni-check-circle me-1"></em> Uploaded
                                                        </span>
                                                    @else
                                                        <span class="badge badge-dim bg-warning text-dark">
                                                            <em class="icon ni ni-alert-circle me-1"></em> Pending Upload
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($sample->actionable_tests_count > 0)
                                                        @if ($sample->manuscript)
                                                            <!-- Manuscript is uploaded -> Go directly to Result Entry Form -->
                                                            <a href="{{ route('template_manuscript', $sample->sample_id) }}"
                                                                class="btn btn-primary btn-sm">
                                                                <em class="icon ni ni-edit-fill me-1"></em> Result Entry
                                                            </a>
                                                        @else
                                                            <!-- Manuscript not uploaded -> Prompt upload modal first -->
                                                            <button type="button" class="btn btn-warning btn-sm text-dark fw-bold open-upload-modal-btn"
                                                                data-ref-id="{{ $sample->reference_id }}"
                                                                data-sample-id="{{ $sample->sample_id }}">
                                                                <em class="icon ni ni-upload me-1"></em> Upload & Enter Result
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-dim bg-secondary p-1 px-2">
                                                            <em class="icon ni ni-check-circle me-1"></em> Results Present in DB
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <em class="icon ni ni-inbox display-4 mb-2 d-block text-soft"></em>
                                                    No completed samples pending result entry in default list. Use the search dropdown above to select any sample.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>

    <!-- Upload Manuscript Modal -->
    <div class="modal fade" id="uploadManuscriptModal" tabindex="-1" aria-labelledby="uploadManuscriptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deoUploadManuscriptForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="redirect_to_result" value="1">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white" id="uploadManuscriptModalLabel">
                            <em class="icon ni ni-upload me-1"></em> Upload Paper Manuscript
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <em class="icon ni ni-info me-1"></em> Sample Reference: <strong id="modalSampleRefText" class="text-dark"></strong>
                        </div>
                        <p class="text-muted small mb-3">
                            Please upload the photo or scanned PDF of the manuscript calculation paper completed by the analyst. Once uploaded, you will be taken directly to the result entry page.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Manuscript File (JPG, PNG, PDF)</label>
                            <input type="file" name="result_file" class="form-control form-control-lg" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <em class="icon ni ni-upload me-1"></em> Upload & Proceed to Result Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let selectedOptionData = null;

        // When a sample is selected from the Dropdown
        $('#deoSampleSelect').on('change', function() {
            const val = $(this).val();
            const $btn = $('#btnSelectSampleProceed');
            
            if (!val) {
                $btn.removeClass('btn-secondary').addClass('btn-primary disabled').prop('disabled', true).html('<em class="icon ni ni-arrow-right me-1"></em> Open Result Entry');
                selectedOptionData = null;
                return;
            }

            const $selectedOption = $(this).find('option:selected');
            const actionable = parseInt($selectedOption.data('actionable')) || 0;

            selectedOptionData = {
                sampleId: val,
                refId: $selectedOption.data('ref-id'),
                hasManuscript: parseInt($selectedOption.data('manuscript')) === 1,
                actionable: actionable,
                resultUrl: $selectedOption.data('result-url')
            };

            if (actionable > 0) {
                $btn.removeClass('btn-secondary disabled').addClass('btn-primary').prop('disabled', false).html('<em class="icon ni ni-arrow-right me-1"></em> Open Result Entry');
            } else {
                $btn.removeClass('btn-primary').addClass('btn-secondary disabled').prop('disabled', true).html('<em class="icon ni ni-check-circle me-1"></em> Results Already Present');
            }

            // Highlight/filter table row if it exists
            const searchRef = selectedOptionData.refId.toLowerCase();
            $('#deoSampleTable tbody tr.sample-row').each(function() {
                const searchData = $(this).attr('data-search') || '';
                if (searchData.includes(searchRef)) {
                    $(this).show().addClass('table-primary');
                } else {
                    $(this).removeClass('table-primary');
                }
            });
        });

        // Click Proceed button from dropdown selection
        $('#btnSelectSampleProceed').on('click', function() {
            if (!selectedOptionData || selectedOptionData.actionable <= 0) return;

            if (selectedOptionData.hasManuscript) {
                window.location.href = selectedOptionData.resultUrl;
            } else {
                // Open upload modal for this sample
                $('#modalSampleRefText').text(selectedOptionData.refId);
                const uploadUrl = "{{ route('testresult_upload', ':id') }}".replace(':id', selectedOptionData.refId);
                $('#deoUploadManuscriptForm').attr('action', uploadUrl);

                const modalElement = document.getElementById('uploadManuscriptModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }
        });

        // Filter table input
        $('#tableFilterInput').on('keyup input', function() {
            const query = $(this).val().toLowerCase().trim();
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#deoSampleTable')) {
                $('#deoSampleTable').DataTable().search(query).draw();
            } else {
                $('#deoSampleTable tbody tr.sample-row').each(function() {
                    const searchData = $(this).attr('data-search') || '';
                    if (searchData.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

        // Open Upload Manuscript Modal directly from table button
        $(document).on('click', '.open-upload-modal-btn', function() {
            const refId = $(this).data('ref-id');
            const sampleId = $(this).data('sample-id');

            $('#modalSampleRefText').text(refId);
            const uploadUrl = "{{ route('testresult_upload', ':id') }}".replace(':id', refId);
            $('#deoUploadManuscriptForm').attr('action', uploadUrl);

            const modalElement = document.getElementById('uploadManuscriptModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        });
    });
</script>
@endsection
