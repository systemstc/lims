<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\District;
use App\Models\Group;
use App\Models\LabSample;
use App\Models\PrimaryTest;
use App\Models\Role;
use App\Models\Sample;
use App\Models\SecondaryTest;
use App\Models\Stage;
use App\Models\Standard;
use App\Models\State;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    public function adminDashboard()
    {
        return view('dashboards.admin_dashboard');
    }

    public function viewStates()
    {
        $states = State::get();
        return view("Master.states", compact('states'));
    }

    public function viewDistricts(Request $request)
    {
        if ($request->ajax()) {
            $data = District::with('state')->select('m02_districts.*');
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('district_name', fn($row) => $row->m02_name)
                ->addColumn('state_name', fn($row) => optional($row->state)->m01_name ?? '-')
                ->addColumn('status', function ($row) {
                    $statusClass = $row->m02_status === 'ACTIVE' ? 'success' : 'danger';
                    return '<strong class="text-' . $statusClass . '">' . $row->m02_status . '</strong>';
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<a class="edit-btn btn" data-id="' . $row->m02_district_id . '" data-name="' . $row->m02_name . '" data-state-id="' . ($row->state->m01_state_id ?? '') . '" data-bs-toggle="modal" data-bs-target="#modalZoom"><em class="icon ni ni-edit"></em><span>Edit</span></a>';
                    $statusBtn = '<a class="btn eg-swal-av3" data-id="' . $row->m02_district_id . '" data-status="' . $row->m02_status . '"><em class="icon ni ni-trash"></em><span>Change Status</span></a>';
                    return '<ul class="nk-tb-actions gx-1 my-n1"><li class="me-n1"><div class="dropdown"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-end"><ul class="link-list-opt no-bdr"><li>' . $editBtn . '</li><li>' . $statusBtn . '</li></ul></div></div></li></ul>';
                })
                ->rawColumns(['status', 'action'])
                ->toJson();
        }
        $states = State::get();
        return view('master.districts', compact('states'));
    }

    public function updateDistrict(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:m02_districts,m02_district_id',
            'district_name' => 'required|string|max:255',
            'state_id' => 'required|exists:m01_states,m01_state_id',
        ]);

        $district = District::findOrFail($request->district_id);
        $district->m02_name = $request->district_name;
        $district->m01_state_id = $request->state_id;
        $district->save();

        Session::flash('type', 'success');
        Session::flash('message', 'District updated successfully.');
        return redirect()->back();
    }

    public function changeStatus(Request $request)
    {
        $district = District::findOrFail($request->id);
        $district->m02_status = $district->m02_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $district->save();

        return response()->json([
            'status' => 'success',
            'message' => 'District status updated to ' . $district->m02_status,
            'new_status' => $district->m02_status
        ]);
    }

    public function viewRoles()
    {
        $roles = Role::all();
        return view('master.roles', compact('roles'));
    }

    public function createRole(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_role' => 'required|string|max:255'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $create = Role::create(['m03_name' => $request->txt_role]);
            if ($create) {
                Session::flash('type', 'success');
                Session::flash('message', 'Role created Successfully!');
                return redirect()->back();
            }
        }
    }
    public function updateRole(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_edit_role' => 'required|string|max:255',
                'txt_edit_id' => 'required|exists:m03_roles,m03_role_id'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $update = Role::where('m03_role_id', $request->txt_edit_id)->update(['m03_name' => $request->txt_edit_role]);
            if ($update) {
                Session::flash('type', 'success');
                Session::flash('message', 'Role created Successfully!');
                return redirect()->back();
            }
        }
    }

    public function changeRoleStatus(Request $request)
    {
        $role = Role::findOrFail($request->id);

        $role->m03_status = $role->m03_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $role->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status has been updated to ' . $role->m03_status
        ]);
    }

    public function getDistricts(Request $request)
    {
        $stateId = $request->state_id;

        $districts = District::where('m01_state_id', $stateId)
            ->where('m02_status', 'ACTIVE')
            ->orderBy('m02_name')
            ->get(['m02_district_id', 'm02_name']);

        return response()->json($districts);
    }

    public function viewSamples()
    {
        $samples = Sample::all();
        return view('master.samples', compact('samples'));
    }

    public function createSample(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_sample_name" => "required|string|max:255|unique:m10_samples,m10_name",
                "txt_remark" => "nullable|string"
            ], [
                "txt_sample_name.required" => "Sample name is required.",
                "txt_sample_name.string" => "Sample name must be a string.",
                "txt_sample_name.max" => "Sample name may not be greater than 255 characters.",
                "txt_sample_name.unique" => "This sample name already exists.",
                "txt_remark.string" => "Remark must be a string."
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            Sample::create([
                'm10_name' => $request->txt_sample_name,
                'm10_remark' => $request->txt_remark,
                'tr01_created_by' => Session::get('user_id')
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Sample created successfully!');
            return redirect()->back();
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Invalid request method.');
        return redirect()->back();
    }

    public function updateSample(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_edit_sample_id' => 'required|integer|exists:m10_samples,m10_sample_id',
                'txt_edit_sample_name' => 'required|string|max:255|unique:m10_samples,m10_name,' . $request->txt_edit_sample_id . ',m10_sample_id',
                'txt_edit_remark' => 'nullable|string',
            ], [
                'txt_edit_sample_id.required' => 'Sample ID is required.',
                'txt_edit_sample_id.integer' => 'Invalid sample ID.',
                'txt_edit_sample_id.exists' => 'The selected sample does not exist.',
                'txt_edit_sample_name.required' => 'Sample name is required.',
                'txt_edit_sample_name.string' => 'Sample name must be a string.',
                'txt_edit_sample_name.max' => 'Sample name must not exceed 255 characters.',
                'txt_edit_sample_name.unique' => 'This sample name already exists.',
                'txt_edit_remark.string' => 'Remark must be a string.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $sample = Sample::find($request->txt_edit_sample_id);
            $sample->update([
                'm10_name' => $request->txt_edit_sample_name,
                'm10_remark' => $request->txt_edit_remark,
                'tr01_created_by' => Session::get('user_id')
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Sample updated successfully!');
            return redirect()->back();
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Invalid request method.');
        return redirect()->back();
    }

    public function deleteSample(Request $request)
    {
        $sample = Sample::findOrFail($request->id);

        $sample->m10_status = $sample->m10_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $sample->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status has been updated to ' . $sample->m10_status
        ]);
    }

    public function viewGroups()
    {
        $groups = Group::all();
        $samples = Sample::get(['m10_sample_id', 'm10_name']);
        return view('master.groups', compact('groups', 'samples'));
    }

    public function createGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txt_sample_id' => 'required|integer|exists:m10_samples,m10_sample_id',
            'txt_group_name' => 'required|string|max:255|unique:m11_groups,m11_name',
            'txt_group_charge' => 'required|integer',
            'txt_remark' => 'nullable|string',
        ], [
            'txt_group_name.required' => 'Group name is required.',
            'txt_group_name.unique' => 'Group name must be unique.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $groupCode = Group::orderBy('m11_group_id', 'desc')->value('m11_group_code');
        Group::create([
            'm10_sample_id' => $request->txt_sample_id,
            'm11_group_code' => $groupCode ? ($groupCode + 1) : 100,
            'm11_name' => $request->txt_group_name,
            'm11_group_charge' => $request->txt_group_charge,
            'm11_remark' => $request->txt_remark,
            'tr01_created_by' => Session::get('user_id'),
        ]);
        Session::flash('type', 'success');
        Session::flash('message', 'Group created successfully.');
        return redirect()->back();
    }

    public function updateGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txt_edit_group_id' => 'required|exists:m11_groups,m11_group_id',
            'txt_edit_group_name' => 'required|string|max:255|unique:m11_groups,m11_name,' . $request->txt_edit_group_id . ',m11_group_id',
            'txt_edit_remark' => 'nullable|string',
        ], [
            'txt_edit_group_name.required' => 'Group name is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $group = Group::find($request->txt_edit_group_id);
        $group->update([
            'm11_name' => $request->txt_edit_group_name,
            'm11_remark' => $request->txt_edit_remark,
        ]);
        Session::flash('type', 'success');
        Session::flash('message', 'Group updated successfully.');
        return to_route('view_groups');
    }

    public function deleteGroup(Request $request)
    {
        $group = Group::find($request->id);

        if ($group) {
            $group->m11_status = $group->m11_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $group->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $group->m11_status
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Group not found.'], 404);
    }

    public function viewDepartments()
    {
        $departments = Department::all();
        return view('master.departments', compact('departments'));
    }
    public function createDepartment(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                "txt_department_name" => "required|string|max:255|unique:m13_departments,m13_name",
                "txt_sample_no" => "required|integer|min:1",
                "txt_remark" => "nullable|string|max:500",
            ], [
                "txt_department_name.required" => "Department name is required.",
                "txt_department_name.unique" => "This department name already exists.",
                "txt_sample_no.required" => "Sample number is required.",
                "txt_sample_no.integer" => "Sample number must be an integer.",
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                Department::create([
                    'm04_ro_id' => Session::get('ro_id') ?? -1,
                    'm13_name' => $request->txt_department_name,
                    'm13_sample_no' => $request->txt_sample_no,
                    'm13_remark' => $request->txt_remark,
                    'tr01_created_by' => Session::get('user_id') ?? -1,
                ]);
                Session::flash('type', 'success');
                Session::flash('message', 'Department created successfully.');
                return redirect()->back();
            } catch (\Exception $e) {
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong: ' . $e->getMessage());
                return redirect()->back();
            }
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Invalid request method.');
        return redirect()->back();
    }

    public function updateDepartment(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                "txt_edit_department_id" => "required|integer|exists:m13_departments,m13_department_id",
                "txt_edit_department_name" => "required|string|max:255",
                "txt_edit_sample_no" => "required|integer|min:1",
                "txt_edit_remark" => "nullable|string|max:500",
            ], [
                "txt_edit_department_id.required" => "Invalid department selection.",
                "txt_edit_department_id.exists" => "Department not found.",
                "txt_edit_department_name.required" => "Department name is required.",
                "txt_edit_sample_no.required" => "Sample number is required.",
                "txt_edit_sample_no.integer" => "Sample number must be an integer.",
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            try {
                $department = Department::findOrFail($request->txt_edit_department_id);
                $department->m13_name = $request->txt_edit_department_name;
                $department->m13_sample_no = $request->txt_edit_sample_no;
                $department->m13_remark = $request->txt_edit_remark;
                $department->save();
                Session::flash('type', 'success');
                Session::flash('message', 'Department updated successfully.');
                return redirect()->back();
            } catch (\Exception $e) {
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong: ' . $e->getMessage());
                return redirect()->back();
            }
        }
        return redirect()->back();
    }

    public function deleteDepartment(Request $request)
    {
        $department = Department::find($request->id);

        if ($department) {
            $department->m13_status = $department->m13_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $department->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $department->m13_status
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Department not found.'], 404);
    }

    public function viewTests()
    {
        $tests = Test::with('group', 'sample', 'user', 'department')->get();
        return view('test.tests', compact('tests'));
    }

    // public function createTest(Request $request)
    // {
    //     if ($request->isMethod('POST')) {

    //         dd($request);
    //         $validator = Validator::make($request->all(), [
    //             "txt_sample_id"      => "required|integer|exists:m10_samples,m10_sample_id",
    //             "txt_group_id"       => "required|integer|exists:m11_groups,m11_group_id",
    //             "txt_department_id"  => "required|integer|exists:m13_departments,m13_department_id",
    //             "txt_name"           => "required|string|max:255",
    //             "txt_description"    => "nullable|string|max:500",
    //             "txt_alias"          => "nullable|string|max:255",
    //             "txt_weight"         => "nullable|numeric|min:0",
    //             "txt_unit"           => "nullable|string|max:100",
    //             "txt_charge"         => "required|numeric|min:0",
    //             "txt_instrument"     => "nullable|string|max:255",
    //             "txt_remark"         => "nullable|string|max:500",
    //         ], [
    //             'txt_sample_id.required'     => 'Please select a sample.',
    //             'txt_sample_id.exists'       => 'The selected sample does not exist.',
    //             'txt_group_id.required'      => 'Please select a group.',
    //             'txt_group_id.exists'        => 'The selected group does not exist.',
    //             'txt_department_id.required' => 'Please select a department.',
    //             'txt_department_id.exists'   => 'The selected department does not exist.',
    //             'txt_name.required'          => 'Test name is required.',
    //             'txt_name.max'               => 'Test name should not exceed 255 characters.',
    //             'txt_description.max'        => 'Description should not exceed 500 characters.',
    //             'txt_charge.required'        => 'Charge is required.',
    //             'txt_charge.numeric'         => 'Charge must be a number.',
    //             'txt_weight.numeric'         => 'Weight must be a number.',
    //             'txt_remark.max'             => 'Remark should not exceed 500 characters.',
    //         ]);
    //         if ($validator->fails()) {
    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }
    //         try {
    //             Test::create([
    //                 'm10_sample_id'      => $request->txt_sample_id,
    //                 'm11_group_id'       => $request->txt_group_id,
    //                 'm13_department_id'  => $request->txt_department_id,
    //                 'm12_name'           => $request->txt_name,
    //                 'm12_description'    => $request->txt_description,
    //                 'm12_alias'          => $request->txt_alias,
    //                 'm12_weight'         => $request->txt_weight,
    //                 'm12_unit'           => $request->txt_unit,
    //                 'm12_charge'         => $request->txt_charge,
    //                 'm12_instrument'     => $request->txt_instrument,
    //                 'm12_remark'         => $request->txt_remark,
    //                 'tr01_created_by'    => Session::get('user_id') ?? -1,
    //             ]);
    //             Session::flash('type', 'success');
    //             Session::flash('message', 'Test created successfully.');
    //             return to_route('view_tests');
    //         } catch (\Exception $e) {
    //             Session::flash('type', 'error');
    //             Session::flash('message', 'An error occurred while creating the test.');
    //             return redirect()->back()->withInput();
    //         }
    //     }
    //     $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
    //     $departments = Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);
    //     return view('test.create_test', compact('samples', 'departments'));
    // }



    public function createTest(Request $request)
    {
        if ($request->isMethod('POST')) {

            $rules = [
                "txt_sample_id"             => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_group_id"              => "required|integer|exists:m11_groups,m11_group_id",
                "txt_department_id"         => "required|integer|exists:m13_departments,m13_department_id",
                "txt_name"                  => "required|string|max:255",
                "txt_category_id"           => "required|string|max:255",
                "txt_input_mode"            => "required|string|max:255",
                "txt_stages"                => "nullable|integer|min:1",
                "txt_output_matrix"         => "required|array",
                "txt_output_matrix.*.name"  => "required|string|max:255",
                "txt_output_matrix.*.value" => "required|numeric|min:0",
                "txt_charge"                => "required|numeric|min:0",
                "txt_description"           => "nullable|string|max:500",
                "txt_alias"                 => "nullable|string|max:255",
                "txt_weight"                => "nullable|numeric|min:0",
                "txt_unit"                  => "nullable|string|max:100",
                "txt_instrument"            => "nullable|string|max:255",
                "txt_remark"                => "nullable|string|max:500",
            ];
            // Additional validation for MULTI STAGE
            if ($request->txt_input_mode === 'MULTI STAGE') {
                $rules['txt_stages'] = 'required|integer|min:1|max:50';
                $rules['stages'] = 'required|array|min:1';
                $rules['stages.*.name'] = 'required|string|max:255';
                $rules['stages.*.inputs'] = 'required|string';
                $rules['stages.*.outputs'] = 'nullable|string';
            }
            $messages = [
                'txt_sample_id.required'              => 'Please select a sample.',
                'txt_sample_id.exists'                => 'The selected sample does not exist.',
                'txt_group_id.required'               => 'Please select a group.',
                'txt_group_id.exists'                 => 'The selected group does not exist.',
                'txt_department_id.required'          => 'Please select a department.',
                'txt_department_id.exists'            => 'The selected department does not exist.',
                'txt_name.required'                   => 'Test name is required.',
                'txt_name.max'                        => 'Test name should not exceed 255 characters.',
                'txt_category_id.required'            => 'Category is required.',
                'txt_input_mode.required'             => 'Input mode is required.',
                'txt_stages.integer'                  => 'Stages must be a number.',
                'txt_stages.min'                      => 'Stages must be at least 1.',
                'txt_output_matrix.required'          => 'Output matrix is required.',
                'txt_output_matrix.array'             => 'Output matrix must be an array.',
                'txt_output_matrix.*.name.required'   => 'Each output stage must have a name.',
                'txt_output_matrix.*.name.string'     => 'Each stage name must be a string.',
                'txt_output_matrix.*.value.required'  => 'Each output stage must have a value.',
                'txt_output_matrix.*.value.numeric'   => 'Each output value must be a number.',
                'txt_output_matrix.*.value.min'       => 'Output values must be 0 or more.',
                'txt_charge.required'                 => 'Charge is required.',
                'txt_charge.numeric'                  => 'Charge must be a number.',
                'txt_description.max'                 => 'Description should not exceed 500 characters.',
                'txt_alias.max'                       => 'Alias should not exceed 255 characters.',
                'txt_weight.numeric'                  => 'Weight must be a number.',
                'txt_unit.max'                        => 'Unit should not exceed 100 characters.',
                'txt_instrument.max'                  => 'Instrument should not exceed 255 characters.',
                'txt_remark.max'                      => 'Remark should not exceed 500 characters.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            try {
                // Create the main test record
                $test = Test::create([
                    'm10_sample_id' => $request->txt_sample_id,
                    'm11_group_id' => $request->txt_group_id,
                    'm13_department_id' => $request->txt_department_id,
                    'm12_name' => $request->txt_name,
                    'm12_category' => $request->txt_category_id,
                    'm12_input_mode' => $request->txt_input_mode,
                    'm12_stages' => $request->txt_input_mode === 'MULTI STAGE' ? $request->txt_stages : null,
                    'm12_output_metrics' => json_encode($request->txt_output_matrix),
                    'm12_charge' => $request->txt_charge,
                    'm12_description' => $request->txt_description,
                    'm12_alias' => $request->txt_alias,
                    'm12_weight' => $request->txt_weight,
                    'm12_unit' => $request->txt_unit,
                    'm12_instrument' => $request->txt_instrument,
                    'm12_remark' => $request->txt_remark,
                    'tr01_created_by' => Session::get('user_id') ?? -1,
                ]);

                // If MULTI STAGE, create stage records
                if ($request->txt_input_mode === 'MULTI STAGE' && $request->stages) {
                    foreach ($request->stages as $index => $stageData) {
                        // Prepare inputs data
                        $inputs = [];
                        if (isset($stageData['inputs']) && is_array($stageData['inputs'])) {
                            $inputs = array_filter($stageData['inputs'], function ($input) {
                                return !empty($input['name']) && !empty($input['type']);
                            });
                        }

                        // Prepare outputs data
                        $outputs = [];
                        if (isset($stageData['outputs']) && is_array($stageData['outputs'])) {
                            $outputs = array_filter($stageData['outputs'], function ($output) {
                                return !empty($output['name']);
                            });
                        }

                        // Create stage record
                        Stage::create([
                            'm12_test_id' => $test->m12_test_id,
                            'm18_name' => $stageData['name'],
                            'm18_stage_number' => $index + 1,
                            'm18_inputs' => $stageData['inputs'],
                            'm18_outputs' => $stageData['outputs'] ?? '',
                            'tr01_created_by' => Session::get('user_id') ?? -1,
                        ]);
                    }
                }
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Test created successfully.');
                return to_route('view_tests');
            } catch (\Exception $e) {
                DB::rollback();
                Session::flash('type', 'error');
                Session::flash('message', 'Error creating test: ' . $e->getMessage());
                return redirect()->back();
            }
        }

        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        $departments = Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);

        return view('test.create_test', compact('samples', 'departments'));
    }

    // public function updateTest(Request $request, $id)
    // {
    //     $test = Test::where('m12_test_id', $id)->first();
    //     if ($request->isMethod('POST')) {
    //         $validator = Validator::make($request->all(), [
    //             "txt_edit_sample_id"      => "required|integer|exists:m10_samples,m10_sample_id",
    //             "txt_edit_group_id"       => "required|integer|exists:m11_groups,m11_group_id",
    //             "txt_edit_department_id"  => "required|integer|exists:m13_departments,m13_department_id",
    //             "txt_edit_name"           => "required|string|max:255",
    //             "txt_edit_description"    => "nullable|string|max:500",
    //             "txt_edit_alias"          => "nullable|string|max:255",
    //             "txt_edit_weight"         => "nullable|numeric|min:0",
    //             "txt_edit_unit"           => "nullable|string|max:100",
    //             "txt_edit_charge"         => "required|numeric|min:0",
    //             "txt_edit_instrument"     => "nullable|string|max:255",
    //             "txt_edit_remark"         => "nullable|string|max:500",
    //         ], [
    //             'txt_edit_sample_id.required'     => 'Please select a sample.',
    //             'txt_edit_sample_id.exists'       => 'The selected sample does not exist.',
    //             'txt_edit_group_id.required'      => 'Please select a group.',
    //             'txt_edit_group_id.exists'        => 'The selected group does not exist.',
    //             'txt_edit_department_id.required' => 'Please select a department.',
    //             'txt_edit_department_id.exists'   => 'The selected department does not exist.',
    //             'txt_edit_name.required'          => 'Test name is required.',
    //             'txt_edit_name.max'               => 'Test name should not exceed 255 characters.',
    //             'txt_edit_description.max'        => 'Description should not exceed 500 characters.',
    //             'txt_edit_charge.required'        => 'Charge is required.',
    //             'txt_edit_charge.numeric'         => 'Charge must be a number.',
    //             'txt_edit_weight.numeric'         => 'Weight must be a number.',
    //             'txt_edit_remark.max'             => 'Remark should not exceed 500 characters.',
    //         ]);
    //         if ($validator->fails()) {
    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }
    //         $data = [
    //             'm10_sample_id'      => $request->txt_edit_sample_id,
    //             'm11_group_id'       => $request->txt_edit_group_id,
    //             'm13_department_id'  => $request->txt_edit_department_id,
    //             'm12_name'           => $request->txt_edit_name,
    //             'm12_description'    => $request->txt_edit_description,
    //             'm12_alias'          => $request->txt_edit_alias,
    //             'm12_weight'         => $request->txt_edit_weight,
    //             'm12_unit'           => $request->txt_edit_unit,
    //             'm12_charge'         => $request->txt_edit_charge,
    //             'm12_instrument'     => $request->txt_edit_instrument,
    //             'm12_remark'         => $request->txt_edit_remark,
    //             'tr01_created_by'    => Session::get('user_id') ?? -1,
    //         ];
    //         $test->update($data);
    //         Session::flash('type', 'success');
    //         Session::flash('message', 'Test Updated Successfully!');
    //         return to_route('view_tests');
    //     }
    //     $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
    //     $departments =  Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);
    //     return view('test.edit_test', compact('test', 'samples', 'departments'));
    // }


    // public function updateTest(Request $request, $id)
    // {
    //     $test = Test::findOrFail($id);

    //     if ($request->isMethod('post')) {
    //         $validator = Validator::make($request->all(), [
    //             "txt_edit_sample_id"                    => "required|integer|exists:m10_samples,m10_sample_id",
    //             "txt_edit_group_id"                     => "required|integer|exists:m11_groups,m11_group_id",
    //             "txt_edit_department_id"                => "required|integer|exists:m13_departments,m13_department_id",
    //             "txt_edit_name"                         => "required|string|max:255",
    //             "txt_edit_category_id"                  => "required|string|in:NUMERIC,QUALITATIVE SINGLE,QUALITATIVE MULTI",
    //             "txt_edit_input_mode"                   => "required|string|in:SINGLE,MULTI,MULTI STAGE",
    //             "txt_edit_stages"                       => "required_if:txt_edit_input_mode,MULTI STAGE|nullable|integer|min:1",
    //             "txt_edit_output_matrix"                => "required|array|min:1",
    //             "txt_edit_output_matrix.*.name"         => "required|string|max:255",
    //             "txt_edit_output_matrix.*.value"        => "required|string|max:255",
    //             "txt_edit_charge"                       => "required|numeric|min:0",
    //             "txt_edit_description"                  => "nullable|string|max:500",
    //             "txt_edit_alias"                        => "nullable|string|max:255",
    //             "txt_edit_weight"                       => "nullable|numeric|min:0",
    //             "txt_edit_unit"                         => "nullable|string|max:100",
    //             "txt_edit_instrument"                   => "nullable|string|max:255",
    //             "txt_edit_remark"                       => "nullable|string|max:500",
    //         ], [
    //             'txt_edit_sample_id.required'           => 'Please select a sample.',
    //             'txt_edit_group_id.required'            => 'Please select a group.',
    //             'txt_edit_department_id.required'       => 'Please select a department.',
    //             'txt_edit_name.required'                => 'Test name is required.',
    //             'txt_edit_category_id.required'         => 'Category is required.',
    //             'txt_edit_input_mode.required'          => 'Input mode is required.',
    //             'txt_edit_stages.required_if'           => 'Stages field is required for Multi Stage input mode.',
    //             'txt_edit_output_matrix.required'       => 'Output matrix is required.',
    //             'txt_edit_output_matrix.min'            => 'At least one output row is required.',
    //             'txt_edit_output_matrix.*.name.required'  => 'Each output must have a name.',
    //             'txt_edit_output_matrix.*.value.required' => 'Each output must have a value.',
    //             'txt_edit_charge.required'              => 'Charge is required.',
    //         ]);

    //         if ($validator->fails()) {
    //             return redirect()->back()->withErrors($validator)->withInput();
    //         }

    //         $data = [
    //             'm10_sample_id'     => $request->txt_edit_sample_id,
    //             'm11_group_id'      => $request->txt_edit_group_id,
    //             'm13_department_id' => $request->txt_edit_department_id,
    //             'm12_name'          => $request->txt_edit_name,
    //             'm12_category'      => $request->txt_edit_category_id,
    //             'm12_input_mode'    => $request->txt_edit_input_mode,
    //             'm12_stages'        => $request->txt_edit_input_mode === 'MULTI STAGE' ? $request->txt_edit_stages : null,
    //             'm12_output_metrics' => json_encode($request->txt_edit_output_matrix),
    //             'm12_charge'        => $request->txt_edit_charge,
    //             'm12_description'   => $request->txt_edit_description,
    //             'm12_alias'         => $request->txt_edit_alias,
    //             'm12_weight'        => $request->txt_edit_weight,
    //             'm12_unit'          => $request->txt_edit_unit,
    //             'm12_instrument'    => $request->txt_edit_instrument,
    //             'm12_remark'        => $request->txt_edit_remark,
    //         ];

    //         $test->update($data);

    //         Session::flash('type', 'success');
    //         Session::flash('message', 'Test Updated Successfully!');
    //         return to_route('view_tests');
    //     }

    //     $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
    //     $departments = Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);

    //     return view('test.edit_test', compact('test', 'samples', 'departments'));
    // }

    public function updateTest(Request $request, $id)
    {
        $test = Test::with('stages')->findOrFail($id);
        if ($request->isMethod('post')) {
            $rules = [
                "txt_sample_id"       => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_group_id"        => "required|integer|exists:m11_groups,m11_group_id",
                "txt_department_id"   => "required|integer|exists:m13_departments,m13_department_id",
                "txt_name"            => "required|string|max:255",
                "txt_category_id"     => "required|string|max:255",
                "txt_input_mode"      => "required|string|max:255",
                "txt_stages"          => "nullable|integer|min:1",
                "txt_output_matrix"   => "required|array",
                "txt_output_matrix.*.name" => "required|string|max:255",
                "txt_output_matrix.*.value" => "required|string|max:255",
                "txt_charge"          => "required|numeric|min:0",
                "txt_description"     => "nullable|string|max:500",
                "txt_alias"           => "nullable|string|max:255",
                "txt_weight"          => "nullable|numeric|min:0",
                "txt_unit"            => "nullable|string|max:100",
                "txt_instrument"      => "nullable|string|max:255",
                "txt_remark"          => "nullable|string|max:500",
            ];

            if ($request->txt_input_mode === 'MULTI STAGE') {
                $rules['txt_stages'] = 'required|integer|min:1|max:50';
                $rules['stages'] = 'required|array|min:1';
                $rules['stages.*.name'] = 'required|string|max:255';
                $rules['stages.*.inputs'] = 'required|string';
                $rules['stages.*.outputs'] = 'nullable|string';
            }
            $message = [
                'txt_edit_sample_id.required'           => 'Please select a sample.',
                'txt_edit_group_id.required'            => 'Please select a group.',
                'txt_edit_department_id.required'       => 'Please select a department.',
                'txt_edit_name.required'                => 'Test name is required.',
                'txt_edit_category_id.required'         => 'Category is required.',
                'txt_edit_input_mode.required'          => 'Input mode is required.',
                'txt_edit_stages.required_if'           => 'Stages field is required for Multi Stage input mode.',
                'txt_edit_output_matrix.required'       => 'Output matrix is required.',
                'txt_edit_output_matrix.min'            => 'At least one output row is required.',
                'txt_edit_output_matrix.*.name.required'  => 'Each output must have a name.',
                'txt_edit_output_matrix.*.value.required' => 'Each output must have a value.',
                'txt_edit_charge.required'              => 'Charge is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            try {
                // Update the main test record
                $test->update([
                    'm10_sample_id' => $request->txt_sample_id,
                    'm11_group_id' => $request->txt_group_id,
                    'm13_department_id' => $request->txt_department_id,
                    'm12_name' => $request->txt_name,
                    'm12_category' => $request->txt_category_id,
                    'm12_input_mode' => $request->txt_input_mode,
                    'm12_stages' => $request->txt_input_mode === 'MULTI STAGE' ? $request->txt_stages : null,
                    'm12_output_metrics' => json_encode($request->txt_output_matrix),
                    'm12_charge' => $request->txt_charge,
                    'm12_description' => $request->txt_description,
                    'm12_alias' => $request->txt_alias,
                    'm12_weight' => $request->txt_weight,
                    'm12_unit' => $request->txt_unit,
                    'm12_instrument' => $request->txt_instrument,
                    'm12_remark' => $request->txt_remark,
                    'tr01_updated_by' => Session::get('user_id') ?? -1,
                ]);

                // Delete old stages and create new ones if mode is MULTI STAGE
                Stage::where('m12_test_id', $test->m12_test_id)->delete();

                if ($request->txt_input_mode === 'MULTI STAGE' && $request->stages) {
                    foreach ($request->stages as $index => $stageData) {
                        Stage::create([
                            'm12_test_id' => $test->m12_test_id,
                            'm18_name' => $stageData['name'],
                            'm18_stage_number' => $index + 1,
                            'm18_inputs' => $stageData['inputs'],
                            'm18_outputs' => $stageData['outputs'] ?? '',
                            'tr01_created_by' => Session::get('user_id') ?? -1,
                        ]);
                    }
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Test updated successfully.');
                return to_route('view_tests');
            } catch (\Exception $e) {
                DB::rollback();
                Session::flash('type', 'error');
                Session::flash('message', 'Error updating test: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }
        $existingStages = $test->stages->map(function ($stage) {
            return [
                'name' => $stage->m18_name,
                'inputs' => $stage->m18_inputs,
                'outputs' => $stage->m18_outputs,
            ];
        })->toArray();
        // Handle the GET request to show the edit form
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        $departments = Department::where('m13_status', 'ACTIVE')->get(['m13_department_id', 'm13_name']);

        return view('test.edit_test', compact('test', 'samples', 'departments', 'existingStages'));
    }

    public function deleteTest(Request $request)
    {
        $test = Test::find($request->id);
        if ($test) {
            $test->m12_status = $test->m12_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $test->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $test->m12_status
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'Test not found.'], 404);
    }

    public function getGroups(Request $request)
    {
        $sampleId = $request->sample_id;
        $groups = Group::where('m10_sample_id', $sampleId)
            ->where('m11_status', 'ACTIVE')
            ->orderBy('m11_name')
            ->get(['m11_group_id', 'm11_name']);
        return response()->json($groups);
    }

    public function viewLabSamples()
    {
        $labSamples = LabSample::with('ro', 'user', 'sample')->get();
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        return view('master.lab_samples', compact('labSamples', 'samples'));
    }

    public function createLabSamples(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_sample_id"   => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_name"        => "required|string|max:255",
                "txt_order_by"    => "required|numeric",
                "txt_sample_no"   => "required|numeric",
                "txt_remark"      => "nullable|string|max:500",
            ], [
                'txt_sample_id.required'  => 'Please select a sample.',
                'txt_sample_id.integer'   => 'Invalid sample format.',
                'txt_sample_id.exists'    => 'Selected sample does not exist.',
                'txt_name.required'       => 'Lab sample name is required.',
                'txt_name.max'            => 'Lab sample name should not exceed 255 characters.',
                'txt_order_by.required'   => 'Order By is required.',
                'txt_order_by.numeric'    => 'Order By must be a number.',
                'txt_sample_no.required'  => 'Sample No is required.',
                'txt_sample_no.numeric'   => 'Sample No must be a number.',
                'txt_remark.max'          => 'Remark should not exceed 500 characters.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            LabSample::create([
                'm04_ro_id' => Session::get('ro_id') ?? -1,
                'm10_sample_id' => $request->txt_sample_id,
                'm14_name' => $request->txt_name,
                'm14_order_by' => $request->txt_order_by,
                'm14_sample_no' => $request->txt_sample_no,
                'm14_remark' => $request->txt_remark,
                'tr01_created_by' => Session::get('user_id') ?? -1,
                'm14_status' => 'ACTIVE',
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Lab Sample created successfully.');
            return to_route('view_lab_samples');
        }
        return redirect()->back();
    }

    public function updateLabSamples(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_edit_id"          => "required|exists:m14_lab_samples,m14_lab_sample_id",
                "txt_edit_sample_id"   => "required|exists:m10_samples,m10_sample_id",
                "txt_edit_name"        => "required|string|max:255",
                "txt_edit_sample_no"   => "required|string",
                "txt_edit_order_by"    => "required|numeric",
                "txt_edit_remark"      => "nullable|string|max:500",
            ], [
                'txt_edit_id.required'         => 'Sample ID is missing.',
                'txt_edit_id.exists'           => 'Selected lab sample does not exist.',
                'txt_edit_sample_id.required'  => 'Please select a sample.',
                'txt_edit_sample_id.exists'    => 'Selected sample is invalid.',
                'txt_edit_name.required'       => 'Lab sample name is required.',
                'txt_edit_name.max'            => 'Lab sample name should not exceed 255 characters.',
                'txt_edit_sample_no.required'  => 'Sample No is required.',
                'txt_edit_sample_no.numeric'   => 'Sample No must be a number.',
                'txt_edit_order_by.required'   => 'Order By is required.',
                'txt_edit_order_by.numeric'    => 'Order By must be a number.',
                'txt_edit_remark.max'          => 'Remark must not exceed 500 characters.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $labSample = LabSample::find($request->txt_edit_id);
            if (!$labSample) {
                Session::flash('type', 'error');
                Session::flash('message', 'Lab Sample not found.');
                return redirect()->back();
            }
            $labSample->m10_sample_id  = $request->txt_edit_sample_id;
            $labSample->m14_name       = $request->txt_edit_name;
            $labSample->m14_sample_no  = $request->txt_edit_sample_no;
            $labSample->m14_order_by   = $request->txt_edit_order_by;
            $labSample->m14_remark     = $request->txt_edit_remark;
            $labSample->save();
            Session::flash('type', 'success');
            Session::flash('message', 'Lab Sample updated successfully.');
            return redirect()->back();
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Bad request');
        return redirect()->back();
    }

    public function deleteLabSamples(Request $request)
    {
        $labSample = LabSample::find($request->id);
        if ($labSample) {
            $labSample->m14_status = $labSample->m14_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $labSample->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $labSample->m14_status
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'Lab Sample not found.'], 404);
    }

    public function getTests(Request $request)
    {
        $groupId = $request->group_id;

        $tests = Test::where('m11_group_id', $groupId)
            ->where('m12_status', 'ACTIVE')
            ->orderBy('m12_name')
            ->get(['m12_test_id', 'm12_name']);

        return response()->json($tests);
    }

    public function viewStandards()
    {
        $standards = Standard::with('sample', 'group', 'test', 'user')->get();
        // $sample = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        return view('test.standard.standards', compact('standards'));
    }

    public function createStandard(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_sample_id" => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_group_id" => "required|integer|exists:m11_groups,m11_group_id",
                "txt_test_id" => "required|integer|exists:m12_tests,m12_test_id",
                "txt_method" => "required|string|max:255",
                "txt_description" => "nullable|string",
                "txt_unit" => "nullable|string",
                "txt_detection_limit" => "nullable|string",
                "txt_requirement" => "nullable|string",
                "txt_remark" => "nullable|string|max:500",
            ], [
                "txt_sample_id.required" => "Sample selection is required.",
                "txt_sample_id.exists" => "Selected sample does not exist.",
                "txt_group_id.required" => "Group selection is required.",
                "txt_group_id.exists" => "Selected group does not exist.",
                "txt_test_id.required" => "Test selection is required.",
                "txt_test_id.exists" => "Selected test does not exist.",
                "txt_method.required" => "Method field is required.",
                "txt_method.max" => "Method must not exceed 255 characters.",
                "txt_remark.max" => "Remark must not exceed 500 characters.",
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            Standard::create([
                'm10_sample_id' => $request->txt_sample_id,
                'm11_group_id' => $request->txt_group_id,
                'm12_test_id' => $request->txt_test_id,
                'm15_method' => $request->txt_method,
                'm15_description' => $request->txt_description,
                'm15_unit' => $request->txt_unit,
                'm15_detection_limit' => $request->txt_detection_limit,
                'm15_requirement' => $request->txt_requirement,
                'm15_remark' => $request->txt_remark,
                'tr01_created_by' => Session::get('user_id') ?? -1,
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Standard created successfully!');
            return redirect()->route('view_standards');
        }
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        return view('test.standard.create_standard', compact('samples'));
    }

    public function updateStandard(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_edit_id" => "required|exists:m15_standards,m15_standard_id",
                "txt_edit_sample_id" => "required|exists:m10_samples,m10_sample_id",
                "txt_edit_group_id" => "required|exists:m11_groups,m11_group_id",
                "txt_edit_test_id" => "required|exists:m12_tests,m12_test_id",
                "txt_edit_method" => "required|string|max:255",
                "txt_edit_description" => "nullable|string",
                "txt_edit_unit" => "nullable|string",
                "txt_edit_detection_limit" => "nullable|string",
                "txt_edit_requirement" => "nullable|string",
                "txt_edit_remark" => "required|string|max:500",
            ], [
                "txt_edit_sample_id.required" => "Sample selection is required.",
                "txt_edit_group_id.required" => "Group selection is required.",
                "txt_edit_test_id.required" => "Test selection is required.",
                "txt_edit_method.required" => "Method is required.",
                "txt_edit_remark.required" => "Remark is required.",
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $standard = Standard::findOrFail($request->txt_edit_id);
            $standard->update([
                'm10_sample_id' => $request->txt_edit_sample_id,
                'm11_group_id' => $request->txt_edit_group_id,
                'm12_test_id' => $request->txt_edit_test_id,
                'm15_method' => $request->txt_edit_method,
                'm15_description' => $request->txt_edit_description,
                'm15_unit' => $request->txt_edit_unit,
                'm15_detection_limit' => $request->txt_edit_detection_limit,
                'm15_requirement' => $request->txt_edit_requirement,
                'm15_remark' => $request->txt_edit_remark,
            ]);
            Session::flash('type', 'success');
            Session::flash('type', 'Standard updated successfully.');
            return to_route('view_standards');
        }
        $standard = Standard::findOrFail($id);
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);

        return view('test.standard.edit_standard', compact('standard', 'samples'));
    }

    public function deleteStandard(Request $request)
    {
        $standard = Standard::find($request->id);
        if ($standard) {
            $standard->m15_status = $standard->m15_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $standard->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $standard->m15_status
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'Standard not found.'], 404);
    }


    public function viewPrimaryTests()
    {
        $primaryTests = PrimaryTest::with(['sample', 'group', 'test', 'user'])->get();
        return view('test.primary.primary_tests', compact('primaryTests'));
    }
    public function createPrimaryTest(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_sample_id'   => 'required|exists:m10_samples,m10_sample_id',
                'txt_group_id'    => 'required|exists:m11_groups,m11_group_id',
                'txt_test_id'     => 'required|exists:m12_tests,m12_test_id',
                'txt_name'        => 'required|string|max:255',
                'txt_unit'        => 'nullable|string|max:255',
                'txt_requirement' => 'nullable|string|max:255',
                'txt_remark'      => 'nullable|string|max:255',
            ], [
                'txt_sample_id.required'   => 'Please select a sample.',
                'txt_sample_id.exists'     => 'Selected sample is invalid.',

                'txt_group_id.required'    => 'Please select a group.',
                'txt_group_id.exists'      => 'Selected group is invalid.',

                'txt_test_id.required'     => 'Please select a test.',
                'txt_test_id.exists'       => 'Selected test is invalid.',

                'txt_name.required'        => 'Parameter name is required.',
                'txt_name.string'          => 'Parameter must be a string.',
                'txt_name.max'             => 'Parameter may not be greater than 255 characters.',

                'txt_unit.string'          => 'Unit must be a valid string.',
                'txt_unit.max'             => 'Unit may not be greater than 255 characters.',

                'txt_requirement.string'   => 'Requirement must be a valid string.',
                'txt_requirement.max'      => 'Requirement may not be greater than 255 characters.',

                'txt_remark.string'        => 'Remark must be a valid string.',
                'txt_remark.max'           => 'Remark may not be greater than 255 characters.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            PrimaryTest::create([
                'm10_sample_id'   => $request->txt_sample_id,
                'm11_group_id'    => $request->txt_group_id,
                'm12_test_id'     => $request->txt_test_id,
                'm16_name'        => $request->txt_name,
                'm16_unit'        => $request->txt_unit,
                'm16_requirement' => $request->txt_requirement,
                'm16_remark'      => $request->txt_remark,
                'tr01_created_by'      => Session::get('user_id'),
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Primary Test created successfully.');
            return to_route('view_primary_tests');
        }

        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        return view('test.primary.create_primary_test', compact('samples'));
    }

    public function updatePrimaryTest(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_edit_id" => "required|exists:m16_primary_tests,m16_primary_test_id",
                "txt_edit_sample_id" => "required|exists:m10_samples,m10_sample_id",
                "txt_edit_group_id" => "required|exists:m11_groups,m11_group_id",
                "txt_edit_test_id" => "required|exists:m12_tests,m12_test_id",
                "txt_edit_name" => "required|string|max:255",
                "txt_edit_unit" => "nullable|string|max:255",
                "txt_edit_requirement" => "nullable|string|max:255",
                "txt_edit_remark" => "nullable|string|max:255",
            ], [
                "txt_edit_sample_id.required" => "Sample selection is required.",
                "txt_edit_group_id.required" => "Group selection is required.",
                "txt_edit_test_id.required" => "Test selection is required.",
                "txt_edit_name.required" => "Parameter name is required.",
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $primaryTest = PrimaryTest::findOrFail($request->txt_edit_id);
            $primaryTest->update([
                'm10_sample_id' => $request->txt_edit_sample_id,
                'm11_group_id' => $request->txt_edit_group_id,
                'm12_test_id' => $request->txt_edit_test_id,
                'm16_name' => $request->txt_edit_name,
                'm16_unit' => $request->txt_edit_unit,
                'm16_requirement' => $request->txt_edit_requirement,
                'm16_remark' => $request->txt_edit_remark,
            ]);

            Session::flash('type', 'success');
            Session::flash('message', 'Primary Test updated successfully.');
            return to_route('view_primary_tests');
        }

        $primaryTest = PrimaryTest::findOrFail($id);
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);

        return view('test.primary.edit_primary_test', compact('primaryTest', 'samples'));
    }

    public function deletePrimaryTest(Request $request)
    {
        $primaryTest = PrimaryTest::find($request->id);
        if ($primaryTest) {
            $primaryTest->m16_status = $primaryTest->m16_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $primaryTest->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $primaryTest->m16_status
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Primary Test not found.'], 404);
    }

    public function getPrimaryTests(Request $request)
    {
        $testId = $request->test_id;

        $primaryTests = PrimaryTest::where('m12_test_id', $testId)
            ->where('m16_status', 'ACTIVE')
            ->orderBy('m16_name')
            ->get(['m16_primary_test_id', 'm16_name']);

        return response()->json($primaryTests);
    }
    public function viewSecondaryTests()
    {
        $secondaryTests = SecondaryTest::with('sample', 'group', 'test', 'primaryTest', 'user')->get();
        return view('test.secondary.secondary_tests', compact('secondaryTests'));
    }

    public function createSecondaryTest(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_sample_id'       => 'required|exists:m10_samples,m10_sample_id',
                'txt_group_id'        => 'required|exists:m11_groups,m11_group_id',
                'txt_test_id'         => 'required|exists:m12_tests,m12_test_id',
                'txt_primary_test_id' => 'required|exists:m16_primary_tests,m16_primary_test_id',
                'txt_name'            => 'required|string|max:255',
                'txt_unit'            => 'nullable|string|max:50',
            ], [
                'txt_sample_id.required'       => 'Please select a sample.',
                'txt_sample_id.exists'         => 'The selected sample is invalid.',

                'txt_group_id.required'        => 'Please select a group.',
                'txt_group_id.exists'          => 'The selected group is invalid.',

                'txt_test_id.required'         => 'Please select a test.',
                'txt_test_id.exists'           => 'The selected test is invalid.',

                'txt_primary_test_id.required' => 'Please select a primary test.',
                'txt_primary_test_id.exists'   => 'The selected primary test is invalid.',

                'txt_name.required'            => 'Please enter the parameter name.',
                'txt_name.max'                 => 'The parameter name must not exceed 255 characters.',

                'txt_unit.max'                 => 'The unit must not exceed 50 characters.',
            ]);
            $data = [
                'm10_sample_id' => $request->txt_sample_id,
                'm11_group_id' => $request->txt_group_id,
                'm12_test_id' => $request->txt_test_id,
                'm16_primary_test_id' => $request->txt_primary_test_id,
                'm17_name' => $request->txt_name,
                'm17_unit' => $request->txt_unit,
                'tr01_created_by' => Session::get('user_id'),
            ];
            $create = SecondaryTest::create($data);
            if ($create) {
                Session::flash('type', 'success');
                Session::flash('message', 'Secondary Test Created Successfully!');
                return to_route('view_secondary_tests');
            }
            Session::flash('type', 'success');
            Session::flash('message', 'Something Wenst Wrong!');
            return redirect()->back();
        }
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        return view('test.secondary.create_secondary_test', compact('samples'));
    }

    public function updateSecondaryTest(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_edit_sample_id'       => 'required|exists:m10_samples,m10_sample_id',
                'txt_edit_group_id'        => 'required|exists:m11_groups,m11_group_id',
                'txt_edit_test_id'         => 'required|exists:m12_tests,m12_test_id',
                'txt_edit_primary_test_id' => 'required|exists:m16_primary_tests,m16_primary_test_id',
                'txt_edit_name'            => 'required|string|max:255',
                'txt_edit_unit'            => 'nullable|string|max:50',
            ], [
                'txt_edit_sample_id.required'       => 'Please select a sample.',
                'txt_edit_sample_id.exists'         => 'The selected sample is invalid.',

                'txt_edit_group_id.required'        => 'Please select a group.',
                'txt_edit_group_id.exists'          => 'The selected group is invalid.',

                'txt_edit_test_id.required'         => 'Please select a test.',
                'txt_edit_test_id.exists'           => 'The selected test is invalid.',

                'txt_edit_primary_test_id.required' => 'Please select a primary test.',
                'txt_edit_primary_test_id.exists'   => 'The selected primary test is invalid.',

                'txt_edit_name.required'            => 'Please enter the parameter name.',
                'txt_edit_name.max'                 => 'The parameter name must not exceed 255 characters.',

                'txt_edit_unit.max'                 => 'The unit must not exceed 50 characters.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = [
                'm10_sample_id'        => $request->txt_edit_sample_id,
                'm11_group_id'         => $request->txt_edit_group_id,
                'm12_test_id'          => $request->txt_edit_test_id,
                'm16_primary_test_id'  => $request->txt_edit_primary_test_id,
                'm17_name'             => $request->txt_edit_name,
                'm17_unit'             => $request->txt_edit_unit,
                'tr01_created_by'      => Session::get('user_id'),
            ];
            $secondaryTest = SecondaryTest::findOrFail($id);
            if ($secondaryTest) {
                $secondaryTest->update($data);
                Session::flash('type', 'success');
                Session::flash('message', 'Secondary Test Updated Successfully!');
            } else {
                Session::flash('type', 'error');
                Session::flash('message', 'Secondary Test not found!');
            }
            return to_route('view_secondary_tests');
        }
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        $editData = SecondaryTest::findOrFail($id);
        return view('test.secondary.edit_secondary_test', compact('samples', 'editData'));
    }

    public function deleteSecondaryTest(Request $request)
    {
        $secondaryTest = SecondaryTest::find($request->id);
        if ($secondaryTest) {
            $secondaryTest->m17_status = $secondaryTest->m17_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $secondaryTest->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $secondaryTest->m17_status
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Primary Test not found.'], 404);
    }
}
