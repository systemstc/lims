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
                $q->where('tr07_is_current', 'YES')
                    ->whereIn('tr07_result_status', ['RESULTED', 'REVISED']);
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
                $results = optional($sample->registration)->testResult ?? collect();

                // Only take current results
                $currentResults = $results->where('tr07_is_current', 'YES');

                // Include sample if *all* current tests are either RESULTED or REVISED
                $hasResults = $currentResults->count() > 0;
                $allVerified = $hasResults && $currentResults->every(function ($r) {
                    return in_array($r->tr07_result_status, ['RESULTED', 'REVISED']);
                });

                return $allVerified;
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
                        in_array($r->tr07_result_status, ['RESULTED', 'REVISED'])
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
        $sample = SampleRegistration::with([
            'testResult.test',
            'testResult.manuscript'
        ])->where('tr04_sample_registration_id', $sample_id)
            ->firstOrFail();

        if ($request->isMethod('POST')) {
            $request->validate([
                'action' => 'required|in:verify,reject',
                'remarks' => 'nullable|string|max:500',
            ]);

            $groupedResults = $sample->testResult->groupBy('m12_test_number');

            foreach ($groupedResults as $testNumber => $results) {
                foreach ($results as $result) {
                    if ($result->tr07_is_current === 'YES') {
                        $result->update([
                            'tr07_result_status' => $request->action === 'verify' ? 'VERIFIED' : 'REJECTED',
                            'm06_verified_by'    => Session::get('user_id') ?? null,
                            'tr07_verified_at'   => now(),
                            'tr07_remarks'       => $request->remarks,
                        ]);
                    }
                }
            }


            Session::flash('type', 'success');
            Session::flash('message', 'Results ' . ucfirst($request->action) . ' successfully.');
            return to_route('view_result_verification');
        }

        $groupedResults = $sample->testResult
            ->where('tr07_is_current', 'YES')
            ->groupBy('m12_test_number')
            ->map(function ($results) use ($sample) {
                return $results->map(function ($result) use ($sample) {
                    if ($result->tr07_result_status === 'REVISED') {
                        $old = $sample->testResult()
                            ->where('m12_test_number', $result->m12_test_number)
                            ->where('tr07_is_current', 'NO')
                            ->orderByDesc('tr07_test_result_id')
                            ->first();
                        $result->old_version = $old;
                    }
                    return $result;
                });
            });

        return view('verification.view_result', compact('sample', 'groupedResults'));
    }
}
