<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;

class RawEntry extends Model
{
    protected $table = 'tr12_raw_entries';
    protected $primaryKey = 'tr12_raw_entry_id';
    protected $fillable = [
        'm23_formula_id',
        'tr04_reference_id',
        'm12_test_id',
        'tr12_variables',
        'tr12_calculated_output',
        'tr12_unit',
        'tr12_remarks',
        'm06_entered_by',
        'created_at',
        'updated_at'
    ];

    public function registration()
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_reference_id', 'tr04_reference_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class, 'm23_formula_id', 'm23_formula_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'm06_entered_by', 'm06_employee_id');
    }
}
