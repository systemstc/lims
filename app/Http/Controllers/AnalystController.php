<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\TestReport;
use App\Models\TestResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AnalystController extends Controller
{
    public function viewAnalystDashboard()
    {
        $userId = Session::get('user_id');

        // Pending tests count
        $pendingTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'ALLOTED')
            ->distinct('tr04_sample_registration_id')
            ->count();

        $rejectedTests = SampleTest::where('m06_alloted_to', $userId)
            ->whereHas('registration.testResult', function ($query) {
                $query->where('tr07_result_status', 'REJECTED');
            })
            ->distinct('tr04_sample_registration_id')
            ->count('tr04_sample_registration_id');
        // In progress tests count
        $inProgressTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'IN_PROGRESS')
            ->count();

        //  Completed today count
        $completedTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'COMPLETED')
            ->whereDate('tr05_completed_at', Carbon::today())
            ->count();

        // Total samples count
        $totalSamples = SampleTest::where('m06_alloted_to', $userId)
            ->distinct('tr04_sample_registration_id')
            ->count();

        //  Fetch recent allotted samples (grouped)
        $allottedSamples = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', '!=', 'REPORTED')
            ->with(['registration'])
            ->select(
                'tr04_sample_registration_id',
                DB::raw('COUNT(*) as test_count'),
                DB::raw('MAX(tr05_alloted_at) as latest_allotment'),
                DB::raw('GROUP_CONCAT(tr05_sample_test_id) as test_ids'),
                DB::raw('MAX(tr05_completed_at) as latest_completed_at'),
                DB::raw('CASE
                    WHEN SUM(CASE WHEN tr05_status = "IN_PROGRESS" THEN 1 ELSE 0 END) > 0
                        THEN "IN_PROGRESS"
                    WHEN SUM(CASE WHEN tr05_status = "ALLOTED" THEN 1 ELSE 0 END) > 0
                        THEN "ALLOTED"
                    ELSE "COMPLETED"
                END as overall_status
               '),
                // Progress counts
                DB::raw('SUM(CASE WHEN tr05_status = "COMPLETED" THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('SUM(CASE WHEN tr05_status = "IN_PROGRESS" THEN 1 ELSE 0 END) as in_progress_count'),
                DB::raw('SUM(CASE WHEN tr05_status = "ALLOTED" THEN 1 ELSE 0 END) as pending_count')
            )
            ->groupBy('tr04_sample_registration_id')
            ->orderByDesc('latest_allotment')
            ->get()
            ->filter(function ($sample) {
                if ($sample->overall_status === 'COMPLETED') {
                    return $sample->latest_completed_at &&
                        \Carbon\Carbon::parse($sample->latest_completed_at)->isToday();
                }
                return true;
            })
            ->take(10);

        $inProgressWeight = 0.4;
        $allottedSamples->each(function ($sample) use ($inProgressWeight) {
            if ($sample->test_count > 0) {
                $weightedProgress = ($sample->completed_count) + ($sample->in_progress_count * $inProgressWeight);
                $sample->progress_percentage = round(($weightedProgress / $sample->test_count) * 100);
            } else {
                $sample->progress_percentage = 0;
            }
        });
        return view('analyst.analyst_dashboard', compact(
            'pendingTests',
            'rejectedTests',
            'inProgressTests',
            'completedTests',
            'totalSamples',
            'allottedSamples'
        ));
    }
    public function rejectedSamples(Request $request)
    {
        $userId = Session::get('user_id');

        $rejectedSamples = SampleTest::query()
            ->where('m06_alloted_to', $userId)
            ->whereHas('registration.testResult', function ($query) {
                $query->where('tr07_result_status', 'REJECTED');
            })
            ->with(['registration.testResult' => function ($query) {
                $query->orderByDesc('tr07_created_at');
            }])
            ->select(
                'tr04_sample_registration_id',
                DB::raw('COUNT(*) as total_tests'),
                DB::raw('MAX(tr05_alloted_at) as latest_allotment'),
                DB::raw('MAX(tr05_completed_at) as latest_completed_at'),
                DB::raw('GROUP_CONCAT(tr05_sample_test_id) as test_ids')
            )
            ->groupBy('tr04_sample_registration_id')
            ->orderByDesc('latest_allotment')
            ->get();

        // Calculate progress and status
        foreach ($rejectedSamples as $sample) {
            $results = $sample->registration->testResult;
            $rejectedCount = $results->where('tr07_result_status', 'REJECTED')
                ->where('tr07_is_current', 'YES')->count();
            $revisedCount = $results->where('tr07_result_status', 'REVISED')
                ->where('tr07_is_current', 'YES')->count();

            // Calculate progress percentage
            $totalToRevise = $rejectedCount + $revisedCount;
            $progress = ($totalToRevise > 0)
                ? round(($revisedCount / $totalToRevise) * 100)
                : 0;

            $sample->progress_percentage = $progress;

            // Determine final overall status
            if ($rejectedCount > 0 && $revisedCount == 0) {
                $sample->overall_status = 'REJECTED';
            } elseif ($revisedCount == $totalToRevise && $totalToRevise > 0) {
                $sample->overall_status = 'REVISED';
            } else {
                $sample->overall_status = 'UNKNOWN';
            }
        }

        return view('analyst.rejected_sample', compact('rejectedSamples'));
    }

    public function reviseTest(Request $request, $refId)
    {
        // GET: Show all rejected results for this sample reference
        if ($request->isMethod('get')) {
            $sample = SampleRegistration::with([
                'testResult.test.standard',
                'testResult.manuscript'
            ])
                ->where('tr04_reference_id', $refId)
                ->firstOrFail();

            // Group by test number for parent-child display
            $groupedResults = $sample->testResult
                ->where('tr07_result_status', 'REJECTED')
                ->where('tr07_is_current', 'YES')
                ->groupBy('m12_test_number');

            if ($groupedResults->isEmpty()) {
                Session::flash('type', 'success');
                Session::flash('message', 'No rejected tests found for this sample.');
                return to_route('rejected_samples');
            }

            return view('analyst.revise_tests', compact('sample', 'groupedResults'));
        }

        // POST: Save all revisions
        if ($request->isMethod('post')) {
            $oldResults = TestResult::where('tr04_reference_id', $refId)
                ->where('tr07_result_status', 'REJECTED')
                ->where('tr07_is_current', 'YES')
                ->get();

            foreach ($oldResults as $old) {
                $old->update(['tr07_is_current' => 'NO']);

                $new = new TestResult();
                $new->fill([
                    'm04_ro_id' => $old->m04_ro_id,
                    'tr04_reference_id' => $old->tr04_reference_id,
                    'm12_test_number' => $old->m12_test_number,
                    'm22_manuscript_id' => $old->m22_manuscript_id,
                    'tr07_result' => $request->input("txt_result_{$old->tr07_test_result_id}"),
                    'tr07_current_version' => $old->tr07_current_version + 1,
                    'tr07_is_current' => 'YES',
                    'tr07_result_status' => 'REVISED',
                    'tr07_test_date' => $request->txt_test_date,
                    'tr07_performance_date' => $request->txt_performance_date,
                    'tr07_remarks' => $request->input("txt_remarks_{$old->tr07_test_result_id}"),
                    'm06_created_by' => Session::get('user_id'),
                    'tr07_created_at' => now(),
                    'tr07_status' => 'ACTIVE',
                ]);
                $new->save();
            }
            Session::flash('type', 'success');
            Session::flash('message', 'Revised results saved successfully!');
            return to_route('rejected_samples');
        }
    }



    public function viewSampleTests($sampleId)
    {
        $userId = Session::get('user_id');
        $sample = SampleRegistration::with([
            'labSample',
            'package',
            'sampleTests' => function ($query) use ($userId) {
                $query->where('m06_alloted_to', $userId)
                    ->with(['test', 'standard']);
            },
        ])->where('tr04_sample_registration_id', $sampleId)
            ->firstOrFail();
        $sample->sampleTests->each(function ($test) {
            $test->append(['primary_tests', 'secondary_tests']);
        });
        return view('analyst.view_test_details', compact('sample'));
    }

    public function updateStatus($id)
    {
        $sampleTest = SampleTest::where('tr05_sample_test_id', $id)->firstOrFail();
        if ($sampleTest->tr05_status === 'ALLOTED') {
            $updated = $sampleTest->update([
                'tr05_status' => 'IN_PROGRESS'
            ]);
        } elseif ($sampleTest->tr05_status === 'IN_PROGRESS') {
            $updated = $sampleTest->update([
                'tr05_status' => 'COMPLETED',
                'tr05_completed_at' => now(),
            ]);
        }
        if ($updated) {
            Session::flash('type', 'success');
            Session::flash('message', 'Test Marked as Completed');
            return redirect()->back();
        }
        Session::flash('type', 'warning');
        Session::flash('message', 'Something wents wrong');
        return redirect()->back();
    }
    public function bulkUpdateStatus($refId)
    {
        $sample = SampleRegistration::where('tr04_reference_id', $refId)->first();
        $userId = Session::get('user_id');

        // Get all tests for the same reference and current user
        $tests = SampleTest::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
            ->where('m06_alloted_to', $userId)
            ->get();

        if ($tests->isEmpty()) {
            Session::flash('type', 'warning');
            Session::flash('message', 'No tests found for this reference.');
            return redirect()->back();
        }

        $updated = false;

        foreach ($tests as $test) {
            if ($test->tr05_status === 'ALLOTED') {
                $test->update(['tr05_status' => 'IN_PROGRESS']);
                $updated = true;
            } elseif ($test->tr05_status === 'IN_PROGRESS') {
                $test->update([
                    'tr05_status' => 'COMPLETED',
                    'tr05_completed_at' => now(),
                ]);
                $updated = true;
            }
        }

        if ($updated) {
            Session::flash('type', 'success');
            Session::flash('message', 'Tests updated successfully.');
        } else {
            Session::flash('type', 'info');
            Session::flash('message', 'No status changes were required.');
        }

        return redirect()->back();
    }
}
