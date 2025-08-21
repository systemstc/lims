<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleRegistration extends Model
{
    protected $table = 'tr04_sample_registrations';
    protected $primaryKey = 'tr04_sample_registration_id';
    protected $fillable = [
        'm04_ro_id',
        'tr04_tracker_id',
        'm09_customer_type_id',
        'm07_customer_id',
        'm07_buyer_id',
        'm07_third_party_id',
        'm07_cha_id',
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
        'm11_group_id',
        'm12_test_ids',
        'tr04_testing_charges',
        'tr04_additional_charges',
        'tr04_total_charges',
        'tr04_expected_date',
        'tr04_status',
        'tr04_created_by',
    ];
}
