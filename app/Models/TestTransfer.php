<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TestTransfer extends Model
{
    protected $table = 'tr06_test_transfers';
    protected $primaryKey = 'tr06_test_transfer_id';
    public $timestamps = true;

    protected $fillable = [
        'tr05_sample_test_id',
        'm04_from_ro_id',
        'm04_to_ro_id',
        'm06_transferred_by',
        'm06_received_by',
        'tr06_transferred_at',
        'tr06_received_at',
        'tr06_reason',
        'tr06_remark',
        'tr06_status',
    ];

    protected $casts = [
        'tr06_transferred_at' => 'datetime',
        'tr06_received_at' => 'datetime',
    ];

    // Relationships
    public function sampleTest(): BelongsTo
    {
        return $this->belongsTo(SampleTest::class, 'tr05_sample_test_id', 'tr05_sample_test_id');
    }

    public function fromRo(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_from_ro_id', 'm04_ro_id');
    }

    public function toRo(): BelongsTo
    {
        return $this->belongsTo(Ro::class, 'm04_to_ro_id', 'm04_ro_id');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'm06_transferred_by', 'm06_employee_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'm06_received_by', 'm06_employee_id');
    }

    // Query Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('tr06_status', 'PENDING');
    }

    public function scopeReceived(Builder $query): Builder
    {
        return $query->where('tr06_status', 'ACCEPTED');
    }

    public function scopeForRo(Builder $query, $roId): Builder
    {
        return $query->where(function($q) use ($roId) {
            $q->where('m04_from_ro_id', $roId)->orWhere('m04_to_ro_id', $roId);
        });
    }

    public function scopeTransferredFrom(Builder $query, $roId): Builder
    {
        return $query->where('m04_from_ro_id', $roId);
    }

    public function scopeTransferredTo(Builder $query, $roId): Builder
    {
        return $query->where('m04_to_ro_id', $roId);
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->tr06_status === 'PENDING';
    }

    public function isReceived(): bool
    {
        return $this->tr06_status === 'ACCEPTED';
    }

    public function getStatusLabel(): string
    {
        return ucfirst(strtolower($this->tr06_status));
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->tr06_status) {
            'ACCEPTED' => 'bg-success',
            'CANCELLED' => 'bg-danger',
            default => 'bg-warning',
        };
    }

    public function getDurationInHours(): ?float
    {
        if (!$this->tr06_received_at) {
            return $this->tr06_transferred_at->diffInHours(now(), true);
        }
        
        return $this->tr06_transferred_at->diffInHours($this->tr06_received_at, true);
    }
}