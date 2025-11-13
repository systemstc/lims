<?php

namespace App\Observers;

use App\Models\TestReport;
use App\Models\SampleRegistration;
use Illuminate\Support\Facades\Log;

class TestReportObserver
{
    public function created(TestReport $report)
    {
        $this->updateSampleProgress($report, 'created');
    }

    public function updated(TestReport $report)
    {
        $this->updateSampleProgress($report, 'updated');
    }

    protected function updateSampleProgress(TestReport $report, string $eventType)
    {
        $sampleId = $report->tr04_reference_id;

        // Find by reference id (not primary key)
        $sample = SampleRegistration::where('tr04_reference_id', $sampleId)->first();

        if (!$sample) {
            Log::warning("⚠️ Sample not found for reference {$sampleId}");
            return;
        }

        $currentReport = TestReport::where('tr04_reference_id', $sampleId)
            ->where('tr09_is_current', 'YES')
            ->latest('tr09_version_number')
            ->first();

        if (!$currentReport) {
            Log::warning("⚠️ No current report found for Sample {$sampleId}");
            return;
        }

        $status = $currentReport->tr09_status;
        $newStatus = $sample->tr04_progress;

        if ($status === 'FINAL') {
            $newStatus = 'REPORTED';
        } elseif ($status === 'CANCELLED') {
            $newStatus = 'IN_PROGRESS';
        }

        Log::info("Current progress: {$sample->tr04_progress} → New: {$newStatus}");

        if ($sample->tr04_progress !== $newStatus) {
            $sample->tr04_progress = $newStatus;
            $sample->saveQuietly(); // avoids triggering other observers
            Log::info("✅ TestReportObserver ({$eventType}) → Sample {$sampleId} updated to {$newStatus}");
        } else {
            Log::info("ℹ️ TestReportObserver ({$eventType}) → No change for Sample {$sampleId}");
        }
    }
}
