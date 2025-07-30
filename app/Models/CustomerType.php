<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $table = 'm09_customer_types';
    protected $primaryKey = 'm09_customer_type_id';
    protected $fillable = [
        'm09_name',
        'm04_ro_id',
        'm09_amount_percent',
        'm09_type',
        'm09_remark',
        'm09_status',
        'tr01_created_by',
        'created_at',
        'updated_at'
    ];
}
