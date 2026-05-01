<?php

declare(strict_types=1);

namespace App\Services\Reportes\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ReporteInventarioServiceInterface
{
    /**
     * KPIs:
     *  - active_count
     *  - inventory_value
     *  - low_stock_count
     *  - expiring_soon_count
     *  - expired_count
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, float|int>
     */
    public function computeKPIs(array $filters): array;

    /**
     * Filtered, paginated rows of medications.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRows(array $filters): LengthAwarePaginator;

    /**
     * Stream a CSV of all rows matching the filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(array $filters): StreamedResponse;
}
