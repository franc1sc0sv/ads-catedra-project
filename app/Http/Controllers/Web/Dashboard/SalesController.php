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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
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
        Log::info('--- INICIO FORZADO ---');

        // 1. Validación mínima
        $data = $request->all();

        try {
            return DB::transaction(function () use ($data) {

                // 2. Crear Venta directamente
                $sale = Sale::create([
                    'sold_at'        => now(),
                    'subtotal'       => $data['total'],
                    'tax'            => 0,
                    'total'          => $data['total'],
                    'payment_method' => 'cash',
                    'status'         => SaleStatus::PENDING, // Forzamos pending para probar
                    'customer_id'    => $data['customer_id'],
                    'salesperson_id' => Auth::id(),
                ]);

                Log::info('Venta creada ID: ' . $sale->id);

                foreach ($data['items'] as $item) {
                    $medication = Medication::find($item['product_id']);

                    // 3. Crear Item
                    $sale->items()->create([
                        'medication_id' => $medication->id,
                        'quantity'      => $item['quantity'],
                        'unit_price'    => $item['unit_price'],
                        'line_total'    => $item['quantity'] * $item['unit_price'],
                    ]);
                    Log::info('Item creado para med: ' . $medication->id);

                    // 4. Crear Receta si no es venta libre
                    if ($medication->category !== 'over_the_counter') {
                        $prescription = Prescription::create([
                            'prescription_number' => 'RX-' . strtoupper(bin2hex(random_bytes(4))),
                            'patient_name'        => $sale->customer->name,
                            'doctor_name'         => $data['doctor_name'] ?? 'Dr. Test',
                            'doctor_license'      => $data['doctor_license'] ?? '0000',
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
                        Log::info('Receta y Pivote creados');
                    }

                    $medication->decrement('stock', $item['quantity']);
                }

                Log::info('--- FIN FORZADO EXITOSO ---');
                return redirect()->route('salesperson.dashboard')->with('success', 'Venta guardada');
            });
        } catch (\Exception $e) {
            Log::error("FALLO CRITICO: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === SaleStatus::CANCELLED) {
            return back()->with('error', 'Esta venta ya ha sido anulada.');
        }

        try {
            DB::transaction(function () use ($sale) {
                // Devolver stock
                foreach ($sale->items as $item) {
                    $item->medication->increment('stock', $item->quantity);
                }

                // Anular recetas vinculadas
                foreach ($sale->prescriptions as $salePrescription) {
                    if ($salePrescription->prescription) {
                        $salePrescription->prescription->update([
                            'status' => PrescriptionStatus::REJECTED,
                            'notes'  => 'Venta anulada por el vendedor.'
                        ]);
                    }
                }

                $sale->update([
                    'status' => SaleStatus::CANCELLED,
                    'cancellation_reason' => 'Anulada desde el panel de ventas.'
                ]);
            });

            return back()->with('success', 'Venta anulada correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al cancelar: " . $e->getMessage());
            return back()->with('error', 'Error al anular la venta.');
        }
    }
}
