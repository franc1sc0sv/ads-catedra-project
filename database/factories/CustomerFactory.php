<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'identification' => fake()->unique()->numerify('########-#'),
            'is_frequent' => false,
            'is_active' => true,
        ];
    }

    public function frequent(): static
    {
        return $this->state(['is_frequent' => true]);
    }
}
