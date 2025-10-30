<?php

use App\Http\Controllers\AllottmentController;
use App\Http\Controllers\AnalystController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RoController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});
Route::post('contact-us', [FrontController::class, 'contactSupport'])->name('contact_support');

Route::middleware(['access_control'])->group(function () {
    Route::match(['get', 'post'], 'admin/login', [AuthController::class, 'adminLogin'])->name('admin_login');
    Route::match(['get', 'post'], 'user/login', [AuthController::class, 'userLogin'])->name('user_login');
    Route::get('dashboard', [MasterController::class, 'adminDashboard'])->name('dashboard');
    Route::get('admin/logout', function () {
        Session::flush();
        return to_route('admin_login')->with('success', 'Logged out successfully.');
    })->name('admin_logout');
    Route::get('user/logout', function () {
        Session::flush();
        return to_route('user_login')->with('success', 'Logged out successfully.');
    })->name('user_logout');
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
    Route::get('tests-all', [MasterController::class, 'viewTests'])->name('view_tests');
    Route::match(['get', 'post'], 'create-test', [MasterController::class, 'createTest'])->name('create_test');
    Route::match(['get', 'post'], 'update-test/{id}', [MasterController::class, 'updateTest'])->name('update_test');
    Route::post('delete-test', [MasterController::class, 'deleteTest'])->name('delete_test');
    // Importing All tests 
    Route::post('/tests/import', [MasterController::class, 'importTests'])->name('import_all_tests');
    Route::post('/standards/import', [MasterController::class, 'importStandards'])->name('import_all_standards');
    Route::post('/primary-tests/import', [MasterController::class, 'importPrimaryTests'])->name('import_all_primary');
    Route::post('/secondary-tests/import', [MasterController::class, 'importSecondaryTests'])->name('import_all_secondary');

    // Accredations Related Routes
    Route::get('view-accreditations', [MasterController::class, 'accreditations'])->name('view_accreditations');
    Route::post('create-accreditation', [MasterController::class, 'createAccreditation'])->name('create_accreditation');
    // Standards
    Route::get('standards', [MasterController::class, 'viewStandards'])->name('view_standards');
    Route::match(['get', 'post'], 'create-standard', [MasterController::class, 'createStandard'])->name('create_standard_main');
    Route::match(['get', 'post'], 'update-standard/{id}', [MasterController::class, 'updateStandard'])->name('update_standard');
    Route::post('delete-standard', [MasterController::class, 'deleteStandard'])->name('delete_standard');

    // Primary Tests
    Route::get('primary-tests', [MasterController::class, 'viewPrimaryTests'])->name('view_primary_tests');
    Route::match(['get', 'post'], 'create-primary-test', [MasterController::class, 'createPrimaryTest'])->name('create_primary');
    Route::match(['get', 'post'], 'update-primary-test/{id}', [MasterController::class, 'updatePrimaryTest'])->name('update_primary_test');
    Route::post('delete-primary-test', [MasterController::class, 'deletePrimaryTest'])->name('delete_primary_test');

    // Secondary test 
    Route::get('secondary-tests', [MasterController::class, 'viewSecondaryTests'])->name('view_secondary_tests');
    Route::match(['get', 'post'], 'create-secondary-test', [MasterController::class, 'createSecondaryTest'])->name('create_secondary');
    Route::match(['get', 'post'], 'update-secondary-test/{id}', [MasterController::class, 'updateSecondaryTest'])->name('update_secondary_test');
    Route::post('delete-secondary-test', [MasterController::class, 'deleteSecondaryTest'])->name('delete_secondary_test');

    // For Packages 
    Route::get('packages', [MasterController::class, 'viewPackage'])->name('view_package');
    Route::match(['get', 'post'], 'create-package', [MasterController::class, 'createPackage'])->name('create_package');
    Route::match(['get', 'post'], 'update-package/{id}', [MasterController::class, 'updatePackage'])->name('update_package');
    Route::post('delete-pckage', [MasterController::class, 'deletePackage'])->name('delete_package');

    // For Contracts 
    Route::get('contracts', [MasterController::class, 'viewContract'])->name('view_contract');
    Route::match(['get', 'post'], 'create-contract', [MasterController::class, 'createContract'])->name('create_contract');
    Route::match(['get', 'post'], 'update-contract/{id}', [MasterController::class, 'updateContract'])->name('update_contract');

    // For Specification 
    Route::get('specifications', [MasterController::class, 'viewSpecification'])->name('view_specification');
    Route::match(['get', 'post'], 'create-specification', [MasterController::class, 'createSpecification'])->name('create_specification');
    Route::match(['get', 'post'], 'update-specification/{id}', [MasterController::class, 'updateSpecification'])->name('update_specification');

    // For Custom 
    Route::get('customs', [MasterController::class, 'viewCustom'])->name('view_custom');
    Route::match(['get', 'post'], 'create-custom', [MasterController::class, 'createCustom'])->name('create_custom');
    Route::match(['get', 'post'], 'update-custom/{id}', [MasterController::class, 'updateCustom'])->name('update_custom');

    //Sample Registration
    Route::match(['get', 'post'], 'sample-regsitration', [RegistrationController::class, 'preRegistration'])->name('register_sample');
    Route::get('registered-samples', [RegistrationController::class, 'viewRegSamples'])->name('view_registered_samples');

    // Blank Registration
    Route::match(['get', 'post'], 'blank-registration', [RegistrationController::class, 'blankRegistration'])->name('blank_registration');

    // Invoice Registration
    Route::get('/samples/{id}/invoicedetails', [InvoiceController::class, 'showInvoiceDetails'])->name('view_invoice');
    Route::get('/customer/{id}/invoicedetails', [InvoiceController::class, 'showAllInvoiceDetails'])->name('view_all_invoice');
    Route::get('/generate-combined-invoice', [InvoiceController::class, 'generateCombinedInvoice'])->name('generate_combined_invoice');



    // Sample detail routes 
    Route::get('/samples/{id}/details', [RegistrationController::class, 'showSampleDetails'])->name('view_registration_pdf');
    Route::get('/samples/{id}/print', [RegistrationController::class, 'printSampleDetails'])->name('print_sample_acknowledgement');
    Route::post('/samples/upgrade', [RegistrationController::class, 'upgradeToTatkal'])->name('upgrade_to_tatkal');

    // aLLOTTMENT 
    Route::prefix('allotment')->group(function () {
        Route::get('/dashboard', [AllottmentController::class, 'pendingAllotments'])->name('view_allottment');
        Route::get('/manage/{registrationId}', [AllottmentController::class, 'viewAllottment'])->name('show_allotment');

        Route::match(['get', 'post'], 'update-sample-tests/{id}', [AllottmentController::class, 'editSampleTests'])->name('edit_sample');
    });

    // Analyst Ruotes
    Route::prefix('analyst')->group(function () {
        Route::get('dashboard', [AnalystController::class, 'viewAnalystDashboard'])->name('view_analyst_dashboard');
        // Route::get('test/{id}', [AnalystController::class, 'viewTest'])->name('create_analysis');
        Route::get('update/{id}', [AnalystController::class, 'updateStatus'])->name('update_analysis');
        Route::get('rejected-samples', [AnalystController::class, 'rejectedSamples'])->name('rejected_samples');
        Route::match(['get', 'post'], 'revise-test/{refId}', [AnalystController::class, 'reviseTest'])->name('revise_test');

        // Add this route to your existing routes
        Route::get('sample-tests/{sampleId}', [AnalystController::class, 'viewSampleTests'])->name('view_sample_tests');
    });
    // ACM Routes
    Route::get('acm-view', [MasterController::class, 'viewACM'])->name('view_acm');
    Route::match(['get', 'post'], 'update-access-to-routes', [MasterController::class, 'updatePermission'])->name('update_acm');
    Route::get('search-role', [MasterController::class, 'searchRole'])->name('search_role');
    Route::post('get-menus', [MasterController::class, 'getMenusForRole'])->name('get_menus_acm');

    // Test Result Management Routes
    Route::prefix('test-results')->group(function () {
        // Main CRUD routes
        Route::get('view-test-results', [TestResultController::class, 'reporting'])->name('test_results');
        Route::post('/create-result-test', [TestResultController::class, 'createResult'])->name('create_test_result');
        Route::get('show-sample-result/{id}', [TestResultController::class, 'showSampleResult'])->name('show_sample_result');
        Route::get('generate-report/{id}', [TestResultController::class, 'generateReport'])->name('generate_report');

        Route::get('/{id}/version/{versionNumber}', [TestResultController::class, 'viewVersion'])->name('view-version');
        Route::get('/{id}/compare', [TestResultController::class, 'compareVersions'])->name('compare-versions');

        // Audit Trail
        Route::get('/audit/trail', [TestResultController::class, 'audit'])->name('test_results_audit');
    });
    Route::get('/template', [TestResultController::class, 'getTestTemplate'])->name('create_test_template');

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

    Route::get('/check-test-exists', [MasterController::class, 'checkTestExists'])->name('check_test_exists');
    Route::get('search-customers', [RegistrationController::class, 'searchCustomer'])->name('search_customer');
    Route::get('/search-test', [RegistrationController::class, 'searchTest'])->name('search_test');
    Route::get('/get-standards-by-test', [RegistrationController::class, 'getStandardByTest'])->name('get_standards_by_test');


    // Get packages on the basis of test types 
    Route::get('/get-package-data', [RegistrationController::class, 'getPackages'])->name('get_packages');
    Route::get('/get-test-by-package', [RegistrationController::class, 'getTestByPackage'])->name('get_tests_by_package');


    // dynamic search side bar
    Route::get('/recent-records', [SampleController::class, 'recentRecords'])->name('recent_records');
    Route::get('/today-stats', [SampleController::class, 'todayStats'])->name('today_stats');
    Route::get('/sample-details', [SampleController::class, 'getSampleDetails'])->name('get_sample_details');

    // Validation
    Route::post('/validate-field', [ValidationController::class, 'checkField'])->name('validate_field');

    //  Create customer location 
    Route::post('add-location', [CustomerController::class, 'addLocation'])->name('create_customer_location');

    // Additional routes for allotment that needs to be under a middleware i had to fix it 
    // Allotment operations
    Route::post('/create', [AllottmentController::class, 'createAllottment'])->name('create_allotment');
    Route::post('/bulk-allot', [AllottmentController::class, 'allotTests'])->name('allot_tests');

    // Transfer operations
    Route::post('/transfer', [AllottmentController::class, 'transferTests'])->name('transfer_tests');
    Route::post('/accept-transfer', [AllottmentController::class, 'acceptTransferred'])->name('accept_transferred');

    // Bulk allot Sample
    Route::post('bulk-allot-sample', [AllottmentController::class, 'bulkAllotSamples'])->name('bulk_allot_sample');
    // Quick Allotment 
    Route::post('quick-allot-sample', [AllottmentController::class, 'quickAllotSample'])->name('quick_allot_sample');
    // Bluck Sample Transfer
    Route::post('bulk-transfer-sample', [AllottmentController::class, 'bulkTransferSamples'])->name('bulk_sample_transfer');
    // Quick Transfer Sample
    Route::post('quick-transfer-sample', [AllottmentController::class, 'quickTransferSample'])->name('quick_sample_transfer');
    // Search and allot test from all samples
    Route::post('/get-test-samples-allotment', [AllottmentController::class, 'getTestSamplesForAllotment'])->name('get_test_samples_allotment');
    // Route::post('/search-tests-allotment', [AllottmentController::class, 'searchTestsForAllotment'])->name('search_tests_allotment');
    Route::post('/allot-specific-tests', [AllottmentController::class, 'allotSpecificTests'])->name('allot_specific_tests');
    // Additional operations
    Route::post('/reassign', [AllottmentController::class, 'reassignTest'])->name('reassign');
    Route::get('/history/{testId}', [AllottmentController::class, 'getAllotmentHistory'])->name('history');

    // Manuscript
    Route::get('/manuscripts', [MasterController::class, 'viewManuscript'])->name('view_manuscripts');
    Route::post('/manuscripts/import', [MasterController::class, 'manuscriptImport'])->name('manuscripts_import');
    Route::get('/get-manuscripts', [MasterController::class, 'getManuscripts'])->name('get_manuscripts');
    Route::match(['get', 'post'], 'create/manuscripts', [MasterController::class, 'createManuscript'])->name('create_manuscript');
    // Manuscript Template
    Route::get('manuscript-template/{id}', [TestResultController::class, 'templateManuscript'])->name('template_manuscript');
    Route::get('completed-tests', [TestResultController::class, 'viewCompletedTests'])->name('view_completed_camples');
    // Transferred samples from other regional offices 
    Route::get('accept-pending-samples', [SampleController::class, 'viewPedingSmples'])->name('view_regional_samples');
    Route::post('accept-pending-transfer-sample/{id}', [SampleController::class, 'acceptTransferdSample'])->name('accept_sample');

    // Verification Routes
    Route::get('view-result-verification', [VerificationController::class, 'viewVerification'])->name('view_result_verification');
    Route::match(['get', 'post'], 'verify-result/{id}', [VerificationController::class, 'verifyResult'])->name('verify_result');

    // Payment for testing
    // Route::get('/razorpay-checkout', [RazorpayController::class, 'checkout'])->name('razorpay.checkout');
    // Route::post('/create-order', [RazorpayController::class, 'createOrder'])->name('razorpay.createOrder');
    // Route::post('/verify-payment', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verifyPayment');

});
Route::get('customer-wallet/{id}', [WalletController::class, 'viewWallet'])->name('view_wallet');

// Wallet Balance
Route::get('/wallet/balance', [WalletController::class, 'getBalance'])->name('wallet.balance');

// Wallet Transactions
Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');

// Process Sample to Reporting
Route::post('/wallet/process-reporting', [WalletController::class, 'processToReporting'])->name('wallet.process.reporting');

// Download Statement
Route::get('/wallet/statement', [WalletController::class, 'downloadStatement'])->name('wallet.statement');

// Razorpay Payment Routes
Route::get('/payment/checkout', [RazorpayController::class, 'checkout'])->name('payment.checkout');
Route::post('/razorpay/create-order', [RazorpayController::class, 'createOrder'])->name('razorpay.create.order');
Route::post('/razorpay/verify-payment', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verify.payment');
Route::get('/razorpay/payment-status/{orderId}', [RazorpayController::class, 'getPaymentStatus'])->name('razorpay.payment.status');

Route::post('/razorpay/webhook', [RazorpayController::class, 'webhook'])->name('razorpay.webhook');


Route::get('/get-all-samples-data', [AllottmentController::class, 'getAllSamplesData'])->name('get_all_samples_data');
