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

    public function user()
    {
        return $this->belongsTo('tr01_created_at', 'tr01_user_id');
    }
}
