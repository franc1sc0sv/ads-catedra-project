<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $customers = Customer::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('identification', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('salesperson.dashboard.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('salesperson.dashboard.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|min:3|max:255',
            'identification' => [
                'required', 
                'string', 
                'regex:/^\d{8}-\d{1}$/', 
                'unique:customers,identification'
            ],
            'phone'          => ['nullable', 'string', 'regex:/^[267]\d{3}-?\d{4}$/'],
            'email'          => 'nullable|email|max:255|unique:customers,email',
            'address'        => 'nullable|string|max:500',
        ], [
            'identification.regex' => 'El formato del DUI debe ser ########-#',
            'phone.regex'          => 'El teléfono debe iniciar con 2, 6 o 7 (####-####).',
        ]);

        try {
            $validated['is_frequent'] = $request->has('is_frequent');
            $validated['is_active']   = $request->has('is_active');

            Customer::create($validated);

            return redirect()
                ->route('salesperson.clientes.index')
                ->with('success', 'Cliente registrado exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al crear cliente: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al procesar el registro.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $cliente)
    {
        $cliente->load(['sales' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('salesperson.dashboard.customer.show', ['customer' => $cliente]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $cliente)
    {
        return view('salesperson.dashboard.customer.edit', ['customer' => $cliente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $cliente)
    {
        $validated = $request->validate([
            'name'           => 'required|string|min:3|max:255',
            'identification' => [
                'required', 
                'string', 
                'regex:/^\d{8}-\d{1}$/', 
                'unique:customers,identification,' . $cliente->id
            ],
            'phone'          => ['nullable', 'string', 'regex:/^[267]\d{3}-?\d{4}$/'],
            'email'          => 'nullable|email|max:255|unique:customers,email,' . $cliente->id,
            'address'        => 'nullable|string|max:500',
        ], [
            'identification.regex' => 'El formato del DUI debe ser ########-#',
            'phone.regex'          => 'Formato de teléfono no válido.',
        ]);

        try {
            $validated['is_frequent'] = $request->has('is_frequent');
            $validated['is_active']   = $request->has('is_active');

            $cliente->update($validated);

            return redirect()
                ->route('salesperson.clientes.index')
                ->with('success', 'Información del cliente actualizada.');
        } catch (\Exception $e) {
            Log::error("Error al actualizar cliente ID {$cliente->id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'No se pudieron guardar los cambios.');
        }
    }

    /**
     * Remove the specified resource from storage (SOLO ADMIN).
     */
    public function destroy(Customer $cliente)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return back()->with('error', 'Acceso denegado. Solo los administradores pueden eliminar registros.');
        }

        try {
            if ($cliente->sales()->count() > 0) {
                return back()->with('error', 'No se puede eliminar un cliente con historial de ventas.');
            }

            $cliente->delete();
            return redirect()
                ->route('salesperson.clientes.index')
                ->with('success', 'El registro ha sido eliminado permanentemente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar cliente: " . $e->getMessage());
            return back()->with('error', 'Error técnico al intentar eliminar el registro.');
        }
    }
}