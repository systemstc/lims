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
        Schema::table('m21_accreditations', function (Blueprint $table) {
            $table->unsignedBigInteger('m12_test_id')->nullable()->after('m15_standard_id');
            // Not adding a strict foreign key constraint here to prevent issues with mismatched types
            // $table->foreign('m12_test_id')->references('m12_test_id')->on('m12_tests')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m21_accreditations', function (Blueprint $table) {
            $table->dropColumn('m12_test_id');
        });
    }
};
