<?php

declare(strict_types=1);

namespace App\Services\Reportes;

use App\Models\Medication;
use App\Services\Reportes\Contracts\ReporteInventarioServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReporteInventarioService implements ReporteInventarioServiceInterface
{
    public function computeKPIs(array $filters): array
    {
        $window = $this->resolveExpiryWindowDays($filters);
        $today = now()->toDateString();
        $windowEnd = now()->addDays($window)->toDateString();

        $activeCount = (int) $this->applyFiltros(Medication::query(), $filters)
            ->where('is_active', true)
            ->count();

        $inventoryValue = (float) $this->applyFiltros(Medication::query(), $filters)
            ->where('is_active', true)
            ->selectRaw('COALESCE(SUM(price * stock), 0) as total')
            ->value('total');

        $lowStockCount = (int) $this->applyFiltros(Medication::query(), $filters)
            ->where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $expiringSoonCount = (int) $this->applyFiltros(Medication::query(), $filters)
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$today, $windowEnd])
            ->count();

        $expiredCount = (int) $this->applyFiltros(Medication::query(), $filters)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $today)
            ->count();

        return [
            'active_count' => $activeCount,
            'inventory_value' => round($inventoryValue, 2),
            'low_stock_count' => $lowStockCount,
            'expiring_soon_count' => $expiringSoonCount,
            'expired_count' => $expiredCount,
        ];
    }

    public function getRows(array $filters): LengthAwarePaginator
    {
        return $this->applyFiltros(Medication::query()->with('supplier'), $filters)
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
    }

    public function exportCsv(array $filters): StreamedResponse
    {
        $query = $this->applyFiltros(Medication::query()->with('supplier'), $filters)
            ->orderBy('name');

        $filename = 'reporte-inventario-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['FARMACIA: '.setting('nombre_farmacia', config('app.name'))]);
            fputcsv($out, ['DIRECCIÓN: '.setting('direccion_farmacia', '')]);
            fputcsv($out, []);
            fputcsv($out, [
                'ID', 'Nombre', 'Categoría', 'Proveedor', 'Precio',
                'Stock', 'Mínimo', 'Vence', 'Activo',
            ]);

            $query->chunk(500, function ($chunk) use ($out): void {
                foreach ($chunk as $med) {
                    fputcsv($out, [
                        $med->id,
                        $med->name,
                        $med->category?->label() ?? '',
                        $med->supplier?->company_name ?? '',
                        $med->price,
                        $med->stock,
                        $med->min_stock,
                        $med->expires_at?->format('Y-m-d') ?? '',
                        $med->is_active ? 'Sí' : 'No',
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFiltros(Builder $query, array $filters): Builder
    {
        $category = $filters['category'] ?? null;
        if ($category !== null && $category !== '') {
            $query->where('category', $category);
        }

        $supplierId = $filters['supplier_id'] ?? null;
        if ($supplierId !== null && $supplierId !== '') {
            $query->where('supplier_id', (int) $supplierId);
        }

        $stockState = $filters['stock_state'] ?? null;
        $today = now()->toDateString();

        if ($stockState === 'low') {
            $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
        } elseif ($stockState === 'zero') {
            $query->where('stock', '<=', 0);
        } elseif ($stockState === 'normal') {
            $query->whereColumn('stock', '>', 'min_stock');
        } elseif ($stockState === 'expired') {
            $query->whereNotNull('expires_at')->where('expires_at', '<', $today);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function resolveExpiryWindowDays(array $filters): int
    {
        $value = (int) ($filters['expiry_window_days'] ?? 30);

        return match ($value) {
            60 => 60,
            90 => 90,
            default => 30,
        };
    }
}
