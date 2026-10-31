<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\TestResult;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand tr10_action from rigid enum to VARCHAR(50) so it supports all ISO 17025 lifecycle actions
        DB::statement("ALTER TABLE tr10_test_result_audit MODIFY COLUMN tr10_action VARCHAR(50) NOT NULL");

        // 2. Backfill initial records for existing test results
        $existingAuditIds = DB::table('tr10_test_result_audit')
            ->pluck('tr07_test_result_id')
            ->toArray();

        $auditSet = array_flip($existingAuditIds);

        TestResult::orderBy('tr07_test_result_id')
            ->chunk(100, function ($testResults) use (&$auditSet) {
                $inserts = [];

                foreach ($testResults as $res) {
                    if (isset($auditSet[$res->tr07_test_result_id])) {
                        continue;
                    }

                    $createdAt = $res->tr07_created_at ?? $res->created_at ?? now();
                    $verifiedAt = $res->tr07_verified_at ?? $res->updated_at ?? $createdAt;
                    $authorizedAt = $res->tr07_authorized_at ?? $res->updated_at ?? $verifiedAt;

                    $resultDisplay = trim(($res->tr07_result ?? '') . ' ' . ($res->tr07_unit ?? ''));
                    if (empty($resultDisplay)) {
                        $resultDisplay = 'Observed / Completed';
                    }

                    // Initial Entry
                    $inserts[] = [
                        'tr07_test_result_id' => $res->tr07_test_result_id,
                        'tr09_version_id'     => $res->tr07_current_version ?? 1,
                        'tr10_action'         => 'CREATED',
                        'tr10_field_changed'  => 'Initial Result Entry',
                        'tr10_old_value'      => null,
                        'tr10_new_value'      => $resultDisplay,
                        'tr10_change_reason'  => 'Initial test result recorded by analyst',
                        'tr10_user_id'        => $res->m06_created_by ?? 1,
                        'tr10_ip_address'     => '127.0.0.1',
                        'tr10_user_agent'     => 'LIMS System',
                        'tr10_created_at'     => $createdAt,
                    ];

                    // Verified Entry
                    if (in_array($res->tr07_result_status, ['VERIFIED', 'REPORTED'])) {
                        $inserts[] = [
                            'tr07_test_result_id' => $res->tr07_test_result_id,
                            'tr09_version_id'     => $res->tr07_current_version ?? 1,
                            'tr10_action'         => 'VERIFIED',
                            'tr10_field_changed'  => 'Status',
                            'tr10_old_value'      => 'RESULTED',
                            'tr10_new_value'      => 'VERIFIED',
                            'tr10_change_reason'  => 'Result verified and approved by Quality Officer',
                            'tr10_user_id'        => $res->m06_verified_by ?? $res->m06_created_by ?? 1,
                            'tr10_ip_address'     => '127.0.0.1',
                            'tr10_user_agent'     => 'LIMS System',
                            'tr10_created_at'     => $verifiedAt,
                        ];
                    }

                    // Reported / Released Entry
                    if ($res->tr07_result_status === 'REPORTED') {
                        $inserts[] = [
                            'tr07_test_result_id' => $res->tr07_test_result_id,
                            'tr09_version_id'     => $res->tr07_current_version ?? 1,
                            'tr10_action'         => 'REPORTED',
                            'tr10_field_changed'  => 'Status',
                            'tr10_old_value'      => 'VERIFIED',
                            'tr10_new_value'      => 'REPORTED',
                            'tr10_change_reason'  => 'Test report finalized and released',
                            'tr10_user_id'        => $res->m06_authorized_by ?? $res->m06_verified_by ?? 1,
                            'tr10_ip_address'     => '127.0.0.1',
                            'tr10_user_agent'     => 'LIMS System',
                            'tr10_created_at'     => $authorizedAt,
                        ];
                    }

                    $auditSet[$res->tr07_test_result_id] = true;
                }

                if (!empty($inserts)) {
                    DB::table('tr10_test_result_audit')->insert($inserts);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
