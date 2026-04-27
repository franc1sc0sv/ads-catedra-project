<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Enums\MovementType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Services\Inventario\Contracts\MovimientoServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MovimientoController extends Controller
{
    public function __construct(
        private readonly MovimientoServiceInterface $movimientos,
    ) {}

    public function index(Request $request, ?Medication $medicamento = null): View
    {
        $validated = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'tipos' => ['nullable', 'array'],
            'tipos.*' => ['string'],
        ]);

        $filters = [
            'desde' => $validated['desde'] ?? null,
            'hasta' => $validated['hasta'] ?? null,
            'tipos' => $validated['tipos'] ?? [],
        ];

        if ($medicamento === null || $medicamento->id === null) {
            // Vista global: pedir al usuario que seleccione un medicamento.
            $medicamentos = Medication::query()->orderBy('name')->get();

            $view = match (auth()->user()->role) {
                UserRole::ADMINISTRATOR => 'admin.inventario.movimientos-index',
                default => 'inventario.movimientos-index',
            };

            return view($view, [
                'medicamentos' => $medicamentos,
            ]);
        }

        $page = $this->movimientos->getByMedicamento($medicamento->id, $filters);

        $view = match (auth()->user()->role) {
            UserRole::ADMINISTRATOR => 'admin.inventario.movimientos',
            default => 'inventario.movimientos',
        };

        return view($view, [
            'medicamento' => $medicamento,
            'movimientos' => $page,
            'filters' => $filters,
            'tiposDisponibles' => MovementType::cases(),
        ]);
    }
}
