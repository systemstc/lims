<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $table = 'm10_samples';
    protected $primaryKey = 'm10_sample_id';
    protected $fillable = [
        'm10_name',
        'm10_remark',
        'tr01_created_by',
        'm10_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
