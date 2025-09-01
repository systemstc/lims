<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestResult;
use App\Models\TestResultVersion;
use App\Models\TestResultAudit;
use App\Models\TestTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestResultController extends Controller
{

    /**
     * Display a listing of test results
     */
    public function index(Request $request)
    {
        $query = TestResult::with(['currentVersion', 'creator'])
            ->active()
            ->orderBy('tr07_created_at', 'desc');

        // Apply filters
        if ($request->filled('test_type')) {
            $query->byTestType($request->test_type);
        }

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
                $q->where('tr07_test_name', 'like', "%{$search}%")
                    ->orWhere('tr07_patient_id', 'like', "%{$search}%");
            });
        }

        $testResults = $query->paginate(15);

        // Get test types for filter
        $testTypes = TestResult::distinct()->pluck('tr07_test_type');

        return view('test-results.view_test_results', compact('testResults', 'testTypes'));
    }

    /**
     * Show the form for creating a new test result
     */
    public function create()
    {
        $templates = TestTemplate::active()->get();
        return view('test-results.create_test_result', compact('templates'));
    }

    /**
     * Store a newly created test result
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'test_type' => 'required|string|max:100',
            'test_name' => 'required|string|max:255',
            'test_date' => 'required|date',
            'findings' => 'required|string',
            'test_values' => 'nullable|array',
            'interpretation' => 'nullable|string',
            'recommendations' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create test result
            $testResult = TestResult::create([
                'tr07_patient_id' => $request->patient_id,
                'tr07_test_type' => $request->test_type,
                'tr07_test_name' => $request->test_name,
                'tr07_test_date' => $request->test_date,
                'tr07_created_by' => Auth::id()
            ]);

            // Create initial version
            TestResultVersion::create([
                'tr07_test_result_id' => $testResult->tr07_test_result_id,
                'tr07_version_number' => 1,
                'tr07_findings' => $request->findings,
                'tr07_test_values' => json_encode($request->test_values ?? []),
                'tr07_interpretation' => $request->interpretation,
                'tr07_recommendations' => $request->recommendations,
                'tr07_normal_ranges' => json_encode($request->normal_ranges ?? []),
                'tr07_abnormal_flags' => json_encode($request->abnormal_flags ?? []),
                'tr07_report_template' => $request->report_template ?? '',
                'tr07_change_reason' => 'Initial creation',
                'tr07_changed_by' => Auth::id(),
                'tr07_is_current' => 1
            ]);

            DB::commit();

            return redirect()->route('test-results.show', $testResult->tr07_test_result_id)
                ->with('success', 'Test result created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Test result creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create test result.');
        }
    }

    /**
     * Display the specified test result
     */
    public function show($id)
    {
        $testResult = TestResult::with(['currentVersion', 'versions.changedBy', 'creator', 'auditTrail.user'])
            ->findOrFail($id);

        return view('test-results.show_test_result', compact('testResult'));
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
    public function getTemplate($templateId)
    {
        $template = TestTemplate::findOrFail($templateId);
        return response()->json($template);
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
