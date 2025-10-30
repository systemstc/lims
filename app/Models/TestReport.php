<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestReport extends Model
{
    protected $table = 'tr09_test_reports';
    protected $primaryKey = 'tr09_test_report_id';
    protected $fillable = [
        'm04_ro_id',
        'tr04_reference_id',
        'tr09_version_number',
        'm06_generated_by',
        'tr09_generated_at',
        'tr09_report_data',
        'tr09_report_file_path',
        'tr09_status',
        'tr09_is_current',
        'created_at',
        'updated_at'
    ];

    public function role()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }

    public function generator()
    {
        return $this->belongsTo(Employee::class, 'm06_generated_by', 'm06_employee_id');
    }

    public function sample()
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_reference_id', 'tr04_reference_id');
    }
}
