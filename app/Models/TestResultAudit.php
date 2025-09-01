<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResultAudit extends Model
{
    protected $table = 'tr07_test_result_audit';
    protected $primaryKey = 'tr07_audit_id';

    const CREATED_AT = 'tr07_created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'tr07_test_result_id',
        'tr07_version_id',
        'tr07_action',
        'tr07_field_changed',
        'tr07_old_value',
        'tr07_new_value',
        'tr07_change_reason',
        'tr07_user_id',
        'tr07_ip_address',
        'tr07_user_agent'
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class, 'tr07_test_result_id', 'tr07_test_result_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr07_user_id');
    }

    public function version()
    {
        return $this->belongsTo(TestResultVersion::class, 'tr07_version_id', 'tr07_version_id');
    }
}
