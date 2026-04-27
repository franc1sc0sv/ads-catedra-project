<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MovementType;
use App\Models\InventoryMovement;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        $stockBefore = fake()->numberBetween(0, 200);
        $quantity = fake()->numberBetween(1, 50);

        return [
            'medication_id' => Medication::factory(),
            'type' => MovementType::PURCHASE_IN->value,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockBefore + $quantity,
            'user_id' => User::factory(),
            'sale_id' => null,
            'purchase_order_id' => null,
            'reason' => null,
        ];
    }
}
