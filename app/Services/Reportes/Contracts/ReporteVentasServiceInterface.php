<?php

declare(strict_types=1);

namespace App\Services\Reportes\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ReporteVentasServiceInterface
{
    /**
     * Returns the 5 KPIs as an associative array:
     *  - count_completed
     *  - total_revenue
     *  - avg_ticket
     *  - count_cancelled
     *  - total_cancelled
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, float|int>
     */
    public function computeKPIs(array $filters): array;

    /**
     * Paginated list of sales matching the filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function listVentas(array $filters): LengthAwarePaginator;

    /**
     * Top medicamentos por unidades vendidas (ventas completed only).
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, object>
     */
    public function topProductos(array $filters, int $limit = 10): Collection;

    /**
     * Stream a CSV download with all sales matching the filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(array $filters): StreamedResponse;
}
