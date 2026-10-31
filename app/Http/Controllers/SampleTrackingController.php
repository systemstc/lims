<?php

namespace App\Http\Controllers;

use App\Models\SampleRegistration;
use App\Models\TestResultAudit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SampleTrackingController extends Controller
{
    /**
     * Show the End-to-End Sample History Journey Track.
     * Accessible with sample reference ID or tracker ID.
     */
    public function showTrack(Request $request, $identifier = null)
    {
        // 1. Resolve identifier from URL path or query params
        $searchQuery = trim($identifier ?? $request->input('query') ?? $request->input('tracker_id') ?? $request->input('reference_id') ?? '');

        // Recent samples list for quick suggestions/landing view
        $recentSamples = SampleRegistration::with(['customer', 'labSample'])
            ->orderByDesc('tr04_sample_registration_id')
            ->take(6)
            ->get();

        if (empty($searchQuery)) {
            return view('samples.tracking_track', [
                'sample'           => null,
                'searchedQuery'    => '',
                'notFound'         => false,
                'recentSamples'    => $recentSamples,
                'stages'           => [],
                'stats'            => [],
            ]);
        }

        // 2. Fetch sample with full relational graph
        $sample = SampleRegistration::with([
            'ro',
            'labSample',
            'department',
            'customer',
            'customerLocation',
            'buyer',
            'buyerLocation',
            'thirdParty',
            'cha',
            'creatorEmployee',
            'contractReviewer',
            'holdTransaction',
            'walletTransactions' => fn($q) => $q->orderBy('created_at', 'asc'),
            'sampleTests'        => function ($q) {
                $q->with(['test', 'standard', 'allotedTo', 'allotedBy', 'transferredBy'])->orderBy('m12_test_number');
            },
            'custodyLogs'        => fn($q) => $q->with(['fromUser', 'toUser'])->orderBy('created_at', 'asc'),
            'manuscripts'        => fn($q) => $q->orderBy('created_at', 'asc'),
            'testResult'         => function ($q) {
                $q->with([
                    'test',
                    'primaryTest',
                    'secondaryTest',
                    'creator',
                    'updater',
                    'remarkRecord',
                    'audits.user',
                ])->orderBy('m12_test_number');
            },
            'customFields',
            'reports'            => fn($q) => $q->with('generator')->orderBy('tr09_version_number', 'asc'),
        ])
        ->where(function ($q) use ($searchQuery) {
            $q->where('tr04_tracker_id', $searchQuery)
              ->orWhere('tr04_reference_id', $searchQuery)
              ->orWhere('tr04_reference_no', $searchQuery);
            if (is_numeric($searchQuery)) {
                $q->orWhere('tr04_sample_registration_id', (int)$searchQuery);
            }
        })
        ->first();

        if (!$sample) {
            return view('samples.tracking_track', [
                'sample'           => null,
                'searchedQuery'    => $searchQuery,
                'notFound'         => true,
                'recentSamples'    => $recentSamples,
                'stages'           => [],
                'stats'            => [],
            ]);
        }

        // 3. Compile End-to-End Milestones for the Zig-Zag Track
        $stages = $this->buildMilestoneStages($sample);
        $stats  = $this->calculateJourneyStats($sample, $stages);

        return view('samples.tracking_track', [
            'sample'           => $sample,
            'searchedQuery'    => $searchQuery,
            'notFound'         => false,
            'recentSamples'    => $recentSamples,
            'stages'           => $stages,
            'stats'            => $stats,
        ]);
    }

    /**
     * Build the structured 10 milestones for the zig-zag track.
     */
    private function buildMilestoneStages(SampleRegistration $sample): array
    {
        $stages = [];

        // -----------------------------------------------------------------
        // STAGE 1: Sample Inward & Registration
        // -----------------------------------------------------------------
        $stages[] = [
            'id'          => 1,
            'slug'        => 'registration',
            'title'       => 'Sample Inward & Registration',
            'short_title' => 'Inward & Registration',
            'icon'        => 'ni-file-text',
            'theme'       => 'primary',
            'status'      => 'COMPLETED',
            'timestamp'   => $sample->created_at,
            'actor_name'  => $sample->creatorEmployee?->m06_name ?? ('User #' . $sample->tr04_created_by),
            'actor_role'  => 'Registration Officer / Front Office',
            'location'    => $sample->ro->m04_name ?? 'Branch RO',
            'summary'     => 'Sample physically received, verified, and entered into LIMS with Reference ID & Tracker barcode.',
            'data'        => [
                'reference_id'       => $sample->tr04_reference_id,
                'tracker_id'         => $sample->tr04_tracker_id,
                'customer_name'      => $sample->customer->m07_name ?? 'N/A',
                'buyer_name'         => $sample->buyer->m07_name ?? 'N/A',
                'lab_sample'         => $sample->labSample->m14_name ?? 'N/A',
                'department'         => $sample->department->m13_name ?? 'N/A',
                'description'        => $sample->tr04_sample_description ?? 'N/A',
                'sample_mark'        => $sample->tr04_sample_mark ?? 'N/A',
                'received_via'       => $sample->tr04_received_via ?? 'Hand Delivery',
                'reference_no'       => $sample->tr04_reference_no ?? 'N/A',
                'reference_date'     => $sample->tr04_reference_date ? Carbon::parse($sample->tr04_reference_date)->format('d M Y') : 'N/A',
                'condition'          => $sample->tr04_sample_condition ?? 'Normal / Acceptable',
                'storage_condition'  => $sample->tr04_storage_condition ?? 'Ambient Room Temperature',
                'number_of_samples'  => $sample->tr04_number_of_samples ?? 1,
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 2: Billing & Wallet Hold / Payment
        // -----------------------------------------------------------------
        $paymentStatus = strtoupper($sample->tr04_payment_status ?? 'PENDING');
        $isPaymentDone = in_array($paymentStatus, ['PAID', 'HOLD']) || ($sample->walletTransactions->isNotEmpty());
        $stage2Status  = $isPaymentDone ? ($paymentStatus === 'PAID' ? 'COMPLETED' : 'HOLD') : 'PENDING';

        $stages[] = [
            'id'          => 2,
            'slug'        => 'billing',
            'title'       => 'Commercial & Payment Hold',
            'short_title' => 'Billing & Payment',
            'icon'        => 'ni-money',
            'theme'       => $stage2Status === 'COMPLETED' ? 'success' : ($stage2Status === 'HOLD' ? 'info' : 'warning'),
            'status'      => $stage2Status,
            'timestamp'   => $sample->holdTransaction?->created_at ?? $sample->walletTransactions->first()?->created_at ?? $sample->created_at,
            'actor_name'  => $sample->customer->m07_name ?? 'Customer Account',
            'actor_role'  => 'Wallet / Accounts System',
            'location'    => 'Billing & Wallet Service',
            'summary'     => 'Testing charges calculated and funds held in customer wallet escrow.',
            'data'        => [
                'commercial_type'    => $sample->tr04_commercial_type ?? 'Commercial',
                'payment_by'         => ucwords(str_replace('_', ' ', $sample->tr04_payment_by ?? 'First Party')),
                'testing_charges'    => number_format((float)($sample->tr04_testing_charges ?? 0), 2),
                'additional_charges' => number_format((float)($sample->tr04_additional_charges ?? 0), 2),
                'tax_amount'         => number_format((float)(($sample->tr04_cgst ?? 0) + ($sample->tr04_sgst ?? 0) + ($sample->tr04_igst ?? 0)), 2),
                'total_charges'      => number_format((float)($sample->tr04_total_charges ?? 0), 2),
                'payment_status'     => $paymentStatus,
                'transactions'       => $sample->walletTransactions->map(function ($wt) {
                    return [
                        'tx_id'       => $wt->tr03_transaction_id,
                        'type'        => $wt->tr03_type,
                        'amount'      => number_format((float)$wt->tr03_amount, 2),
                        'status'      => $wt->tr03_status,
                        'method'      => $wt->tr03_payment_method ?: 'Wallet Escrow',
                        'description' => $wt->tr03_description,
                        'date'        => $wt->created_at ? $wt->created_at->format('d M Y, h:i A') : 'N/A',
                    ];
                })->toArray(),
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 3: Contract Review & Technical Feasibility
        // -----------------------------------------------------------------
        $crStatus    = strtoupper($sample->tr04_contract_review_status ?? '');
        $crReviewer  = $sample->tr04_contract_reviewed_by_name ?? $sample->contractReviewer?->m06_name;
        $isCrDone    = !empty($crStatus) && $crStatus !== 'PENDING';
        $stage3Status = $isCrDone ? ($crStatus === 'REJECTED' ? 'ALERT' : 'COMPLETED') : 'IN_PROGRESS';

        $stages[] = [
            'id'          => 3,
            'slug'        => 'contract_review',
            'title'       => 'Contract Review & Feasibility Check',
            'short_title' => 'Contract Review',
            'icon'        => 'ni-shield-check',
            'theme'       => $stage3Status === 'COMPLETED' ? 'success' : ($stage3Status === 'ALERT' ? 'danger' : 'warning'),
            'status'      => $stage3Status,
            'timestamp'   => $sample->tr04_contract_reviewed_at ?? $sample->created_at,
            'actor_name'  => $crReviewer ?: 'Technical Manager / Quality Officer',
            'actor_role'  => $sample->tr04_contract_reviewed_by_role ?: 'Quality In-Charge',
            'location'    => 'Quality Assurance Section',
            'summary'     => 'Assessment of laboratory capability, test standard availability, environmental conditions, and customer turnaround commitment.',
            'data'        => [
                'review_status'      => $crStatus ?: 'COMPLETED',
                'reviewed_by'        => $crReviewer ?: 'Verified at Registration',
                'reviewed_at'        => $sample->tr04_contract_reviewed_at ? $sample->tr04_contract_reviewed_at->format('d M Y, h:i A') : $sample->created_at->format('d M Y, h:i A'),
                'review_remarks'     => $sample->tr04_contract_review_remarks ?? 'All parameters within technical testing scope and feasibility verified.',
                'decision_rule'      => $sample->tr04_decision_rule ?? 'Standard Laboratory Rule (ISO 17025)',
                'checklist'          => is_array($sample->tr04_feasibility_checklist) ? $sample->tr04_feasibility_checklist : [],
                'customer_deviation' => $sample->tr04_customer_agreed_deviation ? 'Agreed' : 'None',
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 4: Test Allotment & Work Routing
        // -----------------------------------------------------------------
        $sampleTests    = $sample->sampleTests;
        $totalTests     = $sampleTests->count();
        $completedTests = $sampleTests->where('tr05_status', 'COMPLETED')->count();
        $allotedTests   = $sampleTests->whereIn('tr05_status', ['ALLOTED', 'IN_PROGRESS', 'COMPLETED'])->count();

        $stage4Status = 'PENDING';
        if ($totalTests > 0) {
            if ($completedTests === $totalTests) {
                $stage4Status = 'COMPLETED';
            } elseif ($allotedTests > 0) {
                $stage4Status = 'IN_PROGRESS';
            }
        }

        $stages[] = [
            'id'          => 4,
            'slug'        => 'allotment',
            'title'       => 'Test Allotment & Analytical Routing',
            'short_title' => 'Test Allotment',
            'icon'        => 'ni-growth',
            'theme'       => $stage4Status === 'COMPLETED' ? 'success' : 'info',
            'status'      => $stage4Status,
            'timestamp'   => $sampleTests->min('tr05_alloted_at') ?? $sample->created_at,
            'actor_name'  => $sampleTests->first()?->allotedBy?->m06_name ?? 'Technical Manager',
            'actor_role'  => 'Allotment Authority',
            'location'    => 'Analytical Division',
            'summary'     => "Distribution of {$totalTests} test parameter(s) to qualified laboratory analysts.",
            'data'        => [
                'total_tests'     => $totalTests,
                'completed_tests' => $completedTests,
                'test_list'       => $sampleTests->map(function ($st) {
                    return [
                        'test_number'   => $st->m12_test_number,
                        'name'          => $st->test->m12_name ?? ('Test #' . $st->m12_test_number),
                        'standard'      => $st->standard->m15_standard_code ?? $st->standard->m15_method ?? 'In-house / Standard Method',
                        'analyst_name'  => $st->allotedTo->m06_name ?? 'Unassigned / Direct Entry',
                        'status'        => $st->tr05_status ?? 'PENDING',
                        'priority'      => $st->tr05_priority ?? 'NORMAL',
                        'alloted_at'    => $st->tr05_alloted_at ? $st->tr05_alloted_at->format('d M Y, h:i A') : 'N/A',
                        'completed_at'  => $st->tr05_completed_at ? $st->tr05_completed_at->format('d M Y, h:i A') : 'Pending',
                    ];
                })->toArray(),
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 5: Chain of Custody & Sample Location Handover
        // -----------------------------------------------------------------
        $custodyLogs     = $sample->custodyLogs;
        $currentCustody  = $sample->tr04_current_custody_role ?: ($sample->reports->isNotEmpty() ? 'Issuing Officer' : 'Analyst');
        $isUnderClar     = (bool)($sample->tr04_is_under_clarification ?? 0);
        $stage5Status    = $isUnderClar ? 'ALERT' : ($custodyLogs->isNotEmpty() ? 'COMPLETED' : 'IN_PROGRESS');

        $stages[] = [
            'id'          => 5,
            'slug'        => 'custody',
            'title'       => 'Chain of Custody & Inter-Department Movement',
            'short_title' => 'Chain of Custody',
            'icon'        => 'ni-shuffle',
            'theme'       => $stage5Status === 'ALERT' ? 'danger' : 'purple',
            'status'      => $stage5Status,
            'timestamp'   => $custodyLogs->last()?->created_at ?? $sample->created_at,
            'actor_name'  => $custodyLogs->last()?->from_name ?: ($custodyLogs->last()?->fromUser?->m06_name ?: 'System'),
            'actor_role'  => 'Current Custodian: ' . $currentCustody,
            'location'    => 'Current Custody: ' . $currentCustody,
            'summary'     => "Full custody trail showing where the physical sample and dossier reside across departments.",
            'data'        => [
                'current_custody'         => $currentCustody,
                'is_under_clarification'  => $isUnderClar,
                'clarification_remark'    => $sample->tr04_latest_clarification_remark,
                'sent_back_by'            => $sample->tr04_sent_back_by_name,
                'sent_back_at'            => $sample->tr04_sent_back_at ? Carbon::parse($sample->tr04_sent_back_at)->format('d M Y, h:i A') : null,
                'logs'                    => $custodyLogs->map(function ($cl) {
                    return [
                        'id'          => $cl->tr10_custody_log_id,
                        'from_role'   => $cl->from_role,
                        'from_name'   => $cl->from_name ?: ($cl->fromUser?->m06_name ?: 'Authorized Officer'),
                        'to_role'     => $cl->to_role,
                        'to_name'     => $cl->to_name ?: ($cl->toUser?->m06_name ?: 'Receiving Officer'),
                        'action'      => $cl->action_type,
                        'remark'      => $cl->clarification_remark,
                        'is_resolved' => (bool)$cl->is_resolved,
                        'timestamp'   => $cl->created_at ? $cl->created_at->format('d M Y, h:i A') : 'N/A',
                    ];
                })->toArray(),
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 6: Physical Manuscripts & Raw Calculations
        // -----------------------------------------------------------------
        $manuscripts   = $sample->manuscripts;
        $stage6Status  = $manuscripts->isNotEmpty() ? 'COMPLETED' : 'IN_PROGRESS';

        $stages[] = [
            'id'          => 6,
            'slug'        => 'manuscripts',
            'title'       => 'Bench Manuscripts & Raw Calculations',
            'short_title' => 'Manuscripts & Calculations',
            'icon'        => 'ni-notes-alt',
            'theme'       => $stage6Status === 'COMPLETED' ? 'success' : 'info',
            'status'      => $stage6Status,
            'timestamp'   => $manuscripts->first()?->created_at ?? $sample->sampleTests->min('tr05_completed_at') ?? $sample->created_at,
            'actor_name'  => $manuscripts->first()?->analyst_name ?? 'Bench Analysts',
            'actor_role'  => 'Analytical Bench Testing',
            'location'    => 'Laboratory Bench / Instrument Room',
            'summary'     => 'Archived physical test observation sheets, instrument printouts, and calculation records.',
            'data'        => [
                'total_manuscripts' => $manuscripts->count(),
                'manuscript_files'  => $manuscripts->map(function ($m) {
                    return [
                        'analyst'    => $m->analyst_name ?? ('Analyst #' . $m->m06_analyst_id),
                        'page'       => "Page {$m->page_number} of " . ($m->total_pages ?: 1),
                        'tests'      => $m->test_names ?: 'Assigned Tests',
                        'file_path'  => $m->file_path,
                        'created_at' => $m->created_at ? $m->created_at->format('d M Y, h:i A') : 'N/A',
                        'remarks'    => $m->remarks,
                    ];
                })->toArray(),
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 7: Results Entry, Footnotes & Comprehensive Audit Trail
        // -----------------------------------------------------------------
        $testResults   = $sample->testResult;
        $hasResults    = $testResults->isNotEmpty();
        $stage7Status  = $hasResults ? 'COMPLETED' : 'PENDING';

        // Gather all audit records across all results of this sample
        $resultIds     = $testResults->pluck('tr07_test_result_id')->filter()->toArray();
        $auditRecords  = TestResultAudit::with(['user', 'testResult.test'])
            ->whereIn('tr07_test_result_id', $resultIds)
            ->orderByDesc('tr10_created_at')
            ->get();

        $stages[] = [
            'id'          => 7,
            'slug'        => 'results_audit',
            'title'       => 'Analytical Results & Audit History',
            'short_title' => 'Results & Audits',
            'icon'        => 'ni-activity-round',
            'theme'       => 'purple',
            'status'      => $stage7Status,
            'timestamp'   => $testResults->min('created_at') ?? $testResults->first()?->tr07_test_date ?? $sample->created_at,
            'actor_name'  => $testResults->first()?->creator?->m06_name ?? ($sample->sampleTests->first()?->allotedTo?->m06_name ?? 'Analyst'),
            'actor_role'  => 'Analyst / Data Entry Operator',
            'location'    => 'Data Entry Section',
            'summary'     => 'Recorded observations, analytical findings, footnotes, and chronological edit history.',
            'data'        => [
                'results_count' => $testResults->count(),
                'results_list'  => $testResults->map(function ($r) {
                    return [
                        'id'               => $r->tr07_test_result_id,
                        'test_name'        => $r->test->m12_name ?? ('Test #' . $r->m12_test_number),
                        'primary_param'    => $r->primaryTest->m16_name ?? null,
                        'secondary_param'  => $r->secondaryTest->m17_name ?? null,
                        'result'           => $r->tr07_result ?? 'N/A',
                        'unit'             => $r->tr07_unit ?? '',
                        'status'           => $r->tr07_result_status ?? 'DRAFT',
                        'remarks'          => $r->tr07_remarks ?: ($r->remarkRecord?->m24_remark_text ?? ''),
                        'entered_by'       => $r->creator?->m06_name ?? 'Analyst',
                        'entered_at'       => $r->created_at ? $r->created_at->format('d M Y, h:i A') : 'N/A',
                        'updated_by'       => $r->updater?->m06_name ?? null,
                        'updated_at'       => $r->updated_at ? $r->updated_at->format('d M Y, h:i A') : null,
                    ];
                })->toArray(),
                'audits'        => $auditRecords->map(function ($a) {
                    return [
                        'audit_id'      => $a->tr10_audit_id,
                        'action'        => $a->tr10_action,
                        'field_changed' => $a->tr10_field_changed,
                        'old_value'     => $a->tr10_old_value,
                        'new_value'     => $a->tr10_new_value,
                        'change_reason' => $a->tr10_change_reason ?: 'Revised result entry',
                        'user_name'     => $a->user?->m06_name ?? ('User #' . $a->tr10_user_id),
                        'ip_address'    => $a->tr10_ip_address,
                        'timestamp'     => $a->tr10_created_at ? $a->tr10_created_at->format('d M Y, h:i A') : 'N/A',
                        'test_name'     => $a->testResult?->test?->m12_name ?? 'Test Parameter',
                    ];
                })->toArray(),
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 8: Result Verification & Technical Review
        // -----------------------------------------------------------------
        $verifiedResults  = $testResults->whereIn('tr07_result_status', ['VERIFIED', 'REPORTED'])->count();
        $rejectedResults  = $testResults->where('tr07_result_status', 'REJECTED')->count();
        $isVerified       = ($hasResults && $verifiedResults === $testResults->count());
        $isVerificationSentBack = ($rejectedResults > 0) || $isUnderClar;

        $stage8Status = 'PENDING';
        if ($isVerified) {
            $stage8Status = 'COMPLETED';
        } elseif ($isVerificationSentBack) {
            $stage8Status = 'ALERT';
        } elseif ($hasResults) {
            $stage8Status = 'IN_PROGRESS';
        }

        $stages[] = [
            'id'          => 8,
            'slug'        => 'verification',
            'title'       => 'Technical Verification & Quality Review',
            'short_title' => 'Technical Verification',
            'icon'        => 'ni-check-circle-cut',
            'theme'       => $stage8Status === 'COMPLETED' ? 'success' : ($stage8Status === 'ALERT' ? 'danger' : 'info'),
            'status'      => $stage8Status,
            'timestamp'   => $testResults->first()?->tr07_verified_at ?? $custodyLogs->where('to_role', 'Issuing Officer')->first()?->created_at ?? $sample->updated_at,
            'actor_name'  => $sample->custodyLogs->where('from_role', 'Verification Officer')->first()?->from_name ?? 'Verification Officer',
            'actor_role'  => 'Verification Officer / Jr. QA',
            'location'    => 'Verification & Quality Section',
            'summary'     => 'Senior technical review checking calculation precision, standard method alignment, and data consistency.',
            'data'        => [
                'verification_status' => $isVerified ? 'VERIFIED' : ($isVerificationSentBack ? 'SENT_BACK_FOR_CLARIFICATION' : 'PENDING_VERIFICATION'),
                'verified_count'      => $verifiedResults,
                'total_count'         => $testResults->count(),
                'verified_at'         => $testResults->first()?->tr07_verified_at ? Carbon::parse($testResults->first()->tr07_verified_at)->format('d M Y, h:i A') : ($isVerified ? 'Verified' : 'Pending'),
                'clarifications'      => $custodyLogs->where('action_type', 'SEND_BACK')->map(function ($cl) {
                    return [
                        'by'     => $cl->from_name ?: $cl->from_role,
                        'to'     => $cl->to_name ?: $cl->to_role,
                        'remark' => $cl->clarification_remark,
                        'date'   => $cl->created_at ? $cl->created_at->format('d M Y, h:i A') : 'N/A',
                    ];
                })->values()->toArray(),
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 9: Report Generation, Versioning & ULR Issuance
        // -----------------------------------------------------------------
        $reports       = $sample->reports;
        $latestReport  = $reports->first();
        $isReportDone  = $reports->isNotEmpty();
        $stage9Status  = $isReportDone ? 'COMPLETED' : 'PENDING';

        $stages[] = [
            'id'          => 9,
            'slug'        => 'reporting',
            'title'       => 'Test Report Finalization & ULR Issuance',
            'short_title' => 'Report & ULR',
            'icon'        => 'ni-reports',
            'theme'       => $stage9Status === 'COMPLETED' ? 'success' : 'warning',
            'status'      => $stage9Status,
            'timestamp'   => $latestReport?->tr09_generated_at ?? $latestReport?->created_at,
            'actor_name'  => $latestReport?->generator?->m06_name ?? 'Authorized Signatory',
            'actor_role'  => 'Technical Manager / Authorized Signatory',
            'location'    => 'Reporting & Issuance Cell',
            'summary'     => 'Generation of NABL compliant Test Report with unique ULR, QR code authentication, and digital signatures.',
            'data'        => [
                'ulr_no'            => $sample->tr04_ulr_no ?: 'Generated on Report Finalization',
                'report_versions'   => $reports->map(function ($rep) {
                    return [
                        'version'      => $rep->tr09_version_number,
                        'status'       => $rep->tr09_status,
                        'is_current'   => $rep->tr09_is_current,
                        'generated_by' => $rep->generator?->m06_name ?? 'Authorized Signatory',
                        'generated_at' => $rep->tr09_generated_at ? Carbon::parse($rep->tr09_generated_at)->format('d M Y, h:i A') : ($rep->created_at ? $rep->created_at->format('d M Y, h:i A') : 'N/A'),
                        'file_path'    => $rep->tr09_report_file_path,
                    ];
                })->toArray(),
                'latest_file_path'  => $latestReport?->tr09_report_file_path,
            ],
        ];

        // -----------------------------------------------------------------
        // STAGE 10: Dispatch & Customer Delivery
        // -----------------------------------------------------------------
        $isDispatched  = !empty($sample->tr04_dispatch_date);
        $stage10Status = $isDispatched ? 'COMPLETED' : ($isReportDone ? 'IN_PROGRESS' : 'PENDING');

        $stages[] = [
            'id'          => 10,
            'slug'        => 'dispatch',
            'title'       => 'Physical / Electronic Dispatch & Delivery',
            'short_title' => 'Dispatch & Delivery',
            'icon'        => 'ni-truck',
            'theme'       => $stage10Status === 'COMPLETED' ? 'success' : 'info',
            'status'      => $stage10Status,
            'timestamp'   => $sample->tr04_dispatch_date ? Carbon::parse($sample->tr04_dispatch_date) : null,
            'actor_name'  => $sample->tr04_dispatch_person ?? 'Dispatch Cell',
            'actor_role'  => 'Logistics / Despatch Officer',
            'location'    => 'Despatch Counter',
            'summary'     => 'Formal delivery of official test certificate to customer/buyer via chosen mode.',
            'data'        => [
                'dispatch_mode'     => $sample->tr04_dispatch_mode ?: ($isReportDone ? 'Available for Electronic Download' : 'Pending Completion'),
                'dispatch_person'   => $sample->tr04_dispatch_person ?: 'Despatch In-Charge',
                'dispatch_date'     => $sample->tr04_dispatch_date ? Carbon::parse($sample->tr04_dispatch_date)->format('d M Y, h:i A') : ($isDispatched ? 'Dispatched' : 'Pending'),
                'article_no'        => $sample->tr04_post_article_no ?: 'N/A',
                'report_to'         => ucwords(str_replace('_', ' ', $sample->tr04_report_to ?? 'Customer')),
            ],
        ];

        return $stages;
    }

    /**
     * Calculate journey statistics and high-level health metrics.
     */
    private function calculateJourneyStats(SampleRegistration $sample, array $stages): array
    {
        $completedCount = collect($stages)->where('status', 'COMPLETED')->count();
        $totalCount     = count($stages);
        $progressPct    = (int)round(($completedCount / $totalCount) * 100);

        // Find active stage
        $activeStageIndex = 0;
        foreach ($stages as $idx => $stg) {
            if ($stg['status'] === 'IN_PROGRESS' || $stg['status'] === 'ALERT') {
                $activeStageIndex = $idx;
                break;
            }
            if ($stg['status'] === 'COMPLETED') {
                $activeStageIndex = $idx;
            }
        }

        return [
            'progress_percent'    => min(100, max(10, $progressPct)),
            'completed_stages'    => $completedCount,
            'total_stages'        => $totalCount,
            'active_stage_index'  => $activeStageIndex,
            'current_custody'     => $sample->tr04_current_custody_role ?: ($sample->reports->isNotEmpty() ? 'Issuing Officer' : 'Analyst'),
            'payment_status'      => strtoupper($sample->tr04_payment_status ?? 'PENDING'),
            'total_charges'       => $sample->tr04_total_charges ?? 0,
            'is_under_clar'       => (bool)($sample->tr04_is_under_clarification ?? 0),
            'has_report'          => $sample->reports->isNotEmpty(),
            'ulr_no'              => $sample->tr04_ulr_no,
        ];
    }
}

