<?php

namespace App\Http\Controllers;

use App\Models\Accreditation;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Department;
use App\Models\Group;
use App\Models\LabSample;
use App\Models\Package;
use App\Models\Sample;
use App\Models\SampleRegistration;
use App\Models\SampleTest;
use App\Models\Standard;
use App\Models\State;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RegistrationController extends Controller
{
    public function preRegistration(Request $request)
    {
        if ($request->isMethod(method: 'POST')) {
            // dd($request);
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
                "dd_department" => "required|exists:m13_departments,m13_department_id",
                "dd_sample_type" => "required|exists:m14_lab_samples,m14_lab_sample_id",
                "dd_priority_type" => "required|in:Normal,Urgent",
                'txt_number_of_samples' => 'nullable|integer|min:1',
                'txt_unknown_sample' => 'nullable|boolean',
                "txt_description" => "nullable|string",
                'txt_sample_image' => 'nullable|string',
                "txt_due_date" => "required|date",
                "txt_testing_charges" => "required|numeric",
                "txt_aditional_charges" => "nullable|numeric",
                "txt_total_charges" => "required|numeric",
                "tests" => "required|array",
                "tests.*.test_id" => "required|exists:m12_tests,m12_test_id",
                "tests.*.standard_id" => "nullable|exists:m15_standards,m15_standard_id",
            ]);

            if ($validator->fails()) {
                // dd($validator);
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            try {
                $refId =  generateReferenceId($request->dd_department);
                $tracId = generateTrackerId($refId);
                $data = [
                    'm04_ro_id' => Session::get('ro_id') ?? -1,
                    'tr04_reference_id' => $refId,
                    'tr04_tracker_id' => $tracId,
                    'm09_customer_type_id' => $request->dd_customer_type,
                    'm07_customer_id' => $request->selected_customer_id,
                    'm08_customer_location_id' => $request->selected_customer_address_id == 'default' ? 0 : $request->selected_customer_address_id,
                    'm07_buyer_id' => $request->selected_buyer_id,
                    'm08_buyer_location_id' => $request->selected_buyer_address_id == 'default' ? 0 : $request->selected_buyer_address_id,
                    'm07_third_party_id' => $request->selected_third_party_id,
                    'm08_third_party_location_id' => $request->selected_third_party_address_id == 'default' ? 0 : $request->selected_third_party_address_id,
                    'm07_cha_id' => $request->selected_cha_id,
                    'm08_cha_location_id' => $request->selected_cha_address_id == 'default' ? 0 : $request->selected_cha_address_id,
                    'tr04_payment_by' => $request->txt_payment_by,
                    'tr04_report_to' => $request->txt_report_to,
                    'tr04_reference_no' => $request->txt_reference,
                    'tr04_reference_date' => $request->txt_ref_date,
                    'tr04_received_via' => $request->txt_received_via,
                    'tr04_details' => $request->txt_details,
                    'm13_department_id' => $request->dd_department,
                    'm14_lab_sample_id' => $request->dd_sample_type,
                    'tr04_sample_type' => $request->dd_priority_type,
                    'tr04_number_of_samples' => $request->txt_number_of_samples != '' ? $request->txt_number_of_samples : $request->txt_unknown_sample,
                    'tr04_sample_description' => $request->txt_description,
                    'm19_package_id' => $request->dd_contracts,
                    'tr04_charge_type' => $request->dd_charge_type,
                    'm12_test_ids' => json_encode($request->tests),
                    'tr04_testing_charges' => $request->txt_testing_charges,
                    'tr04_additional_charges' => $request->txt_aditional_charges,
                    'tr04_total_charges' => $request->txt_total_charges,
                    'tr04_expected_date' => $request->txt_due_date,
                    'tr04_test_type' => $request->dd_test_type,
                    'tr04_progress' => 'REGISTERED',
                    'tr04_created_by' => Session::get('user_id') ?? -1,
                ];
                // dd($data);
                $base64Image = $request->input('txt_sample_image');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                        return back()->withErrors(['txt_sample_image' => 'Invalid image type']);
                    }

                    $imageData = base64_decode($imageData);
                    if ($imageData === false) {
                        return back()->withErrors(['txt_sample_image' => 'Base64 decode failed']);
                    }

                    $fileName = 'sample_' . time() . '.' . $type;
                    Storage::disk('public')->put("samples/{$fileName}", $imageData);

                    $data['tr04_attachment'] = "samples/{$fileName}";
                }
                //  else {
                // dd("uerfg");
                // return back()->withErrors(['txt_sample_image' => 'Invalid image data']);
                // }

                $registration = SampleRegistration::create($data);
                // dd($registration);
                foreach ($request->tests as $test) {
                    $testId = $test['test_id'] ?? null;
                    $primaryIds = null;
                    $secondaryIds = null;

                    if ($testId) {
                        $testRow = Test::find($testId);
                        if ($testRow) {
                            $primaryIds = $testRow->m16_primary_test_id ?? null;
                            $secondaryIds = $testRow->m17_secondary_test_id ?? null;
                        }
                    }

                    SampleTest::create([
                        'tr04_sample_registration_id' => $registration->tr04_sample_registration_id,
                        'm12_test_id' => $testId,
                        'm04_ro_id' => Session::get('ro_id') ?? -1,
                        'm16_primary_test_id' => $primaryIds,
                        'm17_secondary_test_id' => $secondaryIds,
                        'm15_standard_id' => $test['standard_id'] ?? null,
                        'tr01_allotted_to' => null,
                        'tr05_status' => 'PENDING',
                        'tr05_remark' => null,
                    ]);
                }
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Sample Registered Successfully!');
                return redirect()->back();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Sample Registration Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                Session::flash('type', 'error');
                Session::flash('message', 'Sample Registration Failed! Please try again.');
                return redirect()->back()->withInput();
            }
        }
        $customerTypes = CustomerType::where('m09_status', 'ACTIVE')->get(['m09_customer_type_id', 'm09_name']);
        $labSamples = LabSample::where('m14_status', 'ACTIVE')->get(['m14_lab_sample_id', 'm14_name']);
        $groups = Group::where('m11_status', 'ACTIVE')->get(['m11_group_id', 'm11_name']);
        $states = State::where('m01_status', 'ACTIVE')->get(['m01_state_id', 'm01_name']);
        $departments = Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);
        return view('registration.preRegistration.register_sample', compact('customerTypes', 'labSamples', 'groups', 'states', 'departments'));
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
                                'id' => 'default_' . $customer->m07_customer_id,
                                'customer_id' => $customer->m07_customer_id,
                                'address' => $customer->m07_address,
                                'state' => $customer->state?->m01_state_name,
                                'district' => $customer->district?->m02_district_name,
                                'pincode' => $customer->m07_pincode,
                                'contact_person' => $customer->m07_contact_person,
                                'email' => $customer->m07_email,
                                'phone' => $customer->m07_phone,
                                'gst' => $customer->m07_gst,
                                'is_default' => true
                            ],
                            'other_addresses' => $customer->locations->map(function ($loc) use ($customer) {
                                return [
                                    'id' => $loc->m08_customer_location_id,
                                    'customer_id' => $customer->m07_customer_id,
                                    'address' => $loc->m08_address,
                                    'state' => $loc->state?->m01_state_name,
                                    'district' => $loc->district?->m02_district_name,
                                    'pincode' => $loc->m08_pincode,
                                    'contact_person' => $loc->m08_contact_person,
                                    'email' => $loc->m08_email,
                                    'phone' => $loc->m08_phone,
                                    'gst' => $loc->m08_gst,
                                    'is_default' => false
                                ];
                            })->values(),
                        ];
                    });
            } else {
                $customers = collect();
            }

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

        // Get all selected customer IDs from the session or request
        $customerIds = [];
        if ($request->customer_id) $customerIds[] = $request->customer_id;
        if ($request->buyer_id) $customerIds[] = $request->buyer_id;
        if ($request->third_party_id) $customerIds[] = $request->third_party_id;
        if ($request->cha_id) $customerIds[] = $request->cha_id;

        switch ($type) {
            case 'CONTRACT':
                $query = Package::where('m19_status', 'ACTIVE')
                    ->whereDate('m19_exp_date', '>=', Carbon::today())
                    ->where('m19_type', 'CONTRACT');

                // Filter by customer contracts if customer IDs provided
                if (!empty($customerIds)) {
                    $query->whereIn('m07_contract_with', $customerIds);
                }

                $data = $query->get(['m19_package_id as id', 'm19_name as name']);
                break;

            case 'CUSTOM':
                $query = Package::where('m19_status', 'ACTIVE')
                    ->whereDate('m19_exp_date', '>=', Carbon::today())
                    ->where('m19_type', 'CUSTOM');

                // Filter by customer contracts if customer IDs provided
                if (!empty($customerIds)) {
                    $query->whereIn('m07_contract_with', $customerIds);
                }

                $data = $query->get(['m19_package_id as id', 'm19_name as name']);
                break;

            case 'PACKAGE':
                $data = Package::where('m19_status', 'ACTIVE')
                    ->where('m19_type', 'PACKAGE')
                    ->get(['m19_package_id as id', 'm19_name as name', 'm19_exc_azo_charge as exc_azo_charge', 'm19_inc_azo_charge as inc_azo_charge']);
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
        $package = Package::where('m19_package_id', $request->contract_id)
            ->with('packageTests.test', 'packageTests.standard')
            ->firstOrFail();
        $tests = [
            'id' => $package->m19_package_id,
            'name' => $package->m19_name,
            'charge' => $package->m19_charges,
            'type' => $package->m19_type,
            'inc_azo_charge' => $package->m19_inc_azo_charge,
            'exc_azo_charge' => $package->m19_exc_azo_charge,
            'tests' => $package->packageTests->map(function ($pt) {
                return [
                    'id' => $pt->m20_package_test_id,
                    'test' => $pt->test ? [
                        'id' => $pt->test->m12_test_id,
                        'name' => $pt->test->m12_name,
                        'description' => $pt->test->m12_description,
                        'charge' => $pt->test->m12_charge,
                    ] : null,
                    'standard' => $pt->standard ? [
                        'id' => $pt->standard->m15_standard_id,
                        'method' => $pt->standard->m15_method,
                        'accredited' => $pt->standard->m15_accredited,
                    ] : null,
                ];
            }),
        ];
        return response()->json($tests);
    }

    public function viewRegSamples(Request $request)
    {
        if ($request->ajax()) {
            $samples = SampleRegistration::with([
                'labSample',
                'sampleTests.test',
                'sampleTests.standard',
                'customerType',
            ])->select('tr04_sample_registrations.*')
            ->where('m04_ro_id', Session::get('ro_id'));

            return DataTables::of($samples)
                ->addIndexColumn()
                ->addColumn('sample_id', function ($row) {
                    return $row->tr04_sample_registration_id ?? 'N/A';
                })
                ->addColumn('sample_description', function ($row) {
                    return $row->tr04_sample_description ?? 'N/A';
                })
                ->addColumn('sanple_image', function ($row) {
                    return $row->tr04_attachment ? asset('storage/' . $row->tr04_attachment) : null;
                })
                ->addColumn('sample_type', function ($row) {
                    $color = $row->tr04_sample_type === 'Urgent' ? 'danger' : 'info';
                    return '<span class="text-' . $color . '">' . strtoupper($row->tr04_sample_type) . '</span>';
                })

                ->addColumn('total_tests', function ($row) {
                    return $row->sampleTests->count();
                })
                ->addColumn('status', function ($row) {
                    $statusClass = '';
                    $statusText = $row->tr04_progress ?? 'REGISTERED';

                    switch (strtolower($statusText)) {
                        case 'complete':
                        case 'completed':
                            $statusClass = 'bg-success';
                            break;
                        case 'pending':
                            $statusClass = 'bg-warning';
                            break;
                        case 'processing':
                            $statusClass = 'bg-info';
                            break;
                        default:
                            $statusClass = 'bg-primary';
                    }

                    return '<span class="badge badge-dot ' . $statusClass . '">' . ucfirst($statusText) . '</span>';
                })
                ->addColumn('amount', function ($row) {
                    $amount = $row->tr04_total_charges ?? 0;
                    return '<span class="amount">&#8377; &nbsp;' . number_format($amount, 2) . '</span>';
                })
                ->addColumn('created_date', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y, h:ia') : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="tb-odr-btns d-flex">';
                    $actions .= '<a href="' . route('print_sample_acknowledgement', $row->tr04_sample_registration_id) . '" target="_blank" 
                   class="btn btn-icon btn-white btn-dim btn-sm btn-primary">
                   <em class="icon ni ni-printer-fill"></em>
                </a>';
                    $actions .= '<a href="' . route('view_registration_pdf', $row->tr04_sample_registration_id) . '" 
                   class="btn btn-dim btn-sm btn-primary">
                   View
                </a>';

                $actions .= '<br>'; 

                $actions .= '<a href="' . route('view_invoice', $row->tr04_sample_registration_id) . '" 
                   class="btn btn-dim btn-sm btn-success">
                   <em class="icon ni ni-file-text"></em>

                </a>';
                  $actions .= '<a href="' . route('view_all_invoice', $row->m07_customer_id) . 
                    '?location_id=' . $row->m08_customer_location_id . 
                    '&payment_by=' . $row->tr04_payment_by . '" 
                    class="btn btn-dim btn-sm btn-success">
                        Invoice All
                    </a>';

                    if ($row->tr04_sample_type !== 'Urgent') {
                        $actions .= '<a class="btn btn-xs btn-danger upgrade-to-urgent" 
                    data-id="' . $row->tr04_sample_registration_id . '" title="Upgrade">
                    <em class="icon ni ni-speed"></em>&nbsp;
                    </a>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'amount', 'action', 'sample_type'])
                ->make(true);
        }
        return view('registration.view_registered_samples');
    }

    public function showSampleDetails($id)
    {
        $sample = SampleRegistration::with([
            'labSample',
            'package',
            'sampleTests.test',
            'sampleTests.standard.accreditationForCurrentRo',
            'customerType',
            'ro'
        ])->where('tr04_sample_registration_id', $id)->first();

        $sample->each(function ($sample) {
            $sample->sampleTests->each(function ($test) {
                $test->append(['primary_tests', 'secondary_tests']);
            });
        });
        return view('registration.registration_details', compact('sample'));
    }

    public function printSampleDetails($id)
    {
        $sample = SampleRegistration::with([
            'labSample',
            'package',
            'sampleTests.test',
            'sampleTests.standard.accreditationForCurrentRo',
            'customerType',
            'ro'
        ])->where('tr04_sample_registration_id', $id)->firstOrFail();

        $sample->sampleTests->each(function ($test) {
            $test->append(['primary_tests', 'secondary_tests']);
        });

        return view('registration.print_pdf_acknowledgement', compact('sample'));
    }

    public function upgradeToUrgent(Request $request)
    {
        if ($request->isMethod('POST')) {
            try {
                $sample = SampleRegistration::findOrFail($request->id);
                if ($sample->tr04_sample_type === 'Urgent') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Sample is already marked as Urgent.'
                    ], 400);
                }
                $newTotal = $sample->tr04_testing_charges + ($sample->tr04_testing_charges * 0.50) + $sample->tr04_additional_charges;
                $sample->update([
                    'tr04_total_charges' => $newTotal,
                    'tr04_sample_type'   => 'Urgent',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Sample has been upgraded to Urgent successfully.',
                    'new_total' => number_format($newTotal, 2)
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid request method.'
        ], 405);
    }

    public function blankRegistration(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "dd_customer_type" => "required|integer|exists:m09_customer_types,m09_customer_type_id",
                "selected_customer_id" => "required|integer|exists:m07_customers,m07_customer_id",
                "dd_department" => "required|exists:m13_departments,m13_department_id",
                "dd_sample_type" => "required|exists:m14_lab_samples,m14_lab_sample_id",
                "dd_priority_type" => "required|in:Normal,Urgent",
                "txt_reference" => "required|string",
                "txt_ref_date" => "required|date",
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $refId = generateReferenceId($request->dd_department);
                $tracId = generateTrackerId($refId);

                $data = [
                    'm04_ro_id' => Session::get('ro_id') ?? -1,
                    'tr04_reference_id' => $refId,
                    'tr04_tracker_id' => $tracId,
                    'm09_customer_type_id' => $request->dd_customer_type,
                    'm07_customer_id' => $request->selected_customer_id,
                    'm08_customer_location_id' => $request->selected_customer_address_id == 'default' ? 0 : $request->selected_customer_address_id,
                    'm13_department_id' => $request->dd_department,
                    'm14_lab_sample_id' => $request->dd_sample_type,
                    'tr04_sample_type' => $request->dd_priority_type,
                    'tr04_reference_no' => $request->txt_reference,
                    'tr04_reference_date' => $request->txt_ref_date,
                    'tr04_progress' => 'BLANK',
                    'tr04_created_by' => Session::get('user_id') ?? -1,
                ];

                $registration = SampleRegistration::create($data);

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Instant Sample Registered Successfully!');
                return redirect()->route('blank_registration');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Instant Registration Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                Session::flash('type', 'error');
                Session::flash('message', 'Instant Registration Failed! Please try again.');
                return redirect()->back()->withInput();
            }
        }
        $customerTypes = CustomerType::where('m09_status', 'ACTIVE')->get(['m09_customer_type_id', 'm09_name']);
        $labSamples = LabSample::where('m14_status', 'ACTIVE')->get(['m14_lab_sample_id', 'm14_name']);
        $groups = Group::where('m11_status', 'ACTIVE')->get(['m11_group_id', 'm11_name']);
        $departments = Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);

        return view('registration.preRegistration.blank_registration', compact('customerTypes', 'labSamples', 'groups', 'departments'));
    }
}
