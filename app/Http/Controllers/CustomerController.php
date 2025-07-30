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
    public function createCustomer(Request $request)
    {
        if ($request->isMethod('post')) {
            // dd($request);
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

                Session::flash('type', 'success');
                Session::flash('message', 'Customer and locations added successfully!');
                return redirect()->back();
            } catch (\Exception $e) {
                Session::flash('type', 'danger');
                Session::flash('message', 'An error occurred while saving the data. Please try again.');
                // Log::error($e->getMessage());
                return redirect()->back()->withInput();
            }
        }
        $states = State::get(['m01_state_id', 'm01_name']);
        $ros = Ro::get(['m04_ro_id', 'm04_name']);
        $customerTypes = CustomerType::get(['m09_customer_type_id', 'm09_name']);
        return view('create_customer', compact('states', 'ros', 'customerTypes'));
    }
}
