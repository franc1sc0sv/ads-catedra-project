<?php

declare(strict_types=1);

namespace App\Services\Inventario\Contracts;

use App\Models\InventoryMovement;
use App\Models\Medication;

interface StockServiceInterface
{
    /**
     * Aplica un ajuste de stock dentro de una transacción atómica:
     * lockea la fila, calcula stock_after, valida, actualiza el medicamento
     * y crea un InventoryMovement inmutable.
     *
     * @param  array{type: string, quantity: int, reason: string}  $data
     *
     * @throws \DomainException si el stock resultante quedaría negativo.
     */
    public function ajustar(Medication $medication, array $data): InventoryMovement;
}
