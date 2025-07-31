<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nette\DirectoryNotFoundException;
use PhpParser\Node\Expr\FuncCall;

class CustomerLocation extends Model
{
    protected $table = 'm08_customer_locations';
    protected $primaryKey = 'm08_customer_location_id';
    protected $fillable = [
        'm07_customer_id',
        'm08_contact_person',
        'm08_email',
        'm08_phone',
        'm08_gst',
        'm01_state_id',
        'm02_district_id',
        'm08_pincode',
        'm08_address',
        'm08_status',
        'created_at',
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'm07_customer_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'm01_state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'm02_district_id');
    }
}
