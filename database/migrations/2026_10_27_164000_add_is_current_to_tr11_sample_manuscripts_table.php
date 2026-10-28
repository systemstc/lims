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
        if (Schema::hasTable('tr11_sample_manuscripts')) {
            if (!Schema::hasColumn('tr11_sample_manuscripts', 'is_current')) {
                Schema::table('tr11_sample_manuscripts', function (Blueprint $table) {
                    $table->boolean('is_current')->default(true)->index()->after('total_pages');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tr11_sample_manuscripts')) {
            if (Schema::hasColumn('tr11_sample_manuscripts', 'is_current')) {
                Schema::table('tr11_sample_manuscripts', function (Blueprint $table) {
                    $table->dropColumn('is_current');
                });
            }
        }
    }
};
