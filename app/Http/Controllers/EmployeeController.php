<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Ro;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function viewEmployee()
    {
        $employees = Employee::get();
        return view('employees.employees', compact('employees'));
    }

    public function createEmployee(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_ro_id'        => 'required|integer',
                'txt_role_id'      => 'required|integer',
                'txt_name'         => 'required|string|max:255',
                'txt_email'        => 'required|email|unique:m06_employees,m06_email',
                'txt_phone'        => 'required|digits:10|unique:m06_employees,m06_phone',
                'txt_state_id'     => 'required|integer',
                'txt_district_id'  => 'required|integer',
            ], [
                'txt_ro_id.required'       => 'The RO field is required.',
                'txt_role_id.required'     => 'The role is required.',
                'txt_name.required'        => 'Employee name is required.',
                'txt_email.required'       => 'Email is required.',
                'txt_email.email'          => 'Email must be a valid email address.',
                'txt_email.unique'         => 'This email is already registered.',
                'txt_phone.required'       => 'Phone number is required.',
                'txt_phone.digits'         => 'Phone must be exactly 10 digits.',
                'txt_phone.unique'         => 'This phone number is already in use.',
                'txt_state_id.required'    => 'Please select a state.',
                'txt_district_id.required' => 'Please select a district.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            DB::beginTransaction();
            try {
                $user = User::create([
                    'tr01_email'    => $request->txt_email,
                    'tr01_password' => Hash::make('Default@123'),
                    'tr01_type' => 'EMPLOYEE'
                ]);
                Employee::create([
                    'tr01_user_id'      => $user->tr01_user_id,
                    'm04_ro_id'        => $request->txt_ro_id,
                    'm01_state_id'     => $request->txt_state_id,
                    'm02_district_id'  => $request->txt_district_id,
                    'm06_name'         => $request->txt_name,
                    'm06_email'        => $request->txt_email,
                    'm06_phone'        => $request->txt_phone,
                    'm03_role_id'      => $request->txt_role_id,
                ]);
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Employee created successfully.');
                return to_route('view_employees');
            } catch (\Exception $e) {
                DB::rollBack();
                // \Log::error('Employee creation failed', ['error' => $e->getMessage()]);
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create employee. Please try again.');
                return redirect()->back();
            }
        }
        $states = State::where('m01_status', 'ACTIVE')->get(['m01_state_id', 'm01_name']);
        $ros = Ro::where('m04_status', 'ACTIVE')->get(['m04_ro_id', 'm04_name']);
        $roles = Role::where('m03_status', 'ACTIVE')->get(['m03_role_id', 'm03_name']);
        return view('employees.create_employee', compact('states', 'ros', 'roles'));
    }
}
