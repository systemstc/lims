<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ro extends Model
{
    protected $table = 'm04_ros';
    protected $primaryKey = 'm04_ro_id';
    protected $fillable = [
        'tr01_user_di',
        'm04_name', 
        'm04_email', 
        'm04_phone', 
        'm04_status'
    ];
}
