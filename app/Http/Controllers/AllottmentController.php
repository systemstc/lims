<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Ro;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\TestTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        $stats = $this->calculateLabManagerStats($roId);
        $employees = $this->getLabEmployees($roId);
        $ros = Ro::select('m04_ro_id', 'm04_name')->where('m04_ro_id', '!=', $roId)->orderBy('m04_name')->get();

        return view('allottment.allotment_dashboard', compact(
            'pendingRegistrations',
            'stats',
            'employees',
            'ros'
        ));
    }

    private function buildPendingQuery($roId)
    {
        return SampleTest::join(
            'tr04_sample_registrations as sr',
            'sr.tr04_sample_registration_id',
            '=',
            'tr05_sample_tests.tr04_sample_registration_id'
        )
            ->selectRaw('
            tr05_sample_tests.tr04_sample_registration_id,
            COUNT(*) as total_tests,
            COUNT(CASE WHEN tr05_sample_tests.m06_alloted_to IS NOT NULL THEN 1 END) as allotted_tests,
            COUNT(CASE WHEN tr05_sample_tests.m06_alloted_to IS NULL THEN 1 END) as pending_tests,
            COUNT(CASE WHEN tr05_sample_tests.tr05_status = "TRANSFERRED" AND tr05_sample_tests.m04_transferred_to = ? THEN 1 END) as received_tests,
            COUNT(CASE WHEN tr05_sample_tests.tr05_status = "TRANSFERRED" AND tr05_sample_tests.m04_ro_id = ? THEN 1 END) as transferred_tests
        ', [$roId, $roId])
            ->addSelect([
                'sr.tr04_sample_type',
                'sr.tr04_progress',
                'sr.tr04_status',
                'sr.created_at'
            ])
            ->where(function ($query) use ($roId) {
                $query->where('tr05_sample_tests.m04_ro_id', $roId)
                    ->orWhere('tr05_sample_tests.m04_transferred_to', $roId);
            })
            ->groupBy([
                'tr05_sample_tests.tr04_sample_registration_id',
                'sr.tr04_sample_type',
                'sr.tr04_progress',
                'sr.tr04_status',
                'sr.created_at'
            ])
            ->havingRaw('allotted_tests < total_tests OR received_tests > 0');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('priority')) {
            $query->where('sr.tr04_sample_type', $request->priority);
        }

        if ($request->filled('days_pending')) {
            $days = (int) $request->days_pending;
            $query->where('sr.created_at', '<=', now()->subDays($days));
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'NEW' => $query->having('allotted_tests', '=', 0),
                'PERTIAL' => $query->havingRaw('allotted_tests > 0 AND allotted_tests < total_tests'),
                'URGENT' => $query->where(function ($q) {
                    $q->where('sr.tr04_sample_type', 'Urgent')
                        ->orWhere('sr.created_at', '<=', now()->subDays(3));
                }),
                default => null
            };
        }
    }

    private function applySorting($query)
    {
        $query->orderByRaw("
            CASE 
                WHEN sr.tr04_sample_type = 'Urgent' THEN 1
                WHEN sr.tr04_sample_type = 'Normal' THEN 2
                ELSE 3
            END
        ")->orderBy('sr.created_at', 'asc');
    }

    private function calculateLabManagerStats($roId)
    {
        $baseQuery = SampleTest::where(function ($query) use ($roId) {
            $query->where('m04_ro_id', $roId)
                ->orWhere('m04_transferred_to', $roId);
        });

        return [
            'new_samples' => (clone $baseQuery)->whereNull('m06_alloted_to')
                ->where('tr05_status', 'PENDING')->count(),
            'pending_tests' => (clone $baseQuery)->whereNull('m06_alloted_to')
                ->whereNotIn('tr05_status', ['TRANSFERRED', 'COMPLETED'])->count(),
            'partial_allotted' => SampleRegistration::withCount([
                'sampleTests as total_tests' => function ($query) use ($roId) {
                    $query->where(function ($q) use ($roId) {
                        $q->where('m04_ro_id', $roId)->orWhere('m04_transferred_to', $roId);
                    });
                },
                'sampleTests as allotted_tests' => function ($query) use ($roId) {
                    $query->where(function ($q) use ($roId) {
                        $q->where('m04_ro_id', $roId)->orWhere('m04_transferred_to', $roId);
                    })->whereNotNull('m06_alloted_to');
                }
            ])->having('total_tests', '>', 0)
                ->havingRaw('allotted_tests > 0 AND allotted_tests < total_tests')->count(),
            'ready_for_testing' => (clone $baseQuery)->where('tr05_status', 'ALLOTED')
                ->whereNotNull('m06_alloted_to')->count()
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

        $registration = SampleRegistration::findOrFail($registrationId);

        // Fixed query to prevent duplicate tests from appearing
        $tests = SampleTest::where('tr04_sample_registration_id', $registrationId)
            ->where(function ($q) use ($roId) {
                $q->where('m04_ro_id', $roId)
                    ->orWhere(function ($subQ) use ($roId) {
                        // Only show transferred tests that are pending acceptance
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

    public function createAllottment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txt_sample_registration_id' => 'required|exists:tr04_sample_registrations,tr04_sample_registration_id',
            'allotments' => 'required|array',
            'allotments.*' => 'nullable|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Validation failed: ' . $validator->errors()->first());
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

            return redirect()->back()
                ->with('type', 'success')
                ->with('message', "Successfully alloted {$updatedCount} tests");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Allotment Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'registration_id' => $request->txt_sample_registration_id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Failed to allot tests: ' . $e->getMessage());
        }
    }

    public function allotTests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_ids' => 'required|string',
            'emp_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Validation failed: ' . $validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                return redirect()->back()->with('type', 'error')->with('message', 'No tests selected');
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

            return redirect()->back()
                ->with('type', 'success')
                ->with('message', "Successfully alloted {$totalUpdated} tests to the selected employee");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Allotment Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'emp_id' => $request->emp_id,
                'test_count' => count(explode(',', $request->test_ids)),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Failed to allot tests: ' . $e->getMessage());
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('type', 'error')
                ->with('message', 'Validation failed: ' . $validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                return redirect()->back()->with('type', 'error')->with('message', 'No tests selected');
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
                return redirect()->back()
                    ->with('type', 'error')
                    ->with('message', 'No valid tests found for transfer');
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

            return redirect()->back()
                ->with('type', 'success')
                ->with('message', "Successfully transferred " . count($tests) . " tests to the selected RO");
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

            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Failed to transfer tests: ' . $e->getMessage());
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

            return redirect()->back()
                ->with('type', 'success')
                ->with('message', 'Test accepted successfully and added to your pending tests');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Accept Transfer Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'ro_id' => Session::get('ro_id'),
                'test_id' => $request->test_id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Failed to accept test: ' . $e->getMessage());
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
            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Validation failed: ' . $validator->errors()->first());
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
                return redirect()->back()
                    ->with('type', 'error')
                    ->with('message', 'Test is not currently alloted to the specified user');
            }

            $test->update([
                'm06_alloted_to' => $request->to_user_id,
                'tr05_status' => 'ALLOTED',
                'tr05_reassigned_at' => now()
            ]);

            DB::commit();

            return redirect()->back()
                ->with('type', 'success')
                ->with('message', 'Test reassigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reassign Test Error: ' . $e->getMessage(), [
                'user_id' => Session::get('user_id'),
                'test_id' => $request->test_id,
                'from_user_id' => $request->from_user_id,
                'to_user_id' => $request->to_user_id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Failed to reassign test: ' . $e->getMessage());
        }
    }

    public function getAllotmentHistory($testId)
    {
        $roId = Session::get('ro_id');

        // Get the test to ensure user has access
        $test = SampleTest::where('tr05_sample_test_id', $testId)
            ->where(function ($query) use ($roId) {
                $query->where('m04_ro_id', $roId)
                    ->orWhere('m04_transferred_to', $roId);
            })
            ->with(['test', 'registration'])
            ->firstOrFail();

        // Get transfer history
        $history = TestTransfer::where('tr05_sample_test_id', $testId)
            ->with([
                'fromRo:m04_ro_id,m04_name',
                'toRo:m04_ro_id,m04_name',
                'transferredBy:m06_employee_id,m06_name',
                'receivedBy:m06_employee_id,m06_name'
            ])
            ->orderBy('tr06_transferred_at', 'desc')
            ->get();

        // Get allotment history from the test record itself
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
}
