<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'm13_departments';
    protected $primaryKey = 'm13_department_id';
    protected $fillable = [
        'm04_ro_id',
        'm13_name',
        'm13_sample_no',
        'm13_remark',
        'tr01_created_by',
        'm13_status',
    ];

    public function ro() {
        return $this->belongsTo(Ro::class, 'm04_ro_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
