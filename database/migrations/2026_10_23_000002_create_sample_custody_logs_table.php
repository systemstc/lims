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
        // 1. Create tr10_sample_custody_logs table
        if (!Schema::hasTable('tr10_sample_custody_logs')) {
            Schema::create('tr10_sample_custody_logs', function (Blueprint $table) {
                $table->bigIncrements('tr10_custody_log_id');
                $table->unsignedBigInteger('tr04_sample_registration_id')->index();
                $table->string('tr04_reference_id', 100)->nullable()->index();
                $table->unsignedBigInteger('m04_ro_id')->nullable()->index();
                $table->string('from_role', 50);
                $table->unsignedBigInteger('from_user_id')->nullable();
                $table->string('from_name', 255)->nullable();
                $table->string('to_role', 50);
                $table->unsignedBigInteger('to_user_id')->nullable();
                $table->string('to_name', 255)->nullable();
                $table->string('action_type', 50)->default('SEND_BACK'); // FORWARD, SEND_BACK, CLARIFIED, REASSIGN
                $table->text('clarification_remark')->nullable();
                $table->boolean('is_resolved')->default(false);
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add custody tracking columns to tr04_sample_registrations
        if (Schema::hasTable('tr04_sample_registrations')) {
            Schema::table('tr04_sample_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_current_custody_role')) {
                    $table->string('tr04_current_custody_role', 50)->nullable()->default('RC')->after('tr04_progress');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_is_under_clarification')) {
                    $table->tinyInteger('tr04_is_under_clarification')->default(0)->after('tr04_current_custody_role');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_latest_clarification_remark')) {
                    $table->text('tr04_latest_clarification_remark')->nullable()->after('tr04_is_under_clarification');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sent_back_by_name')) {
                    $table->string('tr04_sent_back_by_name', 255)->nullable()->after('tr04_latest_clarification_remark');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sent_back_by_role')) {
                    $table->string('tr04_sent_back_by_role', 50)->nullable()->after('tr04_sent_back_by_name');
                }
                if (!Schema::hasColumn('tr04_sample_registrations', 'tr04_sent_back_at')) {
                    $table->timestamp('tr04_sent_back_at')->nullable()->after('tr04_sent_back_by_role');
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
                    'tr04_current_custody_role',
                    'tr04_is_under_clarification',
                    'tr04_latest_clarification_remark',
                    'tr04_sent_back_by_name',
                    'tr04_sent_back_by_role',
                    'tr04_sent_back_at',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('tr04_sample_registrations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('tr10_sample_custody_logs');
    }
};
