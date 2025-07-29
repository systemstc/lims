<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ro extends Model
{
    protected $table = 'm04_ros';
    protected $primaryKey = 'm04_ro_id';
    protected $fillable = [
        'm04_name', 
        'm04_email', 
        'm04_phone', 
        'm04_password', 
        'm04_status'
    ];
}
