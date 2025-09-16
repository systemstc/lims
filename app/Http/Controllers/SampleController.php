<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\TextUI\XmlConfiguration\RemoveRegisterMockObjectsFromTestArgumentsRecursivelyAttribute;

class SampleController extends Controller
{
    public function recentRecords(Request $request)
    {
        // Get only recent samples (last 5)
        $recentSamples = SampleRegistration::with('customer:m07_customer_id,m07_name')
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
            'today_samples' => SampleRegistration::whereDate('created_at', $today)->count(),
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
}
