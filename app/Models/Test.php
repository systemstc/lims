<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'm12_tests';
    protected $primaryKey = 'm12_test_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_id',
        'm12_name',
        'm12_description',
        'm15_standard_id',
        'm16_primary_test_id',
        'm17_secondary_test_id',
        'm14_lab_sample_id',
        'm12_result',
        'm12_category',
        'm12_input_mode',
        'm12_stages',
        'm12_output_metrics',
        'm12_alias',
        'm12_weight',
        'm12_unit',
        'm12_charge',
        'm12_instrument',
        'm13_department_id',
        'm12_remark',
        'm12_status',
        'tr01_created_by',
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'm13_department_id');
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'm12_test_id');
    }

    public function getLabSamplesListAttribute()
    {
        $ids = $this->m14_lab_sample_id
            ? explode(',', $this->m14_lab_sample_id)
            : [];
        return LabSample::whereIn('m14_lab_sample_id', $ids)->get();
    }

    public function getStandardsListAttribute()
    {
        $ids = $this->m15_standard_id
            ? explode(',', $this->m15_standard_id)
            : [];

        return Standard::whereIn('m15_standard_id', $ids)->get();
    }

    public function getSecondaryTestsListAttribute()
    {
        $ids = $this->m17_secondary_test_id
            ? explode(',', $this->m17_secondary_test_id)
            : [];

        return Test::whereIn('m12_test_id', $ids)->get();
    }
}
