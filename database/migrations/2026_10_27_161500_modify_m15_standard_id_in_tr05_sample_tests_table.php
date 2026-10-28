<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $fks = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'tr05_sample_tests' 
                  AND COLUMN_NAME = 'm15_standard_id' 
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            foreach ($fks as $fk) {
                if (!empty($fk->CONSTRAINT_NAME)) {
                    DB::statement("ALTER TABLE `tr05_sample_tests` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                }
            }
            DB::statement('ALTER TABLE `tr05_sample_tests` MODIFY `m15_standard_id` VARCHAR(255) NULL');
        } catch (\Throwable $e) {
            try {
                DB::statement('ALTER TABLE `tr05_sample_tests` MODIFY `m15_standard_id` VARCHAR(255) NULL');
            } catch (\Throwable $ex) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `tr05_sample_tests` MODIFY `m15_standard_id` BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
