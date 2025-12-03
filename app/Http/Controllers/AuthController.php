<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Ro;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
            // dd($pass);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $admin = Admin::where('m00_email', $request->txt_email)->first();
            if ($admin && Hash::check($request->txt_password, $admin->m00_password)) {
                // dd($admin);
                session([
                    'admin_id'    => $admin->m00_admin_id,
                    'name'  => $admin->m00_name,
                    'email' => $admin->m00_email,
                    'role_id' => -1,
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
    public function userLogin(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('auth.login_user');
        }

        $validator = Validator::make($request->all(), [
            'txt_email' => 'required|exists:tr01_users,tr01_email',
            'txt_password' => 'required'
        ], [
            'txt_email.required' => 'Email is required.',
            'txt_email.exists' => 'Email not found in records.',
            'txt_password.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::where('tr01_email', $request->txt_email)->first();

        if (!$user || !Hash::check($request->txt_password, $user->tr01_password)) {
            return redirect()->back()
                ->withErrors(['txt_password' => 'Incorrect password.'])
                ->withInput();
        }

        // Set session data based on user type
        $this->setUserSession($user);

        Session::flash('type', 'success');
        Session::flash('message', 'Logged In Successfully!');

        return $this->redirectToDashboard($user);
    }

    /**
     * Set session data based on user type
     */
    private function setUserSession(User $user): void
    {
        if ($user->tr01_type == 'EMPLOYEE') {
            $employee = Employee::with('role')->where('tr01_user_id', $user->tr01_user_id)->first();
            $sessionData = [
                'user_id' => $employee->m06_employee_id,
                'name' => $employee->m06_name,
                'email' => $employee->m06_email,
                'role_id' => $employee->m03_role_id,
                'role' => $employee->role->m03_name,
                'ro_id' => $employee->m04_ro_id
            ];
        } else {
            $ro = Ro::with('role')->where('tr01_user_id', $user->tr01_user_id)->first();
            $sessionData = [
                'user_id' => $ro->m04_ro_id,
                'name' => $ro->m04_name,
                'email' => $ro->m04_email,
                'role_id' => $ro->m03_role_id,
                'role' => $ro->role->m03_name,
                'ro_id' => $ro->m04_ro_id
            ];
        }

        session($sessionData);
    }

    /**
     * Redirect user to appropriate dashboard based on role
     */
    private function redirectToDashboard(User $user): RedirectResponse
    {
        $roleRoutes = [
            'Manager' => 'dashboard',
            'DEO' => 'view_completed_camples',
            'Analyst' => 'view_analyst_dashboard',
            'Verification Officer' => 'view_result_verification',
            'Registrar' => 'register_sample'
        ];

        $userType = $user->tr01_type == 'EMPLOYEE' ? 'EMPLOYEE' : 'RO';
        $role = session('role');

        return isset($roleRoutes[$role])
            ? to_route($roleRoutes[$role])
            : to_route('dashboard');
    }
}
