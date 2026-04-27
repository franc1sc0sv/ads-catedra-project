<?php

declare(strict_types=1);

namespace App\Services\Inventario\Contracts;

use Illuminate\Support\Collection;

interface AlertasStockServiceInterface
{
    /**
     * Medicamentos donde stock < (min_stock + umbral_aviso_stock_bajo),
     * ordenados por urgencia ascendente (ratio stock/min_stock).
     */
    public function getBajoMinimo(): Collection;

    /**
     * Medicamentos cuyo expires_at cae dentro de los próximos
     * `dias_alerta_vencimiento` días, ordenados por fecha asc.
     */
    public function getProximosVencer(): Collection;
}
