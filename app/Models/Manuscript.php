<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manuscript extends Model
{
    protected $table = 'm22_manuscripts';
    protected $primaryKey = 'm22_manuscript_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_code',
        'm12_test_number',
        'm22_name',
        'tr01_created_by',
        'm22_status',
        'created_at',
        'updated_at'
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id', 'm10_sample_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'm11_group_id', 'm11_group_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id', 'm12_test_id');
    }

    public function user()
    {
        return $this->belongsTo('tr01_created_at', 'tr01_user_id');
    }
}
