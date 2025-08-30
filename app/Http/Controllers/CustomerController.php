<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLocation;
use App\Models\CustomerType;
use App\Models\Ro;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

class CustomerController extends Controller
{

    public function viewCustomerTypes()
    {
        $customerTypes = CustomerType::get();
        return view('master.customer_types', compact('customerTypes'));
    }

    public function createCustomerType(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255',
                'txt_invoice_amount' => 'required|integer',
                'txt_categoery_type' => 'required|in:EXTERNAL,INTERNAL',
                'txt_remark' => 'nullable|string|max:500',
            ], [
                'txt_name.required' => 'The customer type name is required.',
                'txt_name.string' => 'The customer type name must be a string.',
                'txt_invoice_amount.required' => 'The invoice amount is required.',
                'txt_invoice_amount.integer' => 'The invoice amount must be an integer.',
                'txt_categoery_type.required' => 'The category type is required.',
                'txt_categoery_type.in' => 'The category type must be either EXTERNAL or INTERNAL.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            CustomerType::create([
                'm09_name' => $request->txt_name,
                'm09_amount_percent' => $request->txt_invoice_amount,
                'm09_type' => $request->txt_categoery_type,
                'm09_remark' => $request->txt_remark,
                'm04_ro_id' => Session::get('ro_id') ?? -1,
                'tr01_created_by' => Session::get('user_id') ?? -1,
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Customer type created successfully.');
            return redirect()->back();
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Invalid request method.');
        return redirect()->back();
    }

    public function updateCustomerType(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                "txt_edit_customer_type_id" => "required|integer",
                "txt_edit_name" => "required|string|max:255",
                "txt_edit_invoice_amount" => "required|integer|max:100",
                "txt_edit_categoery_type" => "required|in:EXTERNAL,INTERNAL",
                "txt_edit_remark" => "nullable|string",
            ], [
                "txt_edit_customer_type_id.required" => "Customer Type ID is required.",
                "txt_edit_customer_type_id.integer" => "Customer Type ID must be an integer.",
                "txt_edit_name.required" => "Name is required.",
                "txt_edit_name.string" => "Name must be a string.",
                "txt_edit_name.max" => "Name cannot exceed 255 characters.",
                "txt_edit_invoice_amount.required" => "Invoice amount is required.",
                "txt_edit_invoice_amount.integer" => "Invoice amount must be an integer.",
                "txt_edit_invoice_amount.max" => "Invoice amount cannot be more than 100.",
                "txt_edit_categoery_type.required" => "Category type is required.",
                "txt_edit_categoery_type.in" => "Category type must be either EXTERNAL or INTERNAL.",
                "txt_edit_remark.string" => "Remark must be a string."
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $customerType = CustomerType::find($request->txt_edit_customer_type_id);
            $data = [
                'm09_name' => $request->txt_edit_name,
                'm04_ro_id' => Session::get('ro_id') ?? -1,
                'm09_amount_percent' => $request->txt_edit_invoice_amount,
                'm09_type' => $request->txt_edit_categoery_type,
                'm09_remark' => $request->txt_edit_remark,
                'tr01_created_by' => Session::get('user_id') ?? -1,
            ];

            $customerType->update($data);
            Session::flash('type', 'success');
            Session::flash('message', 'Customer Type updated successfully.');
            return redirect()->back();
        }
    }

    public function deleteCustomerType(Request $request)
    {
        $customerType = CustomerType::findOrFail($request->id);
        $customerType->m09_status = $customerType->m09_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $customerType->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Customer type status updated to ' . $customerType->m09_status,
            'new_status' => $customerType->m09_status
        ]);
    }

    public function customerAll()
    {
        $customers = Customer::with('customerType', 'locations', 'district')->get();
        // dd($customers);
        return view('customers', compact('customers'));
    }

    public function createCustomer(Request $request)
    {
        if ($request->isMethod('post')) {
            // dd($request->query('from'));
            $validator = Validator::make($request->all(), [
                "txt_customer_type_id" => "required|integer|exists:m09_customer_types,m09_customer_type_id",
                "txt_ro_id" => "required|integer",
                "txt_name" => "required|string|max:255",
                "txt_email" => "required|email|max:255|unique:m07_customers,m07_email",
                "txt_phone" => "required|digits:10|unique:m07_customers,m07_phone",
                "txt_contact_person" => "required|string|max:255",
                "txt_address" => "required|string|max:500",
                "txt_state_id" => "required|integer|exists:m01_states,m01_state_id",
                "txt_district_id" => "required|integer|exists:m02_districts,m02_district_id",
                "txt_pincode" => "required|digits:6",
                "txt_gst" => "nullable|string|max:15|unique:m07_customers,m07_gst|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/",
                "txt_iec" => "nullable|string|max:10|regex:/^[A-Z0-9]{10}$/",
                // "txt_be" => "required|string|max:20",
                // contact and loactions                 
                'contacts' => 'nullable|array',
                'contacts.*.name' => 'required|string|max:255',
                'contacts.*.email' => 'required|email|max:255',
                'contacts.*.phone' => 'required|string|max:15',
                'contacts.*.state_id' => 'required|integer|exists:m01_states,m01_state_id',
                'contacts.*.district_id' => 'required|integer|exists:m02_districts,m02_district_id',
                'contacts.*.pincode' => 'required|string|max:10',
                'contacts.*.address' => 'required|string|max:500',
            ], [
                'txt_customer_type_id.required' => 'Customer Type is required.',
                'txt_customer_type_id.integer' => 'Customer Type must be a number.',
                'txt_customer_type_id.exists' => 'Selected Customer Type does not exist.',

                'txt_ro_id.required' => 'RO ID is required.',
                'txt_ro_id.integer' => 'RO ID must be a number.',
                // 'txt_ro_id.exists' => 'Selected RO ID does not exist.',

                'txt_name.required' => 'Name is required.',
                'txt_name.string' => 'Name must be a string.',
                'txt_name.max' => 'Name must not exceed 255 characters.',

                'txt_email.required' => 'Email is required.',
                'txt_email.email' => 'Enter a valid email address.',
                'txt_email.max' => 'Email must not exceed 255 characters.',
                'txt_email.unique' => 'This email is already registered.',

                'txt_phone.required' => 'Phone number is required.',
                'txt_phone.digits' => 'Phone number must be exactly 10 digits.',
                'txt_phone.unique' => 'This phone number is already registered.',

                'txt_contact_person.required' => 'Contact person name is required.',
                'txt_contact_person.string' => 'Contact person name must be a string.',
                'txt_contact_person.max' => 'Contact person name must not exceed 255 characters.',

                'txt_address.required' => 'Address is required.',
                'txt_address.string' => 'Address must be a valid string.',
                'txt_address.max' => 'Address must not exceed 500 characters.',

                'txt_state_id.required' => 'State is required.',
                'txt_state_id.integer' => 'State ID must be a number.',
                'txt_state_id.exists' => 'Selected state does not exist.',

                'txt_district_id.required' => 'District is required.',
                'txt_district_id.integer' => 'District ID must be a number.',
                'txt_district_id.exists' => 'Selected district does not exist.',

                'txt_pincode.required' => 'Pincode is required.',
                'txt_pincode.digits' => 'Pincode must be exactly 6 digits.',

                // 'txt_gst.required' => 'GST number is required.',
                'txt_gst.unique' => 'This GST number is already registered.',
                'txt_gst.regex' => 'Enter a valid GST number.',
                'txt_gst.max' => 'GST number must not exceed 15 characters.',

                // 'txt_iec.required' => 'IEC code is required.',
                'txt_iec.regex' => 'Enter a valid IEC code (10 alphanumeric characters).',
                'txt_iec.max' => 'IEC code must not exceed 10 characters.',

                // 'txt_be.required' => 'Branch/Business Entity name is required.',
                // 'txt_be.string' => 'Branch/Business Entity must be a string.',
                // 'txt_be.max' => 'Branch/Business Entity must not exceed 20 characters.',

                'contacts.*.name.required' => 'The contact person name is required for all locations.',
                'contacts.*.email.required' => 'The email is required for all locations.',
                'contacts.*.phone.required' => 'The phone number is required for all locations.',
                'contacts.*.state_id.required' => 'The state is required for all locations.',
                'contacts.*.district_id.required' => 'The district is required for all locations.',
                'contacts.*.pincode.required' => 'The pincode is required for all locations.',
                'contacts.*.address.required' => 'The address is required for all locations.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            try {
                DB::transaction(function () use ($request) {
                    $customer = Customer::create([
                        'm09_customer_type_id' => $request->txt_customer_type_id,
                        'm04_ro_id' => $request->txt_ro_id,
                        'm07_name' => $request->txt_name,
                        'm07_email' => $request->txt_email,
                        'm07_phone' => $request->txt_phone,
                        'm07_contact_person' => $request->txt_contact_person,
                        'm01_state_id' => $request->txt_state_id,
                        'm02_district_id' => $request->txt_district_id,
                        'm07_address' => $request->txt_address,
                        'm07_pincode' => $request->txt_pincode,
                        'm07_gst' => $request->txt_gst,
                        'm07_iec_code' => $request->txt_iec,
                    ]);
                    if ($request->has('contacts') && is_array($request->contacts)) {
                        foreach ($request->contacts as $contactData) {
                            CustomerLocation::create([
                                'm07_customer_id' => $customer->m07_customer_id,
                                'm08_contact_person' => $contactData['name'],
                                'm08_email' => $contactData['email'],
                                'm08_phone' => $contactData['phone'],
                                'm01_state_id' => $contactData['state_id'],
                                'm02_district_id' => $contactData['district_id'],
                                'm08_pincode' => $contactData['pincode'],
                                'm08_address' => $contactData['address'],
                            ]);
                        }
                    }
                });
                if ($request->query('from') == 'registration') {
                    return response()->make("
                        <script>
                            window.opener.location.reload(); // refresh parent if needed
                            window.close(); // close popup
                        </script>
                    ");
                } else {
                    // Normal customer creation
                    Session::flash('type', 'success');
                    Session::flash('message', 'Customer and locations added successfully!');
                    return redirect()->back();
                }
            } catch (\Exception $e) {
                Session::flash('type', 'danger');
                Session::flash('message', 'An error occurred while saving the data. Please try again.');
                Log::error($e->getMessage());
                return redirect()->back()->withInput();
            }
        }
        $states = State::get(['m01_state_id', 'm01_name']);
        $ros = Ro::get(['m04_ro_id', 'm04_name']);
        $customerTypes = CustomerType::get(['m09_customer_type_id', 'm09_name']);
        return view('create_customer', compact('states', 'ros', 'customerTypes'));
    }

    public function  deleteCustomer(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $customer->m07_status = $customer->m07_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $customer->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Customer status updated to ' . $customer->m07_status,
            'new_status' => $customer->m07_status
        ]);
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::with('locations')->findOrFail($id);

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_edit_customer_type_id' => 'required|integer|exists:m09_customer_types,m09_customer_type_id',
                'txt_edit_name'             => 'required|string|max:255',
                'txt_edit_email'            => 'required|email|max:255',
                'txt_edit_phone'            => 'required|string|max:15',
                'txt_edit_gst'              => 'nullable|string|max:15',
                'txt_edit_iec'              => 'nullable|string|max:20',
                'txt_edit_contact_person'   => 'required|string|max:255',
                'txt_edit_state_id'         => 'required|integer|exists:m01_states,m01_state_id',
                'txt_edit_district_id'      => 'required|integer|exists:m02_districts,m02_district_id',
                'txt_edit_pincode'          => 'required|string|max:10',
                'txt_edit_address'          => 'required|string|max:500',

                'locations'                 => 'nullable|array',
                'locations.*.id'            => 'nullable|integer|exists:m08_customer_locations,m08_customer_location_id',
                'locations.*.contact_person' => 'required|string|max:255',
                'locations.*.email'         => 'required|email|max:255',
                'locations.*.phone'         => 'required|string|max:15',
                'locations.*.gst'           => 'nullable|string|max:15',
                'locations.*.state_id'      => 'required|integer|exists:m01_states,m01_state_id',
                'locations.*.district_id'   => 'required|integer|exists:m02_districts,m02_district_id',
                'locations.*.pincode'       => 'required|string|max:10',
                'locations.*.address'       => 'required|string|max:500',
            ], [
                'txt_edit_customer_type_id.required' => 'The customer type field is required.',
                'txt_edit_customer_type_id.integer' => 'The customer type must be an integer.',
                'txt_edit_customer_type_id.exists' => 'The selected customer type is invalid.',

                'txt_edit_name.required' => 'The name field is required.',
                'txt_edit_name.string' => 'The name must be a string.',
                'txt_edit_name.max' => 'The name may not be greater than :max characters.',

                'txt_edit_email.required' => 'The email field is required.',
                'txt_edit_email.email' => 'Please enter a valid email address.',
                'txt_edit_email.max' => 'The email may not be greater than :max characters.',

                'txt_edit_phone.required' => 'The phone field is required.',
                'txt_edit_phone.string' => 'The phone must be a string.',
                'txt_edit_phone.max' => 'The phone may not be greater than :max characters.',

                'txt_edit_gst.string' => 'The GST must be a string.',
                'txt_edit_gst.max' => 'The GST may not be greater than :max characters.',

                'txt_edit_iec.string' => 'The IEC must be a string.',
                'txt_edit_iec.max' => 'The IEC may not be greater than :max characters.',

                'txt_edit_contact_person.required' => 'The contact person field is required.',
                'txt_edit_contact_person.string' => 'The contact person must be a string.',
                'txt_edit_contact_person.max' => 'The contact person may not be greater than :max characters.',

                'txt_edit_state_id.required' => 'The state field is required.',
                'txt_edit_state_id.integer' => 'The state must be an integer.',
                'txt_edit_state_id.exists' => 'The selected state is invalid.',

                'txt_edit_district_id.required' => 'The district field is required.',
                'txt_edit_district_id.integer' => 'The district must be an integer.',
                'txt_edit_district_id.exists' => 'The selected district is invalid.',

                'txt_edit_pincode.required' => 'The pincode field is required.',
                'txt_edit_pincode.string' => 'The pincode must be a string.',
                'txt_edit_pincode.max' => 'The pincode may not be greater than :max characters.',

                'txt_edit_address.required' => 'The address field is required.',
                'txt_edit_address.string' => 'The address must be a string.',
                'txt_edit_address.max' => 'The address may not be greater than :max characters.',

                'locations.array' => 'The locations must be an array.',

                'locations.*.id.integer' => 'A location ID must be an integer.',
                'locations.*.id.exists' => 'The selected location ID is invalid.',

                'locations.*.contact_person.required' => 'The location contact person is required.',
                'locations.*.contact_person.string' => 'A location contact person must be a string.',
                'locations.*.contact_person.max' => 'A location contact person may not be greater than :max characters.',

                'locations.*.email.required' => 'The location email field is required.',
                'locations.*.email.email' => 'Please enter a valid email address for the location.',
                'locations.*.email.max' => 'A location email may not be greater than :max characters.',

                'locations.*.phone.required' => 'The location phone field is required.',
                'locations.*.phone.string' => 'A location phone must be a string.',
                'locations.*.phone.max' => 'A location phone may not be greater than :max characters.',

                'locations.*.gst.string' => 'A location GST must be a string.',
                'locations.*.gst.max' => 'A location GST may not be greater than :max characters.',

                'locations.*.state_id.required' => 'The location state field is required.',
                'locations.*.state_id.integer' => 'A location state must be an integer.',
                'locations.*.state_id.exists' => 'The selected location state is invalid.',

                'locations.*.district_id.required' => 'The location district field is required.',
                'locations.*.district_id.integer' => 'A location district must be an integer.',
                'locations.*.district_id.exists' => 'The selected location district is invalid.',

                'locations.*.pincode.required' => 'The location pincode field is required.',
                'locations.*.pincode.string' => 'A location pincode must be a string.',
                'locations.*.pincode.max' => 'A location pincode may not be greater than :max characters.',

                'locations.*.address.required' => 'The location address field is required.',
                'locations.*.address.string' => 'A location address must be a string.',
                'locations.*.address.max' => 'A location address may not be greater than :max characters.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $customer->update([
                    'm09_customer_type_id' => $request->txt_edit_customer_type_id,
                    'm07_name'             => $request->txt_edit_name,
                    'm07_email'            => $request->txt_edit_email,
                    'm07_phone'            => $request->txt_edit_phone,
                    'm07_gst'              => $request->txt_edit_gst,
                    'm07_iec_code'         => $request->txt_edit_iec,
                    'm07_contact_person'   => $request->txt_edit_contact_person,
                    'm01_state_id'         => $request->txt_edit_state_id,
                    'm02_district_id'      => $request->txt_edit_district_id,
                    'm07_pincode'          => $request->txt_edit_pincode,
                    'm07_address'          => $request->txt_edit_address,
                ]);
                $submittedLocations = $request->input('locations', []);
                $submittedLocationIds = collect($submittedLocations)->pluck('id')->filter()->all();

                CustomerLocation::where('m07_customer_id', $customer->m07_customer_id)
                    ->whereNotIn('m08_customer_location_id', $submittedLocationIds)
                    ->delete();

                foreach ($submittedLocations as $locationData) {
                    if (empty($locationData['address']) && empty($locationData['contact_person'])) {
                        continue;
                    }

                    $dataToUpdateOrCreate = [
                        'm08_contact_person' => $locationData['contact_person'],
                        'm08_email'          => $locationData['email'],
                        'm08_phone'          => $locationData['phone'],
                        'm08_gst'            => $locationData['gst'],
                        'm01_state_id'       => $locationData['state_id'],
                        'm02_district_id'    => $locationData['district_id'],
                        'm08_pincode'        => $locationData['pincode'],
                        'm08_address'        => $locationData['address'],
                    ];

                    CustomerLocation::updateOrCreate(
                        [
                            'm08_customer_location_id' => $locationData['id'] ?? null,
                            'm07_customer_id'          => $customer->m07_customer_id
                        ],
                        $dataToUpdateOrCreate
                    );
                }

                DB::commit();

                Session::flash('type', 'success');
                Session::flash('message', 'Customer updated successfully!');
                return to_route('customers');
            } catch (\Exception $e) {
                DB::rollBack();
                // Log::error('Customer Update Failed: ' . $e->getMessage());
                Session::flash('type', 'error');
                Session::flash('message', 'An error occurred while updating the customer. Please try again.');
                return redirect()->back()->withInput();
            }
        }
        $states = State::get(['m01_state_id', 'm01_name']);
        $ros = Ro::get(['m04_ro_id', 'm04_name']);
        $customerTypes = CustomerType::get(['m09_customer_type_id', 'm09_name']);
        $customer->locations = $customer->locations->toArray();

        return view('edit_customer', compact('customer', 'states', 'ros', 'customerTypes'));
    }

    public function addLocation(Request $request)
    {

        $validated = $request->validate([
            'txt_loc_customer_id' => 'required|exists:m07_customers,m07_customer_id',
            'txt_loc_contact_person' => 'required|string|max:255',
            'txt_loc_email' => 'nullable|email',
            'txt_loc_phone' => 'nullable|string|max:20',
            'txt_loc_state_id' => 'required|exists:m01_states,m01_state_id',
            'txt_loc_district_id' => 'required|exists:m02_districts,m02_district_id',
            'txt_loc_pincode' => 'nullable|string|max:10',
            'txt_loc_address' => 'required|string',
        ]);
        $data = [
            'm07_customer_id' => $request->txt_loc_customer_id,
            'm08_contact_person' => $request->txt_loc_contact_person,
            'm08_email' => $request->txt_loc_email,
            'm08_phone' => $request->txt_loc_phone,
            'm01_state_id' => $request->txt_loc_state_id,
            'm02_district_id' => $request->txt_loc_district_id,
            'm08_pincode' => $request->txt_loc_pincode,
            'm08_address' => $request->txt_loc_address,
        ];
        // dd($validated);
        $location = CustomerLocation::create($data);
        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }
}
