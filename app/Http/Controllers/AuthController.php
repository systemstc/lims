<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\LoginLog;
use App\Models\Ro;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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
            // Log failed attempt
            LoginLog::create([
                'tr01_user_id' => $user ? $user->tr01_user_id : null,
                'tr00_ip_address' => $request->ip(),
                'tr00_user_agent' => $request->userAgent(),
                'tr00_email' => $request->txt_email,
                'tr00_login_at' => now(),
                'tr00_successful' => false,
                'tr00_failure_reason' => $user ? 'Incorrect password' : 'User not found'
            ]);

            return redirect()->back()
                ->withErrors(['txt_password' => 'Incorrect password.'])
                ->withInput();
        }

        // Set session data based on user type
        $this->setUserSession($user);

        // Log successful login
        LoginLog::create([
            'tr01_user_id' => $user->tr01_user_id,
            'tr00_ip_address' => $request->ip(),
            'tr00_user_agent' => $request->userAgent(),
            'tr00_email' => $user->tr01_email,
            'tr00_login_at' => now(),
            'tr00_successful' => true,
        ]);

        Session::flash('type', 'success');
        Session::flash('message', 'Logged In Successfully!');

        return $this->redirectToDashboard($user);
    }

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
                    'role_id' => -1,
                    'role' => 'ADMIN',
                ]);

                // Log successful admin login
                LoginLog::create([
                    'tr01_user_id' => -1,
                    'tr00_ip_address' => $request->ip(),
                    'tr00_user_agent' => $request->userAgent(),
                    'tr00_email' => $admin->m00_email,
                    'tr00_login_at' => now(),
                    'tr00_successful' => true,
                ]);

                Session::flash('type', 'success');
                Session::flash('message', 'Logged In Successfully !');
                return to_route('dashboard');
            } else {
                // Log failed admin attempt
                LoginLog::create([
                    'tr01_user_id' => -1,
                    'tr00_ip_address' => $request->ip(),
                    'tr00_user_agent' => $request->userAgent(),
                    'tr00_email' => $request->txt_email,
                    'tr00_login_at' => now(),
                    'tr00_successful' => false,
                    'tr00_failure_reason' => 'Incorrect password'
                ]);

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

    public function logout(Request $request)
    {
        $email = Session::get('email');
        if ($email) {
            // Find active login log and update logout time
            $lastLog = LoginLog::where('tr00_email', $email)
                ->where('tr00_successful', true)
                ->whereNull('tr00_logout_at')
                ->latest('tr00_login_at')
                ->first();

            if ($lastLog) {
                $lastLog->update(['tr00_logout_at' => now()]);
            }
        }

        Session::flush();
        return to_route('user_login')->with('success', 'Logged out successfully.');
    }

    public function adminLogout(Request $request)
    {
        $email = Session::get('email');
        if ($email) {
            $lastLog = LoginLog::where('tr00_email', $email)
                ->where('tr01_user_id', -1)
                ->where('tr00_successful', true)
                ->whereNull('tr00_logout_at')
                ->latest('tr00_login_at')
                ->first();

            if ($lastLog) {
                $lastLog->update(['tr00_logout_at' => now()]);
            }
        }

        Session::flush();
        return to_route('admin_login')->with('success', 'Logged out successfully.');
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
                'tr01_user_id' => $user->tr01_user_id, // Added this for easier access
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
                'tr01_user_id' => $user->tr01_user_id, // Added this for easier access
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

        // $userType = $user->tr01_type == 'EMPLOYEE' ? 'EMPLOYEE' : 'RO';
        $role = session('role');

        return isset($roleRoutes[$role])
            ? to_route($roleRoutes[$role])
            : to_route('dashboard');
    }
}
