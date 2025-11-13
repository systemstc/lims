<?php

namespace App\Observers;

use App\Models\SampleTest;
use App\Models\SampleRegistration;
use Illuminate\Support\Facades\Log;

class SampleTestObserver
{
    public function updated(SampleTest $test)
    {
        $sampleId = $test->tr04_sample_registration_id;

        $sample = SampleRegistration::find($sampleId);
        if (!$sample) return;

        // Exclude transferred or externally received tests
        $query = SampleTest::where('tr04_sample_registration_id', $sampleId)
            ->whereNotIn('tr05_status', ['TRANSFERRED', 'RECEIVED_ACCEPTED']);

        $totalTests = $query->count();

        if ($totalTests === 0) {
            // No valid tests — no update needed
            return;
        }

        $completedTests = (clone $query)->where('tr05_status', 'COMPLETED')->count();
        $inProgressTests = (clone $query)->where('tr05_status', 'IN_PROGRESS')->count();
        $allottedTests = (clone $query)->where('tr05_status', 'ALLOTED')->count();

        // Decide sample status logically
        if ($totalTests === $completedTests) {
            $sample->tr04_progress = 'ANALYSED';
        } elseif ($inProgressTests > 0) {
            $sample->tr04_progress = 'IN_PROGRESS';
        } elseif ($allottedTests > 0) {
            $sample->tr04_progress = 'ALLOTTED';
        }

        $sample->save();
    }
}
