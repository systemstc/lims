<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = 'm06_employees';
    protected $primaryKey = 'm06_employee_id';
    protected $fillable = [
        'tr01_user_id',
        'm06_name',
        'm06_email',
        'm06_phone',
        'm04_ro_id',
        'm03_role_id',
        'm01_state_id',
        'm02_district_id',
        'm06_status',
        'm06_emp_id',
        'm06_valid_upto'
    ];

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
