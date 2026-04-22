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
        Schema::table('tr04_sample_registrations', function (Blueprint $table) {
            $table->integer('tr04_commercial_type')->nullable()->after('m09_customer_type_id')->comment('1: Commercial, 2: Non-Commercial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr04_sample_registrations', function (Blueprint $table) {
            $table->dropColumn('tr04_commercial_type');
        });
    }
};
