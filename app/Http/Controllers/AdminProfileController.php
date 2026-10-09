<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\LoginLog;
use App\Models\Ro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminProfileController extends Controller
{
    /**
     * Display the Admin Profile page.
     */
    public function show()
    {
        $adminId = Session::get('admin_id');
        $roleId = Session::get('role_id');

        // Verify that the current user is an admin
        if (!$adminId && $roleId != -1) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $admin = $adminId ? Admin::find($adminId) : Admin::first();

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Admin account not found.');
        }

        // Fetch recent admin login activity
        $recentLogins = LoginLog::where(function ($query) use ($admin) {
            $query->where('tr01_user_id', -1)
                  ->orWhere('tr00_email', $admin->m00_email);
        })
        ->orderByDesc('tr00_login_at')
        ->take(6)
        ->get();

        // System summary numbers for context
        $stats = [
            'total_ros' => Ro::count(),
            'total_employees' => Employee::count(),
            'last_login' => $recentLogins->first()?->tr00_login_at,
        ];

        return view('admin.profile', compact('admin', 'recentLogins', 'stats'));
    }

    /**
     * Update admin personal details (name and email).
     */
    public function updateProfile(Request $request)
    {
        $adminId = Session::get('admin_id');
        $admin = $adminId ? Admin::find($adminId) : Admin::first();

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Admin record not found.');
        }

        $request->validate([
            'txt_name' => 'required|string|max:255',
            'txt_email' => 'required|email|max:255|unique:m00_admins,m00_email,' . $admin->m00_admin_id . ',m00_admin_id',
        ], [
            'txt_name.required' => 'Full name is required.',
            'txt_email.required' => 'Email address is required.',
            'txt_email.email' => 'Please enter a valid email address.',
            'txt_email.unique' => 'This email address is already in use by another admin.',
        ]);

        $admin->m00_name = trim($request->txt_name);
        $admin->m00_email = trim($request->txt_email);
        $admin->save();

        // Keep session updated
        Session::put('name', $admin->m00_name);
        Session::put('email', $admin->m00_email);

        Session::flash('type', 'success');
        Session::flash('message', 'Admin profile updated successfully!');

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        $adminId = Session::get('admin_id');
        $admin = $adminId ? Admin::find($adminId) : Admin::first();

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Admin record not found.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 6 characters long.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $admin->m00_password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password does not match our records.'])
                ->withInput();
        }

        // Check if new password is same as current password
        if (Hash::check($request->new_password, $admin->m00_password)) {
            return redirect()->back()
                ->withErrors(['new_password' => 'New password cannot be identical to the current password.'])
                ->withInput();
        }

        $admin->m00_password = Hash::make($request->new_password);
        $admin->save();

        Session::flash('type', 'success');
        Session::flash('message', 'Password changed successfully!');

        return redirect()->route('admin.profile')->with('success', 'Password changed successfully.');
    }
}
