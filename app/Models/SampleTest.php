<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SampleTest extends Model
{
    protected $table = 'tr05_sample_tests';
    protected $primaryKey = 'tr05_sample_test_id';

    protected $fillable = [
        'tr04_sample_registration_id',
        'm12_test_id',
        'm16_primary_test_id',
        'm17_secondary_test_id',
        'm15_standard_id',
        'm06_alloted_to',
        'm04_ro_id',
        'tr05_status',
        'tr05_remark',
        'tr05_alloted_at',
        'm04_transferred_to',
        'tr05_transferred_at',
        'tr05_reassigned_at',
        'tr05_completed_at'
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

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class, 'm15_standard_id', 'm15_standard_id');
    }

    public function allotedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'm06_alloted_to', 'm06_employee_id');
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
                'NEW'        => 'New',
                'ALLOTTED'   => 'Allotted',
                'IN_PROGRESS' => 'In Progress',
                'COMPLETED'  => 'Completed',
                default      => 'Unknown',
            };
        });
    }

    public function transfers()
    {
        return $this->hasMany(TestTransfer::class, 'tr05_sample_test_id', 'tr05_sample_test_id');
    }
}
