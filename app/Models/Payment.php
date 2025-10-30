<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'tr02_payments';
    protected $primaryKey = 'tr02_payment_id';

    protected $fillable = [
        'tr02_order_id',
        'tr02_amount',
        'tr02_currency',
        'tr02_status',
        'tr02_payment_t_id',
        'm07_customer_id',
        'tr02_type',
        'tr02_payment_verified_at',
        'tr02_failure_reason',
        'tr03_wallet_transaction_id'
    ];

    protected $casts = [
        'tr02_amount' => 'decimal:2',
        'tr02_payment_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'm07_customer_id');
    }

    /**
     * Get the wallet transaction associated with the payment.
     */
    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'tr03_wallet_transaction_id', 'tr03_transaction_id');
    }

    /**
     * Scope successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('tr02_status', 'paid');
    }

    /**
     * Scope pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('tr02_status', 'pending');
    }

    /**
     * Scope failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('tr02_status', 'failed');
    }

    /**
     * Check if payment is verified.
     */
    public function isVerified()
    {
        return !is_null($this->tr02_payment_verified_at);
    }

    /**
     * Mark payment as verified.
     */
    public function markAsVerified($transactionId = null)
    {
        $this->update([
            'tr02_status' => 'paid',
            'tr02_payment_verified_at' => now(),
            'tr02_payment_t_id' => $transactionId
        ]);
    }
}
