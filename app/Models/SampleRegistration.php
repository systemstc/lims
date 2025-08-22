<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleRegistration extends Model
{
    protected $table = 'tr04_sample_registrations';
    protected $primaryKey = 'tr04_sample_registration_id';
    protected $fillable = [
        'm04_ro_id',
        'tr04_reference_id',
        'tr04_tracker_id',
        'm09_customer_type_id',
        'm07_customer_id',
        'm08_customer_location_id',
        'm07_buyer_id',
        'm08_buyer_location_id',
        'm07_third_party_id',
        'm08_third_party_location_id',
        'm07_cha_id',
        'm08_cha_location_id',
        'tr04_payment_by',
        'tr04_report_to',
        'tr04_reference_no',
        'tr04_reference_date',
        'tr04_received_via',
        'tr04_details',
        'm14_lab_sample_id',
        'tr04_sample_type',
        'tr04_attachment',
        'tr04_sample_description',
        'tr04_test_type',
        'm12_test_ids',
        'tr04_testing_charges',
        'tr04_additional_charges',
        'tr04_total_charges',
        'tr04_payment',
        'tr04_expected_date',
        'tr04_status',
        'tr04_created_by',
        'tr04_progress',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'm07_customer_id', 'm07_customer_id');
    }
    public function customerLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_customer_location_id', 'm08_customer_location_id');
    }

    // Buyer
    public function buyer()
    {
        return $this->belongsTo(Customer::class, 'm07_buyer_id', 'm07_customer_id');
    }
    public function buyerLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_buyer_location_id', 'm08_customer_location_id');
    }

    // Third Party
    public function thirdParty()
    {
        return $this->belongsTo(Customer::class, 'm07_third_party_id', 'm07_customer_id');
    }
    public function thirdPartyLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_third_party_location_id', 'm08_customer_location_id');
    }

    // CHA
    public function cha()
    {
        return $this->belongsTo(Customer::class, 'm07_cha_id', 'm07_customer_id');
    }
    public function chaLocation()
    {
        return $this->belongsTo(CustomerLocation::class, 'm08_cha_location_id', 'm08_customer_location_id');
    }
}
