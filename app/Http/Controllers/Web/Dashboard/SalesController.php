<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Medication;
use App\Models\Sale;
use App\Services\Ventas\Contracts\VentaServiceInterface;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function __construct(
        private readonly VentaServiceInterface $ventas,
    ) {}

    public function index()
    {
        $sales = Sale::with(['customer', 'salesperson'])->latest()->paginate(10);
        $medications = Medication::where('stock', '>', 0)->get();
        $customers = Customer::all();

        $todayTotal = Sale::whereDate('created_at', today())
            ->where('status', '!=', SaleStatus::CANCELLED->value)
            ->sum('total');

        return view('salesperson.dashboard.index', compact('sales', 'medications', 'customers', 'todayTotal'));
    }

    public function create()
    {
        $medications = Medication::where('stock', '>', 0)->get();
        $customers = Customer::all();

        return view('salesperson.dashboard.create', compact('medications', 'customers'));
    }

    public function store(Request $request)
    {
        $allowAnonymous = (bool) setting('permite_venta_sin_cliente', true);

        $validated = $request->validate([
            'sold_at' => 'required|date|before_or_equal:now',
            'customer_id' => [$allowAnonymous ? 'nullable' : 'required', 'exists:customers,id'],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:medications,id',
            'items.*.quantity' => 'required|integer|min:1',
            'doctor_name' => 'nullable|string|max:255',
            'doctor_license' => 'nullable|string|max:255',
        ]);

        try {
            $this->ventas->registerSale($validated, (int) Auth::id());
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('salesperson.dashboard')
            ->with('success', 'Venta registrada con éxito.');
    }

    public function show(Sale $sale): View
    {
        return view('ventas.show', [
            'sale' => $sale->load(['customer', 'salesperson', 'items.medication']),
        ]);
    }

    public function cancel(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'cancellation_reason' => 'required|string|min:3|max:255',
        ]);

        try {
            $this->ventas->cancelSale($sale, $data['cancellation_reason'], (int) Auth::id());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Venta #'.$sale->id.' anulada correctamente.');
    }
}
