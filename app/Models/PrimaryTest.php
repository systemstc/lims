<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimaryTest extends Model
{
    protected $table = 'm16_primary_tests';
    protected $primaryKey = 'm16_primary_test_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_code',
        'm23_formula_id',
        'm16_name',
        'm16_unit',
        'm16_requirement',
        'm16_remark',
        'tr01_created_by',
        'm16_status',
        'm14_lab_sample_ids'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'm14_lab_sample_ids' => 'array',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id', 'm10_sample_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'm11_group_code', 'm11_group_code');
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class, 'm23_formula_id', 'm23_formula_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'tr01_created_by', 'm06_employee_id');
    }
}
