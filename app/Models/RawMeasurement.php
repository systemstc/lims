<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMeasurement extends Model
{
    protected $table = 'tr02_measurements';
    protected $primaryKey = 'tr02_measurement_id';
    protected $fillable = [
        'tr00_sample_id',
        'm12_test_id',
        'tr02_set_no',
        'tr02_data_point_no',
        'tr02_value',
        'tr01_created_by',
        'tr02_status',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
