<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\SampleRegistration;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\RemoveRegisterMockObjectsFromTestArgumentsRecursivelyAttribute;

class SampleController extends Controller
{
    public function searchNames(Request $request)
    {
        $searchTerm = $request->input('query');
        try {
            $results = DB::select('CALL find_similar_names(?)', [$searchTerm . '%']);
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Error calling stored procedure find_similar_names: ' . $e->getMessage(), ['query' => $searchTerm]);
            return response()->json(['error' => 'Could not retrieve search results.'], 500);
        }
    }

    public function registerSample()
    {
        // $customers = Customer::all();
        return view('samples.sample_regsitration');
    }

    public function globalSearch(Request $request)
    {
        $query = $request->get('query', '');
        $type = $request->get('type', 'all');
        $status = $request->get('status', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        $results = [];

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // ---------------------
        // Search Customers
        // ---------------------
        if ($type === 'all' || $type === 'customers') {
            $customers = Customer::query()
                ->where('m07_name', 'LIKE', "%{$query}%")
                ->when($status, fn($q) => $q->where('m07_status', $status))
                ->when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
                ->limit(10)
                ->get();

            foreach ($customers as $customer) {
                $results[] = [
                    'id' => $customer->m07_customer_id,
                    'type' => 'customer',
                    'title' => $customer->m07_name,
                    'subtitle' => $customer->m07_address ?? 'No address',
                    'status' => $customer->m07_status ?? 'active',
                    'date' => optional($customer->created_at)->format('Y-m-d'),
                    'priority' => null
                ];
            }
        }

        // ---------------------
        // Search Samples
        // ---------------------
        if ($type === 'all' || $type === 'samples') {
            $samples = SampleRegistration::with(['customer'])
                ->where(function ($q) use ($query) {
                    $q->where('tr04_reference_no', 'LIKE', "%{$query}%")
                        ->orWhere('tr04_sample_description', 'LIKE', "%{$query}%")
                        ->orWhereHas('customer', fn($cq) => $cq->where('m07_name', 'LIKE', "%{$query}%"));
                })
                ->when($status, fn($q) => $q->where('tr04_status', $status))
                ->when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
                ->limit(10)
                ->get();

            foreach ($samples as $sample) {
                $results[] = [
                    'id' => $sample->tr04_sample_registration_id,
                    'type' => 'sample',
                    'title' => $sample->tr04_reference_no
                        ? "#{$sample->tr04_reference_no}"
                        : "Sample #{$sample->tr04_sample_registration_id}",
                    'subtitle' => $sample->tr04_sample_description ?? 'No description',
                    'status' => strtolower($sample->tr04_status ?? 'pending'),
                    'date' => optional($sample->created_at)->format('Y-m-d'),
                    'customer' => $sample->customer->m07_name ?? 'Unknown',
                    'priority' => strtolower($sample->tr04_charge_type ?? 'normal')
                ];
            }
        }

        // ---------------------
        // Search Tests
        // ---------------------
        if ($type === 'all' || $type === 'tests') {
            $tests = Test::with('group')
                ->where('m12_name', 'LIKE', "%{$query}%")
                ->when($status, fn($q) => $q->where('m12_status', $status))
                ->limit(10)
                ->get();

            foreach ($tests as $test) {
                $results[] = [
                    'id' => $test->m12_test_id,
                    'type' => 'test',
                    'title' => $test->m12_name,
                    'subtitle' => $test->group->m11_name ?? 'No group',
                    'status' => strtolower($test->m12_status ?? 'active'),
                    'date' => optional($test->created_at)->format('Y-m-d'),
                    'priority' => 'normal'
                ];
            }
        }

        // ---------------------
        // Search Contracts/Packages
        // ---------------------
        if ($type === 'all' || $type === 'contracts') {
            $contracts = Package::query()
                ->where('m19_name', 'LIKE', "%{$query}%")
                ->when($status, fn($q) => $q->where('m19_status', $status))
                ->limit(10)
                ->get();

            foreach ($contracts as $contract) {
                $results[] = [
                    'id' => $contract->m19_package_id,
                    'type' => 'contract',
                    'title' => $contract->m19_name,
                    'subtitle' => ucfirst(strtolower($contract->m19_type ?? '')) . ' Package',
                    'status' => strtolower($contract->m19_status ?? 'active'),
                    'date' => optional($contract->created_at)->format('Y-m-d'),
                    'priority' => null
                ];
            }
        }

        // ---------------------
        // Sort results by relevance
        // ---------------------
        usort($results, function ($a, $b) use ($query) {
            $aRelevance = stripos($a['title'], $query) !== false ? 1 : 0;
            $bRelevance = stripos($b['title'], $query) !== false ? 1 : 0;
            return $bRelevance - $aRelevance;
        });

        return response()->json($results);
    }


    /**
     * Get recent records for sidebar
     */
    public function recentRecords(Request $request)
    {
        $records = [];

        // ---------------------
        // Recent Samples
        // ---------------------
        $recentSamples = SampleRegistration::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentSamples as $sample) {
            $records[] = [
                'id' => $sample->tr04_sample_registration_id,
                'type' => 'Sample',
                'title' => ($sample->tr04_reference_no ? "#{$sample->tr04_reference_no}" : "Sample #{$sample->tr04_sample_registration_id}"),
                'date' => $sample->created_at?->diffForHumans() ?? ''
            ];
        }

        // ---------------------
        // Recent Customers
        // ---------------------
        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        foreach ($recentCustomers as $customer) {
            $records[] = [
                'id' => $customer->m07_customer_id,
                'type' => 'Customer',
                'title' => $customer->m07_name,
                'date' => $customer->created_at?->diffForHumans() ?? ''
            ];
        }

        // ---------------------
        // Sort by most recent (using created_at timestamps instead of parsing strings)
        // ---------------------
        usort($records, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return response()->json(array_slice($records, 0, 5));
    }


    /**
     * Get today's statistics
     */
    public function todayStats(Request $request)
    {
        $today = Carbon::today();

        $stats = [
            // Today's sample registrations
            'today_samples' => SampleRegistration::whereDate('created_at', $today)->count(),

            // Pending tests - using a more efficient approach
            'pending_tests' => $this->getPendingTestsCountOptimized(),

            // Tests completed today
            'completed_today' => Test::where('m12_status', 'completed')
                ->whereDate('updated_at', $today)
                ->count(),

            // Total revenue from today's samples
            'total_revenue_today' => SampleRegistration::whereDate('created_at', $today)
                ->sum('tr04_total_charges') ?? 0,

            // Additional useful stats
            'active_samples' => SampleRegistration::whereNotIn('tr04_status', ['completed', 'delivered', 'cancelled'])
                ->count(),

            // Weekly comparison
            'weekly_samples' => SampleRegistration::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
        ];

        return response()->json($stats);
    }

    /**
     * More optimized approach for pending tests
     */
    private function getPendingTestsCountOptimized()
    {
        // Get all test IDs from active sample registrations
        $activeRegistrations = SampleRegistration::whereNotIn('tr04_status', ['completed', 'delivered'])
            ->whereNotNull('m12_test_ids')
            ->where('m12_test_ids', '!=', '')
            ->pluck('m12_test_ids');

        // Flatten all test IDs
        $allTestIds = $activeRegistrations->flatMap(function ($testIds) {
            return explode(',', $testIds);
        })->unique()->filter();

        // Count pending tests
        return Test::whereIn('m12_test_id', $allTestIds)
            ->whereIn('m12_status', ['pending', 'in_progress'])
            ->count();
    }

    // If you want to add caching for better performance
    public function todayStatsCached(Request $request)
    {
        $cacheKey = 'today_stats_' . Carbon::today()->format('Y-m-d');

        $stats = Cache::remember($cacheKey, 300, function () { // Cache for 5 minutes
            return $this->calculateTodayStats();
        });

        return response()->json($stats);
    }

    private function calculateTodayStats()
    {
        $today = Carbon::today();

        return [
            'today_samples' => SampleRegistration::whereDate('created_at', $today)->count(),
            'pending_tests' => $this->getPendingTestsCountOptimized(),
            'completed_today' => Test::where('m12_status', 'completed')
                ->whereDate('updated_at', $today)
                ->count(),
            'total_revenue_today' => SampleRegistration::whereDate('created_at', $today)
                ->sum('tr04_total_charges') ?? 0,
        ];
    }

    /**
     * Get detailed sample information for copying
     */


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
            'priority' => $sample->tr04_progress,
            'test_type' => $sample->tr04_test_type,
            'status' => $sample->tr04_status,
            'customer' => [
                'id' => $sample->customer?->m07_customer_id,
                'name' => $sample->customer?->m07_name
            ],
            'tests' => $tests->values()->toArray()
        ];

        return response()->json($sampleData);
    }
    /**
     * Get detailed test information
     */
    public function getTestDetails(Request $request)
    {
        $testId = $request->get('test_id');

        $test = DB::table('m12_tests as t')
            ->leftJoin('m11_groups as g', 't.m11_group_id', '=', 'g.m11_group_id')
            ->where('t.m12_test_id', $testId)
            ->select(
                't.m12_test_id as id',
                't.m12_name as name',
                't.m12_name as test_name',
                't.m12_charge as charge',
                't.m11_group_id as group_id',
                'g.m11_name as group_name'
            )
            ->first();

        if (!$test) {
            return response()->json(['error' => 'Test not found'], 404);
        }

        return response()->json($test);
    }
}
