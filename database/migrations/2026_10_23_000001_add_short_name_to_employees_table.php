<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Employee;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m06_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('m06_employees', 'm06_short_name')) {
                $table->string('m06_short_name', 50)->nullable()->after('m06_name');
            }
        });

        // Backfill existing employees with their generated short name initials
        try {
            $employees = DB::table('m06_employees')
                ->select('m06_employee_id', 'm06_name')
                ->whereNull('m06_short_name')
                ->orWhere('m06_short_name', '')
                ->get();

            foreach ($employees as $emp) {
                $shortName = Employee::generateShortName($emp->m06_name);
                if (!empty($shortName)) {
                    DB::table('m06_employees')
                        ->where('m06_employee_id', $emp->m06_employee_id)
                        ->update(['m06_short_name' => $shortName]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore backfill errors during migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m06_employees', function (Blueprint $table) {
            if (Schema::hasColumn('m06_employees', 'm06_short_name')) {
                $table->dropColumn('m06_short_name');
            }
        });
    }
};
