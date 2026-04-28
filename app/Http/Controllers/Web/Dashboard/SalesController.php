<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Medication;
use App\Enums\SaleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        // 1. Cargamos las ventas paginadas
        $sales = Sale::with(['customer', 'salesperson'])->latest()->paginate(10);

        // 2. Cargamos los medicamentos con stock
        $medications = Medication::where('stock', '>', 0)->get();

        // 3. Cargamos los clientes
        $customers = \App\Models\Customer::all();

        // 4. NUEVO: Calculamos el total de ventas del día (excluyendo las anuladas)
        $todayTotal = Sale::whereDate('created_at', today())
            ->where('status', '!=', 'CANCELLED')
            ->sum('total');

        // 5. Pasamos las CUATRO variables a la vista
        return view('salesperson.dashboard.index', compact('sales', 'medications', 'customers', 'todayTotal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sold_at'            => 'required|date|before_or_equal:now',
            'subtotal'           => 'required|numeric|min:0',
            'tax'                => 'required|numeric|min:0',
            'total'              => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash', // Solo permite 'cash'
            'status'             => 'required|string',
            'customer_id'        => 'required|exists:customers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:medications,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            // 1. Crear Cabecera de Venta
            $sale = Sale::create([
                'sold_at'        => $validated['sold_at'],
                'subtotal'       => $validated['subtotal'],
                'tax'            => $validated['tax'],
                'total'          => $validated['total'],
                'payment_method' => $validated['payment_method'],
                'status'         => $validated['status'],
                'customer_id'    => $validated['customer_id'],
                'salesperson_id' => Auth::id(),
            ]);

            // 2. Procesar cada medicamento del carrito
            foreach ($validated['items'] as $item) {
                $medication = Medication::findOrFail($item['product_id']);

                if ($medication->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para: " . $medication->name);
                }

                // Descontar inventario
                $medication->decrement('stock', $item['quantity']);

                // Crear detalle usando la relación items() de tu modelo Sale
                $sale->items()->create([
                    'medication_id' => $item['product_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'line_total'    => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('salesperson.dashboard')->with('success', 'Venta registrada con éxito');
        });
    }

    public function create()
    {
        $medications = Medication::where('stock', '>', 0)->get();
        $customers = \App\Models\Customer::all();

        return view('salesperson.dashboard.create', compact('medications', 'customers'));
    }

    public function cancel(Sale $sale)
    {
        // Comparación usando el Enum
        if ($sale->status === SaleStatus::CANCELLED) {
            return back()->with('error', 'Esta venta ya ha sido anulada.');
        }

        $sale->update([
            'status' => SaleStatus::CANCELLED // Laravel se encarga de guardar 'cancelled' en la DB
        ]);

        return back()->with('success', 'Venta #' . $sale->id . ' anulada correctamente.');
    }
}
