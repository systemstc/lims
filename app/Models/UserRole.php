<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $table = 'tr01_user_roles';
    protected $primaryKey = 'tr01_user_role_id';
    protected $fillable = [
        'tr01_user_id',
        'm03_role_id',
        'is_primary'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tr01_user_id', 'tr01_user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'm03_role_id', 'm03_role_id');
    }
}
