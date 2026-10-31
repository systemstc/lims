@extends('layouts.app_back')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                {{-- Page Header --}}
                <div class="nk-block-head nk-block-head-sm mb-3">
                    <div class="nk-block-between align-items-center">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title d-flex align-items-center">
                                <em class="icon ni ni-route text-primary me-2" style="font-size: 1.8rem;"></em>
                                <span>Sample Journey &amp; History Track</span>
                            </h3>
                            <div class="nk-block-des text-soft">
                                <p>End-to-end chronological lifecycle tracking from sample inward to final reporting and dispatch.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-light bg-white text-dark btn-sm" onclick="window.print()">
                                    <em class="icon ni ni-printer me-1"></em><span>Print Track</span>
                                </button>
                                @if ($sample && $sample->reports->isNotEmpty())
                                    @php
                                        $latestRep = $sample->reports->first();
                                        $repUrl = $latestRep->tr09_report_file_path ? asset('storage/' . $latestRep->tr09_report_file_path) : '#';
                                    @endphp
                                    <a href="{{ $repUrl }}" target="_blank" class="btn btn-primary btn-sm">
                                        <em class="icon ni ni-file-pdf me-1"></em><span>Download Report</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search Bar Card --}}
                <div class="card card-bordered mb-4 shadow-sm" style="background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%); border-top: 3px solid #6576ff;">
                    <div class="card-inner py-3">
                        <form action="{{ route('sample_history') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-md-9 col-sm-8">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left" style="top: 50%; transform: translateY(-50%);">
                                        <em class="icon ni ni-search text-primary" style="font-size: 1.2rem;"></em>
                                    </div>
                                    <input type="text"
                                           name="query"
                                           class="form-control form-control-lg ps-5"
                                           placeholder="Enter Sample Reference Number (e.g. 021120226270042) or Tracker ID (e.g. 959B5A)..."
                                           value="{{ $searchedQuery ?? '' }}"
                                           required
                                           autofocus
                                           style="border-radius: 8px; font-weight: 500;">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100 d-flex justify-content-center align-items-center" style="border-radius: 8px;">
                                    <em class="icon ni ni-radar me-1"></em>
                                    <span>Track Sample</span>
                                </button>
                            </div>
                        </form>

                        {{-- Quick sample suggestion pills --}}
                        @if ($recentSamples->isNotEmpty())
                            <div class="d-flex align-items-center flex-wrap gap-1 mt-2 pt-2 border-top">
                                <span class="fs-12px text-muted me-1 fw-bold"><em class="icon ni ni-spark text-warning me-1"></em>Quick Track:</span>
                                @foreach ($recentSamples as $rs)
                                    <a href="{{ route('sample_history', ['identifier' => $rs->tr04_reference_id]) }}"
                                       class="badge badge-dim {{ ($searchedQuery == $rs->tr04_reference_id || $searchedQuery == $rs->tr04_tracker_id) ? 'bg-primary text-white' : 'bg-outline-light text-dark' }} px-2 py-1 fs-11px text-decoration-none">
                                        <span class="fw-bold">{{ $rs->tr04_reference_id }}</span>
                                        @if($rs->tr04_tracker_id)
                                            <span class="text-muted ms-1">({{ $rs->tr04_tracker_id }})</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Not Found Alert --}}
                @if ($notFound)
                    <div class="alert alert-warning alert-icon border-warning shadow-sm my-4">
                        <em class="icon ni ni-alert-circle"></em>
                        <strong>Sample Not Found:</strong> No sample could be found matching <code>{{ $searchedQuery }}</code>.
                        Please verify the Reference ID or Tracker ID.
                    </div>
                @endif

                {{-- If Sample is Loaded --}}
                @if ($sample)
                    {{-- Hero Summary Card --}}
                    <div class="card card-bordered mb-4 shadow-sm">
                        <div class="card-inner">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-8 col-md-12">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <span class="fs-13px text-uppercase text-muted fw-bold">Sample Reference:</span>
                                        <span class="fs-18px fw-bold text-dark font-monospace">{{ $sample->tr04_reference_id }}</span>
                                        <button class="btn btn-xs btn-outline-light px-2" onclick="navigator.clipboard.writeText('{{ $sample->tr04_reference_id }}'); $(this).text('Copied!'); setTimeout(() => $(this).text('Copy'), 1500);" title="Copy Reference ID">Copy</button>

                                        @if ($sample->tr04_tracker_id)
                                            <span class="badge bg-outline-info ms-2 px-2 py-1">
                                                <em class="icon ni ni-qr me-1"></em>Tracker ID: <strong class="ms-1">{{ $sample->tr04_tracker_id }}</strong>
                                            </span>
                                        @endif

                                        @if ($sample->tr04_ulr_no)
                                            <span class="badge bg-outline-success ms-1 px-2 py-1">
                                                <em class="icon ni ni-award me-1"></em>ULR: <strong class="ms-1">{{ $sample->tr04_ulr_no }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="row g-2 fs-13px text-dark">
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block fs-11px text-uppercase">Customer / Client:</span>
                                            <strong class="text-primary">{{ $sample->customer->m07_name ?? 'N/A' }}</strong>
                                            @if($sample->buyer)
                                                <span class="text-muted d-block fs-11px mt-1">Buyer: {{ $sample->buyer->m07_name }}</span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block fs-11px text-uppercase">Sample Type &amp; Lab:</span>
                                            <strong>{{ $sample->labSample->m14_name ?? 'Textile Sample' }}</strong>
                                            <span class="text-muted d-block fs-11px mt-1">Department: {{ $sample->department->m13_name ?? 'Testing' }} | RO: {{ $sample->ro->m04_name ?? 'Branch' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Live Custody & Status Box --}}
                                <div class="col-lg-4 col-md-12 border-start-lg">
                                    <div class="bg-light p-3 rounded border">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fs-11px text-uppercase fw-bold text-muted">Current Custody:</span>
                                            @if ($stats['is_under_clar'])
                                                <span class="badge bg-danger text-white"><em class="icon ni ni-alert-fill me-1"></em>Under Clarification</span>
                                            @else
                                                <span class="badge bg-success text-white"><em class="icon ni ni-check-circle me-1"></em>Active Flow</span>
                                            @endif
                                        </div>
                                        <div class="fs-15px fw-bold text-dark d-flex align-items-center">
                                            <em class="icon ni ni-user-check text-primary me-2" style="font-size: 1.3rem;"></em>
                                            <span>{{ $stats['current_custody'] }}</span>
                                        </div>
                                        @if ($stats['is_under_clar'] && $sample->tr04_latest_clarification_remark)
                                            <div class="text-danger fs-11px mt-1 bg-white p-1 rounded border border-danger">
                                                <strong>Clarification:</strong> {{ Str::limit($sample->tr04_latest_clarification_remark, 80) }}
                                            </div>
                                        @endif
                                        <div class="mt-2 pt-2 border-top">
                                            <div class="d-flex justify-content-between fs-11px text-muted mb-1">
                                                <span>Lifecycle Progress:</span>
                                                <strong class="text-dark">{{ $stats['progress_percent'] }}%</strong>
                                            </div>
                                            <div class="progress progress-sm" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $stats['progress_percent'] }}%;" aria-valuenow="{{ $stats['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================================================= --}}
                    {{-- ZIG-ZAG TRACK CONTAINER --}}
                    {{-- ========================================================================= --}}
                    <div class="zigzag-wrapper mb-5">
                        <div class="zigzag-track">

                            @foreach ($stages as $index => $stage)
                                @php
                                    $isLeft = ($index % 2 === 0);
                                    $statusClass = match($stage['status']) {
                                        'COMPLETED'   => 'stage-completed',
                                        'IN_PROGRESS' => 'stage-active',
                                        'HOLD'        => 'stage-hold',
                                        'ALERT'       => 'stage-alert',
                                        default       => 'stage-pending'
                                    };
                                    $badgeColor = match($stage['status']) {
                                        'COMPLETED'   => 'bg-success',
                                        'IN_PROGRESS' => 'bg-info',
                                        'HOLD'        => 'bg-warning',
                                        'ALERT'       => 'bg-danger',
                                        default       => 'bg-light text-muted'
                                    };
                                @endphp

                                <div class="zigzag-row {{ $isLeft ? 'row-left' : 'row-right' }} {{ $statusClass }}">
                                    {{-- Central Milestone Node Connector --}}
                                    <div class="zigzag-node">
                                        <div class="zigzag-node-circle {{ $badgeColor }}">
                                            <span class="node-num">{{ $stage['id'] }}</span>
                                            <em class="icon ni {{ $stage['icon'] }} node-icon"></em>
                                        </div>
                                        <div class="zigzag-connector-line"></div>
                                    </div>

                                    {{-- Milestone Detail Card --}}
                                    <div class="zigzag-content-card">
                                        <div class="card card-bordered shadow-sm">
                                            {{-- Card Header --}}
                                            <div class="card-inner py-2 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background-color: #fafbfc;">
                                                <div class="d-flex align-items-center">
                                                    <span class="stage-tag me-2 badge {{ $badgeColor }} text-white px-2 py-1 fs-11px">
                                                        Step {{ $stage['id'] }}
                                                    </span>
                                                    <h6 class="card-title mb-0 fs-14px fw-bold text-dark">
                                                        {{ $stage['title'] }}
                                                    </h6>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge {{ $badgeColor }} text-uppercase fs-10px">
                                                        {{ $stage['status'] }}
                                                    </span>
                                                    @if ($stage['timestamp'])
                                                        <span class="fs-11px text-muted d-flex align-items-center">
                                                            <em class="icon ni ni-clock me-1"></em>
                                                            {{ \Carbon\Carbon::parse($stage['timestamp'])->format('d M Y, h:i A') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Meta Actor & Location Bar --}}
                                            <div class="card-inner py-1 px-3 bg-light border-bottom d-flex justify-content-between align-items-center fs-11px text-muted flex-wrap">
                                                <div>
                                                    <span class="fw-bold text-dark">Handled By:</span>
                                                    <span class="text-primary">{{ $stage['actor_name'] }}</span>
                                                    <span class="badge bg-dim-secondary ms-1">{{ $stage['actor_role'] }}</span>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark">Location:</span>
                                                    <span>{{ $stage['location'] }}</span>
                                                </div>
                                            </div>

                                            {{-- Card Body Summary & Data --}}
                                            <div class="card-inner py-3 px-3">
                                                <p class="text-soft fs-12px mb-3">{{ $stage['summary'] }}</p>

                                                {{-- STAGE 1: Registration Specific Details --}}
                                                @if ($stage['slug'] === 'registration')
                                                    <div class="row g-2 fs-12px">
                                                        <div class="col-sm-6">
                                                            <span class="text-muted">Test Memo / Forwarding Letter:</span>
                                                            <strong class="d-block">{{ $stage['data']['reference_no'] }} (Dated: {{ $stage['data']['reference_date'] }})</strong>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <span class="text-muted">Sample Description:</span>
                                                            <strong class="d-block">{{ $stage['data']['description'] }}</strong>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <span class="text-muted">Sample Condition / Storage:</span>
                                                            <span class="d-block">{{ $stage['data']['condition'] }} | {{ $stage['data']['storage_condition'] }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <span class="text-muted">Received Via:</span>
                                                            <span class="d-block">{{ $stage['data']['received_via'] }} (Samples: {{ $stage['data']['number_of_samples'] }})</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- STAGE 2: Billing & Commercial Details --}}
                                                @if ($stage['slug'] === 'billing')
                                                    <div class="row g-2 fs-12px mb-2">
                                                        <div class="col-sm-4">
                                                            <span class="text-muted">Testing Charges:</span>
                                                            <strong class="d-block text-dark">₹{{ $stage['data']['testing_charges'] }}</strong>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <span class="text-muted">Tax (GST):</span>
                                                            <strong class="d-block text-dark">₹{{ $stage['data']['tax_amount'] }}</strong>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <span class="text-muted">Total Charges:</span>
                                                            <strong class="d-block text-success fs-13px">₹{{ $stage['data']['total_charges'] }}</strong>
                                                        </div>
                                                    </div>
                                                    @if (!empty($stage['data']['transactions']))
                                                        <div class="border rounded p-2 bg-light mt-2 fs-11px">
                                                            <span class="fw-bold d-block mb-1 text-muted">Wallet Transactions:</span>
                                                            @foreach ($stage['data']['transactions'] as $tx)
                                                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                                                    <span>#{{ $tx['tx_id'] }} - {{ $tx['description'] }} ({{ $tx['method'] }})</span>
                                                                    <strong class="text-dark">₹{{ $tx['amount'] }} [{{ $tx['status'] }}] - {{ $tx['date'] }}</strong>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif

                                                {{-- STAGE 3: Contract Review Details --}}
                                                @if ($stage['slug'] === 'contract_review')
                                                    <div class="fs-12px">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Review Decision:</span>
                                                            <strong class="badge bg-outline-success">{{ $stage['data']['review_status'] }}</strong>
                                                        </div>
                                                        <div class="mb-2">
                                                            <span class="text-muted">Remarks:</span>
                                                            <span class="d-block fst-italic">{{ $stage['data']['review_remarks'] }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between fs-11px text-muted border-top pt-1">
                                                            <span>Decision Rule: <strong>{{ $stage['data']['decision_rule'] }}</strong></span>
                                                            <span>Agreed Deviation: <strong>{{ $stage['data']['customer_deviation'] }}</strong></span>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- STAGE 4: Allotment Details --}}
                                                @if ($stage['slug'] === 'allotment')
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm fs-11px mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Test Parameter</th>
                                                                    <th>Method / Standard</th>
                                                                    <th>Assigned Analyst</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($stage['data']['test_list'] as $t)
                                                                    <tr>
                                                                        <td>{{ $t['test_number'] }}</td>
                                                                        <td class="fw-bold">{{ $t['name'] }}</td>
                                                                        <td>{{ $t['standard'] }}</td>
                                                                        <td>{{ $t['analyst_name'] }}</td>
                                                                        <td>
                                                                            <span class="badge badge-dot {{ $t['status'] === 'COMPLETED' ? 'bg-success' : 'bg-warning' }}">{{ $t['status'] }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                {{-- STAGE 5: Custody Details --}}
                                                @if ($stage['slug'] === 'custody')
                                                    @if (!empty($stage['data']['logs']))
                                                        <div class="timeline-custody fs-11px">
                                                            @foreach ($stage['data']['logs'] as $log)
                                                                <div class="d-flex align-items-start border-bottom pb-2 mb-2">
                                                                    <div class="me-2">
                                                                        <em class="icon ni {{ $log['action'] === 'SEND_BACK' ? 'ni-arrow-left text-danger' : 'ni-arrow-right text-success' }} fs-14px"></em>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div>
                                                                            <strong>{{ $log['from_role'] }} ({{ $log['from_name'] }})</strong>
                                                                            <span class="text-muted">➔</span>
                                                                            <strong>{{ $log['to_role'] }} ({{ $log['to_name'] }})</strong>
                                                                            <span class="badge {{ $log['action'] === 'SEND_BACK' ? 'bg-danger' : 'bg-primary' }} fs-9px ms-1">{{ $log['action'] }}</span>
                                                                        </div>
                                                                        @if ($log['remark'])
                                                                            <div class="text-danger mt-1 bg-white p-1 rounded border">
                                                                                <em>"{{ $log['remark'] }}"</em>
                                                                            </div>
                                                                        @endif
                                                                        <div class="text-muted fs-10px mt-1">{{ $log['timestamp'] }}</div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="fs-12px text-muted">Currently residing with: <strong class="text-dark">{{ $stage['data']['current_custody'] }}</strong></div>
                                                    @endif
                                                @endif

                                                {{-- STAGE 6: Manuscripts Details --}}
                                                @if ($stage['slug'] === 'manuscripts')
                                                    <div class="fs-12px">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Archived Paper Worksheets:</span>
                                                            <strong class="badge bg-info">{{ $stage['data']['total_manuscripts'] }} Sheet(s)</strong>
                                                        </div>
                                                        @if (!empty($stage['data']['manuscript_files']))
                                                            <ul class="list-group list-group-flush fs-11px">
                                                                @foreach ($stage['data']['manuscript_files'] as $mf)
                                                                    <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                                        <span><em class="icon ni ni-file-text me-1 text-primary"></em>{{ $mf['analyst'] }} - {{ $mf['page'] }}</span>
                                                                        <span class="text-muted">{{ $mf['created_at'] }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- STAGE 7: Results & Audits Details --}}
                                                @if ($stage['slug'] === 'results_audit')
                                                    {{-- Results list accordion --}}
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="fw-bold fs-12px text-dark">Analytical Observations ({{ $stage['data']['results_count'] }} Parameters):</span>
                                                            <button class="btn btn-xs btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#resultsCollapse{{ $stage['id'] }}">
                                                                <span>View Parameters</span>
                                                            </button>
                                                        </div>
                                                        <div class="collapse show" id="resultsCollapse{{ $stage['id'] }}">
                                                            <div class="table-responsive border rounded mt-1" style="max-height: 220px; overflow-y: auto;">
                                                                <table class="table table-bordered table-sm fs-11px mb-0">
                                                                    <thead class="table-light sticky-top">
                                                                        <tr>
                                                                            <th>Test / Parameter</th>
                                                                            <th>Observed Result</th>
                                                                            <th>Unit</th>
                                                                            <th>Analyst Remarks / Footnotes</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($stage['data']['results_list'] as $res)
                                                                            <tr>
                                                                                <td>
                                                                                    <strong>{{ $res['test_name'] }}</strong>
                                                                                    @if($res['primary_param'])
                                                                                        <span class="d-block text-muted">↳ {{ $res['primary_param'] }}</span>
                                                                                    @endif
                                                                                    @if($res['secondary_param'])
                                                                                        <span class="d-block text-muted ms-2">↳ {{ $res['secondary_param'] }}</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="fw-bold text-primary font-monospace">{!! $res['result'] !!}</td>
                                                                                <td>{{ $res['unit'] }}</td>
                                                                                <td class="fs-10px text-muted">{{ $res['remarks'] ?: '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- AUDIT TRAIL: Who edited what, when, old value vs new value --}}
                                                    <div class="audit-trail-box border rounded p-2 bg-light">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="fw-bold fs-12px text-danger d-flex align-items-center">
                                                                <em class="icon ni ni-histroy me-1"></em>
                                                                Result Modification &amp; Edit Audit Trail
                                                            </span>
                                                            <span class="badge bg-outline-danger">{{ count($stage['data']['audits']) }} Edit Record(s)</span>
                                                        </div>

                                                        @if (!empty($stage['data']['audits']))
                                                            <div class="table-responsive mt-1">
                                                                <table class="table table-bordered table-sm fs-11px bg-white mb-0">
                                                                    <thead class="table-danger text-dark">
                                                                        <tr>
                                                                            <th>Date &amp; Time</th>
                                                                            <th>Parameter</th>
                                                                            <th>Field Changed</th>
                                                                            <th>Previous Value</th>
                                                                            <th>New Value</th>
                                                                            <th>Edited By</th>
                                                                            <th>Reason</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($stage['data']['audits'] as $aud)
                                                                            <tr>
                                                                                <td>{{ $aud['timestamp'] }}</td>
                                                                                <td><strong>{{ $aud['test_name'] }}</strong></td>
                                                                                <td><span class="badge bg-light text-dark">{{ $aud['field_changed'] }}</span></td>
                                                                                <td><del class="text-danger font-monospace">{{ $aud['old_value'] }}</del></td>
                                                                                <td><strong class="text-success font-monospace">{{ $aud['new_value'] }}</strong></td>
                                                                                <td>
                                                                                    <strong>{{ $aud['user_name'] }}</strong>
                                                                                    @if($aud['ip_address'])
                                                                                        <span class="d-block text-muted fs-9px">IP: {{ $aud['ip_address'] }}</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $aud['change_reason'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="fs-11px text-muted p-2 bg-white rounded border">
                                                                <em class="icon ni ni-shield-check text-success me-1"></em>
                                                                <strong>Zero post-entry edits:</strong> Original test results intact as submitted with full cryptographic and analytical integrity.
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- STAGE 8: Verification Details --}}
                                                @if ($stage['slug'] === 'verification')
                                                    <div class="fs-12px">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Technical Review Status:</span>
                                                            <strong class="badge bg-success">{{ $stage['data']['verification_status'] }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Verified Parameters:</span>
                                                            <span>{{ $stage['data']['verified_count'] }} of {{ $stage['data']['total_count'] }} Completed</span>
                                                        </div>
                                                        @if (!empty($stage['data']['clarifications']))
                                                            <div class="border rounded p-2 bg-warning bg-opacity-10 mt-2">
                                                                <span class="fw-bold text-warning d-block mb-1">Clarifications Raised:</span>
                                                                @foreach ($stage['data']['clarifications'] as $c)
                                                                    <div class="fs-11px text-dark border-bottom pb-1 mb-1">
                                                                        <span><strong>{{ $c['by'] }}</strong> to <strong>{{ $c['to'] }}</strong>: "{{ $c['remark'] }}"</span>
                                                                        <span class="d-block text-muted fs-10px">{{ $c['date'] }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- STAGE 9: Report & ULR Details --}}
                                                @if ($stage['slug'] === 'reporting')
                                                    <div class="fs-12px">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">NABL ULR Number:</span>
                                                            <strong class="text-success font-monospace">{{ $stage['data']['ulr_no'] }}</strong>
                                                        </div>
                                                        @if (!empty($stage['data']['report_versions']))
                                                            <div class="mt-2">
                                                                <span class="fw-bold d-block mb-1 fs-11px text-muted">Report History:</span>
                                                                @foreach ($stage['data']['report_versions'] as $rep)
                                                                    <div class="d-flex justify-content-between align-items-center border rounded p-2 bg-light mb-1">
                                                                        <div>
                                                                            <strong class="text-dark">Version {{ $rep['version'] }} ({{ $rep['status'] }})</strong>
                                                                            <span class="text-muted d-block fs-11px">Generated: {{ $rep['generated_at'] }} by {{ $rep['generated_by'] }}</span>
                                                                        </div>
                                                                        @if ($rep['file_path'])
                                                                            <a href="{{ asset('storage/' . $rep['file_path']) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                                                <em class="icon ni ni-file-pdf me-1"></em><span>View PDF</span>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted fs-11px">Report finalization will generate official PDF with QR code verification and digital signatures.</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- STAGE 10: Dispatch Details --}}
                                                @if ($stage['slug'] === 'dispatch')
                                                    <div class="fs-12px">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Delivery Mode:</span>
                                                            <strong>{{ $stage['data']['dispatch_mode'] }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Article / Consignment No:</span>
                                                            <strong class="font-monospace">{{ $stage['data']['article_no'] }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted">Report Recipient:</span>
                                                            <span>{{ $stage['data']['report_to'] }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- ZIG-ZAG TRACK STYLES --}}
<style>
/* Zig-Zag Central Container */
.zigzag-wrapper {
    position: relative;
    max-width: 1080px;
    margin: 0 auto;
    padding: 20px 0;
}

.zigzag-track {
    position: relative;
}

/* Central glowing track rail line */
.zigzag-track::before {
    content: '';
    position: absolute;
    top: 30px;
    bottom: 30px;
    left: 50%;
    width: 6px;
    background: linear-gradient(180deg, #6576ff 0%, #1ee0ac 50%, #364a63 100%);
    transform: translateX(-50%);
    border-radius: 6px;
    box-shadow: 0 0 12px rgba(101, 118, 255, 0.35);
    z-index: 1;
}

/* Individual Row */
.zigzag-row {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 40px;
    width: 100%;
}

/* Alternating Left / Right Rows */
.row-left {
    flex-direction: row-reverse;
}

.row-right {
    flex-direction: row;
}

/* Content Card Width */
.zigzag-content-card {
    width: 45%;
    z-index: 3;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.zigzag-content-card:hover {
    transform: translateY(-3px);
}

.zigzag-content-card .card {
    border-radius: 10px;
    overflow: hidden;
}

/* Central Milestone Node */
.zigzag-node {
    width: 10%;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 4;
}

.zigzag-node-circle {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
    border: 3px solid #ffffff;
    color: #ffffff;
    transition: transform 0.2s ease;
}

.zigzag-row:hover .zigzag-node-circle {
    transform: scale(1.12);
}

.node-num {
    font-size: 11px;
    font-weight: 800;
    line-height: 1;
}

.node-icon {
    font-size: 16px;
    line-height: 1;
}

/* Status specific glowing animations */
.stage-active .zigzag-node-circle {
    box-shadow: 0 0 0 6px rgba(101, 118, 255, 0.25), 0 4px 15px rgba(101, 118, 255, 0.5);
    animation: pulseNode 2s infinite;
}

.stage-alert .zigzag-node-circle {
    box-shadow: 0 0 0 6px rgba(232, 90, 90, 0.25), 0 4px 15px rgba(232, 90, 90, 0.5);
}

@keyframes pulseNode {
    0% {
        box-shadow: 0 0 0 0 rgba(101, 118, 255, 0.5);
    }
    70% {
        box-shadow: 0 0 0 12px rgba(101, 118, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(101, 118, 255, 0);
    }
}

/* Empty half spacer to keep alternating left/right layout */
.zigzag-row::before {
    content: '';
    width: 45%;
    display: block;
}

/* Responsive adjustment for tablets & mobile */
@media (max-width: 991.98px) {
    .zigzag-track::before {
        left: 30px;
    }
    .zigzag-row {
        flex-direction: row !important;
        padding-left: 70px;
    }
    .zigzag-row::before {
        display: none;
    }
    .zigzag-node {
        position: absolute;
        left: 0;
        width: 60px;
    }
    .zigzag-content-card {
        width: 100%;
    }
}

/* Print Styles */
@media print {
    body {
        background: #fff !important;
    }
    .nk-sidebar, .nk-header, .form-control-wrap, .btn, form {
        display: none !important;
    }
    .zigzag-wrapper {
        max-width: 100% !important;
    }
    .zigzag-track::before {
        left: 20px !important;
    }
    .zigzag-row {
        flex-direction: row !important;
        padding-left: 50px !important;
        page-break-inside: avoid;
    }
    .zigzag-row::before {
        display: none !important;
    }
    .zigzag-node {
        position: absolute !important;
        left: 0 !important;
    }
    .zigzag-content-card {
        width: 100% !important;
    }
}
</style>
@endsection

