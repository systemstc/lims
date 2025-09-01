<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResultVersion extends Model
{
    protected $table = 'tr07_test_result_versions';
    protected $primaryKey = 'tr07_version_id';

    const CREATED_AT = 'tr07_changed_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'tr07_test_result_id',
        'tr07_version_number',
        'tr07_findings',
        'tr07_test_values',
        'tr07_interpretation',
        'tr07_recommendations',
        'tr07_normal_ranges',
        'tr07_abnormal_flags',
        'tr07_report_template',
        'tr07_change_reason',
        'tr07_changed_by',
        'tr07_is_current'
    ];

    protected $casts = [
        'tr07_test_values' => 'array',
        'tr07_normal_ranges' => 'array',
        'tr07_abnormal_flags' => 'array',
        'tr07_is_current' => 'boolean'
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class, 'tr07_test_result_id', 'tr07_test_result_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'tr07_changed_by');
    }
}
