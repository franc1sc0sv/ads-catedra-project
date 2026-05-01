<?php

declare(strict_types=1);

namespace App\Services\Reportes;

use App\Models\InventoryMovement;
use App\Services\Reportes\Contracts\ReporteMovimientosServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReporteMovimientosService implements ReporteMovimientosServiceInterface
{
    public function getRows(array $filters): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->with(['medication', 'user', 'sale', 'purchaseOrder'])
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();
    }

    public function exportCsv(array $filters): StreamedResponse
    {
        $query = $this->baseQuery($filters)
            ->with(['medication', 'user', 'sale', 'purchaseOrder'])
            ->orderByDesc('created_at');

        $filename = 'reporte-movimientos-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['FARMACIA: '.setting('nombre_farmacia', config('app.name'))]);
            fputcsv($out, ['DIRECCIÓN: '.setting('direccion_farmacia', '')]);
            fputcsv($out, []);
            fputcsv($out, [
                'ID', 'Fecha', 'Tipo', 'Medicamento', 'Cantidad',
                'Stock antes', 'Stock después', 'Usuario',
                'Venta', 'Pedido', 'Motivo',
            ]);

            $query->chunk(500, function ($chunk) use ($out): void {
                foreach ($chunk as $mov) {
                    fputcsv($out, [
                        $mov->id,
                        $mov->created_at?->format('Y-m-d H:i:s'),
                        $mov->type?->label() ?? '',
                        $mov->medication?->name ?? '',
                        $mov->quantity,
                        $mov->stock_before,
                        $mov->stock_after,
                        $mov->user?->name ?? '',
                        $mov->sale_id ?? '',
                        $mov->purchase_order_id ?? '',
                        $mov->reason ?? '',
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
    private function baseQuery(array $filters): Builder
    {
        $query = InventoryMovement::query();

        $type = $filters['type'] ?? null;
        if ($type !== null && $type !== '') {
            $query->where('type', $type);
        }

        $medicationId = $filters['medication_id'] ?? null;
        if ($medicationId !== null && $medicationId !== '') {
            $query->where('medication_id', (int) $medicationId);
        }

        $userId = $filters['user_id'] ?? null;
        if ($userId !== null && $userId !== '') {
            $query->where('user_id', (int) $userId);
        }

        $from = trim((string) ($filters['from'] ?? ''));
        $to = trim((string) ($filters['to'] ?? ''));

        if ($from === '' && $to === '') {
            $query->whereDate('created_at', now()->toDateString());
        } else {
            if ($from !== '') {
                $query->where('created_at', '>=', $from.' 00:00:00');
            }
            if ($to !== '') {
                $query->where('created_at', '<=', $to.' 23:59:59');
            }
        }

        return $query;
    }
}
