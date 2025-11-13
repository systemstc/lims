<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\PrimaryTest;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\SecondaryTest;
use App\Models\TestReport;
use Illuminate\Http\Request;
use App\Models\TestResult;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class TestResultController extends Controller
{
    public function reporting(Request $request)
    {
        // Base query with filters for 
        $query = TestResult::with('creator')
            ->where('tr07_result_status', 'VERIFIED')
            ->where('tr07_is_current', 'YES')
            ->active();

        // Restrict by RO if available
        if (Session::get('ro_id')) {
            $query->where('m04_ro_id', Session::get('ro_id'));
        }

        // Apply filters
        // if ($request->filled('status')) {
        //     $query->byStatus($request->status);
        // }

        if ($request->filled('date_from')) {
            $query->whereDate('tr07_test_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tr07_test_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('m22_manuscript_id', 'like', "%{$search}%")
                    ->orWhere('m12_test_number', 'like', "%{$search}%")
                    ->orWhere('tr04_reference_id', 'like', "%{$search}%");
            });
        }

        $testResults = $query
            ->select(
                'tr04_reference_id',
                DB::raw('MAX(tr07_test_date) as last_test_date'),
                DB::raw('COUNT(*) as total_tests')
            )
            ->groupBy('tr04_reference_id')
            ->orderByDesc('last_test_date')
            ->paginate(15);
        return view('test-results.view_test_results', compact('testResults'));
    }

    // public function templateTestResult(Request $request, $id)
    // {
    //     $route = '';
    //     // Get tests based on role
    //     if (Session::get('role') === 'Manager') {
    //         $sampleTests = SampleTest::with([
    //             'test',
    //             'test.standard',
    //             'registration',
    //             'registration.labSample',
    //             'allotedTo'
    //         ])->where('tr04_sample_registration_id', $id)->get();
    //         $route = 'back';
    //     } elseif (Session::get('role') === 'DEO') {
    //         $sampleTests = SampleTest::with([
    //             'test',
    //             'test.standard',
    //             'registration',
    //             'registration.labSample',
    //             'allotedTo'
    //         ])->where('tr05_status', 'COMPLETED')
    //             ->where('tr04_sample_registration_id', $id)->get();
    //         $route = 'view_completed_camples';
    //     } else {
    //         $sampleTests = SampleTest::with([
    //             'test',
    //             'test.standard',
    //             'registration',
    //             'registration.labSample',
    //             'allotedTo',
    //             'allotedBy'
    //         ])
    //             ->where('tr04_sample_registration_id', $id)
    //             ->where('m06_alloted_to', Session::get('user_id'))
    //             ->whereIn('tr05_status', ['ALLOTED', 'IN_PROGRESS', 'COMPLETED'])
    //             ->get();
    //         $route = 'view_analyst_dashboard';
    //     }

    //     $registrationId = $sampleTests->first()->registration->tr04_reference_id ?? null;

    //     // Check if results exist and are not in DRAFT or SUBMITTED status
    //     if ($registrationId) {
    //         $existingFinalResults = TestResult::where('tr04_reference_id', $registrationId)
    //             ->whereNotIn('tr07_result_status', ['DRAFT', 'SUBMITTED'])
    //             ->exists();

    //         $existingFinalCustomFields = CustomField::where('tr04_reference_id', $registrationId)
    //             ->whereNotIn('tr08_result_status', ['DRAFT', 'SUBMITTED'])
    //             ->exists();

    //         // If final results exist (not draft or submitted), redirect back
    //         if ($existingFinalResults || $existingFinalCustomFields) {
    //             Session::flash('type', 'warning');
    //             Session::flash('message', 'Test results have already been finalized and cannot be modified.');
    //             return to_route($route);
    //         }
    //     }

    //     // Fetch primary and secondary tests for each test
    //     foreach ($sampleTests as $sampleTest) {
    //         if ($sampleTest->test) {
    //             $test = $sampleTest->test;

    //             // Load primary tests
    //             if ($test->m16_primary_test_id) {
    //                 $primaryTestIds = explode(',', $test->m16_primary_test_id);
    //                 $primaryTests = PrimaryTest::whereIn('m16_primary_test_id', $primaryTestIds)->get();
    //                 $test->primaryTests = $primaryTests;

    //                 // Load secondary tests for each primary test
    //                 foreach ($primaryTests as $primaryTest) {
    //                     if ($test->m17_secondary_test_id) {
    //                         $secondaryTestIds = explode(',', $test->m17_secondary_test_id);
    //                         $secondaryTests = SecondaryTest::whereIn('m17_secondary_test_id', $secondaryTestIds)
    //                             ->where('m16_primary_test_id', $primaryTest->m16_primary_test_id)
    //                             ->get();
    //                         $primaryTest->secondaryTests = $secondaryTests;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     // Fetch existing test results and custom fields (only DRAFT or SUBMITTED)
    //     $existingResults = collect();
    //     $existingCustomFields = collect();
    //     $testDate = null;
    //     $performanceDate = null;

    //     if ($registrationId) {
    //         $existingResults = TestResult::where('tr04_reference_id', $registrationId)
    //             ->whereIn('tr07_result_status', ['DRAFT', 'SUBMITTED'])
    //             ->get();

    //         $existingCustomFields = CustomField::where('tr04_reference_id', $registrationId)
    //             ->whereIn('tr08_result_status', ['DRAFT', 'SUBMITTED'])
    //             ->get();

    //         // Get dates from first result if exists
    //         if ($existingResults->isNotEmpty()) {
    //             $firstResult = $existingResults->first();
    //             $testDate = $firstResult->tr07_test_date ?? null;
    //             $performanceDate = $firstResult->tr07_performance_date ?? null;
    //         } elseif ($existingCustomFields->isNotEmpty()) {
    //             $firstCustomField = $existingCustomFields->first();
    //             $testDate = $firstCustomField->tr08_test_date ?? null;
    //             $performanceDate = $firstCustomField->tr08_performance_date ?? null;
    //         }
    //     }

    //     return view('testresult.result_template', compact(
    //         'sampleTests',
    //         'existingResults',
    //         'existingCustomFields',
    //         'testDate',
    //         'performanceDate'
    //     ));
    // }


    public function templateTestResult(Request $request, $id)
    {
        $route = '';
        // Get tests based on role
        if (Session::get('role') === 'Manager') {
            $sampleTests = SampleTest::with([
                'test',
                'test.standard',
                'registration',
                'registration.labSample',
                'allotedTo'
            ])->where('tr04_sample_registration_id', $id)->get();
            $route = 'back';
        } elseif (Session::get('role') === 'DEO') {
            $sampleTests = SampleTest::with([
                'test',
                'test.standard',
                'registration',
                'registration.labSample',
                'allotedTo'
            ])->where('tr05_status', 'COMPLETED')
                ->where('tr04_sample_registration_id', $id)->get();
            $route = 'view_completed_camples';
        } else {
            $sampleTests = SampleTest::with([
                'test',
                'test.standard',
                'registration',
                'registration.labSample',
                'allotedTo',
                'allotedBy'
            ])
                ->where('tr04_sample_registration_id', $id)
                ->where('m06_alloted_to', Session::get('user_id'))
                ->whereIn('tr05_status', ['ALLOTED', 'IN_PROGRESS', 'COMPLETED'])
                ->get();
            $route = 'view_analyst_dashboard';
        }

        $registrationId = $sampleTests->first()->registration->tr04_reference_id ?? null;

        // Check if results exist and are not in DRAFT or SUBMITTED status
        if ($registrationId) {
            $existingFinalResults = TestResult::where('tr04_reference_id', $registrationId)
                ->whereNotIn('tr07_result_status', ['DRAFT', 'SUBMITTED'])
                ->exists();

            $existingFinalCustomFields = CustomField::where('tr04_reference_id', $registrationId)
                ->whereNotIn('tr08_result_status', ['DRAFT', 'SUBMITTED'])
                ->exists();

            // If final results exist (not draft or submitted), redirect back
            if ($existingFinalResults || $existingFinalCustomFields) {
                Session::flash('type', 'warning');
                Session::flash('message', 'Test results have already been finalized and cannot be modified.');
                return to_route($route);
            }
        }

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

        // Fetch existing test results and custom fields (only DRAFT or SUBMITTED)
        $existingResults = collect();
        $existingCustomFields = collect();
        $testDate = null;
        $performanceDate = null;

        if ($registrationId) {
            $existingResults = TestResult::where('tr04_reference_id', $registrationId)
                ->whereIn('tr07_result_status', ['DRAFT', 'SUBMITTED'])
                ->get();

            $existingCustomFields = CustomField::where('tr04_reference_id', $registrationId)
                ->whereIn('tr08_result_status', ['DRAFT', 'SUBMITTED'])
                ->get();

            // Get dates from first result if exists
            if ($existingResults->isNotEmpty()) {
                $firstResult = $existingResults->first();
                $testDate = $firstResult->tr07_test_date ?? null;
                $performanceDate = $firstResult->tr07_performance_date ?? null;
            } elseif ($existingCustomFields->isNotEmpty()) {
                $firstCustomField = $existingCustomFields->first();
                $testDate = $firstCustomField->tr08_test_date ?? null;
                $performanceDate = $firstCustomField->tr08_performance_date ?? null;
            }
        }

        return view('testresult.result_template', compact(
            'sampleTests',
            'existingResults',
            'existingCustomFields',
            'testDate',
            'performanceDate'
        ));
    }
    public function templateManuscript(Request $request, $id)
    {
        // Get manuscripts based on role
        if (Session::get('role') === 'Manager') {
            $manuscripts = SampleTest::with([
                'test',
                'manuscript',
                'standard',
                'registration',
                'registration.labSample',
                'allotedTo'
            ])->where('tr04_sample_registration_id', $id)->get();
        } elseif (Session::get('role') === 'DEO') {
            $manuscripts = SampleTest::with([
                'test',
                'manuscript',
                'standard',
                'registration',
                'registration.labSample',
                'allotedTo'
            ])->where('tr05_status', 'COMPLETED')
                ->where('tr04_sample_registration_id', $id)->get();
        } else {
            $manuscripts = SampleTest::with([
                'test',
                'manuscript',
                'standard',
                'registration',
                'registration.labSample',
                'allotedTo',
                'allotedBy'
            ])
                ->where('tr04_sample_registration_id', $id)
                ->where('m06_alloted_to', Session::get('user_id'))
                ->whereIn('tr05_status', ['ALLOTED', 'IN_PROGRESS', 'COMPLETED'])
                ->get();
        }
        $registrationId = $manuscripts->first()->registration->tr04_reference_id ?? null;

        // Fetch existing test results (drafts or submitted)
        $existingResults = collect();
        $testDate = null;
        $performanceDate = null;

        if ($registrationId) {
            $existingResults = TestResult::where('tr04_reference_id', $registrationId)
                ->whereIn('tr07_result_status', ['DRAFT', 'SUBMITTED'])
                ->get();

            // Get dates from first result if exists
            if ($existingResults->isNotEmpty()) {
                $firstResult = $existingResults->first();
                $testDate = $firstResult->tr07_test_date ?? null;
                $performanceDate = $firstResult->tr07_performance_date ?? null;
            }
        }
        return view('manuscript.template_manuscript', compact(
            'manuscripts',
            'existingResults',
            'testDate',
            'performanceDate'
        ));
    }

    // public function createResult(Request $request)
    // {
    //     $validated = $request->validate([
    //         'registration_id' => 'required|string',
    //         'test_date' => 'required|date',
    //         'performance_date' => 'required|date',
    //         'test_data' => 'nullable|array',
    //         'manuscript_data' => 'nullable|array',
    //         'remarks' => 'nullable|string',
    //         'action' => 'required|string|in:DRAFT,SUBMITTED,RESULTED'
    //     ]);
    //     DB::beginTransaction();
    //     try {
    //         // Handle test data (tests without manuscripts)
    //         if (!empty($request->test_data)) {
    //             foreach ($request->test_data as $test) {
    //                 // Check if result_id exists (updating)
    //                 if (!empty($test['result_id'])) {
    //                     TestResult::where('tr07_test_result_id', $test['result_id'])
    //                         ->update([
    //                             'tr07_result' => $test['result'],
    //                             'tr07_result_status' => $request->action,
    //                             'tr07_test_date' => $request->test_date,
    //                             'tr07_performance_date' => $request->performance_date,
    //                             'tr07_remarks' => $request->remarks,
    //                             'm06_updated_by' => Session::get('user_id') ?? -1,
    //                             'tr07_updated_at' => now(),
    //                         ]);
    //                 } else {
    //                     // Creating new result
    //                     TestResult::create([
    //                         'm04_ro_id' => Session::get('ro_id'),
    //                         'tr04_reference_id' => $request->registration_id,
    //                         'm12_test_number' => $test['test_id'],
    //                         'm22_manuscript_id' => null,
    //                         'tr07_result' => $test['result'],
    //                         'tr07_test_date' => $request->test_date,
    //                         'tr07_performance_date' => $request->performance_date,
    //                         'tr07_remarks' => $request->remarks,
    //                         'tr07_current_version' => 1,
    //                         'tr07_result_status' => $request->action,
    //                         'm06_created_by' => Session::get('user_id') ?? -1,
    //                         'tr07_created_at' => now(),
    //                     ]);
    //                 }
    //             }
    //         }

    //         // Handle manuscript data (tests with manuscripts)
    //         if (!empty($request->manuscript_data)) {
    //             foreach ($request->manuscript_data as $testIndex => $manuscripts) {
    //                 foreach ($manuscripts as $manuscript) {
    //                     // Check if result_id exists (updating)
    //                     if (!empty($manuscript['result_id'])) {
    //                         TestResult::where('tr07_test_result_id', $manuscript['result_id'])
    //                             ->update([
    //                                 'tr07_result' => $manuscript['result'],
    //                                 'tr07_result_status' => $request->action,
    //                                 'tr07_test_date' => $request->test_date,
    //                                 'tr07_performance_date' => $request->performance_date,
    //                                 'tr07_remarks' => $request->remarks,
    //                                 'm06_updated_by' => Session::get('user_id') ?? -1,
    //                                 'tr07_updated_at' => now(),
    //                             ]);
    //                     } else {
    //                         // Creating new result
    //                         TestResult::create([
    //                             'm04_ro_id' => Session::get('ro_id'),
    //                             'tr04_reference_id' => $request->registration_id,
    //                             'm12_test_number' => $manuscript['test_id'],
    //                             'm22_manuscript_id' => $manuscript['manuscript_id'],
    //                             'tr07_result' => $manuscript['result'],
    //                             'tr07_test_date' => $request->test_date,
    //                             'tr07_performance_date' => $request->performance_date,
    //                             'tr07_remarks' => $request->remarks,
    //                             'tr07_current_version' => 1,
    //                             'tr07_result_status' => $request->action,
    //                             'm06_created_by' => Session::get('user_id') ?? -1,
    //                             'tr07_created_at' => now(),
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //         DB::commit();
    //         $message = $request->action === 'DRAFT'
    //             ? 'Test results saved as draft successfully.'
    //             : 'Test results submitted successfully.';

    //         Session::flash('type', 'success');
    //         Session::flash('message', $message);
    //         return back();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error saving test results: ' . $e->getMessage());
    //         Session::flash('type', 'error');
    //         Session::flash('message', 'Failed to save test results: ' . $e->getMessage());
    //         return back()->withInput();
    //     }
    // }


    public function createResult(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|string',
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

            // Handle main test results (keep your existing code)
            if (!empty($request->results)) {
                foreach ($request->results as $testNumber => $resultData) {

                    // Handle main test result (tests without primary tests)
                    if (isset($resultData['test'])) {
                        $testResult = $resultData['test'];
                        $this->saveTestResult([
                            'registration_id' => $request->registration_id,
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

                    // Handle primary tests (similar to manuscript_data nested structure)
                    if (isset($resultData['primary_tests'])) {
                        foreach ($resultData['primary_tests'] as $primaryTestId => $primaryData) {

                            // Primary test without secondary tests
                            if (isset($primaryData['result'])) {
                                $this->saveTestResult([
                                    'registration_id' => $request->registration_id,
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

                            // Primary test with secondary tests (nested like manuscripts)
                            if (isset($primaryData['secondary_tests'])) {
                                foreach ($primaryData['secondary_tests'] as $secondaryTestId => $secondaryData) {
                                    $this->saveTestResult([
                                        'registration_id' => $request->registration_id,
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

            // Handle custom fields with proper hierarchy and update support
            if (!empty($request->custom_fields)) {
                foreach ($request->custom_fields as $testNumber => $testLevelData) {

                    // Process test-level custom fields (no primary or secondary)
                    $this->processCustomFieldLevel($testLevelData, [
                        'registration_id' => $request->registration_id,
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
                ? 'Test results saved as draft successfully.'
                : 'Test results submitted successfully.';

            Session::flash('type', 'success');
            Session::flash('message', $message);
            return to_route('view_completed_camples');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving test results: ' . $e->getMessage());
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to save test results: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Process custom fields recursively through the hierarchy
     */
    private function processCustomFieldLevel($levelData, $baseData, $primaryTestId = null, $secondaryTestId = null)
    {
        foreach ($levelData as $key => $data) {
            if (str_starts_with($key, 'primary_')) {
                // This is a primary test level
                $currentPrimaryTestId = str_replace('primary_', '', $key);
                $this->processCustomFieldLevel($data, $baseData, $currentPrimaryTestId, null);
            } elseif (str_starts_with($key, 'secondary_')) {
                // This is a secondary test level
                $currentSecondaryTestId = str_replace('secondary_', '', $key);
                $this->processCustomFieldLevel($data, $baseData, $primaryTestId, $currentSecondaryTestId);
            } else {
                // This is an actual custom field data
                if (isset($data['name']) && isset($data['value'])) {
                    $this->saveCustomField([
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
     * Save or update custom field
     */
    private function saveCustomField($data)
    {
        $fieldData = $data['field_data'];

        $customFieldData = [
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
        ];

        // Check if we're updating an existing custom field
        if (!empty($fieldData['custom_field_id'])) {
            $customField = CustomField::find($fieldData['custom_field_id']);
            if ($customField) {
                $customField->update(array_merge($customFieldData, [
                    'm06_updated_by' => $data['user_id'],
                ]));
                return;
            }
        }

        // Create new custom field
        CustomField::create(array_merge($customFieldData, [
            'm06_created_by' => $data['user_id'],
        ]));
    }

    private function saveTestResult($data)
    {
        $resultData = $data['result_data'];

        // Check if result_id exists (updating) - similar to manuscript handling
        if (!empty($resultData['result_id'])) {
            TestResult::where('tr07_test_result_id', $resultData['result_id'])
                ->update([
                    'tr07_result' => $resultData['result'],
                    'tr07_unit' => $resultData['unit'] ?? null,
                    'tr07_result_status' => $data['action'],
                    'tr07_test_date' => $data['test_date'],
                    'tr07_performance_date' => $data['performance_date'],
                    'tr07_remarks' => $data['remarks'],
                    'm06_updated_by' => $data['user_id'],
                ]);
        } else {
            // Creating new result - similar to manuscript creation
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
                'tr07_current_version' => 1,
                'tr07_result_status' => $data['action'],
                'm06_created_by' => $data['user_id'],
            ]);
        }
    }
    public function showSampleResult($id)
    {
        // Get all test results with relationships
        $testResults = TestResult::with([
            'test',
            'primaryTest',
            'secondaryTest',
            'creator'
        ])
            ->where('tr04_reference_id', $id)
            ->where('m04_ro_id', Session::get('ro_id'))
            ->where('tr07_result_status', 'VERIFIED')
            ->where('tr07_is_current', 'YES')
            ->orderBy('m12_test_number')
            ->orderBy('m16_primary_test_id')
            ->orderBy('m17_secondary_test_id')
            ->orderBy('tr07_test_date', 'desc')
            ->get();

        // Get custom fields for this sample
        $customFields = CustomField::with(['test', 'primaryTest', 'secondaryTest'])
            ->where('tr04_reference_id', $id)
            ->where('tr08_result_status', 'VERIFIED')
            ->orderBy('m12_test_number')
            ->orderBy('m16_primary_test_id')
            ->orderBy('m17_secondary_test_id')
            ->get();

        if ($testResults->isEmpty() && $customFields->isEmpty()) {
            abort(404, 'No test results found for this sample.');
        }

        // Group test results by test number
        $groupedResults = $testResults->groupBy('m12_test_number');

        // Group custom fields by test number
        $groupedCustomFields = $customFields->groupBy('m12_test_number');

        $sampleInfo = $testResults->first() ?? $customFields->first();
        $totalTests = $groupedResults->count();

        $statusCounts = $testResults
            ->groupBy('tr07_result_status')
            ->map->count();

        return view('test-results.show_test_result', compact(
            'groupedResults',
            'groupedCustomFields',
            'testResults',
            'customFields',
            'sampleInfo',
            'totalTests',
            'statusCounts'
        ));
    }

    public function viewCompletedTests()
    {
        $samples = SampleTest::with(['registration', 'test', 'registration.testResult'])
            ->whereNotIn('tr05_status', ['TRANSFERRED'])
            ->when(Session::get('role') !== 'ADMIN', function ($query) {
                $query->where('m04_ro_id', Session::get('ro_id'));
            })
            ->whereDoesntHave('registration.testResult') // fetch only those without test results
            ->select('tr04_sample_registration_id')
            ->selectRaw("
            COUNT(*) as total_tests,
            SUM(CASE WHEN tr05_status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_tests,
            SUM(CASE WHEN tr05_status != 'COMPLETED' THEN 1 ELSE 0 END) as pending_tests
        ")
            ->groupBy('tr04_sample_registration_id')
            ->get()
            ->map(function ($sample) {
                $registration = $sample->registration;
                $sample->reference_id = $registration?->tr04_reference_id ?? '-';
                $sample->sample_id = $registration?->tr04_sample_registration_id ?? '-';
                $sample->priority = $registration?->tr04_sample_type ?? 'NORMAL';
                $sample->created_at = $registration?->created_at;
                $sample->delay_days = $registration
                    ? round(abs(now()->floatDiffInDays($registration->created_at)), 2)
                    : null;
                return $sample;
            })
            ->sortByDesc(function ($s) {
                return [
                    $s->pending_tests == 0 ? 1 : 0,
                    $s->priority == 'Tatkal' ? 1 : 0,
                    $s->delay_days,
                ];
            })
            ->values();

        return view('measurement.simple.measurements', compact('samples'));
    }


    /**
     * View specific version
     */
    public function viewVersion($id, $versionNumber)
    {
        $testResult = TestResult::with(['versions' => function ($query) use ($versionNumber) {
            $query->where('tr07_version_number', $versionNumber);
        }])->findOrFail($id);

        $version = $testResult->versions->first();

        if (!$version) {
            return redirect()->route('test-results.show', $id)
                ->with('error', 'Version not found.');
        }

        return view('test-results.test_result_version', compact('testResult', 'version'));
    }

    // public function generateReport($sampleId)
    // {
    //     $sample = SampleRegistration::with([
    //         'labSample',
    //         'testResult' => function ($q) {
    //             $q->where('tr07_is_current', 'YES')
    //                 ->where('tr07_result_status', 'VERIFIED')
    //                 ->with(['test.standard', 'manuscript']);
    //         }
    //     ])
    //         ->where('tr04_reference_id', $sampleId)
    //         ->where('m04_ro_id', Session::get('ro_id'))
    //         ->firstOrFail();
    //     $groupedResults = $sample->testResult->groupBy('m12_test_number');

    //     // try to find existing current report
    //     $report = TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
    //         ->where('tr09_is_current', 'YES')
    //         ->first();

    //     $meta = [
    //         'customer_name'     => $sample->parties['customer']['name'],
    //         'customer_address'  => $sample->parties['customer']['address'] . ' , ' . $sample->parties['customer']['district'] . ' , ' . $sample->parties['customer']['state'] . ' , ' . $sample->parties['customer']['pincode'],
    //         'report_no'         => $sample->tr04_reference_id,
    //         'date'              => optional($report)->tr09_generated_at ? Carbon::parse($report->tr09_generated_at)->format('d M Y') : now()->format('d M Y'),
    //         'reference'        => $sample->tr04_reference_no ?? '_',
    //         'reference_date'   => Carbon::parse($sample->tr04_reference_date)->format('d M Y')  ?? '_',
    //         'buyer'             => $sample->parties['buyer']['name'] ?? '_',
    //         'sample_description' => $sample->tr04_sample_description ?? '_',
    //         'be_no'             => $sample->tr04_be_no ?? '_',
    //         'sample_characteristics'     => $sample->labSample->m14_name ?? '_',
    //         'test_performance_date'      => Carbon::parse($sample->testResult[0]->tr07_performance_date)->format('d M Y')  ?? '_',
    //     ];

    //     if (!$report) {
    //         // assemble JSON summary used for archiving
    //         $testsData = [];
    //         foreach ($groupedResults as $testNumber => $results) {
    //             $parent = $results->first();
    //             $entry = [
    //                 'test_number' => $parent->m12_test_number,
    //                 'test_name'   => $parent->test->m12_name ?? null,
    //                 'version'     => $parent->tr07_current_version,
    //                 'result'      => $parent->tr07_result,
    //                 'manuscripts' => [],
    //             ];

    //             foreach ($results as $res) {
    //                 if ($res->manuscript) {
    //                     $entry['manuscripts'][] = [
    //                         'manuscript_id' => $res->manuscript->m22_manuscript_id,
    //                         'name'          => $res->manuscript->m22_name,
    //                         'result'        => $res->tr07_result,
    //                     ];
    //                 }
    //             }
    //             $testsData[] = $entry;
    //         }

    //         // store
    //         $latestVersion = TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
    //             ->max('tr09_version_number');
    //         $nextVersion = $latestVersion ? $latestVersion + 1 : 1;

    //         $report = TestReport::create([
    //             'm04_ro_id' => $sample->m04_ro_id,
    //             'tr04_reference_id' => $sample->tr04_reference_id,
    //             'tr09_version_number' => $nextVersion,
    //             'tr09_report_data' => json_encode(['tests' => $testsData]),
    //             'tr09_report_file_path' => '',
    //             'm06_generated_by' => Session::get('user_id'),
    //             'tr09_generated_at' => now(),
    //             'tr09_status' => 'FINAL',
    //             'tr09_is_current' => 'YES',
    //         ]);

    //         // === generate PDF ===
    //         $preprinted = true;

    //         $pdf = Pdf::loadView('reports.final_report_pdf', compact('sample', 'groupedResults', 'report', 'meta', 'preprinted'))
    //             ->setPaper('A4', 'portrait');
    //         $pdf->setOptions(['isPhpEnabled' => true]);
    //         $fileName = 'report_' . $sample->tr04_reference_id . '_' . now()->timestamp . '.pdf';
    //         $pdfPath = 'reports/' . $fileName;
    //         $fullPath = storage_path('app/public/' . $pdfPath);
    //         if (!file_exists(dirname($fullPath))) mkdir(dirname($fullPath), 0755, true);
    //         $pdf->save($fullPath);
    //         $report->update(['tr09_report_file_path' => $pdfPath]);
    //     }
    //     if ($report) {
    //         return view('reports.final_report', compact('sample', 'groupedResults', 'report', 'meta'));
    //     }
    //     abort(404, 'Report not found.');
    // }

    /**
     * Show audit trail
     */

    public function generateReport($sampleId)
    {

        $sample = SampleRegistration::with([
            'labSample',
            'testResult' => function ($q) {
                $q->where('tr07_is_current', 'YES')
                    ->where('tr07_result_status', 'VERIFIED')
                    ->with(['test', 'primaryTest', 'secondaryTest']);
            }
        ])
            ->where('tr04_reference_id', $sampleId)
            ->where('m04_ro_id', Session::get('ro_id'))
            ->firstOrFail();

        // Get custom fields
        $customFields = CustomField::with(['test', 'primaryTest', 'secondaryTest'])
            ->where('tr04_reference_id', $sampleId)
            ->where('tr08_result_status', 'VERIFIED')
            ->orderBy('m12_test_number')
            ->orderBy('m16_primary_test_id')
            ->orderBy('m17_secondary_test_id')
            ->get();

        // Group results
        $groupedResults = $sample->testResult->groupBy('m12_test_number');
        $groupedCustomFields = $customFields->groupBy('m12_test_number');
        // dd($groupedCustomFields);
        // Get or create order from session
        $orderKey = 'report_order_' . $sampleId;
        $orderedItems = Session::get($orderKey, []);

        // If no order in session, create default order
        if (empty($orderedItems)) {
            $orderedItems = [];
            foreach ($groupedResults as $testNumber => $results) {
                $orderedItems[] = [
                    'type' => 'test',
                    'test_number' => $testNumber,
                    'sort_order' => count($orderedItems)
                ];
            }

            // Add custom fields that don't have main test results
            foreach ($groupedCustomFields as $testNumber => $fields) {
                if (!isset($groupedResults[$testNumber])) {
                    $orderedItems[] = [
                        'type' => 'custom',
                        'test_number' => $testNumber,
                        'sort_order' => count($orderedItems)
                    ];
                }
            }
            Session::put($orderKey, $orderedItems);
        }

        $meta = [
            'customer_name'     => $sample->parties['customer']['name'],
            'customer_address'  => $sample->parties['customer']['address'] . ' , ' . $sample->parties['customer']['district'] . ' , ' . $sample->parties['customer']['state'] . ' , ' . $sample->parties['customer']['pincode'],
            'report_no'         => $sample->tr04_reference_id,
            'date'              => now()->format('d M Y'),
            'reference'         => $sample->tr04_reference_no ?? '_',
            'reference_date'    => Carbon::parse($sample->tr04_reference_date)->format('d M Y') ?? '_',
            'buyer'             => $sample->parties['buyer']['name'] ?? '_',
            'sample_description' => $sample->tr04_sample_description ?? '_',
            'be_no'             => $sample->tr04_be_no ?? '_',
            'sample_characteristics' => $sample->labSample->m14_name ?? '_',
            'test_performance_date'  => $sample->testResult->first() ? Carbon::parse($sample->testResult->first()->tr07_performance_date)->format('d M Y') : now()->format('d M Y'),
        ];

        // Check if we're generating PDF
        if (request()->has('generate_pdf')) {
            return $this->generatePdfReport($sample, $groupedResults, $groupedCustomFields, $orderedItems, $meta);
        }

        // Show reorder view
        return view('reports.final_report', compact(
            'sample',
            'groupedResults',
            'groupedCustomFields',
            'orderedItems',
            'meta'
        ));
    }

    private function generatePdfReport($sample, $groupedResults, $groupedCustomFields, $orderedItems, $meta)
    {
        Log::info("=== Generating PDF Report for Sample: {$sample->tr04_reference_id} ===");

        $paymentBy = strtolower($sample->tr04_payment_by);
        $selectedCustomerId = null;

        // Identify which customer to use based on "Payment By"
        switch ($paymentBy) {
            case 'first_party':
                $selectedCustomerId = $sample->m07_customer_id;
                break;
            case 'second_party':
                $selectedCustomerId = $sample->m07_buyer_id;
                break;
            case 'third_party':
                $selectedCustomerId = $sample->m07_third_party_id;
                break;
            case 'cha':
                $selectedCustomerId = $sample->m07_cha_id;
                break;
            default:
                $selectedCustomerId = null;
                break;
        }

        // Step 1: Check existing report
        $report = TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
            ->where('tr09_is_current', 'YES')
            ->first();

        if ($report) {
            Log::info("Existing report found", [
                'id' => $report->id ?? null,
                'file_path' => $report->tr09_report_file_path,
                'version' => $report->tr09_version_number,
            ]);
        } else {
            Log::info("No existing report found — creating a new one.");
        }

        // Step 2: Proceed only if new or file path missing
        if (!$report || empty($report->tr09_report_file_path)) {
            Log::info("Proceeding to create report record and generate PDF...");
            $this->releaseHoldAndDebit($selectedCustomerId, $sample);
            $testsData = [];

            foreach ($orderedItems as $item) {
                Log::info("Processing item", ['type' => $item['type'], 'test_number' => $item['test_number']]);

                if ($item['type'] === 'test') {
                    $testNumber = $item['test_number'];
                    $results = $groupedResults[$testNumber] ?? collect();
                    Log::info("Found " . $results->count() . " result(s) for test number {$testNumber}");

                    if ($results->isNotEmpty()) {
                        $parent = $results->first();
                        $entry = [
                            'type' => 'test',
                            'test_number' => $testNumber,
                            'test_name' => $parent->test->m12_name ?? null,
                            'version' => $parent->tr07_current_version,
                            'primary_tests' => []
                        ];

                        foreach ($results->groupBy('m16_primary_test_id') as $primaryTestId => $primaryResults) {
                            $primaryTest = $primaryResults->first()->primaryTest;
                            Log::info("Processing primary test", ['id' => $primaryTestId]);

                            if ($primaryTest) {
                                $primaryEntry = [
                                    'primary_test_id' => $primaryTestId,
                                    'primary_test_name' => $primaryTest->m16_name,
                                    'secondary_tests' => [],
                                    'result' => null
                                ];

                                $hasSecondaryTests = false;
                                foreach ($primaryResults as $result) {
                                    if ($result->m17_secondary_test_id) {
                                        $hasSecondaryTests = true;
                                        $primaryEntry['secondary_tests'][] = [
                                            'secondary_test_id' => $result->m17_secondary_test_id,
                                            'secondary_test_name' => $result->secondaryTest->m17_name ?? 'Secondary Test',
                                            'result' => $result->tr07_result
                                        ];
                                    }
                                }

                                if (!$hasSecondaryTests && $primaryResults->first()->tr07_result) {
                                    $primaryEntry['result'] = $primaryResults->first()->tr07_result;
                                }

                                $entry['primary_tests'][] = $primaryEntry;
                            }
                        }

                        if (empty($entry['primary_tests']) && $parent->tr07_result) {
                            $entry['result'] = $parent->tr07_result;
                        }

                        $testsData[] = $entry;
                    }
                } else {
                    // Custom fields
                    $testNumber = $item['test_number'];
                    $customFields = $groupedCustomFields[$testNumber] ?? collect();
                    Log::info("Found " . $customFields->count() . " custom field(s) for test number {$testNumber}");

                    if ($customFields->isNotEmpty()) {
                        $entry = [
                            'type' => 'custom',
                            'test_number' => $testNumber,
                            'custom_fields' => []
                        ];

                        foreach ($customFields as $customField) {
                            $entry['custom_fields'][] = [
                                'field_name' => $customField->tr08_field_name,
                                'field_value' => $customField->tr08_field_value,
                                'field_unit' => $customField->tr08_field_unit
                            ];
                        }

                        $testsData[] = $entry;
                    }
                }
            }

            Log::info("Total test/custom entries prepared: " . count($testsData));

            // Step 3: Create report record
            $latestVersion = TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
                ->max('tr09_version_number');
            $nextVersion = $latestVersion ? $latestVersion + 1 : 1;

            Log::info("Creating report record with version {$nextVersion}");

            $report = TestReport::create([
                'm04_ro_id' => $sample->m04_ro_id,
                'tr04_reference_id' => $sample->tr04_reference_id,
                'tr09_version_number' => $nextVersion,
                'tr09_report_data' => json_encode(['tests' => $testsData]),
                'tr09_report_file_path' => '',
                'm06_generated_by' => Session::get('user_id'),
                'tr09_generated_at' => now(),
                'tr09_status' => 'FINAL',
                'tr09_is_current' => 'YES',
            ]);

            Log::info("Report record created successfully with ID: {$report->id}");

            // Step 4: Generate PDF
            Log::info("Generating PDF file...");
            $pdf = Pdf::loadView('reports.final_report_pdf', compact(
                'sample',
                'groupedResults',
                'groupedCustomFields',
                'orderedItems',
                'meta',
                'report'
            ))->setPaper('A4', 'portrait');

            $pdf->setOptions(['isPhpEnabled' => true]);
            $fileName = 'report_' . $sample->tr04_reference_id . '_' . now()->timestamp . '.pdf';
            $pdfPath = 'reports/' . $fileName;
            $fullPath = storage_path('app/public/' . $pdfPath);

            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
                Log::info("Created directory: " . dirname($fullPath));
            }

            try {
                $pdf->save($fullPath);
                $report->update(['tr09_report_file_path' => $pdfPath]);
                Log::info("PDF saved and report updated with file path: {$pdfPath}");
            } catch (\Exception $e) {
                Log::error("PDF generation failed: " . $e->getMessage());
            }
        }

        // Step 5: Return or error
        if (!empty($report->tr09_report_file_path)) {
            Log::info("Report ready for download: {$report->tr09_report_file_path}");
            return response()->download(storage_path('app/public/' . $report->tr09_report_file_path));
        }

        Log::error("Report file not found for sample {$sample->tr04_reference_id}");
        abort(404, 'Report not found.');
    }

    public function releaseHoldAndDebit($customerId, $sample)
    {
        DB::beginTransaction();
        try {
            // Lock wallet record for safe update
            $wallet = Wallet::where('m07_customer_id', $customerId)->lockForUpdate()->first();

            if (!$wallet) {
                throw new \Exception("Wallet not found for customer ID: {$customerId}");
            }

            // Find existing HOLD transaction for this sample
            $heldTransaction = WalletTransaction::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                ->where('tr03_type', 'HOLD')
                ->where('tr03_status', 'PENDING')
                ->first();

            if (!$heldTransaction) {
                throw new \Exception("No pending HOLD transaction found for sample {$sample->tr04_reference_id}");
            }

            // Release hold amount and deduct from wallet balance
            $wallet->tr02_hold_amount -= $heldTransaction->tr03_amount;
            $wallet->tr02_balance -= $heldTransaction->tr03_amount;
            $wallet->save();

            // Update HOLD transaction status
            $heldTransaction->update([
                'tr03_status' => 'RELEASED',
                'tr03_description' => 'Hold released after report generation',
                'updated_at' => now(),
            ]);

            // Create DEBIT transaction
            $transactionCount = WalletTransaction::count();
            $debitTransaction = WalletTransaction::create([
                'tr03_transaction_uuid' => 'TXN-' . date('Y') . '-' . str_pad($transactionCount + 1, 4, '0', STR_PAD_LEFT),
                'tr02_wallet_id' => $wallet->tr02_wallet_id,
                'tr03_type' => 'DEBIT',
                'tr03_amount' => $heldTransaction->tr03_amount,
                'tr03_currency' => 'INR',
                'tr03_description' => 'Final deduction after report generation',
                'tr04_sample_registration_id' => $sample->tr04_sample_registration_id,
                'tr03_invoice_number' => $heldTransaction->tr03_invoice_number,
                'tr03_balance_before' => $wallet->tr02_balance + $heldTransaction->tr03_amount, // before debit
                'tr03_balance_after' => $wallet->tr02_balance,
                'tr03_status' => 'COMPLETED',
                'm07_created_by' => $customerId,
            ]);

            DB::commit();
            Log::info("✅ Wallet updated: HOLD released & DEBIT done for Sample {$sample->tr04_reference_id}");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Wallet transaction failed for Sample {$sample->tr04_reference_id}: " . $e->getMessage());
            return false;
        }
    }




    public function moveTestUp($sampleId, $index)
    {
        return $this->moveTest($sampleId, $index, 'up');
    }

    public function moveTestDown($sampleId, $index)
    {
        return $this->moveTest($sampleId, $index, 'down');
    }

    private function moveTest($sampleId, $index, $direction)
    {
        $orderKey = 'report_order_' . $sampleId;
        $orderedItems = Session::get($orderKey, []);

        if ($direction === 'up' && $index > 0) {
            // Swap with previous item
            $temp = $orderedItems[$index - 1];
            $orderedItems[$index - 1] = $orderedItems[$index];
            $orderedItems[$index] = $temp;
        } elseif ($direction === 'down' && $index < count($orderedItems) - 1) {
            // Swap with next item
            $temp = $orderedItems[$index + 1];
            $orderedItems[$index + 1] = $orderedItems[$index];
            $orderedItems[$index] = $temp;
        }

        Session::put($orderKey, $orderedItems);

        return redirect()->route('generate_report', ['id' => $sampleId]);
    }
    public function audit(Request $request)
    {
        // $query = TestResultAudit::with(['testResult', 'user', 'version'])
        //     ->orderBy('tr07_created_at', 'desc');

        // if ($request->filled('test_result_id')) {
        //     $query->where('tr07_test_result_id', $request->test_result_id);
        // }

        // if ($request->filled('action')) {
        //     $query->where('tr07_action', $request->action);
        // }

        // if ($request->filled('user_id')) {
        //     $query->where('tr07_user_id', $request->user_id);
        // }

        // if ($request->filled('date_from')) {
        //     $query->where('tr07_created_at', '>=', $request->date_from . ' 00:00:00');
        // }

        // if ($request->filled('date_to')) {
        //     $query->where('tr07_created_at', '<=', $request->date_to . ' 23:59:59');
        // }

        // $auditLogs = $query->paginate(20);

        return view('test-results.audit_test_result', compact('auditLogs'));
    }

    // Test formaula template
    public function getTestTemplate()
    {
        $tests = DB::table('m12_tests')
            ->whereNotIn('m12_test_id', function ($q) {
                $q->select('m12_test_id')->from('tr08_test_templates');
            })
            ->get();
        return view('test-results.create_test_template', compact('tests'));
    }

    /**
     * Compare versions
     */
    public function compareVersions($id, Request $request)
    {
        $request->validate([
            'version1' => 'required|integer',
            'version2' => 'required|integer'
        ]);

        $testResult = TestResult::findOrFail($id);

        $version1 = $testResult->versions()->where('tr07_version_number', $request->version1)->first();
        $version2 = $testResult->versions()->where('tr07_version_number', $request->version2)->first();

        if (!$version1 || !$version2) {
            return back()->with('error', 'One or both versions not found.');
        }

        return view('test-results.compare_test_result', compact('testResult', 'version1', 'version2'));
    }
}
