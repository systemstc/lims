<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ro extends Model
{
    protected $table = 'm04_ros';
    protected $primaryKey = 'm04_ro_id';
    protected $fillable = [
        'tr01_user_id',
        'm04_name', 
        'm04_email', 
        'm04_phone', 
        'm03_role_id',
        'm04_status',
        'cgst',
        'sgst',
        'igst',
        'gst_no'
    ];


    public function ro()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }
}
