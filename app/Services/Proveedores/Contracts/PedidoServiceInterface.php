<?php

declare(strict_types=1);

namespace App\Services\Proveedores\Contracts;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PedidoServiceInterface
{
    /**
     * Create a new purchase order with all its items inside a single DB transaction.
     *
     * @param  array  $data  ['supplier_id', 'notes'?, 'expected_at'?, 'items' => [['medication_id','quantity','unit_price'], ...]]
     */
    public function create(array $data, int $requestedById): PurchaseOrder;

    /**
     * @param  array  $filters  ['status'?, 'supplier_id'?, 'from'?, 'to'?]
     */
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Cancel a purchase order. Only valid from REQUESTED.
     */
    public function cancel(PurchaseOrder $order, string $reason): PurchaseOrder;

    /**
     * Mark a purchase order as SHIPPED. Only valid from REQUESTED.
     */
    public function markShipped(PurchaseOrder $order): PurchaseOrder;

    /**
     * Receive a purchase order in a single atomic transaction:
     *  - persist quantity_received and purchase_price per item
     *  - increment medication stock
     *  - record one InventoryMovement (PURCHASE_IN) per item
     *  - mark order as RECEIVED with timestamp + receiver user
     *
     * @param  array  $items  [purchaseOrderItemId => ['quantity_received' => int, 'unit_price' => ?float]]
     */
    public function receive(PurchaseOrder $order, array $items, int $receivedById): PurchaseOrder;
}
