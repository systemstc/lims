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
            $table->unsignedBigInteger('m09_customer_type_id')->nullable()->change();
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
            $table->unsignedBigInteger('m09_customer_type_id')->nullable(false)->change();
        });
    }
};
