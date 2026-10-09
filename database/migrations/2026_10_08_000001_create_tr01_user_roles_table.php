<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tr01_user_roles')) {
            Schema::create('tr01_user_roles', function (Blueprint $table) {
                $table->id('tr01_user_role_id');
                $table->unsignedBigInteger('tr01_user_id');
                $table->unsignedBigInteger('m03_role_id');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['tr01_user_id', 'm03_role_id'], 'uq_user_role');
            });
        }

        // Backfill existing roles from m06_employees
        if (Schema::hasTable('m06_employees') && Schema::hasTable('tr01_user_roles')) {
            $employees = DB::table('m06_employees')
                ->whereNotNull('tr01_user_id')
                ->whereNotNull('m03_role_id')
                ->get();

            foreach ($employees as $emp) {
                DB::table('tr01_user_roles')->updateOrInsert(
                    ['tr01_user_id' => $emp->tr01_user_id, 'm03_role_id' => $emp->m03_role_id],
                    ['is_primary' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Backfill existing roles from m04_ros
        if (Schema::hasTable('m04_ros') && Schema::hasTable('tr01_user_roles')) {
            $ros = DB::table('m04_ros')
                ->whereNotNull('tr01_user_id')
                ->whereNotNull('m03_role_id')
                ->get();

            foreach ($ros as $ro) {
                DB::table('tr01_user_roles')->updateOrInsert(
                    ['tr01_user_id' => $ro->tr01_user_id, 'm03_role_id' => $ro->m03_role_id],
                    ['is_primary' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr01_user_roles');
    }
};
