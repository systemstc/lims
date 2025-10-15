<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementBatch extends Model
{
    use HasFactory;

    protected $table = 'tr07_settlement_batches';
    protected $primaryKey = 'tr07_batch_id';

    protected $fillable = [
        'tr07_batch_uuid',
        'tr07_settlement_date',
        'tr07_total_amount',
        'tr07_transaction_count',
        'tr07_settlement_status',
        'tr07_notes',
        'tr07_reconciled_by',
        'tr07_reconciled_at'
    ];

    protected $casts = [
        'tr07_total_amount' => 'decimal:2',
        'tr07_settlement_date' => 'date',
        'tr07_reconciled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who reconciled the batch.
     */
    public function reconciledBy()
    {
        return $this->belongsTo(User::class, 'tr07_reconciled_by');
    }

    /**
     * Scope by settlement status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('tr07_settlement_status', $status);
    }

    /**
     * Scope pending settlements.
     */
    public function scopePending($query)
    {
        return $query->where('tr07_settlement_status', 'pending');
    }

    /**
     * Scope completed settlements.
     */
    public function scopeCompleted($query)
    {
        return $query->where('tr07_settlement_status', 'completed');
    }

    /**
     * Scope by date range.
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('tr07_settlement_date', [$fromDate, $toDate]);
    }

    /**
     * Check if batch is reconciled.
     */
    public function isReconciled()
    {
        return $this->tr07_settlement_status === 'reconciled' || $this->tr07_settlement_status === 'completed';
    }

    /**
     * Mark batch as reconciled.
     */
    public function markAsReconciled($userId, $notes = null)
    {
        $this->update([
            'tr07_settlement_status' => 'reconciled',
            'tr07_reconciled_by' => $userId,
            'tr07_reconciled_at' => now(),
            'tr07_notes' => $notes
        ]);
    }
}