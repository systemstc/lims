<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TestResult extends Model
{
    use HasFactory;

    protected $table = 'tr07_test_results';
    protected $primaryKey = 'tr07_test_result_id';

    protected $fillable = [
        'm04_ro_id',
        'tr07_test_result_id',
        'tr04_reference_id',
        'm12_test_number',
        'm16_primary_test_id',
        'm17_secondary_test_id',
        'm22_manuscript_id',
        'tr07_result',
        'tr07_unit',
        'tr07_current_version',
        'tr07_is_current',
        'tr07_result_status',
        'tr07_test_date',
        'tr07_performance_date',
        'tr07_remarks',
        'm06_created_by',
        'tr07_created_at',
        'm06_updated_by',
        'tr07_updated_at',
        'm06_verified_by',
        'tr07_verified_at',
        'm06_authorized_by',
        'tr07_authorized_at',
        'tr07_status',
    ];

    public function registration()
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_reference_id', 'tr04_reference_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_number', 'm12_test_number');
    }

    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class, 'm22_manuscript_id', 'm22_manuscript_id');
    }

    public function primaryTest()
    {
        return $this->belongsTo(PrimaryTest::class, 'm16_primary_test_id', 'm16_primary_test_id');
    }

    public function secondaryTest()
    {
        return $this->belongsTo(SecondaryTest::class, 'm17_secondary_test_id', 'm17_secondary_test_id');
    }

    public function testManuscripts()
    {
        return $this->hasMany(Manuscript::class, 'm12_test_number', 'm12_test_number');
    }

    public function customFields()
    {
        return $this->hasMany(CustomField::class, 'm12_test_number', 'm12_test_number');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'm06_created_by', 'm06_employee_id');
    }

    public function updater()
    {
        return $this->belongsTo(Employee::class, 'm06_updated_by', 'm06_employee_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('tr07_status', 'ACTIVE');
    }

    public function scopeByRo($query)
    {
        return $query->where('m04_ro_id', Session::get('ro_id'));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('tr07_result_status', $status);
    }

    public function scopeIsCurrent($query)
    {
        return $query->where('tr07_is_current', 'YES');
    }
}
