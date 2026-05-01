<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Medication;
use App\Models\Customer;
use App\Models\Prescription;
use App\Models\SalePrescription;
use App\Enums\SaleStatus;
use App\Enums\PrescriptionStatus;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function __construct(
        private readonly BitacoraServiceInterface $bitacora,
    ) {}

    public function index()
    {
        $sales = Sale::with(['customer', 'salesperson'])->latest()->paginate(10);
        $medications = Medication::where('stock', '>', 0)->get();
        $customers = Customer::all();

        $todayTotal = Sale::whereDate('created_at', today())
            ->where('status', '!=', SaleStatus::CANCELLED)
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
        $validated = $request->validate([
            'total'              => 'required|numeric|min:0',
            'customer_id'        => 'required|exists:customers,id',
            'items'              => 'required|array|min:1',
            'doctor_name'        => 'nullable|string',
            'doctor_license'     => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // 1. Determinar si requiere receta
                $requiresPharmacist = false;
                foreach ($validated['items'] as $item) {
                    $med = Medication::find($item['product_id']);
                    if ($med && $med->category !== 'over_the_counter') {
                        $requiresPharmacist = true;
                        break;
                    }
                }

                // 2. Crear Venta
                $sale = Sale::create([
                    'sold_at'        => now(),
                    'subtotal'       => $validated['total'],
                    'tax'            => 0,
                    'total'          => $validated['total'],
                    'payment_method' => $request->payment_method ?? 'cash',
                    'status'         => $requiresPharmacist ? SaleStatus::PENDING : SaleStatus::COMPLETED,
                    'customer_id'    => $validated['customer_id'],
                    'salesperson_id' => Auth::id(),
                ]);

                // 3. Procesar Items
                foreach ($validated['items'] as $itemData) {
                    $medication = Medication::findOrFail($itemData['product_id']);
                    
                    if ($medication->stock < $itemData['quantity']) {
                        throw new \Exception("Stock insuficiente para: " . $medication->name);
                    }

                    $sale->items()->create([
                        'medication_id' => $medication->id,
                        'quantity'      => $itemData['quantity'],
                        'unit_price'    => $itemData['unit_price'],
                        'line_total'    => $itemData['quantity'] * $itemData['unit_price'],
                    ]);

                    if ($medication->category !== 'over_the_counter') {
                        $prescription = Prescription::create([
                            'prescription_number' => 'RX-' . strtoupper(bin2hex(random_bytes(4))),
                            'patient_name'        => $sale->customer->name,
                            'doctor_name'         => $validated['doctor_name'] ?? 'No indicado',
                            'doctor_license'      => $validated['doctor_license'] ?? 'N/A',
                            'status'              => PrescriptionStatus::PENDING,
                            'issued_at'           => now(),
                            'expires_at'          => now()->addDays(30),
                            'medication_id'       => $medication->id,
                        ]);

                        SalePrescription::create([
                            'sale_id'         => $sale->id,
                            'prescription_id' => $prescription->id,
                            'medication_id'   => $medication->id,
                        ]);
                    }

                    $medication->decrement('stock', $itemData['quantity']);
                }

                return redirect()->route('salesperson.dashboard')->with('success', 'Venta registrada con éxito');
            });
        } catch (\Exception $e) {
            Log::error("Error en store: " . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === SaleStatus::CANCELLED) {
            return back()->with('error', 'Esta venta ya ha sido anulada.');
        }

        try {
            DB::transaction(function () use ($sale) {
                foreach ($sale->items as $item) {
                    $item->medication->increment('stock', $item->quantity);
                }

                foreach ($sale->prescriptions as $salePrescription) {
                    if ($salePrescription->prescription) {
                        $salePrescription->prescription->update([
                            'status' => PrescriptionStatus::REJECTED,
                            'notes'  => 'Venta anulada por el vendedor.'
                        ]);
                    }
                }

                $sale->update(['status' => SaleStatus::CANCELLED]);
            });

            return back()->with('success', 'Venta #' . $sale->id . ' anulada correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al cancelar: " . $e->getMessage());
            return back()->with('error', 'No se pudo anular la venta.');
        }
    }
}