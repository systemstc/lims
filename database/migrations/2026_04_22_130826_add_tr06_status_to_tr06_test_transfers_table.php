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
        Schema::table('tr06_test_transfers', function (Blueprint $table) {
            $table->enum('tr06_status', ['PENDING', 'ACCEPTED', 'CANCELLED'])->default('PENDING')->after('tr06_remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr06_test_transfers', function (Blueprint $table) {
            $table->dropColumn('tr06_status');
        });
    }
};
