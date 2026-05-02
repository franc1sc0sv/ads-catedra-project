<?php

declare(strict_types=1);

namespace App\Services\Inventario\Contracts;

use App\Enums\MovementType;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MovimientoServiceInterface
{
    /**
     * Devuelve el historial paginado de movimientos para un medicamento.
     *
     * @param  array{desde?: ?string, hasta?: ?string, tipos?: array<int, string>}  $filters
     */
    public function getByMedicamento(int $medicamentoId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Devuelve el historial global paginado de todos los movimientos.
     *
     * @param  array{desde?: ?string, hasta?: ?string, tipos?: array<int, string>, medication_id?: ?int, user_id?: ?int}  $filters
     */
    public function getGlobal(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Escribe un InventoryMovement por cada ítem de la venta.
     * Debe llamarse dentro de un DB::transaction abierto y DESPUÉS de que
     * el llamador haya mutado el stock (increment/decrement).
     */
    public function recordSaleMovements(Sale $sale, int $userId, MovementType $type, ?string $reason = null): void;
}
