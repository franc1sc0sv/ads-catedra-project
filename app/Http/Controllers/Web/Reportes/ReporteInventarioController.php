<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Reportes;

use App\Enums\MedicationCategory;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\Reportes\Contracts\ReporteInventarioServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReporteInventarioController extends Controller
{
    public function __construct(
        private readonly ReporteInventarioServiceInterface $service,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->validateFilters($request);

        $kpis = $this->service->computeKPIs($filters);
        $rows = $this->service->getRows($filters);

        $view = auth()->user()->role === UserRole::INVENTORY_MANAGER
            ? 'inventory-manager.reportes.inventario'
            : 'admin.reportes.inventario';

        return view($view, [
            'filters' => $filters,
            'kpis' => $kpis,
            'rows' => $rows,
            'categories' => MedicationCategory::cases(),
            'suppliers' => Supplier::query()->orderBy('company_name')->get(['id', 'company_name']),
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
            'category' => ['nullable', 'string'],
            'supplier_id' => ['nullable', 'integer'],
            'stock_state' => ['nullable', 'string', 'in:normal,low,zero,expired'],
            'expiry_window_days' => ['nullable', 'integer', 'in:30,60,90'],
        ]);
    }
}
