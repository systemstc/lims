<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Test;
use App\Models\SampleRegistration;
use App\Models\TestResult;
use App\Models\CustomField;
use App\Models\PrimaryTest;
use App\Models\SecondaryTest;
use App\Models\Ro;
use App\Models\TestReport;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CumulativeReportController extends Controller
{
    /**
     * Display selection page.
     */
    public function index(Request $request)
    {
        $roId = Session::get('ro_id');
        
        $customers = Customer::where('m04_ro_id', $roId)
            ->where('m07_status', 'ACTIVE')
            ->orderBy('m07_name')
            ->get();

        // Get tests that have active records
        $tests = Test::where('m12_status', 'ACTIVE')
            ->orderBy('m12_name')
            ->get()
            ->unique('m12_test_number');

        return view('reports.cumulative.index', compact('customers', 'tests'));
    }

    /**
     * Preview cumulative report page - select samples, add metadata.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'test_number' => 'required',
        ]);

        $customerId = $request->customer_id;
        $testNumber = $request->test_number;
        $roId = Session::get('ro_id');

        $customer = Customer::findOrFail($customerId);
        $test = Test::where('m12_test_number', $testNumber)->firstOrFail();

        // Get registrations for this customer that have VERIFIED/REPORTED results for this test
        $registrations = SampleRegistration::with(['labSample', 'customer', 'ro'])
            ->where('m07_customer_id', $customerId)
            ->where('m04_ro_id', $roId)
            ->whereHas('testResult', function ($query) use ($testNumber) {
                $query->where('m12_test_number', $testNumber)
                    ->whereIn('tr07_result_status', ['VERIFIED', 'REPORTED'])
                    ->where('tr07_is_current', 'YES')
                    ->active();
            })
            ->orderBy('tr04_sample_registration_id', 'asc')
            ->get();

        if ($registrations->isEmpty()) {
            return redirect()->back()->with('error', 'No samples with verified test results found for the selected customer and test.');
        }

        // Try to estimate some default values based on first sample
        $firstSample = $registrations->first();
        
        // Extract financial year and reference details
        $referenceNo = $firstSample->tr04_reference_no ?? '';
        $referenceDate = $firstSample->tr04_reference_date ? Carbon::parse($firstSample->tr04_reference_date)->format('Y-m-d') : '';
        $receivedDate = $firstSample->created_at ? Carbon::parse($firstSample->created_at)->format('Y-m-d') : '';
        
        // Get the test performance date
        $performanceDateStart = '';
        $performanceDateEnd = '';
        $results = TestResult::where('tr04_reference_id', $firstSample->tr04_reference_id)
            ->where('m12_test_number', $testNumber)
            ->where('tr07_is_current', 'YES')
            ->active()
            ->first();
        if ($results && $results->tr07_performance_date) {
            $performanceDateStart = Carbon::parse($results->tr07_performance_date)->format('Y-m-d');
        }

        return view('reports.cumulative.preview', compact(
            'customer',
            'test',
            'registrations',
            'referenceNo',
            'referenceDate',
            'receivedDate',
            'performanceDateStart',
            'performanceDateEnd'
        ));
    }

    /**
     * Generate PDF cumulative report.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'test_number' => 'required',
            'sample_ids' => 'required|array|min:1',
            'report_prefix' => 'required|string',
            'report_year' => 'required|string',
            'lab_sample_prefix' => 'required|string',
        ]);

        $customerId = $request->customer_id;
        $testNumber = $request->test_number;
        $sampleIds = $request->sample_ids;
        $roId = Session::get('ro_id');

        $customer = Customer::findOrFail($customerId);
        $test = Test::where('m12_test_number', $testNumber)->firstOrFail();

        // Get the selected registrations
        $registrations = SampleRegistration::with(['labSample', 'customer', 'ro'])
            ->where('m07_customer_id', $customerId)
            ->where('m04_ro_id', $roId)
            ->whereIn('tr04_sample_registration_id', $sampleIds)
            ->orderBy('tr04_sample_registration_id', 'asc')
            ->get();

        if ($registrations->isEmpty()) {
            return redirect()->back()->with('error', 'Selected samples not found.');
        }

        $firstReg = $registrations->first();
        $lastReg = $registrations->last();

        // Get T.R. numbers (sequence numbers from last 4 digits of reference_id)
        $firstTrNo = intval(substr($firstReg->tr04_reference_id, -4));
        $lastTrNo = intval(substr($lastReg->tr04_reference_id, -4));

        // Get Sample ID range
        $firstSampleNo = $firstReg->tr04_sample_registration_id;
        $lastSampleNo = $lastReg->tr04_sample_registration_id;

        // Build Report Number
        $reportNo = $request->report_prefix . $firstTrNo . '-' . $lastTrNo . '/' . $request->report_year;
        
        // Build Lab Sample Number Range
        $labSampleNoRange = $request->lab_sample_prefix . $firstSampleNo . '-' . $lastSampleNo;

        // Load primary tests associated with this test
        $primaryIds = explode(',', $test->m16_primary_test_id);
        $primaryTests = PrimaryTest::whereIn('m16_primary_test_id', $primaryIds)
            ->where('m16_status', 'ACTIVE')
            ->get();

        // Partition simple (no secondary tests) and nested (with secondary tests) parameters
        $simplePrimaries = [];
        $nestedPrimaries = [];
        $hasAccreditedTests = false;

        $roId = Session::get('ro_id');
        $accreditations = \App\Models\Accreditation::where('m04_ro_id', $roId)
            ->where('m21_is_accredited', 'YES')
            ->get(['m15_standard_id', 'm12_test_id']);

        foreach ($primaryTests as $pt) {
            $secondaries = $pt->secondaryTests()->where('m17_status', 'ACTIVE')->get();
            if ($secondaries->isNotEmpty()) {
                $nestedPrimaries[] = [
                    'primary' => $pt,
                    'secondaries' => $secondaries
                ];
            } else {
                $simplePrimaries[] = $pt;
            }
        }

        // Gather results for each sample
        $tableData = [];
        foreach ($registrations as $reg) {
            $refId = $reg->tr04_reference_id;
            
            // Get seal and unique code from inputs
            $sealNo = $request->input('seal_no.' . $reg->tr04_sample_registration_id) ?: '';
            $uniqueCode = $request->input('unique_code.' . $reg->tr04_sample_registration_id) ?: '';

            // Get test results for this registration and this test
            $results = TestResult::where('tr04_reference_id', $refId)
                ->where('m12_test_number', $testNumber)
                ->where('tr07_is_current', 'YES')
                ->active()
                ->get();

            // Check accreditation
            $sampleTest = $reg->sampleTests->firstWhere('m12_test_number', $testNumber);
            $standardId = $sampleTest->m15_standard_id ?? null;
            $testId = $test->m12_test_id ?? null;
            $isAcc = $accreditations->contains(function ($acc) use ($testId, $standardId) {
                return $acc->m12_test_id == $testId && $acc->m15_standard_id == $standardId;
            });
            if ($isAcc) {
                $hasAccreditedTests = true;
            }

            $parameterResults = [];

            // 1. Simple Primaries
            foreach ($simplePrimaries as $sp) {
                $resVal = $results->firstWhere('m16_primary_test_id', $sp->m16_primary_test_id);
                $parameterResults['simple_' . $sp->m16_primary_test_id] = $resVal ? $resVal->tr07_result : '-';
            }

            // 2. Nested Primaries
            foreach ($nestedPrimaries as $np) {
                foreach ($np['secondaries'] as $sec) {
                    $resVal = $results->where('m16_primary_test_id', $np['primary']->m16_primary_test_id)
                        ->firstWhere('m17_secondary_test_id', $sec->m17_secondary_test_id);
                    $parameterResults['nested_' . $np['primary']->m16_primary_test_id . '_' . $sec->m17_secondary_test_id] = $resVal ? $resVal->tr07_result : '-';
                }
            }

            $tableData[] = [
                'sample_registration_id' => $reg->tr04_sample_registration_id,
                'tr_no' => intval(substr($reg->tr04_reference_id, -4)),
                'seal_no' => $sealNo,
                'unique_code' => $uniqueCode,
                'results' => $parameterResults,
            ];
        }

        // Estimate receipt/performance date strings
        $performanceDateStr = '';
        if ($request->performance_date_start && $request->performance_date_end) {
            $performanceDateStr = Carbon::parse($request->performance_date_start)->format('d.m.Y') . ' to ' . Carbon::parse($request->performance_date_end)->format('d.m.Y');
        } elseif ($request->performance_date_start) {
            $performanceDateStr = Carbon::parse($request->performance_date_start)->format('d.m.Y') . ' to ' . Carbon::parse($request->date ?: now())->format('d.m.Y');
        } else {
            $performanceDateStr = Carbon::parse($firstReg->created_at)->format('d.m.Y') . ' to ' . Carbon::parse($lastReg->created_at)->format('d.m.Y');
        }

        $meta = [
            'report_no' => $reportNo,
            'date' => $request->date ? Carbon::parse($request->date)->format('d.m.Y') : now()->format('d.m.Y'),
            'customer_name' => $customer->m07_name,
            'customer_address' => $customer->m07_address . ' , ' . ($customer->district?->m02_district_name ?? '') . ' , ' . ($customer->state?->m01_state_name ?? '') . ' , ' . $customer->m07_pincode,
            'forwarding_letter' => $request->forwarding_letter_no_date ?: 'Nil Dt. ' . now()->format('d.m.Y'),
            'receipt_date' => $request->receipt_date ? Carbon::parse($request->receipt_date)->format('d.m.Y') : Carbon::parse($firstReg->created_at)->format('d.m.Y'),
            'performance_date' => $performanceDateStr,
            'sample_description' => $request->sample_description ?: ($firstReg->tr04_sample_description ?: ($firstReg->labSample->m14_name ?? '_')),
            'sample_colour' => $request->sample_colour ?: '_',
            'lab_sample_no_range' => $labSampleNoRange,
        ];

        // Fetch latest version or standard report reference for authorizer/signer
        $reportSigner = 'Manager';
        $report = TestReport::where('tr04_reference_id', $firstReg->tr04_reference_id)
            ->where('tr09_is_current', 'YES')
            ->first();
        if ($report && $report->generator) {
            $reportSigner = $report->generator->m06_name;
        }

        // Generate PDF
        $pdf = Pdf::loadView('reports.cumulative.pdf', compact(
            'customer',
            'test',
            'registrations',
            'simplePrimaries',
            'nestedPrimaries',
            'tableData',
            'meta',
            'reportSigner',
            'hasAccreditedTests',
            'firstReg'
        ))->setPaper('A4', 'portrait');

        $pdf->setOptions(['isPhpEnabled' => true]);

        return $pdf->stream('cumulative_report_' . str_replace('/', '_', $reportNo) . '.pdf');
    }
}
