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

class AllottmentController extends Controller
{

    public function pendingAllotments(Request $request)
    {
        $roId = Session::get('ro_id');

        $query = SampleTest::join(
            'tr04_sample_registrations as sr',
            'sr.tr04_sample_registration_id',
            '=',
            'tr05_sample_tests.tr04_sample_registration_id'
        )
            ->selectRaw('
                tr05_sample_tests.tr04_sample_registration_id,
                COUNT(*) as total_tests,
                SUM(CASE WHEN tr05_sample_tests.m06_alloted_to IS NOT NULL THEN 1 ELSE 0 END) as allotted_tests,
                SUM(CASE WHEN tr05_sample_tests.m06_alloted_to IS NULL THEN 1 ELSE 0 END) as pending_tests,
                SUM(CASE WHEN tr05_sample_tests.tr05_status = "TRANSFERRED" AND tr05_sample_tests.m04_transferred_to = ' . $roId . ' THEN 1 ELSE 0 END) as received_tests,
                SUM(CASE WHEN tr05_sample_tests.tr05_status = "TRANSFERRED" AND tr05_sample_tests.m04_ro_id = ' . $roId . ' THEN 1 ELSE 0 END) as transferred_tests
            ')
            ->addSelect([
                'sr.tr04_sample_type',
                'sr.tr04_progress',
                'sr.tr04_status',
                'sr.created_at'
            ])
            ->groupBy(
                'tr05_sample_tests.tr04_sample_registration_id',
                'sr.tr04_sample_type',
                'sr.tr04_progress',
                'sr.tr04_status',
                'sr.created_at'
            )
            ->havingRaw('allotted_tests < total_tests');



        // ---- Filters ----
        if ($request->filled('priority')) {
            $query->where('sr.priority', $request->priority);
        }

        if ($request->filled('days_pending')) {
            $days = (int) $request->days_pending;
            $query->where('sr.created_at', '<=', now()->subDays($days));
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'new') {
                $query->having('allotted_tests', '=', 0);
            } elseif ($status === 'partial') {
                $query->havingRaw('allotted_tests > 0 AND allotted_tests < total_tests');
            } elseif ($status === 'urgent') {
                $query->where(function ($q) {
                    $q->where('sr.tr04_sample_type', 'Urgent')
                        ->orWhere('sr.created_at', '<=', now()->subDays(3));
                });
            }
        }

        // ---- Ordering ----
        $query->orderByRaw("
        CASE 
            WHEN sr.tr04_sample_type = 'Urgent' THEN 1
            WHEN sr.tr04_sample_type = 'Normal' THEN 2
            ELSE 3
        END
    ")->orderBy('sr.created_at', 'asc');

        // ---- Get data ----
        $pendingRegistrations = $query->paginate(20)->appends($request->query());
        // dd($pendingRegistrations);
        $stats = $this->calculateLabManagerStats();
        $employees = $this->getLabEmployees();
        $ros = Ro::select('m04_ro_id', 'm04_name')->orderBy('m04_name')->get();

        return view('allottment.allotment_dashboard', compact(
            'pendingRegistrations',
            'stats',
            'employees',
            'ros'
        ));
    }



    /**
     * Calculate statistics for Lab Manager dashboard
     */
    private function calculateLabManagerStats()
    {
        $newSamples = SampleRegistration::withCount('sampleTests')
            ->having('sample_tests_count', '>', 0)
            ->where('tr04_status', 'RECEIVED')
            ->count();

        $pendingTests = SampleTest::whereNull('m06_alloted_to')
            ->count();

        $partialAllotted = SampleRegistration::withCount([
            'sampleTests as total_tests',
            'sampleTests as allotted_tests' => function ($query) {
                $query->whereNotNull('m06_alloted_to');
            }
        ])
            ->having('total_tests', '>', 0)
            ->havingRaw('allotted_tests > 0 AND allotted_tests < total_tests')
            ->count();

        $readyForTesting = SampleTest::where('tr05_status', 'ALLOTTED')
            ->whereNotNull('m06_alloted_to')
            ->count();

        return [
            'new_samples' => $newSamples,
            'pending_tests' => $pendingTests,
            'partial_allotted' => $partialAllotted,
            'ready_for_testing' => $readyForTesting
        ];
    }

    /**
     * Get lab employees for allotment
     */
    private function getLabEmployees()
    {
        return Employee::join('m03_roles', 'm06_employees.m03_role_id', '=', 'm03_roles.m03_role_id')
            ->whereIn('m03_roles.m03_name', ['Analyst'])
            ->select('m06_employees.m06_employee_id', 'm06_employees.m06_name', 'm03_roles.m03_name as role')
            ->orderBy('m06_employees.m06_name')
            ->get();
    }

    // Allotment view
    public function viewAllottment($registrationId)
    {
        $registration = SampleRegistration::findOrFail($registrationId);

        $tests = SampleTest::where('tr04_sample_registration_id', $registrationId)
            ->where(function ($q) {
                $q->where('m04_ro_id', Session::get('ro_id'))
                    ->orWhere('m04_transferred_to', Session::get('ro_id'));
            })
            ->with(['test', 'standard', 'allotedTo'])
            ->get();

        $tests->each->append(['primary_tests', 'secondary_tests']);

        $employees = Employee::where('m04_ro_id', Session::get('ro_id'))->where('m03_role_id', 4)->get();
        $ros = Ro::all();

        return view('allottment.allottment', compact('registration', 'tests', 'employees', 'ros'));
    }


    /**
     * Handle individual allotments (original functionality)
     */
    public function createAllottment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txt_sample_registration_id' => 'required|exists:tr04_sample_registrations,tr04_sample_registration_id',
            'allotments' => 'required|array'
        ]);
        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot' . $validator->errors());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            foreach ($request->allotments as $testId => $empId) {
                if (!empty($empId)) {

                    $create = SampleTest::where('tr05_sample_test_id', $testId)
                        ->where('tr04_sample_registration_id', $request->txt_sample_registration_id)
                        ->update([
                            'm06_alloted_to' => $empId,
                            'tr05_status' => 'ALLOTED',
                            'tr05_alloted_at' => now()
                        ]);
                }
            }

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('mesage', 'Tests allotted successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Allotment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot tests: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Handle bulk allotment of selected tests to one user
     */
    public function allotTests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_ids' => 'required|string',
            'emp_id' => 'required|exists:m06_employees,m06_employee_id'
        ]);
        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', 'Validation ERROR' . $validator->errors());
            return redirect()->back();
        }

        try {
            DB::beginTransaction();
            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                return back()->with('error', 'No tests selected');
            }
            $updated = SampleTest::whereIn('tr05_sample_test_id', $testIds)
                ->update([
                    'm06_alloted_to' => $request->emp_id,
                    'tr05_status' => 'ALLOTED',
                    'tr05_alloted_at' => now()
                ]);

            DB::commit();
            Session::flash('type', 'success');
            Session::flash('message', "Successfully allotted {$updated} tests to the selected employee");
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to allot tests: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Handle transfer of tests to another RO
     */


    public function transferTests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_ids' => 'required|string',
            'ro_id'    => 'required|exists:m04_ros,m04_ro_id',
            'reason'   => 'nullable|string|max:255',
            'remark'   => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error("Validation failed while transferring tests", [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $testIds = array_filter(explode(',', $request->test_ids));
            if (empty($testIds)) {
                return back()->with('error', 'No tests selected');
            }

            $empId = Session::get('user_id');
            $now   = now();

            $tests = SampleTest::whereIn('tr05_sample_test_id', $testIds)->get();

            foreach ($tests as $test) {
                try {
                    $create = TestTransfer::create([
                        'tr05_sample_test_id' => $test->tr05_sample_test_id,
                        'm04_from_ro_id'      => $test->m04_ro_id,
                        'm04_to_ro_id'        => $request->ro_id,
                        'm06_transferred_by'  => $empId,
                        'tr06_transferred_at' => $now,
                        'tr06_reason'         => $request->reason,
                        'tr06_remark'         => $request->remark,
                    ]);

                    Log::info("Test transfer created", [
                        'transfer_id' => $create->tr06_test_transfer_id,
                        'test_id'     => $test->tr05_sample_test_id
                    ]);

                    $test->update([
                        'm04_transferred_to'  => $request->ro_id,
                        'm06_alloted_to'      => null,
                        'tr05_status'         => 'TRANSFERRED',
                        'tr05_transferred_at' => $now,
                    ]);
                } catch (\Exception $innerEx) {
                    Log::error("Failed to create transfer for test {$test->tr05_sample_test_id}", [
                        'exception' => $innerEx->getMessage(),
                        'trace'     => $innerEx->getTraceAsString()
                    ]);
                    throw $innerEx; // rethrow so transaction rolls back
                }
            }

            DB::commit();

            return back()->with('success', "Successfully transferred " . count($tests) . " tests to the selected RO");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("TransferTests failed", [
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to transfer tests: ' . $e->getMessage());
        }
    }



    /**
     * Get allotment history for a test
     */
    public function getAllotmentHistory($testId)
    {
        $history = SampleTestHistory::where('tr05_sample_test_id', $testId)
            ->with(['user', 'ro'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    /**
     * Reassign test from one user to another
     */
    public function reassignTest(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tr05_sample_tests,tr05_sample_test_id',
            'from_user_id' => 'required|exists:users,id',
            'to_user_id' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $test = SampleTest::findOrFail($request->test_id);

            if ($test->m06_alloted_to != $request->from_user_id) {
                return back()->with('error', 'Test is not currently allotted to the specified user');
            }

            $test->update([
                'm06_alloted_to' => $request->to_user_id,
                'tr05_status' => 'REASSIGNED',
                'tr05_reassigned_at' => now()
            ]);

            // Log the reassignment
            SampleTestHistory::create([
                'tr05_sample_test_id' => $request->test_id,
                'action' => 'REASSIGNED',
                'from_user_id' => $request->from_user_id,
                'to_user_id' => $request->to_user_id,
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);

            DB::commit();
            return back()->with('success', 'Test reassigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reassign test: ' . $e->getMessage());
        }
    }

    // Accept Test received from another ro
    public function acceptTransferred(Request $request)
    {
        $testId = $request->input('test_id');

        DB::transaction(function () use ($testId) {
            $test = SampleTest::where('tr05_sample_test_id', $testId)
                ->where('m04_transferred_to', Session::get('ro_id'))
                ->where('tr05_status', 'TRANSFERRED')
                ->firstOrFail();

            // 1️⃣ Update the old test as accepted
            $test->update([
                'tr05_status' => 'RECEIVED_ACCEPTED',
            ]);

            TestTransfer::where('tr05_sample_test_id', $testId)
                ->latest('tr06_test_transfer_id')
                ->update([
                    'm06_received_by' => Session::get('user_id'),
                    'tr06_received_at' => now(),
                ]);

            // 2️⃣ Create a new test for the current RO
            $newTest = $test->replicate(); // clone all attributes
            $newTest->tr05_sample_test_id = null; // let DB autoincrement
            $newTest->m04_ro_id = Session::get('ro_id'); // assign to current RO
            $newTest->m04_transferred_to = null; // reset transfer
            $newTest->tr05_status = 'PENDING'; // start fresh
            $newTest->tr05_alloted_at = null;
            $newTest->tr05_transferred_at = null;
            $newTest->created_at = now();
            $newTest->updated_at = now();
            $newTest->save();
        });

        return back()->with('success', 'Sample accepted successfully.');
    }
}
