<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondaryTest extends Model
{
    protected $table = 'm17_secondary_tests';
    protected $primaryKey = 'm17_secondary_test_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_id',
        'm16_primary_test_id',
        'm17_name',
        'm17_unit',
        'tr01_created_by',
        'm17_status',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'm11_group_id', 'm11_group_code');
    }

    public function primaryTest()
    {
        return $this->belongsTo(PrimaryTest::class, 'm16_primary_test_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
