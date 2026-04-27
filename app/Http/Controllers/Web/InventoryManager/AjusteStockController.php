<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Enums\MovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\AjusteStockRequest;
use App\Models\Medication;
use App\Services\Inventario\Contracts\StockServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AjusteStockController extends Controller
{
    public function __construct(
        private readonly StockServiceInterface $stock,
    ) {}

    public function create(Request $request): View
    {
        $allowedTypes = [
            MovementType::MANUAL_ADJUST,
            MovementType::EXPIRY_WRITEOFF,
            MovementType::CUSTOMER_RETURN,
        ];

        $defaultType = $request->string('motivo')->toString() === 'vencimiento'
            ? MovementType::EXPIRY_WRITEOFF->value
            : MovementType::MANUAL_ADJUST->value;

        return view('inventario.ajustes.create', [
            'medicamentos' => Medication::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'tipos' => $allowedTypes,
            'preselected' => $request->integer('medicamento_id') ?: null,
            'defaultType' => $defaultType,
        ]);
    }

    public function store(AjusteStockRequest $request): RedirectResponse
    {
        $medication = Medication::query()->findOrFail($request->integer('medication_id'));

        try {
            $this->stock->ajustar($medication, [
                'type' => (string) $request->validated('type'),
                'quantity' => (int) $request->validated('quantity'),
                'reason' => (string) $request->validated('reason'),
            ]);
        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()
            ->route('inventory-manager.catalogo.show', $medication)
            ->with('status', 'Ajuste registrado correctamente.');
    }
}
