<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m06_employees', function (Blueprint $table) {
            $table->string('m06_emp_id')->nullable()->after('m06_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m06_employees', function (Blueprint $table) {
            $table->dropColumn('m06_emp_id');
        });
    }
};
