<?php

declare(strict_types=1);

namespace App\Services\Reportes;

use App\Enums\SaleStatus;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\Reportes\Contracts\ReporteVentasServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReporteVentasService implements ReporteVentasServiceInterface
{
    public function computeKPIs(array $filters): array
    {
        $completedQuery = $this->baseQuery($filters, SaleStatus::COMPLETED->value);
        $cancelledQuery = $this->baseQuery($filters, SaleStatus::CANCELLED->value);

        $countCompleted = (int) (clone $completedQuery)->count();
        $totalRevenue = (float) (clone $completedQuery)->sum('total');
        $countCancelled = (int) (clone $cancelledQuery)->count();
        $totalCancelled = (float) (clone $cancelledQuery)->sum('total');

        $avgTicket = $countCompleted > 0
            ? round($totalRevenue / $countCompleted, 2)
            : 0.0;

        return [
            'count_completed' => $countCompleted,
            'total_revenue' => round($totalRevenue, 2),
            'avg_ticket' => $avgTicket,
            'count_cancelled' => $countCancelled,
            'total_cancelled' => round($totalCancelled, 2),
        ];
    }

    public function listVentas(array $filters): LengthAwarePaginator
    {
        $status = $filters['status'] ?? SaleStatus::COMPLETED->value;
        $query = $this->baseQuery($filters, $status);

        return $query
            ->with(['customer', 'salesperson'])
            ->orderByDesc('sold_at')
            ->paginate(20)
            ->withQueryString();
    }

    public function topProductos(array $filters, int $limit = 10): Collection
    {
        // Force completed-only for top productos.
        $forced = $filters;
        $forced['status'] = SaleStatus::COMPLETED->value;

        $saleIds = $this->baseQuery($forced, SaleStatus::COMPLETED->value)->select('id');

        return SaleItem::query()
            ->join('medications', 'medications.id', '=', 'sale_items.medication_id')
            ->whereIn('sale_items.sale_id', $saleIds)
            ->groupBy('sale_items.medication_id', 'medications.name')
            ->selectRaw('sale_items.medication_id, medications.name as name, SUM(sale_items.quantity) as total_units')
            ->orderByDesc('total_units')
            ->limit($limit)
            ->get();
    }

    public function exportCsv(array $filters): StreamedResponse
    {
        $status = $filters['status'] ?? SaleStatus::COMPLETED->value;
        $query = $this->baseQuery($filters, $status)->with(['customer', 'salesperson']);

        $filename = 'reporte-ventas-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['FARMACIA: '.setting('nombre_farmacia', config('app.name'))]);
            fputcsv($out, ['DIRECCIÓN: '.setting('direccion_farmacia', '')]);
            fputcsv($out, []);
            fputcsv($out, [
                'ID', 'Fecha', 'Cliente', 'Vendedor', 'Método de pago',
                'Estado', 'Subtotal', 'Impuesto', 'Total',
            ]);

            $query->orderByDesc('sold_at')->chunk(500, function ($chunk) use ($out): void {
                /** @var Sale $sale */
                foreach ($chunk as $sale) {
                    fputcsv($out, [
                        $sale->id,
                        $sale->sold_at?->format('Y-m-d H:i:s'),
                        $sale->customer?->name ?? '',
                        $sale->salesperson?->name ?? '',
                        $sale->payment_method?->label() ?? '',
                        $sale->status?->label() ?? '',
                        $sale->subtotal,
                        $sale->tax,
                        $sale->total,
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
    private function baseQuery(array $filters, ?string $status = null): Builder
    {
        $query = Sale::query();

        $from = trim((string) ($filters['from'] ?? ''));
        $to = trim((string) ($filters['to'] ?? ''));

        // Default: month-start → today.
        if ($from === '' && $to === '') {
            $query->where('sold_at', '>=', now()->startOfMonth());
            $query->where('sold_at', '<=', now()->endOfDay());
        } else {
            if ($from !== '') {
                $query->where('sold_at', '>=', $from.' 00:00:00');
            }
            if ($to !== '') {
                $query->where('sold_at', '<=', $to.' 23:59:59');
            }
        }

        $paymentMethod = $filters['payment_method'] ?? null;
        if ($paymentMethod !== null && $paymentMethod !== '') {
            $query->where('payment_method', $paymentMethod);
        }

        $salespersonId = $filters['salesperson_id'] ?? null;
        if ($salespersonId !== null && $salespersonId !== '') {
            $query->where('salesperson_id', (int) $salespersonId);
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query;
    }
}
