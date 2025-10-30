<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayLog extends Model
{
    use HasFactory;

    protected $table = 'tr05_payment_gateway_logs';
    protected $primaryKey = 'tr05_pg_log_id';

    protected $fillable = [
        'tr05_pg_transaction_id',
        'tr05_wallet_transaction_id',
        'tr05_gateway',
        'tr05_gateway_order_id',
        'tr05_gateway_payment_id',
        'tr05_status',
        'tr05_request_payload',
        'tr05_response_payload',
        'tr05_error_message'
    ];

    protected $casts = [
        'tr05_request_payload' => 'array',
        'tr05_response_payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the wallet transaction associated with the log.
     */
    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'tr05_wallet_transaction_id');
    }

    /**
     * Scope by gateway.
     */
    public function scopeGateway($query, $gateway)
    {
        return $query->where('tr05_gateway', $gateway);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('tr05_status', $status);
    }

    /**
     * Scope successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('tr05_status', 'success');
    }

    /**
     * Scope failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('tr05_status', 'failed');
    }
}