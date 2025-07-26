<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer; // Import your Customer model

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $totalRecords = 10000;
        $chunkSize = 1000; // Insert 1000 records at a time

        // Optional: Clear existing data before seeding
        // Be careful with truncate if you have foreign key constraints.
        // If you do, you might need to disable/enable foreign key checks:
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Customer::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Customer::truncate(); // If no foreign key issues

        $this->command->info("Seeding {$totalRecords} customers in chunks of {$chunkSize}...");

        // Generate records in chunks
        for ($i = 0; $i < ($totalRecords / $chunkSize); $i++) {
            $customers = Customer::factory()->count($chunkSize)->make()->toArray();

            // Manually add timestamps if your table expects them and you're using DB::table()->insert()
            // Model::insert() handles this automatically if fillable/mass assignment is set up.
            $now = now();
            foreach ($customers as &$customer) {
                $customer['created_at'] = $now;
                $customer['updated_at'] = $now;
            }

            DB::table('customers')->insert($customers);
            $this->command->info("Chunk " . ($i + 1) . " seeded.");
        }

        $this->command->info("{$totalRecords} customers seeded successfully!");
    }
}