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
        Schema::table('m12_tests', function (Blueprint $table) {
            $table->json('m17_secondary_test_associations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m12_tests', function (Blueprint $table) {
            $table->dropColumn('m17_secondary_test_associations');
        });
    }
};
