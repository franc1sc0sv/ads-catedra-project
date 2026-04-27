<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Models\InventoryMovement;
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
}
