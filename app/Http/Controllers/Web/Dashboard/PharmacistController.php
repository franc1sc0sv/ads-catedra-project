<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\PrescriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\SalePrescription;
use App\Services\Ventas\Contracts\VentaServiceInterface;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacistController extends Controller
{
    public function __construct(
        private readonly VentaServiceInterface $ventas,
    ) {}

    public function index()
    {
        $prescriptions = Prescription::with(['medication'])
            ->where('status', PrescriptionStatus::PENDING)
            ->latest('issued_at')
            ->get()
            ->groupBy('prescription_number');

        return view('pharmacist.dashboard.index', compact('prescriptions'));
    }

    public function queue()
    {
        $prescriptions = Prescription::with(['medication'])
            ->where('status', PrescriptionStatus::PENDING)
            ->latest('issued_at')
            ->get()
            ->groupBy('prescription_number');

        return view('pharmacist.dashboard.queue', compact('prescriptions'));
    }

    public function validate(string $prescriptionNumber): RedirectResponse
    {
        $affected = Prescription::where('prescription_number', $prescriptionNumber)
            ->where('status', PrescriptionStatus::PENDING)
            ->update([
                'status' => PrescriptionStatus::VALIDATED,
                'validated_at' => now(),
                'pharmacist_id' => Auth::id(),
            ]);

        if ($affected === 0) {
            return redirect()->back()->with('error', 'No se pudo validar la receta.');
        }

        $salePrescription = SalePrescription::whereHas(
            'prescription',
            fn ($q) => $q->where('prescription_number', $prescriptionNumber)
        )->with('sale')->first();

        if ($salePrescription?->sale) {
            try {
                $this->ventas->completeSale($salePrescription->sale, (int) Auth::id());
            } catch (DomainException $e) {
                return redirect()->route('pharmacist.queue')
                    ->with('warning', "Receta validada, pero la venta no pudo completarse aún: {$e->getMessage()}");
            }
        }

        return redirect()->route('pharmacist.queue')
            ->with('success', "La receta #{$prescriptionNumber} ha sido validada.");
    }

    public function reject(Request $request, string $prescriptionNumber): RedirectResponse
    {
        $data = $request->validate([
            'reason' => 'required|string|min:3|max:255',
        ]);

        $salePrescription = SalePrescription::whereHas(
            'prescription',
            fn ($q) => $q->where('prescription_number', $prescriptionNumber)
        )->with('sale')->first();

        if (! $salePrescription?->sale) {
            return redirect()->back()->with('error', 'No se encontró la venta asociada a esta receta.');
        }

        try {
            $this->ventas->rejectSaleByPharmacist(
                $salePrescription->sale,
                $data['reason'],
                (int) Auth::id()
            );
        } catch (DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('pharmacist.queue')
            ->with('success', "Receta #{$prescriptionNumber} rechazada. La venta ha sido cancelada.");
    }

    public function history()
    {
        $history = Prescription::with(['medication', 'pharmacist'])
            ->where('status', PrescriptionStatus::VALIDATED)
            ->latest('validated_at')
            ->get()
            ->groupBy('prescription_number');

        return view('pharmacist.dashboard.history', compact('history'));
    }
}
