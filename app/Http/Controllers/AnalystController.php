<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AnalystController extends Controller
{
    public function viewAnalystDashboard()
    {
        $userId = Session::get('user_id');

        $pendingTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'ALLOTED')
            ->count();
        $inProgressTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'IN_PROGRESS')
            ->count();
        $completedTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'COMPLETED')
            ->whereDate('tr05_completed_at', Carbon::today())
            ->count();

        $totalSamples = SampleTest::where('m06_alloted_to', $userId)
            ->count();

        $allottedTests = SampleTest::where('m06_alloted_to', $userId)->whereNot('tr05_status', 'COMPLETED')
            ->with('registration')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('analyst.analyst_dashboard', compact(
            'pendingTests',
            'inProgressTests',
            'completedTests',
            'totalSamples',
            'allottedTests'
        ));
    }


    public function viewTest($id)
    {
        $sampleTest = SampleTest::where('tr05_sample_test_id', $id)->firstOrFail();
        $sampleTest->update([
            'tr05_status' => $sampleTest->tr05_status === 'ALLOTED' ? 'IN_PROGRESS' : $sampleTest->tr05_status
        ]);

        $sample = SampleRegistration::with([
            'labSample',
            'package',
            'sampleTests' => function ($query) use ($id) {
                $query->where('tr05_sample_test_id', $id)
                    ->with(['test', 'standard']);
            },
        ])->where('tr04_sample_registration_id', $sampleTest->tr04_sample_registration_id)
            ->firstOrFail();

        // Append primary/secondary tests only for that test
        $sample->sampleTests->each(function ($test) {
            $test->append(['primary_tests', 'secondary_tests']);
        });

        return view('analyst.view_test_details', compact('sample'));
    }

    public function updateStatus($id)
    {
        $sampleTest = SampleTest::where('tr05_sample_test_id', $id)->firstOrFail();
        $data = [
            'tr05_status' => $sampleTest->tr05_status === 'IN_PROGRESS' ? 'COMPLETED' : $sampleTest->tr05_status,
            'tr05_completed_at' => now(),
        ];
        $update = $sampleTest->update($data);
        if ($update) {
            Session::flash('type', 'success');
            Session::flash('message', 'Test Marked as Completed');
            return to_route('view_analyst_dashboard');
        }
    }
}
