<?php

use App\Models\SampleRegistration;

if (!function_exists('greetUser')) {
    function greetUser($name)
    {
        return "Hello, " . ucfirst($name) . "!";
    }
}


if (!function_exists('getParties')) {
    function getParties($sampleId)
    {
        $sample = SampleRegistration::with([
            'customer',
            'customerLocation.state',
            'customerLocation.district',
            'buyer',
            'buyerLocation.state',
            'buyerLocation.district',
            'thirdParty',
            'thirdPartyLocation.state',
            'thirdPartyLocation.district',
            'cha',
            'chaLocation.state',
            'chaLocation.district'
        ])->find($sampleId);

        if (!$sample) {
            return [];
        }

        return [
            'customer' => [
                'name'     => $sample->customer?->m07_name,
                'contact_person' => $sample->m08_customer_location_id == 0 ? $sample->customer?->m07_contact_person : $sample->customerLocation?->m08_contact_person,
                'email' => $sample->m08_customer_location_id == 0 ? $sample->customer?->m07_email : $sample->customerLocation?->m08_email,
                'phone' => $sample->m08_customer_location_id == 0 ? $sample->customer?->m07_phone : $sample->customerLocation?->m08_phone,
                'gst' => $sample->m08_customer_location_id == 0 ? $sample->customer?->m07_gst : $sample->customerLocation?->m08_gst,
                'address'  => $sample->m08_customer_location_id == 0 ? $sample->customer?->m07_address : $sample->customerLocation?->m08_address,
                'state'    => $sample->m08_customer_location_id == 0 ? $sample->customer?->state?->m01_name : $sample->customerLocation?->state?->m01_name,
                'district' => $sample->m08_customer_location_id == 0 ? $sample->customer?->district?->m02_name : $sample->customerLocation?->district?->m02_name,
            ],
            'buyer' => [
                'name'     => $sample->buyer?->m07_name,
                'contact_person'  => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->m07_contact_person : $sample->buyerLocation?->m08_contact_person,
                'email'  => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->m07_email : $sample->buyerLocation?->m08_email,
                'phone'  => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->m07_phone : $sample->buyerLocation?->m08_phone,
                'gst'  => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->m07_gst : $sample->buyerLocation?->m08_gst,
                'address'  => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->m07_address : $sample->buyerLocation?->m08_address,
                'state'    => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->state?->m01_name : $sample->buyerLocation?->state?->m01_name,
                'district' => $sample->m08_buyer_location_id == 0 ? $sample->buyer?->district?->m02_name : $sample->buyerLocation?->district?->m02_name,
            ],
            'third_party' => [
                'name'     => $sample->thirdParty?->m07_name,
                'contact_person'  => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->m07_contact_person : $sample->thirdPartyLocation?->m08_contact_person,
                'email'  => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->m07_email : $sample->thirdPartyLocation?->m08_email,
                'phone'  => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->m07_phone : $sample->thirdPartyLocation?->m08_phone,
                'gst'  => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->m07_gst : $sample->thirdPartyLocation?->m08_gst,
                'address'  => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->m07_address : $sample->thirdPartyLocation?->m08_address,
                'state'    => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->state?->m01_name : $sample->thirdPartyLocation?->state?->m01_name,
                'district' => $sample->m08_third_party_location_id == 0 ? $sample->thirdParty?->district?->m02_name : $sample->thirdPartyLocation?->district?->m02_name,
            ],
            'cha' => [
                'name'     => $sample->cha?->m07_name,
                'contact_person'  => $sample->m08_cha_location_id == 0 ? $sample->cha?->m07_contact_person : $sample->chaLocation?->m08_contact_person,
                'email'  => $sample->m08_cha_location_id == 0 ? $sample->cha?->m07_email : $sample->chaLocation?->m08_email,
                'phone'  => $sample->m08_cha_location_id == 0 ? $sample->cha?->m07_phone : $sample->chaLocation?->m08_phone,
                'gst'  => $sample->m08_cha_location_id == 0 ? $sample->cha?->m07_gst : $sample->chaLocation?->m08_gst,
                'address'  => $sample->m08_cha_location_id == 0 ? $sample->cha?->m07_address : $sample->chaLocation?->m08_address,
                'state'    => $sample->m08_cha_location_id == 0 ? $sample->cha?->state?->m01_name : $sample->chaLocation?->state?->m01_name,
                'district' => $sample->m08_cha_location_id == 0 ? $sample->cha?->district?->m02_name : $sample->chaLocation?->district?->m02_name,
            ],
        ];
    }
}
