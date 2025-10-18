<?php

namespace App\Http\Controllers;

use App\Models\SampleRegistration;
use App\Models\SampleTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerificationController extends Controller
{
    public function viewVerification()
    {
        $samples = SampleTest::with(['registration', 'test', 'registration.testResult'])
            ->whereNotIn('tr05_status', ['TRANSFERRED'])
            ->whereHas('registration.testResult', function ($q) {
                $q->where('tr07_result_status', 'RESULTED');
            })
            ->select('tr04_sample_registration_id')
            ->selectRaw("
                COUNT(*) as total_tests,
                SUM(CASE WHEN tr05_status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_tests,
                SUM(CASE WHEN tr05_status != 'COMPLETED' THEN 1 ELSE 0 END) as pending_tests
            ")
            ->groupBy('tr04_sample_registration_id')
            ->get()
            // Now filter only samples whose *all* tests have RESULTED status
            ->filter(function ($sample) {
                $results = optional($sample->registration)->testResult ?? collect();
                $allResulted = $results->count() > 0 && $results->every(fn($r) => $r->tr07_result_status === 'RESULTED');
                return $allResulted;
            })
            ->map(function ($sample) {
                $registration = $sample->registration;
                $results = optional($registration)->testResult ?? collect();

                $sample->reference_id = $registration?->tr04_reference_id ?? '-';
                $sample->sample_id = $registration?->tr04_sample_registration_id ?? '-';
                $sample->priority = $registration?->tr04_sample_type ?? 'NORMAL';
                $sample->created_at = $registration?->created_at;
                $sample->delay_days = $registration
                    ? round(abs(now()->floatDiffInDays($registration->created_at)), 2)
                    : null;
                $sample->allResulted = $results->count() > 0 && $results->every(fn($r) => $r->tr07_result_status === 'RESULTED');

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
        $sample = SampleRegistration::with([
            'testResult.test',
            'testResult.testManuscripts'
        ])->where('tr04_sample_registration_id', $sample_id)
            ->firstOrFail();

        if ($request->isMethod('POST')) {
            $request->validate([
                'action' => 'required|in:verify,reject',
                'remarks' => 'nullable|string|max:500',
            ]);

            // Group TestResults by parent test number to avoid duplicate parent rows
            $groupedResults = $sample->testResult->groupBy('m12_test_number');

            foreach ($groupedResults as $testNumber => $results) {
                foreach ($results as $result) {
                    $result->update([
                        'tr07_result_status' => $request->action === 'verify' ? 'VERIFIED' : 'REJECTED',
                        'm06_verified_by' => Session::get('user_id') ?? null,
                        'tr07_verified_at' => now(),
                        'tr07_remarks' => $request->remarks,
                    ]);
                }
            }

            Session::flash('type', 'success');
            Session::flash('message', 'Results ' . ucfirst($request->action) . ' successfully.');
            return to_route('view_result_verification');
        }

        // Prepare grouped results for view
        $groupedResults = $sample->testResult->groupBy('m12_test_number');

        return view('verification.view_result', compact('sample', 'groupedResults'));
    }
}
