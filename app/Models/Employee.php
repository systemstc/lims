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
        'm06_status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr01_user_id');
    }
    public function ro(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id');
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'm03_role_id');
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'm01_state_id');
    }
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'm02_district_id');
    }
}
