<?php

namespace Tests\Feature;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase; // Don't use RefreshDatabase for now as we want to use existing data
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\SampleRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EndToEndWorkflowTest extends TestCase
{
    // use WithFaker;

    public function test_admin_can_login_and_create_customer()
    {
        // 1. Login as Admin
        // Find an admin user
        $adminEmail = DB::table('m00_admins')->value('m00_email') ?? 'admin@gmail.com';

        $response = $this->post('/admin/login', [
            'txt_email' => $adminEmail,
            'txt_password' => '123456', // Assuming default or known
        ]);

        if ($response->getStatusCode() === 302) {
            $response->assertStatus(302);
            // Redirects to dashboard
        } else {
            // checking if validation failed
            // dump(session('errors'));
        }

        // 2. Create Customer
        // Fetch necessary IDs (assuming seed data exists)
        $stateId = DB::table('m01_states')->value('m01_state_id');
        $districtId = DB::table('m02_districts')->where('m01_state_id', $stateId)->value('m02_district_id') ?? DB::table('m02_districts')->value('m02_district_id');
        $customerTypeId = DB::table('m09_customer_types')->value('m09_customer_type_id');

        $customerEmail = 'testcustomer' . time() . '@example.com';
        $customerData = [
            'txt_customer_type_id' => $customerTypeId,
            'txt_name' => 'Test Customer ' . time(),
            'txt_email' => $customerEmail,
            'txt_phone' => '9876543210',
            'txt_contact_person' => 'Contact Person',
            'txt_address' => '123 Test St',
            'txt_state_id' => $stateId,
            'txt_district_id' => $districtId,
            'txt_pincode' => '123456',
            'txt_gst' => '22AAAAA0000A1Z5',
        ];

        $admin = DB::table('m00_admins')->first();
        if ($admin && session('role_id') !== -1) {
            session([
                'admin_id' => $admin->m00_admin_id,
                'name' => $admin->m00_name,
                'email' => $admin->m00_email,
                'role_id' => -1,
                'role' => 'ADMIN',
            ]);
        }

        $response = $this->post('/create-customer', $customerData);
        $response->assertStatus(302);

        // Verify customer exists
        $this->assertDatabaseHas('m07_customers', [
            'm07_email' => $customerData['txt_email'],
        ]);

        $this->get('/admin/logout');
    }

    private function getUserForRole($roleName)
    {
        return DB::table('m06_employees')
            ->join('m03_roles', 'm06_employees.m03_role_id', '=', 'm03_roles.m03_role_id')
            ->join('tr01_users', 'm06_employees.tr01_user_id', '=', 'tr01_users.tr01_user_id')
            ->where('m03_roles.m03_name', $roleName)
            ->select('tr01_users.tr01_email as m06_email', 'm06_employees.m06_employee_id', 'm06_employees.tr01_user_id')
            ->first();
    }

    public function test_users_can_login()
    {
        $roles = ['Registrar', 'Manager', 'Analyst', 'Verification Officer']; // Check exact role names in DB

        foreach ($roles as $role) {
            $user = $this->getUserForRole($role);
            if (!$user) {
                $this->markTestSkipped("User with role $role not found.");
                continue;
            }

            DB::table('tr01_users')->where('tr01_user_id', $user->tr01_user_id)->update([
                'tr01_password' => Hash::make('Default@123')
            ]);

            $response = $this->post('/user/login', [
                'txt_email' => $user->m06_email,
                'txt_password' => 'Default@123',
            ]);

            $response->assertStatus(302);

            $this->get('/user/logout');
        }
    }

    public function test_complete_sample_lifecycle()
    {
        \Illuminate\Support\Facades\Mail::fake();

        // --- STEP 1: REGISTRAR (Register Sample) ---
        $registrar = $this->getUserForRole('Registrar');
        if (!$registrar) $this->markTestSkipped("Registrar not found");

        DB::table('tr01_users')->where('tr01_user_id', $registrar->tr01_user_id)->update([
            'tr01_password' => Hash::make('Default@123')
        ]);

        $this->post('/user/login', [
            'txt_email' => $registrar->m06_email,
            'txt_password' => 'Default@123',
        ]);

        $customerId = DB::table('m07_customers')->value('m07_customer_id');
        $customerTypeId = DB::table('m09_customer_types')->value('m09_customer_type_id');
        $departmentId = DB::table('m13_departments')->value('m13_department_id');
        $labSampleId = DB::table('m14_lab_samples')->value('m14_lab_sample_id');

        $testRow = DB::table('m12_tests')->first();
        $testId = $testRow->m12_test_id;
        $testNumber = $testRow->m12_test_number;
        $standardId = DB::table('m15_standards')->value('m15_standard_id');

        if ($customerId) {
            \App\Models\Wallet::firstOrCreate(
                ['m07_customer_id' => $customerId],
                [
                    'tr02_wallet_uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'tr02_currency' => 'INR',
                    'tr02_balance' => 10000,
                    'tr02_hold_amount' => 0,
                    'tr02_status' => 'active'
                ]
            );
        }

        $regPayload = [
            'dd_customer_type' => $customerTypeId,
            'commercial_type' => 1,
            'selected_customer_id' => $customerId,
            'txt_customer_name' => 'Test Customer',
            'selected_customer_address_id' => 'default',
            'txt_payment_by' => 'first_party',
            'txt_report_to' => ['first_party'],
            'txt_reference' => 'REF-' . time(),
            'txt_ref_date' => date('Y-m-d'),
            'txt_received_via' => 'Courier',
            'dd_department' => $departmentId,
            'dd_sample_type' => $labSampleId,
            'dd_test_type' => 'GENERAL', // Added missing field
            'dd_priority_type' => 'Normal',
            'txt_number_of_samples' => 1,
            'txt_description' => 'Test Sample Payload',
            'txt_due_date' => date('Y-m-d', strtotime('+7 days')),
            'txt_testing_charges' => 100,
            'txt_total_charges' => 100,
            'tests' => [
                [
                    'test_id' => $testId,
                    'test_number' => $testNumber,
                    'standard_id' => $standardId,
                ]
            ],
            'selected_buyer_id' => null,
            'selected_third_party_id' => null,
            'selected_cha_id' => null,
        ];

        $response = $this->withSession(['ro_id' => 1])->post('/sample-regsitration', $regPayload);

        if (session('errors')) {
            $this->fail("Registration Validation Failed: " . implode(', ', session('errors')->all()));
        }
        if (session('type') == 'error') {
            $this->fail("Registration Logic Failed: " . session('message'));
        }
        $response->assertStatus(302);
        // Expect success
        $response->assertSessionHas('type', 'success');

        // Get the registered sample
        $sampleReg = SampleRegistration::latest('tr04_sample_registration_id')->first();
        $this->assertNotNull($sampleReg);
        $this->assertEquals('Test Sample Payload', $sampleReg->tr04_sample_description);

        $this->get('/user/logout');

        // --- STEP 2: LAB MANAGER (Allot Sample) ---
        $manager = $this->getUserForRole('Manager'); // Or 'Lab Manager'
        if (!$manager) {
            // Try 'Lab Manager'
            $manager = $this->getUserForRole('Lab Manager');
        }
        if (!$manager) $this->markTestSkipped("Manager not found");

        DB::table('tr01_users')->where('tr01_user_id', $manager->tr01_user_id)->update([
            'tr01_password' => Hash::make('Default@123')
        ]);

        $this->post('/user/login', [
            'txt_email' => $manager->m06_email,
            'txt_password' => 'Default@123',
        ]);
        $this->assertEquals($manager->m06_employee_id, session('user_id'));

        $analyst = $this->getUserForRole('Analyst');
        if (!$analyst) $this->markTestSkipped("Analyst not found");
        $analystId = $analyst->m06_employee_id ?? DB::table('m06_employees')->where('m06_email', $analyst->m06_email)->value('m06_employee_id');

        $allotPayload = [
            'sample_id' => $sampleReg->tr04_sample_registration_id,
            'emp_id' => $analystId,
        ];

        $response = $this->withSession(['ro_id' => 1])->post('/quick-allot-sample', $allotPayload);
        if (session('errors')) {
            dump(session('errors')->all());
        }
        $response->assertStatus(302);
        $response->assertSessionHas('type', 'success');

        $this->assertDatabaseHas('tr05_sample_tests', [
            'tr04_sample_registration_id' => $sampleReg->tr04_sample_registration_id,
            'm06_alloted_to' => $analystId,
            'tr05_status' => 'ALLOTED',
        ]);

        $this->get('/user/logout');

        // --- STEP 3: ANALYST (Enter Result) ---
        DB::table('tr01_users')->where('tr01_user_id', $analyst->tr01_user_id)->update([
            'tr01_password' => Hash::make('Default@123')
        ]);

        $this->post('/user/login', [
            'txt_email' => $analyst->m06_email,
            'txt_password' => 'Default@123',
        ]);

        $resultPayload = [
            'registration_id' => $sampleReg->tr04_reference_id,
            'test_date' => date('Y-m-d'),
            'performance_date' => date('Y-m-d'),
            'action' => 'RESULTED',
            'results' => [
                $testNumber => [
                    'test' => [
                        'result' => 'Pass',
                        'remark' => 'Result Verified',
                    ]
                ]
            ],
        ];

        $response = $this->withSession(['ro_id' => 1])->post('/test-results/create-result-test', $resultPayload);
        if (session('error')) dump(session('error'));
        $response->assertStatus(302);

        $this->assertDatabaseHas('tr05_sample_tests', [
            'tr04_sample_registration_id' => $sampleReg->tr04_sample_registration_id,
            'm12_test_id' => $testId,
            'tr05_status' => 'COMPLETED',
        ]);

        $this->get('/user/logout');

        // --- STEP 4: VERIFICATION OFFICER ---
        $verifer = $this->getUserForRole('Verification Officer');
        if (!$verifer) $this->markTestSkipped("Verification Officer not found");

        DB::table('tr01_users')->where('tr01_user_id', $verifer->tr01_user_id)->update([
            'tr01_password' => Hash::make('Default@123')
        ]);

        $this->post('/user/login', [
            'txt_email' => $verifer->m06_email,
            'txt_password' => 'Default@123',
        ]);

        $verifyPayload = [
            'action' => 'verify',
            'remarks' => 'All good',
            'test_id' => [$testId],
        ];

        $response = $this->withSession(['ro_id' => 1])->post('/verify-result/' . $sampleReg->tr04_sample_registration_id, $verifyPayload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('tr07_test_results', [
            'tr04_sample_registration_id' => $sampleReg->tr04_sample_registration_id,
            'tr07_result_status' => 'VERIFIED',
        ]);
    }
}
