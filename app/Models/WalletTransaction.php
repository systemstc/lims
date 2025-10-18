<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'tr03_wallet_transactions';
    protected $primaryKey = 'tr03_transaction_id';

    protected $fillable = [
        'tr03_transaction_uuid',
        'tr02_wallet_id',
        'tr03_type',
        'tr03_amount',
        'tr03_tax_amount',
        'tr03_gst_rate',
        'tr03_currency',
        'tr03_payment_reference',
        'tr04_sample_registration_id',
        'tr03_invoice_number',
        'tr03_description',
        'tr03_narration',
        'tr03_balance_before',
        'tr03_balance_after',
        'tr03_status',
        'm07_created_by',
        'tr03_payment_method',
        'tr03_razorpay_order_id',
        'tr03_razorpay_payment_id',
        'tr03_metadata'
    ];

    protected $casts = [
        'tr03_amount' => 'decimal:2',
        'tr03_tax_amount' => 'decimal:2',
        'tr03_gst_rate' => 'decimal:2',
        'tr03_balance_before' => 'decimal:2',
        'tr03_balance_after' => 'decimal:2',
        'tr03_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'tr03_wallet_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'm07_created_by');
    }

    /**
     * Get the sample registration associated with the transaction.
     */
    public function sampleRegistration()
    {
        return $this->belongsTo(SampleRegistration::class, 'tr04_sample_registration_id', 'tr04_sample_registration_id');
    }

    /**
     * Get payment gateway logs for this transaction.
     */
    public function paymentGatewayLogs()
    {
        return $this->hasMany(PaymentGatewayLog::class, 'tr05_wallet_transaction_id', 'tr05_wallet_transaction_id');
    }

    /**
     * Scope completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('tr03_status', 'completed');
    }

    /**
     * Scope pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('tr03_status', 'pending');
    }

    /**
     * Scope by transaction type.
     */
    public function scopeType($query, $type)
    {
        return $query->where('tr03_type', $type);
    }

    /**
     * Scope by date range.
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [$fromDate, $toDate]);
    }

    /**
     * Check if transaction is credit.
     */
    public function isCredit()
    {
        return $this->tr03_type === 'credit';
    }

    /**
     * Check if transaction is debit.
     */
    public function isDebit()
    {
        return $this->tr03_type === 'debit';
    }

    /**
     * Check if transaction is hold.
     */
    public function isHold()
    {
        return $this->tr03_type === 'hold';
    }
}
