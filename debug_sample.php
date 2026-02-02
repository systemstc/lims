<?php

use App\Models\SampleRegistration;
use App\Models\SampleTest;
use Illuminate\Support\Facades\Session;

// Mock session if needed or just query directly
// Assuming we are running this in a context where we can just query

$latestSample = SampleRegistration::latest('created_at')->first();

if (!$latestSample) {
    echo "No samples found.\n";
    exit;
}

echo "Latest Sample ID: " . $latestSample->tr04_sample_registration_id . "\n";
echo "Sample Ref ID: " . $latestSample->tr04_reference_id . "\n";
echo "Sample RO ID: " . $latestSample->m04_ro_id . "\n";

$tests = SampleTest::where('tr04_sample_registration_id', $latestSample->tr04_sample_registration_id)->get();

echo "Tests Count: " . $tests->count() . "\n";

foreach ($tests as $test) {
    echo "Test ID: " . $test->tr05_sample_test_id . " - RO ID: " . $test->m04_ro_id . " - Status: " . $test->tr05_status . "\n";
}
