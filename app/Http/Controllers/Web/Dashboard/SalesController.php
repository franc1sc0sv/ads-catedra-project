<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Medication;
use App\Models\Sale;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct(
        private readonly BitacoraServiceInterface $bitacora,
    ) {}

    public function index()
    {
        // 1. Cargamos las ventas paginadas
        $sales = Sale::with(['customer', 'salesperson'])->latest()->paginate(10);

        // 2. Cargamos los medicamentos con stock
        $medications = Medication::where('stock', '>', 0)->get();

        // 3. Cargamos los clientes
        $customers = Customer::all();

        // 4. NUEVO: Calculamos el total de ventas del día (excluyendo las anuladas)
        $todayTotal = Sale::whereDate('created_at', today())
            ->where('status', '!=', 'CANCELLED')
            ->sum('total');

        // 5. Pasamos las CUATRO variables a la vista
        return view('salesperson.dashboard.index', compact('sales', 'medications', 'customers', 'todayTotal'));
    }

    public function store(Request $request)
    {
        $allowAnonymous = (bool) setting('permite_venta_sin_cliente', true);

        $validated = $request->validate([
            'sold_at' => 'required|date|before_or_equal:now',
            'subtotal' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash', // Solo permite 'cash'
            'status' => 'required|string',
            'customer_id' => [$allowAnonymous ? 'nullable' : 'required', 'exists:customers,id'],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:medications,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $taxRate = (float) setting('tasa_iva', 0.13);
        $subtotal = round((float) $validated['subtotal'], 2);
        $tax = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $tax, 2);

        return DB::transaction(function () use ($validated, $subtotal, $tax, $total) {
            // 1. Crear Cabecera de Venta
            $sale = Sale::create([
                'sold_at' => $validated['sold_at'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'customer_id' => $validated['customer_id'] ?? null,
                'salesperson_id' => Auth::id(),
            ]);

            // 2. Procesar cada medicamento del carrito
            foreach ($validated['items'] as $item) {
                $medication = Medication::findOrFail($item['product_id']);

                if ($medication->stock < $item['quantity']) {
                    throw new \Exception('Stock insuficiente para: '.$medication->name);
                }

                // Descontar inventario
                $medication->decrement('stock', $item['quantity']);

                // Crear detalle usando la relación items() de tu modelo Sale
                $sale->items()->create([
                    'medication_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('salesperson.dashboard')->with('success', 'Venta registrada con éxito');
        });
    }

    public function create()
    {
        $medications = Medication::where('stock', '>', 0)->get();
        $customers = Customer::all();

        return view('salesperson.dashboard.create', compact('medications', 'customers'));
    }

    public function cancel(Sale $sale)
    {
        // Comparación usando el Enum
        if ($sale->status === SaleStatus::CANCELLED) {
            return back()->with('error', 'Esta venta ya ha sido anulada.');
        }

        $sale->update([
            'status' => SaleStatus::CANCELLED, // Laravel se encarga de guardar 'cancelled' en la DB
        ]);

        $this->bitacora->log('VENTA_CANCELADA', Auth::id(), 'sales', (string) $sale->id, [
            'sale_id' => $sale->id,
            'reason' => $sale->cancellation_reason,
        ]);

        return back()->with('success', 'Venta #'.$sale->id.' anulada correctamente.');
    }
}
