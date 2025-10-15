<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletAuditLog extends Model
{
    use HasFactory;

    protected $table = 'tr06_wallet_audit_logs';
    protected $primaryKey = 'tr06_audit_id';

    protected $fillable = [
        'tr06_audit_uuid',
        'tr06_entity_type',
        'tr06_entity_id',
        'tr06_action',
        'tr06_changed_by',
        'tr06_old_value',
        'tr06_new_value',
        'tr06_ip_address',
        'tr06_user_agent'
    ];

    protected $casts = [
        'tr06_old_value' => 'array',
        'tr06_new_value' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who made the change.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'tr06_changed_by');
    }

    /**
     * Scope by entity type.
     */
    public function scopeEntityType($query, $entityType)
    {
        return $query->where('tr06_entity_type', $entityType);
    }

    /**
     * Scope by entity ID.
     */
    public function scopeEntityId($query, $entityId)
    {
        return $query->where('tr06_entity_id', $entityId);
    }

    /**
     * Scope by action.
     */
    public function scopeAction($query, $action)
    {
        return $query->where('tr06_action', $action);
    }

    /**
     * Scope by date range.
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [$fromDate, $toDate]);
    }

    /**
     * Get the related entity based on entity type.
     */
    public function entity()
    {
        if ($this->tr06_entity_type === 'wallet') {
            return $this->belongsTo(Wallet::class, 'tr06_entity_id', 'tr02_wallet_id');
        } elseif ($this->tr06_entity_type === 'transaction') {
            return $this->belongsTo(WalletTransaction::class, 'tr06_entity_id', 'tr03_transaction_id');
        }

        return null;
    }
}
