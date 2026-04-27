<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Medication;
use App\Models\Prescription;
use App\Models\Sale;
use App\Models\SalePrescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalePrescription>
 */
class SalePrescriptionFactory extends Factory
{
    protected $model = SalePrescription::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'prescription_id' => Prescription::factory(),
            'medication_id' => Medication::factory()->controlled(),
        ];
    }
}
