<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Employee;
use App\Models\SampleManuscript;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Services\CustodyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VerificationController extends Controller
{
    public function viewVerification()
    {
        $samples = SampleTest::with(['registration', 'test', 'registration.testResult', 'registration.customFields'])
            ->where('m04_ro_id', session('ro_id'))
            ->whereNotIn('tr05_status', ['TRANSFERRED'])
            ->where(function ($query) {
                $query->whereHas('registration.testResult', function ($q) {
                    $q->where('tr07_is_current', 'YES')
                        ->whereIn('tr07_result_status', ['RESULTED', 'REVISED', 'SUBMITTED']);
                })
                ->orWhereHas('registration.customFields', function ($q) {
                    $q->where('tr08_is_current', 'YES')
                        ->whereIn('tr08_result_status', ['SUBMITTED', 'RESULTED']);
                });
            })
            ->select('tr04_sample_registration_id')
            ->selectRaw("
            COUNT(*) as total_tests,
            SUM(CASE WHEN tr05_status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_tests,
            SUM(CASE WHEN tr05_status != 'COMPLETED' THEN 1 ELSE 0 END) as pending_tests
        ")
            ->groupBy('tr04_sample_registration_id')
            ->get()
            ->filter(function ($sample) {
                $registration = $sample->registration;

                // If sample was sent back to DEO, Analyst, Manager or RC, Verification Officer will not see it until it returns
                if ($registration && $registration->tr04_is_under_clarification && $registration->tr04_current_custody_role !== 'Verification Officer') {
                    return false;
                }

                $results = optional($registration)->testResult ?? collect();
                $customFields = optional($registration)->customFields ?? collect();

                // Only take current results
                $currentResults = $results->where('tr07_is_current', 'YES');
                $currentCustomFields = $customFields->where('tr08_is_current', 'YES')
                    ->whereIn('tr08_result_status', ['SUBMITTED', 'RESULTED']);

                // Allow partial verification: Include sample if at least one current test is RESULTED, REVISED, or SUBMITTED,
                // or if there are current custom fields for the sample.
                $hasResults = $currentResults->isNotEmpty() && $currentResults->contains(function ($r) {
                    return in_array($r->tr07_result_status, ['RESULTED', 'REVISED', 'SUBMITTED']);
                });

                return $hasResults || $currentCustomFields->isNotEmpty();
            })
            ->map(function ($sample) {
                $registration = $sample->registration;
                $results = optional($registration)->testResult ?? collect();

                // Use only current results for counting
                $currentResults = $results->where('tr07_is_current', 'YES');

                $sample->reference_id = $registration?->tr04_reference_id ?? '-';
                $sample->sample_id = $registration?->tr04_sample_registration_id ?? '-';
                $sample->priority = $registration?->tr04_sample_type ?? 'NORMAL';
                $sample->created_at = $registration?->created_at;
                $sample->delay_days = $registration
                    ? round(abs(now()->floatDiffInDays($registration->created_at)), 2)
                    : null;

                $sample->allResulted = $currentResults->count() > 0 &&
                    $currentResults->every(
                        fn($r) =>
                        in_array($r->tr07_result_status, ['RESULTED', 'REVISED', 'SUBMITTED', 'VERIFIED', 'REPORTED', 'FINALIZED'])
                    );

                // Count REVISED vs RESULTED only for current results
                $sample->revised_count = $currentResults->where('tr07_result_status', 'REVISED')->count();
                $sample->resulted_count = $currentResults->where('tr07_result_status', 'RESULTED')->count();

                return $sample;
            })
            ->sortByDesc(function ($s) {
                return [
                    $s->pending_tests == 0 ? 1 : 0,
                    $s->priority == 'TATKAL' ? 1 : 0,
                    $s->delay_days,
                ];
            })
            ->values();
        return view('verification.view_samples_verification', compact('samples'));
    }

    public function verifyResult(Request $request, $sample_id)
    {
        ini_set('memory_limit', '512M');
        $sample = SampleRegistration::with([
            'testResult.test',
            'testResult.primaryTest',
            'testResult.secondaryTest',
            'testResult.creator.role',
            'testResult.updater.role',
            'labSample',
            'customFields'
        ])->where('tr04_sample_registration_id', $sample_id)
            ->where(function ($query) {
                $roId = session('ro_id');
                $query->where('m04_ro_id', $roId)
                    ->orWhereHas('sampleTests', function ($q) use ($roId) {
                        $q->where('m04_ro_id', $roId);
                    });
            })
            ->firstOrFail();
        if ($request->isMethod('POST')) {
            $isReject = $request->action === 'reject';
            $request->validate([
                'action' => 'required|in:verify,reject',
                'target_role' => 'nullable|in:Analyst,DEO',
                'remarks' => $isReject ? 'required|string|min:3|max:1000' : 'nullable|string|max:1000',
                'reassigned_analyst_id' => 'nullable',
            ], [
                'remarks.required' => 'A clear clarification remark is strictly mandatory when rejecting/sending back results.',
                'remarks.min' => 'Please provide a descriptive remark (minimum 3 characters) explaining why results are being sent back.',
            ]);

            DB::beginTransaction();
            try {
                $targetRole = $request->input('target_role', 'Analyst');
                $resultStatus = $request->action === 'verify' ? 'VERIFIED' : ($targetRole === 'DEO' ? 'DRAFT' : 'REJECTED');

                // Update test results
                $currentResults = $sample->testResult->where('tr07_is_current', 'YES');

                foreach ($currentResults as $result) {
                    $result->update([
                        'tr07_result_status' => $resultStatus,
                        'm06_verified_by'    => Session::get('user_id') ?? null,
                        'tr07_verified_at'   => now(),
                        'tr07_remarks'       => $request->remarks,
                    ]);

                    // Handle Reassignment (if rejecting and Analyst selected)
                    if ($request->action === 'reject' && $targetRole === 'Analyst' && $request->filled('reassigned_analyst_id')) {
                        $sampleTest = \App\Models\SampleTest::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                            ->where('m12_test_id', $result->test->m12_test_id)
                            ->first();

                        if ($sampleTest) {
                            $sampleTest->update([
                                'm06_alloted_to' => $request->reassigned_analyst_id,
                                'tr05_status' => 'ALLOTED',
                            ]);
                        }
                    }
                }

                // Update custom fields
                $customFields = CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->whereIn('tr08_result_status', ['SUBMITTED', 'DRAFT', 'RESULTED'])
                    ->get();

                foreach ($customFields as $customField) {
                    $customField->update([
                        'tr08_result_status' => $resultStatus,
                        'm06_updated_by'     => Session::get('user_id') ?? null,
                        'tr08_updated_at'    => now(),
                        'tr08_remarks'       => $request->remarks,
                    ]);
                }

                // Integrate with CustodyService
                if ($request->action === 'verify') {
                    CustodyService::forwardOrResolve(
                        $sample->tr04_sample_registration_id,
                        'Verification Officer',
                        'Issuing Officer',
                        $request->remarks
                    );
                } else {
                    CustodyService::sendBack(
                        $sample->tr04_sample_registration_id,
                        'Verification Officer',
                        $targetRole,
                        $request->remarks,
                        $request->reassigned_analyst_id
                    );
                }

                DB::commit();

                Session::flash('type', 'success');
                Session::flash('message', 'Results ' . ($request->action === 'verify' ? 'verified' : "sent back to {$targetRole} for clarification") . ' successfully.');
                return to_route('view_result_verification');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Sample Verification Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to update results: ' . $e->getMessage());
                return back();
            }
        }

        // Get current results
        $currentResults = $sample->testResult->where('tr07_is_current', 'YES');

        // Get historical results for comparison (where current is NO)
        $historicalResults = $sample->testResult->where('tr07_is_current', 'NO');

        // Get custom fields
        $customFields = CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
            ->where('tr08_is_current', 'YES')
            ->get();

        $historicalCustomFields = CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
            ->where('tr08_is_current', 'NO')
            ->get();

        // Group results by test number
        $testNumbers = $currentResults->pluck('m12_test_number')->unique();
        $sampleTests = SampleTest::with(['test', 'test.standard', 'allotedTo'])
            ->where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
            ->whereIn('m12_test_number', $testNumbers)
            ->get();

        // Organize tests with hierarchy (similar to AnalystController)
        foreach ($sampleTests as $sampleTest) {
            if ($sampleTest->test) {
                $test = $sampleTest->test;
                
                // Get primary test IDs from master + results + custom fields
                $masterPrimaryIds = array_filter(explode(',', $test->m16_primary_test_id ?? ''));
                $resultPrimaryIds = $currentResults->where('m12_test_number', $test->m12_test_number)->pluck('m16_primary_test_id')->filter()->unique()->toArray();
                $customPrimaryIds = $customFields->where('m12_test_number', $test->m12_test_number)->pluck('m16_primary_test_id')->filter()->unique()->toArray();
                
                $allPrimaryIds = array_unique(array_merge($masterPrimaryIds, $resultPrimaryIds, $customPrimaryIds));
                
                $primaryTests = collect();
                if (!empty($allPrimaryIds)) {
                    $primaryTests = \DB::table('m16_primary_tests')->whereIn('m16_primary_test_id', $allPrimaryIds)->get();
                    foreach ($primaryTests as $p) {
                        $p->secondaryTests = collect(\DB::table('m17_secondary_tests')->where('m16_primary_test_id', $p->m16_primary_test_id)->get());
                    }
                }
                $test->primaryTests = $primaryTests;
            }
        }

        // Get analysts for reassignment dropdown (supporting multi-role & validity)
        $roId = Session::get('ro_id');
        $analysts = Employee::where('m06_employees.m04_ro_id', $roId)
            ->where('m06_employees.m06_status', 'Active')
            ->where(function ($q) {
                $q->whereHas('role', function ($sub) {
                    $sub->where('m03_name', 'Analyst');
                });
                if (\Illuminate\Support\Facades\Schema::hasTable('tr01_user_roles')) {
                    $q->orWhereHas('roles', function ($sub) {
                        $sub->where('m03_name', 'Analyst');
                    });
                }
            })
            ->select('m06_employees.m06_employee_id', 'm06_employees.m06_name', 'm06_employees.m06_valid_upto')
            ->orderBy('m06_employees.m06_name')
            ->get()
            ->filter(function ($emp) {
                return !$emp->isExpired();
            })
            ->map(function ($emp) {
                $emp->role = 'Analyst';
                return $emp;
            })
            ->values();

        // Self-healing schema validation & fetch active uploaded multi-analyst manuscripts
        SampleManuscript::ensureManuscriptSchemaExists();
        $sampleManuscripts = SampleManuscript::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
            ->where('is_current', 1)
            ->orderBy('m06_analyst_id')
            ->orderBy('page_number')
            ->orderBy('tr11_manuscript_id')
            ->get();

        return view('verification.view_result', compact(
            'sample', 
            'sampleTests', 
            'currentResults', 
            'historicalResults', 
            'customFields', 
            'historicalCustomFields',
            'analysts',
            'sampleManuscripts'
        ));
    }
}
