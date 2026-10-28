<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tr04_sample_registrations')) {
            Schema::table('tr04_sample_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_contract_review_status')) {
                    $table->string('tr04_contract_review_status', 35)->default('PENDING')->index()->after('tr04_progress');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_contract_reviewed_by')) {
                    $table->unsignedBigInteger('tr04_contract_reviewed_by')->nullable()->after('tr04_contract_review_status');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_contract_reviewed_by_name')) {
                    $table->string('tr04_contract_reviewed_by_name', 191)->nullable()->after('tr04_contract_reviewed_by');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_contract_reviewed_by_role')) {
                    $table->string('tr04_contract_reviewed_by_role', 50)->nullable()->after('tr04_contract_reviewed_by_name');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_contract_reviewed_at')) {
                    $table->timestamp('tr04_contract_reviewed_at')->nullable()->after('tr04_contract_reviewed_by_role');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_contract_review_remarks')) {
                    $table->text('tr04_contract_review_remarks')->nullable()->after('tr04_contract_reviewed_at');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sample_condition')) {
                    $table->string('tr04_sample_condition', 50)->nullable()->default('INTACT_SEALED')->after('tr04_sample_type');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_storage_condition')) {
                    $table->string('tr04_storage_condition', 50)->nullable()->default('AMBIENT')->after('tr04_sample_condition');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_decision_rule')) {
                    $table->string('tr04_decision_rule', 100)->nullable()->default('SIMPLE_ACCEPTANCE')->after('tr04_storage_condition');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_feasibility_checklist')) {
                    $table->json('tr04_feasibility_checklist')->nullable()->after('tr04_decision_rule');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_customer_agreed_deviation')) {
                    $table->tinyInteger('tr04_customer_agreed_deviation')->default(0)->after('tr04_feasibility_checklist');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tr04_sample_registrations')) {
            Schema::table('tr04_sample_registrations', function (Blueprint $table) {
                $columns = [
                    'tr04_contract_review_status',
                    'tr04_contract_reviewed_by',
                    'tr04_contract_reviewed_by_name',
                    'tr04_contract_reviewed_by_role',
                    'tr04_contract_reviewed_at',
                    'tr04_contract_review_remarks',
                    'tr04_sample_condition',
                    'tr04_storage_condition',
                    'tr04_decision_rule',
                    'tr04_feasibility_checklist',
                    'tr04_customer_agreed_deviation',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('tr04_sample_registrations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
