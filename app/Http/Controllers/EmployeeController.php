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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function viewEmployee()
    {
        $employees = Employee::with(['user.roles', 'roles', 'ro', 'role'])->get();
        return view('employees.employees', compact('employees'));
    }

    public function createEmployee(Request $request)
    {
        if ($request->isMethod('POST')) {
            // Support both txt_role_ids array and legacy txt_role_id
            $roleIds = $request->input('txt_role_ids');
            if (empty($roleIds) && $request->filled('txt_role_id')) {
                $roleIds = [$request->input('txt_role_id')];
                $request->merge(['txt_role_ids' => $roleIds]);
            }

            $validator = Validator::make($request->all(), [
                'txt_ro_id'        => 'required|integer',
                'txt_role_ids'     => 'required|array|min:1',
                'txt_role_ids.*'   => 'integer|exists:m03_roles,m03_role_id',
                'txt_name'         => 'required|string|max:255',
                'txt_email'        => 'required|email|unique:m06_employees,m06_email',
                'txt_phone'        => 'required|digits:10|unique:m06_employees,m06_phone',
                'txt_state_id'     => 'required|integer',
                'txt_district_id'  => 'required|integer',
                'txt_emp_id'       => 'nullable|string|max:100',
                'txt_valid_upto'   => 'nullable|date',
            ], [
                'txt_ro_id.required'       => 'The RO field is required.',
                'txt_role_ids.required'    => 'At least one role is required.',
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
                    'tr01_name'     => $request->txt_name,
                    'tr01_email'    => $request->txt_email,
                    'tr01_password' => Hash::make('Default@123'),
                    'tr01_type'     => 'EMPLOYEE'
                ]);

                $primaryRoleId = (int) $request->txt_role_ids[0];

                $employeeData = [
                    'tr01_user_id'     => $user->tr01_user_id,
                    'm04_ro_id'        => $request->txt_ro_id,
                    'm01_state_id'     => $request->txt_state_id,
                    'm02_district_id'  => $request->txt_district_id,
                    'm06_name'         => $request->txt_name,
                    'm06_email'        => $request->txt_email,
                    'm06_phone'        => $request->txt_phone,
                    'm03_role_id'      => $primaryRoleId,
                    'm06_emp_id'       => $request->txt_emp_id,
                ];

                if ($request->filled('txt_valid_upto')) {
                    $employeeData['m06_valid_upto'] = $request->txt_valid_upto;
                }

                $employee = Employee::create($employeeData);

                // Insert all assigned roles into tr01_user_roles pivot
                if (Schema::hasTable('tr01_user_roles')) {
                    foreach ($request->txt_role_ids as $index => $roleId) {
                        DB::table('tr01_user_roles')->updateOrInsert(
                            ['tr01_user_id' => $user->tr01_user_id, 'm03_role_id' => $roleId],
                            ['is_primary' => ($index === 0), 'created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Employee created successfully.');
                return to_route('view_employees');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create employee. Please try again: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }
        $states = State::where('m01_status', 'ACTIVE')->get(['m01_state_id', 'm01_name']);
        $ros = Ro::where('m04_status', 'ACTIVE')->get(['m04_ro_id', 'm04_name']);
        $roles = Role::where('m03_status', 'ACTIVE')->get(['m03_role_id', 'm03_name']);
        return view('employees.create_employee', compact('states', 'ros', 'roles'));
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        if ($request->isMethod('POST')) {
            // Support both txt_role_ids array and legacy txt_role_id
            $roleIds = $request->input('txt_role_ids');
            if (empty($roleIds) && $request->filled('txt_role_id')) {
                $roleIds = [$request->input('txt_role_id')];
                $request->merge(['txt_role_ids' => $roleIds]);
            }

            $validator = Validator::make($request->all(), [
                'txt_ro_id'        => 'required|integer',
                'txt_role_ids'     => 'required|array|min:1',
                'txt_role_ids.*'   => 'integer|exists:m03_roles,m03_role_id',
                'txt_name'         => 'required|string|max:255',
                'txt_email'        => 'required|email|unique:m06_employees,m06_email,' . $employee->m06_employee_id . ',m06_employee_id',
                'txt_phone'        => 'required|digits:10|unique:m06_employees,m06_phone,' . $employee->m06_employee_id . ',m06_employee_id',
                'txt_state_id'     => 'required|integer',
                'txt_district_id'  => 'required|integer',
                'txt_emp_id'       => 'nullable|string|max:100',
                'txt_valid_upto'   => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                // Update User
                if ($employee->user) {
                    $employee->user->update([
                        'tr01_name'  => $request->txt_name,
                        'tr01_email' => $request->txt_email,
                    ]);
                }

                $primaryRoleId = (int) $request->txt_role_ids[0];

                $employeeUpdateData = [
                    'm04_ro_id'        => $request->txt_ro_id,
                    'm01_state_id'     => $request->txt_state_id,
                    'm02_district_id'  => $request->txt_district_id,
                    'm06_name'         => $request->txt_name,
                    'm06_email'        => $request->txt_email,
                    'm06_phone'        => $request->txt_phone,
                    'm03_role_id'      => $primaryRoleId,
                    'm06_emp_id'       => $request->txt_emp_id,
                    'm06_valid_upto'   => $request->txt_valid_upto ?: null,
                ];

                $employee->update($employeeUpdateData);

                // Sync roles in tr01_user_roles pivot
                if (Schema::hasTable('tr01_user_roles') && $employee->tr01_user_id) {
                    DB::table('tr01_user_roles')->where('tr01_user_id', $employee->tr01_user_id)->delete();
                    foreach ($request->txt_role_ids as $index => $roleId) {
                        DB::table('tr01_user_roles')->insert([
                            'tr01_user_id' => $employee->tr01_user_id,
                            'm03_role_id'  => $roleId,
                            'is_primary'   => ($index === 0),
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Employee updated successfully.');
                return to_route('view_employees');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to update employee. Please try again: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        $states = State::where('m01_status', 'ACTIVE')->get(['m01_state_id', 'm01_name']);
        $ros = Ro::where('m04_status', 'ACTIVE')->get(['m04_ro_id', 'm04_name']);
        $roles = Role::where('m03_status', 'ACTIVE')->get(['m03_role_id', 'm03_name']);

        // Get currently assigned role IDs
        $assignedRoleIds = [];
        if (Schema::hasTable('tr01_user_roles') && $employee->tr01_user_id) {
            $assignedRoleIds = DB::table('tr01_user_roles')
                ->where('tr01_user_id', $employee->tr01_user_id)
                ->pluck('m03_role_id')
                ->map(fn($id) => (int) $id)
                ->toArray();
        }
        if (empty($assignedRoleIds) && $employee->m03_role_id) {
            $assignedRoleIds = [(int) $employee->m03_role_id];
        }

        return view('employees.edit_employee', compact('employee', 'states', 'ros', 'roles', 'assignedRoleIds'));
    }
}
