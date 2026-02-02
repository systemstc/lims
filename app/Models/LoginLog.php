<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    protected $table = 'tr00_login_logs';
    protected $primaryKey = 'tr00_login_log_id';
    protected $fillable = [
        'tr01_user_id',
        'tr00_ip_address',
        'tr00_user_agent',
        'tr00_email',
        'tr00_login_at',
        'tr00_logout_at',
        'tr00_successful',
        'tr00_failure_reason'
    ];

    protected $casts = [
        'tr00_login_at' => 'datetime',
        'tr00_logout_at' => 'datetime',
        'tr00_successful' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr01_user_id', 'tr01_user_id');
    }

    // Scope for successful logins
    public function scopeSuccessful($query)
    {
        return $query->where('tr00_successful', true);
    }

    // Scope for failed logins
    public function scopeFailed($query)
    {
        return $query->where('tr00_successful', false);
    }

    // Scope for active sessions (logged in but not logged out)
    public function scopeActiveSessions($query)
    {
        return $query->whereNotNull('tr00_login_at')
            ->whereNull('tr00_logout_at')
            ->where('tr00_successful', true);
    }
}
