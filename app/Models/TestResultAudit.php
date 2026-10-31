<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResultAudit extends Model
{
    protected $table = 'tr10_test_result_audit';
    protected $primaryKey = 'tr10_audit_id';
    public $timestamps = false;

    protected $fillable = [
        'tr07_test_result_id',
        'tr09_version_id',
        'tr10_action',
        'tr10_field_changed',
        'tr10_old_value',
        'tr10_new_value',
        'tr10_change_reason',
        'tr10_user_id',
        'tr10_ip_address',
        'tr10_user_agent',
        'tr10_created_at',
    ];

    protected $casts = [
        'tr10_created_at' => 'datetime',
    ];

    public function testResult()
    {
        return $this->belongsTo(TestResult::class, 'tr07_test_result_id', 'tr07_test_result_id');
    }

    public function user()
    {
        return $this->belongsTo(Employee::class, 'tr10_user_id', 'm06_employee_id');
    }

    public function version()
    {
        return $this->belongsTo(TestResultVersion::class, 'tr09_version_id', 'tr09_version_id');
    }

    // Accessors for backward compatibility with templates
    public function getTr07CreatedAtAttribute()
    {
        return $this->tr10_created_at;
    }

    public function getTr07ActionAttribute()
    {
        return $this->tr10_action;
    }

    public function getTr07FieldChangedAttribute()
    {
        return $this->tr10_field_changed;
    }

    public function getTr07OldValueAttribute()
    {
        return $this->tr10_old_value;
    }

    public function getTr07NewValueAttribute()
    {
        return $this->tr10_new_value;
    }

    public function getTr07ChangeReasonAttribute()
    {
        return $this->tr10_change_reason;
    }

    public function getTr07UserIdAttribute()
    {
        return $this->tr10_user_id;
    }

    public function getTr07IpAddressAttribute()
    {
        return $this->tr10_ip_address;
    }
}

