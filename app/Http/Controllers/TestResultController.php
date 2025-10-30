<?php

namespace App\Http\Controllers;

use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\TestReport;
use Illuminate\Http\Request;
use App\Models\TestResult;
use App\Models\TestResultAudit;
use App\Models\TestTemplate;
use App\Models\TestTemplateParameter;
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

    public function createResult(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|string',
            'test_date' => 'required|date',
            'performance_date' => 'required|date',
            'test_data' => 'nullable|array',
            'manuscript_data' => 'nullable|array',
            'remarks' => 'nullable|string',
            'action' => 'required|string|in:DRAFT,SUBMITTED,RESULTED'
        ]);
        DB::beginTransaction();
        try {
            // Handle test data (tests without manuscripts)
            if (!empty($request->test_data)) {
                foreach ($request->test_data as $test) {
                    // Check if result_id exists (updating)
                    if (!empty($test['result_id'])) {
                        TestResult::where('tr07_test_result_id', $test['result_id'])
                            ->update([
                                'tr07_result' => $test['result'],
                                'tr07_result_status' => $request->action,
                                'tr07_test_date' => $request->test_date,
                                'tr07_performance_date' => $request->performance_date,
                                'tr07_remarks' => $request->remarks,
                                'm06_updated_by' => Session::get('user_id') ?? -1,
                                'tr07_updated_at' => now(),
                            ]);
                    } else {
                        // Creating new result
                        TestResult::create([
                            'm04_ro_id' => Session::get('ro_id'),
                            'tr04_reference_id' => $request->registration_id,
                            'm12_test_number' => $test['test_id'],
                            'm22_manuscript_id' => null,
                            'tr07_result' => $test['result'],
                            'tr07_test_date' => $request->test_date,
                            'tr07_performance_date' => $request->performance_date,
                            'tr07_remarks' => $request->remarks,
                            'tr07_current_version' => 1,
                            'tr07_result_status' => $request->action,
                            'm06_created_by' => Session::get('user_id') ?? -1,
                            'tr07_created_at' => now(),
                        ]);
                    }
                }
            }

            // Handle manuscript data (tests with manuscripts)
            if (!empty($request->manuscript_data)) {
                foreach ($request->manuscript_data as $testIndex => $manuscripts) {
                    foreach ($manuscripts as $manuscript) {
                        // Check if result_id exists (updating)
                        if (!empty($manuscript['result_id'])) {
                            TestResult::where('tr07_test_result_id', $manuscript['result_id'])
                                ->update([
                                    'tr07_result' => $manuscript['result'],
                                    'tr07_result_status' => $request->action,
                                    'tr07_test_date' => $request->test_date,
                                    'tr07_performance_date' => $request->performance_date,
                                    'tr07_remarks' => $request->remarks,
                                    'm06_updated_by' => Session::get('user_id') ?? -1,
                                    'tr07_updated_at' => now(),
                                ]);
                        } else {
                            // Creating new result
                            TestResult::create([
                                'm04_ro_id' => Session::get('ro_id'),
                                'tr04_reference_id' => $request->registration_id,
                                'm12_test_number' => $manuscript['test_id'],
                                'm22_manuscript_id' => $manuscript['manuscript_id'],
                                'tr07_result' => $manuscript['result'],
                                'tr07_test_date' => $request->test_date,
                                'tr07_performance_date' => $request->performance_date,
                                'tr07_remarks' => $request->remarks,
                                'tr07_current_version' => 1,
                                'tr07_result_status' => $request->action,
                                'm06_created_by' => Session::get('user_id') ?? -1,
                                'tr07_created_at' => now(),
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            $message = $request->action === 'DRAFT'
                ? 'Test results saved as draft successfully.'
                : 'Test results submitted successfully.';

            Session::flash('type', 'success');
            Session::flash('message', $message);
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving test results: ' . $e->getMessage());
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to save test results: ' . $e->getMessage());
            return back()->withInput();
        }
    }
    public function showSampleResult($id)
    {
        // Get all test results with relationships
        $testResults = TestResult::with(['manuscript', 'test', 'creator'])
            ->where('tr04_reference_id', $id)
            ->where('m04_ro_id', Session::get('ro_id'))
            ->where('tr07_result_status', 'VERIFIED')
            ->where('tr07_is_current', 'YES')
            ->orderBy('m12_test_number')
            ->orderBy('tr07_test_date', 'desc')
            ->get();
        if ($testResults->isEmpty()) {
            abort(404, 'No test results found for this sample.');
        }
        // Group by test number (parent)
        $groupedResults = $testResults->groupBy('m12_test_number');
        $sampleInfo = $testResults->first();

        $totalTests = $testResults->pluck('m12_test_number')->unique()->count();
        $statusCounts = $testResults
            ->groupBy('tr07_result_status')
            ->map->count();

        return view('test-results.show_test_result', compact('groupedResults', 'testResults', 'sampleInfo', 'totalTests', 'statusCounts'));
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

    public function generateReport($sampleId)
    {
        $sample = SampleRegistration::with([
            'labSample',
            'testResult' => function ($q) {
                $q->where('tr07_is_current', 'YES')
                    ->where('tr07_result_status', 'VERIFIED')
                    ->with(['test.standard', 'manuscript']);
            }
        ])
            ->where('tr04_reference_id', $sampleId)
            ->where('m04_ro_id', Session::get('ro_id'))
            ->firstOrFail();
        $groupedResults = $sample->testResult->groupBy('m12_test_number');

        // try to find existing current report
        $report = TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
            ->where('tr09_is_current', 'YES')
            ->first();

        $meta = [
            'customer_name'     => $sample->parties['customer']['name'],
            'customer_address'  => $sample->parties['customer']['address'] . ' , ' . $sample->parties['customer']['district'] . ' , ' . $sample->parties['customer']['state'] . ' , ' . $sample->parties['customer']['pincode'],
            'report_no'         => $sample->tr04_reference_id,
            'date'              => optional($report)->tr09_generated_at ? Carbon::parse($report->tr09_generated_at)->format('d M Y') : now()->format('d M Y'),
            'reference'        => $sample->tr04_reference_no ?? '_',
            'reference_date'   => Carbon::parse($sample->tr04_reference_date)->format('d M Y')  ?? '_',
            'buyer'             => $sample->parties['buyer']['name'] ?? '_',
            'sample_description' => $sample->tr04_sample_description ?? '_',
            'be_no'             => $sample->tr04_be_no ?? '_',
            'sample_characteristics'     => $sample->labSample->m14_name ?? '_',
            'test_performance_date'      => Carbon::parse($sample->testResult[0]->tr07_performance_date)->format('d M Y')  ?? '_',
        ];

        if (!$report) {
            // assemble JSON summary used for archiving
            $testsData = [];
            foreach ($groupedResults as $testNumber => $results) {
                $parent = $results->first();
                $entry = [
                    'test_number' => $parent->m12_test_number,
                    'test_name'   => $parent->test->m12_name ?? null,
                    'version'     => $parent->tr07_current_version,
                    'result'      => $parent->tr07_result,
                    'manuscripts' => [],
                ];

                foreach ($results as $res) {
                    if ($res->manuscript) {
                        $entry['manuscripts'][] = [
                            'manuscript_id' => $res->manuscript->m22_manuscript_id,
                            'name'          => $res->manuscript->m22_name,
                            'result'        => $res->tr07_result,
                        ];
                    }
                }
                $testsData[] = $entry;
            }

            // store
            $latestVersion = TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
                ->max('tr09_version_number');
            $nextVersion = $latestVersion ? $latestVersion + 1 : 1;

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

            // === generate PDF ===
            $preprinted = true;

            $pdf = Pdf::loadView('reports.final_report_pdf', compact('sample', 'groupedResults', 'report', 'meta', 'preprinted'))
                ->setPaper('A4', 'portrait');
            $pdf->setOptions(['isPhpEnabled' => true]);
            $fileName = 'report_' . $sample->tr04_reference_id . '_' . now()->timestamp . '.pdf';
            $pdfPath = 'reports/' . $fileName;
            $fullPath = storage_path('app/public/' . $pdfPath);
            if (!file_exists(dirname($fullPath))) mkdir(dirname($fullPath), 0755, true);
            $pdf->save($fullPath);
            $report->update(['tr09_report_file_path' => $pdfPath]);
        }
        if ($report) {
            return view('reports.final_report', compact('sample', 'groupedResults', 'report', 'meta'));
        }
        abort(404, 'Report not found.');
    }

    /**
     * Show audit trail
     */
    public function audit(Request $request)
    {
        $query = TestResultAudit::with(['testResult', 'user', 'version'])
            ->orderBy('tr07_created_at', 'desc');

        if ($request->filled('test_result_id')) {
            $query->where('tr07_test_result_id', $request->test_result_id);
        }

        if ($request->filled('action')) {
            $query->where('tr07_action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('tr07_user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('tr07_created_at', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $query->where('tr07_created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $auditLogs = $query->paginate(20);

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
