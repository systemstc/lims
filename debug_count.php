<?php

use App\Models\SampleRegistration;
use Illuminate\Support\Facades\Session;

// Mock RO ID for testing (assuming it's 2 based on previous output)
$roId = 2;

$query = SampleRegistration::query()
    ->whereHas('sampleTests', function ($query) use ($roId) {
        $query->where('m04_ro_id', $roId);
    })
    ->withCount(['sampleTests as total_tests' => function ($query) use ($roId) {
        $query->where('m04_ro_id', $roId)
            ->where('tr05_status', '!=', 'TRANSFERRED');
    }])
    ->withCount(['sampleTests as allotted_tests' => function ($query) use ($roId) {
        $query->where('m04_ro_id', $roId)
            ->where('tr05_status', '!=', 'TRANSFERRED')
            ->whereNotNull('m06_alloted_to');
    }]);

$count = $query->count();
echo "Total Pending Registrations Matching Query: " . $count . "\n";

// Check if Sample 57 is in the list
$exists = $query->where('tr04_sample_registration_id', 57)->exists();
echo "Sample 57 exists in query: " . ($exists ? 'Yes' : 'No') . "\n";
