<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $table = 'tr08_custom_fields';
    protected $primaryKey = 'tr08_custom_field_id';

    protected $fillable = [
        'tr04_reference_id',
        'm12_test_number',
        'm16_primary_test_id',
        'm17_secondary_test_id',
        'tr08_field_name',
        'tr08_field_value',
        'tr08_field_unit',
        'tr08_result_status',
        'tr08_test_date',
        'tr08_performance_date',
        'tr08_remarks',
        'm06_created_by',
        'm06_updated_by',
    ];

    public function registration()
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_reference_id', 'tr04_reference_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_number', 'm12_test_number');
    }

    public function primaryTest()
    {
        return $this->belongsTo(PrimaryTest::class, 'm16_primary_test_id', 'm16_primary_test_id');
    }

    public function secondaryTest()
    {
        return $this->belongsTo(SecondaryTest::class, 'm17_secondary_test_id', 'm17_secondary_test_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'm06_created_by', 'm06_employee_id');
    }

    public function updater()
    {
        return $this->belongsTo(Employee::class, 'm06_updated_by', 'm06_employee_id');
    }
}
