<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Enums\UserRole;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'requested_by_id' => User::factory()->state(['role' => UserRole::INVENTORY_MANAGER->value]),
            'received_by_id' => null,
            'ordered_at' => now(),
            'received_at' => null,
            'status' => PurchaseOrderStatus::REQUESTED->value,
            'total_estimated' => fake()->randomFloat(2, 100, 5000),
            'notes' => null,
            'cancellation_reason' => null,
        ];
    }

    public function received(): static
    {
        return $this->state([
            'status' => PurchaseOrderStatus::RECEIVED->value,
            'received_at' => now(),
        ]);
    }

    public function cancelled(string $reason = 'Stock duplicado'): static
    {
        return $this->state([
            'status' => PurchaseOrderStatus::CANCELLED->value,
            'cancellation_reason' => $reason,
        ]);
    }
}
