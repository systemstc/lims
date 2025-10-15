<?php

namespace App\Http\Controllers;

use App\Models\SampleTest;
use Illuminate\Http\Request;
use App\Models\TestResult;
use App\Models\TestResultAudit;
use App\Models\TestTemplate;
use App\Models\TestTemplateParameter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TestResultController extends Controller
{

    /**
     * Display a listing of test results
     */
    public function index(Request $request)
    {
        $query = TestResult::with('creator')
            ->active();

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('tr07_test_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('tr07_test_date', '<=', $request->date_to);
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
                DB::raw('GROUP_CONCAT(DISTINCT tr07_result_status) as statuses'),
                DB::raw('MAX(tr07_test_date) as last_test_date'),
                DB::raw('MAX(tr07_created_at) as last_created_at'),
                DB::raw('COUNT(*) as total_tests')
            )
            ->groupBy('tr04_reference_id')
            ->orderByDesc('last_created_at')
            ->get();

        $testResults = $query->paginate(15);
        return view('test-results.view_test_results', compact('testResults'));
    }

    /**
     * Show the form for creating a new test result
     */
    public function create()
    {
        $templates = TestTemplate::active()->get();
        return view('test-results.create_test_result', compact('templates'));
    }

    public function templateManuscript(Request $request, $id)
    {
        // Get manuscripts based on role
        if (Session::get('role') === 'Manager') {
            $manuscripts = SampleTest::with([
                'test',
                'manuscript',
                'registration',
                'registration.labSample',
                'allotedTo'
            ])->where('tr04_sample_registration_id', $id)->get();
        } else {
            $manuscripts = SampleTest::with([
                'test',
                'manuscript',
                'registration',
                'registration.labSample',
                'allotedTo',
                'allotedBy'
            ])
                ->where('tr04_sample_registration_id', $id)
                ->where('m06_alloted_to', Session::get('user_id'))
                ->whereIn('tr05_status', ['ALLOTED', 'IN_PROGRESS'])
                ->get();
        }

        // Get registration reference ID
        $registrationId = $manuscripts->first()->registration->tr04_reference_id ?? null;

        // Fetch existing test results (drafts or submitted)
        $existingResults = collect();
        $testDate = null;
        $performanceDate = null;
        $remarks = null;

        if ($registrationId) {
            $existingResults = TestResult::where('tr04_reference_id', $registrationId)
                ->whereIn('tr07_result_status', ['DRAFT', 'SUBMITTED'])
                ->get();

            // Get dates and remarks from first result if exists
            if ($existingResults->isNotEmpty()) {
                $firstResult = $existingResults->first();
                $testDate = $firstResult->tr07_test_date ?? null;
                $performanceDate = $firstResult->tr07_performance_date ?? null;
                $remarks = $firstResult->tr07_remarks ?? null;
            }
        }

        return view('manuscript.template_manuscript', compact(
            'manuscripts',
            'existingResults',
            'testDate',
            'performanceDate',
            'remarks'
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
            'action' => 'required|string|in:DRAFT,SUBMITTED'
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
        ->orderBy('m12_test_number')
        ->orderBy('tr07_test_date', 'desc')
        ->get();

    if ($testResults->isEmpty()) {
        abort(404, 'No test results found for this sample.');
    }

    // Group by test number (parent)
    $groupedResults = $testResults->groupBy('m12_test_number');

    $sampleInfo = $testResults->first();

    return view('test-results.show_test_result', compact('groupedResults', 'testResults', 'sampleInfo'));
}

    /**
     * Show the form for editing the specified test result
     */
    public function edit($id)
    {
        $testResult = TestResult::with('currentVersion')->findOrFail($id);

        if ($testResult->tr07_status === 'finalized') {
            return redirect()->route('test-results.show', $id)
                ->with('warning', 'Finalized results cannot be edited. Create a revision instead.');
        }

        $templates = TestTemplate::active()->get();

        return view('test-results.edit_test_result', compact('testResult', 'templates'));
    }

    /**
     * Update the specified test result
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'findings' => 'required|string',
            'test_values' => 'nullable|array',
            'interpretation' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'change_reason' => 'required|string|max:500'
        ]);

        $testResult = TestResult::findOrFail($id);

        if ($testResult->tr07_status === 'finalized') {
            return back()->with('error', 'Finalized results cannot be edited.');
        }

        try {
            $data = [
                'findings' => $request->findings,
                'test_values' => $request->test_values ?? [],
                'interpretation' => $request->interpretation,
                'recommendations' => $request->recommendations,
                'normal_ranges' => $request->normal_ranges ?? [],
                'abnormal_flags' => $request->abnormal_flags ?? [],
                'report_template' => $request->report_template ?? ''
            ];

            $testResult->createNewVersion($data, $request->change_reason);

            return redirect()->route('test-results.show', $id)
                ->with('success', 'Test result updated successfully. Version ' . $testResult->fresh()->tr07_current_version . ' created.');
        } catch (\Exception $e) {
            Log::error('Test result update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update test result.');
        }
    }

    /**
     * Finalize a test result
     */
    public function finalize(Request $request, $id)
    {
        $request->validate([
            'finalize_reason' => 'nullable|string|max:500'
        ]);

        $testResult = TestResult::findOrFail($id);

        if ($testResult->tr07_status === 'finalized') {
            return back()->with('warning', 'Test result is already finalized.');
        }

        try {
            $testResult->finalizeResult($request->finalize_reason);

            return redirect()->route('test-results.show', $id)
                ->with('success', 'Test result finalized successfully.');
        } catch (\Exception $e) {
            Log::error('Test result finalization failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to finalize test result.');
        }
    }

    /**
     * Create a revision of finalized result
     */
    public function revise(Request $request, $id)
    {
        $request->validate([
            'findings' => 'required|string',
            'test_values' => 'nullable|array',
            'interpretation' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'revision_reason' => 'required|string|max:500'
        ]);

        $testResult = TestResult::findOrFail($id);

        try {
            $data = [
                'findings' => $request->findings,
                'test_values' => $request->test_values ?? [],
                'interpretation' => $request->interpretation,
                'recommendations' => $request->recommendations,
                'normal_ranges' => $request->normal_ranges ?? [],
                'abnormal_flags' => $request->abnormal_flags ?? [],
                'report_template' => $request->report_template ?? ''
            ];

            $testResult->createNewVersion($data, $request->revision_reason);

            return redirect()->route('test-results.show', $id)
                ->with('success', 'Revision created successfully. Version ' . $testResult->fresh()->tr07_current_version . ' created.');
        } catch (\Exception $e) {
            Log::error('Test result revision failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create revision.');
        }
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

    /**
     * Generate and download report
     */
    public function generateReport($id, $versionNumber = null)
    {
        $testResult = TestResult::with('currentVersion')->findOrFail($id);

        if ($versionNumber) {
            $version = $testResult->versions()->where('tr07_version_number', $versionNumber)->first();
        } else {
            $version = $testResult->currentVersion;
        }

        if (!$version) {
            return back()->with('error', 'Version not found.');
        }

        // Generate report logic here
        return view('test-results.report', compact('testResult', 'version'));
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

    /**
     * Soft delete test result
     */
    public function destroy($id)
    {
        $testResult = TestResult::findOrFail($id);

        if ($testResult->tr07_status === 'finalized') {
            return back()->with('error', 'Finalized results cannot be deleted.');
        }

        $testResult->update(['tr07_is_active' => 0]);

        return redirect()->route('test_results')
            ->with('success', 'Test result deleted successfully.');
    }

    /**
     * Get template data via AJAX
     */
    public function getTestTemplate()
    {
        // dd('iufb');
        $tests = DB::table('m12_tests')
            ->whereNotIn('m12_test_id', function ($q) {
                $q->select('m12_test_id')->from('tr08_test_templates');
            })
            ->get();
        return view('test-results.create_test_template', compact('tests'));
    }

    public function createTestTemplate(Request $request)
    {
        $request->validate([
            'm12_test_id'        => 'required|integer',
            'tr08_test_type'     => 'required|string',
            'txt_test_performed' => 'required|integer|min:1',
            'txt_param_num'      => 'required|integer|min:1',
            'parameters'         => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Create Test Template
            $template = TestTemplate::create([
                'm12_test_id'             => $request->m12_test_id,
                'tr08_test_type'          => $request->tr08_test_type,
                'tr08_times_test_perform' => $request->txt_test_performed,
                'tr08_formula'            => $request->txt_main_test_formula ?? null,
                'tr08_fields_config'      => $request->parameters,
                'tr08_status'             => 'YES',
                'm06_created_by'          => Session::get('user_id'),
            ]);

            // Save each parameter
            foreach ($request->parameters as $param) {
                TestTemplateParameter::create([
                    'tr08_test_template_id' => $template->tr08_test_template_id,
                    'tr09_name'             => $param['name'],
                    'tr09_inputs'           => $param['inputs'],
                    'tr09_min'              => $param['min'],
                    'tr09_max'              => $param['max'],
                    'tr09_formula'          => $param['formula'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Test template created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create template: ' . $e->getMessage());
        }
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
