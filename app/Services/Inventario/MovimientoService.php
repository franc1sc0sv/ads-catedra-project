<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Enums\MovementType;
use App\Models\InventoryMovement;
use App\Models\Medication;
use App\Models\Sale;
use App\Services\Inventario\Contracts\MovimientoServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class MovimientoService implements MovimientoServiceInterface
{
    public function getByMedicamento(int $medicamentoId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryMovement::query()
            ->with(['user', 'sale', 'purchaseOrder'])
            ->where('medication_id', $medicamentoId);

        if (! empty($filters['desde'])) {
            $query->whereDate('created_at', '>=', $filters['desde']);
        }

        if (! empty($filters['hasta'])) {
            $query->whereDate('created_at', '<=', $filters['hasta']);
        }

        if (! empty($filters['tipos']) && is_array($filters['tipos'])) {
            $query->whereIn('type', $filters['tipos']);
        }

        return $query->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getGlobal(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = InventoryMovement::query()
            ->with(['medication', 'user', 'sale', 'purchaseOrder']);

        if (! empty($filters['desde'])) {
            $query->whereDate('created_at', '>=', $filters['desde']);
        }

        if (! empty($filters['hasta'])) {
            $query->whereDate('created_at', '<=', $filters['hasta']);
        }

        if (! empty($filters['tipos']) && is_array($filters['tipos'])) {
            $query->whereIn('type', $filters['tipos']);
        }

        if (! empty($filters['medication_id'])) {
            $query->where('medication_id', (int) $filters['medication_id']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        return $query->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function recordSaleMovements(Sale $sale, int $userId, MovementType $type, ?string $reason = null): void
    {
        foreach ($sale->items as $item) {
            // Read post-mutation stock from DB (same transaction sees the already-mutated value).
            $currentStock = Medication::query()->whereKey($item->medication_id)->value('stock');
            $signedQty = $type === MovementType::SALE_OUT ? -$item->quantity : $item->quantity;

            InventoryMovement::create([
                'medication_id' => $item->medication_id,
                'type' => $type,
                'quantity' => $signedQty,
                'stock_before' => $currentStock - $signedQty,
                'stock_after' => $currentStock,
                'user_id' => $userId,
                'sale_id' => $sale->id,
                'reason' => $reason,
            ]);
        }
    }
}
