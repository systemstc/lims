<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accreditation extends Model
{
    protected $table = 'm21_accreditations';
    protected $primaryKey = 'm21_accreditation_id';
    protected $fillable = [
        'm15_standard_id',
        'm04_ro_id',
        'm21_is_accredited',
        'm21_accreditation_date',
        'm21_valid_till',
        'm06_created_by',
    ];

    public function standard()
    {
        return $this->belongsTo(Standard::class, 'm15_standard_id', 'm15_standard_id');
    }

    public function ro()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'm06_created_by', 'm06_employee_id');
    }
}
