<?php

namespace App\Http\Controllers;

use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\Test;
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
            'receivedFromRosCount' => SampleTest::where('m04_transferred_to', Session::get('ro_id'))->where('tr05_status' , 'TRANSFERRED')
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
        $roId = Session::get('ro_id');
        $samples = SampleTest::with(['registration', 'transferredToRo'])
            ->where('m04_transferred_to', $roId)
            ->orderBy('tr05_transferred_at', 'asc')
            ->get()
            ->groupBy('tr04_sample_registration_id')
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        return view('registration.preRegistration.view_accept_pending', compact('samples'));
    }

    public function acceptTransferdSample($id)
    {
        try {
            $userId = Session::get('user_id') ?? -1;
            $roId = Session::get('ro_id');
            DB::transaction(function () use ($id, $roId, $userId) {
                $oldRegistration = SampleRegistration::findOrFail($id);

                $transferredTests = SampleTest::where('tr04_sample_registration_id', $id)
                    ->where('m04_transferred_to', $roId)
                    ->where('tr05_status', 'TRANSFERRED')
                    ->get();

                if ($transferredTests->isEmpty()) {
                    throw new \Exception('No pending tests available to accept for this registration.');
                }

                foreach ($transferredTests as $test) {
                    $test->tr05_status = 'RECEIVED_ACCEPTED';
                    $test->tr05_accepted_at = now();
                    $test->save();
                }

                $newRegistrationData = $oldRegistration->replicate()->toArray();
                unset($newRegistrationData['tr04_sample_registration_id']);

                $newRegistrationData['m04_ro_id'] = $roId;
                $newRegistrationData['tr04_reference_id'] = generateReferenceId($oldRegistration->m13_department_id);
                $newRegistrationData['tr04_tracker_id'] = generateTrackerId($newRegistrationData['tr04_reference_id']);
                $newRegistrationData['tr04_progress'] = 'REGISTERED';
                $newRegistrationData['tr04_created_by'] = $userId;
                $integerFields = [
                    'm09_customer_type_id',
                    'm07_customer_id',
                    'm08_customer_location_id',
                    'm07_buyer_id',
                    'm08_buyer_location_id',
                    'm07_third_party_id',
                    'm08_third_party_location_id',
                    'm07_cha_id',
                    'm08_cha_location_id'
                ];
                foreach ($integerFields as $field) {
                    $newRegistrationData[$field] = null;
                }

                $newRegistrationData['tr04_payment_by'] = 'first_party';
                $newRegistrationData['tr04_report_to'] = 'first_party';
                $newRegistrationData['tr04_reference_no'] = null;
                $newRegistrationData['tr04_reference_date'] = null;
                $newRegistrationData['tr04_received_via'] = 'by_post';
                $newRegistrationData['tr04_details'] = null;
                $newRegistrationData['tr04_testing_charges'] = 0;
                $newRegistrationData['tr04_additional_charges'] = 0;
                $newRegistrationData['tr04_total_charges'] = 0;
                $newRegistrationData['created_at'] = now();
                $newRegistrationData['updated_at'] = now();

                $newRegistration = SampleRegistration::create($newRegistrationData);

                // Clone transferred tests
                foreach ($transferredTests as $test) {
                    $newTest = $test->replicate();
                    unset($newTest->tr05_sample_test_id);
                    $newTest->tr04_sample_registration_id = $newRegistration->tr04_sample_registration_id;
                    $newTest->m04_ro_id = $roId;
                    $newTest->tr05_status = 'PENDING';
                    $newTest->tr05_remark = null;
                    $newTest->m06_alloted_to = null;
                    $newTest->m06_alloted_by = null;
                    $newTest->m04_transferred_to = null;
                    $newTest->m04_transferred_by = null;
                    $newTest->tr05_alloted_at = null;
                    $newTest->tr05_transferred_at = null;
                    $newTest->tr05_reassigned_at = null;
                    $newTest->tr05_completed_at = null;
                    $newTest->tr05_accepted_at = null;
                    $newTest->created_at = now();
                    $newTest->updated_at = now();
                    $newTest->save();
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Transferred sample accepted and new registration created.'
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
}
