<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'txt_email'     => 'required|exists:m00_admins,m00_email',
                'txt_password' => 'required'
            ], [
                'txt_email.required'     => 'Email is required.',
                'txt_email.exists'       => 'Email not found in records.',
                'txt_password.required' => 'Password is required.',
            ]);
            // $pass =  Hash::make($request->txt_password);
            // dd($request);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $admin = Admin::where('m00_email', $request->txt_email)->first();
            if ($admin && Hash::check($request->txt_password, $admin->m00_password)) {
                session([
                    'admin_id'    => $admin->m00_admin_id,
                    'name'  => $admin->m00_name,
                    'email' => $admin->m00_email,
                    'role' => 'ADMIN',
                ]);
                Session::flash('type', 'success');
                Session::flash('message', 'Logged In Successfully !');
                return to_route('dashboard');
            } else {
                return redirect()->back()
                    ->withErrors(['txt_password' => 'Incorrect password.'])
                    ->withInput();
            }
        } elseif (session()->has('admin_id')) {
            return redirect()->route('dashboard');
        } else {
            return view('auth.login_admin');
        }
    }
}
