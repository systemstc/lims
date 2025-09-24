<?php

namespace App\Http\Controllers;

use App\Models\Accreditation;
use App\Models\Department;
use App\Models\District;
use App\Models\Group;
use App\Models\LabSample;
use App\Models\Menu;
use App\Models\Package;
use App\Models\PackageTest;
use App\Models\PrimaryTest;
use App\Models\Role;
use App\Models\Sample;
use App\Models\SecondaryTest;
use App\Models\Standard;
use App\Models\State;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:m02_districts,m02_district_id',
        ], [
            'id.required' => 'District ID is required.',
            'id.exists'   => 'The selected district does not exist.',
        ]);
        if ($validator->fails()) {
            Session::flash('type', 'error');
            Session::flash('message', $validator->errors());
        }
        return toggleStatus(
            'm02_districts',
            'm02_district_id',
            'm02_status',
            $request->id
        );
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
        return toggleStatus(
            'm03_roles',
            'm03_role_id',
            'm03_status',
            $request->id
        );
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
        return toggleStatus('m10_samples', 'm10_sample_id', 'm10_status', $request->id);
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
        return toggleStatus('m11_groups', 'm11_group_id', 'm11_status', $request->id);
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
                return redirect()->back()->withErrors($validator)->withInput();
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
        return toggleStatus('m13_departments', 'm13_department_id', 'm13_status', $request->id);
    }

    public function viewTests()
    {
        $tests = Test::with('group', 'sample', 'user', 'department')->get();
        return view('test.tests', compact('tests'));
    }

    // Importing Data from CSV
public function importTests(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');
    $handle = fopen($file, "r");

    if ($handle === false) {
        return back()->with('error', 'Unable to open file.');
    }

    $insertData = [];
    $rowCount = 0;

    DB::beginTransaction();

    try {
        // Read first row (could be header or first data row)
        $firstRow = fgetcsv($handle, 1000, ",");

        if ($firstRow && str_contains(strtolower(implode(',', $firstRow)), 'test_id')) {
            // This is a header row → do nothing, just move on
        } else {
            // First row contains data → process it manually
            if ($firstRow && count($firstRow) >= 12) {
                $insertData[] = [
                    'm12_test_number'    => trim($firstRow[1]),
                    'm10_sample_id'      => trim($firstRow[2]),
                    'm11_group_id'       => trim($firstRow[3]),
                    'm12_name'           => trim($firstRow[4]),
                    'm12_description'    => trim($firstRow[5]),
                    'm12_unit'           => trim($firstRow[6]),
                    'm12_charge'         => trim($firstRow[7]),
                    'm12_instrument'     => trim($firstRow[8]),
                    'm15_standard_id'    => !empty($firstRow[9]) ? trim($firstRow[9]) : null,
                    'm16_primary_test_id'=> !empty($firstRow[10]) ? trim($firstRow[10]) : null,
                    'm17_secondary_test_id'=> !empty($firstRow[11]) ? trim($firstRow[11]) : null,
                    'm12_category'       => null,
                    'm12_input_mode'     => null,
                    'm12_stages'         => null,
                    'm14_lab_sample_id'  => null,
                    'm12_result'         => null,
                    'm12_alias'          => null,
                    'm12_weight'         => null,
                    'm13_department_id'  => null,
                    'm12_remark'         => null,
                    'm12_status'         => 1,
                    'tr01_created_by'    => Session::get('user_id') ?? -1,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
                $rowCount++;
            }
        }

        // Process the remaining rows
        while (($row = fgetcsv($handle, 10000, ",")) !== false) {
            if (count($row) < 12) {
                continue; // skip invalid/incomplete rows
            }

            $insertData[] = [
                'm12_test_number'    => trim($row[1]),
                'm10_sample_id'      => trim($row[2]),
                'm11_group_id'       => trim($row[3]),
                'm12_name'           => trim($row[4]),
                'm12_description'    => trim($row[5]),
                'm12_unit'           => trim($row[6]),
                'm12_charge'         => trim($row[7]),
                'm12_instrument'     => trim($row[8]),
                'm15_standard_id'    => !empty($row[9]) ? trim($row[9]) : null,
                'm16_primary_test_id'=> !empty($row[10]) ? trim($row[10]) : null,
                'm17_secondary_test_id'=> !empty($row[11]) ? trim($row[11]) : null,
                'm12_category'       => null,
                'm12_input_mode'     => null,
                'm12_stages'         => null,
                'm14_lab_sample_id'  => null,
                'm12_result'         => null,
                'm12_alias'          => null,
                'm12_weight'         => null,
                'm13_department_id'  => null,
                'm12_remark'         => null,
                'm12_status'         => 1,
                'tr01_created_by'    => Session::get('user_id') ?? -1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            $rowCount++;

            // Insert in chunks to avoid memory issues
            if (count($insertData) >= 500) {
                Test::insert($insertData);
                $insertData = [];
            }
        }

        fclose($handle);

        // Insert any remaining data
        if (!empty($insertData)) {
            Test::insert($insertData);
        }

        DB::commit();

        return back()->with('success', "$rowCount rows imported successfully.");
    } catch (\Exception $e) {
        DB::rollBack();
        fclose($handle);
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}


public function importStandards(Request $request)
{
    $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
    $file = $request->file('csv_file');
    $handle = fopen($file, "r");
    if (!$handle) return back()->with('error', 'Unable to open file.');

    $rowCount = 0;
    DB::beginTransaction();

    try {
        // Read first row
        $firstRow = fgetcsv($handle, 1000, ",");
        if ($firstRow && stripos(implode(',', $firstRow), 'c_id') === false && count($firstRow) >= 4) {
            $firstRow[0] = preg_replace('/^\x{FEFF}/u', '', $firstRow[0]); // remove BOM
            DB::table('m15_standards')->insert([
                'm15_standard_id' => trim($firstRow[0]),
                'm11_group_id' => trim($firstRow[3]),
                'm15_method' => trim($firstRow[2]),
                'tr01_created_by' => Session::get('user_id') ?? -1,
                'm15_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $rowCount++;
        }

        // Process remaining rows
        while (($row = fgetcsv($handle, 10000, ",")) !== false) {
            if (count($row) < 4) continue;
            $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]); // remove BOM
            DB::table('m15_standards')->insert([
                'm15_standard_id' => trim($row[0]),
                'm11_group_id' => trim($row[3]),
                'm15_method' => trim($row[2]),
                'tr01_created_by' => Session::get('user_id') ?? -1,
                'm15_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $rowCount++;
        }

        fclose($handle);
        DB::commit();

        return back()->with('success', "$rowCount standards imported successfully with same IDs.");

    } catch (\Exception $e) {
        DB::rollBack();
        fclose($handle);
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}


public function importPrimaryTests(Request $request)
{
    $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
    $file = $request->file('csv_file');
    $handle = fopen($file, "r");
    if (!$handle) return back()->with('error', 'Unable to open file.');

    $rowCount = 0;
    DB::beginTransaction();

    try {
        // Read first row
        $firstRow = fgetcsv($handle, 1000, ",");
        if ($firstRow && stripos(implode(',', $firstRow), 'c_id') === false && count($firstRow) >= 5) {
            $firstRow[0] = preg_replace('/^\x{FEFF}/u', '', $firstRow[0]); // remove BOM
            DB::table('m16_primary_tests')->insert([
                'm16_primary_test_id' => trim($firstRow[0]),
                'm11_group_id' => trim($firstRow[1]),
                'm16_name'     => trim($firstRow[3]),
                'm16_unit'     => trim($firstRow[4]),
                'tr01_created_by' => Session::get('user_id') ?? -1,
                'm16_status'   => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            $rowCount++;
        }

        // Process remaining rows
        while (($row = fgetcsv($handle, 10000, ",")) !== false) {
            if (count($row) < 5) continue;
            $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]); // remove BOM
            DB::table('m16_primary_tests')->insert([
                'm16_primary_test_id' => trim($row[0]),
                'm11_group_id' => trim($row[1]),
                'm16_name'     => trim($row[3]),
                'm16_unit'     => trim($row[4]),
                'tr01_created_by' => Session::get('user_id') ?? -1,
                'm16_status'   => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            $rowCount++;
        }

        fclose($handle);
        DB::commit();

        return back()->with('success', "$rowCount primary tests imported successfully with same IDs.");

    } catch (\Exception $e) {
        DB::rollBack();
        fclose($handle);
                        Log::error('Failed to create Standard/Accreditation', [
                    'error' => $e->getMessage(),
                ]);
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}


public function importSecondaryTests(Request $request)
{
    $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
    $file = $request->file('csv_file');
    $handle = fopen($file, "r");
    if (!$handle) return back()->with('error', 'Unable to open file.');

    $rowCount = 0;
    DB::beginTransaction();

    try {
        // Read first row
        $firstRow = fgetcsv($handle, 1000, ",");
        if ($firstRow && stripos(implode(',', $firstRow), 'c1') === false && count($firstRow) >= 6) {
            $firstRow[0] = preg_replace('/^\x{FEFF}/u', '', $firstRow[0]); // remove BOM
            DB::table('m17_secondary_tests')->insert([
                'm10_sample_id'         => 1,
                'm17_secondary_test_id' => trim($firstRow[0]),
                'm11_group_id'          => trim($firstRow[1]),
                'm16_primary_test_id'   => trim($firstRow[3]),
                'm17_name'              => trim($firstRow[4]),
                'm17_unit'              => trim($firstRow[5]),
                'tr01_created_by'       => Session::get('user_id') ?? -1,
                'm17_status'            => 1,
            ]);
            $rowCount++;
        }

        // Process remaining rows
        while (($row = fgetcsv($handle, 10000, ",")) !== false) {
            if (count($row) < 6) continue;
            $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]); // remove BOM
            DB::table('m17_secondary_tests')->insert([
                'm10_sample_id'         => 1,
                'm17_secondary_test_id' => trim($row[0]),
                'm11_group_id'          => trim($row[1]),
                'm16_primary_test_id'   => trim($row[3]),
                'm17_name'              => trim($row[4]),
                'm17_unit'              => trim($row[5]),
                'tr01_created_by'       => Session::get('user_id') ?? -1,
                'm17_status'            => 1,
            ]);
            $rowCount++;
        }

        fclose($handle);
        DB::commit();

        return back()->with('success', "$rowCount secondary tests imported successfully with same IDs.");

    } catch (\Exception $e) {
        DB::rollBack();
        fclose($handle);
                  Log::error('Failed to create Standard/Accreditation', [
                    'error' => $e->getMessage(),
                ]);
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}



    public function createTest(Request $request)
    {
        if ($request->isMethod('POST')) {
            // dd($request);
            $rules = [
                "txt_sample_id" => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_group_id" => "required|integer|exists:m11_groups,m11_group_id",
                "txt_name" => "required|string|max:255|unique:m12_tests,m12_name",
                "txt_category_id" => "required|string|max:255",
                "txt_input_mode" => "required|string|max:255",
                "txt_stages" => "nullable|integer|min:1",
                "txt_charge" => "required|numeric|min:0",
                "txt_description" => "nullable|string|max:500",
                "txt_alias" => "nullable|string|max:255",
                "txt_weight" => "nullable|numeric|min:0",
                "txt_unit" => "nullable|string|max:100",
                "txt_instrument" => "nullable|string|max:255",
                "txt_remark" => "nullable|string|max:500",
                "standard_ids" => "required|array|min:1",
                "standard_ids.*" => "required|integer|exists:m15_standards,m15_standard_id",
                "primary_test_ids" => "required|array|min:1",
                "primary_test_ids.*" => "required|integer|exists:m16_primary_tests,m16_primary_test_id",
                "secondary_test_ids" => "nullable|array",
                "secondary_test_ids.*" => "nullable|integer|exists:m17_secondary_tests,m17_secondary_test_id",
                "secondary_test_primary_ids" => "nullable|array",
                "secondary_test_primary_ids.*" => "nullable|integer|exists:m16_primary_tests,m16_primary_test_id",
                "lab_sample_ids" => "required|array",
                "lab_sample_ids.*" => "required|integer|exists:m14_lab_samples,m14_lab_sample_id",
                "results" => "required|string|min:1",
            ];

            // Additional validation for MULTI STAGE
            if ($request->txt_input_mode === 'MULTI STAGE') {
                $rules['txt_stages'] = 'required|integer|min:1|max:50';
            }

            // Custom validation for secondary tests
            if ($request->has('secondary_test_ids') && $request->has('secondary_test_primary_ids')) {
                $secondaryIds = $request->secondary_test_ids;
                $primaryIds = $request->secondary_test_primary_ids;

                if (count($secondaryIds) !== count($primaryIds)) {
                    return redirect()->back()
                        ->withErrors(['secondary_test_ids' => 'Secondary tests and their primary test associations must match.'])
                        ->withInput();
                }
            }

            $messages = [
                'txt_sample_id.required' => 'Please select a sample.',
                'txt_sample_id.exists' => 'The selected sample does not exist.',
                'txt_group_id.required' => 'Please select a group.',
                'txt_group_id.exists' => 'The selected group does not exist.',
                'txt_name.required' => 'Test name is required.',
                'txt_name.unique' => 'Test already exists.',
                'txt_name.max' => 'Test name should not exceed 255 characters.',
                'txt_category_id.required' => 'Category is required.',
                'txt_input_mode.required' => 'Input mode is required.',
                'txt_stages.required' => 'Number of stages is required for multi-stage tests.',
                'txt_stages.integer' => 'Stages must be a number.',
                'txt_stages.min' => 'Stages must be at least 1.',
                'txt_stages.max' => 'Stages cannot exceed 50.',
                'txt_charge.required' => 'Charge is required.',
                'txt_charge.numeric' => 'Charge must be a number.',
                'txt_charge.min' => 'Charge must be 0 or more.',
                'standard_ids.required' => 'At least one standard is required.',
                'standard_ids.array' => 'Standards must be provided as an array.',
                'standard_ids.min' => 'At least one standard is required.',
                'standard_ids.*.exists' => 'One or more selected standards do not exist.',
                'primary_test_ids.required' => 'At least one primary test is required.',
                'primary_test_ids.array' => 'Primary tests must be provided as an array.',
                'primary_test_ids.min' => 'At least one primary test is required.',
                'primary_test_ids.*.exists' => 'One or more selected primary tests do not exist.',
                'secondary_test_ids.*.exists' => 'One or more selected secondary tests do not exist.',
                'lab_sample_ids.required' => 'Lab samples are required.',
                'lab_sample_ids.array' => 'Lab samples must be provided as an array.',
                'lab_sample_ids.*.exists' => 'One or more selected lab samples do not exist.',
                'results.required' => 'At least one result is required.',
                'results.min' => 'At least one result is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                // Preserve form data for repopulation
                $input = $request->all();

                // Add additional data for proper form repopulation
                if ($request->has('standard_data')) {
                    $input['standard_data'] = collect($request->standard_data)
                        ->map(fn($item) => json_decode($item, true))
                        ->toArray();
                }

                if ($request->has('primary_test_data')) {
                    $input['primary_test_data'] = collect($request->primary_test_data)
                        ->map(fn($item) => json_decode($item, true))
                        ->toArray();
                }

                if ($request->has('secondary_test_data')) {
                    $input['secondary_test_data'] = collect($request->secondary_test_data)
                        ->map(fn($item) => json_decode($item, true))
                        ->toArray();
                }

                return redirect()->back()->withErrors($validator)->withInput($input);
            }

            DB::beginTransaction();

            try {
                // Create the main test record
                $test = Test::create([
                    'm10_sample_id' => $request->txt_sample_id,
                    'm11_group_id' => $request->txt_group_id,
                    'm12_name' => $request->txt_name,
                    'm12_category' => $request->txt_category_id,
                    'm12_input_mode' => $request->txt_input_mode,
                    'm12_stages' => $request->txt_input_mode === 'MULTI STAGE' ? $request->txt_stages : null,
                    'm15_standard_id' => implode(',', $request->standard_ids ?? []),
                    'm16_primary_test_id' => implode(',', $request->primary_test_ids ?? []),
                    'm17_secondary_test_id' => implode(',', $request->secondary_test_ids ?? []),
                    'm14_lab_sample_id' => implode(',', $request->lab_sample_ids ?? []),
                    'm12_result' => $request->results,
                    'm12_charge' => $request->txt_charge,
                    'm12_description' => $request->txt_description,
                    'm12_alias' => $request->txt_alias,
                    'm12_weight' => $request->txt_weight,
                    'm12_unit' => $request->txt_unit,
                    'm12_instrument' => $request->txt_instrument,
                    'm12_remark' => $request->txt_remark,
                    'tr01_created_by' => Session::get('user_id') ?? -1,
                ]);

                // Store secondary test associations if present
                if ($request->has('secondary_test_ids') && $request->has('secondary_test_primary_ids')) {
                    $secondaryIds = $request->secondary_test_ids;
                    $primaryIds = $request->secondary_test_primary_ids;

                    for ($i = 0; $i < count($secondaryIds); $i++) {
                        if (isset($primaryIds[$i])) {
                            $associations = [];
                            for ($j = 0; $j < count($secondaryIds); $j++) {
                                if (isset($primaryIds[$j])) {
                                    $associations[] = [
                                        'secondary_test_id' => $secondaryIds[$j],
                                        'primary_test_id' => $primaryIds[$j]
                                    ];
                                }
                            }
                            $test->update([
                                'm17_secondary_test_associations' => json_encode($associations)
                            ]);
                            break;
                        }
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
                return redirect()->back()->withInput();
            }
        }

        // GET request - show form
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        $labSamples = LabSample::where('m14_status', 'ACTIVE')->get(['m14_lab_sample_id', 'm14_name']);

        return view('test.create_test', compact('samples', 'labSamples'));
    }

    // Search methods
    public function searchStandards(Request $request)
    {
        $query = $request->get('query', '');
        $selectedGroupId = $request->get('selectedGroupId');
        $currentRoId = Session::get('ro_id');
        if (strlen($query) < 1 || empty($selectedGroupId)) {
            return response()->json([]);
        }

        $standards = Standard::where('m15_status', 'ACTIVE')
            ->where('m11_group_id', $selectedGroupId)
            ->where('m15_method', 'LIKE', "%{$query}%")
            ->select('m15_standard_id as id', 'm15_method as name')
            ->limit(10)
            ->get()
            ->map(function ($standard) use ($currentRoId) {
                $accreditation = Accreditation::where('m15_standard_id', $standard->id)
                    ->where('m04_ro_id', $currentRoId)
                    ->orderByDesc('m21_accreditation_date')
                    ->first();

                $standard->accredited = $accreditation ? $accreditation->m21_is_accredited : 'NO';
                return $standard;
            });
        return response()->json($standards);
    }

    public function searchPrimaryTests(Request $request)
    {
        $query = $request->get('query', '');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $primaryTests = PrimaryTest::where('m16_status', 'ACTIVE')
            ->where('m11_group_id', $request->selectedGroupId)
            ->where('m16_name', 'LIKE', "%{$query}%")
            ->select('m16_primary_test_id as id', 'm16_name as name')
            ->limit(10)
            ->get();
        return response()->json($primaryTests);
    }

    public function searchSecondaryTests(Request $request)
    {
        $query = $request->get('query', '');
        $primaryTestId = $request->get('primary_test_id');

        if (strlen($query) < 1 || !$primaryTestId) {
            return response()->json([]);
        }

        // Search secondary tests that belong to or can be associated with the primary test
        $secondaryTests = SecondaryTest::where('m17_status', 'ACTIVE')
            ->where('m17_name', 'LIKE', "%{$query}%")
            ->where(function ($q) use ($primaryTestId) {
                $q->where('m16_primary_test_id', $primaryTestId)
                    ->orWhereNull('m16_primary_test_id');
            })
            ->select('m17_secondary_test_id as id', 'm17_name as name', 'm16_primary_test_id as primary_test_id')
            ->limit(10)
            ->get();

        return response()->json($secondaryTests);
    }

    public function createAjaxStandard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:m15_standards,m15_method',
            'accredited'  => 'required|in:YES,NO',
            'description' => 'nullable|string|max:500',
            'accExp'      => 'nullable|date',
            'sampleId'    => 'required|integer|exists:m10_samples,m10_sample_id',
            'groupId'     => 'required|integer|exists:m11_groups,m11_group_id',
        ], [
            'name.required'       => 'Standard Name is required.',
            'name.unique'         => 'This standard already exists.',
            'accredited.required' => 'Accreditation selection is required.',
            'accredited.in'       => 'Please select a valid Accreditation option.',
            'accExp.date'         => 'Please select a valid date.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        DB::beginTransaction();

        try {
            $standard = Standard::create([
                'm15_method'      => $request->name,
                'm15_description' => $request->description,
                'm10_sample_id'   => $request->sampleId,
                'm11_group_id'    => $request->groupId,
                'tr01_created_by' => Session::get('user_id') ?? -1,
            ]);

            $accreditation = null;
            if ($request->accredited === 'YES') {
                $accreditation = Accreditation::create([
                    'm15_standard_id'        => $standard->m15_standard_id,
                    'm04_ro_id'              => Session::get('ro_id'),
                    'm21_is_accredited'      => $request->accredited,
                    'm21_accreditation_date' => now()->format('Y-m-d'),
                    'm21_valid_till'         => $request->accExp,
                    'm06_created_by'         => Session::get('user_id'),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Standard & Accreditation created successfully!',
                'data' => [
                    'id'         => $standard->m15_standard_id,
                    'name'       => $standard->m15_method,
                    'accredited' => $accreditation ? $accreditation->m21_is_accredited : 'NO',
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create record: ' . $th->getMessage(),
            ], 500);
        }
    }



    public function createAjaxPrimaryTest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:m16_primary_tests,m16_name',
                'sampleId' => 'required|integer|exists:m10_samples,m10_sample_id',
                'groupId' => 'required|integer|exists:m11_groups,m11_group_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $primaryTest = PrimaryTest::create([
                'm16_name' => $request->name,
                'm10_sample_id' => $request->sampleId,
                'm11_group_id' => $request->groupId,
                'm16_status' => 'ACTIVE',
                'tr01_created_by' => Session::get('user_id') ?? -1,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $primaryTest->m16_primary_test_id,
                    'name' => $primaryTest->m16_name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating primary test: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createAjaxSecondaryTest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'primary_test_id' => 'required|integer|exists:m16_primary_tests,m16_primary_test_id',
                'sampleId' => 'required|integer|exists:m10_samples,m10_sample_id',
                'groupId' => 'required|integer|exists:m11_groups,m11_group_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Check if secondary test with same name exists for this primary test
            $existingSecondaryTest = SecondaryTest::where('m17_name', $request->name)
                ->where('m16_primary_test_id', $request->primary_test_id)
                ->first();

            if ($existingSecondaryTest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Secondary test with this name already exists for the selected primary test.'
                ], 422);
            }

            $secondaryTest = SecondaryTest::create([
                'm17_name' => $request->name,
                'm16_primary_test_id' => $request->primary_test_id,
                'm10_sample_id' => $request->sampleId,
                'm11_group_id' => $request->groupId,
                'm17_status' => 'ACTIVE',
                'tr01_created_by' => Session::get('user_id') ?? -1,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $secondaryTest->m17_secondary_test_id,
                    'name' => $secondaryTest->m17_name,
                    'primary_test_id' => $secondaryTest->m16_primary_test_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating secondary test: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    public function checkTestExists(Request $request)
    {
        $name = $request->get('name', '');

        if (strlen($name) < 2) {
            return response()->json(['exists' => false]);
        }

        $exists = Test::where('m12_name', $name)
            ->where('m12_status', 'ACTIVE')
            ->exists();

        return response()->json(['exists' => $exists]);
    }


    public function updateTest(Request $request, $id)
    {
        $test = Test::with('sample', 'group')->findOrFail($id);
        if ($request->isMethod('POST')) {
            $rules = [
                "txt_sample_id"             => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_group_id"              => "required|integer|exists:m11_groups,m11_group_id",
                "txt_name"                  => "required|string|max:255",
                "txt_category_id"           => "required|string|max:255",
                "txt_input_mode"            => "required|string|max:255",
                "txt_stages"                => "nullable|integer|min:1",
                "txt_charge"                => "required|numeric|min:0",
                "txt_description"           => "nullable|string|max:500",
                "txt_alias"                 => "nullable|string|max:255",
                "txt_weight"                => "nullable|numeric|min:0",
                "txt_unit"                  => "nullable|string|max:100",
                "txt_instrument"            => "nullable|string|max:255",
                "txt_remark"                => "nullable|string|max:500",

                "standard_ids"              => "required|array|min:1",
                "standard_ids.*"            => "required|integer|exists:m15_standards,m15_standard_id",

                "primary_test_ids"          => "required|array|min:1",
                "primary_test_ids.*"        => "required|integer|exists:m16_primary_tests,m16_primary_test_id",

                "secondary_test_ids"        => "nullable|array",
                "secondary_test_ids.*"      => "nullable|integer|exists:m17_secondary_tests,m17_secondary_test_id",

                "secondary_test_primary_ids" => "nullable|array",
                "secondary_test_primary_ids.*" => "nullable|integer|exists:m16_primary_tests,m16_primary_test_id",

                "results"                   => "required|min:1",
                "results.*.name"            => "required|string|max:255",
                "lab_sample_ids" => "required|array",
                "lab_sample_ids.*" => "required|integer|exists:m14_lab_samples,m14_lab_sample_id",
            ];
            if ($request->txt_input_mode === 'MULTI STAGE') {
                $rules['txt_stages'] = 'required|integer|min:1|max:50';
            }

            $rules['secondary_test_ids'] = [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->secondary_test_primary_ids) {
                        if (count($value) !== count($request->secondary_test_primary_ids)) {
                            $fail('Secondary tests and their primary test associations must match.');
                        }
                    }
                },
            ];
            $messages = [
                'secondary_test_primary_ids.array' => 'Secondary test primary IDs must be provided as an array.',
                'secondary_test_primary_ids.*.integer' => 'Secondary test primary ID must be a number.',
                'secondary_test_primary_ids.*.exists' => 'One or more secondary test primary associations do not exist.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $test->update([
                    'm10_sample_id' => $request->txt_sample_id,
                    'm11_group_id' => $request->txt_group_id,
                    'm13_department_id' => $request->txt_department_id,
                    'm12_name' => $request->txt_name,
                    'm12_category' => $request->txt_category_id,
                    'm12_input_mode' => $request->txt_input_mode,
                    'm12_stages' => $request->txt_input_mode === 'MULTI STAGE' ? $request->txt_stages : null,
                    'm15_standard_id' => implode(',', $request->standard_ids ?? []),
                    'm16_primary_test_id' => implode(',', $request->primary_test_ids ?? []),
                    'm17_secondary_test_id' => implode(',', $request->secondary_test_ids ?? []),
                    'm14_lab_sample_id' => implode(',', $request->lab_sample_ids ?? []),
                    'm12_result' => $request->results,
                    'm12_charge' => $request->txt_charge,
                    'm12_description' => $request->txt_description,
                    'm12_alias' => $request->txt_alias,
                    'm12_weight' => $request->txt_weight,
                    'm12_unit' => $request->txt_unit,
                    'm12_instrument' => $request->txt_instrument,
                    'm12_remark' => $request->txt_remark,
                    'tr01_updated_by' => Session::get('user_id') ?? -1,
                ]);

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
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);
        $groups = Group::where('m11_status', 'ACTIVE')->get(['m11_group_code', 'm11_name']);
        $labSamples = LabSample::where('m14_status', 'ACTIVE')->get(['m14_lab_sample_id', 'm14_name']);
        return view('test.edit_test', compact('test', 'samples', 'groups', 'labSamples'));
    }
    public function getStandardsByIds(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
            }
            $standards = Standard::whereIn('m15_standard_id', $ids)
                ->where('m15_status', 'ACTIVE')
                ->get(['m15_standard_id as id', 'm15_method as name']);
            return response()->json($standards);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getPrimaryTestById(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
            }
            $primaryTest = PrimaryTest::whereIn('m16_primary_test_id', $ids)
                ->where('m16_status', 'ACTIVE')
                ->get(['m16_primary_test_id as id', 'm16_name as name']);
            return response()->json($primaryTest);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching primary test'
            ]);
        }
    }

    public function getSecondaryTestById(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
            }
            $secondaryTest = SecondaryTest::whereIn('m17_secondary_test_id', $ids)
                ->where('m17_status', 'ACTIVE')
                ->get(['m17_secondary_test_id as id', 'm17_name as name', 'm16_primary_test_id as primary_test_id']);
            return response()->json($secondaryTest);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching secondary test'
            ]);
        }
    }
    public function deleteTest(Request $request)
    {
        return toggleStatus('m12_tests', 'm12_test_id', 'm12_status', $request->id);
    }

    public function getGroups(Request $request)
    {
        $sampleId = $request->sample_id;
        $groups = Group::where('m10_sample_id', $sampleId)
            ->where('m11_status', 'ACTIVE')
            ->orderBy('m11_name')
            ->get(['m11_group_id', 'm11_group_code', 'm11_name']);
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
        return toggleStatus('m14_lab_samples', 'm14_lab_sample_id', 'm14_status', $request->id);
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
        $standards = Standard::with('sample', 'group', 'user')->get();
        return view('test.standard.standards', compact('standards'));
    }

    public function createStandard(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                "txt_sample_id" => "required|integer|exists:m10_samples,m10_sample_id",
                "txt_group_id" => "required|integer|exists:m11_groups,m11_group_id",
                "txt_method" => "required|string|max:255",
                "txt_description" => "nullable|string",
                "txt_unit" => "nullable|string",
                "txt_detection_limit" => "nullable|string",
                "txt_requirement" => "nullable|string",
                "txt_remark" => "nullable|string|max:500",
                'txt_is_accredated' => 'required|in:YES,NO',
                'txt_acc_exp' => 'nullable|date'
            ], [
                "txt_sample_id.required" => "Sample selection is required.",
                "txt_sample_id.exists" => "Selected sample does not exist.",
                "txt_group_id.required" => "Group selection is required.",
                "txt_group_id.exists" => "Selected group does not exist.",
                "txt_method.required" => "Method field is required.",
                "txt_method.max" => "Method must not exceed 255 characters.",
                "txt_remark.max" => "Remark must not exceed 500 characters.",
                'txt_is_accredated.required' => 'Accreditation is required',
                'txt_is_accredated.in' => 'Please select a valid Accreditation',
                'txt_acc_axp.date' => 'Please Select a proper Date'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();

            try {
                $standard = Standard::create([
                    'm10_sample_id'        => $request->txt_sample_id,
                    'm11_group_id'         => $request->txt_group_id,
                    'm15_method'           => $request->txt_method,
                    'm15_description'      => $request->txt_description,
                    'm15_unit'             => $request->txt_unit,
                    'm15_detection_limit'  => $request->txt_detection_limit,
                    'm15_requirement'      => $request->txt_requirement,
                    'm15_remark'           => $request->txt_remark,
                    'tr01_created_by'      => Session::get('user_id') ?? -1,
                ]);
                if ($request->txt_is_accredated === 'YES') {
                    Accreditation::create([
                        'm15_standard_id'       => $standard->m15_standard_id,
                        'm04_ro_id'             => Session::get('ro_id'),
                        'm21_is_accredited'     => $request->txt_is_accredated,
                        'm21_accreditation_date' => now()->format('Y-m-d'),
                        'm21_valid_till'        => $request->txt_acc_exp,
                        'm06_created_by'        => Session::get('user_id'),
                    ]);
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Standard & Accreditation created successfully!');
                return redirect()->route('view_standards');
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error('Failed to create Standard/Accreditation', [
                    'error' => $th->getMessage(),
                ]);
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create record: ' . $th->getMessage());
                return redirect()->back();
            }
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
                "txt_edit_method" => "required|string|max:255",
                "txt_edit_description" => "nullable|string",
                "txt_edit_unit" => "nullable|string",
                "txt_edit_detection_limit" => "nullable|string",
                "txt_edit_requirement" => "nullable|string",
                "txt_edit_remark" => "required|string|max:500",
            ], [
                "txt_edit_sample_id.required" => "Sample selection is required.",
                "txt_edit_group_id.required" => "Group selection is required.",
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
                'm15_method' => $request->txt_edit_method,
                'm15_description' => $request->txt_edit_description,
                'm15_unit' => $request->txt_edit_unit,
                'm15_detection_limit' => $request->txt_edit_detection_limit,
                'm15_requirement' => $request->txt_edit_requirement,
                'm15_remark' => $request->txt_edit_remark,
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Standard updated successfully.');
            return to_route('view_standards');
        }
        $standard = Standard::findOrFail($id);
        $samples = Sample::where('m10_status', 'ACTIVE')->get(['m10_sample_id', 'm10_name']);

        return view('test.standard.edit_standard', compact('standard', 'samples'));
    }

    public function deleteStandard(Request $request)
    {
        return toggleStatus('m15_standards', 'm15_standard_id', 'm15_status', $request->id);
    }


    public function viewPrimaryTests()
    {
        $primaryTests = PrimaryTest::with(['sample', 'group', 'user'])->get();
        return view('test.primary.primary_tests', compact('primaryTests'));
    }
    public function createPrimaryTest(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_sample_id'   => 'required|exists:m10_samples,m10_sample_id',
                'txt_group_id'    => 'required|exists:m11_groups,m11_group_id',
                'txt_name'        => 'required|string|max:255',
                'txt_unit'        => 'nullable|string|max:255',
                'txt_requirement' => 'nullable|string|max:255',
                'txt_remark'      => 'nullable|string|max:255',
            ], [
                'txt_sample_id.required'   => 'Please select a sample.',
                'txt_sample_id.exists'     => 'Selected sample is invalid.',

                'txt_group_id.required'    => 'Please select a group.',
                'txt_group_id.exists'      => 'Selected group is invalid.',

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
                "txt_edit_name" => "required|string|max:255",
                "txt_edit_unit" => "nullable|string|max:255",
                "txt_edit_requirement" => "nullable|string|max:255",
                "txt_edit_remark" => "nullable|string|max:255",
            ], [
                "txt_edit_sample_id.required" => "Sample selection is required.",
                "txt_edit_group_id.required" => "Group selection is required.",
                "txt_edit_name.required" => "Parameter name is required.",
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $primaryTest = PrimaryTest::findOrFail($request->txt_edit_id);
            $primaryTest->update([
                'm10_sample_id' => $request->txt_edit_sample_id,
                'm11_group_id' => $request->txt_edit_group_id,
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
        return toggleStatus('m16_primary_tests', 'm16_primary_test_id', 'm16_status', $request->id);
    }

    public function getPrimaryTests(Request $request)
    {
        $groupId = $request->group_id;
        $primaryTests = PrimaryTest::where('m11_group_id', $groupId)
            ->where('m16_status', 'ACTIVE')
            ->orderBy('m16_name')
            ->get(['m16_primary_test_id', 'm16_name']);
        return response()->json($primaryTests);
    }
    public function viewSecondaryTests()
    {
        $secondaryTests = SecondaryTest::with('sample', 'group', 'primaryTest', 'user')->get();
        return view('test.secondary.secondary_tests', compact('secondaryTests'));
    }

    public function createSecondaryTest(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_sample_id'       => 'required|exists:m10_samples,m10_sample_id',
                'txt_group_id'        => 'required|exists:m11_groups,m11_group_id',
                'txt_primary_test_id' => 'required|exists:m16_primary_tests,m16_primary_test_id',
                'txt_name'            => 'required|string|max:255',
                'txt_unit'            => 'nullable|string|max:50',
            ], [
                'txt_sample_id.required'       => 'Please select a sample.',
                'txt_sample_id.exists'         => 'The selected sample is invalid.',

                'txt_group_id.required'        => 'Please select a group.',
                'txt_group_id.exists'          => 'The selected group is invalid.',

                'txt_primary_test_id.required' => 'Please select a primary test.',
                'txt_primary_test_id.exists'   => 'The selected primary test is invalid.',

                'txt_name.required'            => 'Please enter the parameter name.',
                'txt_name.max'                 => 'The parameter name must not exceed 255 characters.',

                'txt_unit.max'                 => 'The unit must not exceed 50 characters.',
            ]);
            $data = [
                'm10_sample_id' => $request->txt_sample_id,
                'm11_group_id' => $request->txt_group_id,
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
                'txt_edit_primary_test_id' => 'required|exists:m16_primary_tests,m16_primary_test_id',
                'txt_edit_name'            => 'required|string|max:255',
                'txt_edit_unit'            => 'nullable|string|max:50',
            ], [
                'txt_edit_sample_id.required'       => 'Please select a sample.',
                'txt_edit_sample_id.exists'         => 'The selected sample is invalid.',

                'txt_edit_group_id.required'        => 'Please select a group.',
                'txt_edit_group_id.exists'          => 'The selected group is invalid.',

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
        return toggleStatus('m17_secondary_tests', 'm17_secondary_test_id', 'm17_status', $request->id);
    }

    public function viewPackage()
    {
        $packages = Package::where('m19_type', 'PACKAGE')->with(['packageTests.test', 'packageTests.standard'])->get();
        return view('master.package.packages', compact('packages'));
    }


    public function createPackage(Request $request)
    {
        if ($request->isMethod('POST')) {

            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name',
                'txt_exc_azo_charge' => 'nullable|numeric|min:0',
                'txt_inc_azo_charge' => 'nullable|numeric|min:0',
                'txt_description' => 'nullable|string',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|integer|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|integer|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Package name is required.',
                'txt_name.unique' => 'This package name already exists.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.test_id.exists' => 'Selected test is invalid.',
                'tests.*.standard_id.required' => 'Please select a standard for the chosen test.',
                'tests.*.standard_id.exists' => 'Selected standard is invalid.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            try {
                $package = Package::create([
                    'm19_name' => $request->txt_name,
                    'm19_exc_azo_charge' => $request->txt_exc_azo_charge,
                    'm19_inc_azo_charge' => $request->txt_inc_azo_charge,
                    'tr01_created_by' => Session::get('user_id'),
                ]);
                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Package created successfully!');
                return to_route('view_package');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create package. Error: ' . $e->getMessage());
                return redirect()->back();
            }
        }
        $tests = Test::all();
        return view('master.package.create_package', compact('tests'));
    }


    public function updatePackage(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'txt_name' => 'required|string|max:255',
                'txt_inc_azo_charge' => 'nullable|numeric',
                'txt_exc_azo_charge' => 'nullable|numeric',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Package name is required.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.standard_id.required' => 'Please select a standard.'
            ]);

            DB::beginTransaction();
            try {
                $package = Package::findOrFail($id);
                $package->update([
                    'm19_name' => $request->txt_name,
                    'm19_exc_azo_charge' => $request->txt_exc_azo_charge,
                    'm19_inc_azo_charge' => $request->txt_inc_azo_charge,
                    'm19_description' => $request->m19_description,
                ]);

                PackageTest::where('m19_package_id', $package->m19_package_id)->delete();

                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Package updated successfully!');
                return to_route('view_package');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong. ' . $e->getMessage());
                return back();
            }
        }
        $package = Package::with('packageTests.test')->findOrFail($id);
        $tests = Test::all();
        return view('master.package.edit_package', compact('package', 'tests'));
    }

    public function deletePackage(Request $request)
    {
        return toggleStatus(
            'm19_packages',
            'm19_package_id',
            'm19_status',
            $request->id
        );
    }
    public function viewContract()
    {
        $packages = Package::where('m19_type', 'CONTRACT')->with(['packageTests.test', 'packageTests.standard', 'customer'])->get();
        return view('master.contract.contracts', compact('packages'));
    }


    public function createContract(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name',
                'txt_charges' => 'nullable|numeric|min:0',
                'txt_exp_date' => 'required|date',
                'txt_customer_id' => 'required|exists:m07_customers,m07_customer_id',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|integer|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|integer|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Contract name is required.',
                'txt_name.unique' => 'This contract name already exists.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.test_id.exists' => 'Selected test is invalid.',
                'tests.*.standard_id.required' => 'Please select a standard for the chosen test.',
                'tests.*.standard_id.exists' => 'Selected standard is invalid.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            try {
                $package = Package::create([
                    'm19_name' => $request->txt_name,
                    'm19_charges' => $request->txt_charges,
                    'm19_exp_date' => $request->txt_exp_date,
                    'm07_contract_with' => $request->txt_customer_id,
                    'tr01_created_by' => Session::get('user_id'),
                    'm19_type' => 'CONTRACT',
                ]);
                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Contract created successfully!');
                return to_route('view_contract');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create contract. Error: ' . $e->getMessage());
                return redirect()->back();
            }
        }
        $tests = Test::all();
        return view('master.contract.create_contract', compact('tests'));
    }


    public function updateContract(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name,' . $id . ',m19_package_id',
                'txt_charges' => 'nullable|numeric|min:0',
                'txt_exp_date' => 'required|date',
                'txt_customer_id' => 'required|exists:m07_customers,m07_customer_id',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Package name is required.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.standard_id.required' => 'Please select a standard.'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $package = Package::findOrFail($id);
                $package->update([
                    'm19_name' => $request->txt_name,
                    'm19_charges' => $request->txt_charges,
                    'm19_exp_date' => $request->txt_exp_date,
                    'm07_contract_with' => $request->txt_customer_id,
                ]);

                // Delete old tests and re-insert
                PackageTest::where('m19_package_id', $package->m19_package_id)->delete();

                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Contract updated successfully!');
                return to_route('view_contract');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong. ' . $e->getMessage());
                return back();
            }
        }
        $package = Package::with('packageTests.test', 'customer')->findOrFail($id);
        $tests = Test::all();
        return view('master.contract.edit_contract', compact('package', 'tests'));
    }


    public function viewSpecification()
    {
        $specifications = Package::where('m19_type', 'SPECIFICATION')->with('packageTests.test', 'packageTests.standard')->get(['m19_name', 'm19_charges', 'm19_status', 'm19_package_id',]);
        return view('master.specification.specifications', compact('specifications'));
    }

    public function createSpecification(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name',
                'txt_charges' => 'nullable|numeric|min:0',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|integer|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|integer|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Specification name is required.',
                'txt_name.unique' => 'This specification name already exists.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.test_id.exists' => 'Selected test is invalid.',
                'tests.*.standard_id.required' => 'Please select a standard for the chosen test.',
                'tests.*.standard_id.exists' => 'Selected standard is invalid.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $specification = Package::create([
                    'm19_name' => $request->txt_name,
                    'm19_charges' => $request->txt_charges,
                    'tr01_created_by' => Session::get('user_id'),
                    'm19_type' => 'SPECIFICATION',
                ]);

                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $specification->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Specification created successfully!');
                return to_route('view_specification');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create specification. Error: ' . $e->getMessage());
                return redirect()->back();
            }
        }

        $tests = Test::all();
        return view('master.specification.create_specification', compact('tests'));
    }

    public function updateSpecification(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name,' . $id . ',m19_package_id',
                'txt_charges' => 'nullable|numeric|min:0',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Package name is required.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.standard_id.required' => 'Please select a standard.'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $package = Package::findOrFail($id);
                $package->update([
                    'm19_name' => $request->txt_name,
                    'm19_charges' => $request->txt_charges,
                ]);

                // Delete old tests and re-insert
                PackageTest::where('m19_package_id', $package->m19_package_id)->delete();

                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Specification updated successfully!');
                return to_route('view_specification');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong. ' . $e->getMessage());
                return redirect()->back();
            }
        }
        $specification = Package::with('packageTests.test')->findOrFail($id);
        $tests = Test::all();
        return view('master.specification.edit_specification', compact('specification', 'tests'));
    }

    public function viewCustom()
    {
        $customs = Package::where('m19_type', 'CUSTOM')->with(['packageTests.test', 'packageTests.standard', 'customer'])->get();

        return view('master.custom.customs', compact('customs'));
    }

    public function createCustom(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name',
                'txt_charges' => 'nullable|numeric|min:0',
                'txt_exp_date' => 'required|date',
                'txt_customer_id' => 'required|exists:m07_customers,m07_customer_id',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|integer|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|integer|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Contract name is required.',
                'txt_name.unique' => 'This contract name already exists.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.test_id.exists' => 'Selected test is invalid.',
                'tests.*.standard_id.required' => 'Please select a standard for the chosen test.',
                'tests.*.standard_id.exists' => 'Selected standard is invalid.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            try {
                $package = Package::create([
                    'm19_name' => $request->txt_name,
                    'm19_charges' => $request->txt_charges,
                    'm19_exp_date' => $request->txt_exp_date,
                    'm07_contract_with' => $request->txt_customer_id,
                    'tr01_created_by' => Session::get('user_id'),
                    'm19_type' => 'CUSTOM',
                ]);
                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Custom contract created successfully!');
                return to_route('view_custom');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create custom contract. Error: ' . $e->getMessage());
                return redirect()->back();
            }
        }
        $tests = Test::all();
        return view('master.custom.create_custom', compact('tests'));
    }

    public function updateCustom(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make($request->all(), [
                'txt_name' => 'required|string|max:255|unique:m19_packages,m19_name,' . $id . ',m19_package_id',
                'txt_charges' => 'nullable|numeric|min:0',
                'tests' => 'required|array|min:1',
                'tests.*.test_id' => 'required|exists:m12_tests,m12_test_id',
                'tests.*.standard_id' => 'required|exists:m15_standards,m15_standard_id',
            ], [
                'txt_name.required' => 'Package name is required.',
                'tests.required' => 'At least one test is required.',
                'tests.*.test_id.required' => 'Please select a test.',
                'tests.*.standard_id.required' => 'Please select a standard.'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $package = Package::findOrFail($id);
                $package->update([
                    'm19_name' => $request->txt_name,
                    'm19_charges' => $request->txt_charges,
                ]);

                // Delete old tests and re-insert
                PackageTest::where('m19_package_id', $package->m19_package_id)->delete();

                foreach ($request->tests as $testRow) {
                    PackageTest::create([
                        'm19_package_id' => $package->m19_package_id,
                        'm12_test_id' => $testRow['test_id'],
                        'm15_standard_id' => $testRow['standard_id'],
                    ]);
                }

                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Custom contract updated successfully!');
                return to_route('view_custom');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('type', 'error');
                Session::flash('message', 'Something went wrong. ' . $e->getMessage());
                return redirect()->back();
            }
        }
        $custom = Package::with('packageTests.test')->findOrFail($id);
        $tests = Test::all();
        return view('master.custom.edit_custom', compact('custom', 'tests'));
    }
    public function accreditations()
    {
        $ro = Session::get('ro_id');
        if (!empty($ro)) {
            $accreditations =  Accreditation::where('m04_ro_id', $ro)->with('ro', 'standard', 'employee')->get();
        } else {
            $accreditations = Accreditation::with('ro', 'standard', 'employee')->get();
        }
        $standards = Standard::where('m15_status', 'ACTIVE')->get(['m15_method', 'm15_standard_id']);
        return view('master.accreditations', compact('accreditations', 'standards'));
    }

    public function createAccreditation(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'dd_standard'           => 'required|exists:m15_standards,m15_standard_id',
                'txt_accredited'        => 'required|string|in:Yes,No',
                'txt_acc_date'          => 'required|date',
                'txt_accredited_till'   => 'required|date|after_or_equal:txt_acc_date',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            Accreditation::create([
                'm15_standard_id'        => $request->dd_standard,
                'm04_ro_id'              => Session::get('ro_id') ?? -1,
                'm21_is_accredited'      => $request->txt_accredited,
                'm21_accreditation_date' => $request->txt_acc_date,
                'm21_valid_till'         => $request->txt_accredited_till,
                'm06_created_by'         => Session::get('user_id') ?? -1,
            ]);
            Session::flash('type', 'success');
            Session::flash('message', 'Accreditation created successfully.');
            return redirect()->back();
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Invalid request method.');
    }
    public function viewACM()
    {
        $inputs = ['force' => 50, 'width' => 2];
        $formula = "((force + width)*force)/100";

        foreach ($inputs as $key => $value) {
            $formula = str_replace($key, $value, $formula);
        }

        $result = eval("return $formula;");
        return view('master.acm.view_acm');
    }

    public function searchRole(Request $request)
    {
        $search = $request->get('q');
        $roles = Role::where('m03_name', 'like', "%{$search}%")->where('m03_status', 'ACTIVE')
            ->select('m03_role_id', 'm03_name')
            ->get();

        return response()->json($roles);
    }

    public function getMenusForRole(Request $request)
    {
        $roleId = $request->role_id;
        $menus = Menu::with('children')->get();
        return response()->json([
            'role_id' => $roleId,
            'menus' => $menus
        ]);
    }

    public function updatePermission(Request $request)
    {
        $menu = Menu::findOrFail($request->menu_id);
        $column = "m05_role_" . $request->permission_type;

        $roles = $menu->$column ? explode(',', $menu->$column) : [];

        if ($request->checked == 'true') {
            if (!in_array($request->role_id, $roles)) {
                $roles[] = $request->role_id;
            }
            $type = 'success';
            $message = 'Permission Granted Successfully.';
        } else {
            $roles = array_diff($roles, [$request->role_id]);
            $type = 'warning';
            $message = 'Permission Revoked Successfully.';
        }

        $menu->$column = implode(',', $roles);
        $menu->save();

        return response()->json([
            'success' => true,
            'type' => $type,
            'message' => $message
        ]);
    }
}
