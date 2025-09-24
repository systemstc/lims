<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AccessControl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = Route::currentRouteName();
        // 1. PREVENT ACCESS TO LOGIN PAGES IF ALREADY LOGGED IN
        if (in_array($routeName, ['admin_login', 'user_login'])) {
            if (Session::has('admin_id') || Session::has('user_id')) {
                return redirect()->route('dashboard');
            }
            return $next($request);
        }

        // 2. AUTH CHECK
        if (!Session::has('role_id')) {
            return to_route('admin_login')->with('error', 'Please login first.');
        }

        $roleId = Session::get('role_id');

        // 3. SUPER ADMIN BYPASS
        if ($roleId == -1) {
            return $next($request);
        }

        // 4. DEFINE ROUTES THAT SHOULD SKIP DB CHECK (but still need login)
        $skipPermissionRoutes = [
            // AJAX/helper routes that all logged-in users can access
            'dashboard',
            'user_logout',
            'get_districts',
            'get_groups',
            'get_tests',
            'get_primary_tests',
            'search_standards',
            'search_primary_tests',
            'search_secondary_tests',
            'create_standard',
            'create_primary_test',
            'create_secondary_test',
            'get_standards_by_ids',
            'get_primary_tests_by_ids',
            'get_secondary_tests_by_ids',
            'search_tests',
            'search_customer',
            'search_test',
            'get_standards_by_test',
            'get_packages',
            'get_tests_by_package',
            'global_search',
            'recent_records',
            'today_stats',
            'get_sample_details',
            'validate_field',
            'create_customer_location',
            'bulk_allot_sample',
            'quick_allot_sample',
            'bulk_sample_transfer',
            'quick_allot_sample',
            'search_tests_allotment',
            'allot_specific_tests',
            'create_allotment',
            'allot_tests',
            'transfer_tests',
            'accept_transferred',
            'reassign',
            'history',
            'import_all_tests',
            'import_all_standards',
            'import_all_primary',
            'import_all_secondary',
        ];

        if (in_array($routeName, $skipPermissionRoutes)) {
            return $next($request);
        }

        // 5. ROLE-BASED PERMISSION CHECK (DB)
        $routeMap = [
            'm05_route_view'   => 'm05_role_view',
            'm05_route_create' => 'm05_role_create',
            'm05_route_edit'   => 'm05_role_edit',
            'm05_route_delete' => 'm05_role_delete',
        ];

        $isAllowed = false;
        foreach ($routeMap as $routeField => $roleField) {
            $exists = DB::table('m05_menus')
                ->where('m05_status', 'ACTIVE')
                ->where($routeField, $routeName)
                ->whereRaw("FIND_IN_SET(?, {$roleField})", [$roleId])
                ->exists();

            if ($exists) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            Session::flash('type', 'error');
            Session::flash('message', 'Permission denied: You are not allowed to access this module.');
            return redirect()->back();
        }

        return $next($request);
    }
}
