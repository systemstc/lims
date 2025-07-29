<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Role;
use App\Models\State;
use Illuminate\Http\Request;
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
}
