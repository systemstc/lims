<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'testResult.primaryTest',
            'testResult.secondaryTest',
            'labSample',
            'customFields'
        ])->where('tr04_sample_registration_id', $sample_id)
            ->firstOrFail();
        if ($request->isMethod('POST')) {
            $request->validate([
                'action' => 'required|in:verify,reject',
                'remarks' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();
            try {
                // Update test results
                $currentResults = $sample->testResult->where('tr07_is_current', 'YES');

                foreach ($currentResults as $result) {
                    $result->update([
                        'tr07_result_status' => $request->action === 'verify' ? 'VERIFIED' : 'REJECTED',
                        'm06_verified_by'    => Session::get('user_id') ?? null,
                        'tr07_verified_at'   => now(),
                        'tr07_remarks'       => $request->remarks,
                    ]);
                }

                // Update custom fields
                $customFields = CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->whereIn('tr08_result_status', ['SUBMITTED', 'DRAFT', 'RESULTED'])
                    ->get();

                foreach ($customFields as $customField) {
                    $customField->update([
                        'tr08_result_status' => $request->action === 'verify' ? 'VERIFIED' : 'REJECTED',
                        'm06_updated_by'     => Session::get('user_id') ?? null,
                        'tr08_updated_at'    => now(),
                        'tr08_remarks'       => $request->remarks,
                    ]);
                }

                DB::commit();

                Session::flash('type', 'success');
                Session::flash('message', 'Results ' . ($request->action === 'verify' ? 'verified' : 'rejected') . ' successfully.');
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

        // Get current results and organize them properly
        $currentResults = $sample->testResult->where('tr07_is_current', 'YES');

        // Get custom fields for this sample
        $customFields = CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
            ->whereIn('tr08_result_status', ['SUBMITTED', 'DRAFT', 'RESULTED'])
            ->get();

        // dd($customFields);
        // Group by test number first
        $groupedResults = $currentResults->groupBy('m12_test_number');

        // Process each test group to organize the data properly
        $organizedResults = $groupedResults->map(function ($results, $testNumber) use ($sample, $customFields) {
            $test = $results->first()->test;

            // Check what type of results we have
            $hasPrimaryTests = $results->whereNotNull('m16_primary_test_id')->count() > 0;
            $hasSecondaryTests = $results->whereNotNull('m17_secondary_test_id')->count() > 0;
            $hasMainResult = $results->whereNull('m16_primary_test_id')->whereNull('m17_secondary_test_id')->count() > 0;

            // Get custom fields for this test
            $testCustomFields = $customFields->where('m12_test_number', $testNumber);

            // Organize results by type
            $organized = [
                'test' => $test,
                'has_primary_tests' => $hasPrimaryTests,
                'has_secondary_tests' => $hasSecondaryTests,
                'has_main_result' => $hasMainResult,
                'main_results' => collect(),
                'primary_results' => collect(),
                'custom_fields' => [
                    'main' => collect(),
                    'primary' => collect()
                ]
            ];

            // Get main test result (if exists)
            if ($hasMainResult) {
                $organized['main_results'] = $results->whereNull('m16_primary_test_id')
                    ->whereNull('m17_secondary_test_id');
            }

            // Get custom fields for main test
            $organized['custom_fields']['main'] = $testCustomFields->whereNull('m16_primary_test_id');

            // Get primary tests and their secondary tests
            if ($hasPrimaryTests) {
                $primaryGroups = $results->whereNotNull('m16_primary_test_id')
                    ->groupBy('m16_primary_test_id');

                foreach ($primaryGroups as $primaryTestId => $primaryResults) {
                    $primaryTest = $primaryResults->first()->primaryTest;

                    // Get custom fields for this primary test
                    $primaryCustomFields = $testCustomFields->where('m16_primary_test_id', $primaryTestId);

                    $primaryData = [
                        'primary_test' => $primaryTest,
                        'primary_result' => $primaryResults->whereNull('m17_secondary_test_id')->first(),
                        'secondary_results' => $primaryResults->whereNotNull('m17_secondary_test_id'),
                        'custom_fields' => $primaryCustomFields
                    ];

                    $organized['primary_results']->push($primaryData);

                    // Also add to organized custom fields collection
                    $organized['custom_fields']['primary'] = $organized['custom_fields']['primary']->merge($primaryCustomFields);
                }
            }

            // Add old version for revised results
            foreach ($results as $result) {
                if ($result->tr07_result_status === 'REVISED') {
                    $old = $sample->testResult()
                        ->where('m12_test_number', $result->m12_test_number)
                        ->where('m16_primary_test_id', $result->m16_primary_test_id)
                        ->where('m17_secondary_test_id', $result->m17_secondary_test_id)
                        ->where('tr07_is_current', 'NO')
                        ->orderByDesc('tr07_test_result_id')
                        ->first();
                    $result->old_version = $old;
                }
            }

            return $organized;
        });

        return view('verification.view_result', compact('sample', 'organizedResults'));
    }
}
