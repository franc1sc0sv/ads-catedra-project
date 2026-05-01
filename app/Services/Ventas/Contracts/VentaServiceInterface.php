<?php

declare(strict_types=1);

namespace App\Services\Ventas\Contracts;

use App\Models\Sale;

interface VentaServiceInterface
{
    /**
     * Persist a new sale with server-recomputed prices and tax.
     *
     * Expected $data keys: customer_id (int|null), items (array of
     * {product_id, quantity}), sold_at (string|Carbon), payment_method
     * (string), doctor_name (string|null), doctor_license (string|null).
     *
     * @throws \DomainException on stock insufficiency or missing prescription metadata.
     */
    public function registerSale(array $data, int $salespersonId): Sale;

    /**
     * Cancel a sale: restore stock, reject linked prescriptions,
     * persist the cancellation reason, and write the audit entry.
     *
     * @throws \DomainException if the sale is already cancelled.
     */
    public function cancelSale(Sale $sale, string $reason, int $userId): Sale;
}
