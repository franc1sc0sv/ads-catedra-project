<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Proveedores\StoreProveedorRequest;
use App\Http\Requests\Proveedores\UpdateProveedorRequest;
use App\Models\Supplier;
use App\Services\Proveedores\Contracts\ProveedorServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

final class ProveedorController extends Controller
{
    public function __construct(
        private readonly ProveedorServiceInterface $proveedorService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $search = is_string($search) && $search !== '' ? $search : null;

        $suppliers = $this->proveedorService->list($search);

        return view('inventory-manager.proveedores.index', [
            'suppliers' => $suppliers,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('inventory-manager.proveedores.create');
    }

    public function store(StoreProveedorRequest $request): RedirectResponse
    {
        $this->proveedorService->create($request->validated());

        return redirect()
            ->route('inventory-manager.proveedores.index')
            ->with('status', 'Proveedor creado correctamente.');
    }

    public function edit(Supplier $proveedor): View
    {
        return view('inventory-manager.proveedores.edit', [
            'supplier' => $proveedor,
        ]);
    }

    public function update(UpdateProveedorRequest $request, Supplier $proveedor): RedirectResponse
    {
        $this->proveedorService->update($proveedor, $request->validated());

        return redirect()
            ->route('inventory-manager.proveedores.index')
            ->with('status', 'Proveedor actualizado correctamente.');
    }

    public function toggle(Supplier $proveedor): RedirectResponse
    {
        $updated = $this->proveedorService->toggleActive($proveedor);

        $message = $updated->is_active
            ? 'Proveedor activado.'
            : 'Proveedor desactivado.';

        return redirect()
            ->route('inventory-manager.proveedores.index')
            ->with('status', $message);
    }

    public function destroy(Supplier $proveedor): RedirectResponse
    {
        try {
            $this->proveedorService->delete($proveedor);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('inventory-manager.proveedores.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('inventory-manager.proveedores.index')
            ->with('status', 'Proveedor eliminado correctamente.');
    }
}
