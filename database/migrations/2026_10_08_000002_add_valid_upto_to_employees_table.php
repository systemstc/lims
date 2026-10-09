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
        Schema::table('m06_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('m06_employees', 'm06_valid_upto')) {
                $table->date('m06_valid_upto')->nullable()->after('m06_emp_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m06_employees', function (Blueprint $table) {
            if (Schema::hasColumn('m06_employees', 'm06_valid_upto')) {
                $table->dropColumn('m06_valid_upto');
            }
        });
    }
};
