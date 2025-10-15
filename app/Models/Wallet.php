<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'tr02_wallets';
    protected $primaryKey = 'tr02_wallet_id';

    protected $fillable = [
        'tr02_wallet_uuid',
        'm07_customer_id',
        'tr02_wallet_type',
        'tr02_currency',
        'tr02_balance',
        'tr02_hold_amount',
        'tr02_status'
    ];

    protected $casts = [
        'tr02_balance' => 'decimal:2',
        'tr02_hold_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'm07_customer_id', 'm07_customer_id');
    }

    /**
     * Get all transactions for the wallet.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'tr03_wallet_id');
    }

    /**
     * Get credit transactions.
     */
    public function creditTransactions()
    {
        return $this->transactions()->where('tr03_type', 'credit');
    }

    /**
     * Get debit transactions.
     */
    public function debitTransactions()
    {
        return $this->transactions()->where('tr03_type', 'debit');
    }

    /**
     * Get hold transactions.
     */
    public function holdTransactions()
    {
        return $this->transactions()->where('tr03_type', 'hold');
    }

    /**
     * Calculate available balance.
     */
    public function getAvailableBalanceAttribute()
    {
        return $this->tr02_balance - $this->tr02_hold_amount;
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance($amount)
    {
        return $this->available_balance >= $amount;
    }

    /**
     * Scope active wallets.
     */
    public function scopeActive($query)
    {
        return $query->where('tr02_status', 'active');
    }

    /**
     * Scope by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('tr02_user_id', $userId);
    }
}
