<?php

declare(strict_types=1);

namespace App\Services\Reportes\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ReporteMovimientosServiceInterface
{
    /**
     * Filtered, paginated list of inventory movements.
     *
     * Filters:
     *  - type (MovementType value)
     *  - medication_id
     *  - user_id
     *  - from / to (Y-m-d)
     *
     * Default: today's movements when no date range provided.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getRows(array $filters): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(array $filters): StreamedResponse;
}
