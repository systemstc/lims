<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $table  = 'm18_stages';
    protected $primaryKey = 'm18_stage_id';
    protected $fillable = [
        'm12_test_id',
        'm18_name',
        'm18_inputs',
        'm18_outputs',
        'tr01_created_by',
        'm18_status',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class, 'm12_test_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }

    public function getInputsArrayAttribute()
    {
        return $this->m18_inputs ? array_map('trim', explode(',', $this->m18_inputs)) : [];
    }

    public function getOutputsArrayAttribute()
    {
        return $this->m18_outputs ? array_map('trim', explode(',', $this->m18_outputs)) : [];
    }

    public function scopeForTest($query, $testId)
    {
        return $query->where('m12_test_id', $testId)->orderBy('m18_stage_number');
    }

    public function scopeActive($query)
    {
        return $query->where('m18_status', 'ACTIVE');
    }
}
