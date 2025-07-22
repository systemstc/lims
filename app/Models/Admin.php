<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'm00_admins';
    protected $primaryKey = 'm00_admin_id';
    protected $fillable = [
        'm00_name',
        'm00_email', 
        'm00_password',
        'm00_status'
    ];
}
