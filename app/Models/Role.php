<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'm04_roles';
    protected $primaryKey = 'm04_role_id';
    protected $fillable = [
        'm04_name',
        'm04_status'
    ];
}
