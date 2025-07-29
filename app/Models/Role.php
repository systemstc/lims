<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'm03_roles';
    protected $primaryKey = 'm03_role_id';
    protected $fillable = [
        'm03_name',
        'm03_status'
    ];
}
