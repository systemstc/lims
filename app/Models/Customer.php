<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // use HasFactory;
    protected $table = 'm07_customers';
    protected $primaryKey = 'm07_customer_id';
    protected $fillable = [
        'm04_ro_id',
        'm07_name',
        'm07_email',
        'm07_phone',
        'm07_contact_person',
        'm01_state_id',
        'm02_district_id',
        'm07_address',
        'm07_pincode',
        'm07_gst',
        'm07_iec_code',
        'm07_be_no',
        'm07_status',
    ];
}
