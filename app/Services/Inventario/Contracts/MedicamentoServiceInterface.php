<?php

declare(strict_types=1);

namespace App\Services\Inventario\Contracts;

use App\Models\Medication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MedicamentoServiceInterface
{
    /**
     * Listado paginado de medicamentos con filtros.
     *
     * @param  array<string, mixed>  $filters  search, category, supplier_id, is_active
     */
    public function listar(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Crea un medicamento. Si $data contiene `stock_inicial > 0`, lo aplica
     * generando un movimiento `MANUAL_ADJUST` dentro de la misma transacción.
     *
     * @param  array<string, mixed>  $data
     */
    public function crear(array $data): Medication;

    /**
     * Actualiza un medicamento. Nunca modifica el stock por esta vía.
     *
     * @param  array<string, mixed>  $data
     */
    public function actualizar(Medication $medication, array $data): Medication;

    /**
     * Marca el medicamento como inactivo. Falla si hay ventas EN_PROCESO con él.
     *
     * @throws \DomainException
     */
    public function desactivar(Medication $medication): Medication;

    public function reactivar(Medication $medication): Medication;

    /**
     * Devuelve true si la fecha de vencimiento es anterior a hoy.
     */
    public function estaVencido(Medication $medication): bool;
}
