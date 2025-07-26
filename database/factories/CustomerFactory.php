<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(), // Generates a random name
            // 'created_at' and 'updated_at' are handled automatically if you use `insert` as shown below,
            // or by Eloquent if you used `create()`. No need to define them here for bulk insert.
        ];
    }
}