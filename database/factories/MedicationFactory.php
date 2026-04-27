<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MedicationCategory;
use App\Models\Medication;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'barcode' => fake()->unique()->ean13(),
            'price' => fake()->randomFloat(2, 1, 200),
            'stock' => fake()->numberBetween(0, 200),
            'min_stock' => 10,
            'expires_at' => fake()->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'category' => MedicationCategory::OVER_THE_COUNTER->value,
            'supplier_id' => Supplier::factory(),
            'is_active' => true,
        ];
    }

    public function controlled(): static
    {
        return $this->state(['category' => MedicationCategory::CONTROLLED->value]);
    }

    public function prescriptionRequired(): static
    {
        return $this->state(['category' => MedicationCategory::PRESCRIPTION_REQUIRED->value]);
    }

    public function lowStock(): static
    {
        return $this->state(['stock' => 2, 'min_stock' => 10]);
    }
}
