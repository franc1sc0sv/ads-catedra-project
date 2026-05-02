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
     * {product_id, quantity}), sold_at (string|Carbon),
     * doctor_name (string|null), doctor_license (string|null).
     *
     * If any item requires a prescription the sale is created as PENDING
     * and stock is NOT decremented until completeSale() is called.
     *
     * @throws \DomainException on stock insufficiency or missing prescription metadata.
     */
    public function registerSale(array $data, int $salespersonId): Sale;

    /**
     * Complete a PENDING sale after all linked prescriptions are VALIDATED.
     * Decrements stock for every item and writes SALE_OUT inventory movements.
     *
     * @throws \DomainException if any prescription is still pending, or if stock
     *                          is no longer sufficient at approval time.
     */
    public function completeSale(Sale $sale, int $pharmacistId): Sale;

    /**
     * Cancel a sale: reject linked prescriptions, persist reason, and write audit.
     * If the sale was COMPLETED, also restores stock and writes CUSTOMER_RETURN movements.
     * If the sale was PENDING (stock never decremented), no stock change occurs.
     *
     * @throws \DomainException if the sale is already cancelled.
     */
    public function cancelSale(Sale $sale, string $reason, int $userId): Sale;

    /**
     * Reject a prescription-gated sale on behalf of the pharmacist.
     * Delegates to cancelSale with a prefixed reason.
     */
    public function rejectSaleByPharmacist(Sale $sale, string $reason, int $pharmacistId): Sale;
}
