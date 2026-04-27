<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PrescriptionStatus;
use App\Models\Medication;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        return [
            'prescription_number' => strtoupper(fake()->unique()->bothify('RX-####-???')),
            'patient_name' => fake()->name(),
            'doctor_name' => 'Dr. '.fake()->name(),
            'doctor_license' => strtoupper(fake()->bothify('JVPM-#####')),
            'issued_at' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'expires_at' => fake()->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'status' => PrescriptionStatus::PENDING->value,
            'pharmacist_id' => null,
            'validated_at' => null,
            'notes' => null,
            'medication_id' => Medication::factory()->controlled(),
            'current_reviewer_id' => null,
            'lock_expires_at' => null,
        ];
    }

    public function validated(): static
    {
        return $this->state([
            'status' => PrescriptionStatus::VALIDATED->value,
            'validated_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => PrescriptionStatus::REJECTED->value,
            'validated_at' => now(),
        ]);
    }
}
