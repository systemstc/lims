<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerSearchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\RoController;
use App\Http\Controllers\SampleController;
use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});


Route::match(['get', 'post'], 'admin/login', [AuthController::class, 'adminLogin'])->name('admin_login');
Route::match(['get', 'post'], 'user/login', [AuthController::class, 'userLogin'])->name('user_login');
Route::get('admin/logout', function () {
    Session::flush();
    return to_route('admin_login')->with('success', 'Logged out successfully.');
})->name('admin_logout');
Route::get('user/logout', function () {
    Session::flush();
    return to_route('user_login')->with('success', 'Logged out successfully.');
})->name('user_logout');



Route::middleware(['auth_check'])->group(function () {
    Route::get('dashboard', [MasterController::class, 'adminDashboard'])->name('dashboard');
});
Route::middleware(['check_permission'])->group(function () {
    Route::get('states', [MasterController::class, 'viewStates'])->name('view_states');

    Route::get('districts', [MasterController::class, 'viewDistricts'])->name('view_districts');
    Route::post('update-district', [MasterController::class, 'updateDistrict'])->name('update_district');
    Route::post('district/change-status', [MasterController::class, 'changeStatus'])->name('change_district_status');

    Route::get('roles', [MasterController::class, 'viewRoles'])->name('view_roles');
    Route::post('create-role', [MasterController::class, 'createRole'])->name('create_role');
    Route::post('update-role', [MasterController::class, 'updateRole'])->name('update_role');
    Route::post('change-role-status', [MasterController::class, 'changeRoleStatus'])->name('change_role_status');

    Route::get('employees', [EmployeeController::class, 'viewEmployee'])->name('view_employees');
    Route::match(['get', 'post'], 'create-employee', [EmployeeController::class, 'createEmployee'])->name('create_employee');


    Route::get('sample-registration', [SampleController::class, 'registerSample'])->name('sample_registration');

    Route::get('/search-names', [SampleController::class, 'searchNames'])->name('search.names');

    // Ros
    Route::get('ros', [RoController::class, 'ros'])->name('view_ros');
    Route::post('create-ro', [RoController::class, 'createRo'])->name('create_ro');
    Route::post('update-ro', [RoController::class, 'updateRo'])->name('update_ro');
    Route::post('change-status-ro', [RoController::class, 'changeRoStatus'])->name('change_ro_status');

    // Customer Routes
    Route::get('customers', [CustomerController::class, 'customerAll'])->name('customers');
    Route::match(['get', 'post'], 'create-customer', [CustomerController::class, 'createCustomer'])->name('create_customer');
    Route::match(['get', 'post'], 'update-customer/{id}', [CustomerController::class, 'updateCustomer'])->name('update_customer');
    Route::post('delete-customer', [CustomerController::class, 'deleteCustomer'])->name('delet_customer');
    // Customer types 
    Route::get('view-customer-types', [CustomerController::class, 'viewCustomerTypes'])->name('view_customer_type');
    Route::post('create-customer-type', [CustomerController::class, 'createCustomerType'])->name('create_customer_type');
    Route::post('update-customer-type', [CustomerController::class, 'updateCustomerType'])->name('update_customer_type');
    Route::post('delete-customer-type', [CustomerController::class, 'deleteCustomerType'])->name('delete_customer_type');

    // Samples 
    Route::get('samples', [MasterController::class, 'viewSamples'])->name('view_samples');
    Route::post('create-sample', [MasterController::class, 'createSample'])->name('create_sample');
    Route::post('update-sample', [MasterController::class, 'updateSample'])->name('update_sample');
    Route::post('delete-sample', [MasterController::class, 'deleteSample'])->name('delete_sample');

    // Groups
    Route::get('groups', [MasterController::class, 'viewGroups'])->name('view_groups');
    Route::post('create-group', [MasterController::class, 'createGroup'])->name('create_group');
    Route::post('update-group', [MasterController::class, 'updateGroup'])->name('update_group');
    Route::post('delete-group', [MasterController::class, 'deleteGroup'])->name('delete_group');

    // Departments
    Route::get('departments', [MasterController::class, 'viewDepartments'])->name('view_departments');
    Route::post('create-department', [MasterController::class, 'createDepartment'])->name('create_department');
    Route::post('update-department', [MasterController::class, 'updateDepartment'])->name('update_department');
    Route::post('delete-department', [MasterController::class, 'deleteDepartment'])->name('delete_department');

    // Lab Samples
    Route::get('lab_samples', [MasterController::class, 'viewLabSamples'])->name('view_lab_samples');
    Route::post('create-lab-sample', [MasterController::class, 'createLabSamples'])->name('create_lab_sample');
    Route::post('update-lab-sample', [MasterController::class, 'updateLabSamples'])->name('update_lab_sample');
    Route::post('delete-lab-sample', [MasterController::class, 'deleteLabSamples'])->name('delete_lab_sample');

    // Tests
    Route::get('tests', [MasterController::class, 'viewTests'])->name('view_tests');
    Route::match(['get', 'post'], 'create-test', [MasterController::class, 'createTest'])->name('create_test');
    Route::match(['get', 'post'], 'update-test/{id}', [MasterController::class, 'updateTest'])->name('update_test');
    Route::post('delete-test', [MasterController::class, 'deleteTest'])->name('delete_test');

    // Standards
    Route::get('standards', [MasterController::class, 'viewStandards'])->name('view_standards');
    Route::match(['get', 'post'], 'create-standard', [MasterController::class, 'createStandard'])->name('create_standard');
    Route::match(['get', 'post'], 'update-standard/{id}', [MasterController::class, 'updateStandard'])->name('update_standard');
    Route::post('delete-standard', [MasterController::class, 'deleteStandard'])->name('delete_standard');

    // Primary Tests
    Route::get('primary-tests', [MasterController::class, 'viewPrimaryTests'])->name('view_primary_tests');
    Route::match(['get', 'post'], 'create-primary-test', [MasterController::class, 'createPrimaryTest'])->name('create_primary_test');
    Route::match(['get', 'post'], 'update-primary-test/{id}', [MasterController::class, 'updatePrimaryTest'])->name('update_primary_test');
    Route::post('delete-primary-test', [MasterController::class, 'deletePrimaryTest'])->name('delete_primary_test');

    // Secondary test 
    Route::get('secondary-tests', [MasterController::class, 'viewSecondaryTests'])->name('view_secondary_tests');
    Route::match(['get', 'post'], 'create-secondary-test', [MasterController::class, 'createSecondaryTest'])->name('create_secondary_test');
    Route::match(['get', 'post'], 'update-secondary-test/{id}', [MasterController::class, 'updateSecondaryTest'])->name('update_secondary_test');
    Route::post('delete-secondary-test', [MasterController::class, 'deleteSecondaryTest'])->name('delete_secondary_test');

    // For Simple and Multi Value measurements
    Route::get('measurements', [MeasurementController::class, 'viewMeasurements'])->name('view_measurements');
    Route::match(['get', 'post'], 'create-measurement', [MeasurementController::class, 'createMeasurement'])->name('create_measurement');
    Route::match(['get', 'post'], 'update-measurement/{id}', [MeasurementController::class, 'updateMeasurement'])->name('update_measurement');
    Route::post('delete-measurement', [MeasurementController::class, 'deleteMeasurement'])->name('delete_measurement');
});

Route::get('/get-districts', [MasterController::class, 'getDistricts'])->name('get_districts');
Route::get('group-bysample', [MasterController::class, 'getGroups'])->name('get_groups');
Route::get('test-bygroup', [MasterController::class, 'getTests'])->name('get_tests');
Route::get('primarytest-bytest', [MasterController::class, 'getPrimaryTests'])->name('get_primary_tests');

// Search routes for test creation
Route::get('/search-standards', [MasterController::class, 'searchStandards'])->name('search_standards');
Route::get('/search-primary-tests', [MasterController::class, 'searchPrimaryTests'])->name('search_primary_tests');
Route::get('/search-secondary-tests', [MasterController::class, 'searchSecondaryTests'])->name('search_secondary_tests');

// Create new item routes
Route::post('/ajax/create-standard', [MasterController::class, 'createAjaxStandard'])->name('create_standard');
Route::post('/ajax/create-primary-test', [MasterController::class, 'createAjaxPrimaryTest'])->name('create_primary_test');
Route::post('/ajax/create-secondary-test', [MasterController::class, 'createAjaxSecondaryTest'])->name('create_secondary_test');

// Routes for getting individual items by ID (needed for edit form)
Route::get('/get-standard-by-id', [MasterController::class, 'getStandardsByIds'])->name('get_standards_by_ids');
Route::get('/get-primary-test-by-id', [MasterController::class, 'getPrimaryTestById'])->name('get_primary_tests_by_ids');
Route::get('/get-secondary-test-by-id', [MasterController::class, 'getSecondaryTestById'])->name('get_secondary_tests_by_ids');
