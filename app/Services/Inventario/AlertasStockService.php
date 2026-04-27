<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Models\Medication;
use App\Models\Setting;
use App\Services\Inventario\Contracts\AlertasStockServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class AlertasStockService implements AlertasStockServiceInterface
{
    public function getBajoMinimo(): Collection
    {
        $umbral = $this->intSetting('umbral_aviso_stock_bajo', 0);

        return Medication::query()
            ->with('supplier')
            ->where('is_active', true)
            ->whereRaw('stock < (min_stock + ?)', [$umbral])
            ->orderByRaw('CASE WHEN min_stock = 0 THEN stock ELSE (stock::float / min_stock) END ASC')
            ->get();
    }

    public function getProximosVencer(): Collection
    {
        $dias = $this->intSetting('dias_alerta_vencimiento', 30);

        $hoy = Carbon::today();
        $limite = Carbon::today()->addDays($dias);

        return Medication::query()
            ->with('supplier')
            ->where('is_active', true)
            ->whereBetween('expires_at', [$hoy->toDateString(), $limite->toDateString()])
            ->orderBy('expires_at')
            ->get();
    }

    private function intSetting(string $key, int $default): int
    {
        $setting = Setting::query()->where('key', $key)->first();

        if ($setting === null) {
            return $default;
        }

        $value = $setting->typedValue();

        return is_int($value) ? $value : (int) $value;
    }
}
