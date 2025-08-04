<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabSample extends Model
{
    protected $table = 'm14_lab_samples';
    protected $primaryKey = 'm14_lab_sample_id';
    protected $fillable = [
        'm04_ro_id',
        'm10_sample_id',
        'm14_name',
        'm14_order_by',
        'm14_sample_no',
        'm14_remark',
        'tr01_created_by',
        'm14_status',
    ];

    public function ro()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id');
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
