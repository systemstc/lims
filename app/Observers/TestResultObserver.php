<?php

namespace App\Observers;

use App\Models\TestResult;
use App\Models\SampleRegistration;
use Illuminate\Support\Facades\Log;

class TestResultObserver
{
    public function created(TestResult $result)
    {
        $this->updateSampleProgress($result, 'created');
    }

    public function updated(TestResult $result)
    {
        $this->updateSampleProgress($result, 'updated');
    }

    protected function updateSampleProgress(TestResult $result, string $eventType)
    {
        $sampleId = $result->tr04_reference_id;

        $sample = SampleRegistration::where('tr04_reference_id', $sampleId)->first();
        if (!$sample) {
            Log::warning("No SampleRegistration found for reference {$sampleId}");
            return;
        }

        $query = TestResult::where('tr04_reference_id', $sampleId)
            ->where('tr07_is_current', 'YES');

        $totalResults = $query->count();
        if ($totalResults === 0) return;

        $verifiedResults  = (clone $query)->where('tr07_result_status', 'VERIFIED')->count();
        $completedResults = (clone $query)->where('tr07_result_status', 'RESULTED')->count();
        $rejectedResults  = (clone $query)->whereIn('tr07_result_status', ['REJECTED', 'REVISED'])->count();

        // Determine new status
        if ($totalResults === $verifiedResults) {
            $newStatus = 'VERIFIED';
        } elseif ($completedResults > 0) {
            $newStatus = 'RESULT_ENTERED';
        } elseif ($rejectedResults > 0) {
            $newStatus = 'IN_PROGRESS';
        } else {
            $newStatus = $sample->tr04_progress;
        }

        // Prevent redundant updates
        if ($sample->tr04_progress !== $newStatus) {
            $sample->tr04_progress = $newStatus;
            $sample->save();

            Log::info("✅ Sample {$sampleId} progress updated to {$newStatus} ({$eventType})");
        }
    }
}
