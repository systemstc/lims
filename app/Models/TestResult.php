<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestResult extends Model
{
    use HasFactory;

    protected $table = 'tr07_test_results';
    protected $primaryKey = 'tr07_test_result_id';

    protected $fillable = [
        'tr07_test_result_id',
        'tr04_reference_id',
        'm12_test_number',
        'm22_manuscript_id',
        'tr07_result',
        'tr07_current_version',
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


    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_number', 'm12_test_number');
    }
// In TestResult model
public function manuscript()
{
    return $this->belongsTo(Manuscript::class, 'm22_manuscript_id', 'm22_manuscript_id');
}

// Add this relationship to get all manuscripts for the test
public function testManuscripts()
{
    return $this->hasMany(Manuscript::class, 'm12_test_number', 'm12_test_number');
}
    

    public function auditTrail()
    {
        return $this->hasMany(TestResultAudit::class, 'tr07_test_result_id', 'tr07_test_result_id')
            ->orderBy('tr07_created_at', 'desc');
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('tr07_result_status', $status);
    }
}
