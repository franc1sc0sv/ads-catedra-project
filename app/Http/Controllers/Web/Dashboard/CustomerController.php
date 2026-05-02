<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use App\Services\Clientes\Contracts\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerServiceInterface $customerService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'incluir_inactivos']);
        $customers = $this->customerService->list($filters);

        return view('salesperson.dashboard.customer.index', [
            'customers' => $customers,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('salesperson.dashboard.customer.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'identification' => [
                'required',
                'string',
                'regex:/^\d{8}-\d{1}$/',
                'unique:customers,identification',
            ],
            'phone' => ['nullable', 'string', 'regex:/^(\+?503[\s-]?)?[267]\d{3}[\s-]?\d{4}$/'],
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string|max:500',
        ], [
            'identification.regex' => 'El formato del DUI debe ser ########-#',
            'phone.regex' => 'El teléfono debe iniciar con 2, 6 o 7 (####-####). Opcional: prefijo +503.',
        ]);

        $validated['is_frequent'] = $request->has('is_frequent');
        $validated['is_active'] = true;

        Customer::create($validated);

        return redirect()
            ->route('salesperson.clientes.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }

    public function show(?Customer $cliente = null): RedirectResponse|View
    {
        if ($cliente === null) {
            return redirect()->route('salesperson.clientes.index');
        }

        $sales = $cliente->sales()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('salesperson.dashboard.customer.show', [
            'customer' => $cliente,
            'sales' => $sales,
        ]);
    }

    public function edit(Customer $cliente): View
    {
        return view('salesperson.dashboard.customer.edit', ['customer' => $cliente]);
    }

    public function update(Request $request, Customer $cliente): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'identification' => [
                'required',
                'string',
                'regex:/^\d{8}-\d{1}$/',
                'unique:customers,identification,'.$cliente->id,
            ],
            'phone' => ['nullable', 'string', 'regex:/^(\+?503[\s-]?)?[267]\d{3}[\s-]?\d{4}$/'],
            'email' => 'nullable|email|max:255|unique:customers,email,'.$cliente->id,
            'address' => 'nullable|string|max:500',
        ], [
            'identification.regex' => 'El formato del DUI debe ser ########-#',
            'phone.regex' => 'Formato de teléfono no válido. Use ####-#### (opcional prefijo +503).',
        ]);

        // Lock DUI if the customer has sales history.
        if ($validated['identification'] !== $cliente->identification && $cliente->sales()->exists()) {
            return back()->withInput()
                ->with('error', 'No se puede modificar la identificación de un cliente con ventas registradas.');
        }

        $validated['is_frequent'] = $request->has('is_frequent');
        $validated['is_active'] = $request->has('is_active');

        $cliente->update($validated);

        return redirect()
            ->route('salesperson.clientes.index')
            ->with('success', 'Información del cliente actualizada.');
    }

    public function destroy(Customer $cliente): RedirectResponse
    {
        if ($cliente->sales()->exists()) {
            $this->customerService->softDelete($cliente);

            return redirect()
                ->route('salesperson.clientes.index')
                ->with('success', 'Cliente desactivado (tiene historial de ventas y no puede eliminarse).');
        }

        $cliente->delete();

        return redirect()
            ->route('salesperson.clientes.index')
            ->with('success', 'Cliente eliminado permanentemente.');
    }

    public function reactivate(Customer $cliente): RedirectResponse
    {
        $this->customerService->reactivate($cliente);

        return redirect()
            ->route('salesperson.clientes.index', ['incluir_inactivos' => '1'])
            ->with('success', "Cliente {$cliente->name} reactivado.");
    }

    public function toggleFrecuente(Customer $cliente): JsonResponse
    {
        $updated = $this->customerService->toggleFrecuente($cliente);

        return response()->json(['is_frequent' => $updated->is_frequent]);
    }

    public function showSale(Sale $sale): View
    {
        $sale->load(['customer', 'salesperson', 'items.medication']);

        return view('salesperson.ventas.show', ['sale' => $sale]);
    }
}
