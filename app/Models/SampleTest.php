<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class SampleTest extends Model
{
    protected $table = 'tr05_sample_tests';
    protected $primaryKey = 'tr05_sample_test_id';
    public $timestamps = true;

    protected $fillable = [
        'tr04_sample_registration_id',
        'm12_test_id',
        'm12_test_number',
        'm16_primary_test_id',
        'm17_secondary_test_id',
        'm15_standard_id',
        'm06_alloted_to',
        'm06_alloted_by',
        'm04_ro_id',
        'tr05_status',
        'tr05_priority',
        'tr05_remark',
        'tr05_alloted_at',
        'm04_transferred_to',
        'm04_transferred_by',
        'tr05_transferred_at',
        'tr05_reassigned_at',
        'tr05_accepted_at',
        'tr05_completed_at'
    ];
    protected $casts = [
        'tr05_alloted_at' => 'datetime',
        'tr05_transferred_at' => 'datetime',
        'tr05_reassigned_at' => 'datetime',
        'tr05_completed_at' => 'datetime',
    ];

    // Relationships
    public function registration(): BelongsTo
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }
    public function manuscript()
    {
        return $this->hasMany(Manuscript::class, 'm12_test_number', 'm12_test_number');
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class, 'm15_standard_id', 'm15_standard_id');
    }

    public function allotedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'm06_alloted_to', 'm06_employee_id');
    }

    public function allotedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'm06_alloted_by', 'm06_employee_id');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_transferred_by', 'm04_ro_id');
    }

    public function ro(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }

    public function transferredToRo(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_transferred_to', 'm04_ro_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(TestTransfer::class, 'tr05_sample_test_id', 'tr05_sample_test_id')
            ->orderBy('tr06_transferred_at', 'desc');
    }

    public function latestTransfer(): HasMany
    {
        return $this->hasMany(TestTransfer::class, 'tr05_sample_test_id', 'tr05_sample_test_id')
            ->latest('tr06_transferred_at')
            ->limit(1);
    }

    // Accessors
    public function getPrimaryTestsAttribute()
    {
        if (!$this->m16_primary_test_id) {
            return collect();
        }

        $ids = explode(',', $this->m16_primary_test_id);
        return PrimaryTest::whereIn('m16_primary_test_id', $ids)->get();
    }

    public function getSecondaryTestsAttribute()
    {
        if (!$this->m17_secondary_test_id) {
            return collect();
        }

        $ids = explode(',', $this->m17_secondary_test_id);
        return SecondaryTest::whereIn('m17_secondary_test_id', $ids)->get();
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->tr05_status) {
                'PENDING' => 'Pending',
                'ALLOTED' => 'Alloted',
                'IN_PROGRESS' => 'In Progress',
                'COMPLETED' => 'Completed',
                'TRANSFERRED' => 'Transferred',
                'RECEIVED_ACCEPTED' => 'Received & Accepted',
                default => 'Unknown',
            };
        });
    }

    protected function canBeAllotted(): Attribute
    {
        return Attribute::get(function () {
            return in_array($this->tr05_status, ['PENDING']) && is_null($this->m06_alloted_to);
        });
    }

    protected function canBeTransferred(): Attribute
    {
        return Attribute::get(function () {
            return !in_array($this->tr05_status, ['COMPLETED', 'TRANSFERRED', 'RECEIVED_ACCEPTED']);
        });
    }

    protected function isTransferPending(): Attribute
    {
        return Attribute::get(function () {
            return $this->tr05_status === 'TRANSFERRED' && !is_null($this->m04_transferred_to);
        });
    }

    // Query Scopes
    public function scopeForRo(Builder $query, $roId): Builder
    {
        return $query->where(function ($q) use ($roId) {
            $q->where('m04_ro_id', $roId)->orWhere('m04_transferred_to', $roId);
        });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('tr05_status', 'PENDING')->whereNull('m06_alloted_to');
    }

    public function scopeAllotted(Builder $query): Builder
    {
        return $query->where('tr05_status', 'ALLOTTED')->whereNotNull('m06_alloted_to');
    }

    public function scopeTransferred(Builder $query): Builder
    {
        return $query->where('tr05_status', 'TRANSFERRED');
    }

    public function scopeTransferredTo(Builder $query, $roId): Builder
    {
        return $query->where('tr05_status', 'TRANSFERRED')->where('m04_transferred_to', $roId);
    }

    public function scopeByPriority(Builder $query, $priority = null): Builder
    {
        if ($priority) {
            return $query->where('tr05_priority', $priority);
        }
        return $query->orderByRaw("
            CASE tr05_priority 
                WHEN 'URGENT' THEN 1
                WHEN 'HIGH' THEN 2  
                WHEN 'NORMAL' THEN 3
                WHEN 'LOW' THEN 4
                ELSE 5
            END
        ");
    }

    // Helper Methods
    public function canAcceptTransfer($currentRoId): bool
    {
        return $this->tr05_status === 'TRANSFERRED' &&
            $this->m04_transferred_to == $currentRoId &&
            $this->transfers()->whereNull('m06_received_by')->exists();
    }

    public function getTransferHistory()
    {
        return $this->transfers()
            ->with(['sampleTest.test', 'fromRo', 'toRo', 'transferredBy', 'receivedBy'])
            ->get();
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->tr05_status) {
            'PENDING' => 'bg-warning',
            'ALLOTTED' => 'bg-info',
            'IN_PROGRESS' => 'bg-primary',
            'COMPLETED' => 'bg-success',
            'TRANSFERRED' => 'bg-secondary',
            'RECEIVED_ACCEPTED' => 'bg-dark',
            default => 'bg-light',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->tr05_priority ?? 'NORMAL') {
            'URGENT' => 'bg-danger',
            'NORMAL' => 'bg-info',
            default => 'bg-light',
        };
    }
}
