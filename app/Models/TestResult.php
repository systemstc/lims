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
        'tr04_sample_registration_id',
        'tr07_test_type',
        'tr07_test_name',
        'tr07_test_date',
        'tr07_current_version',
        'tr07_result_status',
        'm06_created_by',
        'created_at',
        'm06_updated_by',
        'updated_at',
        'tr07_status'
    ];

    protected $casts = [
        'tr07_test_date' => 'date',
        'tr07_is_active' => 'boolean'
    ];

    // Relationships
    public function versions()
    {
        return $this->hasMany(TestResultVersion::class, 'tr07_test_result_id', 'tr07_test_result_id')
            ->orderBy('tr07_version_number', 'desc');
    }

    public function currentVersion()
    {
        return $this->hasOne(TestResultVersion::class, 'tr07_test_result_id', 'tr07_test_result_id')
            ->where('tr07_is_current', 1);
    }

    public function auditTrail()
    {
        return $this->hasMany(TestResultAudit::class, 'tr07_test_result_id', 'tr07_test_result_id')
            ->orderBy('tr07_created_at', 'desc');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'tr07_created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'tr07_updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('tr07_is_active', 1);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('tr07_status', $status);
    }

    public function scopeByTestType($query, $type)
    {
        return $query->where('tr07_test_type', $type);
    }

    // Custom Methods
    public function createNewVersion($data, $changeReason = null)
    {
        DB::beginTransaction();

        try {
            // Mark current version as not current
            $this->versions()->update(['tr07_is_current' => 0]);

            // Increment version number
            $newVersionNumber = $this->tr07_current_version + 1;

            // Create new version
            $version = TestResultVersion::create([
                'tr07_test_result_id' => $this->tr07_test_result_id,
                'tr07_version_number' => $newVersionNumber,
                'tr07_findings' => $data['findings'] ?? '',
                'tr07_test_values' => json_encode($data['test_values'] ?? []),
                'tr07_interpretation' => $data['interpretation'] ?? '',
                'tr07_recommendations' => $data['recommendations'] ?? '',
                'tr07_normal_ranges' => json_encode($data['normal_ranges'] ?? []),
                'tr07_abnormal_flags' => json_encode($data['abnormal_flags'] ?? []),
                'tr07_report_template' => $data['report_template'] ?? '',
                'tr07_change_reason' => $changeReason,
                'tr07_changed_by' => Session::get('user_id'),
                'tr07_is_current' => 1
            ]);

            // Update main record
            $this->update([
                'tr07_current_version' => $newVersionNumber,
                'tr07_status' => 'revised',
                'tr07_updated_by' => Session::get('user_id'),
            ]);

            // Create audit trail
            $this->createAuditTrail('updated', $version->tr07_version_id, null, null, $changeReason);

            DB::commit();
            return $version;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function finalizeResult($changeReason = null)
    {
        $this->update(['tr07_status' => 'finalized']);
        $this->createAuditTrail('finalized', null, 'tr07_status', 'draft', 'finalized', $changeReason);
    }

    protected function createAuditTrail($action, $versionId = null, $field = null, $oldValue = null, $newValue = null, $reason = null)
    {
        TestResultAudit::create([
            'tr07_test_result_id' => $this->tr07_test_result_id,
            'tr07_version_id' => $versionId,
            'tr07_action' => $action,
            'tr07_field_changed' => $field,
            'tr07_old_value' => $oldValue,
            'tr07_new_value' => $newValue,
            'tr07_change_reason' => $reason,
            'tr07_user_id' => Session::get('user_id'),
            'tr07_ip_address' => request()->ip(),
            'tr07_user_agent' => request()->userAgent()
        ]);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->createAuditTrail('created');
        });

        static::deleting(function ($model) {
            $model->createAuditTrail('deleted');
        });
    }
}
