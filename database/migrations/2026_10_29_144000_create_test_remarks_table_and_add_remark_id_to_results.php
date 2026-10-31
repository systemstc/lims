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
        // 1. Create m24_test_remarks library table
        if (!Schema::hasTable('m24_test_remarks')) {
            Schema::create('m24_test_remarks', function (Blueprint $table) {
                $table->bigIncrements('m24_remark_id');
                $table->unsignedBigInteger('m04_ro_id')->nullable()->index();
                $table->unsignedBigInteger('m06_user_id')->nullable()->index();
                $table->unsignedBigInteger('m12_test_id')->nullable()->index();
                $table->text('m24_remark_text');
                $table->timestamps();
            });
        }

        // 2. Add m24_remark_id to tr07_test_results
        if (Schema::hasTable('tr07_test_results')) {
            Schema::table('tr07_test_results', function (Blueprint $table) {
                if (!Schema::hasColumn('tr07_test_results', 'm24_remark_id')) {
                    $table->unsignedBigInteger('m24_remark_id')->nullable()->after('tr07_remarks')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tr07_test_results')) {
            Schema::table('tr07_test_results', function (Blueprint $table) {
                if (Schema::hasColumn('tr07_test_results', 'm24_remark_id')) {
                    $table->dropColumn('m24_remark_id');
                }
            });
        }

        if (Schema::hasTable('m24_test_remarks')) {
            Schema::dropIfExists('m24_test_remarks');
        }
    }
};

