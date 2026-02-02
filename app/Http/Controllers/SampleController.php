<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ro;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\Test;
use App\Models\TestTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SampleController extends Controller
{
    public function recentRecords(Request $request)
    {
        // Get only recent samples (last 5)
        $recentSamples = SampleRegistration::with('customer:m07_customer_id,m07_name')
            ->where('m04_ro_id', Session::get('ro_id'))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $records = [];

        foreach ($recentSamples as $sample) {
            $records[] = [
                'id' => $sample->tr04_sample_registration_id,
                'type' => 'Sample',
                'title' => ($sample->tr04_reference_id ? "#{$sample->tr04_reference_id}" : "Sample #{$sample->tr04_tracker_id}"),
                'subtitle' => $sample->tr04_sample_description ? substr($sample->tr04_sample_description, 0, 50) . '...' : 'No description',
                'customer' => $sample->customer?->m07_name ?? 'Unknown Customer',
                'date' => $sample->created_at?->diffForHumans() ?? '',
                'status' => $sample->tr04_status ?? 'pending',
                'created_at' => $sample->created_at
            ];
        }

        // Sort by creation time (most recent first)
        usort($records, function ($a, $b) {
            if (!$a['created_at'] || !$b['created_at']) return 0;
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        return response()->json($records);
    }


    /**
     * Get today's statistics
     */
    public function todayStats(Request $request)
    {
        $today = Carbon::today();
        $stats = [
            'today_samples' => SampleRegistration::where('m04_ro_id', Session::get('ro_id'))->whereDate('created_at', $today)->count(),
            'receivedFromRosCount' => SampleTest::where('m04_transferred_to', Session::get('ro_id'))->where('tr05_status', 'TRANSFERRED')
                ->distinct('tr04_sample_registration_id')
                ->count('tr04_sample_registration_id'),
            'pending_tests' => $this->getPendingTestsCountOptimized(),
            'weekly_samples' => SampleRegistration::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
        ];
        return response()->json($stats);
    }

    private function getPendingTestsCountOptimized()
    {
        return SampleTest::query()
            ->where('m04_ro_id', Session::get('ro_id'))
            ->whereIn('tr05_status', ['PENDING'])
            ->count();
    }


    public function getSampleDetails(Request $request)
    {
        $sampleId = $request->get('sample_id');

        // Using Eloquent with relationships - eager loading to prevent N+1 queries
        $sample = SampleRegistration::with([
            'customer:m07_customer_id,m07_name',
            'labSample:m14_lab_sample_id,m14_name'
        ])->find($sampleId);

        if (!$sample) {
            return response()->json(['error' => 'Sample not found'], 404);
        }

        // Parse JSON test details and get test information
        $tests = collect([]);
        if ($sample->m12_test_ids) {
            $testDetails = json_decode($sample->m12_test_ids, true);

            if ($testDetails && is_array($testDetails)) {
                $testIds = array_keys($testDetails);

                // Get tests with their standards
                $testsData = Test::with(['standard:m15_standard_id,m15_method'])
                    ->whereIn('m12_test_id', $testIds)
                    ->get()
                    ->keyBy('m12_test_id');

                $tests = collect($testDetails)->map(function ($detail, $testId) use ($testsData) {
                    $test = $testsData->get($testId);

                    return [
                        'id' => $testId,
                        'name' => $test?->m12_name ?? 'Unknown Test',
                        'charge' => $test?->m12_charge ?? 0,
                        'remark' => $detail['remark'] ?? '',
                        'standard_id' => $detail['standard_id'] ?? null,
                        'package_id' => $detail['package_id'] ?? null,
                        'standard_method' => $test?->standard?->m15_method ?? null,
                        'test_status' => $test?->m12_status ?? 'pending'
                    ];
                });
            }
        }

        $sampleData = [
            'id' => $sample->tr04_sample_registration_id,
            'reference_no' => $sample->tr04_reference_no,
            'reference_date' => $sample->tr04_reference_date,
            'description' => $sample->tr04_sample_description,
            'sample_type_id' => $sample->m14_lab_sample_id,
            'sample_type_name' => $sample->labSample?->m14_name,
            'priority' => $sample->tr04_sample_type,
            'test_type' => $sample->tr04_test_type,
            'status' => $sample->tr04_status,
            'customer_type' => $sample->customerType->m09_customer_type_id,
            'customer' => [
                'id' => $sample->customer?->m07_customer_id,
                'name' => $sample->customer?->m07_name
            ],
            'buyer' => [
                'id' => $sample->buyer?->m07_customer_id,
                'name' => $sample->buyer?->m07_name
            ],
            'payment_by' => $sample->tr04_payment_by,
            'report_to' => $sample->tr04_report_to,
            'received_via' => $sample->tr04_received_via,
            'details' => $sample->tr04_details,
            'department' => $sample->m13_department_id,
            'sample_numbers' => $sample->tr04_number_of_samples,
            'tests' => $tests->values()->toArray(),
            'testing_charges' => $sample->tr04_testing_charges,
            'additional_charges' => $sample->tr04_additional_charges,
            'total_charges' => $sample->tr04_total_charges,
        ];

        return response()->json($sampleData);
    }
    public function viewPedingSmples()
    {
        if (Session::get('role') == 'ADMIN') {
            $samples = SampleTest::with(['registration', 'transferredToRo', 'transferredBy'])
                ->whereIn('tr05_status', ['TRANSFERRED', 'RECEIVED_ACCEPTED'])
                ->orderBy('tr05_transferred_at', 'asc')
                ->get()
                ->groupBy('tr04_sample_registration_id')
                ->map(function ($group) {
                    return $group->first();
                })
                ->values();
        } else {
            $roId = Session::get('ro_id');
            $samples = SampleTest::with(['registration', 'transferredToRo', 'transferredBy'])
                ->where('m04_transferred_to', $roId)
                ->orderBy('tr05_transferred_at', 'asc')
                ->get()
                ->groupBy('tr04_sample_registration_id')
                ->map(function ($group) {
                    return $group->first();
                })
                ->values();
        }

        return view('registration.preRegistration.view_accept_pending', compact('samples'));
    }

    public function acceptTransferdSample($id)
    {
        try {
            $userId = Session::get('user_id') ?? -1;
            $roId = Session::get('ro_id');
            // Get current RO details to use for logic if needed
            $currentRo = Ro::find($roId);

            DB::transaction(function () use ($id, $roId, $userId, $currentRo) {
                // Find tests waiting for transfer acceptance
                // We lock the source tests but do not move them.
                $transferredTests = SampleTest::where('tr04_sample_registration_id', $id)
                    ->where('m04_transferred_to', $roId)
                    ->where('tr05_status', 'TRANSFERRED')
                    ->get();

                if ($transferredTests->isEmpty()) {
                    throw new \Exception('No pending tests available to accept for this registration.');
                }

                // 1. Identify Sender RO
                // All tests in this batch should come from the same RO (the owner of the sample)
                $firstTest = $transferredTests->first();
                $senderRoId = $firstTest->m04_ro_id;
                $senderRo = Ro::find($senderRoId);

                // 2. Find or Create Customer representing the Sender RO
                // We check if a customer exists with the name of the RO
                $customerIdx = Customer::where('m07_name', $senderRo->m04_name)->first();

                if (!$customerIdx) {
                    $customerIdx = Customer::create([
                        'm07_name' => $senderRo->m04_name,
                        'm07_cust_type' => 'RO', // Assuming this field exists or similar. If not, maybe use a default or 'OTHER'
                        'm07_status' => 'Active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 3. Create NEW Sample Registration at Destination RO
                // Clone details from original registration but override specifics
                $originalReg = SampleRegistration::find($id);

                // Use standard ID generation
                $newRefId = generateReferenceId(
                    $originalReg->m13_department_id,
                    $customerIdx->m09_customer_type_id ?? $originalReg->m09_customer_type_id,
                    $originalReg->m14_lab_sample_id
                );
                $newTrackerId = generateTrackerId($newRefId);

                $newReg = SampleRegistration::create([
                    'm04_ro_id' => $roId,
                    'tr04_reference_id' => $newRefId,
                    'tr04_tracker_id' => $newTrackerId,

                    'm07_customer_id' => $customerIdx->m07_customer_id, // The customer is the SENDER RO
                    'm09_customer_type_id' => $customerIdx->m09_customer_type_id ?? $originalReg->m09_customer_type_id,

                    'tr04_payment_status' => 'NOT_APPLICABLE', // Non-commercial
                    'tr04_progress' => 'REGISTERED',

                    'tr04_sample_type' => $originalReg->tr04_sample_type,
                    'tr04_sample_description' => $originalReg->tr04_sample_description . ' (Transferred from ' . $senderRo->m04_name . ')',
                    'tr04_received_via' => 'TRANSFER',

                    // Copy other relevant fields
                    'm13_department_id' => $originalReg->m13_department_id,
                    'm14_lab_sample_id' => $originalReg->m14_lab_sample_id,
                    'tr04_test_type' => $originalReg->tr04_test_type,

                    'tr04_created_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                foreach ($transferredTests as $test) {
                    // Update the TestTransfer record to mark as received and LINK to new sample
                    $transferRecord = TestTransfer::where('tr05_sample_test_id', $test->tr05_sample_test_id)
                        ->where('m04_to_ro_id', $roId)
                        ->whereNull('m06_received_by')
                        ->first();

                    if ($transferRecord) {
                        // 4. Create NEW Test Record
                        $newTest = SampleTest::create([
                            'tr04_sample_registration_id' => $newReg->tr04_sample_registration_id,
                            'm12_test_id' => $test->m12_test_id,
                            'm12_test_number' => $test->m12_test_number,
                            'm16_primary_test_id' => $test->m16_primary_test_id,
                            'm17_secondary_test_id' => $test->m17_secondary_test_id,
                            'm15_standard_id' => $test->m15_standard_id,

                            'm04_ro_id' => $roId, // Belongs to CURRENT RO

                            'tr05_status' => 'PENDING',
                            'tr05_priority' => $test->tr05_priority,

                            'tr05_accepted_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update Transfer Request
                        $transferRecord->update([
                            'm06_received_by' => $userId,
                            'tr06_received_at' => now(),
                            'tr06_remark' => json_encode([
                                'new_sample_id' => $newReg->tr04_sample_registration_id,
                                'new_test_id' => $newTest->tr05_sample_test_id,
                                'original_remark' => $transferRecord->tr06_remark
                            ])
                        ]);

                        // Update Original Test
                        // We change status to RECEIVED_ACCEPTED to prevent double acceptance
                        $test->update([
                            'tr05_status' => 'RECEIVED_ACCEPTED',
                            'tr05_accepted_at' => now()
                        ]);
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Transferred sample accepted. A new registration has been created in your RO.'
            ]);
        } catch (\Exception $e) {
            Log::error('Accept Transferred Sample Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function viewSampleStatus()
    {
        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay();

        $samples = SampleRegistration::with(['customer'])
            ->where('m04_ro_id', Session::get('ro_id'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('samples.view_sample_status', compact('samples'));
    }

    /**
     * Search sample by Tracker ID and redirect based on role
     */
    public function searchTracker(Request $request)
    {
        $trackerId = $request->input('tracker_id');
        if (!$trackerId) {
            return back()->with('message', 'Please enter a Tracker ID')->with('type', 'warning');
        }
        return $this->trackSample($trackerId);
    }

    /**
     * Track sample by ID and redirect based on role
     */
    public function trackSample($trackerId)
    {
        $sample = SampleRegistration::where('tr04_tracker_id', $trackerId)->first();

        if (!$sample) {
            return redirect()->route('dashboard')->with('message', 'Sample not found for Tracker ID: ' . $trackerId)->with('type', 'error');
        }

        $role = Session::get('role');
        $id = $sample->tr04_sample_registration_id;

        // Dispatch based on role
        // Analyst -> Analyst Dashboard / Test Views
        if ($role === 'Analyst') {
            // Check if Analyst has tests for this sample
            // The route view_sample_tests takes sampleId
            return redirect()->route('view_sample_tests', ['sampleId' => $id]);
        }

        // Verification Officer -> Verification View
        // Assuming role name, checking if they have access
        if ($role === 'Verification Officer' || $role === 'Jr.QA') {
            // The verification view takes report/sample ID? 
            // view_result_verification list all. verify_result/{id} verifies specific.
            // We can redirect to verify_result
            return redirect()->route('verify_result', ['id' => $sample->tr04_sample_registration_id]);
        }

        // Manager / Admin -> Full Details + Reports
        if ($role === 'Manager' || $role === 'Admin') {
            return redirect()->route('view_registration_pdf', ['id' => $id]);
        }

        // Registrar / DEO -> Registration Details
        if ($role === 'Registrar' || $role === 'DEO') {
            return redirect()->route('view_registration_pdf', ['id' => $id]);
        }

        // Default Fallback
        return redirect()->route('view_registration_pdf', ['id' => $id]);
    }
}
