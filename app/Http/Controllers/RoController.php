<?php

namespace App\Http\Controllers;

use App\Models\Ro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RoController extends Controller
{
    public function ros()
    {
        $ros = Ro::all();
        return view('ro.ros', compact('ros'));
    }

    public function createRo(Request $request)
    {
        // dd($request);
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_name'      => 'required',
                'txt_email'     => 'required|email|unique:m04_ros,m04_email',
                'txt_phone'     => 'required|digits:10|unique:m04_ros,m04_phone',
            ], [
                'txt_name.required'         => 'Name is required.',
                'txt_email.required'        => 'Email is required.',
                'txt_email.email'           => 'Please enter a valid email address.',
                'txt_email.unique'          => 'This email is already registered.',
                'txt_phone.required'        => 'Phone number is required.',
                'txt_phone.digits'          => 'Phone number must be 10 digits.',
                'txt_phone.unique'          => 'This phone number is already registered.',
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
                    'tr01_type'     => 'RO'
                ]);
                Ro::create([
                    'tr01_user_id' => $user->tr01_user_id,
                    'm04_name'     => $request->txt_name,
                    'm04_email'    => $request->txt_email,
                    'm04_phone'    => $request->txt_phone,
                    'm03_role_id'  => 1,
                ]);
                DB::commit();
                Session::flash('type', 'success');
                Session::flash('message', 'Ro Created Successfully!');
                return to_route('view_ros');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Employee creation failed', ['error' => $e->getMessage()]);
                Session::flash('type', 'error');
                Session::flash('message', 'Failed to create RO. Please try again.');
                return redirect()->back();
            }
        }
    }

    public function  updateRo(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_edit_name'      => 'required',
                'txt_edit_email' => 'required|email|unique:m04_ros,m04_email,' . $request->txt_edit_id . ',m04_ro_id',
                'txt_edit_phone'     => 'required|digits:10|unique:m04_ros,m04_phone,' . $request->txt_edit_id . ',m04_ro_id',
            ], [
                'txt_edit_name.required'         => 'Name is required.',
                'txt_edit_email.required'        => 'Email is required.',
                'txt_edit_email.email'           => 'Please enter a valid email address.',
                'txt_edit_email.unique'          => 'This email is already registered.',
                'txt_edit_phone.required'        => 'Phone number is required.',
                'txt_edit_phone.digits'          => 'Phone number must be 10 digits.',
                'txt_edit_phone.unique'          => 'This phone number is already registered.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = [
                'm04_name' => $request->txt_edit_name,
                'm04_email' => $request->txt_edit_email,
                'm04_phone' => $request->txt_edit_phone,
            ];

            $ro = Ro::findOrFail($request->txt_edit_id);
            $ro->update($data);
            if ($ro) {
                Session::flash('type', 'success');
                Session::flash('message', 'Ro Updated Successfully!');
                return to_route('view_ros');
            }
            Session::flash('type', 'error');
            Session::flash('message', 'Something wents Wrong. Please try again!');
            return to_route('view_ros');
        }
    }
    public function changeRoStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:m04_ros,m04_ro_id',
        ]);

        $ro = Ro::findOrFail($request->id);
        $ro->m04_status = $ro->m04_status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $ro->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status has been updated to ' . $ro->m04_status
        ]);
    }
}
