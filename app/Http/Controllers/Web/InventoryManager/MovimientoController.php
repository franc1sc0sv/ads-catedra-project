<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Enums\MovementType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\User;
use App\Services\Inventario\Contracts\MovimientoServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MovimientoController extends Controller
{
    public function __construct(
        private readonly MovimientoServiceInterface $movimientos,
    ) {}

    public function index(Request $request, ?Medication $medicamento = null): View|RedirectResponse
    {
        if (auth()->user()->role === UserRole::ADMINISTRATOR && $medicamento === null) {
            return redirect()->route('admin.reportes.movimientos.index', $request->query());
        }

        $validated = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'tipos' => ['nullable', 'array'],
            'tipos.*' => ['string'],
            'medication_id' => ['nullable', 'integer'],
            'user_id' => ['nullable', 'integer'],
        ]);

        $filters = [
            'desde' => $validated['desde'] ?? null,
            'hasta' => $validated['hasta'] ?? null,
            'tipos' => $validated['tipos'] ?? [],
        ];

        if ($medicamento === null || $medicamento->id === null) {
            $globalFilters = array_merge($filters, [
                'medication_id' => $request->integer('medication_id') ?: null,
                'user_id' => $request->integer('user_id') ?: null,
            ]);

            $movimientos = $this->movimientos->getGlobal($globalFilters);
            $medicamentos = Medication::query()->orderBy('name')->get(['id', 'name']);
            $usuarios = User::query()->orderBy('name')->get(['id', 'name']);

            return view('inventario.movimientos-index', [
                'movimientos' => $movimientos,
                'medicamentos' => $medicamentos,
                'usuarios' => $usuarios,
                'filters' => $globalFilters,
                'tiposDisponibles' => MovementType::cases(),
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

    public function global(Request $request): View
    {
        $validated = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'tipos' => ['nullable', 'array'],
            'tipos.*' => ['string'],
            'medication_id' => ['nullable', 'integer', 'exists:medications,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $filters = [
            'desde' => $validated['desde'] ?? null,
            'hasta' => $validated['hasta'] ?? null,
            'tipos' => $validated['tipos'] ?? [],
            'medication_id' => $validated['medication_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ];

        $movimientos = $this->movimientos->getGlobal($filters);
        $medicamentos = Medication::query()->orderBy('name')->get();
        $usuarios = User::query()->orderBy('name')->get();
        $tiposDisponibles = MovementType::cases();

        return view('inventario.movimientos-global', compact(
            'movimientos', 'filters', 'medicamentos', 'usuarios', 'tiposDisponibles'
        ));
    }
}
