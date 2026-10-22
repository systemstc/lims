<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::statement('ALTER TABLE `m20_package_tests` MODIFY `m15_standard_id` BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            Schema::table('m20_package_tests', function (Blueprint $table) {
                $table->unsignedBigInteger('m15_standard_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::statement('ALTER TABLE `m20_package_tests` MODIFY `m15_standard_id` BIGINT UNSIGNED NOT NULL');
        } catch (\Throwable $e) {
            Schema::table('m20_package_tests', function (Blueprint $table) {
                $table->unsignedBigInteger('m15_standard_id')->nullable(false)->change();
            });
        }
    }
};
