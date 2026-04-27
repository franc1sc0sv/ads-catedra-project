<?php

declare(strict_types=1);

namespace App\Services\Proveedores;

use App\Enums\MovementType;
use App\Enums\PurchaseOrderStatus;
use App\Models\InventoryMovement;
use App\Models\Medication;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Services\Proveedores\Contracts\PedidoServiceInterface;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class PedidoService implements PedidoServiceInterface
{
    public function create(array $data, int $requestedById): PurchaseOrder
    {
        $items = $data['items'] ?? [];

        if ($items === []) {
            throw new DomainException('El pedido debe tener al menos una línea.');
        }

        return DB::transaction(function () use ($data, $items, $requestedById): PurchaseOrder {
            $totalEstimated = 0.0;
            foreach ($items as $line) {
                $totalEstimated += ((int) $line['quantity']) * ((float) $line['unit_price']);
            }

            $order = PurchaseOrder::create([
                'supplier_id' => (int) $data['supplier_id'],
                'requested_by_id' => $requestedById,
                'status' => PurchaseOrderStatus::REQUESTED,
                'total_estimated' => round($totalEstimated, 2),
                'notes' => $data['notes'] ?? null,
                'expected_at' => $data['expected_at'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($items as $line) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'medication_id' => (int) $line['medication_id'],
                    'quantity_requested' => (int) $line['quantity'],
                    'quantity_received' => 0,
                    'purchase_price' => round((float) $line['unit_price'], 2),
                ]);
            }

            return $order->load('items.medication', 'supplier');
        });
    }

    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return PurchaseOrder::query()
            ->with(['supplier', 'requestedBy'])
            ->when(! empty($filters['status']), function ($q) use ($filters): void {
                $q->where('status', $filters['status']);
            })
            ->when(! empty($filters['supplier_id']), function ($q) use ($filters): void {
                $q->where('supplier_id', (int) $filters['supplier_id']);
            })
            ->when(! empty($filters['from']), function ($q) use ($filters): void {
                $q->whereDate('ordered_at', '>=', $filters['from']);
            })
            ->when(! empty($filters['to']), function ($q) use ($filters): void {
                $q->whereDate('ordered_at', '<=', $filters['to']);
            })
            ->orderByDesc('ordered_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function cancel(PurchaseOrder $order, string $reason): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $reason): PurchaseOrder {
            /** @var PurchaseOrder $fresh */
            $fresh = PurchaseOrder::query()->lockForUpdate()->findOrFail($order->id);

            if ($fresh->status !== PurchaseOrderStatus::REQUESTED) {
                throw new DomainException('Solo se pueden cancelar pedidos en estado Solicitado.');
            }

            $fresh->status = PurchaseOrderStatus::CANCELLED;
            $fresh->cancellation_reason = $reason;
            $fresh->save();

            return $fresh->refresh();
        });
    }

    public function markShipped(PurchaseOrder $order): PurchaseOrder
    {
        return DB::transaction(function () use ($order): PurchaseOrder {
            /** @var PurchaseOrder $fresh */
            $fresh = PurchaseOrder::query()->lockForUpdate()->findOrFail($order->id);

            if ($fresh->status !== PurchaseOrderStatus::REQUESTED) {
                throw new DomainException('Solo se pueden marcar como enviados pedidos en estado Solicitado.');
            }

            $fresh->status = PurchaseOrderStatus::SHIPPED;
            $fresh->save();

            return $fresh->refresh();
        });
    }

    public function receive(PurchaseOrder $order, array $items, int $receivedById): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $items, $receivedById): PurchaseOrder {
            /** @var PurchaseOrder $fresh */
            $fresh = PurchaseOrder::query()->lockForUpdate()->findOrFail($order->id);

            if (! in_array($fresh->status, [PurchaseOrderStatus::REQUESTED, PurchaseOrderStatus::SHIPPED], true)) {
                throw new DomainException('Solo se pueden recibir pedidos en estado Solicitado o Enviado.');
            }

            $orderItems = $fresh->items()->with('medication')->get()->keyBy('id');

            foreach ($items as $itemId => $row) {
                $itemId = (int) $itemId;
                /** @var PurchaseOrderItem|null $item */
                $item = $orderItems->get($itemId);

                if ($item === null) {
                    throw new DomainException("La línea {$itemId} no pertenece a este pedido.");
                }

                $receivedQty = (int) ($row['quantity_received'] ?? 0);
                $unitPrice = isset($row['unit_price']) && $row['unit_price'] !== null && $row['unit_price'] !== ''
                    ? round((float) $row['unit_price'], 2)
                    : ($item->purchase_price !== null ? (float) $item->purchase_price : null);

                $item->quantity_received = $receivedQty;
                if ($unitPrice !== null) {
                    $item->purchase_price = $unitPrice;
                }
                $item->save();

                if ($receivedQty <= 0) {
                    continue;
                }

                /** @var Medication $medication */
                $medication = Medication::query()
                    ->whereKey($item->medication_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $stockBefore = (int) $medication->stock;
                $stockAfter = $stockBefore + $receivedQty;

                $medication->stock = $stockAfter;
                $medication->save();

                InventoryMovement::create([
                    'medication_id' => $medication->id,
                    'type' => MovementType::PURCHASE_IN,
                    'quantity' => $receivedQty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'user_id' => $receivedById,
                    'purchase_order_id' => $fresh->id,
                ]);
            }

            $fresh->status = PurchaseOrderStatus::RECEIVED;
            $fresh->received_at = CarbonImmutable::now();
            $fresh->received_by_id = $receivedById;
            $fresh->save();

            return $fresh->refresh()->load(['items.medication', 'supplier', 'requestedBy', 'receivedBy']);
        });
    }
}
