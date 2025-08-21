<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    protected $table = 'm15_standards';
    protected $primaryKey = 'm15_standard_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_id',
        'm15_method',
        'm15_description',
        'm15_accredited',
        'm15_unit',
        'm15_detection_limit',
        'm15_requirement',
        'm15_remark',
        'tr01_created_by',
        'm15_status',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'm11_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
