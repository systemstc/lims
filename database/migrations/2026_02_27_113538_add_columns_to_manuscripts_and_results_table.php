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
        Schema::table('m22_manuscripts', function (Blueprint $table) {
            if (!Schema::hasColumn('m22_manuscripts', 'm15_standard_ids')) {
                $table->text('m15_standard_ids')->nullable()->after('m12_test_number')->comment('Comma-separated standard IDs');
            }
            if (!Schema::hasColumn('m22_manuscripts', 'm22_content')) {
                $table->longText('m22_content')->nullable()->after('m22_name')->comment('HTML content for the manuscript template');
            }
        });

        Schema::table('tr07_test_results', function (Blueprint $table) {
            if (!Schema::hasColumn('tr07_test_results', 'tr07_manuscript_content')) {
                $table->longText('tr07_manuscript_content')->nullable()->after('m22_manuscript_id')->comment('Final edited manuscript content from analyst');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m22_manuscripts', function (Blueprint $table) {
            $table->dropColumn(['m15_standard_ids', 'm22_content']);
        });

        Schema::table('tr07_test_results', function (Blueprint $table) {
            $table->dropColumn('tr07_manuscript_content');
        });
    }
};
