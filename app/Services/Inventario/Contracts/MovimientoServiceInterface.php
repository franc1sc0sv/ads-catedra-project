<?php

declare(strict_types=1);

namespace App\Services\Inventario\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MovimientoServiceInterface
{
    /**
     * Devuelve el historial paginado de movimientos para un medicamento.
     *
     * @param  array{desde?: ?string, hasta?: ?string, tipos?: array<int, string>}  $filters
     */
    public function getByMedicamento(int $medicamentoId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
