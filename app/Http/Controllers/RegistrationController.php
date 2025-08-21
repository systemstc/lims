<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Group;
use App\Models\LabSample;
use App\Models\Package;
use App\Models\SampleRegistration;
use App\Models\Standard;
use App\Models\Test;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function preRegistration(Request $request)
    {
        if ($request->method() == 'POST') {
            dd($request);
            $validator = Validator::make($request->all(), [
                "dd_customer_type" => "required|exists:m09_customer_types,m09_customer_type_id",
                "txt_customer_name" => "required|string",
                "txt_buyer_name" => "nullable|string",
                "txt_third_party" => "nullable|string",
                "txt_cha" => "nullable|string",
                "txt_payment_by" => "required",
                "txt_report_to" => "required",
                "txt_reference" => "required|string",
                "txt_ref_date" => "required|date",
                "txt_received_via" => "required",
                "txt_details" => "nullable|string",
                "dd_sample_type" => "required|exists:m14_lab_samples,m14_lab_sample_id",
                "dd_priority_type" => "reuired|in:Normal,Urgent",
                "txt_description" => "nullable|string",
                "dd_test_type" => "required|in:Specification,General,Contract,Custom",
                "txt_due_date" => "required",
                "txt_testing_charges" => "required",
                "txt_aditional_charges" => "nullable",
                "txt_total_charges" => "required",
                "tests" => "required",
            ], []);

            $data = [
                'm04_ro_id' => Session::get('ro_id') ?? -1,
                'tr04_tracker_id' => 'ABCD',
                'm09_customer_type_id' => $request->dd_customer_type,
                'm07_customer_id' => $request->txt_customer_name,
                'm07_buyer_id' => $request->txt_buyer_name,
                'm07_third_party_id' => $request->txt_third_party,
                'm07_cha_id' => $request->txt_cha,
                'tr04_payment_by' => $request->txt_payment_by,
                'tr04_report_to' => $request->txt_report_to,
                'tr04_reference_no' => $request->txt_reference,
                'tr04_reference_date' => $request->txt_ref_date,
                'tr04_received_via' => $request->txt_received_via,
                'tr04_details' => $request->txt_details,
                'm14_lab_sample_id' => $request->dd_sample_type,
                'tr04_sample_type' => $request->dd_priority_type,
                'tr04_sample_description' => $request->txt_description,
                'tr04_test_type' => $request->dd_test_type,
                'm12_test_ids' => json_encode($request->tests),
                'tr04_testing_charges' => $request->txt_testing_charges,
                'tr04_additional_charges' => $request->txt_aditional_charges,
                'tr04_total_charges' => $request->txt_total_charges,
                'tr04_expected_date' => $request->txt_due_date,
                'tr04_created_by' => Session::get('user_id') ?? -1,
            ];

            if ($request->hasFile('txt_attachment')) {
                $folderPath = 'attachments/' . date('Y') . '/' . date('m');
                $originalName = pathinfo($request->file('txt_attachment')->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($originalName);
                $extension = $request->file('txt_attachment')->getClientOriginalExtension();
                $uniqueName = $safeName . '-' . time() . '-' . Str::random(6) . '.' . $extension;
                $path = $request->file('txt_attachment')->storeAs($folderPath, $uniqueName, 'public');
                $data['tr04_attachment'] = $path;
            }
            $create = SampleRegistration::create($data);
            if ($create) {
                Session::flash('type', 'success');
                Session::flash('message', 'Sample Registered Successfully!');
                return redirect()->back();
            }
            Session::flash('type', 'error');
            Session::flash('message', 'Sample Registered Failed!');
            return redirect()->back();
            // dd($data);
        }
        $customerTypes = CustomerType::where('m09_status', 'ACTIVE')->get(['m09_customer_type_id', 'm09_name']);
        $labSamples =  LabSample::where('m14_status', 'ACTIVE')->get(['m14_lab_sample_id', 'm14_name']);
        $groups = Group::where('m11_status', 'ACTIVE')->get(['m11_group_id', 'm11_name']);
        return view('registration.preRegistration.register_sample', compact('customerTypes', 'labSamples', 'groups'));
    }

    public function searchCustomer(Request $request)
    {
        Log::info('Search customer request', [
            'query' => $request->input('query'),
            'all_data' => $request->all()
        ]);

        try {
            $query = $request->input('query');

            if ($query) {
                $customers = Customer::with(['locations', 'state', 'district'])
                    ->where('m07_name', 'like', "%{$query}%")
                    ->take(10)
                    ->get()
                    ->map(function ($customer) {
                        return [
                            'id' => $customer->m07_customer_id,
                            'name' => $customer->m07_name,
                            'default_address' => [
                                'address' => $customer->m07_address,
                                'state' => $customer->state?->m01_state_name,
                                'district' => $customer->district?->m02_district_name,
                                'pincode' => $customer->m07_pincode,
                                'contact_person' => $customer->m07_contact_person,
                                'email' => $customer->m07_email,
                                'phone' => $customer->m07_phone,
                                'gst' => $customer->m07_gst,
                            ],
                            'other_addresses' => $customer->locations->map(function ($loc) {
                                return [
                                    'id' => $loc->m08_customer_location_id,
                                    'address' => $loc->m08_address,
                                    'state' => $loc->state?->m01_state_name,
                                    'district' => $loc->district?->m02_district_name,
                                    'pincode' => $loc->m08_pincode,
                                    'contact_person' => $loc->m08_contact_person,
                                    'email' => $loc->m08_email,
                                    'phone' => $loc->m08_phone,
                                    'gst' => $loc->m08_gst,
                                ];
                            })->values(),
                        ];
                    });
            } else {
                $customers = collect();
            }
            // dd($customers);
            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('Error in searchCustomer', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function searchTest(Request $request)
    {
        $query = $request->get('query');
        $groupId = $request->get('group_id');

        $tests = Test::query()
            ->when($groupId, function ($q) use ($groupId) {
                $q->where('m11_group_id', $groupId);
            })
            ->where(function ($q) use ($query) {
                $q->where('m12_name', 'LIKE', "%{$query}%")
                    ->orWhere('m12_test_id', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $results = $tests->map(function ($test) {
            $standard = null;

            if (!empty($test->m15_standard_id)) {
                $ids = explode(',', $test->m15_standard_id);

                $standard = Standard::whereIn('m15_standard_id', $ids)
                    ->where('m15_method', 'LIKE', 'IS%')
                    ->first(['m15_standard_id as standard_id', 'm15_method as name']);
            }

            return [
                'id'        => $test->m12_test_id,
                'test_name' => $test->m12_name,
                'charge'    => $test->m12_charge,
                'remark'    => $test->m12_remark,
                'standard'  => $standard
            ];
        });
        return response()->json($results);
    }

    public function getStandardByTest(Request $request)
    {
        $testId = $request->test_id;
        $test = Test::where('m12_test_id', $testId)->first();
        if (!$test || empty($test->m15_standard_id)) {
            return response()->json([]);
        }
        $standardIds = explode(',', $test->m15_standard_id);
        $standards = Standard::whereIn('m15_standard_id', $standardIds)
            ->select('m15_standard_id as id', 'm15_method as name')
            ->get();
        return response()->json($standards);
    }

    public function getPackages(Request $request)
    {
        $type = $request->type;
        $data = [];

        switch ($type) {
            case 'CONTRACT':
                $data = Package::where('m19_status', 'ACTIVE')
                    ->whereDate('m19_exp_date', '>=', Carbon::today())
                    ->where('m19_type', 'CONTRACT')
                    ->get(['m19_package_id as id', 'm19_name as name']);
                break;
            case 'CUSTOM':
                $data = Package::where('m19_status', 'ACTIVE')
                    ->whereDate('m19_exp_date', '>=', Carbon::today())
                    ->where('m19_type', 'CUSTOM')
                    ->get(['m19_package_id as id', 'm19_name as name']);
                break;
            case 'PACKAGE':
                $data = Package::where('m19_status', 'ACTIVE')
                    ->where('m19_type', 'PACKAGE')
                    ->get(['m19_package_id as id', 'm19_name as name', 'm19_exc_azo_charge as exc_azo', 'm19_inc_azo_charge as inc_azo']);
                break;
            case 'SPECIFICATION':
                $data = Package::where('m19_status', 'ACTIVE')
                    ->where('m19_type', 'SPECIFICATION')
                    ->get(['m19_package_id as id', 'm19_name as name']);
                break;
        }

        return response()->json(['data' => $data]);
    }

    public function getTestByPackage(Request $request)
    {
        $contractId = $request->contract_id;

        $tests = Package::where('m19_package_is', $contractId)->with('packageTests.test', 'packageTests.standard')
            ->select('m19_package_id as id', 'm19_name as name', '19_charge as charge')
            ->get();

        return response()->json(['tests' => $tests]);
    }
}
