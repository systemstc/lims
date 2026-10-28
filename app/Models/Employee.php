<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Employee extends Model
{
    protected $table = 'm06_employees';
    protected $primaryKey = 'm06_employee_id';
    protected $fillable = [
        'tr01_user_id',
        'm06_name',
        'm06_short_name',
        'm06_email',
        'm06_phone',
        'm04_ro_id',
        'm03_role_id',
        'm01_state_id',
        'm02_district_id',
        'm06_status',
        'm06_emp_id',
        'm06_valid_upto',
        'm06_designation'
    ];

    protected $appends = ['short_name'];

    protected static bool $columnChecked = false;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($employee) {
            if (empty($employee->m06_short_name) && !empty($employee->m06_name)) {
                $employee->m06_short_name = self::generateShortName($employee->m06_name);
            }
        });
    }

    /**
     * Ensure m06_short_name column exists and backfill existing employees.
     */
    public static function ensureShortNameColumnExists(): void
    {
        if (self::$columnChecked) {
            return;
        }
        self::$columnChecked = true;

        try {
            if (!Schema::hasColumn('m06_employees', 'm06_short_name')) {
                Schema::table('m06_employees', function (Blueprint $table) {
                    $table->string('m06_short_name', 50)->nullable()->after('m06_name');
                });
            }

            // Backfill records that have empty short_name
            $toBackfill = DB::table('m06_employees')
                ->whereNull('m06_short_name')
                ->orWhere('m06_short_name', '')
                ->select('m06_employee_id', 'm06_name')
                ->limit(200)
                ->get();

            foreach ($toBackfill as $emp) {
                $short = self::generateShortName($emp->m06_name);
                if (!empty($short)) {
                    DB::table('m06_employees')
                        ->where('m06_employee_id', $emp->m06_employee_id)
                        ->update(['m06_short_name' => $short]);
                }
            }
        } catch (\Throwable $t) {
            Log::warning('ensureShortNameColumnExists error: ' . $t->getMessage());
        }
    }

    /**
     * Generate short name initials using first letters of first and last name.
     * e.g. "Abhishek Singh" -> "AS", "Rajesh Kumar Sharma" -> "RS", "Pooja" -> "PO"
     */
    public static function generateShortName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }

        // Clean punctuation and special characters
        $cleaned = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $name));
        $words = array_values(array_filter(preg_split('/\s+/', $cleaned)));

        if (count($words) === 0) {
            return '';
        }

        // Skip professional prefixes if more names exist
        $prefixes = ['DR', 'MR', 'MRS', 'MS', 'ER', 'PROF', 'DOC'];
        if (count($words) > 2 && in_array(strtoupper($words[0]), $prefixes)) {
            array_shift($words);
        }

        if (count($words) === 1) {
            return strtoupper(mb_substr($words[0], 0, 2));
        }

        $firstLetter = mb_substr($words[0], 0, 1);
        $lastLetter = mb_substr(end($words), 0, 1);

        return strtoupper($firstLetter . $lastLetter);
    }

    /**
     * Accessor for short_name attribute
     */
    public function getShortNameAttribute(): string
    {
        if (!empty($this->attributes['m06_short_name'])) {
            return $this->attributes['m06_short_name'];
        }
        return self::generateShortName($this->attributes['m06_name'] ?? '');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr01_user_id', 'tr01_user_id');
    }
    public function ro(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'm03_role_id', 'm03_role_id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tr01_user_roles', 'tr01_user_id', 'm03_role_id', 'tr01_user_id', 'm03_role_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'm01_state_id', 'm01_state_id');
    }
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'm02_district_id', 'm02_district_id');
    }

    /**
     * Check if the employee's contract/account validity has expired.
     * Null means permanent / no expiration.
     */
    public function isExpired(): bool
    {
        if (empty($this->m06_valid_upto)) {
            return false;
        }
        return \Carbon\Carbon::parse($this->m06_valid_upto)->endOfDay()->isPast();
    }
}

