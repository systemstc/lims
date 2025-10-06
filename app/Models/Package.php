<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'm19_packages';
    protected $primaryKey = 'm19_package_id';
    protected $fillable = [
        'm19_name',
        'm19_charges',
        'm19_description',
        'm19_start_date',
        'm19_end_date',
        'm12_tests',
        'm19_type',
        'm07_contract_with',
        'm19_exp_date',
        'm19_order_by',
        'm19_status',
        'tr01_created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'tr01_user_id', 'tr01_created_by');
    }

    public function packageTests()
    {
        return $this->hasMany(PackageTest::class, 'm19_package_id', 'm19_package_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'm07_contract_with', 'm07_customer_id');
    }
}
