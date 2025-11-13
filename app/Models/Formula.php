<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    protected $table = 'm23_formulas';
    protected $primaryKey = 'm23_formula_id';
    protected $fillable = [
        'm23_code',
        'm23_name',
        'm23_expression',
        'm23_description',
        'm23_unit',
        'm23_status',
        'm06_created_by',
        'm06_updated_by',
        'created_at',
        'updated_at'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'm06_created_by', 'm06_employee_id');
    }
    public function updator()
    {
        return $this->belongsTo(Employee::class, 'm06_created_by', 'm06_employee_id');
    }
}
