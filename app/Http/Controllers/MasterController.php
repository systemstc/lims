<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

    public function viewDistricts()
    {
        $districts = District::with('state')->get();
        $states = State::get();
        return view('master.districts', compact('districts', 'states'));
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
}
