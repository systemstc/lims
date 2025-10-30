<?php

use App\Models\SampleRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
                'pincode'       => $locationId == 0 ? $party?->m07_pincode : $location?->m08_pincode,
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

if (!function_exists('toggleStatus')) {
    function toggleStatus($table, $idColumn, $statusColumn, $id)
    {
        $row = DB::table($table)->where($idColumn, $id)->first();
        if (!$row) {
            return ['status' => 'error', 'message' => 'Record not found'];
        }
        $newStatus = ($row->$statusColumn === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $update = DB::table($table)
            ->where($idColumn, $id)
            ->update([$statusColumn => $newStatus]);
        if ($update) {
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $newStatus
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data not found.'], 404);
        }
    }
}

if (!function_exists('generateCode')) {
    function generateReferenceId($sampleType)
    {
        return DB::transaction(function () use ($sampleType) {
            $roId = str_pad(Session::get('ro_id') ?? '0', 2, '0', STR_PAD_LEFT);
            $year = date('Y');
            $type = str_pad($sampleType, 2, '0', STR_PAD_LEFT);
            $prefix = $year . $type . $roId;

            $lastRecord = SampleRegistration::lockForUpdate()
                ->orderBy('tr04_sample_registration_id', 'desc')
                ->first();

            if ($lastRecord && !empty($lastRecord->tr04_reference_id)) {
                preg_match('/(\d{4})$/', $lastRecord->tr04_reference_id, $matches);
                $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            return $prefix . $formattedNumber;
        });
    }
}

if (!function_exists('generateTrackerId')) {
    function generateTrackerId($referenceId)
    {
        $intVal = intval($referenceId);
        $hex = strtoupper(dechex($intVal));
        return substr(str_pad($hex, 6, '0', STR_PAD_LEFT), -6);
    }
}
