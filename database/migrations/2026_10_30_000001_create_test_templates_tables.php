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
        if (!Schema::hasTable('tr08_test_templates')) {
            Schema::create('tr08_test_templates', function (Blueprint $table) {
                $table->bigIncrements('tr08_test_template_id');
                $table->unsignedBigInteger('m12_test_id')->index();
                $table->string('tr08_test_type', 50)->default('Single');
                $table->integer('tr08_times_test_perform')->default(1);
                $table->text('tr08_formula')->nullable();
                $table->json('tr08_fields_config')->nullable();
                $table->string('tr08_status', 20)->default('YES');
                $table->unsignedBigInteger('m06_created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tr09_test_template_parameters')) {
            Schema::create('tr09_test_template_parameters', function (Blueprint $table) {
                $table->bigIncrements('tr09_parameter_id');
                $table->unsignedBigInteger('tr08_test_template_id')->index();
                $table->string('tr09_name', 255);
                $table->string('tr09_inputs', 50)->default('1');
                $table->string('tr09_min', 50)->nullable();
                $table->string('tr09_max', 50)->nullable();
                $table->text('tr09_formula')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr09_test_template_parameters');
        Schema::dropIfExists('tr08_test_templates');
    }
};

 