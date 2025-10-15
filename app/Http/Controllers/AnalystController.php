<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
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
            ->count();

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
        $totalSamples = SampleTest::where('m06_alloted_to', $userId)->count();

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
            'inProgressTests',
            'completedTests',
            'totalSamples',
            'allottedSamples'
        ));
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
}
