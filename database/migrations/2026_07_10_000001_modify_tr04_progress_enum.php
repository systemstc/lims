<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE tr04_sample_registrations MODIFY COLUMN tr04_progress VARCHAR(50) DEFAULT 'REGISTERED'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
