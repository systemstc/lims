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
        Schema::table('m07_customers', function (Blueprint $table) {
            $table->string('m07_email')->nullable()->change();
            $table->string('m07_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m07_customers', function (Blueprint $table) {
            $table->string('m07_email')->nullable(false)->change();
            $table->string('m07_phone')->nullable(false)->change();
        });
    }
};
