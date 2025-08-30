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
            'chaLocation.district',
        ])->find($sampleId);

        if (!$sample) {
            return [];
        }

        $formatParty = function ($party, $location, $locationId) {
            return [
                'name'           => $party?->m07_name,
                'contact_person' => $locationId == 0 ? $party?->m07_contact_person : $location?->m08_contact_person,
                'email'          => $locationId == 0 ? $party?->m07_email : $location?->m08_email,
                'phone'          => $locationId == 0 ? $party?->m07_phone : $location?->m08_phone,
                'gst'            => $locationId == 0 ? $party?->m07_gst : $location?->m08_gst,
                'address'        => $locationId == 0 ? $party?->m07_address : $location?->m08_address,
                'state'          => $locationId == 0 ? $party?->state?->m01_name : $location?->state?->m01_name,
                'district'       => $locationId == 0 ? $party?->district?->m02_name : $location?->district?->m02_name,
            ];
        };

        return [
            'customer'    => $formatParty($sample->customer, $sample->customerLocation, $sample->m08_customer_location_id),
            'buyer'       => $formatParty($sample->buyer, $sample->buyerLocation, $sample->m08_buyer_location_id),
            'third_party' => $formatParty($sample->thirdParty, $sample->thirdPartyLocation, $sample->m08_third_party_location_id),
            'cha'         => $formatParty($sample->cha, $sample->chaLocation, $sample->m08_cha_location_id),
        ];
    }
}

if (!function_exists('getNamesFromCsv')) {
    function getNamesFromCsv(?string $csv, string $modelClass, string $idColumn, string $nameColumn = 'name'): array
    {
        if (!$csv) {
            return [];
        }

        $ids = array_filter(explode(',', $csv));

        return $modelClass::whereIn($idColumn, $ids)
            ->pluck($nameColumn)
            ->toArray();
    }
}
