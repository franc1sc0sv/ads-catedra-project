<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Enums\MedicationCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\CreateMedicamentoRequest;
use App\Http\Requests\Inventario\UpdateMedicamentoRequest;
use App\Models\Medication;
use App\Models\Supplier;
use App\Services\Inventario\Contracts\MedicamentoServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MedicamentoController extends Controller
{
    public function __construct(
        private readonly MedicamentoServiceInterface $medicamentos,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString() ?: null,
            'category' => $request->string('category')->toString() ?: null,
            'supplier_id' => $request->integer('supplier_id') ?: null,
            'is_active' => $request->has('is_active') && $request->input('is_active') !== ''
                ? (bool) $request->input('is_active')
                : null,
        ];

        return view('inventario.medicamentos.index', [
            'medicamentos' => $this->medicamentos->listar($filters),
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'categorias' => MedicationCategory::cases(),
            'filters' => $filters,
        ]);
    }

    public function show(Medication $medicamento): View
    {
        $medicamento->load('supplier');

        return view('inventario.medicamentos.show', [
            'medicamento' => $medicamento,
            'estaVencido' => $this->medicamentos->estaVencido($medicamento),
        ]);
    }

    public function create(): View
    {
        return view('inventario.medicamentos.create', [
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'categorias' => MedicationCategory::cases(),
        ]);
    }

    public function store(CreateMedicamentoRequest $request): RedirectResponse
    {
        $medicamento = $this->medicamentos->crear($request->validated());

        return redirect()
            ->route('inventory-manager.catalogo.show', $medicamento)
            ->with('status', 'Medicamento creado correctamente.');
    }

    public function edit(Medication $medicamento): View
    {
        return view('inventario.medicamentos.edit', [
            'medicamento' => $medicamento,
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'categorias' => MedicationCategory::cases(),
        ]);
    }

    public function update(UpdateMedicamentoRequest $request, Medication $medicamento): RedirectResponse
    {
        $this->medicamentos->actualizar($medicamento, $request->validated());

        return redirect()
            ->route('inventory-manager.catalogo.show', $medicamento)
            ->with('status', 'Medicamento actualizado.');
    }

    public function destroy(Medication $medicamento): RedirectResponse
    {
        try {
            $this->medicamentos->desactivar($medicamento);
        } catch (\DomainException $e) {
            return redirect()
                ->route('inventory-manager.catalogo.show', $medicamento)
                ->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('inventory-manager.catalogo.index')
            ->with('status', 'Medicamento desactivado.');
    }

    public function restore(Medication $medicamento): RedirectResponse
    {
        $this->medicamentos->reactivar($medicamento);

        return redirect()
            ->route('inventory-manager.catalogo.show', $medicamento)
            ->with('status', 'Medicamento reactivado.');
    }
}
