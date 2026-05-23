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
        Schema::table('m04_ros', function (Blueprint $table) {
            $table->string('certificate_no')->nullable();
            $table->string('lab_name_hi')->nullable();
            $table->string('lab_name_en')->nullable();
            $table->string('ministry_hi')->nullable();
            $table->string('ministry_en')->nullable();
            $table->text('lab_address')->nullable();
            $table->string('lab_contact')->nullable();
            $table->string('lab_email')->nullable();
            $table->string('lab_website')->nullable();
        });

        Schema::table('tr04_sample_registrations', function (Blueprint $table) {
            $table->string('tr04_ulr_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m04_ros', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_no', 'lab_name_hi', 'lab_name_en', 
                'ministry_hi', 'ministry_en', 'lab_address', 
                'lab_contact', 'lab_email', 'lab_website'
            ]);
        });
        
        Schema::table('tr04_sample_registrations', function (Blueprint $table) {
            $table->dropColumn('tr04_ulr_no');
        });
    }
};
