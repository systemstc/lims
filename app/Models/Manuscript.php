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
        'm15_standard_ids',
        'm22_content',
        'created_at',
        'updated_at'
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'm10_sample_id', 'm10_sample_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'm11_group_code', 'm11_group_code');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_number', 'm12_test_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }

    public function getStandardsListAttribute()
    {
        $ids = $this->m15_standard_ids
            ? explode(',', $this->m15_standard_ids)
            : [];

        return Standard::whereIn('m15_standard_id', $ids)->get();
    }
}
