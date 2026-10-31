<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleRegistration extends Model
{
    protected $table = 'tr04_sample_registrations';
    protected $primaryKey = 'tr04_sample_registration_id';
    protected $fillable = [
        'm04_ro_id',
        'tr04_reference_id',
        'tr04_tracker_id',
        'm09_customer_type_id',
        'tr04_commercial_type',
        'm07_customer_id',
        'm08_customer_location_id',
        'm07_buyer_id',
        'm08_buyer_location_id',
        'm07_third_party_id',
        'm08_third_party_location_id',
        'm07_cha_id',
        'm08_cha_location_id',
        'tr04_payment_by',
        'tr04_report_to',
        'tr04_reference_no',
        'tr04_reference_date',
        'tr04_received_via',
        'tr04_details',
        'm13_department_id',
        'm14_lab_sample_id',
        'tr04_sample_type',
        'tr04_number_of_samples',
        'tr04_attachment',
        'tr04_sample_description',
        'tr04_sample_mark',
        'tr04_test_type',
        'tr04_be_no',
        'm19_package_id',
        'tr04_charge_type',
        'm12_test_ids',
        'tr04_testing_charges',
        'tr04_additional_charges',
        'tr04_total_charges',
        'tr04_cgst',
        'tr04_sgst',
        'tr04_igst',
        'tr04_payment_status',
        'tr04_expected_date',
        'tr04_status',
        'tr04_created_by',
        'tr04_progress',
        'tr03_hold_transaction_id',
        'tr04_hold_amount',
        'tr04_manuscript',
        'tr04_ulr_no',
        'tr04_current_custody_role',
        'tr04_is_under_clarification',
        'tr04_latest_clarification_remark',
        'tr04_sent_back_by_name',
        'tr04_sent_back_by_role',
        'tr04_sent_back_at',
        'tr04_contract_review_status',
        'tr04_contract_reviewed_by',
        'tr04_contract_reviewed_by_name',
        'tr04_contract_reviewed_by_role',
        'tr04_contract_reviewed_at',
        'tr04_contract_review_remarks',
        'tr04_sample_condition',
        'tr04_storage_condition',
        'tr04_decision_rule',
        'tr04_feasibility_checklist',
        'tr04_customer_agreed_deviation',
    ];

    protected $casts = [
        'tr04_contract_reviewed_at' => 'datetime',
        'tr04_feasibility_checklist' => 'array',
        'tr04_customer_agreed_deviation' => 'boolean',
    ];

    protected $appends = ['parties', 'test_details'];

    public function getPartiesAttribute()
    {
        return getParties($this->tr04_sample_registration_id);
    }

    public function getTestDetailsAttribute()
    {
        $details = [];
        $testData = json_decode($this->m12_test_ids, true);
        if (!is_array($testData)) {
            return $details;
        }
        foreach ($testData as $id => $data) {
            $details[] = [
                'test'     => Test::find($data['test_id']),
                'standard' => Standard::find($data['standard_id']),
                // 'package'  => Package::find($data['package_id']),
                'remark'   => $data['remark'] ?? null,
            ];
        }
        return $details;
    }

    public function ro()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }
    public function testResult()
    {
        return $this->hasMany(TestResult::class, 'tr04_reference_id', 'tr04_reference_id');
    }
    public function customFields()
    {
        return $this->hasMany(CustomField::class, 'tr04_reference_id', 'tr04_reference_id');
    }

    public function sampleTests()
    {
        return $this->hasMany(SampleTest::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id');
    }
    public function additional()
    {
        return $this->hasMany(SampleAdditional::class, 'sample_id', 'tr04_sample_registration_id');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'm09_customer_type_id', 'm09_customer_type_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'm13_department_id', 'm13_department_id');
    }
    public function labSample()
    {
        return $this->belongsTo(LabSample::class, 'm14_lab_sample_id', 'm14_lab_sample_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'm07_customer_id', 'm07_customer_id');
    }
    public function customerLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_customer_location_id', 'm08_customer_location_id');
    }

    // Buyer
    public function buyer()
    {
        return $this->belongsTo(Customer::class, 'm07_buyer_id', 'm07_customer_id');
    }
    public function buyerLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_buyer_location_id', 'm08_customer_location_id');
    }

    // Third Party
    public function thirdParty()
    {
        return $this->belongsTo(Customer::class, 'm07_third_party_id', 'm07_customer_id');
    }
    public function thirdPartyLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_third_party_location_id', 'm08_customer_location_id');
    }

    // CHA
    public function cha()
    {
        return $this->belongsTo(Customer::class, 'm07_cha_id', 'm07_customer_id');
    }
    public function chaLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_cha_location_id', 'm08_customer_location_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'm19_package_id', 'm19_package_id');
    }

    public function holdTransaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'tr03_hold_transaction_id', 'tr03_transaction_id');
    }

    /**
     * Get all wallet transactions for the sample.
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id');
    }

    /**
     * Get the user who created the sample.
     */
    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'tr04_created_by', 'm06_employee_id');
    }

    public function creatorEmployee()
    {
        return $this->belongsTo(Employee::class, 'tr04_created_by', 'm06_employee_id');
    }

    public function reports()
    {
        return $this->hasMany(TestReport::class, 'tr04_reference_id', 'tr04_reference_id')
            ->orderBy('tr09_version_number', 'desc');
    }

    public function latestReport()
    {
        return $this->hasOne(TestReport::class, 'tr04_reference_id', 'tr04_reference_id')
            ->where('tr09_is_current', 'YES');
    }

    /**
     * Scope by payment status.
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('tr04_payment_status', $status);
    }

    /**
     * Scope by sample status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('tr04_status', $status);
    }

    /**
     * Scope ready for reporting samples.
     */
    public function scopeReadyForReporting($query)
    {
        return $query->where('tr04_status', 'testing')->where('tr04_progress', '>=', 80);
    }

    /**
     * Check if sample is paid.
     */
    public function isPaid()
    {
        return $this->tr04_payment_status === 'PAID';
    }

    /**
     * Check if sample has hold amount.
     */
    public function hasHold()
    {
        return $this->tr04_payment_status === 'HOLD' && $this->tr04_hold_amount > 0;
    }

    /**
     * Check if sample can be processed to reporting.
     */
    public function canProcessToReporting()
    {
        return in_array($this->tr04_status, ['testing', 'ready-for-reporting']) &&
            $this->tr04_progress >= 80;
    }

    /**
     * Sample Custody Logs history.
     */
    public function custodyLogs()
    {
        return $this->hasMany(SampleCustodyLog::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id')
            ->orderByDesc('tr10_custody_log_id');
    }

    /**
     * Latest custody log.
     */
    public function latestCustodyLog()
    {
        return $this->hasOne(SampleCustodyLog::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id')
            ->latestOfMany('tr10_custody_log_id');
    }

    /**
     * Check if sample is currently under clarification.
     */
    public function isUnderClarification(): bool
    {
        return (bool)($this->tr04_is_under_clarification ?? 0);
    }

    /**
     * Uploaded multi-analyst, multi-page paper manuscripts.
     */
    public function manuscripts()
    {
        return $this->hasMany(SampleManuscript::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id')
            ->orderBy('m06_analyst_id')
            ->orderBy('page_number');
    }

    /**
     * Contract Reviewer Employee.
     */
    public function contractReviewer()
    {
        return $this->belongsTo(Employee::class, 'tr04_contract_reviewed_by', 'm06_employee_id');
    }

    /**
     * Get Contract Review Status Badge HTML.
     */
    public function getContractReviewBadgeAttribute(): string
    {
        $status = strtoupper($this->tr04_contract_review_status ?? 'PENDING');
        return match ($status) {
            'ACCEPTED' => '<span class="badge bg-success"><em class="icon ni ni-check-circle me-1"></em>Accepted</span>',
            'ACCEPTED_WITH_DEVIATION' => '<span class="badge bg-info text-white"><em class="icon ni ni-alert-circle me-1"></em>Accepted w/ Deviation</span>',
            'CLARIFICATION_REQUIRED' => '<span class="badge bg-warning text-dark"><em class="icon ni ni-help-circle me-1"></em>Clarification Required</span>',
            'REJECTED' => '<span class="badge bg-danger"><em class="icon ni ni-cross-circle me-1"></em>Rejected</span>',
            default => '<span class="badge bg-secondary"><em class="icon ni ni-clock me-1"></em>Pending Review</span>',
        };
    }
}

