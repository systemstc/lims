<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\PrimaryTest;
use App\Models\Sample;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\SecondaryTest;
use App\Models\TestReport;
use App\Models\TestResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    // public function reviseTest(Request $request, $refId)
    // {
    //     // GET: Show all rejected results for this sample reference
    //     if ($request->isMethod('get')) {
    //         $sample = SampleRegistration::with([
    //             'testResult.test.standard',
    //             'testResult.manuscript'
    //         ])
    //             ->where('tr04_reference_id', $refId)
    //             ->firstOrFail();

    //         // Group by test number for parent-child display
    //         $groupedResults = $sample->testResult
    //             ->where('tr07_result_status', 'REJECTED')
    //             ->where('tr07_is_current', 'YES')
    //             ->groupBy('m12_test_number');

    //         if ($groupedResults->isEmpty()) {
    //             Session::flash('type', 'success');
    //             Session::flash('message', 'No rejected tests found for this sample.');
    //             return to_route('rejected_samples');
    //         }

    //         return view('analyst.revise_tests', compact('sample', 'groupedResults'));
    //     }

    //     // POST: Save all revisions
    //     if ($request->isMethod('post')) {
    //         $oldResults = TestResult::where('tr04_reference_id', $refId)
    //             ->where('tr07_result_status', 'REJECTED')
    //             ->where('tr07_is_current', 'YES')
    //             ->get();

    //         foreach ($oldResults as $old) {
    //             $old->update(['tr07_is_current' => 'NO']);

    //             $new = new TestResult();
    //             $new->fill([
    //                 'm04_ro_id' => $old->m04_ro_id,
    //                 'tr04_reference_id' => $old->tr04_reference_id,
    //                 'm12_test_number' => $old->m12_test_number,
    //                 'm22_manuscript_id' => $old->m22_manuscript_id,
    //                 'tr07_result' => $request->input("txt_result_{$old->tr07_test_result_id}"),
    //                 'tr07_current_version' => $old->tr07_current_version + 1,
    //                 'tr07_is_current' => 'YES',
    //                 'tr07_result_status' => 'REVISED',
    //                 'tr07_test_date' => $request->txt_test_date,
    //                 'tr07_performance_date' => $request->txt_performance_date,
    //                 'tr07_remarks' => $request->input("txt_remarks_{$old->tr07_test_result_id}"),
    //                 'm06_created_by' => Session::get('user_id'),
    //                 'tr07_created_at' => now(),
    //                 'tr07_status' => 'ACTIVE',
    //             ]);
    //             $new->save();
    //         }
    //         Session::flash('type', 'success');
    //         Session::flash('message', 'Revised results saved successfully!');
    //         return to_route('rejected_samples');
    //     }
    // }

    public function reviseTest(Request $request, $refId)
    {
        // GET: Show the re-entry form with existing rejected results
        if ($request->isMethod('get')) {
            // Get the sample registration
            $sample = SampleRegistration::with([
                'labSample',
                'sampleTests.test',
                'sampleTests.test.standard',
                'sampleTests.allotedTo',
                'sampleTests.allotedBy'
            ])->where('tr04_reference_id', $refId)->firstOrFail();

            // Get rejected test results
            $rejectedResults = TestResult::where('tr04_reference_id', $refId)
                ->where('tr07_result_status', 'REJECTED')
                ->where('tr07_is_current', 'YES')
                ->get();

            // Get rejected custom fields
            $rejectedCustomFields = CustomField::where('tr04_reference_id', $refId)
                ->where('tr08_result_status', 'REJECTED')
                ->where('tr08_is_current', 'YES')
                ->get();

            if ($rejectedResults->isEmpty() && $rejectedCustomFields->isEmpty()) {
                Session::flash('type', 'warning');
                Session::flash('message', 'No rejected test results found for this sample.');
                return to_route('rejected_samples');
            }

            // Get sample tests for the rejected results
            $testNumbers = $rejectedResults->pluck('m12_test_number')->unique();
            $sampleTests = SampleTest::with([
                'test',
                'test.standard'
            ])->where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                ->whereIn('m12_test_number', $testNumbers)
                ->get();

            // Prepare test data with associated primary and secondary tests
            foreach ($sampleTests as $sampleTest) {
                if ($sampleTest->test) {
                    $test = $sampleTest->test;

                    // Load primary tests that are associated with this test
                    if ($test->m16_primary_test_id) {
                        $primaryTestIds = explode(',', $test->m16_primary_test_id);
                        $primaryTests = PrimaryTest::whereIn('m16_primary_test_id', $primaryTestIds)->get();

                        // Load secondary tests for each primary test
                        foreach ($primaryTests as $primaryTest) {
                            $secondaryTests = SecondaryTest::where('m16_primary_test_id', $primaryTest->m16_primary_test_id)->get();
                            $primaryTest->secondaryTests = $secondaryTests;
                        }

                        $test->primaryTests = $primaryTests;
                    }
                }
            }

            // Get dates from first rejected result
            $testDate = $rejectedResults->first()->tr07_test_date ?? null;
            $performanceDate = $rejectedResults->first()->tr07_performance_date ?? null;

            return view('analyst.revise_tests', compact(
                'sample',
                'sampleTests',
                'rejectedResults',
                'rejectedCustomFields',
                'testDate',
                'performanceDate'
            ));
        }

        // POST: Save the revised results
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'test_date' => 'required|date',
                'performance_date' => 'required|date',
                'results' => 'nullable|array',
                'custom_fields' => 'nullable|array',
                'remarks' => 'nullable|string',
                'action' => 'required|string|in:DRAFT,SUBMITTED,RESULTED'
            ]);

            DB::beginTransaction();
            try {
                $userId = Session::get('user_id') ?? -1;
                $roId = Session::get('ro_id');

                // Mark old rejected results as not current
                TestResult::where('tr04_reference_id', $refId)
                    ->where('tr07_result_status', 'REJECTED')
                    ->where('tr07_is_current', 'YES')
                    ->update(['tr07_is_current' => 'NO']);

                // Mark old rejected custom fields as not current
                CustomField::where('tr04_reference_id', $refId)
                    ->where('tr08_result_status', 'REJECTED')
                    ->where('tr08_is_current', 'YES')
                    ->update(['tr08_is_current' => 'NO']);

                // Handle main test results (similar to createResult)
                if (!empty($request->results)) {
                    foreach ($request->results as $testNumber => $resultData) {

                        // Handle main test result (tests without primary tests)
                        if (isset($resultData['test'])) {
                            $testResult = $resultData['test'];
                            $this->saveRevisedTestResult([
                                'registration_id' => $refId,
                                'test_number' => $testNumber,
                                'result_data' => $testResult,
                                'test_date' => $request->test_date,
                                'performance_date' => $request->performance_date,
                                'remarks' => $request->remarks,
                                'action' => $request->action,
                                'user_id' => $userId,
                                'ro_id' => $roId
                            ]);
                        }

                        // Handle primary tests
                        if (isset($resultData['primary_tests'])) {
                            foreach ($resultData['primary_tests'] as $primaryTestId => $primaryData) {

                                // Primary test without secondary tests
                                if (isset($primaryData['result'])) {
                                    $this->saveRevisedTestResult([
                                        'registration_id' => $refId,
                                        'test_number' => $testNumber,
                                        'primary_test_id' => $primaryTestId,
                                        'result_data' => $primaryData,
                                        'test_date' => $request->test_date,
                                        'performance_date' => $request->performance_date,
                                        'remarks' => $request->remarks,
                                        'action' => $request->action,
                                        'user_id' => $userId,
                                        'ro_id' => $roId
                                    ]);
                                }

                                // Primary test with secondary tests
                                if (isset($primaryData['secondary_tests'])) {
                                    foreach ($primaryData['secondary_tests'] as $secondaryTestId => $secondaryData) {
                                        $this->saveRevisedTestResult([
                                            'registration_id' => $refId,
                                            'test_number' => $testNumber,
                                            'primary_test_id' => $primaryTestId,
                                            'secondary_test_id' => $secondaryTestId,
                                            'result_data' => $secondaryData,
                                            'test_date' => $request->test_date,
                                            'performance_date' => $request->performance_date,
                                            'remarks' => $request->remarks,
                                            'action' => $request->action,
                                            'user_id' => $userId,
                                            'ro_id' => $roId
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                // Handle custom fields with proper hierarchy
                if (!empty($request->custom_fields)) {
                    foreach ($request->custom_fields as $testNumber => $testLevelData) {
                        $this->processRevisedCustomFieldLevel($testLevelData, [
                            'registration_id' => $refId,
                            'test_number' => $testNumber,
                            'test_date' => $request->test_date,
                            'performance_date' => $request->performance_date,
                            'remarks' => $request->remarks,
                            'action' => $request->action,
                            'user_id' => $userId
                        ]);
                    }
                }

                DB::commit();

                $message = $request->action === 'DRAFT'
                    ? 'Revised test results saved as draft successfully.'
                    : 'Revised test results submitted successfully.';

                Session::flash('type', 'success');
                Session::flash('message', $message);
                return to_route('rejected_samples');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving revised test results: ' . $e->getMessage());
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to save revised test results: ' . $e->getMessage());
                return back()->withInput();
            }
        }
    }

    /**
     * Save revised test result (creates new version)
     */
    private function saveRevisedTestResult($data)
    {
        $resultData = $data['result_data'];

        // Get the previous version to increment version number
        $previousResult = TestResult::where('tr04_reference_id', $data['registration_id'])
            ->where('m12_test_number', $data['test_number'])
            ->where('m16_primary_test_id', $data['primary_test_id'] ?? null)
            ->where('m17_secondary_test_id', $data['secondary_test_id'] ?? null)
            ->where('tr07_is_current', 'NO')
            ->orderBy('tr07_current_version', 'desc')
            ->first();

        $newVersion = $previousResult ? $previousResult->tr07_current_version + 1 : 1;

        TestResult::create([
            'm04_ro_id' => $data['ro_id'],
            'tr04_reference_id' => $data['registration_id'],
            'm12_test_number' => $data['test_number'],
            'm16_primary_test_id' => $data['primary_test_id'] ?? null,
            'm17_secondary_test_id' => $data['secondary_test_id'] ?? null,
            'm22_manuscript_id' => null,
            'tr07_result' => $resultData['result'],
            'tr07_unit' => $resultData['unit'] ?? null,
            'tr07_test_date' => $data['test_date'],
            'tr07_performance_date' => $data['performance_date'],
            'tr07_remarks' => $data['remarks'],
            'tr07_current_version' => $newVersion,
            'tr07_result_status' => $data['action'],
            'tr07_is_current' => 'YES',
            'm06_created_by' => $data['user_id'],
        ]);
    }

    /**
     * Process revised custom fields recursively through the hierarchy
     */
    private function processRevisedCustomFieldLevel($levelData, $baseData, $primaryTestId = null, $secondaryTestId = null)
    {
        foreach ($levelData as $key => $data) {
            if (str_starts_with($key, 'primary_')) {
                // This is a primary test level
                $currentPrimaryTestId = str_replace('primary_', '', $key);
                $this->processRevisedCustomFieldLevel($data, $baseData, $currentPrimaryTestId, null);
            } elseif (str_starts_with($key, 'secondary_')) {
                // This is a secondary test level
                $currentSecondaryTestId = str_replace('secondary_', '', $key);
                $this->processRevisedCustomFieldLevel($data, $baseData, $primaryTestId, $currentSecondaryTestId);
            } else {
                // This is an actual custom field data
                if (isset($data['name']) && isset($data['value'])) {
                    $this->saveRevisedCustomField([
                        'registration_id' => $baseData['registration_id'],
                        'test_number' => $baseData['test_number'],
                        'primary_test_id' => $primaryTestId,
                        'secondary_test_id' => $secondaryTestId,
                        'field_data' => $data,
                        'test_date' => $baseData['test_date'],
                        'performance_date' => $baseData['performance_date'],
                        'remarks' => $baseData['remarks'],
                        'action' => $baseData['action'],
                        'user_id' => $baseData['user_id']
                    ]);
                }
            }
        }
    }

    /**
     * Save revised custom field (creates new version)
     */
    private function saveRevisedCustomField($data)
    {
        $fieldData = $data['field_data'];

        // Get the previous version to increment version number
        $previousCustomField = CustomField::where('tr04_reference_id', $data['registration_id'])
            ->where('m12_test_number', $data['test_number'])
            ->where('m16_primary_test_id', $data['primary_test_id'])
            ->where('m17_secondary_test_id', $data['secondary_test_id'])
            ->where('tr08_field_name', $fieldData['name'])
            ->where('tr08_is_current', 'NO')
            ->orderBy('tr08_current_version', 'desc')
            ->first();

        $newVersion = $previousCustomField ? $previousCustomField->tr08_current_version + 1 : 1;

        CustomField::create([
            'tr04_reference_id' => $data['registration_id'],
            'm12_test_number' => $data['test_number'],
            'm16_primary_test_id' => $data['primary_test_id'],
            'm17_secondary_test_id' => $data['secondary_test_id'],
            'tr08_field_name' => $fieldData['name'],
            'tr08_field_value' => $fieldData['value'],
            'tr08_field_unit' => $fieldData['unit'] ?? null,
            'tr08_result_status' => $data['action'],
            'tr08_test_date' => $data['test_date'],
            'tr08_performance_date' => $data['performance_date'],
            'tr08_remarks' => $data['remarks'],
            'tr08_current_version' => $newVersion,
            'tr08_is_current' => 'YES',
            'm06_created_by' => $data['user_id'],
        ]);
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
