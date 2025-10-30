<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Group;
use App\Models\Ro;
use App\Models\Sample;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\Standard;
use App\Models\Test;
use App\Models\TestTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use function Illuminate\Log\log;

class AllottmentController extends Controller
{
    private const BATCH_SIZE = 100;


    public function pendingAllotments(Request $request)
    {
        $roId = Session::get('ro_id');
        $query = $this->buildPendingQuery($roId);
        $this->applyFilters($query, $request);
        $this->applySorting($query);

        $pendingRegistrations = $query->paginate(20)->appends($request->query());

        // Separate into two collections
        $allSamples = $pendingRegistrations;

        // Get unallotted or partially allotted (where allotted_tests < total_tests)
        $unallottedOrPartial = $pendingRegistrations->filter(function ($reg) {
            return $reg->allotted_tests < $reg->total_tests;
        });

        $stats = $this->calculateLabManagerStats($roId);
        $employees = $this->getLabEmployees($roId);
        $ros = Ro::select('m04_ro_id', 'm04_name')->where('m04_ro_id', '!=', $roId)->orderBy('m04_name')->get();
        $availableTests = $this->getAvailableTestsForAllotment();

        return view('allottment.allotment_dashboard', compact(
            'pendingRegistrations',
            'allSamples',           // NEW
            'unallottedOrPartial',  // NEW
            'stats',
            'employees',
            'ros',
            'availableTests'
        ));
    }

    public function getAvailableTestsForAllotment(Request $request = null)
    {
        $roId = Session::get('ro_id');

        try {
            // Get all unique tests that have unallotted samples in the current RO
            $tests = SampleTest::join('m12_tests', 'tr05_sample_tests.m12_test_id', '=', 'm12_tests.m12_test_id')
                ->select(
                    'm12_tests.m12_test_id',
                    'm12_tests.m12_name',
                    DB::raw('COUNT(tr05_sample_tests.tr05_sample_test_id) as test_count')
                )
                ->where(function ($query) use ($roId) {
                    $query->where('tr05_sample_tests.m04_ro_id', $roId);
                })
                ->whereNull('tr05_sample_tests.m06_alloted_to')
                ->whereNotIn('tr05_sample_tests.tr05_status', ['COMPLETED', 'TRANSFERRED'])
                ->groupBy('m12_tests.m12_test_id', 'm12_tests.m12_name')
                ->orderBy('m12_tests.m12_name')
                ->get();

            // If this is an AJAX request, return JSON
            if ($request && $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'tests' => $tests
                ]);
            }

            // Otherwise return the data for view
            return $tests;
        } catch (\Exception $e) {
            Log::error('Get Available Tests Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'ro_id' => $roId,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request && $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load available tests'
                ]);
            }

            return collect(); // Return empty collection for view
        }
    }

    public function getTestSamplesForAllotment(Request $request)
    {
        $roId = Session::get('ro_id');
        $testId = $request->input('test_id');

        try {
            if (!$testId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test ID is required'
                ]);
            }

            // Get all unallotted samples for the specific test
            $testSamples = SampleTest::with(['registration'])
                ->join('m12_tests', 'tr05_sample_tests.m12_test_id', '=', 'm12_tests.m12_test_id')
                ->select(
                    'tr05_sample_tests.*',
                    'm12_tests.m12_name as test_name'
                )
                ->where('tr05_sample_tests.m12_test_id', $testId)
                ->where(function ($query) use ($roId) {
                    $query->where('tr05_sample_tests.m04_ro_id', $roId)
                        ->orWhere('tr05_sample_tests.m04_transferred_to', $roId);
                })
                ->whereNull('tr05_sample_tests.m06_alloted_to')
                ->whereNotIn('tr05_sample_tests.tr05_status', ['COMPLETED', 'TRANSFERRED'])
                ->orderBy('tr05_sample_tests.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'samples' => $testSamples,
                'test_name' => $testSamples->first()->test_name ?? 'Unknown Test'
            ]);
        } catch (\Exception $e) {
            Log::error('Get Test Samples Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'ro_id' => $roId,
                'test_id' => $testId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load test samples'
            ]);
        }
    }

    private function buildPendingQuery($roId)
    {
        return SampleRegistration::selectRaw("
            tr04_sample_registrations.*,
            COUNT(st.tr05_sample_test_id) as total_tests,
            COUNT(CASE WHEN st.m06_alloted_to IS NOT NULL THEN 1 END) as allotted_tests,
            COUNT(CASE WHEN st.m06_alloted_to IS NULL THEN 1 END) as pending_tests,
            COUNT(CASE WHEN st.tr05_status = 'TRANSFERRED' AND st.m04_ro_id = {$roId} THEN 1 END) as transferred_tests
        ")
            ->join('tr05_sample_tests as st', 'st.tr04_sample_registration_id', '=', 'tr04_sample_registrations.tr04_sample_registration_id')
            ->where(function ($query) use ($roId) {
                $query->where('st.m04_ro_id', $roId);
            })
            ->groupBy(
                'tr04_sample_registrations.tr04_sample_registration_id',
                'tr04_sample_registrations.tr04_reference_id',
                'tr04_sample_registrations.tr04_sample_type',
                'tr04_sample_registrations.tr04_progress',
                'tr04_sample_registrations.tr04_status',
                'tr04_sample_registrations.created_at'
            );
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('priority')) {
            $query->where('tr04_sample_registrations.tr04_sample_type', $request->priority);
        }

        if ($request->filled('days_pending')) {
            $days = (int) $request->days_pending;
            $query->where('tr04_sample_registrations.created_at', '<=', now()->subDays($days));
        }

        if ($request->filled('status')) {
            // dd($request);
            match ($request->status) {
                'new' => $query->having('allotted_tests', '=', 0),
                'partial' => $query->havingRaw('allotted_tests > 0 AND allotted_tests < total_tests'),
                'tatkal' => $query->where(function ($q) {
                    $q->where('tr04_sample_registrations.tr04_sample_type', 'Tatkal')
                        ->orWhere('tr04_sample_registrations.created_at', '<=', now()->subDays(3));
                }),
                default => null
            };
        }
    }

    private function applySorting($query)
    {
        $query->orderByRaw("
            CASE 
                WHEN tr04_sample_registrations.tr04_sample_type = 'Tatkal' THEN 1
                WHEN tr04_sample_registrations.tr04_sample_type = 'Normal' THEN 2
                ELSE 3
            END
        ")->orderBy('tr04_sample_registrations.created_at', 'asc');
    }

    private function calculateLabManagerStats($roId)
    {
        $today = now()->toDateString();
        $baseQuery = SampleTest::where(function ($query) use ($roId) {
            $query->where('m04_ro_id', $roId);
        });

        return [
            // ✅ Samples received today
            'samples_received_today' => SampleRegistration::whereDate('created_at', $today)
                ->whereHas('sampleTests', function ($query) use ($roId) {
                    $query->where('m04_ro_id', $roId);
                })
                ->count(),

            // ✅ Pending samples (not completed or reported)
            'pending_samples' => (clone $baseQuery)
                ->whereNotIn('tr05_status', ['COMPLETED', 'REPORTED', 'TRANSFERRED', 'RECEIVED_ACCEPTED', 'VERIFIED'])
                ->distinct('tr04_sample_registration_id')
                ->count(),

            // ✅ Tested today
            'tested_today' => (clone $baseQuery)
                ->whereDate('tr05_completed_at', $today)
                ->distinct('tr04_sample_registration_id')
                ->count(),

            // ✅ Reported samples
            'reported_samples' => (clone $baseQuery)
                ->where('tr05_status', 'REPORTED')
                ->distinct('tr04_sample_registration_id')
                ->count(),
        ];
    }


    private function getLabEmployees($roId)
    {
        return Employee::join('m03_roles', 'm06_employees.m03_role_id', '=', 'm03_roles.m03_role_id')
            ->where('m06_employees.m04_ro_id', $roId)
            ->whereIn('m03_roles.m03_name', ['Analyst'])
            ->select('m06_employees.m06_employee_id', 'm06_employees.m06_name', 'm03_roles.m03_name as role')
            ->orderBy('m06_employees.m06_name')
            ->get();
    }

    public function viewAllottment($registrationId)
    {
        $roId = Session::get('ro_id');

        $registration = SampleRegistration::with('department')->findOrFail($registrationId);

        $tests = SampleTest::where('tr04_sample_registration_id', $registrationId)
            ->where(function ($q) use ($roId) {
                $q->where('m04_ro_id', $roId)
                    ->orWhere(function ($subQ) use ($roId) {
                        $subQ->where('m04_transferred_to', $roId)
                            ->where('tr05_status', 'TRANSFERRED');
                    });
            })
            ->whereNotIn('tr05_status', ['RECEIVED_ACCEPTED'])
            ->with(['test', 'standard', 'allotedTo', 'transfers'])
            ->orderBy('tr05_sample_test_id')
            ->get();

        $employees = $this->getLabEmployees($roId);
        $ros = Ro::where('m04_ro_id', '!=', $roId)->orderBy('m04_name')->get();

        return view('allottment.allottment', compact('registration', 'tests', 'employees', 'ros'));
    }
    public function editSampleTests(Request $request, $id = null)
    {
        if ($request->isMethod('get')) {
            $sample = SampleRegistration::findOrFail($id);
            $sampleTests = SampleTest::with('test', 'standard')
                ->where('tr04_sample_registration_id', $id)
                ->get();

            $allTests = Test::orderBy('m12_name')->get();
            $samples = Sample::where('m10_status', 'ACTIVE')->get();
            $allStandards = Standard::orderBy('m15_method')->get();
            $groups = Group::where('m11_status', 'ACTIVE')->get();

            return view('allottment.update_sample_tests', compact('samples', 'groups', 'sample', 'sampleTests', 'allTests', 'allStandards'));
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'txt_test_ids' => 'required|array',
                'txt_test_ids.*' => 'required|exists:m12_tests,m12_test_id',
                'txt_standard_ids' => 'required|array',
                'txt_standard_ids.*' => 'required|exists:m15_standards,m15_standard_id',
                'txt_status' => 'array',
            ]);
            try {
                DB::beginTransaction();

                $sample = SampleRegistration::findOrFail($id);
                SampleTest::where('tr04_sample_registration_id', $id)->delete();

                $totalTestCharges = 0;

                foreach ($request->txt_test_ids as $index => $testId) {
                    $test = Test::find($testId);
                    if (!$test) continue;

                    $totalTestCharges += (float) $test->m12_charge;

                    SampleTest::create([
                        'tr04_sample_registration_id' => $id,
                        'm12_test_id' => $testId,
                        'm12_test_number' => $test->m12_test_number,
                        'm15_standard_id' => $request->txt_standard_ids[$index],
                        'm04_ro_id' => $sample->m04_ro_id ?? Session::get('ro_id') ?? -1,
                        'm16_primary_test_id' => $test->m16_primary_test_id ?? null,
                        'm17_secondary_test_id' => $test->m17_secondary_test_id ?? null,
                        'tr05_status' => $request->txt_status[$index] ?? 'PENDING',
                        'tr05_priority' => 'NORMAL',
                    ]);
                }

                $additionalCharges = (float) ($sample->tr04_additional_charges ?? 0);
                $totalCharges = $totalTestCharges + $additionalCharges;

                if (strtolower($sample->tr04_sample_type) === 'tatkal') {
                    $totalCharges = $totalTestCharges * 1.5 + $additionalCharges;
                }

                $update = $sample->update([
                    'tr04_testing_charges' => $totalTestCharges,
                    'tr04_additional_charges' => $additionalCharges,
                    'tr04_total_charges' => $totalCharges,
                ]);
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Sample tests and charges updated successfully.');
                return to_route('view_allottment');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', ['error' => 'Failed to update tests: ' . $e->getMessage()]);
                return back();
            }
        }
    }


    // Bulk Allot Samples
    public function bulkAllotSamples(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sample_ids' => 'required|string',
            'emp_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $sampleIds = array_filter(explode(',', $request->sample_ids));
            if (empty($sampleIds)) {
                Session::flash('type', 'error');
                Session::flash('message', 'No samples selected');
                return redirect()->back();
            }

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');
            $totalUpdated = 0;

            foreach ($sampleIds as $sampleId) {
                $tests = SampleTest::where('tr04_sample_registration_id', $sampleId)
                    ->where(function ($query) use ($roId) {
                        $query->where('m04_ro_id', $roId)
                            ->orWhere('m04_transferred_to', $roId);
                    })
                    ->whereNull('m06_alloted_to')
                    ->whereNotIn('tr05_status', ['COMPLETED', 'TRANSFERRED'])
                    ->get();

                foreach ($tests as $test) {
                    $test->update([
                        'm06_alloted_to' => $request->emp_id,
                        'm06_alloted_by' => $userId,
                        'tr05_status' => 'ALLOTED',
                        'tr05_alloted_at' => now()
                    ]);
                    $totalUpdated++;
                }
            }

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully allotted {$totalUpdated} tests from " . count($sampleIds) . " samples to the selected analyst");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Sample Allotment Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'emp_id' => $request->emp_id,
                'sample_count' => count(explode(',', $request->sample_ids)),
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot samples: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Bulk Transfer Samples
    public function bulkTransferSamples(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sample_ids' => 'required|string',
            'ro_id' => [
                'required',
                'exists:m04_ros,m04_ro_id',
                Rule::notIn([Session::get('ro_id')])
            ],
            'reason' => 'required|string|max:255',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $sampleIds = array_filter(explode(',', $request->sample_ids));
            if (empty($sampleIds)) {
                Session::flash('type', 'error');
                Session::flash('message', 'No samples selected');
                return redirect()->back();
            }

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');
            $now = now();
            $totalTransferred = 0;

            foreach ($sampleIds as $sampleId) {
                $tests = SampleTest::where('tr04_sample_registration_id', $sampleId)
                    ->where('m04_ro_id', $roId)
                    ->whereNotIn('tr05_status', ['COMPLETED', 'TRANSFERRED'])
                    ->get();

                $transferData = [];
                foreach ($tests as $test) {
                    $transferData[] = [
                        'tr05_sample_test_id' => $test->tr05_sample_test_id,
                        'm04_from_ro_id' => $test->m04_ro_id,
                        'm04_to_ro_id' => $request->ro_id,
                        'm06_transferred_by' => $userId,
                        'tr06_transferred_at' => $now,
                        'tr06_reason' => $request->reason,
                        'tr06_remark' => $request->remark,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $test->update([
                        'm04_transferred_to' => $request->ro_id,
                        'm06_alloted_to' => null,
                        'm04_transferred_by' => $roId,
                        'tr05_status' => 'TRANSFERRED',
                        'tr05_transferred_at' => $now,
                    ]);
                    $totalTransferred++;
                }

                if (!empty($transferData)) {
                    TestTransfer::insert($transferData);
                }
            }

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully transferred {$totalTransferred} tests from " . count($sampleIds) . " samples to the selected RO");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Sample Transfer Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'ro_id' => Session::get('ro_id'),
                'target_ro_id' => $request->ro_id,
                'sample_count' => count(explode(',', $request->sample_ids)),
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to transfer samples: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Quick Allot Single Sample
    public function quickAllotSample(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sample_id' => 'required|exists:tr04_sample_registrations,tr04_sample_registration_id',
            'emp_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');

            $tests = SampleTest::where('tr04_sample_registration_id', $request->sample_id)
                ->where(function ($query) use ($roId) {
                    $query->where('m04_ro_id', $roId)
                        ->orWhere('m04_transferred_to', $roId);
                })
                ->whereNull('m06_alloted_to')
                ->whereNotIn('tr05_status', ['COMPLETED', 'TRANSFERRED'])
                ->get();

            if ($tests->isEmpty()) {
                Session::flash('type', 'warning');
                Session::flash('message', 'No unallotted tests found in this sample');
                return redirect()->back();
            }

            $updatedCount = 0;
            foreach ($tests as $test) {
                $test->update([
                    'm06_alloted_to' => $request->emp_id,
                    'm06_alloted_by' => $userId,
                    'tr05_status' => 'ALLOTED',
                    'tr05_alloted_at' => now()
                ]);
                $updatedCount++;
            }

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully allotted {$updatedCount} tests to the selected analyst");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quick Allot Sample Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'sample_id' => $request->sample_id,
                'emp_id' => $request->emp_id,
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot sample: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Quick Transfer Single Sample
    public function quickTransferSample(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sample_id' => 'required|exists:tr04_sample_registrations,tr04_sample_registration_id',
            'ro_id' => [
                'required',
                'exists:m04_ros,m04_ro_id',
                Rule::notIn([Session::get('ro_id')])
            ],
            'reason' => 'required|string|max:255',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');
            $now = now();

            $tests = SampleTest::where('tr04_sample_registration_id', $request->sample_id)
                ->where('m04_ro_id', $roId)
                ->whereNotIn('tr05_status', ['COMPLETED', 'TRANSFERRED'])
                ->get();

            if ($tests->isEmpty()) {
                Session::flash('type', 'warning');
                Session::flash('message', 'No transferable tests found in this sample');
                return redirect()->back();
            }

            $transferData = [];
            foreach ($tests as $test) {
                $transferData[] = [
                    'tr05_sample_test_id' => $test->tr05_sample_test_id,
                    'm04_from_ro_id' => $test->m04_ro_id,
                    'm04_to_ro_id' => $request->ro_id,
                    'm06_transferred_by' => $userId,
                    'tr06_transferred_at' => $now,
                    'tr06_reason' => $request->reason,
                    'tr06_remark' => $request->remark,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $test->update([
                    'm04_transferred_to' => $request->ro_id,
                    'm06_alloted_to' => null,
                    'm04_transferred_by' => $roId,
                    'tr05_status' => 'TRANSFERRED',
                    'tr05_transferred_at' => $now,
                ]);
            }

            TestTransfer::insert($transferData);

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully transferred " . count($tests) . " tests to the selected RO");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quick Transfer Sample Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'sample_id' => $request->sample_id,
                'ro_id' => $request->ro_id,
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to transfer sample: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function createAllottment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txt_sample_registration_id' => 'required|exists:tr04_sample_registrations,tr04_sample_registration_id',
            'allotments' => 'required|array',
            'allotments.*' => 'nullable|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');
            $updatedCount = 0;

            foreach ($request->allotments as $testId => $empId) {
                if (!empty($empId)) {
                    $updated = SampleTest::where('tr05_sample_test_id', $testId)
                        ->where('tr04_sample_registration_id', $request->txt_sample_registration_id)
                        ->where(function ($query) use ($roId) {
                            $query->where('m04_ro_id', $roId)
                                ->orWhere('m04_transferred_to', $roId);
                        })
                        ->update([
                            'm06_alloted_to' => $empId,
                            'm06_alloted_by' => $userId,
                            'tr05_status' => 'ALLOTED',
                            'tr05_alloted_at' => now()
                        ]);

                    $updatedCount += $updated;
                }
            }
            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully alloted {$updatedCount} tests");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Allotment Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'registration_id' => $request->txt_sample_registration_id,
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot tests: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function allotTests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_ids' => 'required|string',
            'emp_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                Session::flash('type', 'error');
                Session::flash('message', 'No tests selected');
                return redirect()->back();
            }
            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');

            // Process in batches for large datasets
            $testIdChunks = array_chunk($testIds, self::BATCH_SIZE);
            $totalUpdated = 0;

            foreach ($testIdChunks as $chunk) {
                $updated = SampleTest::whereIn('tr05_sample_test_id', $chunk)
                    ->where(function ($query) use ($roId) {
                        $query->where('m04_ro_id', $roId)
                            ->orWhere('m04_transferred_to', $roId);
                    })
                    ->whereNull('m06_alloted_to')
                    ->update([
                        'm06_alloted_to' => $request->emp_id,
                        'm06_alloted_by' => $userId,
                        'tr05_status' => 'ALLOTED',
                        'tr05_alloted_at' => now()
                    ]);

                $totalUpdated += $updated;
            }

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully alloted {$totalUpdated} tests to the selected employee");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Allotment Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'emp_id' => $request->emp_id,
                'test_count' => count(explode(',', $request->test_ids)),
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot tests: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function transferTests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_ids' => 'required|string',
            'ro_id' => [
                'required',
                'exists:m04_ros,m04_ro_id',
                Rule::notIn([Session::get('ro_id')])
            ],
            'reason' => 'required|string|max:255',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                Session::flash('type', 'error');
                Session::flash('message', 'No tests selected');
                return redirect()->back();
            }

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');
            $now = now();

            // Get tests that belong to current RO and are transferable
            $tests = SampleTest::whereIn('tr05_sample_test_id', $testIds)
                ->where('m04_ro_id', $roId)
                ->whereNotIn('tr05_status', ['COMPLETED', 'TRANSFERRED'])
                ->get();

            if ($tests->isEmpty()) {
                Session::flash('type', 'error');
                Session::flash('message', 'No valid tests found for transfer');
                return redirect()->back();
            }

            $transferData = [];
            $testUpdates = [];

            foreach ($tests as $test) {
                $transferData[] = [
                    'tr05_sample_test_id' => $test->tr05_sample_test_id,
                    'm04_from_ro_id' => $test->m04_ro_id,
                    'm04_to_ro_id' => $request->ro_id,
                    'm06_transferred_by' => $userId,
                    'tr06_transferred_at' => $now,
                    'tr06_reason' => $request->reason,
                    'tr06_remark' => $request->remark,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $testUpdates[] = [
                    'tr05_sample_test_id' => $test->tr05_sample_test_id,
                    'm04_transferred_to' => $request->ro_id,
                    'm06_alloted_to' => null,
                    'm04_transferred_by' => $roId,
                    'tr05_status' => 'TRANSFERRED',
                    'tr05_transferred_at' => $now,
                ];
            }

            // Batch insert transfers
            TestTransfer::insert($transferData);

            // Batch update tests
            foreach ($testUpdates as $update) {
                SampleTest::where('tr05_sample_test_id', $update['tr05_sample_test_id'])
                    ->update(collect($update)->except('tr05_sample_test_id')->toArray());
            }

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully transferred " . count($tests) . " tests to the selected RO");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Transfer Tests failed", [
                'user_id' => Session::get('user_id'),
                'ro_id' => Session::get('ro_id'),
                'target_ro_id' => $request->ro_id,
                'test_count' => count(explode(',', $request->test_ids)),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to transfer tests: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function acceptTransferred(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tr05_sample_tests,tr05_sample_test_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Invalid test ID');
        }

        try {
            DB::beginTransaction();

            $roId = Session::get('ro_id');
            $userId = Session::get('user_id');

            $test = SampleTest::where('tr05_sample_test_id', $request->test_id)
                ->where('m04_transferred_to', $roId)
                ->where('tr05_status', 'TRANSFERRED')
                ->firstOrFail();

            // Update transfer record to mark as received
            TestTransfer::where('tr05_sample_test_id', $request->test_id)
                ->where('m04_to_ro_id', $roId)
                ->whereNull('m06_received_by')
                ->update([
                    'm06_received_by' => $userId,
                    'tr06_received_at' => now(),
                ]);

            $test->update([
                'm04_ro_id' => $roId,
                'm04_transferred_to' => null,
                'm06_alloted_to' => null,
                'm04_transferred_by' => null,
                'tr05_status' => 'PENDING',
                'tr05_alloted_at' => null,
                'tr05_transferred_at' => null,
                'updated_at' => now()
            ]);

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', 'Test accepted successfully and added to your pending tests');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Accept Transfer Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'ro_id' => Session::get('ro_id'),
                'test_id' => $request->test_id,
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to accept test: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function reassignTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tr05_sample_tests,tr05_sample_test_id',
            'from_user_id' => 'required|exists:m06_employees,m06_employee_id',
            'to_user_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation failed: ' . $validator->errors()->first());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $roId = Session::get('ro_id');
            $test = SampleTest::where('tr05_sample_test_id', $request->test_id)
                ->where(function ($query) use ($roId) {
                    $query->where('m04_ro_id', $roId)->orWhere('m04_transferred_to', $roId);
                })
                ->firstOrFail();

            if ($test->m06_alloted_to != $request->from_user_id) {
                Session::flash('type', 'error');
                Session::flash('message', 'Test is not currently alloted to the specified user');
                return redirect()->back();
            }

            $test->update([
                'm06_alloted_to' => $request->to_user_id,
                'tr05_status' => 'ALLOTED',
                'tr05_reassigned_at' => now()
            ]);

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', 'Test reassigned successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reassign Test Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'test_id' => $request->test_id,
                'from_user_id' => $request->from_user_id,
                'to_user_id' => $request->to_user_id,
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to reassign test: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function getAllotmentHistory($testId)
    {
        $roId = Session::get('ro_id');

        $test = SampleTest::where('tr05_sample_test_id', $testId)
            ->where(function ($query) use ($roId) {
                $query->where('m04_ro_id', $roId)
                    ->orWhere('m04_transferred_to', $roId);
            })
            ->with(['test', 'registration'])
            ->firstOrFail();

        $history = TestTransfer::where('tr05_sample_test_id', $testId)
            ->with([
                'fromRo:m04_ro_id,m04_name',
                'toRo:m04_ro_id,m04_name',
                'transferredBy:m06_employee_id,m06_name',
                'receivedBy:m06_employee_id,m06_name'
            ])
            ->orderBy('tr06_transferred_at', 'desc')
            ->get();

        $allotmentHistory = collect();
        if ($test->m06_alloted_to) {
            $allotmentHistory->push((object)[
                'type' => 'allotment',
                'employee_name' => $test->allotedTo->m06_name ?? 'Unknown',
                'alloted_at' => $test->tr05_alloted_at,
                'status' => $test->tr05_status
            ]);
        }
        return view('allottment.allotment_history', compact('test', 'history', 'allotmentHistory'));
    }

    // Search and Allot Previously Used but not required anymore

    // public function searchTestsForAllotment(Request $request)
    // {
    //     $roId = Session::get('ro_id');
    //     if ($request->has('search_test') && !empty($request->search_test)) {
    //         $searchTerm = $request->search_test;

    //         $testResults = SampleTest::join('tr04_sample_registrations', 'tr05_sample_tests.tr04_sample_registration_id', '=', 'tr04_sample_registrations.tr04_sample_registration_id')
    //             ->join('m12_tests', 'tr05_sample_tests.m12_test_id', '=', 'm12_tests.m12_test_id')
    //             ->select(
    //                 'tr05_sample_tests.tr05_sample_test_id',
    //                 'tr05_sample_tests.tr04_sample_registration_id',
    //                 'tr05_sample_tests.m06_alloted_to',
    //                 'tr05_sample_tests.tr05_status',
    //                 'tr04_sample_registrations.tr04_reference_id',
    //                 'm12_tests.m12_name as test_name',
    //                 'm12_tests.m12_test_id'
    //             )
    //             ->where(function ($query) use ($roId) {
    //                 $query->where('tr05_sample_tests.m04_ro_id', $roId)
    //                     ->orWhere('tr05_sample_tests.m04_transferred_to', $roId);
    //             })
    //             ->where('m12_tests.m12_name', 'LIKE', '%' . $searchTerm . '%')
    //             ->whereNull('tr05_sample_tests.m06_alloted_to')
    //             ->whereNotIn('tr05_sample_tests.tr05_status', ['COMPLETED', 'TRANSFERRED'])
    //             ->with(['registration:tr04_sample_registration_id,tr04_reference_id,created_at'])
    //             ->orderBy('tr04_sample_registrations.created_at', 'desc')
    //             ->get()
    //             ->groupBy('m12_test_id');

    //         $employees = $this->getLabEmployees($roId);

    //         return response()->json([
    //             'success' => true,
    //             'test_results' => $testResults,
    //             'search_term' => $searchTerm,
    //             'employees' => $employees
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Please enter a test name to search'
    //     ]);
    // }

    public function allotSpecificTests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_name' => 'required|string',
            'test_ids' => 'required|string',
            'emp_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();
            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tests selected'
                ]);
            }

            $userId = Session::get('user_id');
            $roId = Session::get('ro_id');

            // Verify all tests belong to current RO and are unallotted
            $validTests = SampleTest::whereIn('tr05_sample_test_id', $testIds)
                ->where(function ($query) use ($roId) {
                    $query->where('m04_ro_id', $roId)
                        ->orWhere('m04_transferred_to', $roId);
                })
                ->whereNull('m06_alloted_to')
                ->whereNotIn('tr05_status', ['COMPLETED', 'TRANSFERRED'])
                ->get();

            if ($validTests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid tests found for allotment'
                ]);
            }

            $updatedCount = 0;
            foreach ($validTests as $test) {
                $test->update([
                    'm06_alloted_to' => $request->emp_id,
                    'm06_alloted_by' => $userId,
                    'tr05_status' => 'ALLOTED',
                    'tr05_alloted_at' => now()
                ]);
                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully allotted {$updatedCount} tests of '{$request->test_name}' to the selected analyst",
                'allotted_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Specific Test Allotment Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'emp_id' => $request->emp_id,
                'test_ids' => $request->test_ids,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to allot tests: ' . $e->getMessage()
            ]);
        }
    }
}
