<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimaryTest extends Model
{
    protected $table = 'm16_primary_tests';
    protected $primaryKey = 'm16_primary_test_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_id',
        'm16_name',
        'm16_unit',
        'm16_requirement',
        'm16_remark',
        'tr01_created_by',
        'm16_status',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'm11_group_id', 'm11_group_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
