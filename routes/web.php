<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerSearchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\SampleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});


Route::match(['get', 'post'], 'admin/login', [AuthController::class, 'adminLogin'])->name('admin_login');
Route::get('admin/logout', function () {
    Session::flush();
    return to_route('admin_login')->with('success', 'Logged out successfully.');
})->name('admin_logout');



Route::middleware(['auth_check'])->group(function () {
    Route::get('admin/dashboard', [MasterController::class, 'adminDashboard'])->name('dashboard');

    Route::get('states', [MasterController::class, 'viewStates'])->name('view_states');

    Route::get('districts', [MasterController::class, 'viewDistricts'])->name('view_districts');
    Route::post('update-district', [MasterController::class, 'updateDistrict'])->name('update_district');
    Route::post('district/change-status', [MasterController::class, 'changeStatus'])->name('change_district_status');

    Route::get('roles', [MasterController::class, 'viewRoles'])->name('view_roles');
    Route::post('create-role', [MasterController::class, 'createRole'])->name('create_role');
    Route::post('update-role', [MasterController::class, 'updateRole'])->name('update_role');
    Route::post('change-role-status', [MasterController::class, 'changeRoleStatus'])->name('change_role_status');

    Route::get('employees', [EmployeeController::class, 'viewEmployee'])->name('view_employees');

    Route::get('sample-registration', [SampleController::class, 'registerSample'])->name('sample_registration');

    Route::get('/search-names', [SampleController::class, 'searchNames'])->name('search.names');
});
