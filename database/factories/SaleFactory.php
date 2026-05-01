<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 5, 500);
        $tax = round($subtotal * 0.13, 2);

        return [
            'sold_at' => now(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'payment_method' => PaymentMethod::CASH->value,
            'status' => SaleStatus::PENDING->value,
            'customer_id' => null,
            'salesperson_id' => User::factory()->state(['role' => UserRole::SALESPERSON->value]),
            'cancellation_reason' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(['status' => SaleStatus::COMPLETED->value]);
    }

    public function cancelled(string $reason = 'Cliente desistió'): static
    {
        return $this->state([
            'status' => SaleStatus::CANCELLED->value,
            'cancellation_reason' => $reason,
        ]);
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(['customer_id' => $customer->id]);
    }
}
