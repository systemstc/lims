<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roleId = Session::get('role_id');
        $routeName = Route::currentRouteName();

        if ($roleId == -1) {
            return $next($request);
        }

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
        if ($isAllowed) {
            return $next($request);
        }
        Session::flash('type', 'error');
        Session::flash('message', 'Permission denied: You are not allowed to access this module.');
        return redirect()->back();
    }
}
