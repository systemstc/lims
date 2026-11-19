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
                                                        if (!empty($s->has_draft)) {
                                                            $statusLabel = 'Draft Saved — Incomplete (' . $s->completed_tests . '/' . $s->total_tests . ')';
                                                        } elseif ($s->actionable_tests_count > 0) {
                                                            $statusLabel = 'Analyst Completed (' . $s->completed_tests . '/' . $s->total_tests . ')';
                                                        } elseif ($s->completed_tests == 0) {
                                                            $statusLabel = 'Pending Analyst (0/' . $s->total_tests . ')';
                                                        } elseif ($s->completed_tests < $s->total_tests) {
                                                            $statusLabel = 'Partial Results in DB (' . ($s->total_tests - $s->completed_tests) . ' Pending Analyst)';
                                                        } else {
                                                            $statusLabel = 'Results Already Present in DB';
                                                        }
                                                        $msLabel = $s->manuscript ? 'Manuscript Uploaded' : 'No Manuscript';
                                                    @endphp
                                                    <option value="{{ $s->sample_id }}"
                                                        data-ref-id="{{ $s->reference_id }}"
                                                        data-manuscript="{{ $s->manuscript ? 1 : 0 }}"
                                                        data-actionable="{{ $s->actionable_tests_count }}"
                                                        data-completed="{{ $s->completed_tests }}"
                                                        data-total="{{ $s->total_tests }}"
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
                                                    @if (!empty($sample->has_draft))
                                                        <br><span class="badge bg-warning text-dark mt-1" title="Incomplete entry saved as draft">
                                                            <em class="icon ni ni-file-text me-1"></em> Draft Saved
                                                        </span>
                                                    @endif
                                                    @if (optional($sample->registration)->tr04_is_under_clarification)
                                                        <br><span class="badge bg-warning text-dark mt-1" title="{{ $sample->registration->tr04_latest_clarification_remark }}">
                                                            <em class="icon ni ni-undo me-1"></em> Clarification: {{ $sample->registration->tr04_sent_back_by_role }}
                                                        </span>
                                                    @endif
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
                                                                <em class="icon ni ni-edit-fill me-1"></em> {{ !empty($sample->has_draft) ? 'Resume Entry' : 'Result Entry' }}
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
                            <label class="form-label fw-bold">Select Manuscript File(s) (JPG, PNG, PDF)</label>
                            <input type="file" id="deoManuscriptInput" name="result_files[]" class="form-control form-control-lg" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" multiple required>
                            <div class="form-text text-muted small mt-1">
                                <em class="icon ni ni-spark me-1 text-primary"></em> High-resolution photos are automatically compressed in your browser before upload to keep file size under 2 MB while preserving laboratory sharpness.
                            </div>
                        </div>

                        <!-- Live File Info / Compression Status Box -->
                        <div id="deoFileStatusBox" class="border rounded p-2 bg-light d-none mb-3">
                            <div class="fw-bold text-dark fs-12px mb-1" id="deoFileCountBadge">Selected File(s):</div>
                            <ul class="list-unstyled mb-0 small text-muted" id="deoFileListNames" style="max-height: 140px; overflow-y: auto;"></ul>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="deoUploadSubmitBtn" class="btn btn-primary">
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
            const completed = parseInt($selectedOption.data('completed')) || 0;
            const total = parseInt($selectedOption.data('total')) || 0;

            selectedOptionData = {
                sampleId: val,
                refId: $selectedOption.data('ref-id'),
                hasManuscript: parseInt($selectedOption.data('manuscript')) === 1,
                actionable: actionable,
                completed: completed,
                resultUrl: $selectedOption.data('result-url')
            };

            if (actionable > 0) {
                $btn.removeClass('btn-secondary disabled').addClass('btn-primary').prop('disabled', false).html('<em class="icon ni ni-arrow-right me-1"></em> Open Result Entry');
            } else if (completed === 0) {
                $btn.removeClass('btn-primary').addClass('btn-secondary disabled').prop('disabled', true).html('<em class="icon ni ni-clock me-1"></em> Pending Analyst Completion');
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

        // In-browser image compressor: keeps images strictly under 2MB while preserving crisp laboratory text
        function compressImageForUpload(file, maxDimension = 2048, initialQuality = 0.82) {
            if (!file.type || !file.type.match(/^image\//i)) {
                return Promise.resolve(file); // PDFs and non-images bypass without alteration
            }

            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxDimension || height > maxDimension) {
                            if (width > height) {
                                height = Math.round((height * maxDimension) / width);
                                width = maxDimension;
                            } else {
                                width = Math.round((width * maxDimension) / height);
                                height = maxDimension;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        function exportBlob(quality) {
                            canvas.toBlob(function(blob) {
                                if (!blob) {
                                    return resolve(file);
                                }

                                // Target strictly under 2 MB (1.8 MB ceiling for safety)
                                if (blob.size > 1.8 * 1024 * 1024 && quality > 0.45) {
                                    exportBlob(quality - 0.15);
                                    return;
                                }

                                const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                                const compressedFile = new File([blob], cleanName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            }, 'image/jpeg', quality);
                        }

                        exportBlob(initialQuality);
                    };
                    img.onerror = function() { resolve(file); };
                    img.src = e.target.result;
                };
                reader.onerror = function() { resolve(file); };
                reader.readAsDataURL(file);
            });
        }

        // Auto-compress photos and show real-time feedback when files are chosen
        $('#deoManuscriptInput').on('change', async function() {
            const files = this.files;
            const $summaryBox = $('#deoFileStatusBox');
            const $listEl = $('#deoFileListNames');
            const $countBadge = $('#deoFileCountBadge');
            const $submitBtn = $('#deoUploadSubmitBtn');

            if (!files || files.length === 0) {
                $summaryBox.addClass('d-none');
                return;
            }

            $summaryBox.removeClass('d-none');
            $listEl.html('<li class="py-1 text-primary"><span class="spinner-border spinner-border-sm me-1"></span> Preparing & optimizing file(s)...</li>');
            $submitBtn.prop('disabled', true);

            $countBadge.text(`Selected ${files.length} File(s):`);

            let dt = null;
            if (window.DataTransfer) {
                try { dt = new DataTransfer(); } catch (err) { dt = null; }
            }

            let html = '';

            for (let i = 0; i < files.length; i++) {
                const origFile = files[i];
                const origSizeKb = (origFile.size / 1024).toFixed(1);
                const origSizeMb = (origFile.size / (1024 * 1024)).toFixed(2);

                if (origFile.type && origFile.type.match(/^image\//i)) {
                    const compressed = await compressImageForUpload(origFile);
                    if (dt) dt.items.add(compressed);

                    const compSizeKb = (compressed.size / 1024).toFixed(1);
                    const compSizeMb = (compressed.size / (1024 * 1024)).toFixed(2);
                    const sizeLabel = compressed.size >= 1024 * 1024 ? `${compSizeMb} MB` : `${compSizeKb} KB`;
                    const savedPct = Math.max(0, Math.round((1 - (compressed.size / origFile.size)) * 100));

                    html += `<li class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                        <span>
                            <em class="icon ni ni-img me-1 text-primary"></em>
                            <strong>Page ${i + 1}:</strong> ${origFile.name}
                            <small class="text-muted">(${origSizeMb >= 1 ? origSizeMb + ' MB' : origSizeKb + ' KB'})</small>
                        </span>
                        <span class="badge bg-success-dim text-success">
                            <em class="icon ni ni-check me-1"></em> Compressed: ${sizeLabel} (-${savedPct}%)
                        </span>
                    </li>`;
                } else {
                    if (dt) dt.items.add(origFile);

                    const isPdf = origFile.name.toLowerCase().endsWith('.pdf');
                    const icon = isPdf ? 'ni-file-pdf text-danger' : 'ni-file-text text-primary';
                    const sizeDisplay = origSizeMb >= 1 ? `${origSizeMb} MB` : `${origSizeKb} KB`;

                    html += `<li class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                        <span>
                            <em class="icon ni ${icon} me-1"></em>
                            <strong>${origFile.name}</strong>
                        </span>
                        <span class="badge bg-info-dim text-info">${sizeDisplay} (Ready)</span>
                    </li>`;
                }
            }

            $listEl.html(html);

            if (dt) {
                try {
                    this.files = dt.files;
                } catch (e) {
                    console.warn('DataTransfer files assignment skipped:', e);
                }
            }

            $submitBtn.prop('disabled', false);
        });

        // UI spinner on submit
        $('#deoUploadManuscriptForm').on('submit', function() {
            const $submitBtn = $('#deoUploadSubmitBtn');
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading Document...');
        });

        // Reset modal state on close
        $('#uploadManuscriptModal').on('hidden.bs.modal', function() {
            $('#deoManuscriptInput').val('');
            $('#deoFileStatusBox').addClass('d-none');
            $('#deoFileListNames').empty();
            $('#deoUploadSubmitBtn').prop('disabled', false).html('<em class="icon ni ni-upload me-1"></em> Upload & Proceed to Result Entry');
        });
    });
</script>
@endsection
