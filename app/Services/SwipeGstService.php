<?php

namespace App\Services;

use App\Models\District;
use App\Models\State;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SwipeGstService
{
    /**
     * Map of standard GST 2-digit state codes to State Names.
     */
    protected array $gstStateCodes = [
        '01' => 'Jammu and Kashmir',
        '02' => 'Himachal Pradesh',
        '03' => 'Punjab',
        '04' => 'Chandigarh',
        '05' => 'Uttarakhand',
        '06' => 'Haryana',
        '07' => 'Delhi',
        '08' => 'Rajasthan',
        '09' => 'Uttar Pradesh',
        '10' => 'Bihar',
        '11' => 'Sikkim',
        '12' => 'Arunachal Pradesh',
        '13' => 'Nagaland',
        '14' => 'Manipur',
        '15' => 'Mizoram',
        '16' => 'Tripura',
        '17' => 'Meghalaya',
        '18' => 'Assam',
        '19' => 'West Bengal',
        '20' => 'Jharkhand',
        '21' => 'Odisha',
        '22' => 'Chhattisgarh',
        '23' => 'Madhya Pradesh',
        '24' => 'Gujarat',
        '26' => 'Dadra and Nagar Haveli and Daman and Diu',
        '27' => 'Maharashtra',
        '28' => 'Andhra Pradesh',
        '29' => 'Karnataka',
        '30' => 'Goa',
        '31' => 'Lakshadweep',
        '32' => 'Kerala',
        '33' => 'Tamil Nadu',
        '34' => 'Puducherry',
        '35' => 'Andaman and Nicobar Islands',
        '36' => 'Telangana',
        '37' => 'Andhra Pradesh',
        '38' => 'Ladakh',
    ];

    /**
     * Common state name aliases/variants to handle mismatches.
     */
    protected array $stateAliases = [
        'orissa' => 'odisha',
        'pondicherry' => 'puducherry',
        'nct of delhi' => 'delhi',
        'national capital territory of delhi' => 'delhi',
        'jammu & kashmir' => 'jammu and Kashmir',
        'uttaranchal' => 'uttarakhand',
        'tamilnadu' => 'tamil nadu',
        'telengana' => 'telangana',
        'andaman & nicobar islands' => 'andaman and nicobar islands',
        'daman and diu' => 'dadra and nagar haveli and daman and diu',
        'dadra & nagar haveli' => 'dadra and nagar haveli and daman and diu',
    ];

    /**
     * Fetch GST details from Swipe Partner API.
     *
     * @param string $gstin
     * @return array
     */
    public function getGstDetails(string $gstin): array
    {
        $cleanGst = strtoupper(trim($gstin));

        // Basic GST format check (15 chars, alphanumeric)
        if (strlen($cleanGst) !== 15 || !preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i', $cleanGst)) {
            return [
                'success' => false,
                'message' => 'Invalid GST Number format. It must be 15 alphanumeric characters (e.g. 27AAAAA0000A1Z5).'
            ];
        }

        $apiKey = config('services.swipe.api_key')
            ?: env('SWIPE_API_KEY')
            ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoyNjQ3Nzc0LCJuYW1lIjoiQVBJIFVzZXIiLCJjb21wYW55X2lkIjoyNjcyNDExLCJjb21wYW55X25hbWUiOiJZT1VSIEJVU0lORVNTIE5BTUUiLCJyZWFkX29ubHkiOmZhbHNlLCJwYXJ0bmVyIjp0cnVlLCJpYXQiOjE3OTE2MzAwNjAsInZlcnNpb24iOjJ9.vCbmdZZ4g_T1Rrc26ALMB5Qoo9YCXbUex5fUC3YZG-M';

        $baseUrl = rtrim(config('services.swipe.base_url') ?: env('SWIPE_BASE_URL') ?: 'https://app.getswipe.in/api/partner', '/');
        $endpoint = "{$baseUrl}/v2/utils/gstin/{$cleanGst}";

        try {
            // First attempt with SSL verification
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->timeout(15)->get($endpoint);

        } catch (\Exception $e) {
            Log::warning('Swipe GST API call initial error: ' . $e->getMessage());

            // If local SSL failure, retry without verifying SSL
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                ])->withoutVerifying()->timeout(15)->get($endpoint);
            } catch (\Exception $ex) {
                Log::error('Swipe GST API call failed completely: ' . $ex->getMessage());
                return [
                    'success' => false,
                    'message' => 'Unable to connect to GST verification service. Please check network connectivity.'
                ];
            }
        }

        if ($response->successful()) {
            $body = $response->json();

            if (!empty($body['success']) && !empty($body['data'])) {
                $data = $body['data'];
                return $this->formatGstResponse($cleanGst, $data);
            }

            $msg = $body['message'] ?? 'GST details could not be found for the given number.';
            return [
                'success' => false,
                'message' => $msg
            ];
        }

        $body = $response->json();
        $message = $body['message'] ?? ($body['error_code'] ?? 'Failed to fetch GST details from portal (HTTP ' . $response->status() . ').');

        return [
            'success' => false,
            'message' => $message
        ];
    }

    /**
     * Format and map the raw Swipe GST response to LIMS customer fields.
     */
    protected function formatGstResponse(string $gstin, array $data): array
    {
        $companyName = trim($data['company_name'] ?? '');
        $tradeName = trim($data['trade_name'] ?? '');
        $legalName = trim($data['legal_name'] ?? '');

        // Preferred Customer Name: trade_name > legal_name > company_name
        $customerName = $tradeName ?: ($legalName ?: $companyName);

        // Preferred Contact Person
        $contactPerson = $legalName ?: ($tradeName ?: $companyName);

        $billing = $data['billing'] ?? [];
        $address1 = trim($billing['address_1'] ?? '');
        $address2 = trim($billing['address_2'] ?? '');
        $city = trim($billing['city'] ?? '');
        $pincode = trim($billing['pincode'] ?? '');
        $stateNameFromApi = trim($billing['state'] ?? '');

        $addressParts = array_filter([$address1, $address2]);
        $formattedAddress = implode(', ', $addressParts);

        // Resolve State & District in local database
        $stateMatch = $this->resolveState($gstin, $stateNameFromApi);
        $matchedStateId = $stateMatch['state_id'] ?? null;
        $matchedStateName = $stateMatch['state_name'] ?? $stateNameFromApi;

        $districts = [];
        $matchedDistrictId = null;
        $matchedDistrictName = null;

        if ($matchedStateId) {
            $districts = District::where('m01_state_id', $matchedStateId)
                ->where('m02_status', 'ACTIVE')
                ->orderBy('m02_name')
                ->get(['m02_district_id', 'm02_name']);

            $districtMatch = $this->resolveDistrict($districts, $city);
            $matchedDistrictId = $districtMatch['district_id'] ?? null;
            $matchedDistrictName = $districtMatch['district_name'] ?? null;
        }

        return [
            'success' => true,
            'message' => 'GST details fetched successfully.',
            'data' => [
                'gst' => $gstin,
                'name' => $customerName,
                'company_name' => $companyName,
                'trade_name' => $tradeName,
                'legal_name' => $legalName,
                'contact_person' => $contactPerson,
                'address' => $formattedAddress,
                'address_1' => $address1,
                'address_2' => $address2,
                'city' => $city,
                'pincode' => $pincode,
                'state_id' => $matchedStateId,
                'state_name' => $matchedStateName,
                'district_id' => $matchedDistrictId,
                'district_name' => $matchedDistrictName,
                'districts' => $districts,
                'status' => $data['status'] ?? 'Active',
            ]
        ];
    }

    /**
     * Resolve State model by API state name or GST State Code.
     */
    protected function resolveState(string $gstin, string $apiStateName): array
    {
        $allStates = State::all(['m01_state_id', 'm01_name']);
        $normalizedApi = $this->normalizeString($apiStateName);

        // 1. Direct match on API state name
        if (!empty($normalizedApi)) {
            foreach ($allStates as $state) {
                if ($this->normalizeString($state->m01_name) === $normalizedApi) {
                    return ['state_id' => $state->m01_state_id, 'state_name' => $state->m01_name];
                }
            }

            // Check aliases
            if (isset($this->stateAliases[$normalizedApi])) {
                $alias = $this->stateAliases[$normalizedApi];
                foreach ($allStates as $state) {
                    if ($this->normalizeString($state->m01_name) === $alias) {
                        return ['state_id' => $state->m01_state_id, 'state_name' => $state->m01_name];
                    }
                }
            }
        }

        // 2. Fallback to GST State Code from first 2 digits
        $gstCode = substr($gstin, 0, 2);
        if (isset($this->gstStateCodes[$gstCode])) {
            $expectedState = $this->normalizeString($this->gstStateCodes[$gstCode]);
            foreach ($allStates as $state) {
                if ($this->normalizeString($state->m01_name) === $expectedState) {
                    return ['state_id' => $state->m01_state_id, 'state_name' => $state->m01_name];
                }
            }
        }

        // 3. Substring match
        if (!empty($normalizedApi)) {
            foreach ($allStates as $state) {
                $normDb = $this->normalizeString($state->m01_name);
                if (str_contains($normDb, $normalizedApi) || str_contains($normalizedApi, $normDb)) {
                    return ['state_id' => $state->m01_state_id, 'state_name' => $state->m01_name];
                }
            }
        }

        return ['state_id' => null, 'state_name' => $apiStateName];
    }

    /**
     * Resolve District model from city name.
     */
    protected function resolveDistrict($districts, string $city): array
    {
        if (empty($city) || empty($districts) || count($districts) === 0) {
            return ['district_id' => null, 'district_name' => null];
        }

        $normCity = $this->normalizeString($city);

        // 1. Exact normalized match
        foreach ($districts as $d) {
            if ($this->normalizeString($d->m02_name) === $normCity) {
                return ['district_id' => $d->m02_district_id, 'district_name' => $d->m02_name];
            }
        }

        // 2. Substring match
        foreach ($districts as $d) {
            $normD = $this->normalizeString($d->m02_name);
            if (str_contains($normD, $normCity) || str_contains($normCity, $normD)) {
                return ['district_id' => $d->m02_district_id, 'district_name' => $d->m02_name];
            }
        }

        return ['district_id' => null, 'district_name' => null];
    }

    /**
     * Normalize string for comparison.
     */
    protected function normalizeString(?string $str): string
    {
        if ($str === null) return '';
        $clean = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($str));
        return trim($clean);
    }
}
