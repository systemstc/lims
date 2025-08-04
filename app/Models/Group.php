<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'm11_groups';
    protected $primaryKey = 'm11_group_id';
    protected $fillable = [
        'm10_sample_id',
        'm11_group_code',
        'm11_name',
        'm11_group_charge',
        'm11_remark',
        'tr01_created_by',
        'm11_status',
    ];

    public function sample() {
        return $this->belongsTo(Sample::class, 'm10_sample_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'tr01_created_by', 'tr01_user_id');
    }
}
