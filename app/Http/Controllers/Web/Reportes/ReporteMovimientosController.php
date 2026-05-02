<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Reportes;

use App\Enums\MovementType;
use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\User;
use App\Services\Inventario\Contracts\MovimientoServiceInterface;
use App\Services\Reportes\Contracts\ReporteMovimientosServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReporteMovimientosController extends Controller
{
    public function __construct(
        private readonly ReporteMovimientosServiceInterface $service,
        private readonly MovimientoServiceInterface $movimientos,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->validateFilters($request);

        $rows = $this->service->getRows($filters);

        return view('admin.reportes.movimientos', [
            'filters' => $filters,
            'rows' => $rows,
            'tipos' => MovementType::cases(),
            'medicamentos' => Medication::query()->orderBy('name')->get(['id', 'name']),
            'usuarios' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Request $request, Medication $medicamento): View
    {
        $filters = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'tipos' => ['nullable', 'array'],
            'tipos.*' => ['string'],
        ]);

        $filters = array_merge(['desde' => null, 'hasta' => null, 'tipos' => []], $filters);

        return view('admin.inventario.movimientos', [
            'medicamento' => $medicamento,
            'movimientos' => $this->movimientos->getByMedicamento($medicamento->id, $filters),
            'filters' => $filters,
            'tiposDisponibles' => MovementType::cases(),
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
            'type' => ['nullable', 'string'],
            'medication_id' => ['nullable', 'integer'],
            'user_id' => ['nullable', 'integer'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);
    }
}
