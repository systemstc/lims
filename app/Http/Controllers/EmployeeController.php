<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Employee;
use App\Models\Ro;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    /**
     * Download sample CSV template formatted for employee import.
     */
    public function downloadSampleCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="employee_import_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $sampleData = [
            ['Region', 'Name', 'Designation', 'Gov Email', 'mobile', 'Empl Id', 'Role1', 'Role2', 'Role3', 'Role4', 'Password'],
            ['MUMBAI', 'DR. K S MURALIDHARA', 'JT. DIRECTOR (LAB)', 'ks.muralidhara@tc.gov.in', '9967533770', '130228', '', '', '', 'Admin', 'Default@123'],
            ['INDORE', 'SACHIN THAKUR', 'QAO (LAB)', 'sachin-thakur.tc@gov.in', '9509210055', '130245', 'RC DEO', 'Analyst', 'Lab Manager', '', 'Default@123'],
            ['CHENNAI', 'R SUNDARI', 'QAO (LAB)', 'sundari-r.tc@gov.in', '9444949108', '130215', '', 'Analyst', 'Lab Manager', '', 'Default@123'],
            ['COIMBATORE', 'L GOPALAKRISHNAN', 'QAO (LAB)', 'l-gopalakrishnan.tc@gov.in', '8667763136', '130214', '', 'Analyst', 'Lab Manager', '', 'Default@123'],
            ['DELHI - NCR', 'POONAM MALIK', 'ASSISTANT', 'poonam-malik.tc@gov.in', '7988652529', '124061', 'RC DEO', '', '', '', 'Default@123'],
            ['MUMBAI - JNPT', 'JAYANTI HEMBRAM', 'JQAO (LAB)', 'jayanti-hembram.tc@gov.in', '7319105330', '130282', '', 'Analyst', '', '', 'Default@123'],
            ['HYDERABAD', 'K SIREESHA', 'QAO (LAB)', 'sireesha-k.tc@gov.in', '9849679368', '130211', '', 'Analyst', 'Lab Manager', '', 'Default@123'],
        ];

        $callback = function () use ($sampleData) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            foreach ($sampleData as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import employees from uploaded CSV or Excel file.
     */
    public function importEmployees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_file' => 'required|file|max:10240',
        ], [
            'employee_file.required' => 'Please select a CSV or Excel (.xlsx) file to upload.',
            'employee_file.max'      => 'File size cannot exceed 10MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('employee_file');
        $defaultPassword = $request->input('default_password') ?: 'Default@123';
        $updateExisting = $request->boolean('update_existing', true);
        $resetExistingPassword = $request->boolean('reset_existing_password', false);

        try {
            $rows = $this->parseUploadedFile($file);
        } catch (\Exception $e) {
            Session::flash('type', 'error');
            Session::flash('message', 'Failed to read uploaded file: ' . $e->getMessage());
            return redirect()->back();
        }

        if (empty($rows) || count($rows) < 2) {
            Session::flash('type', 'error');
            Session::flash('message', 'The uploaded file appears to be empty or contains only a header.');
            return redirect()->back();
        }

        // Extract header and map columns
        $headerRow = array_shift($rows);
        $colMap = $this->mapColumns($headerRow);

        if ($colMap['email'] === null && $colMap['name'] === null) {
            Session::flash('type', 'error');
            Session::flash('message', 'Could not detect required columns ("Name" and "Gov Email") in the file header. Please ensure your header matches the template.');
            return redirect()->back();
        }

        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($rows as $rowIndex => $row) {
            $displayRow = $rowIndex + 2;

            $name        = $colMap['name'] !== null ? trim($row[$colMap['name']] ?? '') : '';
            $email       = $colMap['email'] !== null ? strtolower(trim($row[$colMap['email']] ?? '')) : '';
            $phone       = $colMap['phone'] !== null ? $this->sanitizePhone($row[$colMap['phone']] ?? '') : '';
            $empId       = $colMap['emp_id'] !== null ? trim($row[$colMap['emp_id']] ?? '') : '';
            $region      = $colMap['region'] !== null ? trim($row[$colMap['region']] ?? '') : '';
            $designation = $colMap['designation'] !== null ? trim($row[$colMap['designation']] ?? '') : '';
            $rowPass     = $colMap['password'] !== null ? trim($row[$colMap['password']] ?? '') : '';
            $validUpto   = $colMap['valid_upto'] !== null ? trim($row[$colMap['valid_upto']] ?? '') : null;

            // Skip empty rows
            if (empty($name) && empty($email) && empty($phone) && empty($empId)) {
                continue;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                $errors[] = "Row #{$displayRow} (" . ($name ?: 'Unnamed') . "): Invalid or missing email address '{$email}'.";
                continue;
            }

            if (empty($name)) {
                $skippedCount++;
                $errors[] = "Row #{$displayRow}: Employee Name is required.";
                continue;
            }

            // Collect all roles from mapped role columns
            $roleStrings = [];
            foreach ($colMap['role_indices'] as $roleIdx) {
                $val = trim($row[$roleIdx] ?? '');
                if (!empty($val)) {
                    $parts = preg_split('/[,;\/|]+/', $val);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if (!empty($p)) {
                            $roleStrings[] = $p;
                        }
                    }
                }
            }

            // If RC and DEO appear as separate tokens, ensure RC DEO is recognized
            if (in_array('RC', $roleStrings) && in_array('DEO', $roleStrings) && !in_array('RC DEO', $roleStrings)) {
                $roleStrings[] = 'RC DEO';
            }

            // Resolve role IDs
            $assignedRoleIds = [];
            foreach ($roleStrings as $rStr) {
                $roleModel = $this->resolveRole($rStr);
                if ($roleModel) {
                    $assignedRoleIds[] = (int) $roleModel->m03_role_id;
                }
            }
            $assignedRoleIds = array_values(array_unique($assignedRoleIds));

            if (empty($assignedRoleIds)) {
                $defaultRole = Role::where('m03_name', 'Analyst')->first() ?? Role::first();
                if ($defaultRole) {
                    $assignedRoleIds[] = (int) $defaultRole->m03_role_id;
                }
            }

            $primaryRoleId = $assignedRoleIds[0] ?? 1;

            // Resolve Regional Office
            $ro = $this->resolveRo($region);
            $roId = $ro ? $ro->m04_ro_id : 1;

            // Resolve State and District
            [$stateId, $districtId] = $this->resolveStateAndDistrict($region, $ro);

            // Password
            $passwordToUse = !empty($rowPass) ? $rowPass : $defaultPassword;

            DB::beginTransaction();
            try {
                // 1. User Account
                $user = User::where('tr01_email', $email)->first();
                if ($user) {
                    $user->tr01_name = $name;
                    $user->tr01_type = 'EMPLOYEE';
                    if ($resetExistingPassword || !empty($rowPass)) {
                        $user->tr01_password = Hash::make($passwordToUse);
                    }
                    $user->save();
                } else {
                    $user = User::create([
                        'tr01_name'     => $name,
                        'tr01_email'    => $email,
                        'tr01_password' => Hash::make($passwordToUse),
                        'tr01_type'     => 'EMPLOYEE',
                    ]);
                }

                // 2. Employee Record
                $employee = null;
                if ($user) {
                    $employee = Employee::where('tr01_user_id', $user->tr01_user_id)->first();
                }
                if (!$employee && !empty($email)) {
                    $employee = Employee::where('m06_email', $email)->first();
                }
                if (!$employee && !empty($empId)) {
                    $employee = Employee::where('m06_emp_id', $empId)->first();
                }

                if ($employee) {
                    if (!$updateExisting) {
                        DB::rollBack();
                        $skippedCount++;
                        $errors[] = "Row #{$displayRow} ($email): Employee already exists and overwrite option is disabled.";
                        continue;
                    }

                    $employee->tr01_user_id = $user->tr01_user_id;
                    $employee->m06_name     = $name;
                    $employee->m06_email    = $email;
                    if (!empty($phone)) {
                        $employee->m06_phone = $phone;
                    }
                    if (!empty($empId)) {
                        $employee->m06_emp_id = $empId;
                    }
                    $employee->m04_ro_id       = $roId;
                    $employee->m03_role_id     = $primaryRoleId;
                    $employee->m01_state_id    = $stateId;
                    $employee->m02_district_id = $districtId;
                    $employee->m06_status      = 'ACTIVE';

                    if (!empty($validUpto)) {
                        try {
                            $employee->m06_valid_upto = \Carbon\Carbon::parse($validUpto)->format('Y-m-d');
                        } catch (\Exception $e) {}
                    }

                    if (Schema::hasColumn('m06_employees', 'm06_designation') && !empty($designation)) {
                        $employee->m06_designation = $designation;
                    }

                    $employee->save();
                    $updatedCount++;
                } else {
                    $employeeData = [
                        'tr01_user_id'    => $user->tr01_user_id,
                        'm04_ro_id'       => $roId,
                        'm01_state_id'    => $stateId,
                        'm02_district_id' => $districtId,
                        'm06_name'        => $name,
                        'm06_email'       => $email,
                        'm06_phone'       => $phone ?: '9999999999',
                        'm03_role_id'     => $primaryRoleId,
                        'm06_emp_id'      => $empId ?: null,
                        'm06_status'      => 'ACTIVE',
                    ];

                    if (!empty($validUpto)) {
                        try {
                            $employeeData['m06_valid_upto'] = \Carbon\Carbon::parse($validUpto)->format('Y-m-d');
                        } catch (\Exception $e) {}
                    }

                    if (Schema::hasColumn('m06_employees', 'm06_designation') && !empty($designation)) {
                        $employeeData['m06_designation'] = $designation;
                    }

                    $employee = Employee::create($employeeData);
                    $createdCount++;
                }

                // 3. User Roles Pivot Sync
                if (Schema::hasTable('tr01_user_roles') && $user->tr01_user_id) {
                    DB::table('tr01_user_roles')->where('tr01_user_id', $user->tr01_user_id)->delete();
                    foreach ($assignedRoleIds as $index => $roleId) {
                        DB::table('tr01_user_roles')->insert([
                            'tr01_user_id' => $user->tr01_user_id,
                            'm03_role_id'  => $roleId,
                            'is_primary'   => ($index === 0),
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error importing employee row #{$displayRow}", ['error' => $e->getMessage()]);
                $skippedCount++;
                $errors[] = "Row #{$displayRow} ({$email}): " . $e->getMessage();
            }
        }

        $totalProcessed = $createdCount + $updatedCount;
        if ($totalProcessed > 0) {
            Session::flash('type', 'success');
            Session::flash('message', "Import completed: {$createdCount} employee(s) created, {$updatedCount} updated successfully." . ($skippedCount > 0 ? " ({$skippedCount} skipped/failed)" : ''));
        } else {
            Session::flash('type', 'error');
            Session::flash('message', "No employees were imported. Please check the error list below.");
        }

        if (!empty($errors)) {
            Session::flash('import_errors', $errors);
        }

        return to_route('view_employees');
    }

    /**
     * Map headers to expected columns dynamically.
     */
    protected function mapColumns(array $headers): array
    {
        $map = [
            'region'       => null,
            'name'         => null,
            'designation'  => null,
            'email'        => null,
            'phone'        => null,
            'emp_id'       => null,
            'password'     => null,
            'valid_upto'   => null,
            'role_indices' => [],
        ];

        foreach ($headers as $idx => $h) {
            $key = strtolower(trim((string)$h));
            $cleanKey = preg_replace('/[^a-z0-9]/', '', $key);

            if ($cleanKey === 'region' || str_contains($key, 'region') || $cleanKey === 'ro' || str_contains($key, 'office')) {
                $map['region'] = $idx;
            } elseif ($cleanKey === 'name' || $cleanKey === 'fullname' || $cleanKey === 'employeename' || $cleanKey === 'empname' || ($cleanKey !== 'rolename' && str_contains($key, 'name') && !str_contains($key, 'role') && !str_contains($key, 'lab') && !str_contains($key, 'gov'))) {
                $map['name'] = $idx;
            } elseif (str_contains($key, 'designation') || str_contains($key, 'desig') || $cleanKey === 'post') {
                $map['designation'] = $idx;
            } elseif (str_contains($key, 'email') || str_contains($key, 'mail') || $cleanKey === 'govemail') {
                $map['email'] = $idx;
            } elseif (str_contains($key, 'mobile') || str_contains($key, 'phone') || str_contains($key, 'contact')) {
                $map['phone'] = $idx;
            } elseif (str_contains($key, 'empl') || str_contains($key, 'empid') || str_contains($key, 'govt') || $cleanKey === 'emplid' || $cleanKey === 'empid') {
                $map['emp_id'] = $idx;
            } elseif (str_contains($key, 'pass')) {
                $map['password'] = $idx;
            } elseif (str_contains($key, 'valid') || str_contains($key, 'expiry')) {
                $map['valid_upto'] = $idx;
            } elseif (str_contains($cleanKey, 'role') || in_array($cleanKey, ['rc', 'deo', 'analyst', 'admin', 'manager', 'labmanager', 'rcdeo'])) {
                $map['role_indices'][] = $idx;
            }
        }

        // If no role columns detected, any remaining unmapped columns are treated as possible role columns
        if (empty($map['role_indices'])) {
            $knownIndices = array_filter([$map['region'], $map['name'], $map['designation'], $map['email'], $map['phone'], $map['emp_id'], $map['password'], $map['valid_upto']], fn($v) => $v !== null);
            foreach ($headers as $idx => $h) {
                if (!in_array($idx, $knownIndices)) {
                    $map['role_indices'][] = $idx;
                }
            }
        }

        return $map;
    }

    /**
     * Resolve or dynamically create Role.
     */
    protected function resolveRole(string $roleName): ?Role
    {
        $cleanName = trim($roleName);
        if (empty($cleanName)) {
            return null;
        }

        // Direct case-insensitive match
        $role = Role::whereRaw('LOWER(TRIM(m03_name)) = ?', [strtolower($cleanName)])->first();
        if ($role) {
            return $role;
        }

        // Common aliases in LIMS
        $lower = strtolower($cleanName);
        $aliasMap = [
            'rc deo'       => ['RC DEO', 'DEO', 'Registrar'],
            'rc'           => ['RC DEO', 'Registrar', 'DEO'],
            'deo'          => ['DEO', 'RC DEO', 'Registrar'],
            'lab manager'  => ['Lab Manager', 'Manager', 'RO Admin', 'Admin'],
            'manager'      => ['Lab Manager', 'Manager'],
            'analyst'      => ['Analyst'],
            'admin'        => ['Admin', 'RO Admin', 'Super Admin'],
            'ro admin'     => ['RO Admin', 'Admin'],
        ];

        if (isset($aliasMap[$lower])) {
            foreach ($aliasMap[$lower] as $candidate) {
                $matched = Role::whereRaw('LOWER(TRIM(m03_name)) = ?', [strtolower($candidate)])->first();
                if ($matched) {
                    return $matched;
                }
            }
        }

        // Substring match
        $matched = Role::whereRaw('LOWER(m03_name) LIKE ?', ['%' . $lower . '%'])->first();
        if ($matched) {
            return $matched;
        }

        // Dynamically create role so import never breaks
        return Role::create([
            'm03_name'   => $cleanName,
            'm03_status' => 'ACTIVE',
        ]);
    }

    /**
     * Resolve Regional Office (RO) by name or region string.
     */
    protected function resolveRo(?string $regionName): ?Ro
    {
        $cleanRegion = trim((string)$regionName);
        if (empty($cleanRegion)) {
            return Ro::where('m04_status', 'ACTIVE')->first() ?? Ro::first();
        }

        // Direct match
        $ro = Ro::whereRaw('LOWER(TRIM(m04_name)) = ?', [strtolower($cleanRegion)])->first();
        if ($ro) {
            return $ro;
        }

        // Try prefix "RO " or "RO - "
        $ro = Ro::whereRaw('LOWER(TRIM(m04_name)) = ?', ['ro ' . strtolower($cleanRegion)])
            ->orWhereRaw('LOWER(TRIM(m04_name)) = ?', ['ro - ' . strtolower($cleanRegion)])
            ->first();
        if ($ro) {
            return $ro;
        }

        // Substring match
        $ro = Ro::whereRaw('LOWER(m04_name) LIKE ?', ['%' . strtolower($cleanRegion) . '%'])
            ->orWhereRaw('? LIKE CONCAT("%", LOWER(m04_name), "%")', [strtolower($cleanRegion)])
            ->first();
        if ($ro) {
            return $ro;
        }

        // Multi-part regions like "DELHI - NCR" or "MUMBAI - JNPT"
        if (str_contains($cleanRegion, '-')) {
            $primaryPart = trim(explode('-', $cleanRegion)[0]);
            if (!empty($primaryPart)) {
                $ro = Ro::whereRaw('LOWER(m04_name) LIKE ?', ['%' . strtolower($primaryPart) . '%'])->first();
                if ($ro) {
                    return $ro;
                }
            }
        }

        // Fallback to first available active RO
        return Ro::where('m04_status', 'ACTIVE')->first() ?? Ro::first();
    }

    /**
     * Resolve State & District IDs.
     */
    protected function resolveStateAndDistrict(?string $regionName, ?Ro $ro): array
    {
        $stateId = null;
        $districtId = null;

        if (!empty($regionName)) {
            $cleanRegion = trim(explode('-', $regionName)[0]);
            $state = State::whereRaw('LOWER(m01_name) LIKE ?', ['%' . strtolower($cleanRegion) . '%'])->first();
            if ($state) {
                $stateId = $state->m01_state_id;
                $district = District::where('m01_state_id', $stateId)
                    ->whereRaw('LOWER(m02_name) LIKE ?', ['%' . strtolower($cleanRegion) . '%'])
                    ->first()
                    ?? District::where('m01_state_id', $stateId)->first();
                if ($district) {
                    $districtId = $district->m02_district_id;
                }
            } else {
                $district = District::whereRaw('LOWER(m02_name) LIKE ?', ['%' . strtolower($cleanRegion) . '%'])->first();
                if ($district) {
                    $districtId = $district->m02_district_id;
                    $stateId = $district->m01_state_id;
                }
            }
        }

        if (!$stateId) {
            $stateId = State::value('m01_state_id') ?? 1;
        }
        if (!$districtId) {
            $districtId = District::where('m01_state_id', $stateId)->value('m02_district_id')
                ?? District::value('m02_district_id')
                ?? 1;
        }

        return [$stateId, $districtId];
    }

    /**
     * Clean phone numbers to digits (last 10 digits).
     */
    protected function sanitizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $digits = preg_replace('/\D/', '', (string)$phone);
        if (strlen($digits) > 10) {
            return substr($digits, -10);
        }
        return $digits;
    }

    /**
     * Parse uploaded file (CSV or XLSX).
     */
    protected function parseUploadedFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            return $this->parseXlsxFile($path);
        }

        return $this->parseCsvFile($path);
    }

    /**
     * Parse CSV or TXT file.
     */
    protected function parseCsvFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        // Strip UTF-8 BOM if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        // Auto-detect delimiter
        $firstLine = strtok($content, "\r\n");
        $delimiter = ',';
        if ($firstLine !== false) {
            $commaCount = substr_count($firstLine, ',');
            $semicolonCount = substr_count($firstLine, ';');
            $tabCount = substr_count($firstLine, "\t");
            if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
                $delimiter = ';';
            } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
                $delimiter = "\t";
            }
        }

        $tempStream = fopen('php://memory', 'r+');
        fwrite($tempStream, $content);
        rewind($tempStream);

        $rows = [];
        while (($row = fgetcsv($tempStream, 0, $delimiter)) !== false) {
            $cleanedRow = array_map(function ($val) {
                return trim((string)$val);
            }, $row);

            $hasData = false;
            foreach ($cleanedRow as $cell) {
                if ($cell !== '') {
                    $hasData = true;
                    break;
                }
            }

            if ($hasData) {
                $rows[] = $cleanedRow;
            }
        }

        fclose($tempStream);
        return $rows;
    }

    /**
     * Parse XLSX file using built-in ZipArchive and SimpleXML without external dependencies.
     */
    protected function parseXlsxFile(string $filePath): array
    {
        if (!class_exists('\ZipArchive')) {
            throw new \Exception('PHP ZipArchive extension is not enabled. Please save your file as CSV (.csv) and re-upload.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception('Failed to open Excel file. Please save as standard CSV (.csv) and upload.');
        }

        // 1. Shared Strings
        $sharedStrings = [];
        $sharedXmlString = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXmlString !== false) {
            $xml = @simplexml_load_string($sharedXmlString);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $val = '';
                        foreach ($si->r as $r) {
                            $val .= (string)$r->t;
                        }
                        $sharedStrings[] = $val;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Sheet XML
        $sheetXmlString = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlString === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                    $sheetXmlString = $zip->getFromIndex($i);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXmlString === false) {
            throw new \Exception('Could not read worksheet from Excel file.');
        }

        $xml = @simplexml_load_string($sheetXmlString);
        if (!$xml || !isset($xml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            $maxColIndex = 0;
            foreach ($row->c as $c) {
                $ref = (string)$c['r'];
                preg_match('/^([A-Z]+)(\d+)$/', $ref, $matches);
                if (!empty($matches[1])) {
                    $colLetters = $matches[1];
                    $colIndex = 0;
                    for ($k = 0; $k < strlen($colLetters); $k++) {
                        $colIndex = $colIndex * 26 + (ord($colLetters[$k]) - ord('A') + 1);
                    }
                    $colIndex -= 1;
                } else {
                    $colIndex = $maxColIndex;
                }

                $type = (string)$c['t'];
                $val = isset($c->v) ? (string)$c->v : '';

                if ($type === 's') {
                    $strIndex = (int)$val;
                    $cellValue = $sharedStrings[$strIndex] ?? '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $cellValue = (string)$c->is->t;
                } else {
                    $cellValue = $val;
                }

                $rowData[$colIndex] = trim($cellValue);
                if ($colIndex > $maxColIndex) {
                    $maxColIndex = $colIndex;
                }
            }

            $completeRow = [];
            for ($cIdx = 0; $cIdx <= $maxColIndex; $cIdx++) {
                $completeRow[] = $rowData[$cIdx] ?? '';
            }
            $rows[] = $completeRow;
        }

        return $rows;
    }
}
