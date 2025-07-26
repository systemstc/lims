<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = 'm05_employees';
    protected $primaryKey = 'm05_employee_id';
    protected $fillable = [
        'm06_user_id',
        'm05_name',
        'm05_email',
        'm05_phone',
        'm05_gender',
        'm03_ro_id',
        'm04_role_id',
        'm01_state_is',
        'm02_district_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'm06_user_id');
    }
    public function ro(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm03_ro_id');
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'm04_role_id');
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'm01_state_is');
    }
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'm02_district_id');
    }
}
