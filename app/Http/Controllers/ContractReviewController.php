<?php

namespace App\Http\Controllers;

use App\Models\Accreditation;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Group;
use App\Models\LabSample;
use App\Models\Package;
use App\Models\Ro;
use App\Models\SampleCustodyLog;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\Standard;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ContractReviewController extends Controller
{
    /**
     * Default ISO/IEC 17025 Clause 7.1 Feasibility Checklist items
     */
    public static function getDefaultChecklist(): array
    {
        return [
            'method_available' => [
                'title' => 'Test Methods Capability',
                'description' => 'Requested test standard methods (IS/ISO/ASTM/AATCC) are validated and within laboratory testing scope.',
                'status' => 'YES',
            ],
            'equipment_calibrated' => [
                'title' => 'Equipment & Calibration Status',
                'description' => 'Required testing equipment, instruments, and reference standards are calibrated with unbroken SI traceability.',
                'status' => 'YES',
            ],
            'environmental_control' => [
                'title' => 'Atmospheric Conditioning Facility',
                'description' => 'Standard atmosphere for conditioning and testing (20±2°C / 65±4% RH or 27±2°C / 65±2% RH) is available and controlled.',
                'status' => 'YES',
            ],
            'personnel_competency' => [
                'title' => 'Competent Technical Personnel',
                'description' => 'Qualified, trained and authorized analysts are available to execute testing within target completion timeline.',
                'status' => 'YES',
            ],
            'sample_adequacy' => [
                'title' => 'Sample Integrity & Quantity Suitability',
                'description' => 'Submitted sample quantity is sufficient for initial testing, potential duplicate re-testing, and mandatory lab retention.',
                'status' => 'YES',
            ],
            'decision_rule' => [
                'title' => 'Statement of Conformity / Decision Rule',
                'description' => 'Customer requirements, tolerance limits, and decision rule for conformity statements are documented and understood.',
                'status' => 'YES',
            ],
        ];
    }

    /**
     * Attach laboratory accreditation status to sample test items based on m21_accreditations for the particular lab.
     */
    public static function attachLabAccreditations($sample): void
    {
        if (!$sample || empty($sample->sampleTests)) {
            return;
        }

        $labRoId = $sample->m04_ro_id ?? Session::get('ro_id');
        if (!$labRoId) {
            return;
        }

        $labAccreditations = Accreditation::where('m04_ro_id', $labRoId)->get();

        foreach ($sample->sampleTests as $st) {
            $testId = $st->m12_test_id;
            $standardId = $st->m15_standard_id;

            // Search for matched record with priority:
            // 1. Exact match on both test and standard
            $matched = $labAccreditations->first(function ($acc) use ($testId, $standardId) {
                if (!empty($acc->m12_test_id) && !empty($acc->m15_standard_id)) {
                    return (int)$acc->m12_test_id === (int)$testId && (int)$acc->m15_standard_id === (int)$standardId;
                }
                return false;
            });

            // 2. Standard-level match for this lab (when m12_test_id is null/empty)
            if (!$matched && $standardId) {
                $matched = $labAccreditations->first(function ($acc) use ($standardId) {
                    return empty($acc->m12_test_id) && (int)$acc->m15_standard_id === (int)$standardId;
                });
            }

            // 3. Test-level match for this lab (when m15_standard_id is null/empty)
            if (!$matched && $testId) {
                $matched = $labAccreditations->first(function ($acc) use ($testId) {
                    return (int)$acc->m12_test_id === (int)$testId && empty($acc->m15_standard_id);
                });
            }

            $st->setAttribute('lab_accreditation_record', $matched);
            $isYes = $matched && strtoupper($matched->m21_is_accredited ?? '') === 'YES';
            $isExpired = $isYes && !empty($matched->m21_valid_till) && Carbon::parse($matched->m21_valid_till)->endOfDay()->isPast();

            $st->setAttribute('is_lab_accredited', $isYes && !$isExpired);
            $st->setAttribute('is_accreditation_expired', $isExpired);
            $st->setAttribute('accreditation_valid_till', $matched?->m21_valid_till);
        }
    }

    /**
     * Contract Reviews Dashboard & Listing
     */
    public function index(Request $request)
    {
        $roId = Session::get('ro_id');
        $isAdmin = Session::get('role') === 'ADMIN' || Session::get('role_id') == -1 || Session::has('admin_id');

        if ($request->ajax()) {
            $query = SampleRegistration::with([
                'customer',
                'customerLocation.district',
                'labSample',
                'department',
                'sampleTests.test',
                'sampleTests.standard',
                'ro'
            ])->select('tr04_sample_registrations.*');

            // Regional office scope: filter by RO only if specific user is locked to an RO and is not admin
            if (!$isAdmin && $roId && !in_array(strtoupper(Session::get('role', '')), ['MANAGER', 'LAB_INCHARGE'])) {
                $query->where('m04_ro_id', $roId);
            } elseif ($request->filled('ro_id')) {
                $query->where('m04_ro_id', $request->ro_id);
            }

            // Status filter
            if ($request->filled('review_status') && $request->review_status !== 'all') {
                if ($request->review_status === 'PENDING') {
                    $query->where(function ($q) {
                        $q->whereNull('tr04_contract_review_status')
                          ->orWhere('tr04_contract_review_status', 'PENDING')
                          ->orWhere('tr04_contract_review_status', '');
                    });
                } elseif ($request->review_status === 'CLARIFICATION_REQUIRED') {
                    $query->where(function ($q) {
                        $q->where('tr04_contract_review_status', 'CLARIFICATION_REQUIRED')
                          ->orWhere('tr04_is_under_clarification', 1);
                    });
                } else {
                    $query->where('tr04_contract_review_status', $request->review_status);
                }
            }

            // Sample service type filter (Normal / Tatkal)
            if ($request->filled('sample_type') && $request->sample_type !== 'all') {
                $query->where('tr04_sample_type', $request->sample_type);
            }

            // Date Range Filter
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->filterColumn('reference_info', function ($q, $keyword) {
                    $q->where(function ($sub) use ($keyword) {
                        $sub->where('tr04_reference_id', 'like', "%{$keyword}%")
                            ->orWhere('tr04_tracker_id', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('customer_info', function ($q, $keyword) {
                    $q->whereHas('customer', function ($sub) use ($keyword) {
                        $sub->where('m07_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('sample_info', function ($q, $keyword) {
                    $q->where(function ($sub) use ($keyword) {
                        $sub->where('tr04_sample_description', 'like', "%{$keyword}%")
                            ->orWhere('tr04_sample_mark', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('reference_info', function ($row) {
                    $ref = '<div class="user-info">';
                    $ref .= '<span class="tb-lead fw-bold text-primary fs-14px">#' . e($row->tr04_reference_id ?? 'N/A') . '</span>';
                    if (!empty($row->tr04_tracker_id)) {
                        $ref .= '<span class="sub-text text-muted fs-11px"><em class="icon ni ni-ticket me-1"></em>Tracker: ' . e($row->tr04_tracker_id) . '</span>';
                    }
                    if ($row->tr04_is_under_clarification) {
                        $ref .= '<span class="badge bg-warning-dim text-warning fw-bold fs-11px mt-1"><em class="icon ni ni-alert-circle me-1"></em>Under Clarification</span>';
                    }
                    $ref .= '</div>';
                    return $ref;
                })
                ->addColumn('customer_info', function ($row) {
                    $name = $row->customer->m07_name ?? 'N/A';
                    $loc = $row->customerLocation->m08_address ?? '';
                    $district = $row->customerLocation->district->m02_name ?? '';
                    $html = '<div class="user-card"><div class="user-info">';
                    $html .= '<span class="tb-lead fw-semibold text-dark fs-13px">' . e($name) . '</span>';
                    if ($loc || $district) {
                        $html .= '<span class="sub-text text-soft fs-11px"><em class="icon ni ni-map-pin me-1"></em>' . e($district ?: $loc) . '</span>';
                    }
                    $html .= '</div></div>';
                    return $html;
                })
                ->addColumn('sample_info', function ($row) {
                    $desc = $row->tr04_sample_description ?? 'Standard Sample';
                    $mark = $row->tr04_sample_mark ? 'Mark: ' . $row->tr04_sample_mark : '';
                    $category = $row->labSample->m14_name ?? '';
                    $qty = $row->tr04_number_of_samples ? $row->tr04_number_of_samples . ' Qty' : '';
                    
                    $html = '<div><span class="tb-lead fw-semibold text-truncate d-block" style="max-width: 200px;" title="' . e($desc) . '">' . e($desc) . '</span>';
                    $meta = array_filter([$category, $mark, $qty]);
                    if ($meta) {
                        $html .= '<span class="sub-text text-muted fs-11px">' . e(implode(' • ', $meta)) . '</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('tests_requested', function ($row) {
                    $count = $row->sampleTests->count();
                    $testNames = $row->sampleTests->take(2)->map(function ($st) {
                        return $st->test->m12_name ?? 'Test';
                    })->implode(', ');
                    
                    $html = '<span class="badge bg-light text-dark border fw-bold">' . $count . ' Test' . ($count > 1 ? 's' : '') . '</span>';
                    if ($testNames) {
                        $html .= '<span class="sub-text text-truncate d-block fs-11px mt-1" style="max-width: 170px;" title="' . e($testNames) . '">' . e($testNames) . '</span>';
                    }
                    return $html;
                })
                ->addColumn('service_tat', function ($row) {
                    $isTatkal = strtolower($row->tr04_sample_type ?? '') === 'tatkal';
                    $typeBadge = $isTatkal 
                        ? '<span class="badge bg-danger text-white"><em class="icon ni ni-zap me-1"></em>TATKAL</span>'
                        : '<span class="badge bg-info-dim text-info">REGULAR</span>';
                    
                    $expected = $row->tr04_expected_date 
                        ? Carbon::parse($row->tr04_expected_date)->format('d M, Y')
                        : 'Not Set';
                        
                    return '<div class="d-flex flex-column">' . $typeBadge . '<span class="sub-text fs-11px mt-1"><em class="icon ni ni-calendar me-1"></em>Target: ' . $expected . '</span></div>';
                })
                ->addColumn('review_status', function ($row) {
                    $status = strtoupper($row->tr04_contract_review_status ?? 'PENDING');
                    return match ($status) {
                        'ACCEPTED' => '<span class="badge bg-success-dim text-success fw-bold py-1 px-2"><em class="icon ni ni-check-circle me-1"></em>Accepted</span>',
                        'ACCEPTED_WITH_DEVIATION' => '<span class="badge bg-info-dim text-info fw-bold py-1 px-2"><em class="icon ni ni-alert-circle me-1"></em>With Deviation</span>',
                        'CLARIFICATION_REQUIRED' => '<span class="badge bg-warning-dim text-warning fw-bold py-1 px-2"><em class="icon ni ni-help-circle me-1"></em>Clarification Req.</span>',
                        'REJECTED' => '<span class="badge bg-danger-dim text-danger fw-bold py-1 px-2"><em class="icon ni ni-cross-circle me-1"></em>Rejected</span>',
                        default => '<span class="badge bg-light text-secondary border fw-bold py-1 px-2"><em class="icon ni ni-clock me-1"></em>Pending Review</span>',
                    };
                })
                ->addColumn('charges', function ($row) {
                    $amount = $row->tr04_total_charges ?? 0;
                    $payStatus = strtoupper($row->tr04_payment_status ?? 'PENDING');
                    $payBadge = match ($payStatus) {
                        'PAID' => 'text-success',
                        'HOLD' => 'text-warning',
                        'PENDING' => 'text-danger',
                        default => 'text-muted'
                    };
                    return '<div><span class="tb-lead fw-bold fs-13px">₹' . number_format($amount, 2) . '</span><span class="sub-text fw-semibold fs-11px ' . $payBadge . '">' . $payStatus . '</span></div>';
                })
                ->addColumn('action', function ($row) {
                    $id = $row->tr04_sample_registration_id;
                    $ref = e($row->tr04_reference_id);
                    $status = strtoupper($row->tr04_contract_review_status ?? 'PENDING');

                    $html = '<div class="dropdown">
                        <button class="btn btn-trigger btn-icon dropdown-toggle" data-bs-toggle="dropdown">
                            <em class="icon ni ni-more-h"></em>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px; z-index: 1050;">
                            <ul class="link-list-opt no-bdr">
                                <li>
                                    <a href="' . route('show_contract', $id) . '" class="text-primary fw-semibold">
                                        <em class="icon ni ni-file-docs"></em><span>Review Contract</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('edit_contract_details', $id) . '" class="text-dark">
                                        <em class="icon ni ni-edit-alt"></em><span>Edit Sample & Tests</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="' . route('print_contract_review', $id) . '" target="_blank">
                                        <em class="icon ni ni-printer"></em><span>Print Review Sheet</span>
                                    </a>
                                </li>
                                <li class="divider"></li>';

                    if ($status !== 'ACCEPTED') {
                        $html .= '<li>
                            <a href="javascript:void(0)" class="text-success" onclick="quickAcceptContract(' . $id . ', \'' . $ref . '\')">
                                <em class="icon ni ni-check-circle"></em><span>Quick Accept</span>
                            </a>
                        </li>';
                    }

                    $html .= '<li>
                        <a href="javascript:void(0)" class="text-warning" onclick="quickClarificationModal(' . $id . ', \'' . $ref . '\')">
                            <em class="icon ni ni-undo"></em><span>Request Clarification</span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="javascript:void(0)" class="text-danger" onclick="deleteContractRegistration(' . $id . ', \'' . $ref . '\')">
                            <em class="icon ni ni-trash"></em><span>Cancel / Delete</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>';

                    return $html;
                })
                ->rawColumns(['reference_info', 'customer_info', 'sample_info', 'tests_requested', 'service_tat', 'review_status', 'charges', 'action'])
                ->make(true);
        }

        // Dashboard Metrics Calculation
        $baseQuery = SampleRegistration::query();
        if (!$isAdmin && $roId) {
            $baseQuery->where('m04_ro_id', $roId);
        }

        $metrics = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where(function ($q) {
                $q->whereNull('tr04_contract_review_status')->orWhere('tr04_contract_review_status', 'PENDING');
            })->count(),
            'accepted' => (clone $baseQuery)->where('tr04_contract_review_status', 'ACCEPTED')->count(),
            'deviation' => (clone $baseQuery)->where('tr04_contract_review_status', 'ACCEPTED_WITH_DEVIATION')->count(),
            'clarification' => (clone $baseQuery)->where(function ($q) {
                $q->where('tr04_contract_review_status', 'CLARIFICATION_REQUIRED')->orWhere('tr04_is_under_clarification', 1);
            })->count(),
            'rejected' => (clone $baseQuery)->where('tr04_contract_review_status', 'REJECTED')->count(),
        ];

        $ros = $isAdmin ? Ro::where('m04_status', 'ACTIVE')->get() : collect();

        return view('contract_review.index', compact('metrics', 'ros', 'isAdmin'));
    }

    /**
     * Inspect & Review Full Contract Review Details (ISO/IEC 17025 Sheet)
     */
    public function show($id)
    {
        $roId = Session::get('ro_id');
        $isAdmin = Session::get('role') === 'ADMIN' || Session::get('role_id') == -1;

        $sample = SampleRegistration::with([
            'customer',
            'customerLocation.district.state',
            'buyer',
            'thirdParty',
            'cha',
            'department',
            'labSample',
            'sampleTests.test',
            'sampleTests.standard',
            'package',
            'ro',
            'contractReviewer',
            'manuscripts'
        ])->findOrFail($id);

        if (!$isAdmin && $roId && $sample->m04_ro_id != $roId) {
            abort(403, 'Unauthorized access to sample from another regional office.');
        }

        // Attach laboratory accreditation status for this specific lab
        self::attachLabAccreditations($sample);

        // Feasibility Checklist
        $savedChecklist = $sample->tr04_feasibility_checklist;
        $defaultChecklist = self::getDefaultChecklist();
        $checklist = !empty($savedChecklist) && is_array($savedChecklist) ? $savedChecklist : $defaultChecklist;

        // Ensure all default keys exist
        foreach ($defaultChecklist as $key => $val) {
            if (!isset($checklist[$key])) {
                $checklist[$key] = $val;
            }
        }

        return view('contract_review.show', compact('sample', 'checklist'));
    }

    /**
     * Edit Sample & Contract Review Details Form (GET & POST)
     */
    public function edit(Request $request, $id)
    {
        $roId = Session::get('ro_id');
        $isAdmin = Session::get('role') === 'ADMIN' || Session::get('role_id') == -1;

        $sample = SampleRegistration::with([
            'customer',
            'customerLocation',
            'department',
            'labSample',
            'sampleTests.test',
            'sampleTests.standard',
            'package',
            'ro'
        ])->findOrFail($id);

        if (!$isAdmin && $roId && $sample->m04_ro_id != $roId) {
            abort(403, 'Unauthorized access to sample from another regional office.');
        }

        // Handle POST update request
        if ($request->isMethod('post')) {
            return $this->update($request, $id);
        }

        // GET: Prepare modification view
        $savedChecklist = $sample->tr04_feasibility_checklist;
        $defaultChecklist = self::getDefaultChecklist();
        $checklist = !empty($savedChecklist) && is_array($savedChecklist) ? $savedChecklist : $defaultChecklist;
        foreach ($defaultChecklist as $key => $val) {
            if (!isset($checklist[$key])) {
                $checklist[$key] = $val;
            }
        }

        $departments = Department::where('m13_status', 'ACTIVE')->get();
        $labSamples = LabSample::where('m14_status', 'ACTIVE')->get();
        $groups = Group::where('m11_status', 'ACTIVE')->get();
        $tests = Test::where('m12_status', 'ACTIVE')->get();
        $standards = Standard::where('m15_status', 'ACTIVE')->get();
        $ro = Ro::find($sample->m04_ro_id) ?? Ro::find($roId);

        // Attach laboratory accreditation status for this specific lab
        self::attachLabAccreditations($sample);

        // Include any standards referenced in existing sample tests
        $sampleStdIds = $sample->sampleTests->pluck('m15_standard_id')->filter()->unique();
        if ($sampleStdIds->isNotEmpty()) {
            $extraStandards = Standard::whereIn('m15_standard_id', $sampleStdIds)->get();
            $standards = $standards->merge($extraStandards)->unique('m15_standard_id');
        }

        $labRoId = $sample->m04_ro_id ?? $roId;
        $labAccreditations = $labRoId ? Accreditation::where('m04_ro_id', $labRoId)->get() : collect();

        // Build test-to-standards map with lab-specific accreditation info
        $testStandardsMap = [];
        foreach ($tests as $t) {
            $stdIds = !empty($t->m15_standard_id)
                ? array_filter(array_map('trim', explode(',', $t->m15_standard_id)))
                : [];
            $matched = $standards->whereIn('m15_standard_id', $stdIds)->values()->map(function ($s) use ($t, $labAccreditations) {
                $testId = $t->m12_test_id;
                $stdId = $s->m15_standard_id;

                $acc = $labAccreditations->first(function ($a) use ($testId, $stdId) {
                    if (!empty($a->m12_test_id) && !empty($a->m15_standard_id)) {
                        return (int)$a->m12_test_id === (int)$testId && (int)$a->m15_standard_id === (int)$stdId;
                    }
                    return false;
                });
                if (!$acc) {
                    $acc = $labAccreditations->first(function ($a) use ($stdId) {
                        return empty($a->m12_test_id) && (int)$a->m15_standard_id === (int)$stdId;
                    });
                }
                if (!$acc) {
                    $acc = $labAccreditations->first(function ($a) use ($testId) {
                        return (int)$a->m12_test_id === (int)$testId && empty($a->m15_standard_id);
                    });
                }

                $isAccredited = false;
                $isExpired = false;
                if ($acc && strtoupper($acc->m21_is_accredited ?? '') === 'YES') {
                    $validTill = $acc->m21_valid_till;
                    $isExpired = !empty($validTill) && Carbon::parse($validTill)->endOfDay()->isPast();
                    $isAccredited = !$isExpired;
                }

                return [
                    'm15_standard_id' => $s->m15_standard_id,
                    'm15_method' => $s->m15_method,
                    'is_accredited' => $isAccredited,
                    'is_expired' => $isExpired,
                ];
            });
            $testStandardsMap[$t->m12_test_id] = $matched;
        }

        return view('contract_review.edit', compact(
            'sample',
            'checklist',
            'departments',
            'labSamples',
            'groups',
            'tests',
            'standards',
            'testStandardsMap',
            'ro'
        ));
    }

    /**
     * Update Contract Review Details, Sample Metadata & Test Parameters
     */
    public function update(Request $request, $id)
    {
        $sample = SampleRegistration::with('sampleTests')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tr04_sample_description' => 'required|string|max:1000',
            'tr04_sample_mark' => 'nullable|string|max:255',
            'tr04_number_of_samples' => 'nullable|numeric|min:1',
            'tr04_sample_type' => 'required|in:Normal,Tatkal',
            'tr04_commercial_type' => 'nullable|string|max:100',
            'tr04_expected_date' => 'nullable|date',
            'tr04_sample_condition' => 'required|string|max:50',
            'tr04_storage_condition' => 'required|string|max:50',
            'tr04_decision_rule' => 'required|string|max:100',
            'tr04_contract_review_status' => 'required|in:PENDING,ACCEPTED,ACCEPTED_WITH_DEVIATION,CLARIFICATION_REQUIRED,REJECTED',
            'tr04_contract_review_remarks' => 'nullable|string',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'required|exists:m12_tests,m12_test_id',
            'standard_ids' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $reviewerId = Session::get('employee_id') ?? Auth::id();
            $reviewerName = Session::get('employee_name') ?? Auth::user()->name ?? 'Laboratory In-Charge';
            $reviewerRole = Session::get('role') ?? 'Manager';

            // 1. Process Feasibility Checklist
            $checklist = self::getDefaultChecklist();
            if ($request->has('checklist') && is_array($request->checklist)) {
                foreach ($request->checklist as $k => $v) {
                    if (isset($checklist[$k])) {
                        $checklist[$k]['status'] = $v['status'] ?? 'YES';
                        if (isset($v['remark'])) {
                            $checklist[$k]['remark'] = $v['remark'];
                        }
                    }
                }
            }

            // 2. Recalculate Testing Charges from selected tests
            $testSubtotal = 0;
            $selectedTests = Test::whereIn('m12_test_id', $request->test_ids)->get()->keyBy('m12_test_id');

            // Synchronize SampleTest records
            $existingTests = SampleTest::where('tr04_sample_registration_id', $id)->get()->keyBy('m12_test_id');
            $submittedTestIds = array_unique($request->test_ids);

            // Remove unselected tests
            foreach ($existingTests as $eTestId => $existingRecord) {
                if (!in_array($eTestId, $submittedTestIds)) {
                    $existingRecord->delete();
                }
            }

            // Add or update tests
            foreach ($request->test_ids as $idx => $tId) {
                $stdId = $request->standard_ids[$idx] ?? null;
                $testObj = $selectedTests->get($tId);
                if (!$testObj) continue;

                $testCharge = (float)($testObj->m12_charge ?? $testObj->m12_charges ?? 0);
                $testSubtotal += $testCharge;

                SampleTest::updateOrCreate(
                    [
                        'tr04_sample_registration_id' => $id,
                        'm12_test_id' => $tId,
                    ],
                    [
                        'm12_test_number' => $testObj->m12_test_number ?? $tId,
                        'm15_standard_id' => $stdId,
                        'm04_ro_id' => $sample->m04_ro_id ?? Session::get('ro_id') ?? -1,
                        'm16_primary_test_id' => $testObj->m16_primary_test_id ?? null,
                        'm17_secondary_test_id' => $testObj->m17_secondary_test_id ?? null,
                        'tr05_remark' => $request->test_remarks[$idx] ?? null,
                        'tr05_priority' => $request->tr04_sample_type === 'Tatkal' ? 'TATKAL' : 'NORMAL',
                        'tr05_status' => $existingTests->get($tId)?->tr05_status ?? 'PENDING',
                    ]
                );
            }

            // Also maintain m12_test_ids JSON on sample registration for legacy compatibility
            $jsonTests = [];
            foreach ($request->test_ids as $idx => $tId) {
                $stdId = $request->standard_ids[$idx] ?? null;
                $jsonTests[$tId] = [
                    'test_id' => $tId,
                    'standard_id' => $stdId,
                    'remark' => $request->test_remarks[$idx] ?? null,
                ];
            }

            // Additional charges
            $additionalCharges = (float)($sample->tr04_additional_charges ?? 0);
            $testingCharges = $testSubtotal;

            // Tatkal surcharge: if Tatkal, double or apply rate
            $isTatkal = $request->tr04_sample_type === 'Tatkal';
            $baseCharges = $isTatkal ? ($testingCharges * 1.5) : $testingCharges;
            $subtotalForTax = $baseCharges + $additionalCharges;

            // GST Calculation based on RO and Customer Location
            $ro = Ro::find($sample->m04_ro_id);
            $customerGst = $sample->customerLocation->m08_gst ?? $sample->customerLocation->m08_gst_no ?? $sample->customer->m07_gst ?? '';
            $roGst = $ro->m04_gst ?? $ro->gst_no ?? '';

            $roStateCode = substr($roGst, 0, 2);
            $custStateCode = substr($customerGst, 0, 2);

            $cgst = 0; $sgst = 0; $igst = 0;
            if ($roStateCode && $custStateCode && $roStateCode === $custStateCode) {
                // Intra-state (CGST 9% + SGST 9%)
                $cgstRate = $ro->cgst ?? 9;
                $sgstRate = $ro->sgst ?? 9;
                $cgst = ($subtotalForTax * $cgstRate) / 100;
                $sgst = ($subtotalForTax * $sgstRate) / 100;
            } else {
                // Inter-state (IGST 18%)
                $igstRate = $ro->igst ?? 18;
                if ($igstRate == 0 && (($ro->cgst ?? 0) > 0 || ($ro->sgst ?? 0) > 0)) {
                    $igstRate = (($ro->cgst ?? 9) + ($ro->sgst ?? 9));
                }
                $igst = ($subtotalForTax * $igstRate) / 100;
            }
            $taxAmount = $cgst + $sgst + $igst;
            $totalCharges = round($subtotalForTax + $taxAmount, 2);

            // 3. Update Sample Registration Record
            $reviewStatus = $request->tr04_contract_review_status;
            $isClarification = $reviewStatus === 'CLARIFICATION_REQUIRED';

            $sample->update([
                'tr04_sample_description' => $request->tr04_sample_description,
                'tr04_sample_mark' => $request->tr04_sample_mark,
                'tr04_number_of_samples' => $request->tr04_number_of_samples ?? 1,
                'tr04_sample_type' => $request->tr04_sample_type,
                'tr04_commercial_type' => $request->tr04_commercial_type,
                'tr04_expected_date' => $request->tr04_expected_date,
                'm12_test_ids' => json_encode($jsonTests),
                'tr04_sample_condition' => $request->tr04_sample_condition,
                'tr04_storage_condition' => $request->tr04_storage_condition,
                'tr04_decision_rule' => $request->tr04_decision_rule,
                'tr04_customer_agreed_deviation' => $request->has('tr04_customer_agreed_deviation') ? 1 : 0,
                'tr04_feasibility_checklist' => $checklist,
                'tr04_contract_review_status' => $reviewStatus,
                'tr04_contract_review_remarks' => $request->tr04_contract_review_remarks,
                'tr04_contract_reviewed_by' => $reviewerId,
                'tr04_contract_reviewed_by_name' => $reviewerName,
                'tr04_contract_reviewed_by_role' => $reviewerRole,
                'tr04_contract_reviewed_at' => now(),
                'tr04_testing_charges' => $testingCharges,
                'tr04_cgst' => $cgst,
                'tr04_sgst' => $sgst,
                'tr04_igst' => $igst,
                'tr04_total_charges' => $totalCharges,
                'tr04_is_under_clarification' => $isClarification ? 1 : 0,
                'tr04_latest_clarification_remark' => $isClarification ? $request->tr04_contract_review_remarks : null,
                'tr04_sent_back_by_name' => $isClarification ? $reviewerName : null,
                'tr04_sent_back_by_role' => $isClarification ? $reviewerRole : null,
                'tr04_sent_back_at' => $isClarification ? now() : null,
            ]);

            // 4. Log to Custody Log if clarification or review changed
            if ($isClarification) {
                SampleCustodyLog::create([
                    'tr04_sample_registration_id' => $id,
                    'tr04_reference_id' => $sample->tr04_reference_id,
                    'm04_ro_id' => $sample->m04_ro_id,
                    'from_role' => $reviewerRole,
                    'from_user_id' => $reviewerId,
                    'from_name' => $reviewerName,
                    'to_role' => 'RC',
                    'action_type' => 'SEND_BACK',
                    'clarification_remark' => 'Contract Review Clarification: ' . $request->tr04_contract_review_remarks,
                ]);
            }

            DB::commit();

            return redirect()->route('show_contract', $id)
                ->with('success', 'Contract review and sample details updated successfully under ISO/IEC 17025 standard.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contract Review Update Error: ' . $e->getMessage(), [
                'sample_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Error updating contract review: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Quick Decision Action (AJAX) - Accept, Accept with Deviation, Clarification, Reject
     */
    public function updateDecision(Request $request, $id)
    {
        $sample = SampleRegistration::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:ACCEPTED,ACCEPTED_WITH_DEVIATION,CLARIFICATION_REQUIRED,REJECTED',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $reviewerId = Session::get('employee_id') ?? Auth::id();
            $reviewerName = Session::get('employee_name') ?? Auth::user()->name ?? 'Laboratory In-Charge';
            $reviewerRole = Session::get('role') ?? 'Manager';
            $decision = $request->decision;
            $isClarification = $decision === 'CLARIFICATION_REQUIRED';

            $sample->update([
                'tr04_contract_review_status' => $decision,
                'tr04_contract_review_remarks' => $request->remarks,
                'tr04_contract_reviewed_by' => $reviewerId,
                'tr04_contract_reviewed_by_name' => $reviewerName,
                'tr04_contract_reviewed_by_role' => $reviewerRole,
                'tr04_contract_reviewed_at' => now(),
                'tr04_is_under_clarification' => $isClarification ? 1 : 0,
                'tr04_latest_clarification_remark' => $isClarification ? $request->remarks : null,
                'tr04_sent_back_by_name' => $isClarification ? $reviewerName : null,
                'tr04_sent_back_by_role' => $isClarification ? $reviewerRole : null,
                'tr04_sent_back_at' => $isClarification ? now() : null,
            ]);

            if ($isClarification) {
                SampleCustodyLog::create([
                    'tr04_sample_registration_id' => $id,
                    'tr04_reference_id' => $sample->tr04_reference_id,
                    'm04_ro_id' => $sample->m04_ro_id,
                    'from_role' => $reviewerRole,
                    'from_user_id' => $reviewerId,
                    'from_name' => $reviewerName,
                    'to_role' => 'RC',
                    'action_type' => 'SEND_BACK',
                    'clarification_remark' => 'Contract Review Clarification: ' . $request->remarks,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contract review decision recorded as ' . str_replace('_', ' ', $decision) . '.',
                'status' => $decision,
                'badge' => $sample->contract_review_badge,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quick Decision Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to record decision: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete or Cancel Contract Registration
     */
    public function destroy(Request $request, $id)
    {
        try {
            $sample = SampleRegistration::findOrFail($id);

            // Lab In-Charge can cancel registration if not yet analysed
            if (in_array(strtoupper($sample->tr04_progress ?? ''), ['ANALYSED', 'REPORTED', 'DISPATCHED'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel or delete sample that has already been tested or reported.',
                ], 400);
            }

            $sample->update([
                'tr04_status' => 'CANCELLED',
                'tr04_contract_review_status' => 'REJECTED',
                'tr04_contract_review_remarks' => $request->input('reason', 'Cancelled during Contract Review by Laboratory In-Charge.'),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sample registration # ' . $sample->tr04_reference_id . ' has been cancelled.',
                ]);
            }

            return redirect()->route('view_contract_reviews')
                ->with('success', 'Sample registration has been cancelled successfully.');
        } catch (\Exception $e) {
            Log::error('Delete Contract Review Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel registration: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to cancel registration: ' . $e->getMessage());
        }
    }

    /**
     * ISO/IEC 17025 Printable Contract Review Sheet (Format No: TC/LAB/CR/01)
     */
    public function printReview($id)
    {
        $sample = SampleRegistration::with([
            'customer',
            'customerLocation.district.state',
            'buyer',
            'department',
            'labSample',
            'sampleTests.test',
            'sampleTests.standard',
            'package',
            'ro',
            'contractReviewer'
        ])->findOrFail($id);

        // Attach laboratory accreditation status for this specific lab
        self::attachLabAccreditations($sample);

        $savedChecklist = $sample->tr04_feasibility_checklist;
        $defaultChecklist = self::getDefaultChecklist();
        $checklist = !empty($savedChecklist) && is_array($savedChecklist) ? $savedChecklist : $defaultChecklist;
        foreach ($defaultChecklist as $key => $val) {
            if (!isset($checklist[$key])) {
                $checklist[$key] = $val;
            }
        }

        return view('contract_review.print', compact('sample', 'checklist'));
    }

    /**
     * Helper AJAX: Get Standards by Test ID
     */
    public function getStandardsByTest(Request $request)
    {
        $testId = $request->test_id;
        if (!$testId) {
            return response()->json(['standards' => []]);
        }

        $test = Test::find($testId);
        if (!$test || empty($test->m15_standard_id)) {
            return response()->json(['standards' => []]);
        }

        $standardIds = array_filter(array_map('trim', explode(',', $test->m15_standard_id)));
        $standards = Standard::whereIn('m15_standard_id', $standardIds)
            ->where('m15_status', 'ACTIVE')
            ->get(['m15_standard_id', 'm15_method']);

        $roId = $request->ro_id ?? Session::get('ro_id');
        $labAccreditations = $roId ? Accreditation::where('m04_ro_id', $roId)->get() : collect();

        $mapped = $standards->map(function ($s) use ($testId, $labAccreditations) {
            $stdId = $s->m15_standard_id;
            $acc = $labAccreditations->first(function ($a) use ($testId, $stdId) {
                if (!empty($a->m12_test_id) && !empty($a->m15_standard_id)) {
                    return (int)$a->m12_test_id === (int)$testId && (int)$a->m15_standard_id === (int)$stdId;
                }
                return false;
            });
            if (!$acc) {
                $acc = $labAccreditations->first(function ($a) use ($stdId) {
                    return empty($a->m12_test_id) && (int)$a->m15_standard_id === (int)$stdId;
                });
            }
            if (!$acc) {
                $acc = $labAccreditations->first(function ($a) use ($testId) {
                    return (int)$a->m12_test_id === (int)$testId && empty($a->m15_standard_id);
                });
            }

            $isAccredited = false;
            $isExpired = false;
            if ($acc && strtoupper($acc->m21_is_accredited ?? '') === 'YES') {
                $validTill = $acc->m21_valid_till;
                $isExpired = !empty($validTill) && Carbon::parse($validTill)->endOfDay()->isPast();
                $isAccredited = !$isExpired;
            }

            return [
                'm15_standard_id' => $s->m15_standard_id,
                'm15_method' => $s->m15_method,
                'is_accredited' => $isAccredited,
                'is_expired' => $isExpired,
            ];
        });

        return response()->json(['standards' => $mapped]);
    }

    /**
     * Helper AJAX: Get Tests by Department / Group
     */
    public function getTestsByGroup(Request $request)
    {
        $groupId = $request->group_id;
        $deptId = $request->department_id;

        $query = Test::where('m12_status', 'ACTIVE');
        if ($groupId) {
            $query->where('m11_group_id', $groupId);
        }
        if ($deptId) {
            $query->where('m13_department_id', $deptId);
        }

        $tests = $query->orderBy('m12_name')->get(['m12_test_id', 'm12_name', 'm12_charges']);
        return response()->json(['tests' => $tests]);
    }
}
