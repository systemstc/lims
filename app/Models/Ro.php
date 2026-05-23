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
        'gst_no',
        'certificate_no',
        'lab_name_hi',
        'lab_name_en',
        'ministry_hi',
        'ministry_en',
        'lab_address',
        'lab_contact',
        'lab_email',
        'lab_website'
    ];


    public function ro()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id', 'm04_ro_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'm03_role_id', 'm03_role_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_user_id', 'tr01_user_id');
    }
}
