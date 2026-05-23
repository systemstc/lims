<?php

namespace App\Http\Controllers;

use App\Models\SampleRegistration;
use App\Models\TestReport;
use App\Models\TestTransfer;
use App\Models\SampleTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SampleTransferController extends Controller
{
    public function viewTransferredSamples(Request $request)
    {
        $roId = Session::get('ro_id');

        // Fetch transfer history where FROM RO is current RO
        $transferredTests = TestTransfer::join('tr05_sample_tests as st', 'tr06_test_transfers.tr05_sample_test_id', '=', 'st.tr05_sample_test_id')
            ->join('m04_ros as to_ro', 'tr06_test_transfers.m04_to_ro_id', '=', 'to_ro.m04_ro_id')
            ->join('m12_tests as t', 'st.m12_test_id', '=', 't.m12_test_id')
            ->join('tr04_sample_registrations as reg', 'st.tr04_sample_registration_id', '=', 'reg.tr04_sample_registration_id')
            ->leftJoin('m07_customers as cust', 'reg.m07_customer_id', '=', 'cust.m07_customer_id')
            ->where('tr06_test_transfers.m04_from_ro_id', $roId)
            ->select(
                'st.tr04_sample_registration_id',
                'reg.tr04_reference_id',
                'cust.m07_name as m07_customer_name',
                'to_ro.m04_name as to_ro_name',
                'to_ro.m04_ro_id as to_ro_id',
                't.m12_name as test_name',
                'st.tr05_status', // This is the OLD sample status (TRANSFERRED)
                'tr06_test_transfers.tr06_transferred_at',
                'tr06_test_transfers.tr06_remark', // Contains linkage to NEW sample
                'tr06_test_transfers.m06_received_by',
                'tr06_test_transfers.tr06_status as transfer_status'
            )
            ->orderBy('tr06_test_transfers.tr06_transferred_at', 'desc')
            ->get();

        // Group by Reference ID
        $groupedSamples = $transferredTests->groupBy('tr04_reference_id')->map(function ($tests) use ($roId) {
            $first = $tests->first();

            // Try to find the NEW sample from linkage
            $newSampleId = null;
            $newStatus = 'Transferred';
            $isCompleted = false;
            $report = null;

            // Check if cancelled via DB status or legacy string fallback
            if ($first->transfer_status === 'CANCELLED' || str_contains((string)$first->tr06_remark, '[CANCELLED BY SENDER]')) {
                $newStatus = 'Cancelled';
            } else {
                // Look for linkage in remark
                $linkData = json_decode($first->tr06_remark, true);
                if ($linkData && isset($linkData['new_sample_id'])) {
                    $newSampleId = $linkData['new_sample_id'];

                    // Get status of the NEW sample
                    $linkedSample = SampleRegistration::find($newSampleId);
                    if ($linkedSample) {
                        $newStatus = $linkedSample->tr04_progress; // e.g., 'testing', 'completed'

                        // Check completion
                        $isCompleted = in_array($newStatus, ['completed', 'reported', 'verified']);

                        // Check for report on the NEW sample
                        // TestReport uses tr04_reference_id, not sample_registration_id
                        $report = TestReport::where('tr04_reference_id', $linkedSample->tr04_reference_id)
                            ->where('tr09_is_current', 'YES')
                            ->where('tr09_status', 'FINAL')
                            ->first();
                    }
                } else {
                    // Fallback to old logic or just check if received
                    if ($first->m06_received_by) {
                        $newStatus = 'Received (Processing)';
                    }
                }
            }


            return (object) [
                'tr04_sample_registration_id' => $first->tr04_sample_registration_id,
                'tr04_reference_id' => $first->tr04_reference_id,
                'customer_name' => $first->m07_customer_name,
                'to_ro_name' => $first->to_ro_name,
                'to_ro_id' => $first->to_ro_id,
                'tests' => $tests->pluck('test_name')->implode(', '),
                'statuses' => $newStatus, // Show status of the REMOTE sample
                'test_count' => $tests->count(),
                'transferred_at' => \Illuminate\Support\Carbon::parse($first->tr06_transferred_at),
                'report_available' => $report ? true : false,
                'report_id' => $report ? $report->tr09_test_report_id : null, // Use ID for URL generation
                'remote_sample_id' => $newSampleId, // Store for specific lookups if needed
                'is_completed' => $isCompleted || $report,
            ];
        });

        return view('transfer.view_transferred_samples', compact('groupedSamples'));
    }

    public function downloadRemoteReport($reportId)
    {
        try {
            // We receive the REPORT ID directly now (from the view logic above)
            // But we should verify we have rights to it.

            $roId = Session::get('ro_id');

            $report = TestReport::findOrFail($reportId);

            // Security Check: 
            // Is this report for a sample linked to a transfer FROM us?
            // TestReport links to SampleRegistration via tr04_reference_id
            $reportRefId = $report->tr04_reference_id;

            // We need the Primary Key (sample_id) because that is what is stored in the JSON linkage
            $linkedSampleReg = SampleRegistration::where('tr04_reference_id', $reportRefId)->first();
            $sampleId = $linkedSampleReg ? $linkedSampleReg->tr04_sample_registration_id : null;

            if (!$sampleId) {
                abort(404, "Linked sample registration not found.");
            }

            // Check if we transferred a test that LINKED to this sample
            // The link is in tr06_remark JSON -> new_sample_id
            $linkedTransfer = TestTransfer::where('m04_from_ro_id', $roId)
                ->where('tr06_remark', 'LIKE', '%"new_sample_id":' . $sampleId . '%')
                ->exists();

            if (!$linkedTransfer) {
                // Fallback: Check strictly by Reference ID if they are same (legacy)? 
                // Or if we are ADMIN? 
                if (Session::get('role') !== 'ADMIN') {
                    abort(403, "Unauthorized access to this report.");
                }
            }

            if (empty($report->tr09_report_file_path) || !file_exists(storage_path('app/public/' . $report->tr09_report_file_path))) {
                return back()->with('error', 'Report file not found on server.');
            }

            return response()->download(storage_path('app/public/' . $report->tr09_report_file_path));
        } catch (\Exception $e) {
            Log::error("Download Remote Report Error: " . $e->getMessage());
            return back()->with('error', 'Failed to download report: ' . $e->getMessage());
        }
    }

    public function cancelTransfer(Request $request)
    {
        $request->validate([
            'sample_registration_id' => 'required|exists:tr04_sample_registrations,tr04_sample_registration_id',
            'to_ro_id' => 'required|exists:m04_ros,m04_ro_id'
        ]);

        $roId = Session::get('ro_id');
        $sampleId = $request->sample_registration_id;
        $toRoId = $request->to_ro_id;

        try {
            DB::beginTransaction();

            // Fetch transferred tests for this sample that have not been accepted yet
            $testsToCancel = SampleTest::where('tr04_sample_registration_id', $sampleId)
                ->where('tr05_status', 'TRANSFERRED')
                ->where('m04_transferred_to', $toRoId)
                ->get();

            if ($testsToCancel->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No pending transfers found or already accepted by destination.']);
            }

            foreach ($testsToCancel as $test) {
                // Revert status to PENDING
                $test->update([
                    'tr05_status' => 'PENDING',
                    'm04_transferred_to' => null,
                    'm04_transferred_by' => null,
                    'tr05_transferred_at' => null
                ]);

                // Update transfer history record to mark as cancelled
                $transferRecord = TestTransfer::where('tr05_sample_test_id', $test->tr05_sample_test_id)
                    ->where('m04_from_ro_id', $roId)
                    ->where('m04_to_ro_id', $toRoId)
                    ->whereNull('m06_received_by') // Only if not received
                    ->orderBy('tr06_transferred_at', 'desc')
                    ->first();

                if ($transferRecord) {
                    $transferRecord->update([
                        'tr06_status' => 'CANCELLED',
                        'tr06_remark' => $transferRecord->tr06_remark ? $transferRecord->tr06_remark . ' [CANCELLED BY SENDER]' : '[CANCELLED BY SENDER]'
                    ]);
                }
            }

            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transfer cancelled successfully.']);
            }
            return back()->with('success', 'Transfer cancelled successfully. Tests have been returned to pending status.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel Transfer Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to cancel transfer: ' . $e->getMessage()]);
            }
            return back()->with('error', 'Failed to cancel transfer: ' . $e->getMessage());
        }
    }

    public function pullResult(Request $request)
    {
        // Deprecated/Replaced key logic. 
        // Keeping empty or basic return to avoid route errors if called.
        return response()->json(['success' => false, 'message' => 'Use download report instead.']);
    }
}
