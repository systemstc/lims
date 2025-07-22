<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterController;
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
});
