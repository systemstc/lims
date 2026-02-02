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
    public function viewAnalystDashboard(Request $request)
    {
        $userId = Session::get('user_id');
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // Pending tests count
        $pendingTests = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'ALLOTED')
            ->distinct('tr04_sample_registration_id')
            ->count();

        $rejectedTests = SampleTest::where('m06_alloted_to', $userId)
            ->whereHas('registration.testResult', function ($query) {
                $query->where('tr07_result_status', 'REJECTED')
                    ->where('tr07_is_current', 'YES');
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

        // Allotted Samples Logic (Existing)
        $inProgressWeight = 0.4;
        $allottedSamples->each(function ($sample) use ($inProgressWeight) {
            if ($sample->test_count > 0) {
                $weightedProgress = ($sample->completed_count) + ($sample->in_progress_count * $inProgressWeight);
                $sample->progress_percentage = round(($weightedProgress / $sample->test_count) * 100);
            } else {
                $sample->progress_percentage = 0;
            }
        });

        // NEW: Weekly Trend (Last 7 Days) - Completed Tests & Samples
        $weeklyTrend = SampleTest::where('m06_alloted_to', $userId)
            ->where('tr05_status', 'COMPLETED')
            ->whereDate('tr05_completed_at', '>=', Carbon::now()->subDays(6))
            ->select(
                DB::raw('DATE(tr05_completed_at) as date'),
                DB::raw('count(*) as tests_count'),
                DB::raw('count(distinct tr04_sample_registration_id) as samples_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with 0
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $weeklyTrend->firstWhere('date', $date);
            $trendData[] = [
                'date' => $date,
                'tests_count' => $dayData ? $dayData->tests_count : 0,
                'samples_count' => $dayData ? $dayData->samples_count : 0
            ];
        }

        // NEW: Test Distribution by Standard/Type
        // Assuming we want to see what KIND of tests they are doing (Microbiology, Chemical, etc - represented by Standard or Group?)
        // Let's use Standard Name or Test Name distribution (Top 5)
        $testDistribution = SampleTest::join('m12_tests', 'tr05_sample_tests.m12_test_id', '=', 'm12_tests.m12_test_id')
            ->where('m06_alloted_to', $userId)
            ->select('m12_tests.m12_name', DB::raw('count(*) as count'))
            ->groupBy('m12_tests.m12_name')
            ->orderByDesc('count')
            ->take(5)
            ->take(5)
            ->get();

        $heatmapData = $this->generateAnalystHeatmapData($userId, $month, $year);

        return view('analyst.analyst_dashboard', compact(
            'pendingTests',
            'rejectedTests',
            'inProgressTests',
            'completedTests',
            'totalSamples',
            'allottedSamples',
            'trendData',        // NEW
            'testDistribution',  // NEW
            'heatmapData',
            'month',
            'year'
        ));
    }

    private function generateAnalystHeatmapData($userId, $month, $year)
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $currentDate = Carbon::now();

        // Get all samples ALLOTTED to this user in this month/year
        // Using tr05_alloted_at as the primary date for the calendar
        $samples = SampleTest::with('registration')
            ->where('m06_alloted_to', $userId)
            ->whereYear('tr05_alloted_at', $year)
            ->whereMonth('tr05_alloted_at', $month)
            ->get();

        // Group samples by allotment day
        $samplesByDay = [];
        foreach ($samples as $sample) {
            $day = Carbon::parse($sample->tr05_alloted_at)->day;
            if (!isset($samplesByDay[$day])) {
                $samplesByDay[$day] = [];
            }
            $samplesByDay[$day][] = $sample;
        }

        // Generate heatmap data for each day
        $heatmapData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $daySamples = $samplesByDay[$day] ?? [];
            // Count unique samples (based on registration ID) instead of total tests
            $sampleCount = collect($daySamples)->pluck('tr04_sample_registration_id')->unique()->count();

            if ($sampleCount === 0) {
                $heatmapData[] = [
                    'day' => $day,
                    'sample_count' => 0,
                    'status_class' => 'empty',
                    'tooltip' => "No allotments on " . sprintf("%02d", $day) . " " . \DateTime::createFromFormat('!m', $month)->format('M')
                ];
                continue;
            }

            // Calculate day status based on sample statuses & deadlines
            $dayStatus = $this->calculateAnalystDayStatus($daySamples, $day, $month, $year, $currentDate);

            $heatmapData[] = [
                'day' => $day,
                'sample_count' => $sampleCount,
                'status_class' => $dayStatus['class'],
                'tooltip' => $dayStatus['tooltip']
            ];
        }

        return $heatmapData;
    }

    private function calculateAnalystDayStatus($samples, $day, $month, $year, $currentDate)
    {
        $allCompleted = true;
        $anyOverdue = false;
        $anyNearDeadline = false;
        $totalTests = count($samples);
        $totalUniqueSamples = collect($samples)->pluck('tr04_sample_registration_id')->unique()->count();
        $completedCount = 0;

        foreach ($samples as $sample) {
            // Check completion status
            if ($sample->tr05_status === 'COMPLETED' || $sample->tr05_status === 'REPORTED') {
                $completedCount++;
            } else {
                $allCompleted = false;

                // Check deadlines for pending items
                // Use registration expected date
                if ($sample->registration && $sample->registration->tr04_expected_date) {
                    $expectedDate = Carbon::parse($sample->registration->tr04_expected_date);
                    $daysUntilDeadline = $currentDate->diffInDays($expectedDate, false);

                    if ($daysUntilDeadline < 0) {
                        $anyOverdue = true;
                    } elseif ($daysUntilDeadline <= 3) {
                        $anyNearDeadline = true;
                    }
                }
            }
        }

        // Determine status class
        if ($allCompleted) {
            return [
                'class' => 'on-time', // Reusing 'on-time' (green) for all completed
                'tooltip' => sprintf("%02d: All %d samples completed", $day, $totalUniqueSamples)
            ];
        } elseif ($anyOverdue) {
            return [
                'class' => 'overdue',
                'tooltip' => sprintf("%02d: %d samples (%d tests done) - Overdue", $day, $totalUniqueSamples, $completedCount)
            ];
        } elseif ($anyNearDeadline) {
            return [
                'class' => 'near-deadline',
                'tooltip' => sprintf("%02d: %d samples (%d tests done) - Deadline approaching", $day, $totalUniqueSamples, $completedCount)
            ];
        } else {
            return [
                'class' => 'reported', // Using gray/neutral for "In Progress" without urgent issues
                'tooltip' => sprintf("%02d: %d samples (%d tests done) - In progress", $day, $totalUniqueSamples, $completedCount)
            ];
        }
    }

    public function getAnalystDateSamples(Request $request)
    {
        $userId = Session::get('user_id');
        $day = $request->day;
        $month = $request->month;
        $year = $request->year;

        $samples = SampleTest::with(['registration', 'test'])
            ->where('m06_alloted_to', $userId)
            ->whereYear('tr05_alloted_at', $year)
            ->whereMonth('tr05_alloted_at', $month)
            ->whereDay('tr05_alloted_at', $day)
            ->orderBy('tr05_status') // COMPLETED checks last usually, but 'C' comes before 'I' (In progress)?
            // Default alphabetic: ALLOTED, COMPLETED, IN_PROGRESS. 
            // We might want pending first.
            ->get();

        // Map to simpler format for frontend
        $mappedSamples = $samples->map(function ($s) {
            $expectedDate = $s->registration->tr04_expected_date ? Carbon::parse($s->registration->tr04_expected_date)->format('d/m/Y') : '-';

            // Calculate days remaining/overdue
            $daysRemaining = '-';
            if ($s->registration->tr04_expected_date && $s->tr05_status !== 'COMPLETED') {
                $diff = now()->diffInDays(Carbon::parse($s->registration->tr04_expected_date), false);
                $daysRemaining = $diff < 0 ? abs(round($diff)) . ' days overdue' : round($diff) . ' days left';
            }

            return [
                'reference_id' => $s->registration->tr04_reference_id ?? 'N/A',
                'sample_test_id' => $s->tr05_sample_test_id, // For viewing
                'test_name' => $s->test->m12_name ?? 'N/A',
                'status' => $s->tr05_status,
                'expected_date' => $expectedDate,
                'days_remaining' => $daysRemaining,
                'reg_id' => $s->tr04_sample_registration_id
            ];
        });

        return response()->json([
            'success' => true,
            'samples' => $mappedSamples,
            'count' => $mappedSamples->count()
        ]);
    }
    public function rejectedSamples(Request $request)
    {
        $userId = Session::get('user_id');

        $rejectedSamples = SampleTest::query()
            ->where('m06_alloted_to', $userId)
            ->whereHas('registration.testResult', function ($query) {
                $query->where('tr07_result_status', 'REJECTED')
                    ->where('tr07_is_current', 'YES');
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

                // Auto-complete SampleTest status
                $this->autoCompleteSampleTests($refId, $userId);

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
            Session::flash('message', 'Tests Updated Successfully');
            return redirect()->back();
        }
        Session::flash('type', 'warning');
        Session::flash('message', 'No tests could be updated.');
        return redirect()->back();
    }

    private function autoCompleteSampleTests($registrationId, $userId)
    {
        $query = SampleTest::where('tr04_sample_registration_id', $registrationId)
            ->where('tr05_status', '!=', 'COMPLETED')
            ->where('tr05_status', '!=', 'REPORTED');

        // If not admin/manager, restrict to user's tests
        // But for rejection flow, we might want to be permissive if they are fixing it
        $query->where('m06_alloted_to', $userId);

        $tests = $query->get();

        foreach ($tests as $test) {
            $test->update([
                'tr05_status' => 'COMPLETED',
                'tr05_completed_at' => now(),
            ]);
        }
    }

    public function saveRemark(Request $request)
    {
        try {
            $request->validate([
                'sample_test_id' => 'required',
                'remark' => 'nullable|string'
            ]);

            $sampleTest = SampleTest::where('tr05_sample_test_id', $request->sample_test_id)->firstOrFail();
            $sampleTest->update(['tr05_remark' => $request->remark]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error saving remark: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error saving remark'], 500);
        }
    }
}
