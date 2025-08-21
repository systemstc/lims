<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    // use HasFactory;
    protected $table = 'm07_customers';
    protected $primaryKey = 'm07_customer_id';
    protected $fillable = [
        'm04_ro_id',
        'm07_name',
        'm09_customer_type_id',
        'm07_email',
        'm07_phone',
        'm07_contact_person',
        'm01_state_id',
        'm02_district_id',
        'm07_address',
        'm07_pincode',
        'm07_gst',
        'm07_iec_code',
        'm07_status',
    ];

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'm09_customer_type_id');
    }

    public function locations()
    {
        return $this->hasMany(CustomerLocation::class, 'm07_customer_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'm01_state_id');
    }
    public function district()
    {
        return $this->hasOne(District::class, 'm02_district_id');
    }

    public function ro()
    {
        return $this->belongsTo(Ro::class, 'm04_ro_id');
    }
    public function packages()
    {
        return $this->hasMany(Package::class, 'm19_package_id');
    }
}
