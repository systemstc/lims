<?php

namespace App\Services;

use App\Models\CustomField;
use App\Models\Employee;
use App\Models\SampleCustodyLog;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\TestReport;
use App\Models\TestResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustodyService
{
    /**
     * Send sample back to previous custody node with a strictly mandatory remark.
     *
     * @param int|string $sampleRegistrationId
     * @param string $fromRole ('Issuing Officer', 'Verification Officer', 'DEO', 'Analyst', 'Manager')
     * @param string $toRole   ('Verification Officer', 'DEO', 'Analyst', 'Manager', 'RC')
     * @param string $remark   MANDATORY
     * @param int|null $toUserId
     * @param string|null $toName
     * @return SampleCustodyLog
     * @throws \InvalidArgumentException
     */
    public static function sendBack(
        $sampleRegistrationId,
        string $fromRole,
        string $toRole,
        string $remark,
        $toUserId = null,
        $toName = null
    ): SampleCustodyLog {
        SampleCustodyLog::ensureCustodySchemaExists();

        $cleanRemark = trim($remark);
        if (empty($cleanRemark)) {
            throw new \InvalidArgumentException('A clear clarification remark is strictly mandatory when sending back a sample.');
        }

        $sample = SampleRegistration::where('tr04_sample_registration_id', $sampleRegistrationId)
            ->orWhere('tr04_reference_id', $sampleRegistrationId)
            ->firstOrFail();

        $fromUserId = Session::get('user_id') ?? null;
        $fromName = Session::get('user_name') ?? Session::get('employee_name') ?? $fromRole;

        // Auto-resolve recipient name if not provided
        if ($toUserId && !$toName) {
            $emp = Employee::find($toUserId);
            $toName = $emp?->m06_name;
        }

        return DB::transaction(function () use ($sample, $fromRole, $toRole, $cleanRemark, $fromUserId, $fromName, $toUserId, $toName) {
            // 1. Create Custody Log
            $log = SampleCustodyLog::create([
                'tr04_sample_registration_id' => $sample->tr04_sample_registration_id,
                'tr04_reference_id'           => $sample->tr04_reference_id,
                'm04_ro_id'                   => $sample->m04_ro_id ?? Session::get('ro_id'),
                'from_role'                   => $fromRole,
                'from_user_id'                => $fromUserId,
                'from_name'                   => $fromName,
                'to_role'                     => $toRole,
                'to_user_id'                  => $toUserId,
                'to_name'                     => $toName,
                'action_type'                 => 'SEND_BACK',
                'clarification_remark'        => $cleanRemark,
                'is_resolved'                 => false,
            ]);

            // 2. Update Sample Registration Custody Status
            $sample->update([
                'tr04_current_custody_role'        => $toRole,
                'tr04_is_under_clarification'      => 1,
                'tr04_latest_clarification_remark' => $cleanRemark,
                'tr04_sent_back_by_name'           => $fromName,
                'tr04_sent_back_by_role'           => $fromRole,
                'tr04_sent_back_at'                => now(),
            ]);

            // 3. Node-specific state adjustments strictly adhering to laboratory SOP:
            
            // A. Send Back to Verification Officer (from Issuing Officer / Report Finalizer)
            // Reverts test results from VERIFIED/REPORTED back to RESULTED so they leave Issuing queue and re-enter Verification queue
            if ($toRole === 'Verification Officer') {
                TestResult::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->where('tr07_is_current', 'YES')
                    ->whereIn('tr07_result_status', ['VERIFIED', 'REPORTED', 'FINALIZED'])
                    ->update([
                        'tr07_result_status' => 'RESULTED',
                        'tr07_remarks'       => $cleanRemark,
                    ]);

                CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->where('tr08_is_current', 'YES')
                    ->whereIn('tr08_result_status', ['VERIFIED', 'REPORTED', 'FINALIZED'])
                    ->update([
                        'tr08_result_status' => 'RESULTED',
                        'tr08_remarks'       => $cleanRemark,
                    ]);

                // Invalidate any generated current test reports so a fresh report must be generated upon re-verification
                TestReport::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->where('tr09_is_current', 'YES')
                    ->update(['tr09_is_current' => 'NO']);
            }

            // B. Send Back to Analyst (from Verification Officer [Usecase A: Re-analysis] or DEO [Manuscript Ambiguity])
            if ($toRole === 'Analyst') {
                if ($fromRole === 'Verification Officer' || in_array($fromRole, ['Verification Officer', 'Technical Manager'])) {
                    // Usecase A: Re-analysis / analytical error detected during verification
                    TestResult::where('tr04_reference_id', $sample->tr04_reference_id)
                        ->where('tr07_is_current', 'YES')
                        ->update([
                            'tr07_result_status' => 'REJECTED',
                            'tr07_remarks'       => $cleanRemark,
                            'tr07_verified_at'   => now(),
                        ]);

                    CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
                        ->where('tr08_is_current', 'YES')
                        ->update([
                            'tr08_result_status' => 'REJECTED',
                            'tr08_remarks'       => $cleanRemark,
                            'tr08_updated_at'    => now(),
                        ]);

                    $stUpdate = ['tr05_status' => 'IN_PROGRESS'];
                    if ($toUserId) {
                        $stUpdate['m06_alloted_to'] = $toUserId;
                    }
                    SampleTest::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                        ->update($stUpdate);
                } else {
                    // From DEO: Manuscript illegible or missing values from Analyst
                    SampleTest::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                        ->whereIn('tr05_status', ['COMPLETED', 'IN_PROGRESS'])
                        ->update([
                            'tr05_status' => 'IN_PROGRESS',
                            'tr05_remark' => $cleanRemark,
                        ]);
                }
            }

            // C. Send Back to DEO (from Verification Officer [Usecase B: Typo / Data Entry])
            // Reverts test results to DRAFT so DEO can reopen and correct manuscript data entry
            if ($toRole === 'DEO') {
                TestResult::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->where('tr07_is_current', 'YES')
                    ->update([
                        'tr07_result_status' => 'DRAFT',
                        'tr07_remarks'       => $cleanRemark,
                    ]);

                CustomField::where('tr04_reference_id', $sample->tr04_reference_id)
                    ->where('tr08_is_current', 'YES')
                    ->update([
                        'tr08_result_status' => 'DRAFT',
                        'tr08_remarks'       => $cleanRemark,
                    ]);
            }

            // D. Send Back to Manager (from Analyst: Sample damaged, insufficient quantity, wrong allotment)
            // Clears allotted analyst so sample disappears from Analyst dashboard and returns to Manager unallotted pool
            if ($toRole === 'Manager') {
                SampleTest::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                    ->update([
                        'm06_alloted_to' => null,
                        'tr05_status'    => 'PENDING',
                        'tr05_remark'    => $cleanRemark,
                    ]);
            }

            // E. Send Back to Reception Center (RC) (from Manager: Sample registration flaw / test selection error)
            // Sets progress to UNDER_CLARIFICATION so sample is removed from Manager's pending allotment queue
            if ($toRole === 'RC') {
                $sample->update([
                    'tr04_progress' => 'UNDER_CLARIFICATION',
                ]);
            }

            return $log;
        });
    }

    /**
     * Advance sample forward or resolve previous clarification.
     *
     * @param int|string $sampleRegistrationId
     * @param string $fromRole
     * @param string $toRole
     * @param string|null $remark
     * @return SampleCustodyLog
     */
    public static function forwardOrResolve(
        $sampleRegistrationId,
        string $fromRole,
        string $toRole,
        ?string $remark = null
    ): SampleCustodyLog {
        SampleCustodyLog::ensureCustodySchemaExists();

        $sample = SampleRegistration::where('tr04_sample_registration_id', $sampleRegistrationId)
            ->orWhere('tr04_reference_id', $sampleRegistrationId)
            ->firstOrFail();

        $fromUserId = Session::get('user_id') ?? null;
        $fromName = Session::get('user_name') ?? Session::get('employee_name') ?? $fromRole;

        return DB::transaction(function () use ($sample, $fromRole, $toRole, $remark, $fromUserId, $fromName) {
            // Mark previous active send_back logs as resolved
            SampleCustodyLog::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
                ->where('is_resolved', false)
                ->update([
                    'is_resolved' => true,
                    'resolved_at' => now(),
                ]);

            // Create Forward / Clarified log
            $actionType = $sample->tr04_is_under_clarification ? 'CLARIFIED' : 'FORWARD';

            $log = SampleCustodyLog::create([
                'tr04_sample_registration_id' => $sample->tr04_sample_registration_id,
                'tr04_reference_id'           => $sample->tr04_reference_id,
                'm04_ro_id'                   => $sample->m04_ro_id ?? Session::get('ro_id'),
                'from_role'                   => $fromRole,
                'from_user_id'                => $fromUserId,
                'from_name'                   => $fromName,
                'to_role'                     => $toRole,
                'action_type'                 => $actionType,
                'clarification_remark'        => $remark,
                'is_resolved'                 => true,
                'resolved_at'                 => now(),
            ]);

            // Update Sample Registration
            $updates = [
                'tr04_current_custody_role'        => $toRole,
                'tr04_is_under_clarification'      => 0,
                'tr04_latest_clarification_remark' => null,
                'tr04_sent_back_by_name'           => null,
                'tr04_sent_back_by_role'           => null,
                'tr04_sent_back_at'                => null,
            ];

            if ($sample->tr04_progress === 'UNDER_CLARIFICATION' && $toRole === 'Manager') {
                $updates['tr04_progress'] = 'REGISTERED';
            }

            $sample->update($updates);

            return $log;
        });
    }

    /**
     * Get custody timeline history for a sample.
     */
    public static function getCustodyTimeline($sampleRegistrationId)
    {
        SampleCustodyLog::ensureCustodySchemaExists();

        $sample = SampleRegistration::where('tr04_sample_registration_id', $sampleRegistrationId)
            ->orWhere('tr04_reference_id', $sampleRegistrationId)
            ->first();

        if (!$sample) {
            return collect();
        }

        return SampleCustodyLog::where('tr04_sample_registration_id', $sample->tr04_sample_registration_id)
            ->orderByDesc('tr10_custody_log_id')
            ->get();
    }
}
