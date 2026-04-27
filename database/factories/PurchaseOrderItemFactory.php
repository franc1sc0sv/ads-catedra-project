<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Medication;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'medication_id' => Medication::factory(),
            'quantity_requested' => fake()->numberBetween(10, 100),
            'quantity_received' => 0,
            'purchase_price' => fake()->randomFloat(2, 1, 50),
        ];
    }
}
