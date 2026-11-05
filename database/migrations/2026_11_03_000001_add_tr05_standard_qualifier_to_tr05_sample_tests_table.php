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
        if (!Schema::hasColumn('tr05_sample_tests', 'tr05_standard_qualifier')) {
            Schema::table('tr05_sample_tests', function (Blueprint $table) {
                $table->string('tr05_standard_qualifier', 255)->nullable()->after('m15_standard_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tr05_sample_tests', 'tr05_standard_qualifier')) {
            Schema::table('tr05_sample_tests', function (Blueprint $table) {
                $table->dropColumn('tr05_standard_qualifier');
            });
        }
    }
};

