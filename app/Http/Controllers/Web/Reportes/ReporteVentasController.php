<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Reportes;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Reportes\Contracts\ReporteVentasServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReporteVentasController extends Controller
{
    public function __construct(
        private readonly ReporteVentasServiceInterface $service,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->validateFilters($request);

        $kpis = $this->service->computeKPIs($filters);
        $ventas = $this->service->listVentas($filters);
        $topProductos = $this->service->topProductos($filters);

        return view('admin.reportes.ventas', [
            'filters' => $filters,
            'kpis' => $kpis,
            'ventas' => $ventas,
            'topProductos' => $topProductos,
            'paymentMethods' => PaymentMethod::cases(),
            'statuses' => SaleStatus::cases(),
            'salespersons' => User::query()
                ->where('role', UserRole::SALESPERSON->value)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validateFilters($request);

        return $this->service->exportCsv($filters);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'payment_method' => ['nullable', 'string'],
            'salesperson_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string'],
        ]);
    }
}
